@component('components.widget', ['class' => '', 'title' => 'Airlines'])
    <div class="row" style="margin-bottom: 4px;">
        <button type="button" class="btn btn-primary btn-modal pull-right" id="create_airline">
            <i class="fa fa-plus"></i>
            Add
        </button>
    </div>

    <div class="row">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="airline_table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Airline</th>
                        <th>User</th>
                        <th>Date Create</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($airlines as $i => $ap)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $ap->airline }}</td>
                            <td>{{ $ap->user->surname }} {{ $ap->user->first_name }} {{ $ap->user->last_name }}</td>
                            <td>{{ $ap->date }}</td>
                            <td class="d-flex">
                                <button class="btn btn-sm bg-warning edit" data-id="{{ $ap->id }}"
                                    data-airline="{{ $ap->airline }}">Edit</button>
                                @if($ap->flights->count() > 0)
                                <button class="btn btn-sm bg-danger delete disabled" style="margin-left: 4px"
                                    >Delete</button>
                               
                                @else    
                                <button class="btn btn-sm bg-danger delete" style="margin-left: 4px"
                                    data-id="{{ $ap->id }}" data-airline="{{ $ap->airline }}">Delete</button>
                                @endif    
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="airline_form_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <form id="airline_form">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="airline_form_title">Modal title</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label for="airline" class="col-md-4 col-form-label text-md-right">Airline*</label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" name="airline" autocomplete="name" autofocus>
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

    <div class="modal fade" id="airline_delete_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Are you sure to remove <strong id="airline_delete_name"></strong></h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" id="delete_airline">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endcomponent

<script>
    const airline_module = {
        edit_id: null,
        delete_id: null,
        init: function() {
            this.listener()
        },
        submit: () => {
            const postData = {
                airline: $('#airline_form input[name=airline]').val()
            }

            $.ajax({
                url: '{{ route('add_airline') }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: postData,
                success: function(data, b, c) {
                    if (data.statusText == 'Success') {
                        var currentUrl = window.location.href;
                        var urlParts = currentUrl.split('?');
                        var mainUrl = urlParts[0];
                        var updatedUrl = updatedUrl = mainUrl + '?tab=airlines';
                        window.location.href = updatedUrl;
                    }
                },
                error: function(data, b, c) {console.log('failed')}
            });
        },
        update: () => {
            const patchData = {
                id: airline_module.edit_id,
                airline: $('#airline_form input[name=airline]').val()
            }

            $.ajax({
                url: '{{ route('edit_airline') }}',
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: patchData,
                success: function(data, b, c) {
                    if (data.statusText == 'Success') {
                        var currentUrl = window.location.href;
                        var urlParts = currentUrl.split('?');
                        var mainUrl = urlParts[0];
                        var updatedUrl = updatedUrl = mainUrl + '?tab=airlines';
                        window.location.href = updatedUrl;
                    }
                },
                error: function(data, b, c) {}
            });
        },
        delete: () => {
            const patchData = {
                id: airline_module.delete_id,
            }

            $.ajax({
                url: '{{ route('delete_airline') }}',
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: patchData,
                success: function(data, b, c) {
                    if (data.statusText == 'Success') {
                        var currentUrl = window.location.href;
                        var urlParts = currentUrl.split('?');
                        var mainUrl = urlParts[0];
                        var updatedUrl = updatedUrl = mainUrl + '?tab=airlines';
                        window.location.href = updatedUrl;
                    }
                },
                error: function(data, b, c) {}
            });
        },
        resetError: () => {
            $('#airline_form .form-group').removeClass('hass-error');
            $('#airline_form .invalid-feedback').hide();
        },
        listener: function() {
            $('#create_airline').click(function() {
                airline_module.edit_id = null;
                airline_module.resetError();

                $('#airline_form_title').html('Create Airline');
                $('#airline_form_modal').modal('show');
            });

            $('#airline_table .edit').click(function() {
                let data = $(this).data();
                airline_module.resetError();

                airline_module.edit_id = data.id;
                $('#airline_form input[name=airline]').val(data.airline);
                $('#airline_form_title').html('Update <strong>' + data.airline + '</strong>');
                $('#airline_form_modal').modal('show');
            });

            $('#airline_form').submit((e) => {
                e.preventDefault();
                const value = $('#airline_form input[name=airline]').val();

                if (value.length < 3) {
                    $('#airline_form input[name=airline]').parents('.form-group:first').addClass(
                        'has-error');
                    $('#airline_form input[name=airline]').next().show();

                    return false;
                }

                if (!airline_module.edit_id) {
                    this.submit();
                } else {
                    this.update();
                }
            });

            $('#airline_table .delete').click(function() {
                let data = $(this).data();
                airline_module.delete_id = data.id;
                $('#airline_delete_name').html(data.airline);

                $('#airline_delete_modal').modal('show');
            });

            $('#delete_airline').click(() => {
                if (airline_module.delete_id) {
                    this.delete();
                }
            })

            $('#airline_form input[name=airline]').on('focus', () => airline_module.resetError());
        }
    }

    $(document).ready(() => {
        airline_module.init();
    });
</script>
