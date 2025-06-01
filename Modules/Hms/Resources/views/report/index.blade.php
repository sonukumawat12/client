@extends('layouts.app')
@section('title', __('hms::lang.reports'))
@section('css')
<style>
    .stat-box {
        padding: 15px;
        margin-bottom: 15px;
        color: white;
        font-weight: bold;
        border-radius: 5px;
        text-align: center;
    }

    .bg-blue {
        background-color: #2196f3;
    }

    .bg-red {
        background-color: #f44336;
    }

    .bg-green {
        background-color: #4caf50;
    }

    .bg-orange {
        background-color: #ff9800;
    }

    .bg-purple {
        background-color: #9c27b0;
    }

    .bg-yellow {
        background-color: #ffc107;
    }
</style>

@endsection
@section('content')

<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black"> @lang('hms::lang.reports')
    </h1>
    <p><i class="fa fa-info-circle"></i> @lang('hms::lang.report_help_text') </p>
</section>
<!-- Main content -->
<section class="content">

    @component('components.filters', ['title' => __('report.filters'), 'closed' => false])

    @php
    $business_id = session()->get('user.business_id');
    $business_details = App\Business::find($business_id);
    $currency_precision = !empty($business_details->currency_precision) ? $business_details->currency_precision
    : 2;
    @endphp

    <div class="row">
        <div class="col-md-3">
            {!! Form::open([
            'url' => action([\Modules\Hms\Http\Controllers\HmsReportController::class, 'index']),
            'method' => 'get',
            ]) !!}
            <div class="form-group">
                {!! Form::label('date_to', __('hms::lang.date_to') . ':') !!}
                {!! Form::text('date_to', request('date_to'), [
                'class' => 'form-control date_to',
                'placeholder' => __('hms::lang.date_to'),
                'readonly',
                'required',
                'id' => 'date_to',
                ]) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('date_from', __('hms::lang.date_from') . ':') !!}
                {!! Form::text('date_from', request('date_from'), [
                'class' => 'form-control date_from',
                'placeholder' => __('hms::lang.date_from'),
                'readonly',
                'required',
                'id' => 'date_from',
                ]) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>&nbsp;</label>
                <button type="submit"
                    style="background-color: #f57c00; color: white; border: none; padding: 4px 20px; font-weight: bold; font-size: 14px; border-radius: 0; cursor: pointer; margin-top: 29px;">
                    Generate Report
                </button>


            </div>
        </div>
        {!! Form::close() !!}
    </div>
    @endcomponent

    @if (request()->has('date_to') && request()->has('date_from'))
    @component('components.widget')
    <div class="row">
        @include('hms::partials.hms_report_table')
    </div>
    @endcomponent
    @component('components.widget')
    <div class="row">
        <hr>
        <div class="col-md-3">
            <div class="stat-box bg-green">
                <strong>@lang('hms::lang.total_bookings_received'):</strong> <br>
                {{ $total_booking->total_count }}
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box bg-yellow">
                <strong>@lang('hms::lang.total_guests'):</strong> <br>
                {{ $total_booking->total_guest }}
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box bg-blue">
                <strong>@lang('hms::lang.total_nights_booked'):</strong> <br>
                {{ $total_booking->total_nights }}
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box bg-purple" style="
                            padding-bottom: 32px;
                        ">
                <strong style="
                            float: left;
                        ">Total Amount:</strong> <br>
                <span class="display_currency" data-currency_symbol="true" style="/* float: none; */padding-bottom: -4px;/* margin-bottom: 13px !important; */">₨ 560,319.25</span>
            </div>
        </div>
    </div>
    <div class="row mt-5">
        <hr>
        <div class="col-md-3">
            <div class="stat-box bg-blue">
                <strong>@lang('hms::lang.total_confirmed_bookings'):</strong> <br>
                {{ $total_confirmed_booking->total_count }}
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box bg-purple">
                <strong>@lang('hms::lang.total_confirmed_guests'):</strong> <br>
                {{ $total_confirmed_booking->total_guest }}
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box bg-orange">
                <strong>@lang('hms::lang.total_confirmed_nights')</strong> <br>
                {{ $total_confirmed_booking->total_nights }}
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box bg-green" style="
                            padding-bottom: 32px;
                        ">
                <strong style="
                            float: left;
                        ">@lang('hms::lang.total_amount'):</strong> <br>
                <span class="display_currency" data-currency_symbol="true" style="/* float: none; */padding-bottom: -4px;/* margin-bottom: 13px !important; */">
                    {{ number_format($total_booking->total_amount, $currency_precision) }}
                </span>
            </div>
        </div>
    </div>
    <div class="row mt-5">
        <hr>
        <div class="col-md-3">
            <div class="stat-box bg-red">
                <strong>@lang('hms::lang.total_cancelled_bookings'):</strong> <br>
                {{ $total_cancelled_booking->total_count }}
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box bg-green">
                <strong>@lang('hms::lang.total_cancelled_guests'):</strong> <br>
                {{ $total_cancelled_booking->total_guest }}
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box bg-blue">
                <strong>@lang('hms::lang.total_cancelled_nights')</strong> <br>
                {{ $total_cancelled_booking->total_nights }}
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box bg-red" style="
                            padding-bottom: 32px;
                        ">
                <strong style="
                            float: left;
                        ">@lang('hms::lang.total_amount'):</strong> <br>
                <span class="display_currency" data-currency_symbol="true" style="/* float: none; */padding-bottom: -4px;/* margin-bottom: 13px !important; */">
                    {{ number_format($total_cancelled_booking->total_amount, $currency_precision) }} </span>
            </div>
        </div>
    </div>
    <div class="row mt-5">
        <hr>
        <div class="col-md-3">
            <div class="stat-box bg-purple">
                <strong>@lang('hms::lang.total_pending_bookings'):</strong> <br>
                {{ $total_pending_booking->total_count }}
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box bg-orange">
                <strong>@lang('hms::lang.total_pending_guests'):</strong> <br>
                {{ $total_pending_booking->total_guest }}
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box bg-green">
                <strong>@lang('hms::lang.total_pending_nights')</strong> <br>
                {{ $total_pending_booking->total_nights }}
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box bg-blue" style="
                            padding-bottom: 32px;
                        ">
                <strong style="
                            float: left;
                        ">@lang('hms::lang.total_amount'):</strong> <br>
                <span class="display_currency" data-currency_symbol="true" style="/* float: none; */padding-bottom: -4px;/* margin-bottom: 13px !important; */">
                    {{ number_format($total_pending_booking->total_amount, $currency_precision) }} </span>
            </div>
        </div>
    </div>
    <div class="row">
        <hr>
        <div class="col-md-3">
            <div class="stat-box bg-green">
                <strong>@lang('hms::lang.adults_guests'):</strong> <br>
                {{ $total_confirmed_booking->total_adult_guest }}

                @if ($total_confirmed_booking->total_guest != 0)
                ({{ ($total_confirmed_booking->total_adult_guest / $total_confirmed_booking->total_guest) * 100 }}
                %)
                @endif
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box bg-blue">
                <strong>@lang('hms::lang.children_guests'):</strong> <br>
                {{ $total_confirmed_booking->total_childs_guest }}

                @if ($total_confirmed_booking->total_guest != 0)
                ({{ ($total_confirmed_booking->total_childs_guest / $total_confirmed_booking->total_guest) * 100 }}
                %)
                @endif
            </div>
        </div>
    </div>
    @endcomponent

    @component('components.widget')
    <div class="row">
        <table class="table table-hover">
            <thead>
                <tr class="bg-light-green">
                    <th>@lang('hms::lang.rooms_per_booking')</th>
                    <th>@lang('hms::lang.bookings')</th>
                    <th>%</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>@lang('hms::lang.one_room_bookings')</td>
                    <td>{{ $rooms_by_booking_count->one_line_count }}</td>
                    <td>
                        @if (
                        $rooms_by_booking_count->one_line_count +
                        $rooms_by_booking_count->two_lines_count +
                        $rooms_by_booking_count->more_than_two_lines_count !=
                        0)
                        {{ number_format(($rooms_by_booking_count->one_line_count / ($rooms_by_booking_count->one_line_count + $rooms_by_booking_count->two_lines_count + $rooms_by_booking_count->more_than_two_lines_count)) * 100) }}
                        %
                        @else
                        0 %
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>@lang('hms::lang.two_room_bookings')</td>
                    <td>{{ $rooms_by_booking_count->two_lines_count }}</td>
                    <td>
                        @if (
                        $rooms_by_booking_count->one_line_count +
                        $rooms_by_booking_count->two_lines_count +
                        $rooms_by_booking_count->more_than_two_lines_count !=
                        0)
                        {{ number_format(($rooms_by_booking_count->two_lines_count / ($rooms_by_booking_count->one_line_count + $rooms_by_booking_count->two_lines_count + $rooms_by_booking_count->more_than_two_lines_count)) * 100) }}
                        %
                        @else
                        0 %
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>@lang('hms::lang.two_+_room_bookings')</td>
                    <td>{{ $rooms_by_booking_count->more_than_two_lines_count }}</td>
                    <td>
                        @if (
                        $rooms_by_booking_count->one_line_count +
                        $rooms_by_booking_count->two_lines_count +
                        $rooms_by_booking_count->more_than_two_lines_count !=
                        0)
                        {{ number_format(($rooms_by_booking_count->more_than_two_lines_count / ($rooms_by_booking_count->one_line_count + $rooms_by_booking_count->two_lines_count + $rooms_by_booking_count->more_than_two_lines_count)) * 100) }}
                        %
                        @else
                        0 %
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @endcomponent

    @component('components.widget')
    <div class="row">
        <table class="table table-hover">
            <thead>
                <tr class="bg-light-green">
                    <th>@lang('hms::lang.nights_per_booking')</th>
                    <th>@lang('hms::lang.booking')</th>
                    <th>%</th>
                </tr>
            </thead>
            <tbody>
                @php
                $total_nights =
                $count_by_night->one_night_count +
                $count_by_night->two_night_count +
                $count_by_night->three_night_count +
                $count_by_night->four_night_count +
                $count_by_night->five_night_count +
                $count_by_night->six_night_count +
                $count_by_night->more_than_six_night_count;
                @endphp
                <tr>
                    <td>@lang('hms::lang.1_night_bookings')</td>
                    <td>{{ $count_by_night->one_night_count }}</td>
                    <td>
                        @if ($total_nights != 0)
                        {{ number_format(($count_by_night->one_night_count / $total_nights) * 100) }} %
                        @else
                        0%
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>@lang('hms::lang.2_night_bookings')</td>
                    <td>{{ $count_by_night->two_night_count }}</td>
                    <td>
                        @if ($total_nights != 0)
                        {{ number_format(($count_by_night->two_night_count / $total_nights) * 100) }} %
                        @else
                        0%
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>@lang('hms::lang.3_night_bookings')</td>
                    <td>{{ $count_by_night->three_night_count }}</td>
                    <td>
                        @if ($total_nights != 0)
                        {{ number_format(($count_by_night->three_night_count / $total_nights) * 100) }} %
                        @else
                        0%
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>@lang('hms::lang.4_night_bookings')</td>
                    <td>{{ $count_by_night->four_night_count }}</td>
                    <td>
                        @if ($total_nights != 0)
                        {{ number_format(($count_by_night->four_night_count / $total_nights) * 100) }} %
                        @else
                        0%
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>@lang('hms::lang.5_night_bookings')</td>
                    <td>{{ $count_by_night->five_night_count }}</td>
                    <td>
                        @if ($total_nights != 0)
                        {{ number_format(($count_by_night->five_night_count / $total_nights) * 100) }} %
                        @else
                        0%
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>@lang('hms::lang.6_night_bookings')</td>
                    <td>{{ $count_by_night->six_night_count }}</td>
                    <td>
                        @if ($total_nights != 0)
                        {{ number_format(($count_by_night->six_night_count / $total_nights) * 100) }} %
                        @else
                        0%
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>@lang('hms::lang.6_+_night_bookings')</td>
                    <td>{{ $count_by_night->more_than_six_night_count }}</td>
                    <td>
                        @if ($total_nights != 0)
                        {{ number_format(($count_by_night->more_than_six_night_count / $total_nights) * 100) }}
                        %
                        @else
                        0%
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @endcomponent

    @component('components.widget')
    <div class="row">
        <table class="table table-hover">
            <thead>
                <tr class="bg-light-green">
                    <th>@lang('hms::lang.guests_per_booking')</th>
                    <th>@lang('hms::lang.booking')</th>
                    <th>%</th>
                </tr>
            </thead>
            <tbody>
                @php
                $total_adults =
                $count_by_adults->one_adult_count +
                $count_by_adults->two_adults_count +
                $count_by_adults->three_adults_count +
                $count_by_adults->four_adults_count +
                $count_by_adults->five_adults_count +
                $count_by_adults->six_adults_count +
                $count_by_adults->more_than_six_adults_count;
                @endphp
                <tr>
                    <td>@lang('hms::lang.1_guest_bookings')</td>
                    <td>{{ $count_by_adults->one_adult_count }}</td>
                    <td>
                        @if ($total_adults != 0)
                        {{ number_format(($count_by_adults->one_adult_count / $total_adults) * 100) }} %
                        @else
                        0%
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>@lang('hms::lang.2_guest_bookings')</td>
                    <td>{{ $count_by_adults->two_adults_count }}</td>
                    <td>
                        @if ($total_adults != 0)
                        {{ number_format(($count_by_adults->two_adults_count / $total_adults) * 100) }} %
                        @else
                        0%
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>@lang('hms::lang.3_guest_bookings')</td>
                    <td>{{ $count_by_adults->three_adults_count }}</td>
                    <td>
                        @if ($total_adults != 0)
                        {{ number_format(($count_by_adults->three_adults_count / $total_adults) * 100) }} %
                        @else
                        0%
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>@lang('hms::lang.4_guest_bookings')</td>
                    <td>{{ $count_by_adults->four_adults_count }}</td>
                    <td>
                        @if ($total_adults != 0)
                        {{ number_format(($count_by_adults->four_adults_count / $total_adults) * 100) }} %
                        @else
                        0%
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>@lang('hms::lang.5_guest_bookings')</td>
                    <td>{{ $count_by_adults->five_adults_count }}</td>
                    <td>
                        @if ($total_adults != 0)
                        {{ number_format(($count_by_adults->five_adults_count / $total_adults) * 100) }} %
                        @else
                        0%
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>@lang('hms::lang.6_guest_bookings')</td>
                    <td>{{ $count_by_adults->six_adults_count }}</td>
                    <td>
                        @if ($total_adults != 0)
                        {{ number_format(($count_by_adults->six_adults_count / $total_adults) * 100) }} %
                        @else
                        0%
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>@lang('hms::lang.6_+_guest_bookings')</td>
                    <td>{{ $count_by_adults->more_than_six_adults_count }}</td>
                    <td>
                        @if ($total_adults != 0)
                        {{ number_format(($count_by_adults->more_than_six_adults_count / $total_adults) * 100) }}
                        %
                        @else
                        0%
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @endcomponent

    @endif
</section>
<!-- /.content -->
@endsection

@section('javascript')
<script>
    $(document).ready(function() {
        var currentDate = new Date();
        var currentYear = currentDate.getFullYear(); // Get the current year
        var startOfYear = new Date(currentYear, 0, 1); // January 1st of the current year
        var formattedStartOfYear = moment(startOfYear)
        var currentDateTime = moment(currentDate)


        $('.date_to').datetimepicker({
            format: moment_date_format,
            ignoreReadonly: true,
            defaultDate: formattedStartOfYear
        });

        $('.date_from').datetimepicker({
            format: moment_date_format,
            ignoreReadonly: true,
            defaultDate: currentDateTime,
            minDate: formattedStartOfYear,
        });

        $('.date_to').on('dp.change', function(e) {
            var selectedDate = e.date;
            // Update the minimum date of the departure datepicker
            $('.date_from').data('DateTimePicker').minDate(selectedDate);
        });
    });
</script>
@endsection