<?php

namespace Modules\Petro\Http\Controllers;

use App\Account;
use App\AccountGroup;

use App\Business;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\NotificationUtil;
use App\Utils\Util;
use App\Utils\ContactUtil;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; 
use Modules\Petro\Entities\Pump;
use Modules\Petro\Entities\PumperDayEntry;
use Modules\Petro\Entities\PumpOperator;
use Modules\Petro\Entities\PumpOperatorAssignment;
use Modules\Petro\Entities\PumpOperatorPayment;
use Yajra\DataTables\Facades\DataTables;
use Modules\Petro\Entities\DailyCard;
use Modules\Petro\Entities\DailyCollection;
use Modules\Petro\Entities\Settlement;
use App\Contact;
use Milon\Barcode\DNS2D;
use Modules\Petro\Entities\FuelTank;
use App\Product;
use App\Store;
use Modules\Superadmin\Entities\Subscription;
;
use Modules\Petro\Entities\SettlementCreditSalePayment;
use App\Transaction;
use App\NotificationTemplate;
use App\Http\Controllers\ContactController;
use App\AccountTransaction;
use App\CustomerReference;
use App\ContactLedger;
use Modules\Petro\Entities\DailyVoucher;
use Modules\Petro\Entities\DailyVoucherItem;

use Modules\Petro\Entities\PumpOperatorMeterSale;
use Modules\Petro\Entities\PumpOperatorOtherSale;
use Modules\Petro\Entities\PumpOperatorMeterSaleDetail;
use Modules\Petro\Entities\PetroShift;
use Modules\Petro\Entities\DailyChequePayment;
use App\Category;

class PumpOperatorPaymentController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $moduleUtil;
    protected $transactionUtil;
    protected $commonUtil;
    protected $contactUtil;
    protected $notificationUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, TransactionUtil $transactionUtil, BusinessUtil   $businessUtil,ContactUtil $contactUtil, NotificationUtil $notificationUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
        $this->contactUtil = $contactUtil;
        $this->notificationUtil = $notificationUtil;

        //barcode types
        $this->barcode_types = $this->productUtil->barcode_types();
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $business_id =  Auth::user()->business_id;
        $pump_operator_id = Auth::user()->pump_operator_id;
        $business_details = Business::find($business_id);

        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module')) {
            abort(403, 'Unauthorized Access');
        }

        $only_pumper = request()->only_pumper;
        $shift_id = request()->shift_id;

        if (request()->ajax()) {
            $business_id =  Auth::user()->business_id;
            $query = PumpOperatorPayment::leftjoin('pump_operators', 'pump_operator_payments.pump_operator_id', 'pump_operators.id')
                ->leftjoin('users as edited_user', 'pump_operator_payments.edited_by', 'edited_user.id')
                ->leftjoin('business_locations','business_locations.id','pump_operators.location_id')
                ->where('pump_operators.business_id', $business_id)
                ->select('pump_operator_payments.*', 'pump_operators.name as pump_operator_name', 'edited_user.username as edited_by','business_locations.name as location_name');

            if ($only_pumper) {
                $query->where('pump_operator_payments.pump_operator_id', $pump_operator_id);
            }
            
            if (!empty($shift_id)) {
                $query->where('pump_operator_payments.shift_id', $shift_id);
            }
            
            if (!empty(request()->payment_method)) {
                $query->where('payment_type', request()->payment_method);
            }
            if (!empty(request()->location_id)) {
                $query->where('pump_operators.location_id', request()->location_id);
            }
            if (!empty(request()->pump_operator_id)) {
                $query->where('pump_operator_id', request()->pump_operator_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $query->whereDate('date_and_time', '>=', request()->start_date);
                $query->whereDate('date_and_time', '<=', request()->end_date);
            }

            $fuel_tanks = DataTables::of($query)
                ->addColumn(
                    'action',
                    function ($row) use($pump_operator_id, $business_id, $only_pumper) {
                        $html = '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                            data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        $is_shift_close = null;
                        if($only_pumper){
                            $is_shift_close =  PumpOperatorAssignment::where('pump_operator_id', $pump_operator_id)->where('business_id', $business_id)->where('status', 'open')->count();
                            if($is_shift_close){
                                $html .= '<li><a href="#" data-href="' . action('\Modules\Petro\Http\Controllers\PumpOperatorPaymentController@edit', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                            }
                        }else{
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Petro\Http\Controllers\PumpOperatorPaymentController@edit', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        }


                        return $html;
                    }
                )
                ->addColumn('date', '{{@format_date($date_and_time)}}')
                ->addColumn('time', '{{@format_time($date_and_time)}}')
                ->removeColumn('id')
                ->editColumn('payment_type', '{{ucfirst($payment_type)}}')
                ->editColumn(
                    'amount',
                    function ($row) use ($business_details) {
                        return  '<span class="display_currency amount" data-orig-value="' . $row->payment_amount . '" data-currency_symbol = false>' . $this->productUtil->num_f($row->payment_amount, false, $business_details, true) . '</span>';
                    }
                );

            return $fuel_tanks->rawColumns(['amount', 'action'])
                ->make(true);
        }

        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');
        $payment_types = $this->transactionUtil->payment_types();
        $layout = 'app';
        if ($only_pumper) {
            $layout = 'pumper';
        }
        
        $shifts = PetroShift::join('pump_operators','pump_operators.id','petro_shifts.pump_operator_id')->where('petro_shifts.business_id',$business_id)->select('pump_operators.name','petro_shifts.*')->orderBy('id','DESC');
        
        if ($only_pumper) {
            $shifts->where('pump_operator_id', $pump_operator_id);
        }
        
        $shifts = $shifts->get();
        
        $user = Auth::user();
        
        $pump_operator_id = $user->pump_operator_id;
        $shift_number = PumpOperatorAssignment::where('pump_operator_id', $pump_operator_id)->max('shift_number');

        return view('petro::pump_operators.payment_summary')->with(compact(
            'pump_operators',
            'only_pumper',
            'payment_types',
            'layout',
            'shifts',
            'shift_number'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $pump_operator_id = Auth::user()->pump_operator_id;
        $business_id = Auth::user()->business_id;

        $pumps = Pump::leftjoin('pump_operator_assignments', function ($join) {
            $join->on('pumps.id', 'pump_operator_assignments.pump_id')->whereDate('date_and_time', date('Y-m-d'));
        })->leftjoin('pump_operators', 'pump_operator_assignments.pump_operator_id', 'pump_operators.id')
            ->where('pumps.business_id', $business_id)
            ->where('pump_operator_assignments.pump_operator_id', $pump_operator_id)
            ->select('pumps.*', 'pump_operator_assignments.pump_operator_id', 'pump_operator_assignments.pump_id', 'pump_operators.name as pumper_name')
            ->orderBy('pumps.id')
            ->get();

        $layout = 'pumper';
        
        $customers = Contact::customersDropdown($business_id, false, true, 'customer');
        $walkin = Contact::where('name','Walk-In Customer')->where('business_id', $business_id)->pluck('name','id');
        $products = Product::where('business_id', $business_id)->pluck('name', 'id');
        
        $subscription = Subscription::current_subscription($business_id);
        $package_details = $subscription->package_details;
        
        $only_walkin = $package_details['only_walkin'];
        
        $pump_operator = PumpOperator::findOrFail($pump_operator_id);
        $settings = json_decode($pump_operator->dashboard_settings,true);;
        
        $direct_cr = "no";
        if(!empty($settings) && !empty($settings['credit_sales_direct_to_customer'])){
            $direct_cr = $settings['credit_sales_direct_to_customer'];
        }
        
        $enter_cash_denoms = 'no';
        if(!empty($settings) && !empty($settings['enter_cash_denominations'])){
            $enter_cash_denoms = $settings['enter_cash_denominations'];
        }
        
        $card_pmt_type = 'bulk';
        if(!empty($settings) && !empty($settings['card_amount_to_enter'])){
            $card_pmt_type = $settings['card_amount_to_enter'];
        }

        $card_types = [];
        $card_group = AccountGroup::where('business_id', $business_id)->where('name', 'Card')->first();
        if (!empty($card_group)) {
            $card_types = Account::where('business_id', $business_id)->where('asset_type', $card_group->id)->where(DB::raw("REPLACE(`name`, '  ', ' ')"), '!=', 'Cards (Credit Debit) Account')->pluck('name', 'id');
        }
        
        $pending_pumps = PumpOperatorAssignment::leftjoin('pumps','pumps.id','pump_operator_assignments.pump_id')
                            ->leftjoin('products','products.id','pumps.product_id')
                            ->leftjoin('variations','variations.product_id','products.id')
                            ->join('petro_shifts','petro_shifts.id','pump_operator_assignments.shift_id')
                            ->where('petro_shifts.status','0')
                            ->where('pump_operator_assignments.pump_operator_id', $pump_operator_id)
                            ->where('pump_operator_assignments.business_id', $business_id)
                            ->where('pump_operator_assignments.status', 'open')
                            ->whereNull('pump_operator_assignments.pump_operator_other_sale_id')
                            ->select('pump_operator_assignments.*','variations.sell_price_inc_tax','pumps.pump_no')
                            ->get();
                            
        $daily_cards = DailyCard::where('business_id',$business_id)
                                ->where('pump_operator_id',$pump_operator_id)
                                ->whereNull('used_status')
                                ->sum('amount');
        $pending_vouchers = DailyVoucher::where('business_id',$business_id)
                                ->where('operator_id',$pump_operator_id)
                                ->whereNull('settlement_no')
                                ->sum('total_amount');
                                
        $daily_shortage_excess = PumpOperatorPayment::where('business_id',$business_id)
                                ->where('pump_operator_id',$pump_operator_id)
                                ->where(function($query){
                                    $query->whereNull('is_used')->orWhere('is_used',0);
                                })
                                ->whereIn('payment_type',['shortage','excess','other'])
                                ->sum('payment_amount');
        
        $all_pending_payments = $daily_cards + $pending_vouchers + $daily_shortage_excess;
        
        $already_entered = PumpOperatorMeterSaleDetail::join('pump_operator_assignments','pump_operator_assignments.pump_operator_other_sale_id','pump_operator_meter_sale_details.id')
                                                ->where('pump_operator_assignments.pump_operator_id',$pump_operator_id)
                                                ->where('pump_operator_assignments.closed_in_settlement',0)
                                                ->sum('pump_operator_meter_sale_details.amount');
                                                
        $bank_account_group_id = AccountGroup::getGroupByName('Bank Account');
        $bank_accounts = Account::where('business_id', $business_id)->where('asset_type', $bank_account_group_id->id)->pluck('name','name');
     
        $balance_to_deposit = $already_entered - $all_pending_payments;
        
        $business = Business::where('id', $business_id)->first();
        $pos_settings = json_decode($business->pos_settings,true);
        $cash_denoms = !empty($pos_settings['cash_denominations']) ? explode(',',$pos_settings['cash_denominations']) : array();

        $user = Auth::user();
        
        $pump_operator_id = $user->pump_operator_id;
        $shift_number = PumpOperatorAssignment::where('pump_operator_id', $pump_operator_id)->max('shift_number');
        
        return view('petro::pump_operators.actions.payments')->with(compact(
            'pumps','card_types',
            'layout', 'customers','walkin','products','only_walkin','direct_cr',
            'pending_pumps','all_pending_payments','balance_to_deposit' ,'bank_accounts','cash_denoms','enter_cash_denoms', 'card_pmt_type', 'shift_number'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $pump_operator_id = Auth::user()->pump_operator_id;
        $business_id = Auth::user()->business_id;
        $created_by = Auth::user()->id;
        $pump_operator = PumpOperator::findOrFail($pump_operator_id);
        $settings = json_decode($pump_operator->dashboard_settings,true);
        $settlement = Settlement::where('settlement_no',$pump_operator->settlement_no)->first();
        $shift = PetroShift::where('pump_operator_id',$pump_operator_id)->get()->last()->id ?? 0;


        try {
            $payment_amount = $request->amount;
            $payment_type = $request->payment_type;

             /* 
            removed payment type check
            */
            if ($payment_amount == "") {
                $output = [
                    'success' => false,
                    'msg' => "Please  amount are mendatory field!"
                ];
                return $output;
            }

            $data = [
                'business_id' => $business_id,
                'pump_operator_id' => $pump_operator_id,
                'payment_type' => $payment_type,
                'payment_amount' => $payment_amount,
                'created_by' => $created_by,
                'shift_id' => $shift
            ];
            
            PumpOperatorPayment::create($data);
            
            if($request->payment_type == 'cash'){
                $collection_form_no = (int) (DailyCollection::where('business_id', $business_id)->count()) + 1;
                
                $data = array(
                    'business_id' => $business_id,
                    'collection_form_no' => $collection_form_no,
                    'pump_operator_id' => $pump_operator_id,
                    'location_id' => $pump_operator->location_id,
                    'balance_collection' => 0, //$request->balance_collection,
                    'current_amount' => $payment_amount,
                    'created_by' =>  Auth::user()->id
                );
    
                DailyCollection::create($data);
                
                $pump_operator = PumpOperator::where('id', $pump_operator_id)->first();
                /*$balance_collection = DailyCollection::where('business_id', $business_id)->where('pump_operator_id', $pump_operator_id)->sum('current_amount');
                $settlement_collection = DailyCollection::where('business_id', $business_id)->where('pump_operator_id', $pump_operator_id)->sum('balance_collection');
                $cum_amount = $balance_collection - $settlement_collection;*/
                                
                
                $sms_data = array(
                    'date' => $this->transactionUtil->format_date(date('Y-m-d')),
                    'time' => date('H:i'),
                    'pump_operator' => $pump_operator->name,
                    'amount' => $this->transactionUtil->num_f($payment_amount)
                );
                
                $this->notificationUtil->sendPetroNotification('pumper_dashboard_cash_deposit',$sms_data);
                
            }
            
            if($request->payment_type == 'card'){
                if(!empty($request->card_type)){
                    $collection_form_no = (int) (DailyCard::where('business_id', $business_id)->count()) + 1;
                    
                    $walkin_customer = Contact::where('name','Walk-In Customer')->where('business_id', $business_id)->first();
                    
                    $data = array(
                        'location_id' => $pump_operator->location_id,
                        'collection_no' => $collection_form_no,
                        'business_id' => $business_id,
                        'amount' => $payment_amount,
                        'card_type' => $request->card_type,
                        'card_number' => $request->card_number,
                        'customer_id' => $walkin_customer->id,
                        'slip_no' => $request->slip_no,
                        'date' => date('Y-m-d'),
                        'pump_operator_id' => $pump_operator_id
                    );
        
                    DailyCard::create($data);
                }
                
                
            }

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return  $output;
    }
    
    public function saveCardPayment(Request $request)
    {
        $pump_operator_id = Auth::user()->pump_operator_id;
        $business_id = Auth::user()->business_id;
        $created_by = Auth::user()->id;
        $pump_operator = PumpOperator::findOrFail($pump_operator_id);
        $settings = json_decode($pump_operator->dashboard_settings,true);
        $settlement = Settlement::where('settlement_no',$pump_operator->settlement_no)->first();
        $shift = PetroShift::where('pump_operator_id',$pump_operator_id)->get()->last()->id ?? 0;

        try {
            
            
            foreach($request->card_data as $card){
                $_data = json_decode($card,true);
                
                $data = [
                    'business_id' => $business_id,
                    'pump_operator_id' => $pump_operator_id,
                    'payment_type' => 'card',
                    'payment_amount' => $_data['amount'],
                    'created_by' => $created_by,
                    'shift_id' => $shift
                ];
                
                PumpOperatorPayment::create($data);
                
                
                $collection_form_no = (int) (DailyCard::where('business_id', $business_id)->count()) + 1;
                        
                $walkin_customer = Contact::where('name','Walk-In Customer')->where('business_id', $business_id)->first();
                
                $data = array(
                    'location_id' => $pump_operator->location_id,
                    'collection_no' => $collection_form_no,
                    'business_id' => $business_id,
                    'amount' => $_data['amount'],
                    'card_type' => $_data['card_type'],
                    'card_number' => $_data['card_number'],
                    'customer_id' => $walkin_customer->id,
                    'slip_no' => $_data['slip_no'],
                    'date' => date('Y-m-d'),
                    'pump_operator_id' => $pump_operator_id
                );
    
                DailyCard::create($data);
            }

            

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }
    
    public function saveCashDenom(Request $request)
    {
        $pump_operator_id = Auth::user()->pump_operator_id;
        $business_id = Auth::user()->business_id;
        $created_by = Auth::user()->id;
        $pump_operator = PumpOperator::findOrFail($pump_operator_id);
        $settings = json_decode($pump_operator->dashboard_settings,true);
        $settlement = Settlement::where('settlement_no',$pump_operator->settlement_no)->first();
        $shift = PetroShift::where('pump_operator_id',$pump_operator_id)->get()->last()->id ?? 0;
        
        try {
            $payment_amount = $request->grand_total;
            $payment_type = 'cash';

            if($payment_amount == "" || $payment_type == ""){
                $output = [
                    'success' => false,
                    'msg' => "Please payment type and amount are mendatory fields!"
                ];
                return $output;
            }

            $data = [
                'business_id' => $business_id,
                'pump_operator_id' => $pump_operator_id,
                'payment_type' => $payment_type,
                'payment_amount' => $payment_amount,
                'created_by' => $created_by,
                'shift_id' => $shift
            ];
            
            PumpOperatorPayment::create($data);
            
            $collection_form_no = (int) (DailyCollection::where('business_id', $business_id)->count()) + 1;
                
                $data = array(
                    'business_id' => $business_id,
                    'collection_form_no' => $collection_form_no,
                    'pump_operator_id' => $pump_operator_id,
                    'location_id' => $pump_operator->location_id,
                    'balance_collection' => 0, //$request->balance_collection,
                    'current_amount' => $payment_amount,
                    'created_by' =>  Auth::user()->id
                );
    
                DailyCollection::create($data);
                
                $pump_operator = PumpOperator::where('id', $pump_operator_id)->first();
                
                $sms_data = array(
                    'date' => $this->transactionUtil->format_date(date('Y-m-d')),
                    'time' => date('H:i'),
                    'pump_operator' => $pump_operator->name,
                    'amount' => $this->transactionUtil->num_f($payment_amount)
                );
                
                $this->notificationUtil->sendPetroNotification('pumper_dashboard_cash_deposit',$sms_data);
            

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }
    
    public function saveChequePayment(Request $request)
    {
        $pump_operator_id = Auth::user()->pump_operator_id;
        $business_id = Auth::user()->business_id;
        $created_by = Auth::user()->id;
        $pump_operator = PumpOperator::findOrFail($pump_operator_id);
        
        
        $shift = PetroShift::where('pump_operator_id',$pump_operator_id)->get()->last()->id ?? 0;


        try {
            DB::beginTransaction();
            $payment_amount = $request->amount;
            

            $data = [
                'business_id' => $business_id,
                'pump_operator_id' => $pump_operator_id,
                'payment_type' => 'cheque',
                'payment_amount' => $payment_amount,
                'created_by' => $created_by,
                'shift_id' => $shift
            ];
            
            $payment = PumpOperatorPayment::create($data);
            
            $data = array(
                'linked_payment_id' => $payment->id,
                'business_id' => $business_id,
                'amount' => $payment_amount,
                'bank_name' => $request->cheque_bank,
                'customer_id' => $request->customer_id,
                'cheque_number' => $request->cheque_number,
                'cheque_date' => $request->cheque_date,
                'shift_id' => $shift
            );

            DailyChequePayment::create($data);
        
            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            DB::rollback();
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }
    
    public function saveCredit(Request $request){
        try {
            $data = $request->credit_data;
            $pump_operator_id = Auth::user()->pump_operator_id;
            $pump_operator = PumpOperator::findOrFail($pump_operator_id);
            $business_id = $request->session()->get('business.id');
            //$pump_operator->settlement_no
            $settlement = Settlement::where('settlement_no',$pump_operator->settlement_no)->first();
            $shift = PetroShift::where('pump_operator_id',$pump_operator_id)->get()->last()->id ?? 0;
            
            
            foreach($data as $one){
                $price = $this->productUtil->num_uf($one['price']);
                $unit_discount = $this->productUtil->num_uf($one['unit_discount']);
                $qty = $this->productUtil->num_uf($one['qty']);
                $amount = $this->productUtil->num_uf($one['amount']);
                $sub_total = $this->productUtil->num_uf($one['sub_total']);
                $total_discount = $this->productUtil->num_uf($one['total_discount']);
                
                
                 $pp_data = [
                    'business_id' => $business_id,
                    'pump_operator_id' => $pump_operator_id,
                    'payment_type' => 'credit',
                    'payment_amount' => $amount,
                    'created_by' => auth()->user()->id,
                    'shift_id' => $shift
                ];
                
                PumpOperatorPayment::create($pp_data);
                
                
                $dt = array(
                    'business_id' => $business_id,
                    'pump_operator_id' => $pump_operator_id,
                    'customer_id' => $one['customer_id'],
                    'product_id' => $one['product_id'],
                    'order_number' => $one['order_number'],
                    'order_date' => \Carbon::parse($one['order_date'])->format('Y-m-d'),
                    'price' => $price,
                    'discount' => $unit_discount,
                    'qty' => $qty,
                    'amount' => $amount,
                    'sub_total' => $sub_total,
                    'total_discount' => $total_discount,
                    'outstanding' => $this->productUtil->num_uf($one['outstanding']),
                    'credit_limit' => $one['credit_limit'],
                    'customer_reference' => $one['customer_reference'],
                    'note' => $one['note'],
                    'is_from_pumper' => 1
                );
                $credit_sale_payment = SettlementCreditSalePayment::create($dt);
                
                // store the customer reference
                if(!empty($credit_sale_payment->customer_reference)){
                    $customer = Contact::findOrFail($credit_sale_payment->customer_id);
                    $name = $customer->name;
                    $barcode_string = $name . '.' . $credit_sale_payment->customer_reference;
                    $qr = new DNS2D();
                    $qr = $qr->getBarcodePNG($barcode_string, 'QRCODE');
                    $src = 'data:image/png;base64,' . $qr;
        
                    
                    $ref_data = array(
                        'business_id' => $credit_sale_payment->business_id,
                        'date' => date('Y-m-d', strtotime($credit_sale_payment->order_date)),
                        'contact_id' => $credit_sale_payment->customer_id,
                        'reference' =>$credit_sale_payment->customer_reference,
                        'barcode_src' => $src
                    );
                    CustomerReference::updateOrCreate(['business_id' => $credit_sale_payment->business_id,'contact_id' => $credit_sale_payment->customer_id,'reference' =>$credit_sale_payment->customer_reference],$ref_data);
                    
                }
                
                $customer_reference =  CustomerReference::where('reference' ,$credit_sale_payment->customer_reference)->first()->id ?? 0;
                
                $daily_vouchers_no = (DailyVoucher::where('business_id', $business_id)->count()) + 1;
                
                $assignment = PumpOperatorAssignment::where('pump_operator_id', $pump_operator->id)->where('business_id', $business_id)->where('status', 'open')->first();
                
                $data = array(
                    'business_id' => $business_id,
                    'transaction_date' => date('Y-m-d', strtotime($credit_sale_payment->order_date)),
                    'daily_vouchers_no' => $daily_vouchers_no,
                    'location_id' => $pump_operator->location_id,
                    
                    'pump_id' => !empty($assignment) ? $assignment->pump_id : null,
                    
                    'operator_id' => $pump_operator->id,
                    'customer_id' => $credit_sale_payment->customer_id,
                    'current_outstanding' => $this->productUtil->num_uf($one['outstanding']),
                    'outstanding_pending' => $this->productUtil->num_uf($one['outstanding']),
                    
                    'voucher_order_number' => $one['order_number'],
                    'voucher_order_date' => \Carbon::parse($credit_sale_payment->order_date)->format('Y-m-d'),
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'vehicle_no' => $customer_reference,
                    'total_amount' => $sub_total
                );
                
                $daily_voucher = DailyVoucher::create($data);
                
                $details = array(
                    'business_id' => $business_id,
                    'daily_voucher_id' => $daily_voucher->id,
                    'product_id' => $this->productUtil->num_uf($one['product_id']),
                    'unit_price' => $this->productUtil->num_uf($price),
                    'qty' => $qty,
                    'sub_total' => $this->productUtil->num_uf($sub_total),

                );
                DailyVoucherItem::create($details);
                
                
                $uncreditted = SettlementCreditSalePayment::where('customer_id',$one['customer_id'])->whereNull('is_committed')->where('is_from_pumper',1)->sum('sub_total') ?? 0;
                $final_total = $credit_sale_payment->amount - $credit_sale_payment->total_discount;
                
                $contact = Contact::findOrFail($one['customer_id']);
                $phones = [];
                $phones = array($contact->mobile,$contact->alternate_number);
                
                $sms_data = array(
                    'date' => $this->transactionUtil->format_date(date('Y-m-d')),
                    'time' => date('H:i'),
                    'pump_operator' => $pump_operator->name,
                    'amount' => $this->transactionUtil->num_f($final_total),
                    'order_no' => $one['order_number'],
                    'customer' => $contact->name,
                    'cumulative_amount' => $this->productUtil->num_f($this->contactUtil->getCustomerBalance($credit_sale_payment->customer_id,$business_id,true) +  $uncreditted),
                );
                $this->notificationUtil->sendPetroNotification('pumper_dashboard_credit_sales_customer',$sms_data,implode(',',$phones));
                $this->notificationUtil->sendPetroNotification('pumper_dashboard_credit_sales',$sms_data);
                
            }

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
    
    public function getOtherSale(){
        if (request()->ajax()) {
            $business_id =  Auth::user()->business_id;
            $query = PumpOperatorMeterSaleDetail::leftjoin('pump_operator_meter_sales', 'pump_operator_meter_sale_details.sale_id', 'pump_operator_meter_sales.id')
                ->leftjoin('pump_operators', 'pump_operator_meter_sales.pump_operator_id', 'pump_operators.id')
                ->leftjoin('pumps','pumps.id','pump_operator_meter_sale_details.pump_id')
                ->where('pump_operator_meter_sales.business_id', $business_id)
                ->select('pump_operator_meter_sale_details.*', 'pump_operators.name as pump_operator_name', 'pumps.pump_no','pump_operator_meter_sales.amount as total_amount','pump_operator_meter_sales.date_time','pump_operator_meter_sales.deposited','pump_operator_meter_sales.balance');

           
            if (!empty(request()->pump_id)) {
                $query->where('pump_operator_meter_sale_details.pump_id', request()->pump_id);
            }
            if (!empty(request()->pump_operator_id)) {
                $query->where('pump_operator_meter_sales.pump_operator_id', request()->pump_operator_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $query->whereDate('pump_operator_meter_sales.date_time', '>=', request()->start_date);
                $query->whereDate('pump_operator_meter_sales.date_time', '<=', request()->end_date);
            }

            $fuel_tanks = DataTables::of($query->orderBy('pump_operator_meter_sale_details.id','DESC'))
                ->addColumn('date', '{{@format_date($date_time)}}')
                ->addColumn('time', '{{@format_time($date_time)}}')
                ->addColumn('received_meter', '{{ number_format($received_meter,"3",".",",") }}')
                ->addColumn('new_meter', '{{ number_format($new_meter,"3",".",",") }}')
                ->addColumn('sold_qty', '{{ @num_format($sold_qty) }}')
                ->addColumn('unit_price', '{{ @num_format($unit_price) }}')
                ->addColumn('amount', '{{ @num_format($amount) }}')
                ->addColumn('total_amount', '{{ @num_format($total_amount) }}')
                ->addColumn('deposited', '{{ @num_format($deposited) }}')
                ->addColumn('balance', '{{ @num_format($balance) }}')
                ;

            return $fuel_tanks->rawColumns(['amount', 'action'])
                ->make(true);
        }
    }
    
    
        
    // Meter sale filter
public function meterSalesList(Request $request) {
    $business_id = Auth::user()->business_id;

    if ($request->ajax()) {
        $business_details = Business::find($business_id);
        $query = DB::table('meter_sales')
            ->join('pump_operator_assignments', 'meter_sales.pump_id', '=', 'pump_operator_assignments.pump_id')
            ->join('products', 'meter_sales.product_id', '=', 'products.id')
            ->where('pump_operator_assignments.status', 'close')
            ->where('pump_operator_assignments.shift_id', 2);

        // Check if shift_ids are provided
        if ($request->shift_ids) {
            $query = $query->whereIn('pump_operator_assignments.shift_id', $request->shift_ids); // Fixed table reference
        } else {
            $query = $query->where('pump_operator_assignments.shift_id', $request->shift_id); // Fixed table reference
        }

        // Retrieve results
        $results = $query->select('meter_sales.*', 'products.name as product_name', 'products.sku as product_sku', 'pump_operator_assignments.shift_number');

        $meter_sales = DataTables::of($results)
            ->addColumn('quantity', function($row) {
                return number_format($row->qty);
            })
            ->editColumn('price', function ($row) use ($business_details) {
                return '<span class="display_currency amount" data-orig-value="' . $row->price . '" data-currency_symbol="false">' . 
                        $this->productUtil->num_f($row->price, false, $business_details, true) . 
                        '</span>';
            })
            ->editColumn('sub_total', function ($row) use ($business_details) {
                return '<span class="display_currency sub_total" data-orig-value="' . $row->sub_total . '" data-currency_symbol="false">' . 
                        $this->productUtil->num_f($row->sub_total, false, $business_details, true) . 
                        '</span>';
            });

        return $meter_sales->rawColumns(['price', 'sub_total'])->make(true);
        
    }

    return response()->json(['message' => 'Invalid request'], 400); // Handle non-AJAX requests
}

    
    public function otherSales($shift_id){
        $business_id = request()->session()->get('business.id');
        $stores =  Store::forDropdown($business_id, 0,0, 'sell');
        $bulk_tanks = FuelTank::where('business_id', $business_id)->where('bulk_tank', 1)->pluck('fuel_tank_number', 'id');
        $items = [];
        $fuel_category_id = Category::where('business_id', $business_id)->where('name', 'Fuel')->first();
        $fuel_category_id = !empty($fuel_category_id) ? $fuel_category_id->id : null;
        $items = $this->transactionUtil->getProductDropDownArray($business_id, $fuel_category_id,'petro_settlements');
        
        $pump_operator_id = Auth::user()->pump_operator_id;
        $pump_operator = PumpOperator::findOrFail($pump_operator_id);
        $other_sales = PumpOperatorOtherSale::where('shift_id',$shift_id)->get();
        
        return view('petro::pump_operators.partials.modal_other_sales')->with(compact('shift_id','stores','bulk_tanks','items','pump_operator','other_sales'));
    }
    
  public function otherSalesList(Request $request){
    $business_id = Auth::user()->business_id;
    
    if (request()->ajax()) {
        $business_details = Business::find($business_id);
        $query = PumpOperatorOtherSale::join('products', 'products.id', '=', 'pump_operator_other_sales.product_id');
        
        // Log the received shift_ids for debugging
        \Log::info('Received shift_ids:', ['shift_ids' => $request->shift_ids]);

        if ($request->shift_ids) {
            \Log::info('Applying shift_ids filter:', ['shift_ids' => $request->shift_ids]);
            $query = $query->whereIn('pump_operator_other_sales.shift_id', $request->shift_ids);
        } else {
            $query = $query->where('pump_operator_other_sales.shift_id', $request->shift_id);
        }

        $query = $query->join('pump_operator_assignments', function ($join) {
            $join->on('pump_operator_assignments.shift_id', '=', 'pump_operator_other_sales.shift_id')
                 ->where('pump_operator_assignments.status', 'close')
                 ->whereRaw('pump_operator_assignments.id = (
                     SELECT MAX(poa.id)
                     FROM pump_operator_assignments poa
                     WHERE poa.shift_id = pump_operator_other_sales.shift_id AND poa.status = "close"
                 )');
        })
        ->select('pump_operator_other_sales.*', 'products.name as product_name', 'products.sku as product_sku', 'pump_operator_assignments.shift_number');


        $other_sales = DataTables::of($query)
            ->addColumn('quantity', function($row) {
                return number_format($row->qty);
            })
            ->editColumn(
                'price',
                function ($row) use ($business_details) {
                    return '<span class="display_currency amount" data-orig-value="' . $row->price . '" data-currency_symbol=false>' .
                        $this->productUtil->num_f($row->price, false, $business_details, true) .
                        '</span>';
                }
            )
            ->editColumn(
                'sub_total',
                function ($row) use ($business_details) {
                    return '<span class="display_currency sub_total" data-orig-value="' . $row->sub_total . '" data-currency_symbol=false>' .
                        $this->productUtil->num_f($row->sub_total, false, $business_details, true) .
                        '</span>';
                }
            );

        return $other_sales->rawColumns(['price', 'sub_total'])->make(true);
    }

    $pump_operator_id = Auth::user()->pump_operator_id;
    $shifts = PetroShift::join('pump_operators', 'pump_operators.id', 'petro_shifts.pump_operator_id')
        ->where('pump_operator_id', $pump_operator_id)
        ->where('petro_shifts.business_id', $business_id)
        ->select('pump_operators.name', 'petro_shifts.*')
        ->orderBy('id', 'DESC')
        ->get();
    $shift_number = PumpOperatorAssignment::where('pump_operator_id', $pump_operator_id)->max('shift_number');
    $layout = 'pumper';

    return view('petro::pump_operators.other_sales_list')->with(compact('shift_number', 'shifts', 'layout'));
}

    public function othersalespage(Request $request) 
    {
        $products = Product::all();
        return view('petro::pump_operators.partials.other_sales')->with(compact('products'));
    }
    
    public function getProducts(Request $request) {
        $business_id = $request->session()->get('business.id');
        $currency_precision = Business::where('business.id', '=', $business_id)
                                    ->select('currency_precision')
                                    ->first();

        // $product = Product::leftjoin('units', 'products.unit_id', 'units.id')->where('products.id', $request->id)->select('units.short_name', 'products.min_sell_price');
        $product = DB::table('products')
                        ->leftJoin('units', 'products.unit_id', '=', 'units.id')
                        ->leftJoin('variations', 'products.id', '=', 'variations.product_id')
                        ->select('variations.sell_price_inc_tax', 'units.short_name')
                        ->where('products.id', $request->product_id)
                        ->first();
        

        // $product = DB::select("select products.min_sell_price, units.short_name from products left join units on products.unit_id = units.id where products.id = '{$request->id}'");
        // dump($product);exit;
        return [
            'product' => $product,
            'currency_precision' => $currency_precision
        ];
    }
    
    public function saveOtherSale(Request $request){
        try {
            $business_id = $request->session()->get('business.id');
            
            
            $data = array(
                'business_id' => $business_id,
                'other_sale_id' => null,
                'store_id' => $request->store_id,
                'product_id' => $request->product_id,
                'price' => $request->price,
                'qty' => $request->qty,
                'balance_stock' => $request->balance_stock,
                'discount' => $request->discount,
                'discount_type' => $request->discount_type,
                'discount_amount' => $request->discount_amount,
                'sub_total' => $request->sub_total,
                'shift_id' => $request->shift_id
            );
            $other_sale = PumpOperatorOtherSale::create($data);

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

    public function saveOtherSaleItems(Request $request){
        try {
            $business_id = $request->session()->get('business.id');
            // get store_id from store table where business_id match
            $store = Store::where('business_id',$business_id)->get()->last();
            $store_id = $store->id ?? 0;
            // get balance_stock from store (deduct quantity?)
            $balance_stock = $store->stock ?? 0;
            // discount default to 0
            $discount = 0;
            $pump_operator_id = Auth::user()->pump_operator_id;
            $shift_id = PetroShift::where('pump_operator_id',$pump_operator_id)->get()->last()->id ?? 0;

            
            foreach($request->items as $item){
                $data = array(
                    'business_id' => $business_id,
                    'store_id' => $store_id,
                    'product_id' => $item['product_id'],
                    'price' => $item['price'],
                    'qty' => $item['amount'],
                    'balance_stock' => $balance_stock,
                    'discount' => $discount,
                    'sub_total' => ($item['price'] * $item['amount']),
                    'shift_id' => $shift_id
                );
                $other_sale = PumpOperatorOtherSale::create($data);
                $databaseName = DB::connection()->getDatabaseName();
                Log::debug($databaseName);
                Log::debug($other_sale);
            }

            $output = [
                'success' => true,
                'msg' => __('petro::lang.success')
            ];
        } catch (\Exception $e) {
            Log::debug($e);
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }
    
    public function saveMeterSale(Request $request){
        try {
            DB::beginTransaction();
            // dd($request->all());
            
            $business_id = $request->session()->get('business.id');
            $pump_operator_id = Auth::user()->pump_operator_id;
            
            $meter_sale = PumpOperatorMeterSale::create(array(
                'business_id' => $business_id,	
                'date_time' => date('Y-m-d H:i'),	
                'pump_operator_id' => $pump_operator_id,	
                'amount' => $request->grand_total,	
                'deposited' => $request->today_deposited,	
                'balance' => $request->balance_to_deposit,	
            ));
            
            foreach($request->pump_no as $key => $pump_no){
                $pump = Pump::where('pump_no',$pump_no)->first();
                $other_sale = PumpOperatorMeterSaleDetail::create(array(
                    'sale_id' => $meter_sale->id,	
                    'business_id' => $business_id,	
                    'pump_operator_id' => $pump_operator_id,	
                    'pump_id' => $pump->id,	
                    'received_meter' => $request->starting_meter[$key],	
                    'new_meter' => $request->new_meter[$key],	
                    'sold_qty' => $request->sold_qty[$key],	
                    'unit_price' => $request->unit_price[$key],	
                    'amount' => $request->sale_amount[$key],
                ));
                
                PumpOperatorAssignment::where('id',$request->assignment_id[$key])->update(array('pump_operator_other_sale_id' => $other_sale->id));
            }
            
            DB::commit();

            $output = [
                'success' => true,
                'msg' => __('petro::lang.success')
            ];
        } catch (\Exception $e) {
            DB::rollback();
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }
    
    public function deleteOtherSale($id)
    {
        try {
            $other_sale = PumpOperatorOtherSale::where('id', $id)->first();
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
    
   
    /**
     * Show the specified resource in modal.
     * @param int $id
     * @return Renderable
     */
    public function getPaymentSummaryModal()
    {
        $only_pumper = request()->only_pumper;
        $pump_operator_id = Auth::user()->pump_operator_id;
        $business_id =  Auth::user()->business_id;
        
        $shifts = PetroShift::join('pump_operators','pump_operators.id','petro_shifts.pump_operator_id')->where('petro_shifts.business_id',$business_id)->select('pump_operators.name','petro_shifts.*')->orderBy('id','DESC');
        
        if ($only_pumper) {
            $shifts->where('pump_operator_id', $pump_operator_id);
        }
        
        $shifts = $shifts->get();

        return view('petro::pump_operators.partials.payment_summary_modal')->with(compact('only_pumper','shifts'));
    }
    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('petro::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $payment = PumpOperatorPayment::find($id);

        $payment_types = PumpOperatorPayment::getPaymentTypesArray();

        return view('petro::pump_operators.partials.edit_payment')->with(compact(
            'payment',
            'payment_types'
        ));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'note' => 'required'
        ]);

        try {
            $data = $request->except('_token', '_method');
            $data['edited_by'] = Auth::user()->id;
            
            PumpOperatorPayment::where('id', $id)->update($data);
            $output = [
                'success' => true,
                'tab' => 'payment_summary',
                'msg' => __('lang_v1.success')
            ];
            return redirect()->back()->with('status', $output);
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'payment_summary',
                'msg' => __('messages.something_went_wrong')
            ];
        }


        return redirect()->back()->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
    /**
     * return modal view
     * @param int $id
     * @return Renderable
     */
    public function getPaymentModal()
    {
        $pump_operator_id = Auth::user()->pump_operator_id;
        $business_id = Auth::user()->business_id;

        $pumps = Pump::leftjoin('pump_operator_assignments', function ($join) {
            $join->on('pumps.id', 'pump_operator_assignments.pump_id')->whereDate('date_and_time', date('Y-m-d'));
        })->leftjoin('pump_operators', 'pump_operator_assignments.pump_operator_id', 'pump_operators.id')
            ->where('pumps.business_id', $business_id)
            ->where('pump_operator_assignments.pump_operator_id', $pump_operator_id)
            ->select('pumps.*', 'pump_operator_assignments.pump_operator_id', 'pump_operator_assignments.pump_id', 'pump_operators.name as pumper_name')
            ->orderBy('pumps.id')
            ->get();
        $pop_up = true;

        return view('petro::pump_operators.partials.payment_modal')->with(compact(
            'pumps',
            'pop_up'
        ));
    }

    public function balanceToOperator($pump_operator_id)
    {

        $business_id = Auth::user()->business_id;
        $shift = PetroShift::where('pump_operator_id',$pump_operator_id)->get()->last()->id ?? 0;
        
        $payments = PumpOperatorPayment::where('shift_id',$shift)
            ->where('pump_operator_id', $pump_operator_id)
            ->select(
                DB::raw('SUM(IF(payment_type="cash", payment_amount, 0)) as cash'),
                DB::raw('SUM(IF(payment_type="card", payment_amount, 0)) as card'),
                DB::raw('SUM(IF(payment_type="cheque", payment_amount, 0)) as cheque'),
                DB::raw('SUM(IF(payment_type="credit", payment_amount, 0)) as credit'),
                DB::raw('SUM(payment_amount) as total')
            )->first();

        $day_entries = PumperDayEntry::leftjoin('pump_operators', 'pumper_day_entries.pump_operator_id', 'pump_operators.id')
            ->leftjoin('pumps', 'pumper_day_entries.pump_id', 'pumps.id')
            ->leftjoin('pump_operator_assignments','pump_operator_assignments.id','pumper_day_entries.pumper_assignment_id')
            ->where('shift_id', $shift)
            ->where('pumper_day_entries.business_id', $business_id)
            ->where('pumper_day_entries.pump_operator_id', $pump_operator_id)
            ->select('pump_operators.name', 'pumper_day_entries.*', 'pumps.pump_name')
            ->get();
            
        $other_sale = PumpOperatorOtherSale::where('shift_id', $shift)
                        ->select(DB::raw('SUM(sub_total - discount_amount) as total'))
                        ->value('total');
                        
                        
        if ($day_entries->sum('amount') + $other_sale - $payments->total > 0) {
            $payment_type = 'shortage';
            $payment_amount = $day_entries->sum('amount') + $other_sale - $payments->total;
        }
        if ($day_entries->sum('amount') + $other_sale - $payments->total < 0) {
            $payment_type = 'excess';
            $payment_amount = $day_entries->sum('amount') + $other_sale - $payments->total;
        }
        if(!isset($payment_type) || $payment_type == ""){
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
            return redirect()->back()->with('status', $output);
        }
        $data = [
            'business_id' => $business_id,
            'pump_operator_id' => $pump_operator_id,
            'payment_type' => $payment_type,
            'payment_amount' => $payment_amount,
            'created_by' => Auth::user()->id,
            'shift_id' => $shift
        ];
        if ($day_entries->sum('amount') + $other_sale - $payments->total != 0) {
            PumpOperatorPayment::create($data);
        }

        $output = [
            'success' => 1,
            'msg' => __('lang_v1.success')
        ];
        return redirect()->back()->with('status', $output);
    }
}
