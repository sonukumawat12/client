<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
            <h3 class="modal-title" id="exampleModalLabel">Room Add</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        {!! Form::open([
        'url' => action([\Modules\Hms\Http\Controllers\RoomController::class, 'store']),
        'method' => 'post',
        'id' => 'create_room_modal_form',
        'files' => true,
        ]) !!}

        <!-- Modal Body -->
        <div class="modal-body">
            <div class="row">
                {!! Form::hidden('room_limit', $room_could_be_added, ['id' => 'room_could_be_added']) !!}
                <div class="col-md-4">
                    <div class="form-group " id="TypeContainer">
                        {!! Form::label('type', __('hms::lang.type') . ':') !!}
                        <div class="d-flex align-items-center">
                            {!! Form::select('type_id', [], '', [
                            'class' => 'form-control',
                            'id' => 'type_id',
                            'placeholder' => __('hms::lang.Select_a_type'),
                            ]) !!}
                            <button type="button" id="addTypeBtn" class="btn btn-outline-primary ml-2" title="Ajouter un type">
                                +
                            </button>
                        </div>
                    </div>

                    {{-- Champ affiché si l'utilisateur clique sur "+" --}}
                    <div class="form-group" id="newTypeContainer" style="display: none;">
                        {!! Form::label('type', __('hms::lang.type') . ':') !!}
                        <div class="d-flex align-items-center">
                            {!! Form::text('type', null, [
                            'class' => 'form-control',
                            'id' => 'type',
                            'placeholder' => __('hms::lang.Enter_the_new_type'),
                            ]) !!}
                            <button type="button" id="selectTypeBtn" class="btn btn-outline-primary ml-2" title="Select Type">
                                Select
                            </button>
                        </div>
                    </div>

                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('no_of_adult', __('hms::lang.max_no_of_adult') . ':') !!}
                        {!! Form::number('no_of_adult', null, [
                        'class' => 'form-control',
                        'required',
                        'placeholder' => __('hms::lang.no_of_adult'),
                        'min' => 0,
                        ]) !!}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('no_of_child', __('hms::lang.max_no_of_child') . ':') !!}
                        {!! Form::number('no_of_child', null, [
                        'class' => 'form-control',
                        'required',
                        'placeholder' => __('hms::lang.no_of_child'),
                        ]) !!}
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('rooms', __('hms::lang.room_no') . ':') !!}
                        {!! Form::text('rooms', null, [
                        'class' => 'form-control',
                        'required',
                        'id' => 'room_numbers_input',
                        'placeholder' => __('hms::lang.room_no'),
                        ]) !!}
                    </div>
                </div>
            </div>
        </div>

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
<style>
    .select2-container--open .select2-search--dropdown {
        display: block !important;
    }
</style>
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
                        q: params.term || '' // autorise une recherche vide
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
                template += $('label[for="no_of_adult"]').text() + ': ' + data.no_of_adult + ', ' +
                    $('label[for="no_of_child"]').text() + ': ' + data.no_of_child;

                return template;
            },
            templateSelection: function(data) {
                return data.type || data.text;
            },
            minimumInputLength: 0,
            width: '100%',
            escapeMarkup: function(markup) {
                return markup;
            }
        });

        $('#type_id').on('select2:open', function() {
            $('.select2-search__field').trigger('input');
        });


        $('#type_id').on('select2:select', function(e) {
            let d = e.params.data;
            $('#no_of_adult').val(d.no_of_adult || '');
            $('#no_of_child').val(d.no_of_child || '');
        });

        // Afficher le champ de nouveau type
        $('#addTypeBtn').on('click', function() {
            $('#TypeContainer').hide();
            $('#newTypeContainer').show();

            $('#type_id').prop('required', false); // désactive le select
            $('#type').prop('required', true);
        });
        // Afficher le champ de nouveau type
        $('#selectTypeBtn').on('click', function() {
            $('#newTypeContainer').hide();
            $('#TypeContainer').show();

            $('#type_id').prop('required', true); // désactive le select
            $('#type').prop('required', false);
        });

        $('#create_room_modal_form').on('submit', function(e) {
            e.preventDefault();

            const roomCouldBeAdded = parseInt($('#room_could_be_added').val());
            const inputVal = $('#room_numbers_input').val();
            const roomNumbers = inputVal
                .split(',')
                .map(function(r) {
                    return $.trim(r);
                })
                .filter(function(r) {
                    return r.length > 0;
                }).length;

            var canProceed = 1;
            if (roomCouldBeAdded < roomNumbers) {
                swal({
                    title: 'Limit Reached',
                    text: 'You have ' + roomCouldBeAdded + ' room(s) remaining under your current subscription',
                    icon: 'error',
                    button: 'OK'
                });
                canProceed = 0;
            }

            if (canProceed === 1) {
                var btn = $('#save_room_btn');
                var modal = $('#cancel_cheque_add_modal'); // Make sure this matches your modal ID
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
                            modal.modal('hide');
                            toastr.success(response.msg);
                            // Refresh your room list or table here if needed
                            //if (typeof room_table !== 'undefined') {

                            // }
                        } else {
                            modal.modal('hide');
                            // Show alert instead of toastr for limit reached
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
                    error: function(xhr, status, error) {
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
            }

        });
    });
</script>