@extends('layouts.app')

@section('title', __('sms::lang.sms_ledger'))

@section('content')
<!-- Main content -->
<section class="content">
    <div class="row">
        @component('components.filters', ['title' => __('report.filters')])
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('history_date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('history_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class'
                        => 'form-control', 'readonly']); !!}
                    </div>
                </div>
                
               
              
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('sender_name', __('superadmin::lang.sender_name') . ':') !!}
                        {!! Form::select('sender_name', $sender_names, null, ['class' => 'form-control
                        select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
                
                
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('sms_status', __('superadmin::lang.sms_status') . ':') !!}
                        {!! Form::select('sms_status', $sms_status, null, ['class' => 'form-control
                        select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
                
               
                
            </div>
        @endcomponent
        
        
        @component('components.widget', ['class' => 'box-primary', 'title' => __( 'superadmin::lang.sms_history' )])
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="sms_history_table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>@lang( 'superadmin::lang.date' )</th>
                        <th>@lang( 'superadmin::lang.id_no' )</th>
                        <th>@lang( 'superadmin::lang.sender_name' )</th>
                        <th>@lang( 'superadmin::lang.phone_no' )</th>
                        <th>@lang( 'superadmin::lang.message' )</th>
                        <th>@lang( 'superadmin::lang.sms_type' )</th>
                        <th>@lang( 'superadmin::lang.no_of_sms' )</th>
                        <th>@lang( 'superadmin::lang.sms_status' )</th
                    </tr>
                </thead>
            </table>
        </div>
        
        @endcomponent
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
      
@endsection



@section('javascript')
<script>
$(document).ready(function(){
    
    $(document).on('click', '.msg_btn', function(e){
      let note = $(this).data('string');
      // Replace newline characters with <br>
      note = note.replace(/\n/g, '<br>');
      $("#msgContent").html(note);
      $("#msgModal").modal('show');
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
      
        
})
</script>
@endsection