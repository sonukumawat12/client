<form action="{{action('\Modules\Airline\Http\Controllers\AirlineTicketingController@store_invoice')}}" method="POST" id="invoice_add_form">
    @csrf
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('airline_invoice_no', __( 'airline::lang.airline_invoice_no' ) . '') !!}
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline_invoice_no')}}">
                            <i class="fa fa-file-text"></i>
                        </button>
                    </div>
                    {!! Form::text('airline_invoice_no', null, [
                    'class' => 'form-control mousetrap',
                    'id' =>'airline_invoice_no',
                    'placeholder' => __('airline::lang.airline_invoice_no_placeholder'),
                    '']); !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('vat_invoice', __( 'airline::lang.vat_invoice' ) . '') !!}
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.customer_group')}}">
                            <i class="fa fa-users"></i>
                        </button>
                    </div>
                    {!! Form::select('vat_invoice', ["Yes", "No"], null, ['class' => 'form-control', 'id' => 'vat_invoice', '',
                    'placeholder' => __('lang_v1.please_select')]); !!}
                </div>
            </div>
            <div class="form-group ">
                {!! Form::label('airticket_no', __( 'PNR / Ticket No' ) . ':*') !!}
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airticket_no')}}">
                            <i class="fa fa-ticket"></i>
                        </button>
                    </div>
                    {!! Form::text('airticket_no', null, [
                    'class' => 'form-control mousetrap',
                    'id' =>'airticket_no',
                    'placeholder' => __('airline::lang.airticket_no_placeholder'),
                    'required']); !!}
                </div>
            </div>
            <div class="form-group ">
                {!! Form::label('departure_date', __( 'airline::lang.departure_date' ) . ':*') !!}
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                            <i class="fa fa-calendar"></i>
                        </button>
                    </div>
                    {!! Form::text('departure_date', @format_date(date('Y-m-d')), ['class' => 'form-control datepicker', 'id' => 'departure_date', 'required',
                    'placeholder' => __('airline::lang.departure_date_placeholder')]); !!}
                </div>
            </div>

            <div class="form-group ">
                {!! Form::label('arrival_airport', __( 'airline::lang.arrival_airport' ) . ':*') !!}
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                            <i class="fa fa-plane"></i>
                        </button>
                    </div>
                    {!! Form::select('arrival_airport', [], null, ['class' => 'form-control', 'id' => 'arrival_airport_select', 'required',
                    'placeholder' => __('airline::lang.arrival_airport_placeholder')]); !!}
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat" id="create_arrival_airport">
                            <i class="fa fa-plus-circle text-primary fa-lg"></i>
                        </button>
                    </span>
                </div>
            </div>



        </div>
        <div class="col-md-3">

            <div class="form-group ">
                {!! Form::label('customer_group', __( 'airline::lang.customer_group' ) . ':*') !!}
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.customer_group')}}">
                            <i class="fa fa-users"></i>
                        </button>
                    </div>
                    {!! Form::select('customer_group', [], null, ['class' => 'form-control', 'id' => 'customer_group_select', 
                    'required', 'data-type' => $type ?? 'customer',
                    'placeholder' => __('airline::lang.customer_group_placeholder')]); !!}
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat btn-modal" data-href="{{action('ContactGroupController@create')}}?type=customer" data-container=".contact_group_modal">
                            <i class="fa fa-plus-circle text-primary fa-lg"></i>
                        </button>
                    </span>
                </div>
            </div>
            <div class="form-group ">
                {!! Form::label('supplier_id', __('purchase.supplier') . ':') !!}
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.customer')}}">
                            <i class="fa fa-user"></i>
                        </button>
                    </div>
                    {!! Form::select('supplier', $airline_supplier, null, ['class' => 'form-control select2', 'id' => 'supplier_select', '']) !!}
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat btn-modal" data-href="/contacts/create?type=supplier" data-container=".contact_modal">
                            <i class="fa fa-plus-circle text-primary fa-lg"></i>
                        </button>
                    </span>
                </div>
                <div id="wallet_amount hide"></div>
            </div>
            <div class="form-group ">
                {!! Form::label('travel_mode', __( 'airline::lang.travel_mode' ) . ':*') !!}
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.travel_mode')}}">
                            <i class="fa fa-info"></i>
                        </button>
                    </div>
                    {!! Form::select('travel_mode', ['One Way' => 'One Way', 'Return' => 'Return', 'Multicity' => 'Multi City'], null, ['class' => 'form-control', 'id' => 'travel_mode_select', 'required']); !!}
                </div>
            </div>
            <div class="form-group ">
                {!! Form::label('transit', __( 'airline::lang.transit' ) . ':*') !!}
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                            <i class="fa fa-hand-pointer"></i>
                        </button>
                    </div>
                    {!! Form::select('transit', [1 => 'Yes', 0 => 'No'], 0, ['class' => 'form-control', 'id' => 'transit_select', 'required']); !!}
                </div>
            </div>

            <div class="form-group ">
                {!! Form::label('arrival_date', __( 'airline::lang.arrival_date' ) . ':*') !!}
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                            <i class="fa fa-calendar"></i>
                        </button>
                    </div>
                    {!! Form::text('arrival_date', @format_date(date('Y-m-d')), ['class' => 'form-control datepicker', 'id' => 'arrival_date', 'required',
                    'placeholder' => __('airline::lang.arrival_date_placeholder')]); !!}
                </div>
            </div>


        </div>
        <div class="col-md-3">

            <div class="form-group ">
                {!! Form::label('customer', __( 'airline::lang.customer' ) . ':*') !!}
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.customer')}}">
                            <i class="fa fa-user"></i>
                        </button>
                    </div>
                    {!! Form::select('customer', $customers, null, ['class' => 'form-control select2', 'id' => 'customer_select', 'required','placeholder' => __('lang_v1.please_select')]); !!}
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat btn-modal" data-href="/contacts/create?type=customer&form_id=airline_contact_add_form" data-container=".contact_modal">
                            <i class="fa fa-plus-circle text-primary fa-lg"></i>
                        </button>
                    </span>
                </div>
            </div>

            <div class="form-group ">
                {!! Form::label('airline', __( 'airline::lang.airline' ) . ':*') !!}
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                            <i class="fa fa-plane"></i>
                        </button>
                    </div>
                    {!! Form::select('airline', [], null, ['class' => 'form-control', 'id' => 'airline_select', 'required',
                    'placeholder' => __('airline::lang.airline_placeholder')]); !!}
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat" id="create_airline">
                            <i class="fa fa-plus-circle text-primary fa-lg"></i>
                        </button>
                    </span>
                </div>
            </div>
            <div class="form-group ">
                {!! Form::label('departure_country', __( 'airline::lang.departure_country' ) . ':*') !!}
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                            <i class="fa fa-globe"></i>
                        </button>
                    </div>
                    {!! Form::select('departure_country', [], null, ['class' => 'form-control', 'id' => 'departure_country_select', 'required',
                    'placeholder' => __('airline::lang.departure_country_placeholder')]); !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('arrival_country', __( 'airline::lang.arrival_country' ) . ':*') !!}
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                            <i class="fa fa-globe"></i>
                        </button>
                    </div>
                    {!! Form::select('arrival_country', [], null, ['class' => 'form-control', 'id' => 'arrival_country_select', 'required',
                    'placeholder' => __('airline::lang.arrival_country_placeholder')]); !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('arrival_time', __( 'airline::lang.arrival_time' ) . '') !!}
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                            <i class="fa fa-clock"></i>
                        </button>
                    </div>
                    {!! Form::input('time', 'arrival_time', '12:00', ['class' => 'form-control timepicker', 'id' => 'arrival_time', 'required',
                    'placeholder' => __('airline::lang.arrival_time_placeholder')]); !!}
                </div>
            </div>


        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('vat_number', __( 'airline::lang.customer_vat_no' ) . '') !!}
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.customer_vat_no')}}">
                            <i class="fa fa-user"></i>
                        </button>
                    </div>
                    {!! Form::text('vat_number', null, ['class' => 'form-control', 'id' => 'customer_vat_number','placeholder' => __('airline::lang.customer_vat_no')]); !!}
                    <input type="hidden" id="vat_btn_input">
                    <span class="input-group-btn vat-btn-group ">
                        <button type="button" class="btn btn-default bg-white btn-flat btn-vat-modal vat-btn-group-action" data-href="" data-container=".contact_modal_noreload">
                            <i class="fa fa-plus-circle text-primary fa-lg"></i>
                        </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('airline_agent', __( 'airline::lang.airline_agent' ) . ':*') !!}
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                            <i class="fa fa-link"></i>
                        </button>
                    </div>
                    {!! Form::select('airline_agent', [], null, ['class' => 'form-control', 'id' => 'airline_agent_select', 'required',
                    'placeholder' => __('airline::lang.airline_agent_placeholder')]); !!}
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat" id="create_airline_agent">
                            <i class="fa fa-plus-circle text-primary fa-lg"></i>
                        </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('departure_airport', __( 'airline::lang.departure_airport' ) . ':*') !!}
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                            <i class="fa fa-plane"></i>
                        </button>
                    </div>
                    {!! Form::select('departure_airport', [], null, ['class' => 'form-control', 'id' => 'departure_airport_select', 'required',
                    'placeholder' => __('airline::lang.departure_airport_placeholder')]); !!}
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat" id="create_departure_airport">
                            <i class="fa fa-plus-circle text-primary fa-lg"></i>
                        </button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <div id="multiCityTableContainer"></div>
            </div>


            <div class="form-group col-sm-4 transit_airport_field hide">
                {!! Form::label('transit_airport', __( 'airline::lang.transit_airport' ) . '') !!}
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                            <i class="fa fa-plane"></i>
                        </button>
                    </div>
                    {!! Form::select('transit_airport', [], null, ['class' => 'form-control', 'id' => 'transit_airport_select', '',
                    'placeholder' => __('airline::lang.transit_airport_placeholder')]); !!}
                </div>
            </div>



        </div>
    </div>

    <div class="row" style="margin-bottom: 10px;">
        <button type="button" class="btn btn-primary btn-modal pull-right" id="create_passenger" data-href="{{action('\Modules\Airline\Http\Controllers\AirlineTicketingController@create_passenger')}}" data-container=".passenger_modal">
            <i class="fa fa-plus"></i>
            Add Passenger
        </button>
    </div>

    <div class="row">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="passenger_table" style="width:100%!important">
                <thead>
                    <tr>
                        <th width="12%">{{__('airline::lang.passenger_name')}} </th>
                        <th width="12%">{{__('airline::lang.passport_no')}}</th>
                        <th width="13%">{{__('airline::lang.image')}}</th>
                        <th width="13%">{{__('airline::lang.airticket_no')}}</th>
                        <th width="15%">{{__('airline::lang.frequent_flyer_no')}}</th>
                        <th width="11%">{{__('airline::lang.child')}}</th>
                        <th width="11%">{{__('airline::lang.price')}}</th>
                        <th width="11%">{{__('airline::lang.passenger_type')}}</th>
                        <th width="11%">{{__('airline::lang.amount')}}</th>
                        <th width="11%">{{__('airline::lang.total_amount')}}</th>
                        <th width="11%">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr class="bg-gray font-17 footer-total text-center">
                        <td colspan="4"><strong>@lang('sale.total'):</strong></td>
                        <td colspan="4" id="total_price">0
                        </td>
                    </tr>
                    <input type="hidden" id="tot_price" name="tot_price" required>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="row" style="margin-bottom: 20px;">
        <button type="button" class="btn btn-primary" id="saveInvoiceButton" onclick="submitInvoiceForm()">Save Invoice</button>
    </div>
</form>


<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Success</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="alertContainer" class="alert alert-success" role="alert">
                    Invoice created successfully!
                </div>
                <div id="buttonContainer">
                    <a href="javascript:void(0)" class="btn btn-primary choices" style="margin: 5px;" id="saveAndPrint">Print</a>
                    <a href="javascript:void(0)" class="btn btn-primary choices" style="margin: 5px;" id="saveAndPrintWithoutSupplier">Print without Supplier Payment Details</a>
                    <a href="javascript:void(0)" class="btn btn-success choices" style="margin: 5px;" id="sendInvoiceEmail">Send invoice by Email</a>
                    <a href="javascript:void(0)" class="btn btn-danger choices" style="margin: 5px;" id="sendInvoiceWhatsApp">Send invoice by WhatsApp (PDF)</a>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Modal for viewing Passport Image -->
<div class="modal fade" id="passportImageModal" tabindex="-1" role="dialog" aria-labelledby="passportImageModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="passportImageModalLabel">Passport Image</h4>
            </div>
            <div class="modal-body">
                <img id="passportImage" src="" alt="Passport Image" style="width: 100%;">
            </div>
        </div>
    </div>
</div>

<!-- Modal for viewing Airline Itinerary (Image or PDF) -->
<div class="modal fade" id="airlineItineraryModal" tabindex="-1" role="dialog" aria-labelledby="airlineItineraryModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="airlineItineraryModalLabel">Airline Itinerary</h4>
            </div>
            <div class="modal-body">
                <!-- You can load the image or PDF here -->
                <div id="airlineItineraryContent"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="airport_form_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form id="airport_form">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="airport_title">Create Airport</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-sm-12">
                            {!! Form::label('date_added', __( 'airline::lang.date_added' ) . ':*') !!}
                            {!! Form::text('date_added', @format_date(date('Y-m-d')), ['class' => 'form-control','required',
                            'placeholder' => __('airline::lang.date_added' )]); !!}
                        </div>
                        <div class="form-group col-sm-12">
                            {!! Form::label('country_select', __( 'airline::lang.country_select' ) .":*") !!}
                            {!! Form::select('country_select', [], null, ['class' => 'form-control', 'id' => 'country_select', 'required', 'disabled',
                            'placeholder' =>__('messages.please_select')]); !!}
                        </div>

                        <div class="form-group col-sm-12">
                            {!! Form::label('province_select', __( 'airline::lang.province_select' ) .":*") !!}
                            {!! Form::text('province_select', null, ['class' => 'form-control', 'id' => 'province_select', 'required',
                            'placeholder' => __('airline::lang.province') ]); !!}
                        </div>
                        <div class="form-group col-sm-12">
                            {!! Form::label('airport_name', __( 'airline::lang.airport_name' ) . ':*') !!}
                            {!! Form::text('airport_name', null, ['class' => 'form-control', 'id' => 'airport_name', 'required',
                            'placeholder' => __('airline::lang.airport_name'), ]); !!}
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="airline_form_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form id="airline_form">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="airline_form_title">Create Airline</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="airline" class="col-md-4 col-form-label text-md-right">Airline:*</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="airline" id="airline" autocomplete="name" autofocus>
                            <span class="invalid-feedback" role="alert">Min 3 characters</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="agent_form_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form id="agent_form">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="agent_form_title">Create Airline Agent</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="agent" class="col-md-4 col-form-label text-md-right">Agent:*</label>

                        <div class="col-md-6">
                            <input type="text" class="form-control" id="agent" name="agent" autocomplete="name" autofocus>
                            <span class="invalid-feedback" role="alert">Min 3 characters</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade contact_group_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
</div>

<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
</div>

<div class="modal fade contact_modal_noreload" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
</div>

<div class="modal fade passenger_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
</div>

<div class="modal fade invoice_payment_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
</div>
<div class="modal fade invoice_payment_modal_supplier" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
</div>

<div class="modal fade" id="multiCityModal" tabindex="-1" role="dialog" aria-labelledby="multiCityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title" id="multiCityModalLabel">Multi City Travel</h5>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" method="POST" id="multiCityForm">

                    <p>Add or remove origin and destination pairs below</p>

                    <div id="flight-rows">
                        <div class="row mb-3 flight-row pt-4" id="flight-row-1" style="padding-top: 10px ">

                            <div class="col-md-1">
                                <label class="text-wrap">Flight 1</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control origin" required placeholder="From (Origin)">

                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control departure_date_modal" id="" required placeholder="Departure Date" value="9/14/2024">
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control destination" required placeholder="To (Destination)">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3 " style="padding-bottom: 10px ">
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3 " style="padding-top: 10px ">
                                <button type="button" class="btn btn-primary" id="add-flight-btn">Add Flight</button>
                                <button type="button" class="btn btn-secondary" id="remove-flight-btn">Remove Flight</button>
                            </div>

                        </div>

                    </div>



                    <div class="row mb-3 " style="padding-bottom: 10px ">

                        <div class="col-md-1">
                        </div>
                        <div class="col-md-11">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label>Class</label>
                                    <select class="form-control">
                                        @foreach($airline_class as $class)
                                        <option value="{{$class->id}}">{{$class->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Adults</label>
                                    <input type="number" placeholder="Adults" class="form-control" value="1">
                                </div>
                                <div class="col-md-3">
                                    <label>Children</label>
                                    <input type="number" placeholder="Children" class="form-control" value="0">
                                </div>
                                <div class="col-md-3">
                                    <label>Infants</label>
                                    <input type="number" placeholder="Infants" class="form-control" value="0">
                                </div>
                            </div>

                        </div>
                    </div>


            </div>


            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close_modal" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="">Save</button>
            </div>

            </form>
        </div>
    </div>
</div>

@section('javascript')
<script>
    let flightCount = 1;

    function submitInvoiceForm() {
        // Get the form element
        let form = $('#invoice_add_form');

        // Check if the form is valid
        if (form[0].checkValidity()) {
            // If valid, proceed with the AJAX request
            $.ajax({
                url: form.attr('action'), // Form's action URL
                type: 'POST',
                data: form.serialize(), // Serialize form data
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                }, // Include CSRF token
                success: function(response) {
                    $('#successModal').modal('show');
                },
                error: function(xhr) {
                    alert('Failed to create invoice. Please try again.');
                }
            });
        } else {
            // If the form is invalid, display validation errors
            form[0].reportValidity();
        }
    }




    $(document).ready(function() {


        $('.btn.btn-primary.choices').on('click', function(e) {
            e.preventDefault();

            let form = $('#invoice_add_form');
            let action = $(this).attr('id'); // Get the ID of the clicked button
            let excludeSupplierDetails = (action === 'saveAndPrintWithoutSupplier'); // Set a flag
            console.log(excludeSupplierDetails)

            if (form[0].checkValidity()) {
                $.ajax({
                    url: "{{ url('airline/create_invoice/invoice/print') }}",
                    type: "POST",
                    data: form.serialize() + '&exclude_supplier_details=' + excludeSupplierDetails, // Include the flag
                    dataType: "html",
                    beforeSend: function() {
                        $('#dynamic-content').html("");
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#dynamic-content').html(response).show();

                        let images = $('#dynamic-content').find('img');
                        let totalImages = images.length;
                        let loadedImages = 0;

                        if (totalImages === 0) {
                            window.print();
                            $('#dynamic-content').hide();
                            window.location.href = "/airline/ticketing";
                        } else {
                            function checkAllImagesLoaded() {
                                if (loadedImages === totalImages) {
                                    window.print();
                                    $('#dynamic-content').hide();
                                    window.location.href = '/airline/ticketing';
                                }
                            }

                            images.each(function() {
                                if (this.complete) {
                                    loadedImages++;
                                    checkAllImagesLoaded();
                                } else {
                                    $(this).on('load', function() {
                                        loadedImages++;
                                        checkAllImagesLoaded();
                                    }).on('error', function() {
                                        loadedImages++;
                                        checkAllImagesLoaded();
                                    });
                                }
                            });
                        }
                    },
                    error: function() {
                        alert("Failed to load the invoice content.");
                    }
                });
            } else {
                form[0].reportValidity();
            }
        });


        $('#sendInvoiceEmail').on('click', function(e) {
            e.preventDefault();

            let form = $('#invoice_add_form');
            if (form[0].checkValidity()) {
                $.ajax({
                    url: "{{url('airline/email/invoice')}}", // Update this to your route
                    type: 'POST',
                    data: form.serialize(),
                    dataType: "html",
                    beforeSend: function() {
                        $('#dynamic-content').html("");
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {

                        if (response.success) {
                            alert(response.msg);
                            window.location.href = "{{url('/airline/ticketing')}}";

                        } else {
                            alert('Error: ' + response.msg);
                        }
                    },
                    error: function(xhr) {
                        alert('An error occurred: ' + xhr.status + ' ' + xhr.statusText);
                    }
                });
            } else {
                form[0].reportValidity();
            }

        });

        $('#sendInvoiceWhatsApp').on('click', function(e) {
            e.preventDefault();

            let form = $('#invoice_add_form'); // The form with invoice data
            if (form[0].checkValidity()) {
                $.ajax({
                    url: "{{url('/airline/whatsapp/invoice')}}", // Your route to generate PDF
                    type: 'POST',
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(response) {

                        if (response.success) {

                            let phone = response.contact; // Customer's phone number
                            let pdfUrl = response.pdf_url; // PDF link from the server
                            let message = encodeURIComponent("Hello, your invoice is ready. You can download it from the following link: " + pdfUrl);

                            // Open WhatsApp with the pre-filled message in a new tab
                            let whatsappUrl = `https://wa.me/${phone}?text=${message}`;
                            let whatsappWindow = window.open(whatsappUrl, '_blank'); // Opens WhatsApp

                            // Check if the WhatsApp window opened successfully
                            if (whatsappWindow) {
                                whatsappWindow.focus(); // Focus on the new window if it opens

                                // Redirect to the invoices list page after a short delay (to allow WhatsApp window to open)
                                whatsappWindow.onload = function() {
                                    window.location.href = "{{url('/airline/ticketing')}}";
                                };
                            } else {
                                // If the window didn't open, alert the user and then redirect manually
                                alert("Unable to open WhatsApp. Please check your browser's popup settings.");
                                window.location.href = "{{url('/airline/invoices')}}"; // Fallback redirection
                            }
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        alert('An error occurred: ' + xhr.status + ' ' + xhr.statusText);
                    }
                });
            } else {
                form[0].reportValidity();
            }
        });


        $('.close_modal').on('click', function() {
            $('#multiCityModal').modal('hide');
        })

        function resetMultiCityForm() {
            $('#multiCityForm input').val('');

            $('#multiCityForm select').prop('selectedIndex', 0);

            $('.departure_date_modal').datepicker('setDate', new Date());

            $('.flight-row:not(:first)').remove();

            flightCount = 1;

            $('#flight-row-1 input').val('');
            $('#flight-row-1 .departure_date_modal').datepicker('setDate', new Date());
        }




        initializeDatepicker('.departure_date_modal');

        $('.departure_date_modal').datepicker('setDate', new Date());

        $('#add-flight-btn').on('click', function() {
            flightCount++;

            const flightRow = `
                <div class="row mb-3 flight-row" id="flight-row-${flightCount}"  style="padding-top: 10px ">
                    <div class="col-md-1">
                        <label>Flight ${flightCount}</label>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control origin" required placeholder="From (Origin)">
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control departure_date_modal" required placeholder="Departure Date">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control destination" required placeholder="To (Destination)">
                    </div>
                </div>
            `;

            $('#flight-rows').append(flightRow);

            initializeDatepicker(`#flight-row-${flightCount} .departure_date_modal`);
        });



        $('#remove-flight-btn').on('click', function() {
            if (flightCount > 1) {
                $('#flight-row-' + flightCount).remove();
                flightCount--;
            }
        });


        function initializeDatepicker(selector) {
            $(selector).datepicker({
                format: 'mm/dd/yyyy',
                autoclose: true,
                startDate: new Date()
            });
        }



        $('#travel_mode_select').on('change', function() {
            let selectedValue = $(this).val();
            if (selectedValue == 'Multicity') {
                $('#multiCityModal').modal('show');
            }
        });


        $('#multiCityForm').on('submit', function() {
            let flightData = [];
            $('.flight-row').each(function() {
                let origin = $(this).find('.origin').val();
                let destination = $(this).find('.destination').val();
                let departureDate = $(this).find('.departure_date_modal').val();
                flightData.push({
                    origin,
                    destination,
                    departureDate
                });
            });

            let travelClass = $('#multiCityModal select').val();
            let adults = $('#multiCityModal input[placeholder="Adults"]').val();
            let children = $('#multiCityModal input[placeholder="Children"]').val();
            let infants = $('#multiCityModal input[placeholder="Infants"]').val();

            // Create table rows
            let tableRows = '';
            flightData.forEach((flight, index) => {
                tableRows += `
                    <tr>
                       
                        <td>${index + 1}</td>
                        <td>${flight.origin}</td>
                        <td>${flight.destination}</td>
                        <td>${flight.departureDate}</td>
                        <td>${travelClass}</td>
                        <td>${adults}</td>
                        <td>${children}</td>
                        <td>${infants}</td>
                    </tr>
                `;
            });


            let table = `
                <input type="hidden" name="multicity[]" value='` + JSON.stringify(flightData) + `' >
                <input type="hidden" name="multi_class" value='` + travelClass + `' >
                <input type="hidden" name="multi_adults" value='` + adults + `' >
                <input type="hidden" name="multi_children" value='` + children + `' >
                <input type="hidden" name="multi_infants" value='` + infants + `' >
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>Flight</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Departure Date</th>
                            <th>Class</th>
                            <th>Adults</th>
                            <th>Children</th>
                            <th>Infants</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${tableRows}
                    </tbody>
                </table>
            `;


            $('#multiCityTableContainer').html(table);

            $('#multiCityModal').modal('hide');

            resetMultiCityForm();
        });


        // Listen for the change event on the supplier select box
        $('#supplier_select').on('change', function() {
            // Get the selected supplier ID
            var supplierId = $(this).val();

            // Make an Ajax request to fetch the wallet amount based on the selected supplier
            $.ajax({
                url: '/airline/get_wallet_amount', // Replace with the actual URL to fetch the wallet amount
                method: 'GET',
                data: {
                    supplierId: supplierId
                },
                success: function(response) {
                    // Update the wallet amount in the HTML
                    $('#wallet_amount').text('Wallet Amount: ' + response.amounts); // Adjust this line based on the response structure
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching wallet amount:', error);
                    $('#wallet_amount').text('Wallet Amount: ' + '0.0'); // Adjust this line based on the response structure
                }
            });
        });
        $('#supplier_select').trigger("change");
        
        
        $('#customer_group_select').select2({
            placeholder: "{{__('airline::lang.customer_group_placeholder')}}",
            ajax: {
                url: "{{url('contact-group/search')}}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    var query = {
                        q: params.term,
                        type: $('#customer_group_select').data('type'),
                        business_id: "{{$business_id}}"
                    }

                    // Query parameters will be ?search=[term]&type=public
                    return query;
                },
                processResults: function (data) {
                    data = data.data;
                    const results = data.map(item => ({ id: item.id, text: item.name }));                        
                    
                    return {
                        results: results
                    };
                }
            }
        });
    });
    const createticket_module = {
        selected_modal: null,
        init: function() {

            this.init_airlines();
            this.init_airline_agents();
            this.init_countries();
            this.init_airports(null, $('#transit_airport_select'));

            $('#departure_date').datepicker('setDate', new Date());
            $('#arrival_date').datepicker('setDate', new Date());


            this.listener();
        },
        init_airlines: function() {
            $.ajax({
                url: "{{action('\Modules\Airline\Http\Controllers\AirlineTicketingController@airlines')}}",
                method: 'GET',
                success: function(airlines) {
                    console.log(airlines);
                    $('#airline_select').empty();
                    var defaultOption = $('<option>').text("{{__('airline::lang.airline_placeholder')}}").val("");

                    $('#airline_select').append(defaultOption);

                    Object.keys(airlines).forEach(id => {
                        var newOption = $('<option>').text(airlines[id]).val(parseInt(id));
                        $('#airline_select').append(newOption);
                    })
                    $('#airline_select').select2();
                },
                error: function(error) {
                    console.log(error);
                }
            });
        },
        init_airline_agents: function() {
            $.ajax({
                url: "{{action('\Modules\Airline\Http\Controllers\AirlineTicketingController@airline_agents')}}",
                method: 'GET',
                success: function(airlines) {
                    $('#airline_agent_select').empty();

                    var defaultOption = $('<option>').text("{{__('airline::lang.airline_agent_placeholder')}}").val("");
                    $('#airline_agent_select').append(defaultOption);

                    if (Object.keys(airlines).length > 0) {
                        Object.keys(airlines).forEach(id => {
                            var newOption = $('<option>').text(airlines[id]).val(parseInt(id));
                            $('#airline_agent_select').append(newOption);
                        });

                        $('#airline_agent_select').select2();
                    }

                },
                error: function(error) {
                    console.log(error);
                }
            });
        },
        init_countries: function() {
            fetch("{{url('/airline/get-countries')}}")
                .then(response => response.json())
                .then(data => {

                    $('#departure_country_select').empty();
                    $('#arrival_country_select').empty();
                    $('#country_select').empty();
                    data.forEach(country => {
                        $('#departure_country_select').append($('<option>').text(country.country).val(country.country));
                        $('#arrival_country_select').append($('<option>').text(country.country).val(country.country));
                        $('#country_select').append($('<option>').text(country.country).val(country.country));
                    });
                    $('#departure_country_select').select2({
                        minimumResultsForSearch: 0, // Set this to 0 to always display the search input
                        placeholder: 'Select departure country', // Placeholder text
                        allowClear: true, // Allow clearing the selection
                        // Add any other options you need
                    });
                    $('#arrival_country_select').select2({
                        minimumResultsForSearch: 0, // Set this to 0 to always display the search input
                        placeholder: 'Select departure country', // Placeholder text
                        allowClear: true, // Allow clearing the selection
                        // Add any other options you need
                    });
                    if (data.length > 0) {
                        $('#departure_country_select').val(data[0].name).trigger('change');
                        $('#arrival_country_select').val(data[0].name).trigger('change');
                        $('#country_select').val(data[0].name).trigger('change');
                    }
                })
                .catch(error => {
                    console.log(error);
                })
        },
        init_airports: function(country, component, is_arrival = false) {

            $(component).empty();

            var defaultOption = $('<option>').text("{{__('lang_v1.please_select')}}").val("");
            $(component).append(defaultOption);


            // if(country){
            $.ajax({
                url: "{{action('\Modules\Airline\Http\Controllers\AirlineTicketingController@airline_airports')}}",
                method: 'GET',
                data: {
                    country
                },
                success: function(airports) {


                    Object.keys(airports).forEach(id => {
                        if ($("#departure_airport_select").val() == id && is_arrival == true) {

                        } else {
                            var newOption = $('<option>').text(airports[id].airport_name).val(parseInt(id));
                            $(component).append(newOption);
                        }

                    })

                },
                error: function(error) {
                    console.log(error);
                }
            });
            // }


            component.select2();
        },



        listener: function() {

            function get_customer_fin_data() {
                $('.user_fin_info').html('');
                $('.user_fin_info').hide('');

                //console.log($(this).val());
                var contactId = $('#customer_select').val();
                console.log(contactId);

                if (contactId) {

                    //already disable button
                    //$(".vat-btn-group").removeClass('hide');

                    $.ajax({
                        url: "{{action('\Modules\Airline\Http\Controllers\AirlineTicketingController@get_customer_fin_information_by_contact_id')}}",
                        method: 'GET',
                        data: {
                            contactId: contactId,
                        },
                        success: function(data) {
                            console.log(data);
                            var openingBalance;
                            var creditLimit;
                            if (data.opening_balance === null) {
                                openingBalance = 0;
                            }
                            if (data.opening_balance !== null) {
                                openingBalance = parseInt(data.opening_balance);
                            }
                            var openingBalanceInfo = '<b>Opening Balance: </b>' + openingBalance;

                            if (data.credit_limit === null) {
                                creditLimit = 0;
                            }
                            if (data.credit_limit !== null) {
                                creditLimit = parseInt(data.credit_limit);
                            }
                            console.log(openingBalance, creditLimit);
                            var creditLimitInfo = '<b>Credit Limit: </b>' + creditLimit;
                            $('.user_fin_info').append(openingBalanceInfo);
                            var br = '</br>';
                            $('.user_fin_info').append(br);
                            $('.user_fin_info').append(creditLimitInfo);
                            $('.user_fin_info').show();

                            // TODO: add vat number

                            $("#customer_vat_number").val(data.vat_number);

                            $(".vat-btn-group").addClass('disabled');
                            if (!data.vat_number) {
                                $(".vat-btn-group").removeClass('disabled');
                            }

                        },
                        error: function(err) {
                            console.log(err);
                        }
                    })

                    $("#vat_btn_input").val(contactId);

                } else {
                    $(".vat-btn-group").addClass('hide');
                    $("#customer_vat_number").val("");
                }

            }
            /*Func Def*/

            $('.contact_modal').on('hidden.bs.modal', function() {
                $('#customer_group_select').change();
            });

            $('#customer_group_select').change(function() {
                $('#customer_select').html('');
                var customer_group_id = $(this).val();

                if (!customer_group_id) {
                    customer_group_id = 0;
                }

                if (!isNaN(parseInt(customer_group_id))) {
                    $.ajax({
                        url: "{{action('\Modules\Airline\Http\Controllers\AirlineTicketingController@customers_by_group_id')}}",
                        method: 'GET',
                        data: {
                            customer_group_id
                        },
                        success: function(customers) {
                            if (Object.keys(customers).length > 0) {

                                var defaultOption = $('<option>').text("{{__('lang_v1.please_select')}}").val("");
                                $('#customer_select').append(defaultOption);


                                $.each(customers, function(key, value) {
                                    var newOption = $('<option>');
                                    //console.log('Key:' + key + 'Value:' + value);
                                    newOption.text(value);
                                    newOption.val(parseInt(key));
                                    $('#customer_select').append(newOption);
                                })

                                // Initialize Select2 on the customer_select element
                                $('#customer_select').select2();

                                $('#customer_select').trigger('change');
                            }
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                } else {
                    $('#customer_select').empty();
                }
            });
            
            /*
            "#customer_select" On Change Event Listener
            */
            $('#customer_select').change(function() {
                get_customer_fin_data();
            })

            /*
            "#airticket_no_prefix_id" On Change Event Listener
            */
            $('#airticket_no_prefix_id').change(function() {
                var prefix = $(this).val();


                $('#airticket_no_prefix_value').val(prefix);
                //console.log(prefix);

            })

            /*
            "#airline_select" On Change Event Listener
            */

            $('#airline_select').change(function() {

                $('.user_fin_info').html('');
                $('.user_fin_info').hide('');

                //console.log($(this).val());
                var contactId = $('#customer_select').val();
                console.log(contactId);

                $.ajax({
                    url: "{{action('\Modules\Airline\Http\Controllers\AirlineTicketingController@get_customer_fin_information_by_contact_id')}}",
                    method: 'GET',
                    data: {
                        contactId: contactId,
                    },
                    success: function(data) {
                        var openingBalance;
                        var creditLimit;
                        if (data.opening_balance === null) {
                            openingBalance = 0;
                        }
                        if (data.opening_balance !== null) {
                            openingBalance = parseInt(data.opening_balance);
                        }
                        var openingBalanceInfo = '<b>Opening Balance: </b>' + openingBalance;

                        if (data.credit_limit === null) {
                            creditLimit = 0;
                        }
                        if (data.credit_limit !== null) {
                            creditLimit = parseInt(data.credit_limit);
                        }
                        console.log(openingBalance, creditLimit);
                        var creditLimitInfo = '<b>Credit Limit: </b>' + creditLimit;
                        $('.user_fin_info').append(openingBalanceInfo);
                        var br = '</br>';
                        $('.user_fin_info').append(br);
                        $('.user_fin_info').append(creditLimitInfo);
                        $('.user_fin_info').show();

                    },
                    error: function(err) {
                        console.log(err);
                    }
                })

            });

            $('#airline_agent_select').change(function() {

                get_customer_fin_data();

            })


            $('#departure_country_select').change(function() {
                var country = $(this).val();
                createticket_module.init_airports(country, $('#departure_airport_select'));
            });

            $('#arrival_country_select').change(function() {
                var country = $(this).val();
                createticket_module.init_airports(country, $('#arrival_airport_select'), true);
            });
            $('#note').hide();
            $('#transit_select').change(function() {
                var is_transit = $(this).val();
                if (parseInt(is_transit)) {
                    document.getElementById('transit_airport_select').disabled = false;
                    $(".transit_airport_field").removeClass('hide');
                    $('#note').show()
                    document.getElementById('note').display = 'block';
                } else {
                    document.getElementById('transit_airport_select').disabled = true;
                    $(".transit_airport_field").addClass('hide');
                    $('#note').hide()
                }
            });
            $('#transit_select').trigger("change");

            $('#create_departure_airport').click(function() {
                $('#date_added').datepicker('setDate', new Date());
                $('#country_select').val($('#departure_country_select').val()).trigger('change');
                $('#province_select').val('');
                $('#airport_name').val('');
                createticket_module.selected_modal = $('#departure_airport_select');
                $('#airport_form_modal').modal('show');
            });

            $('#create_arrival_airport').click(function() {
                $('#date_added').datepicker('setDate', new Date());
                $('#country_select').val($('#arrival_country_select').val()).trigger('change');
                $('#province_select').val('');
                $('#airport_name').val('');
                createticket_module.selected_modal = $('#arrival_airport_select');
                $('#airport_form_modal').modal('show');
            });

            $('#airport_form').submit((e) => {
                e.preventDefault();

                const data = {};
                const inputs = $('#airport_form .form-control');
                for (var i = 0; i < inputs.length; i++) {
                    const value = $(inputs[i]).val();
                    if (value) {
                        data[inputs[i].name] = value;
                    }
                }
                console.log(data);
                $.ajax({
                    url: '/airline/add_airport',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: data,
                    success: function(data, b, c) {
                        if (data.statusText == 'Success') {
                            toastr.success(data.message);
                            $('#airport_form_modal').modal('hide');
                            $(createticket_module.selected_modal).append($('<option>').text(data.data.airport_name).val(data.data.id))
                            $('#transit_airport_select').append($('<option>').text(data.data.airport_name).val(data.data.id))
                            $(createticket_module.selected_modal).val(data.data.id).trigger('change');
                        }
                    },
                    error: function(data, b, c) {
                        toastr.error(data.message);
                    }
                });
            });

            $('#create_airline').click(function() {
                $('#airline').val('');
                createticket_module.selected_modal = $('#airline_select');
                $('#airline_form_modal').modal('show');
            });

            $('#airline_form').submit((e) => {
                e.preventDefault();

                $.ajax({
                    url: '/airline/add_airline',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        airline: $('#airline').val()
                    },
                    success: function(data, b, c) {
                        if (data.statusText == 'Success') {
                            toastr.success(data.message);
                            $('#airline_form_modal').modal('hide');
                            $(createticket_module.selected_modal).append($('<option>').text(data.data.airline).val(data.data.id))
                            $(createticket_module.selected_modal).val(data.data.id).trigger('change');
                        }
                    },
                    error: function(data, b, c) {}
                });
            })

            $('#create_airline_agent').click(function() {
                $('#agent').val('');
                createticket_module.selected_modal = $('#airline_agent_select');
                $('#agent_form_modal').modal('show');
            });

            $('#agent_form').submit((e) => {
                e.preventDefault();

                $.ajax({
                    url: '/airline/add_agent',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        agent: $('#agent').val()
                    },
                    success: function(data, b, c) {
                        if (data.statusText == 'Success') {
                            toastr.success(data.message);
                            $('#agent_form_modal').modal('hide');
                            $(createticket_module.selected_modal).append($('<option>').text(data.data.agent).val(data.data.id))
                            $(createticket_module.selected_modal).val(data.data.id).trigger('change');
                        }
                    },
                    error: function(data, b, c) {}
                });
            })


        }
    }

    $(document).ready(() => {
        createticket_module.init();

        $('.invoice_payment_modal').on('shown.bs.modal', function() {

            $('.payment-amount').val(parseInt(__number_uf($('#tot_price').val()), 10));
            console.log($('.payment-amount').val())
        });

        $('.invoice_payment_modal_supplier').on('shown.bs.modal', function() {

            $('.payment-amount').val(parseInt(__number_uf($('#tot_price').val()), 10));
            console.log($('.payment-amount').val())
        });
    });





    $('div.invoice_payment_modal').on('hidden.bs.modal', function() {

        calculate_balance_due();
    });

    $('div.invoice_payment_modal_supplier').on('hidden.bs.modal', function() {

        calculate_balance_due();
    });

    function calculate_balance_due() {
        var total_payable = parseInt(__number_uf($('#total_price').text()), 10);
        var total_paying = 0;
        $('.pmt-amount')
            .each(function() {

                console.log($(this).val());

                if (parseFloat($(this).val())) {
                    total_paying += __read_number($(this));
                }
            });
        var bal_due = total_payable - total_paying;


        if (bal_due != 0) {
            $("#save").hide();
        } else {
            $("#save").show();
        }


        $('#payment_due').text(__currency_trans_from_en(bal_due, false, false));
    }
    $(document).on('click', '.closing_contact_modal', function() {
        $('.passenger_contact_modal').modal('hide');
        // $('.contact_modal').modal('hide');
    })

    $(document).on('click', '#update_vat_number', function(e) {
        e.preventDefault();

        if ($("#update_fields_type").val() == 'nic_number') {
            var data = {
                'nic_number': $("#add_nic_number").val()
            };
        } else if ($("#update_fields_type").val() == 'mobile') {
            var data = {
                'mobile': $("#add_mobile").val()
            };
        } else {
            if ($("#is_single_field").val() == 'yes') {
                var data = {
                    'vat_number': $("#main_add_vat_number").val()
                };
            } else {
                var data = {
                    'vat_number': $("#add_vat_number").val()
                };
            }

        }


        $.ajax({
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: data,
            url: $('#contact_vat_number_form').attr('action'),
            success: function(result) {
                if (result.success == true) {
                    $('div.contact_modal_noreload').modal('hide');
                    toastr.success(result.msg);

                    // Get the currently selected option
                    var select = $('#passenger_name_select');
                    var selectedOption = select.find('option:selected');

                    // Update attributes of the selected option
                    selectedOption.attr('passport_id', result.contact.nic_number);
                    selectedOption.attr('phone_no', result.contact.mobile);
                    selectedOption.attr('vat_number', result.contact.vat_number);
                    selectedOption.attr('passport_image', result.contact.image);

                    if ($("#update_fields_type").val() == 'nic_number') {
                        $("#passport_number_text").val(result.contact.nic_number);
                    } else if ($("#update_fields_type").val() == 'mobile') {
                        $("#passenger_mobile_text").val(result.contact.mobile);
                    } else {
                        $("#customer_vat_number").val(result.contact.vat_number);
                        $("#passenger_vat_number_text").val(result.contact.vat_number);
                    }

                    $('.passenger_contact_modal').modal('hide');


                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });

    $(document).on('click', '.btn-vat-modal', function(e) {

        e.preventDefault();

        var url = '/contacts/update-vatnumber/' + $("#vat_btn_input").val();


        console.log(url);

        var container = $(this).data('container');

        $(container).empty();

        $.ajax({

            url: url,

            dataType: 'html',

            success: function(result) {
                // var contact = $('#default_contact_id').val();
                $(container).html(result).modal('show');
                // $(container).find('input#contact_id').val(contact);
            },

        });

    });

    $(document).ready(function() {
        $('#departure_airport_select').change(function() {
            let selectedValue = $(this).val();

            if (selectedValue && $('#arrival_country_select').val()) {
                createticket_module.init_airports($('#arrival_country_select').val(), $('#arrival_airport_select'), true);
            }
        });
    });

    $(document).on('click', '.btn-modal-popup', function(e) {

        e.preventDefault();

        var type = $(this).data('string');

        var url = '/contacts/update-vatnumber/' + $("#passenger_name_select").val() + '?is_single=yes&type=' + type;


        console.log(url);

        var container = $(this).data('container');

        $(container).empty();

        $.ajax({

            url: url,

            dataType: 'html',

            success: function(result) {
                // var contact = $('#default_contact_id').val();
                $(container).html(result).modal('show');
                // $(container).find('input#contact_id').val(contact);
            },

        });

    });


    function editRow(newRowId, name, passport_number, passenger_vat_number, passenger_mobile, image, should_notify, frequent_flyer_no, airline_itinerary, airticket_no, child, price, additional_services, passenger_id, passenger_type, amount) {
        $('div.passenger_modal').modal('show');
        // $('.btn-save-submit').text('Update');


        $('#id_row').val(newRowId);
        $('#passenger_name_select').val(name);
        $('#passport_number_text').val(passport_number);
        $('#passenger_vat_number_text').val(passenger_vat_number);
        $('#passenger_mobile_text').val(passenger_mobile);
        $('#passport_image').attr('src', '{{ url("public/uploads/media") }}' + '/' + image);
        $('#message_notify').val(should_notify);
        $('#frequent_flyer_no').val(frequent_flyer_no);
        $('#child').val(child);
        $('#price').val(price);
        $('#additional_service_select').val(additional_services);
        $('#amount').val(amount);
        $('#passenger_type_select').val(passenger_type);




    }


    $('#add_group_btn').click(function() {
        $('.contact_group_modal').modal({
            backdrop: 'static',
            keyboard: false
        });

    });
</script>

@endsection