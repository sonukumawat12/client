@extends('layouts.app')

@section('title', __('petro::lang.daily_status_report'))



@section('content')

<!-- Content Header (Page header) -->

@php
    $business_id = session()->get('user.business_id');
    $business_details = App\Business::find($business_id);
    $currency_precision = !empty($business_details->currency_precision) ? $business_details->currency_precision : 2;
@endphp

<style>

	.daily_report_div table. {

		border: 1px solid #222;
		margin-top: 10px;
		margin-bottom: 0px;
	}

	.daily_report_div table.table-bordered>thead>tr>th {
		border: 1px solid #222;
		;
	}

	.daily_report_div table.table-bordered>tbody>tr>td {
		border: 1px solid #222;
		font-size: 13px;
	}

	.daily_reportt_div {
		max-width: 70%;
	}
    
    .daily_report_div .export {
        padding-top: 10px;
        display: flex;
        justify-content: flex-end;
    }
    

</style>

<div class="container daily_report_div" style="width: 100%; margin-auto">
    <div class="export">
        <button class="btn btn-primary" id="print_report" style="margin-right: 10px;">
            <i class="fas fa-print"></i> Print
        </button>    
        <button class="btn btn-primary" id='download_pdf'>
            <i class="fas fa-file-pdf"></i> Download PDF
        </button>
    </div>
    
	<div class="col-xs-12 text-center text-danger">
	    <h2 class="text-center"><strong>@lang('petro::lang.daily_status_report')</strong></h2>
		<p style="font-size: 22px;" class="text-center"><strong>{{request()->session()->get('business.name')}}</strong>
		</p>
	</div>
	<div class="col-md-12">
	    {!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\DailyStatusReportController@index'), 'method' => 'get', 'id' =>
            'product_transaction_report_filter_form', 'style'=>'margin:0 auto; width:50%' ]) !!}
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('date_range', @format_date('today') . ' ~ ' . @format_date('today') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'report_date_range', 'readonly']); !!}
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                        {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                    </div>
                </div>
            </div>
                
                
          {!! Form::close() !!}
	</div>
    	
	<div class="clearfix"></div>
	<br>
	<h3 class="text-danger" style="font-weight: bold; maring-bottom: 0px; font-size: 20px;">
		@lang('petro::lang.dip_details_section')
	</h3>
	<div class="row">
		<div class="col-md-12">
			<table class="table table-striped" id="dip_details_section" style="width: 100%;">
				<thead>
					<tr class="row-border">
						<th>@lang('petro::lang.tank_no')</th>
						<th>@lang('petro::lang.location')</th>
						<th>@lang('petro::lang.dip_stick_reading')</th>
						<th>@lang('petro::lang.qty_in_liters')</th>
						<th>@lang('petro::lang.qty_in_system')</th>
						<th>@lang('petro::lang.difference')</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
	
	<div class="clearfix"></div>
	<br>
	<h3 class="text-danger" style="font-weight: bold; maring-bottom: 0px; font-size: 20px;">
		@lang('petro::lang.pump_sales_details')
	</h3>
	<div class="row">
	    <div class="col-md-12">
    		<table class="table table-striped" id="pump_sales_details" style="width: 100%;">
    			<thead>
    				<tr class="row-border">
    					<th>@lang('petro::lang.pump_no' )</th>
    					<th>@lang('petro::lang.location')</th>
						<th>@lang('petro::lang.previous_day_meter' )</th>
    					<th>@lang('petro::lang.today_meter' )</th>
    					<th>@lang('petro::lang.sold_qty_liters')</th>
    					<th>@lang('petro::lang.amount')</th>
    					<th>@lang('petro::lang.banked_by_3pm')</th>
    					<th>@lang('petro::lang.locker')</th>
    					<th>@lang('petro::lang.card')</th>
    				</tr>
    			</thead>
    		</table>
	    </div>
	</div>
	
	<div class="clearfix"></div>
	<br>
	<h3 class="text-danger" style="font-weight: bold; maring-bottom: 0px; font-size: 20px;">
		@lang('petro::lang.fuel_sale_summary')
	</h3>
	<div class="row">
		<div class="col-md-6">
			<table class="table table-striped" id="fuel_sale" style="width: 100%;">
    			<thead>
    				<tr class="row-border">
    					<th>@lang('petro::lang.sub_category' )</th>
    					<th>@lang('petro::lang.location')</th>
						<th>@lang('petro::lang.qty' )</th>
    					<th>@lang('petro::lang.total_amount' )</th>
    				</tr>
    			</thead>
    		</table>
		</div>
	</div>
	
	<div class="clearfix"></div>
	<br>
	<h3 class="text-danger" style="font-weight: bold; maring-bottom: 0px; font-size: 20px;">
		@lang('petro::lang.lubricant_sale')
	</h3>
	<div class="row">
		<div class="col-md-12">
			<table class="table table-striped" id="lubricant_sale" style="width: 100%;">
				<thead>
					<tr class="row-border">
						<th>@lang('petro::lang.product' )</th>
						<th>@lang('petro::lang.location')</th>
						<th>@lang('petro::lang.starting_qty' )</th>
						<th>@lang('petro::lang.purchase_qty' )</th>
						<th>@lang('petro::lang.sold_qty' )</th>
						<th>@lang('petro::lang.amount')</th>
						<th>@lang('petro::lang.balance_qty')</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
	
	<div class="clearfix"></div>
	<br>
	<h3 class="text-danger" style="font-weight: bold; maring-bottom: 0px; font-size: 20px;">
		@lang('petro::lang.other_sales')
	</h3>
	<div class="row">
		<div class="col-md-12">
			<table class="table table-striped" id="other_sale" style="width: 100%;">
				<thead>
					<tr class="row-border">
						<th>@lang('petro::lang.product' )</th>
						<th>@lang('petro::lang.location')</th>
						<th>@lang('petro::lang.sold_qty' )</th>
						<th>@lang('petro::lang.amount')</th>
						<th>@lang('petro::lang.balance_qty')</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
	
	<div class="clearfix"></div>
	<br>
	<h3 class="text-danger" style="font-weight: bold; maring-bottom: 0px; font-size: 20px;">
		@lang('petro::lang.gas_sales')
	</h3>
	<div class="row">
		<div class="col-md-12">
			<table class="table table-striped" id="gas_sale" style="width: 100%;">
				<thead>
					<tr class="row-border">
						<th>@lang('petro::lang.product' )</th>
						<th>@lang('petro::lang.location')</th>
						<th>@lang('petro::lang.starting_qty' )</th>
						<th>@lang('petro::lang.purchase_qty' )</th>
						<th>@lang('petro::lang.sold_qty' )</th>
						<th>@lang('petro::lang.amount')</th>
						<th>@lang('petro::lang.balance_qty')</th>
						<th>@lang('petro::lang.empty_cylinders')</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
	
	<div class="clearfix"></div>
	<br>
	<h3 class="text-danger" style="font-weight: bold; maring-bottom: 0px; font-size: 20px;">
		@lang('petro::lang.total_payment_summary')
	</h3>
	<div class="row" id="total_payments">
		<div class="col-md-3" >
		    <h4 class="text-center">@lang('petro::lang.total_sale')</h4>
		    <p class="text-center total_sale"></p>
		</div>
		<div class="col-md-2">
		    <h4 class="text-center">@lang('petro::lang.total_card')</h4>
		    <p class="text-center total_card"></p>
		</div>
		
		<div class="col-md-2">
		    <h4 class="text-center">@lang('petro::lang.total_credit')</h4>
		    <p class="text-center total_credit"></p>
		</div>
		
		<div class="col-md-2">
		    <h4 class="text-center">@lang('petro::lang.total_bank')</h4>
		    <p class="text-center total_bank"></p>
		</div>
		
		<div class="col-md-3">
		    <h4 class="text-center">@lang('petro::lang.cash')</h4>
		    <p class="text-center cash"></p>
		</div>
	</div>
	
	<div class="clearfix"></div>
	<br>
	
	<div class="row">
		<div class="col-md-6">
        	<h3 class="text-danger text-center" style="font-weight: bold; maring-bottom: 0px; font-size: 20px;">
        		@lang('petro::lang.balance_credit_receipt')
        	</h3>
		</div>
		<div class="col-md-6">
    		 <h3 class="text-danger text-center" style="font-weight: bold; maring-bottom: 0px; font-size: 20px;">
        		@lang('petro::lang.credit_sales')
        	</h3>
        	<table class="table table-striped table-bordered" id="credit_sales" style="width: 100%;">
				<thead>
					<tr class="row-border">
						<th>@lang('petro::lang.customer' )</th>
						<th>@lang('petro::lang.location')</th>
						<th>@lang('petro::lang.amount' )</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
	
    <div class="hide">
        <div id="report_print_div"></div>
    </div>
    
</div>



@endsection

@section('javascript')

<script type="text/javascript">

    $(document).ready(function(){
        
        if ($('#report_date_range').length == 1) {
            $('#report_date_range').daterangepicker(dateRangeSettings, function (start, end) {
                $('#report_date_range').val(
                    start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
                );
            });
            $('#report_date_range').data('daterangepicker').setStartDate(moment().startOf('today'));
            $('#report_date_range').data('daterangepicker').setEndDate(moment().endOf('today'));
        }
        var dateRangeSelector = $('input#report_date_range').data('daterangepicker');
        var dip_details = $('#dip_details_section').DataTable({
            processing: true,
            serverSide: true,
            paging: false,
            searching: false,
            dom: 't',
            ajax: {
                url: '{{action('\Modules\Petro\Http\Controllers\DailyStatusReportController@index')}}',
                data: function(d) {
                    d.start_date = $('input#report_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    d.end_date = $('input#report_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                        
                    d.location_id = $("#location_id").val();
                },
            },
            columnDefs: [ {
                "targets": 0,
                "orderable": false,
                "searchable": false
            } ],
            columns: [
                { data: 'tank_no', name: 'tank_no'},
                { data: 'location_name', name: 'business_locations.name'},
                { data: 'dip_reading', name: 'dip_reading' },
                { data: 'qty_liters', name: 'fuel_balance_dip_reading' },
                { data: 'qty_system', name: 'current_qty'},
                { data: 'difference', name: 'difference'},
            ],
            fnDrawCallback: function() {
            }
        });
        var pump_sales = $('#pump_sales_details').DataTable({
            processing: true,
            serverSide: true,
            paging: false,
            searching: false,
            dom: 't',
            ajax: {
                url: '{{action('\Modules\Petro\Http\Controllers\DailyStatusReportController@getPumpSales')}}',
                data: function(d) {
                    d.start_date = $('input#report_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    d.end_date = $('input#report_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                        
                    d.location_id = $("#location_id").val();
                },
            },
            columnDefs: [ {
                "targets": 0,
                "orderable": false,
                "searchable": false
            } ],
            columns: [
                { data: 'pump_no', name: 'pump_no'},
                { data: 'location_name', name: 'business_locations.name'},
                { data: 'previous_meter', name: 'starting_meter' },
                { data: 'today_meter', name: 'closing_meter' },
                { data: 'sold_qty', name: 'sold_qty'},
                { data: 'amount', name: 'amount'},
                { data: 'banked', name: 'banked'},
                { data: 'locker', name: 'locker'},
                { data: 'card', name: 'card'},
            ],
            fnDrawCallback: function() {
                var api = this.api();
                var totalAmount = 0;
                var totalBanked = 0;
                var totalLocker = 0;
                var totalCard = 0;
        
                // Iterate through the visible rows and calculate the total for specific conditions
                api.rows({page: 'current'}).every(function() {
                    var data = this.data();
                    totalAmount += parseFloat(data.amount.replace(/,/g, ''));
                    totalBanked += data.banked !== 0 && parseFloat(data.banked.replace(/,/g, ''));
                    totalLocker += data.locker !== 0 && parseFloat(data.locker.replace(/,/g, ''));
                    totalCard += data.card !== 0 && parseFloat(data.card.replace(/,/g, ''));
                });
                totalAmount = totalAmount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                totalBanked = totalBanked.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                totalLocker = totalLocker.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                totalCard = totalCard.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                $("#pump_sales_details").append('<tr class="bg-gray font-17 footer-total text-center"><td>Total</td><td></td><td></td><td></td><td></td><td>'+totalAmount+'</td><td>'+totalBanked+'</td><td>'+totalLocker+'</td><td>'+totalCard+'</td></tr>');
            }
        });
        var lubricant_sale = $('#lubricant_sale').DataTable({
            processing: true,
            serverSide: true,
            paging: false,
            searching: false,
            dom: 't',
            ajax: {
                url: '{{action('\Modules\Petro\Http\Controllers\DailyStatusReportController@getLubricantSale')}}',
                data: function(d) {
                    d.start_date = $('input#report_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    d.end_date = $('input#report_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                        
                    d.location_id = $("#location_id").val();
                },
            },
            columnDefs: [ {
                "targets": 0,
                "orderable": false,
                "searchable": false
            } ],
            columns: [
                { data: 'product', name: 'product'},
                { data: 'location_name', name: 'business_locations.name'},
                { data: 'starting_qty', name: 'starting_qty' },
                { data: 'purchase_qty', name: 'purchase_qty' },
                { data: 'sold_qty', name: 'sold_qty' },
                { data: 'amount', name: 'amount'},
                { data: 'balance_qty', name: 'balance_qty'},
            ],
            fnDrawCallback: function() {
                var api = this.api();
                var totalAmount = 0;
                api.rows({page: 'current'}).every(function() {
                    var data = this.data();
                    totalAmount += parseFloat(data.amount.replace(/,/g, ''));
                });
                totalAmount = totalAmount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                $("#lubricant_sale").append('<tr class="bg-gray font-17 footer-total text-center"><td>Total</td><td></td><td></td><td></td><td>'+totalAmount+'</td></tr>');
            }
        });
        var other_sale = $('#other_sale').DataTable({
            processing: true,
            serverSide: true,
            paging: false,
            searching: false,
            dom: 't',
            ajax: {
                url: '{{action('\Modules\Petro\Http\Controllers\DailyStatusReportController@getOtherSale')}}',
                data: function(d) {
                    d.start_date = $('input#report_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    d.end_date = $('input#report_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                        
                    d.location_id = $("#location_id").val();
                },
            },
            columnDefs: [ {
                "targets": 0,
                "orderable": false,
                "searchable": false
            } ],
            columns: [
                { data: 'product', name: 'product'},
                { data: 'location_name', name: 'business_locations.name'},
                { data: 'sold_qty', name: 'sold_qty' },
                { data: 'amount', name: 'amount'},
                { data: 'balance_qty', name: 'balance_qty'},
            ],
            fnDrawCallback: function() {
                var api = this.api();
                var totalAmount = 0;
                api.rows({page: 'current'}).every(function() {
                    var data = this.data();
                    totalAmount += parseFloat(data.amount.replace(/,/g, ''));
                });
                totalAmount = totalAmount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                $("#other_sale").append('<tr class="bg-gray font-17 footer-total text-center"><td>Total</td><td></td><td></td><td>'+totalAmount+'</td><td></td></tr>');
            }
        });
        var fuel_sale = $('#fuel_sale').DataTable({
            processing: true,
            serverSide: true,
            paging: false,
            searching: false,
            dom: 't',
            ajax: {
                url: '{{action('\Modules\Petro\Http\Controllers\DailyStatusReportController@getFuelSale')}}',
                data: function(d) {
                    d.start_date = $('input#report_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    d.end_date = $('input#report_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                        
                    d.location_id = $("#location_id").val();
                },
            },
            columnDefs: [ {
                "targets": 0,
                "orderable": false,
                "searchable": false
            } ],
            columns: [
                { data: 'name', name: 'name'},
                { data: 'location_name', name: 'business_locations.name'},
                { data: 'qty', name: 'qty' },
                { data: 'value', name: 'value' },
            ],
            fnDrawCallback: function() {
                var api = this.api();
                var totalAmount = 0;
                api.rows({page: 'current'}).every(function() {
                    var data = this.data();
                    totalAmount += parseFloat(data.value.replace(/,/g, ''));
                });
                totalAmount = totalAmount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                $("#fuel_sale").append('<tr class="bg-gray font-17 footer-total text-left"><td>Total</td><td></td><td></td><td>'+totalAmount+'</td></tr>');
            }
        });
       
        var gas_sale = $('#gas_sale').DataTable({
            processing: true,
            serverSide: true,
            paging: false,
            searching: false,
            dom: 't',
            ajax: {
                url: '{{action('\Modules\Petro\Http\Controllers\DailyStatusReportController@getGasSale')}}',
                data: function(d) {
                    d.start_date = $('input#report_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    d.end_date = $('input#report_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                        
                    d.location_id = $("#location_id").val();
                },
            },
            columnDefs: [ {
                "targets": 0,
                "orderable": false,
                "searchable": false
            } ],
            columns: [
                { data: 'product', name: 'product'},
                { data: 'location_name', name: 'business_locations.name'},
                { data: 'starting_qty', name: 'starting_qty' },
                { data: 'purchase_qty', name: 'purchase_qty' },
                { data: 'sold_qty', name: 'sold_qty' },
                { data: 'amount', name: 'amount'},
                { data: 'balance_qty', name: 'balance_qty'},
                { data: 'empty_cylinders', name: 'empty_cylinders'}
            ],
            fnDrawCallback: function() {
                var api = this.api();
                var totalAmount = 0;
                api.rows({page: 'current'}).every(function() {
                    var data = this.data();
                    totalAmount += parseFloat(data.amount.replace(/,/g, ''));
                });
                totalAmount = totalAmount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                $("#gas_sale").append('<tr class="bg-gray font-17 footer-total text-center"><td>Total</td><td></td><td></td><td></td><td>'+totalAmount+'</td></tr>');
            }
        });
        
        var credit_sales = $('#credit_sales').DataTable({
            processing: true,
            serverSide: true,
            paging: false,
            searching: false,
            dom: 't',
            ajax: {
                url: '{{action('\Modules\Petro\Http\Controllers\DailyStatusReportController@getCreditSale')}}',
                data: function(d) {
                    d.start_date = $('input#report_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    d.end_date = $('input#report_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                        
                    d.location_id = $("#location_id").val();
                },
            },
            columnDefs: [ {
                "targets": 0,
                "orderable": false,
                "searchable": false
            } ],
            columns: [
                { data: 'customer', name: 'customer'},
                { data: 'location_name', name: 'business_locations.name'},
                { data: 'amount', name: 'amount' },
            ],
            fnDrawCallback: function() {
                var api = this.api();
                var totalAmount = 0;
                api.rows({page: 'current'}).every(function() {
                    var data = this.data();
                    totalAmount += parseFloat(data.amount.replace(/,/g, ''));
                });
                totalAmount = totalAmount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                $("#credit_sales").append('<tr class="bg-gray font-17 footer-total text-center"><td>Total</td><td></td><td>'+totalAmount+'</td></tr>');
            }
        });
        var refreshData = function(init) {
            if (init === 1) {
                dip_details.ajax.reload();
                pump_sales.ajax.reload();
                other_sale.ajax.reload();
                lubricant_sale.ajax.reload();
                fuel_sale.ajax.reload();
                credit_sales.ajax.reload();
            }
            $.ajax({
                method: 'get',
                url: '{{ action('\Modules\Petro\Http\Controllers\DailyStatusReportController@getTotalPayments') }}',
                data: {
                    start_date: $('input#report_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                    end_date: $('input#report_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                },
                success: function(result) {
                    var cash = parseFloat(result.total_cash_payments) ?? 0;
                    var card = parseFloat(result.total_card_payments) ?? 0;
                    var bank = parseFloat(result.total_cash_deposits) ?? 0;
                    var credit =  parseFloat(result.total_credit_sale_payments) ?? 0;
                    
                    
                    var total = parseFloat(result.total_sales);
                    cash = cash.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    card = card.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    bank = bank.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    credit = credit.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    total = total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    $('#total_payments .total_sale').text(total);
                    $('#total_payments .total_card').text(card);
                    $('#total_payments .total_bank').text(bank);
                    $('#total_payments .total_credit').text(credit);
                    $('#total_payments .cash').text(cash);
                },
            });
        };
        refreshData(0);
        // Handle date range change
        $('#report_date_range').change(function() {
            refreshData(1);
        });
        
        $('#location_id').change(function() {
            refreshData(1);
        });
        
        $(document).on('click', '#print_report', function(e){
            $.ajax({
                method: 'get',
                contentType: 'html',
                url: '{{action('\Modules\Petro\Http\Controllers\DailyStatusReportController@printReport')}}',
                data: { 
                    start_date: $('input#report_date_range')
                                    .data('daterangepicker')
                                    .startDate.format('YYYY-MM-DD'),
                    end_date: $('input#report_date_range')
                                .data('daterangepicker')
                                .endDate.format('YYYY-MM-DD'),
                },
                success: function(result) {
                    $('#report_print_div').empty().append(result);
                    $('#report_print_div').printThis();

                },
            });
        });
        
        $(document).on('click', '#download_pdf', function(e){
            $.ajax({
                method: 'get',
                contentType: 'html',
                url: '{{action('\Modules\Petro\Http\Controllers\DailyStatusReportController@printReport')}}',
                data: { 
                    start_date: $('input#report_date_range')
                                    .data('daterangepicker')
                                    .startDate.format('YYYY-MM-DD'),
                    end_date: $('input#report_date_range')
                                .data('daterangepicker')
                                .endDate.format('YYYY-MM-DD'),
                },
                success: function(result) {
                    generatePdf(result,'pdf');

                },
            });
        });
        
        function generatePdf(html,action) {
            console.log(html);
            $.ajax({
                url: '{{action('\Modules\Petro\Http\Controllers\DailyStatusReportController@downloadPdf')}}',
                method: 'POST',
                data: {
                    html: html
                },
                success: function(data) {
                    // Handle the success response, for example:
                    var downloadUrl = data.path;
                    downloadPdf(downloadUrl);
                },
                error: function(xhr, status, error) {
                    // Handle the error response, for example:
                    alert('An error occurred while generating the PDF.');
                }
            });
            function downloadPdf(file){
                var link = document.createElement('a');
                link.href = file;
                link.download = 'report.pdf';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }
    
    });
</script>

@endsection