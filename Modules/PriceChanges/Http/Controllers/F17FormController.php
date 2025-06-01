<?php

namespace Modules\PriceChanges\Http\Controllers;

use App\Brands;
use App\Business;
use App\BusinessLocation;
use App\Category;
use App\Product;
use App\Store;
use App\Unit;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\PriceChanges\Entities\MpcsFormSetting;
use Yajra\DataTables\Facades\DataTables;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\PriceChanges\Entities\PriceChangesDetail;
use Modules\PriceChanges\Entities\PriceChangesHeader;
use Modules\PriceChanges\Entities\FormF17HeaderController;
use Modules\PriceChanges\Entities\FormF22Header;
use Modules\PriceChanges\Entities\PriceChangeSettings;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Models\Activity;

use App\Variation;

use App\Account;
use App\AccountTransaction;
use App\Transaction;
use App\AccountType;


class F17FormController extends Controller
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
    public function index()
    {
        $business_id = request()->session()->get('business.id');

        $settings = MpcsFormSetting::where('business_id', $business_id)->first();
        $count = PriceChangesHeader::where('business_id', $business_id)->count();
        if (!empty($settings)) {
            $F17_from_no = $settings->F17_form_sn + $count;
        } else {
            $F17_from_no = 1 + $count;
        }

        $stores = Store::where('business_id', $business_id)->pluck('name', 'id');
        $products = Product::where('business_id', $business_id)->forModule('pricechange_pricechanges')->pluck('name', 'id');

        $categories = Category::forDropdown($business_id,true);

        $brands = Brands::forDropdown($business_id);

        $units = Unit::forDropdown($business_id);

        $business_locations = BusinessLocation::forDropdown($business_id);

        $forms_nos = PriceChangesHeader::where('business_id', $business_id)->pluck('form_no', 'id');
        
        $query = Account::leftJoin('account_types', 'accounts.account_type_id', '=', 'account_types.id')->
            where('account_types.business_id',$business_id)->where('account_types.name','Expenses');
        
        $expenseAccounts = $query->pluck("accounts.name","accounts.id");
        $query = Account::leftJoin('account_types', 'accounts.account_type_id', '=', 'account_types.id')->
            where('account_types.business_id',$business_id)->where('account_types.name','Income');
        
        $incomeAccounts = $query->pluck("accounts.name","accounts.id");
        
        $users = User::forDropdown($business_id, true, true);
        
        $usersInChangeDetails = User::whereIn("id", function($query){
            $query->select("user")->from("price_changes_headers")->distinct();
        })->pluck("username","id");
        $subCategories = Category::where('business_id', $business_id)->whereNotNull('parent_id')->pluck('name','id');
        return view('pricechanges::forms.F17.index')->with(compact(
            'stores',
            'products',
            'categories',
            'brands',
            'units',
            'business_locations',
            'F17_from_no',
            'forms_nos',
            'incomeAccounts',
            'expenseAccounts',
            'users',
            'subCategories',
            'usersInChangeDetails'
        ));
    }
    
    public function getUnit()
{
    $units = Unit::where('category_id', request()->category_id)->get();
    return response()->json($units);
}
    
    public function getProduct()
    {
        $products = Product::where('category_id', request()->category_id)
        ->orWhere('unit_id', request()->unit_id)
        ->forModule('pricechange_pricechanges')
        ->get();
        return response()->json($products);
    }
    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
       
        
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            
            $products = Product::leftjoin('variations', 'products.id', 'variations.product_id')
                ->leftjoin('variation_location_details as vld', 'variations.id', 'vld.variation_id')
                ->leftJoin('categories as c1', 'products.category_id', '=', 'c1.id')
                ->where('products.business_id', $business_id)
                ->select(
                    'products.id as p_id',
                    'products.name as product',
                    'products.sku as sku',
                    'c1.name as category',
                    'variations.default_sell_price as unit_price',
                    'vld.qty_available as current_stock',
                    'variations.sell_price_inc_tax as current_sale_price',
                    'variations.dpp_inc_tax as unit_purchase_price'
                )->forModule('pricechange_pricechanges');

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $products->whereIn('vld.location_id', $permitted_locations);
            }

            if (!empty(request()->location_id)) {
                $products->where('vld.location_id', request()->location_id);
            }
            if (!empty(request()->category_id)) {
                $products->where('products.category_id', request()->category_id);
            }
            if (!empty(request()->unit_id)) {
                $products->where('products.unit_id', request()->unit_id);
            }
            if (!empty(request()->brand_id)) {
                $products->where('products.brand_id', request()->brand_id);
            }
            
            if (!empty(request()->product_id)) {
                $products->where('products.id', request()->product_id);
            }


            $business_id = session()->get('user.business_id');
            $business_details = Business::find($business_id);

            return DataTables::of($products)
                ->addIndexColumn()
                ->editColumn('product', function ($row) use ($business_details) {
                    return '<span>' . $row->product . '</span><input type="hidden" value="' . $row->product . '" name="F17[' . $row->p_id . '][product]" id="F17[' . $row->p_id . '][product]">';
                })
                ->editColumn('sku', function ($row) use ($business_details) {
                    return '<span>' . $row->sku . '</span><input type="hidden" value="' . $row->sku . '" name="F17[' . $row->p_id . '][sku]" id="F17[' . $row->p_id . '][sku]">';
                })
                ->editColumn('unit_price', function ($row) use ($business_details) {
                    return '<span class="display_currency unit_price" data-orig-value="' . $row->unit_purchase_price . '">' . $row->unit_purchase_price . '</span><input type="hidden" value="' . $row->unit_purchase_price . '" name="F17[' . $row->p_id . '][unit_price]" id="F17[' . $row->p_id . '][unit_price]">';
                })
                ->editColumn('current_sale_price', function ($row) use ($business_details) {
                    return '<span class="display_currency current_sale_price" data-orig-value="' . $row->current_sale_price . '" data-currency_symbol = "false">' . $this->productUtil->num_f($row->current_sale_price,false,null,true) . '</span><input type="hidden" value="' . $row->current_sale_price . '" name="F17[' . $row->p_id . '][current_sale_price]" id="F17[' . $row->p_id . '][current_sale_price]">';
                })
                ->editColumn('current_stock', function ($row) use ($business_details) {
                    $stock = $row->current_stock;
                    
                    if ($row->category == 'Fuel') {
                        $stock = $this->transactionUtil->getTankProductBalanceByProductId($row->p_id);
                    }
                    
                    
                    return '<span  class="current_stock" data-orig-value="' . $stock . '">' . $stock . '</span><input type="hidden" value="' . $this->productUtil->num_f($stock, false, $business_details, true) . '" name="F17[' . $row->p_id . '][current_stock]" id="F17[' . $row->p_id . '][current_stock]">';
                })
                ->addColumn('select_mode', function ($row) use ($business_details) {
                    $html = '<select name="F17[' . $row->p_id . '][select_mode]" id="F17[' . $row->p_id . '][select_mode]" class="form-control select_mode input_number" placeholder="Please Select">
                        <option value="increase">Increase</option>
                        <option value="decrease">Decrease</option>
                    </select>';
                    return $html;
                })
                ->addColumn('unit_price_difference', function ($row) use ($business_details) {
                    return '<span class="display_currency unit_price_difference" data-orig-value="' . $row->unit_price_difference . '" data-currency_symbol = "false"></span><input type="hidden" name="F17[' . $row->p_id . '][unit_price_difference]" id="F17[' . $row->p_id . '][unit_price_difference]" class="unit_price_difference_value">';
                })
                ->addColumn('new_price', function ($row) {
                    return '<input type="text" style="width: 60px;" name="F17[' . $row->p_id . '][new_price]" id="F17[' . $row->p_id . '][new_price]" class="form-control input_number new_price_value">';
                })
                ->addColumn('price_changed_loss', function ($row) {
                    return '<span class="display_currency price_changed_loss" data-orig-value="" data-currency_symbol = "false"></span><input type="hidden" name="F17[' . $row->p_id . '][price_changed_loss]" id="F17[' . $row->p_id . '][price_changed_loss]" class="price_changed_loss_value">';
                })
                ->addColumn('price_changed_gain', function ($row) {
                    return '<span class="display_currency price_changed_gain" data-orig-value="" data-currency_symbol = "false"></span><input type="hidden" name="F17[' . $row->p_id . '][price_changed_gain]" id="F17[' . $row->p_id . '][price_changed_gain]" class="price_changed_gain_value">';
                })
                ->addColumn('page_no', function ($row) {
                    return '<input type="text" style="width: 60px;" name="F17[' . $row->p_id . '][page_no]" id="F17[' . $row->p_id . '][page_no]" class="form-control input_number page_no">';
                })
                ->addColumn('new_sale_price', function ($row) {
                    return '<input type="text" style="width: 60px;" name="F17[' . $row->p_id . '][new_sale_price]" id="F17[' . $row->p_id . '][new_sale_price]" class="form-control input_number new_sale_price_value">';
                })
                ->addColumn('total_sale_difference', function ($row) use ($business_details) {
                    return '<span class="display_currency total_sale_difference" data-orig-value="0" data-currency_symbol = "false"></span><input readonly type="hidden" name="F17[' . $row->p_id . '][total_sale_difference]" id="F17[' . $row->p_id . '][total_sale_difference]" class="total_sale_difference_value" style="border:none">';
                })
                ->removeColumn('id')

                ->rawColumns(['sku', 'unit_price', 'current_stock', 'product', 'select_mode', 'unit_price_difference', 'new_price', 'new_sale_price','current_sale_price', 'price_changed_loss', 'price_changed_gain', 'page_no','total_sale_difference'])
                ->make(true);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $data = array();
            parse_str($request->data, $data); // converting serielize string to array

            $data_array = $data['F17'];
            

            $header_data = array(
                'business_id' => $business_id,
                'date' => !empty($request->date) ? \Carbon::parse($request->date)->format('Y-m-d') : null,
                'form_no' => $request->form_no,
                'location_id' => $request->location_id,
                'store_id' => $request->store_id,
                'category_id' => $request->category_id,
                'unit_id' => $request->unit_id,
                'brand_id' => $request->brand_id,
                'total_price_change_loss' => $request->total_price_change_loss,
                'total_price_change_gain' => $request->total_price_change_gain,
                'page_no' => $request->page_no,
                'user' => Auth::user()->id
            );
            
            DB::beginTransaction();
            $header = PriceChangesHeader::create($header_data);

            $total_price_change_loss = 0;
            $total_price_change_gain = 0;
            foreach ($data_array as $key => $item) {
                $array_details = array(
                    'header_id' => $header->id,
                    'product_id' => $key,
                    'sku' => $item['sku'],
                    'product' => $item['product'],
                    'current_stock' => $item['current_stock'],
                    'unit_price' => $item['unit_price'],
                    'select_mode' => "",
                    'new_price' => $item['new_price'],
                    'unit_price_difference' => $item['unit_price_difference'],
                    'price_changed_loss' => $item['price_changed_loss'],
                    'price_changed_gain' => $item['price_changed_gain'],
                    'page_no' => "",
                );
                
                
                $fdetail = PriceChangesDetail::create($array_details);
                $total_price_change_loss += !empty($item['price_changed_loss']) ? $item['price_changed_loss'] : 0;
                $total_price_change_gain += !empty($item['price_changed_gain']) ? $item['price_changed_gain'] : 0;
                
                
                
                // store transactions
              
                if($item['unit_price_difference'] > 0 || $item['unit_price_difference'] < 0){

                
                    $product = Product::findOrFail($key);
                    $category = Category::findOrFail($product->sub_category_id);
                    
                    if($category->remaining_stock_adjusts == "Yes"){
                        $ssdd = 31 ;
                        if(($item['price_changed_gain'] > 0 && $category->price_increment_acc > 0) || ($item['price_changed_loss'] < 0 && $category->price_reduction_acc > 0)){
                           
                            if($item['price_changed_gain'] > 0){
                                $type = "price_change_increase";
                                $account = $category->price_increment_acc;
                                $amount = $item['price_changed_gain'];
                                
                                $t1 = "credit";
                                $t2 = "debit";
                                
                            }elseif($item['price_changed_loss'] < 0){
                                $type = "price_change_decrease";
                                $account = $category->price_reduction_acc;
                                $amount = -1* ((int) $item['price_changed_loss']);
                                
                                $t2 = "credit";
                                $t1 = "debit";
                                
                            }else{
                                continue;
                            }
                            
                            if($amount == 0){
                                continue;
                            }
                            
                            
                            $ob_data = [
                                'business_id' => $business_id,
                                'location_id' => $request->location_id,
                                'type' => $type,
                                'status' => 'final',
                                'payment_status' => 'paid',
                                'transaction_date' => !empty($request->date) ? \Carbon::parse($request->date)->format('Y-m-d') : date('Y-m-d'),
                                'total_before_tax' => $amount,
                                'final_total' => $amount,
                                'created_by' => request()->session()->get('user.id')
                            ];
                            
                            $ob_data['ref_no'] = $fdetail->id;
                            
                            $transaction = Transaction::create($ob_data);
                            
                            
                            $credit_data = [
                                'amount' => $amount,
                                'account_id' => $account,
                                'transaction_id' => $transaction->id,
                                'type' => $t1,
                                'sub_type' => null,
                                'operation_date' => $ob_data['transaction_date'],
                                'created_by' => session()->get('user.id'),
                                'note' => null,
                                'attachment' => null
                            ];
                            $credit = AccountTransaction::createAccountTransaction($credit_data);
                            
                            $fga = $this->transactionUtil->account_exist_return_id('Finished Goods Account');
                            
                            $debit_data = [
                                'amount' => $amount,
                                'account_id' => $fga,
                                'transaction_id' => $transaction->id,
                                'type' => $t2,
                                'sub_type' => null,
                                'operation_date' => $ob_data['transaction_date'],
                                'created_by' => session()->get('user.id'),
                                'note' => null,
                                'attachment' => null
                            ];
                            
                            
                            $credit = AccountTransaction::createAccountTransaction($debit_data);
                            
                            
                           

                            //print_r($variation);

                            
                            
                    
                        }
                    }


                     // adjust prices:
                     $variation = Variation::where('product_id',$key)->first();
                     if(!empty($variation)){
                         
                         $sp = $item['new_sale_price'];
                         $dpp = $item['new_price'];
                         $pp = ((100 - $variation->profit_percent) * ((int)$item['new_price']))/100;
                         
                         //$variation->default_purchase_price = $pp;
                         $variation->dpp_inc_tax = $dpp;
                        // $variation->default_sell_price = $sp;
                         $variation->sell_price_inc_tax = $sp;
                         
                         $variation->save();
                     }
                    
                }
                
                
            }
            $header->total_price_change_loss = $total_price_change_loss;
            $header->total_price_change_gain = $total_price_change_gain;
            $header->save();
            DB::commit();
            $output = [
                'success' => 1,
                'msg' => __('pricechanges::lang.success'),
                
                
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
                'mesage'=> 'File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage()
            ];
        }

        return $output;
    }
    
    /**
     * Price Changed Details resource
     * @return Response
     */ 
    public function details() {
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $products = PriceChangesHeader::leftjoin('categories', 'price_changes_headers.category_id', 'categories.id')
                ->leftjoin('categories as sub_cat', 'price_changes_headers.sub_category_id', 'sub_cat.id')
                ->leftjoin('users', 'price_changes_headers.user', 'users.id')
                ->leftjoin('price_changes_details', 'price_changes_headers.id','price_changes_details.header_id')
                ->leftjoin('products', 'price_changes_details.product_id', 'products.id')
                ->leftjoin('categories as cto', 'products.category_id', 'cto.id')
                ->leftjoin('categories as scto', 'products.sub_category_id', 'scto.id')
                ->where('price_changes_headers.business_id', $business_id)
                ->select(
                    'price_changes_headers.date as date',
                    'price_changes_headers.total_price_change_loss as total_loss',
                    'price_changes_headers.total_price_change_gain as total_gain',
                    'price_changes_headers.form_no as form_no',
                    'cto.name as category',
                    'cto.id as categoryId',
                    'scto.name as sub_category',
                    'scto.id as sub_category_id',
                    'users.username as user',
                    'price_changes_details.unit_price as current_price',
                    'price_changes_details.new_price as new_price',
                    'price_changes_details.current_stock as quantity'
                )->groupBy('form_no')->get();

            
            if (!empty(request()->category_id)) {
                $products->where('products.category_id', request()->category_id);
            }
            if (!empty(request()->subcategory_id)) {
                $products->where('products.sub_category_id', request()->subcategory_id);
            }
            if (!empty(request()->user_id)) {
                Log::info("Its has".request()->user_id);
                $products->where('price_changes_headers.user', request()->user_id);
            }
            if (!empty(request()->product_id)) {
                $products->where('price_changes_details.product_id', request()->product_id);
            }

            $start_date = \Carbon::parse(request()->start_date)->format('Y-m-d');
            $end_date = \Carbon::parse(request()->end_date)->format('Y-m-d');

            if (!empty($start_date) && !empty($end_date)) {
                $products->whereBetween('price_changes_headers.date', [request()->start_date, request()->end_date]);
            }
            
            Log::info(request()->product_id . " => ". request()->user_id);

            return DataTables::of($products)
                ->make(true);
        }
    } 

    /**
     * list the specified resource.
     * @return Response
     */
    public function list()
    {
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $products = PriceChangesHeader::leftjoin('categories', 'price_changes_headers.category_id', 'categories.id')
                ->leftjoin('categories as sub_cat', 'price_changes_headers.sub_category_id', 'sub_cat.id')
                ->leftjoin('business_locations', 'price_changes_headers.location_id', 'business_locations.id')
                ->leftjoin('units', 'price_changes_headers.unit_id', 'units.id')
                ->leftjoin('brands', 'price_changes_headers.brand_id', 'brands.id')
                ->leftjoin('users', 'price_changes_headers.user', 'users.id')
                ->where('price_changes_headers.business_id', $business_id)
                ->select(
                    'price_changes_headers.*',
                    'categories.name as category',
                    'sub_cat.name as sub_category',
                    'business_locations.name as location',
                    'units.actual_name as unit',
                    'brands.name as brands',
                    'users.username'
                );

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $products->whereIn('price_changes_headers.location_id', $permitted_locations);
            }

            if (!empty(request()->location_id)) {
                $products->where('price_changes_headers.location_id', request()->location_id);
            }
            if (!empty(request()->category_id)) {
                $products->where('price_changes_headers.category_id', request()->category_id);
            }
            if (!empty(request()->unit_id)) {
                $products->where('price_changes_headers.unit_id', request()->unit_id);
            }
            if (!empty(request()->brand_id)) {
                $products->where('price_changes_headers.brand_id', request()->brand_id);
            }

            $start_date = \Carbon::parse(request()->start_date)->format('Y-m-d');
            $end_date = \Carbon::parse(request()->end_date)->format('Y-m-d');

            if (!empty($start_date) && !empty($end_date)) {
                $products->whereBetween('price_changes_headers.date', [request()->start_date, request()->end_date]);
            }


            $business_id = session()->get('user.business_id');
            $business_details = Business::find($business_id);

            return DataTables::of($products)
                ->addColumn('action', function ($row) use ($business_details) {
                    $html = '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                    if (auth()->user()->can("edit_f17_form")) {
                        $html .= '<li><a target="_blank" href="' . action('\Modules\PriceChanges\Http\Controllers\F17FormController@edit', [$row->id]) . '"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                    }
                    
                    $html .= '<li><a target="_blank" href="' . action('\Modules\PriceChanges\Http\Controllers\F17FormController@show', [$row->id]) . '"><i class="fa fa-eye"></i> ' . __("messages.view") . '</a></li>';

                    $html .= '</ul></div>';

                    return $html;
                })
                ->addColumn('select_mode', function ($row) use ($business_details) {
                    return '';
                })
                ->addColumn('store', function ($row) use ($business_details) {
                    return '';
                })
                ->editColumn('total_price_change_loss','{{@num_format(abs($total_price_change_loss))}}')
                ->editColumn('total_price_change_gain','{{@num_format($total_price_change_gain)}}')
                ->removeColumn('id')

                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id)
    {
        $business_id = session()->get('user.business_id');
        if (request()->ajax()) {
            
            $form = PriceChangesHeader::leftjoin('price_changes_details', 'price_changes_headers.id', 'price_changes_details.header_id')
            ->where('price_changes_headers.id', $id)
            ->where(function($query){
                $query->where('price_changed_loss', '!=', 0)
                    ->orWhere('price_changed_gain', '!=', 0);
            })
            ->select('price_changes_headers.*', 'price_changes_details.*', 'price_changes_details.id as detial_id');
 
            $business_details = Business::find($business_id);
            

            return DataTables::of($form)
                ->addIndexColumn()
                ->editColumn('product', function ($row) use ($business_details) {
                    return '<span>' . $row->product . '</span><input type="hidden" value="' . $row->product . '" name="F17[' . $row->detial_id . '][product]" id="F17[' . $row->detial_id . '][product]">';
                })
                ->editColumn('sku', function ($row) use ($business_details) {
                    return '<span>' . $row->sku . '</span><input type="hidden" value="' . $row->sku . '" name="F17[' . $row->detial_id . '][sku]" id="F17[' . $row->detial_id . '][sku]">';
                })
                ->editColumn('unit_price', function ($row) use ($business_details) {
                    return '<span class="display_currency unit_price" data-orig-value="' . $row->unit_price . '">' . $row->unit_price . '</span><input type="hidden" value="' . $row->unit_price . '" name="F17[' . $row->detial_id . '][unit_price]" id="F17[' . $row->detial_id . '][unit_price]">';
                })
                ->editColumn('current_stock', function ($row) use ($business_details) {
                    return '<span  class="current_stock" data-orig-value="' . $row->current_stock . '">' . $row->current_stock . '</span><input type="hidden" value="' . $this->productUtil->num_f($row->current_stock, false, $business_details, true) . '" name="F17[' . $row->detial_id . '][current_stock]" id="F17[' . $row->detial_id . '][current_stock]">';
                })
                ->addColumn('select_mode', function ($row) use ($business_details) {
                    $increase = ''; $decrease = '';
                    if($row->select_mode == 'increase'){
                        $increase = 'selected';
                    }else{
                        $decrease = 'selected';
                    }
                    $html = '<select name="F17[' . $row->detial_id . '][select_mode]" id="F17[' . $row->detial_id . '][select_mode]" class="form-control select_mode" disabled placeholder="Please Select">
                        <option '.$increase.' value="increase">Increase</option>
                        <option '.$decrease.' value="decrease">Decrease</option>
                    </select>';
                    return $html;
                })
                ->addColumn('unit_price_difference', function ($row) use ($business_details) {
                    return '<span class="display_currency unit_price_difference" data-orig-value="' . $row->unit_price_difference . '" data-currency_symbol = "false">' . $this->productUtil->num_f($row->unit_price_difference, false, $business_details, false) . '</span><input type="hidden" name="F17[' . $row->detial_id . '][unit_price_difference]" id="F17[' . $row->detial_id . '][unit_price_difference]" class="unit_price_difference_value">';
                })
                ->addColumn('new_price', function ($row) {
                    return '<input type="text" disabled style="width: 60px;" name="F17[' . $row->detial_id . '][new_price]" id="F17[' . $row->detial_id . '][new_price]" value="'.$row->new_price.'" class="form-control input_number new_price_value">';
                })
                ->addColumn('price_changed_loss', function ($row) use ($business_details) {
                    return '<span class="display_currency price_changed_loss" data-orig-value="'.$row->price_changed_loss.'" data-currency_symbol = "false">'. $this->productUtil->num_f($row->price_changed_loss, false, $business_details, false).'</span><input type="hidden" name="F17[' . $row->detial_id . '][price_changed_loss]" id="F17[' . $row->detial_id . '][price_changed_loss]" value="'.$row->price_changed_loss.'" class="price_changed_loss_value">';
                })
                ->addColumn('price_changed_gain', function ($row) use ($business_details) {
                    return '<span class="display_currency price_changed_gain" data-orig-value="'.$row->price_changed_gain.'" data-currency_symbol = "false">'.  $this->productUtil->num_f($row->price_changed_gain, false, $business_details, false) .'</span><input type="hidden" name="F17[' . $row->detial_id . '][price_changed_gain]" id="F17[' . $row->detial_id . '][price_changed_gain]" value="'.$row->price_changed_gain.'" class="price_changed_gain_value">';
                })
                ->addColumn('page_no', function ($row) {
                    return '<input value="'.$row->page_no.'" type="text" style="width: 60px;" name="F17[' . $row->detial_id . '][page_no]" id="F17[' . $row->detial_id . '][page_no]" class="form-control input_number page_no">';
                })
                ->addColumn('signature', function ($row) {
                    return '';
                })
                ->removeColumn('id')

                ->rawColumns(['sku', 'unit_price', 'current_stock', 'product', 'select_mode', 'unit_price_difference', 'new_price', 'price_changed_loss', 'price_changed_gain', 'page_no'])
                ->make(true);
        }

        return view('pricechanges::forms.F17.show')->with(compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
     
        $business_id = session()->get('user.business_id');
        if (request()->ajax()) {
            
            $form = PriceChangesHeader::leftjoin('price_changes_details', 'price_changes_headers.id', 'price_changes_details.header_id')
            ->where('price_changes_headers.id', $id)
            ->where(function($query){
                $query->where('price_changed_loss', '!=', 0)
                    ->orWhere('price_changed_gain', '!=', 0);
            })
            ->select('price_changes_headers.*', 'price_changes_details.*', 'price_changes_details.id as detial_id');
            
            $business_details = Business::find($business_id);   

           
            
            return DataTables::of($form)
                ->addIndexColumn()
                ->editColumn('product', function ($row) use ($business_details) {
                    return '<span>' . $row->product . '</span><input type="hidden" value="' . $row->product . '" name="F17[' . $row->detial_id . '][product]" id="F17[' . $row->detial_id . '][product]">';
                })
                ->editColumn('sku', function ($row) use ($business_details) {
                    return '<span>' . $row->sku . '</span><input type="hidden" value="' . $row->sku . '" name="F17[' . $row->detial_id . '][sku]" id="F17[' . $row->detial_id . '][sku]">';
                })
                ->editColumn('unit_price', function ($row) use ($business_details) {
                    return '<span class="display_currency unit_price" data-orig-value="' . $row->unit_price . '">' . $row->unit_price . '</span><input type="hidden" value="' . $row->unit_price . '" name="F17[' . $row->detial_id . '][unit_price]" id="F17[' . $row->detial_id . '][unit_price]">';
                })
                ->editColumn('current_stock', function ($row) use ($business_details) {
                    return '<span  class="current_stock" data-orig-value="' . $row->current_stock . '">' . $row->current_stock . '</span><input type="hidden" value="' . $this->productUtil->num_f($row->current_stock, false, $business_details, true) . '" name="F17[' . $row->detial_id . '][current_stock]" id="F17[' . $row->detial_id . '][current_stock]">';
                })
                ->addColumn('select_mode', function ($row) use ($business_details) {
                    $increase = ''; $decrease = '';
                    if($row->select_mode == 'increase'){
                        $increase = 'selected';
                    }else{
                        $decrease = 'selected';
                    }
                    $html = '<select name="F17[' . $row->detial_id . '][select_mode]" id="F17[' . $row->detial_id . '][select_mode]" class="form-control select_mode" placeholder="Please Select">
                        <option '.$increase.' value="increase">Increase</option>
                        <option '.$decrease.' value="decrease">Decrease</option>
                    </select>';
                    return $html;
                })
                ->addColumn('unit_price_difference', function ($row) use ($business_details) {
                    return '<span class="display_currency unit_price_difference" data-orig-value="' . $row->unit_price_difference . '" data-currency_symbol = "false">' . $this->productUtil->num_f($row->unit_price_difference, false, $business_details, false) . '</span><input type="hidden" name="F17[' . $row->detial_id . '][unit_price_difference]" id="F17[' . $row->detial_id . '][unit_price_difference]" class="unit_price_difference_value">';
                })
                ->addColumn('new_price', function ($row) {
                    return '<input type="text" style="width: 60px;" name="F17[' . $row->detial_id . '][new_price]" id="F17[' . $row->detial_id . '][new_price]" value="'.$row->new_price.'" class="form-control input_number new_price_value">';
                })
                ->addColumn('price_changed_loss', function ($row) use ($business_details) {
                    return '<span class="display_currency price_changed_loss" data-orig-value="'.$row->price_changed_loss.'" data-currency_symbol = "false">'. $this->productUtil->num_f($row->price_changed_loss, false, $business_details, false).'</span><input type="hidden" name="F17[' . $row->detial_id . '][price_changed_loss]" id="F17[' . $row->detial_id . '][price_changed_loss]" value="'.$row->price_changed_loss.'" class="price_changed_loss_value">';
                })
                ->addColumn('price_changed_gain', function ($row) use ($business_details) {
                    return '<span class="display_currency price_changed_gain" data-orig-value="'.$row->price_changed_gain.'" data-currency_symbol = "false">'.  $this->productUtil->num_f($row->price_changed_gain, false, $business_details, false) .'</span><input type="hidden" name="F17[' . $row->detial_id . '][price_changed_gain]" id="F17[' . $row->detial_id . '][price_changed_gain]" value="'.$row->price_changed_gain.'" class="price_changed_gain_value">';
                })
                ->addColumn('page_no', function ($row) {
                    return '<input value="'.$row->page_no.'" type="text" style="width: 60px;" name="F17[' . $row->detial_id . '][page_no]" id="F17[' . $row->detial_id . '][page_no]" class="form-control input_number page_no">';
                })
                ->addColumn('signature', function ($row) {
                    return '';
                })
                ->removeColumn('id')

                ->rawColumns(['sku', 'unit_price', 'current_stock', 'product', 'select_mode', 'unit_price_difference', 'new_price', 'price_changed_loss', 'price_changed_gain', 'page_no'])
                ->make(true);

                
        }

        $price_change = PriceChangesHeader::leftjoin('price_changes_details', 'price_changes_headers.id', 'price_changes_details.header_id')
        ->where('price_changes_headers.id', $id)
        ->firstOrFail();


        $settings = MpcsFormSetting::where('business_id', $business_id)->first();
        $count = PriceChangesHeader::where('business_id', $business_id)->count();
        if (!empty($settings)) {
            $F17_from_no =  $count;
        } else {
            $F17_from_no = 1 + $count;
        }

        $stores = Store::where('business_id', $business_id)->pluck('name', 'id');
        $products = Product::where('business_id', $business_id)->forModule('pricechange_pricechanges')->pluck('name', 'id');

        $categories = Category::forDropdown($business_id,true);

        $brands = Brands::forDropdown($business_id);

        $units = Unit::forDropdown($business_id);

        $business_locations = BusinessLocation::forDropdown($business_id);

        $forms_nos = PriceChangesHeader::where('business_id', $business_id)->pluck('form_no', 'id');
        
        $query = Account::leftJoin('account_types', 'accounts.account_type_id', '=', 'account_types.id')->
            where('account_types.business_id',$business_id)->where('account_types.name','Expenses');
        
        $expenseAccounts = $query->pluck("accounts.name","accounts.id");
        $query = Account::leftJoin('account_types', 'accounts.account_type_id', '=', 'account_types.id')->
            where('account_types.business_id',$business_id)->where('account_types.name','Income');
        
        $incomeAccounts = $query->pluck("accounts.name","accounts.id");
        
        $users = User::forDropdown($business_id, true, true);
        
        $usersInChangeDetails = User::whereIn("id", function($query){
            $query->select("user")->from("price_changes_headers")->distinct();
        })->pluck("username","id");
        $subCategories = Category::where('business_id', $business_id)->whereNotNull('parent_id')->pluck('name','id');
       

        return view('pricechanges::forms.F17.edit')->with(
        compact(
            'id',
            'price_change',
            'stores',
            'products',
            'categories',
            'brands',
            'units',
            'business_locations',
            'F17_from_no',
            'forms_nos',
            'incomeAccounts',
            'expenseAccounts',
            'users',
            'subCategories',
            'usersInChangeDetails'
        ));

        
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update($id, Request $request)
    {
        $business_id = session()->get('user.business_id');

        try {
            $data = array();
            parse_str($request->data, $data); // converting serielize string to array

            $data_array = $data['F17'];

            $header = PriceChangesHeader::findOrFail($id);

            DB::beginTransaction();
            $total_price_change_loss = 0;
            $total_price_change_gain = 0;
            foreach ($data_array as $key => $item) {
                $array_details = array(
                    'sku' => $item['sku'],
                    'product' => $item['product'],
                    'current_stock' => $item['current_stock'],
                    'unit_price' => $item['unit_price'],
                    'select_mode' => $item['select_mode'],
                    'new_price' => $item['new_price'],
                    'unit_price_difference' => $item['unit_price_difference'],
                    'price_changed_loss' => $item['price_changed_loss'],
                    'price_changed_gain' => $item['price_changed_gain'],
                    'page_no' => $item['page_no'],
                );

                PriceChangesDetail::where('id', $key)->update($array_details);
                $total_price_change_loss += !empty($item['price_changed_loss']) ? $item['price_changed_loss'] : 0;
                $total_price_change_gain += !empty($item['price_changed_gain']) ? $item['price_changed_gain'] : 0;
            }
            $header->total_price_change_loss = $total_price_change_loss;
            $header->total_price_change_gain = $total_price_change_gain;
            $header->save();
            DB::commit();
            $output = [
                'success' => 1,
                'msg' => __('pricechanges::lang.success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
    

    
    public function savePriceChangeSettings(Request $request) {
        $business_id = session()->get('user.business_id');
        DB::table('price_change_settings')->insert([
            'gain_account_id' => $request->price_gain_account_id,//linked to accounts table
            'loss_account_id' => $request->price_loss_account_id,
            'date' => \Carbon::parse($request->date)->format('Y-m-d'),
            'business_id' => $business_id,
            'user' =>  Auth::user()->id
        ]);
        
        $output = [
                'success' => 1,
                'msg' => __('pricechanges::lang.success')
            ];
            
        return $output;    
        
    }
    
    public function editPriceChangeSettings(Request $request) {
        //$business_id = session()->get('user.business_id');
        
        
        $setting = PriceChangeSettings::find($request->setting_id);
        $originalAttributes = $setting->getOriginal();
        $original_name = Account::find($originalAttributes["gain_account_id"]);
        $original_name_1 = Account::find($originalAttributes["loss_account_id"]);
        $orig1 = $setting->gain_account_id;
        $orig2 = $setting->loss_account_id;
        $setting->gain_account_id = $request->income_account_id_edit;
        $setting->loss_account_id = $request->expense_account_id_edit;
        $setting->updated_by = Auth::user()->id; 
        
        $setting->save();
        
        if($originalAttributes["gain_account_id"] != $setting->gain_account_id) {
            Activity::create([
                'log_name' => 'Form F17 Price Change Details',
                'description' => $original_name->name ." Changed to => ". $setting->gainAccount->name,
                'subject_type' => get_class($setting),
                'subject_id' => $setting->id,
                'causer_id' => Auth::user()->id,
                'properties' => [
                    'original' => $originalAttributes,
                    'updated' => $setting->getAttributes(),
                ],
            ]);
        }
        if($originalAttributes["loss_account_id"] != $setting->loss_account_id) {
            Activity::create([
                'log_name' => 'Form F17 Price Change Details',
                'description' =>  $original_name_1->name . " Changed to => ". $setting->lossAccount->name,
                'subject_type' => get_class($setting),
                'subject_id' => $setting->id,
                'causer_id' => Auth::user()->id,
                'properties' => [
                    'original' => $originalAttributes,
                    'updated' => $setting->getAttributes(),
                ],
            ]);
        }
        
          
        $output = [
                'success' => 1,
                'msg' => __('pricechanges::lang.success')
            ];
        
        return $output;    
    }
    
    public function editForm($id) {
        $business_id = session()->get('user.business_id');
        $setting = PriceChangeSettings::find($id);
        
        $query = Account::leftJoin('account_types', 'accounts.account_type_id', '=', 'account_types.id')->
            where('account_types.business_id',$business_id)->where('account_types.name','Expenses');
        
        $expenseAccounts = $query->pluck("accounts.name","accounts.id");
        $query = Account::leftJoin('account_types', 'accounts.account_type_id', '=', 'account_types.id')->
            where('account_types.business_id',$business_id)->where('account_types.name','Income');
        
        $incomeAccounts = $query->pluck("accounts.name","accounts.id");
        
        return view('pricechanges::forms.F17.partials.edit_price_settings')->with(compact('setting','incomeAccounts','expenseAccounts'));
    }
    
    public function listPriceChangeSettings() {
        $price_change_settings = DB::table('price_change_settings')
        ->leftJoin('accounts as gain_account', 'price_change_settings.gain_account_id', '=', 'gain_account.id')
        ->leftJoin('accounts as loss_account', 'price_change_settings.loss_account_id', '=', 'loss_account.id')
        ->leftJoin('users' ,'users.id','=','price_change_settings.user')
        ->select(
            'price_change_settings.*',
            'gain_account.name as gain_account_name',
            'loss_account.name as loss_account_name',
            'users.username as user'
        )
        ->where('price_change_settings.user', Auth::user()->id);
        
        
        return  DataTables::of($price_change_settings)
                ->addIndexColumn()
                ->addColumn("edit", function($row) {
                    $html = '<li><a href="#" data-href="' . action('\Modules\PriceChanges\Http\Controllers\F17FormController@editForm', [$row->id]) . '" class="btn-modal" data-container=".price_change_settings_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                    return $html;
                })
                ->rawColumns(["edit"])
                ->make(true);
        
    }
}
