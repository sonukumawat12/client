@section('css')
<style>
    .mb-3 {
        margin-bottom: 1rem !important;
    }

    .inline {
        display: inline;
    }
    .dataTable{
        width:100% !important;
    }
</style>
@endsection
<div class="pos-tab-content">
    <div class="container-xl">
        <!-- Page title -->
        <div class="mb-3">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="page-title">
                        {{ __('Wastage') }}
                    </h2>
                </div>
                <!-- Page title actions -->
                <div class="col-lg-6  mt-10">
                    <button type="button" onclick="addWastage()" class="btn btn-primary pull-right mt-10" aria-label="Left Align">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ __('Add Wastage') }}
                    </button>

                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="table-responsive px-2 py-2">
                            <table class="table table-bordered table-striped" id="wastage_table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('User') }}</th>
                                        <th class="w-1">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade " id="add-wastage-modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="false">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content main-modal">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Wastage</h4>
                </div>
                <form id="mfg_wastage_form" action="javascript:saveWastage();" method="post">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-sm-12">
                                {!! Form::label('wastage_name', __( 'Wastage Name' ) . ':*') !!}
                                {!! Form::text('wastage_name', '', ['id'=>'wastage_name','class' => 'form-control', 'required', 'placeholder' => __(
                                'Wastage Name' )]); !!}
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" onclick="submitWastage()" class="btn btn-primary">@lang( 'messages.save' )</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
                    </div>
                </form>

            </div>
        </div>
    </div>


    <script type="text/javascript">
        var wastage_table;
        $(document).ready(function() {

            buildWastageTable();

        });

        function submitWastage() {
            if ($('#wastage_name').valid()) {
                saveWastage();
            }
        }

        function addWastage(parameter) {
            "use strict";
            $("#add-wastage-modal").modal("show");
        }

        function buildWastageTable() {
            wastage_table = $('#wastage_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
            ajax: {
                url: "{{action('\Modules\Manufacturing\Http\Controllers\SettingsController@getWastage')}}",
            },
            columns: [
                { data: 'date', name: 'date' },
                { data: 'name', name: 'name' },
                { data: 'user', name: 'user' },
                { data: 'action', searchable: false, orderable: false },
                
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
        }

        function saveWastage() {
            $.ajax({
                type: 'POST',
                url: '{{ route("saveWastage") }}',
                data: {
                    wastage_name: $('#wastage_name').val()
                },

                success: function(response) {
                    console.log(response);
                    if (response.success == 1) {
                        swal({
                            text: response.msg,
                            icon: "success",
                        });
                        $("#add-wastage-modal").modal("hide");
                        $('#wastage_name').val('');
                        wastage_table.ajax.reload();
                    } else {
                        swal({
                            text: response.msg,
                            icon: "error",
                        });
                    }

                },
                error: function(error) {
                    console.log(error);
                }
            });

            return false;
        }

        function disableWastageItem(id){
            $.ajax({
                type: 'POST',
                url: '{{ route("disableItem") }}',
                data: {
                    id: id
                },

                success: function(response) {
                    console.log(response);
                    if (response.success == 1) {
                        swal({
                            text: response.msg,
                            icon: "success",
                        });
                        console.log('reloading');
                        wastage_table.ajax.reload();
                    } else {
                        swal({
                            text: response.msg,
                            icon: "error",
                        });
                    }

                },
                error: function(error) {
                    console.log(error);
                }
            });

            return false;
        }


        function enableWastageItem(id){
            $.ajax({
                type: 'POST',
                url: '{{ route("enableItem") }}',
                data: {
                    id: id
                },

                success: function(response) {
                    console.log(response);
                    if (response.success == 1) {
                        swal({
                            text: response.msg,
                            icon: "success",
                        });
                        wastage_table.ajax.reload();
                    } else {
                        swal({
                            text: response.msg,
                            icon: "error",
                        });
                    }

                },
                error: function(error) {
                    console.log(error);
                }
            });

            return false;
        }
    </script>

</div>