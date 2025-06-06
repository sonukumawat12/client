@extends('layouts.app')
@section('title', __('role.add_role'))
@section('content')
    <style>
        .content h4 label {
            font-weight: inherit !important;
        }
        .blue-hr{
            border: 1px solid blue;
        }
    </style>

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang( 'role.add_role' )</h1>
    </section>
    <section class="content-header">
        @include('layouts.partials.search_settings')
    </section>
    <!-- Main content -->
    <section class="content pos-tab-container">
        @component('components.widget', ['class' => 'box-primary'])
            {!! Form::open(['url' => action('RoleController@store'), 'method' => 'post', 'id' => 'role_add_form' ]) !!}
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('name', __( 'user.role_name' ) . ':*') !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'user.role_name' ) ]);
                        !!}
                    </div>
                </div>
            </div>
            @if(in_array('service_staff', $enabled_modules))
                <div class="row">
                    <div class="col-md-2">
                        <h4><label>@lang( 'lang_v1.user_type' )</label></h4>
                    </div>
                    <div class="col-md-9 col-md-offset-1">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('is_service_staff', 1, false,
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'restaurant.service_staff' ) }}
                                </label>
                                @show_tooltip(__('restaurant.tooltip_service_staff'))
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-md-3">
                    <label>@lang( 'user.permissions' ):</label>
                </div>
            </div>
            
            <!-- new module permissions-->
            
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang('lang_v1.customer_loans'):</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <input type="checkbox" class="check_all input-icheck"> {{ __('role.select_all') }}
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'edit_customer_loan', in_array('edit_customer_loan', $role_permissions), [
                                    'class' => 'input-icheck',
                                ]) !!} {{ __('lang_v1.edit_customer_loan') }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox(
                                    'permissions[]',
                                    'delete_customer_loan',
                                    in_array('delete_customer_loan', $role_permissions),
                                    ['class' => 'input-icheck'],
                                ) !!} {{ __('lang_v1.delete_customer_loan') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="blue-hr">
            
            @if(!empty($get_permissions['bakery_module']) && $get_permissions['bakery_module'])
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang('superadmin::lang.bakery_module'):</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <input type="checkbox" class="check_all input-icheck"> {{ __('role.select_all') }}
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'bakery_login', in_array('bakery_login', $role_permissions), [
                                        'class' => 'input-icheck',
                                    ]) !!} {{ __('role.bakery_login') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox(
                                        'permissions[]',
                                        'bakery_add_loading',
                                        in_array('bakery_add_loading', $role_permissions),
                                        ['class' => 'input-icheck'],
                                    ) !!} {{ __('role.bakery_add_loading') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox(
                                        'permissions[]',
                                        'bakery_edit_loading',
                                        in_array('bakery_edit_loading', $role_permissions),
                                        ['class' => 'input-icheck'],
                                    ) !!} {{ __('role.bakery_edit_loading') }}
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox(
                                        'permissions[]',
                                        'bakery_returns',
                                        in_array('bakery_returns', $role_permissions),
                                        ['class' => 'input-icheck'],
                                    ) !!} {{ __('role.bakery_returns') }}
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox(
                                        'permissions[]',
                                        'bakery_edit_user',
                                        in_array('bakery_edit_user', $role_permissions),
                                        ['class' => 'input-icheck'],
                                    ) !!} {{ __('role.bakery_edit_user') }}
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox(
                                        'permissions[]',
                                        'bakery_add_due_amount',
                                        in_array('bakery_add_due_amount', $role_permissions),
                                        ['class' => 'input-icheck'],
                                    ) !!} {{ __('role.bakery_add_due_amount') }}
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox(
                                        'permissions[]',
                                        'bakery_make_payment',
                                        in_array('bakery_make_payment', $role_permissions),
                                        ['class' => 'input-icheck'],
                                    ) !!} {{ __('role.bakery_make_payment') }}
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox(
                                        'permissions[]',
                                        'bakery_add_user',
                                        in_array('bakery_add_user', $role_permissions),
                                        ['class' => 'input-icheck'],
                                    ) !!} {{ __('role.bakery_add_user') }}
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox(
                                        'permissions[]',
                                        'bakery_list_loading',
                                        in_array('bakery_list_loading', $role_permissions),
                                        ['class' => 'input-icheck'],
                                    ) !!} {{ __('role.bakery_list_loading') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
            @endif
            
            @if(!empty($get_permissions['list_sms']) && $get_permissions['list_sms'])
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang('lang_v1.list_sms'):</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <input type="checkbox" class="check_all input-icheck"> {{ __('role.select_all') }}
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sms_ledger', in_array('sms_ledger', $role_permissions), [
                                        'class' => 'input-icheck',
                                    ]) !!} {{ __('lang_v1.sms_ledger') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox(
                                        'permissions[]',
                                        'sms_delivery_report',
                                        in_array('sms_delivery_report', $role_permissions),
                                        ['class' => 'input-icheck'],
                                    ) !!} {{ __('lang_v1.sms_delivery_report') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox(
                                        'permissions[]',
                                        'sms_list_sms',
                                        in_array('sms_list_sms', $role_permissions),
                                        ['class' => 'input-icheck'],
                                    ) !!} {{ __('lang_v1.sms_list_sms') }}
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox(
                                        'permissions[]',
                                        'sms_history',
                                        in_array('sms_history', $role_permissions),
                                        ['class' => 'input-icheck'],
                                    ) !!} {{ __('lang_v1.sms_history') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
            @endif
            
            @if(!empty($get_permissions['bakery_module']) && $get_permissions['bakery_module'])
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang('bakery::lang.bakery_module'):</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <input type="checkbox" class="check_all input-icheck"> {{ __('role.select_all') }}
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'bakery.loading_edit', in_array('bakery.loading_edit', $role_permissions), [
                                        'class' => 'input-icheck',
                                    ]) !!} {{ __('bakery::lang.bakery.loading_edit') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
            @endif
            
            
             @if(!empty($get_permissions['smsmodule_module']) && $get_permissions['smsmodule_module'])
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang('lang_v1.smsmodule'):</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <input type="checkbox" class="check_all input-icheck"> {{ __('role.select_all') }}
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sms_quick_send', in_array('sms_quick_send', $role_permissions), [
                                        'class' => 'input-icheck',
                                    ]) !!} {{ __('lang_v1.sms_quick_send') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox(
                                        'permissions[]',
                                        'sms_from_file',
                                        in_array('sms_from_file', $role_permissions),
                                        ['class' => 'input-icheck'],
                                    ) !!} {{ __('lang_v1.sms_from_file') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox(
                                        'permissions[]',
                                        'sms_campaign',
                                        in_array('sms_campaign', $role_permissions),
                                        ['class' => 'input-icheck'],
                                    ) !!} {{ __('lang_v1.sms_campaign') }}
                                </label>
                            </div>
                        </div>
                        
                        
                    </div>
                </div>
                <hr class="blue-hr">
            @endif
            
             <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang( 'customer_payments.customer_payments' ):</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">

                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                    </div>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'list_customer_payments.delete', in_array('list_customer_payments.delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'customer_payments.delete' ) }}
                            </label>
                        </div>
                    </div>
                    
                </div>
            </div>
            <hr class="blue-hr">
            
            @if(!empty($get_permissions['vat_module']) && $get_permissions['vat_module'])
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang( 'superadmin::lang.vat_module' ):</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">

                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                    </div>
                </div>
                <div class="col-md-9">
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'vat.delete_customer_statement', in_array('vat.delete_customer_statement', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.vat.delete_customer_statement' ) }}
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'vat.delete_statement_payment', in_array('vat.delete_statement_payment', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.vat.delete_statement_payment' ) }}
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'vat_sale', in_array('vat_sale', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'superadmin::lang.vat_sale' ) }}
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'list_vat_sale', in_array('list_vat_sale', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'superadmin::lang.list_vat_sale' ) }}
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'vat_purchase', in_array('vat_purchase', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'superadmin::lang.vat_purchase' ) }}
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'list_vat_purchase', in_array('list_vat_purchase', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'superadmin::lang.list_vat_purchase' ) }}
                            </label>
                        </div>
                    </div>
                    
                     <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'vat_expense', in_array('vat_expense', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'superadmin::lang.vat_expense' ) }}
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'list_vat_expense', in_array('list_vat_expense', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'superadmin::lang.list_vat_expense' ) }}
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'vat_products', in_array('vat_products', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'superadmin::lang.vat_products' ) }}
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'vat_contacts', in_array('vat_contacts', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'superadmin::lang.vat_contacts' ) }}
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'edit.vat_statement', in_array('edit.vat_statement', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'vat::lang.edit.vat_statement' ) }}
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'vat_edit_invoice127', in_array('vat_edit_invoice127', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.vat_invoice127' ) }}
                            </label>
                        </div>
                    </div>
                    
                </div>
            </div>
            <hr class="blue-hr">
            @endif
            
            @if(!empty($get_permissions['pos_sale']) && $get_permissions['pos_sale'])
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang( 'role.pos' ):</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">

                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                    </div>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'pos.edit_pos_tax', in_array('pos.edit_pos_tax', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.pos.edit_pos_tax' ) }}
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'pos.edit_total_tax', in_array('pos.edit_total_tax', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.pos.edit_total_tax' ) }}
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'pos.edit_discount', in_array('pos.edit_discount', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.pos.edit_discount' ) }}
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'pos.edit_shipping', in_array('pos.edit_shipping', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.pos.edit_shipping' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="blue-hr">
            @endif
            
            @if(!empty($get_permissions['crm_module']) && $get_permissions['crm_module'])
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang( 'superadmin::lang.crm_module' ):</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">

                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                    </div>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'crm.access', in_array('crm.access', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.crm.access' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'crm.access_all_leads', in_array('crm.access_all_leads', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.crm.access_all_leads' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'crm.access_own_leads', in_array('crm.access_own_leads', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.crm.access_own_leads' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'crm.access_all_schedule', in_array('crm.access_all_schedule', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.crm.access_all_schedule' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'crm.access_own_schedule', in_array('crm.access_own_schedule', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.crm.access_own_schedule' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'crm.access_all_campaigns', in_array('crm.access_all_campaigns', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.crm.access_all_campaigns' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'crm.access_own_campaigns', in_array('crm.access_own_campaigns', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.crm.access_own_campaigns' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'crm.access_contact_login', in_array('crm.access_contact_login', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.crm.access_contact_login' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'crm.view_all_call_log', in_array('crm.view_all_call_log', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.crm.view_all_call_log' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'crm.view_own_call_log', in_array('crm.view_own_call_log', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.crm.view_own_call_log' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'crm.view_reports', in_array('crm.view_reports', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.crm.view_reports' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'crm.access_resources', in_array('crm.access_resources', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.crm.access_resources' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'crm.access_life_stage', in_array('crm.access_life_stage', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.crm.access_life_stage' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'crm.add_proposal_template', in_array('crm.add_proposal_template', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.crm.add_proposal_template' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'crm.access_proposal', in_array('crm.access_proposal', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.crm.access_proposal' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="blue-hr">
            @endif
            
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang( 'role.deposits_module' ):</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
            
                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
            
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'deposit.cash_deposit', in_array('deposit.cash_deposit', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.deposit.cash_deposit' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'deposit.cheque_deposit', in_array('deposit.cheque_deposit', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.deposit.cheque_deposit' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'deposit.card_deposit', in_array('deposit.card_deposit', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.deposit.card_deposit' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'deposit.access', in_array('deposit.access', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.deposit.access' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'deposit.transfer', in_array('deposit.transfer', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.deposit.transfer' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'deposit.realize_cheque', in_array('deposit.realize_cheque', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'account.realize_cheque' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="blue-hr">
            
            @if(!empty($get_permissions['hms_module']) && $get_permissions['hms_module'])
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang( 'superadmin::lang.hms_module' ):</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
            
                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
            
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'hms.manage_amenities', in_array('hms.manage_amenities', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.hms.manage_amenities' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'hms.manage_extra', in_array('hms.manage_extra', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.hms.manage_extra' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'hms.edit_booking', in_array('hms.edit_booking', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.hms.edit_booking' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'hms.add_booking', in_array('hms.add_booking', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.hms.add_booking' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'hms.manage_coupon', in_array('hms.manage_coupon', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.hms.manage_coupon' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'hms.manage_rooms', in_array('hms.manage_rooms', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.hms.manage_rooms' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'hms.manage_price', in_array('hms.manage_price', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.hms.manage_price' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'hms.manage_unavailable', in_array('hms.manage_unavailable', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.hms.manage_unavailable' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'hms.access', in_array('hms.access', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.hms.access' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="blue-hr">
            @endif
            
            @if(!empty($get_permissions['asset_module']) && $get_permissions['asset_module'])
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang( 'superadmin::lang.asset_module' ):</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
            
                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
            
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'asset.view_all_maintenance', in_array('asset.view_all_maintenance', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.asset.view_all_maintenance' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'asset.update', in_array('asset.update', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.asset.update' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'asset.delete', in_array('asset.delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.asset.delete' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'asset.create', in_array('asset.create', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.asset.create' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'asset.view_own_maintenance', in_array('asset.view_own_maintenance', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.asset.view_own_maintenance' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'asset.view', in_array('asset.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.asset.view' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="blue-hr">
            @endif
            
            @if(!empty($get_permissions['shipping_module']) && $get_permissions['shipping_module'])
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang( 'superadmin::lang.shipping_module' ):</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
            
                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
            
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'shipping.helpers.edit', in_array('shipping.helpers.edit', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.shipping.helpers.edit' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'shipping.helpers.delete', in_array('shipping.helpers.delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.shipping.helpers.delete' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'shipping.access', in_array('shipping.access', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.shipping.access' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'shipping.shipper_tracking_no.add', in_array('shipping.shipper_tracking_no.add', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.shipping.shipper_tracking_no.add' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'shipping.shipper_tracking_no.edit', in_array('shipping.shipper_tracking_no.edit', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.shipping.shipper_tracking_no.edit' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="blue-hr">
            @endif
            
            @if(!empty($get_permissions['ezyinvoice_module']) && $get_permissions['ezyinvoice_module'])
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang( 'superadmin::lang.ezyinvoice_module' ):</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
            
                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
            
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'ezyinvoice.access', in_array('ezyinvoice.access', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.ezyinvoice.access' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="blue-hr">
            @endif
            
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang( 'superadmin::lang.airline_module' ):</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
            
                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
            
                    </div>
                </div>
                
                    
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'airline.view_setting', in_array('airline.view_setting', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.airline.view_setting' ) }}
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'airline.access', in_array('airline.access', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.airline.access' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'airline_edit_invoice', in_array('airline_edit_invoice', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.airline_edit_invoice' ) }}
                                </label>
                            </div>
                        </div>
                    
                </div>
            </div>
            <hr class="blue-hr">
            
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang( 'role.user' )</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">

                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                    </div>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'user.view', in_array('user.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.user.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'user.create', in_array('user.create', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.user.create' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'user.update', in_array('user.update', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.user.update' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'user.delete', in_array('user.delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.user.delete' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="blue-hr">
            
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>Daily Report Review</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">

                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                    </div>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'DailyReviewAll', in_array('DailyReviewAll', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} Review All
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'DailyReviewOne', in_array('DailyReviewOne', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} Review
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'bypass.review', in_array('bypass.review', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} Bypass Review
                            </label>
                        </div>
                    </div>
                    
                </div>
            </div>
            <hr class="blue-hr">
            
            @if(!empty($get_permissions['spreadsheet']) && $get_permissions['spreadsheet'])
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>Spreadsheets</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">

                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                    </div>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'access.spreadsheet', in_array('access.spreadsheet', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} Access Spreadsheets
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'create.spreadsheet', in_array('create.spreadsheet', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} Create Spreadsheet
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'edit.spreadsheet', in_array('edit.spreadsheet', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} Edit Spreadsheet
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'delete.spreadsheet', in_array('delete.spreadsheet', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} Delete Spreadsheet
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'toggle.spreadsheet', in_array('toggle.spreadsheet', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} Enable / Disable Spreadsheet
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'download.spreadsheet', in_array('download.spreadsheet', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} Download Spreadsheet
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'create.folder', in_array('create.folder', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} Create Folder
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'edit.folder', in_array('edit.folder', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} Edit Folder
                            </label>
                        </div>
                    </div>
                    
                </div>
            </div>
            <hr class="blue-hr">
            @endif
            
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang( 'user.roles' )</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">

                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                    </div>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'roles.view', in_array('roles.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_role' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'roles.create', in_array('roles.create', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.add_role' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'roles.update', in_array('roles.update', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.edit_role' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'roles.delete', in_array('roles.delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_role' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="blue-hr">
            
            @if(!empty($get_permissions['contact_supplier']) && $get_permissions['contact_supplier'])
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'role.supplier' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">

                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'supplier.view', in_array('supplier.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.supplier.view' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'supplier.create', in_array('supplier.create', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.supplier.create' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'supplier.update', in_array('supplier.update', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.supplier.update' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'supplier.delete', in_array('supplier.delete', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.supplier.delete' ) }}
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'supplier_pay_due', in_array('supplier_pay_due', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.supplier_pay_due' ) }}
                                </label>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <hr class="blue-hr">
            @endif
            
            @if(!empty($get_permissions['contact_customer']) && $get_permissions['contact_customer'])    
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'role.customer' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">

                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'customer.view', in_array('customer.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.customer.view' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'customer.create', in_array('customer.create', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.customer.create' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'customer.update', in_array('customer.update', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.customer.update' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'customer.delete', in_array('customer.delete', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.customer.delete' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'customer_pay_due', in_array('customer_pay_due', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.customer_pay_due' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
            @endif
            
            @if(!empty($get_permissions['products']) && $get_permissions['products'])
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'business.product' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">

                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'product.view', in_array('product.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.product.view' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'product.create', in_array('product.create', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.product.create' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'product.update', in_array('product.update', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.product.update' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'product.delete', in_array('product.delete', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.product.delete' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'product.opening_stock', in_array('product.opening_stock',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.add_opening_stock' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'view_purchase_price', in_array('view_purchase_price',
                                    $role_permissions),['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.view_purchase_price') }}
                                </label>
                                @show_tooltip(__('lang_v1.view_purchase_price_tooltip'))
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'product.price_section', in_array('product.price_section',
                                    $role_permissions),['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.product.price_section') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
            @endif
            
            
            
            @if(!empty($get_permissions['purchase']) && $get_permissions['purchase'])
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'role.purchase' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">

                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase.view', in_array('purchase.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase.view' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase.create', in_array('purchase.create', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase.create' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase.update', in_array('purchase.update', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase.update' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase.delete', in_array('purchase.delete', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase.delete' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase.zero', $role_permissions,
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase.zero' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase.update_status', in_array('purchase.update_status',
                                    $role_permissions),['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.update_status') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'add.payments', in_array('add.payments',
                                    $role_permissions),['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.add.payments') }}
                                </label>
                            </div>
                        </div>
                        
                         <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase.edit.payments', in_array('purchase.edit.payments',
                                    $role_permissions),['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.edit_payments') }}
                                </label>
                            </div>
                        </div>
                        
                         <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase.delete.payments', in_array('purchase.delete.payments',
                                    $role_permissions),['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.delete_payments') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
            @endif

            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang( 'role.expense' )</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">

                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                    </div>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'expense.create', in_array('expense.create', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.expense.create' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'expense.update', in_array('expense.update', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.expense.update' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'expense.delete', in_array('expense.delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.expense.delete' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'expense.add_payment', in_array('expense.add_payment',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.expense.add_payment' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="blue-hr">

            @if(!empty($get_permissions['sale_module']) && $get_permissions['sale_module'])
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'sale.sale' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">

                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sell.view', in_array('sell.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.view' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sell.create', in_array('sell.create', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.create' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sell.update', in_array('sell.update', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.update' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sell.delete', in_array('sell.delete', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.delete' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'direct_sell.access', in_array('direct_sell.access', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.direct_sell.access' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'list_drafts', in_array('list_drafts', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.list_drafts' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'list_quotations', in_array('list_quotations', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.list_quotations' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'view_own_sell_only', in_array('view_own_sell_only', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_sell_only' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sell.payments', in_array('sell.payments', $role_permissions), ['class'
                                    => 'input-icheck']); !!}
                                    {{ __('lang_v1.sell.payments') }}
                                </label>
                                @show_tooltip(__('lang_v1.sell_payments'))
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'edit_product_price_from_sale_screen',
                                    in_array('edit_product_price_from_sale_screen', $role_permissions), ['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.edit_product_price_from_sale_screen') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'edit_product_price_from_pos_screen',
                                    in_array('edit_product_price_from_pos_screen', $role_permissions), ['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.edit_product_price_from_pos_screen') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'edit_product_price_below_purchase_price',
                                    in_array('edit_product_price_below_purchase_price', $role_permissions), ['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.edit_product_price_below_purchase_price') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'edit_product_discount_from_sale_screen',
                                    in_array('edit_product_discount_from_sale_screen', $role_permissions), ['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.edit_product_discount_from_sale_screen') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'edit_product_discount_from_pos_screen',
                                    in_array('edit_product_discount_from_pos_screen', $role_permissions), ['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.edit_product_discount_from_pos_screen') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'discount.access', in_array('discount.access', $role_permissions),
                                    ['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.discount.access') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'access_shipping', in_array('access_shipping', $role_permissions),
                                    ['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.access_shipping') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'pos_page_return', in_array('pos_page_return', $role_permissions),
                                    ['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.pos_page_return') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'status_order', in_array('status_order', $role_permissions),
                                    ['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.status_order') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
            @endif
    
            @if(!empty($get_permissions['brands']) && $get_permissions['brands'])
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang( 'role.brand' )</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">

                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                    </div>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'brand.view', in_array('brand.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'brand.create', in_array('brand.create', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.create' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'brand.update', in_array('brand.update', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.update' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'brand.delete', in_array('brand.delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.delete' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="blue-hr">
            @endif

            @if(!empty($get_permissions['visitors_registration_module']) && $get_permissions['visitors_registration_module'])
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang('visitors.visitor_registration' )</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'visitor.registration.create',in_array('visitor.registration.create',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_registration_create' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'visitor.registration.view',in_array('visitor.registration.view',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_registration_view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'visitor.registration.edit',in_array('visitor.registration.edit',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_registration_edit' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'visitor.registration.delete',in_array('visitor.registration.delete',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_registration_delete' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'visitor.settings.view', in_array('visitor.settings.view',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_settings_view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'visitor.settings.edit', in_array('visitor.settings.edit',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'visitors.visitor_settings_edit' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="blue-hr">
            @endif

            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang( 'role.tax_rate' )</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">

                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                    </div>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'tax_rate.view', in_array('tax_rate.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'tax_rate.create', in_array('tax_rate.create', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.create' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'tax_rate.update', in_array('tax_rate.update', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.update' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'tax_rate.delete', in_array('tax_rate.delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.delete' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="blue-hr">
            
            @if(!empty($get_permissions['products_units']) && $get_permissions['products_units'])
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang( 'role.unit' )</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">

                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                    </div>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'unit.view', in_array('unit.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'unit.create', in_array('unit.create', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.create' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'unit.update', in_array('unit.update', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.update' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'unit.delete', in_array('unit.delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.delete' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="blue-hr">
            @endif
            
            @if(!empty($get_permissions['products_categories']) && $get_permissions['products_categories'])
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang( 'category.category' )</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">

                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                    </div>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'category.view', in_array('category.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.category.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'category.create', in_array('category.create', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.category.create' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'category.update', in_array('category.update', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.category.update' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'category.delete', in_array('category.delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.category.delete' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="blue-hr">
            @endif
            
            @if(!empty($get_permissions['crm_module']) && $get_permissions['crm_module'])
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang( 'lang_v1.crm' )</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">

                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                    </div>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'crm.view', in_array('crm.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.crm.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'crm.create', in_array('crm.create', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.crm.create' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'crm.update', in_array('crm.update', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.crm.update' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'crm.delete', in_array('crm.delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.crm.delete' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="blue-hr">
            @endif
            
            <div class="row">
                <div class="col-md-1">
                    <h4><label>@lang( 'role.report' )</label></h4>
                </div>
                <div class="col-md-2">
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'report.access', in_array('report.access',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.report.access' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="blue-hr">
            
            @if(!empty($get_permissions['product_report']) && $get_permissions['product_report'])
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang( 'role.product_reports' )</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">

                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                    </div>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'stock_report.view', in_array('stock_report.view',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.stock_report.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'stock_adjustment_report.view',
                                in_array('stock_adjustment_report.view',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.stock_adjustment_report.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'item_report.view', in_array('item_report.view',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.item_report.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'product_purchase_report.view',
                                in_array('product_purchase_report.view',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.product_purchase_report.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'product_sell_report.view', in_array('product_sell_report.view',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.product_sell_report.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'product_transaction_report.view',
                                in_array('product_transaction_report.view',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.product_transaction_report.view' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="blue-hr">
            @endif
            
            @if(!empty($get_permissions['stock_adjustment']) && $get_permissions['stock_adjustment'])
            <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'role.stock_adjustments' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'stockAdjustment.add',
                                    in_array('stockAdjustment.add',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.stockAdjustment.add') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'stockAdjustment.edit', in_array('stockAdjustment.edit',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.stockAdjustment.edit') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'stockAdjustment.delete', in_array('stockAdjustment.delete',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.stockAdjustment.delete') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'stockAdjustment.list', in_array('stockAdjustment.list',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.stockAdjustment.list') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            <hr class="blue-hr">
            @endif
            
            @if(!empty($get_permissions['payment_status_report']) && $get_permissions['payment_status_report'])
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang( 'role.payment_status_reports' )</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'purchase_payment_report.view',
                                in_array('purchase_payment_report.view',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase_payment_report.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sell_payment_report.view', in_array('sell_payment_report.view',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.sell_payment_report.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'outstanding_received_report.view',
                                in_array('outstanding_received_report.view',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.outstanding_received_report.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'edit_received_outstanding',
                                in_array('edit_received_outstanding',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.edit_received_outstanding' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'delete_received_outstanding',
                                in_array('delete_received_outstanding',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.delete_received_outstanding' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'aging_report.view', in_array('aging_report.view',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.aging_report.view' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="blue-hr">
            @endif
            
            @if(!empty($get_permissions['management_reports']) && $get_permissions['management_reports'])
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang( 'role.management_reports' )</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'daily_report.view', in_array('daily_report.view',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.daily_report.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'daily_summary_report.view', in_array('daily_summary_report.view',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.daily_summary_report.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'register_report.view', in_array('register_report.view',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.register_report.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'profit_loss_report.view', in_array('profit_loss_report.view',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.profit_loss_report.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'credit_status.view', in_array('credit_status.view',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.credit_status.view' ) }}
                            </label>
                        </div>
                    </div>

                </div>
            </div>
            <hr class="blue-hr">
            @endif
            
            @if(!empty($get_permissions['verification_report']) && $get_permissions['verification_report'])
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang( 'role.verification_reports' )</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'monthly_report.view',in_array('monthly_report.view',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.monthly_report.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'comparison_report.view',in_array('comparison_report.view',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.comparison_report.view' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="blue-hr">
            @endif
            
            
            @if(!empty($get_permissions['activity_report']) && $get_permissions['activity_report'])
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang( 'role.activity_report' )</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sales_report.view', in_array('sales_report.view',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.sales_report.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'purchase_and_slae_report.view',
                                in_array('purchase_and_slae_report.view',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase_and_slae_report.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'expense_report.view', in_array('expense_report.view',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.expense_report.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sales_representative.view', in_array('sales_representative.view',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.sales_representative.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'tax_report.view', in_array('tax_report.view',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_report.view' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="blue-hr">
            @endif
            
            @if(!empty($get_permissions['contact_report']) && $get_permissions['contact_report'])
            <div class="row">
                <div class="col-md-1">
                    <h4><label>@lang( 'role.contact_report' )</label></h4>
                </div>
                <div class="col-md-2">

                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'contact_report.view', in_array('contact_report.view',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.contact_report.view' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="blue-hr">
            @endif
            
            <div class="row">
                <div class="col-md-1">
                    <h4><label>@lang( 'role.trending_products' )</label></h4>
                </div>
                <div class="col-md-2">

                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'trending_products.view', in_array('trending_products.view',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.trending_products.view' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="blue-hr">
            
            <div class="row">
                <div class="col-md-1">
                    <h4><label>@lang( 'role.user_activity' )</label></h4>
                </div>
                <div class="col-md-2">

                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'user_activity.view', in_array('user_activity.view',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.user_activity.view' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="blue-hr">
            
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang( 'role.settings' )</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">

                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                    </div>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'business_settings.access', in_array('business_settings.access',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.business_settings.access' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'barcode_settings.access', in_array('barcode_settings.access',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.barcode_settings.access' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'invoice_settings.access', in_array('invoice_settings.access',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.invoice_settings.access' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'expense.access', in_array('expense.access', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.expense.access' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'backup', in_array('backup', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.backup' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'backup.restore', in_array('backup.restore', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.restore' ) }} 
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'backup.upload', in_array('backup.upload', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.upload' ) }} 
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="blue-hr">
            
            @if(!empty($get_permissions['unfinished_form']) && $get_permissions['unfinished_form'])
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang( 'role.unfinished_form' )</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">

                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                    </div>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'unfinished_form.purchase', in_array('unfinished_form.purchase',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.unfinished_form.purchase' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'unfinished_form.sale', in_array('unfinished_form.sale',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.unfinished_form.sale' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'unfinished_form.pos', in_array('unfinished_form.pos',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.unfinished_form.pos' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'unfinished_form.stock_adjustment',
                                in_array('unfinished_form.stock_adjustment',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.unfinished_form.stock_adjustment' ) }}
                            </label>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'unfinished_form.stock_transfer',
                                in_array('unfinished_form.stock_transfer',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.unfinished_form.stock_transfer' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'unfinished_form.expense', in_array('unfinished_form.expense',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.unfinished_form.expense' ) }}
                            </label>
                        </div>
                    </div>


                </div>
            </div>
            <hr class="blue-hr">
            @endif
            
            <div class="row">
                <div class="col-md-3">
                    <h4><label>@lang( 'role.dashboard' )</label></h4>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'dashboard.data', in_array('dashboard.data', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.dashboard.data' ) }}
                            </label>
                        </div>
                    </div>
                    
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'DashboardSummaryCards', in_array('DashboardSummaryCards', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} 
                                Summary Cards Report
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'DashboardPaymentMethods', in_array('DashboardPaymentMethods', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} 
                                Payment Methods Report
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'DashboardProductCategories', in_array('DashboardProductCategories', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} 
                                Product Categories Report
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'DashboardGlance', in_array('DashboardGlance', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} 
                                Dashboard Glance Chart
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'DashboardCurrentPastGraph', in_array('DashboardCurrentPastGraph', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} 
                                Current & Previous Selection Report
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'DashboardCurrentPastPayments', in_array('DashboardCurrentPastPayments', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} 
                                Current & Previous Payments Report
                            </label>
                        </div>
                    </div>
                    
                </div>
            </div>
            <hr class="blue-hr">
            
            @if(!empty($get_permissions['payroll']) && $get_permissions['payroll'])
            <div class="row">
                <div class="col-md-3">
                    <h4><label>PayRoll</label></h4>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'payday', in_array('payday', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} PayRoll
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="blue-hr">
            @endif
            
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang( 'account.account' )</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">

                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                    </div>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'account.access', in_array('account.access', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.access_accounts' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'account.edit', in_array('account.edit', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.edit_accounts' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'account.link_account', in_array('account.link_account',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.link_account' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'account.reconcile', in_array('account.reconcile',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.reconcile' ) }}
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'account.unreconcile', in_array('account.unreconcile',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.unreconcile' ) }}
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'account.settings', in_array('account.settings',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.account_settings' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'account.settings.edit', in_array('account.settings.edit',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.account_settings_edit' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'account.deposit_transfer.edit', in_array('account.deposit_transfer.edit',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.deposit_transfer_edit' ) }}
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'edit.cheque_ob', in_array('edit.cheque_ob',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.edit_cheque_ob' ) }}
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'delete.cheque_ob', in_array('delete.cheque_ob',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_cheque_ob' ) }}
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'account.realize_cheque', in_array('account.realize_cheque', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'account.realize_cheque' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="blue-hr">
            
            @if(!empty($get_permissions['deposits_module']) && $get_permissions['deposits_module'])
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang( 'deposits.deposits' )</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">

                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                    </div>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'deposits_module', in_array('deposits_module', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'deposits.deposits_module' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'deposits.cash_deposit', in_array('deposits.cash_deposit', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'deposits.cash_deposit' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'deposits.card_deposit', in_array('deposits.card_deposit',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'deposits.card_deposit' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'deposits.cheque_deposit', in_array('deposits.cheque_deposit',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'deposits.cheque_deposit' ) }}
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'deposits.transfer', in_array('deposits.transfer',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'deposits.transfer' ) }}
                            </label>
                        </div>
                    </div>
                   
                </div>
            </div>
            <hr class="blue-hr">
            @endif
            
            @if(!empty($get_permissions['contact_module']) && $get_permissions['contact_module'])
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'contact.customer_statement' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">

                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'contact.delete_customer_statement', in_array('contact.delete_customer_statement', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.contact.delete_customer_statement' ) }}
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'contact.delete_statement_payment', in_array('contact.delete_statement_payment', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.contact.delete_statement_payment' ) }}
                                </label>
                            </div>
                        </div>
                    
                    
                    
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'enable_separate_customer_statement_no',
                                    in_array('enable_separate_customer_statement_no', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'contact.enable_separate_customer_statement_no' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'edit_customer_statement', in_array('edit_customer_statement',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'contact.edit_customer_statement' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
            @endif
            
            @if(!empty($get_permissions['edit_received_outstanding']) && $get_permissions['edit_received_outstanding'])
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'contact.edit_received_outstanding' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'edit_received_outstanding',
                                    in_array('edit_received_outstanding', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'contact.edit_received_outstanding' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'add_received_outstanding', in_array('add_received_outstanding',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'contact.add_received_outstanding' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
            @endif
            
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang( 'lang_v1.customer_reference' )</label></h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">

                        <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                    </div>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'customer_reference.edit',
                                in_array('customer_reference.edit', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.customer_reference.edit' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="blue-hr">
            
            @if(!empty($get_permissions['mpcs_module']) && $get_permissions['mpcs_module'])
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'role.MPCS' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">

                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'mpcs.access', in_array('mpcs.access',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.mpcs.access' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'f9c_form', in_array('f9c_form',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.f9c_form' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'f15a9abc_form', in_array('f15a9abc_form',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.f15a9abc_form' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'f16a_form', in_array('f16a_form',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.f16a_form' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'f21c_form', in_array('f21c_form',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.f21c_form' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'f17_form', in_array('f17_form',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.f17_form' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'f14b_form', in_array('f14b_form',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.f14b_form' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'f20_form', in_array('f20_form',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.f20_form' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'f21_form', in_array('f21_form',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.f21_form' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'f22_stock_taking_form', in_array('f22_stock_taking_form',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.f22_stock_taking_form' ) }}
                                </label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'edit_f22_stock_Taking_form', in_array('edit_f22_stock_Taking_form',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.edit_f22_stock_Taking_form' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'edit_f17_form', in_array('edit_f17_form', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.edit_f17_form' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'mpcs_form_settings', in_array('mpcs_form_settings',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.mpcs_form_settings' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'list_opening_values', in_array('list_opening_values',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.list_opening_values' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
            @endif
            
            @if(!empty($get_permissions['price_changes_module']) && $get_permissions['price_changes_module'])
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'pricechanges::lang.mpcs' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">

                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'pricechanges.access', in_array('pricechanges.access',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.pricechanges.access' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
            @endif
          
            
            @if(!empty($get_permissions['fleet_module']) && $get_permissions['fleet_module'])
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'role.fleet' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'fleet.access', in_array('fleet.access',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.fleet.access' ) }}
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'fleet.edit_trip_category', in_array('fleet.edit_trip_category',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'fleet::lang.fleet.edit_trip_category' ) }}
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'fleet.edit_trip_category', in_array('fleet.edit_trip_category',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'fleet::lang.fleet.edit_trip_category' ) }}
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'edit.fleet_opening_balance', in_array('edit.fleet_opening_balance',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.edit.fleet_opening_balance' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'fleet.add_actual_meter', in_array('fleet.add_actual_meter',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.fleet.add_actual_meter' ) }}
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'fuel_management', in_array('fuel_management',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.fleet.fuel_management' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'edit_fuel_type', in_array('edit_fuel_type',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.fleet.edit_fuel_type' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'list_trip_operations', in_array('list_trip_operations',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.fleet.list_trip_operations' ) }}
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'edit_fleet', in_array('edit_fleet',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.fleet.edit_fleet' ) }}
                                </label>
                            </div>
                        </div>
                        
                        
                    </div>
                </div>
                <hr class="blue-hr">
                
                @if(!empty($get_permissions['routes']) && $get_permissions['routes'])
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'role.routes' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'fleet.routes.edit', in_array('fleet.routes.edit',
                                    $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.edit') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'fleet.routes.delete', in_array('fleet.routes.delete',
                                    $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.delete') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
                @endif
                
                @if(!empty($get_permissions['drivers']) && $get_permissions['drivers'])
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'role.drivers' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'fleet.drivers.edit', in_array('fleet.drivers.edit',
                                    $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.edit') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'fleet.drivers.delete', in_array('fleet.drivers.delete',
                                    $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.delete') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
                @endif
                
                @if(!empty($get_permissions['helpers']) && $get_permissions['helpers'])
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'role.helpers' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'fleet.helpers.edit', in_array('fleet.helpers.edit',
                                    $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.edit') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'fleet.helpers.delete', in_array('fleet.helpers.delete',
                                    $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.delete') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
                @endif
            @endif
            @if(!empty($get_permissions['ran_module']) && $get_permissions['ran_module'])
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'role.ran' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'ran.access', in_array('ran.access',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.ran.access' ) }}
                                </label>
                            </div>
                        </div>

                    </div>
                </div>
                <hr class="blue-hr">
            @endif

            
            @if(!empty($get_permissions['catalogue_qr']) && $get_permissions['catalogue_qr'])
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang( 'role.catalogue_qr' )</label></h4>
                </div>
                <div class="col-md-2">

                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'catalogue.access', in_array('catalogue.access',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.catalogue.access' ) }}
                            </label>
                        </div>
                    </div>

                </div>
            </div>
            <hr class="blue-hr">
            @endif


            @if(!empty($get_permissions['repair_module']) && $get_permissions['repair_module'])
            <div class="row check_group">
                <div class="col-md-1">
                    <h4><label>@lang( 'role.repair' )</label></h4>
                </div>
                <div class="col-md-2">

                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'repair.access', in_array('repair.access',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.repair.access' ) }}
                            </label>
                        </div>
                    </div>

                </div>
            </div>
            <hr class="blue-hr">
            @endif


            @if(!empty($get_permissions['enable_petro_module']) && $get_permissions['enable_petro_module'])
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'role.petro' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'daily_shortage.edit', in_array('daily_shortage.edit',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'petro::lang.daily_shortage.edit' ) }}
                                </label>
                            </div>
                        </div>
                        
                        
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'daily_card.edit', in_array('daily_card.edit',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'petro::lang.daily_card.edit' ) }}
                                </label>
                            </div>
                        </div>
                        
                        
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'daily_collection.edit', in_array('daily_collection.edit',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'petro::lang.daily_collection.edit' ) }}
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'petro_sms_notifications', in_array('petro_sms_notifications',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'petro::lang.petro_sms_notifications' ) }}
                                </label>
                            </div>
                        </div>
                        
                         <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'add_day_end_settlement', in_array('add_day_end_settlement',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'petro::lang.day_end_settlement' ) }}
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'edit_day_end_settlement', in_array('edit_day_end_settlement',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'petro::lang.edit_day_end_settlement' ) }}
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'bulk_assign_pumps', in_array('bulk_assign_pumps',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'petro::lang.assign_pumps' ) }}
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'bulk_assign_pumps', in_array('bulk_assign_pumps',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'petro::lang.assign_pumps' ) }}
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'petro.access', in_array('petro.access',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.petro.access' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'fuel_tank.edit', in_array('fuel_tank.edit',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.fuel_tank.edit' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'meter_resetting_tab', in_array('meter_resetting_tab',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.meter_resetting_tab' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'add_dip_resetting', in_array('add_dip_resetting',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.add_dip_resetting' ) }}
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'dipmanagement.edit', in_array('dipmanagement.edit',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.dipmanagement_edit' ) }}
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'dipmanagement.delete', in_array('dipmanagement.delete',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.dipmanagement_delete' ) }}
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'dipmanagement.add_dip_chart', in_array('dipmanagement.add_dip_chart',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.dipmanagement_add_dip_chart' ) }}
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'dipmanagement.edit_dip_chart', in_array('dipmanagement.edit_dip_chart',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.dipmanagement_edit_dip_chart' ) }}
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'dipmanagement.delete_dip_chart', in_array('dipmanagement.delete_dip_chart',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.dipmanagement_delete_dip_chart' ) }}
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'edit_other_income_prices', in_array('edit_other_income_prices',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'petro::lang.edit_other_income_prices' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'daily_collection.delete', in_array('daily_collection.delete',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.daily_collection.delete' ) }}
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'edit_pumper_opening_balance', in_array('edit_pumper_opening_balance',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'petro::lang.edit_pumper_opening_balance' ) }}
                                </label>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <hr class="blue-hr">

                @if(!empty($get_permissions['settlement']) && $get_permissions['settlement'])
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'petro::lang.settlement' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">

                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'settlement.edit', in_array('settlement.edit', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'petro::lang.edit_settlement' ) }}
                                </label>
                            </div>
                        </div>
                       @if(!empty($get_permissions['delete_settlement']) && $get_permissions['delete_settlement'])
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'settlement.delete', in_array('settlement.delete', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'petro::lang.delete_settlement' ) }}
                                </label>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'reset_dip', in_array('reset_dip', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.reset_dip' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'manual_discount', in_array('manual_discount', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.manual_discount' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
                @endif

                @if(!empty($get_permissions['pump_operator']) && $get_permissions['pump_operator'])
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'petro::lang.pump_operator' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">

                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'pum_operator.active_inactive',
                                    in_array('pum_operator.active_inactive', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.pum_operator.active_inactive' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'pump_operator.dashboard', in_array('pump_operator.dashboard',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.pump_operator.dashboard' ) }}
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'pumper_dashboard_settings', in_array('pumper_dashboard_settings',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'petro::lang.pumper_dashboard_settings' ) }}
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'pump_operator.main_system', in_array('pump_operator.main_system',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.main_system' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'pump_operator.access_code', in_array('pump_operator.access_code',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.pump_operator.access_code' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
                @endif

                @if(!empty($get_permissions['daily_pump_status']) && $get_permissions['daily_pump_status'])
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'petro::lang.daily_pump_status' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">

                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'daily_pump_status.edit', in_array('daily_pump_status.edit',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.edit' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'daily_pump_status.delete', in_array('daily_pump_status.delete',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.delete' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
                @endif
            @endif

            @if(!empty($get_permissions['issue_customer_bill']) && $get_permissions['issue_customer_bill'])
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'role.issue_customer_bill' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'issue_customer_bill.access', in_array('issue_customer_bill.access',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.issue_customer_bill.access' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'issue_customer_bill.add', in_array('issue_customer_bill.add',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.issue_customer_bill.add' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'issue_customer_bill.view', in_array('issue_customer_bill.view',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.issue_customer_bill.view' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
            @endif

            @if(!empty($get_permissions['customer_settings']) && $get_permissions['customer_settings'])
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'role.customer_settings' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'customer_settings.access', in_array('customer_settings.access',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.customer_settings.access' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'approve_sell_over_limit', in_array('approve_sell_over_limit',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.approve_sell_over_limit' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
            @endif

            @if(!empty($get_permissions['tasks_management']) && $get_permissions['tasks_management'])
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'role.tasks_management' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'tasks_management.access', in_array('tasks_management.access',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.tasks_management.access' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'tasks_management.tasks', in_array('tasks_management.tasks',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.tasks_management.tasks' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'tasks_management.reminder', in_array('tasks_management.reminder',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.tasks_management.reminder' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
            @endif

            @if(!empty($get_permissions['member_registration']) && $get_permissions['member_registration'])
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'role.member_registration')</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'member_registration.access', in_array('member_registration.access',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.member_registration.access' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'add_remarks', in_array('add_remarks',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.add_remarks' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'update_status_of_issue', in_array('update_status_of_issue',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.update_status_of_issue' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
            @endif

            @if(!empty($get_permissions['leads_module']) && $get_permissions['leads_module'])
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'role.leads')</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'leads.view', in_array('leads.view',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.leads.view' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'leads.create', in_array('leads.create',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.leads.create' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'leads.edit', in_array('leads.edit',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.leads.edit' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'leads.delete', in_array('leads.delete',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.leads.delete' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'leads.import', in_array('leads.import',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.leads.import' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'leads.settings', in_array('leads.settings',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.leads.settings' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
                
                @if(!empty($get_permissions['day_count']) && $get_permissions['day_count'])
                <div class="row">
                    <div class="col-md-3">
                        <h4><label>@lang( 'role.day_count' )</label></h4>
                    </div>
                    <div class="col-md-9">
                        @can('day_count')
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'day_count', in_array('day_count',
                                        $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('role.day_count') }}
                                    </label>
                                </div>
                            </div>
                        @endcan
                    </div>
                </div>
                <hr class="blue-hr">
                @endif
            @endif

            @if(!empty($get_permissions['property_module']) && $get_permissions['property_module'])
                <div class="row">
                    <div class="col-md-3">
                        <h4><label>@lang( 'role.property' )</label></h4>
                    </div>
                </div>
                <hr class="blue-hr">
                
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'role.property_purchase' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'property.purchase.view', in_array('property.purchase.view',
                                    $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.view') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'property.purchase.create', in_array('property.purchase.create',
                                    $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.create') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'property.purchase.edit', in_array('property.purchase.edit',
                                    $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.edit') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'property.purchase.delete', in_array('property.purchase.delete',
                                    $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.delete') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
                
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'role.property_list' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'property.list.view', in_array('property.list.view', $role_permissions)
                                    ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.view') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'property.list.create', in_array('property.list.create',
                                    $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.create') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'property.list.edit', in_array('property.list.edit', $role_permissions)
                                    ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.edit') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'property.list.delete', in_array('property.list.delete',
                                    $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.delete') }}
                                </label>
                            </div>
                        </div>




                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'property_finalize.edit', in_array('property_finalize.edit',
                                    $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.property_finalize.edit') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'property_account_settings.edit',
                                    in_array('property_account_settings.edit', $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.property_account_settings.edit') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'property_penalty.delete', in_array('property_penalty.delete',
                                    $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.property_penalty.delete') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'list_easy_payments.access', in_array('list_easy_payments.access',
                                    $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.list_easy_payments.access') }}
                                </label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'property.approve_commission', in_array('property.approve_commission',
                                    $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.property.approve_commission') }}
                                </label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'property.update_sale_commission', in_array('property.update_sale_commission',
                                    $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.property.update_sale_commission') }}
                                </label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'property.update_commission_status', in_array('property.update_commission_status',
                                    $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.property.update_commission_status') }}
                                </label>
                            </div>
                        </div>

                    </div>
                </div>
                <hr class="blue-hr">
                
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'role.property_settings' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'property.settings.access', in_array('property.settings.access',
                                    $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.access') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'property.settings.unit', in_array('property.settings.unit',
                                    $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.unit') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'property.settings.tax', in_array('property.settings.tax',
                                    $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.tax') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
                
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'role.property_customer' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'property.customer.view', in_array('property.customer.view',
                                    $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.view') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'property.customer.create', in_array('property.customer.create',
                                    $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.create') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'property.customer.edit', in_array('property.customer.edit',
                                    $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.edit') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'property.customer.delete', in_array('property.customer.delete',
                                    $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.delete') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
                
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'role.project_dashboard' ) </label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'property.project_dashboard.sell_land_blocks',
                                    in_array('property.project_dashboard.sell_land_blocks', $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.sell_land_blocks') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'property.project_dashboard.customer_payments',
                                    in_array('property.project_dashboard.customer_payments', $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.customer_payments') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'dashboard.change',
                                    in_array('dashboard.change', $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} Permission to edit Prices in Sales dashboard
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
                
                @if(!empty($get_permissions['current_sale']) && $get_permissions['current_sale'])
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'role.current_sale' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'property.current_sale.edit', in_array('property.current_sale.edit',
                                    $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.edit') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'property.current_sale.view', in_array('property.current_sale.view',
                                    $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.view') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'property.current_sale.close.create',
                                    in_array('property.current_sale.close.create', $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.close.create') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'property.current_sale.close.edit',
                                    in_array('property.current_sale.close.edit', $role_permissions) ,
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.close.edit') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'property.add_new_sale', in_array('property.add_new_sale',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __('role.add_new_sale') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
                @endif
            @endif

            @if(!empty($get_permissions['sms_module']) && $get_permissions['sms_module'])
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'role.sms')</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sms.access', in_array('sms.access',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.sms.access' ) }}
                                </label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sms.list', in_array('sms.list',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.sms.list' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
            @endif



            @if(!empty($get_permissions['enable_cheque_writing']) && $get_permissions['enable_cheque_writing'])
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'lang_v1.enable_cheque_writing' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'enable_cheque_templates', in_array('enable_cheque_templates',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.Templates' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'enable_cheque_add_new_template', in_array('enable_cheque_add_new_template',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.Add new Template' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'enable_cheque_writing', in_array('enable_cheque_writing',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.Write Cheque' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'enable_cheque_manage_stamps', in_array('enable_cheque_manage_stamps',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.Manage Stamps' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'enable_cheque_manage_payee', in_array('enable_cheque_manage_payee',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.Manage Payee' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'enable_cheque_number_list', in_array('enable_cheque_number_list',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.Cheque Number List' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'enable_cheque_delete_numbers', in_array('enable_cheque_delete_numbers',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.Delete Cheque Numbers' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'enable_cheque_printed_details', in_array('enable_cheque_printed_details',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.Printed Cheque Details' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'enable_cheque_default_settings', in_array('enable_cheque_default_settings',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.Default Settings' ) }}
                                </label>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <hr class="blue-hr">
            @endif
            @if(in_array('tables', $enabled_modules) && in_array('service_staff', $enabled_modules) )
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'restaurant.bookings' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">

                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'crud_all_bookings', in_array('crud_all_bookings',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'restaurant.add_edit_view_all_booking' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'crud_own_bookings', in_array('crud_own_bookings',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'restaurant.add_edit_view_own_booking' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
            @endif
            
            @if(!empty($get_permissions['access_selling_price']) && $get_permissions['access_selling_price'])
            <div class="row">
                <div class="col-md-3">
                    <h4><label>@lang( 'lang_v1.access_selling_price_groups' )</label></h4>
                </div>
                <div class="col-md-9">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'access_default_selling_price',
                                in_array('access_default_selling_price',
                                $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __('lang_v1.default_selling_price') }}
                            </label>
                        </div>
                    </div>
                    @if(count($selling_price_groups) > 0)
                        @foreach($selling_price_groups as $selling_price_group)
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('spg_permissions[]', 'selling_price_group.' . $selling_price_group->id,
                                        in_array('selling_price_group.' . $selling_price_group->id, $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ $selling_price_group->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            <hr class="blue-hr">
            @endif


            @if(!empty($get_permissions['set_minimum_price']) && $get_permissions['set_minimum_price'])
                <div class="row">
                    <div class="col-md-3">
                        <h4><label>@lang( 'lang_v1.min_sell_price' )</label></h4>
                    </div>
                    <div class="col-md-9">
                        @can('product.set_min_sell_price')
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'product.set_min_sell_price', in_array('product.set_min_sell_price',
                                        $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('lang_v1.min_sell_price') }}
                                    </label>
                                </div>
                            </div>
                        @endcan
                    </div>
                </div>
                <hr class="blue-hr">
            @endif
            
            @if(!empty($get_permissions['view_sales_commission']) && $get_permissions['view_sales_commission'])
                <div class="row">
                    <div class="col-md-3">
                        <h4><label>@lang( 'lang_v1.sales_commission_agents_create' )</label></h4>
                    </div>
                    <div class="col-md-9">
                        @can('sales-commission-agents.create')
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'sales-commission-agents.create',
                                        in_array('sales-commission-agents.create',
                                        $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('lang_v1.sales_commission_agents_create') }}
                                    </label>
                                </div>
                            </div>
                        @endcan
                    </div>
                </div>
                <hr class="blue-hr">
            @endif


            @if(!empty($get_permissions['essentials_module']) && $get_permissions['essentials_module'])
            <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'essentials::lang.essentials' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                        </div>
                    </div>
                    <div class="col-md-9">
                        
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'essentials.crud_all_attendance', in_array('essentials.crud_all_attendance',
                                        $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('essentials::lang.crud_all_attendance') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'essentials.view_own_attendance', in_array('essentials.view_own_attendance',
                                        $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('essentials::lang.view_own_attendance') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'essentials.allow_users_for_attendance_from_web', in_array('essentials.allow_users_for_attendance_from_web',
                                        $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('essentials::lang.allow_users_for_attendance_from_web') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'essentials.view_allowance_and_deduction', in_array('essentials.view_allowance_and_deduction',
                                        $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('essentials::lang.view_allowance_and_deduction') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'essentials.crud_all_leave', in_array('essentials.crud_all_leave',
                                        $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('essentials::lang.crud_all_leave') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'essentials.crud_own_leave', in_array('essentials.crud_own_leave',
                                        $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('essentials::lang.crud_own_leave') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'essentials.crud_leave_type', in_array('essentials.crud_leave_type',
                                        $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('essentials::lang.crud_leave_type') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'essentials.view_all_payroll', in_array('essentials.view_all_payroll',
                                        $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('essentials::lang.view_all_payroll') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'essentials.create_payroll', in_array('essentials.create_payroll',
                                        $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('essentials::lang.create_payroll') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'essentials.update_payroll', in_array('essentials.update_payroll',
                                        $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('essentials::lang.update_payroll') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'essentials.delete_payroll', in_array('essentials.delete_payroll',
                                        $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('essentials::lang.delete_payroll') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'essentials.access_sales_target', in_array('essentials.access_sales_target',
                                        $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('essentials::lang.access_sales_target') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'essentials.edit_todos', in_array('essentials.edit_todos',
                                        $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('essentials::lang.edit_todos') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'essentials.delete_todos', in_array('essentials.delete_todos',
                                        $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('essentials::lang.delete_todos') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'essentials.add_todos', in_array('essentials.add_todos',
                                        $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('essentials::lang.add_todos') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'edit_essentials_settings', in_array('edit_essentials_settings',
                                        $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('essentials::lang.edit_essentials_settings') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'essentials.crud_department', in_array('essentials.crud_department',
                                        $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('essentials::lang.crud_department') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'essentials.crud_designation', in_array('essentials.crud_designation',
                                        $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('essentials::lang.crud_designation') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'essentials.view_all_payroll', in_array('essentials.view_all_payroll',
                                        $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('essentials::lang.view_all_payroll') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'add_essentials_leave_type', in_array('add_essentials_leave_type',
                                        $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('essentials::lang.add_essentials_leave_type') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'essentials.create_message', in_array('essentials.create_message',
                                        $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('essentials::lang.create_message') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'essentials.view_message', in_array('essentials.view_message',
                                        $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('essentials::lang.view_message') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'essentials.approve_leave', in_array('essentials.approve_leave',
                                        $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('essentials::lang.approve_leave') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'essentials.assign_todos', in_array('essentials.assign_todos',
                                        $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('essentials::lang.assign_todos') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'essentials.add_allowance_and_deduction', in_array('essentials.add_allowance_and_deduction',
                                        $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('essentials::lang.add_allowance_and_deduction') }}
                                    </label>
                                </div>
                            </div>
                         
                    </div>
                </div>
            <hr class="blue-hr">
            @endif
            
            @if(!empty($get_permissions['day_end_module']) && $get_permissions['day_end_module'])
                <div class="row check_group">
                    <div class="col-md-1">
                        <h4><label>@lang( 'lang_v1.day_end' )</label></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">

                            <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}

                        </div>
                    </div>
                    <div class="col-md-9">
                        @can('day_end.view')
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'day_end.view', in_array('day_end.view',
                                        $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('lang_v1.day_end_view') }}
                                    </label>
                                </div>
                            </div>
                        @endcan
                        @can('day_end.bypass')
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'day_end.bypass', in_array('day_end.bypass',
                                        $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('lang_v1.day_end_bypass') }}
                                    </label>
                                </div>
                            </div>
                        @endcan
                    </div>
                </div>
                <hr class="blue-hr">
            @endif

            @if(!empty($get_permissions['upload_images']) && $get_permissions['upload_images'])
                @if(auth()->user()->can('upload_images'))
                    <div class="row">
                        <div class="col-md-3">
                            <h4><label>@lang( 'lang_v1.upload_images' )</label></h4>
                        </div>
                        <div class="col-md-9">
                            @can('upload_images')
                                <div class="col-md-12">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('permissions[]', 'upload_images', in_array('upload_images',
                                            $role_permissions),
                                            [ 'class' => 'input-icheck']); !!} {{ __('lang_v1.upload_images') }}
                                        </label>
                                    </div>
                                </div>
                            @endcan
                        </div>
                    </div>
                    <hr class="blue-hr">
                @endif
            @endif

            @if(!empty($get_permissions['sms_enable']) && $get_permissions['sms_enable'])
                @if(auth()->user()->can('sms.view'))
                    <div class="row">
                        <div class="col-md-3">
                            <h4><label>@lang( 'lang_v1.sms' )</label></h4>
                        </div>
                        <div class="col-md-9">
                            @can('sms.view')
                                <div class="col-md-12">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('permissions[]', 'sms.view', in_array('sms.view',
                                            $role_permissions),
                                            [ 'class' => 'input-icheck']); !!} {{ __('lang_v1.sms_view') }}
                                        </label>
                                    </div>
                                </div>
                            @endcan
                        </div>
                    </div>
                    <hr class="blue-hr">
                @endif
            @endif

            @if(!empty($get_permissions['enable_restaurant']) && $get_permissions['enable_restaurant'])
                <div class="row">
                    <div class="col-md-3">
                        <h4><label>@lang( 'lang_v1.restaurant' )</label></h4>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'restaurant.access', in_array('restaurant.access',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __('lang_v1.access_restaurant') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
            @endif

            @if(!empty($get_permissions['cache_clear']) && $get_permissions['cache_clear'])
                <div class="row">
                    <div class="col-md-3">
                        <h4><label>@lang( 'lang_v1.clear_cache' )</label></h4>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'cache_clear', in_array('cache_clear',
                                    $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __('lang_v1.clear_cache') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="blue-hr">
            @endif

            @include('role.partials.module_permissions')
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary pull-right">@lang( 'messages.update' )</button>
                </div>
            </div>

            {!! Form::close() !!}
        @endcomponent
    </section>
    <!-- /.content -->
@endsection