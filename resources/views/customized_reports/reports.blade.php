@extends('layouts.app')
@section('title', __('report.product_report'))

@section('content')
<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                  
                    <li class="active">
                        <a href="#stock_report" class="stock_report" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('lang_v1.daily_sales_and_stock_report')</strong>
                        </a>
                    </li> 
                    <li class="">
                        <a href="#stock_pumpers" class="stock_summary" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('lang_v1.pumpers_daily_summary')</strong>
                        </a>
                    </li>
                    
                </ul>
                <div class="tab-content">
                 
                    <div class="tab-pane active" id="stock_report">
                        @include('customized_reports.stock_report_tab')
                    </div>
                   
                    <div class="tab-pane" id="stock_pumpers">
                        @include('customized_reports.pumpers_stock_summary_tab')
                    </div>
                   

                    


                </div>
            </div>
        </div>
    </div>

</section>
<!-- /.content -->

@endsection
@section('javascript')
 
<script>
 $(document).ready(function() {
    $('.nav-tabs li').click(function(e) {
        e.preventDefault();
      
        $(this).toggleClass('active');
      
        var tabId = $(this).find('a').attr('href');
        $(tabId).toggleClass('active');
    });
});
    
function printDocument() {
        var html = $(".report_div").html();
        console.log(html);
        var w = window.open('', '_self');
        $(w.document.body).html(html);
        w.print();
        w.close();
        location.reload();
    }
    
  $(document).ready(function() {
   
    $('#stock_details_date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#stock_details_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            $("#report_date_range").text("Date Range: "+ $("#stock_details_date_range").val());
            populateProductCategories()
        }
    );
    $('#custom_date_apply_button').on('click', function() {
        let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
        let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

        if (startDate.length === 10 && endDate.length === 10) {
            let formattedStartDate = moment(startDate).format(moment_date_format);
            let formattedEndDate = moment(endDate).format(moment_date_format);

            $('#stock_details_date_range').val(
                formattedStartDate + ' ~ ' + formattedEndDate
            );
            $("#report_date_range").text("Date Range: "+ $("#stock_details_date_range").val());

            $('#stock_details_date_range').data('daterangepicker').setStartDate(moment(startDate));
            $('#stock_details_date_range').data('daterangepicker').setEndDate(moment(endDate));

            $('.custom_date_typing_modal').modal('hide');
            populateProductCategories();
        } else {
            alert("Please select both start and end dates.");
        }
    });
    $('#stock_details_date_range').on('apply.daterangepicker', function(ev, picker) {
        if (picker.chosenLabel === 'Custom Date Range') {
            $('.custom_date_typing_modal').modal('show');
        }
    });
    $('#stock_details_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#stock_details_date_range').val('');
        $("#report_date_range").text("Date Range: - ");
        populateProductCategories()
    });
    

    function populateProductCategories() {
        updateReport();
    }
    
    function updateReport() {
        
        $(".report_div").empty().append("<h5 class='text-center'>Loading</h5>");
        
        var start_date = "";
        var end_date = "";
        
        if($('#stock_details_date_range').val()) {
            var start = $('#stock_details_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
            var end = $('#stock_details_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
            start_date = start;
            end_date = end;
        }
       
        $.ajax({
            url: '/customized_reports',
            method: 'GET',
            data: {
                start_date: start_date,
                end_date: end_date
            },
        success: function(response) {
                $(".report_div").empty().append(response);
            },
            error: function(xhr, status, error) {
                
                console.log("Error: " + error);
            }
        });
    }
    
    populateProductCategories();
});
    
    
function printPumperDocument() {
        var html = $(".pumper_report_div").html();
        console.log(html);
        var w = window.open('', '_self');
        $(w.document.body).html(html);
        w.print();
        w.close();
        location.reload();
    }
    
  $(document).ready(function() {
   
    $('#pumper_details_date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#pumper_details_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            $("#report_date_range").text("Date Range: "+ $("#pumper_details_date_range").val());
            loadReports()
        }
    );
    $('#custom_date_apply_button').on('click', function() {
        let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
        let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

        if (startDate.length === 10 && endDate.length === 10) {
            let formattedStartDate = moment(startDate).format(moment_date_format);
            let formattedEndDate = moment(endDate).format(moment_date_format);

            $('#pumper_details_date_range').val(
                formattedStartDate + ' ~ ' + formattedEndDate
            );
            $("#report_date_range").text("Date Range: "+ $("#pumper_details_date_range").val());

            $('#pumper_details_date_range').data('daterangepicker').setStartDate(moment(startDate));
            $('#pumper_details_date_range').data('daterangepicker').setEndDate(moment(endDate));

            $('.custom_date_typing_modal').modal('hide');
            loadReports();
        } else {
            alert("Please select both start and end dates.");
        }
    });
    $('#pumper_details_date_range').on('apply.daterangepicker', function(ev, picker) {
        if (picker.chosenLabel === 'Custom Date Range') {
            $('.custom_date_typing_modal').modal('show');
        }
    });
    $('#pumper_details_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#pumper_details_date_range').val('');
        $("#report_date_range").text("Date Range: - ");
        loadReports()
    });
    

    function loadReports() {
        updateReport();
    }
    
    function updateReport() {
        
        $(".pumper_report_div").empty().append("<h5 class='text-center'>Loading</h5>");
        
        var start_date = "";
        var end_date = "";
        
        if($('#pumper_details_date_range').val()) {
            var start = $('#pumper_details_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
            var end = $('#pumper_details_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
            start_date = start;
            end_date = end;
        }
       
        $.ajax({
            url: '/customized_reports',
            method: 'GET',
            data: {
                start_date: start_date,
                end_date: end_date,
                report_type: 'pumper'
            },
        success: function(response) {
                $(".pumper_report_div").empty().append(response);
            },
            error: function(xhr, status, error) {
                
                console.log("Error: " + error);
            }
        });
    }
    
    loadReports();
    
});
</script>
@endsection