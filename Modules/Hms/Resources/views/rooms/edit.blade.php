<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
            <h3 class="modal-title" id="exampleModalLabel">Room Edit</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        {!! Form::open([
        'url' => action([\Modules\Hms\Http\Controllers\RoomController::class, 'update'], ['room' => $room_type[0]['id']]),
        'method' => 'put',
        'id' => 'create_room_modal_form',
        'files' => true,
        ]) !!}


        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">

                    <div class="form-group " id="TypeContainer" style="display: none;">
                        {!! Form::label('type', __('hms::lang.type') . ':') !!}
                        <div class="d-flex align-items-center">
                            {!! Form::select('type_id', [], null, [
                            'class' => 'form-control',
                            'id' => 'type_id',
                            'placeholder' => __('hms::lang.Select_a_type'),
                            ]) !!}
                            <button type="button" id="addTypeBtn" class="btn btn-success" title="Ajouter un type">
                                <i class="fa fa-edit"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Champ affiché si l'utilisateur clique sur "+" --}}
                    <div class="form-group" id="newTypeContainer">
                        {!! Form::label('type', __('hms::lang.type') . ':') !!}
                        <div class="d-flex align-items-center">
                            {!! Form::text('type', $room_type[0]['type'], [
                            'class' => 'form-control',
                            'id' => 'type',
                            'disabled' => 'disabled',
                            'placeholder' => __('hms::lang.Enter_the_new_type'),
                            ]) !!}
                            <button type="button" id="selectTypeBtn" class="btn btn-primary" title="Select Type">
                                <i class="fa fa-edit"></i>
                            </button>
                        </div>
                    </div>
                </div>
                {!! Form::hidden('past_room_type_id', $room_type[0]['id']) !!}
                {!! Form::hidden('past_room_number', $room_number) !!}
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('no_of_adult', __('hms::lang.max_no_of_adult') . ':') !!}
                        {!! Form::number('no_of_adult', $room_type[0]['no_of_adult'], [
                        'class' => 'form-control',
                        'required',
                        'disabled' => 'disabled',
                        'placeholder' => __('hms::lang.no_of_adult'),
                        'min' => 0,
                        ]) !!}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('no_of_child', __('hms::lang.max_no_of_child') . ':') !!}
                        {!! Form::number('no_of_child', $room_type[0]['no_of_child'], [
                        'class' => 'form-control',
                        'required',
                        'disabled' => 'disabled',
                        'placeholder' => __('hms::lang.no_of_child'),
                        ]) !!}
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('rooms', __('hms::lang.room_no') . ':') !!}
                        {!! Form::number('rooms', $room_number, [
                        'class' => 'form-control',
                        'required',
                        'placeholder' => __('hms::lang.room_no'),
                        ]) !!}
                    </div>
                </div>
            </div>
        </div> <!-- /.modal-body -->

        <!-- Modal Footer -->
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                @lang('messages.close')
            </button>
            <button type="submit" class="btn btn-primary" id="save_room_btn">
                @lang('cheque.save')
            </button>
        </div>

        {!! Form::close() !!}

    </div>
</div>

<script>
    $(document).ready(function() {

        // Initialiser Select2 avec chargement AJAX
        $('#type_id').select2({
            placeholder: 'Choisir un type',
            ajax: {
                url: '{{ route("accommodation-types.index") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            templateResult: function(data) {
                if (data.loading) {
                    return data.text;
                }

                var template = '';
                if (data.type) {
                    template += '<strong>' + data.type + '</strong><br>';
                }
                template += $('label[for="no_of_adult"]').text() + ': ' + data.no_of_adult + ', ' + $('label[for="no_of_child"]').text() + data.no_of_child;

                return template;
            },

            templateSelection: function(data) {
                return data.type;
            },

            minimumInputLength: 1,
            width: '100%',
            escapeMarkup: function(markup) {
                return markup;
            }
        });


        $('#type_id').on('select2:select', function(e) {
            let d = e.params.data;
            $('#no_of_adult').val(d.no_of_adult || '');
            $('#no_of_child').val(d.no_of_child || '');
        });

        // Afficher le champ de nouveau type
        $('#addTypeBtn').on('click', function() {
            // $('#TypeContainer').hide();
            // $('#newTypeContainer').show();

            $('#type_id').prop('required', false); // désactive le select
            $('#type').prop('required', true);

            $('#no_of_adult').prop('disabled', false); // désactive le select
            $('#no_of_child').prop('disabled', false);
        });
        // Afficher le champ de nouveau type
        $('#selectTypeBtn').on('click', function() {
            $('#newTypeContainer').hide();
            $('#TypeContainer').show();

            $('#type_id').prop('required', true);
            $('#type').prop('required', false);

            $('#no_of_adult').prop('disable', true);
            $('#no_of_child').prop('disable', true);
        });

        $('input[name="rooms"]').on('input', function() {
            $(this).val($(this).val().replace(/,/g, ''));
        });

        $('#edit_room_form').on('submit', function(e) {
            e.preventDefault();

            var form = $(this);
            var actionUrl = form.attr('action');
            var formData = form.serializeArray(); // serializeArray to add _method

            // add _method=PUT 
            formData.push({
                name: '_method',
                value: 'PUT'
            });

            var btn = form.find('[type="submit"]');
            var modal = form.closest('.modal');

            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

            $.ajax({
                url: actionUrl,
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    console.log("Room updated:", response);

                    if (response.success) {
                        modal.modal('hide');
                        toastr.success(response.msg);
                        location.reload();
                    } else {
                        modal.modal('hide');
                        if (response.limit_reached) {
                            swal({
                                title: 'Limit Reached',
                                text: response.msg,
                                icon: 'error',
                                button: 'OK'
                            });
                        } else {
                            toastr.error(response.msg);
                        }
                    }
                },
                error: function(xhr) {
                    var errorMsg = xhr.responseJSON && xhr.responseJSON.msg ?
                        xhr.responseJSON.msg :
                        'Something went wrong. Please try again.';

                    modal.modal('hide');
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
        });
    });
</script>