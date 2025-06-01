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
use App\User;
use Modules\MPCS\Entities\Mpcs21cFormSettings;
use Modules\Petro\Entities\Settlement;

class F21FormController extends Controller
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
       if (!auth()->check()) {
            return redirect()->route('login');
        } 
        $business_id = request()->session()->get('business.id');      

               
         if (auth()->check() && auth()->user()->can('superadmin'))
            $settings = Mpcs21cFormSettings::first();
        else 
            $settings = Mpcs21cFormSettings::where('business_id', $business_id)->first();

        $bname = Business::where('id', $business_id)->first();
    
        $form_number = optional($settings)->starting_number ? $settings->starting_number : "";
        $date = optional($settings)->date ? $settings->date : "";
        $userAdded = $bname ? $bname->name : "";

        $merged_sub_categories = MergedSubCategory::where('business_id', $business_id)->get();
        $business_locations = BusinessLocation::forDropdown($business_id);
      
        $business_details = Business::find($business_id);
        $currency_precision = (int) $business_details->currency_precision;
        $qty_precision = (int) $business_details->quantity_precision;
      
       
          $sub_categories = Category::where('business_id', $business_id)->where('parent_id', '!=', 0)->get();
                     
       
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
        $previous_start_date = Carbon::parse($request->start_date)->subDays(1)->format('Y-m-d');
        $previous_end_date = Carbon::parse($request->end_date)->subDays(1)->format('Y-m-d');
        $today = Transaction::leftJoin('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
                    ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')
                    ->leftJoin('products', 'transaction_sell_lines.product_id', '=', 'products.id')
                    ->leftJoin('categories', 'products.sub_category_id', '=', 'categories.id')
                    ->select(
                        'categories.name as category_name',  
                                             
                        'transaction_payments.amount as amount',
                       'transaction_sell_lines.quantity as quantity' ,
                   
                       DB::raw('SUM(transaction_payments.amount) as total_final_amount'),
                       DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity')
                    )
                    ->whereDate('transactions.transaction_date', '>=', $previous_start_date)
                    ->whereDate('transactions.transaction_date', '<=', $previous_end_date)
                    ->where('categories.parent_id',$fuelCategoryId)
                    ->groupBy('categories.name','products.id')
                    ->get();
            
                $previous_transaction =Transaction::leftJoin('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
                    ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')
                    ->leftJoin('products', 'transaction_sell_lines.product_id', '=', 'products.id')
                    ->leftJoin('categories', 'products.sub_category_id', '=', 'categories.id')
                    ->select(
                        'categories.name as category_name',  
                                             
                        'transaction_payments.amount as amount',
                       'transaction_sell_lines.quantity as quantity' ,
                   
                       DB::raw('SUM(transaction_payments.amount) as total_final_amount'),
                       DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity')
                    )
                    ->whereDate('transactions.transaction_date', '>=', $previous_start_date)
                   
                    ->where('categories.parent_id',$fuelCategoryId)
                    ->groupBy('categories.name')
                                  
                    ->get();
                $query = Transaction::leftjoin('purchase_lines', 'transactions.id', '=', 'purchase_lines.transaction_id')
                    ->leftjoin('products', 'purchase_lines.product_id', 'products.id')
                    ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
                    ->leftjoin('business', 'transactions.business_id', 'business.id')
                    ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
                    ->where('transactions.type', 'purchase')
        
                    ->whereDate('transactions.transaction_date', '>=', $previous_start_date)
                    ->whereDate('transactions.transaction_date', '<=', $previous_end_date)
                    ->select('transactions.id', 'transactions.transaction_date', 'transactions.final_total as final_total', 'products.name as description', 'products.sub_category_id', 'purchase_lines.quantity', 'purchase_lines.purchase_price', 'transactions.ref_no', 'transactions.invoice_no', 'contacts.name as customer', 'transactions.invoice_no as order_no', 'business.name as comapany', 'business_locations.mobile as tel');
               
                $receipts = $query->get();
                
        $layout = 'layouts.app';
       
         // Get the latest form setting record
         $latestForm = Mpcs21cFormSettings::latest()->first();
    
         $categoriesData = [];
         $opening_stock = DB::table('form_f22_details')        
         ->join('products', 'form_f22_details.product', '=', 'products.name')
         ->join('categories', 'products.sub_category_id', '=', 'categories.id')
         ->where('form_f22_details.business_id', $business_id)           
         ->where('categories.parent_id', $fuelCategoryId) 
         ->groupBy('categories.id', 'categories.name')
         ->orderBy('form_no', 'desc') // Optional: applies to full result set
         ->select(
             'categories.id as category_id',
             'categories.name as category_name',
             DB::raw('SUM(form_f22_details.stock_count) as total_quantity'),
             DB::raw('SUM(form_f22_details.debit) as total_sales'),
             DB::raw('MAX(form_f22_details.form_no) as form_no') // Representative form_no
         )
         ->get();
     
         $formNo = optional($opening_stock->first())->form_no;
         if ($latestForm && $latestForm->categories) {
             $categoriesData = json_decode($latestForm->categories, true);
         }
           //today
           $start_today_date = Carbon::parse($request->start_date)->format('Y-m-d');//Carbon::now();
           $end_today_date =Carbon::parse($request->end_date)->format('Y-m-d');//Carbon::now();
 
           $pump_operator = DB::table('pumps')
           ->leftJoin('products', 'pumps.product_id', '=', 'products.id')
           ->leftJoin('categories', 'products.sub_category_id', '=', 'categories.id')
           
          
           ->where('categories.parent_id', $fuelCategoryId)
           
           ->groupBy(
               'categories.id',
               'categories.name',
               'pumps.pump_no',
               'pumps.id',
               'products.name'
           )
           ->select([
               'categories.id as category_id',
               'categories.name as category_name',
               'pumps.pump_no',
               'pumps.id as pump_id',
          
           ])
           ->get();
           
        return view('mpcs::forms.21CForm.F21_form')->with(compact(
            'today',
            'previous_transaction',
            'receipts',
           'F21c_from_no',
            'sub_categories',
            'fuelCategory',
            'currency_precision',
            'qty_precision',
            'merged_sub_categories',
             'business_locations',
             'settings',
             'form_number',
             'date',
             'formNo',
             'userAdded',
            'layout',
            'categoriesData',
            'latestForm',
            'pump_operator' 
            ));
    }

        /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function get21CFormSettings() {

        $business_id = request()->session()->get('user.business_id');

        $fuelCategoryId = Category::where('name', 'Fuel')->value('id');

        if (auth()->user()->can('superadmin')) {
            $fuelCategory = Category::where('parent_id', $fuelCategoryId)
                ->select(['name', 'id'])
                ->get()
                ->pluck('name', 'id');
        
            $pumps = Product::leftJoin('pumps', 'products.id', 'pumps.product_id')
                ->pluck('pumps.pump_name', 'pumps.id');
        
        } else {
            $fuelCategory = Category::where('business_id', $business_id)
                ->where('parent_id', $fuelCategoryId)
                ->select(['name', 'id'])
                ->get()
                ->pluck('name', 'id');
        
            $pumps = Product::leftJoin('pumps', 'products.id', 'pumps.product_id')
                ->where('products.business_id', $business_id)
                ->pluck('pumps.pump_name', 'pumps.id');
        }

        $latest = Mpcs21cFormSettings::orderBy('date', 'desc')->first();
        if (!empty( $latest))
        {
            $current_date = Carbon::today();
            $starting_day = Carbon::parse($latest->date);
            $days_passed = $starting_day->diffInDays($current_date);
            $starting_number =$latest->starting_number + $days_passed;
        }
       else
       {
        $starting_number =1;
       }
        return view('mpcs::forms.21CForm.create_21c_form_settings', compact('fuelCategory', 'pumps','starting_number'));

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
                    
            // $today = AccountTransaction::join('transactions','transactions.id','account_transactions.transaction_id')
            //         ->whereDate('transactions.transaction_date', '=', Carbon::now())
            //         ->where('account_transactions.business_id',$business_id)
            //         ->sum('account_transactions.amount');   
                    // ->get();



                    $today = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
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
                    ->whereDate('transactions.transaction_date', '=', Carbon::now())                   
                    ->orderBy('id', 'DESC')               
                    ->first();        
            $previous_day = AccountTransaction::join('transactions','transactions.id','account_transactions.transaction_id')
                    ->whereDate('transactions.transaction_date', '=', Carbon::now()->subDays(1))
                    ->where('account_transactions.business_id',$business_id)
                    ->sum('account_transactions.amount');
                    // ->get();
                        $incomeGrp_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')->where('accounts.business_id', $business_id)->where('account_groups.name', 'Sales Income Group')->select('accounts.id')->get()->pluck('id');
             $cash_sales_today = AccountTransaction::whereDate('account_transactions.operation_date','=', Carbon::now())
                ->join('transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
                ->where('account_transactions.business_id',$business_id)
                ->where('account_transactions.type','debit')
                ->get()->sum('amount');
             $credit_sales_today = AccountTransaction::whereDate('account_transactions.operation_date','=', Carbon::now())
                ->join('transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
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
                "today" => $today,
                "cash_sales_today" => (int)$cash_sales_today,
                "credit_sales_today" => (int)$credit_sales_today,
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
             $cash_sales_today = AccountTransaction::whereDate('account_transactions.operation_date','=', Carbon::now())
                ->join('transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
                ->where('account_transactions.business_id',$business_id)
                ->where('account_transactions.type','debit')
                ->get()->sum('amount');
             $credit_sales_today = AccountTransaction::whereDate('account_transactions.operation_date','=', Carbon::now())
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
                'business_locations.mobile as tel'
            );
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        $credit_sales = $query->get();

        return $credit_sales;
    }
  
    
    public function store21cFormSettings(Request $request)
    {
        $business_id = session()->get('user.business_id');
    
        // Step 1: Initialize an empty categories array
        $categories_data = [];
    
        // Step 2: Extract relevant fields from the request
        $previous_day = $request->input('previous_day', []);
        $opening_stock = $request->input('opening_stock', []);
        $total_issues = $request->input('total_issues', []);
    
        // Step 3: Get all category keys
        $category_keys = array_unique(array_merge(
            array_keys($previous_day),
            array_keys($opening_stock),
            array_keys($total_issues)
        ));
    
        // Step 4: Build categories data
        foreach ($category_keys as $category_id) {
            $categories_data[$category_id] = [
                'previous_day' => [
                    'qty' => $previous_day[$category_id]['qty'] ?? null,
                    'val' => $previous_day[$category_id]['val'] ?? null,
                ],
                'opening_stock' => [
                    'qty' => $opening_stock[$category_id]['qty'] ?? null,
                    'val' => $opening_stock[$category_id]['val'] ?? null,
                ],
                'total_issues' => [
                    'qty' => $total_issues[$category_id]['qty'] ?? null,
                    'val' => $total_issues[$category_id]['val'] ?? null,
                ],
            ];
        }
    
        // Step 5: Prepare data for insertion
        $formData = [
            'business_id' => $business_id,
            'date' => $request->datepicker,
            'time' => $request->time,
            'starting_number' => $request->starting_number,
            'ref_pre_form_number' => 5,
            'rec_sec_prev_day_amt' => 5,
            'rec_sec_opn_stock_amt' => 5,
            'issue_section_previous_day_amount' => 5,
            'manager_name' => $request->manager_name,
            'categories' => json_encode($categories_data),
        ];
    
        // Step 6: Insert into database
        Mpcs21cFormSettings::create($formData);
    
        $output = [
            'success' => 1,
            'msg' => __('success'),
        ];
        
        return back()->with('status', $output);
    }
    
    
    public function mpcs21cFormSettings()
    {
        if (request()->ajax()) {
            $header = Mpcs21cFormSettings::select('*')
            ->groupBy('starting_number')
            ->get();
             
            return DataTables::of($header)
            ->addColumn('action', function ($row) {
                if (auth()->user()->can('superadmin')) {
                    return '<button href="#" data-href="' . url('/mpcs/edit-21-c-form-settings/' . $row->id) . '" class="btn-modal btn btn-primary btn-xs" data-container=".update_form_16_a_settings_modal"><i class="fa fa-edit" aria-hidden="true"></i> ' . __("messages.edit") . '</button>';
                }
                return '';
            })
            ->editColumn('date', function($row) {
                $formattedTime = Carbon::parse($row->time)->format('H:i');
                return $row->date.' '.$formattedTime;
            })
             ->editColumn('rec_sec_prev_day_amt', function($row) {
                return number_format($row->rec_sec_prev_day_amt, 2, '.', ',');
            })
            ->editColumn('rec_sec_opn_stock_amt', function($row) {
                return number_format($row->rec_sec_opn_stock_amt, 2, '.', ',');
            })
            ->editColumn('issue_section_previous_day_amount', function($row) {
                return number_format($row->issue_section_previous_day_amount, 2, '.', ',');
            })
            ->addColumn('pumps_data', function ($row) {
                $pumps = json_decode($row->pumps, true);
                $meters = json_decode($row->meters, true);
        
                $rows = [];
        
                if (!empty($pumps)) {
                    foreach ($pumps as $index => $pump) {
                        $rows[] = [
                            'pump_name' => Pump::where('id', $pump)->value('pump_name'),
                            'last_meter_value' => isset($meters[$index]) ? $meters[$index] : null
                        ];
                    }
                }
        
                return $rows; // Return array of pump-meter pairs
            })
            ->rawColumns(['action'])
            ->make(true);
        }

    }


    public function edit21cFormSetting($id) {
        $business_id = request()->session()->get('user.business_id');

        $fuelCategoryId = Category::where('name', 'Fuel')->value('id');

        if(auth()->user()->can('superadmin')) {

            $fuelCategory = Category::where('parent_id', $fuelCategoryId)
            ->select(['name', 'id'])
            ->get()->pluck('name', 'id');
            
              $allpumps = Product::leftjoin('pumps', 'products.id', 'pumps.product_id')
            ->select('pumps.pump_name', 'pumps.id')
            ->get()
            ->pluck('pump_name', 'id'); 

            $settings = Mpcs21cFormSettings::where('id', $id)->first();
        } else {

            $fuelCategory = Category::where('business_id', $business_id)
            ->where('parent_id', $fuelCategoryId)
            ->select(['name', 'id'])
            ->get()->pluck('name', 'id');

            $allpumps = Product::leftjoin('pumps', 'products.id', 'pumps.product_id')
            ->where('products.business_id', $business_id)
            ->select('pumps.pump_name', 'pumps.id')
            ->get()
            ->pluck('pump_name', 'id'); 

            $settings = Mpcs21cFormSettings::where('business_id', $business_id)->where('id', $id)->first();

        }    
        $latestForm = Mpcs21cFormSettings::latest()->first();
    
        
           $categoriesData = [];
       
           if ($latestForm && $latestForm->categories) {
               $categoriesData = json_decode($latestForm->categories, true);
           }
        //$settings = Mpcs21cFormSettings::where('business_id', $business_id)->where('id', $id)->first();
        
        return view('mpcs::forms.21CForm.edit_21c_form_settings')->with(compact(
                    'categoriesData', 'fuelCategory', 'latestForm'
        ));
    }


    public function mpcs21Update(Request $request, $id)
{
    $business_id = $request->session()->get('user.business_id');

    // Delete the previous entry
    Mpcs21cFormSettings::destroy($id);

    // Step 1: Initialize an empty categories array
    $categories_data = [];

    // Step 2: Extract relevant fields from the request
    $previous_day = $request->input('previous_day', []);
    $opening_stock = $request->input('opening_stock', []);
    $total_issues = $request->input('total_issues', []);

    // Step 3: Loop through the unique category IDs
    $category_keys = array_unique(array_merge(
        array_keys($previous_day),
        array_keys($opening_stock),
        array_keys($total_issues)
    ));

    foreach ($category_keys as $category_id) {
        $categories_data[$category_id] = [
            'previous_day' => [
                'qty' => $previous_day[$category_id]['qty'] ?? null,
                'val' => $previous_day[$category_id]['val'] ?? null,
            ],
            'opening_stock' => [
                'qty' => $opening_stock[$category_id]['qty'] ?? null,
                'val' => $opening_stock[$category_id]['val'] ?? null,
            ],
            'total_issues' => [
                'qty' => $total_issues[$category_id]['qty'] ?? null,
                'val' => $total_issues[$category_id]['val'] ?? null,
            ],
        ];
    }

    // Step 4: Prepare the data for insertion
    $formData = [
        'business_id' => $business_id,
        'date' => $request->datepicker,
        'time' => $request->time,
        'starting_number' => $request->starting_number,
        'ref_pre_form_number' => 0,
        'rec_sec_prev_day_amt' => 0,
        'rec_sec_opn_stock_amt' => 0,
        'issue_section_previous_day_amount' => 0,
        'manager_name' => $request->manager_name,
        'categories' => json_encode($categories_data),
    ];

    // Step 5: Insert into database
    Mpcs21cFormSettings::create($formData);

    $output = [
        'success' => 1,
        'msg' => __('success'),
    ];
    
    return back()->with('status', $output);
    
}


    
    /**  GET DISTRICT BASED ON PROVINCE */
    public function getSubcategoryPumps($subCategoryId)
    {

     $business_id = request()->session()->get('business.id');

     if(auth()->user()->can('superadmin')) {    
            $pumps = Product::leftjoin('pumps', 'products.id', 'pumps.product_id')
            ->where('products.sub_category_id', $subCategoryId)
            ->whereNotNull('pumps.id')
            ->pluck('pumps.pump_name', 'pumps.id');
    } else  {
            $pumps = Product::leftjoin('pumps', 'products.id', 'pumps.product_id')
            ->where('products.business_id', $business_id)
            ->where('products.sub_category_id', $subCategoryId)
            ->whereNotNull('pumps.id')
            ->pluck('pumps.pump_name', 'pumps.id');
    }

    return response()->json($pumps);

   }
    
    public function addNewPumpRow(Request $request) {

        $business_id = request()->session()->get('business.id');

        $fuelCategoryId = Category::where('name', 'Fuel')->value('id');

       if(auth()->user()->can('superadmin')) {
            $fuelCategory = Category::where('parent_id', $fuelCategoryId)
            ->select(['name', 'id'])
            ->get();
        }
         else {
            $fuelCategory = Category::where('business_id', $business_id)
            ->where('parent_id', $fuelCategoryId)
            ->select(['name', 'id'])
            ->get();
         }   

        $html = '<tr>
        <td>
            <select class="form-control select2 category_select" style="width: 100% !important;" name="category[]" required onChange=loadPump(this)>
                <option selected value="">Please select</option>';

    // Loop through categories and append options
    foreach ($fuelCategory as $category) {
        $html .= '<option value="' . $category->id . '">' . $category->name . '</option>';
    }

    $html .= '</select>
        </td>
        <td>
            <select class="form-control select2 pump_select" style="width: 100% !important;" name="pump[]" required>
              
            </select>
        </td>
        <td>
            <input type="number" step="0.01" name="meter[]" class="form-control meter" required>
        </td>
        <td>
            <button type="button" class="btn btn-danger remove_row"><i class="fa fa-minus-circle"></i></button>
        </td>
    </tr>';

    return response()->json(['html' => $html]);
   

    }
   
    public function printF21cForm(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $data = array();
        parse_str($request->data, $data); // converting serielize string to array
       
         $fuelCategory = Category::where('business_id', $business_id)
        ->where('parent_id', 3)
        ->select(['name', 'id'])
        ->get()->pluck('name', 'id');
       
        $details = $data;
        //$data = $data['f21c'];
        return view('mpcs::forms.21CForm.print_f21c_form')->with(compact('details', 'fuelCategory'));
    }
 

    /**
     * F21 Form
     * 07-02-2025
     * Intrithm
     */

     public function get21Form(Request $request){
        $business_id = request()->session()->get('business.id');
        $settings = MpcsFormSetting::where('business_id', $business_id)->first();
    
        $bname = Business::where('id', $business_id)->first();
        $products = Product::where('business_id', $business_id)->pluck('name', 'id');

    
        $form_number = optional($settings)->ref_pre_form_number ? $settings->ref_pre_form_number : "";
        $date = optional($settings)->date ? $settings->date : "";
        $userAdded = $bname ? $bname->name : "";
    
       $dateRange = $request->input('form_16a_date_range');
        if ($dateRange) {
            $dates = explode(' - ', $dateRange);
            $start_date = $dates[0];
            $end_date = $dates[1];
        } else {
            // Set default date range if the request parameter is empty
            $start_date = Carbon::now()->subDays(7)->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
        }
    
    
        $form16a = FormF16Detail::latest()->first();
    
        if (!empty($settings)) {
            $F16a_from_no = !empty($form16a) ? $form16a->form_no + 1 : $settings->starting_number;
        } else {
            $F16a_from_no = '';
        }
    
        $suppliers = Contact::suppliersDropdown($business_id, false);
        $business_locations = BusinessLocation::forDropdown($business_id);

        $transactionTypes = array(
            0 => 'POS Sale',
            1 => 'Settlement',
            2 => 'Purchase Order',
            3 => 'Sales Return',
            4 => 'Purchase return'
        );
     
      //$form_f16a = FormF16Detail::where('transaction_id', $lastRecord['id'])->first();
        return view('mpcs::forms.21Form.F21')->with(compact(
            'business_locations',
            'products',
            'F16a_from_no',
            'settings',
            'form_number',
            'date',
            'userAdded',
            'transactionTypes'
        ));
     }


public function getPos() {

    $business_id = request()->session()->get('user.business_id');

    $type = ['sell'];
    $status = ['final', 'order'];

    $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')
                ->leftJoin('transaction_sell_lines as tsl', 'transactions.id', '=', 'tsl.transaction_id')
                ->leftJoin('products', 'tsl.product_id', '=', 'products.id')
                ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
                ->leftJoin('users as ss', 'transactions.res_waiter_id', '=', 'ss.id')
                ->leftjoin('users as deleted','transactions.deleted_by','deleted.id')
                ->leftJoin('res_tables as tables', 'transactions.res_table_id', '=', 'tables.id')
                ->join(
                    'business_locations AS bl',
                    'transactions.location_id',
                    '=',
                    'bl.id'
                )
                ->leftJoin(
                    'transactions AS SR',
                    'transactions.id',
                    '=',
                    'SR.return_parent_id'
                )
                ->leftJoin(
                    'types_of_services AS tos',
                    'transactions.types_of_service_id',
                    '=',
                    'tos.id'
                )
                ->where('transactions.business_id', $business_id)
                ->whereIn('transactions.type', $type)
                ->whereIn('transactions.status', $status)
                ->select(
                    DB::raw('MIN(transactions.id) as id'), // Keep the first entry's ID
                    DB::raw('SUM(DISTINCT transactions.final_total) as final_total'),
                    DB::raw('GROUP_CONCAT(DISTINCT transactions.id) as transaction_ids'),
                    'deleted.username as deletedBy',
                    // 'transactions.id',
                    'transactions.transaction_date',
                    'transactions.is_direct_sale',
                    'transactions.invoice_no',
                    'contacts.name',
                    'contacts.mobile',
                    'transactions.price_later',
                    'transactions.payment_status',
                    // 'transactions.final_total',
                    'transactions.tax_amount',
                    'transactions.discount_amount',
                    'transactions.discount_type',
                    'transactions.total_before_tax',
                    'transactions.rp_redeemed',
                    'transactions.rp_redeemed_amount',
                    'transactions.rp_earned',
                    'transactions.types_of_service_id',
                    'transactions.shipping_status',
                    'transactions.pay_term_number',
                    'transactions.pay_term_type',
                    'transactions.additional_notes',
                    'transactions.staff_note',
                    'transactions.shipping_details',
                    'transactions.commission_agent',
                    'transactions.ref_no as ref_no',
                    'products.sku',
                    'products.name as productname',
                    'tsl.quantity as recd_qty',
                    'transactions.sub_type as the_transaction_sub_type',
                    DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by"),
                    DB::raw('(SELECT SUM(transaction_payments.amount) FROM transaction_payments WHERE
                        transaction_payments.transaction_id=transactions.id) as total_paid'),
                    'bl.name as business_location',
                    DB::raw('COUNT(SR.id) as return_exists'),
                    DB::raw('(SELECT SUM(TP2.amount) FROM transaction_payments AS TP2 WHERE
                        TP2.transaction_id=SR.id ) as return_paid'),
                    DB::raw('COALESCE(SR.final_total, 0) as amount_return'),
                    'SR.id as return_transaction_id',
                    'tos.name as types_of_service_name',
                    'transactions.service_custom_field_1',
                    DB::raw('COUNT( DISTINCT tsl.id) as total_items'),
                    DB::raw("CONCAT(COALESCE(ss.surname, ''),' ',COALESCE(ss.first_name, ''),' ',COALESCE(ss.last_name,'')) as waiter"),
                    'tables.name as table_name'
                )->with('sell_lines')
                ->orderBy('transactions.id','DESC')
                ->withTrashed();

                if (!empty(request()->start_date) && !empty(request()->end_date)) {
                    $start = request()->start_date;
                    $end =  request()->end_date;
                    $sells->whereDate('transactions.transaction_date', '>=', $start)
                        ->whereDate('transactions.transaction_date', '<=', $end);
                }


                $permitted_locations = auth()->user()->permitted_locations();
                if ($permitted_locations != 'all') {
                    $sells->whereIn('transactions.location_id', $permitted_locations);
                }

                if (request()->has('location_id')) {
                    $location_id = request()->get('location_id');
                    if (!empty($location_id)) {
                        $sells->where('transactions.location_id', $location_id);
                    }
                }
                if (request()->has('product_id')) {
                    $product_id = request()->get('product_id');
                    if (!empty($product_id)) {
                        $sells->where('tsl.product_id', $product_id);
                    }
                }

                $sells->groupBy('transactions.invoice_no');
                
                $final_sells = $sells->get();

                foreach($final_sells as $sell){
                    $transaction_ids = explode(',', $sell->transaction_ids);
                    $sell->final_total = Transaction::whereIn('id', $transaction_ids)
                        ->where(function ($query) {
                            $query->where('sub_type', '!=', "credit_sale")
                            ->orWhereNull('sub_type');
                        })
                        ->sum('final_total');
                }

                $datatable = Datatables::of($final_sells)
                ->removeColumn('id')
                ->editColumn(
                    'bill_no',
                    function ($row) {
                        return $row->invoice_no;
                    }
                )
                ->editColumn(
                    'book_no',
                    function ($row) {
                        return '-';
                    }
                )
                ->editcolumn('received_qty', function($row){
                    return '0.00';
                })
                ->editcolumn('transaction_type', function($row){
                        return 'POS Sale';
                    }
                )
                ->editColumn(
                    'product_code',
                    function ($row) {
                        return $row->sku;
                    }
                )
                ->editColumn(
                    'product_name',
                    function ($row) {
                        return $row->productname;
                    }
                )
                ->editColumn(
                    'sold_qty',
                    function ($row) {
                        return number_format($row->recd_qty, 2);
                })
                ->editColumn(
                    'balance_qty',
                    function ($row) {
                        return number_format(0 + $row->recd_qty, 2);
                  });

               return $datatable->make(true);

}     

/**
 * Filter purchase order
 * 10-02-2025
 * Sandy
 */

public function getPurchaseOrder() {

    $business_id = request()->session()->get('user.business_id');

    $purchases = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')

                ->join(

                    'business_locations AS BS',

                    'transactions.location_id',

                    '=',

                    'BS.id'

                )

                ->leftJoin('transaction_payments AS TP', function ($join) {

                    $join->on('transactions.id', '=', 'TP.transaction_id')

                        ->whereNull('TP.deleted_at');
                })

                ->leftJoin(

                    'transactions AS PR',

                    'transactions.id',

                    '=',

                    'PR.return_parent_id'

                )

                ->leftjoin('users as deleted', 'transactions.deleted_by', 'deleted.id')

                ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')

                ->leftJoin('purchase_lines as pl', 'pl.transaction_id', '=', 'transactions.id')

                ->join('products', 'pl.product_id', '=', 'products.id')

                ->where('transactions.business_id', $business_id)

                ->where('transactions.type', 'purchase')

                ->select(

                    'deleted.username as deletedBy',

                    'transactions.id',

                    'transactions.document',

                    'transactions.transaction_date',

                    'transactions.invoice_date',

                    'transactions.ref_no',

                    'transactions.invoice_no',

                    'transactions.purchase_entry_no',

                    'contacts.name',

                    'transactions.status',

                    'transactions.payment_status',

                    'transactions.final_total',

                    'BS.name as location_name',

                    'transactions.pay_term_number',

                    'transactions.pay_term_type',

                    'transactions.overpayment_setoff',

                    'PR.id as return_transaction_id',

                    'TP.method',

                    'TP.id as tp_id',

                    'TP.account_id',

                    'TP.cheque_number',

                    'pl.lot_number',

                    'pl.quantity as recd_qty',
                    
                    'products.sku',

                    'products.name as productname',

                    DB::raw('(SELECT SUM(transaction_payments.amount) FROM transaction_payments WHERE

                    transaction_payments.transaction_id = transactions.id AND transaction_payments.deleted_at IS NULL) as amount_paid'),

                    DB::raw('(SELECT SUM(TP2.amount) FROM transaction_payments AS TP2 WHERE

                        TP2.transaction_id=PR.id ) as return_paid'),

                    DB::raw('COUNT(PR.id) as return_exists'),

                    DB::raw('COALESCE(PR.final_total, 0) as amount_return'),

                    DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by"),
                    DB::raw('(SELECT SUM(pl.purchase_price * pl.quantity)) as total_before_tax'),
                    DB::raw('(SELECT SUM(pl.item_tax * pl.quantity)) as item_tax'),

                )

                ->groupBy('transactions.id');

                $permitted_locations = auth()->user()->permitted_locations();

                if (!empty(request()->start_date) && !empty(request()->end_date)) {
                    $start = request()->start_date;
                    $end =  request()->end_date;
                    $purchases->whereDate('transactions.transaction_date', '>=', $start)
                        ->whereDate('transactions.transaction_date', '<=', $end);
                }

                if ($permitted_locations != 'all') {
                    $purchases->whereIn('transactions.location_id', $permitted_locations);
                }

                if (request()->has('location_id')) {
                    $location_id = request()->get('location_id');
                    if (!empty($location_id)) {
                        $purchases->where('transactions.location_id', $location_id);
                    }
                }
                if (request()->has('product_id')) {
                    $product_id = request()->get('product_id');
                    if (!empty($product_id)) {
                        $purchases->where('pl.product_id', $product_id);
                    }
                }

                $datatable = Datatables::of($purchases)
                ->removeColumn('id')
                ->editColumn(
                    'bill_no',
                    function ($row) {
                        return $row->invoice_no;
                    }
                )
                ->editColumn(
                    'book_no',
                    function ($row) {
                        return '-';
                    }
                )
                ->editcolumn('received_qty', function($row){
                    return number_format($row->recd_qty, 2);
                })
                ->editcolumn('transaction_type', function($row){
                        return 'Purchase Order';
                    }
                )
                ->editColumn(
                    'product_code',
                    function ($row) {
                        return $row->sku;
                    }
                )
                ->editColumn(
                    'product_name',
                    function ($row) {
                        return $row->productname;
                    }
                )
                ->editColumn(
                    'sold_qty',
                    function ($row) {
                        return '0.00';
                })
                ->editColumn(
                    'balance_qty',
                    function ($row) {
                        return number_format(0 + $row->recd_qty, 2);
                  });

               return $datatable->make(true);

}

/**
 * SALES RETURN FILTER
 */

public function getSellReturn() {

    $business_id = request()->session()->get('user.business_id');

    $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')

        ->join(
            'business_locations AS bl',
            'transactions.location_id',
            '=',
            'bl.id'
        )
        ->join(
            'transactions as T1',
            'transactions.return_parent_id',
            '=',
            'T1.id'
        )
        ->leftJoin('transaction_sell_lines as tsl', 'transactions.return_parent_id', '=', 'tsl.transaction_id')
        ->join('products', 'tsl.product_id', '=', 'products.id')    
        ->leftJoin(
            'transaction_payments AS TP',
            'transactions.id',
            '=',
            'TP.transaction_id'
        )
        ->where('transactions.business_id', $business_id)
        ->where('transactions.type', 'sell_return')
        ->where('transactions.status', 'final')
        ->select(
            'transactions.id',
            'transactions.transaction_date',
            'transactions.invoice_no',
            'contacts.name',
            'transactions.final_total',
            'transactions.payment_status',
            'bl.name as business_location',
            'T1.invoice_no as parent_sale',
            'T1.id as parent_sale_id',
            'products.sku',
            'products.name as productname',
            'tsl.quantity_returned as recd_qty',
            DB::raw('SUM(TP.amount) as amount_paid')
        );


                $permitted_locations = auth()->user()->permitted_locations();

                if (!empty(request()->start_date) && !empty(request()->end_date)) {
                    $start = request()->start_date;
                    $end =  request()->end_date;
                    $sells->whereDate('transactions.transaction_date', '>=', $start)
                        ->whereDate('transactions.transaction_date', '<=', $end);
                }

                if ($permitted_locations != 'all') {
                    $sells->whereIn('transactions.location_id', $permitted_locations);
                }

                if (request()->has('location_id')) {
                    $location_id = request()->get('location_id');
                    if (!empty($location_id)) {
                        $sells->where('transactions.location_id', $location_id);
                    }
                }
                if (request()->has('product_id')) {
                    $product_id = request()->get('product_id');
                    if (!empty($product_id)) {
                        $sells->where('tsl.product_id', $product_id);
                    }
                }

                $sells->groupBy('transactions.id');

                $datatable = Datatables::of($sells)
                ->removeColumn('id')
                ->editColumn(
                    'bill_no',
                    function ($row) {
                        return $row->invoice_no;
                    }
                )
                ->editColumn(
                    'book_no',
                    function ($row) {
                        return '-';
                    }
                )
                ->editcolumn('received_qty', function($row){
                    return number_format($row->recd_qty, 2);
                })
                ->editcolumn('transaction_type', function($row){
                        return 'Sales Return';
                    }
                )
                ->editColumn(
                    'product_code',
                    function ($row) {
                        return $row->sku;
                    }
                )
                ->editColumn(
                    'product_name',
                    function ($row) {
                        return $row->productname;
                    }
                )
                ->editColumn(
                    'sold_qty',
                    function ($row) {
                        return '0.00';
                })
                ->editColumn(
                    'balance_qty',
                    function ($row) {
                        return number_format(0 + $row->recd_qty, 2);
                  });

               return $datatable->make(true);         
    }

/**
 * PURCHASE RETURN
 */
public function getPurchaseReturn(){
    $business_id = request()->session()->get('user.business_id');
    $purchases_returns = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->join(
                    'business_locations AS BS',
                    'transactions.location_id',
                    '=',
                    'BS.id'
                )
                ->leftJoin(
                    'transactions AS T',
                    'transactions.return_parent_id',
                    '=',
                    'T.id'
                )
                ->leftJoin('purchase_lines as pl', 'transactions.return_parent_id', '=', 'pl.transaction_id')
                ->join('products', 'pl.product_id', '=', 'products.id')  
                ->leftJoin(
                    'transaction_payments AS TP',
                    'transactions.id',
                    '=',
                    'TP.transaction_id'
                )
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'purchase_return')
                ->select(
                    'transactions.id',
                    'transactions.transaction_date',
                    'transactions.invoice_no as invoiceno',
                    'transactions.ref_no as invoice_no',
                    'contacts.name',
                    'transactions.status',
                    'transactions.payment_status',
                    'transactions.final_total',
                    'transactions.return_parent_id',
                    'BS.name as location_name',
                    'T.ref_no as parent_purchase',
                    'products.sku',
                    'products.name as productname',
                    'pl.quantity_returned as recd_qty',
                    DB::raw('SUM(TP.amount) as amount_paid')
                );

                $permitted_locations = auth()->user()->permitted_locations();

                if (!empty(request()->start_date) && !empty(request()->end_date)) {
                    $start = request()->start_date;
                    $end =  request()->end_date;
                    $purchases_returns->whereDate('transactions.transaction_date', '>=', $start)
                        ->whereDate('transactions.transaction_date', '<=', $end);
                }

                if ($permitted_locations != 'all') {
                    $purchases_returns->whereIn('transactions.location_id', $permitted_locations);
                }

                if (request()->has('location_id')) {
                    $location_id = request()->get('location_id');
                    if (!empty($location_id)) {
                        $purchases_returns->where('transactions.location_id', $location_id);
                    }
                }
                if (request()->has('product_id')) {
                    $product_id = request()->get('product_id');
                    if (!empty($product_id)) {
                        $purchases_returns->where('tsl.product_id', $product_id);
                    }
                }

                $purchases_returns->groupBy('transactions.id');

                $datatable = Datatables::of($purchases_returns)
                ->removeColumn('id')
                ->editColumn(
                    'bill_no',
                    function ($row) {
                        return $row->ref_no;
                    }
                )
                ->editColumn(
                    'book_no',
                    function ($row) {
                        return '-';
                    }
                )
                ->editcolumn('received_qty', function($row){
                    return '0.00';
                })
                ->editcolumn('transaction_type', function($row){
                        return 'Purchase Return';
                    }
                )
                ->editColumn(
                    'product_code',
                    function ($row) {
                        return $row->sku;
                    }
                )
                ->editColumn(
                    'product_name',
                    function ($row) {
                        return $row->productname;
                    }
                )
                ->editColumn(
                    'sold_qty',
                    function ($row) {
                        return number_format($row->recd_qty, 2);
                })
                ->editColumn(
                    'balance_qty',
                    function ($row) {
                        return number_format(0 + $row->recd_qty, 2);
                  });

               return $datatable->make(true);       

}

/**
 * SETTLEMENT FILTER
 * 11-02-2025
 */

public function getSettlement() {

    $business_id = request()->session()->get('user.business_id');

    $settlement = Settlement::leftJoin('business_locations', 'settlements.location_id', '=', 'business_locations.id')

                ->leftJoin('pump_operators', 'settlements.pump_operator_id', '=', 'pump_operators.id')

                ->leftJoin('pump_operator_assignments', function ($join) {
                    $join->on('settlements.id', '=', 'pump_operator_assignments.settlement_id');
                })

                ->where('settlements.business_id', $business_id)

                ->select([
                    'pump_operators.name as pump_operator_name',
                    'business_locations.name as location_name',
                    'settlements.*',
                    'pump_operator_assignments.shift_number',
                ]);



                $permitted_locations = auth()->user()->permitted_locations();

                if (!empty(request()->start_date) && !empty(request()->end_date)) {
                    $start = request()->start_date;
                    $end =  request()->end_date;
                    $settlement->whereDate('settlements.transaction_date', '>=', $start)
                        ->whereDate('settlements.transaction_date', '<=', $end);
                }

                if ($permitted_locations != 'all') {
                    $settlement->whereIn('settlements.location_id', $permitted_locations);
                }

                if (request()->has('location_id')) {
                    $location_id = request()->get('location_id');
                    if (!empty($location_id)) {
                        $settlement->where('settlements.location_id', $location_id);
                    }
                }

                $settlement->with(['meter_sales', 'other_sales']);

                $datatable = Datatables::of($settlement->get()->flatMap(function ($row) {
                    $rows = [];
                
                    if (!empty($row->meter_sales)) {
                        foreach ($row->meter_sales as $meter_sale) {

                            $product_id = request()->get('product_id'); 

                            if (!empty($product_id)) {
                                if ($meter_sale->product_id != $product_id) {
                                    continue; 
                                }
                            }

                            $product = \App\Product::find($meter_sale->product_id);

                            if(!empty($product)) {
                                $rows[] = [
                                    'transaction_date' => $row->transaction_date,
                                    'transaction_type' => 'Settlement - Meter Sale',
                                    'invoice_no' => $row->settlement_no,
                                    'book_no' => '-',
                                    'product_code' => $product->sku,
                                    'product_name' => $product->name,
                                    'received_qty' => '0.00',
                                    'sold_qty' => number_format($meter_sale->qty, 2),
                                    'balance_qty' => number_format(0 + $meter_sale->qty, 2),
                                ];
                            }
                        }
                    }
                
                    if (!empty($row->other_sales)) {
                        foreach ($row->other_sales as $other_sale) {

                            $product_id = request()->get('product_id'); 

                            if (!empty($product_id)) {
                                if ($other_sale->product_id != $product_id) {
                                    continue; 
                                }
                            }

                            if(!empty($product)) {
                                $rows[] = [
                                    'transaction_date' => $row->transaction_date,
                                    'transaction_type' => 'Settlement - Other Sale',
                                    'invoice_no' => $row->settlement_no,
                                    'book_no' => '-',
                                    'product_code' => $product->sku,
                                    'product_name' => $product->name,
                                    'received_qty' => '0.00',
                                    'sold_qty' => number_format($other_sale->qty, 2),
                                    'balance_qty' => number_format(0 + $other_sale->qty, 2),
                                ];
                            }
                        }
                    }
                
                    return $rows; 
                }));  

                return $datatable->make(true);

    }

    /**
     * PRINT FORM21
     */

    public function printF21Form(Request $request) {
        $business_id = request()->session()->get('user.business_id');

        $data = array();
        parse_str($request->data, $data); // converting serielize string to array
       
        $details = $data;
        //$data = $data['f21c'];
        return view('mpcs::forms.21Form.print_f21_form')->with(compact('details'));
    }
    public function showLatest21cFormSettings()
    {
        // Get Fuel category and its subcategories
        $fuelCategoryId = Category::where('name', 'Fuel')->value('id');
    
        // Get fuel subcategories as id => name
        $fuelCategory = Category::where('parent_id', $fuelCategoryId)
            ->select(['id', 'name'])
            ->pluck('name', 'id');
    
        // Get the latest form setting record
        $latestForm = Mpcs21cFormSettings::latest()->first();
    
        $categoriesData = [];
    
        if ($latestForm && $latestForm->categories) {
            $categoriesData = json_decode($latestForm->categories, true);
        }
     
        return view('mpcs::forms.21CForm.list_f21c', compact('fuelCategory', 'categoriesData'));
    }
    

}