@extends('layouts.app')

@section('title', __('leads::lang.leads'))

@section('content')
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="row">
            <div class="col-md-3">
                <div class="form-group col-md-12">
                    {!! Form::label('date_range_filter', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range_filter', @format_date('first day of this month') . ' ~ ' .
                    @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control date_range', 'id' => 'date_range_filter', 'readonly']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group col-md-12">
                    {!! Form::label('sector_fitler', __( 'leads::lang.sector' )) !!}<br>
                    {!! Form::select('sector_fitler', ['private' => __('leads::lang.private'), 'government' =>
                    __('leads::lang.government')], null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'leads::lang.please_select' ), 'id' => 'sector_fitler']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group col-md-12">
                    {!! Form::label('category_id_fitler', __( 'leads::lang.category' )) !!}<br>
                    {!! Form::select('category_id_fitler', $categories, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'leads::lang.please_select' ), 'id' => 'category_id_fitler']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group col-md-12">
                    {!! Form::label('main_organization_fitler', __( 'leads::lang.main_organization' )) !!}<br>
                    {!! Form::select('main_organization_fitler', $main_organizations, null, ['class' => 'form-control
                    select2',
                    'required',
                    'placeholder' => __(
                    'leads::lang.please_select' ), 'id' => 'main_organization_fitler']);
                    !!}
                </div>
            </div>
            </div>
            <div class="row">
            <div class="col-md-3">
                <div class="form-group col-md-12">
                    {!! Form::label('business_fitler', __( 'leads::lang.business' )) !!}<br>
                    {!! Form::select('business_fitler', $businesses, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'leads::lang.please_select' ), 'id' => 'business_fitler']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group col-md-12">
                    {!! Form::label('town_fitler', __( 'leads::lang.town' )) !!}<br>
                    {!! Form::select('town_fitler', $towns, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'leads::lang.please_select' ), 'id' => 'town_fitler']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group col-md-12">
                    {!! Form::label('district_fitler', __( 'leads::lang.district' )) !!}<br>
                    {!! Form::select('district_fitler', $districts, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'leads::lang.please_select' ), 'id' => 'district_fitler']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group col-md-12">
                    {!! Form::label('mobile_no_fitler', __( 'leads::lang.mobile_no' )) !!} <br>
                    {!! Form::select('mobile_no_fitler', $mobile_nos, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'leads::lang.please_select' ), 'id' => 'mobile_no_fitler']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group col-md-12">
                    {!! Form::label('users_fitler', __( 'leads::lang.user' )) !!} <br>
                    {!! Form::select('users_fitler', $users, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'leads::lang.please_select' ), 'id' => 'users_fitler']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group col-md-12">
                    {!! Form::label('follow_up_date_fitler', __( 'leads::lang.follow_up_date' )) !!} <br>
                    {!! Form::text('follow_up_date_fitler', null, ['class' => 'form-control',
                    'required',
                    'placeholder' => __(
                    'leads::lang.follow_up_date' ), 'id' => 'follow_up_date_filter']);
                    !!}
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group col-md-12">
                    {!! Form::label('lead_no', __( 'leads::lang.lead_no' )) !!} <br>
                    {!! Form::select('lead_no', $lead_nos, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'leads::lang.lead_no' ), 'id' => 'lead_no']);
                    !!}
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group col-md-12">
                    {!! Form::label('label_id_filter', __( 'leads::lang.labels' )) !!} <br>
                    {!! Form::select('label_id_filter', $labels, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'leads::lang.labels' ), 'id' => 'label_id_filter']);
                    !!}
                </div>
            </div>
            
            </div>
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'leads::lang.all_leads')])
            @slot('tool')
            @can('leads.create')
            <div class="box-tools">
                <button type="button" class="btn btn-primary btn-modal pull-right" id="add_leads_btn"
                    data-href="{{action('\Modules\Leads\Http\Controllers\LeadsController@create')}}"
                    data-container=".leads_model">
                    <i class="fa fa-plus"></i> @lang( 'leads::lang.add' )</button>
            </div>
            @endcan
            @endslot

            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="leads_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <td colspan="2">
                                        <div style="display: flex; width: 100%;">
                                            {!! Form::open(['url' =>
                                            action('\Modules\Leads\Http\Controllers\LeadsController@massInvalid'),
                                            'method' => 'post', 'id' => 'mass_invalid_form' ]) !!}
                                            {!! Form::hidden('selected_rows', null, ['id' => 'selected_rows_invalid']);
                                            !!}
                                            {!! Form::submit(__('leads::lang.bulk_invalid'), array('class' => 'btn
                                            btn-xs btn-danger', 'id' => 'bulk-invalid')) !!}
                                            {!! Form::close() !!}

                                            {!! Form::open(['url' =>
                                            action('\Modules\Leads\Http\Controllers\LeadsController@massValid'),
                                            'method' => 'post', 'id' => 'mass_valid_form' ]) !!}
                                            {!! Form::hidden('selected_rows', null, ['id' => 'selected_rows_valid']);
                                            !!}
                                            {!! Form::submit(__('leads::lang.bulk_valid'), array('class' => 'btn btn-xs
                                            btn-primary', 'style' => 'margin-left: 5px;', 'id' => 'bulk-valid')) !!}
                                            {!! Form::close() !!}

                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th><input type="checkbox" id="select-all-row"></th>
                                    <th>@lang( 'messages.action' )</th>
                                    <th>@lang( 'leads::lang.follow_up_date' )</th>
                                    <th>@lang( 'leads::lang.lead_no' )</th>
                                    <th>@lang( 'leads::lang.date' )</th>
                                    <th>@lang( 'leads::lang.time' )</th>
                                    <th>@lang( 'leads::lang.sector' )</th>
                                    <th>@lang( 'leads::lang.category' )</th>
                                    <th>@lang( 'leads::lang.main_organization' )</th>
                                    
                                    <th>@lang( 'leads::lang.label_1' )</th>
                                    <th>@lang( 'leads::lang.label_2' )</th>
                                    <th>@lang( 'leads::lang.label_3' )</th>
                                    
                                    <th>@lang( 'leads::lang.business' )</th>
                                    <th>@lang( 'leads::lang.address' )</th>
                                    <th>@lang( 'leads::lang.town' )</th>
                                    <th>@lang( 'leads::lang.district' )</th>
                                    <th>@lang( 'leads::lang.mobile_no_1' )</th>
                                    <th>@lang( 'leads::lang.mobile_no_2' )</th>
                                    <th>@lang( 'leads::lang.mobile_no_3' )</th>
                                    <th>@lang( 'leads::lang.land_number' )</th>
                                    <th>@lang( 'leads::lang.email' )</th>
                                    <th>@lang( 'leads::lang.status' )</th>
                                    <th>@lang( 'leads::lang.user' )</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    <div class="modal fade leads_model add_modal" role="dialog" aria-labelledby="gridSystemModalLabel" id="addModal">
    </div>
</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
$("#follow_up_date_filter").datepicker({
    format: 'mm/dd/yyyy'
});
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
    
    $(document).on('click', '#add_leads_btn', function(){
        $("#addModal").removeClass("add_modal");
        $('.leads_model').modal({
            backdrop: 'static',
            keyboard: false
        })
    })

    $('#date_range_filter,#label_id_filter, #sector_fitler, #category_id_fitler, #main_organization_fitler, #business_fitler, #town_fitler, #district_fitler, #mobile_no_fitler, #users_fitler, #follow_up_date_filter,#lead_no').change(function(){
        leads_table.ajax.reload();
    })

    // leads_table
    leads_table = $('#leads_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url : "{{action('\Modules\Leads\Http\Controllers\LeadsController@index')}}",
                data: function(d){
                    d.start_date = $('#date_range_filter')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    d.end_date = $('#date_range_filter')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                    d.sector = $('#sector_fitler').val();
                    d.category_id = $('#category_id_fitler').val();
                    d.main_organization = $('#main_organization_fitler').val();
                    d.business = $('#business_fitler').val();
                    d.town = $('#town_fitler').val();
                    d.district = $('#district_fitler').val();
                    d.mobile_no = $('#mobile_no_fitler').val();
                    d.label_id = $('#label_id_filter').val();
                    d.created_by = $('#users_fitler').val();
                    d.follow_up_date = $('#follow_up_date_filter').data('datepicker').getFormattedDate('yy-mm-dd');
                    d.lead_no = $('#lead_no').val();
                }
            },
            columnDefs:[{
                    "targets": 1,
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'mass_delete'},
                {data: 'action', name: 'action'},
                {data: 'follow_up_date', name: 'follow_up_date'},
                {data: 'lead_no', name: 'lead_no'},
                {data: 'date', name: 'date'},
                {data: 'time', name: 'time'},
                {data: 'sector', name: 'sector'},
                {data: 'category', name: 'categories.name'},
                {data: 'main_organization', name: 'main_organization'},
                
                {data: 'label_1', name: 'labels.label_1'},
                {data: 'label_2', name: 'labels.label_2'},
                {data: 'label_3', name: 'labels.label_3'},
                
                {data: 'business', name: 'business'},
                {data: 'address', name: 'address'},
                {data: 'town', name: 'town'},
                {data: 'district', name: 'district'},
                {data: 'mobile_no_1', name: 'mobile_no_1'},
                {data: 'mobile_no_2', name: 'mobile_no_2'},
                {data: 'mobile_no_3', name: 'mobile_no_3'},
                {data: 'land_number', name: 'land_number'},
                {data: 'email', name: 'email'},
                {data: 'status', name: 'status'},
                {data: 'user', name: 'users.username'},
            ],
            @include('layouts.partials.datatable_export_button')
            createdRow: function( row, data, dataIndex ) {
                $( row ).find('td:eq(0)').attr('class', 'selectable_td');
            }
        });

        $(document).on('click', 'a.delete-leads', function(){
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
                            leads_table.ajax.reload();
                        },
                    });
                }
            });
        })
    
        $(document).on('click', 'a.change-status', function(){
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete)=>{
                if(willDelete){
                    let href = $(this).data('href');

                    $.ajax({
                        method: 'post',
                        url: href,
                        data: {  },
                        success: function(result) {
                            if(result.success == 1){
                                toastr.success(result.msg);
                            }else{
                                toastr.error(result.msg);
                            }
                            leads_table.ajax.reload();
                        },
                    });
                }
            });
        })

        $(document).on('click', '#bulk-invalid', function(e){
              e.preventDefault();
              var selected_rows = getSelectedRows();

              if(selected_rows.length > 0){
                $('input#selected_rows_invalid').val(selected_rows);
                swal({
                  title: LANG.sure,
                  icon: "warning",
                  buttons: true,
                  dangerMode: true,
                }).then((willDelete) => {
                  if (willDelete) {
                    var form = $('form#mass_invalid_form')

                    var data = form.serialize();
                    $.ajax({
                      method: form.attr('method'),
                      url: form.attr('action'),
                      dataType: 'json',
                      data: data,
                      success: function(result) {
                        if (result.success == true) {
                          toastr.success(result.msg);
                          leads_table.ajax.reload();
                          form
                          .find('#selected_rows_invalid')
                          .val('');
                        } else {
                          toastr.error(result.msg);
                        }
                      },
                    });
                  }
                });
              } else{
                $('input#selected_rows_invalid').val('');
                swal('@lang("lang_v1.no_row_selected")');
              }    
        });
        $(document).on('click', '#bulk-valid', function(e){
              e.preventDefault();
              var selected_rows = getSelectedRows();

              if(selected_rows.length > 0){
                $('input#selected_rows_valid').val(selected_rows);
                swal({
                  title: LANG.sure,
                  icon: "warning",
                  buttons: true,
                  dangerMode: true,
                }).then((willDelete) => {
                  if (willDelete) {
                    var form = $('form#mass_valid_form')

                    var data = form.serialize();
                    $.ajax({
                      method: form.attr('method'),
                      url: form.attr('action'),
                      dataType: 'json',
                      data: data,
                      success: function(result) {
                        if (result.success == true) {
                          toastr.success(result.msg);
                          leads_table.ajax.reload();
                          form
                          .find('#selected_rows_valid')
                          .val('');
                        } else {
                          toastr.error(result.msg);
                        }
                      },
                    });
                  }
                });
              } else{
                $('input#selected_rows_valid').val('');
                swal('@lang("lang_v1.no_row_selected")');
              }    
        });

        function getSelectedRows() {
            var selected_rows = [];
            var i = 0;
            $('.row-select:checked').each(function () {
                selected_rows[i++] = $(this).val();
            });

            return selected_rows; 
        }
    
</script>
@endsection