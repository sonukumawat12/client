@extends('layouts.app')
@section('title', __('lang_v1.calendar'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header" style="padding-bottom: 5px;">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Calendar</h1>
    <p><i class="fa fa-info-circle"></i> @lang('hms::lang.calender_help_text') </p>
</section>

<!-- Main content -->
<section class="content" style="padding-top: 0px;">
    @component('components.widget')
    <div class="box-header">
        <h3>@lang('hms::lang.jump_to')</h3>
        <div style="display: flex; align-items: center; justify-content: space-between; width: 100%; gap: 20px; margin-bottom: 8px; margin-top: 3px;">
            <div style="display: flex; gap: 10px;">
                <input type="text" value="{{ request()->input('date') ? request()->input('date') : '' }}"
                    class="form-control date_picker" style="width: 150px;">

                <!-- Previous Week Button (Blue) -->
                <button type="button" id="week_prev"
                    style="background-color: #007bff; color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold;">
                    <span aria-hidden="true">&laquo;</span> Previous Week
                </button>

                <!-- Previous Day Button (Orange) -->
                <button type="button" id="day_prev"
                    aria-label="Previous"
                    style="background-color: orange; color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold;">
                    <span aria-hidden="true">&lsaquo;</span> Previous Day
                </button>
            </div>

            <!-- Espace vide qui pousse la droite -->
            <div style="flex-grow: 1;"></div>

            <!-- Bloc droit : boutons droite -->
            <div style="display: flex; gap: 10px;">
                <!-- Next Day Button (Purple) -->
                <button type="button" id="day_next"
                    aria-label="Next Day"
                    style="background-color: purple; color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold;">
                    <span aria-hidden="true">&rsaquo;</span> Next Day
                </button>

                <!-- Next Week Button (Green) -->
                <button type="button" id="week_next"
                    aria-label="Next Week"
                    style="background-color: green; color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold;">
                    <span aria-hidden="true">&raquo;</span> Next Week
                </button>
            </div>
        </div>
    </div>
    <table class="table table-bordered " id="bookings_calender">
        <thead>
            <tr>
                <th style="width: 150px;">
                    {!! Form::select('type_id', $types, request()->input('type_id') ? request()->input('type_id') : null, [
                    'class' => 'form-control',
                    'id' => 'type_id',
                    'placeholder' => __('hms::lang.type'),
                    ]) !!}
                </th>
                {!! $date_html !!}
            </tr>
            {!! $html !!}
        </thead>
    </table>
    @endcomponent

</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
    $(document).ready(function() {
        var currentDate = new Date();
        var currentDateTime = moment(currentDate);

        $('.date_picker').datetimepicker({
            format: moment_date_format,
            ignoreReadonly: true,
            defaultDate: currentDateTime
        });

        $('.date_picker').on('dp.change', function(e) {

            window.location.href = "{{ route('booking_calendar') }}?type_id=" + $('#type_id').val() +
                "&date=" + $('.date_picker').val();

        });

        $('#type_id').on('change', function() {
            window.location.href = "{{ route('booking_calendar') }}?type_id=" + $('#type_id').val() +
                "&date=" + $('.date_picker').val();
        })


        $('#week_next').on('click', function() {
            var weekNext = "{{ request()->input('week_next') }}";
            if (weekNext == '') {
                weekNext = 1;
            } else {
                weekNext++;
            }
            window.location.href = "{{ route('booking_calendar') }}?type_id=" + $('#type_id').val() +
                "&week_next=" + weekNext;
        })

        $('#week_prev').on('click', function() {

            var weekNext = "{{ request()->input('week_next') }}";
            if (weekNext == '') {
                weekNext = -1;
            } else {
                weekNext--;
            }
            window.location.href = "{{ route('booking_calendar') }}?type_id=" + $('#type_id').val() +
                "&week_next=" + weekNext;
        })


        $('#day_next').on('click', function() {

            var daynext = "{{ request()->input('day_next') }}";
            if (daynext == '') {
                daynext = 1;
            } else {
                daynext++;
            }
            window.location.href = "{{ route('booking_calendar') }}?type_id=" + $('#type_id').val() +
                "&day_next=" + daynext;
        })

        $('#day_prev').on('click', function() {

            var daynext = "{{ request()->input('day_next') }}";
            if (daynext == '') {
                daynext = -1;
            } else {
                daynext--;
            }
            window.location.href = "{{ route('booking_calendar') }}?type_id=" + $('#type_id').val() +
                "&day_next=" + daynext;
        })

        $(".add_booking").hover(
            function() {
                $(this).find(".add_booking_div").fadeIn();
            },
            function() {
                $(this).find(".add_booking_div").fadeOut();
            }
        );


    });
</script>
@endsection

<style>
    .hotel-reservation-outer:last-child {
        padding-bottom: 5px;
        height: 30px;
    }

    .hotel-reservation-outer {
        height: 25px;
        width: 100%;
        position: relative;
    }

    .hotel-reservation-inner {
        height: 20px;
        width: 100%;
        border-radius: 2px;
        padding: 0 5px;
        color: #fff;
    }

    .bg-confirmed {
        background-color: #5ac5b6;
        border-color: #5ac5b6;
        color: #fff;
    }

    .add_booking_div {
        display: none;
        padding-top: 15px;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .box-tools {
            display: block;
            width: 100%;
        }

        .box-tools.pull-left,
        .box-tools.pull-right {
            width: 100%;
            margin-bottom: 10px;
        }

        .box-tools.pull-left {
            margin-top: 0;
        }

        .box-tools.pull-right {
            margin-top: 0;
        }

        /* Stack buttons on small screens */
        .box-tools.pull-left button,
        .box-tools.pull-right button {
            width: 100%;
            margin-bottom: 10px;
        }
    }
</style>