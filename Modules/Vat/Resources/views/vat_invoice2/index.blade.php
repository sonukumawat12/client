@extends('layouts.app')
@section('title', __('vat::lang.vat_invoice'))

@section('content')
<!-- Main content -->
<section class="content">

    <div class="row">
        @include('vat::vat_invoice2.partials.nav')
        <div class="clearfix"></div>
        <hr>
        <div class="row">
            <div class="col-md-3">
                @component('components.widget', ['class' => 'box'])
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('vat_invoice2_filter_date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('vat_invoice2_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                    </div>
                </div>
                @endcomponent
            </div>
            <div class="col-md-3">
                {!! Form::label('contact_id', __('vat::lang.customer') . ':') !!}
                {!! Form::select('contact_id', $contact_dropdown, null, ['class' => 'form-control select2', 'id' => 'contact_id']) !!}
            </div>
            <div class="col-md-3">
                {!! Form::label('sub_contact_id', __('vat::lang.sub_customer') . ':') !!}
                {!! Form::select('sub_contact_id', $contact_dropdown, null, ['class' => 'form-control select2', 'id' => 'sub_contact_id']) !!}
            </div>
            <div class="col-md-3">
                {!! Form::label('customer_bill_no', __('vat::lang.bill_no') . ':') !!}
                {!! Form::select('customer_bill_no', $bill_no_dropdown, null, ['class' => 'form-control select2', 'id' => 'customer_bill_no']) !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'vat::lang.vat_invoice')])
            
            @slot('tool')
            <div class="box-tools">
                <a type="button" class="btn btn-primary pull-right" 
                    href="{{action('\Modules\Vat\Http\Controllers\VatInvoice2Controller@create')}}">
                    <i class="fa fa-plus"></i> @lang( 'vat::lang.add' )</a>
            </div>
            @endslot
            
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered" id="vat_invoices_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang( 'vat::lang.date' )</th>
                                <th>@lang( 'vat::lang.bill_no' )</th>
                                <th>@lang( 'vat::lang.total' )</th>
                                <th>@lang( 'vat::lang.customer' )</th>
                                <th>@lang( 'vat::lang.sub_customer' )</th>
                                <th>@lang( 'vat::lang.credit_limit' )</th>
                                <th>@lang( 'vat::lang.outstanding' )</th>
                                <th>@lang( 'vat::lang.user' )</th>
                                <th>@lang( 'messages.action' )</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    <div class="modal fade issue_bill_customer_model" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>
<!-- /.content -->

@endsection
@section('javascript')
<script>
    if ($('#vat_invoice2_filter_date_range').length == 1) {
        $('#vat_invoice2_filter_date_range').daterangepicker(dateRangeSettings, function(start, end) {
            $('#vat_invoice2_filter_date_range').val(
                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
            );
            vat_invoices_table.ajax.reload();
        });
        $('#custom_date_apply_button').on('click', function() {
            let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
            let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

            if (startDate.length === 10 && endDate.length === 10) {
                let formattedStartDate = moment(startDate).format(moment_date_format);
                let formattedEndDate = moment(endDate).format(moment_date_format);

                $('#vat_invoice2_filter_date_range').val(
                    formattedStartDate + ' ~ ' + formattedEndDate
                );

                $('#vat_invoice2_filter_date_range').data('daterangepicker').setStartDate(moment(startDate));
                $('#vat_invoice2_filter_date_range').data('daterangepicker').setEndDate(moment(endDate));

                $('.custom_date_typing_modal').modal('hide');
                vat_invoices_table.ajax.reload();
            } else {
                toastr.error("Please select both start and end dates.");
            }
        });
        $('#vat_invoice2_filter_date_range').on('apply.daterangepicker', function(ev, picker) {
            if (picker.chosenLabel === 'Custom Date Range') {
                $('.custom_date_typing_modal').modal('show');
            }
        });
        $('#vat_invoice2_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#vat_invoice2_filter_date_range')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#vat_invoice2_filter_date_range')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }
    $('#contact_id, #sub_contact_id, #customer_bill_no').change(function(){
        vat_invoices_table.ajax.reload();
    });
    // vat_invoices_table
    if ($.fn.DataTable.isDataTable('#vat_invoices_table')) {
        $('#vat_invoices_table').DataTable().destroy();
    }
        vat_invoices_table = $('#vat_invoices_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{action('\Modules\Vat\Http\Controllers\VatInvoice2Controller@index')}}",
                cache: false,
                data: function ( d ) {
                    if($('#vat_invoice2_filter_date_range').val()) {
                        var start = $('#vat_invoice2_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#vat_invoice2_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                    }
                    d.contact_id = $('#contact_id').val();
                    d.sub_contact_id = $('#sub_contact_id').val();
                    d.customer_bill_no = $('#customer_bill_no').val();
                },
            },
            
            columns: [
                {data: 'date', name: 'date', orderable: false},
                {data: 'customer_bill_no', name: 'customer_bill_no'},
                {data: 'total_amount', name: 'total_amount'},
                {data: 'customer_name', name: 'contacts.name'},
                
                {data: 'sub_customer', name: 'subc.name'},
                
                {data: 'credit_limit', name: 'credit_limit'},
                {data: 'outstanding_amount', name: 'outstanding_amount'},
                
                {data: 'username', name: 'users.username'},
                {data: 'action', name: 'action'},
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });
        $(document).on('click', 'a.delete-issue_bill_customer', function(e){
            e.preventDefault();
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete)=>{
                if(willDelete){
                    let href = $(this).data('href');

                    $.ajax({
                        method: 'delete',
                        url: href,
                        data: {  },
                        success: function(result) {
                            if(result.success == 1){
                                toastr.success(result.msg);
                            }else{
                                toastr.error(result.msg);
                            }
                            vat_invoices_table.ajax.reload();
                        },
                    });
                }
            });
        });
        
        @if(!empty(session('status')['print_url']))
            let href = "{{session('status')['print_url']}}";
          
            $.ajax({
                method: 'get',
                url: href,
                data: {  },
                contentType: 'html',
                success: function(result) {
                    html = result;
                    console.log(html);
                    var w = window.open('', '_self');
                    $(w.document.body).html(html);
                    w.print();
                    w.close();
                    location.reload();
                },
            });
        @endif

        $(document).on('click', 'a.print_bill', function(){
            let href = $(this).data('href');

            $.ajax({
                method: 'get',
                url: href,
                data: {  },
                contentType: 'html',
                success: function(result) {
                    html = result;
                    console.log(html);
                    var w = window.open('', '_self');
                    $(w.document.body).html(html);
                    w.print();
                    w.close();
                    setTimeout(function() {
                location.reload();
            }, 50000);
                },
            });


        });


        $(document).on('click', '#add_issue_bill_customer_btn', function(){
            $('.issue_bill_customer_model').modal({
                backdrop: 'static',
                keyboard: false
            })
        })
</script>
@endsection