@extends('layouts.app')
@section('title', __('ledger_discount.customer_discounts'))

@section('content')
<link rel="stylesheet"
    href="{{ asset('plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css?v='.$asset_v) }}">

<!-- Content Header (Page header) -->

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">@lang('ledger_discount.list_discount')</h4>
                
            </div>
        </div>
    </div>
</div>


<!-- Main content -->
<section class="content main-content-inner">
    
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('date_range', __('lang_v1.date_range').':') !!}
                {!! Form::text('date_range', null, ['class' => 'form-control ','readonly', 'style' => 'width: 100%;']); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('form_no', __('ledger_discount.form_no').':') !!}
                {!! Form::select('form_no', $form_nos, null, ['class' => 'form-control
                select2', 'style' => 'width: 100%;', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('location', __('ledger_discount.location').':') !!}
                {!! Form::select('location', $business_locations, null, ['class' => 'form-control
                select2', 'style' => 'width: 100%;', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('customer', __('ledger_discount.customer').':') !!}
                {!! Form::select('customer', $customers, null, ['class' => 'form-control
                select2', 'style' => 'width: 100%;', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
    </div>
    <div class="row">
        
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('invoice_nos', __('ledger_discount.invoice_nos').':') !!}
                {!! Form::select('invoice_nos', $invoices, null, ['class' => 'form-control
                select2', 'style' => 'width: 100%;', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('against_invoice', __('ledger_discount.against_invoice').':') !!}
                {!! Form::select('against_invoice', ['yes' => __('ledger_discount.yes'),'no' => __('ledger_discount.no')], null, ['class' => 'form-control
                select2', 'style' => 'width: 100%;', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('user_id', __('lang_v1.user').':') !!}
                {!! Form::select('user_id', $users, null, ['class' => 'form-control
                select2', 'style' => 'width: 100%;', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
    </div>
<div class="row">
    <div class="col-md-12">
        <table class="table table-striped table-bordered" id="customer_discounts_table" style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang( 'lang_v1.action' )</th>
                    <th>@lang( 'lang_v1.date' )</th>
                    <th>@lang( 'ledger_discount.form_no' )</th>
                    <th>@lang( 'ledger_discount.location' )</th>
                    <th>@lang( 'ledger_discount.customer' )</th>
                    <th>@lang( 'ledger_discount.against_invoice' )</th>
                    <th>@lang( 'ledger_discount.invoice_nos' )</th>
                    <th>@lang( 'ledger_discount.total_discount' )</th>
                    <th>@lang( 'lang_v1.user' )</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

    <div class="modal fade account_model"  role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection

@section('javascript')
<script src="{{ asset('plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js?v=' . $asset_v) }}"></script>
<script>
    $(document).ready(function(){
       if($('#date_range').length) {
            $('#date_range').daterangepicker(
                dateRangeSettings,
                function (start, end) {
                    $('#date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                    customer_discounts_table.ajax.reload();
                }
            );
            $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#date_range').val('');
                customer_discounts_table.ajax.reload();
            });
            
            $('#date_range')
                .data('daterangepicker')
                .setStartDate(moment().startOf('month'));
            $('#date_range')
                .data('daterangepicker')
                .setEndDate(moment().endOf('month'));
        }
        
        
        
        customer_discounts_table = $('#customer_discounts_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '/ledger-discount',
                data: function(d){
                    if($('#date_range').val()) {
                        var start = $('#date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                    }
                    d.sub_type = $('#form_no').val();
                    d.location = $('#location').val();
                    d.customer = $('#customer').val();
                    d.user_id = $('#user_id').val();
                    d.form_no = $("#form_no").val();
                    d.against_invoice = $("#against_invoice").val();
                }
                

            },
            columnDefs:[{
                    "targets": 8,
                    "orderable": false,
                    "searchable": false,
                    "width" : "30%",
                }],
            columns: [
                {data: 'action', name: 'action'},
                {data: 'transaction_date', name: 'transaction_date'},
                {data: 'invoice_no', name: 'invoice_no'},
                {data: 'location', name: 'location'},
                {data: 'contact_name', name: 'contact_name'},
                {data: 'against_invoice', name: 'against_invoice'},
                {data: 'invoice_nos', name: 'invoice_nos'},
                {data: 'final_total', name: 'final_total'},
                {data: 'username', name: 'users.username'},
            
               
            ],
            @include('layouts.partials.datatable_export_button')
            "fnDrawCallback": function (oSettings) {
                __currency_convert_recursively($('#customer_discounts_table'));
            },
            "rowCallback": function( row, data, index ) {
                
            }
        }); 
        
        $(document).change('#form_no,#location,#customer,#user_id,#form_no,#date_range',function(){
            customer_discounts_table.ajax.reload();
        })
        
        $(document).on('submit', 'form#add_discount_form, form#edit_discount_form', function(e) {
            e.preventDefault();
            var form = $(this);
            
            swal({
    
                title: LANG.sure,
    
                text: 'You are about to perform this operation.',
    
                icon: 'warning',
    
                buttons: true,
    
                dangerMode: true,
    
            }).then((willDelete) => {
    
                if (willDelete) {
    
                   var data = form.serialize();
        
                    $.ajax({
                        method: 'POST',
                        url: form.attr('action'),
                        dataType: 'json',
                        data: data,
                        success: function(result) {
                            if (result.success === true) {
                                $('.account_model').modal('hide');
                                toastr.success(result.msg);
                                form[0].reset();
                                form.find('button[type="submit"]').removeAttr('disabled');
                                customer_discounts_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
    
            });
           
        });

    });
</script>
@endsection