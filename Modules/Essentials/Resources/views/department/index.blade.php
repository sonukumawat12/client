@extends('layouts.app')
@section('title', __('essentials::lang.department'))

@section('content')
    @include('essentials::layouts.nav_hrm')
    <section class="content-header">
        <h1>@lang('essentials::lang.department')
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        @component('components.widget', ['class' => 'box-solid', 'title' => __( 'essentials::lang.department' )])
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn pull-right btn-primary" id = "add_department_modal_btn">
                        <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                </div>
            @endslot
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="hrm_department_table">
                    <thead>
                    <tr>
                        <th>@lang( 'essentials::lang.department' )</th>
                        <th>@lang( 'essentials::lang.department_id' )</th>
                        <th>@lang( 'essentials::lang.description' )</th>
                        <th>@lang( 'essentials::lang.added_by' )</th>
                        <th>@lang( 'messages.action' )</th>
                    </tr>
                    </thead>
                </table>
            </div>
        @endcomponent



    </section>
    <!-- /.content -->


    @include('essentials::department.create')





@endsection

@section('javascript')
    <script type="text/javascript">

        $(document).ready(function(){
            department_table = $('#hrm_department_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ action([\Modules\Essentials\Http\Controllers\EssentialsDepartmentController::class, 'index']) }}",
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'short_code', name: 'short_code' },
                    { data: 'description', name: 'description' },
                    { data: 'username', name: 'u.username' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                columnDefs: [
                    {
                        targets: 4,
                        orderable: false,
                        searchable: false,
                    },
                ],
            });



        });


        $(document).on('click', '#add_department_modal_btn', function (e) {
            $("#add_department_modal").modal('show');
        });

        $(document).on('submit', 'form#edit_department_form, form#edit_department_form', function (e) {
            e.preventDefault();
            var data = $(this).serialize();
            $.ajax({
                method: $(this).attr('method'),
                url: $(this).attr('action'),
                dataType: 'json',
                data: data,
                success: function(result) {
                    if (result.success == true) {
                        $('div#add_department_modal').modal('hide');
                        $('.view_modal').modal('hide');
                        toastr.success(result.msg);
                        leave_type_table.ajax.reload();
                        $('form#add_department_form')[0].reset();
                    } else {
                        toastr.error(result.msg);
                    }
                },
            });
        })
        $(document).on('click', '.delete-button', function () {
            swal({
                title: LANG.sure, // Make sure LANG.sure is defined in your localization file, e.g., "Are you sure?"
                text: "{{ __('This action cannot be undone.') }}", // Add a warning message if needed
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var href = $(this).data('href');

                    $.ajax({
                        method: 'DELETE',
                        url: href,
                        dataType: 'json',
                        success: function (result) {
                            if (result.success === true) {
                                toastr.success(result.msg);
                                department_table.ajax.reload(); // Replace with your DataTable variable name
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                        error: function () {
                            toastr.error("{{ __('messages.something_went_wrong') }}");
                        }
                    });
                }
            });
        });



    </script>
@endsection
