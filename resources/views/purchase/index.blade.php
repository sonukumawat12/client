@extends('layouts.app')
@section('title', __('purchase.purchases'))

@section('content')
@php 
$business_id = request()->session()->get('user.business_id');
$add_purchase = \App\Utils\ModuleUtil::hasThePermissionInSubscription($business_id, 'add_purchase');
@endphp


<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">List @lang('purchase.purchases')</h4>
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">@lang('purchase.purchases')</a></li>
                    <li><span>List @lang('purchase.purchases')</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content main-content-inner no-print">
    @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_location_id',  __('purchase.business_location') . ':') !!}
                {!! Form::select('purchase_list_filter_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('purchase_list_order_no',  __('purchase.purchase_order_no') . ':') !!}
                {!! Form::select('purchase_list_order_no', array_combine($ordernos, $ordernos), null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}

            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_supplier_id',  __('purchase.supplier') . ':') !!}
                {!! Form::select('purchase_list_filter_supplier_id', $suppliers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_status',  __('purchase.purchase_status') . ':') !!}
                {!! Form::select('purchase_list_filter_status', $orderStatuses, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_payment_status',  __('purchase.payment_status') . ':') !!}
                {!! Form::select('purchase_list_filter_payment_status', ['paid' => __('lang_v1.paid'), 'due' => __('lang_v1.due'), 'partial' => __('lang_v1.partial'), 'overdue' => __('lang_v1.overdue')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="form-group">
            <!-- D 81 Added some code here-->
                {!! Form::label('purchase_list_filter_date_range', __('report.date_range') . ':') !!}
                {!! Form::text('purchase_list_filter_date_range',  @format_date('first day of this month') . ' ~ ' . @format_date('last day of this month')  , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
            </div>
        </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => __('purchase.all_purchases')])
        @if($add_purchase == 1)
            @slot('tool')
                <div class="row">
                    <div class="box-tools pull-right">
                        <a class="btn btn-primary" href="{{action('PurchaseController@create')}}">
                        <i class="fa fa-plus"></i> @lang('messages.add')</a>
                    </div>
                </div>
                <hr>
                
            @endslot
        @endif
        @can('purchase.view')
            @include('purchase.partials.purchase_table')
        @endcan
    @endcomponent

    <div class="modal fade product_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade payment_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

    @include('purchase.partials.update_purchase_status_modal')

</section>

<section id="receipt_section" class="print_section"></section>

<!-- /.content -->
@stop
@section('javascript')
<script src="{{ asset('js/purchase.js?v=' . $asset_v+1) }}"></script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
<script>
        //Date range as a button
    $('#purchase_list_filter_date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#purchase_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
           purchase_table.ajax.reload();
        }
    );
    $('#purchase_list_filter_date_range').on('apply.daterangepicker', function(ev, picker) {
        if (picker.chosenLabel === 'Custom Date Range') {
            $('#target_custom_date_input').val('purchase_list_filter_date_range');
            $('.custom_date_typing_modal').modal('show');
        }
    });


    $('#custom_date_apply_button').on('click', function() {
        debugger;
        if($('#target_custom_date_input').val() == "purchase_list_filter_date_range"){
            let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
            let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

            if (startDate.length === 10 && endDate.length === 10) {
                let formattedStartDate = moment(startDate).format(moment_date_format);
                let formattedEndDate = moment(endDate).format(moment_date_format);

                $('#purchase_list_filter_date_range').val(
                    formattedStartDate + ' ~ ' + formattedEndDate
                );

                $('#purchase_list_filter_date_range').data('daterangepicker').setStartDate(moment(startDate));
                $('#purchase_list_filter_date_range').data('daterangepicker').setEndDate(moment(endDate));

                $('.custom_date_typing_modal').modal('hide');
            } else {
                alert("Please select both start and end dates.");
            }
        }
    });


    $('#purchase_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#purchase_list_filter_date_range').val('');
        purchase_table.ajax.reload();
    });
    //D 81 Added the following two line of code
	$('#purchase_list_filter_date_range').data('daterangepicker').setStartDate(moment().startOf('month'));
	$('#purchase_list_filter_date_range').data('daterangepicker').setEndDate(moment().endOf('month'));

    $(document).on('click', '.update_status', function(e){
        e.preventDefault();
        $('#update_purchase_status_form').find('#status').val($(this).data('status'));
        $('#update_purchase_status_form').find('#purchase_id').val($(this).data('purchase_id'));
        $('#update_purchase_status_modal').modal('show');
    });

    $(document).on('submit', '#update_purchase_status_form', function(e){
        e.preventDefault();
        $(this)
            .find('button[type="submit"]')
            .attr('disabled', true);
        var data = $(this).serialize();

        $.ajax({
            method: 'POST',
            url: $(this).attr('action'),
            dataType: 'json',
            data: data,
            success: function(result) {
                if (result.success == true) {
                    $('#update_purchase_status_modal').modal('hide');
                    toastr.success(result.msg);
                    purchase_table.ajax.reload();
                    $('#update_purchase_status_form')
                        .find('button[type="submit"]')
                        .attr('disabled', false);
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });
</script>
	
@endsection