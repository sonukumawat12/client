 
@extends('layouts.app')
@section('title', __('hms::lang.rooms'))
@section('content')
    
    <section class="content-header">

    <div class="row">
    <div class="col-md-1">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">
            @lang('hms::lang.rooms')
        </h1>
    </div>

    <div class="col-md-4">
        <div class="form-group row align-items-center">
            <label for="room_subscribe" class="col-sm-6 col-form-label" style="color: #007bff;">{{ __('No of Room Subscribe') }}</label>
            <div class="col-sm-6">
                {!! Form::text('room_subscribe', $room_subscribe, ['class' => 'form-control', 'id' => 'room_subscribe', 'readonly' => true]) !!}
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group row align-items-center">
            <label for="room_added" class="col-sm-4 col-form-label" style="color: #007bff;">{{ __('No of Room Added') }}</label>
            <div class="col-sm-6">
                {!! Form::text('room_added', $room_added, ['class' => 'form-control', 'id' => 'room_added', 'readonly' => true]) !!}
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group row align-items-center">
            <label for="room_could_be_added" class="col-sm-6 col-form-label" style="color: #007bff;">{{ __('No of Room Could be added') }}</label>
            <div class="col-sm-6">
                {!! Form::text('room_could_be_added', $room_could_be_added, ['class' => 'form-control', 'id' => 'room_could_be_added', 'readonly' => true]) !!}
            </div>
        </div>
    </div>
</div>

      
        <p><i class="fa fa-info-circle"></i> @lang('hms::lang.rooms_help_text') </p>
    </section>

    <!-- Main content btn btn-primary pull-right all-p-btn   //tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right-->
    <section class="content">

        @component('components.widget')
        
            <div class="box-tools pull-right">
        <button type="button" class="btn  btn-primary"  id="cheque_stamp_add" data-href="{{ action([\Modules\Hms\Http\Controllers\RoomController::class, 'create']) }}">
            <i class="fa fa-plus"></i> @lang('messages.add')
        </button>
    </div>
            <table class="table table-bordered table-striped" id="rooms_table">
                <thead>
                    <tr>
                        <th style="width: 10%; text-align: center;">
                            @lang('hms::lang.type')
                        </th>
                        <th style="width: 10%; text-align: center;">
                            @lang('hms::lang.max_no_of_adult')
                        </th>
                        <th style="width: 10%; text-align: center;">
                            @lang('hms::lang.max_no_of_child')
                        </th>
                        <th style="width: 10%; text-align: center;">
                            Room Number
                        </th>
                        
                        <th style="width: 10%; text-align: center;">
                            @lang('lang_v1.created_at')
                        </th>
                        <th style="width: 25%; text-align: center;">
                            @lang('messages.action')
                        </th>
                    </tr>
                </thead>
            </table>
        @endcomponent

    </section>
    <!-- /.content -->
    <div class="modal fade" id="cancel_cheque_add_modal" tabindex="-1" role="dialog"></div>
    <div class="modal fade" id="edit_room_modal" tabindex="-1" role="dialog"></div>
@endsection

@section('javascript')

<script type="text/javascript">
        $(document).ready(function() {
            var count = 1;
            $(document).on('click', '.add-room', function(e) {
                var inputField = $('#room_count');
                count++;
                var place_holder = "{{ __('hms::lang.room_no') }}";

               
                var newRoomField = $('<tr>' +
                        '<td>' +
                            '<input type="text" name="rooms[' + count + ']" class="form-control room-input" required placeholder="' + place_holder + '">' +
                            '<div class="invalid-feedback error" style="display:none">@lang('hms::lang.room_number_unick')</div>' +
                        '</td>' +
                        '<td>' +
                            '<button type="button" class="btn btn-danger remove">' +
                                '<i class="fas fa-trash-alt"></i> Delete' + // Added "Delete" text
                            '</button>' +
                        '</td>' +
                    '</tr>');
                $('.add_room table tbody').append(newRoomField);
            });


            tinymce.init({
                selector: 'textarea#description',
                height: 250
            });

            $("form#create_room").validate();

            // Remove row functionality
            $(document).on('click', '.remove', function() {
                $(this).closest('tr').remove();
            });

            $(document).on('click', '.submit_form', function(e) {
                e.preventDefault();
                var submit_type = $(this).attr('value');
                $('#submit_type').val(submit_type);
                if ($('form#create_room').valid()) {
                    if (!checkUniqueRoomNumbers()) {
                        return false;
                    }
                    $('form#create_room').submit();
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
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on('click', '#cheque_stamp_add', function () {
            
            var  roomSubscribe= Number($('#room_subscribe').val());
            var roomAdded = Number($('#room_added').val());
            var canProceed = 1;
            console.log(roomSubscribe, roomAdded)
            if (roomSubscribe <= roomAdded) {
                console.log('enter')
                swal({
                    title: 'Limit Reached',
                    text: 'You have reached the maximum number of rooms allowed by your subscription.',
                    icon: 'error',
                    button: 'OK'
                });
                canProceed = 0
            }

            if (canProceed === 1) {
                var url = $(this).data('href');
                console.log(url);
                $.ajax({
                    method: 'GET',
                    dataType: 'html',
                    url: url,
                    success: function (response) {
                        console.log(response);
                        $("#cancel_cheque_add_modal").html(response).modal('show');
                    }
                });
            }
		});
        $(document).on('click', '#edit_room', function () {
			var url = $(this).data('href');
            console.log(url);
			$.ajax({
				method: 'GET',
				dataType: 'html',
				url: url,
				success: function (response) {
                    console.log(response);
					$("#edit_room_modal").html(response).modal('show');
				}
			});
		});
            superadmin_business_table = $('#rooms_table').DataTable({
                processing: true,
                serverSide: true,
                fixedHeader:false,
                ajax: {
                    url: "{{ action([\Modules\Hms\Http\Controllers\RoomController::class, 'index']) }}",
                },
                aaSorting: [
                    [5, 'desc']
                ],
                columns: [{
                        data: 'type',
                        name: 'hms_room_types.type'
                    },
                    {
                        data: 'no_of_adult',
                        name: 'hms_room_types.no_of_adult'
                    },
                    {
                        data: 'no_of_child',
                        name: 'hms_room_types.no_of_child'
                    },
                    {
                        data: 'room_number',
                        name: 'room_number',
                    },
                     
                    {
                        data: 'created_at',
                        name: 'hms_room_types.created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        sorting: false,
                    },
                ]
            });

            $(document).on('click', 'a.delete_room_confirmation', function(e) {
                e.preventDefault();
                swal({
                    title: LANG.sure,
                    text: "Once deleted, you will not be able to recover this Room !",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((confirmed) => {
                    if (confirmed) {
                        window.location.href = $(this).attr('href');
                    }
                });
            });
        });
    </script>

@endsection
