@extends('layouts.app')
@section('title', __('hms::lang.prices'))
@section('content')

<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('hms::lang.set_price_for') {{ $room_type->type ?? '' }}
    </h1>
    <p><i class="fa fa-info-circle"></i> @lang('hms::lang.pricing_help_text') </p>
</section>

@php
$business_id = session()->get('user.business_id');
$business_details = App\Business::find($business_id);
$currency_precision = !empty($business_details->currency_precision) ? $business_details->currency_precision
: 2;
@endphp

<section class="content">
    @component('components.widget')
    <div class="box-body">
        {!! Form::open([
        'url' => action([\Modules\Hms\Http\Controllers\RoomController::class, 'post_pricing']),
        'method' => 'post',
        'id' => 'create_pricing',
        'files' => true,
        ]) !!}
        <div class="col-md-12">
            <div class="col-md-4">
                <input type="hidden" name="season_type" value="default">
                <div class="form-group">
                    {!! Form::label('type_id', 'Room type') !!}
                    {!! Form::select('type_id', $types, $room_type->id ?? null, [
                    'class' => 'form-control',
                    'id' => 'type_id',
                    'placeholder' => __('messages.please_select'),
                    'required',
                    ]) !!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="checkbox">
                <label>
                    <input type="checkbox" class="check_price_type" checked>
                    @lang('hms::lang.set_price_for_each_day')
                </label>
            </div>
        </div>
        <div class="col-md-12 week_days_pricing">
            <table class="table table-bordered">
                <thead>
                    <tr class="bg-light-green">
                        <th>@lang('hms::lang.monday')</th>
                        <th>@lang('hms::lang.tuesday')</th>
                        <th>@lang('hms::lang.wednesday')</th>
                        <th>@lang('hms::lang.thursday')</th>
                        <th>@lang('hms::lang.friday')</th>
                        <th>@lang('hms::lang.saturday')</th>
                        <th>@lang('hms::lang.sunday')</th>
                    </tr>
                </thead>
                <tbody>
                    <td>
                        @if (is_object($default_pricing))
                        <input type="hidden" name="pricing[0][id]" value="{{ $default_pricing->id }}">
                        @endif
                        {!! Form::number('pricing[0][monday]', $default_pricing ? number_format($default_pricing->price_monday, $currency_precision) : null, [
                        'class' => 'form-control',
                        'required',
                        ]) !!}
                    </td>
                    <td>
                        {!! Form::number('pricing[0][tuesday]', $default_pricing ? number_format($default_pricing->price_tuesday, $currency_precision) : null, [
                        'class' => 'form-control',
                        'required',
                        ]) !!}
                    </td>
                    <td>
                        {!! Form::number('pricing[0][wednesday]', $default_pricing ? number_format($default_pricing->price_wednesday, $currency_precision) : null, [
                        'class' => 'form-control',
                        'required',
                        ]) !!}
                    </td>
                    <td>
                        {!! Form::number('pricing[0][thursday]', $default_pricing ? number_format($default_pricing->price_thursday, $currency_precision) : null, [
                        'class' => 'form-control',
                        'required',
                        ]) !!}
                    </td>
                    <td>
                        {!! Form::number('pricing[0][friday]', $default_pricing ? number_format($default_pricing->price_friday, $currency_precision) : null, [
                        'class' => 'form-control',
                        'required',
                        ]) !!}
                    </td>
                    <td>
                        {!! Form::number('pricing[0][saturday]', $default_pricing ? number_format($default_pricing->price_saturday, $currency_precision) : null, [
                        'class' => 'form-control',
                        'required',
                        ]) !!}
                    </td>
                    <td>
                        {!! Form::number('pricing[0][sunday]', $default_pricing ? number_format($default_pricing->price_sunday, $currency_precision) : null, [
                        'class' => 'form-control',
                        'required',
                        ]) !!}
                    </td>
                </tbody>
            </table>
        </div>
        <div class="col-md-12 default_price" style="display: none;">
            <div class="col-md-4">
                {!! Form::label('default_price', __('hms::lang.default_price_per_night')) !!}
                {!! Form::number(
                'pricing[0][default_price]',
                $default_pricing ? number_format($default_pricing->default_price_per_night, $currency_precision) : null,
                [
                'class' => 'form-control',
                'required',
                ],
                ) !!}
            </div>
        </div>
        <div class="col-md-12 mt-5">
            <div class="alert alert-info">
                @lang('hms::lang.add_different_price_based_on_number_of_guests')
            </div>
        </div>
        <div class="col-md-12">
            <h3>
                @lang('hms::lang.special_price_based_on_number_of_guests')
            </h3>
        </div>
        <div class="col-md-12">
            <div class="checkbox">
                <label>
                    <input type="checkbox" id="check_by_guest" @if (count($spacial_pricing) !=0) checked @endif
                        class="check_by_guest">
                    @lang('hms::lang.set_different_prices_based_on_number_of_guests')
                </label>
            </div>
        </div>

        <div class="col-md-12 week_days_pricing_spacial" @if (count($spacial_pricing)==0) style="display: none" @endif>
            <table class="table table-bordered">
                <thead>
                    <tr class="bg-light-green">
                        <th style="width: 100px;">@lang('hms::lang.adults')</th>
                        <th style="width: 100px;">@lang('hms::lang.childrens')</th>
                        <th>@lang('hms::lang.monday')</th>
                        <th>@lang('hms::lang.tuesday')</th>
                        <th>@lang('hms::lang.wednesday')</th>
                        <th>@lang('hms::lang.thursday')</th>
                        <th>@lang('hms::lang.friday')</th>
                        <th>@lang('hms::lang.saturday')</th>
                        <th>@lang('hms::lang.sunday')</th>
                        <th style="width: 100px;">@lang('messages.action')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($spacial_pricing as $index => $pricing)
                    <tr>
                        <td>
                            <input type="hidden" name="pricing[{{ $index + 1 }}][id]"
                                value="{{ $pricing->id }}">
                            <select class="form-control" required name="pricing[{{ $index + 1 }}][adults]">
                                @for ($i = 1; $i <= $room_type->no_of_adult; $i++)
                                    <option @if ($pricing->adults == $i) selected @endif
                                        value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                            </select>
                        </td>
                        <td>
                            <select class="form-control" required name="pricing[{{ $index + 1 }}][childrens]">
                                @for ($i = 0; $i <= $room_type->no_of_child; $i++)
                                    <option @if ($pricing->childrens == $i) selected @endif
                                        value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                            </select>
                        </td>
                        <td>
                            <input class="form-control" required
                                name="pricing[{{ $index + 1 }}][monday]" value="{{ number_format($pricing->price_monday, $currency_precision) }}"
                                type="number">
                        </td>
                        <td>
                            <input class="form-control" required
                                name="pricing[{{ $index + 1 }}][tuesday]" value="{{ number_format($pricing->price_tuesday, $currency_precision) }}"
                                type="number">
                        </td>
                        <td>
                            <input class="form-control" required
                                name="pricing[{{ $index + 1 }}][wednesday]"
                                value="{{ number_format($pricing->price_wednesday, $currency_precision) }}" type="number">
                        </td>
                        <td>
                            <input class="form-control" required
                                name="pricing[{{ $index + 1 }}][thursday]"
                                value="{{ number_format($pricing->price_thursday, $currency_precision) }}" type="number">
                        </td>
                        <td>
                            <input class="form-control" required
                                name="pricing[{{ $index + 1 }}][friday]" value="{{ number_format($pricing->price_friday, $currency_precision) }}"
                                type="number">
                        </td>
                        <td>
                            <input class="form-control" required
                                name="pricing[{{ $index + 1 }}][saturday]"
                                value="{{ number_format($pricing->price_saturday, $currency_precision) }}" type="number">
                        </td>
                        <td>
                            <input class="form-control" required
                                name="pricing[{{ $index + 1 }}][sunday]" value="{{ number_format($pricing->price_sunday, $currency_precision) }}"
                                type="number">
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-xs remove"><i class="fa fa-trash"></i></button>
                            &nbsp;&nbsp;
                            <button type="button" class="btn btn-info btn-xs copy"><i class="fa fa-copy"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="button" class="tw-dw-btn tw-dw-btn-success tw-text-white tw-dw-btn-sm add-row" style="padding: 6px; font-size: 1.125em; line-height: 1.5;">@lang('hms::lang.add_number_of_guests_spacial_price')</button>
        </div>

        <div class="col-md-12 text-center">
            <button type="submit" class="btn btn-primary" id="save_room_btn">
                {{ __('messages.submit') }}
            </button>
        </div>

        {!! Form::close() !!}
    </div>
    @endcomponent

</section>
<!-- /.content -->
@endsection

@section('javascript')

<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', '.check_price_type', function(e) {
            if ($(this).is(':checked')) {
                $('.default_price').hide();
                $('.week_days_pricing').show();
            } else {
                $('.week_days_pricing').hide();
                $('.default_price').show();
            }
        });

        $(document).on('click', '#check_by_guest', function(e) {
            if ($(this).is(':checked')) {
                $('.week_days_pricing_spacial').show();
            } else {
                $('.week_days_pricing_spacial').hide();
            }
        });

        var currentIndex = parseFloat("{{ count($spacial_pricing) }}") + 1;
        $('.add-row').on('click', function() {

            if ($('#type_id').val() == '') {
                toastr.error('Please select room type');
                return false;
            }

            currentIndex++; // Increment the current index

            $.ajax({
                method: 'get',
                url: "{{ route('get_spacial_pricing_html') }}",
                dataType: 'html',
                data: {
                    'currentIndex': currentIndex,
                    'id': $('#type_id').val(),
                },
                success: function(response) {
                    $('.week_days_pricing_spacial table tbody').append(response);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error(textStatus, errorThrown);
                },
            });
        });

        // Remove row functionality
        $(document).on('click', '.remove', function() {
            $(this).closest('tr').remove();
        });

        $(document).on('click', '.copy', function() {
            var $row = $(this).closest('tr').clone();
            currentIndex++;
            $row.find('input, select').each(function() {
                var name = $(this).attr('name');

                if (name && name.endsWith('[id]')) {
                    $(this).removeAttr('name');
                } else if (name) {
                    var newName = name.replace(/\[(\d+)\]/g, '[' + currentIndex + ']');
                    $(this).attr('name', newName);
                }
            });
            $('.week_days_pricing_spacial table tbody').append($row);
        });

        $(document).on('change', '#type_id', function() {
            // Get the selected value of the dropdown (assuming it's a select element)
            var selectedValue = $(this).val();
            window.location.href = "{{ route('room_pricing') }}?room_id=" + selectedValue;
        });

        $('#create_pricing').on('submit', function(e) {
            e.preventDefault();

            var keys = new Set();
            var hasDuplicate = false;


            $('select[name^="pricing"][name$="[adults]"]').each(function() {
                var name = $(this).attr('name'); // Ex: pricing[1][adults]
                var indexMatch = name.match(/\[([0-9]+)\]/);
                if (!indexMatch) return;

                var index = indexMatch[1];
                var adults = $(this).val();
                var childrens = $(`select[name="pricing[${index}][childrens]"]`).val();

                var key = adults + '-' + childrens;

                if (keys.has(key)) {
                    hasDuplicate = true;
                    return false; // Stop the loop
                }

                keys.add(key);
            });

            if (hasDuplicate) {
                swal({
                    title: 'Duplicate Entries Detected',
                    text: 'Remove the duplicate entries in Special Price Based on Number of Guests section to proceed',
                    icon: 'error',
                    button: 'OK'
                });
            }

            if (!hasDuplicate) {
                var btn = $('#save_room_btn');
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        console.log("Room", response);
                        if (response.success) {
                            location.reload();
                            toastr.success(response.msg);
                        }
                    },
                    error: function(xhr, status, error) {
                        var errorMsg = xhr.responseJSON && xhr.responseJSON.msg ?
                            xhr.responseJSON.msg :
                            'Something went wrong. Please try again.';
                        swal({
                            title: 'Error',
                            text: errorMsg,
                            icon: 'error',
                            button: 'OK'
                        });
                        console.error(xhr.responseText);
                    },
                    complete: function() {
                        btn.prop('disabled', false).html('@lang("cheque.save")');
                    }
                });
            }

        });

        $("form#create_pricing").validate();
    });
</script>

@endsection