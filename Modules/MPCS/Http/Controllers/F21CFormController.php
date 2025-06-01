<?php

namespace Modules\MPCS\Http\Controllers;
use App\Account;
use App\Brands;
use App\Business;
use App\BusinessLocation;
use App\Category;
use App\Product;
use App\Store;
use App\Unit;
use Modules\Petro\Entities\Pump;
use App\AccountTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\MPCS\Entities\MpcsFormSetting;
use Yajra\DataTables\Facades\DataTables;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Modules\Petro\Entities\MeterSale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\MPCS\Entities\FormF16Detail;
use Modules\MPCS\Entities\FormF17Detail;
use Modules\MPCS\Entities\FormF17Header;
use Modules\MPCS\Entities\FormF17HeaderController;
use Modules\MPCS\Entities\FormF22Header;
use Modules\MPCS\Entities\FormF22Detail;
use App\Contact;
use App\Transaction;
use App\MergedSubCategory;
use Modules\MPCS\Entities\Mpcs21cFormSettings;


class F21CFormController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $transactionUtil;
    protected $productUtil;
    protected $moduleUtil;
    protected $util;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, Util $util)
    {
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->util = $util;
    }


    /**
     * Display a listing of the resource.
     * @return Response
     */
   
    
    
    public function index(Request $request)
{
    
        if (!auth()->check()) {
            return redirect()->route('login');
        } 
       
        $business_id = request()->session()->get('business.id');
        $merged_sub_categories = MergedSubCategory::where('business_id', $business_id)->get();
        $business_locations = BusinessLocation::forDropdown($business_id);
        $fuelCategoryId = Category::where('name', 'Fuel')->value('id');
        if(auth()->user()->can('user')) {
            $fuelCategory = Category::where('parent_id', $fuelCategoryId)
            ->select(['name', 'id'])
            ->get()->pluck('name', 'id');
        } else {
            $fuelCategory = Category::where('business_id', $business_id)
            ->where('parent_id', $fuelCategoryId)
            ->select(['name', 'id'])
            ->get()->pluck('name', 'id');
        }
           $business_details = Business::find($business_id);
            $currency_precision = (int) $business_details->currency_precision;
            $qty_precision = (int) $business_details->quantity_precision;
      
       
          $sub_categories = Category::where('business_id', $business_id)->where('parent_id', '!=', 0)->get();
          $settings = MpcsFormSetting::where('business_id', $business_id)->first();
       
          if (!empty($settings)) {
            // Get today's date and the starting day from settings (assumes the field 'date' is the starting day)
            $current_date = Carbon::today();
            $starting_day = Carbon::parse($settings->date);
            $days_passed = $starting_day->diffInDays($current_date);
            // If no days have passed yet, use the starting number
            if ($days_passed === 0) {
                $F21c_from_no = $settings->starting_number;
            } else {
                // Otherwise, increment the form number based on the number of days passed
                // Add the number of days passed to the starting number
                $F21c_from_no = $settings->starting_number + $days_passed;
            }
        } else {
            $F21c_from_no = '';
        }
        $layout = 'layouts.app';
        return view('mpcs::forms.21CForm.F21_form')->with(compact(
            
           'F21c_from_no',
            'sub_categories',
            'currency_precision',
            'qty_precision',
            'merged_sub_categories',
             'business_locations',
            'layout',
            'fuelCategory'
            ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
        public function get21CForms(Request $request)
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

            return view('mpcs::forms.partials.9c_details_section')->with(compact(
                'credit_sales',
                'sub_categories',
                'start_date',
                'end_date',
                'location',
                'F9C_sn'
            ));
        }
    }
    
     public function get_21_c_form_all_query(Request $request)
    {
       
        $business_id = request()->session()->get('business.id');
        $merged_sub_categories = MergedSubCategory::where('business_id', $business_id)->get();
        $fuelCategoryId = Category::where('name', 'Fuel')->value('id');
        $business_details = Business::find($business_id);
        $currency_precision = (int) $business_details->currency_precision;
        $qty_precision = (int) $business_details->quantity_precision;
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $location_id = $request->location_id;
            $today_sales=[];
            $pump_operator =[];
            $discount_previous =[];
            $discount_todays =[];
            $cash_sales_previous = [];
            $credit_sales_today = [];
            $cash_sales_today = [];
            $settings = MpcsFormSetting::where('business_id', $business_id)->first();
            $F21C_form_tdate = $settings->F21C_form_tdate;
            $previous_start_date = Carbon::parse($request->start_date)->subDays(1)->format('Y-m-d');
            $previous_end_date = Carbon::parse($request->end_date)->subDays(1)->format('Y-m-d');
            $startDate = Carbon::createFromFormat('Y-m-d', $start_date);
            $endDate = Carbon::createFromFormat('Y-m-d', $end_date);
            $meter_end_date = Carbon::parse($request->end_date)->addDays(1)->format('Y-m-d');
            //today
            $start_today_date = Carbon::parse($request->start_date)->format('Y-m-d');//Carbon::now();
            $end_today_date =Carbon::parse($request->end_date)->format('Y-m-d');//Carbon::now();

            $credit_sales = $this->Form21CQuery($business_id, $start_date, $end_date, $location_id);
            $previous_credit_sales = $this->Form21CQuery($business_id, $previous_end_date, $previous_end_date, $location_id);
            $form22_details = FormF22Detail::where('business_id', $business_id)
                                ->whereDate('created_at', '>=', $startDate)
                                ->whereDate('created_at', '<=', $endDate) 
                                ->orderBy('id', 'DESC')               
                                ->first();
            $form17_increase = FormF17Detail::where('select_mode', 'increase')
                                ->whereDate('created_at', '>=', $startDate)
                                ->whereDate('created_at', '<=', $endDate) 
                                ->orderBy('id', 'DESC')               
                                ->first();

            $form17_increase_previous = FormF17Detail::where('select_mode', 'increase')
                                ->whereDate('created_at', '>=', $previous_start_date) 
                                ->orderBy('id', 'DESC')               
                                ->first();

            $form17_decrease = FormF17Detail::where('select_mode', 'descrease')
                                ->whereDate('created_at', '>=', $startDate)
                                ->whereDate('created_at', '<=', $endDate) 
                                ->orderBy('id', 'DESC')               
                                ->first();
                                
            $form17_decrease_previous = FormF17Detail::where('select_mode', 'decrease')
                                ->whereDate('created_at', '>=', $previous_start_date)
                                // ->whereDate('created_at', '<=', $previous_end_date) 
                                ->orderBy('id', 'DESC')               
                                ->first();

            $transaction = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                            ->select(
                                'transactions.id',
                                'transactions.transaction_date',
                                'transactions.final_total',
                                'transaction_payments.method as payment_method',
                                'transaction_sell_lines.quantity',
                                'transaction_sell_lines.unit_price',
                                'transactions.ref_no',
                                'transactions.invoice_no',
                                'transactions.invoice_no as order_no'
                            )
                            ->whereDate('transactions.transaction_date', '>=', $start_date)
                            ->whereDate('transactions.transaction_date', '<=', $end_date)
                            ->orWhere('transaction_payments.method', 'cash')
                            ->orWhere('transaction_payments.method', 'cheque')
                            ->orWhere('transaction_payments.method', 'card')
                            ->orderBy('id', 'DESC')               
                            ->first();

            $previous_transaction = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                            ->select(
                                'transactions.id',
                                'transactions.transaction_date',
                                'transactions.final_total',
                                'transaction_payments.method as payment_method',
                                'transaction_sell_lines.quantity',
                                'transaction_sell_lines.unit_price',
                                'transactions.ref_no',
                                'transactions.invoice_no',
                                'transactions.invoice_no as order_no'
                            )
                            ->whereDate('transactions.transaction_date', '>=', $previous_start_date)
                            // ->whereDate('transactions.transaction_date', '<=', $previous_end_date)
                            ->orWhere('transaction_payments.method', 'cash')
                            ->orWhere('transaction_payments.method', 'cheque')
                            ->orWhere('transaction_payments.method', 'card')
                            ->orderBy('id', 'DESC')               
                            ->first();

            $own_group = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                            ->select(
                                'transactions.id',
                                'transactions.transaction_date',
                                'transactions.final_total',
                                'transaction_payments.method as payment_method',
                                'transaction_sell_lines.quantity',
                                'transaction_sell_lines.unit_price',
                                'transactions.ref_no',
                                'transactions.invoice_no',
                                'transactions.invoice_no as order_no'
                            )
                            ->whereDate('transactions.transaction_date', '>=', $start_date)
                            ->whereDate('transactions.transaction_date', '<=', $end_date)
                            ->orWhere('transaction_payments.method', 'custom_pay_1')
                            ->orWhere('transaction_payments.method', 'custom_pay_2')
                            ->orderBy('id', 'DESC')               
                            ->first();

            $previous_own_group = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                            ->select(
                                'transactions.id',
                                'transactions.transaction_date',
                                'transactions.final_total',
                                'transaction_payments.method as payment_method',
                                'transaction_sell_lines.quantity',
                                'transaction_sell_lines.unit_price',
                                'transactions.ref_no',
                                'transactions.invoice_no',
                                'transactions.invoice_no as order_no'
                            )
                            ->whereDate('transactions.transaction_date', '>=', $previous_start_date)
                            ->orWhere('transaction_payments.method', 'custom_pay_1')
                            ->orWhere('transaction_payments.method', 'custom_pay_2')
                            ->orderBy('id', 'DESC')               
                            ->first();

            $credit_sales_transaction = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                            ->select(
                                'transactions.id',
                                'transactions.transaction_date',
                                'transactions.final_total',
                                'transaction_payments.method as payment_method',
                                'transaction_sell_lines.quantity',
                                'transaction_sell_lines.unit_price',
                                'transactions.ref_no',
                                'transactions.invoice_no',
                                'transactions.invoice_no as order_no'
                            )
                            ->whereDate('transactions.transaction_date', '>=', $start_date)
                            ->whereDate('transactions.transaction_date', '<=', $end_date)
                            ->where('transaction_payments.method', 'credit_sales')
                            ->orderBy('id', 'DESC')               
                            ->first();

            $previous_credit_sales_transaction = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                            ->select(
                                'transactions.id',
                                'transactions.transaction_date',
                                'transactions.final_total',
                                'transaction_payments.method as payment_method',
                                'transaction_sell_lines.quantity',
                                'transaction_sell_lines.unit_price',
                                'transactions.ref_no',
                                'transactions.invoice_no',
                                'transactions.invoice_no as order_no'
                            )
                            ->whereDate('transactions.transaction_date', '>=', $previous_start_date)
                            ->where('transaction_payments.method', 'credit_sales')
                            ->orderBy('id', 'DESC')               
                            ->first();

            $account_transactions = AccountTransaction::join('transactions','transactions.id','account_transactions.transaction_id')
                    ->whereDate('transactions.transaction_date', '>=', $start_date)
                    ->whereDate('transactions.transaction_date', '<=', $end_date)
                    ->where('account_transactions.business_id',$business_id)
                    ->get();
             
                if(auth()->user()->can('user')) {
                    $fuelCategory = Category::where('parent_id', $fuelCategoryId)
                    ->select(['name', 'id'])
                    ->get()->pluck('name', 'id');
                } else {
                    $fuelCategory = Category::where('business_id', $business_id)
                    ->where('parent_id', $fuelCategoryId)
                    ->select(['name', 'id'])
                    ->get()->pluck('name', 'id');
                }
               
            
             
                $sell_query_prev = DB::table('transactions')
                ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
                ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')
                ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
                ->join('categories', 'products.sub_category_id', '=', 'categories.id')
                ->where('transactions.type', 'sell')
                ->where('categories.parent_id', $fuelCategoryId)
                ->whereDate('transactions.transaction_date', '>=', $previous_start_date)
                ->whereDate('transactions.transaction_date', '<=', $previous_end_date)
                ->select(
                    'categories.id as category_id',
                    'categories.name as category_name',
                    DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity'),
                    DB::raw('SUM(transaction_payments.amount) as total_amount'),
                    DB::raw('"sell" as type')
                )
                ->groupBy('categories.id', 'categories.name');
            
            $purchase_query_prev  = DB::table('transactions')
                ->join('purchase_lines', 'transactions.id', '=', 'purchase_lines.transaction_id')
                ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')
                ->join('products', 'purchase_lines.product_id', '=', 'products.id')
                ->join('categories', 'products.sub_category_id', '=', 'categories.id')
                ->where('transactions.type', 'purchase')
                ->where('categories.parent_id', $fuelCategoryId)
                ->whereDate('transactions.transaction_date', '>=', $previous_start_date)
                ->whereDate('transactions.transaction_date', '<=', $previous_end_date)
                ->select(
                    'categories.id as category_id',
                    'categories.name as category_name',
                    DB::raw('SUM(purchase_lines.quantity) as total_quantity'),
                    DB::raw('SUM(transaction_payments.amount) as total_amount'),
                    DB::raw('"purchase" as type')
                )
                ->groupBy('categories.id', 'categories.name');
            
            $combined = $sell_query_prev ->unionAll($purchase_query_prev );
            
           


            
      

                $maxFormNos = DB::table('form_f22_details')        
           
                ->orderBy('form_no', 'desc') 
                ->select(                       
                    DB::raw('MAX(form_f22_details.form_no) as form_no')  
                )
                ->get();
                $formNo = optional($maxFormNos->first())->form_no;
   

        $header = Mpcs21cFormSettings::orderBy('created_at', 'desc')
            ->get();
        $header_prev = Mpcs21cFormSettings::whereDate('date', '>=', $previous_start_date)
            ->whereDate('date', '<=', $previous_end_date)
            ->orderBy('created_at', 'desc')
            ->get();
       $header_latest = Mpcs21cFormSettings::orderBy('created_at', 'desc')
    ->latest()
    ->value('date'); // Retrieve only the 'date' column value
        
        
        $latest = Mpcs21cFormSettings::orderBy('date', 'desc')->first();

        // if ($header->isEmpty()) {
          
        //     $starting_number = (int) $latest->starting_number;
        //     // Only generate starting number if latest exists AND latest date is <= today AND start_today_date < today
        //     if (
        //         $latest &&               
        //         Carbon::parse($start_today_date)->lt(Carbon::today() ) < Carbon::today()
        //     ) {
              
        
        //         $header = collect([
        //             (object)[
        //                 'starting_number' => $latest->starting_number,
        //                 'date' => $latest->date,
        //                 'manager_name' =>  $latest->starting_number,
        //                 'categories' => json_encode([]) // Avoid null error
        //             ]
        //         ]);
        //     } else {
        //         // If conditions not met, don't auto-generate starting number
        //         $header = collect([
        //             (object)[
        //                 'starting_number' =>  $latest->starting_number,
        //                 'date' => $latest->date,
        //                 'manager_name' => $latest->starting_number,
        //                 'categories' => json_encode([]) // Avoid null error
        //             ]
        //         ]);
        //     }
        // }
      
             
        
        
        $starting_number = $header->first()->starting_number ?? '';
      
        $headerCategoriesMap = [];

 
        $latestDatePlusOne = $latest ? Carbon::parse($latest->date)->addDay()->toDateString() : null;   
        $latestDate = $latest ? Carbon::parse($latest->date)->toDateString() : null;   
        $current_date = Carbon::parse($start_date);
        $starting_day = Carbon::parse($header->first()->date ?? '');
        $days_passed = $starting_day->diffInDays($current_date);
 
        foreach ($header  as $setting) {
            if (!isset($setting->categories)) {
                continue;
            }
        
            $categories = json_decode($setting->categories, true);
        
            if (!is_array($categories)) {
                continue;
            }
        
            foreach ($categories as $catId => $values) {
                if (!isset($headerCategoriesMap[$catId])) {
                    $headerCategoriesMap[$catId] = $values;
                }
            }
        }
        
        // Get fuel subcategories
        $fuelCategories = Category::where('business_id', $business_id)
            ->where('parent_id', $fuelCategoryId)
            ->select(['name', 'id'])
            ->get()
            ->pluck('name', 'id'); // [id => name]
        
        $finalCategoryData = [];
        
        foreach ($fuelCategories as $catId => $catName) {
            $categoryData = $headerCategoriesMap[$catId] ?? [];
        
            $finalCategoryData[] = [
                'category_id' => $catId,
                'category_name' => $catName,
                'previous_day' => [
                    'qty' => $categoryData['previous_day']['qty'] ?? '',
                    'val' => $categoryData['previous_day']['val'] ?? 0,
                ],
                'opening_stock' => [
                    'qty' => $categoryData['opening_stock']['qty'] ?? '',
                    'val' => $categoryData['opening_stock']['val'] ?? 0,
                ],
                'total_issues' => [
                    'qty' => $categoryData['total_issues']['qty'] ?? '',
                    'val' => $categoryData['total_issues']['val'] ?? 0,
                ],
            ];
        }
        
      
            // For opening_stock section
            
 
        if ((int) $days_passed === 0  )
        {
            
            
            
            // For opening_stock section
            $opening_stock = array_map(function ($item) {
                return [
                    'category_id' => $item['category_id'],
                    'category_name' => $item['category_name'],
                    'total_quantity' => $item['opening_stock']['qty'],
                    'total_sales' => $item['opening_stock']['val'],
                ];
            }, $finalCategoryData);
           
           // For total_issues section
            $total_issues = array_map(function ($item) {
                return [
                    'category_id' => $item['category_id'],
                    'category_name' => $item['category_name'],
                    'total_quantity' => $item['total_issues']['qty'],
                    'total_sales' => $item['total_issues']['val'],
                ];
            }, $finalCategoryData);



          
            // Use finalCategoryData to map previous_day
            $previous_day = array_map(function ($item) {
                return [
                    'category_id' => $item['category_id'],
                    'category_name' => $item['category_name'],
                    'total_quantity' => $item['previous_day']['qty'],
                    'total_sales' => $item['previous_day']['val'],
                ];
            }, $finalCategoryData);

//transaction
$cash_sales_today = DB::table('transactions')
       ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
       ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
       ->join('categories', 'products.sub_category_id', '=', 'categories.id')
      
       ->where('transactions.business_id', $business_id)
       ->where('transactions.type', 'sell')
       ->where('transactions.is_credit_sale', 0)
       ->where('categories.parent_id', $fuelCategoryId)
       ->whereDate('transactions.transaction_date', '>=', $start_today_date)
       ->whereDate('transactions.transaction_date', '<=', $end_today_date)
       ->groupBy('categories.id', 'categories.name')
       ->select(
           'categories.id as category_id',
           'categories.name as category_name',
           DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity'),
           DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price) as total_sales')
       )
       ->get();
   
  
       //dd($cash_sales_today);
           $credit_sales_today = DB::table('transactions')
           ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
           ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
           ->join('categories', 'products.sub_category_id', '=', 'categories.id')
            
           ->where('transactions.business_id', $business_id)
           ->where('transactions.type', 'sell')
       ->where('transactions.is_credit_sale', 1)
         
           ->where('categories.parent_id', $fuelCategoryId)
           ->whereDate('transactions.transaction_date', '>=',$start_date)
           ->whereDate('transactions.transaction_date', '<=', $end_date)
           ->groupBy('categories.id', 'categories.name')
           ->select(
               'categories.id as category_id',
               'categories.name as category_name',
               DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity'),
               DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price) as total_sales')
           )
           ->get();
          
           if ((int)$starting_number === 0) {
             
               $cash_sales_previous = DB::table('transactions')
               ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
               ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
               ->join('categories', 'products.sub_category_id', '=', 'categories.id')
               ->join('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
               ->where('account_transactions.business_id', $business_id)
                             
               ->where('categories.parent_id', $fuelCategoryId)
               ->whereDate('transactions.transaction_date', '>=', $previous_start_date)
               ->whereDate('transactions.transaction_date', '<=', $previous_end_date)
               ->groupBy('categories.id', 'categories.name')
               ->select(
                   'categories.id as category_id',
                   'categories.name as category_name',
                   DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity'),
                   DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price) as total_sales')
               )
               ->get();
           }
           else
           {   
                 // For total_issues section
               $cash_sales_previous = array_map(function ($item) {
                   return [
                       'category_id' => $item['category_id'],
                       'category_name' => $item['category_name'],
                       'total_quantity' => $item['total_issues']['qty'],
                       'total_sales' => $item['total_issues']['val'],
                   ];
               }, $finalCategoryData);

                
        
           }   
       
        
           
       
           $discount_todays = DB::table('transactions')
               ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
               ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
               ->join('categories', 'products.sub_category_id', '=', 'categories.id')
               ->join('contacts', 'transactions.contact_id', '=', 'contacts.id')
               ->join('business', 'transactions.business_id', '=', 'business.id')
               ->join('business_locations', 'transactions.location_id', '=', 'business_locations.id')
               ->join('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
               ->where('account_transactions.business_id', $business_id)
               ->where('categories.parent_id', $fuelCategoryId)
               ->whereDate('transactions.transaction_date', '>=',$start_date)
           ->whereDate('transactions.transaction_date', '<=', $end_date)
               ->groupBy('categories.id', 'categories.name')
               ->select(
                   'categories.id as category_id',
                   'categories.name as category_name',
                   DB::raw('SUM(transaction_sell_lines.quantity) as total_quantitys'),
                   DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price) as total_sale'),
                   DB::raw('SUM(transactions.discount_amount) as total_sales'),
                   DB::raw('SUM(transactions.final_total) as total_final_amount')
               )
               ->get();
               $discount_previous = DB::table('transactions')
               ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
               ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
               ->join('categories', 'products.sub_category_id', '=', 'categories.id')
               ->join('contacts', 'transactions.contact_id', '=', 'contacts.id')
               ->join('business', 'transactions.business_id', '=', 'business.id')
               ->join('business_locations', 'transactions.location_id', '=', 'business_locations.id')
               ->join('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
               ->where('account_transactions.business_id', $business_id)
               ->where('categories.parent_id', $fuelCategoryId)
               ->whereDate('transactions.transaction_date', '>=', $previous_start_date)
               ->whereDate('transactions.transaction_date', '<=', $previous_end_date)
               ->groupBy('categories.id', 'categories.name')
               ->select(
                   'categories.id as category_id',
                   'categories.name as category_name',
                   DB::raw('SUM(transaction_sell_lines.quantity) as total_quantityy'),
                   DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price) as total_sale'),
                   DB::raw('SUM(transactions.discount_amount) as total_sales'),
                   DB::raw('SUM(transactions.final_total) as total_final_amount')
               )
               ->get();

              
               
           
           

         // opening stock
         $formF22Exists = FormF22Header::whereDate('created_at', $start_date)->exists();
           
         if ($formF22Exists) {
           $opening_stock = DB::table('form_f22_headers')   
           ->join('form_f22_details', 'form_f22_headers.form_no', '=', 'form_f22_details.form_no')
           ->join('products', 'form_f22_details.product', '=', 'products.name')
           ->join('categories', 'products.sub_category_id', '=', 'categories.id')
           ->where('form_f22_details.business_id', $business_id)           
           ->where('categories.parent_id', $fuelCategoryId) 
           ->whereDate('form_date', '>=', $start_date)
           ->whereDate('form_date', '<=', $end_date)             
           ->groupBy('categories.id', 'categories.name')   
           ->orderBy('form_f22_headers.form_no', 'desc') // Optional: applies to full result set        
           ->select(
               'categories.id as category_id',
               'categories.name as category_name',
               DB::raw('SUM(form_f22_details.stock_count) as total_quantity'),
               DB::raw('SUM(form_f22_details.unit_sale_price) as total_sales'),                    
               DB::raw('MAX(form_f22_details.form_no) as form_no')
           )
           ->get();
         } 

        }
        elseif((int)$days_passed > 0 && $current_date > $starting_day )
        {
           
            //$start_date = Carbon::parse($header->first()->date)->addDay($days_passed);
            //$end_date = Carbon::parse($header->first()->date)->addDay($days_passed);
              // Use finalCategoryData to map previous_day
              $previous_day = array_map(function ($item) {
                return [
                    'category_id' => $item['category_id'],
                    'category_name' => $item['category_name'],
                    'total_quantity' => $item['previous_day']['qty'],
                    'total_sales' => $item['previous_day']['val'],
                ];
            }, $finalCategoryData);
              // For opening_stock section
              $opening_stock = array_map(function ($item) {
                return [
                    'category_id' => $item['category_id'],
                    'category_name' => $item['category_name'],
                    'total_quantity' =>'',
                    'total_sales' =>'',
                ];
            }, $finalCategoryData);

        // Combine today's sales/purchases with previous day's data
$merged_results = [];
$merged_results_prev = [];
$merged_results_prev_issue = [];


// Output the final result

    //today sales
       $sell_query = DB::table('transactions')
       ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
       ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')
       ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
       ->join('categories', 'products.sub_category_id', '=', 'categories.id')
       ->where('transactions.type', 'sell')
       ->where('transactions.is_credit_sale', 0)
       ->where('categories.parent_id', $fuelCategoryId)
       ->whereDate('transactions.transaction_date', '>=', $start_date)
       ->whereDate('transactions.transaction_date', '<=', $end_date)
       ->select(
           'categories.id as category_id',
           'categories.name as category_name',
           DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity'),
           DB::raw('SUM(transaction_payments.amount) as total_sales'),
           DB::raw('"sell" as type')
       )
       ->groupBy('categories.id', 'categories.name');
   
   $today_sales = DB::table('transactions')
       ->join('purchase_lines', 'transactions.id', '=', 'purchase_lines.transaction_id')
       ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')
       ->join('products', 'purchase_lines.product_id', '=', 'products.id')
       ->join('categories', 'products.sub_category_id', '=', 'categories.id')
       ->where('transactions.type', 'purchase')
       ->where('categories.parent_id', $fuelCategoryId)
       ->whereDate('transactions.transaction_date', '>=',$start_date)
       ->whereDate('transactions.transaction_date', '<=', $end_date)
       ->select(
           'categories.id as category_id',
           'categories.name as category_name',
           DB::raw('SUM(purchase_lines.quantity) as total_quantity'),
           DB::raw('SUM(transaction_payments.amount) as total_sales'),
           DB::raw('"purchase" as type')
       )
       ->groupBy('categories.id', 'categories.name')
       ->get();
  
// Merge $previous_day into the combined results
foreach ($previous_day as $item) {
    $category_id = $item['category_id'];
    if (isset($merged_results[$category_id])) {
        // If the category already exists, add the quantities and sales
        $merged_results[$category_id]['total_quantity'] += (int)$item['total_quantity'];
        $merged_results[$category_id]['total_sales'] += $item['total_sales'];
    } else {
        // If the category doesn't exist, initialize it
        $merged_results[$category_id] = [
            'category_id' => $item['category_id'],
            'category_name' => $item['category_name'],
            'total_quantity' => $item['total_quantity'],
            'total_sales' => $item['total_sales'],
            ];
        }
    }

    // Convert the merged results back to an array
    $total_receipts = array_values($merged_results);
 //dd($total_receipts);
//previously
 //today sales
 $sell_query_prev = DB::table('transactions')
 ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
 ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')
 ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
 ->join('categories', 'products.sub_category_id', '=', 'categories.id')
 ->where('transactions.type', 'sell')
 ->where('categories.parent_id', $fuelCategoryId)
 ->whereDate('transactions.transaction_date', '>=', $header->first()->date)
 ->whereDate('transactions.transaction_date', '<=', $previous_start_date)
 ->select(
     'categories.id as category_id',
     'categories.name as category_name',
     DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity'),
     DB::raw('SUM(transaction_payments.amount) as total_amount'),
     DB::raw('"sell" as type')
 )
 ->groupBy('categories.id', 'categories.name');

$purchase_query_prev = DB::table('transactions')
 ->join('purchase_lines', 'transactions.id', '=', 'purchase_lines.transaction_id')
 ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')
 ->join('products', 'purchase_lines.product_id', '=', 'products.id')
 ->join('categories', 'products.sub_category_id', '=', 'categories.id')
 ->where('transactions.type', 'purchase')
 ->where('categories.parent_id', $fuelCategoryId)
 ->whereDate('transactions.transaction_date', '>=',  $header->first()->date)
 ->whereDate('transactions.transaction_date', '<=', $previous_start_date)
 ->select(
     'categories.id as category_id',
     'categories.name as category_name',
     DB::raw('SUM(purchase_lines.quantity) as total_quantity'),
     DB::raw('SUM(transaction_payments.amount) as total_amount'),
     DB::raw('"purchase" as type')
 )
 ->groupBy('categories.id', 'categories.name');

$combined_prev = $sell_query_prev->unionAll($purchase_query_prev);

$today_sales_prev = DB::table(DB::raw("({$combined_prev->toSql()}) as combined"))
 ->mergeBindings($combined_prev) // necessary for union subquery
 ->groupBy('category_id', 'category_name')
 ->select(
     'category_id',
     'category_name',
     DB::raw('SUM(total_quantity) as total_quantity'),
     DB::raw('SUM(total_amount) as total_sales')
 )
 ->get();
 

// Process $today_sales
foreach ($today_sales_prev as $item) {
$category_id = $item->category_id;
$merged_results_prev[$category_id] = [
  'category_id' => $item->category_id,
  'category_name' => $item->category_name,
  'total_quantity' => $item->total_quantity,
  'total_sales' => $item->total_sales,
];
}
// Merge $previous_day into the combined results
foreach ($previous_day as $item) {
$category_id = $item['category_id'];
if (isset($merged_results_prev[$category_id])) {
  // If the category already exists, add the quantities and sales
  $merged_results_prev[$category_id]['total_quantity'] += $item['total_quantity'];
  $merged_results_prev[$category_id]['total_sales'] += $item['total_sales'];
} else {
  // If the category doesn't exist, initialize it
  $merged_results_prev[$category_id] = [
      'category_id' => $item['category_id'],
      'category_name' => $item['category_name'],
      'total_quantity' => $item['total_quantity'],
      'total_sales' => $item['total_sales'],
      ];
  }
}
$previous_day = array_values($merged_results_prev);

//transactions
$cash_sales_today = DB::table('transactions')
       ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
       ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
       ->join('categories', 'products.sub_category_id', '=', 'categories.id')       
       ->where('transactions.business_id', $business_id)
       ->where('transactions.type', 'sell')
       ->where('transactions.is_credit_sale', 0)
       ->where('categories.parent_id', $fuelCategoryId)
       ->whereDate('transactions.transaction_date', '>=', $start_today_date)
       ->whereDate('transactions.transaction_date', '<=', $end_today_date)
       ->groupBy('categories.id', 'categories.name')
       ->select(
           'categories.id as category_id',
           'categories.name as category_name',
           DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity'),
           DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price) as total_sales')
       )
       ->get();
   
  
       //dd($cash_sales_today);
    $credit_sales_today = DB::table('transactions')
    ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
    ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
    ->join('categories', 'products.sub_category_id', '=', 'categories.id')       
    ->where('transactions.business_id', $business_id)
    ->where('transactions.type', 'sell')
    ->where('transactions.is_credit_sale', 1)
    ->where('categories.parent_id', $fuelCategoryId)
    ->whereDate('transactions.transaction_date', '>=', $start_today_date)
    ->whereDate('transactions.transaction_date', '<=', $end_today_date)
    ->groupBy('categories.id', 'categories.name')
    ->select(
        'categories.id as category_id',
        'categories.name as category_name',
        DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity'),
        DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price) as total_sales')
    )
    ->get();
//

//cash and credit sales previous

$cash_sales_prev =DB::table('transactions')
                    ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
                    ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
                    ->join('categories', 'products.sub_category_id', '=', 'categories.id')       
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell')
                    ->where('transactions.is_credit_sale', 0)
       
                    ->where('categories.parent_id', $fuelCategoryId)
                    ->whereDate('transactions.transaction_date', '>=',$header->first()->date)
                    ->whereDate('transactions.transaction_date', '<=',$previous_start_date)
                    ->groupBy('categories.id', 'categories.name')
                    ->select(
                        'categories.id as category_id',
                        'categories.name as category_name',
                        DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity'),
                        DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price) as total_sales')
                    )
                    ->get();
   
  
       //dd($cash_sales_today);
    $credit_sales_prev = DB::table('transactions')
                ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
                ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
                ->join('categories', 'products.sub_category_id', '=', 'categories.id')       
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.is_credit_sale', 1)
         
                ->where('categories.parent_id', $fuelCategoryId)
                ->whereDate('transactions.transaction_date', '>=',$header->first()->date)
                ->whereDate('transactions.transaction_date', '<=',$previous_start_date)
                ->groupBy('categories.id', 'categories.name')
                ->select(
                    'categories.id as category_id',
                    'categories.name as category_name',
                    DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity'),
                    DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price) as total_sales')
                )
                ->get();
             // For total_issues section
             $total_issues_previous = array_map(function ($item) {
                return [
                    'category_id' => $item['category_id'],
                    'category_name' => $item['category_name'],
                    'total_quantity' => $item['total_issues']['qty'],
                    'total_sales' => $item['total_issues']['val'],
                ];
            }, $finalCategoryData);


    // Initialize an empty array to store merged results
$merged_results_prev_issue = [];

// Process $cash_sales_prev
foreach ($cash_sales_prev as $item) {
    $category_id = $item->category_id;
    if (!isset($merged_results_prev_issue[$category_id])) {
        // Initialize the category if it doesn't exist
        $merged_results_prev_issue[$category_id] = [
            'category_id' => $item->category_id,
            'category_name' => $item->category_name,
            'total_quantity' => 0,
            'total_sales' => 0,
        ];
    }
    // Add quantities and sales for cash sales
    $merged_results_prev_issue[$category_id]['total_quantity'] += $item->total_quantity;
    $merged_results_prev_issue[$category_id]['total_sales'] += $item->total_sales;
}

// Process $credit_sales_prev
foreach ($credit_sales_prev as $item) {
    $category_id = $item->category_id;
    if (!isset($merged_results_prev_issue[$category_id])) {
        // Initialize the category if it doesn't exist
        $merged_results_prev_issue[$category_id] = [
            'category_id' => $item->category_id,
            'category_name' => $item->category_name,
            'total_quantity' => 0,
            'total_sales' => 0,
        ];
    }
    // Add quantities and sales for credit sales
    $merged_results_prev_issue[$category_id]['total_quantity'] += $item->total_quantity;
    $merged_results_prev_issue[$category_id]['total_sales'] += $item->total_sales;
}

// Process $total_issues_previous
foreach ($total_issues_previous as $item) {
    $category_id = $item['category_id'];
    if (!isset($merged_results_prev_issue[$category_id])) {
        // Initialize the category if it doesn't exist
        $merged_results_prev_issue[$category_id] = [
            'category_id' => $item['category_id'],
            'category_name' => $item['category_name'],
            'total_quantity' => 0,
            'total_sales' => 0,
        ];
    }
    // Add quantities and sales for total issues
    $merged_results_prev_issue[$category_id]['total_quantity'] += $item['total_quantity'];
    $merged_results_prev_issue[$category_id]['total_sales'] += $item['total_sales'];
}

// Convert the associative array back to an indexed array
$total_issues = array_values($merged_results_prev_issue);
   
  
           if ((int)$starting_number === 0) {
             
               $cash_sales_previous = DB::table('transactions')
               ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
               ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
               ->join('categories', 'products.sub_category_id', '=', 'categories.id')
               ->join('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
               ->where('account_transactions.business_id', $business_id)
                             
               ->where('categories.parent_id', $fuelCategoryId)
               ->whereDate('transactions.transaction_date', '>=', $previous_start_date)
               ->whereDate('transactions.transaction_date', '<=', $previous_end_date)
               ->groupBy('categories.id', 'categories.name')
               ->select(
                   'categories.id as category_id',
                   'categories.name as category_name',
                   DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity'),
                   DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price) as total_sales')
               )
               ->get();
           }
           else
           {   
                 // For total_issues section
               $cash_sales_previous = array_map(function ($item) {
                   return [
                       'category_id' => $item['category_id'],
                       'category_name' => $item['category_name'],
                       'total_quantity' => $item['total_issues']['qty'],
                       'total_sales' => $item['total_issues']['val'],
                   ];
               }, $finalCategoryData);

                
        
           }   
       
        
           
       
           $discount_todays = DB::table('transactions')
               ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
               ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
               ->join('categories', 'products.sub_category_id', '=', 'categories.id')
               ->join('contacts', 'transactions.contact_id', '=', 'contacts.id')
               ->join('business', 'transactions.business_id', '=', 'business.id')
               ->join('business_locations', 'transactions.location_id', '=', 'business_locations.id')
               ->join('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
               ->where('account_transactions.business_id', $business_id)
               ->where('categories.parent_id', $fuelCategoryId)
               ->whereDate('transactions.transaction_date', '>=',$start_date)
           ->whereDate('transactions.transaction_date', '<=', $end_date)
               ->groupBy('categories.id', 'categories.name')
               ->select(
                   'categories.id as category_id',
                   'categories.name as category_name',
                   DB::raw('SUM(transaction_sell_lines.quantity) as total_quantitys'),
                   DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price) as total_sale'),
                   DB::raw('SUM(transactions.discount_amount) as total_sales'),
                   DB::raw('SUM(transactions.final_total) as total_final_amount')
               )
               ->get();
               $discount_previous = DB::table('transactions')
               ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
               ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
               ->join('categories', 'products.sub_category_id', '=', 'categories.id')
               ->join('contacts', 'transactions.contact_id', '=', 'contacts.id')
               ->join('business', 'transactions.business_id', '=', 'business.id')
               ->join('business_locations', 'transactions.location_id', '=', 'business_locations.id')
               ->join('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
               ->where('account_transactions.business_id', $business_id)
               ->where('categories.parent_id', $fuelCategoryId)
               ->whereDate('transactions.transaction_date', '>=', $previous_start_date)
               ->whereDate('transactions.transaction_date', '<=', $previous_end_date)
               ->groupBy('categories.id', 'categories.name')
               ->select(
                   'categories.id as category_id',
                   'categories.name as category_name',
                   DB::raw('SUM(transaction_sell_lines.quantity) as total_quantityy'),
                   DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price) as total_sale'),
                   DB::raw('SUM(transactions.discount_amount) as total_sales'),
                   DB::raw('SUM(transactions.final_total) as total_final_amount')
               )
               ->get();

              
               
           
           

         // opening stock
         $formF22Exists = FormF22Header::whereDate('created_at', $start_date)->exists();
           
         if ($formF22Exists) {
           $opening_stock = DB::table('form_f22_headers')   
           ->join('form_f22_details', 'form_f22_headers.form_no', '=', 'form_f22_details.form_no')
           ->join('products', 'form_f22_details.product', '=', 'products.name')
           ->join('categories', 'products.sub_category_id', '=', 'categories.id')
           ->where('form_f22_details.business_id', $business_id)           
           ->where('categories.parent_id', $fuelCategoryId) 
           ->whereDate('form_date', '>=', $start_date)
           ->whereDate('form_date', '<=', $end_date)             
           ->groupBy('categories.id', 'categories.name')   
           ->orderBy('form_f22_headers.form_no', 'desc') // Optional: applies to full result set        
           ->select(
               'categories.id as category_id',
               'categories.name as category_name',
               DB::raw('SUM(form_f22_details.stock_count) as total_quantity'),
               DB::raw('SUM(form_f22_details.unit_sale_price) as total_sales'),                    
               DB::raw('MAX(form_f22_details.form_no) as form_no')
           )
           ->get();
         } 

        }
        else
        {

           
            $header = collect([
                (object)[
                    'starting_number' => $latest->starting_number ?? '',
                    'date' => $latest->date ?? '',
                    'manager_name' =>  $latest->starting_number ?? '',
                    'categories' => json_encode([]) // Avoid null error
                ]
            ]);

            foreach ($header as $setting) {
                if (!isset($setting->categories)) {
                    continue;
                }
            
                $categories = json_decode($setting->categories, true);
            
                if (!is_array($categories)) {
                    continue;
                }
            
                foreach ($categories as $catId => $values) {
                    if (!isset($headerCategoriesMap[$catId])) {
                        $headerCategoriesMap[$catId] = $values;
                    }
                }
            }
            
            // Get fuel subcategories
            $fuelCategories = Category::where('business_id', $business_id)
                ->where('parent_id', $fuelCategoryId)
                ->select(['name', 'id'])
                ->get()
                ->pluck('name', 'id'); // [id => name]
            
            $finalCategoryData = [];
            
            foreach ($fuelCategories as $catId => $catName) {
                $categoryData = $headerCategoriesMap[$catId] ?? [];
            
                $finalCategoryData[] = [
                    'category_id' => $catId,
                    'category_name' => $catName,
                    'previous_day' => [
                        'qty' => $categoryData['previous_day']['qty'] ?? '',
                        'val' => $categoryData['previous_day']['val'] ?? 0,
                    ],
                    'opening_stock' => [
                        'qty' => $categoryData['opening_stock']['qty'] ?? '',
                        'val' => $categoryData['opening_stock']['val'] ?? 0,
                    ],
                    'total_issues' => [
                        'qty' => $categoryData['total_issues']['qty'] ?? '',
                        'val' => $categoryData['total_issues']['val'] ?? 0,
                    ],
                ];
            }
            
            
             // For opening_stock section
             $previous_day = array_map(function ($item) {
                return [
                    'category_id' => $item['category_id'],
                    'category_name' => $item['category_name'],
                    'total_quantity' =>'',
                    'total_sales' =>'',
                ];
            }, $finalCategoryData);
            // For opening_stock section
            $opening_stock = array_map(function ($item) {
                return [
                    'category_id' => $item['category_id'],
                    'category_name' => $item['category_name'],
                    'total_quantity' =>'',
                    'total_sales' =>'',
                ];
            }, $finalCategoryData);
            
            // For total_issues section
            $total_issues = array_map(function ($item) {
                return [
                    'category_id' => $item['category_id'],
                    'category_name' => $item['category_name'],
                    'total_quantity' => 0,
                    'total_sales' => 0,
                ];
            }, $finalCategoryData);

        }
    


       
                
     $incomeGrp_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')->where('accounts.business_id', $business_id)->where('account_groups.name', 'Sales Income Group')->select('accounts.id')->get()->pluck('id');
     

   
     $pump_operator = DB::table('pumps')
     ->leftJoin('products', 'pumps.product_id', '=', 'products.id')
     ->leftJoin('categories', 'products.sub_category_id', '=', 'categories.id')
     ->leftJoin('meter_sales', function ($join) use ($start_date, $meter_end_date) {
         $join->on('pumps.id', '=', 'meter_sales.pump_id');
         $join->whereBetween('meter_sales.created_at', [$start_date, $meter_end_date]);
     })
     ->leftJoin('settlements', 'meter_sales.settlement_no', '=', 'settlements.id')
     ->leftJoin('business_locations', 'settlements.location_id', '=', 'business_locations.id')
     ->leftJoin('pump_operators', 'settlements.pump_operator_id', '=', 'pump_operators.id')
     ->where('categories.parent_id', $fuelCategoryId)
     ->where(function ($query) use ($business_id) {
         $query->where('settlements.business_id', $business_id)
               ->orWhereNull('settlements.business_id');
     })
     ->groupBy(
         'categories.id',
         'categories.name',
         'pumps.pump_no',
         'pumps.id',
         'products.name',
         'business_locations.name',
         'pump_operators.name'
     )
     ->select([
         'categories.id as category_id',
         'categories.name as category_name',
         'pumps.pump_no',
         'pumps.id as pump_id',
         'products.name as product_name',
         'business_locations.name as location_name',
         DB::raw("MAX(settlements.transaction_date) as transaction_date"),
         DB::raw("COALESCE(pump_operators.name, '-') as pump_operator_name"),
         DB::raw('COALESCE(MIN(meter_sales.starting_meter), 0) as min_starting_meter'),
         DB::raw('COALESCE(MAX(meter_sales.closing_meter), 0) as max_closing_meter'),
         DB::raw('COALESCE(SUM(meter_sales.qty), 0) as total_quantity'),
         DB::raw('COALESCE(SUM(meter_sales.sub_total), 0) as total_sales'),
     ])
     ->get();
 
  // dd($pump_operator);
            return [
                "credit_sales" => $credit_sales, 
                "previous_credit_sales" => $previous_credit_sales,
                "form22_details" => $form22_details,
                "form17_increase" => $form17_increase,
                "form17_decrease" => $form17_decrease,
                "transaction" => $transaction,
                "own_group" => $own_group,
                "credit_sales_transaction" => $credit_sales_transaction,
                "previous_transaction" => $previous_transaction,
                "previous_own_group" => $previous_own_group,
                "previous_credit_sales_transaction" => $previous_credit_sales_transaction,
                "form17_increase_previous" => $form17_increase_previous,
                "form17_decrease_previous" => $form17_decrease_previous,
                "merged_sub_categories" => $merged_sub_categories,
                "account_transactions" => $account_transactions,
                "opening_stock" => $opening_stock,
                "previous_day" => $previous_day,
                "today_sales" => $today_sales ,
                "fuelCategory" =>$fuelCategory,
                "total_receipts_last" =>$total_issues,
                "cash_sales_today" => $cash_sales_today,
                "credit_sales_today" => $credit_sales_today,
                'cash_sales_previous' =>$cash_sales_previous,
                'discount_todays'  => $discount_todays, 
                'discount_previous' => $discount_previous,
                'header' =>$header,
                'header_latest'  => $header_latest,
                'pump_operator' =>$pump_operator,               
                'currency_precision' => $currency_precision,
                'qty_precision' => $qty_precision
            ];
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
            $form22_details = FormF22Detail::where('business_id', $business_id)
                                ->whereDate('created_at', '>=', $startDate)
                                ->whereDate('created_at', '<=', $endDate) 
                                ->orderBy('id', 'DESC')               
                                ->first();
            $form17_increase = FormF17Detail::where('select_mode', 'increase')
                                ->whereDate('created_at', '>=', $startDate)
                                ->whereDate('created_at', '<=', $endDate) 
                                ->orderBy('id', 'DESC')               
                                ->first();

            $form17_increase_previous = FormF17Detail::where('select_mode', 'increase')
                                ->whereDate('created_at', '>=', $previous_start_date) 
                                ->orderBy('id', 'DESC')               
                                ->first();

            $form17_decrease = FormF17Detail::where('select_mode', 'descrease')
                                ->whereDate('created_at', '>=', $startDate)
                                ->whereDate('created_at', '<=', $endDate) 
                                ->orderBy('id', 'DESC')               
                                ->first();
                                
            $form17_decrease_previous = FormF17Detail::where('select_mode', 'decrease')
                                ->whereDate('created_at', '>=', $previous_start_date)
                                // ->whereDate('created_at', '<=', $previous_end_date) 
                                ->orderBy('id', 'DESC')               
                                ->first();

            $transaction = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                            ->select(
                                'transactions.id',
                                'transactions.transaction_date',
                                'transactions.final_total',
                                'transaction_payments.method as payment_method',
                                'transaction_sell_lines.quantity',
                                'transaction_sell_lines.unit_price',
                                'transactions.ref_no',
                                'transactions.invoice_no',
                                'transactions.invoice_no as order_no'
                            )
                            ->whereDate('transactions.transaction_date', '>=', $start_date)
                            ->whereDate('transactions.transaction_date', '<=', $end_date)
                            ->orWhere('transaction_payments.method', 'cash')
                            ->orWhere('transaction_payments.method', 'cheque')
                            ->orWhere('transaction_payments.method', 'card')
                            ->orderBy('id', 'DESC')               
                            ->first();

            $previous_transaction = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                            ->select(
                                'transactions.id',
                                'transactions.transaction_date',
                                'transactions.final_total',
                                'transaction_payments.method as payment_method',
                                'transaction_sell_lines.quantity',
                                'transaction_sell_lines.unit_price',
                                'transactions.ref_no',
                                'transactions.invoice_no',
                                'transactions.invoice_no as order_no'
                            )
                            ->whereDate('transactions.transaction_date', '>=', $previous_start_date)
                            // ->whereDate('transactions.transaction_date', '<=', $previous_end_date)
                            ->orWhere('transaction_payments.method', 'cash')
                            ->orWhere('transaction_payments.method', 'cheque')
                            ->orWhere('transaction_payments.method', 'card')
                            ->orderBy('id', 'DESC')               
                            ->first();

            $own_group = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                            ->select(
                                'transactions.id',
                                'transactions.transaction_date',
                                'transactions.final_total',
                                'transaction_payments.method as payment_method',
                                'transaction_sell_lines.quantity',
                                'transaction_sell_lines.unit_price',
                                'transactions.ref_no',
                                'transactions.invoice_no',
                                'transactions.invoice_no as order_no'
                            )
                            ->whereDate('transactions.transaction_date', '>=', $start_date)
                            ->whereDate('transactions.transaction_date', '<=', $end_date)
                            ->orWhere('transaction_payments.method', 'custom_pay_1')
                            ->orWhere('transaction_payments.method', 'custom_pay_2')
                            ->orderBy('id', 'DESC')               
                            ->first();

            $previous_own_group = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                            ->select(
                                'transactions.id',
                                'transactions.transaction_date',
                                'transactions.final_total',
                                'transaction_payments.method as payment_method',
                                'transaction_sell_lines.quantity',
                                'transaction_sell_lines.unit_price',
                                'transactions.ref_no',
                                'transactions.invoice_no',
                                'transactions.invoice_no as order_no'
                            )
                            ->whereDate('transactions.transaction_date', '>=', $previous_start_date)
                            ->orWhere('transaction_payments.method', 'custom_pay_1')
                            ->orWhere('transaction_payments.method', 'custom_pay_2')
                            ->orderBy('id', 'DESC')               
                            ->first();

            $credit_sales_transaction = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                            ->select(
                                'transactions.id',
                                'transactions.transaction_date',
                                'transactions.final_total',
                                'transaction_payments.method as payment_method',
                                'transaction_sell_lines.quantity',
                                'transaction_sell_lines.unit_price',
                                'transactions.ref_no',
                                'transactions.invoice_no',
                                'transactions.invoice_no as order_no'
                            )
                            ->whereDate('transactions.transaction_date', '>=', $start_date)
                            ->whereDate('transactions.transaction_date', '<=', $end_date)
                            ->where('transaction_payments.method', 'credit_sales')
                            ->orderBy('id', 'DESC')               
                            ->first();

            $previous_credit_sales_transaction = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                            ->select(
                                'transactions.id',
                                'transactions.transaction_date',
                                'transactions.final_total',
                                'transaction_payments.method as payment_method',
                                'transaction_sell_lines.quantity',
                                'transaction_sell_lines.unit_price',
                                'transactions.ref_no',
                                'transactions.invoice_no',
                                'transactions.invoice_no as order_no'
                            )
                            ->whereDate('transactions.transaction_date', '>=', $previous_start_date)
                            ->where('transaction_payments.method', 'credit_sales')
                            ->orderBy('id', 'DESC')               
                            ->first();

            $account_transactions = AccountTransaction::join('transactions','transactions.id','account_transactions.transaction_id')
                    ->whereDate('transactions.transaction_date', '>=', $start_date)
                    ->whereDate('transactions.transaction_date', '<=', $end_date)
                    ->where('account_transactions.business_id',$business_id)
                    ->get();
                    
            $opening_stock = AccountTransaction::join('transactions','transactions.id','account_transactions.transaction_id')
                    //->whereDate('transactions.transaction_date', '>=', $start_date)
                    //->whereDate('transactions.transaction_date', '<=', $end_date)
                    ->where('account_transactions.business_id',$business_id)
                    ->where('transactions.type','opening_stock')
                    ->where('transactions.status','final')
                    ->sum('account_transactions.amount');
                    // ->get();
                    
            $today = AccountTransaction::join('transactions','transactions.id','account_transactions.transaction_id')
                    ->whereDate('transactions.transaction_date', '=', Carbon::now())
                    ->where('account_transactions.business_id',$business_id)
                    ->sum('account_transactions.amount');   
                    // ->get();
            $previous_day = AccountTransaction::join('transactions','transactions.id','account_transactions.transaction_id')
                    ->whereDate('transactions.transaction_date', '=', Carbon::now()->subDays(1))
                    ->where('account_transactions.business_id',$business_id)
                    ->sum('account_transactions.amount');
                    // ->get();
                        $incomeGrp_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')->where('accounts.business_id', $business_id)->where('account_groups.name', 'Sales Income Group')->select('accounts.id')->get()->pluck('id');
             $cash_sales_today = AccountTransaction::whereDate('account_transactions.operation_date','=', $start_date)
                ->join('transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
                ->where('account_transactions.business_id',$business_id)
                ->where('account_transactions.type','debit')
                ->get()->sum('amount');
             $credit_sales_today = AccountTransaction::whereDate('account_transactions.operation_date','=',$start_date)
                ->join('transactions', 'transactions.id', '=', 'account_transactions.trfansaction_id')
                ->where('account_transactions.business_id',$business_id)
                ->where('account_transactions.type','credit')
                ->get()->sum('amount');
            
            return [
                "credit_sales" => $credit_sales, 
                "previous_credit_sales" => $previous_credit_sales,
                "form22_details" => $form22_details,
                "form17_increase" => $form17_increase,
                "form17_decrease" => $form17_decrease,
                "transaction" => $transaction,
                "own_group" => $own_group,
                "credit_sales_transaction" => $credit_sales_transaction,
                "previous_transaction" => $previous_transaction,
                "previous_own_group" => $previous_own_group,
                "previous_credit_sales_transaction" => $previous_credit_sales_transaction,
                "form17_increase_previous" => $form17_increase_previous,
                "form17_decrease_previous" => $form17_decrease_previous,
                "merged_sub_categories" => $merged_sub_categories,
                "account_transactions" => $account_transactions,
                "opening_stock" => $opening_stock,
                "previous_day" => (int)$previous_day,
                "today" => (int)$today,
                "cash_sales_today" => (int)$cash_sales_today,
                "credit_sales_today" => (int)$credit_sales_today,
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
             ->whereNull('transactions.customer_group_id')
               ->whereDate('transactions.transaction_date', '=', Carbon::now())
            ->select(
                'transactions.transaction_date',
                'transactions.final_total',
                'products.name as description',
                'products.sub_category_id',
                'transaction_sell_lines.quantity',
                'transaction_sell_lines.unit_price',
                'transactions.ref_no',
                'transactions.invoice_no',
                'contacts.name as customer',
                'transactions.invoice_no as order_no',
                'business.name as comapany',
                'business_locations.mobile as tel',
            );
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        $credit_sales = $query->get();

        return $credit_sales;
    }
  
   
   
 
}
