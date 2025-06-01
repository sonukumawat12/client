@extends('layouts.app')
@section('title', 'Superadmin Subscription')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'superadmin::lang.subscription' )
        <small>@lang( 'superadmin::lang.view_subscription' )</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">

    @include('superadmin::layouts.partials.currency')

    <div class="row">
        <div class="col-md-12 dip_tab">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    <li class="active" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#superadmin_subscription" class="" data-toggle="tab">
                            <i class="fa fa-superpowers"></i>
                            <strong>@lang('superadmin::lang.superadmin_subscription')</strong>
                        </a>
                    </li>
                    <li class="" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#family_subscription" class="" data-toggle="tab">
                            <i class="fa fa-users"></i>
                            <strong>@lang('superadmin::lang.family_subscription')</strong>
                        </a>
                    </li>
                    
                    <li class="" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#module_subscription" class="" data-toggle="tab">
                            <i class="fa fa-users"></i>
                            <strong>Module Subscription</strong>
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane active" id="superadmin_subscription">
            @include('superadmin::superadmin_subscription.superadmin_subscription')
        </div>
        <div class="tab-pane" id="family_subscription">
            @include('superadmin::superadmin_subscription.family_subscription')
        </div>
        <div class="tab-pane" id="module_subscription">
            @include('superadmin::superadmin_subscription.module_subscription')
        </div>
    </div>

    <!--<div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>-->

</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
    $(document).ready(function(){
        
        
        if ($('#expiry_date_range').length == 1) {
            $('#expiry_date_range').daterangepicker(dateRangeSettings, function(start, end) {
                $('#expiry_date_range').val(
                    start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
                );
               
            });
            $('#expiry_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#expiry_date_range').val('');
            });
            $('#expiry_date_range')
                .data('daterangepicker')
                .setStartDate(moment().startOf('year'));
            $('#expiry_date_range')
                .data('daterangepicker')
                .setEndDate(moment().endOf('year'));
        }
        
        var modules_sub_cols = [
            { data: 'name', name: 'name' },
            { data: 'business', name: 'business' },
            { data: 'status', name: 'status' },
            { data: 'activated_on', name: 'activated_on' },
            { data: 'expired_on', name: 'expired_on' },
            { data: 'price', name: 'price' },
        ];
        
        $(
            '#expiry_date_range,' +
            '#status,' +
            '#expired_on,' +
            '#modules,' +
            '#business'
        ).change(function() {
            modules_sub_table.ajax.reload();
        });
        
        modules_sub_table = $('#module_subscription_table').DataTable({
                processing: true,
                serverSide: true,
                scrollY: "75vh",
                scrollX:        true,
                scrollCollapse: true,
                ajax: {
                    url: "{{ url('module-subscription') }}",
                    data: function(d) {
                        
                        var dateRange = $('#expiry_date_range').val();
                        
                        d.start_date = dateRange.split(' - ')[0];
                        d.end_date = dateRange.split(' - ')[1];
                        d.status = $("#status").val();
                        d.expired_on = $("#expired_on").val();
                        d.module_name = $("#modules").val();
                        d.business_id = $("#business").val();
                        
                    },
                },
                columns: modules_sub_cols,
            });
                

        // superadmin_subscription_table
        var superadmin_subscription_table = $('#superadmin_subscription_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/superadmin/superadmin-subscription',
            columnDefs:[{
                    "targets": 10,
                    "orderable": false,
                    "searchable": false
                }],
            "fnDrawCallback": function (oSettings) {
                __currency_convert_recursively($('#superadmin_subscription_table'), true);
            }
        });

        // family_subscription_table
        var family_subscription_table = $('#family_subscription_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/superadmin/family-subscription',
            columnDefs:[{
                    "targets": 6,
                    "orderable": false,
                    "searchable": false
                }],
            "fnDrawCallback": function (oSettings) {
                __currency_convert_recursively($('#family_subscription_table'), true);
            }
        });


        // change_status button
        $(document).on('click', 'button.change_status', function(){
            $("div#statusModal").load($(this).data('href'), function(){
                $(this).modal('show');
                $("form#status_change_form, form#fs_status_change_form").submit(function(e){
                    e.preventDefault();
                    var url = $(this).attr("action");
                    var data = $(this).serialize();
                    $.ajax({
                        method: "POST",
                        dataType: "json",
                        data: data,
                        url: url,
                        success:function(result){
                            if( result.success == true){
                                $("div#statusModal").modal('hide');
                                toastr.success(result.msg);
                                superadmin_subscription_table.ajax.reload();
                                family_subscription_table.ajax.reload();
                            }else{
                                toastr.error(result.msg);
                            }
                        }
                    });
                });
            });
        });

        $(document).on('shown.bs.modal', '.view_modal', function(){
            $('.edit-subscription-modal .datepicker').datepicker({
                autoclose: true,
                format:datepicker_date_format
            });
            $("form#edit_subscription_form").submit(function(e){
              e.preventDefault();
              var url = $(this).attr("action");
              var data = $(this).serialize();
              $.ajax({
                  method: "POST",
                  dataType: "json",
                  data: data,
                  url: url,
                  success:function(result){
                      if( result.success == true){
                          $("div.view_modal").modal('hide');
                          toastr.success(result.msg);
                          superadmin_subscription_table.ajax.reload();
                          family_subscription_table.ajax.reload();
                      }else{
                          toastr.error(result.msg);
                      }
                  }
              });
            });
        });

    });
</script>
@endsection