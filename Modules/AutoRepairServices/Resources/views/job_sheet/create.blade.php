@extends('layouts.app')

@section('title', __('autorepairservices::lang.add_job_sheet'))

@section('css')
<link rel="stylesheet" href="{{ asset('css/patternlock.min.css') }}">
@include('autorepairservices::job_sheet.tagify_css')
<link rel="stylesheet" href="{{ asset('plugins/tagify/tagify.css') }}">
@endsection


@section('content')
@include('autorepairservices::layouts.nav')
<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>
    	@lang('repair::lang.job_sheet')
        <small>@lang('repair::lang.create')</small>
    </h1>
</section>
<section class="content">
    @if(!empty($repair_settings))
        @php
            $product_conf = isset($repair_settings['product_configuration']) ? explode(',', $repair_settings['product_configuration']) : [];

            $defects = isset($repair_settings['problem_reported_by_customer']) ? explode(',', $repair_settings['problem_reported_by_customer']) : [];

            $product_cond = isset($repair_settings['product_condition']) ? explode(',', $repair_settings['product_condition']) : [];
        @endphp
    @else
        @php
            $product_conf = [];
            $defects = [];
            $product_cond = [];
        @endphp
    @endif
    {!! Form::open(['action' => '\Modules\AutoRepairServices\Http\Controllers\JobSheetController@store', 'id' => 'job_sheet_form', 'method' => 'post', 'files' => true]) !!}
        @includeIf('autorepairservices::job_sheet.partials.scurity_modal')
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    @if(count($business_locations) == 1)
                        @php
                            $default_location = current(array_keys($business_locations->toArray()));
                        @endphp
                    @else
                        @php $default_location = null;
                        @endphp
                    @endif
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('location_id', __('business.business_location') . ':*' )!!}
                            {!! Form::select('location_id', $business_locations, $default_location, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
                    <div class="col-md-4" id="customer">
                        <!--<div class="form-group">-->
                        <!--    {!! Form::label('contact_id', __('role.customer') .':*') !!}-->
                        <!--    <div class="input-group">-->
                        <!--        <input type="hidden" id="default_customer_id" value="{{ $walk_in_customer['id'] ?? ''}}" >-->
                        <!--        <input type="hidden" id="default_customer_name" value="{{ $walk_in_customer['name'] ?? ''}}" >-->
                        <!--        <input type="hidden" id="default_customer_balance" value="{{ $walk_in_customer['balance'] ?? ''}}" >-->
                        <!--        <input type="hidden" id="default_contact_id" value="{{ $contact_id ?? ''}}" >-->

                        <!--        {!! Form::select('contact_id',-->
                        <!--            [], null, ['class' => 'form-control select2', 'id' => 'customer_id', 'placeholder' => 'Select Customer', 'required', 'style' => 'width: 100%;']); !!}-->
                        <!--        <span class="input-group-btn">-->
                        <!--            <button type="button" class="btn btn-default bg-white btn-flat add_new_customer" data-name=""  @if(!auth()->user()->can('customer.create')) disabled @endif><i class="fa fa-plus-circle text-primary fa-lg"></i></button>-->
                        <!--        </span>-->
                        <!--    </div>-->
                        <!--</div>-->
                        <!--New Hagop-->
                        <div class="col-md-4" id="customer">
                                <div class="form-group">
                                    {!! Form::label('customer_name', __('role.customer') . ':*') !!}
                                    <div class="input-group">
                                        <!-- Hidden contact ID for form submission -->
                                        <input type="hidden" name="contact_id" value="{{ $contact_id ?? '' }}">
                                        
                                        <!-- Read-only customer name -->
                                        <input type="text" class="form-control" id="customer_name" value="{{ $customer_name ?? '' }}" readonly>
                            
                                        <!-- Disabled add new customer button -->
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default bg-white btn-flat add_new_customer" disabled>
                                                <i class="fa fa-plus-circle text-muted fa-lg"></i>
                                            </button>
                                        </span>
                                    </div>
                                    <!-- Optional: Display phone number -->
                                    @if(!empty($customer_phone))
                                        <small class="text-muted">📞 {{ $customer_phone }}</small>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="col-md-4" id="vehicle_number">
                                <div class="form-group">
                                    {!! Form::label('vehicle_number', __('lang_v1.vehicle_number') . ':') !!}
                                    <!-- Read-only vehicle number -->
                                    <input type="text" class="form-control" name="vehicle_number" value="{{ $vehicle_number ?? '' }}" readonly>
                                </div>
                            </div>
                         <!--New Hagop-->

                    </div>
                    <div class="col-md-5">
                        {!! Form::label('service_type',  __('repair::lang.service_type').':*', ['style' => 'margin-left:20px;'])!!}
                        <br>
                        <label class="radio-inline">
                            {!! Form::radio('service_type', 'carry_in', false, [ 'class' => 'input-icheck', 'required']); !!}
                            @lang('repair::lang.carry_in')
                        </label>
                        <label class="radio-inline">
                            {!! Form::radio('service_type', 'pick_up', false, [ 'class' => 'input-icheck']); !!}
                            @lang('repair::lang.pick_up')
                        </label>
                        <label class="radio-inline radio_btns">
                            {!! Form::radio('service_type', 'on_site', false, [ 'class' => 'input-icheck']); !!}
                            @lang('repair::lang.on_site')
                        </label>
                    </div>
                </div>
                <div class="row pick_up_onsite_addr" style="display: none;">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('pick_up_on_site_addr', __('repair::lang.pick_up_on_site_addr') . ':') !!}

                            {!! Form::textarea('pick_up_on_site_addr',null, ['class' => 'form-control  ', 'id' => 'pick_up_on_site_addr', 'placeholder' => __('repair::lang.pick_up_on_site_addr'), 'rows' => 3]); !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-solid">
            <div class="box-body">
               <div class="row">

                   <div class="col-sm-4">
                        <div class="form-group">
                            {!! Form::label('vehicle_id',' Vehicle No'. ':*' )!!}
                            <div class="input-group">
                                <!--{!! Form::select('vehicle_id',$vehicals,$last_inserted_vehicel_id, ['class' => 'form-control select2', 'placeholder' => 'Select Vehicle Number', 'required', 'style' => 'width: 100%;']); !!}-->
                                <!--changed-->
                                {!! Form::select('vehicle_id', $vehicals, old('vehicle_id', session('last_vehicle_id', $last_inserted_vehicel_id)), [
                                    'class' => 'form-control select2',
                                    'placeholder' => 'Select Vehicle Number',
                                    'required',
                                    'style' => 'width: 100%;'
                                ]) !!}
                               <!--changed-->
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default bg-white btn-flat btn-modal"
                                    data-href="{{action('VehicleController@create')}}"
                                    data-container=".brands_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                   <div class="col-md-4">

                   </div>
                   <div class="col-md-5">

                   </div>
               </div>
            </div>
        </div>

        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            {!! Form::label('brand_id', __('product.brand') . ':') !!}
                            <div class="input-group">
                            {!! Form::select('brand_id', $brands, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default bg-white btn-flat btn-modal"
                                    data-href="{{action('BrandController@create')}}"
                                    data-container=".brands_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {!! Form::label('device_id', __('repair::lang.device') . ':') !!}
                            <div class="input-group">
                                {!! Form::select('device_id', $devices, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}

                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default bg-white btn-flat btn-modal"
                                     data-href="{{url('taxonomies/create')}}?type=device" data-container=".category_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                                {!! Form::label('device_model_id', __('repair::lang.device_model') . ':') !!}
                                <div class="input-group">
                                    {!! Form::select('device_model_id', $device_models, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}

                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default bg-white btn-flat"
                                        data-href="{{action('\Modules\AutoRepairServices\Http\Controllers\DeviceModelController@create')}}" id="add_device_model"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                    </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-solid">
                            <div class="box-header with-border">
                                <h5 class="box-title">
                                    @lang('repair::lang.pre_repair_checklist'):
                                    @show_tooltip(__('repair::lang.prechecklist_help_text'))
                                    <small>
                                        @lang('repair::lang.not_applicable_key') = @lang('repair::lang.not_applicable')
                                    </small>
                                </h5>
                            </div>
                            <div class="box-body">
                                <div class="append_checklists"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('serial_no', __('repair::lang.serial_no') . ':*') !!}
                            {!! Form::text('serial_no', null, ['class' => 'form-control', 'placeholder' => __('repair::lang.serial_no'), 'required']); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                           {!! Form::label('security_pwd', __('repair::lang.repair_passcode') . ':') !!}
                            <div class="input-group">
                                {!! Form::text('security_pwd', null, ['class' => 'form-control', 'placeholder' => __('lang_v1.password')]); !!}
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-primary btn-flat" data-toggle="modal" data-target="#security_pattern">
                                        <i class="fas fa-lock"></i> @lang('repair::lang.pattern_lock')
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('product_configuration', __('repair::lang.product_configuration') . ':') !!} <br>
                           {!! Form::textarea('product_configuration', null, ['class' => 'tags-look', 'rows' => 3]); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('defects', __('repair::lang.problem_reported_by_customer') . ':') !!} <br>
                            {!! Form::textarea('defects', null, ['class' => 'tags-look', 'rows' => 3]); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('product_condition', __('repair::lang.condition_of_product') . ':') !!} <br>
                            {!! Form::textarea('product_condition', null, ['class' => 'tags-look', 'rows' => 3]); !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    @if(in_array('service_staff' ,$enabled_modules))
                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('service_staff', __('repair::lang.assign_service_staff') . ':') !!}
                                {!! Form::select('service_staff', $technecians, null, ['class' => 'form-control select2', 'placeholder' => __('restaurant.select_service_staff')]); !!}
                            </div>
                        </div>
                    @endif
                    <!----------------6029--------------------------->
                    <div class="col-sm-4">
                        <div class="form-group">
                            {!! Form::label('warranty_number', __('repair::lang.warranty_no') . ':') !!}
                            {!! Form::text('warranty_number', null, ['class' => 'form-control', 'placeholder' => __('repair::lang.warranty_no')]); !!}

                            <input type="hidden" name="status" id="" value="{{$status}}">
                        </div>
                    </div>
                    <!---------------- end 6029--------------------------->
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('comment_by_ss', __('repair::lang.comment_by_ss') . ':') !!}
                            {!! Form::textarea('comment_by_ss', null, ['class' => 'form-control ', 'rows' => '3']); !!}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {!! Form::label('estimated_cost', __('repair::lang.estimated_cost') . ':') !!}
                            {!! Form::text('estimated_cost', null, ['class' => 'form-control input_number', 'placeholder' => __('repair::lang.estimated_cost')]); !!}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="status_id">{{__('sale.status') . ':*'}}</label>
                            <select name="status_id" class="form-control status" id="status_id" required>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('delivery_date', __('repair::lang.expected_delivery_date') . ':') !!}
                            @show_tooltip(__('repair::lang.delivery_date_tooltip'))
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                {!! Form::text('delivery_date', null, ['class' => 'form-control', 'readonly']); !!}
                                <span class="input-group-addon">
                                    <i class="fas fa-times-circle cursor-pointer clear_delivery_date"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {!! Form::label('images', __('repair::lang.document') . ':') !!}
                            {!! Form::file('images[]', ['id' => 'upload_job_sheet_image', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types'))), 'multiple']); !!}
                            <small>
                                <p class="help-block">
                                Max File size: 200 KB 
                                    @includeIf('components.document_help_text')
                                </p>
                                <span id="upload-error"></span>
                            </small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>@lang('repair::lang.send_notification')</label><br>
                            <div class="checkbox-inline">
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="send_notification[]" value="sms">
                                    @lang('repair::lang.sms')
                                </label>
                            </div>
                            <div class="checkbox-inline">
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="send_notification[]" value="email">
                                    @lang('business.email')
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <hr>
                    <div class="clearfix"></div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            @php
                                $custom_field_1_label = !empty($repair_settings['job_sheet_custom_field_1']) ? $repair_settings['job_sheet_custom_field_1'] : __('lang_v1.custom_field', ['number' => 1])
                            @endphp
                            {!! Form::label('custom_field_1', $custom_field_1_label . ':') !!}
                            {!! Form::text('custom_field_1', null, ['class' => 'form-control']); !!}
                        </div>
                    </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        @php
                            $custom_field_2_label = !empty($repair_settings['job_sheet_custom_field_2']) ? $repair_settings['job_sheet_custom_field_2'] : __('lang_v1.custom_field', ['number' => 2])
                        @endphp
                        {!! Form::label('custom_field_2', $custom_field_2_label . ':') !!}
                        {!! Form::text('custom_field_2', null, ['class' => 'form-control']); !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        @php
                            $custom_field_3_label = !empty($repair_settings['job_sheet_custom_field_3']) ? $repair_settings['job_sheet_custom_field_3'] : __('lang_v1.custom_field', ['number' => 3])
                        @endphp
                        {!! Form::label('custom_field_3', $custom_field_3_label . ':') !!}
                        {!! Form::text('custom_field_3', null, ['class' => 'form-control']); !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        @php
                            $custom_field_4_label = !empty($repair_settings['job_sheet_custom_field_4']) ? $repair_settings['job_sheet_custom_field_4'] : __('lang_v1.custom_field', ['number' => 4])
                        @endphp
                        {!! Form::label('custom_field_4', $custom_field_4_label . ':') !!}
                        {!! Form::text('custom_field_4', null, ['class' => 'form-control']); !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        @php
                            $custom_field_5_label = !empty($repair_settings['job_sheet_custom_field_5']) ? $repair_settings['job_sheet_custom_field_5'] : __('lang_v1.custom_field', ['number' => 5])
                        @endphp
                        {!! Form::label('custom_field_5', $custom_field_5_label . ':') !!}
                        {!! Form::text('custom_field_5', null, ['class' => 'form-control']); !!}
                    </div>
                </div>
                <div class="col-sm-12 text-right">
                    <input type="hidden" name="submit_type" id="submit_type">
                    <button type="submit" class="btn btn-success submit_button" value="save_and_add_parts" id="save_and_add_parts">
                        @lang('repair::lang.save_and_add_parts')
                    </button>
                    <button type="submit" class="btn btn-primary submit_button" value="submit" id="save">
                        @lang('messages.save')
                    </button>
                    <button type="submit" class="btn btn-info submit_button" value="save_and_upload_docs" id="save_and_upload_docs">
                        @lang('repair::lang.save_and_upload_docs')
                    </button>
                </div>
                </div>

            </div>
        </div>
    {!! Form::close() !!} <!-- /form close -->
    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        @include('contact.create', ['quick_add' => true])
    </div>
</section>
<div class="modal fade brands_modal" tabindex="-1" role="dialog"
    	aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade" id="device_model_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>
<div class="modal fade category_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
@stop
@section('css')
    @include('autorepairservices::job_sheet.tagify_css')
@stop
@section('javascript')
    <script type="text/javascript" src="{{ asset('plugins/tagify/jQuery.tagify.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/pos.js') }}"></script>
    <script  type="text/javascript" src="{{ asset('js/patternlock.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
    const maxSize = 100 * 1024;

    $('#upload_job_sheet_image').fileinput({
        showUpload: false,
        showPreview: false,
        browseLabel: LANG.file_browse_label,
        removeLabel: LANG.remove,       
        maxFileSize: 200,
        msgSizeTooLarge: 'File "{name}" ({size} KB) exceeds maximum allowed size of 200 KB.',
    });

    $('#upload_job_sheet_image').on('change', function (e) {
        const input = this;
        const files = Array.from(input.files);
        let validFiles = [];
        let errorMessages = [];

        files.forEach(file => {
            if (file.size <= maxSize) {
                validFiles.push(file);
            } else {
                errorMessages.push(`"${file.name}" exceeds 200 KB`);
            }
        });

        if (errorMessages.length > 0) {
            $('#upload-error').html('<br>' + errorMessages.join('<br>')).css('color', 'red');
        } else {
            $('#upload-error').html('');
        }

        // If we removed any files, we need to reset and re-add only valid ones
        if (validFiles.length !== files.length) {
            const dataTransfer = new DataTransfer();
            validFiles.forEach(file => dataTransfer.items.add(file));
            input.files = dataTransfer.files;

            // Reinitialize fileinput to show remaining valid files
            $('#upload_job_sheet_image').fileinput('refresh', {
                showUpload: false,
                showPreview: false,
                initialPreviewAsData: true,
                browseLabel: LANG.file_browse_label,
                removeLabel: LANG.remove,
            });
        }
    });
});

        $(document).ready( function() {
            $(document).ready(function () {
    // Initialize select2 for the vehicle_id dropdown
    $('#vehicle_id').select2();

    // Listen for changes in the vehicle_id dropdown
    $('#vehicle_id').on('change', function () {
        const vehicleId = $(this).val(); // Get the selected vehicle ID

        if (vehicleId) {
            // Make an AJAX request to fetch customers based on the selected vehicle
            $.ajax({
                method: 'GET',
                url: '/autorepairservices/customerData/' + vehicleId, // Endpoint to fetch customer data
                success: function (result) {
                    if (Array.isArray(result) && result.length > 0) {
                        // Generate the new customer dropdown options
                        let optionsHtml = '<option value="">Select Customer</option>';
                        result.forEach(customer => {
                            optionsHtml += `<option value="${customer.id}">${customer.name}</option>`;
                        });

                        // Replace the #customer div content with the new format
                        const newCustomerHtml = `
                            <div class="form-group">
                                {!! Form::label('contact_id', __('role.customer') . ':*' ) !!}
                                {!! Form::select('contact_id', [], null, [
                                    'class' => 'form-control',
                                    'id' => 'customer_id',
                                    'placeholder' => __('messages.please_select'),
                                    'required',
                                    'style' => 'width: 70%;'
                                ]); !!}
                            </div>
                        `;

                        // Update the #customer div with the new HTML
                        $('#customer').html(newCustomerHtml);

                        // Populate the new dropdown with the fetched customer data
                        $('#customer_id').html(optionsHtml);

                        // Reinitialize select2 for the new dropdown
                        $('#customer_id').select2();
                    } else {
                        // If no customers are returned, show a default message
                        $('#customer').html('<p>No customers found for the selected vehicle.</p>');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching customer data:', error);
                    alert('Failed to fetch customer data. Please try again.');
                }
            });
        } else {
            // If no vehicle is selected, reset the #customer div to its default state
            const defaultCustomerHtml = `
                <div class="form-group">
                    {!! Form::label('contact_id', __('role.customer') . ':*' ) !!}
                    <div class="input-group">
                        <input type="hidden" id="default_customer_id" value="{{ $walk_in_customer['id'] ?? '' }}">
                        <input type="hidden" id="default_customer_name" value="{{ $walk_in_customer['name'] ?? '' }}">
                        <input type="hidden" id="default_customer_balance" value="{{ $walk_in_customer['balance'] ?? '' }}">
                        <input type="hidden" id="default_contact_id" value="{{ $contact_id ?? '' }}">

                        {!! Form::select('contact_id', [], null, [
                            'class' => 'form-control select2',
                            'id' => 'customer_id',
                            'placeholder' => 'Select Customer',
                            'required',
                            'style' => 'width: 100%;'
                        ]); !!}
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default bg-white btn-flat add_new_customer" data-name="" @if(!auth()->user()->can('customer.create')) disabled @endif>
                                <i class="fa fa-plus-circle text-primary fa-lg"></i>
                            </button>
                        </span>
                    </div>
                </div>
            `;
            $('#customer').html(defaultCustomerHtml);

            // Reinitialize select2 for the default dropdown
            $('#customer_id').select2();
        }
    });

    // Trigger the change event on page load if a vehicle_id is pre-selected
    const preSelectedVehicleId = $('#vehicle_id').val();
    if (preSelectedVehicleId) {
        $('#vehicle_id').trigger('change'); // Trigger the change event programmatically
    }
});
            $("#customer_id").on('change',function(){

                $.ajax({
                    method: 'GET',
                    url: '/autorepairservices/vehicleDetails/'+$("#customer_id").val(),
                    success: function(result) {
                        // $("#vehicle_id").html('');
                        // $("#vehicle_id").append('<option value="">Select Vehicle Number</option>');
                        // var html='';
                        // for(var i=0;i<result.length;i++)
                        // {
                        //     html+='<option value="'+result[i].id+'">'+result[i].vehicle_no+'</option>';
                        // }
                        // $("#vehicle_id").append(html);
                    }
                });

            })


                        // Listen for changes in the vehicle_id dropdown
    $('#vehicle_id').on('change', function () {
        const vehicleId = $(this).val(); // Get the selected vehicle ID

        if (vehicleId) {
            // Make an AJAX request to fetch customers based on the selected vehicle
            $.ajax({
                method: 'GET',
                url: '/autorepairservices/customerData/' + vehicleId, // Endpoint to fetch customer data
                success: function (result) {
                    if (Array.isArray(result) && result.length > 0) {
                        // Generate the new customer dropdown options
                        let optionsHtml = '<option value="">Select Customer</option>';
                        result.forEach(customer => {
                            optionsHtml += `<option value="${customer.id}">${customer.name}</option>`;
                        });

                        // Replace the #customer div content with the new format
                        const newCustomerHtml = `
                            <div class="form-group">
                                {!! Form::label('contact_id', __('role.customer') . ':*' ) !!}
                                {!! Form::select('contact_id', [], null, [
                                    'class' => 'form-control',
                                    'id' => 'customer_id',
                                    'placeholder' => __('messages.please_select'),
                                    'required',
                                    'style' => 'width: 100%;'
                                ]); !!}
                            </div>
                        `;

                        // Update the #customer div with the new HTML
                        $('#customer').html(newCustomerHtml);

                        // Populate the new dropdown with the fetched customer data
                        $('#customer_id').html(optionsHtml);

                        // Reinitialize select2 for the new dropdown
                        $('#customer_id').select2();
                    } else {
                        // If no customers are returned, show a default message
                        $('#customer').html('<p>No customers found for the selected vehicle.</p>');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching customer data:', error);
                    alert('Failed to fetch customer data. Please try again.');
                }
            });
        } else {
            // If no vehicle is selected, reset the #customer div to its default state
            const defaultCustomerHtml = `
                <div class="form-group">
                    {!! Form::label('contact_id', __('role.customer') . ':*' ) !!}
                    <div class="input-group">
                        <input type="hidden" id="default_customer_id" value="{{ $walk_in_customer['id'] ?? '' }}">
                        <input type="hidden" id="default_customer_name" value="{{ $walk_in_customer['name'] ?? '' }}">
                        <input type="hidden" id="default_customer_balance" value="{{ $walk_in_customer['balance'] ?? '' }}">
                        <input type="hidden" id="default_contact_id" value="{{ $contact_id ?? '' }}">

                        {!! Form::select('contact_id', [], null, [
                            'class' => 'form-control select2',
                            'id' => 'customer_id',
                            'placeholder' => 'Select Customer',
                            'required',
                            'style' => 'width: 100%;'
                        ]); !!}
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default bg-white btn-flat add_new_customer" data-name="" @if(!auth()->user()->can('customer.create')) disabled @endif>
                                <i class="fa fa-plus-circle text-primary fa-lg"></i>
                            </button>
                        </span>
                    </div>
                </div>
            `;
            $('#customer').html(defaultCustomerHtml);

            // Reinitialize select2 for the default dropdown
            $('#customer_id').select2();
        }
    });
            
                //             $("#vehicle_id").on('change', function() {
                //     $.ajax({
                //         method: 'GET',
                //         url: '/autorepairservices/customerData/' + $("#vehicle_id").val(),
                //         success: function(result) {
                //             if (result.length > 0) {
                //                 $("#customer_id").html(''); // use the actual id
                //                 $("#customer_id").append('<option value="">Select Customer</option>');

                //                 var html = '';
                //                 for (var i = 0; i < result.length; i++) {
                //                     html += '<option value="' + result[i].id + '">' + result[i].name + '</option>';
                //                 }
                //                 $("#customer_id").append(html);

                //                 $("#customer_id").prop('disabled', false).show();
                             
                //             }
                //         },
                //         error: function() {
                //             // Optional: handle error
                //         }
                //     });
                // });



            window.addEventListener("brandAdded", function(evt) {
                var brand = evt.detail;
                if(brand.use_for_repair == 1) {
                    var newBrand = new Option(brand.name, brand.id, true, true);
                    // Append it to the select
                    $("select#brand_id").append(newBrand);
                    $("#brand_id").val(brand.id).trigger('change');
                }

            }, false);

            window.addEventListener("categoryAdded", function(evt) {
                var device = evt.detail;
                if(device.category_type == 'device') {
                    var newDevice = new Option(device.name, device.id, true, true);
                    // Append it to the select
                    $("select#device_id").append(newDevice);
                    $("#device_id").val(device.id).trigger('change');
                }

            }, false);

            $('.submit_button').click( function(){
                $('#submit_type').val($(this).attr('value'));
            });
            $('form#job_sheet_form').validate({
                errorPlacement: function(error, element) {
                    if (element.parent('.iradio_square-blue').length) {
                        error.insertAfter($(".radio_btns"));
                    } else if (element.hasClass('status')) {
                        error.insertAfter(element.parent());
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });

            var data = [{
              id: "",
              text: '@lang("messages.please_select")',
              html: '@lang("messages.please_select")',
              is_complete : '0',
            },
            @foreach($repair_statuses as $repair_status)
                {
                id: {{$repair_status->id}},
                is_complete : '{{$repair_status->is_completed_status}}',
                @if(!empty($repair_status->color))
                    text: '<i class="fa fa-circle" aria-hidden="true" style="color: {{$repair_status->color}};"></i> {{$repair_status->name}}',
                    title: '{{$repair_status->name}}'
                @else
                    text: "{{$repair_status->name}}"
                @endif
                },
            @endforeach
            ];

            $("select#status_id").select2({
                data: data,
                escapeMarkup: function(markup) {
                    return markup;
                },
                templateSelection: function (data, container) {
                    $(data.element).attr('data-is_complete', data.is_complete);
                    return data.text;
                }
            });

            @if(!empty($default_status))
                $("select#status_id").val({{$default_status}}).change();
            @endif

                        
                            // Initialize datetimepicker
                    $('#delivery_date').datetimepicker({
                        format: moment_date_format + ' ' + moment_time_format,
                        ignoreReadonly: true,
                    });

                    // Set current date and time if empty
                    if (!$('#delivery_date').val()) {
                        let now = moment().format(moment_date_format + ' ' + moment_time_format);
                        $('#delivery_date').data("DateTimePicker").date(now);
                    }

                    // Clear button handler
                    $(document).on('click', '.clear_delivery_date', function() {
                        $('#delivery_date').data("DateTimePicker").clear();
                    });

            var lock = new PatternLock("#pattern_container", {
                onDraw:function(pattern){
                    $('input#security_pattern').val(pattern);
                },
                enableSetPattern: true
            });

            //filter device model id based on brand & device
            $(document).on('change', '#brand_id', function() {
                getModelForDevice();
                getModelRepairChecklists();
            });

            // get models for particular device
            $(document).on('change', '#device_id', function() {
                getModelForDevice();
            });

            $(document).on('change', '#device_model_id', function() {
                getModelRepairChecklists();
            });

            function getModelForDevice() {
                var data = {
                    device_id : $("#device_id").val(),
                    brand_id: $("#brand_id").val()
                };

                $.ajax({
                    method: 'GET',
                    url: '/repair/get-device-models',
                    dataType: 'html',
                    data: data,
                    success: function(result) {
                        $('select#device_model_id').html(result);
                    }
                });
            }

            function getModelRepairChecklists() {
                var data = {
                        model_id : $("#device_model_id").val(),
                    };
                $.ajax({
                    method: 'GET',
                    url: '/repair/models-repair-checklist',
                    dataType: 'html',
                    data: data,
                    success: function(result) {
                        $(".append_checklists").html(result);
                    }
                });
            }

            $('input[type=radio][name=service_type]').on('ifChecked', function(){
              if ($(this).val() == 'pick_up' || $(this).val() == 'on_site') {
                $("div.pick_up_onsite_addr").show();
              } else {
                $("div.pick_up_onsite_addr").hide();
              }
            });

            //initialize file input
            // $('#upload_job_sheet_image').fileinput({
            //     showUpload: false,
            //     showPreview: false,
            //     browseLabel: LANG.file_browse_label,
            //     maxFileSize: 100,
            //     removeLabel: LANG.remove
            // });

            //initialize tags input (tagify)
            var product_configuration = document.querySelector('textarea#product_configuration');
            tagify_pc = new Tagify(product_configuration, {
              whitelist: {!!json_encode($product_conf)!!},
              maxTags: 100,
              dropdown: {
                maxItems: 100,           // <- mixumum allowed rendered suggestions
                classname: "tags-look", // <- custom classname for this dropdown, so it could be targeted
                enabled: 0,             // <- show suggestions on focus
                closeOnSelect: false    // <- do not hide the suggestions dropdown once an item has been selected
              }
            });

            var product_defects = document.querySelector('textarea#defects');
            tagify_pd = new Tagify(product_defects, {
              whitelist: {!!json_encode($defects)!!},
              maxTags: 100,
              dropdown: {
                maxItems: 100,           // <- mixumum allowed rendered suggestions
                classname: "tags-look", // <- custom classname for this dropdown, so it could be targeted
                enabled: 0,             // <- show suggestions on focus
                closeOnSelect: false    // <- do not hide the suggestions dropdown once an item has been selected
              }
            });

            var product_condition = document.querySelector('textarea#product_condition');
            tagify_p_condition = new Tagify(product_condition, {
              whitelist: {!!json_encode($product_cond)!!},
              maxTags: 100,
              dropdown: {
                maxItems: 100,           // <- mixumum allowed rendered suggestions
                classname: "tags-look", // <- custom classname for this dropdown, so it could be targeted
                enabled: 0,             // <- show suggestions on focus
                closeOnSelect: false    // <- do not hide the suggestions dropdown once an item has been selected
              }
            });

            //TODO:Uncomment the below code

            // function toggleSubmitButton () {
            //     if ($('select#status_id').find(':selected').data('is_complete')) {
            //         $("#save_and_add_parts").attr('disabled', false);
            //         $("#save_and_upload_docs").attr('disabled', true);
            //         $("#save").attr('disabled', false);
            //     } else {
            //         $("#save_and_add_parts").attr('disabled', true);
            //         $("#save_and_upload_docs").attr('disabled', false);
            //         $("#save").attr('disabled', true);
            //     }
            // }

            // $("select#status_id").on('change', function () {
            //     toggleSubmitButton();
            // });

            // toggleSubmitButton();
        });

        $(document).on('click', '#add_device_model', function () {
            var url = $(this).data('href');
            $.ajax({
                method: 'GET',
                url: url,
                dataType: 'html',
                success: function(result) {
                    $('#device_model_modal').html(result).modal('show');
                }
            });
        });

        $(document).on('submit', 'form#device_model', function(e){
            e.preventDefault();
            var url = $('form#device_model').attr('action');
            var method = $('form#device_model').attr('method');
            var data = $('form#device_model').serialize();
            $.ajax({
                method: method,
                dataType: "json",
                url: url,
                data:data,
                success: function(result){
                    if (result.success) {
                        $('#device_model_modal').modal("hide");
                        toastr.success(result.msg);
                        var model = result.data;
                        var newModel= new Option(model.name, model.id, true, true);
                        // Append it to the select
                        $("select#device_model_id").append(newModel);
                        $("#device_model_id").val(model.id).trigger('change');

                    } else {
                        toastr.error(result.msg);
                    }
                }
            });
        });
    </script>
    @includeIf('taxonomy.taxonomies_js', ['cat_code_enabled' => false])
@endsection
