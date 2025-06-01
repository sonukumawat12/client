<?php

namespace App\Http\Controllers;

use App\BusinessLocation;

use App\Account;
use App\AccountGroup;
use App\AccountType;
use App\Business;
use App\Store;
use App\Transaction;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Category;
use App\StockAdjustmentSetting;

use Datatables;
use DB;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class StockAdjustmentSettings extends Controller
{
    /**
     * All Utils instance.
     */
    protected $productUtil;

    protected $transactionUtil;

    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    //   
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            $stock_adjustments = StockAdjustmentSetting::leftJoin('users', 'stock_adjustment_settings.created_by', '=', 'users.id')
            ->leftJoin('categories', 'stock_adjustment_settings.category_id', '=', 'categories.id')
            ->leftJoin('categories as sub_category', 'stock_adjustment_settings.sub_category_id', '=', 'sub_category.id')
            ->leftJoin('accounts', 'stock_adjustment_settings.account_to_link', '=', 'accounts.id')
            ->leftJoin('accounts as stock_accounts', 'stock_adjustment_settings.stock_account', '=', 'stock_accounts.id')
            ->leftJoin('account_groups', 'stock_adjustment_settings.stock_group', '=', 'account_groups.id')
            ->where('stock_adjustment_settings.business_id', $business_id)
            ->select(
                'stock_adjustment_settings.*',
                DB::raw("categories.name as category"),
                DB::raw("sub_category.name as sub_category"),
                DB::raw("accounts.name as account_to_link_name"),
                DB::raw("stock_accounts.name as stock_account_name"),
                DB::raw("account_groups.name as account_group_name"),
                DB::raw("CONCAT(COALESCE(users.surname, ''),' ',COALESCE(users.first_name, ''),' ',COALESCE(users.last_name,'')) as added_by")
            );

            return Datatables::of($stock_adjustments)
                ->addColumn('action', '<a href="{{action([\App\Http\Controllers\StockAdjustmentSettings::class, \'create\'], [\'id\'=>$id]) }}" class="btn btn-primary btn-xs"><i class="fa fa-pencil" aria-hidden="true"></i> @lang("messages.edit")</a>')
                ->editColumn('date', '{{@format_datetime($date)}}')
                ->editColumn('adjustment_type', function ($row) {
                    return ucfirst($row->adjustment_type);
                })
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $categories = Category::forDropdown($business_id, 'product');
        $sub_categories = Category::where('business_id', $business_id)
                        ->whereNotNull('parent_id')
                        ->pluck('name', 'id')
                        ->toArray();
        $groups = [
            AccountGroup::getGroupByName("Finished Goods Account")->id => "Finished Goods Account",
            AccountGroup::getGroupByName("Raw Material Account")->id => "Raw Material Account",
            AccountGroup::getGroupByName("Other Stocks")->id => "Other Stocks",
        ];

        $settings = null;
        if(request()->id){
            $settings = StockAdjustmentSetting::where('business_id',$business_id)
            ->where('id',request()->id)
            ->first();
        }
        $accounts = Account::where('business_id',$business_id)->pluck('name','id');

        return view('stock_adjustment_settings.create')
                ->with(compact('business_locations','categories','sub_categories','groups','accounts','settings'));
    }
    
    public function get_account_by($by,$id){
        $accounts = [];
        $business_id = request()->session()->get('user.business_id');
        if($by == "group"){
            $accounts =  Account::where('business_id',$business_id)->where('asset_type',$id)->pluck('name','id')->toArray();
        }elseif($by == "type"){
             $type_id = AccountType::getAccountTypeIdOfType($id, $business_id);
            $accounts =  Account::where('business_id',$business_id)->where('account_type_id',$type_id)->pluck('name','id')->toArray();
        }
        
        $options = "<option value=''>".__('messages.please_select')."</option>";
        
        foreach($accounts as $key => $account){
            $options .= "<option value='".$key."'>".$account."</option>";
        }
        
        return $options;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $input_data = $request->only(['date', 'adjustment_type', 'category_id', 'sub_category_id', 'account_to_link', 'stock_account','stock_group']);
            $business_id = $request->session()->get('user.business_id');
            $input_data['business_id'] = $business_id;
            $input_data['date'] = $this->productUtil->uf_date($input_data['date'], true);
            $user_id = $request->session()->get('user.id');
            $input_data['created_by'] = $user_id;
            if($request->id){
                StockAdjustmentSetting::where('id',$request->id)->update($input_data);
            } else {
                StockAdjustmentSetting::create($input_data);
            }
            
            $output = ['success' => 1,
                'msg' => __('stock_adjustment_settings.stock_adjustment_added_successfully'),
            ];

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            $msg = trans('messages.something_went_wrong');

            if (get_class($e) == \App\Exceptions\PurchaseSellMismatch::class) {
                $msg = $e->getMessage();
            }

            $output = ['success' => 0,
                'msg' => $msg,
            ];
        }

        return redirect()->action([StockAdjustmentSettings::class, 'create'])->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Transaction  $stockAdjustment
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $stock_adjustment = StockAdjustmentSetting::where('business_id', $business_id)
                            ->where('id', $id)
                            ->first();

        if (empty($stock_adjustment)) {
            abort(404, 'Stock Adjustment not found');
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $categories = Category::forDropdown($business_id, 'product');
        $sub_categories = Category::where('business_id', $business_id)
                        ->whereNotNull('parent_id')
                        ->pluck('name', 'id')
                        ->toArray();
        $groups = [
            AccountGroup::getGroupByName("Finished Goods Account")->id => "Finished Goods Account",
            AccountGroup::getGroupByName("Raw Material Account")->id => "Raw Material Account",
            AccountGroup::getGroupByName("Other Stocks")->id => "Other Stocks",
        ];
        $accounts = Account::where('business_id',$business_id)->pluck('name','id');

        return view('stock_adjustment_settings.edit')
                ->with(compact('stock_adjustment','business_locations','categories','sub_categories','groups','accounts'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Transaction  $stockAdjustment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (! auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $stock_adjustment = StockAdjustmentSetting::where('business_id', $business_id)
                            ->where('id', $id)
                            ->first();

        if (empty($stock_adjustment)) {
            abort(404, 'Stock Adjustment not found');
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $categories = Category::forDropdown($business_id, 'product');
        $sub_categories = Category::where('business_id', $business_id)
                        ->whereNotNull('parent_id')
                        ->pluck('name', 'id')
                        ->toArray();
        $groups = [
            AccountGroup::getGroupByName("Finished Goods Account")->id => "Finished Goods Account",
            AccountGroup::getGroupByName("Raw Material Account")->id => "Raw Material Account",
            AccountGroup::getGroupByName("Other Stocks")->id => "Other Stocks",
        ];
        $accounts = Account::where('business_id',$business_id)->pluck('name','id');

        return view('stock_adjustment_settings.edit')
                ->with(compact('stock_adjustment','business_locations','categories','sub_categories','groups','accounts'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
    }

    
}
