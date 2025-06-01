<?php
if(!isset($is_ajax)){
    ?>
    {{-- @extends('layouts.app') --}}
    @extends($layout)
    @section('title', __('mpcs::lang.F21_form'))
    
    @section('content')
    <!-- Main content -->
    <section class="content">
    <?php
}
?>


     <div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">FORM F21C</h4>
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">F21C</a></li>
                    <li><span>Last Record</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>
    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
               <ul class="nav nav-tabs">
                    @if(auth()->user()->can('f16a_form'))
                    <li class="active">
                        <a href="#21c_form_tab" class="21c_form_tab" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.F21_form')</strong>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can('f21c_form'))
                    <li class="">
                        <a href="#21c_form_list_tab" class="21c_form_tab" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.F21_form_list')</strong>
                        </a>
                    </li>
                    @endif
               </ul>        
                <div class="tab-content">
                    @if(auth()->user()->can('F21_form'))
                    <div class="tab-pane active" id="21c_form_tab">
                          @include('mpcs::forms.partials.21c_form')
                    </div>
                    @endif
                    @if(auth()->user()->can('F21_form'))
                    <div class="tab-pane" id="21c_form_list_tab">
                          @include('mpcs::forms.partials.list_f21c')
                    </div>
                    @endif
                   

                </div>
               
            </div>
        </div>
    </div>
  

@if (empty($is_ajax))
    </section>
    <!-- /.content -->
    @endsection
    @section('javascript')
@endif
<script type="text/javascript">
    $(document).ready(function(){
  $('#form_date_range').daterangepicker({
        ranges: ranges,
        autoUpdateInput: false,
        locale: {
            format: moment_date_format,
            cancelLabel: LANG.clear,
            applyLabel: LANG.apply,
            customRangeLabel: LANG.custom_range,
        },
    });
    $('#form_date_range').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(
            picker.startDate.format(moment_date_format) +
                ' - ' +
                picker.endDate.format(moment_date_format)
        );
    });

    $('#form_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
    
    
    
   //form 21C
    $('#form_21c_date_range').daterangepicker();
      $('#form_21c_date_range').daterangepicker({
                    onSelect: function() {
                        $(this).change();
                    }                    
                });
    if ($('#form_21c_date_range').length == 1) {
        $('#form_21c_date_range').daterangepicker(dateRangeSettings, function(start, end) {
            $('#form_21c_date_range').val(
                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
            );
        });
        $('#form_21c_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#form_21c_date_range')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#form_21c_date_range')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
            get_21_c_form_all_query();
          
    }
    
    let f21c_date = $('#form_21c_date_range').val().split(' - ');
    
    $('.21c_from_date').text(f21c_date[0]);
    $('.21c_to_date').text(f21c_date[1]);

    if($('#f21c_location_id').val() !== ''  && $('#f21c_location_id').val() !== undefined){
        $('.f21c_location_name').text($('#f21c_location_id :selected').text())
    }else{
        $('.f21c_location_name').text('All')
    }

    $('#form_21c_date_range, #f21c_location_id').change(function(){
        let f21c_date = $('#form_21c_date_range').val().split(' - ');
        $('.21c_from_date').text(f21c_date[0]);
        $('.21c_to_date').text(f21c_date[1]);

        if($('#f21c_location_id').val() !== ''  && $('#f21c_location_id').val() !== undefined){
            $('.f21c_location_name').text($('#f21c_location_id :selected').text())
        }else{
            $('.f21c_location_name').text('All')
        }
        console.log('change');
        get_21_c_form_all_query();
        
    });
    
   
  $('#form_21c_date_range').click(function(){
     
      get_21_c_form_all_query();
     
  });
  
  
  //21c list
   //form 21C
   
   $('#form_21c_date_range_list').daterangepicker();
$('#form_21c_date_range_list').daterangepicker({
    onSelect: function() {
        $(this).change();
    }
});

if ($('#form_21c_date_range_list').length == 1) {
    $('#form_21c_date_range_list').daterangepicker(dateRangeSettings, function(start, end) {
        $('#form_21c_date_range_list').val(
            start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
        );
        get_21_c_form_all_query_list(); // Call the function when the date range changes
    });

    $('#form_21c_date_range_list').on('cancel.daterangepicker', function(ev, picker) {
        $('#product_sr_date_filter').val('');
        get_21_c_form_all_query_list(); // Call the function when the date range is cancelled
    });

    $('#form_21c_date_range_list')
        .data('daterangepicker')
        .setStartDate(moment().startOf('month'));
    $('#form_21c_date_range_list')
        .data('daterangepicker')
        .setEndDate(moment().endOf('month'));
}
    
  var today = 0;
  var previous =0 ;
  var opening =0 ;
  var today_inc=0 ;
  var predate_inc = 0;
  
  
  $('#_own_usage_sales_today').on('keyup', function() {
     var cash_sales_today = $('#_cash_sales_today').val() == "" ? 0 : $('#_cash_sales_today').val();
      var credit_sales_today = $('#_credit_sales_today').val() == "" ? 0 : $('#_credit_sales_today').val();
      var own_usage_sales_today = $('#_own_usage_sales_today').val() == "" ? 0 : $('#_own_usage_sales_today').val();
      var price_reduction_today = $('#_price_reduction_today').val() == "" ? 0 : $('#_price_reduction_today').val();
      var price_reduction_predate = $('#_price_reduction_predate').val() == "" ? 0 : $('#_price_reduction_predate').val();
      
      $('#_price_reduction_total').val(parseInt(price_reduction_today) + parseInt(price_reduction_predate));
      
      $('#_total_issued_today').val(parseInt(cash_sales_today) + parseInt(credit_sales_today) + parseInt(own_usage_sales_today) + parseInt(price_reduction_today))
      
  });
  
  $('#_price_reduction_today').on('keyup', function() {
      var cash_sales_today = $('#_cash_sales_today').val() == "" ? 0 : $('#_cash_sales_today').val();
      var credit_sales_today = $('#_credit_sales_today').val() == "" ? 0 : $('#_credit_sales_today').val();
      var own_usage_sales_today = $('#_own_usage_sales_today').val() == "" ? 0 : $('#_own_usage_sales_today').val();
      var price_reduction_today = $('#_price_reduction_today').val() == "" ? 0 : $('#_price_reduction_today').val();
      var price_reduction_predate = $('#_price_reduction_predate').val() == "" ? 0 : $('#_price_reduction_predate').val();
      
      $('#_price_reduction_total').val(parseInt(price_reduction_today) + parseInt(price_reduction_predate));
      
      $('#_total_issued_today').val(parseInt(cash_sales_today) + parseInt(credit_sales_today) + parseInt(own_usage_sales_today) + parseInt(price_reduction_today))
      
  })
  
     function get_21_c_form_all_query(){
        var start_date = $('input#form_21c_date_range')
            .data('daterangepicker')
            .startDate.format('YYYY-MM-DD');
        var end_date = $('input#form_21c_date_range')
            .data('daterangepicker')
            .endDate.format('YYYY-MM-DD');
        var  location_id = $('#f21c_location_id').val();

        $.ajax({
            method: 'get',
            url: '/mpcs/get_21_c_form_all_query',
            data: { start_date, end_date, location_id},
            success: function(result) {
                
               console.log(result);
               today = $('#_today').val(result.today);
               
               previous = $('#_previous_day').val(result.previous_day);
              // console.log(result.transact);
               opening = $('#_opening_stock').val(Math.round(result.opening_stock*Math.pow(10,2))/Math.pow(10,2));
            //   Added by rmtemplate
                $('#_total_receipts').val(parseInt(result.today) + parseInt(result.previous_day));
               
                today_inc = $('#_price_increment_today').val(0);
                predate_inc = $('#_price_increment_pre_date').val(0);
                
                $('#_cash_sales_today').val(result.cash_sales_today);
                $('#_credit_sales_today').val(result.credit_sales_today);
                $('#_own_usage_sales_today').val(0);
                $('#_price_reduction_today').val(0);
                
                if(result.form17_decrease_previous == null) {
                    $('#_price_reduction_predate').val(0);
                } else {
                    $('#_price_reduction_predate').val(parseFloat(result.form17_decrease_previous.new_price));
                }
                
                $('#_price_reduction_total').val(parseInt($('#_price_reduction_predate').val()) + parseInt($('#_own_usage_sales_today').val()));
      
                $('#_total_issued_today').val(parseInt(result.cash_sales_today) + parseInt(result.credit_sales_today) + parseInt($('#_own_usage_sales_today').val()) + parseInt($('#_price_reduction_predate').val()))
                
                
                var price_increment_previous = $('#_price_increment_pre_date').val() == "" ? 0 : $('#_price_increment_pre_date').val();
          
                var price_increment_today = $('#_price_increment_today').val() == "" ? 0 : $('#_price_increment_today').val();
             
                $('#_price_increment_total').val(parseInt(price_increment_previous) + parseInt(price_increment_today)); 
          
                cal_total_receipt();
            },
        });
    }
    
    $('#_price_increment_today').on('keyup', function() {
         
          cal_total_receipt(today,previous,opening,today_inc,predate_inc)
          
  });
  
  function cal_total_receipt(today,previous,opening,today_inc,predate_inc) {
      
        today = $('#_today').val() == "" ? 0 : $('#_today').val();
               
        previous = $('#_previous_day').val() == ""  ? 0 : $('#_previous_day').val();
               
        opening = $('#_opening_stock').val() == "" ? 0 :  $('#_opening_stock').val();
      
      var price_increment_previous = $('#_price_increment_pre_date').val() == "" ? 0 : $('#_price_increment_pre_date').val();
          
        var price_increment_today = $('#_price_increment_today').val() == "" ? 0 : $('#_price_increment_today').val();
         
         $('#_price_increment_total').val(parseInt(price_increment_previous) + parseInt(price_increment_today)); 
          
          $('#_total_receipt_to_date').val(parseInt(today) + parseInt(previous) + parseInt(opening) + parseInt(price_increment_previous) + parseInt(price_increment_today));
  }  
    // all
     function get_21_c_form_all_query_list(){
        var start_date = $('input#form_21c_date_range_list')
            .data('daterangepicker')
            .startDate.format('YYYY-MM-DD');
        var end_date = $('input#form_21c_date_range_list')
            .data('daterangepicker')
            .endDate.format('YYYY-MM-DD');
        var  location_id = $('#f21c_location_id').val();

        $.ajax({
            method: 'get',
             url: '/mpcs/get-9c-forms',
             data: { start_date, end_date, location_id},
            contentType: 'html',
            success: function(result) {
                   console.log("result_list_ss");
                   $('#21c_details_section').empty().append(result);
                
          
            },
        });
    }
    
    $('#_price_increment_today').on('keyup', function() {
         
          cal_total_receipt(today,previous,opening,today_inc,predate_inc)
          
  });
  
  function cal_total_receipt(today,previous,opening,today_inc,predate_inc) {
      
        today = $('#_today').val() == "" ? 0 : $('#_today').val();
               
        previous = $('#_previous_day').val() == ""  ? 0 : $('#_previous_day').val();
               
        opening = $('#_opening_stock').val() == "" ? 0 :  $('#_opening_stock').val();
      
      var price_increment_previous = $('#_price_increment_pre_date').val() == "" ? 0 : $('#_price_increment_pre_date').val();
          
        var price_increment_today = $('#_price_increment_today').val() == "" ? 0 : $('#_price_increment_today').val();
         
         $('#_price_increment_total').val(parseInt(price_increment_previous) + parseInt(price_increment_today)); 
          
          $('#_total_receipt_to_date').val(parseInt(today) + parseInt(previous) + parseInt(opening) + parseInt(price_increment_previous) + parseInt(price_increment_today));
  }
});
</script>
@if (empty($is_ajax))
    @endsection
@endif