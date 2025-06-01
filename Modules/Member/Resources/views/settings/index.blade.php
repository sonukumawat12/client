@extends('layouts.app')
@section('title', __('member::lang.settings'))

@section('content')
<style>
    

.checkbox.checbox-switch {
    padding-left: 0;
}

.checkbox.checbox-switch label,
.checkbox-inline.checbox-switch {
    display: inline-block;
    position: relative;
    padding-left: 0;
}
.checkbox.checbox-switch label input,
.checkbox-inline.checbox-switch input {
    display: none;
}
.checkbox.checbox-switch label span,
.checkbox-inline.checbox-switch span {
    width: 35px;
    border-radius: 20px;
    height: 18px;
    border: 1px solid #dbdbdb;
    background-color: rgb(255, 255, 255);
    border-color: rgb(223, 223, 223);
    box-shadow: rgb(223, 223, 223) 0px 0px 0px 0px inset;
    transition: border 0.4s ease 0s, box-shadow 0.4s ease 0s;
    display: inline-block;
    vertical-align: middle;
    margin-right: 5px;
}
.checkbox.checbox-switch label span:before,
.checkbox-inline.checbox-switch span:before {
    display: inline-block;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: rgb(255,255,255);
    content: " ";
    top: 0;
    position: relative;
    left: 0;
    transition: all 0.3s ease;
    box-shadow: 0 1px 4px rgba(0,0,0,0.4);
}
.checkbox.checbox-switch label > input:checked + span:before,
.checkbox-inline.checbox-switch > input:checked + span:before {
    left: 17px;
}


/* Switch Default */
.checkbox.checbox-switch label > input:checked + span,
.checkbox-inline.checbox-switch > input:checked + span {
    background-color: rgb(180, 182, 183);
    border-color: rgb(180, 182, 183);
    box-shadow: rgb(180, 182, 183) 0px 0px 0px 8px inset;
    transition: border 0.4s ease 0s, box-shadow 0.4s ease 0s, background-color 1.2s ease 0s;
}
.checkbox.checbox-switch label > input:checked:disabled + span,
.checkbox-inline.checbox-switch > input:checked:disabled + span {
    background-color: rgb(220, 220, 220);
    border-color: rgb(220, 220, 220);
    box-shadow: rgb(220, 220, 220) 0px 0px 0px 8px inset;
    transition: border 0.4s ease 0s, box-shadow 0.4s ease 0s, background-color 1.2s ease 0s;
}
.checkbox.checbox-switch label > input:disabled + span,
.checkbox-inline.checbox-switch > input:disabled + span {
    background-color: rgb(232,235,238);
    border-color: rgb(255,255,255);
}
.checkbox.checbox-switch label > input:disabled + span:before,
.checkbox-inline.checbox-switch > input:disabled + span:before {
    background-color: rgb(248,249,250);
    border-color: rgb(243, 243, 243);
    box-shadow: 0 1px 4px rgba(0,0,0,0.1);
}

/* Switch Light */
.checkbox.checbox-switch.switch-light label > input:checked + span,
.checkbox-inline.checbox-switch.switch-light > input:checked + span {
    background-color: rgb(248,249,250);
    border-color: rgb(248,249,250);
    box-shadow: rgb(248,249,250) 0px 0px 0px 8px inset;
    transition: border 0.4s ease 0s, box-shadow 0.4s ease 0s, background-color 1.2s ease 0s;
}

/* Switch Dark */
.checkbox.checbox-switch.switch-dark label > input:checked + span,
.checkbox-inline.checbox-switch.switch-dark > input:checked + span {
    background-color: rgb(52,58,64);
    border-color: rgb(52,58,64);
    box-shadow: rgb(52,58,64) 0px 0px 0px 8px inset;
    transition: border 0.4s ease 0s, box-shadow 0.4s ease 0s, background-color 1.2s ease 0s;
}
.checkbox.checbox-switch.switch-dark label > input:checked:disabled + span,
.checkbox-inline.checbox-switch.switch-dark > input:checked:disabled + span {
    background-color: rgb(100, 102, 104);
    border-color: rgb(100, 102, 104);
    box-shadow: rgb(100, 102, 104) 0px 0px 0px 8px inset;
    transition: border 0.4s ease 0s, box-shadow 0.4s ease 0s, background-color 1.2s ease 0s;
}

/* Switch Success */
.checkbox.checbox-switch.switch-success label > input:checked + span,
.checkbox-inline.checbox-switch.switch-success > input:checked + span {
    background-color: rgb(40, 167, 69);
    border-color: rgb(40, 167, 69);
    box-shadow: rgb(40, 167, 69) 0px 0px 0px 8px inset;
    transition: border 0.4s ease 0s, box-shadow 0.4s ease 0s, background-color 1.2s ease 0s;
}
.checkbox.checbox-switch.switch-success label > input:checked:disabled + span,
.checkbox-inline.checbox-switch.switch-success > input:checked:disabled + span {
    background-color: rgb(153, 217, 168);
    border-color: rgb(153, 217, 168);
    box-shadow: rgb(153, 217, 168) 0px 0px 0px 8px inset;
}

/* Switch Danger */
.checkbox.checbox-switch.switch-danger label > input:checked + span,
.checkbox-inline.checbox-switch.switch-danger > input:checked + span {
    background-color: rgb(200, 35, 51);
    border-color: rgb(200, 35, 51);
    box-shadow: rgb(200, 35, 51) 0px 0px 0px 8px inset;
    transition: border 0.4s ease 0s, box-shadow 0.4s ease 0s, background-color 1.2s ease 0s;
}
.checkbox.checbox-switch.switch-danger label > input:checked:disabled + span,
.checkbox-inline.checbox-switch.switch-danger > input:checked:disabled + span {
    background-color: rgb(216, 119, 129);
    border-color: rgb(216, 119, 129);
    box-shadow: rgb(216, 119, 129) 0px 0px 0px 8px inset;
    transition: border 0.4s ease 0s, box-shadow 0.4s ease 0s, background-color 1.2s ease 0s;
}

/* Switch Primary */
.checkbox.checbox-switch.switch-primary label > input:checked + span,
.checkbox-inline.checbox-switch.switch-primary > input:checked + span {
    background-color: rgb(0, 105, 217);
    border-color: rgb(0, 105, 217);
    box-shadow: rgb(0, 105, 217) 0px 0px 0px 8px inset;
    transition: border 0.4s ease 0s, box-shadow 0.4s ease 0s, background-color 1.2s ease 0s;
}
.checkbox.checbox-switch.switch-primary label > input:checked:disabled + span,
.checkbox-inline.checbox-switch.switch-primary > input:checked:disabled + span {
    background-color: rgb(109, 163, 221);
    border-color: rgb(109, 163, 221);
    box-shadow: rgb(109, 163, 221) 0px 0px 0px 8px inset;
    transition: border 0.4s ease 0s, box-shadow 0.4s ease 0s, background-color 1.2s ease 0s;
}

/* Switch Info */
.checkbox.checbox-switch.switch-info label > input:checked + span,
.checkbox-inline.checbox-switch.switch-info > input:checked + span {
    background-color: rgb(23, 162, 184);
    border-color: rgb(23, 162, 184);
    box-shadow: rgb(23, 162, 184) 0px 0px 0px 8px inset;
    transition: border 0.4s ease 0s, box-shadow 0.4s ease 0s, background-color 1.2s ease 0s;
}
.checkbox.checbox-switch.switch-info label > input:checked:disabled + span,
.checkbox-inline.checbox-switch.switch-info > input:checked:disabled + span {
    background-color: rgb(102, 192, 206);
    border-color: rgb(102, 192, 206);
    box-shadow: rgb(102, 192, 206) 0px 0px 0px 8px inset;
    transition: border 0.4s ease 0s, box-shadow 0.4s ease 0s, background-color 1.2s ease 0s;
}

/* Switch Warning */
.checkbox.checbox-switch.switch-warning label > input:checked + span,
.checkbox-inline.checbox-switch.switch-warning > input:checked + span {
    background-color: rgb(255, 193, 7);
    border-color: rgb(255, 193, 7);
    box-shadow: rgb(255, 193, 7) 0px 0px 0px 8px inset;
    transition: border 0.4s ease 0s, box-shadow 0.4s ease 0s, background-color 1.2s ease 0s;
}
.checkbox.checbox-switch.switch-warning label > input:checked:disabled + span,
.checkbox-inline.checbox-switch.switch-warning > input:checked:disabled + span {
    background-color: rgb(226, 195, 102);
    border-color: rgb(226, 195, 102);
    box-shadow: rgb(226, 195, 102) 0px 0px 0px 8px inset;
    transition: border 0.4s ease 0s, box-shadow 0.4s ease 0s, background-color 1.2s ease 0s;
}

</style>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    
                    <li class="@if(empty(session('status.tab'))) active @endif">
                        <a href="#province" class="provinces" data-toggle="tab">
                            <strong>@lang('member::lang.provinces')</strong>
                        </a>
                    </li>
                    <li class="@if(session('status.tab') == 'district') active @endif">
                        <a href="#district" class="district" data-toggle="tab">
                            <strong>@lang('member::lang.district')</strong>
                        </a>
                    </li>
                   
                    <li class="@if(session('status.tab')  == 'electrorate') active @endif">
                        <a href="#electrorate" class="electrorate" data-toggle="tab">
                            <strong>@lang('member::lang.electrorate')</strong>
                        </a>
                    </li>
                   
                    
                    
                    <li class="@if(session('status.tab')  == 'gramaseva_vasama') active @endif">
                        <a href="#gramaseva_vasama" class="gramaseva_vasama" data-toggle="tab">
                            <strong>@lang('member::lang.gramaseva_vasama')</strong>
                        </a>
                    </li>
                    <!--<li class="@if(session('status.tab') =='balamandalaya') active @endif">-->
                    <!--    <a href="#balamandalaya" class="balamandalaya" data-toggle="tab">-->
                    <!--        <strong>@lang('member::lang.balamandalaya')</strong>-->
                    <!--    </a>-->
                    <!--</li>-->
                    <li class="@if(session('status.tab') =='member_group') active @endif">
                        <a href="#member_group" class="member_group" data-toggle="tab">
                            <strong>@lang('member::lang.member_group')</strong>
                        </a>
                    </li>
                    <li class="@if(session('status.tab') =='service_areas') active @endif">
                        <a href="#service_areas" class="service_areas" data-toggle="tab">
                            <strong>@lang('member::lang.service_areas')</strong>
                        </a>
                    </li>
                    {{-- Staff to Assign --}}
                    <li class="@if(session('status.tab') =='staff_to_assign') active @endif">
                        <a href="#staff_to_assign" class="staff_to_assign" data-toggle="tab">
                            <strong>@lang('member::lang.staff_to_assign')</strong>
                        </a>
                    </li>

                </ul>
                <div class="tab-content">
                    <div class="tab-pane @if(empty(session('status.tab'))) active @endif" id="province">
                        @include('member::settings.province.index')
                    </div>
                    <div class="tab-pane @if(session('status.tab') == 'district') active @endif" id="district">
                        @include('member::settings.district.index')
                    </div>
                    <div class="tab-pane @if(session('status.tab') == 'electrorate') active @endif" id="electrorate">
                        @include('member::settings.electrorate.index')
                    </div>
                    <div class="tab-pane @if(session('status.tab')  == 'gramaseva_vasama') active @endif" id="gramaseva_vasama">
                        @include('member::settings.gramaseva_vasama.index')
                    </div>
                    <div class="tab-pane @if(session('status.tab') =='balamandalaya') active @endif" id="balamandalaya">
                        @include('member::settings.balamandalaya.index')
                    </div>
                    <div class="tab-pane @if(session('status.tab') =='member_group') active @endif" id="member_group">
                        @include('member::settings.member_group.index')
                    </div>
                    <div class="tab-pane @if(session('status.tab') =='service_areas') active @endif" id="service_areas">
                        @include('member::settings.service_areas.index')
                    </div>
                    <div class="tab-pane @if(session('status.tab') =='staff_to_assign') active @endif" id="staff_to_assign">
                        @include('member::settings.staff.index')
                    </div>

                    


                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade common_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade districts_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade provinces_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade electrorate_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade gramaseva_vasama_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade balamandalaya_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade member_group_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade service_areas_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    
    
<div class="modal fade" id="add_new_district_modal" tabindex="-1" role="dialog" aria-labelledby="addDistrictModalLabel">
     <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="addDistrictModalLabel">@lang('member::lang.district')</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
          {!! Form::label('new_district_name', __('member::lang.district') . ':*') !!}
          <input type="text" class="form-control" id="new_district_name" placeholder="Enter District Name." required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="save_new_district">@lang('messages.add')</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
      </div>
    </div>
  </div>
</div>
    

<div class="modal fade" id="add_new_province_modal" tabindex="-1" role="dialog" aria-labelledby="addProvinceModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="addProvinceModalLabel">@lang('member::lang.add_province')</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
          {!! Form::label('new_province_name', __('member::lang.province') . ':*') !!}
          <input type="text" class="form-control" id="new_province_name" placeholder="Enter Province Name." required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="save_new_province">@lang('messages.add')</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
      </div>
    </div>
  </div>
</div>

    <div class="modal fade" id="add_new_electrorate_modal" tabindex="-1" role="dialog" aria-labelledby="addElectrorateModalLabel">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title" id="addElectrorateModalLabel">@lang('member::lang.electrorate')</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
            {!! Form::label('new_district_name', __('member::lang.electrorate') . ':*') !!}
            <input type="text" class="form-control" id="new_electrorate_name" placeholder="Enter Electrorate Name." required>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="save_new_electrorate">@lang('messages.add')</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
        </div>
        </div>
    </div>
    </div>

  <div class="modal fade" id="add_new_gsvasama_modal" tabindex="-1" role="dialog" aria-labelledby="addGsvasamaModalLabel">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title" id="addGsvasamaModalLabel">@lang('member::lang.gramaseva_vasama')</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
            {!! Form::label('new_gsvasama_name', __('member::lang.gramaseva_vasama') . ':*') !!}
            <input type="text" class="form-control" id="new_gsvasama_name" placeholder="Enter Gramasevavasama Name." required>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="save_new_gsvasama">@lang('messages.add')</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
        </div>
        </div>
    </div>
    </div>


</section>
<!-- /.content -->


@endsection
@section('javascript')
<script>
     if ($('#date_range_filter').length == 1) {
            $('#date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
                $('#date_range_filter').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );

                routes_table.ajax.reload();
            });
            $('#date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#date_range_filter')
                .data('daterangepicker')
                .setStartDate(moment().startOf('year'));
            $('#date_range_filter')
                .data('daterangepicker')
                .setEndDate(moment().endOf('year'));
    }

    // staff_assign_table

    $(document).ready(function() {
        staff_assign_table = $('#staff_assign_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{action('\Modules\Member\Http\Controllers\MemberStaffController@index')}}',
                    data: function(d) {
                    }
                },
                @include('layouts.partials.datatable_export_button')
                columns: [{
                        data: 'action',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'join_date',
                        name: 'join_date'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'job_id',
                        name: 'job_id'
                    }
                    ,
                    {
                        data: 'status',
                        name: 'status'
                    }
                    ,
                    {
                        data: 'created_by',
                        name: 'created_by'
                    }

                ],
                fnDrawCallback: function(oSettings) {

                },
            });
           
    });

    $(document).on('click', 'button.staff_to_assign_dlt', function(){
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
                            service_areas_table.ajax.reload();
                        },
                    });
                }
            });
        })
        $(document).on('change', '.change_status', function(){
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete)=>{
               
                if(willDelete){
                    let href = $(this).data('href');

                    $.ajax({
                        method: 'get',
                        url: href,
                        data: {  },
                        success: function(result) {
                            if(result.success == 1){
                                toastr.success(result.msg);
                            }else{
                                toastr.error(result.msg);
                            }
                            staff_assign_table.ajax.reload();
                        },
                    });
                }
            });
        })
        
        
        
     // gramaseva_vasama_table

     $(document).ready(function() {
        gramaseva_vasama_table = $('#gramaseva_vasama_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{action('\Modules\Member\Http\Controllers\GramasevaVasamaController@index')}}',
                    data: function(d) {
                        d.district =  $('#gramaseva_district').val();
                        d.province =  $('#gramaseva_province').val();
                        d.electrorate =  $('#gramaseva_electrorate').val();
                    }
                },
                @include('layouts.partials.datatable_export_button')
                columns: [{
                        data: 'action',
                        searchable: false,
                        orderable: false
                    },
                     {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'province_name',
                        name: 'province_name'
                    },
                    {
                        data: 'district_name',
                        name: 'district_name'
                    },
                    {
                        data: 'electrorate_name',
                        name: 'electrorate_name'
                    }
                    ,
                    {
                        data: 'gramaseva_vasama',
                        name: 'gramaseva_vasama'
                    },
                    {
                        data: 'username',
                        name: 'username'
                    }

                ],
                fnDrawCallback: function(oSettings) {

                },
            });
            $('#gramaseva_district').change(function () {
                gramaseva_vasama_table.ajax.reload();
            });
            $('#gramaseva_province').change(function () {
                gramaseva_vasama_table.ajax.reload();
            });
            $('#gramaseva_electrorate').change(function () {
                gramaseva_vasama_table.ajax.reload();
            });
    });
 $(document).on('click', 'button.gramaseva_vasama_delete', function(){
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
                            gramaseva_vasama_table.ajax.reload();
                        },
                    });
                }
            });
        })
   

    var electrorate_table;
    $(document).ready(function() {
            electrorate_table = $('#electrorate_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{action('\Modules\Member\Http\Controllers\ElectrorateController@index')}}',
                    data: function(d) {
                        d.district_id = $('#district_id').val();
                        // d.orignal_location = $('#orignal_locations').val();
                        // d.destination = $('#destinations').val();
                        // d.user_id = $('#users').val();
                        // var start_date = $('input#date_range_filter')
                        //     .data('daterangepicker')
                        //     .startDate.format('YYYY-MM-DD');
                        // var end_date = $('input#date_range_filter')
                        //     .data('daterangepicker')
                        //     .endDate.format('YYYY-MM-DD');
                        // d.start_date = start_date;
                        // d.end_date = end_date;
                    }
                },
                @include('layouts.partials.datatable_export_button')
                columns: [{
                        data: 'action',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'province_name',
                        name: 'province_name'
                    },
                    {
                        data: 'district_name',
                        name: 'district_name'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'username',
                        name: 'username'
                    }

                ],
                fnDrawCallback: function(oSettings) {

                },
            });
            $('#district_id').change(function () {
                electrorate_table.ajax.reload();
            });
    })

  $(document).on('click', 'button.electrorate_delete', function(){
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
                            electrorate_table.ajax.reload();
                        },
                    });
                }
            });
        })
   
    
     //districts_table
    districts_table = $('#districts_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{action('\Modules\Member\Http\Controllers\DistrictController@index')}}",
            columnDefs:[{
                    "targets": 1,
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'name', name: 'name'},
                {data: 'province_name', name: 'province_name'},
                {data: 'username', name: 'username'},
                {data: 'action', name: 'action'}
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });
        $(document).on('click', 'button.district_delete', function(){
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
                            districts_table.ajax.reload();
                        },
                    });
                }
            });
        })
    //provice_table
    province_table = $('#province_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{action('\Modules\Member\Http\Controllers\ProvinceController@index')}}",
            columnDefs:[{
                    "targets": 1,
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'name', name: 'name'},
                {data: 'country', name: 'country'},
                {data: 'user_name', name: 'user_name'},
                {data: 'action', name: 'action'}
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });
        $(document).on('click', 'button.province_delete', function(){
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
                            province_table.ajax.reload();
                        },
                    });
                }
            });
        })
    


        // gramaseva_vasama_table = $('#gramaseva_vasama_table').DataTable({
        //     processing: true,
        //     serverSide: false,
        //     ajax: "{{action('\Modules\Member\Http\Controllers\GramasevaVasamaController@index')}}",
        //     columnDefs:[{
        //             "targets": 1,
        //             "orderable": false,
        //             "searchable": false
        //         }],
        //     columns: [
        //         {data: 'date', name: 'date'},
        //         {data: 'gramaseva_vasama', name: 'gramaseva_vasama'},
        //         {data: 'action', name: 'action'}
        //     ],
        //     "fnDrawCallback": function (oSettings) {
        //     }
        // });

        $(document).on('click', 'button.note_group_delete', function(){
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
                            gramaseva_vasama_table.ajax.reload();
                        },
                    });
                }
            });
        })
    // balamandalaya_table
        balamandalaya_table = $('#balamandalaya_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{action('\Modules\Member\Http\Controllers\BalamandalayaController@index')}}",
            columnDefs:[{
                    "targets": 1,
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'date', name: 'date'},
                {data: 'gramaseva_vasama', name: 'gramaseva_vasama'},
                {data: 'balamandalaya', name: 'balamandalaya'},
                {data: 'action', name: 'action'}
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });

        $(document).on('click', 'button.task_group_delete', function(){
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
                            balamandalaya_table.ajax.reload();
                        },
                    });
                }
            });
        })


    // member_group_table
        member_group_table = $('#member_group_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{action('\Modules\Member\Http\Controllers\MemberGroupController@index')}}",
            columnDefs:[{
                    "targets": 1,
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'date', name: 'date'},
                {data: 'member_group', name: 'member_group'},
                {data: 'action', name: 'action'}
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });

        $(document).on('click', 'button.member_group_delete', function(){
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
                            member_group_table.ajax.reload();
                        },
                    });
                }
            });
        })


    // service_areas_table
        service_areas_table = $('#service_areas_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{action('\Modules\Member\Http\Controllers\ServiceAreasController@index')}}",
            columnDefs:[{
                    "targets": 1,
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'date', name: 'date'},
                {data: 'service_area', name: 'service_area'},
                {data: 'action', name: 'action'}
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });

        $(document).on('click', 'button.service_areas_delete', function(){
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
                            service_areas_table.ajax.reload();
                        },
                    });
                }
            });
        })
        $(document).on('change', '#district_electrorate', function(){
        $.ajax({
            type: "POST",
            url: "{{action('\Modules\Member\Http\Controllers\DistrictController@get')}}",
            data: {
                'id' : $(this).val(),
            },
            dataType: "json",
            success: function (response) {
                $('#province_select').val(response.province).trigger('change');
            }
        });
        }); 
        $('#district_id').select2({
            width: '100%'
        });

        
</script>
@endsection