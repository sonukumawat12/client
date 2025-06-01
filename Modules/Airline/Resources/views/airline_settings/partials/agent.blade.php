@component('components.widget', ['class' => '', 'title' => 'Airline Agents'])


<div class="row" style="margin-bottom: 4px;">
    <button type="button" class="btn btn-primary btn-modal pull-right"
    id="create_agent">
        <i class="fa fa-plus"></i>
        Add
    </button>
</div>

<div class="row">
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="agent_table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Agent</th>
                    <th>User</th>
                    <th>Date Create</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($airline_agents as $i => $ap)
                <tr>
                    <td>{{ $i +1 }}</td>
                    <td>{{ $ap->agent }}</td>
                    <td>{{ $ap->user->surname }} {{ $ap->user->first_name }} {{ $ap->user->last_name }}</td>
                    <td>{{ $ap->date }}</td>
                    <td class="d-flex">
                        
                        <button class="btn btn-sm bg-warning edit" data-id="{{ $ap->id }}" data-agent="{{ $ap->agent }}">Edit</button>
                        @if($ap->flights->count() > 0)
                        <button class="btn btn-sm bg-danger disabled" style="margin-left: 4px">Delete</button>
                        @else
                        <button class="btn btn-sm bg-danger delete" style="margin-left: 4px" data-id="{{ $ap->id }}" data-agent="{{ $ap->agent }}">Delete</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="agent_form_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form id="agent_form">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="agent_form_title">Modal title</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="agent" class="col-md-4 col-form-label text-md-right">Agent*</label>

                        <div class="col-md-6">
                            <input type="text" class="form-control" name="agent" autocomplete="name" autofocus>
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

    <div class="modal fade" id="agent_delete_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Are you sure to remove <strong id="agent_delete_name"></strong></h4>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-danger" id="delete_agent">Delete</button>
            </div>
        </div>
    </div>
</div>
@endcomponent

<script>
    const agent_module = {
        edit_id: null,
        delete_id: null,
        init: function () {
            this.listener()
        },
        submit: () => {
            const postData = {
                agent: $('#agent_form input[name=agent]').val()
            }

            $.ajax({
            url: '{{ route('add_airline_agent') }}',
            method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: postData,
                success: function (data, b, c) {
                    if (data.statusText == 'Success') {
                         var currentUrl = window.location.href;
                        var urlParts = currentUrl.split('?');
                        var mainUrl = urlParts[0];
                        var updatedUrl = updatedUrl = mainUrl + '?tab=airlinesagents';
                        window.location.href = updatedUrl;
                    }
                },
                error: function(data, b, c) {
                }
            });
        },
        update: () => {
            const patchData = {
                id: agent_module.edit_id,
                agent: $('#agent_form input[name=agent]').val()
            }

            $.ajax({
                url: '{{ route('edit_airline_agent') }}',
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: patchData,
                success: function (data, b, c) {
                    if (data.statusText == 'Success') {
                         var currentUrl = window.location.href;
                        var urlParts = currentUrl.split('?');
                        var mainUrl = urlParts[0];
                        var updatedUrl = updatedUrl = mainUrl + '?tab=airlinesagents';
                        window.location.href = updatedUrl;
                    }
                },
                error: function(data, b, c) {
                }
            });
        },
        delete: () => {
            const patchData = {
                id: agent_module.delete_id,
            }

            $.ajax({
                url: '{{ route('delete_airline_agent') }}',
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: patchData,
                success: function (data, b, c) {
                    if (data.statusText == 'Success') {
                        
                        var currentUrl = window.location.href;
                        var urlParts = currentUrl.split('?');
                        var mainUrl = urlParts[0];
                        var updatedUrl = updatedUrl = mainUrl + '?tab=airlinesagents';
                        window.location.href = updatedUrl;
                        
                    }
                },
                error: function(data, b, c) {
                }
            });
        },
        resetError: () => {
            $('#agent_form .form-group').removeClass('hass-error');
            $('#agent_form .invalid-feedback').hide();
        },
        listener: function() {
            $('#create_agent').click(function() {
                agent_module.edit_id = null;
                agent_module.resetError();

                $('#agent_form_title').html('Create Agent');
                $('#agent_form_modal').modal('show');
            });

            $('#agent_table .edit').click(function() {
                let data = $(this).data();
                agent_module.resetError();

                agent_module.edit_id = data.id;
                $('#agent_form input[name=agent]').val(data.agent);
                $('#agent_form_title').html('Update <strong>' + data.agent + '</strong>');
                $('#agent_form_modal').modal('show');
            });

            $('#agent_form').submit((e) => {
                e.preventDefault();
                const value = $('#agent_form input[name=agent]').val();

                if (value.length < 3) {
                    $('#agent_form input[name=agent]').parents('.form-group:first').addClass('has-error');
                    $('#agent_form input[name=agent]').next().show();

                    return false;
                }

                if (!agent_module.edit_id) {
                     this.submit();
                } else {
                    this.update();
                }
            });

            $('#agent_table .delete').click(function() {
                let data = $(this).data();
                agent_module.delete_id = data.id;
                $('#agent_delete_name').html(data.agent);

                $('#agent_delete_modal').modal('show');
            });

            $('#delete_agent').click(() => {
                if (agent_module.delete_id) {
                    this.delete();
                }
            })

            $('#agent_form input[name=agent]').on('focus', () => agent_module.resetError());
        }
    }

    $(document).ready(() => {
        agent_module.init();
    });
</script>