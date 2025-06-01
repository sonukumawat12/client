@extends('layouts.app')
@section('title', __('account.list_realize_cheque'))
@section('content')


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
   
    
    <div class="page-title-area">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <div class="breadcrumbs-area clearfix">
                    <h4 class="page-title pull-left">@lang('account.list_realize_cheque')</h4>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main content -->
    <section class="content main-content-inner">
        
        <div class="row">
            <div class="col-md-12 p_cheque_table ">
                @component('components.widget', ['class' => 'box-primary'])
                    <table class="table table-bordered table-striped" id="realized_cheques_table" style="width: 100%;">
                        <thead>
                        <tr>
                            <th>@lang('account.cheque_date' )</th>
                            <th>@lang('lang_v1.customer_or_supplier' )</th>
                            <th>@lang('account.deposited_to' )</th>
                            <th>@lang('account.cheque_no' )</th>
                            <th>@lang('lang_v1.amount' )</th>
                            <th>@lang('lang_v1.bank' )</th>
                            <th>@lang('account.realize_date' )</th>
                           <th>@lang('contact.user')</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
                @endcomponent
            </div>
        </div>
        
    </section>
    <!-- /.content -->

@endsection
@section('javascript')
<script type="text/javascript">
    $(document).ready( function(){
        
        realized_cheques_table = $('#realized_cheques_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[1, 'desc']],
            "ajax": {
                "url": "/accounting-module/realized-cheques",
                "data": function (d) {
                    
                }
            },
            columns: [
                {data: 'cheque_date', name: 'cheque_date'},
                {data: 'customer_name', name: 'customer_name'},
                {data: 'account_number', name: 'account_number'},
                {data: 'cheque_number', name: 'cheque_number'},
                {data: 'amount', name: 'amount'},
                {data: 'bank_name', name: 'bank_name'},
                {data: 'updated_at', name: 'updated_at'},
                {data: 'username', name: 'users.username'},
            ],
            "fnDrawCallback": function (oSettings) {
                __currency_convert_recursively($('#realized_cheques_table'));
            },
        });
    
    });

  </script>
@endsection
