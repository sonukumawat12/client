@extends('layouts.app')
@section('title', __('essentials::lang.work_shifts'))

@section('content')
@include('essentials::layouts.nav_hrm')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">@lang('essentials::lang.work_shifts')</h4>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border bg-primary-dark">
                        <h3 class="box-title"><label>@lang('essentials::lang.workshift_list')</label></h3>
                        <button type="button" class="btn btn-primary pull-right" id="working_shift_modal_btn">@lang('essentials::lang.add_workshift')</button>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="msg"></div>
                                <table id="workshift_table" class="table table-striped table-bordered" cellspacing="0"
                                    width="100%">
                                    <thead>
                                        <tr>
                                            <th>@lang('essentials::lang.shift_name')</th>
                                            <th>@lang('essentials::lang.shift_from')</th>
                                            <th>@lang('essentials::lang.shift_to')</th>
                                            <th style="width:125px;">@lang('lang_v1.action')</th>
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
    </section>
<!-- /.content -->

<!-- Modal -->
<div id="working_shift_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">@lang('essentials::lang.add_workshift')</h4>
            </div>
            {!! Form::open(['url' => action('\Modules\Essentials\Http\Controllers\WorkShiftController@store'), 'method' => 'post']) !!}
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="shift_name">@lang('essentials::lang.shift_name'):</label>
                            <input type="text" class="form-control" name="shift_name">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="shift_form">@lang('essentials::lang.shift_from'):</label>
                            <input type="text" id="shif_from" class="form-control" name="shift_form">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="shift_to">@lang('essentials::lang.shift_to'):</label>
                            <input type="text" id="shif_to" class="form-control" name="shift_to">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            {!! Form::close() !!}
        </div>

    </div>
</div>

<div class="modal fade workshift_edit_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">

@stop

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            
                //working_shift list
    workshift_table = $('#workshift_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/hr/settings/workshift',
            data: function (d) {d.is_superadmin_page = false},
        },
        columns: [
            { data: 'shift_name', name: 'shift_name' },
            { data: 'shift_form', name: 'shift_form' },
            { data: 'shift_to', name: 'shift_to' },
            { data: 'action', name: 'action' },
        ],
        fnDrawCallback: function (oSettings) {},
    });
    $(document).on('click', '#working_shift_modal_btn', function (e) {
        $("#working_shift_modal").modal('show');
    });
    $(document).on('click', 'a.delete_workshift', function (e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            text: 'This work shift will be deleted.',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();

                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function (result) {
                        if (result.success === true) {
                            toastr.success(result.msg);
                            workshift_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });

    $('#shif_from').datetimepicker({ format: 'HH:mm a' });
    $('#shif_to').datetimepicker({ format: 'HH:mm a' });
            
        });
    </script>
@endsection

