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
use Modules\MPCS\Entities\Mpcs20FormSettings;
use App\MergedSubCategory;
class F20FormController extends Controller
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
     // Check if user is authenticated
if (!auth()->check()) {
    return redirect()->route('login');
}

$business_id = session()->get('business.id');
$settings = Mpcs20FormSettings::first();

$credit_sale = $settings?->credit_sale ?? 0;
$cash_sale = $settings?->cash_sale ?? 0;
$starting_number = $settings?->starting_number ?? 1;

$form_type = $request->get('form_type') ?? 'default';
$form_date = $request->get('form_date') ?? now()->format('Y-m-d'); // ✅ read from request
$opening_date = $settings?->date ?? now()->format('Y-m-d');

$is_credit = $form_type === 'credit';

// ✅ Dynamic Form Number Logic
if ($form_date === $opening_date) {
    // Use base starting number from settings
    $F20_form_sn = match ($form_type) {
        'credit' => $credit_sale,
        'cash' => $cash_sale,
        default => $starting_number,
    };
} else {
    // Check if a form exists on this date already
    $last_form = \App\Models\Form20::where('form_type', $form_type)
        ->whereDate('date', $form_date)
        ->orderBy('form_no', 'desc')
        ->first();

    $base_number = match ($form_type) {
        'credit' => $credit_sale,
        'cash' => $cash_sale,
        default => $starting_number,
    };

    $F20_form_sn = $last_form ? $last_form->form_no + 1 : $base_number;
}

// Continue with existing logic...
$bname = Business::findOrFail($business_id);
$form_number = $settings->starting_number ?? "1";
$date = $settings->date ?? Carbon::now()->format('Y-m-d');
$userAdded = $bname->name;

$merged_sub_categories = MergedSubCategory::where('business_id', $business_id)->get();
$business_locations = BusinessLocation::forDropdown($business_id);

$business_details = $bname;
$currency_precision = (int) $business_details->currency_precision;
$qty_precision = (int) $business_details->quantity_precision;

// Get all subcategories
$sub_categories = Category::where('business_id', $business_id)->where('parent_id', '!=', 0)->get();

// Fuel categories
$fuelCategoryId = Category::where('name', 'Fuel')->value('id');
$fuelCategoryQuery = Category::where('parent_id', $fuelCategoryId)->select(['name', 'id']);
$fuelCategory = auth()->user()->can('superadmin')
    ? $fuelCategoryQuery->get()->pluck('name', 'id')
    : $fuelCategoryQuery->where('business_id', $business_id)->get()->pluck('name', 'id');

// Date range filters
$start_date = $request->start_date ?? now()->subMonth()->format('Y-m-d');
$end_date = $request->end_date ?? now()->format('Y-m-d');
$categories = [];
if (!empty($settings) && !empty($settings->category)) {
    $categories = explode(',', $settings->category);
}
// Query product transactions

// Query products and related data
    $products = Product::leftJoin('transaction_sell_lines', 'transaction_sell_lines.product_id', '=', 'products.id')
    ->leftJoin('transactions', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
    ->leftJoin('mpcs_20_form_settings', function ($join) {
        // Parse the category field and join only if the product's category_id matches
        $join->on(DB::raw("FIND_IN_SET(products.category_id, REPLACE(REPLACE(mpcs_20_form_settings.category, '(', ''), ')', ''))"), '>', DB::raw(0));
    })
    ->whereBetween('transactions.transaction_date', [$start_date, $end_date])
    ->where('products.business_id', $business_id)
    ->when($is_credit, function ($query) {
        return $query->where('is_credit_sale', true);
    })
    ->when(!empty($categories), function ($query) use ($categories) {
        return $query->whereIn('products.sub_category_id', $categories);
    })
    ->select(
        'products.id as product_id',
        'products.name as product_name',
        'products.sku as product_sku',
        'transaction_sell_lines.quantity as qty',
        'transaction_sell_lines.unit_price',
        'transactions.id as transaction_id',
        'transactions.transaction_date',
        'transactions.invoice_no',
        DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price) as total_amount')
    )
    ->groupBy('products.id', 'products.name', 'products.sku', 'transactions.id', 'transactions.transaction_date')
    ->get()
    ->groupBy('product_id');

// View layout
$layout = 'layouts.app';

return view('mpcs::forms.20Form.F20_form', compact(
    'F20_form_sn',
    'sub_categories',
    'fuelCategory',
    'currency_precision',
    'qty_precision',
    'merged_sub_categories',
    'business_locations',
    'settings',
    'form_number',
    'date',
    'userAdded',
    'layout',
    'products',
    'is_credit',
    
));

        }

        public function getForm20Data(Request $request)
        {
            $business_id = session()->get('business.id');
            $form_date_range = $request->input('form_date_range');
            $form_type = $request->get('form_type') ?? 'default';
        
            // Parse the date range
            $start_date = Carbon::parse($request->form_date_range)->format('Y-m-d');
            $end_date = Carbon::parse($request->form_date_range)->addDays(1)->format('Y-m-d');
        
            // Determine if it's a credit or cash sale
            $is_credit = $form_type === 'Credit';
        
            // Fetch settings for the business
            $settings = Mpcs20FormSettings::where('business_id', $business_id)->first();
            $opening_date = $settings?->opening_date ?? now()->format('Y-m-d');
            $form_date = $start_date;
        
            // Initialize the base number for the form
            $base_number = match ($form_type) {
                'credit' => $settings->credit_sale ?? 0,
                'cash' => $settings->cash_sale ?? 0,
                default => $settings->starting_number ?? 1,
            };
        
            // Calculate the adjusted form number based on the selected date
            $F20_form_sn = $this->calculateFormNumber($settings, $form_date, $form_type);
            $categories = [];
            if (!empty($settings) && !empty($settings->category)) {
                $categories = explode(',', $settings->category);
            }
            // Query products and related data
            $products = Product::leftJoin('transaction_sell_lines', 'transaction_sell_lines.product_id', '=', 'products.id')
                ->leftJoin('transactions', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
                ->whereBetween('transactions.transaction_date', [$start_date, $end_date])
                ->where('products.business_id', $business_id)
                ->when($is_credit, function ($query) {
                    return $query->where('is_credit_sale', true);
                })
                ->when(!empty($categories), function ($query) use ($categories) {
                    return $query->whereIn('products.sub_category_id', $categories);
                })
                ->select(
                    'products.id as product_id',
                    'products.name as product_name',
                    'products.sku as product_sku',
                    'transaction_sell_lines.quantity as qty',
                    'transaction_sell_lines.unit_price',
                    'transactions.invoice_no'
                )
                ->groupBy('products.id', 'products.name', 'products.sku', 'transactions.id','transactions.invoice_no')
                ->get()
                ->groupBy('product_id');
            
        
            // Prepare totals as an associative array
            $totals = [];
            foreach ($products as $productId => $productTransactions) {
                $totalQty = $productTransactions->sum('qty');
                $totalAmount = $productTransactions->sum(function ($item) {
                    return $item->qty * $item->unit_price;
                });
                $unitPrice = $totalQty > 0 ? $totalAmount / $totalQty : 0;
        
                $totals[$productId] = [
                    'qty' => $totalQty,
                    'unit_price' => $unitPrice,
                    'amount' => $totalAmount,
                ];
            }
           // dd(  $products );
            if(!($is_credit))
            {

                $F20_form_sn='';
            }
            // Return data as JSON
            return response()->json([
           'products' => $products->map(function ($transactions, $productId) use ($F20_form_sn) {
    return [
        'bill_no' => $F20_form_sn,
        'product_id' => $productId,
        'product_name' => $transactions->first()->product_name ?? '',
        'details' => $transactions->map(function ($item) {
            return [
                'settlement_no' => $item->invoice_no ?? '',
                'qty' => $item->qty,
                'unit_price' => $item->unit_price,
            ];
        })->values(),
    ];
})->values(), 'totals' => $totals,
            ]);
        }
        
        /**
         * Helper function to calculate the adjusted form number.
         */
        private function calculateFormNumber($settings, $form_date, $form_type)
        {
            if (!$settings) {
                return 1; // Default starting number if no settings exist
            }
        
            $cleanDate = trim($settings->opening_date);
            $selectedDate = trim($form_date);
        
            // Convert dates to timestamps
            $startTimestamp = strtotime(str_replace('/', '-', $cleanDate));
            $selectedTimestamp = strtotime(str_replace('/', '-', $selectedDate));
        
            $base_number = match ($form_type) {
                'credit' => $settings->credit_sale ?? 0,
                'cash' => $settings->cash_sale ?? 0,
                default => $settings->starting_number ?? 1,
            };
        
            if ($selectedTimestamp == $startTimestamp) {
                return $base_number;
            } elseif ($selectedTimestamp > $startTimestamp) {
                // Calculate day difference (in seconds) and convert to days
                $daysDiff = floor(($selectedTimestamp - $startTimestamp) / 86400);
                return $base_number + $daysDiff;
            } else {
                return 'N/A'; // Invalid date range
            }
        }
        
public function fetchFormNumber(Request $request)
{
     $business_id = session()->get('business.id');
    $settings = Mpcs20FormSettings::first();

    $form_type = $request->get('form_type') ?? 'default';
    $form_date = $request->get('form_date') ?? now()->format('Y-m-d');
    $opening_date = $settings?->opening_date ?? now()->format('Y-m-d');
    $F20_form_sn=0;



        // Check if a form exists on this date already
        $form = Mpcs20FormSettings::where('business_id', $business_id)
            ->first();
        $data = 0;
            if(!empty($form))
            {
        if ($form_type == 'Cash'){
            
           $cleanDate = trim($form->opening_date);
           $selectedDate = trim($form_date);

          // Convert dates to timestamps
           $startTimestamp = strtotime(str_replace('/', '-', $cleanDate));
           $selectedTimestamp = strtotime(str_replace('/', '-', $selectedDate));

          

          if ($selectedTimestamp == $startTimestamp) {
            $data = $form->cash_sale;
          } elseif ($selectedTimestamp > $startTimestamp) {
              // Calculate day difference (in seconds) and convert to days
            $daysDiff = floor(($selectedTimestamp - $startTimestamp) / 86400); 
            $data = $form->cash_sale + $daysDiff; 
          } else {
            $data = Carbon :: now()->format('Y-m-d');
            }
            
        } else if ($form_type == 'Credit') {
           
           $cleanDate = trim($form->opening_date);
           $selectedDate = trim($form_date);

          // Convert dates to timestamps
           $startTimestamp = strtotime(str_replace('/', '-', $cleanDate));
           $selectedTimestamp = strtotime(str_replace('/', '-', $selectedDate));

           

          if ($selectedTimestamp == $startTimestamp) {
            $data = $form->credit_sale;
          } elseif ($selectedTimestamp > $startTimestamp) {
              // Calculate day difference (in seconds) and convert to days
            $daysDiff = floor(($selectedTimestamp - $startTimestamp) / 86400);
            $data = $form->credit_sale + $daysDiff; 
          } else {
            $data = 'N/A';
            }
        } else {         
           
           $cleanDate = trim($form->opening_date);
           $selectedDate = trim($form_date);

          // Convert dates to timestamps
           $startTimestamp = strtotime(str_replace('/', '-', $cleanDate));
           $selectedTimestamp = strtotime(str_replace('/', '-', $selectedDate));

           $data = 0;

          if ($selectedTimestamp == $startTimestamp) {
            $data = $form->total_sale;
          } elseif ($selectedTimestamp > $startTimestamp) {
              // Calculate day difference (in seconds) and convert to days
            $daysDiff = floor(($selectedTimestamp - $startTimestamp) / 86400);
            $data = $form->total_sale + $daysDiff; 
          } else {
            $data = 'N/A';
            }
        
        }
    }
        $base_number = match ($form_type) {
            'credit' => $settings->credit_sale ?? 0,
            'cash' => $settings->cash_sale ?? 0,
            default => $settings->starting_number ?? 1,
        };

        $F20_form_sn = $form ? $data : $base_number;

    return response()->json(['form_number' => $F20_form_sn] );
}
    
    public function get20FormSettings() {

        $business_id = request()->session()->get('user.business_id');
        $pumps = [];
        if (auth()->user()->can('superadmin')) {
            $categories = Category::where('parent_id', '!=', 0)->select(['name', 'id'])
                ->get()
                ->pluck('name', 'id');
        
        } else {
            $categories = Category::where('parent_id', '!=', 0)->where('business_id', $business_id)
                ->select(['name', 'id'])
                ->get()
                ->pluck('name', 'id');
        }


        return view('mpcs::forms.20Form.create_20_form_settings', compact('categories', 'pumps'));

    }
  
    public function store20FormSettings(Request $request) {

        $business_id = session()->get('user.business_id');
        // dd($request->all());
         // Prepare the data for insertion
         $formData = [
            'business_id' => $business_id,
            'opening_date' => $request->date,
            'starting_number' => $request->starting_number,
            'total_sale' => $request->total_sale,
            'cash_sale' => $request->cash_sale,
            'credit_sale' => $request->credit_sale,
            'category' => $request->selected_categories,
            'created_by' => Auth::user()->id       
        ];
        

        if(Mpcs20FormSettings::where('business_id', $business_id)->where('opening_date', $request->date)->where('starting_number', $request->starting_number)->where('total_sale', $request->total_sale)->where('cash_sale', $request->cash_sale)->where('credit_sale', $request->credit_sale)->where('category', $request->selected_categories)->doesntExist()){
        // Insert into database
        Mpcs20FormSettings::create($formData);
        }

        $output = [
            'success' => 1,
            'msg' => __('mpcs::lang.form_16a_settings_add_success')
        ];

        return $output;
    }
   
    public function mpcs20FormSettings()
    {
        if (request()->ajax()) {
            $header = Mpcs20FormSettings::select('*');
            $business_id = request()->session()->get('user.business_id');
            return DataTables::of($header)
            ->addColumn('action', function ($row) {
                // if (auth()->user()->can('superadmin')) {
                    return '<button href="#" data-href="' . url('/mpcs/edit-20-form-settings/' . $row->id) . '" class="btn-modal btn btn-primary btn-xs" data-container=".update_form_16_a_settings_modal"><i class="fa fa-edit" aria-hidden="true"></i> ' . __("messages.edit") . '</button>';
                // }
                return '';
            })
            //  ->editColumn('starting_number', function($row) {
            //     return $row->starting_number;
            // })
            ->addColumn('opening_date', function ($row) {
                return $row->opening_date;
             })
            ->editColumn('total_sale', function($row) {
                return $row->total_sale;
            })
            ->editColumn('cash_sale', function($row) {
                return $row->cash_sale;
            })
            ->editColumn('credit_sale', function($row) {
                return $row->credit_sale;
            })
            ->editColumn('category', function($row) {
                $categoryId = explode(",", $row->category);
                $category = Category::whereIn('id', $categoryId)
                    ->select(['name', 'id'])
                    ->get();
                $html = '';
                foreach($category as $cat){
                    $html .= '<span class="badge badge-primary">'.$cat->name.'</span>';
                }
                return $html;
            })
            ->rawColumns(['action', 'opening_date', 'total_sale', 'cash_sale', 'credit_sale', 'category'])
            ->make(true);
        }

    }
    
    public function edit20FormSetting($id) {
        $business_id = request()->session()->get('user.business_id');
        $settings = Mpcs20FormSettings::where('business_id', $business_id)->where('id', $id)->first();
        $categoryId = explode(",", $settings->category);

        if(auth()->user()->can('superadmin')) {

            $categories = Category::where('parent_id', '!=', 0)->select(['name', 'id'])
            ->get()->pluck('name', 'id');

        } else {

            $categories = Category::where('parent_id', '!=', 0)->where('business_id', $business_id)
            ->select(['name', 'id'])
            ->get()->pluck('name', 'id');

        }    
        
        return view('mpcs::forms.20Form.edit_20_form_settings')->with(compact(
                    'categories',
                    'categoryId',
                    'settings'
        ));
    }
    
 
    public function mpcs20Update(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        // dd($request->all());
        $prev21cDet = Mpcs20FormSettings::find($id);
        if(Mpcs20FormSettings::where('id', $id)->exists()){
        Mpcs20FormSettings::destroy($id);

        $formData = [
            'business_id' => $business_id,
            'opening_date' => $request->date,
            'starting_number' => $request->starting_number,
            'total_sale' => $request->total_sale,
            'cash_sale' => $request->cash_sale,
            'credit_sale' => $request->credit_sale,
            'category' => $request->selected_categories,
            'created_by' => Auth::user()->id       
        ];
        
        Mpcs20FormSettings::create($formData);
        }

        $output = [
            'success' => 1,
            'msg' => __('mpcs::lang.form_21c_settings_update_success')
        ];

        return $output;
    }
    
    public function getFrom20Data()
    {
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $purchases = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->join(
                    'business_locations AS BS',
                    'transactions.location_id',
                    '=',
                    'BS.id'
                )
                ->leftJoin(
                    'transaction_payments AS TP',
                    'transactions.id',
                    '=',
                    'TP.transaction_id'
                )
                ->leftJoin(
                    'transactions AS PR',
                    'transactions.id',
                    '=',
                    'PR.return_parent_id'
                )
                ->leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
                ->leftjoin('variations', 'products.id', 'variations.product_id')
                ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->select(
                    'transactions.id',
                    'transactions.ref_no as reference_no',
                    'transaction_sell_lines.quantity as sold_qty',
                    'transaction_sell_lines.unit_price as unit_price',
                    'transactions.final_total as total_purchase_price',
                    'transactions.is_credit_sale',
                    'BS.name as location',
                    'products.sku as sku',
                    'products.name as product',
                    'products.id as product_id',
                    'TP.method as payment_method',
                    'variations.default_sell_price',
                    'transactions.pay_term_number',
                    'transactions.pay_term_type',
                    'PR.id as return_transaction_id',
                    DB::raw('SUM(TP.amount) as amount_paid'),
                    DB::raw('(SELECT SUM(TP2.amount) FROM transaction_payments AS TP2 WHERE
                        TP2.transaction_id=PR.id ) as return_paid'),
                    DB::raw('COUNT(PR.id) as return_exists'),
                    DB::raw('COALESCE(PR.final_total, 0) as amount_return'),
                    DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by")
                )
                ->groupBy('transactions.id');

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $purchases->whereIn('transactions.location_id', $permitted_locations);
            }

            if (!empty(request()->location_id)) {
                $purchases->where('transactions.location_id', request()->location_id);
            }

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $purchases->whereDate('transactions.transaction_date', '>=', $start)
                    ->whereDate('transactions.transaction_date', '<=', $end);
            }

            $business_id = session()->get('user.business_id');
            $business_details = Business::find($business_id);
    // dd($purchases->get());
            return DataTables::of($purchases)
                ->addIndexColumn()
                ->addColumn('total_amount', function ($row) use ($business_details) {
                    $total_amount = $row->sold_qty * $row->unit_price;
                    $html = '';
                    if ($row->payment_method == 'cash' || $row->payment_method == 'card' || $row->payment_method == 'cheque') {
                         $html .='<span class="display_currency cash_sale" data-orig-value="' . $total_amount . '" data-currency_symbol = "false">' . $this->productUtil->num_f($total_amount, false, $business_details, false) . '</span>';
                    }
                    if ($row->is_credit_sale == 1) {
                         $html .='<span class="display_currency credit_sale" data-orig-value="' . $total_amount . '" data-currency_symbol = "false">' . $this->productUtil->num_f($total_amount, false, $business_details, false) . '</span>';
                    }
                    return $html;
                })
                ->addColumn('unit_price', function ($row) use ($business_details) {
                    return $this->productUtil->num_f($row->unit_price, false, $business_details, false);
                })
                ->addColumn('sold_qty', function ($row) use ($business_details) {
                    return $this->productUtil->num_f($row->sold_qty, false, $business_details, true);
                })
                ->removeColumn('id')

                ->rawColumns(['total_amount', 'unit_price'])
                ->make(true);
        }
    }
}
