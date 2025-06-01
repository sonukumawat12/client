@extends('layouts.app')
@section('title', __('superadmin::lang.superadmin') . ' | ' . __('superadmin::lang.packages'))

@section('content')
	
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('superadmin::lang.packages') <small>@lang('superadmin::lang.all_packages')</small></h1>
    <!-- <ol class="breadcrumb">
        <a href="#"><i class="fa fa-dashboard"></i> Level</a><br/>
        <li class="active">Here<br/>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
	@include('superadmin::layouts.partials.currency')
	
	<div class="row">
                <div class="col-md-12">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#list_tab" data-toggle="tab" aria-expanded="true">
                                    <i class="fas fa-exclamation-circle text-orange"></i>
                                    @lang('superadmin::lang.subscription_packages')
                                </a>
                            </li>
                            <li>
                                <a href="#add_subscription" data-toggle="tab" aria-expanded="true">
                                    <i class="fa fas fa-check-circle text-success"></i>
                                    @lang('superadmin::lang.subscription')
                                </a>
                            </li>
                            <li>
                                <a href="#tenant_management" data-toggle="tab" aria-expanded="true">
                                    <i class="fa fas fa-check-circle text-success"></i>
                                    @lang('superadmin::lang.tenant_management')
                                </a>
                            </li>
                            <li>
                                <a href="#pay_online" data-toggle="tab" aria-expanded="true">
                                    <i class="fa fas fa-check-circle text-success"></i>
                                     @lang('superadmin::lang.pay_online')
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="list_tab">
                                <div class="box">
                                    <div class="box-header row">
                                        <div class="box-tools pull-right">
                                            <a href="{{action('\Modules\Superadmin\Http\Controllers\PackagesController@create')}}" 
                                                class="btn btn-primary">
                                            	<i class="fa fa-plus"></i> @lang( 'messages.add' )</a>
                                        </div>
                                    </div>
                            
                                    <div class="box-body">
                                        
                                        <div class="row">
                                        
                            			@foreach ($packages as $package)
                            			@php
                            				$is_patient_package = 0;
                            				if($package->hospital_system && in_array('patient' , json_decode($package->hospital_business_type))){
                            					$is_patient_package = 1;
                            				}else{
                            					$is_patient_package = 0;
                            				}
                            			@endphp
                            			<div class="col-md-4" style="min-height: 400px">
                                            <div class="card" style="margin-top: 20px; background-color: #FC9DF3; padding: 20px">
                                            	
                            					<div class="box box-success hvr-grow-shadow" style="width: 100%">
                            						<div class="box-header with-border text-center">
                            							<h4 class="box-title">{{$package->name}}</h4>
                            
                            							<div class="row">
                            								@if($package->is_active == 1)
                            									<span class="badge bg-green">
                            										@lang('superadmin::lang.active')
                            									</span>
                            								@else
                            									<span class="badge bg-red">
                            									@lang('superadmin::lang.inactive')
                            									</span>
                            								@endif
                            								
                            								<a href="{{action('\Modules\Superadmin\Http\Controllers\PackagesController@edit', [$package->id])}}" class="btn btn-box-tool" title="edit"><i class="fa fa-edit"></i></a>
                            								<a href="{{action('\Modules\Superadmin\Http\Controllers\PackagesController@destroy', [$package->id])}}" class="btn btn-box-tool link_confirmation" title="delete"><i class="fa fa-trash"></i></a>
                                          					
                            							</div>
                            						</div>
                            						<!-- /.box-header -->
                            						<div class="box-body text-center">
                            							@if (!$is_patient_package)
                            							@if($package->location_count == 0)
                            								@lang('superadmin::lang.unlimited')
                            							@else
                            								{{$package->location_count}}
                            							@endif
                            
                            							@lang('business.business_locations')
                            							<br/>
                            							@endif
                            
                            							@if($package->user_count == 0)
                            								@lang('superadmin::lang.unlimited')
                            							@else
                            								{{$package->user_count}}
                            							@endif
                            							@if ($is_patient_package)
                            							@lang('superadmin::lang.family_members')
                            							@else
                            							@lang('superadmin::lang.users')
                            							@endif
                            							<br/>
                            						
                            							@if (!$is_patient_package)
                            							@if($package->product_count == 0)
                            								@lang('superadmin::lang.unlimited')
                            							@else
                            								{{$package->product_count}}
                            							@endif
                            
                            							@lang('superadmin::lang.products')
                            							<br/>
                            							@endif
                            
                            							@if (!$is_patient_package)
                            							@if($package->invoice_count == 0)
                            								@lang('superadmin::lang.unlimited')
                            							@else
                            								{{$package->invoice_count}}
                            							@endif
                            
                            							@lang('superadmin::lang.invoices')
                            							<br/>
                            							@endif
                            
                            							@if(!empty($package->fleet_module))
                            								@if($package->vehicle_count == 0)
                            									@lang('superadmin::lang.unlimited')
                            								@else
                            									{{$package->vehicle_count}}
                            								@endif
                            									@lang('superadmin::lang.no_of_vehicle')
                            								<br />
                            							@endif
                            
                            							@if($package->trial_days != 0)
                            									{{$package->trial_days}} @lang('superadmin::lang.trial_days')
                            								<br/>
                            							@endif
                            							@php
                            								$modules = json_decode($package->package_permissions, true);
                            								$manage_module_enable = json_decode($package->manage_module_enable, true);
                            							@endphp
                            							@if(empty($package->only_for_business))
                            							@if(!empty($modules['account_access']))
                            							@lang('superadmin::lang.accounting_module')
                            							<br />
                            							@endif
                            							@else
                            							@if(!empty($manage_module_enable['access_account']))
                            							@lang('superadmin::lang.accounting_module')
                            							<br />
                            							@endif
                            							@endif
                            							@if(!empty($package->pump_operator_dashboard))
                            							@lang('superadmin::lang.pump_operator_dashboard')
                            							<br />
                            							@endif
                            							@if(!empty($package->property_module))
                            							@lang('superadmin::lang.property_module')
                            							<br />
                            							@endif
                            
                            							@if(!empty($package->custom_permissions))
                            								@foreach($package->custom_permissions as $permission => $value)
                            									@isset($permission_formatted[$permission])
                            										{{$permission_formatted[$permission]}}
                            										<br/>
                            									@endisset
                            								@endforeach
                            							@endif
                            							@php
                            								$currency_symbol = App\Currency::select('symbol')->where('id', $package->currency_id)->first();
                            								if(empty($currency_symbol)){
                            									$currency_symbol = '$';
                            								}else{
                            									$currency_symbol = $currency_symbol->symbol;
                            
                            								}
                            							@endphp
                            							<h3 class="text-center">
                            								@if($package->price != 0)
                            									<span>
                            									{{$currency_symbol}} {{number_format($package->price, 2)}}
                            									</span>
                            
                            									<small>
                            										/ {{$package->interval_count}} {{__('lang_v1.' . $package->interval)}}
                            									</small>
                            								@else
                            									@lang('superadmin::lang.free_for_duration', ['duration' => $package->interval_count . ' ' . __('lang_v1.' . $package->interval)])
                            								@endif
                            							</h3>
                            
                            						</div>
                            						<!-- /.box-body -->
                            
                            						<div class="box-footer text-center">
                            							{{$package->description}}
                            						</div>
                            					</div>
                            					<!-- /.box -->
                                            </div>
                                        </div>
                                        @endforeach
                                        
                                        
                            
                                        <div class="col-md-12">
                                            {{ $packages->links() }}
                                        </div>
                                        
                                    </div>
                                    </div>
                            
                                </div>
                            </div>
                            <div class="tab-pane" id="add_subscription">
                                
                                <div class="row">
                                    <div class="col-md-12 dip_tab">
                                        <div class="settlement_tabs">
                                            <ul class="nav nav-tabs">
                                                <li class="active" style="margin-left: 20px;">
                                                    <a style="font-size:13px;" href="#superadmin_subscription" class="" data-toggle="tab">
                                                        <i class="fa fa-superpowers"></i>
                                                        <strong>@lang('superadmin::lang.superadmin_subscription')</strong>
                                                    </a>
                                                </li>
                                                <li class="" style="margin-left: 20px;">
                                                    <a style="font-size:13px;" href="#family_subscription" class="" data-toggle="tab">
                                                        <i class="fa fa-users"></i>
                                                        <strong>@lang('superadmin::lang.family_subscription')</strong>
                                                    </a>
                                                </li>
                                                
                                            <li class="" style="margin-left: 20px;">
                                                <a style="font-size:13px;" href="#module_subscription" class="" data-toggle="tab">
                                                    <i class="fa fa-users"></i>
                                                    <strong>Module Subscription</strong>
                                                </a>
                                            </li>
                            
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="superadmin_subscription">
                                        @include('superadmin::superadmin_subscription.superadmin_subscription')
                                    </div>
                                    <div class="tab-pane" id="family_subscription">
                                        @include('superadmin::superadmin_subscription.family_subscription')
                                    </div>
                                     <div class="tab-pane" id="module_subscription">
                                        @include('superadmin::superadmin_subscription.module_subscription')
                                    </div>
                                </div>
                            
                                
                            </div>
                            <div class="tab-pane" id="tenant_management">
                                @component('components.widget', ['class' => 'box-primary', 'title' => __( 'superadmin::lang.all_your_tenant' )])
                                @can('superadmin::lang.create')
                                @slot('tool')
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-primary btn-modal"
                                        data-href="{{action('\Modules\Superadmin\Http\Controllers\TenantManagementController@create')}}"
                                        data-container=".tenant_modal">
                                        <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                                </div>
                                @endslot
                                @endcan
                                @can('superadmin::lang.view')
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="tenant_table" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>@lang( 'superadmin::lang.tenant' )</th>
                                                <th>@lang( 'superadmin::lang.created_at' )</th>
                                                <th class="notexport">@lang( 'messages.action' )</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                @endcan
                                @endcomponent

                                
                                
                            </div>
                            <div class="tab-pane" id="pay_online">
                                
                                @component('components.filters', ['title' => __('report.filters')])
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('date_range', __('report.date_range') . ':') !!}
                                            {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'),
                                            'class' => 'form-control', 'readonly']); !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('pay_online_no', __('superadmin::lang.pay_online_no') . ':') !!}
                                            {!! Form::select('pay_online_no', $pay_online_nos, null, ['class' => 'form-control select2',
                                            'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('status', __('superadmin::lang.status') . ':') !!}
                                            {!! Form::select('status', $status, null, ['class' => 'form-control select2',
                                            'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                                        </div>
                                    </div>
                                
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('name', __('superadmin::lang.name') . ':') !!}
                                            {!! Form::select('name', $names, null, ['class' => 'form-control select2',
                                            'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('currency', __('superadmin::lang.currency') . ':') !!}
                                            {!! Form::select('currency', $currencies, null, ['class' => 'form-control select2',
                                            'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('paid_via', __('superadmin::lang.paid_via') . ':') !!}
                                            {!! Form::select('paid_via', ['payhere' => 'Payhere', 'offline' => 'Offline'], null, ['class' =>
                                            'form-control select2',
                                            'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                                        </div>
                                    </div>
                                </div>
                                @endcomponent
                            
                            
                                @component('components.widget', ['class' => 'box-primary', 'title' => __( 'superadmin::lang.all_online_payments',
                                ['contacts' =>
                                __('superadmin::lang.') ])])
                                @if(auth()->user()->can('superadmin') )
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="pay_online_table" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>@lang('lang_v1.date')</th>
                                                <th>@lang('superadmin::lang.pay_online_no')</th>
                                                <th>@lang('superadmin::lang.reference_no')</th>
                                                <th>@lang('superadmin::lang.name')</th>
                                                <th>@lang('superadmin::lang.notes')</th>
                                                <th>@lang('superadmin::lang.amount')</th>
                                                <th>@lang('superadmin::lang.currency')</th>
                                                <th>@lang('superadmin::lang.payment_method')</th>
                                                <th>@lang('superadmin::lang.status')</th>
                                                <th class="notexport">@lang('messages.action')</th>
                            
                                            </tr>
                                        </thead>
                                        <tfoot>
                            
                                        </tfoot>
                                    </table>
                                </div>
                                @endif
                                @endcomponent
                                
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>


    <div class="modal fade brands_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>
    
    
    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>
    
    <div class="modal fade tenant_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
                                </div>
    
    
</section>
<!-- /.content -->

@endsection

@section('javascript')
<!-- START: package subscription scripts-->
<script>
    $(document).ready(function(){

                
        if ($('#expiry_date_range').length == 1) {
            $('#expiry_date_range').daterangepicker(dateRangeSettings, function(start, end) {
                $('#expiry_date_range').val(
                    start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
                );
               
            });
            $('#expiry_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#expiry_date_range').val('');
            });
            $('#expiry_date_range')
                .data('daterangepicker')
                .setStartDate(moment().startOf('year'));
            $('#expiry_date_range')
                .data('daterangepicker')
                .setEndDate(moment().endOf('year'));
        }
        
        var modules_sub_cols = [
            { data: 'name', name: 'name' },
            { data: 'business', name: 'business' },
            { data: 'status', name: 'status' },
            { data: 'activated_on', name: 'activated_on' },
            { data: 'expired_on', name: 'expired_on' },
            { data: 'price', name: 'price' },
        ];
        
        $(
            '#expiry_date_range,' +
            '#status,' +
            '#expired_on,' +
            '#modules,' +
            '#business'
        ).change(function() {
            modules_sub_table.ajax.reload();
        });
        
        modules_sub_table = $('#module_subscription_table').DataTable({
                processing: true,
                serverSide: true,
                scrollY: "75vh",
                scrollX:        true,
                scrollCollapse: true,
                ajax: {
                    url: "{{ url('module-subscription') }}",
                    data: function(d) {
                        
                        var dateRange = $('#expiry_date_range').val();
                        
                        d.start_date = dateRange.split(' - ')[0];
                        d.end_date = dateRange.split(' - ')[1];
                        d.status = $("#status").val();
                        d.expired_on = $("#expired_on").val();
                        d.module_name = $("#modules").val();
                        d.business_id = $("#business").val();
                        
                    },
                },
                columns: modules_sub_cols,
            });
                



        // superadmin_subscription_table
        var superadmin_subscription_table = $('#superadmin_subscription_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/superadmin/superadmin-subscription',
            columnDefs:[{
                    "targets": 10,
                    "orderable": false,
                    "searchable": false
                }],
            "fnDrawCallback": function (oSettings) {
                __currency_convert_recursively($('#superadmin_subscription_table'), true);
            }
        });

        // family_subscription_table
        var family_subscription_table = $('#family_subscription_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/superadmin/family-subscription',
            columnDefs:[{
                    "targets": 6,
                    "orderable": false,
                    "searchable": false
                }],
            "fnDrawCallback": function (oSettings) {
                __currency_convert_recursively($('#family_subscription_table'), true);
            }
        });


        // change_status button
        $(document).on('click', 'button.change_status', function(){
            
            $("div#statusModal").load($(this).data('href'), function(){
                $(this).modal('show');
                $("form#status_change_form, form#fs_status_change_form").submit(function(e){
                    e.preventDefault();
                    var url = $(this).attr("action");
                    var data = $(this).serialize();
                    $.ajax({
                        method: "POST",
                        dataType: "json",
                        data: data,
                        url: url,
                        success:function(result){
                            if( result.success == true){
                                $("div#statusModal").modal('hide');
                                toastr.success(result.msg);
                                superadmin_subscription_table.ajax.reload();
                                family_subscription_table.ajax.reload();
                            }else{
                                toastr.error(result.msg);
                            }
                        }
                    });
                });
            });
        });

        $(document).on('shown.bs.modal', '.view_modal', function(){
            $('.edit-subscription-modal .datepicker').datepicker({
                autoclose: true,
                format:datepicker_date_format
            });
            $("form#edit_subscription_form").submit(function(e){
              e.preventDefault();
              var url = $(this).attr("action");
              var data = $(this).serialize();
              $.ajax({
                  method: "POST",
                  dataType: "json",
                  data: data,
                  url: url,
                  success:function(result){
                      if( result.success == true){
                          $("div.view_modal").modal('hide');
                          toastr.success(result.msg);
                          superadmin_subscription_table.ajax.reload();
                          family_subscription_table.ajax.reload();
                      }else{
                          toastr.error(result.msg);
                      }
                  }
              });
            });
        });

    });
</script>
<!--END: package subscription scripts -->

<!-- start: Tenant management-->
<script>
    $(document).ready(function(){
        $(document).on('click', 'button.delete_tenant_button', function(){
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete)=>{
                if(willDelete){
                     var url = $(this).data('href');
                     $.ajax({
                         method: "delete",
                         url: url,
                         dataType: "json",
                         success: function(result){
                             if(result.success == true){
                                toastr.success(result.msg);
                                
                                tenant_table.ajax.reload();
                             }else{
                                toastr.error(result.msg);
                            }

                        }
                    });
                }
            });
        });

          // tenant_table
        tenant_table = $('#tenant_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{action('\Modules\Superadmin\Http\Controllers\TenantManagementController@index')}}",
                data: function(d){
                  
                }
            },
            columnDefs:[{
                    "targets": 2,
                    "orderable": false,
                    "searchable": false,
                    "width" : "30%",
                }],
            columns: [
                {data: 'id', name: 'id'},
                {data: 'created_at', name: 'created_at'},
                {data: 'action', name: 'action'},
               
            ]
        });
    });


</script>
<!-- END: Tenant management-->

<!--START: Pay online-->
<script>
    $('#date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            journal_table.ajax.reload();
        }
    );
    $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#date_range').val('');
        journal_table.ajax.reload();
    });


    var columns = [
            { data: 'date', name: 'date' },
            { data: 'pay_online_no', name: 'pay_online_no' },
            { data: 'reference_no', name: 'reference_no' },
            { data: 'name', name: 'name' },
            { data: 'note', name: 'note' },
            { data: 'amount', name: 'amount' },
            { data: 'currency', name: 'currency' },
            { data: 'paid_via', name: 'paid_via' },
            { data: 'status', name: 'status' },
            { data: 'action', searchable: false, orderable: false },
        ];
  
    $(document).ready(function(){
        var pay_online_table = $('#pay_online_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
            url: '{{action("\Modules\Superadmin\Http\Controllers\PayOnlineController@index")}}',
                data: function (d) {
                    if($('#date_range').val()) {
                        var start = $('#date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                        d.name = $('#name').val();
                        d.pay_online_no = $('#pay_online_no').val();
                        d.status = $('#status').val();
                        d.paid_via = $('#paid_via').val();
                        d.currency = $('#currency').val();
                    }
                }
            },
            columns: columns,
            fnDrawCallback: function(oSettings) {
            
            },
        });
        

          $('#name, #pay_online_no, #status, #paid_via, #currency, #date_range').change(function(){
            pay_online_table.ajax.reload();
          })
    })


</script>
<!-- END: Pay online -->

@endsection