<?php

namespace Modules\MPCS\Http\Controllers;

use App\Unit;
use App\Store;
use App\Brands;
use App\Account;
use App\Product;
use App\TaxRate;
use App\Business;
use App\Category;
use App\Variation;
use Carbon\Carbon;
use App\Transaction;
use App\AccountGroup;
use Modules\MPCS\Entities\MPCSForm9CCash;
use App\BusinessLocation;
use App\MergedSubCategory;
use App\Utils\ProductUtil;
use App\AccountTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Petro\Entities\Pump;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\MPCS\Entities\FormF17Detail;
use Modules\MPCS\Entities\Mpcs16aFormSettings;
use Modules\MPCS\Entities\Mpcs21cFormSettings;
use Modules\MPCS\Entities\FormF17Header;
use Modules\MPCS\Entities\FormF22Detail;
use Modules\MPCS\Entities\FormF22Header;
use Yajra\DataTables\Facades\DataTables;
use Modules\MPCS\Entities\MpcsFormSetting;
use Modules\MPCS\Entities\Form9cSubCategory;
use Modules\MPCS\Entities\Mpcs9aFormSettings;
use Modules\MPCS\Entities\FormF17HeaderController;
use Modules\MPCS\Entities\Mpcs9cCashFormSettings;
use Modules\MPCS\Entities\Mpcs9cCreditFormSettings;

class MPCSController extends Controller
{
    public function FromSet1()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        $business_id = request()->session()->get('business.id');
        $settings = MpcsFormSetting::where('business_id', $business_id)->first();
        if (!empty($settings)) {
            $F9C_sn = $settings->F9C_sn;
        } else {
            $F9C_sn = 1;
        }
        if (!empty($settings)) {
            $F16a_from_no = $settings->F16A_form_sn;
        } else {
            $F16a_from_no = 1;
        }
        if (!empty($settings)) {
            $F21c_from_no = $settings->F21C_form_sn;
        } else {
            $F21c_from_no = 1;
        }
        if (!empty($settings)) {
            $F15a9ab_from_no = $settings->F159ABC_form_sn;
        } else {
            $F15a9ab_from_no = 1;
        }
        $business_location_name = BusinessLocation::where('business_id', $business_id)->pluck('name')->first();
        $ref_pre_form_number = Mpcs9cCashFormSettings::where('business_id', $business_id)->orderBy('id', 'desc')->first();
        if (!empty($ref_pre_form_number)) {
            $form_9a_no = $ref_pre_form_number->starting_number;
        } else {
            $form_9a_no = 1;
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $merged_sub_categories = MergedSubCategory::where('business_id', $business_id)->get();

        $sub_categories = Category::where('business_id', $business_id)->where('parent_id', '!=', 0)->get();

        $setting = MpcsFormSetting::where('business_id', $business_id)->first();

        return view('mpcs::forms.form_set_1')->with(compact('business_locations', 'F9C_sn', 'business_location_name', 'form_9a_no', 'F16a_from_no', 'F21c_from_no', 'F15a9ab_from_no', 'merged_sub_categories', 'sub_categories', 'setting'));
    }

    public function From9A()
    {
        $business_id = request()->session()->get('business.id');
        $business_locations = BusinessLocation::forDropdown($business_id);

        $ref_pre_form_number = DB::connection('mysql')->table('mpcs_9a_form_settings')->where('business_id', $business_id)->first();
        $form_9a_no = $ref_pre_form_number ? $ref_pre_form_number->starting_number : '';
        //$form_number = $ref_pre_form_number ? $ref_pre_form_number->ref_pre_form_number : '';

        $start_date = DB::connection('mysql')->table('mpcs_9a_form_settings')->select(DB::raw('date'))->where('business_id', $business_id)->first();
        $date = $start_date ? $start_date->date : '';

        $username = DB::connection('mysql')

            ->table('business')
            ->select(DB::raw('name'))
            ->where('id', $business_id)
            ->first();
        $name = $username ? $username->name : '';
      
        if (!empty($ref_pre_form_number)) {
            // Get today's date and the starting day from settings (assumes the field 'date' is the starting day)
            $current_date = Carbon::today();
            $starting_day = Carbon::parse($ref_pre_form_number->date);
            $days_passed = $starting_day->diffInDays($current_date);
          
            // If no days have passed yet, use the starting number
            if ($days_passed === 0) {
                $form_number = $ref_pre_form_number->starting_number;
            } else {
                // Otherwise, increment the form number based on the number of days passed
                // Add the number of days passed to the starting number
                $form_number = $ref_pre_form_number->starting_number + $days_passed;
               
            }
        } else {
            $form_number = 1;
        }
        return view('mpcs::forms.form_9a')->with(compact('business_locations', 'form_9a_no', 'form_number', 'date', 'name'));
    }

    //  public function From9C()
    //     {
    //         $business_id = request()->session()->get('business.id');
    //         $business_locations = BusinessLocation::forDropdown($business_id);
    //         $business_location_name = BusinessLocation::where('business_id', $business_id)
    //                     ->pluck('name')
    //                     ->first();
    //         $ref_pre_form_number = Mpcs9cCashFormSettings::where('business_id', $business_id)
    //                             ->orderBy('id', 'desc')
    //                             ->first();

    //           if (!empty($ref_pre_form_number)) {
    //             $form_9a_no = $ref_pre_form_number->starting_number;
    //         } else {
    //             $form_9a_no = 1;
    //         }

    //         return view('mpcs::forms.form_9c')->with(compact(
    //             'business_locations',
    //             'business_location_name',
    //             'form_9a_no'
    //         ));
    //     }

    public function From9C()
    {
        $business_id = request()->session()->get('business.id');
        $business_locations = BusinessLocation::forDropdown($business_id);
        $business_location_name = BusinessLocation::where('business_id', $business_id)->value('name');

        $form_date = Mpcs9cCashFormSettings::where('business_id', $business_id)->orderBy('id', 'desc')->value('date_time');
        $latest_entry = Mpcs9cCashFormSettings::all();
        $latest_entry = Mpcs9cCashFormSettings::orderBy('id', 'desc')->first();

        $form_date = $latest_entry?->date_time;
        $form_9a_no = $latest_entry?->starting_number ?? 1;


        $parsed_form_date = $form_date ? Carbon::parse($form_date)->timezone(config('app.timezone')) : null;

        $total_days = $parsed_form_date ? $parsed_form_date->diffInDays(Carbon::now()) : 0;

        $form_9a_no = Mpcs9cCashFormSettings::where('business_id', $business_id)->orderBy('id', 'desc')->value('starting_number') ?? 1;


        return view('mpcs::forms.form_9c')->with(compact('business_locations', 'business_location_name', 'form_9a_no', 'total_days'));
    }

    public function From9CCR()
    {
        $business_id = request()->session()->get('business.id');
        $business_locations = BusinessLocation::forDropdown($business_id);
        $business_location_name = BusinessLocation::where('business_id', $business_id)->pluck('name')->first();
        $setting = Mpcs9cCreditFormSettings::where('business_id', $business_id)->orderBy('id', 'desc')->first();

        if (!empty($setting)) {
            $form_9a_no = $setting->starting_number;
        } else {
            $form_9a_no = 1;
        }

        $business = '';
        $default_business_location = '';

        return view('mpcs::forms.form_9ccr')->with(compact('business_locations', 'business_location_name', 'form_9a_no', 'setting', 'business', 'default_business_location'));
    }
/**
     * Show the form for getFrom20
     * @return Response
     */
    public function get9AForm(Request $request)
    {
        $business_id = request()->session()->get('business.id');
      
        $business_details = Business::find($business_id);
        $currency_precision = (int) $business_details->currency_precision;
        $qty_precision = (int) $business_details->quantity_precision;
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $location_id = $request->location_id;

          
            $startDate = Carbon::createFromFormat('Y-m-d', $start_date);
            $endDate = Carbon::createFromFormat('Y-m-d', $end_date);

            //today
            $start_today_date = Carbon::parse($request->start_date)->format('Y-m-d');//Carbon::now();
            $end_today_date =Carbon::parse($request->end_date)->format('Y-m-d');//Carbon::now();
            $previous_day = Carbon::parse($request->start_date)->subDay(1)->format('Y-m-d');          

            $card_sale = DB::table('transactions')
                ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
                ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')              
                ->where('transactions.type', 'sell') // assuming you're filtering sales only
                ->where('transactions.is_credit_sale', 0)
                ->where('transaction_payments.method',"card")
                ->whereDate('transactions.transaction_date', '>=',$start_date)
                ->whereDate('transactions.transaction_date', '<=', $end_date)              
                ->select(
                    
                    DB::raw('SUM(transaction_payments.amount) as total_sales')
                )
                ->first();
               
            $cash_sale = DB::table('transactions')
                ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
                ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')              
                ->where('transactions.type', 'sell') // assuming you're filtering sales only
                ->where('transactions.is_credit_sale', 0)
                ->where('transaction_payments.method',"cash")
                ->whereDate('transactions.transaction_date', '>=',$start_date)
                ->whereDate('transactions.transaction_date', '<=', $end_date)              
                ->select(
                    
                    DB::raw('SUM(transaction_payments.amount) as total_sales')
                )
                ->first();
        
       
            //dd($cash_sales_today);
                $credit_sale = DB::table('transactions')
                ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
                ->where('transactions.is_credit_sale', 1) 
                 ->whereDate('transactions.transaction_date', '>=',$start_date)
                ->whereDate('transactions.transaction_date', '<=', $end_date)
                
                ->select(
                     
                    DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price) as total_sales')
                )
                ->first();
                
            //total sales previous
            $formF22Exists = FormF22Header::whereDate('created_at', $start_date)->exists();
                
            if ($formF22Exists) {
                $previous_total = 0;
                $previous_total_sum =0;
            } else {
                $previous_total = DB::table('transactions')
                    ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
                    ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
                    ->join('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
                    ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')
                    ->where('account_transactions.business_id', $business_id)                  
                    ->whereDate('transactions.transaction_date', '=', $previous_day)
                    ->select(
                        'products.name as product',
                        'transactions.invoice_no as billno',
                        'transaction_sell_lines.quantity as quantity',
                        'transactions.final_total as final_total_rs'
                    )
                    ->groupBy('products.name', 'transactions.invoice_no', 'transaction_sell_lines.quantity', 'transactions.final_total')
                    ->get();
            
                    $previous_total_sum =  $previous_total->sum('final_total_rs');
                 
            }
         
                $cash_sales = $cash_sale->total_sales ?? 0;
                $card_sales = $card_sale->total_sales ?? 0;
                $credit_sales = $credit_sale->total_sales ?? 0;
              //  $previous_totals = $previous_total->final_total_rs ?? 0;

            //cash
                $cash_sales_rup = (int) $cash_sales;                
                $amount = (float)$cash_sales;
                $decimal = round(($amount - floor($amount)) * 100); // This gives the decimal part (e.g., 56 from 205.56)               
                $cash_sales_cent = str_pad($decimal, $currency_precision, '0', STR_PAD_LEFT); // Ensure 2 digits like 05, 09, etc.
              // card
              $card_sales_rup = (int) $card_sales;                
              $amount = (float)$card_sales;
              $decimal = round(($amount - floor($amount)) * 100); // This gives the decimal part (e.g., 56 from 205.56)               
              $card_sales_cent = str_pad($decimal, $currency_precision, '0', STR_PAD_LEFT); // Ensure 2 digits like 05, 09, etc.

             //credit
             $credit_sales_rup = (int) $credit_sales;                
             $amount = (float)$credit_sales;
             $decimal = round(($amount - floor($amount)) * 100); // This gives the decimal part (e.g., 56 from 205.56)               
             $credit_sales_cent = str_pad($decimal, $currency_precision, '0', STR_PAD_LEFT); // Ensure 2 digits like 05, 09, etc

             //previous sales
              //credit
             
              $previous_sales_rup = (int) $previous_total_sum;                
              $amount = (float)$previous_total_sum;
              $decimal = round(($amount - floor($amount)) * 100); // This gives the decimal part (e.g., 56 from 205.56)               
              $previous_sales_cent = str_pad($decimal, $currency_precision, '0', STR_PAD_LEFT); // Ensure 2 digits like 05, 09, etc
 
              return response()->json([
                "credit_sales_rup" => $credit_sales_rup, 
                "credit_sales_cent" => $credit_sales_cent, 
                "cash_sales_rup" => $cash_sales_rup,
                "cash_sales_cent" => $cash_sales_cent,
                "card_sales_rup" => $card_sales_rup, 
                "card_sales_cent" => $card_sales_cent,  
                "previous_sales_rup" => $previous_sales_rup, 
                "previous_sales_cent" => $previous_sales_cent,          
                'currency_precision' => $currency_precision,
                'qty_precision' => $qty_precision
            ]);
    }
    /**
     * Show the form for getFrom20
     * @return Response
     */
    public function get9CForm(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $settings = MpcsFormSetting::where('business_id', $business_id)->first();
        if (!empty($settings)) {
            $F9C_sn = $settings->F9C_sn;
        } else {
            $F9C_sn = 1;
        }
        if (request()->ajax()) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $cash_sales_today = DB::table('transactions')
            ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
            ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')          
            ->join('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
            ->where('account_transactions.business_id', $business_id)
            ->where('account_transactions.type', 'debit')
            ->whereNull('transactions.customer_group_id')           
            ->whereDate('transactions.transaction_date', '>=', $start_today_date)
            ->whereDate('transactions.transaction_date', '<=', $end_today_date)
            ->groupBy('categories.id', 'categories.name')
            ->select(
                'products.name as product',
                'transactions.invoice_no as billno',              
                DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity'),
                DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price) as total_sales')
            )
            ->get(); 
            return Datatables::of($cash_sales_today)
           
            ->make(true);
        
        }
    }

    public function get9CCRForm(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $settings = MpcsFormSetting::where('business_id', $business_id)->first();
        if (!empty($settings)) {
            $F9C_sn = $settings->F9C_sn;
        } else {
            $F9C_sn = 1;
        }
        if (request()->ajax()) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $location_id = $request->location_id;

            $credit_sales = $this->Form9CQuery($business_id, $start_date, $end_date, $location_id);

            $location = [];
            if (!empty($request->location_id)) {
                $location = BusinessLocation::findOrFail($request->location_id);
            }

            $sub_categories = Category::where('business_id', $business_id)->where('parent_id', '!=', 0)->get();

            return view('mpcs::forms.partials.9c_details_section')->with(compact('credit_sales', 'sub_categories', 'start_date', 'end_date', 'location', 'F9C_sn'));
        }
    }
    public function get21CForms(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $settings = MpcsFormSetting::where('business_id', $business_id)->first();
        if (!empty($settings)) {
            $F9C_sn = $settings->F9C_sn;
        } else {
            $F9C_sn = 1;
        }
        $business_details = Business::find($business_id);
        $currency_precision = (int) $business_details->currency_precision;
        $qty_precision = (int) $business_details->quantity_precision;
        if (request()->ajax()) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $location_id = $request->location_id;
            $previous_start_date = Carbon::parse($request->start_date)->subDays(1)->format('Y-m-d');
            $previous_end_date = Carbon::parse($request->end_date)->subDays(1)->format('Y-m-d');
            $credit_sales = $this->Form9CQuery($business_id, $start_date, $end_date, $location_id);
            $todays = $this->Form21CQuerytoday($business_id, $start_date, $end_date, $location_id);
            $receipts = $this->Form21CQueryReceipts($business_id, $start_date, $end_date, $location_id);
            $previous_days = $this->Form21CQuerypreviousday($business_id, $previous_start_date, $previous_end_date, $location_id);

            //   $credit_sales = $this->Form21CQuery($business_id, $start_date, $end_date, $location_id);
            //         $sub_categories = Category::where('business_id', $business_id)->where('parent_id', '!=', 0)->get();
            $cash_sales_todays = $this->Form21CQueryCashToday($business_id, $start_date, $end_date, $location_id);
            $opening_stocks = $this->Form21CQueryOpeningStock($business_id, $start_date, $end_date, $location_id);
            $issuess = $this->Form21CQueryIssue($business_id, $start_date, $end_date, $location_id);
            //         $total_receipts =$this->Form21CQueryOpeningStock($business_id, $start_date, $end_date, $location_id);
            $total_receipts_todates = $this->Form21CQueryTotalReceipts($business_id, $start_date, $end_date, $location_id);
            $corporate_section_todates = $this->Form21CQueryCorporate($business_id, $start_date, $end_date, $location_id);
            $total_issuess = $this->Form21CQueryTotalIssue($business_id, $start_date, $end_date, $location_id);
            $issue_upto_last_days = $this->Form21CQueryTotalIssueLastday($business_id, $start_date, $end_date, $location_id);
            $total_issue_ones = $this->Form21CQueryTotalIssueOne($business_id, $start_date, $end_date, $location_id);
            $price_discount_todays = $this->Form21CQueryDiscountToday($business_id, $start_date, $end_date, $location_id);
            $price_discount_previouss = $this->Form21CQueryDiscountPrevious($business_id, $previous_start_date, $previous_end_date, $location_id);
            $total_discount_twos = $this->Form21CQueryDiscountTotal($business_id, $start_date, $end_date, $location_id);
            $total_discount_one_twos = $this->Form21CQueryOpeningStock($business_id, $start_date, $end_date, $location_id);
            $balances = $this->Form21CQueryBalance($business_id, $start_date, $end_date, $location_id);
            $subtotal_for_todays = $this->Form21CQuerySubtotal($business_id, $start_date, $end_date, $location_id);
            $pump_one_last_meters = $this->Form21CQueryPumperLast($business_id, $start_date, $end_date, $location_id);
            $issue_qty_todays = $this->Form21CQueryQuantityIssue($business_id, $start_date, $end_date, $location_id);
            $location = [];
            if (!empty($request->location_id)) {
                $location = BusinessLocation::findOrFail($request->location_id);
            }

            $sub_categories = Category::where('business_id', $business_id)->where('parent_id', '!=', 0)->get();

            return view('mpcs::forms.partials.21c_details_section')->with(compact('receipts', 'todays', 'previous_days', 'opening_stocks', 'cash_sales_todays', 'issuess', 'total_receipts_todates', 'credit_sales', 'corporate_section_todates', 'total_issuess', 'issue_upto_last_days', 'total_issue_ones', 'price_discount_todays', 'price_discount_previouss', 'total_discount_twos', 'total_discount_one_twos', 'balances', 'subtotal_for_todays', 'pump_one_last_meters', 'issue_qty_todays', 'sub_categories', 'start_date', 'end_date', 'currency_precision', 'qty_precision', 'location', 'F9C_sn'));
        }
    }

    public function Form9CQuery($business_id, $start_date, $end_date, $location_id)
    {
        $query = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')->leftjoin('business', 'transactions.business_id', 'business.id')->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')->where('transactions.business_id', $business_id)->where('transactions.is_credit_sale', 1) // ->whereDate('transactions.transaction_date', '>=', $start_date)
            // ->whereDate('transactions.transaction_date', '<=', $end_date)
            ->select('transactions.transaction_date', 'transactions.final_total', 'products.name as description', 'products.sub_category_id', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'contacts.name as customer', 'transactions.invoice_no as order_no', 'business.name as comapany', 'business_locations.mobile as tel');
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        if (!empty($start_date)) {
            $query->whereDate('transactions.transaction_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $query->whereDate('transactions.transaction_date', '<=', $end_date);
        }
        $credit_sales = $query->get();

        return $credit_sales;
    }

    public function getPreviousValue9CForm(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $settings = MpcsFormSetting::where('business_id', $business_id)->first();
        $F9C_tdate = $settings->F9C_tdate;
        $checkSetValueZero = $this->checkSetValueZero9C($request);
        $start_date = $checkSetValueZero['start_date'];
        $end_date = Carbon::parse($request->start_date)->subDays(1)->format('Y-m-d');
        $location_id = $request->location_id;

        $credit_sales = $this->Form9CQuery($business_id, $start_date, $end_date, $location_id);
        $sub_categories = Category::where('business_id', $business_id)->where('parent_id', '!=', 0)->get();

        $sub_cat_data = [];
        foreach ($sub_categories as $item) {
            $pre_page_set_value = Form9cSubCategory::where('business_id', $business_id)->where('sub_category_id', $item->id)->first();
            if ($request->start_date == $F9C_tdate) {
                $sub_cat_data[$item->id]['qty'] = !empty($pre_page_set_value->qty) ? $pre_page_set_value->qty : 0;
                $sub_cat_data[$item->id]['amount'] = !empty($pre_page_set_value->amount) ? $pre_page_set_value->amount : 0;
            } elseif ($checkSetValueZero['status']) {
                $sub_cat_data[$item->id]['qty'] = 0.0;
                $sub_cat_data[$item->id]['amount'] = 0.0;
            } elseif ($checkSetValueZero['lap']) {
                $sub_cat_data[$item->id]['qty'] = 0;
                $sub_cat_data[$item->id]['amount'] = 0;
                foreach ($credit_sales as $sale) {
                    if ($item->id == $sale->sub_category_id) {
                        $sub_cat_data[$item->id]['qty'] = $sale->quantity;
                        $sub_cat_data[$item->id]['amount'] = $sale->unit_price * $sale->quantity;
                    }
                }
            } else {
                $sub_cat_data[$item->id]['qty'] = !empty($pre_page_set_value->qty) ? $pre_page_set_value->qty : 0;
                $sub_cat_data[$item->id]['amount'] = !empty($pre_page_set_value->amount) ? $pre_page_set_value->amount : 0;
                foreach ($credit_sales as $sale) {
                    if ($item->id == $sale->sub_category_id) {
                        $sub_cat_data[$item->id]['qty'] = !empty($pre_page_set_value->qty) ? $pre_page_set_value->qty : 0 + $sale->quantity;
                        $sub_cat_data[$item->id]['amount'] = !empty($pre_page_set_value->amount) ? $pre_page_set_value->amount : 0 + $sale->unit_price * $sale->quantity;
                    }
                }
            }
        }

        return $sub_cat_data;
    }

    public function checkSetValueZero9C($request)
    {
        $business_id = request()->session()->get('user.business_id');
        $settings = MpcsFormSetting::where('business_id', $business_id)->first();
        $result['status'] = 0;
        $result['lap'] = 0;
        $result['start_date'] = $settings->F9C_tdate;
        $date_diff_days = Carbon::parse($settings->F9C_tdate)->diffInDays(Carbon::parse($request->start_date), false);
        $date_diff_months = Carbon::parse($settings->F9C_tdate)->diffInMonths(Carbon::parse($request->start_date), false);
        if ($date_diff_days < 0) {
            $result['status'] = 1;
        }
        $stock_taking_date = FormF22Header::where('business_id', $business_id)->orderBy('id', 'desc')->first();
        if (!empty($stock_taking_date)) {
            if ($request->start_date == $stock_taking_date->form_date) {
                $result['lap'] = 1;
            }
        }

        if ($settings->F9C_first_day_of_next_month) {
            $F9C_first_day_of_next_month_selected = $date_diff_days <= 30 * $settings->F9C_first_day_of_next_month_selected ? $settings->F9C_first_day_of_next_month_selected - 1 : $settings->F9C_first_day_of_next_month_selected;
            if (!empty($F9C_first_day_of_next_month_selected)) {
                $first_date_after_selected_mothn_from_start_date = Carbon::parse($settings->F9C_tdate)->addMonth($F9C_first_day_of_next_month_selected)->firstOfMonth()->format('Y-m-d');

                if ($date_diff_months % $F9C_first_day_of_next_month_selected == 0 && $date_diff_months > 0) {
                    $result['start_date'] = $first_date_after_selected_mothn_from_start_date;
                    $result['lap'] = 1;
                }
                if ($request->start_date == $first_date_after_selected_mothn_from_start_date) {
                    $result['status'] = 1;
                }
            }
        }

        return $result;
    }

    public function get16AForm(Request $request)
    {
        // dd($request->start_date);
        $business_id = request()->session()->get('user.business_id');
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $location_id = $request->location_id;
        $business_details = Business::find($business_id);
        $currency_precision = (int) $business_details->currency_precision;
        $qty_precision = (int) $business_details->quantity_precision;

        if (request()->ajax()) {            
      
            $purchases = $this->F16AQuery($business_id, $start_date, $end_date, $location_id);
            $rows = [];
        //     if (auth()->check() && auth()->user()->can('user'))
        //     $setting = Mpcs21cFormSettings::where('date', $start_date)->first();
        // else 
            $setting = Mpcs21cFormSettings::first();
    //   dd($setting);
        $form_number_21 = optional($setting)->ref_pre_form_number ? $setting->ref_pre_form_number : "";
            foreach ($purchases->get() as $purchaseDet) {
                $purchase = Transaction::where('business_id', $business_id)
                    ->where('id', $purchaseDet->id)
                    ->withTrashed()
                    ->with(['contact', 'purchase_lines', 'purchase_lines.product', 'purchase_lines.product.unit', 'purchase_lines.variations', 'purchase_lines.variations.product_variation', 'purchase_lines.sub_unit', 'location', 'purchase_lines.product.category'])
                    ->first();

                if ($purchase) {
                    foreach ($purchase->purchase_lines as $line) {
                        // dd($form_number_21);
                        $rows[] = [
                            'index_no' => $purchase->invoice_no,
                            'product' => optional($line->product)->name,
                            'location' => optional($purchase->location)->name,
                            'received_qty' => number_format($line->quantity, $qty_precision),
                            'unit_purchase_price' => number_format($line->variations->dpp_inc_tax, $currency_precision),
                            'total_purchase_price' => '<span class="display_currency total_purchase_price" data-orig-value="' . $line->variations->dpp_inc_tax * $line->quantity . '" data-currency_symbol = false>' . number_format($line->variations->dpp_inc_tax * $line->quantity, $currency_precision) . '</span>',
                            'unit_sale_price' => number_format($line->variations->sell_price_inc_tax, $currency_precision),
                            'total_sale_price' => '<span class="display_currency total_sale_price" data-orig-value="' . $line->variations->sell_price_inc_tax * $line->quantity . '" data-currency_symbol = false>' . number_format($line->variations->sell_price_inc_tax * $line->quantity, $currency_precision) . '</span>',
                            'reference_no' => $purchase->ref_no ?? '-',
                            'stock_book_no' => $form_number_21 ?? '',
                        ];
                    }
                }
            }
//             $data=$rows->toArray();
// \Log::info("F16 settings{$data}");
            return Datatables::of($rows)
                ->rawColumns(['total_purchase_price', 'total_sale_price'])
                ->make(true);
        }
    }
///get 9 c cash
public function get9CCashForm(Request $request)
{
    try {
     
        $business_id = $request->session()->get('user.business_id');
        $business_details = Business::find($business_id);
        $currency_precision = (int) $business_details->currency_precision;
        if ($request->ajax()) {
            $business_id = $request->session()->get('user.business_id');
            $business_details = Business::find($business_id);
            $currency_precision = (int) $business_details->currency_precision;
            $qty_precision = (int) $business_details->quantity_precision;
            $start_date = Carbon::parse($request->start_date)->format('Y-m-d');
            $end_date = Carbon::parse($request->end_date)->format('Y-m-d');
             
            $previous_day = Carbon::parse($request->start_date)->subDay(1)->format('Y-m-d');
            
            $cash_sales_today = DB::table('transactions')
                ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
                ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
                ->join('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
                ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')
                ->where('account_transactions.business_id', $business_id)
                ->where('transactions.is_credit_sale', 0)
                ->whereDate('transactions.transaction_date', '>=', $start_date)
                ->whereDate('transactions.transaction_date', '<=', $end_date)
                ->select(
                    'products.name as product',
                    'transactions.invoice_no as billno',
                    'transaction_sell_lines.quantity as quantity',
                    'transactions.final_total as final_total_rs'
                )
                ->groupBy('products.name', 'transactions.invoice_no', 'transaction_sell_lines.quantity', 'transactions.final_total')
                ->get();
                // Check if FormF22Header exists for the given start date
                $formF22Exists = FormF22Header::whereDate('created_at', $start_date)->exists();
                $header = Mpcs21cFormSettings::orderBy('created_at', 'desc')
                ->get();
               $previous_totals=[];
                if ($formF22Exists) {
                    $previous_total = 0;
                } else {
                    $previous_total = DB::table('transactions')
                        ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
                        ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
                        ->join('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
                        ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')
                        ->where('account_transactions.business_id', $business_id)
                        ->where('transactions.is_credit_sale', 0)
                        ->whereDate('transactions.transaction_date', '>=', $header->first()->date)
                        ->whereDate('transactions.transaction_date', '<=', $previous_day)
                        ->select(
                            'products.name as product',
                            'transactions.invoice_no as billno',
                            'transaction_sell_lines.quantity as quantity',
                            'transactions.final_total as final_total_rs'
                        )
                        ->groupBy('products.name', 'transactions.invoice_no', 'transaction_sell_lines.quantity', 'transactions.final_total')
                        ->get();
                
                        $previous_totals=$previous_total->sum('final_total_rs');
                }
             
                \Log::info('get9CCashForm called', [
                    'business_id' => $business_id,
                    'prev' =>$previous_day,
                    'data' => $previous_totals,
                     
                ]);
            return Datatables::of($cash_sales_today)
            ->removeColumn('id')
            ->with([
                'previous_total' => [
                    'rs' => (int) $previous_totals,
                    'cent' => str_pad(round(($previous_totals - floor($previous_totals)) * 100), $currency_precision, '0', STR_PAD_LEFT),
                ]
            ])
            ->addColumn('page', fn () => '')

            ->editColumn('quantity', function ($row) use($qty_precision){
                return str_pad($row->quantity, $qty_precision, '0', STR_PAD_LEFT); // Ensure 2 digits like 05, 09, etc.
            })
            // Split final_total into Rs and Cents
            ->editColumn('final_total_rs', function ($row) {
                $amount = (float) $row->final_total_rs;
                return (int) $amount;
            })
            ->addColumn('final_total_cent', function ($row) use($currency_precision){
                $amount = (float) $row->final_total_rs;
                $decimal = round(($amount - floor($amount)) * 100);
                return str_pad($decimal, $currency_precision, '0', STR_PAD_LEFT);
            })
        
            // Optional: You can handle other columns similarly if needed
            ->addColumn('goods_rs', fn () => '')
            ->addColumn('goods_cent', fn () => '')
            ->addColumn('loading_rs', fn () => '')
            ->addColumn('loading_cent', fn () => '')
            ->addColumn('empty_rs', fn () => '')
            ->addColumn('empty_cent', fn () => '')
            ->addColumn('transport_rs', fn () => '')
            ->addColumn('transport_cent', fn () => '')
            ->addColumn('other_rs', fn () => '')
            ->addColumn('other_cent', fn () => '')
            ->make(true);
            
        }
        
    } catch (\Exception $e) {
        \Log::error('Error in get9CCashForm', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'request_data' => $request->all(),
        ]);
        return response()->json(['error' => 'An error occurred while processing the request.'], 500);
    }
}
///get 9 c cash
public function get9CCreditForm(Request $request)
{
    try {
     
        $business_id = $request->session()->get('user.business_id');
        $business_details = Business::find($business_id);
        $currency_precision = (int) $business_details->currency_precision;
        if ($request->ajax()) {
            $business_id = $request->session()->get('user.business_id');
            $business_details = Business::find($business_id);
            $currency_precision = (int) $business_details->currency_precision;
            $qty_precision = (int) $business_details->quantity_precision;
            $start_date = Carbon::parse($request->start_date)->format('Y-m-d');
            $end_date = Carbon::parse($request->end_date)->format('Y-m-d');
             
            $previous_day = Carbon::parse($request->start_date)->subDay(1)->format('Y-m-d');
            
            $cash_sales_today = DB::table('transactions')
                ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
                ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
                ->join('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
                ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')
                ->where('account_transactions.business_id', $business_id)
                ->where('transactions.is_credit_sale', 0)
                ->whereDate('transactions.transaction_date', '>=', $start_date)
                ->whereDate('transactions.transaction_date', '<=', $end_date)
                ->select(
                    'products.name as product',
                    'transactions.invoice_no as billno',
                    'transaction_sell_lines.quantity as quantity',
                    'transactions.final_total as final_total_rs'
                )
                ->groupBy('products.name', 'transactions.invoice_no', 'transaction_sell_lines.quantity', 'transactions.final_total')
                ->get();
                // Check if FormF22Header exists for the given start date
                $formF22Exists = FormF22Header::whereDate('created_at', $start_date)->exists();
                $header = Mpcs21cFormSettings::orderBy('created_at', 'desc')
                ->get();
               $previous_totals=[];
                if ($formF22Exists) {
                    $previous_total = 0;
                } else {
                    $previous_total = DB::table('transactions')
                        ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
                        ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
                        ->join('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
                        ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')
                        ->where('account_transactions.business_id', $business_id)
                        ->where('transactions.is_credit_sale', 1)
                        ->whereDate('transactions.transaction_date', '>=', $header->first()->date)
                        ->whereDate('transactions.transaction_date', '<=', $previous_day)
                        ->select(
                            'products.name as product',
                            'transactions.invoice_no as billno',
                            'transaction_sell_lines.quantity as quantity',
                            'transactions.final_total as final_total_rs'
                        )
                        ->groupBy('products.name', 'transactions.invoice_no', 'transaction_sell_lines.quantity', 'transactions.final_total')
                        ->get();
                
                        $previous_totals=$previous_total->sum('final_total_rs');
                }
             
                \Log::info('get9CCashForm called', [
                    'business_id' => $business_id,
                    'prev' =>$previous_day,
                    'data' => $previous_totals,
                     
                ]);
            return Datatables::of($cash_sales_today)
            ->removeColumn('id')
            ->with([
                'previous_total' => [
                    'rs' => (int) $previous_totals,
                    'cent' => str_pad(round(($previous_totals - floor($previous_totals)) * 100), $currency_precision, '0', STR_PAD_LEFT),
                ]
            ])
            ->addColumn('page', fn () => '')

            ->editColumn('quantity', function ($row) use($qty_precision){
                return str_pad($row->quantity, $qty_precision, '0', STR_PAD_LEFT); // Ensure 2 digits like 05, 09, etc.
            })
            // Split final_total into Rs and Cents
            ->editColumn('final_total_rs', function ($row) {
                $amount = (float) $row->final_total_rs;
                return (int) $amount;
            })
            ->addColumn('final_total_cent', function ($row) use($currency_precision){
                $amount = (float) $row->final_total_rs;
                $decimal = round(($amount - floor($amount)) * 100);
                return str_pad($decimal, $currency_precision, '0', STR_PAD_LEFT);
            })
        
            // Optional: You can handle other columns similarly if needed
            ->addColumn('goods_rs', fn () => '')
            ->addColumn('goods_cent', fn () => '')
            ->addColumn('loading_rs', fn () => '')
            ->addColumn('loading_cent', fn () => '')
            ->addColumn('empty_rs', fn () => '')
            ->addColumn('empty_cent', fn () => '')
            ->addColumn('transport_rs', fn () => '')
            ->addColumn('transport_cent', fn () => '')
            ->addColumn('other_rs', fn () => '')
            ->addColumn('other_cent', fn () => '')
            ->make(true);
            
        }
        
    } catch (\Exception $e) {
        \Log::error('Error in get9CCashForm', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'request_data' => $request->all(),
        ]);
        return response()->json(['error' => 'An error occurred while processing the request.'], 500);
    }
}
    public function F16AQuery($business_id, $start_date, $end_date, $location_id)
    {
        $purchases = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
            ->join('business_locations AS BS', 'transactions.location_id', '=', 'BS.id')
            ->leftJoin('transaction_payments AS TP', 'transactions.id', '=', 'TP.transaction_id')
            ->leftJoin('transactions AS PR', 'transactions.id', '=', 'PR.return_parent_id')
            ->leftjoin('purchase_lines', 'transactions.id', 'purchase_lines.transaction_id')
            ->leftjoin('products', 'purchase_lines.product_id', 'products.id')
            ->leftjoin('variations', 'products.id', 'variations.product_id')
            ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'purchase')
            ->where('transactions.status', 'received')
            ->select(
                'transactions.id',
                'transactions.ref_no as reference_no',
                'purchase_lines.quantity as received_qty',
                'purchase_lines.purchase_price_inc_tax as unit_purchase_price',
                'transactions.final_total as total_purchase_price',
                'BS.name as location',
                'products.name as product',
                'products.id as product_id',
                'variations.default_sell_price',
                'variations.sell_price_inc_tax',
                'transactions.pay_term_number',
                'transactions.pay_term_type',
                'PR.id as return_transaction_id',
                DB::raw('SUM(TP.amount) as amount_paid'),
                DB::raw('(SELECT SUM(TP2.amount) FROM transaction_payments AS TP2 WHERE
                        TP2.transaction_id=PR.id ) as return_paid'),
                DB::raw('COUNT(PR.id) as return_exists'),
                DB::raw('COALESCE(PR.final_total, 0) as amount_return'),
                DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by"),
                'transactions.invoice_no',
            )
            ->groupBy('transactions.id');
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $purchases->whereIn('transactions.location_id', $permitted_locations);
        }

        if (!empty($location_id)) {
            $purchases->where('transactions.location_id', $location_id);
        }

        if (!empty($start_date)) {
            $purchases->whereDate('transactions.transaction_date', $start_date);
        }
        $purchases->orderBy('id', 'DESC');
        return $purchases;
    }

    public function getPreviousValue16AForm(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $settings = MpcsFormSetting::where('business_id', $business_id)->first();
        $F16A_form_tdate = $settings->F16A_form_tdate;
       // $start_date = $F16A_form_tdate;
        $end_date = Carbon::parse($request->start_date)->subDays(1)->format('Y-m-d');
        $start_date = Carbon::parse($request->start_date)->format('Y-m-d');
        $start_date_prev = Carbon::parse($request->start_date)->subDays(1)->format('Y-m-d');

        $location_id = $request->location_id;
     $formF16asettings=Mpcs16aFormSettings::where('business_id', $business_id)->first();
        $purchases = $this->F16AQuery($business_id, $start_date, $end_date, $location_id)->get();
        $pre_total_purchase_price = $settings->F16A_total_pp;
        $pre_total_sale_price = $settings->F16A_total_sp;
          //total sales previous
          $formF22Exists = FormF22Header::whereDate('created_at', $start_date)->exists();    
        
           \Log::info("F16A{$formF22Exists}");       
          $header = Mpcs21cFormSettings::orderBy('created_at', 'desc')
          ->get();
         $previous_totals=[];
         $starting_date= $header->first()->date;
         $purchase_query_prevs=[];
          if ($formF22Exists) {
              $previous_total = 0;
              $purchase_query_prevs=0;

          } else {
              $previous_total = DB::table('transactions')
                  ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
                  ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
                  ->join('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
                  ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')
                  ->where('account_transactions.business_id', $business_id)
                  ->where('transactions.is_credit_sale', 1)
                  ->whereDate('transactions.transaction_date', '>=', $formF16asettings->date)
                  ->whereDate('transactions.transaction_date', '<=', $start_date_prev)
                  ->select(
                      'products.name as product',
                      'transactions.invoice_no as billno',
                      'transaction_sell_lines.quantity as quantity',
                      'transactions.final_total as final_total_rs'
                  )
                  ->groupBy('products.name', 'transactions.invoice_no', 'transaction_sell_lines.quantity', 'transactions.final_total')
                  ->get();
          
                  $previous_totals=$previous_total->sum('final_total_rs');
            $purchase_query_prev = DB::table('transactions')
                  ->join('purchase_lines', 'transactions.id', '=', 'purchase_lines.transaction_id')
                  ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')
                  ->join('products', 'purchase_lines.product_id', '=', 'products.id')
                  ->join('categories', 'products.sub_category_id', '=', 'categories.id')
                  ->where('transactions.type', 'purchase')                 
                   ->whereDate('transactions.transaction_date', '>=', $formF16asettings->date)
                  ->whereDate('transactions.transaction_date', '<=', $start_date_prev)
                  ->select(
                      'categories.id as category_id',
                      'categories.name as category_name',
                      DB::raw('SUM(transaction_payments.amount) as total_amount')                  
                  )
                  
                  ->groupBy('categories.id', 'categories.name')
                  ->get();
                
                  $purchase_query_prevs=$purchase_query_prev->sum('total_amount');
                   
          }     
          //dd($start_date);
          if ($formF22Exists) {
              $pre_total_purchase_price = 0;
              $pre_total_sale_price =0;
          } else {
            if ($formF16asettings->date == $start_date) {
                \Log::info("F16Astart date{ $start_date}");
    $pre_total_sale_price = $formF16asettings->total_sale_price_with_vat;
    $pre_total_purchase_price = $formF16asettings->total_purchase_price_with_vat;
} else {
     \Log::info("previous{ $previous_totals}");
    $pre_total_sale_price = $formF16asettings->total_sale_price_with_vat +  $previous_totals;
    $pre_total_purchase_price =  $formF16asettings->total_purchase_price_with_vat + $purchase_query_prevs;
}

           
            // foreach ($purchases as $item) {
            //     $pre_total_purchase_price = $pre_total_purchase_price + $item->total_purchase_price;
            //     $pre_total_sale_price = $pre_total_sale_price + $item->default_sell_price * $item->received_qty;
            // }
          }
 
    \Log::info("F16A data{$pre_total_purchase_price}");
        return ['pre_total_purchase_price' => $pre_total_purchase_price, 'pre_total_sale_price' => $pre_total_sale_price];
    }

    public function F14()
    {
        return view('mpcs::forms.F14_form');
    }

    public function F159ABC()
    {
        $business_id = request()->session()->get('business.id');

        if (request()->ajax()) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $location_id = $request->location_id;

            $settings = MpcsFormSetting::where('business_id', $business_id)->first();
            $previous_start_date = Carbon::parse($request->start_date)->subDays(1)->format('Y-m-d');
            $previous_end_date = Carbon::parse($request->end_date)->subDays(1)->format('Y-m-d');
            $startDate = Carbon::createFromFormat('Y-m-d', $start_date);
            $endDate = Carbon::createFromFormat('Y-m-d', $end_date);

            $credit_sales = $this->F159ABCQuery($business_id, $start_date, $end_date, $location_id);
            $previous_credit_sales = $this->F159ABCQuery($business_id, $previous_end_date, $previous_end_date, $location_id);
            $form22_details = FormF22Detail::where('business_id', $business_id)->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->orderBy('id', 'DESC')->first();

            return [
                'credit_sales' => $credit_sales,
                'previous_credit_sales' => $previous_credit_sales,
                'form22_details' => $form22_details,
            ];
        }
    }

    public function F159ABCQuery($business_id, $start_date, $end_date, $location_id)
    {
        $query = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
            ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->leftjoin('business', 'transactions.business_id', 'business.id')
            ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.is_credit_sale', 1)
            ->whereDate('transactions.transaction_date', '>=', $start_date)
            ->whereDate('transactions.transaction_date', '<=', $end_date)
            ->select('transactions.transaction_date', 'transactions.final_total as opening_balance', 'products.name as description', 'products.sub_category_id', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'contacts.name as customer', 'transactions.invoice_no as order_no', 'business.name as comapany', 'business_locations.mobile as tel');
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        $credit_sales = $query->get();

        return $credit_sales;
    }

    public function F21()
    {
        $layout = 'layouts.app';
        return view('mpcs::forms.F21_form')->with(compact('layout'));
    }

    public function get21CForm(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        $merged_sub_categories = MergedSubCategory::where('business_id', $business_id)->get();
        $business_locations = BusinessLocation::forDropdown($business_id);
        //  dd($business_locations);
        if (request()->ajax()) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $location_id = $request->location_id;

            $settings = MpcsFormSetting::where('business_id', $business_id)->first();
            $F21C_form_tdate = $settings->F21C_form_tdate;
            $previous_start_date = Carbon::parse($request->start_date)->subDays(1)->format('Y-m-d');
            $previous_end_date = Carbon::parse($request->end_date)->subDays(1)->format('Y-m-d');
            $startDate = Carbon::createFromFormat('Y-m-d', $start_date);
            $endDate = Carbon::createFromFormat('Y-m-d', $end_date);

            $credit_sales = $this->Form21CQuery($business_id, $start_date, $end_date, $location_id);
            $previous_credit_sales = $this->Form21CQuery($business_id, $previous_end_date, $previous_end_date, $location_id);
            $form22_details = FormF22Detail::where('business_id', $business_id)->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->orderBy('id', 'DESC')->first();
            $form17_increase = FormF17Detail::where('select_mode', 'increase')->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->orderBy('id', 'DESC')->first();

            $form17_increase_previous = FormF17Detail::where('select_mode', 'increase')->whereDate('created_at', '>=', $previous_start_date)->orderBy('id', 'DESC')->first();

            $form17_decrease = FormF17Detail::where('select_mode', 'descrease')->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->orderBy('id', 'DESC')->first();

            $form17_decrease_previous = FormF17Detail::where('select_mode', 'descrease')
                ->whereDate('created_at', '>=', $previous_start_date)
                // ->whereDate('created_at', '<=', $previous_end_date)
                ->orderBy('id', 'DESC')
                ->first();
            $form17_decrease_previous_day = FormF17Detail::where('select_mode', 'descrease')
                ->whereDate('created_at', '>=', Carbon::now())
                // ->whereDate('created_at', '<=', $previous_end_date)
                ->orderBy('id', 'DESC')
                ->first();

            $transaction = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')->select('transactions.id', 'transactions.transaction_date', 'transactions.final_total', 'transaction_payments.method as payment_method', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'transactions.invoice_no as order_no')->whereDate('transactions.transaction_date', '>=', $start_date)->whereDate('transactions.transaction_date', '<=', $end_date)->orWhere('transaction_payments.method', 'cash')->orWhere('transaction_payments.method', 'cheque')->orWhere('transaction_payments.method', 'card')->orderBy('id', 'DESC')->first();

            $previous_transaction = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                ->select('transactions.id', 'transactions.transaction_date', 'transactions.final_total', 'transaction_payments.method as payment_method', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'transactions.invoice_no as order_no')
                ->whereDate('transactions.transaction_date', '>=', $previous_start_date)
                // ->whereDate('transactions.transaction_date', '<=', $previous_end_date)
                ->orWhere('transaction_payments.method', 'cash')
                ->orWhere('transaction_payments.method', 'cheque')
                ->orWhere('transaction_payments.method', 'card')
                ->orderBy('id', 'DESC')
                ->first();

            $own_group = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')->select('transactions.id', 'transactions.transaction_date', 'transactions.final_total', 'transaction_payments.method as payment_method', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'transactions.invoice_no as order_no')->whereDate('transactions.transaction_date', '>=', $start_date)->whereDate('transactions.transaction_date', '<=', $end_date)->orWhere('transaction_payments.method', 'custom_pay_1')->orWhere('transaction_payments.method', 'custom_pay_2')->orderBy('id', 'DESC')->first();

            $previous_own_group = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')->select('transactions.id', 'transactions.transaction_date', 'transactions.final_total', 'transaction_payments.method as payment_method', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'transactions.invoice_no as order_no')->whereDate('transactions.transaction_date', '>=', $previous_start_date)->orWhere('transaction_payments.method', 'custom_pay_1')->orWhere('transaction_payments.method', 'custom_pay_2')->orderBy('id', 'DESC')->first();

            $credit_sales_transaction = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')->select('transactions.id', 'transactions.transaction_date', 'transactions.final_total', 'transaction_payments.method as payment_method', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'transactions.invoice_no as order_no')->whereDate('transactions.transaction_date', '>=', $start_date)->whereDate('transactions.transaction_date', '<=', $end_date)->where('transaction_payments.method', 'credit_sales')->orderBy('id', 'DESC')->first();

            $previous_credit_sales_transaction = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')->select('transactions.id', 'transactions.transaction_date', 'transactions.final_total', 'transaction_payments.method as payment_method', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'transactions.invoice_no as order_no')->whereDate('transactions.transaction_date', '>=', $previous_start_date)->where('transaction_payments.method', 'credit_sales')->orderBy('id', 'DESC')->first();
            $F21c_from_no = '';
            $sub_categories = $merged_sub_categories;
            $is_ajax = 1;
            $layout = 'layouts.empty';
            return view('mpcs::forms.F21_form')->with(
                compact(
                    'credit_sales',
                    'previous_credit_sales',
                    'form22_details',
                    'form17_increase',
                    'form17_decrease',
                    'transaction',
                    'own_group',
                    'credit_sales_transaction',
                    'previous_transaction',
                    'previous_own_group',
                    'previous_credit_sales_transaction',
                    'form17_increase_previous',
                    'form17_decrease_previous',
                    'merged_sub_categories',
                    'business_locations',
                    // 'F9C_sn',
                    // 'F16a_from_no',
                    'F21c_from_no',
                    // 'F15a9ab_from_no',
                    'sub_categories',
                    // 'setting'
                    'is_ajax',
                    'layout',
                ),
            );
        }
    }

    public function get_21_c_form_all_query(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        $merged_sub_categories = MergedSubCategory::where('business_id', $business_id)->get();

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $location_id = $request->location_id;

        $settings = MpcsFormSetting::where('business_id', $business_id)->first();
        $F21C_form_tdate = $settings->F21C_form_tdate;
        $previous_start_date = Carbon::parse($request->start_date)->subDays(1)->format('Y-m-d');
        $previous_end_date = Carbon::parse($request->end_date)->subDays(1)->format('Y-m-d');
        $startDate = Carbon::createFromFormat('Y-m-d', $start_date);
        $endDate = Carbon::createFromFormat('Y-m-d', $end_date);

        $credit_sales = $this->Form21CQuery($business_id, $start_date, $end_date, $location_id);
        $previous_credit_sales = $this->Form21CQuery($business_id, $previous_end_date, $previous_end_date, $location_id);
        $form22_details = FormF22Detail::where('business_id', $business_id)->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->orderBy('id', 'DESC')->first();
        $form17_increase = FormF17Detail::where('select_mode', 'increase')->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->orderBy('id', 'DESC')->first();

        $form17_increase_previous = FormF17Detail::where('select_mode', 'increase')->whereDate('created_at', '>=', $previous_start_date)->orderBy('id', 'DESC')->first();

        $form17_decrease = FormF17Detail::where('select_mode', 'descrease')->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->orderBy('id', 'DESC')->first();

        $form17_decrease_previous = FormF17Detail::where('select_mode', 'decrease')
            ->whereDate('created_at', '>=', $previous_start_date)
            // ->whereDate('created_at', '<=', $previous_end_date)
            ->orderBy('id', 'DESC')
            ->first();

        $transaction = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')->select('transactions.id', 'transactions.transaction_date', 'transactions.final_total', 'transaction_payments.method as payment_method', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'transactions.invoice_no as order_no')->whereDate('transactions.transaction_date', '>=', $start_date)->whereDate('transactions.transaction_date', '<=', $end_date)->orWhere('transaction_payments.method', 'cash')->orWhere('transaction_payments.method', 'cheque')->orWhere('transaction_payments.method', 'card')->orderBy('id', 'DESC')->first();

        $previous_transaction = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->select('transactions.id', 'transactions.transaction_date', 'transactions.final_total', 'transaction_payments.method as payment_method', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'transactions.invoice_no as order_no')
            ->whereDate('transactions.transaction_date', '>=', $previous_start_date)
            // ->whereDate('transactions.transaction_date', '<=', $previous_end_date)
            ->orWhere('transaction_payments.method', 'cash')
            ->orWhere('transaction_payments.method', 'cheque')
            ->orWhere('transaction_payments.method', 'card')
            ->orderBy('id', 'DESC')
            ->first();

        $own_group = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')->select('transactions.id', 'transactions.transaction_date', 'transactions.final_total', 'transaction_payments.method as payment_method', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'transactions.invoice_no as order_no')->whereDate('transactions.transaction_date', '>=', $start_date)->whereDate('transactions.transaction_date', '<=', $end_date)->orWhere('transaction_payments.method', 'custom_pay_1')->orWhere('transaction_payments.method', 'custom_pay_2')->orderBy('id', 'DESC')->first();

        $previous_own_group = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')->select('transactions.id', 'transactions.transaction_date', 'transactions.final_total', 'transaction_payments.method as payment_method', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'transactions.invoice_no as order_no')->whereDate('transactions.transaction_date', '>=', $previous_start_date)->orWhere('transaction_payments.method', 'custom_pay_1')->orWhere('transaction_payments.method', 'custom_pay_2')->orderBy('id', 'DESC')->first();

        $credit_sales_transaction = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')->select('transactions.id', 'transactions.transaction_date', 'transactions.final_total', 'transaction_payments.method as payment_method', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'transactions.invoice_no as order_no')->whereDate('transactions.transaction_date', '>=', $start_date)->whereDate('transactions.transaction_date', '<=', $end_date)->where('transaction_payments.method', 'credit_sales')->orderBy('id', 'DESC')->first();

        $previous_credit_sales_transaction = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')->select('transactions.id', 'transactions.transaction_date', 'transactions.final_total', 'transaction_payments.method as payment_method', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'transactions.invoice_no as order_no')->whereDate('transactions.transaction_date', '>=', $previous_start_date)->where('transaction_payments.method', 'credit_sales')->orderBy('id', 'DESC')->first();

        $account_transactions = AccountTransaction::join('transactions', 'transactions.id', 'account_transactions.transaction_id')->whereDate('transactions.transaction_date', '>=', $start_date)->whereDate('transactions.transaction_date', '<=', $end_date)->where('account_transactions.business_id', $business_id)->get();

        $opening_stock = AccountTransaction::join('transactions', 'transactions.id', 'account_transactions.transaction_id')
            //->whereDate('transactions.transaction_date', '>=', $start_date)
            //->whereDate('transactions.transaction_date', '<=', $end_date)
            ->where('account_transactions.business_id', $business_id)
            ->where('transactions.type', 'opening_stock')
            ->where('transactions.status', 'final')
            ->sum('account_transactions.amount');
        // ->get();

        $today = AccountTransaction::join('transactions', 'transactions.id', 'account_transactions.transaction_id')->whereDate('transactions.transaction_date', '=', Carbon::now())->where('account_transactions.business_id', $business_id)->sum('account_transactions.amount');
        // ->get();
        $previous_day = AccountTransaction::join('transactions', 'transactions.id', 'account_transactions.transaction_id')
            ->whereDate('transactions.transaction_date', '=', Carbon::now()->subDays(1))
            ->where('account_transactions.business_id', $business_id)
            ->sum('account_transactions.amount');
        // ->get();
        $incomeGrp_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')->where('accounts.business_id', $business_id)->where('account_groups.name', 'Sales Income Group')->select('accounts.id')->get()->pluck('id');
        $cash_sales_today = AccountTransaction::whereDate('account_transactions.operation_date', '=', Carbon::now())->join('transactions', 'transactions.id', '=', 'account_transactions.transaction_id')->where('account_transactions.business_id', $business_id)->where('account_transactions.type', 'debit')->get()->sum('amount');
        $credit_sales_today = AccountTransaction::whereDate('account_transactions.operation_date', '=', Carbon::now())->join('transactions', 'transactions.id', '=', 'account_transactions.transaction_id')->where('account_transactions.business_id', $business_id)->where('account_transactions.type', 'credit')->get()->sum('amount');

        return [
            'credit_sales' => $credit_sales,
            'previous_credit_sales' => $previous_credit_sales,
            'form22_details' => $form22_details,
            'form17_increase' => $form17_increase,
            'form17_decrease' => $form17_decrease,
            'transaction' => $transaction,
            'own_group' => $own_group,
            'credit_sales_transaction' => $credit_sales_transaction,
            'previous_transaction' => $previous_transaction,
            'previous_own_group' => $previous_own_group,
            'previous_credit_sales_transaction' => $previous_credit_sales_transaction,
            'form17_increase_previous' => $form17_increase_previous,
            'form17_decrease_previous' => $form17_decrease_previous,
            'merged_sub_categories' => $merged_sub_categories,
            'account_transactions' => $account_transactions,
            'opening_stock' => $opening_stock,
            'previous_day' => (int) $previous_day,
            'today' => (int) $today,
            'cash_sales_today' => (int) $cash_sales_today,
            'credit_sales_today' => (int) $credit_sales_today,
        ];
    }

    public function Form21CQuery($business_id, $start_date, $end_date, $location_id)
    {
        $query = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
            ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->leftjoin('business', 'transactions.business_id', 'business.id')
            ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.is_credit_sale', 1)
            ->whereDate('transactions.transaction_date', '>=', $start_date)
            ->whereDate('transactions.transaction_date', '<=', $end_date)
            ->select('transactions.transaction_date', 'transactions.final_total', 'products.name as description', 'products.sub_category_id', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'contacts.name as customer', 'transactions.invoice_no as order_no', 'business.name as comapany', 'business_locations.mobile as tel');
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        $credit_sales = $query->get();

        return $credit_sales;
    }
    //
    public function Form21CQueryReceipts($business_id, $start_date, $end_date, $location_id)
    {
        $query = Transaction::leftjoin('purchase_lines', 'transactions.id', '=', 'purchase_lines.transaction_id')
            ->leftjoin('products', 'purchase_lines.product_id', 'products.id')
            ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->leftjoin('business', 'transactions.business_id', 'business.id')
            ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
            ->where('transactions.type', 'purchase')

            ->whereDate('transactions.transaction_date', '>=', $start_date)
            ->whereDate('transactions.transaction_date', '<=', $end_date)
            ->select('transactions.id', 'transactions.transaction_date', 'transactions.final_total', 'products.name as description', 'products.sub_category_id', 'purchase_lines.quantity', 'purchase_lines.purchase_price', 'transactions.ref_no', 'transactions.invoice_no', 'contacts.name as customer', 'transactions.invoice_no as order_no', 'business.name as comapany', 'business_locations.mobile as tel');
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        $receipts = $query->get();

        return $receipts;
    }

    public function Form21CQuerytoday($business_id, $start_date, $end_date, $location_id)
    {
        $query = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
            ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->leftjoin('business', 'transactions.business_id', 'business.id')
            ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
            ->leftjoin('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
            ->where('account_transactions.business_id', $business_id)
            ->whereDate('transactions.transaction_date', '=', Carbon::now())
            ->select('transactions.transaction_date', 'transactions.final_total', 'products.name as description', 'products.sub_category_id', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'contacts.name as customer', 'transactions.invoice_no as order_no', 'business.name as comapany', 'business_locations.mobile as tel');
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        $todays = $query->get();

        return $todays;
    }
    //Form21CQuerypreviousday
    public function Form21CQuerypreviousday($business_id, $start_date, $end_date, $location_id)
    {
        $query = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
            ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->leftjoin('business', 'transactions.business_id', 'business.id')
            ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
            ->leftjoin('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
            ->where('account_transactions.business_id', $business_id)
            ->whereDate('transactions.transaction_date', '>=', $start_date)
            ->whereDate('transactions.transaction_date', '<=', $end_date)
            ->select('transactions.transaction_date', 'transactions.final_total', 'products.name as description', 'products.sub_category_id', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'contacts.name as customer', 'transactions.invoice_no as order_no', 'business.name as comapany', 'business_locations.mobile as tel');
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        $todays = $query->get();

        return $todays;
    }
    //opening stock
    public function Form21CQueryOpeningStock($business_id, $start_date, $end_date, $location_id)
    {
        $opening_stock = Transaction::leftjoin('purchase_lines', 'transactions.id', 'purchase_lines.transaction_id')->leftjoin('products', 'purchase_lines.product_id', 'products.id')->where('transactions.business_id', $business_id)->where('transactions.type', 'opening_stock')->select('purchase_lines.quantity', 'purchase_lines.purchase_price', 'products.sub_category_id')->get();

        return $opening_stock;
    }
    public function Form21CQueryTotalReceipts($business_id, $start_date, $end_date, $location_id)
    {
        $query = Transaction::leftjoin('purchase_lines', 'transactions.id', '=', 'purchase_lines.transaction_id')
            ->leftjoin('products', 'purchase_lines.product_id', 'products.id')
            ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->leftjoin('business', 'transactions.business_id', 'business.id')
            ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
            ->where('transactions.type', 'purchase')

            ->select('transactions.id', 'transactions.transaction_date', 'transactions.final_total', 'products.name as description', 'products.sub_category_id', 'purchase_lines.quantity', 'purchase_lines.purchase_price', 'transactions.ref_no', 'transactions.invoice_no', 'contacts.name as customer', 'transactions.invoice_no as order_no', 'business.name as comapany', 'business_locations.mobile as tel');
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        $total_receipts = $query->get();

        return $total_receipts;
    }
    //Form21CQueryIssue
    public function Form21CQueryIssue($business_id, $start_date, $end_date, $location_id)
    {
        $query = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
            ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->leftjoin('business', 'transactions.business_id', 'business.id')
            ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
            ->leftjoin('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
            ->where('account_transactions.business_id', $business_id)
            ->where('account_transactions.type', 'debit')
            ->whereDate('transactions.transaction_date', '=', Carbon::now())
            ->select('transactions.transaction_date', 'transactions.final_total', 'products.name as description', 'products.sub_category_id', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'contacts.name as customer', 'transactions.invoice_no as order_no', 'business.name as comapany', 'business_locations.mobile as tel');
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        $issuess = $query->get();

        return $issuess;
    }

    public function Form21CQuerycashtoday($business_id, $start_date, $end_date, $location_id)
    {
        $query = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
            ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->leftjoin('business', 'transactions.business_id', 'business.id')
            ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
            ->leftjoin('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
            ->where('account_transactions.business_id', $business_id)
            ->where('account_transactions.type', 'debit')
            ->whereNull('transactions.customer_group_id')
            ->whereDate('transactions.transaction_date', '=', Carbon::now())
            ->select('transactions.transaction_date', 'transactions.final_total', 'products.name as description', 'products.sub_category_id', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'contacts.name as customer', 'transactions.invoice_no as order_no', 'business.name as comapany', 'business_locations.mobile as tel');
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        $cash_sales_todays = $query->get();

        return $cash_sales_todays;
    }
    public function Form21CQueryCorporate($business_id, $start_date, $end_date, $location_id)
    {
        $query = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
            ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->leftjoin('contact_groups', 'transactions.customer_group_id', 'contact_groups.id')
            ->leftjoin('business', 'transactions.business_id', 'business.id')
            ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
            ->leftjoin('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
            ->where('account_transactions.business_id', $business_id)
            ->whereDate('transactions.transaction_date', '=', Carbon::now())
            ->select('transactions.transaction_date', 'transactions.final_total', 'products.name as description', 'products.sub_category_id', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'contacts.name as customer', 'transactions.invoice_no as order_no', 'business.name as comapany', 'business_locations.mobile as tel');
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        $corporate = $query->get();

        return $corporate;
    }
    public function Form21CQueryTotalIssue($business_id, $start_date, $end_date, $location_id)
    {
        $query = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
            ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->leftjoin('business', 'transactions.business_id', 'business.id')
            ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
            ->leftjoin('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
            ->where('account_transactions.business_id', $business_id)
            ->where(function ($query) {
                $query->where('account_transactions.type', 'debit')->orWhere('account_transactions.type', 'credit');
            })
            ->whereDate('transactions.transaction_date', '=', Carbon::now())
            ->select('transactions.transaction_date', 'transactions.final_total', 'products.name as description', 'products.sub_category_id', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'contacts.name as customer', 'transactions.invoice_no as order_no', 'business.name as comapany', 'business_locations.mobile as tel');
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        $totalissuess = $query->get();

        return $totalissuess;
    }
    public function Form21CQueryTotalIssueLastday($business_id, $start_date, $end_date, $location_id)
    {
        $query = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
            ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->leftjoin('business', 'transactions.business_id', 'business.id')
            ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
            ->leftjoin('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
            ->where('account_transactions.business_id', $business_id)
            ->where(function ($query) {
                $query->where('account_transactions.type', 'debit')->orWhere('account_transactions.type', 'credit');
            })
            ->whereDate('transactions.transaction_date', '>=', $start_date)
            ->whereDate('transactions.transaction_date', '<=', $end_date)
            ->select('transactions.transaction_date', 'transactions.final_total', 'products.name as description', 'products.sub_category_id', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'contacts.name as customer', 'transactions.invoice_no as order_no', 'business.name as comapany', 'business_locations.mobile as tel');
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        $totalissuesslastday = $query->get();

        return $totalissuesslastday;
    }
    public function Form21CQueryTotalIssueOne($business_id, $start_date, $end_date, $location_id)
    {
        $query = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
            ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->leftjoin('business', 'transactions.business_id', 'business.id')
            ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
            ->leftjoin('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
            ->where('account_transactions.business_id', $business_id)
            ->where(function ($query) {
                $query->where('account_transactions.type', 'debit')->orWhere('account_transactions.type', 'credit');
            })
            ->whereDate('transactions.transaction_date', '>=', $start_date)
            ->whereDate('transactions.transaction_date', '<=', Carbon::now())
            ->select('transactions.transaction_date', 'transactions.final_total', 'products.name as description', 'products.sub_category_id', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'contacts.name as customer', 'transactions.invoice_no as order_no', 'business.name as comapany', 'business_locations.mobile as tel');
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        $totalissuesslastday = $query->get();

        return $totalissuesslastday;
    }
    //Form21CQueryDiscountToday
    public function Form21CQueryDiscountToday($business_id, $start_date, $end_date, $location_id)
    {
        $query = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
            ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->leftjoin('business', 'transactions.business_id', 'business.id')
            ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
            ->leftjoin('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
            ->where('account_transactions.business_id', $business_id)
            ->whereDate('transactions.transaction_date', '=', Carbon::now())
            ->select('transactions.transaction_date', 'transactions.final_total', 'transactions.discount_amount', 'products.name as description', 'products.sub_category_id', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'contacts.name as customer', 'transactions.invoice_no as order_no', 'business.name as comapany', 'business_locations.mobile as tel');
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        $discount_todays = $query->get();

        return $discount_todays;
    }
    public function Form21CQueryDiscountPrevious($business_id, $prevstart_date, $prevend_date, $location_id)
    {
        $query = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
            ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->leftjoin('business', 'transactions.business_id', 'business.id')
            ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
            ->leftjoin('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
            ->where('account_transactions.business_id', $business_id)
            ->whereDate('transactions.transaction_date', '>=', $prevstart_date)
            ->whereDate('transactions.transaction_date', '<=', $prevend_date)
            ->select('transactions.transaction_date', 'transactions.final_total', 'transactions.discount_amount', 'products.name as description', 'products.sub_category_id', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'contacts.name as customer', 'transactions.invoice_no as order_no', 'business.name as comapany', 'business_locations.mobile as tel');
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        $discount_previouss = $query->get();

        return $discount_previouss;
    }
    public function Form21CQueryDiscountTotal($business_id, $start_date, $end_date, $location_id)
    {
        $query = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
            ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->leftjoin('business', 'transactions.business_id', 'business.id')
            ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
            ->leftjoin('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
            ->where('account_transactions.business_id', $business_id)
            ->whereDate('transactions.transaction_date', '>=', $start_date)
            ->whereDate('transactions.transaction_date', '<=', Carbon::now())
            ->select('transactions.transaction_date', 'transactions.final_total', 'transactions.discount_amount', 'products.name as description', 'products.sub_category_id', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'contacts.name as customer', 'transactions.invoice_no as order_no', 'business.name as comapany', 'business_locations.mobile as tel');
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        $discount_totals = $query->get();

        return $discount_totals;
    }
    //balance
    public function Form21CQueryBalance($business_id, $start_date, $end_date, $location_id)
    {
        $query = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
            ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->leftjoin('business', 'transactions.business_id', 'business.id')
            ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
            ->leftjoin('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
            ->whereDate('transactions.transaction_date', '>=', $start_date)
            ->whereDate('transactions.transaction_date', '<=', Carbon::now())
            ->select('transactions.transaction_date', 'transactions.final_total', 'transactions.discount_amount', 'products.name as description', 'products.sub_category_id', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'contacts.name as customer', 'transactions.invoice_no as order_no', 'business.name as comapany', 'business_locations.mobile as tel');
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        $balances = $query->get();

        return $balances;
    }
    //subtotal
    public function Form21CQuerySubtotal($business_id, $start_date, $end_date, $location_id)
    {
        $query = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
            ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->leftjoin('business', 'transactions.business_id', 'business.id')
            ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
            ->leftjoin('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
            ->whereDate('transactions.transaction_date', '>=', $start_date)
            ->whereDate('transactions.transaction_date', '<=', Carbon::now())
            ->select('transactions.transaction_date', 'transactions.final_total', 'transactions.discount_amount', 'products.name as description', 'products.sub_category_id', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'contacts.name as customer', 'transactions.invoice_no as order_no', 'business.name as comapany', 'business_locations.mobile as tel');
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        $subtotal = $query->get();

        return $subtotal;
    }
    public function Form21CQueryQuantityIssue($business_id, $start_date, $end_date, $location_id)
    {
        $query = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')->leftjoin('business', 'transactions.business_id', 'business.id')->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')->leftjoin('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')->whereDate('transactions.transaction_date', '=', Carbon::now())->select('transactions.transaction_date', 'transactions.final_total', 'transactions.discount_amount', 'products.name as description', 'products.sub_category_id', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'contacts.name as customer', 'transactions.invoice_no as order_no', 'business.name as comapany', 'business_locations.mobile as tel');
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        $subtotal = $query->get();

        return $subtotal;
    }
    public function Form21CQueryPumperLast($business_id, $prevstart_date, $prevend_date, $location_id)
    {
        $query = Pump::leftjoin('products', 'pumps.product_id', 'products.id')
            ->leftjoin('business_locations', 'pumps.location_id', 'business_locations.id')
            ->leftjoin('fuel_tanks', 'pumps.fuel_tank_id', 'fuel_tanks.id')
            ->where('pumps.business_id', $business_id)
            ->select(['pumps.*', 'products.sub_category_id', 'fuel_tanks.fuel_tank_number', 'products.name as product_name', 'business_locations.name as location_name']);
        $pumper_lasts = $query->get();

        return $pumper_lasts;
    }
    public function get_21_c_form_all_querys(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        $merged_sub_categories = MergedSubCategory::where('business_id', $business_id)->get();

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $location_id = $request->location_id;

        $settings = MpcsFormSetting::where('business_id', $business_id)->first();
        $F21C_form_tdate = $settings->F21C_form_tdate;
        $previous_start_date = Carbon::parse($request->start_date)->subDays(1)->format('Y-m-d');
        $previous_end_date = Carbon::parse($request->end_date)->subDays(1)->format('Y-m-d');
        $startDate = Carbon::createFromFormat('Y-m-d', $start_date);
        $endDate = Carbon::createFromFormat('Y-m-d', $end_date);

        $credit_sales = $this->Form21CQuery($business_id, $start_date, $end_date, $location_id);
        $previous_credit_sales = $this->Form21CQuery($business_id, $previous_end_date, $previous_end_date, $location_id);
        $form22_details = FormF22Detail::where('business_id', $business_id)->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->orderBy('id', 'DESC')->first();
        $form17_increase = FormF17Detail::where('select_mode', 'increase')->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->orderBy('id', 'DESC')->first();

        $form17_increase_previous = FormF17Detail::where('select_mode', 'increase')->whereDate('created_at', '>=', $previous_start_date)->orderBy('id', 'DESC')->first();

        $form17_decrease = FormF17Detail::where('select_mode', 'descrease')->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->orderBy('id', 'DESC')->first();

        $form17_decrease_previous = FormF17Detail::where('select_mode', 'decrease')
            ->whereDate('created_at', '>=', $previous_start_date)
            // ->whereDate('created_at', '<=', $previous_end_date)
            ->orderBy('id', 'DESC')
            ->first();

        $transaction = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')->select('transactions.id', 'transactions.transaction_date', 'transactions.final_total', 'transaction_payments.method as payment_method', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'transactions.invoice_no as order_no')->whereDate('transactions.transaction_date', '>=', $start_date)->whereDate('transactions.transaction_date', '<=', $end_date)->orWhere('transaction_payments.method', 'cash')->orWhere('transaction_payments.method', 'cheque')->orWhere('transaction_payments.method', 'card')->orderBy('id', 'DESC')->first();

        $previous_transaction = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->select('transactions.id', 'transactions.transaction_date', 'transactions.final_total', 'transaction_payments.method as payment_method', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'transactions.invoice_no as order_no')
            ->whereDate('transactions.transaction_date', '>=', $previous_start_date)
            // ->whereDate('transactions.transaction_date', '<=', $previous_end_date)
            ->orWhere('transaction_payments.method', 'cash')
            ->orWhere('transaction_payments.method', 'cheque')
            ->orWhere('transaction_payments.method', 'card')
            ->orderBy('id', 'DESC')
            ->first();

        $own_group = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')->select('transactions.id', 'transactions.transaction_date', 'transactions.final_total', 'transaction_payments.method as payment_method', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'transactions.invoice_no as order_no')->whereDate('transactions.transaction_date', '>=', $start_date)->whereDate('transactions.transaction_date', '<=', $end_date)->orWhere('transaction_payments.method', 'custom_pay_1')->orWhere('transaction_payments.method', 'custom_pay_2')->orderBy('id', 'DESC')->first();

        $previous_own_group = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')->select('transactions.id', 'transactions.transaction_date', 'transactions.final_total', 'transaction_payments.method as payment_method', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'transactions.invoice_no as order_no')->whereDate('transactions.transaction_date', '>=', $previous_start_date)->orWhere('transaction_payments.method', 'custom_pay_1')->orWhere('transaction_payments.method', 'custom_pay_2')->orderBy('id', 'DESC')->first();

        $credit_sales_transaction = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')->select('transactions.id', 'transactions.transaction_date', 'transactions.final_total', 'transaction_payments.method as payment_method', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'transactions.invoice_no as order_no')->whereDate('transactions.transaction_date', '>=', $start_date)->whereDate('transactions.transaction_date', '<=', $end_date)->where('transaction_payments.method', 'credit_sales')->orderBy('id', 'DESC')->first();

        $previous_credit_sales_transaction = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')->select('transactions.id', 'transactions.transaction_date', 'transactions.final_total', 'transaction_payments.method as payment_method', 'transaction_sell_lines.quantity', 'transaction_sell_lines.unit_price', 'transactions.ref_no', 'transactions.invoice_no', 'transactions.invoice_no as order_no')->whereDate('transactions.transaction_date', '>=', $previous_start_date)->where('transaction_payments.method', 'credit_sales')->orderBy('id', 'DESC')->first();

        $account_transactions = AccountTransaction::join('transactions', 'transactions.id', 'account_transactions.transaction_id')->whereDate('transactions.transaction_date', '>=', $start_date)->whereDate('transactions.transaction_date', '<=', $end_date)->where('account_transactions.business_id', $business_id)->get();

        $opening_stock = AccountTransaction::join('transactions', 'transactions.id', 'account_transactions.transaction_id')
            //->whereDate('transactions.transaction_date', '>=', $start_date)
            //->whereDate('transactions.transaction_date', '<=', $end_date)
            ->where('account_transactions.business_id', $business_id)
            ->where('transactions.type', 'opening_stock')
            ->where('transactions.status', 'final')
            ->sum('account_transactions.amount');
        // ->get();

        $today = AccountTransaction::join('transactions', 'transactions.id', 'account_transactions.transaction_id')->whereDate('transactions.transaction_date', '=', Carbon::now())->where('account_transactions.business_id', $business_id)->sum('account_transactions.amount');
        // ->get();
        $previous_day = AccountTransaction::join('transactions', 'transactions.id', 'account_transactions.transaction_id')
            ->whereDate('transactions.transaction_date', '=', Carbon::now()->subDays(1))
            ->where('account_transactions.business_id', $business_id)
            ->sum('account_transactions.amount');
        // ->get();
        $incomeGrp_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')->where('accounts.business_id', $business_id)->where('account_groups.name', 'Sales Income Group')->select('accounts.id')->get()->pluck('id');
        $cash_sales_today = AccountTransaction::whereDate('account_transactions.operation_date', '=', Carbon::now())->join('transactions', 'transactions.id', '=', 'account_transactions.transaction_id')->where('account_transactions.business_id', $business_id)->where('account_transactions.type', 'debit')->get()->sum('amount');
        $credit_sales_today = AccountTransaction::whereDate('account_transactions.operation_date', '=', Carbon::now())->join('transactions', 'transactions.id', '=', 'account_transactions.trfansaction_id')->where('account_transactions.business_id', $business_id)->where('account_transactions.type', 'credit')->get()->sum('amount');

        return [
            'credit_sales' => $credit_sales,
            'previous_credit_sales' => $previous_credit_sales,
            'form22_details' => $form22_details,
            'form17_increase' => $form17_increase,
            'form17_decrease' => $form17_decrease,
            'transaction' => $transaction,
            'own_group' => $own_group,
            'credit_sales_transaction' => $credit_sales_transaction,
            'previous_transaction' => $previous_transaction,
            'previous_own_group' => $previous_own_group,
            'previous_credit_sales_transaction' => $previous_credit_sales_transaction,
            'form17_increase_previous' => $form17_increase_previous,
            'form17_decrease_previous' => $form17_decrease_previous,
            'merged_sub_categories' => $merged_sub_categories,
            'account_transactions' => $account_transactions,
            'opening_stock' => $opening_stock,
            'previous_day' => (int) $previous_day,
            'today' => (int) $today,
            'cash_sales_today' => (int) $cash_sales_today,
            'credit_sales_today' => (int) $credit_sales_today,
        ];
    }
}
