@component('components.widget', ['class' => '', 'title' => 'Airline Prefix & Starting'])

<div class="row" style="margin-bottom: 4px;">
    <button type="button" class="btn btn-primary btn-modal pull-right"
    id="create_prefix">
        <i class="fa fa-plus"></i>
        Add
    </button>
</div>

<div class="row">
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="prefix_table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Prefix / Starting No</th>
                    <th>Value</th>
                    <th>User</th>
                    <th>Date Create</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($airline_prefix_starting_no as $i => $ap)
                <tr>
                    <td>{{ $i +1 }}</td>
                    <td>{{ $ap->mode->name }}</td>
                    <td>{{ $ap->value }}</td>
                    <td>{{ $ap->user->surname }} {{ $ap->user->first_name }} {{ $ap->user->last_name }}</td>
                    <td>{{ $ap->date }}</td>
                    <td>
                        <h4>
                            <span class="label {{ $ap->status ? 'label-success': 'label-danger'}}">{{ $ap->status_name }}</span>
                        </h4>
                    </td>
                    <td class="d-flex">
                        @if ($ap->status)
                            <button style="width: 70px;" class="btn btn-default bg-warning disable"
                                data-id="{{ $ap->id }}"
                                data-mode_name="{{ $ap->mode->name }}"
                                data-value="{{ $ap->value }}"
                            >Disable</button>
                        @else
                            <button style="width: 70px;" class="btn btn-sm bg-primary enable"
                                data-id="{{ $ap->id }}"
                                data-mode_name="{{ $ap->mode->name }}"
                                data-value="{{ $ap->value }}"
                            >Enable</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="prefix_form_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form id="prefix_form">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="prefix_form_title">Modal title</h4>
              </div>
              <div class="modal-body">
                <div class="row" style="border-bottom:1px solid #e5e5e5">
                    <div class="form-group col-md-6">
                        <label for="prefix_mode" class="form-label text-md-right">Mode</label>
                        @if($airline_prefix_starting_mode->count() > 0)
                        @foreach ($airline_prefix_starting_mode as $mode)
                                <input type="text" modeV="000" value="{{ $mode->name }}" class="form-control mode_name" style="margin-bottom:8px" >
                        @endforeach
                        @else
                            <input type="text" modeV="000"  class="form-control mode_name" style="margin-bottom:8px" data-mode-id="000">
                        @endif
                    </div>
                    <div class="form-group col-md-6 row">
                        <label for="prefix_starting_value" class="form-label text-md-right">Value</label>
                        @if($airline_prefix_starting_mode->count() > 0)
                        @foreach ($airline_prefix_starting_mode as $mode)
                            <input type="text" data-mode-id="{{ $mode->id }}" class="form-control mode_value" style="margin-bottom:8px;">
                        @endforeach
                        @else
                            <input type="text"  class="form-control mode_value" style="margin-bottom:8px" data-mode-id="999">
                        @endif

                    </div>
                </div>
                <div class="row" style="padding-top: 10px;">
                    <div class="form-group col-md-6">
                        <label for="prefix_mode" class="form-label text-md-right">Mode</label>
                        @if($airline_prefix_starting_mode->count() > 0)
                        @foreach ($airline_prefix_starting_mode as $mode)
                            <div class="mode_view" id="000" style="margin-bottom:8px">{{ $mode->name }}</div>
                        @endforeach
                            @else
                            <div class="mode_view" id="000" style="margin-bottom:8px">{{ '' }}</div>
                        @endif

                    </div>
                    <div class="form-group col-md-6 row">
                        <label for="prefix_starting_value" class="form-label text-md-right">Value</label>
                        @if($airline_prefix_starting_mode->count() > 0)
                        @foreach ($airline_prefix_starting_mode as $mode)
                            <div class="value_view" modeId="{{ $mode->id }}" style="height:19px"></div>
                        @endforeach
                        @else
                            <div class="value_view" modeId="999" style="height:19px"></div>
                        @endif

                    </div>
                </div>
                <div class="col-md-12">
                    <div class="text-danger pull-right error_form">Please input at least one of the form!</div>
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

  {{-- <div class="modal fade" id="prefix_delete_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Are you sure to remove <strong id="prefix_delete_name"></strong></h4>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-danger" id="delete_prefix">Delete</button>
            </div>
        </div>
    </div>
  </div> --}}

  <div class="modal fade" id="prefix_status_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Are you sure to update <strong id="prefix_status_name"></strong> to <strong id="prefix_status_value"></strong></h4>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="update_status_prefix"></button>
            </div>
        </div>
    </div>
  </div>
@endcomponent

<script>
    const prefix_module = {
        edit_id: null,
        delete_id: null,
        init: function () {
            this.listener()
        },
        submit: (data) => {
            const postData = {
                data: data
            };

            $.ajax({
                url: '/airline/add_multiple_prefix_starting_no',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: postData,
                success: function (data, b, c) {
                    if (data.statusText == 'Success') {
                        window.location.reload()
                    }
                },
                error: function(data, b, c) {
                }
            });
        },
        update_status: (patchData) => {
            $.ajax({
                url: '/airline/update_status_prefix_starting_no',
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: patchData,
                success: function (data, b, c) {
                    if (data.statusText == 'Success') {
                        window.location.reload()
                    }
                },
                error: function(data, b, c) {
                }
            });
        },
        show_update_status_modal: (status, data) => {
            const status_name = status ? 'Enable' : 'Disable';

            $('#prefix_status_name').html(data.mode_name + ': ' +data.value);
            $('#prefix_status_value').html(status_name);
            $('#update_status_prefix').html(status_name);
            $('#update_status_prefix').data({id: data.id, status: status});

            if (status) {
                $('#update_status_prefix').removeClass('btn-danger').addClass('btn-primary');
            } else {
                $('#update_status_prefix').removeClass('btn-primary').addClass('btn-danger');
            }

            $('#prefix_status_modal').modal('show');
        },
        resetError: () => {
            $('#prefix_form .form-group').removeClass('hass-error');
            $('#prefix_form .invalid-feedback').hide();
        },
        listener: function() {
            $('#prefix_form input.mode_value').on('keyup', function() {
                const data = $(this).data();
                $('#prefix_form .value_view[modeid=' + data.modeId + ']').html(this.value);
                $('#prefix_form .error_form').hide();
            });
            $('#prefix_form input.mode_name').on('keyup', function() {
                const data = $(this).data();
                console.log(data.modeId);
                $('#prefix_form .mode_view[id=' + data.modeId + ']').html(this.value);
                $('#prefix_form .error_form').hide();
            });

            $('#create_prefix').click(function() {
                prefix_module.edit_id = null;
                prefix_module.resetError();

                $('#prefix_form_title').html('Create Prefix');

                $('#prefix_form input.mode_value').val('').trigger('keyup');

                $('#prefix_form .error_form').hide();
                $('#prefix_form_modal').modal('show');
            });


            $('#prefix_form').submit((e) => {
                e.preventDefault();
                const data = [];
                const inputs = $('#prefix_form input.mode_value');
                const mInputs = $('#prefix_form input.mode_name');


            for (let i = 0; i < inputs.length; i++) {
                    const value = inputs[i].value;
                    const dt = $(inputs[i]).data();
                    const name = $(mInputs[i]).val();
                    console.log(name);

                    if (value) {
                        data.push({
                            mode_id: dt.modeId,
                            value: value,
                            mode_name: name
                        });
                    }
                }

                if (!data.length) {
                    $('#prefix_form .error_form').show();
                    return false;
                }

                this.submit(data);
            });


            $('#prefix_table .disable').click((e) => {
                let data = $(e.target).data();

                this.show_update_status_modal(0, data);
            });

            $('#prefix_table .enable').click((e) => {
                let data = $(e.target).data();

                this.show_update_status_modal(1, data);
            });

            $('#update_status_prefix').click((e) => {
                const data = $(e.target).data();

                this.update_status(data);
            });

        }
    }

    $(document).ready(() => {
        prefix_module.init();
    });
</script>

<style>
    .select2-container .select2-selection {
    height: 34px;
    border: 1px solid #ccc;
}
</style>