@extends('layouts.app')
@section('title', __('petro::lang.list_settlement'))

@section('content')



<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">@lang('petro::lang.petro')</a></li>
                    <li><span>@lang( 'petro::lang.mange_list_settlement') </span></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content main-content-inner">
    @if(!empty($message)) {!! $message !!} @endif
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                        {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('pump_operator', __('petro::lang.pump_operator').':') !!}
                        {!! Form::select('pump_operator', $pump_operators, null, ['class' => 'form-control select2', 'placeholder' => __('petro::lang.all')]); !!}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('settlement_no', __('petro::lang.settlement_number').':') !!}
                        {!! Form::select('settlement_no', $settlement_nos, null, ['class' => 'form-control select2', 'placeholder' => __('petro::lang.all')]); !!}
                    </div>
                </div>
            
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'expense_date_range', 'readonly']); !!}
                    </div>
                </div>
            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary'])
    @slot('tool')
    <div class="box-tools pull-right ">
            <a class="btn  btn-primary" href="{{action('\Modules\Petro\Http\Controllers\SettlementController@create')}}">
                <i class="fa fa-plus"></i> @lang('messages.add')</a>
    </div>
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="list_settlement">
            <thead>
                <tr>
                    <th class="notexport">@lang('messages.action')</th>
                    <th>@lang('petro::lang.status')</th>
                    <th>@lang('petro::lang.settlement_date')</th>
                    <th>@lang('petro::lang.settlement_no')</th>
                    <th>@lang('petro::lang.shift_number')</th>
                    <th>@lang('petro::lang.pump_operator_name')</th>
                    <th>@lang('petro::lang.pumps')</th>
                    <th>@lang('petro::lang.location')</th>
                    <th>@lang('petro::lang.shift')</th>
                    <th>@lang('petro::lang.note')</th> 
                    <th>@lang('petro::lang.total_amnt')</th>
                    <th>@lang('petro::lang.added_user')</th>
                </tr>
            </thead>
        </table>
    </div>
    @endcomponent

    <div class="modal fade settlement_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div id="settlement_print" class="container"></div>
</section>
<!-- /.content -->

@endsection
@section('javascript')
<script type="text/javascript">
    $(document).ready( function(){
    var columns = [
            { data: 'action', searchable: false, orderable: false },
            { data: 'status', name: 'status' },
            { data: 'transaction_date', name: 'transaction_date' },
            { data: 'settlement_no', name: 'settlement_no' },
            { data: 'shift_number', name: 'shift_number' },
            { data: 'pump_operator_name', name: 'pump_operators.name' },
            { data: 'pump_nos', name: 'pump_nos', searchable: false },
            { data: 'location_name', name: 'business_locations.name' },
            { data: 'shift', name: 'shift', searchable: false},
            { data: 'note', name: 'note' },
            { data: 'total_amount', name: 'total_amount' },
            { data: 'created_by',searchable: false, name: 'created_by' }
        ];
  
    list_settlement = $('#list_settlement').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: {
            url: '{{action('\Modules\Petro\Http\Controllers\SettlementController@index')}}',
            data: function(d) {
                d.location_id = $('select#location_id').val();
                d.pump_operator = $('select#pump_operator').val();
                d.settlement_no = $('select#settlement_no').val();
                d.start_date = $('input#expense_date_range')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                d.end_date = $('input#expense_date_range')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
            },
        },
        columnDefs: [ {
            "targets": 0,
            "orderable": false,
            "searchable": false
        } ],
        columns: columns,
        fnDrawCallback: function(oSettings) {
            total_amount = 0.00;
            $("#list_settlement tbody tr").each(function(){
                let number = $(this).find("td").eq(-2).text();
                if (number !== 'No data available in table') {
                    total_amount += parseFloat(number.replace(/,/g, ''));
                }
            });
            
            total_amount = total_amount === 0 ? '0.00' : parseFloat(total_amount).toLocaleString(undefined, {
                          minimumFractionDigits: 2,
                          maximumFractionDigits: 2,
                          useGrouping: true
                        });
            $("#list_settlement").append('<tr class="bg-gray font-17 footer-total text-center"><td>Total</td><td><td></td></td><td></td><td></td><td></td><td></td><td></td><td></td><td>'+total_amount+'</td></tr>');
        },
    });
    $('#location_id, #pump_operator, #pump_operator, #settlement_no, #type, #expense_date_range').change(function(){
        list_settlement.ajax.reload();
    });

    $(document).on('click', 'a.delete_settlement_button', function(e) {
		e.preventDefault();
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).attr('href');
                var data = $(this).serialize();
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                        list_settlement.ajax.reload();
                    },
                });
            }
        });
    });

    $(document).on('click', 'a.delete_reference_button', function(e) {
		var page_details = $(this).closest('div.page_details')
		e.preventDefault();
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).attr('href');
                var data = $(this).serialize();
                console.log(href);
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            page_details.remove();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                        list_settlement.ajax.reload();
                    },
                });
            }
        });
    });
});

$(document).on('click', '.edit_contact_button', function(e) {
    e.preventDefault();
    $('div.pump_operator_modal').load($(this).attr('href'), function() {
        $(this).modal('show');
    });
});

$('#location_id').select2();


//save settlement
$(document).on('click', '.print_settlement_button', function () {
    var url = $(this).data('href');
    $.ajax({
        method: 'get',
        url: url,
        data: {},
        success: function(result) {
            $('#settlement_print').html(result);

            var divToPrint=document.getElementById('settlement_print');

            var newWin=window.open('','Print-Ledger');
        
            newWin.document.open();
        
            newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');
        
            newWin.document.close();
            
        },
    });
});

$('#settlement_print').css('visibility', 'hidden');
</script>
@endsection