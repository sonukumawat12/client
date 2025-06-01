@extends('layouts.app')
@section('title', __('dsr::lang.dsr'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('dsr::lang.dsr')
    </h1>
</section>
<section class="content">
     <div class="row">

        <div class="col-md-12">
            <div class="mx-auto my-2">
                <h3 class="text-center">@lang('dsr::lang.daily_sales_report') </h3>
            </div>
                @if(auth()->user()->hasRole('dsr_officer'))
                   @include('dsr::partials.filters')
                @else
                    @include('dsr::partials.filters_business')
               @endif
        </div>
        <div class="col-md-12" id="report_data"></div>
    </div>
</section>
@endsection
@section('javascript')
<script>
    $(()=>{
        $('#province_id').empty();
        $('#district_id').empty();
        $('#area_id').empty();
    })
    // if country id changes fetch provivinces districts and areas
    $('#country_id').on('change', function () {
        $('#province_id').empty();
        $('#district_id').empty();
        $('#area_id').empty();
        var country_id = $(this).val();
        $.ajax({
            url: '/dsr/get-provinces-multiple',
            data: {'country_id' :  country_id},
            type: 'GET',
            success: function (data) {
                $('#province_id').html(data);
            }
        });
    });
    $('#province_id').on('change', function () {
        $('#district_id').empty();
        $('#area_id').empty();
        var province_id = $(this).val();
        $.ajax({
            url: '/dsr/get-districts-multiple',
            type: 'GET',
            data: {'province_id' :  province_id},
            success: function (data) {
                $('#district_id').html(data);
            }
        });
    });
    $('#district_id').on('change', function () {
        $('#area_id').empty();
        var district_id = $(this).val();
        $.ajax({
            url: '/dsr/get-areas-multiple',
            data: {'district_id' : district_id},
            type: 'GET',
            success: function (data) {
                $('#area_id').html(data);
            }
        });
    });
    
    $('#area_id').on('change', function () {
        var area_id = $(this).val();
        $("#dealer_id").empty();
        $.ajax({
            url: '/dsr/get-dealers',
            type: 'GET',
            data: {area_id},
            success: function (data) {
                $("#dealer_id").append(data);
            }
        });
    });
    
    $('#dealer_id').on('change', function () {
        var dealer_id = $(this).val();
        $("#product_id").empty();
        $.ajax({
            url: '/dsr/get-products',
            type: 'GET',
            data: {dealer_id},
            success: function (data) {
                $("#product_id").append(data);
            }
        });
    });
    
    $('#product_id').on('change', function () {
        var product_id = $(this).val();
        if(!product_id){
            toastr.error('{{__("dsr::lang.please_select_product")}}');
            return false;
        }
        
        getReport();
        
    });
    
    function getReport(){
        var dealer_id = $("#dealer_id").val();
        var product_id = $('#product_id').val();
        
        console.log(product_id);
        
        if(!product_id){
            toastr.error('{{__("dsr::lang.please_select_product")}}');
            return false;
        }
        
        
         var start_date = $('input#report_date_range')
            .data('daterangepicker')
            .startDate.format('YYYY-MM-DD');
        var end_date = $('input#report_date_range')
            .data('daterangepicker')
            .endDate.format('YYYY-MM-DD');
        
        $("#report_data").empty();
        
        $.ajax({
            url: '/dsr/get-report',
            type: 'GET',
            data: {dealer_id,product_id,start_date,end_date},
            success: function (data) {
                $("#report_data").append(data);
            }
        });
        
    }
    
    
    if ($('#report_date_range').length == 1) {
        $('#report_date_range').daterangepicker(dateRangeSettings, function(start, end) {
            $('#report_date_range').val(
                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
            );
            getReport();
        });
        $('#report_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#report_date_range')
            .data('daterangepicker')
            .setStartDate(moment().startOf('week'));
        $('#report_date_range')
            .data('daterangepicker')
            .setEndDate(moment().endOf('week'));
    }
        
    
</script>
@endsection

