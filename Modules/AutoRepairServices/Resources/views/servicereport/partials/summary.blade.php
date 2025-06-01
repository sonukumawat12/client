<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
          
        <div class="col-md-10 text-center pull-center">
            <h3>{{ $business_locations }}</h3>
            </div>
            <div class="form-group">
            <input type="hidden" name="whatsapp_phone_no" id="whatsapp_phone_no" value="{{$whatsapp_phone_no}}">
           
</div>
            <div class="col-md-2 text-right pull-right">
                <div class="form-group">
                    
                    {!! Form::text('date_range_filter', @format_date('first day of this month') . ' ~ ' .
                    @format_date('last day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control date_range', 'id' => 'date_range_filter', 'readonly']); !!}
                </div>
            </div>       
                      
        </div>
        <div class="col-md-12">
        <div class="col-md-4">
        <h4 style="color: darkblue;">Service Details</h4>        
        </div>
        <div class="col-md-8 pull-right">
        <button class="btn btn-primary print_report pull-right" id="print_report_btn">
        <i class="fa fa-print"></i> @lang('messages.print')</button>  
        <button class="btn pull-right" id="whatsApp" 
        style="background-color: #25D366; border-color: #25D366; color: white;">
        <i class="fa fa-whatsapp"></i> WhatsApp
            </button>       
            </div>
            </div>

    </div>
     <div class="row" id ="print_content">
    <div class="table-responsive" >
        <table class="table table-bordered table-striped" id="cancelled_table">
            <thead>
                <tr>
                <th class="align-middle text-center">@lang('autorepairservices::lang.job_sheet_no')
                    </th>
                    <th class="align-middle text-center" >@lang('autorepairservices::lang.bill_no')
                    </th>
                    <th class="align-middle text-center">@lang('autorepairservices::lang.customer')
                    </th>
                    <th class="align-middle text-center" >@lang('autorepairservices::lang.vehicle_no')
                    </th>
                    <th class="align-middle text-center" >@lang('autorepairservices::lang.service')</th>
                    <th class="align-middle text-center" >@lang('autorepairservices::lang.bill_amount')</th>
                    <th class="align-middle text-center" >@lang('autorepairservices::lang.cash')</th>
                    <th class="align-middle text-center">@lang('autorepairservices::lang.card')</th>
                    <th class="align-middle text-center">@lang('autorepairservices::lang.credit')</th>
                    <th class="align-middle text-center">@lang('autorepairservices::lang.note')</th>
                    <th class="align-middle text-center">@lang('autorepairservices::lang.assigned_staff')</th>
                </tr>
            </thead>

        </table>
    </div>
    <div class="row">  
        <div class="col-md-12">
            <div class="col-md-6">
            <h4 style="color: darkblue;" > POS Sales Detail</h4>           
            </div>
            <div class="col-md-2">
            <h4 style="color: darkblue;">Payment summery</h4>           
            </div>
            <div class="col-md-4">
            <table class="table table-bordered table-striped"> 
            <tr>
            <td id="totalService">Loading...</td> <!-- This will be updated by jQuery -->
            <td id="totalSales">Loading...</td> <!-- This will be updated by jQuery -->
            <td id="grandtotal">Loading...</td> <!-- This will be updated by jQuery -->
            </tr>
            </table>        
            </div>        
        </div>
        </div>
        <div class="col-md-12">
            <div class="col-md-6">
            <table class="table table-bordered table-striped"> 
    <thead>
        <tr>
            <th>Bill No</th>
            <th>Bill Amount</th>
            <th>Cash</th>
            <th>Card</th>
            <th>Credit</th>
        </tr>
    </thead>

    <tbody id="salesData">
        <tr>
            <td colspan="5" style="text-align: center;">Load Sales Data</td>
        </tr>
    </tbody>

    <tfoot>
        <tr id="salesFooter">
            <td><strong>Total</strong></td>
            <td id="salesbill"></td>
            <td id="salescash"></td>
            <td id="salescard"></td>
            <td id="salescredit"></td>
        </tr>
    </tfoot>
</table>
               
            </div>
            <div class="col-md-2">
            <h4 style="color: darkblue;  "> Total Sales Today</h4> 
            <table class="table table-bordered table-striped"> 
            <tr>
            <th></th>
            <th>Amount</th>        
            </tr>
            <tr>
            <td >Expense</td>
            <td id="expense"></td>
            
            </tr>
            <tr>
            <td>Credit</td>
            <td id="credit"></td>
            
            </tr>
            <tr>
            <td>Card</td>
            <td id="card"></td>
            
            </tr>
            <tr>
            <td>Cash</td>
            <td id="cash"></td>
            
            </tr>
            <tr>
            <td>Total</td>
            <td id="totals"></td>
            
            </tr>
            </table>          
            </div>
            <div class="col-md-4">
            <h4 style="color: darkblue;">Expense Summary</h4>
            <table class="table table-bordered table-striped" style="width: 100%;"> 
        <thead>
            <tr>
                <th style="width: 70%;">Expense Category</th>
                <th style="width: 30%;">Amount</th>
            </tr>
        </thead>
        <tbody id="expenseBody">
            <tr>
                <td colspan="2">Loading...</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td><strong>Total:</strong></td>
                <td id="totalExpenses">0.00</td>
            </tr>
        </tfoot>
    </table>
        
            </div>        
        </div>
   
    </div>
    </div> 
</section>