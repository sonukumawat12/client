@extends('layouts.app')
@section('title', __('mpcs::lang.9c_cash_form'))

@section('content')
<!-- Main content -->
<section class="content">
    
     <div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">FORM 9C Cash</h4>
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">Form F9C Cash</a></li>
                    <li><span>Form F9 C Setting</span></li>
                   
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
                        <a href="#16a_form_tab" class="16a_form_tab" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.Form9_CCash')</strong>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can('f16a_form'))
                    <li class="">
                        <a href="#16a_form_list_tab" class="16a_form_tab" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.Form9_CSetting')</strong>
                        </a>
                    </li>
                    @endif
               </ul>        
                <div class="tab-content">
                    @if(auth()->user()->can('16a_form'))
                    <div class="tab-pane active" id="16a_form_tab">
                        @include('mpcs::forms.partials.form9_ccash')
                    </div>
                    @endif
                    @if(auth()->user()->can('16a_form'))
                    <div class="tab-pane" id="16a_form_list_tab">
                          @include('mpcs::forms.partials.form9_csetting')
                    </div>
                    @endif
                   

                </div>
               
            </div>
        </div>
    </div>
    

</section>
<!-- /.content -->

@endsection
@section('javascript')
<script type="text/javascript">
   
    $(document).ready(function(){
      // get_previous_value_16a();
   $('#form-id').hide();
let previousFormNo = null;
let previousInvoiceNo = null;
let previousSupplier = null;
let previousDateRange =null;
form_f22_list_table = $('#form_f22_list_table').DataTable({
    processing: true,
    serverSide: false,
    ajax: {
        url: '/mpcs/get-form-f16-list',
        data: function(d) {
            let formNo = $('select[id="form_no"] option:selected').text();
            let invoiceNo = $('select[id="invoice_no"] option:selected').text();
            let supplierName = $('select[id="supplier"] option:selected').text();
            let dateRange = $('#form_16a_date_range_list').val();
            if (formNo !== previousFormNo) {
                d.form_no = formNo;
                previousFormNo = formNo;
            }

            if (invoiceNo !== previousInvoiceNo) {
                d.invoice_no = invoiceNo;
                previousInvoiceNo = invoiceNo;
            }
             if (supplierName !== previousSupplier) {
                d.supplier = supplierName;
                previousSupplier = supplierName;
            }
          if (dateRange !== previousDateRange) {
            
            let [start, end] = dateRange.split(' - ');
            d.start_date = start;
            d.end_date = end;
            previousDateRange = dateRange;
            }
            return d;
        }
    },
    columns: [
        { data: 'created_at', name: 'created_at' },
        { data: 'supplier', name: 'Supplier' },
        { data: 'form_no', name: 'form_no' },
        { data: 'invoice_no', name: 'Invoice No' },
        { data: 'this_form_total', name: 'this_form_total' },
        { data: 'last_form_total', name: 'last_form_total' },
        { data: 'grand_total', name: 'grand_total' },
        { data: 'action', name: 'action' },
    ],
    fnDrawCallback: function(oSettings) {

    }
});

$('#form_no').on('change', function() {
    form_f22_list_table.ajax.reload();
});

$('#invoice_no').on('change', function() {
    form_f22_list_table.ajax.reload();
});
    $('#supplier').on('change', function() {
          form_f22_list_table.ajax.reload();
});

// Add event listener to the date range input field
$('#form_16a_date_range_list').on('change', function() {
    console.log("date changed");
    form_f22_list_table.ajax.reload();
});
// 
 
 
    //form 16a section
    $('#form_16a_date_range').daterangepicker();
        if ($('#form_16a_date_range').length == 1) {
            $('#form_16a_date_range').daterangepicker(dateRangeSettings, function(start, end) {
                $('#form_16a_date_range').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );
            });
            $('#form_16a_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#form_16a_date_range')
                .data('daterangepicker')
                .setStartDate(moment().startOf('month'));
            $('#form_16a_date_range')
                .data('daterangepicker')
                .setEndDate(moment().endOf('month'));
        }
        
        let date = $('#form_16a_date_range').val().split(' - ');
        
        $('.from_date').text(date[0]);
        $('.to_date').text(date[1]);
    
  
    
    
        $('#form_16a_date_ranges').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
        format: 'YYYY-MM-DD'
        }
        }, function(start, end, label) {
        $('#form_16a_date_range').val(start.format('YYYY-MM-DD'));
        });
        
        $('#form_16a_date_ranges').on('cancel.daterangepicker', function(ev, picker) {
        $('#form_16a_date_range').val('');
        });
        
      $('#form_16a_date_ranges').data('daterangepicker').setStartDate(moment());
         
        
          $('#form_16a_date_range_list').daterangepicker();
        if ($('#form_16a_date_range_list').length == 1) {
            $('#form_16a_date_range_list').daterangepicker(dateRangeSettings, function(start, end) {
                $('#form_16a_date_range_list').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );
            });
            $('#form_16a_date_range_list').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#form_16a_date_range_list')
                .data('daterangepicker')
                .setStartDate(moment().startOf('month'));
            $('#form_16a_date_range_list')
                .data('daterangepicker')
                .setEndDate(moment().endOf('month'));
        }
     

   get_previous_value_16a();

    let selected_date = date[0];
    var F16A_this_total = [];
    var F16A_pre_total = [];
    var F16A_grand_total = [];

    function caculateF16AFromTotal(){
       
        var total_purchase_price = @if($setting->F16A_first_day_after_stock_taking == 1) 0 @else sum_table_col($('#form_16a_table_listss'), 'total_purchase_price') @endif;
        $('#footer_F16A_total_purchase_price').text(__number_f(total_purchase_price, false, false, __currency_precision));
        var total_sale_price =  @if($setting->F16A_first_day_after_stock_taking == 1) 0 @else sum_table_col($('#form_16a_table_listss'), 'total_sale_price') @endif;
        $('#footer_F16A_total_sale_price').text(__number_f(total_sale_price, false, false, __currency_precision));
        $('#total_this_p').val(total_purchase_price);
        $('#total_this_s').val(total_sale_price);
    }

    $('#form_16a_date_range, #16a_location_id').change(function(){
        form_16a_table.ajax.reload();
        if($('#16a_location_id').val() !== ''  && $('#16a_location_id').val() !== undefined){
            $('.f16a_location_name').text($('#16a_location_id :selected').text())
        }else{
            $('.f16a_location_name').text('All')
        }
    });
    $('#f16_save').click(function(e){
        e.preventDefault();
        var transactionid = $('#form-index').text();
        
        var formNumber = $('#form-number').text();
        var formInvoice = $('#form-invoice').text();
        var formSupplier = $('#form-supplier').text();
        var stockNo = $('#new_price_value').val();
        var stockBook = $('#new_price_value2').val();
        var thisbook = $('#this_book').val();
        var prev_book = $('#prev_book').val();
         var grand_book = $('#grand_book').val();
        var thisformtotals = $('#this_form_input').val();
        var prev_formtotals = parseFloat($('#this_form_prevs').val()).toFixed(2);
var grand_formtotals = parseFloat($('#this_form_grands').val()).toFixed(2);
       console.log(prev_formtotals);
       console.log(grand_formtotals);
     $.ajax({
                method: 'POST',
                url: '/mpcs/save-form-f16',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                
              data: {
                    transaction_id: transactionid,
                form_number: formNumber,
                form_invoice: formInvoice,
                formSupplier: formSupplier,
                prevformtotal: prev_formtotals,
                grandformtotal: grand_formtotals,
                thisformtotal: thisformtotals,
                stockNo: stockNo,
                stockBook: stockBook,
                thisbook: thisbook,
                prevbook: prev_book,
                grandbook: grand_book
                   
                  
                    // Include any other data you want to send form-supplier
                },
                
                success: function(result) {
                    
                    if(result.success == 0){
                        toastr.error(result.msg);
                        return false;
                    }
                   toastr.success('Record Saved');
                  
                },
            });
    });
     
    function get_previous_value_16a(){
         
        var start_date = $('input#form_16a_date_range')
            .data('daterangepicker')
            .startDate.format('YYYY-MM-DD');
        var end_date = $('input#form_16a_date_range')
            .data('daterangepicker')
            .endDate.format('YYYY-MM-DD');
        var  location_id = $('#16a_location_id').val();

        $.ajax({
            method: 'get',
            url: '/mpcs/get_previous_value_16a',
            data: { start_date, end_date, location_id},
            success: function(result) {
             
                let footer_total_purchase_price = __read_number($('#total_this_p'));
                let footer_total_sale_price = __read_number($('#total_this_s'));
                var thisFormValue = parseFloat($('#this_form_input').val());
           
               
                var pre_grand_total_integer = Math.floor(result.pre_total_purchase_price);
                var pre_grand_total_decimal = Math.round((result.pre_total_purchase_price - pre_grand_total_integer) * 100);
                
                let grand_total_purchase_price = thisFormValue + footer_total_purchase_price + parseFloat(result.pre_total_purchase_price);
                
                var grand_total_integer = Math.floor(grand_total_purchase_price);
                var grand_total_decimal = Math.round((grand_total_purchase_price - grand_total_integer) * 100);
                
                let grand_total_sale_price = footer_total_sale_price + parseFloat(result.pre_total_sale_price);
                
                  $('#pre_F16A_total_purchase_price').text(__number_f(pre_grand_total_integer, false, false, __currency_precision));
                $('#pre_F16A_total_sale_price_decimal').text(pre_grand_total_decimal.toString().padStart(2, '0'));
                
                // $('#grand_total').text(__number_f(grand_total_integer, false, false, __currency_precision));
                 $('#grand_F16A_total_purchase_price').text(__number_f(grand_total_integer, false, false, __currency_precision))
                 
                 $('#grand_total_decimal').text(grand_total_decimal.toString().padStart(2, '0'));
                 
                 $('#this_form_prev').val(__number_f(result.pre_total_purchase_price, false, false, __currency_precision))
                
                 $('#this_form_grand').val(__number_f(pre_grand_total_integer  + thisFormValue, false, false, __currency_precision))
                 
                   $('#this_form_prevs').val(result.pre_total_purchase_price)
                
                 $('#this_form_grands').val(result.pre_total_purchase_price  + thisFormValue)
              
            },
        });
    }
    
     
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
                
            //  End
                // cal_total_receipt(parseInt(today),parseInt(previous),parseInt(opening),parseInt(today_inc),parseInt(predate_inc));
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

    //form 15a9ab
   
});
</script>
@endsection