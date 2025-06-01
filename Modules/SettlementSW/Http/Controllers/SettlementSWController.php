<?php

namespace Modules\SettlementSW\Http\Controllers;


use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\Product;
use App\Category;
use App\AccountType;
use App\Store;
use App\Account;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\NotificationUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use App\Variation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Petro\Entities\SettlementExpensePayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\HR\Entities\WorkShift;
use Modules\Petro\Entities\CustomerPayment;
use Modules\Petro\Entities\DayEnd;
use Modules\Petro\Entities\OtherIncome;
use Modules\Petro\Entities\OtherSale;
use Modules\Petro\Entities\Pump;
use Modules\Petro\Entities\PumperDayEntry;
use Modules\Petro\Entities\PumpOperator;
use Modules\Petro\Entities\PumpOperatorCommission;
use Modules\Petro\Entities\Settlement;
use Modules\Petro\Entities\SettlementCardPayment;
use Modules\Petro\Entities\SettlementCashPayment;
use Modules\Petro\Entities\SettlementChequePayment;
use App\ExpenseCategory;
use Modules\Petro\Entities\SettlementCreditSalePayment;
use Modules\Superadmin\Entities\Subscription;
use Modules\Petro\Entities\PumpOperatorAssignment;
use Modules\Petro\Entities\PumpOperatorOtherSale;
use Modules\Petro\Entities\MeterSale;
use Modules\Petro\Entities\FuelTank;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use App\Transaction;

class SettlementSWController extends Controller
{

    protected $productUtil;

    protected $moduleUtil;

    protected $transactionUtil;

    protected $commonUtil;

    protected $notificationUtil;

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
                $query = Settlement::leftJoin('business_locations', 'settlements.location_id', '=', 'business_locations.id')

                ->leftJoin('pump_operators', 'settlements.pump_operator_id', '=', 'pump_operators.id')

                ->leftJoin('pump_operator_assignments','settlements.id', 'pump_operator_assignments.settlement_id')

                ->where('settlements.business_id', $business_id)
                ->where('settlements.settlement_no', 'LIKE', 'SET-SW%')

                ->select([
                    'pump_operators.name as pump_operator_name',
                    'business_locations.name as location_name',
                    'settlements.*',
                    'pump_operator_assignments.shift_number',
                ])

                ->with(['meter_sales', 'other_sales']);

                
                $query->groupBy('settlements.id');

                $query->orderBy('settlements.id', 'desc');

                $first = null;

                $first = Settlement::where('business_id', $business_id)->where('status', 0)->orderBy('id', 'desc')->first();



                $delete_settlement = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'delete_settlement');

                $edit_settlement = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'edit_settlement');

                $edit_settlement_no_change = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'edit_settlement_no_change');

            
                $settlements = Datatables::of($query)

                    ->addColumn(

                        'action',

                        function ($row) use ($first,$delete_settlement,$edit_settlement,$edit_settlement_no_change) {

                            $html = '';

                            if ($row->status == 1) {
                                if (Str::startsWith($row->settlement_no, 'SET-SW')) {
                                    $html .= '<a class="btn  btn-danger btn-sm" href="' . action("\Modules\SettlementSW\Http\Controllers\SettlementSWController@create") . '">' . __("petro::lang.finish_settlement") . '</a>';
                                }else{
                                     $html .= '<a class="btn  btn-danger btn-sm" href="' . action("\Modules\Petro\Http\Controllers\SettlementController@create") . '">' . __("petro::lang.finish_settlement") . '</a>';
                                }

                            }else if($row->is_edit == 1){

                               $html .= '<a class="btn  btn-warning btn-sm" href="' . action("\Modules\Petro\Http\Controllers\SettlementController@edit", [$row->id]) . '">' . __("petro::lang.finish_editting") . '</a>'; 

                            } else {

                                $html .=  '<div class="btn-group">

                                <button type="button" class="btn btn-info dropdown-toggle btn-xs"

                                    data-toggle="dropdown" aria-expanded="false">' .

                                    __("messages.actions") .

                                    '<span class="caret"></span><span class="sr-only">Toggle Dropdown

                                    </span>

                                </button>

                                <ul class="dropdown-menu dropdown-menu-left" role="menu">';



                                $html .= '<li><a data-href="' . action("\Modules\Petro\Http\Controllers\SettlementController@show", [$row->id]) . '" class="btn-modal" data-container=".settlement_modal"><i class="fa fa-eye" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';

                                if (auth()->user()->can("settlement.edit") && $edit_settlement) {

                                    $html .= '<li><a href="' . action("\Modules\Petro\Http\Controllers\SettlementController@edit", [$row->id]) . '" class="edit_settlement_button"><i class="fa fa-pencil-square-o"></i> ' . __("messages.edit") . '</a></li>';

                                }

                                

                                if (auth()->user()->can("settlement.edit") && $edit_settlement_no_change) {

                                    $html .= '<li><a href="' . action("\Modules\Petro\Http\Controllers\SettlementController@edit", [$row->id]) . '?no_change=1" class="edit_settlement_button"><i class="fa fa-pencil-square-o"></i> ' . __("petro::lang.edit_no_change") . '</a></li>';

                                }

                                

                                if($this->moduleUtil->hasThePermissionInSubscription(request()->session()->get('user.business_id'), 'individual_sale')){

                                    $settlement = DB::table('transactions')->where('invoice_no',$row->settlement_no)->where('type','sell')->first();

                                    

                                    if(!empty($settlement)){

                                        if(strtotime($this->transactionUtil->__getVatEffectiveDate(request()->session()->get('user.business_id'))) <= strtotime($row->transaction_date)){

                                            $html .= '<li><a href="#" data-href="' . action('\Modules\Vat\Http\Controllers\VatController@updateSingleVats', ['transaction_id' => $settlement->id]) . '" class="regenerate-vat"><i class="fa fa-pencil"></i> ' . __("superadmin::lang.regenerate_vat") . '</a></li>';

                                        }

                                    }

                                    

                                }

                                

                                

                                if (!empty($first) && $first->id == $row->id && $delete_settlement && auth()->user()->can("settlement.delete")) {

                                   // commented By M Usman for hiding Delete Action

                                    $html .= '<li><a href="' . action("\Modules\Petro\Http\Controllers\SettlementController@destroy", [$row->id]) . '" class="delete_settlement_button"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';

                                }

                                $html .= '<li><a data-href="' . action("\Modules\Petro\Http\Controllers\SettlementController@print", [$row->id]) . '" class="print_settlement_button"><i class="fa fa-print"></i> ' . __("petro::lang.print") . '</a></li>';



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

                    ->addColumn('pump_nos', function($row) {
                        $pump_nos = '';
                        if (!empty($row->meter_sales) && $row->meter_sales->count() > 0) {
                            $_pump_nos = $row->meter_sales->pluck('pump_id')->toArray();
                            $_pumps = Pump::whereIn('id', $_pump_nos)->pluck('pump_no')->toArray();
                            $pump_nos = implode(', ', $_pumps);
                        }
                        return $pump_nos;
                    })

                    ->editColumn('shift', function ($row) {
                        if (!empty($row->work_shift) && is_array($row->work_shift)) {
                            $shifts = WorkShift::whereIn('id', $row->work_shift)->pluck('shift_name')->toArray();
                            return implode(',', $shifts);
                        }
                        return '';
                    })

                    ->addColumn('created_by', function ($row) {

                        $transaction = Transaction::where('invoice_no',$row->settlement_no)->leftJoin('users','users.id','transactions.created_by')->select('users.username')->first();

                        if(!empty($transaction)){

                            return $transaction->username;

                        }

                    })

                    ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')

                    // ->editColumn('total_amount', '{{@num_format($total_amount)}}')
                    ->editColumn('total_amount', function ($row) {
                    
                    $adjusted_total = $row->total_amount;
                    if(!empty($other_sales_discount))
                    {
                        if ($row->other_sales && $row->other_sales->count() > 0) {
                            $other_sales_discount = $row->other_sales->sum('discount_amount'); 
                            $adjusted_total -= $other_sales_discount;
                        }
                    }

                        return '<span class="total_amount">' . number_format($adjusted_total , 2, '.', ',') . '</span>';
                    })

                    ->setRowAttr([

                        'data-href' => function ($row) {

                            return  action('\Modules\Petro\Http\Controllers\SettlementController@show', [$row->id]);

                        }

                    ])
                    ->removeColumn('id');



                return $settlements->rawColumns(['action', 'status', 'total_amount'])

                    ->make(true);

            }

        }

        $business_locations = BusinessLocation::forDropdown($business_id);

        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');

        $settlement_nos = Settlement::where('business_id', $business_id)->pluck('settlement_no', 'id');



        $message = $this->transactionUtil->getGeneralMessage('general_message_pump_management_checkbox');



        return view('settlementsw::index')->with(compact(

            'business_locations',

            'pump_operators',

            'settlement_nos',

            'message'

        ));

    }
    public function create()
    {

        $reviewed = $this->transactionUtil->get_review(date('Y-m-d'), date('Y-m-d'));


        if (!empty($reviewed)) {

            $output = [

                'success' => 0,

                'msg' => "You can't add a settlement for an already reviewed date",

            ];


            return redirect()->back()->with(['status' => $output]);

        }


        $business_id = request()->session()->get('business.id');
        $services = Product::where('business_id', $business_id)->forModule('petro_settlements')->where('enable_stock', 0)->pluck('name', 'id');
        $products = Product::where('business_id', $business_id)->forModule('petro_settlements')->pluck('name', 'id');
        $business = Business::where('id', $business_id)->first();


        $expense_categories = ExpenseCategory::where('business_id', $business_id)
        ->pluck('name', 'id');
        $expense_account_type_id = AccountType::where('business_id', $business_id)->where('name', 'Expenses')->first();
        $expense_accounts = [];
        if ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account')) {
            if (!empty($expense_account_type_id)) {
                $expense_accounts = Account::where('business_id', $business_id)->where('account_type_id', $expense_account_type_id->id)->pluck('name', 'id');
            }
        }

        $pos_settings = json_decode($business->pos_settings, true);

        $check_qty = !isset($pos_settings['allow_overselling']) ? true : false; 

        $cash_denoms = !empty($pos_settings['cash_denominations']) ? explode(',', $pos_settings['cash_denominations']) : array();

        
        $business_locations = BusinessLocation::forDropdown($business_id);

        $default_location = current(array_keys($business_locations->toArray()));


        $payment_types = $this->productUtil->payment_types($default_location, false, false, false, false, "is_sale_enabled");

        $customers = Contact::customersDropdown($business_id, false);

        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');


        $items = [];


        $prefix = !empty($business->ref_no_prefixes['settlement_sw']) 
            ? $business->ref_no_prefixes['settlement_sw'] 
            : 'SET-SW';

        $starting_no = !empty($business->ref_no_starting_number['settlement_sw']) 
            ? (int)$business->ref_no_starting_number['settlement_sw'] 
            : 1;

        $count = Settlement::where('business_id', $business_id)
        ->where('settlement_no', 'LIKE', $prefix . '%')
        ->orderBy('id', 'DESC')->first();


        if (!empty($count)) {

            $count = $this->extractLastInteger($count->settlement_no);

        } else {

            $count = 0;

        }


        $settlement_no = $prefix . (1 + $count);


        $prefix = !empty($business->ref_no_prefixes['settlement_customer_payment']) 
            ? $business->ref_no_prefixes['settlement_customer_payment'] 
            : 'SW-CP';

        $starting_no = !empty($business->ref_no_starting_number['settlement_customer_payment']) 
            ? (int)$business->ref_no_starting_number['settlement_customer_payment'] 
            : 1;

        // Get the last settlement with prefix 'SW-CP'
        $last_customer_payment = CustomerPayment::where('business_id', $business_id)
            ->where('customer_payment_no', 'LIKE', $prefix . '%')
            ->whereNotNull('customer_payment_no')
            ->orderBy('id', 'DESC')
            ->first();

        if (!empty($last_customer_payment)) {
            $count = $this->extractLastInteger($last_customer_payment->customer_payment_no);
        } else {
            $count = 0;
        }


        $customer_payment_settlement_no = $prefix . (1 + $count);

        $prefix = !empty($business->ref_no_prefixes['settlement_expense']) 
            ? $business->ref_no_prefixes['settlement_expense'] 
            : 'SW-EXP';

        $starting_no = !empty($business->ref_no_starting_number['settlement_expense']) 
            ? (int)$business->ref_no_starting_number['settlement_expense'] 
            : 1;

        // Get the last settlement with prefix 'SW-EXP'
        $last_expense_payment = SettlementExpensePayment::where('business_id', $business_id)
            ->where('expense_number', 'LIKE', $prefix . '%')
            ->orderBy('id', 'DESC')
            ->first();

        if (!empty($last_expense_payment)) {
            $count = $this->extractLastInteger($last_expense_payment->expense_number);
        } else {
            $count = 0;
        }


        $expense_payment_settlement_no = $prefix . (1 + $count);


        $currency_precision = !empty($business->currency_precision) ? $business->currency_precision : 2;

        $meeter_precision = 3;

        $temp_data = DB::table('temp_data')->where('business_id', $business_id)->select('settlement_sw_data')->first();
        // dd($temp_data);
        
        $decoded = json_decode($temp_data->settlement_sw_data);

        if (!empty((array) $decoded)) {
            $temp_data = $decoded;
            $active_settlement = $temp_data;
            $settlement_no = $temp_data->settlement_no ?? null;
        }else{
            $active_settlement = Settlement::where('status', 1)
            ->where('business_id', $business_id)
            ->where('settlement_no', 'LIKE', 'SET-SW%')
            ->select('settlements.*')
            ->with(['meter_sales', 'other_sales', 'other_incomes', 'customer_payments'])->first();
        }

        $other_sale_final_total = 0.00;
        $pump_other_sale_final_total = 0.00;
        $combinedOtherSales = [];


        if ($active_settlement) {

            $shift_number = PumpOperatorAssignment::where('pump_operator_assignments.pump_operator_id', $active_settlement->pump_operator_id)
                ->leftJoin('settlements', 'pump_operator_assignments.settlement_id', '=', 'settlements.id')
                ->where(function ($query) {
                    $query->where('settlements.status', 1)
                        ->orWhereNull('pump_operator_assignments.settlement_id');
                })
                ->select('pump_operator_assignments.shift_number')
                ->groupBy('pump_operator_assignments.shift_number')
                ->get()->toarray();

            $userOtherDetails = [];

            foreach ((isset($active_settlement->other_sales) ? $active_settlement->other_sales : []) as $ot_item) {
                $product = \App\Product::find($ot_item->product_id);

                // dd($ot_item);
                $sub_total = isset($ot_item->sub_total) ? floatval(str_replace(',', '', $ot_item->sub_total)) : 0;
                $discount_amount = isset($ot_item->discount_amount) && is_numeric($ot_item->discount_amount)
                ? floatval(str_replace(',', '', $ot_item->discount_amount))
                : 0;
                $withDiscount = $sub_total - $discount_amount;

                $pump_other_sale_final_total += $withDiscount;

                // Prepare formatted array for user-entered sales
                $userOtherDetails[] = [
                    'id' => $ot_item->id,
                    'sku' => !empty($product) ? $product->sku : '',
                    'name' => !empty($product) ? $product->name : '',
                    'balance_stock' => number_format(floatval(str_replace(',', '', $ot_item->balance_stock)), 4, '.', ','),
                    'price' => number_format(floatval(str_replace(',', '', $ot_item->price)), $currency_precision),
                    'qty' => number_format(floatval(str_replace(',', '', $ot_item->qty)), 4, '.', ','),
                    'discount_type' => $ot_item->discount_type,
                    'discount' => number_format(floatval(str_replace(',', '', $ot_item->discount)), $currency_precision),
                    'sub_total' => number_format(floatval(str_replace(',', '', $ot_item->sub_total)), $currency_precision),
                    'with_discount' => number_format($withDiscount, $currency_precision),
                    'user_check' => 1 // 1 for user entry
                ];
            }

            // ✅ Prepare shift_ids
            $shiftIds = array_column($shift_number, 'shift_id');

            // ✅ Pump operator other sale query
            $query = PumpOperatorOtherSale::join('products', 'products.id', '=', 'pump_operator_other_sales.product_id')
                ->leftJoin('variations', 'products.id', 'variations.product_id')
                ->leftJoin('variation_location_details', 'variations.id', 'variation_location_details.variation_id')
                ->whereIn('pump_operator_other_sales.shift_id', $shiftIds)
                ->join('pump_operator_assignments', function ($join) {
                    $join->on('pump_operator_assignments.shift_id', '=', 'pump_operator_other_sales.shift_id')
                        ->where('pump_operator_assignments.status', 'close')
                        ->whereRaw('pump_operator_assignments.id = (
                             SELECT MAX(poa.id)
                             FROM pump_operator_assignments poa
                             WHERE poa.shift_id = pump_operator_other_sales.shift_id AND poa.status = "close"
                         )');
                })
                ->select(
                    'pump_operator_other_sales.*',
                    'products.name as product_name',
                    'products.sku as product_sku',
                    'pump_operator_assignments.shift_number',
                    'qty_available'
                )
                ->groupBy('pump_operator_other_sales.id');

            // ✅ Pump operator data processing
            $pumperOthersaleDetails = [];

            $pumpSales = $query->get();

            foreach ($pumpSales as $pumpSale) {
                $discount_amount = $pumpSale->discount ?? 0;
                $withDiscount = ($pumpSale->sub_total ?? 0) - $discount_amount;

                $pump_other_sale_final_total += $withDiscount;

                // Prepare formatted array for pump-operator-entered sales
                $pumperOthersaleDetails[] = [
                    'sku' => $pumpSale->product_sku,
                    'name' => $pumpSale->product_name,
                    'balance_stock' => number_format($pumpSale->qty_available, 4, '.', ','),
                    'price' => number_format($pumpSale->price, $currency_precision),
                    'qty' => number_format($pumpSale->quantity, 4, '.', ','),
                    'discount_type' => $pumpSale->discount_type,
                    'discount' => number_format($pumpSale->discount, $currency_precision),
                    'sub_total' => number_format($pumpSale->sub_total, $currency_precision),
                    'with_discount' => number_format($withDiscount, $currency_precision),
                    'user_check' => 0 // 0 for pump operator entry
                ];
            }

            // ✅ Merge both user and pump operator details
            $combinedOtherSales = array_merge($userOtherDetails, $pumperOthersaleDetails);

            // ✅ Final Total of both
            $final_other_sale_total = $other_sale_final_total + $pump_other_sale_final_total;

        }


        //$combinedOtherSales = []; $pump_other_sale_final_total = 0;

        $business_locations = BusinessLocation::forDropdown($business_id);

        $default_location = current(array_keys($business_locations->toArray()));

        if (!empty($active_settlement) && isset($active_settlement->id)) {

            $already_pumps = MeterSale::where('settlement_no', $active_settlement->id ?? null)->pluck('pump_id')->toArray();

            $pump_nos = Pump::where('business_id', $business_id)->whereNotIn('id', $already_pumps)->pluck('pump_name', 'id');

        } else {

            $pump_nos = Pump::where('business_id', $business_id)->pluck('pump_name', 'id');

        }


        //other_sale tab

        $stores = Store::forDropdown($business_id, 0, 1, 'sell');


        $fuel_category_id = Category::where('business_id', $business_id)->where('name', 'Fuel')->first();

        $fuel_category_id = !empty($fuel_category_id) ? $fuel_category_id->id : null;

        $items = $this->transactionUtil->getProductDropDownArray($business_id, $fuel_category_id, 'petro_settlements');
        $filtered = array_filter($items, function ($value) {
            if (preg_match('/Available Qty\s*:\s*([\d,\.]+)\s/', $value, $matches)) {
                $qty = floatval(str_replace(',', '', $matches[1]));
                return $qty > 0;
            }
            return false;
        });
        $items = $filtered;
        // other income tab

        $services = Product::where('business_id', $business_id)->forModule('petro_settlements')->where('enable_stock', 0)->pluck('name', 'id');

        $subscription = Subscription::active_subscription($business_id);

        $package_details = $subscription->package_details;

        $only_walkin = $package_details['only_walkin'] ?? 0;

        if(!empty($only_walkin)){
            $credit_customers = Contact::customersDropdown($business_id, false, true, 'customer');
        }else{
            $credit_customers = Contact::where('name','!=','Walk-In Customer')->where('type','customer')->where('business_id', $business_id)->pluck('name','id');
        }


        if (is_null($subscription)) {
            $show_shift_no = false;
        } else {
            if ($subscription->customer_credit_notification_type == []) {
                $show_shift_no = false;
            } else {
                $firstDecode = json_decode($subscription->customer_credit_notification_type, true);
                if (is_string($firstDecode)) {
                    $decodedData = json_decode($firstDecode, true);
                    $show_shift_no = in_array("pumper_dashboard", $decodedData) ? true : false;
                } else {
                    $show_shift_no = false;
                }
            }
        }


        $payment_meter_sale_total = !empty($active_settlement->meter_sales) && isset($active_settlement->id) ? $active_settlement->meter_sales->sum('discount_amount') : 0.00;

        $payment_other_sale_total = !empty($active_settlement->other_sales) && isset($active_settlement->id) ? $active_settlement->other_sales->sum('sub_total') : 0.00;


        $payment_other_sale_discount = !empty($active_settlement->other_sales) && isset($active_settlement->id) ? $active_settlement->other_sales->sum('discount_amount') : 0.00;


        $payment_other_sale_total -= $payment_other_sale_discount;


        $payment_other_income_total = !empty($active_settlement->other_incomes) && isset($active_settlement->id) ? $active_settlement->other_incomes->sum('sub_total') : 0.00;

        $payment_customer_payment_total = !empty($active_settlement->customer_payments) && isset($active_settlement->id) ? $active_settlement->customer_payments->sum('sub_total') : 0.00;


        $wrok_shifts = WorkShift::where('business_id', $business_id)->pluck('shift_name', 'id');

        $bulk_tanks = FuelTank::where('business_id', $business_id)->where('bulk_tank', 1)->pluck('fuel_tank_number', 'id');


        $select_pump_operator_in_settlement = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'select_pump_operator_in_settlement');


        $message = $this->transactionUtil->getGeneralMessage('general_message_pump_management_checkbox');

        $discount_types = ['fixed' => 'Fixed', 'percentage' => 'Percentage']; 

        // if (!request()->session()->get('business.popup_load_save_data')) {

        //     $temp_data = [];
        // }

        $settlement_expense_payments = SettlementExpensePayment::leftjoin('accounts', 'settlement_expense_payments.account_id', 'accounts.id')
            ->leftjoin('expense_categories', 'settlement_expense_payments.category_id', 'expense_categories.id')
            ->where('settlement_expense_payments.settlement_no', $expense_payment_settlement_no)
            ->select('settlement_expense_payments.*', 'accounts.name as account_name', 'expense_categories.name as category_name')
            ->get();

        $currency_precision = $business->currency_precision;

        $subscription = Subscription::active_subscription($business_id);
        $pacakge_details = $subscription->package_details;
        
        $is_other_income = $package_details['settlement_sw_other_income'] ?? 0;
        $is_customer_payments = $package_details['settlement_sw_customer_payments'] ?? 0;
        $is_expenses = $package_details['settlement_sw_expenses'] ?? 0;
        $is_payment = $package_details['settlement_sw_payment'] ?? 0;

        $accounts = [];
        $accounts = Account::forDropdown($business_id, true, false, true);
        $location_id=$payment_types['location_id'];
        $group_id = $this->moduleUtil->one_payment_type('card', $location_id);
        $account_modules = Account::getAccountByAccountGroupId($group_id);

        // dd($account_modules, $group_id, $location_id);
        return view('settlementsw::create')->with(compact(

            'select_pump_operator_in_settlement',

            'message',

            'is_other_income',

            'is_customer_payments',

            'is_payment',

            'is_expenses',

            'business_locations',

            'temp_data',

            'accounts',

            'account_modules',

            'payment_types',

            'customer_payment_settlement_no',

            'expense_categories',

            'expense_accounts',
            'expense_payment_settlement_no',
            'settlement_expense_payments',
            'currency_precision',

            'customers',

            'pump_operators',

            'wrok_shifts',

            'pump_nos',

            'items',

            'settlement_no',

            'default_location',

            'active_settlement',

            'stores',

            'payment_meter_sale_total',

            'payment_other_sale_total',

            'payment_other_income_total',

            'payment_customer_payment_total',

            'bulk_tanks',

            'services',

            'discount_types',

            'cash_denoms',

            'check_qty',

            'payment_other_sale_discount',

            'show_shift_no',

            'combinedOtherSales',

            'pump_other_sale_final_total',

            'only_walkin',

            'credit_customers',

            'products'
        ));
    }

    public function saveMeterSale(Request $request)

    {

        try {

            $business_id = $request->session()->get('business.id');

            $business_locations = BusinessLocation::forDropdown($business_id);

            $default_location = current(array_keys($business_locations->toArray()));



            DB::beginTransaction();



            $settlement_exist = $this->createSettlementIfNotExist($request);



            if(is_int($settlement_exist) && $settlement_exist == 406){

                return ['success' => false,

                    'msg' => __('petro::lang.date_greater_than_day_end')

                ];

            }



            $pump = Pump::where('id', $request->pump_id)->first();
            $fuel_tank = FuelTank::where('id', $pump->fuel_tank_id)->first();



            $product = Variation::leftjoin('products', 'variations.product_id', 'products.id')

                ->leftjoin('variation_location_details', 'variations.id', 'variation_location_details.variation_id')

                ->where('products.id', $fuel_tank->product_id)

                ->select('sku', 'variations.sell_price_inc_tax as default_sell_price', 'products.name', 'products.id', 'variation_location_details.qty_available')->first();

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



            $meter_sale = MeterSale::create($data);



            if(!empty($request->is_from_pumper)){

                logger($request->pumper_entry_id);

                logger($request->assignment_id);



                PumperDayEntry::where('id', $request->pumper_entry_id)

                    ->update(['settlement_no' => $request->settlement_no,'settlement_added_by' => auth()->user()->id,'closed_in_settlement' => 1]);



                PumpOperatorAssignment::where('id',$request->assignment_id)->update(array('closed_in_settlement' => 1));

            }



            Settlement::where('id',$settlement_exist->id)->update(['is_edit' => request()->is_edit]);



            // add pump operator commission

            $pump_operator = PumpOperator::find($settlement_exist->pump_operator_id);

            if(!empty($pump_operator)){

                if(!empty($pump_operator->commission_type) && !empty($pump_operator->commission_ap)){

                    $commission_amount = 0;

                    $discounted_amount = $request->discount_amount;

                    if($pump_operator->commission_type == 'percentage'){

                        $commission_amount = $discounted_amount * $pump_operator->commission_ap / 100;

                    }



                    if($pump_operator->commission_type == 'fixed'){

                        $commission_amount = $request->qty * $pump_operator->commission_ap;

                    }



                    $commission_data = array(

                        'pump_operator_id' => $settlement_exist->pump_operator_id,

                        'meter_sale_id' => $meter_sale->id,

                        'transaction_date' => $settlement_exist->transaction_date,

                        'amount' => $commission_amount,

                        'type' => $pump_operator->commission_type,

                        'value' => $pump_operator->commission_ap

                    );

                    PumpOperatorCommission::create($commission_data);

                }

            }



            Pump::where('id', $request->pump_id)->update(['starting_meter' => $request->starting_meter, 'last_meter_reading' => $request->closing_meter]);



            DB::commit();

            $afterDiscount = $request->sub_total; // Default value if no discount is applied

            if ($request->discount_type === 'fixed' && $request->discount > 0) {
                $afterDiscount = $request->sub_total - (float)($request->discount);
            }

            if ($request->discount_type === 'percentage' && $request->discount > 0) {
                $afterDiscount = $request->sub_total - (($request->sub_total * (float)($request->discount)) / 100);
            }
            $business = Business::where('id', $business_id)->first();
            $currency_precision = $business->currency_precision;

            $output = [

                'success' => true,

                'msg' => 'success',

                'data' => [
                    'meter_sale_id' => $meter_sale->id,
                    'product_name' => optional($meter_sale->product)->name,
                    'pump_no' => optional($pump)->pump_no ?? '',
                    'pump_start' => number_format((float) $request->starting_meter, 3, '.', ''),
                    'pump_close' => number_format((float) $request->closing_meter, 3, '.', ''),
                    'unit_price' => number_format((float) $request->price, $currency_precision, '.', ''),
                    'sold_qty' => number_format((float) $request->qty, 3, '.', ''),
                    'discount_type' => $request->discount_type,
                    'discount_val' => number_format((float) $request->discount, $currency_precision, '.', ''),
                    'testing_qty' => number_format((float) $request->testing_qty, 3, '.', ''),
                    'total_qty' => number_format((float) $request->qty + (float) $request->testing_qty, 3, '.', ''),
                    'sub_total' => number_format((float) $request->sub_total, $currency_precision, '.', ''),
                    'after_discount' => number_format((float) $afterDiscount, $currency_precision, '.', ''),
                    'product' => $product
                ]

            ];

        } catch (\Exception $e) {
           echo json_encode($e->getMessage());exit();
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return $output;

    }

    function extractLastInteger($text) {

        if (preg_match('/\d+$/', $text, $matches)) {

            return intval($matches[0]);

        } else {

            return 0;

        }



    }

    public function createSettlementIfNotExist(Request $request)

    {

        $business_id = $request->session()->get('business.id');

        $settlement_data = array(

            'settlement_no' => $request->settlement_no,

            'business_id' => $business_id,

            'transaction_date' => \Carbon::parse($request->transaction_date)->format('Y-m-d'),

            'location_id' => $request->location_id,

            'pump_operator_id' => $request->pump_operator_id,

            'work_shift' => !empty($request->work_shift) ? $request->work_shift : [],

            'note' => $request->note,

            'status' => 1

        );



        $latest_date = DayEnd::where('business_id',$business_id)->get()->last()->day_end_date ?? null;

        if(!empty($latest_date) && strtotime($latest_date) >= strtotime($settlement_data['transaction_date'])){

            return 406;

        }





        $settlement_exist = Settlement::where('settlement_no', $request->settlement_no)->where('business_id', $business_id)->first();

        if (empty($settlement_exist)) {

            $settlement_exist = Settlement::create($settlement_data);

        }



        return $settlement_exist;

    }

    public function getPumpDetails($pump_id)

    {

        $pump = Pump::where('id', $pump_id)->first();

        $last_meter_reading = $pump->last_meter_reading;

        $last_meter_sale = MeterSale::where('pump_id', $pump_id)->orderBy('id', 'desc')->first();

        if (!empty($last_meter_sale)) {

            $last_meter_reading = !empty($last_meter_sale->meter_reset_value) ? $last_meter_sale->meter_reset_value : $last_meter_sale->closing_meter;

        }









        $ass = PumpOperatorAssignment::where('pump_id', $pump_id)->where('closed_in_settlement',0)->orderBy('id', 'desc')->first();







        $po_closing = 0;

        $day_entry = null;



        if(!empty($ass)){

            $po_closing = $ass->closing_meter;

            $day_entry = PumperDayEntry::where('pump_id', $pump_id)->where('pumper_assignment_id', $ass->id)->where('closed_in_settlement',0)->first();

        }





        $fuel_tank = FuelTank::where('id', $pump->fuel_tank_id)->first();



        $product = Variation::leftjoin('products', 'variations.product_id', 'products.id')

            ->leftjoin('variation_location_details', 'variations.id', 'variation_location_details.variation_id')

            ->where('products.id', $fuel_tank->product_id)

            ->select('sku', 'variations.sell_price_inc_tax as default_sell_price', 'products.name', 'products.id', 'variation_location_details.qty_available')->first();

        $business_id = request()->session()->get('business.id');
        $business = Business::where('id', $business_id)->first();
        $currency_precision = $business->currency_precision;
        $product->default_sell_price = number_format($product->default_sell_price, $currency_precision, '.', '');



        $current_balance = $this->transactionUtil->getTankBalanceById($pump->fuel_tank_id);



        return [

            'colsing_value' => number_format($last_meter_reading, 3, '.', ''),

            'tank_remaing_qty' => $current_balance,

            'product' => $product,

            'pump_name' => $pump->pump_name,

            'product_id' => $product->id,

            'pump_id' => $pump->id,

            'bulk_sale_meter' => $pump->bulk_sale_meter,

            'po_closing' => number_format(($last_meter_reading >= $po_closing ? 0 : $po_closing), 3, '.', ''),

            'po_testing' => number_format(($last_meter_reading >= $po_closing ? 0 : (!empty($day_entry) ? $day_entry->testing_ltr : 0)), 3, '.', ''),



            'assignment_id' => !empty($ass) ? $ass->id : 0,

            'pumper_entry_id' => !empty($day_entry) ? $day_entry->id : 0,


        ];

    }



    /**

     * get balance stock of product

     * @param product_id

     * @return Response

     */



    public function getPumps($id){

        try {

            $business_id = request()->session()->get('business.id');

            $assigned_pumps = PumpOperatorAssignment::where('pump_operator_id',$id)->where('settlement_id', null)->whereDate('date_and_time', date('Y-m-d'))->pluck('pump_id');

            if(!empty($assigned_pumps) && sizeof($assigned_pumps) > 0){

                $pumps = Pump::where('business_id', $business_id)->whereIn('id',$assigned_pumps)->pluck('pump_name', 'id');

            }else{

                $pumps = Pump::where('business_id', $business_id)->pluck('pump_name', 'id');

            }



            $output = [

                'success' => true,

                'pumps' => $pumps,

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

    public function saveOtherSale(Request $request)

    {

        try {

            $business_id = $request->session()->get('business.id');



            $settlement_exist = $this->createSettlementIfNotExist($request);



            if(is_int($settlement_exist) && $settlement_exist == 406){

                return ['success' => false,

                    'msg' => __('petro::lang.date_greater_than_day_end')

                ];

            }

            $data = array(

                'business_id' => $business_id,

                'settlement_no' => $settlement_exist->id,

                'store_id' => $request->store_id,

                'product_id' => $request->product_id,

                'price' => $request->price,

                'qty' => $request->qty,

                'balance_stock' => $request->balance_stock,

                'discount' => $request->discount,

                'discount_type' => $request->discount_type,

                'discount_amount' => $request->discount_amount,

                'sub_total' => $request->sub_total

            );

            $other_sale = OtherSale::create($data);

            Settlement::where('id',$settlement_exist->id)->update(['is_edit' => request()->is_edit]);



            $output = [

                'success' => true,

                'other_sale_id' => $other_sale->id,

                'msg' => __('petro::lang.success')

            ];

        } catch (\Exception $e) {

            var_dump($e->getMessage());exit();

            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return $output;

    }

    public function getBalanceStockById(Request $request, $id)
    {
        try {
            $product = Product::join('variations', 'products.id', '=', 'variations.product_id')
                ->leftJoin('variation_location_details', function($join) use ($id, $request) {
                    $join->on('variations.id', '=', 'variation_location_details.variation_id')
                        ->where('variation_location_details.product_id', '=', $id)
                        ->where('variation_location_details.location_id', '=', $request->location_id);
                })
                ->leftJoin('variation_store_details', function($join) use ($request) {
                    $join->on('variations.id', '=', 'variation_store_details.variation_id')
                        ->where('variation_store_details.store_id', '=', $request->store_id);
                })
                ->where('products.id', $id)
                ->select(
                    DB::raw('COALESCE(variation_store_details.qty_available, 0) as qty_available'),
                    DB::raw('COALESCE(sell_price_inc_tax, 0) as sell_price_inc_tax'),
                    'products.name',
                    'products.sku'
                )
                ->first();

            $output = [
                'success' => true,
                'balance_stock' => $product->qty_available,
                'price' => $product->sell_price_inc_tax,
                'product_name' => $product->name,
                'code' => $product->sku,
                'msg' => __('petro::lang.success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }


    public function saveOtherIncome(Request $request)

    {

        try {

            $business_id = $request->session()->get('business.id');



                $settlement_exist = $this->createSettlementIfNotExist($request);
                if(is_int($settlement_exist) && $settlement_exist == 406){
    
                    return ['success' => false,
    
                        'msg' => __('petro::lang.date_greater_than_day_end')
    
                    ];
    
                }
            


            $data = array(

                'business_id' => $business_id,

                'settlement_no' => $settlement_exist ? $settlement_exist->id : '',

                'product_id' => $request->product_id,

                'qty' => $request->qty,

//                'price' => $request->price,

                'reason' => $request->other_income_reason,

//                'sub_total' => $request->sub_total

            );

            $other_income = OtherIncome::create($data);
// Calculate total qty for the same business
            $total_qty = OtherIncome::where('business_id', $business_id)->sum('qty');
            Settlement::where('id',$settlement_exist->id)->update(['is_edit' => request()->is_edit]);



            $output = [

                'success' => true,

                'other_income_id' => $other_income->id,

                'msg' => __('SettlementSW::lang.success'),
                'data' => [
                    'qty' => $other_income->qty,
                    'reason' => $other_income->reason,
                    'total_othe_income_for_business' => $total_qty
                ]

            ];

        } catch (\Exception $e) {
            echo json_encode($e->getMessage());exit();

            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return $output;

    }


    public function saveCustomerPayment(Request $request)

    {

        try {

            $business_id = $request->session()->get('business.id');


                $settlement_exist = $this->createSettlementIfNotExist($request);
                if(is_int($settlement_exist) && $settlement_exist == 406){
    
                    return ['success' => false,
    
                        'msg' => __('petro::lang.date_greater_than_day_end')
    
                    ];
    
                }
            




            $data = array(

                'business_id' => $business_id,

                'settlement_no' => $settlement_exist ? $settlement_exist->id : '',

                'customer_id' => $request->customer_id,
                'customer_payment_no' => $request->settlement_customer_payment_no,

                'payment_method' => $request->payment_method,

                'cheque_date' => !empty($request->cheque_date) ? \Carbon::parse($request->cheque_date)->format('Y-m-d') : null,

                'cheque_number' => $request->cheque_number,

                'bank_name' => $request->bank_name,

                'amount' => $request->amount,

                'sub_total' => $request->sub_total,

                'post_dated_cheque' => $request->post_dated_cheque

            );

            DB::beginTransaction();

            $customer_payment = CustomerPayment::create($data);
            Settlement::where('id',$settlement_exist->id)->update(['is_edit' => request()->is_edit]);







            if ($request->payment_method == 'cash') {

                $cash_data = array(

                    'business_id' => $business_id,

                    'settlement_no' => $settlement_exist ? $settlement_exist->id : '',

                    'amount' => $request->amount,

                    'customer_id' => $request->customer_id,

                    'customer_payment_id' => $customer_payment->id

                );



                $settlement_cash_payment = SettlementCashPayment::create($cash_data);

            }

            if ($request->payment_method == 'card') {

                $card_data = array(

                    'business_id' => $business_id,

                    'settlement_no' => $settlement_exist ? $settlement_exist->id : '',

                    'amount' => $request->amount,

                    'card_type' => $request->card_type,

                    'card_number' => $request->card_number,

                    'customer_id' => $request->customer_id,

                    'customer_payment_id' => $customer_payment->id

                );



                $settlement_card_payment = SettlementCardPayment::create($card_data);

            }

            if ($request->payment_method == 'cheque') {

                $cheque_data = array(

                    'business_id' => $business_id,

                    'settlement_no' => $settlement_exist ? $settlement_exist->id : '',

                    'amount' => $request->amount,

                    'bank_name' => $request->bank_name,

                    'cheque_number' => $request->cheque_number,

                    'cheque_date' => !empty($request->cheque_date) ? \Carbon::parse($request->cheque_date)->format('Y-m-d') : null,

                    'customer_id' => $request->customer_id,

                    'customer_payment_id' => $customer_payment->id

                );



                $settlement_cheque_payment = SettlementChequePayment::create($cheque_data);

            }

            DB::commit();
            $business = Business::where('id', $business_id)->first();

            $prefix = !empty($business->ref_no_prefixes['settlement_customer_payment']) 
                ? $business->ref_no_prefixes['settlement_customer_payment'] 
                : 'SW-CP';

            $starting_no = !empty($business->ref_no_starting_number['settlement_customer_payment']) 
                ? (int)$business->ref_no_starting_number['settlement_customer_payment'] 
                : 1;

            // Get the last settlement with prefix 'SW-CP'
            $last_customer_payment = CustomerPayment::where('business_id', $business_id)
                ->where('customer_payment_no', 'LIKE', $prefix . '%')
                ->whereNotNull('customer_payment_no')
                ->orderBy('id', 'DESC')
                ->first();

            if (!empty($last_customer_payment)) {
                $count = $this->extractLastInteger($last_customer_payment->customer_payment_no);
            } else {
                $count = 0;
            }


            $customer_payment_settlement_no = $prefix . (1 + $count);

            // Get customer name
            $customer_name = Contact::where('id', $request->customer_id)->value('name');

            // Get total payments for the customer
            $total_customer_payments = CustomerPayment::where('customer_id', $request->customer_id)
                ->where('business_id', $business_id)
                ->sum('amount');

            $total_business_payments = CustomerPayment::where('business_id', $business_id)
                ->sum('amount');

            $output = [

                'success' => true,
                'customer_payment_id' => $customer_payment->id,
                'customer_name' => $customer_name,
                'settlement_no' => $customer_payment_settlement_no,
                'total_customer_payments' => $total_customer_payments,
                'total_business_payments' => $total_business_payments,
                'msg' => __('petro::lang.success')

            ];

        } catch (\Exception $e) {

            // echo json_encode($e->getMessage());exit();
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return $output;

    }

    public function saveExpansePayment(Request $request)

    {
        try {
            $business_id = $request->session()->get('business.id');
            $business = Business::where('id', $business_id)->first();
            $settlement = Settlement::where('settlement_no', $request->settlement_no)->where('business_id', $business_id)->first();
            $data = array(
                'business_id' => $business_id,
                'settlement_no' => $settlement ? $settlement->id : '',
                'expense_number' => $request->expense_number,
                'category_id' => $request->category_id,
                'reference_no' => $request->reference_no,
                'account_id' => $request->account_id,
                'reason' => $request->reason,
                'amount' => $request->amount,
            );

            //Update reference count
            $ref_count = $this->transactionUtil->setAndGetReferenceCount('expense');
            //Generate reference number
            if (empty($request->reference_no)) {
                $data['reference_no'] = $this->transactionUtil->generateReferenceNumber('expense', $ref_count);
            }

            $settlement_expense_payment = SettlementExpensePayment::create($data);
            if(isset($settlement)){
            Settlement::where('id',$settlement->id)->update(['is_edit' => request()->is_edit]);
            }

            $prefix = !empty($business->ref_no_prefixes['settlement_expense']) 
            ? $business->ref_no_prefixes['settlement_expense'] 
            : 'SW-EXP';

            $starting_no = !empty($business->ref_no_starting_number['settlement_expense']) 
                ? (int)$business->ref_no_starting_number['settlement_expense'] 
                : 1;

            // Get the last settlement with prefix 'SW-EXP'
            $last_expense_payment = SettlementExpensePayment::where('business_id', $business_id)
                ->where('expense_number', 'LIKE', $prefix . '%')
                ->orderBy('id', 'DESC')
                ->first();

            if (!empty($last_expense_payment)) {
                $count = $this->extractLastInteger($last_expense_payment->expense_number);
            } else {
                $count = 0;
            }


            $expense_payment_settlement_no = $prefix . (1 + $count);

            $output = [
                'success' => true,
                'expense_number' => $expense_payment_settlement_no,
                'reference_no' => $settlement_expense_payment->reference_no,
                'settlement_expense_payment_id' => $settlement_expense_payment->id,
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


    public function saveCreditSalePayment(Request $request)
    {
        try {
            $business_id = $request->session()->get('business.id');

            if(!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'same_order_no')){
                $validator = Validator::make($request->all(), [
                    'order_number' => [
                        'required',
                        Rule::unique('settlement_credit_sale_payments', 'order_number')
                            ->where(function ($query) use ($request) {
                                return $query->where('customer_id', $request->customer_id);
                            }),
                    ],
                ]);

                if ($validator->fails()) {
                    return [
                        'success' => false,
                        'msg' => $validator->errors()->first()
                    ];
                }
            }

            $settlement = Settlement::where('settlement_no', $request->settlement_no)->where('business_id', $business_id)->first();
            Settlement::where('id',$settlement->id)->update(['is_edit' => request()->is_edit]);


            $price = $this->productUtil->num_uf($request->price);
            $unit_discount = $this->productUtil->num_uf($request->unit_discount);
            $qty = $this->productUtil->num_uf($request->qty);
            $amount = $this->productUtil->num_uf($request->amount);
            $sub_total = $this->productUtil->num_uf($request->sub_total);
            $total_discount = $this->productUtil->num_uf($request->total_discount);


            $data = array(
                'business_id' => $business_id,
                'settlement_no' => $settlement->id,
                'customer_id' => $request->customer_id,
                'product_id' => $request->product_id,
                'order_number' => $request->order_number,
                'order_date' => \Carbon::parse($request->order_date)->format('Y-m-d'),
                'price' => $price,
                'discount' => $unit_discount,
                'qty' => $qty,
                'amount' => $amount,
                'sub_total' => $sub_total,
                'total_discount' => $total_discount,
                'outstanding' => $this->productUtil->num_uf($request->outstanding),
                'credit_limit' => $request->credit_limit,
                'customer_reference' => $request->customer_reference,
                'note' => $request->note
            );
            $settlement_credit_sale_payment = SettlementCreditSalePayment::create($data);

            // Calculate total credit sales for this business
            $total_credit_sales = SettlementCreditSalePayment::where('business_id', $business_id)
                ->sum('sub_total');

            $output = [
                'success' => true,
                'settlement_credit_sale_payment_id' => $settlement_credit_sale_payment->id,
                'total_credit_sales' => $total_credit_sales,
                'msg' => __('petro::lang.success')
            ];
        } catch (\Exception $e) {
            echo json_encode($e->getMessage());
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

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

            $meter_sale = MeterSale::where('id', $id)->first();

            Settlement::where('id',$meter_sale->settlement_no)->update(['is_edit' => request()->is_edit]);



            $amount = $meter_sale->discount_amount;

            $starting_meter = $meter_sale->starting_meter;

            $closing_meter = $meter_sale->closing_meter;

            $pump = Pump::where('id', $meter_sale->pump_id)->first();

            $tank_id = $pump->fuel_tank_id;

            FuelTank::where('id', $tank_id)->increment('current_balance', $meter_sale->qty);

            $meter_sale->delete();

            $pump->last_meter_reading = $starting_meter; //reset back to previous starting meter



            $previous_meter_sale = MeterSale::where('pump_id', $pump->id)->orderBy('id', 'desc')->first();

            if (!empty($previous_meter_sale)) {

                $pump->starting_meter = $previous_meter_sale->starting_meter;

            }

            $pump->save();



            $pump_name = $pump->pump_name;

            $pump_id = $pump->id;



            // delete pump operator commission

            PumpOperatorCommission::where('meter_sale_id',$id)->delete();



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


    public function deleteOtherSale($id)

    {

        try {

            $other_sale = OtherSale::where('id', $id)->first();

            Settlement::where('id',$other_sale->settlement_no)->update(['is_edit' => request()->is_edit]);

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


}
