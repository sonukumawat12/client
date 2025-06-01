@extends('layouts.app')
@section('title', 'List Discount')
@section('content')
  <section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="row" style="margin-bottom: 5px !important;">
    
    <div id="accordion1" class="according  " data-select2-id="accordion1">
        <div class="card" data-select2-id="11">
            <div class="card-header" style="cursor: pointer;">
                <a class="card-link" data-toggle="collapse" style="padding-top: 5px !important; padding-bottom: 5px !important" href="#accordion11"> <i class="fa fa-filter" aria-hidden="true"></i>  Filters </a>
            </div>
            <div id="accordion11" class="collapse show" data-parent="#accordion1" data-select2-id="accordion11">
                <div class="card-body" data-select2-id="10">
                    <div class="col-md-3" data-select2-id="9">
                <div class="form-group" data-select2-id="8">
                    <label for="note_groups">@lang('discount::lang.customer')</label>
                    <select class="form-control select2 select2-hidden-accessible" style="width:100%" id="note_groups_filter" name="note_groups" data-select2-id="note_groups_filter" tabindex="-1" aria-hidden="true">
                        <option selected="selected">All</option>
                        <option value="1">Demo</option></select>
                </div>
            </div>
            <div class="col-md-3" data-select2-id="16">
                <div class="form-group" data-select2-id="15">
                    <label for="note_ids">@lang('discount::lang.invoice')</label>
                    <select class="form-control select2 select2-hidden-accessible" style="width:100%" id="note_ids_filter" name="note_ids" data-select2-id="note_ids_filter" tabindex="-1" aria-hidden="true">
                        <option selected="selected" value="" data-select2-id="4">All</option><option value="1" data-select2-id="17">1</option></select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="note_headings">@lang('discount::lang.sales')</label>
                    <select class="form-control select2 select2-hidden-accessible" style="width:100%" id="note_headings_filter" name="note_headings" data-select2-id="note_headings_filter" tabindex="-1" aria-hidden="true"><option selected="selected" value="" data-select2-id="6">All</option><option value="jdjsds">jdjsds</option></select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="date_range_filter">@lang('discount::lang.data_range')</label>
                    <input placeholder="Select a date range" class="form-control daily_report_change" id="date_range_filter" readonly="" name="date_range" type="text" value="01/01/2024 ~ 01/01/1970">
                </div>
            </div>
                </div>
            </div>
        </div>
    </div>
</div>        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary" style="font-size: 12px !important">
                    <div class="box-header">
            
            <h4 class="box-title text-center">All Discount</h4><br>
                        <div class="box-tools">
                <button type="button" class="btn btn-primary btn-modal pull-right" id="add_note_note_btn" data-href="https://vim20.xyz/discounts-templates" data-container=".note_model">
                    <i class="fa fa-plus" aria-hidden="true"></i> Add Discount</button>
            </div>
        </div>
            
    <div class="box-body">
        <div class="row">
                <div class="col-md-12">
                                    </div>
            </div>
            <br>
            <br>
            <br>
            <div class="clearfix"></div>
            <div class="row">
                        <div class="col-md-12">
                            <div id="msg"></div>
                            <table id="discount_table" class="table table-striped table-bordered" cellspacing="0"
                                width="100%">
                                <thead>
                                    <tr>
                                        <th style="width:125px;">@lang('discount::lang.actions')</th>
                                        <th>@lang('discount::lang.data_time')</th>
                                        <th>@lang('discount::lang.sub_category')</th>
                                        <th>Users</th>
                                        <th>@lang('discount::lang.max_discount')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
            </div>
    </div>
    <!-- /.box-body -->
</div>        </div>
    </div>
    <div class="modal fade note_model" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>
@stop
@section('javascript')
<script>

    if ($('#date_range_filter').length == 1) {
        $('#date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#date_range_filter').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
            );
        });
        $('#date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#date_range_filter')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#date_range_filter')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }
$(document).ready(function(){
   /* BEGIN: yajra datatable*/$('#employee_location_id').change(function () {
        employee_table.ajax.reload();
    });
    //employee list
    discount_table = $('#discount_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{action("\Modules\Discount\Http\Controllers\NewdiscountController@getdiscounts")}}',
            type: 'delete',
        },
        columns: [
            
                    {
                        data: null,
                        render: function(data, type, full, meta) {
                            return '<div class="btn-group dropleft"><button type="button" class="btn btn-sm dropdown-toffle hide-arrow" data-toggle="dropdown" arial-haspopup="true" arial-expanded="false"><i class="fa fa-ellipsis-v"></i></button><div class = "dropdown-menu" > <a  class ="dropdown-item regionInfo" title="show" data-id = "' +
                                data.id +
                                '"><i class="fa fa-info"></i><span> Info </span></a>' +
                                '<a class="drp-down-item" id="adddiscount">' +
                          '<i class="fa fa-plus"></i>Edit</a>' +
                                '<a class ="dropdown-item deletebtn" data-status data-toggle="modal" id="smallButton" data-target="#examplemodal-delete" data-attr title="Delete Record" data-id = "' +
                                data.id +
                                '"><i class="fa fa-trash"></i><span> Delete </span></a></div></div> ';
                        },
                        orderable: false,
                        searchable: false
                    }, {
                        data: 'data_time',
                        name: 'data_time'
                    },
                    {
                        data: 'sub_category',
                        name: 'sub_category'
                    },
                    {
                        data: 'user',
                        name: 'user'
                    },
                    {
                        data: 'max_discount',
                        name: 'max_discount'
                    },
                ],
        fnDrawCallback: function (oSettings) {
          
        },
    });
});
        $(document).on('click', 'a.delete-note', function(){
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete)=>{
                if(willDelete){
                    let href = $(this).data('href');

                    $.ajax({
                        method: 'delete',
                        url: href,
                        data: {  },
                        success: function(result) {
                            if(result.success == 1){
                                toastr.success(result.msg);
                            }else{
                                toastr.error(result.msg);
                            }
                            notes_table.ajax.reload();
                        },
                    });
                }
            });
        });

        $(document).on('click', '#add_note_note_btn', function(){
            $('.note_model').modal({
                backdrop: 'static',
                keyboard: false
            })
        })

        $(".note_model").on('hide.bs.modal', function(){
            tinymce.remove('#note_details');
        });

</script>
@endsection

