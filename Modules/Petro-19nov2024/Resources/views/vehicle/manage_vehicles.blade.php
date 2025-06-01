@extends('layouts.app')
@section('title', 'Registered Vehicle Details')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Registered Vehicle Details
        <small>Vehicle Details</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">

    @include('superadmin::layouts.partials.currency')

    <div class="tab-content">
      <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-body">
                          <div class="table-responsive">
                              <table class="table table-bordered table-striped" id="vehicle_details_table">
                                  <thead>
                                      <tr>
                                          <th>Date & Time</th>
                                          <th>Vehicle No</th>
                                          <th>Vehicle Category </th>
                                          <th>Fuel type</th>
                                          <th>Name</th>
                                          <th>Town</th>
                                          <th>District</th>
                                          <th>Mobile No</th>
                                          <th>@lang( 'superadmin::lang.action' )</th>
                                      </tr>
                                  </thead>
                              </table>
                          </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    </div>

    <div class="modal fade" id="vehicleModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>

</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
    $(document).ready(function(){

        // vehicle_details_table
        var vehicle_details_table = $('#vehicle_details_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/vehicles',
            columnDefs:[{
                "targets": 0,
                "orderable": false,
                "searchable": false
            }],
        });

        // update_vehicle button
        $(document).on('click', 'button.update_vehicle', function(){
          $("div#vehicleModal").load($(this).data('href'), function(){
              $(this).modal('show');
              $("form#update_vehicle_form").submit(function(e){
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
                              $("div#vehicleModal").modal('hide');
                              toastr.success(result.msg);
                              vehicle_details_table.ajax.reload();
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
                          vehicle_details_table.ajax.reload();
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