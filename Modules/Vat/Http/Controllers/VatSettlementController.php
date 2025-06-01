<?php

namespace Modules\Vat\Http\Controllers;

use App\Account;
use App\AccountTransaction;
use App\AccountType;
use App\Business;
use App\BusinessLocation;
use App\Category;
use App\Contact;
use Modules\Vat\Entities\VatContact;
use Modules\Vat\Entities\VatProduct;

use App\ContactLedger;
use App\CustomerReference;
use App\ExpenseCategory;
use App\Product;
use App\Store;
use App\Transaction;
use App\TransactionPayment;
use App\TransactionSellLine;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Petro\Entities\Pump;
use Modules\Petro\Entities\PumpOperator;
use App\Utils\Util;
use App\Utils\ProductUtil;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use App\Utils\BusinessUtil;
use App\Variation;
use Modules\HR\Entities\WorkShift;
;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Modules\Petro\Entities\FuelTank;
use Modules\Vat\Entities\VatMeterSale;

use Modules\Vat\Entities\VatOtherSale;
use Modules\Vat\Entities\VatSettlement;
use Modules\Vat\Entities\VatSettlementCardPayment;
use Modules\Vat\Entities\VatSettlementCashPayment;
use Modules\Vat\Entities\VatSettlementCreditSalePayment;

use Modules\Petro\Entities\TankPurchaseLine;
use Modules\Petro\Entities\TankSellLine;
use Modules\Petro\Entities\DailyCollection;
use Modules\Petro\Entities\PumpOperatorCommission;
use Modules\Superadmin\Entities\Subscription;
use Yajra\DataTables\DataTables;
use App\Utils\NotificationUtil;

use Modules\Petro\Entities\SettlementLoanPayment;

use App\NotificationTemplate;
use App\Http\Controllers\ContactController;

class VatSettlementController extends Controller
{

    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $moduleUtil;
    protected $transactionUtil;
    protected $commonUtil;
    protected $notificationUtil;

    private $barcode_types;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil,NotificationUtil $notificationUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
        $this->notificationUtil = $notificationUtil;
    }
    
    
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module')) {
            abort(403, 'Unauthorized Access');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            if (request()->ajax()) {
                $query = VatSettlement::leftjoin('pump_operators', 'vat_settlements.pump_operator_id', 'pump_operators.id')
                    ->where('vat_settlements.business_id', $business_id)
                    ->select([
                        'pump_operators.name as pump_operator_name',
                        'vat_settlements.*',
                    ]);

               
                if (!empty(request()->pump_operator)) {
                    $query->where('vat_settlements.pump_operator_id', request()->pump_operator);
                }
                if (!empty(request()->settlement_no)) {
                    $query->where('vat_settlements.id', request()->settlement_no);
                }
                if (!empty(request()->start_date) && !empty(request()->end_date)) {
                    $query->whereDate('vat_settlements.transaction_date', '>=', request()->start_date);
                    $query->whereDate('vat_settlements.transaction_date', '<=', request()->end_date);
                }
                $query->orderBy('vat_settlements.id', 'desc');
                
                
                $settlements = Datatables::of($query)
                    ->addColumn(
                        'action',
                        function ($row) {
                            $html = '';
                            if ($row->status == 1) {
                                $html .= '<a class="btn  btn-danger btn-sm" href="' . action("\Modules\Vat\Http\Controllers\VatSettlementController@create") . '">' . __("petro::lang.finish_settlement") . '</a>';
                            } else {
                                $html .=  '<div class="btn-group">
                                <button type="button" class="btn btn-info dropdown-toggle btn-xs"
                                    data-toggle="dropdown" aria-expanded="false">' .
                                    __("messages.actions") .
                                    '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                    </span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                                $html .= '<li><a data-href="' . action("\Modules\Vat\Http\Controllers\VatSettlementController@show", [$row->id]) . '" class="btn-modal" data-container=".settlement_modal"><i class="fa fa-eye" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';
                                $html .= '<li><a href="' . action("\Modules\Vat\Http\Controllers\VatSettlementController@edit", [$row->id]) . '" class="edit_settlement_button"><i class="fa fa-pencil-square-o"></i> ' . __("messages.edit") . '</a></li>';
                                $html .= '<li><a href="' . action("\Modules\Vat\Http\Controllers\VatSettlementController@destroy", [$row->id]) . '" class="delete_settlement_button"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                                $html .= '<li><a data-href="' . action("\Modules\Vat\Http\Controllers\VatSettlementController@print", [$row->id]) . '" class="print_settlement_button"><i class="fa fa-print"></i> ' . __("petro::lang.print") . '</a></li>';

                                $html .= '</ul></div>';
                            }
                            return $html;
                        }
                    )
                    ->editColumn('status', function ($row) {
                        if ($row->status == 0) {
                            return '<span class="label label-success">Completed</span>';
                        } else {
                            return '<span class="label label-danger">Pending</span>';
                        }
                    })
                    
                    ->addColumn('created_by', function ($row) {
                        $transaction = Transaction::where('invoice_no',$row->settlement_no)->leftJoin('users','users.id','transactions.created_by')->select('users.username')->first();
                        if(!empty($transaction)){
                            return $transaction->username;
                        }
                    })
                    ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                    ->editColumn('total_amount', '{{@num_format($total_amount)}}')
                    ->setRowAttr([
                        'data-href' => function ($row) {
                            return  action('\Modules\Vat\Http\Controllers\VatSettlementController@show', [$row->id]);
                        }
                    ])

                    ->removeColumn('id');

                return $settlements->rawColumns(['action', 'status', 'total_amount'])
                    ->make(true);
            }
        }
        $business_locations = BusinessLocation::forDropdown($business_id);
        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');
        $settlement_nos = VatSettlement::where('business_id', $business_id)->pluck('settlement_no', 'id');

        $message = $this->transactionUtil->getGeneralMessage('general_message_pump_management_checkbox');

        return view('vat::settlement.index')->with(compact(
            'business_locations',
            'pump_operators',
            'settlement_nos',
            'message'
        ));
    }
    
    function extractLastInteger($text) {
        // Use regular expression to find the last integer in the text
        preg_match('/\d+$/', $text, $matches);
    
        if (isset($matches[0])) {
            // $matches[0] contains the matched integer
            return intval($matches[0]);
        } else {
            // No integer found, handle accordingly (e.g., return 0, null, etc.)
            return 0;
        }
    }
    
    public function create()
    {
        $business_id = request()->session()->get('business.id');
        $business = Business::where('id', $business_id)->first();
        $pos_settings = json_decode($business->pos_settings,true);
        $check_qty =  true;
        $cash_denoms = !empty($pos_settings['cash_denominations']) ? explode(',',$pos_settings['cash_denominations']) : array();
        
        $business_locations = BusinessLocation::forDropdown($business_id);
        $default_location = current(array_keys($business_locations->toArray()));

        $payment_types = $this->productUtil->payment_types($default_location,false, false, false, false,"is_sale_enabled");
        $customers = VatContact::customersDropdown($business_id, false);
        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');

        $items = [];

        $ref_no_prefixes = request()->session()->get('business.ref_no_prefixes');
        $ref_no_starting_number = request()->session()->get('business.ref_no_starting_number');
        $prefix =   !empty($ref_no_prefixes['settlement']) ? $ref_no_prefixes['settlement'] : '';
        $starting_no =  !empty($ref_no_starting_number['settlement']) ? (int) $ref_no_starting_number['settlement'] : 1;
        $count = VatSettlement::where('business_id', $business_id)->orderBy('id','DESC')->first();
        
        if(!empty($count)){
            $count = $this->extractLastInteger($count->settlement_no);
        }else{
            $count = 0;
        }
        
        $settlement_no = $prefix . (1 + $count);
        
        
    
        $active_settlement = VatSettlement::where('status', 1)
            ->where('business_id', $business_id)
            ->select('vat_settlements.*')
            ->with(['meter_sales', 'other_sales'])->first();

            
            
        $business_locations = BusinessLocation::forDropdown($business_id);
        $default_location = current(array_keys($business_locations->toArray()));
        if (!empty($active_settlement)) {
            $already_pumps = VatMeterSale::where('settlement_no', $active_settlement->id)->pluck('pump_id')->toArray();
            $pump_nos = Pump::where('business_id', $business_id)->whereNotIn('id', $already_pumps)->pluck('pump_name', 'id');
        } else {
            $pump_nos = Pump::where('business_id', $business_id)->pluck('pump_name', 'id');
        }

        //other_sale tab
        $stores =  Store::forDropdown($business_id, 0,1, 'sell');
        
        $fuel_category_id = Category::where('business_id', $business_id)->where('name', 'Fuel')->first();
        $fuel_category_id = !empty($fuel_category_id) ? $fuel_category_id->id : null;
        $items = VatProduct::where('business_id',$business_id)->pluck('name','id');
        
        $payment_meter_sale_total = !empty($active_settlement->meter_sales) ? $active_settlement->meter_sales->sum('discount_amount') :  0.00;
        $payment_other_sale_total = !empty($active_settlement->other_sales) ? $active_settlement->other_sales->sum('sub_total') :  0.00;
        $payment_other_sale_discount = !empty($active_settlement->other_sales) ? $active_settlement->other_sales->sum('discount_amount') :  0.00;
        $payment_other_sale_total -= $payment_other_sale_discount;
        $bulk_tanks = FuelTank::where('business_id', $business_id)->where('bulk_tank', 1)->pluck('fuel_tank_number', 'id');


        $select_pump_operator_in_settlement = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'select_pump_operator_in_settlement');

        $message = $this->transactionUtil->getGeneralMessage('general_message_pump_management_checkbox');
        $discount_types = ['fixed' => 'Fixed', 'percentage' => 'Percentage'];

        return view('vat::settlement.create')->with(compact(
            'select_pump_operator_in_settlement',
            'message',
            'business_locations',
            'payment_types',
            'customers',
            'pump_operators',
            'pump_nos',
            'items',
            'settlement_no',
            'default_location',
            'active_settlement',
            'stores',
            'payment_meter_sale_total',
            'payment_other_sale_total',
            'bulk_tanks',
            'discount_types',
            'cash_denoms',
            'check_qty',
            'payment_other_sale_discount'
        ));
    }
    
    public function getBalanceStockById(Request $request, $id)
    {
        try {
            
            $product = VatProduct::join('vat_variations', 'vat_products.id', 'vat_variations.product_id')
                        ->where('vat_variations.product_id', $id)
                        ->select('sell_price_inc_tax', 'vat_products.name', 'sku')->first();

            $output = [
                'success' => true,
                'price' => $product->sell_price_inc_tax,
                'product_name' => $product->name,
                'code' => $product->sku,
                'msg' => __('petro::lang.success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request, ContactController $contactController)
    {
        try {
            $denom_qty = $request->denom_qty;
            $denom_value = $request->denom_value;
            $denom_enabled = $request->denom_enabled;
            $denom_data = array();
            
            if($denom_enabled > 0){
                $i = 0;
                foreach($denom_qty as $one){
                   $denom_data[] = array("value" => $denom_value[$i], "qty" => $denom_qty[$i]);
                    $i++; 
                }
            }
            $settlement_no = $request->settlement_no;
            $business_id = $request->session()->get('business.id');
            $settlement = VatSettlement::where('settlement_no', $request->settlement_no)->where('business_id', $business_id)->first();
            $edit = VatSettlement::where('vat_settlements.id', $settlement->id)->where('vat_settlements.business_id', $business_id)->where('status', 0)->first();
            DB::beginTransaction();
            
            $business_locations = BusinessLocation::forDropdown($business_id);
            $default_location = current(array_keys($business_locations->toArray()));

            $settlement = VatSettlement::where('vat_settlements.id', $settlement->id)->where('vat_settlements.business_id', $business_id)
                ->leftjoin('pump_operators', 'vat_settlements.pump_operator_id', 'pump_operators.id')
                ->with([
                    'meter_sales',
                    'other_sales',
                    'cash_payments',
                    'card_payments',
                    'credit_sale_payments',
                ])
                ->select('vat_settlements.*', 'pump_operators.name as pump_operator_name')
                ->first();
            $business = Business::where('id', $settlement->business_id)->first();
            $pump_operator = PumpOperator::where('id', $settlement->pump_operator_id)->first();
            
            
            $total_sales_amount = $settlement->meter_sales->sum('sub_total') + $settlement->other_sales->sum('sub_total');
            $total_sales_discount_amount = $settlement->meter_sales->sum('discount_amount') + $settlement->other_sales->sum('discount_amount');

            foreach ($settlement->meter_sales as $meter_sale) {
                VatMeterSale::where('id', $meter_sale->id)->update(['transaction_id' => $settlement->id]);
            }
            
            foreach ($settlement->other_sales as $other_sale) {
                VatOtherSale::where('id', $other_sale->id)->update(['transaction_id' => $settlement->id]);
            }
           

            $settlement_total = $settlement->meter_sales->sum('sub_total') + $settlement->other_sales->sum('sub_total');
            $settlement->total_amount = $settlement_total;
            
            $settlement->status = 0; // set status to non active
            if($denom_enabled > 0){
                $settlement->cash_denomination = json_encode($denom_data);
            }else{
                $settlement->cash_denomination = NULL;
            }
            $settlement->finish_date = date('Y-m-d');
            $settlement->save();


            DB::commit();

            return view('vat::settlement.print')->with(compact('settlement', 'business', 'pump_operator'));
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];
        }

        return $output;
    }


    public function getDiscount($discount)
    {
        $pos = strpos($discount, '%');
        $discount_amount = str_replace('%', '', $discount);
        if ($pos === false) {
            $discount_type = 'fixed';
        } else {
            $discount_type = 'percentage';
        }

        return ['discount_amount' => $discount_amount, 'discount_type' => $discount_type];
    }
    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id)
    {
        $business_id = request()->session()->get('business.id');
        $business_locations = BusinessLocation::forDropdown($business_id);
        $default_location = current(array_keys($business_locations->toArray()));

        $settlement = VatSettlement::where('vat_settlements.id', $id)->where('vat_settlements.business_id', $business_id)
            ->leftjoin('pump_operators', 'vat_settlements.pump_operator_id', 'pump_operators.id')
            ->with([
                'meter_sales',
                'other_sales',
                'cash_payments',
                'card_payments',
                'credit_sale_payments'
            ])
            ->select('vat_settlements.*', 'pump_operators.name as pump_operator_name')
            ->first();
        
        $business = Business::where('id', !empty($settlement) ? $settlement->business_id : 0)->first();
        $pump_operator = PumpOperator::where('id', !empty($settlement) ? $settlement->pump_operator_id : 0)->first();

        
        return view('vat::settlement.show')->with(compact('settlement', 'business', 'pump_operator'));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('business.id');
        $business_locations = BusinessLocation::forDropdown($business_id);
        $default_location = current(array_keys($business_locations->toArray()));
        $payment_types = $this->productUtil->payment_types($default_location,false, false, false, false,"is_sale_enabled");
        $customers = VatContact::customersDropdown($business_id, false, true, 'customer');
        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');

        $pump_nos = Pump::where('business_id', $business_id)->pluck('pump_name', 'id');

        $items = [];


        $active_settlement = VatSettlement::where('id', $id)
            ->select('vat_settlements.*')
            ->with(['meter_sales', 'other_sales'])->first();
        $settlement_no = $active_settlement->settlement_no;
        
        $business_locations = BusinessLocation::forDropdown($business_id);
        $default_location = current(array_keys($business_locations->toArray()));
        if (!empty($active_settlement)) {
            $already_pumps = VatMeterSale::where('settlement_no', $active_settlement->id)->pluck('pump_id')->toArray();
            $pump_nos = Pump::where('business_id', $business_id)->whereNotIn('id', $already_pumps)->pluck('pump_name', 'id');
        } else {
            $pump_nos = Pump::where('business_id', $business_id)->pluck('pump_name', 'id');
        }

        //other_sale tab
        $items = VatProduct::where('business_id',$business_id)->pluck('name','id');

        $payment_meter_sale_total = !empty($active_settlement->meter_sales) ? $active_settlement->meter_sales->sum('discount_amount') :  0.00;
        $payment_other_sale_total = !empty($active_settlement->other_sales) ? $active_settlement->other_sales->sum('sub_total') :  0.00;
        $payment_other_sale_discount = !empty($active_settlement->other_sales) ? $active_settlement->other_sales->sum('discount_amount') :  0.00;
        $payment_other_sale_total -= $payment_other_sale_discount;
        $bulk_tanks = FuelTank::where('business_id', $business_id)->where('bulk_tank', 1)->pluck('fuel_tank_number', 'id');


        $select_pump_operator_in_settlement = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'select_pump_operator_in_settlement');

        $message = $this->transactionUtil->getGeneralMessage('general_message_pump_management_checkbox');
        $discount_types = ['fixed' => 'Fixed', 'percentage' => 'Percentage'];

        return view('vat::settlement.edit')->with(compact(
            'business_locations',
            'payment_types',
            'customers',
            'pump_operators',
            'pump_nos',
            'items',
            'settlement_no',
            'default_location',
            'active_settlement',
            'payment_meter_sale_total',
            'payment_other_sale_total',
            'discount_types'
        ));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            $input = $request->except('_token', '_method','location_id','work_shift');
            $input['transaction_date'] = \Carbon::parse($request->transaction_date)->format('Y-m-d');
            VatSettlement::where('id', $id)->update($input);
            $output = [
                'success' => true,
                'msg' => __('petro::lang.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }
    
    public function deletePreviouseTransactions($settlement_id, $is_destory = false)
    {
        $business_id = request()->session()->get('business.id');
        $settlement = Settlement::find($settlement_id);
        if ($is_destory) {
            VatOtherSale::where('settlement_no', $settlement->id)->delete();
            VatMeterSale::where('settlement_no', $settlement->id)->get();
            VatSettlementCardPayment::where('settlement_no', $settlement->id)->delete();
            VatSettlementCashPayment::where('settlement_no', $settlement->id)->delete();
            VatSettlementCreditSalePayment::where('settlement_no', $settlement->id)->delete();
        }
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $settlement = VatSettlement::findOrFail($id);
            $this->deletePreviouseTransactions($settlement->id, true);
            $settlement->delete();
            DB::commit();

            $output = [
                'success' => true,
                'msg' => __('petro::lang.settlement_delete_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        return $output;
    }


    public function saveMeterSale(Request $request)
    {
        try {
            $business_id = $request->session()->get('business.id');
            $business_locations = BusinessLocation::forDropdown($business_id);
            $default_location = current(array_keys($business_locations->toArray()));

            DB::beginTransaction();

            $settlement_exist = $this->createSettlementIfNotExist($request);

            $pump = Pump::where('id', $request->pump_id)->first();
            $tank_id = $pump->fuel_tank_id;
            $data = array(
                'business_id' => $business_id,
                'settlement_no' => $settlement_exist->id,
                'product_id' => $request->product_id,
                'pump_id' => $request->pump_id,
                'starting_meter' => $request->starting_meter,
                'closing_meter' => $pump->bulk_sale_meter == 0 ? $request->closing_meter : '',
                'price' => $request->price,
                'qty' => $request->qty,
                'discount' => $request->discount,
                'discount_type' => $request->discount_type,
                'discount_amount' => $request->discount_amount,
                'testing_qty' => $request->testing_qty,
                'sub_total' => $request->sub_total
            );

            $meter_sale = VatMeterSale::create($data);
            
            DB::commit();
            $output = [
                'success' => true,
                'msg' => 'success',
                'meter_sale_id' => $meter_sale->id,
                'settlement_id' => $settlement_exist->id
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    public function deleteMeterSale($id)
    {
        try {
            $meter_sale = VatMeterSale::where('id', $id)->first();
            $amount = $meter_sale->discount_amount;
            
            $meter_sale->delete();
            
            $pump = Pump::where('id', $meter_sale->pump_id)->first();
            $pump_name = $pump->pump_name;
            $pump_id = $pump->id;
            
            $output = [
                'success' => true,
                'amount' => $amount,
                'pump_name' => $pump_name,
                'pump_id' => $pump_id,
                'msg' => __('petro::lang.success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     * save other sale data in db
     * @param product_id
     * @return Response
     */
    public function saveOtherSale(Request $request)
    {
        try {
            $business_id = $request->session()->get('business.id');

            $settlement_exist = $this->createSettlementIfNotExist($request);
            $data = array(
                'business_id' => $business_id,
                'settlement_no' => $settlement_exist->id,
                'product_id' => $request->product_id,
                'price' => $request->price,
                'qty' => $request->qty,
                'discount' => $request->discount,
                'discount_type' => $request->discount_type,
                'discount_amount' => $request->discount_amount,
                'sub_total' => $request->sub_total
            );
            $other_sale = VatOtherSale::create($data);

            $output = [
                'success' => true,
                'other_sale_id' => $other_sale->id,
                'msg' => __('petro::lang.success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    public function deleteOtherSale($id)
    {
        try {
            $other_sale = VatOtherSale::where('id', $id)->first();
            $amount = $other_sale->sub_total - $other_sale->discount_amount;
            $other_sale->delete();

            $output = [
                'success' => true,
                'amount' => $amount,
                'msg' => __('petro::lang.success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }


    public function createSettlementIfNotExist(Request $request)
    {
        $business_id = $request->session()->get('business.id');
        $settlement_data = array(
            'settlement_no' => $request->settlement_no,
            'business_id' => $business_id,
            'transaction_date' => \Carbon::parse($request->transaction_date)->format('Y-m-d'),
            'pump_operator_id' => $request->pump_operator_id,
            'note' => $request->note,
            'status' => 1
        );
        $settlement_exist = VatSettlement::where('settlement_no', $request->settlement_no)->where('business_id', $business_id)->first();
        if (empty($settlement_exist)) {
            $settlement_exist = VatSettlement::create($settlement_data);
        }

        return $settlement_exist;
    }

    /**
     * print resources
     * @param settlement_id
     * @return Response
     */
    public function print($id)
    {
        $business_id = request()->session()->get('business.id');
        $business_locations = BusinessLocation::forDropdown($business_id);
        $default_location = current(array_keys($business_locations->toArray()));

        $settlement = VatSettlement::where('vat_settlements.id', $id)->where('vat_settlements.business_id', $business_id)
            ->leftjoin('pump_operators', 'vat_settlements.pump_operator_id', 'pump_operators.id')
            ->with([
                'meter_sales',
                'other_sales',
                'cash_payments',
                'card_payments',
                'credit_sale_payments'
            ])
            ->select('vat_settlements.*', 'pump_operators.name as pump_operator_name')
            ->first();

        $business = Business::where('id', $settlement->business_id)->first();
        $pump_operator = PumpOperator::where('id', $settlement->pump_operator_id)->first();

        
        return view('vat::settlement.print')->with(compact('settlement', 'business', 'pump_operator'));
    }

}
