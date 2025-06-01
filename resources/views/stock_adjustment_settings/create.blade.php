@extends('layouts.app')
@section('title', __('stock_adjustment_settings.list'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
<br>
    <h1>@if(isset($_GET['id'])) Edit @endif @lang('stock_adjustment_settings.list')</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content no-print">
	{!! Form::open(['url' => action([\App\Http\Controllers\StockAdjustmentSettings::class, 'store']), 'method' => 'post', 'id' => 'stock_adjustment_form' ]) !!}
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
                @if(isset($_GET['id']))
				    <input type="hidden" name="id" value="{{ !empty($settings) ? $settings->id : '' }}">
                @endif
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('transaction_date', __('messages.date') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::text('date', !empty($settings) ? @format_datetime($settings->date) : @format_datetime('now'), ['class' => 'form-control', 'readonly', 'required']); !!}
						</div>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('adjustment_type', __('stock_adjustment_settings.adjustment_type') . ':*') !!} 
						{!! Form::select('adjustment_type', [ 'increase' =>  __('stock_adjustment_settings.increase'), 'decrease' =>  __('stock_adjustment_settings.decrease')], !empty($settings) ? $settings->adjustment_type : null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
					</div>
				</div>
				
				<div class="col-sm-4 ">
                    <div class="form-group">
                        {!! Form::label('category_id', __('product.category') . ':') !!}<br>
                        {!! Form::select('category_id', $categories, !empty($settings->category_id) ? $settings->category_id : null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2', 'required']); !!}
                    </div>
                </div>
            </div>
            <div class="row">
        
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('sub_category_id', __('product.sub_category') . ':') !!}
                        {!! Form::select('sub_category_id', $sub_categories, !empty($settings->sub_category_id) ? $settings->sub_category_id : null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2', 'required']); !!}
                    </div>
                </div>
                
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('account_to_link', __('stock_adjustment_settings.account_to_link') . ':') !!}
                        {!! Form::select('account_to_link', $accounts, !empty($settings->account_to_link) ? $settings->account_to_link : null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2', 'required']); !!}
                    </div>
                </div>
                
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('stock_group', __('stock_adjustment_settings.stock_account_group') . ':') !!}
                        {!! Form::select('stock_group', $groups, !empty($settings->stock_group) ? $settings->stock_group : null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2', 'required']); !!}
                    </div>
                </div>
                
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('stock_account', __('stock_adjustment_settings.stock_account') . ':') !!}
                        {!! Form::select('stock_account', $accounts, !empty($settings->stock_account) ? $settings->stock_account : null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2', 'required']); !!}
                    </div>
                </div>

			</div>
			<div class="row">
				<div class="col-sm-12 text-center">
					<button type="submit" class="btn btn-primary btn-big">@if(isset($_GET['id'])) @lang('messages.update') @else @lang('messages.save') @endif</button>
				</div>
			</div>
		</div>
	</div>
	{!! Form::close() !!}
    @if(!isset($_GET['id']))
        <div class="table-responsive">
            <table class="table table-bordered table-striped ajax_view" id="stock_adjustment_settings_table">
                <thead>
                    <tr>
                        <th>@lang('messages.action')</th>
                        <th>@lang('stock_adjustment_settings.date_time')</th>
                        <th>@lang('stock_adjustment.adjustment_type')</th>
                        <th>@lang('stock_adjustment_settings.category')</th>
                        <th>@lang('stock_adjustment_settings.sub_category')</th>
                        <th>@lang('stock_adjustment_settings.account_to_link')</th>
                        <th>@lang('stock_adjustment_settings.stock_account_group')</th>
                        <th>@lang('stock_adjustment_settings.stock_account')</th>
                        <th>@lang('lang_v1.added_by')</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endif
</section>
@stop
@section('javascript')
	<script type="text/javascript">
	$(document).on('change','#adjustment_type',function(){
	    var type = $(this).val();
	    if(type == 'increase'){
	        var type_i = 'Income';
	    }else if(type == 'decrease'){
	        var type_i = 'Expense';
	    }
	    
	    $("#account_to_link").empty();
	    if(type_i){
	        $.ajax({
                method: 'get',
                url: '/stock-settings/fetch-accounts/type/'+type_i,
                data: {  },
                contentType: 'html',
                success: function(result) {
                   $("#account_to_link").empty().append(result);
                },
    
            });
	    }
	    
	});
	
	$(document).on('change','#stock_group',function(){
	    var type_i = $(this).val();
	    $("#stock_account").empty();
	    
	    if(type_i){
	        $.ajax({
                method: 'get',
                url: '/stock-settings/fetch-accounts/group/'+type_i,
                data: {  },
                contentType: 'html',
                success: function(result) {
                   $("#stock_account").empty().append(result);
                },
    
            });
	    }
	    
	});
    
    var stock_adjustment_settings_table = $('#stock_adjustment_settings_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/stock-settings/create',
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false,
            },
        ],
        aaSorting: [[1, 'desc']],
        columns: [
            { data: 'action', name: 'action' },
            { data: 'date', name: 'date' },
            { data: 'adjustment_type', name: 'adjustment_type' },
            { data: 'category', name: 'categories.name' },
            { data: 'sub_category', name: 'sub_category.name' },
            { data: 'account_to_link_name', name: 'accounts.name' },
            { data: 'account_group_name', name: 'account_groups.name' },
            { data: 'stock_account_name', name: 'stock_accounts.name' },
            { data: 'added_by', name: 'users.first_name' },
        ],
        fnDrawCallback: function(oSettings) {
            // 
        },
    });
	</script>
@endsection






