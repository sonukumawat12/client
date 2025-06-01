@extends('layouts.app')
@section('title', 'Doc Management Upload')

@section('css')
 
<style>
    #airport_table > tbody > tr > td{
        vertical-align: middle;
    }
</style>
@endsection

@section('content')

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h5 class="page-title pull-left">Doc Management Upload</h5>
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li>Doc Management </li>
                    <li><span>Upload</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content main-content-inner">
    @component('components.filters', ['title' => __('airline::lang.filters')])
  <div class="row">
   <div class="col-md-3">
    <div class="form-group">
        {!! Form::label('date_range_filter', __('airline::lang.date_range') . ':') !!}
        {!! Form::text('date_range_filter', null, [
            'placeholder' => __('lang_v1.select_a_date_range'),
            'class' => 'form-control',
            'readonly',
        ]) !!}
    </div>
</div>
    <div class="form-group col-sm-3">
        {!! Form::label('type', __('Document Type')) !!}
    {!! Form::select('type', $docTypes, !empty($type) ? $type : null, [
      'class' => 'form-control select2',
      'id' => 'type',
      'placeholder' => __('Document Type'),
      'required'
    ]) !!}
    </div>
       <div class="form-group col-sm-3">
         {!! Form::label('province_select', __('Orginator') . ':*') !!}
        {!! Form::text('province_select', null, [
        'class' => 'form-control',
        'id' => 'province_select',
        'required',
        'placeholder' => __('Orginator'),
        ]) !!}
    </div>
<div class="form-group col-sm-3">
         {!! Form::label('type', __('Referred to')) !!}
    {!! Form::select('type', $docReferred, !empty($type) ? $type : null, [
      'class' => 'form-control select2',
      'id' => 'type',
      'placeholder' => __('Referred to'),
      'required'
    ]) !!}
    </div>
</div>
 
@endcomponent
 @component('components.widget', ['class' => '', 'title' => 'Document Status'])
   
    <div class="row">
         
            
            <div class="table-responsive">
           <table class="table table-bordered table-striped"  id="doc_upload_table" style ="width:100%;">
            <thead>
              
                
                <tr>
                  <th >
                       Action
                    </th>
                    <th >
                        @lang('lang_v1.date')
                    </th>
                    <th >
                      Doc No
                    </th>

                    <th >
                     Originator
                    </th>
                     <th >
                      Document Type
                    </th> 
                    <th >
                       Purpose
                    </th>
                    <th >
                   Referred to
                    </th>
                    <th >
                       Note
                    </th>
                      <th >
                       Status
                    </th>
                    
                    
                     <!--  <th class="notexport">
                         @lang('lang_v1.action')
                    </th> -->
                </tr>
               
            </thead>
             
         
       
        </table>
        </div>
    </div>
    <div class"row">
         <div class="col-sm-6">
             <h4> Document Status pie chart</h4>
              <canvas id="pie-chart"></canvas>
    </div>
    <div class="col-sm-6">
         <h4> Document Status Bar chart</h4>
             <canvas id="bar-chart"></canvas> 
    </div>
    </div>
    
@endcomponent
 
 <div class="modal fade" id="print_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 80% !important;">
        {!! Form::open(['url' => action('\Modules\DocManagement\Http\Controllers\DocManagementController@update_referred'), 'method' => 'post', 'id' => 'referredto_form', 'enctype' => "multipart/form-data", 'files' => true]) !!}
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="airport_title">Print Preview</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('document_type', __('Document Type')) !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-file"></i>
                                </span>
                                {!! Form::select('document_type',[], null, [
                                    'class' => 'form-control select2',
                                    'id' => 'document_type',
                                    'placeholder' => __('Select Document Type'),
                                    'required',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('created_date', __('Created Date')) !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                {!! Form::text('created_date', null, [
                                    'class' => 'form-control',
                                    'id' => 'created_date',
                                    'placeholder' => __('Select Created Date'),
                                    'required',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('document_log', __('Document Log')) !!}
                    {!! Form::textarea('document_log', null, [
                        'class' => 'form-control',
                        'id' => 'document_log',
                        'rows' => 4,
                        'placeholder' => __('Enter Document Log'),
                    ]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('document_preview', __('Document Preview')) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-file-pdf-o"></i>
                        </span>
                        {!! Form::file('document_preview', [
                            'class' => 'form-control',
                            'id' => 'document_preview',
                            'accept' => 'application/pdf',
                            'required',
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Print</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>
 
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>

<!-- start amcharts -->
<script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
<script src="https://www.amcharts.com/lib/3/ammap.js"></script>
<script src="https://www.amcharts.com/lib/3/maps/js/worldLow.js"></script>
<script src="https://www.amcharts.com/lib/3/serial.js"></script>
<script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
<script src="https://www.amcharts.com/lib/3/themes/light.js"></script>
<script>
        // Get the chart data passed from the controller
        var chartData = @json($chartData);
 
        // Render the pie chart
        var pieChartCtx = document.getElementById('pie-chart').getContext('2d');
        new Chart(pieChartCtx, {
            type: 'pie',
            data: chartData.pie,
            options: {
                responsive: true,
            },
        });

        // Render the bar chart
        var barChartCtx = document.getElementById('bar-chart').getContext('2d');
        new Chart(barChartCtx, {
            type: 'bar',
            data: chartData.bar,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        precision: 0,
                    },
                },
            },
        });
    </script>
<!-- /.content -->
<script>
    $(document).ready(function(){
        $('#airport_form').submit(function(e) {
        e.preventDefault();
        var data = new FormData(this);
        console.log(data)
        $.ajax({
            method: 'get',
            url: $(this).attr('url'),
            dataType: 'json',
            contentType: false,
            processData: false,
            cache: false,
            encode: true,
            data: data,
            success: function(result) {
                console.log(result);
                    // Handle success response
                    console.log(response);
                },
                error: function(xhr, status, error){
                    // Handle error response
                    console.log(error);
                }
            });
        });
    });
</script>


<script>

  

$(document).ready(function() {
    
  $('#create_airport').click(() => {
    $('#airport_form_modal').modal('show');
});

  $(document).ready(function() {
       if ($('#date_range_filter').length == 1) {
        $('#date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#date_range_filter').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
            );
            
            doc_upload_table.ajax.reload();
            
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
    function printDocument(docNo) {
    // Generate the URL for the print route using the document number
    var printUrl = "{{ route('doc.print', ['doc_no' => ':docNo']) }}";
    printUrl = printUrl.replace(':docNo', docNo);

    // Open a new window or tab to initiate the printing
    window.open(printUrl);
}
    initializeDataTable();
});
function initializeDataTable() {
   doc_upload_table=  $('#doc_upload_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ action('\Modules\DocManagement\Http\Controllers\DocManagementController@show_status') }}",
            data: function(d) {
                d.type = $('#document_type').val();
            }
        },
        columnDefs: [{
            targets: 1,
            orderable: false,
            searchable: false
        }],
        columns: [
            { data: 'action', name: 'action' },
            { data: 'created_at', name: 'created_at' },
            { data: 'doc_no', name: 'doc_no' },
            { data: 'originator', name: 'originator' },
            { data: 'document_type', name: 'document_type' },
            // Add additional columns here
            { data: 'purpose', name: 'purpose' },
            { data: 'referred_to', name: 'referred_to' },
            { data: 'note', name: 'note' },
              { data: 'status', name: 'status' },
        ],
        createdRow: function(row, data, dataIndex) {
            // Add any custom row creation logic here
        },
        initComplete: function () {
            $('#document_type').on('change', function () {
              
                $('#doc_upload_table').DataTable().ajax.reload();
            });
        }
    });
}
    
    $('#image').change(function() {
        var files = $(this)[0].files;
        var fileSizeLabel = '';
        var exceedsLimit = false;
        for (var i = 0; i < files.length; i++) {
            var fileSize = files[i].size / 1024; // Convert file size to KB
            if (fileSize > 200) {
                fileSizeLabel += '<span style="color: red;">File ' + (i+1) + ': ' + fileSize.toFixed(2) + ' KB (Exceeds 200 KB limit)</span><br>';
                exceedsLimit = true;
            } else {
                fileSizeLabel += 'File ' + (i+1) + ': ' + fileSize.toFixed(2) + ' KB<br>';
            }
        }
        $('#file-size-label').html(fileSizeLabel);
        if (exceedsLimit) {
            $('#image').val(''); // Clear the file input value
        }
    });
});
     
    
    
</script>
<style>
  .nav-tabs-custom>.nav-tabs>li.active a{
    color:#3c8dbc;
  }
  .nav-tabs-custom>.nav-tabs>li.active a:hover{
    color:#3c8dbc;
  }
</style>
@endsection

@section('javascript')
@endsection