@extends('layouts.app')

@section('title', __('sms::lang.sms'))

@section('content')
@php
   
   $business_id = request()
        ->session()
        ->get('user.business_id');
    
    $pacakge_details = [];
        
    $subscription = Modules\Superadmin\Entities\Subscription::active_subscription($business_id);
    if (!empty($subscription)) {
        $pacakge_details = $subscription->package_details;
    }

@endphp


<!-- Main content -->
<section class="content">
    
    <div class="row">
        <div class="col-md-12 dip_tab">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    @if(!empty($pacakge_details['list_sms']) && auth()->user()->can('sms_list_sms'))
                    <li class="active" style="margin-left: 20px;">
                        <a style="font-size:13px;" href="#sms" class="" data-toggle="tab">
                            <i class="fa fa-superpowers"></i> <strong>@lang('sms::lang.sms')</strong>
                        </a>
                    </li>
                    @endif
                    
                    @if(!empty($pacakge_details['sms_history']) && auth()->user()->can('sms_history'))
                    <li class="">
                        <a style="font-size:13px;" href="#sms_history" data-toggle="tab">
                            <i class="fa fa-thermometer"></i> <strong>@lang('sms::lang.sms_history')</strong>
                        </a>
                    </li>
                    @endif
                    
                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        @if(!empty($pacakge_details['list_sms']) && auth()->user()->can('sms_list_sms'))
        <div class="tab-pane active" id="sms">
            @include('sms::list_sms.list_sms')
        </div>
        @endif
        
        @if(!empty($pacakge_details['sms_history']) && auth()->user()->can('sms_history'))
        <div class="tab-pane" id="sms_history">
            @include('sms::list_sms.sms_history')
        </div>
        @endif
    </div>
    
    <div class="modal fade sms_model" role="dialog" aria-labelledby="gridSystemModalLabel">
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
<script>

    $(document).on('click', '.msg_btn', function(e){
      let note = $(this).data('string');
      // Replace newline characters with <br>
      note = note.replace(/\n/g, '<br>');
      $("#msgContent").html(note);
      $("#msgModal").modal('show');
    });  


    $(document).ready(function(){
        
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
                url: "{{ url('sms/sms-logs') }}",
                data: function(d) {
                    
                    var start = '';
                      var end = '';
    
                      if($('#history_date_range').val()){
                        start = $('#history_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        end = $('#history_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                      }
    
                      d.start_date = start;
                      d.end_date = end;
                    
                    
                    d.username = $('#username').val(); 
                    d.sender_name = $('#sender_name').val(); 
                    d.sms_type_ = $('#sms_type_').val();
                    d.sms_status = $('#sms_status').val();
                    
                    
                },
            },
            columns: refill_business_cols,
        });
        
        
        $(document).on('change', '#username,#sender_name,#sms_type_,#sms_status',  function() {
            sms_history_table.ajax.reload();
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
    
        $('#date_range_filter').change(function(){
            sms_table.ajax.reload();
        })
    
        // // sms_table
        sms_table = $('#sms_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url : "{{action('\Modules\SMS\Http\Controllers\SMSController@index')}}",
                data: function(d){
                    d.start_date = $('#date_range_filter')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    d.end_date = $('#date_range_filter')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                    // d.sector = $('#sector_fitler').val();
                    // d.category_id = $('#category_id_fitler').val();
                    // d.main_organization = $('#main_organization_fitler').val();
                    // d.business = $('#business_fitler').val();
                    // d.town = $('#town_fitler').val();
                    // d.district = $('#district_fitler').val();
                    // d.mobile_no = $('#mobile_no_fitler').val();
                    // d.created_by = $('#users_fitler').val();
                }
            },
            columnDefs:[{
                    "targets": 1,
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'action', name: 'action'},
                {data: 'schedule_date_time', name: 'schedule_date_time'},
                {data: 'id', name: 'id'},
                {data: 'count_message', name: 'count_message'},
            ]
        });
        
    });
        
        
    $(document).on('click', '#add_sms_btn', function(){
        $('.sms_model').modal({
            backdrop: 'static',
            keyboard: false
        })
    })
    $(document).on('click', 'a.delete-sms', function(){
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
                        sms_table.ajax.reload();
                    },
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
@endsection