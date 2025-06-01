@extends('layouts.app')

@section('title', __('vat::lang.vat_module'))

@section('content')
<!-- Main content -->
@php
                    
    $business_id = request()
        ->session()
        ->get('user.business_id');
    
    $pacakge_details = [];
        
    $subscription = Modules\Superadmin\Entities\Subscription::active_subscription($business_id);
    if (!empty($subscription)) {
        $pacakge_details = $subscription->package_details;
    }

@endphp


<section class="content">

    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs no-print">
                    
                    <li class="active">
                        <a href="#vat_settings" class="vat_settings" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.vat_report_settings')</strong>
                        </a>
                    </li>
                   
                    <li>
                        <a href="#vat_userinvoice_prefixes" class="vat_userinvoice_prefixes" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.vat_userinvoice_prefixes')</strong>
                        </a>
                    </li>
                    
                     <li>
                        <a href="#vat_userinvoice_smstypes" class="vat_userinvoice_smstypes" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.vat_userinvoice_smstypes')</strong>
                        </a>
                    </li>
                    
                    @if(!empty($pacakge_details['vat_linked_accounts']))
                         <li>
                            <a href="#vat_payable_to" class="vat_payable_to" data-toggle="tab">
                                <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.vat_payable_to')</strong>
                            </a>
                        </li>
                    @endif
                    
                    @if(!empty($pacakge_details['vat_credit_bill']))
                         <li>
                            <a href="#vat_credit_bill" class="vat_credit_bill" data-toggle="tab">
                                <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.vat_credit_bill')</strong>
                            </a>
                        </li>
                    @endif
                    
                </ul> 
                <div class="tab-content">
                    <div class="tab-pane active" id="vat_settings">
                        @include('vat::vat_settings.vat_settings')
                    </div>
                    
                    
                    <div class="tab-pane" id="vat_userinvoice_prefixes">
                        @include('vat::vat_settings.vat_userinvoice_prefixes')
                    </div>
                    
                    <div class="tab-pane" id="vat_userinvoice_smstypes">
                        @include('vat::vat_settings.vat_userinvoice_smstypes')
                    </div>
                    
                    @if(!empty($pacakge_details['vat_linked_accounts']))
                        <div class="tab-pane" id="vat_payable_to">
                            @include('vat::vat_settings.vat_payable_to_accounts')
                        </div>
                    @endif
                    
                    @if(!empty($pacakge_details['vat_credit_bill']))
                        <div class="tab-pane" id="vat_credit_bill">
                            @include('vat::vat_settings.vat_credit_bill')
                        </div>
                    @endif
                    
                    
                </div>
            </div>
        </div>
    </div>
    
    
    <div class="modal fade fuel_tank_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection
@section('javascript')
@if(!empty(session('status')) && empty(session('status')['success']))
    <script>
        toastr.error('{{session('status')['msg']}}');
    </script>
    
@endif 
<script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
<script>
    
    $(document).ready( function(){
        
        var columns = [
    
                { data: 'created_at', name: 'created_at' },
                
                { data: 'vat_period', name: 'vat_period' },
    
                { data: 'effective_date', name: 'effective_date' },
                
                { data: 'status', name: 'status',searchable: false },
                
                { data: 'tax_report_name', name: 'tax_report_name'},
                
                { data: 'username', name: 'users.username' }
            ];
    
      
    
        tank_transfers_table = $('#vat_settings_table').DataTable({
    
            processing: true,
    
            serverSide: true,
    
            aaSorting: [[0, 'desc']],
    
            ajax: {
                url: '{{action('\Modules\Vat\Http\Controllers\SettingsController@index')}}',
                data: function(d) {
                    
                },
            },
    
            @include('layouts.partials.datatable_export_button')
    
            columns: columns,
    
            fnDrawCallback: function(oSettings) {
    
            
    
            },
    
        });

});
    
</script>


<script>
    
    $(document).ready( function(){
        
        var columns = [
    
                { data: 'created_at', name: 'created_at' },
                
                { data: 'cg_name', name: 'cg_name' },
    
                { data: 'customer_name', name: 'customer_name' },
                
                { data: 'linked_accounts', name: 'linked_accounts' },
                
                { data: 'user_created', name: 'users.username' },
                
                { data: 'action', name: 'action' },
            ];
    
      
    
        vat_credit_bill_table = $('#vat_credit_bill_table').DataTable({
    
            processing: true,
    
            serverSide: true,
    
            aaSorting: [[0, 'desc']],
    
            ajax: {
                url: '{{action('\Modules\Vat\Http\Controllers\VatCreditBillController@index')}}',
                data: function(d) {
                    
                },
            },
    
            @include('layouts.partials.datatable_export_button')
    
            columns: columns,
    
            fnDrawCallback: function(oSettings) {
    
            
    
            },
    
        });

});
    
</script>

<script type="text/javascript">

    $(document).ready( function(){
        
    var columns = [

            { data: 'date_time', name: 'date_time' },
            
            { data: 'location_name', name: 'bl.name' },

            { data: 'username', name: 'users.username' },
            
            { data: 'prefix_name', name: 'vp.prefix_name' },
            
            { data: 'prefix_name2', name: 'vp2.prefix_name' },

            { data: 'user_created', name: 'uc.username' },
            
            { data: 'action', name: 'action' }
        ];

    userinvoice_prefixes_table = $('#userinvoice_prefixes_table').DataTable({

        processing: true,

        serverSide: true,

        aaSorting: [[0, 'desc']],

        ajax: {
            url: '{{action('\Modules\Vat\Http\Controllers\VatUserInvoicePrefixController@index')}}',
            data: function(d) {
                
            },
        },

        @include('layouts.partials.datatable_export_button')

        columns: columns,

        fnDrawCallback: function(oSettings) {

        

        },

    });
    
    var columns = [

            { data: 'date_time', name: 'date_time' },
            
            { data: 'sms_to', name: 'sms_to' },

            { data: 'user_created', name: 'users.username' },
            
            { data: 'action', name: 'action' }
        ];

    userinvoice_smstypes_table = $('#userinvoice_smstypes_table').DataTable({

        processing: true,

        serverSide: true,

        aaSorting: [[0, 'desc']],

        ajax: {
            url: '{{action('\Modules\Vat\Http\Controllers\VatInvoiceSmsTypeController@index')}}',
            data: function(d) {
                
            },
        },

        @include('layouts.partials.datatable_export_button')

        columns: columns,

        fnDrawCallback: function(oSettings) {

        

        },

    });
    
    
     var columns = [

            { data: 'created_at', name: 'created_at' },
            
            { data: 'type', name: 'type' },
            
            { data: 'account_name', name: 'account_name' },
            
            { data: 'amount', name: 'amount' },

            { data: 'user_created', name: 'users.username' },
            
            { data: 'note', name: 'note' },
            
            { data: 'action', name: 'action' }
        ];

    vat_payable_to_table = $('#vat_payable_to_table').DataTable({

        processing: true,

        serverSide: true,

        aaSorting: [[0, 'desc']],

        ajax: {
            url: '{{action('\Modules\Vat\Http\Controllers\VatPayableToAccountController@index')}}',
            data: function(d) {
                
            },
        },

        @include('layouts.partials.datatable_export_button')

        columns: columns,

        fnDrawCallback: function(oSettings) {

        

        },

    });
    

    var columns = [

            { data: 'prefix', name: 'prefix' },
            
            { data: 'starting_no', name: 'starting_no' },

            { data: 'user_created', name: 'users.username' },
            
            { data: 'action', name: 'action' }
        ];

    prefixes_table = $('#prefixes_table').DataTable({

        processing: true,

        serverSide: true,

        aaSorting: [[0, 'desc']],

        ajax: {
            url: '{{action('\Modules\Vat\Http\Controllers\VatPrefixController@index')}}',
            data: function(d) {
                
            },
        },

        @include('layouts.partials.datatable_export_button')

        columns: columns,

        fnDrawCallback: function(oSettings) {

        

        },

    });
    
    $(document).on('click', 'a.delete_task', function(e) {
		e.preventDefault();
		var href = $(this).data('href');
        var data = $(this).serialize();
        console.log(href);
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
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
                        prefixes_table.ajax.reload();
                        
                        tank_transfers_table.ajax.reload();
                        userinvoice_prefixes_table.ajax.reload();
                        userinvoice_smstypes_table.ajax.reload();
                        vat_payable_to_table.ajax.reload();
                    },
                });
            }
        });
    });

});

</script>
@endsection