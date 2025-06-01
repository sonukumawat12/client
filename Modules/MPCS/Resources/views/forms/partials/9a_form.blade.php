
<style>
    #form_9a_tables {
        border-collapse: collapse;
        width: 100%;
    }

    #form_9a_tables thead th {
        position: sticky;
        top: 0;
        background: #fff; /* or any color you want */
        z-index: 10;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4); /* Optional: a little shadow */
    }
</style>


<!-- Main content -->
<section class="content"  style="padding:0px;">    
     
<div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3" id="location_filter">
                    <div class="form-group">
                        {!! Form::label('form_9a_location_id', __('purchase.business_location') . ':') !!}

                        {!! Form::select('form_9a_location_id', $business_locations, $business_locations->keys()->first(), [
                            'id' => 'form_9a_location_id',
                            'class' => 'form-control select2',
                            'style' => 'width:100%',
                            'placeholder' => __('lang_v1.all'),
                        ]) !!}


                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('form_16a_date', __('report.date') . ':') !!}
                        <div class="dropdown">
                     

                        {!! Form::text(
                    'date_range',
                    @format_date('first day of this month') .
                        ' ~ ' .
                        @format_date('last                                                                                                                                                                                    day of this month'),
                    [
                        'placeholder' => __('lang_v1.select_a_date_range'),
                        'class' => 'form-control',
                        'id' => '9a_date_ranges',
                        'readonly',
                    ],
                ) !!}
                        </div>
                    </div>

                </div>


                
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            @slot('tool')
            <div class="box-tools">
                <!-- Standard Print button -->
                <button class="btn btn-primary print_report pull-right" id="print_div">
                    <i class="fa fa-print"></i> @lang('messages.print')</button>
            </div>
            @endslot
            <div class="col-md-12">

<div class="row" style="margin-top: 20px;" id="print_content">
                    <style>
                    </style>
                    <div class="col-md-12">
                    <div class="col-md-10">

                        <h2 class="text-center">
                        {{ request()->session()->get('business.name') }}                               
                        
                       </h2>
                       
                    </div>
                   <div class="col-md-1">
                   <b>Form  No: <span class="9c_from_date">{{$form_number}}</span> </b>
                    </div>
                    <div class="col-md-1">

                    <h2 class="text-right">
                        F9A
                    <h2>

                    </div>
                    <div class="col-md-10">
                        <h4 class="text-center" style="margin:10px;">@lang('mpcs::lang.daily_sales_report')</h4>
                    </div>
                    
                    <div class="row">
                    <div xlass="col-md-12">
                    <div class="col-md-4 text-right"  >                                              
                    </div>
                    <div class="col-md-4 ">
                   
                    <div class="dropdown-date">
                     
                    <h5 style="font-weight: bold;"><span id="openingdate"></span></h5>
                        
</div>
           </div>
           <div class="col-md-3">
               <div class="text-right">
                   <h5 style="font-weight: bold;">Today Filling Station Report </h5>
               </div>
           </div>
           <div class="col-md-1">
               <div class="text-center pull-left">
                  
               </div>
           </div>
                    </div>
                    </div>
                        <div class="row">
                            <div class="col-md-12">
                               
                                <div class="table-responsive">
                               
                                    <table class="table table-bordered table-striped" id="form_9a_tables" style="max-height: 600px; overflow-y: auto;">
                                        <thead class="align-middle">
                                            <tr class="align-middle text-center">
                                                <th class="align-middle text-center" rowspan="2">@lang('mpcs::lang.description')</th>
                                                <th class="align-middle text-center" colspan="2">Total Sale</th>
                                                <th class="align-middle text-center" colspan="2">Card Sale</th>
                                                <th class="align-middle text-center" colspan="2">Cash Sale</th>
                                                <th class="align-middle text-center" colspan="2">Empty Barrels</th>
                                                <th class="align-middle text-center" colspan="2">Others</th>
                                                <th class="align-middle text-center" colspan="2">Total</th>
                                                <th class="align-middle text-center" colspan="2">With Taxes</th>
                                                <th class="align-middle text-center" colspan="2">Without Taxes</th>
                                                <th class="align-middle text-center" rowspan="2">Office Use</th>
                                            </tr>
                                            <tr class="align-middle" style="text-align: center;">
                                                <td>Rupees</td>
                                                <td>Cents</td>
                                                <td>Rupees</td>
                                                <td>Cents</td>
                                                <td>Rupees</td>
                                                <td>Cents</td>
                                                <td>Rupees</td>
                                                <td>Cents</td>
                                                <td>Rupees</td>
                                                <td>Cents</td>
                                                <td>Rupees</td>
                                                <td>Cents</td>
                                                <td>Rupees</td>
                                                <td>Cents</td>
                                                <td>Rupees</td>
                                                <td>Cents</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th>Cash</th>
                                                <td></td>
                                                <td></td>
                                                <td id="card_sales_rup"></td>
                                                <td id="card_sales_cent"></td> <!-- Total Card Sales -->
                                                <td id="cash_sales_rup"></td>
                                                <td id="cash_sales_cent"></td> <!-- Total Cash Sales -->
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td id="total_cash_sale_rup"></td>
                                                <td id="total_cash_sale_cent"></td> <!-- Total = Card Sales + Cash Sales -->
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td rowspan="7">
                                                    <p>Rceived On ...........................</p>
                                                    <p>Checked ................................</p>
                                                    <p>Approved .............................</p>
                                                    <p style="text-align: center;">After Checking</p>
                                                    <p style="display: flex; justify-content: center; gap: 10px;">
                                                        <span style="text-decoration-line: underline;"> Short Money </span>
                                                        <span style="text-decoration-line: underline;"> Excess Money </span>
                                                    </p>
                                                    <p>Today ...................................</p>
                                                    <p>Previous Day ........................</p>
                                                    <p>As of Today ...........................</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Deposit / Credit Sales</th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td id="total_credit_sale_rup"></td>
                                                <td id="total_credit_sale_cent"></td> <!-- Total Credit Sales -->
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th> &nbsp; </th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td> <!--  -->
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th>MPCS Branches</th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th>Today Sale</th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td id="total_sale_rup"></td>
                                                <td id="total_sale_cent"></td> <!-- 6 + 7 -->
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th>Total Sale up to Previous Day</th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td id="total_sale_pre_day_rup"></td> <!-- Total Sale up to Previous Day -->
                                                <td id="total_sale_pre_day_cent"></td> <!-- Total Sale up to Previous Day -->
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th>Total Sale as of Today</th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td id="total_sale_today_rup"></td>
                                                <td id="total_sale_today_cent"></td> <!-- 8 + 9 -->
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->