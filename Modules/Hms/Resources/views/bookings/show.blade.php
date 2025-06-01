@extends('layouts.app')
@section('title', __('hms::lang.booking'))
@section('css')
<style>
    .card-highlight {
        background: #ffffff;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        margin-bottom: 30px;
        transition: box-shadow 0.3s ease-in-out;
    }

    .card-highlight:hover {
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.25);
    }

    @media (max-width: 600px) {
        .box-header {
            flex-direction: column !important;
            align-items: flex-start !important;
        }

        .box-header>* {
            width: 100%;
        }
    }
</style>
@endsection
@section('content')
@php
$business_id = session()->get('user.business_id');
$business_details = App\Business::find($business_id);
$currency_precision = !empty($business_details->currency_precision) ? $business_details->currency_precision
: 2;
@endphp
<!-- Main content -->
<section class="content">

    <div class="col-md-8">
        <div class="card-highlight">
            <div class="box box-solid">
                <div class="box-header" style="
                        display: flex;
                        flex-wrap: wrap;
                        justify-content: space-between;
                        align-items: center;
                        gap: 10px;
                    ">
                    <!-- Left side: Booking title + Created Date -->
                    <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px;">
                        <h3 class="box-title" style="margin: 0;">
                            @lang('hms::lang.booking') - {{ $transaction->ref_no }}
                        </h3>
                        <div style="font-size: 14px;">
                            <strong>Created Date:</strong> {{ \Carbon\Carbon::parse($transaction->created_at)->format('d F Y') }} &nbsp;
                            <strong>Time:</strong> {{ \Carbon\Carbon::parse($transaction->created_at)->format('H:i') }}
                        </div>
                    </div>

                    <!-- Right side: Print Button -->
                    <a href="{{ action([\Modules\Hms\Http\Controllers\HmsBookingController::class, 'print'], [$transaction->id]) }}"
                        target="_blank"
                        style="
                               display: inline-flex;
                               align-items: center;
                               background-color: #ffc107;
                               color: #fff;
                               padding: 8px 16px;
                               border-radius: 8px;
                               font-weight: bold;
                               text-decoration: none;
                               font-size: 14px;
                               box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                               transition: background 0.3s ease;
                           "
                        onmouseover="this.style.backgroundColor='#e0a800'"
                        onmouseout="this.style.backgroundColor='#ffc107'">
                        <i class="fa fa-print" style="margin-right: 6px;"></i>
                        @lang('hms::lang.print_format_1')
                    </a>
                </div>


                <div class="box-body">
                    <div class="row">
                        {{-- <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('customer', __('hms::lang.customer') . ':') !!}
                                 {{ $transaction->name }}
                    </div>
                </div> --}}
                <div class="col-md-6">
                    <b>{{ __('sale.customer_name') }}:</b>
                    {{ $transaction->contact->name }}<br>
                    <b>{{ __('business.address') }}:</b><br>
                    @if(!empty($transaction->billing_address()))
                    {{$transaction->billing_address()}}
                    @else
                    @if($transaction->contact->landmark)
                    {{ $transaction->contact->landmark }},
                    @endif

                    {{ $transaction->contact->city }}

                    @if($transaction->contact->state)
                    {{ ', ' . $transaction->contact->state }}
                    @endif
                    <br>
                    @if($transaction->contact->country)
                    {{ $transaction->contact->country }}
                    @endif
                    @if($transaction->contact->mobile)
                    <br>
                    {{__('contact.mobile')}}: {{ $transaction->contact->mobile }}
                    @endif
                    @if($transaction->contact->alternate_number)
                    <br>
                    {{__('contact.alternate_contact_number')}}:
                    {{ $transaction->contact->alternate_number }}
                    @endif
                    @if($transaction->contact->landline)
                    <br>
                    {{__('contact.landline')}}:
                    {{ $transaction->contact->landline }}
                    @endif
                    @endif
                </div>
                <div class="col-md-6">

                    @if($transaction->status == 'confirmed')
                    <div class="form-group">
                        {!! Form::label('status', __('hms::lang.status') . ':') !!}
                        <h6 class="bg-green badge">{{ucfirst($transaction->status) }}</h6>
                    </div>
                    @elseif($transaction->status == 'pending')
                    <div class="form-group">
                        {!! Form::label('status', __('hms::lang.status') . ':') !!}
                        <h6 class="bg-yellow badge">{{ucfirst($transaction->status) }}</h6>
                    </div>
                    @elseif($transaction->status == 'cancelled')
                    <div class="form-group">
                        {!! Form::label('status', __('hms::lang.status') . ':') !!}
                        <h6 class="bg-red badge">{{ucfirst($transaction->status) }}</h6>
                    </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('arrival_date', __('hms::lang.arrival_date') . ':') !!}
                        {{ @format_date($transaction->hms_booking_arrival_date_time) }}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('arrival_time', __('hms::lang.arrival_time') . ':') !!}
                        {{ @format_time($transaction->hms_booking_arrival_date_time) }}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('departure_date', __('hms::lang.departure_date') . ':') !!}
                        {{ @format_date($transaction->hms_booking_departure_date_time) }}
                    </div>
                    <div class="days_count"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('departure_time', __('hms::lang.departure_time') . ':') !!}
                        {{ @format_time($transaction->hms_booking_departure_date_time) }}
                    </div>
                </div>
                @if(!empty($transaction->check_in))
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('check_in', __('hms::lang.check_in') . ':') !!}
                        {{ @format_datetime($transaction->check_in) }}
                    </div>
                </div>
                @endif
                @if(!empty($transaction->check_out))
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('check_out', __('hms::lang.check_out') . ':') !!}
                        {{ @format_datetime($transaction->check_out) }}
                    </div>
                </div>
                @endif
                <div class="col-md-12">
                    <hr>
                </div>
                <div>
                    <h3 class="col-md-12">
                        @lang('hms::lang.rooms_and_extras')
                    </h3>
                </div>
                <div class="col-md-8 booking_add_room">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="bg-light-green">
                                <th>@lang('hms::lang.type')</th>
                                <th>@lang('hms::lang.room_no')</th>
                                <th>@lang('hms::lang.no_of_adult')</th>
                                <th>@lang('hms::lang.no_of_child')</th>
                                <th>@lang('hms::lang.price')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($booking_rooms as $room)
                            <tr>
                                <td>
                                    {{ $room->type }}

                                </td>
                                <td>
                                    {{ $room->room_number }}
                                </td>
                                <td>
                                    {{ $room->adults }}
                                </td>
                                <td>
                                    {{ $room->childrens }}
                                </td>
                                <td class="price-td">
                                    {{ number_format($room->total_price, $currency_precision) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>


                <div class="col-md-12">
                    <hr>
                    <h3>Payment Account</h3>
                </div>
                <div class="col-md-8 booking_add_room">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="bg-light-green">
                                <th>Transaction Date</th>
                                <th>Description</th>
                                <th>Customer</th>
                                <th>Cheque</th>
                                <th>Debit</th>
                                <th>Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($accounts as $paying)
                            <tr>
                                <td>
                                    {{ $paying->created_at }}

                                </td>
                                <td>
                                    {{ $paying->description }}
                                </td>
                                <td>
                                    {{ $transaction->contact->name }}
                                </td>
                                <td>
                                    {{ $paying->cheque_number }}
                                </td>
                                <td>
                                    {{ $paying->amount}}
                                </td>
                                <td>

                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="col-md-12">
                    <hr>
                    <h3>Sales Income Account</h3>
                </div>
                <div class="col-md-8 booking_add_room">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="bg-light-green">
                                <th>Transaction Date</th>
                                <th>Description</th>
                                <th>Customer</th>
                                <th>Cheque</th>
                                <th>Debit</th>
                                <th>Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($accounts as $paying)
                            <tr>
                                <td>
                                    {{ $paying->created_at }}

                                </td>
                                <td>
                                    {{ $paying->description }}
                                </td>
                                <td>
                                    {{ $transaction->contact->name }}
                                </td>
                                <td>
                                    {{ $paying->cheque_number }}
                                </td>
                                <td>

                                </td>
                                <td>
                                    {{ $paying->amount}}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>


                <div class="col-md-4">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>@lang('hms::lang.extras')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ( $extras as $index => $extra)
                            @if (in_array($extra->id, $extras_id))
                            <tr>
                                <td>
                                    {{ $extra->name }} /<span class="display_currency" data-currency_symbol="true"> {{ number_format($extra->price, $currency_precision) }} </span> - {{ str_replace("_", " ", $extra->price_per) }}

                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    @lang('hms::lang.status')
                    <span class="pull-right status_value">
                        @if($transaction->status == 'confirmed')
                        <div class="badge" style="background-color: #4CAF50 !important; color: white;">{{ ucfirst($transaction->status) }}</div>
                        @elseif($transaction->status == 'pending')
                        <div class="badge" style="background-color: #FFC107 !important; color: black;">{{ ucfirst($transaction->status) }}</div>
                        @elseif($transaction->status == 'cancelled')
                        <div class="badge" style="background-color: #F44336 !important; color: white;">{{ ucfirst($transaction->status) }}</div>
                        @endif
                    </span>
                </h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-6">
                        <strong>@lang('hms::lang.room_price') :</strong>
                    </div>
                    <div class="col-xs-6 text-right">
                        <strong class="room_price"> <span class="display_currency" data-currency_symbol="true"> {{ number_format($transaction->room_price, $currency_precision) }} </span></strong>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <strong>@lang('hms::lang.extra_price') :</strong>
                    </div>
                    <div class="col-xs-6 text-right">
                        <strong class="extra_price"><span class="display_currency" data-currency_symbol="true"> {{ number_format($transaction->extra_price, $currency_precision) }} </span></strong>
                    </div>
                </div>
                <div class="row">
                    @php
                    $discount_percent_disable = 0;

                    if(!empty($transaction->hms_coupon_id)){
                    $discount_percent_disable = 1;
                    }

                    @endphp
                    @if($discount_percent_disable == 0 && $transaction->discount_amount > 0)
                    <div class="col-xs-6">
                        <strong>@lang('hms::lang.discount'):</strong> ( {{ number_format($transaction->discount_amount, 2)  }} % )
                    </div>
                    <div class="col-xs-6 text-right">
                        <strong class="total_discount"> <span class="display_currency" data-currency_symbol="true"> {{ number_format($transaction->discount_amount * ( $transaction->extra_price + $transaction->room_price ) / 100, $currency_precision) }} </span></strong>
                        @else
                        <div class="col-xs-6">
                            <strong>@lang('hms::lang.discount'):</strong>
                        </div>
                        <div class="col-xs-6 text-right">
                            <strong class="total_discount"> <span class="display_currency" data-currency_symbol="true"> {{ $transaction->discount_amount }} </span></strong>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <hr>
                        <div class="col-xs-6">
                            <strong>@lang('hms::lang.total'):</strong>
                        </div>
                        <div class="col-xs-6 text-right">
                            <strong class="total"> <span class="display_currency" data-currency_symbol="true"> {{ $transaction->final_total }} </span></strong>
                        </div>
                    </div>
                </div>
            </div>
            @if (!empty($transaction->coupon_code))
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        @lang('hms::lang.apply_coupon')
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                {!! Form::label('coupon_code', __('hms::lang.coupon_code') . ':') !!}
                                {{ $transaction->coupon_code }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <div class="panel panel-default">
                <div class="panel panel-default" style="border: 1px solid #ddd; box-shadow: none;">
                    <div class="panel-heading" style="background-color: #f5f5f5; border-bottom: 1px solid #ddd;">
                        <h3 class="panel-title" style="color: #007bff; font-weight: 600;">
                            @lang('hms::lang.payment_status')
                        </h3>
                    </div>
                    <div class="panel-body" style="padding: 15px 20px;">
                        <div class="row" style="margin-bottom: 10px;">
                            <div class="col-xs-6">
                                <strong>@lang('hms::lang.total'):</strong>
                            </div>
                            <div class="col-xs-6 text-right">
                                <strong>
                                    <span class="display_currency" data-currency_symbol="true">
                                        {{ $transaction->final_total }}
                                    </span>
                                </strong>
                            </div>
                        </div>

                        <div class="row" style="margin-bottom: 10px;">
                            <div class="col-xs-6">
                                <strong>Paid:</strong>
                            </div>
                            <div class="col-xs-6 text-right">
                                <strong>
                                    <span class="display_currency" data-currency_symbol="true">
                                        {{ $transaction->payment_lines->sum('amount') }}
                                    </span>
                                </strong>
                            </div>
                        </div>

                        <hr style="margin: 10px 0;">

                        <div class="row">
                            <div class="col-xs-12 text-right">
                                <strong style="color: #007bff;float:left;">
                                    Balance Due:
                                </strong>
                                <strong>
                                    <span class="display_currency" data-currency_symbol="true" style="color: #007bff;">
                                        {{ $transaction->final_total - $transaction->payment_lines->sum('amount') }}
                                    </span>
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
</section>
<!-- /.content -->
@endsection