@extends('layouts.app')
@section('title', __('hms::lang.edit_room'))
@section('content')

<section class="content-header">
   <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black"> @lang('Copy to Another Room')</h1>
    <p><i class="fa fa-info-circle"></i> @lang('hms::lang.edit_rooms_help_text') </p>
</section>
    <!-- Main content -->
    <section class="content">
        <div class="box box-solid">
            {{-- <div class="box-header">
                <h3 class="box-title">@lang('hms::lang.edit_room')</h3>
            </div> --}}
            <div class="box-body">
                {!! Form::open([
                    'url' => action([\Modules\Hms\Http\Controllers\RoomController::class, 'update'], ['room' => $room_type->id]),
                    'method' => 'put',
                    'id' => 'edit_room',
                    'files' => true
                ]) !!}
                <div class="col-md-6">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('type', __('hms::lang.type') . ':') !!}
                            <div class="d-flex align-items-center">
                                <select name="type" class="form-control select2 mr-2" required>
                                    <option value="" selected disabled>Select or create room type</option>
                                    @foreach($room_types as $key => $value)
                                        <option value="{{ $value }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-primary" id="addRoomTypeBtn">
                                    <i class="fa fa-plus"></i>
                                </button>
                                <!-- Modal -->
                                <div class="modal fade" id="addTypeModal" tabindex="-1" role="dialog" aria-labelledby="addTypeModalLabel" aria-hidden="true">
                                  <div class="modal-dialog" role="document">
                                      <div class="modal-content">
                                          <div class="modal-header">
                                              <h5 class="modal-title" id="addTypeModalLabel">Add Accommodation Type</h5>
                                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                                          </div>
                                          <div class="modal-body">
                                              <input type="text" id="new_type" class="form-control" placeholder="Enter new type" required>
                                          </div>
                                          <div class="modal-footer">
                                              <button type="button" class="btn btn-success" id="submitNewTypeBtn">Add</button>
                                          </div>
                                      </div>
                                  </div>
                                </div>
                            </div>
                            <small class="help-block">Select an existing type or type to create a new one</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('no_of_adult', __('hms::lang.max_no_of_adult') . ':') !!}
                            {!! Form::number('no_of_adult', $room_type->no_of_adult, [
                                'required',
                                'class' => 'form-control',
                                'placeholder' => __('hms::lang.no_of_adult'),
                                'min' => 0,
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('no_of_child', __('hms::lang.max_no_of_child') . ':') !!}
                            {!! Form::number('no_of_child', $room_type->no_of_child, [
                                'class' => 'form-control',
                                'required',
                                'placeholder' => __('hms::lang.no_of_child'),
                            ]) !!}
                        </div>
                    </div>
                   
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('max_occupancy', __('hms::lang.max_occupancy') . ':') !!}
                            {!! Form::number('max_occupancy', $room_type->max_occupancy, [
                                'class' => 'form-control',
                                'required',
                                'placeholder' => __('hms::lang.max_occupancy'),
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-12 add_room">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="bg-light-green">
                                    <th>@lang('hms::lang.room_no')</th>
                                    <th style="width: 100px;">@lang('messages.action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($room_type['rooms'] as $index => $room)
                                <tr><td><input type="hidden" name="rooms[{{ $index }}][id]" value="{{ $room->id }}">
                                    <input type="text" name="rooms[{{ $index }}][name]" class="form-control room-input" required value="">
                                    <div class="invalid-feedback error" style="display:none">@lang('hms::lang.room_number_unick')</div>
                                </td><td><button type="button" class="tw-dw-btn tw-dw-btn-error tw-text-white tw-dw-btn-sm remove"><i class="fas fa-trash-alt"></i></button></td></tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-primary add-room"> @lang('messages.add') @lang('hms::lang.rooms')</button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="col-md-12">
                        {!! Form::label('amenities', __('hms::lang.amenities') . ':') !!}
                    </div>
                        @foreach ($amenities as $amenity)
                            <div class="col-md-4">
                                <div class="checkbox">
                                    <label>
                                    {!! Form::checkbox('amenities[]', $amenity->id , in_array($amenity->id, $existing_amenities) ,
                                    [ 'class' => 'input-icheck']); !!} {{ $amenity->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    <div class="col-md-12">
                        {!! Form::label('images', __('hms::lang.images') . ':') !!} <br>
                        @foreach($room_type->media as $media)
                            <div class="img-thumbnail">
                                <span class="badge bg-red delete-media" data-href="{{ action([\App\Http\Controllers\ProductController::class, 'deleteMedia'], ['media_id' => $media->id])}}"><i class="fas fa-times"></i></span>
                                {!! $media->thumbnail() !!}
                            </div>
                        @endforeach
                        <div class="form-group">
                            {!! Form::file('images[]', ['id' => 'upload_image', 'accept' => 'image/*',
                            'required' => false, 'multiple' => true, 'class' => 'upload-element']); !!}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('description', __('hms::lang.description') . ':') !!}
                            {!! Form::textarea('description', $room_type->description, ['class' => 'form-control', 'rows'=> 5]); !!}
                        </div>
                    </div>
                </div>
                    <div class="col-md-12 text-center">

                        <input type="hidden" name="submit_type" id="submit_type">
                        <button type="submit" name="submit_action" value="save_and_pricing" class="tw-dw-btn tw-text-white btn submit_form"
                        style="background-color: #ff7400;  color: white; border-radius: 4px; cursor: pointer; margin-right: 10px;">@lang('hms::lang.update_and_add_price')</button>
                        <button type="submit" name="submit_action" value="save"  class="btn btn-primary tw-text-white submit_form" style="background-color: #008000; color: white;">@lang('messages.update')</button>                    </div>

                    {!! Form::close() !!}
            </div>
        </div>
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
                                {!! Form::number('pricing[0][monday]', $default_pricing ? $default_pricing->price_monday : null, [
                                    'class' => 'form-control',
                                    'required',
                                    'step' => '0.01',
                                ]) !!}
                            </td>
                            <td>
                                {!! Form::number('pricing[0][tuesday]', $default_pricing ? $default_pricing->price_tuesday : null, [
                                    'class' => 'form-control',
                                    'required',
                                    'step' => '0.01',
                                ]) !!}
                            </td>
                            <td>
                                {!! Form::number('pricing[0][wednesday]', $default_pricing ? $default_pricing->price_wednesday : null, [
                                    'class' => 'form-control',
                                    'required',
                                    'step' => '0.01',
                                ]) !!}
                            </td>
                            <td>
                                {!! Form::number('pricing[0][thursday]', $default_pricing ? $default_pricing->price_thursday : null, [
                                    'class' => 'form-control',
                                    'required',
                                    'step' => '0.01',
                                ]) !!}
                            </td>
                            <td>
                                {!! Form::number('pricing[0][friday]', $default_pricing ? $default_pricing->price_friday : null, [
                                    'class' => 'form-control',
                                    'required',
                                    'step' => '0.01',
                                ]) !!}
                            </td>
                            <td>
                                {!! Form::number('pricing[0][saturday]', $default_pricing ? $default_pricing->price_saturday : null, [
                                    'class' => 'form-control',
                                    'required',
                                    'step' => '0.01',
                                ]) !!}
                            </td>
                            <td>
                                {!! Form::number('pricing[0][sunday]', $default_pricing ? $default_pricing->price_sunday : null, [
                                    'class' => 'form-control',
                                    'required',
                                    'step' => '0.01',
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
                            $default_pricing ? $default_pricing->default_price_per_night : null,
                            [
                                'class' => 'form-control',
                                'required',
                                'step' => '0.01',
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
                            <input type="checkbox" id="check_by_guest" @if (count($spacial_pricing) != 0) checked @endif
                                class="check_by_guest">
                            @lang('hms::lang.set_different_prices_based_on_number_of_guests')
                        </label>
                    </div>
                </div>

                <div class="col-md-12 week_days_pricing_spacial" @if (count($spacial_pricing) == 0) style="display: none" @endif>
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
                                        <input class="form-control" required step="0.0001"
                                            name="pricing[{{ $index + 1 }}][monday]" value="{{ $pricing->price_monday }}"
                                            type="number">
                                    </td>
                                    <td>
                                        <input class="form-control" required step="0.0001"
                                            name="pricing[{{ $index + 1 }}][tuesday]" value="{{ $pricing->price_tuesday }}"
                                            type="number">
                                    </td>
                                    <td>
                                        <input class="form-control" required step="0.0001"
                                            name="pricing[{{ $index + 1 }}][wednesday]"
                                            value="{{ $pricing->price_wednesday }}" type="number">
                                    </td>
                                    <td>
                                        <input class="form-control" required step="0.0001"
                                            name="pricing[{{ $index + 1 }}][thursday]"
                                            value="{{ $pricing->price_thursday }}" type="number">
                                    </td>
                                    <td>
                                        <input class="form-control" required step="0.0001"
                                            name="pricing[{{ $index + 1 }}][friday]" value="{{ $pricing->price_friday }}"
                                            type="number">
                                    </td>
                                    <td>
                                        <input class="form-control" required step="0.0001"
                                            name="pricing[{{ $index + 1 }}][saturday]"
                                            value="{{ $pricing->price_saturday }}" type="number">
                                    </td>
                                    <td>
                                        <input class="form-control" required step="0.0001"
                                            name="pricing[{{ $index + 1 }}][sunday]" value="{{ $pricing->price_sunday }}"
                                            type="number">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger remove"><i class="fa fa-trash"></i></button>
                                        &nbsp;&nbsp;
                                        <button type="button" class="btn btn-success copy"><i class="fa fa-copy"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="button" class="tw-dw-btn tw-dw-btn-success tw-text-white tw-dw-btn-sm add-row" style="padding: 6px; font-size: 1.125em; line-height: 1.5;">@lang('hms::lang.add_number_of_guests_spacial_price')</button>
                </div>

                <div class="col-md-12 text-center">
                    {!! Form::submit(__('messages.submit'), ['class' => 'tw-dw-btn tw-dw-btn-success tw-text-white tw-dw-btn-lg', 'style' => 'padding: 6px; font-size: 1.125em; line-height: 1.5;']) !!}
                </div>

                {!! Form::close() !!}
            </div>
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
                    var currentName = $(this).attr('name');
                    console.log(currentName)
                    var newName = currentName.replace(/\[(\d+)\]/g, '[' + currentIndex + ']');
                    console.log(newName)
                    $(this).attr('name', newName);
                });
                $('.week_days_pricing_spacial table tbody').append($row);
            });

            $(document).on('change', '#type_id', function() {
                // Get the selected value of the dropdown (assuming it's a select element)
                var selectedValue = $(this).val();
                window.location.href = "{{ route('room_pricing') }}?room_id=" + selectedValue;
            });

            $("form#create_pricing").validate();
        });
    </script>

<script>
    $(document).ready(function() {
    // Open modal
    $('#addRoomTypeBtn').on('click', function() {
        $('#addTypeModal').modal('show');
    });

    // Handle manual submit button click inside modal
    $('#submitNewTypeBtn').on('click', function() {
        let newType = $('#new_type').val().trim();
        let select = $('select[name="type"]');

        if (!newType) return;

        if ($(`#type-select option[value="${newType}"]`).length) {
            alert('This accommodation type already exists.');
            return;
        }

        $.ajax({
            url: '{{ route("accommodation-types.store") }}', // Adjusted to use correct route/controller
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                type: newType
            },
            success: function(response) {
                if (response.success) {
                    // Add to dropdown and trigger update
                        select.append(new Option(response.data, response.data));
                        select.val(response.data).trigger('change');
                    $('#addTypeModal').modal('hide');
                    $('#new_type').val('');
                } else {
                    alert(response.message || 'Error adding type.');
                }
            },
            error: function(xhr) {
                alert(xhr.responseJSON?.message || 'Something went wrong.');
            }
        });
    });
});
    
</script>
    <script type="text/javascript">
        $(document).ready(function() {
            var currentIndex = parseFloat("{{ count($room_type['rooms']) }}") + 1;

            $(document).on('click', '.add-room', function(e) {  
                var place_holder = "{{ __('hms::lang.room_no') }}";
                currentIndex ++;
                var newRoomField = $('<tr><td><input type="text" name="rooms['+currentIndex+'][name]" class="form-control room-input" required placeholder="'+place_holder+'" ><div class="invalid-feedback error" style="display:none">@lang('hms::lang.room_number_unick')</div></td><td><button type="button" class="btn btn-sm btn-danger remove"><i class="fas fa-trash-alt"></i></button></td></tr>');
                $('.add_room table tbody').append(newRoomField);
            });
          
            tinymce.init({
                    selector: 'textarea#description', 
                    height:250
            });
             // Remove row functionality
            $(document).on('click', '.remove', function() {
                    swal({
                    title: LANG.sure,
                    text: "Once deleted, you will not be able to recover this Room !",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                    }).then((confirmed) => {
                        if (confirmed) {
                            $(this).closest('tr').remove();
                        }
                    });
            });

            $("form#edit_room").validate({
                rules: {
                    "rooms[][name]": {
                        required: true
                    }
                },
            });

            $(document).on('click', '.submit_form', function(e) { 
                e.preventDefault();
                var submit_type = $(this).attr('value');
                $('#submit_type').val(submit_type);
                if ($('form#edit_room').valid()) {
                    if (!checkUniqueRoomNumbers()) {
                        return false;
                    }
                    $('form#edit_room').submit();
                }
            });

            function checkUniqueRoomNumbers() {
                var roomNumbers = {};
                var hasDuplicate = false;
                // Loop through each room input field
                $('.room-input').each(function() {
                    var roomNumber = $(this).val();
                    // Check if the room number is already added to the object
                    if (roomNumbers[roomNumber]) {
                        $(this).addClass('is-invalid');
                        $(this).siblings('.invalid-feedback').show();
                        hasDuplicate = true;
                    } else {
                        $(this).removeClass('is-invalid');
                        $(this).siblings('.invalid-feedback').hide();
                    }
                    // Add the room number to the object
                    roomNumbers[roomNumber] = true;
                });
                return !hasDuplicate;
            }
        });

      
    </script>

@endsection
