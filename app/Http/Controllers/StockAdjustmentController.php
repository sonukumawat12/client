<?php

namespace App\Http\Controllers;

use App\BusinessLocation;

use App\Account;
use App\Category;
use App\AccountTransaction;
use App\AccountType;
use App\Business;
use App\DefaultAccountType;
use App\Product;
use App\Unit;
use App\PurchaseLine;
use App\Store;
use App\Transaction;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\StockAdjustmentSetting;

use Datatables;
use DB;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class StockAdjustmentController extends Controller
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
        if (! auth()->user()->can('purchase.view') && ! auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $stock_adjustments = Transaction::join(
                'business_locations AS BL',
                'transactions.location_id',
                '=',
                'BL.id'
            )
                ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'stock_adjustment')
                    ->select(
                        'transactions.id',
                        'transaction_date',
                        'ref_no',
                        'BL.name as location_name',
                        'adjustment_type',
                        'stock_adjustment_type',
                        'final_total',
                        'total_amount_recovered',
                        'additional_notes',
                        'transactions.id as DT_RowId',
                        DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by")
                    );

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $stock_adjustments->whereIn('transactions.location_id', $permitted_locations);
            }

            $hide = '';
            $start_date = request()->get('start_date');
            $end_date = request()->get('end_date');
            if (! empty($start_date) && ! empty($end_date)) {
                $stock_adjustments->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
                $hide = 'hide';
            }
            $location_id = request()->get('location_id');
            if (! empty($location_id)) {
                $stock_adjustments->where('transactions.location_id', $location_id);
            }

            return Datatables::of($stock_adjustments)
                ->addColumn('action', '<button type="button" data-href="{{action([\App\Http\Controllers\StockAdjustmentController::class, \'show\'], [$id]) }}" class="btn btn-primary btn-xs btn-modal" data-container=".view_modal"><i class="fa fa-eye" aria-hidden="true"></i> @lang("messages.view")</button>
                 &nbsp;
                    <button type="button" data-href="{{  action([\App\Http\Controllers\StockAdjustmentController::class, \'destroy\'], [$id]) }}" class="btn btn-danger btn-xs delete_stock_adjustment '.$hide.'"><i class="fa fa-trash" aria-hidden="true"></i> @lang("messages.delete")</button>')
                ->removeColumn('id')
                ->editColumn(
                    'final_total',
                    function ($row) {
                        if(($row->stock_adjustment_type == "decrease" && $row->final_total > 0) || ($row->stock_adjustment_type == "increase" && $row->final_total < 0)){
                            return $this->transactionUtil->num_f($row->final_total * -1, true);
                        }
                        return $this->transactionUtil->num_f($row->final_total, true);
                    }
                )
                ->editColumn(
                    'total_amount_recovered',
                    function ($row) {
                        return $this->transactionUtil->num_f($row->total_amount_recovered, true);
                    }
                )
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn('adjustment_type', function ($row) {
                    return ucfirst($row->adjustment_type);
                })
                ->editColumn('stock_adjustment_type', function ($row) {
                    return ucfirst($row->stock_adjustment_type);
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        return  action([\App\Http\Controllers\StockAdjustmentController::class, 'show'], [$row->id]);
                    }, ])
                ->rawColumns(['final_total', 'action', 'total_amount_recovered'])
                ->make(true);
        }

        return view('stock_adjustment.index');
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

        //Check if subscribed or not
        if (! $this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse(action([\App\Http\Controllers\StockAdjustmentController::class, 'index']));
        }
        
        //Update reference count
        $ref_count = $this->productUtil->setAndGetReferenceCount('stock_adjustment');
        $ref_no = $this->productUtil->generateReferenceNumber('stock_adjustment', $ref_count);

        $business_locations = BusinessLocation::forDropdown($business_id);

        return view('stock_adjustment.create')
                ->with(compact('business_locations','ref_no'));
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

        // dd($request->all());

        try {
            DB::beginTransaction();

            $input_data = $request->only(['location_id', 'transaction_date', 'adjustment_type', 'stock_adjustment_type', 'additional_notes', 'total_amount_recovered', 'final_total', 'ref_no', 'store_id']);
            $business_id = $request->session()->get('user.business_id');
            // dd($input_data);
            //Check if subscribed or not
            if (! $this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse(action([\App\Http\Controllers\StockAdjustmentController::class, 'index']));
            }

            $user_id = $request->session()->get('user.id');

            $input_data['type'] = 'stock_adjustment';
            $input_data['business_id'] = $business_id;
            $input_data['created_by'] = $user_id;
            $input_data['transaction_date'] = $this->productUtil->uf_date($input_data['transaction_date'], true);
            $input_data['total_amount_recovered'] = $this->productUtil->num_uf($input_data['total_amount_recovered']);

            //Update reference count
            $ref_count = $this->productUtil->setAndGetReferenceCount('stock_adjustment');
            //Generate reference number
            if (empty($input_data['ref_no'])) {
                $input_data['ref_no'] = $this->productUtil->generateReferenceNumber('stock_adjustment', $ref_count);
            }

            $products = $request->input('products');
            $stock_adjustment = Transaction::create($input_data);

            if (! empty($products)) {
                $product_data = [];

                foreach ($products as $product) {
                    $adjustment_line = [
                        'product_id' => $product['product_id'],
                        'variation_id' => $product['variation_id'],
                        'quantity' => $this->productUtil->num_uf($product['quantity']),
                        'unit_price' => $this->productUtil->num_uf($product['unit_price']),
                        'type' =>  request()->adjustment_type,
                        'stock_adjustment_type' => request()->stock_adjustment_type,
                    ];
                    $prod_amount = $adjustment_line['quantity'] * $adjustment_line['unit_price'];
                    
                    if (! empty($product['lot_no_line_id'])) {
                        //Add lot_no_line_id to stock adjustment line
                        $adjustment_line['lot_no_line_id'] = $product['lot_no_line_id'];
                    }
                    $product_data[] = $adjustment_line;

                    //Decrease available quantity
                    $this->productUtil->decreaseProductQuantity(
                        $product['product_id'],
                        $product['variation_id'],
                        $input_data['location_id'],
                        $this->productUtil->num_uf($product['quantity']),
                        0,
                        $request['stock_adjustment_type']
                    );
                    
                    $store_id = $stock_adjustment->store_id ?? Store::where('business_id', $business_id)->first()->id;
					$this->productUtil->decreaseProductQuantityStore(
                        $product['product_id'],
                        $product['variation_id'],
                        $input_data['location_id'],
                        $this->productUtil->num_uf($product['quantity']),
                        $store_id,
                        $request['stock_adjustment_type'],
                        0
                    );
                    $this_product = Product::where('id', $product['product_id'])->first();
                    $category  = Category::where('id', $this_product->sub_category_id)->first();
                    
                    // dd($category);
                    
                    if ($request['stock_adjustment_type']  == 'increase') {
                        if (!empty($this_product->stock_type)) {
                            $account_transaction_data = [
                                'amount' => $prod_amount,
                                'account_id' => $this_product->stock_type,
                                'type' => 'debit',
                                'operation_date' => $stock_adjustment->transaction_date,
                                'created_by' => $stock_adjustment->created_by,
                                'transaction_id' => $stock_adjustment->id,
                                'transaction_payment_id' => null,
                                'note' => null
                            ];
                               
                            AccountTransaction::createAccountTransaction($account_transaction_data);
                        }

                        $price_increment_acc = $category->price_increment_acc ?? null;
                        $StockAdjustmentSetting = StockAdjustmentSetting::where('business_id',$business_id)
                        ->where('sub_category_id', $this_product->sub_category_id)
                        ->where('adjustment_type', "increase")
                        ->first();
                        if(!is_null($StockAdjustmentSetting)){
                            $price_increment_acc = $StockAdjustmentSetting->account_to_link;
                        }
                        
                        if (!empty($price_increment_acc)) {
                            $account_transaction_data = [
                                'amount' => $prod_amount,
                                'account_id' => $price_increment_acc,
                                'type' => 'credit',
                                'operation_date' => $stock_adjustment->transaction_date,
                                'created_by' => $stock_adjustment->created_by,
                                'transaction_id' => $stock_adjustment->id,
                                'transaction_payment_id' => null,
                                'note' => null
                            ];
                               
                            AccountTransaction::createAccountTransaction($account_transaction_data);
                        }
                        
                    }
                    if ($request['stock_adjustment_type']  ==  'decrease') {
                        if (!empty($this_product->stock_type)) {
                        
                            $account_transaction_data = [
                                'amount' => $prod_amount,
                                'account_id' => $this_product->stock_type,
                                'type' => 'credit',
                                'operation_date' => $stock_adjustment->transaction_date,
                                'created_by' => $stock_adjustment->created_by,
                                'transaction_id' => $stock_adjustment->id,
                                'transaction_payment_id' => null,
                                'note' => null
                            ];
                               
                            AccountTransaction::createAccountTransaction($account_transaction_data);
                        }

                        $price_reduction_acc = $category->price_reduction_acc ?? null;
                        $StockAdjustmentSetting = StockAdjustmentSetting::where('business_id',$business_id)
                        ->where('sub_category_id', $this_product->sub_category_id)
                        ->where('adjustment_type', "decrease")
                        ->first();
                        if(!is_null($StockAdjustmentSetting)){
                            $price_reduction_acc = $StockAdjustmentSetting->account_to_link;
                        }
                        
                        if (!empty($price_reduction_acc)) {
                            $account_transaction_data = [
                                'amount' => $prod_amount,
                                'account_id' => $price_reduction_acc,
                                'type' => 'credit',
                                'operation_date' => $stock_adjustment->transaction_date,
                                'created_by' => $stock_adjustment->created_by,
                                'transaction_id' => $stock_adjustment->id,
                                'transaction_payment_id' => null,
                                'note' => null
                            ];
                               
                            AccountTransaction::createAccountTransaction($account_transaction_data);
                        }
                    }
                    
                  
                    
                }

               
                $stock_adjustment->stock_adjustment_lines()->createMany($product_data);

                //Map Stock adjustment & Purchase.
                $business = ['id' => $business_id,
                    'accounting_method' => $request->session()->get('business.accounting_method'),
                    'location_id' => $input_data['location_id'],
                ];
                $this->transactionUtil->mapPurchaseSell($business, $stock_adjustment->stock_adjustment_lines, 'stock_adjustment');

                // $this->transactionUtil->activityLog($stock_adjustment, 'added', null, [], false);
                
            }

            $output = ['success' => 1,
                'msg' => __('stock_adjustment.stock_adjustment_added_successfully'),
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

        return redirect('stock-adjustments')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! auth()->user()->can('purchase.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $stock_adjustment = Transaction::where('transactions.business_id', $business_id)
                    ->where('transactions.id', $id)
                    ->where('transactions.type', 'stock_adjustment')
                    ->with(['stock_adjustment_lines', 'location', 'business', 'stock_adjustment_lines.variation', 'stock_adjustment_lines.variation.product', 'stock_adjustment_lines.variation.product_variation', 'stock_adjustment_lines.lot_details'])
                    ->first();

        $lot_n_exp_enabled = false;
        if (request()->session()->get('business.enable_lot_number') == 1 || request()->session()->get('business.enable_product_expiry') == 1) {
            $lot_n_exp_enabled = true;
        }

        $activities = Activity::forSubject($stock_adjustment)
           ->with(['causer', 'subject'])
           ->latest()
           ->get();

        return view('stock_adjustment.show')
                ->with(compact('stock_adjustment', 'lot_n_exp_enabled', 'activities'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Transaction  $stockAdjustment
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $stockAdjustment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Transaction  $stockAdjustment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $stockAdjustment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('purchase.delete')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            if (request()->ajax()) {
                DB::beginTransaction();

                $stock_adjustment = Transaction::where('id', $id)
                                    ->where('type', 'stock_adjustment')
                                    ->with(['stock_adjustment_lines'])
                                    ->first();

                //Add deleted product quantity to available quantity
                $stock_adjustment_lines = $stock_adjustment->stock_adjustment_lines;
                if (! empty($stock_adjustment_lines)) {
                    $line_ids = [];
                    foreach ($stock_adjustment_lines as $stock_adjustment_line) {
                        $this->productUtil->updateProductQuantity(
                            $stock_adjustment->location_id,
                            $stock_adjustment_line->product_id,
                            $stock_adjustment_line->variation_id,
                            $this->productUtil->num_f($stock_adjustment_line->quantity)
                        );
                        
                        $this->productUtil->updateProductQuantityStore($stock_adjustment->location_id,
                            $stock_adjustment_line->product_id,
                            $stock_adjustment_line->variation_id,
                            $this->productUtil->num_f($stock_adjustment_line->quantity));
        
                        
                        
                        $line_ids[] = $stock_adjustment_line->id;
                    }

                    $this->transactionUtil->mapPurchaseQuantityForDeleteStockAdjustment($line_ids);
                }
                $stock_adjustment->delete();

                //Remove Mapping between stock adjustment & purchase.

                $output = ['success' => 1,
                    'msg' => __('stock_adjustment.delete_success'),
                ];

                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Return product rows
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function getInventoryAdjustmentAccount(Request $request)
    {

        $type = $request->type;

        $business_id = $request->session()->get('user.business_id');
        $account_type = null;

        if ($type == 'increase') {
            $account_type = AccountType::getAccountTypeIdByName('Income', $business_id)->id;
        }

        if ($type == 'decrease') {
            $account_type = AccountType::getAccountTypeIdByName('Expenses', $business_id)->id;
        }
        $result = '<option value="">Please Select</option>';
        $account_access = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');
        if ($account_access == 0) {
            return $result;
        }

        if (!empty($account_type)) {
            $result = Account::where('account_type_id', $account_type)->pluck('name', 'id');
        }
        return $this->transactionUtil->createDropdownHtml($result, 'Please Select');
    }
    
    public function getProductRowStockTransfer(Request $request)
    {
        if (request()->ajax()) {
            $row_index = $request->input('row_index');
            $variation_id = $request->input('variation_id');
            $location_id = $request->input('location_id');
            
            $store_id = $request->input('store_id');
            
            
            $temp_qty = 0; //passing empty value for temp products to avoid undefined error

            $business_id = $request->session()->get('user.business_id');
            $product = $this->productUtil->getDetailsFromVariation($variation_id, $business_id, $location_id,null,$store_id);
            
            $product->formatted_qty_available = $this->productUtil->num_f($product->current_stock);
            
            $units = Unit::forDropdown($business_id, false, false, 'show_in_add_product_unit');

            //Get lot number dropdown if enabled
            $lot_numbers = [];
            if (request()->session()->get('business.enable_lot_number') == 1 || request()->session()->get('business.enable_product_expiry') == 1) {
                $lot_number_obj = $this->transactionUtil->getLotNumbersFromVariation($variation_id, $business_id, $location_id, true);
                foreach ($lot_number_obj as $lot_number) {
                    $lot_number->qty_formated = $this->productUtil->num_f($lot_number->qty_available);
                    $lot_numbers[] = $lot_number;
                }
            }
            $product->lot_numbers = $lot_numbers;//dd($product);
            return view('stock_transfer.partials.product_table_row')
                ->with(compact('product', 'units', 'row_index', 'temp_qty'));
        }
    }
    
    public function getProductRow(Request $request)
    {
        if (request()->ajax()) {
            $row_index = $request->input('row_index');
            $variation_id = $request->input('variation_id');
            $location_id = $request->input('location_id');

            $business_id = $request->session()->get('user.business_id');
            $product = $this->productUtil->getDetailsFromVariation($variation_id, $business_id, $location_id);
            $product->formatted_qty_available = $this->productUtil->num_f($product->qty_available);
            $type = ! empty($request->input('type')) ? $request->input('type') : 'stock_adjustment';

            //Get lot number dropdown if enabled
            $lot_numbers = [];
            if (request()->session()->get('business.enable_lot_number') == 1 || request()->session()->get('business.enable_product_expiry') == 1) {
                $lot_number_obj = $this->transactionUtil->getLotNumbersFromVariation($variation_id, $business_id, $location_id, true);
                foreach ($lot_number_obj as $lot_number) {
                    $lot_number->qty_formated = $this->productUtil->num_f($lot_number->qty_available);
                    $lot_numbers[] = $lot_number;
                }
            }
            $product->lot_numbers = $lot_numbers;

            $sub_units = $this->productUtil->getSubUnits($business_id, $product->unit_id, false, $product->id);
            if ($type == 'stock_transfer') {
                return view('stock_transfer.partials.product_table_row')
                    ->with(compact('product', 'row_index', 'sub_units'));
            } else {
                return view('stock_adjustment.partials.product_table_row')
                        ->with(compact('product', 'row_index', 'sub_units'));
            }
        }
    }

    /**
     * Sets expired purchase line as stock adjustmnet
     *
     * @param  int  $purchase_line_id
     * @return json $output
     */
    public function removeExpiredStock($purchase_line_id)
    {
        if (! auth()->user()->can('purchase.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $purchase_line = PurchaseLine::where('id', $purchase_line_id)
                                    ->with(['transaction'])
                                    ->first();

            if (! empty($purchase_line)) {
                DB::beginTransaction();

                $qty_unsold = $purchase_line->quantity - $purchase_line->quantity_sold - $purchase_line->quantity_adjusted - $purchase_line->quantity_returned;
                $final_total = $purchase_line->purchase_price_inc_tax * $qty_unsold;

                $user_id = request()->session()->get('user.id');
                $business_id = request()->session()->get('user.business_id');

                //Update reference count
                $ref_count = $this->productUtil->setAndGetReferenceCount('stock_adjustment');

                $stock_adjstmt_data = [
                    'type' => 'stock_adjustment',
                    'business_id' => $business_id,
                    'created_by' => $user_id,
                    'transaction_date' => \Carbon::now()->format('Y-m-d'),
                    'total_amount_recovered' => 0,
                    'location_id' => $purchase_line->transaction->location_id,
                    'adjustment_type' => 'normal',
                    'final_total' => $final_total,
                    'ref_no' => $this->productUtil->generateReferenceNumber('stock_adjustment', $ref_count),
                ];

                //Create stock adjustment transaction
                $stock_adjustment = Transaction::create($stock_adjstmt_data);

                $stock_adjustment_line = [
                    'product_id' => $purchase_line->product_id,
                    'variation_id' => $purchase_line->variation_id,
                    'quantity' => $qty_unsold,
                    'unit_price' => $purchase_line->purchase_price_inc_tax,
                    'removed_purchase_line' => $purchase_line->id,
                ];

                //Create stock adjustment line with the purchase line
                $stock_adjustment->stock_adjustment_lines()->create($stock_adjustment_line);

                //Decrease available quantity
                $this->productUtil->decreaseProductQuantity(
                    $purchase_line->product_id,
                    $purchase_line->variation_id,
                    $purchase_line->transaction->location_id,
                    $qty_unsold,
                    0,
                    'decrease'
                );
                
                $store_id = Store::where('business_id', $business_id)->first()->id;
				$this->productUtil->decreaseProductQuantityStore(
                    $purchase_line->product_id,
                    $purchase_line->variation_id,
                    $purchase_line->transaction->location_id,
                    $qty_unsold,
                    $store_id,
                    "decrease",
                    0
                );

                //Map Stock adjustment & Purchase.
                $business = ['id' => $business_id,
                    'accounting_method' => request()->session()->get('business.accounting_method'),
                    'location_id' => $purchase_line->transaction->location_id,
                ];
                $this->transactionUtil->mapPurchaseSell($business, $stock_adjustment->stock_adjustment_lines, 'stock_adjustment', false, $purchase_line->id);

                DB::commit();

                $output = ['success' => 1,
                    'msg' => __('lang_v1.stock_removed_successfully'),
                ];
            }
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

        return $output;
    }
}
