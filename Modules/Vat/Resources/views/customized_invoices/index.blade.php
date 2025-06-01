@extends('layouts.app')

@section('title', __('vat::lang.customized_vat_invoices'))

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
                        <a href="#bank_details" class="bank_details" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.bank_details')</strong>
                        </a>
                    </li>
                    <li>
                        <a href="#supply_from" class="supply_from" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.supply_from')</strong>
                        </a>
                    </li>
                    
                    <li>
                        <a href="#vat_concerns" class="vat_concerns" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.vat_concerns')</strong>
                        </a>
                    </li>
                    
                    
                </ul> 
                <div class="tab-content">
                    <div class="tab-pane active" id="bank_details">
                        @include('vat::customized_invoices.bank_details')
                    </div>
                    
                    <div class="tab-pane" id="supply_from">
                        @include('vat::customized_invoices.supply_from')
                    </div>
                    
                    <div class="tab-pane" id="vat_concerns">
                        @include('vat::customized_invoices.vat_concerns')
                    </div>
                    
                    
                    
                    
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
    
                { data: 'bank_name', name: 'bank_name' },
                
                { data: 'bank_branch', name: 'bank_branch' },
    
                { data: 'account_number', name: 'account_number' },
                
                { data: 'account_name', name: 'account_name' },
                
                { data: 'special_instructions', name: 'special_instructions' },
                
                { data: 'status', name: 'status',searchable: false },
                
                { data: 'user_created', name: 'users.username' },
                
                { data: 'created_at', name: 'created_at' },
                
                { data: 'action', name: 'action' },
            ];
    
      
    
        bank_details_table = $('#bank_details_table').DataTable({
    
            processing: true,
    
            serverSide: true,
    
            aaSorting: [[0, 'desc']],
    
            ajax: {
                url: '{{action('\Modules\Vat\Http\Controllers\VatBankDetailController@index')}}',
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
    
                { data: 'line_1', name: 'line_1' },
                
                { data: 'line_2', name: 'line_2' },
    
                { data: 'line_3', name: 'line_3' },
                
                { data: 'line_4', name: 'line_4' },
                
                { data: 'line_5', name: 'line_5' },
                
                { data: 'status', name: 'status',searchable: false },
                
                { data: 'user_created', name: 'users.username' },
                
                { data: 'created_at', name: 'created_at' },
                
                { data: 'action', name: 'action' },
            ];
    
      
    
        vat_concerns_table = $('#vat_concerns_table').DataTable({
    
            processing: true,
    
            serverSide: true,
    
            aaSorting: [[0, 'desc']],
    
            ajax: {
                url: '{{action('\Modules\Vat\Http\Controllers\VatConcernController@index')}}',
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

           { data: 'supply_from', name: 'supply_from' },
            
           { data: 'status', name: 'status',searchable: false },
            
           { data: 'user_created', name: 'users.username' },
            
           { data: 'created_at', name: 'created_at' },
            
           { data: 'action', name: 'action' },
        ];

    supply_from_table = $('#supply_from_table').DataTable({

        processing: true,

        serverSide: true,

        aaSorting: [[0, 'desc']],

        ajax: {
            url: '{{action('\Modules\Vat\Http\Controllers\VatSupplyFromController@index')}}',
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
                        supply_from_table.ajax.reload();
                        
                        vat_concerns_table.ajax.reload();
                        bank_details_table.ajax.reload();
                    },
                });
            }
        });
    });

});

</script>
@endsection