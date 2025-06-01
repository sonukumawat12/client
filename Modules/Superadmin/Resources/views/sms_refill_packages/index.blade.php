@extends('layouts.app')
@section('title', __('superadmin::lang.superadmin') . ' | ' . __('superadmin::lang.packages'))

@section('content')
	
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('superadmin::lang.packages') <small>@lang('superadmin::lang.all_packages')</small></h1>
    <!-- <ol class="breadcrumb">
        <a href="#"><i class="fa fa-dashboard"></i> Level</a><br/>
        <li class="active">Here<br/>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
	@include('superadmin::layouts.partials.currency')
	
	<div class="row"> 
                <div class="col-md-12">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="@if(empty(session('status.tab'))) active @endif">
                                <a href="#sms_packages" data-toggle="tab" aria-expanded="true">
                                    @lang('superadmin::lang.sms_packages')
                                </a>
                            </li>
                            
                            <li class="@if(session('status.tab') == 'refill_business') active @endif">
                                <a href="#refill_business" data-toggle="tab" aria-expanded="true">
                                    @lang('superadmin::lang.refill_business')
                                </a>
                            </li>
                            
                            <li class="@if(session('status.tab') == 'external_api_clients') active @endif">
                                <a href="#external_api_clients" data-toggle="tab" aria-expanded="true">
                                    @lang('superadmin::lang.external_api_clients')
                                </a>
                            </li>
                            
                            
                            <li class="@if(session('status.tab') == 'sms_summary') active @endif">
                                <a href="#sms_summary" data-toggle="tab" aria-expanded="true">
                                    @lang('sms::lang.sms_summary')
                                </a>
                            </li>
                            
                            <li class="@if(session('status.tab') == 'sms_history') active @endif">
                                <a href="#sms_history" data-toggle="tab" aria-expanded="true">
                                    @lang('superadmin::lang.sms_history')
                                </a>
                            </li>
                            
                            <li class="@if(session('status.tab') == 'sms_reminder_settings') active @endif">
                                <a href="#sms_reminder_settings" data-toggle="tab" aria-expanded="true">
                                    @lang('superadmin::lang.sms_reminder_settings')
                                </a>
                            </li>
                            
                            
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane @if(empty(session('status.tab'))) active @endif" id="sms_packages">
                                @include('superadmin::sms_refill_packages.partials.sms_packages')
                            </div>
                            
                            <div class="tab-pane @if(session('status.tab') == 'refill_business') active @endif" id="refill_business">
                                @include('superadmin::sms_refill_packages.partials.refill_business')
                            </div>
                            
                            <div class="tab-pane @if(session('status.tab') == 'external_api_clients') active @endif" id="external_api_clients">
                                @include('superadmin::sms_refill_packages.partials.external_api_clients')
                            </div>
                            
                            <div class="tab-pane @if(session('status.tab') == 'sms_summary') active @endif" id="sms_summary">
                                @include('superadmin::sms_refill_packages.partials.sms_summary')
                            </div>
                            
                            <div class="tab-pane @if(session('status.tab') == 'sms_history') active @endif" id="sms_history">
                                @include('superadmin::sms_refill_packages.partials.sms_history')
                            </div>
                            
                            <div class="tab-pane @if(session('status.tab') == 'sms_reminder_settings') active @endif" id="sms_reminder_settings">
                                @include('superadmin::sms_refill_packages.partials.sms_reminder_settings')
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>


    <div class="modal fade packages_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>
    
    <div class="modal fade" id="noteModal" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
        <div class="modal-dialog">
          <div class="modal-content">
    
            <!-- Modal Header -->
            <div class="modal-header">
              <h4 class="modal-title">@lang( 'lang_v1.note' )</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
    
            <!-- Modal Body -->
            <div class="modal-body">
              <p id="noteContent" class="text-center text-bold"></p>
            </div>
    
          </div>
        </div>
      </div>


<div class="modal fade" id="msgModal" role="dialog" 
      aria-labelledby="gridSystemModalLabel">
        <div class="modal-dialog">
          <div class="modal-content">
    
            <!-- Modal Header -->
            <div class="modal-header">
              <h4 class="modal-title">@lang( 'superadmin::lang.message' )</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
    
            <!-- Modal Body -->
            <div class="modal-body">
              <p id="msgContent" class="text-center"></p>
            </div>
    
          </div>
        </div>
      </div>
    
    
</section>
<!-- /.content -->

@endsection

@section('javascript')
<!-- START: package subscription scripts-->
<script>

$(document).ready(function(){
       $('#days_1_status').trigger('change');
       $('#days_2_status').trigger('change');
       $('#days_3_status').trigger('change');
       $('#days_4_status').trigger('change');
       
       $('#days_1_status').change(function() {
            var isChecked = $(this).prop('checked');
            if(isChecked){
                $(".days_1").prop('readonly', false);
                $(".days_1").prop('required', true);
            } else {
                $(".days_1").prop('readonly', true);
                $(".days_1").prop('required', false);
            }
        });
        
       $('#days_2_status').change(function() {
            var isChecked = $(this).prop('checked');
            if(isChecked){
                $(".days_2").prop('readonly', false);
                $(".days_2").prop('required', true);
            } else {
                $(".days_2").prop('readonly', true);
                $(".days_2").prop('required', false);
            }
        });
        
       $('#days_3_status').change(function() {
            var isChecked = $(this).prop('checked');
            if(isChecked){
                $(".days_3").prop('readonly', false);
                $(".days_3").prop('required', true);
            } else {
                $(".days_3").prop('readonly', true);
                $(".days_3").prop('required', false);
            }
        });
        
       $('#days_4_status').change(function() {
            var isChecked = $(this).prop('checked');
            if(isChecked){
                $(".days_4").prop('readonly', false);
                $(".days_4").prop('required', true);
            } else {
                $(".days_4").prop('readonly', true);
                $(".days_4").prop('required', false);
            }
        });
        
       $('#days_1_status').trigger('change');
       $('#days_2_status').trigger('change');
       $('#days_3_status').trigger('change');
       $('#days_4_status').trigger('change');
        
   })

    
    $(document).on('click', '.msg_btn', function(e){
      let note = $(this).data('string');
      // Replace newline characters with <br>
      note = note.replace(/\n/g, '<br>');
      $("#msgContent").html(note);
      $("#msgModal").modal('show');
    });  

    $(document).on('change','#default_gateway', function(){
        
        if($(this).val() == 'ultimate_sms'){
            $(".direct").addClass('hide');
            $(".hutch_sms").addClass('hide');
            $(".ultimate_sms").removeClass('hide');
        }else if($(this).val() == 'hutch_sms'){
            $(".direct").addClass('hide');
            $(".hutch_sms").removeClass('hide');
            $(".ultimate_sms").addClass('hide');
        }else{
            $(".direct").removeClass('hide');
            $(".hutch_sms").addClass('hide');
            $(".ultimate_sms").addClass('hide');
        }
    })
    

    $(document).on('click', '.note_btn', function(e){
      let note = $(this).data('string');
      $("#noteContent").html(note);
      $("#noteModal").modal('show');
       
    });
        
    $(document).ready(function(){
    

        var sms_packages_cols = [
            { data: 'date', name: 'date' },
            { data: 'name', name: 'name' },
            { data: 'unit_cost', name: 'unit_cost' },
            { data: 'amount', name: 'amount' },
            { data: 'no_of_sms', name: 'no_of_sms' },
            { data: 'username', name: 'users.username' },
            { data: 'action', name: 'action', searchable: false },
        ];
        
        
        sms_packages_table = $('#sms_packages_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('superadmin/smsrefill-package') }}",
                data: function(d) {
                    
                    
                },
            },
            columns: sms_packages_cols,
        });
        
        
        $('#date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                $("#report_date_range").text("Date Range: "+ $("#date_range").val());
                refill_business_table.ajax.reload();
            }
        );
        $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#date_range').val('');
            $("#report_date_range").text("Date Range: - ");
            refill_business_table.ajax.reload();
        });
        
        $('#date_range').data('daterangepicker').setStartDate(moment().startOf('month'));
    
        $('#date_range').data('daterangepicker').setEndDate(moment().endOf('month'));
        
        
        var refill_business_cols = [
            { data: 'date', name: 'date' },
            { data: 'type', name: 'type' },
            { data: 'business_name', name: 'business_name', searchable: false },
            { data: 'package_name', name: 'sms_refill_packages.name' },
            { data: 'amount', name: 'amount' },
            { data: 'no_of_sms', name: 'no_of_sms' },
            { data: 'expiry_date', name: 'refill_business.expiry_date' },
            { data: 'payment_method', name: 'payment_method' },
            { data: 'username', name: 'users.username' },
            { data: 'action', name: 'action', searchable: false },
        ];
        
        
        refill_business_table = $('#refill_business_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('superadmin/refill-business') }}",
                data: function(d) {
                    
                    var start = '';
                      var end = '';
    
                      if($('#date_range').val()){
                        start = $('#date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        end = $('#date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                      }
    
                      d.start_date = start;
                      d.end_date = end;
                    
                    d.business_id = $('#filter_business_id').val(); 
                    
                    var selectedOption = $('#filter_business_id').find('option:selected');
                    d.type = selectedOption.data('string') || '';
                    
                    d.package_id = $('#filter_package_id').val(); 
                    d.payment_method = $('#filter_payment_method').val(); 
                    d.created_by = $('#filter_created_by').val();
                    
                    d.business_type = $('#filter_type').val();
                    
                },
            },
            columns: refill_business_cols,
        });
        
        
        $(document).on('change', '#filter_business_id, #filter_package_id, #filter_payment_method, #filter_created_by,#filter_type',  function() {
            refill_business_table.ajax.reload();
        });
        
        
        sms_summary_table = $('#sms_summary_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('superadmin/sms-summary') }}",
                data: function(d) {
                    
                },
            },
            columns: [
                { data: 'name', name: 'name' },
                { data: 'type', name: 'type' },
                { data: 'sms_balance', name: 'sms_balance', searchable: false },
                { data: 'action', name: 'action', searchable: false },
            ],
        });
        
        
        
        external_api_clients_table = $('#external_api_clients_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('superadmin/sms-api-clients') }}",
                data: function(d) {
                    
                },
            },
            columns: [
                { data: 'date', name: 'date' },
                { data: 'name', name: 'name'},
                { data: 'contact_mobile', name: 'contact_mobile'},
                { data: 'land_no', name: 'land_no'},
                { data: 'contact_name', name: 'contact_name'},
                { data: 'api_key', name: 'api_key'},
                { data: 'sender_names', name: 'sender_names'},
                { data: 'username', name: 'username'},
                { data: 'password', name: 'password'},
                { data: 'action', name: 'action', searchable: false },
            ],
        });
        
        
        
        
        $('#history_date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#history_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                $("#report_history_date_range").text("Date Range: "+ $("#history_date_range").val());
                sms_history_table.ajax.reload();
            }
        );
        $('#history_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#history_date_range').val('');
            $("#report_history_date_range").text("Date Range: - ");
            sms_history_table.ajax.reload();
        });
        
        $('#history_date_range').data('daterangepicker').setStartDate(moment().startOf('month'));
    
        $('#history_date_range').data('daterangepicker').setEndDate(moment().endOf('month'));
        
        
        var refill_business_cols = [
            { data: 'created_at', name: 'created_at' },
            { data: 'id', name: 'id' },
            { data: 'business_name', name: 'business_name', searchable: false },
            { data: 'business_type', name: 'business_type' },
            { data: 'username', name: 'username' },
            { data: 'sender_name', name: 'sender_name' },
            { data: 'recipient', name: 'recipient' },
            { data: 'message', name: 'message'},
            { data: 'sms_type_', name: 'sms_type_' },
            { data: 'no_of_sms', name: 'no_of_sms' },
            { data: 'sms_status', name: 'sms_status'},
        ];
        
        
        sms_history_table = $('#sms_history_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('superadmin/get-history') }}",
                data: function(d) {
                    
                    var start = '';
                      var end = '';
    
                      if($('#history_date_range').val()){
                        start = $('#history_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        end = $('#history_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                      }
    
                      d.start_date = start;
                      d.end_date = end;
                    
                    d.business_id = $('#history_business_id').val(); 
                    
                    var selectedOption = $('#history_business_id').find('option:selected');
                    d.type = selectedOption.data('string') || '';
                    
                    d.username = $('#username').val(); 
                    d.sender_name = $('#sender_name').val(); 
                    d.sms_type_ = $('#sms_type_').val();
                    d.sms_status = $('#sms_status').val();
                    d.business_type = $('#history_type').val();
                    
                    
                },
            },
            columns: refill_business_cols,
        });
        
        
        $(document).on('change', '#history_business_id,#username,#sender_name,#sms_type_,#sms_status,#history_type',  function() {
            sms_history_table.ajax.reload();
        });
        
    });
</script>
<!--END: package subscription scripts -->

<!-- start: Tenant management-->
<script>
    $(document).on('click', '.delete_record', function(){
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete)=>{
                if(willDelete){
                     var url = $(this).data('href');
                     $.ajax({
                         method: "delete",
                         url: url,
                         dataType: "json",
                         success: function(result){
                             if(result.success == true){
                                toastr.success(result.msg);
                                sms_packages_table.ajax.reload();
                                refill_business_table.ajax.reload();
                             }else{
                                toastr.error(result.msg);
                            }

                        }
                    });
                }
            });
        });
        
        $(document).on('submit','#sms_list_interest_form', function(event) {
            // Prevent the default form submission
            event.preventDefault();
            
    
            // Perform AJAX request
            $.ajax({
                method: 'POST',
                url: $(this).attr('action'),
                data: $(this).serialize(), // Serialize form data
                success: function(response) {
                    if(response.success == true){
                        toastr.success(response.msg);
                        $("#form_no").val(response.form_no);
                        $(".sms_list_fields").val("");
                        sms_list_interests_table.ajax.reload();
                        sms_summary_table.ajax.reload();
                    }else{
                        toastr.error(response.msg);
                    }
                    
                    
                    
                },
                error: function(xhr, status, error) {
                    console.error(error); // Log any errors
                }
            });
        });
</script>
<!-- END: Tenant management-->

@endsection