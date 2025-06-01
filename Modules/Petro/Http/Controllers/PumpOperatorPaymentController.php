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
use App\BusinessLocation;
use App\UserStorePermission;
use App\Variation;
use App\VariationLocationDetails;

class PumpOperatorPaymentController extends Controller
{
    const FUEL_CATEGORY_ID = 1;
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
                ->leftjoin('pump_operator_assignments','pump_operator_assignments.shift_id','pump_operator_payments.shift_id')
                ->where('pump_operators.business_id', $business_id)
                ->select('pump_operator_payments.id', 'pump_operator_payments.date_and_time', 'pump_operator_payments.collection_form_no', 'pump_operator_payments.payment_type', 'pump_operator_payments.payment_amount', 'pump_operator_payments.note', 'pump_operators.name as pump_operator_name', 'edited_user.username as edited_by','business_locations.name as location_name', 'pump_operator_assignments.shift_number')
                ->groupBy('pump_operator_payments.id');

            // $query2 = PumpOperatorOtherSale::join('pump_operator_assignments','pump_operator_assignments.shift_id','pump_operator_other_sales.shift_id')
            // ->join('pump_operators', 'pump_operator_assignments.pump_operator_id', 'pump_operators.id')
            // ->join('business_locations','business_locations.id','pump_operators.location_id')
            // ->where('pump_operators.business_id', $business_id)
            // ->select('pump_operator_other_sales.id', 'pump_operator_other_sales.created_at as date_and_time',
            // DB::raw('NULL as collection_form_no'),
            // DB::raw('"Other Sale" as payment_type'),
            // 'pump_operator_other_sales.sub_total as payment_amount',
            // DB::raw('NULL as note'),
            // 'pump_operators.name as pump_operator_name',
            // DB::raw('NULL as edited_by'),
            // 'business_locations.name as location_name', 'pump_operator_assignments.shift_number')
            // ->groupBy('pump_operator_other_sales.id');

            if ($only_pumper) {
                $query->where('pump_operator_payments.pump_operator_id', $pump_operator_id);
                //$query2->where('pump_operator_assignments.pump_operator_id', $pump_operator_id);
            }
            
            if (!empty($shift_id)) {
                $query->where('pump_operator_payments.shift_id', $shift_id);
                //$query2->where('pump_operator_assignments.shift_id', $shift_id);
            }
            
            if (!empty(request()->payment_method)) {
                $query->where('payment_type', request()->payment_method);
                //$query2->where('payment_type', request()->payment_method);
            }
            if (!empty(request()->location_id)) {
                $query->where('pump_operators.location_id', request()->location_id);
                //$query2->where('pump_operators.location_id', request()->location_id);
            }
            if (!empty(request()->pump_operator_id)) {
                $query->where('pump_operator_id', request()->pump_operator_id);
                $query->where('pump_operator_assignments.pump_operator_id', request()->pump_operator_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $query->whereDate('pump_operator_payments.date_and_time', '>=', request()->start_date);
                $query->whereDate('pump_operator_payments.date_and_time', '<=', request()->end_date);
                //$query2->whereDate('pump_operator_other_sales.created_at', '>=', request()->start_date);
                //$query2->whereDate('pump_operator_other_sales.created_at', '<=', request()->end_date);
            }

            //$query = $query->unionAll($query2)->orderBy('id', 'asc');
            $query = $query->orderBy('id', 'asc');
            

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
                        if($row->payment_type == 'Other Sale'){
                            return '';
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
        $settings = json_decode($pump_operator->dashboard_settings,true);

      
        $direct_cr = "no";
        if(!empty($settings) && !empty($settings['credit_sales_direct_to_customer'])){
            $direct_cr = $settings['credit_sales_direct_to_customer'];
        }
        
        $enter_cash_denoms = 'no';
        if(!empty($settings) && !empty($settings['enter_cash_denominations'])){
            $enter_cash_denoms = $settings['enter_cash_denominations'];
        }
        
        $card_pmt_type = 'one_by_one';
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
                            // ->whereNull('pump_operator_assignments.pump_operator_other_sale_id')
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
        $shift_id = PumpOperatorAssignment::where('pump_operator_id', $pump_operator_id)->orderBy('id', 'DESC')->select('shift_id')->first()->shift_id;
        $daily_shortage_excess = PumpOperatorPayment::where('business_id',$business_id)
                                ->where('pump_operator_id',$pump_operator_id)
                                ->where('shift_id',$shift_id)
                                ->where(function($query){
                                    $query->whereNull('is_used')->orWhere('is_used',0);
                                })
                                ->whereIn('payment_type',['shortage','excess','other', 'cash', 'cheque'])
                                ->sum('payment_amount');
        
        $all_pending_payments = $daily_cards + $pending_vouchers + $daily_shortage_excess;
        
        $already_entered = PumpOperatorMeterSaleDetail::join('pump_operator_assignments','pump_operator_assignments.pump_operator_other_sale_id','pump_operator_meter_sale_details.id')
                                                ->where('pump_operator_assignments.pump_operator_id',$pump_operator_id)
                                                ->where('pump_operator_assignments.closed_in_settlement',0)
                                                 ->where('pump_operator_assignments.status','open')
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

        $settings = PumpOperator::where('business_id', $business_id)->whereNotNull('dashboard_settings')->select('dashboard_settings')->first();
        $dashboard_settings = (!is_null($settings)) ? json_decode($settings->dashboard_settings,true) : [];
        $meter_sales_compulsory = $dashboard_settings['meter_sales_compulsory'] ?? 'no';
        $meter_sales_compulsory = ($meter_sales_compulsory == "yes");

        $daily_collection = PumpOperatorPayment::where('business_id', $business_id)->whereNotNull('collection_form_no')->orderBy('id', 'DESC')->select('collection_form_no')->first();
        if(!is_null($daily_collection)){
            $collection_form_no = (int) $daily_collection->collection_form_no + 1;
        } else {
            $collection_form_no = 1;
        }
        $DailyCollection = DailyCollection::where('business_id', $business_id)->whereNotNull('collection_form_no')->orderBy('id', 'DESC')->select('collection_form_no')->first();
        if(!is_null($DailyCollection)){
            if($DailyCollection->collection_form_no >= $collection_form_no){
                $collection_form_no = (int) $DailyCollection->collection_form_no + 1;
            }
        }

        $shift_id = PumpOperatorAssignment::where('pump_operator_id', $pump_operator_id)->orderBy('id', 'DESC')->select('shift_id')->first()->shift_id;
        $meter_sale = PumpOperatorMeterSale::where('shift_id', $shift_id)
        ->where('pump_operator_id', $pump_operator_id)
        ->where('business_id', $business_id)
        ->whereNull('p_o_payment_id')
        ->orderBy('id', 'DESC')
        ->first();
        if(!is_null($meter_sale)){
            $collection_form_no = $meter_sale->collection_form_no;
            $meter_sales_compulsory = false;
        }

        $pumps = PumpOperatorAssignment::join('pumps', 'pumps.id', 'pump_operator_assignments.pump_id')
        ->leftjoin('pump_operators', 'pump_operator_assignments.pump_operator_id', 'pump_operators.id')
        ->leftjoin('petro_shifts','petro_shifts.id','pump_operator_assignments.shift_id')
        ->where('petro_shifts.status',0)
        ->where('pumps.business_id', $business_id)
        ->where('pump_operator_assignments.pump_operator_id', $pump_operator_id)
        ->select('pumps.*', 'pump_operator_assignments.pump_operator_id', 'pump_operators.name as pumper_name', 'pump_operator_assignments.status','pump_operator_assignments.is_confirmed', 'pump_operator_assignments.id as assignment_id')
        ->orderBy('pump_operator_assignments.date_and_time', 'desc')
        ->groupBy('pumps.id')
        ->get();
        if($pumps->count() == 0){
            $meter_sales_compulsory = false;
        }

        return view('petro::pump_operators.actions.payments')->with(compact(
            'pumps','card_types',
            'layout', 'customers','walkin','products','only_walkin','direct_cr',
            'pending_pumps','all_pending_payments','balance_to_deposit' ,'bank_accounts','cash_denoms','enter_cash_denoms', 'card_pmt_type', 'shift_number', 'collection_form_no', 'meter_sales_compulsory'
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
                    'msg' => "Please  amount are a mandatory field!"
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
            
            $PumpOperatorPayment = PumpOperatorPayment::create($data);
            
            if($request->payment_type == 'cash'){
                // $collection_form_no = (int) (DailyCollection::where('business_id', $business_id)->count()) + 1;
                $daily_collection = PumpOperatorPayment::where('business_id', $business_id)->whereNotNull('collection_form_no')->orderBy('id', 'DESC')->select('collection_form_no')->first();
                if(!is_null($daily_collection)){
                    $collection_form_no = (int) $daily_collection->collection_form_no + 1;
                } else {
                    $collection_form_no = 1;
                }
                $DailyCollection = DailyCollection::where('business_id', $business_id)->whereNotNull('collection_form_no')->orderBy('id', 'DESC')->select('collection_form_no')->first();
                if(!is_null($DailyCollection)){
                    if($DailyCollection->collection_form_no >= $collection_form_no){
                        $collection_form_no = (int) $DailyCollection->collection_form_no + 1;
                    }
                }

                $shift_id = PumpOperatorAssignment::where('pump_operator_id', $pump_operator_id)->orderBy('id', 'DESC')->select('shift_id')->first()->shift_id;
                $meter_sale = PumpOperatorMeterSale::where('shift_id', $shift_id)
                ->where('pump_operator_id', $pump_operator_id)
                ->where('business_id', $business_id)
                ->whereNull('p_o_payment_id')
                ->orderBy('id', 'DESC')
                ->first();
                if(!is_null($meter_sale)){
                    $collection_form_no = $meter_sale->collection_form_no;
                    $meter_sale->p_o_payment_id = $PumpOperatorPayment->id;
                    $meter_sale->update();
                }

                if(!empty($request->collection_form_no)){
                    $collection_form_no = $request->collection_form_no;
                }
                
                $data = array(
                    'business_id' => $business_id,
                    'collection_form_no' => $collection_form_no,
                    'pump_operator_id' => $pump_operator_id,
                    'location_id' => $pump_operator->location_id,
                    'balance_collection' => 0, //$request->balance_collection,
                    'current_amount' => $payment_amount,
                    'created_by' =>  Auth::user()->id,
                    'shift_id' => $shift_id
                );
    
                DailyCollection::create($data);
                $PumpOperatorPayment->collection_form_no = $collection_form_no;
                $PumpOperatorPayment->update();
                
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
                    // $collection_form_no = (int) (DailyCard::where('business_id', $business_id)->count()) + 1;

                    $collection = PumpOperatorPayment::where('business_id', $business_id)->whereNotNull('collection_form_no')->orderBy('id', 'DESC')->select('collection_form_no')->first();
                    if(!is_null($collection)){
                        $collection_form_no = (int) $collection->collection_form_no + 1;
                    } else {
                        $collection_form_no = 1;
                    }
                    $DailyCollection = DailyCollection::where('business_id', $business_id)->whereNotNull('collection_form_no')->orderBy('id', 'DESC')->select('collection_form_no')->first();
                    if(!is_null($DailyCollection)){
                        if($DailyCollection->collection_form_no >= $collection_form_no){
                            $collection_form_no = (int) $DailyCollection->collection_form_no + 1;
                        }
                    }

                    $shift_id = PumpOperatorAssignment::where('pump_operator_id', $pump_operator_id)->orderBy('id', 'DESC')->select('shift_id')->first()->shift_id;
                    $meter_sale = PumpOperatorMeterSale::where('shift_id', $shift_id)
                    ->where('pump_operator_id', $pump_operator_id)
                    ->where('business_id', $business_id)
                    ->whereNull('p_o_payment_id')
                    ->orderBy('id', 'DESC')
                    ->first();
                    if(!is_null($meter_sale)){
                        $collection_form_no = $meter_sale->collection_form_no;
                        $meter_sale->p_o_payment_id = $PumpOperatorPayment->id;
                        $meter_sale->update();
                    }

                    if(!empty($request->collection_form_no)){
                        $collection_form_no = $request->collection_form_no;
                    }
                    
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
                    $PumpOperatorPayment->collection_form_no = $collection_form_no;
                    $PumpOperatorPayment->update();
                }
                
                
            }

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
                'collection_form_no' => $collection_form_no
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
            
            $collection = PumpOperatorPayment::where('business_id', $business_id)->whereNotNull('collection_form_no')->orderBy('id', 'DESC')->select('collection_form_no')->first();
            if(!is_null($collection)){
                $collection_form_no = (int) $collection->collection_form_no + 1;
            } else {
                $collection_form_no = 1;
            }
            $DailyCollection = DailyCollection::where('business_id', $business_id)->whereNotNull('collection_form_no')->orderBy('id', 'DESC')->select('collection_form_no')->first();
            if(!is_null($DailyCollection)){
                if($DailyCollection->collection_form_no >= $collection_form_no){
                    $collection_form_no = (int) $DailyCollection->collection_form_no + 1;
                }
            }

            $shift_id = PumpOperatorAssignment::where('pump_operator_id', $pump_operator_id)->orderBy('id', 'DESC')->select('shift_id')->first()->shift_id;
            $meter_sale = PumpOperatorMeterSale::where('shift_id', $shift_id)
            ->where('pump_operator_id', $pump_operator_id)
            ->where('business_id', $business_id)
            ->whereNull('p_o_payment_id')
            ->orderBy('id', 'DESC')
            ->first();
            if(!is_null($meter_sale)){
                $collection_form_no = $meter_sale->collection_form_no;
            }

            if(!empty($request->collection_form_no)){
                $collection_form_no = $request->collection_form_no;
            }
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
                
                $PumpOperatorPayment = PumpOperatorPayment::create($data);
                
                
                // $collection_form_no = (int) (DailyCard::where('business_id', $business_id)->count()) + 1;

                if(!is_null($meter_sale)){
                    $meter_sale->p_o_payment_id = $PumpOperatorPayment->id;
                    $meter_sale->update();
                }
                        
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
                $PumpOperatorPayment->collection_form_no = $collection_form_no;
                $PumpOperatorPayment->update();
            }

            

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
                'collection_form_no' => $collection_form_no
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
                    'created_by' =>  Auth::user()->id,
                    'shift_id' => $shift,
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

            $collection = PumpOperatorPayment::where('business_id', $business_id)->whereNotNull('collection_form_no')->orderBy('id', 'DESC')->select('collection_form_no')->first();
            if(!is_null($collection)){
                $collection_form_no = (int) $collection->collection_form_no + 1;
            } else {
                $collection_form_no = 1;
            }
            $DailyCollection = DailyCollection::where('business_id', $business_id)->whereNotNull('collection_form_no')->orderBy('id', 'DESC')->select('collection_form_no')->first();
            if(!is_null($DailyCollection)){
                if($DailyCollection->collection_form_no >= $collection_form_no){
                    $collection_form_no = (int) $DailyCollection->collection_form_no + 1;
                }
            }

            $shift_id = PumpOperatorAssignment::where('pump_operator_id', $pump_operator_id)->orderBy('id', 'DESC')->select('shift_id')->first()->shift_id;
            $meter_sale = PumpOperatorMeterSale::where('shift_id', $shift_id)
            ->where('pump_operator_id', $pump_operator_id)
            ->where('business_id', $business_id)
            ->whereNull('p_o_payment_id')
            ->orderBy('id', 'DESC')
            ->first();
            if(!is_null($meter_sale)){
                $collection_form_no = $meter_sale->collection_form_no;
                $meter_sale->p_o_payment_id = $payment->id;
                $meter_sale->update();
            }

            if(!empty($request->collection_form_no)){
                $collection_form_no = $request->collection_form_no;
            }
            
            $data = array(
                'linked_payment_id' => $payment->id,
                'business_id' => $business_id,
                'amount' => $payment_amount,
                'bank_name' => $request->cheque_bank,
                'customer_id' => $request->customer_id,
                'cheque_number' => $request->cheque_number,
                'cheque_date' => $request->cheque_date,
                'shift_id' => $shift,
                'collection_form_no' => $collection_form_no,
            );

            DailyChequePayment::create($data);
            $payment->collection_form_no = $collection_form_no;
            $payment->update();
        
            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
                'collection_form_no' => $collection_form_no
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
            
            $collection = PumpOperatorPayment::where('business_id', $business_id)->whereNotNull('collection_form_no')->orderBy('id', 'DESC')->select('collection_form_no')->first();
            if(!is_null($collection)){
                $collection_form_no = (int) $collection->collection_form_no + 1;
            } else {
                $collection_form_no = 1;
            }
            $DailyCollection = DailyCollection::where('business_id', $business_id)->whereNotNull('collection_form_no')->orderBy('id', 'DESC')->select('collection_form_no')->first();
            if(!is_null($DailyCollection)){
                if($DailyCollection->collection_form_no >= $collection_form_no){
                    $collection_form_no = (int) $DailyCollection->collection_form_no + 1;
                }
            }
            $shift_id = PumpOperatorAssignment::where('pump_operator_id', $pump_operator_id)->orderBy('id', 'DESC')->select('shift_id')->first()->shift_id;
            $meter_sale = PumpOperatorMeterSale::where('shift_id', $shift_id)
            ->where('pump_operator_id', $pump_operator_id)
            ->where('business_id', $business_id)
            ->whereNull('p_o_payment_id')
            ->orderBy('id', 'DESC')
            ->first();
            if(!is_null($meter_sale)){
                $collection_form_no = $meter_sale->collection_form_no;
            }

            if(!empty($request->collection_form_no)){
                $collection_form_no = $request->collection_form_no;
            }

            $daily_voucher_item_ids = [];
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
                
                $PumpOperatorPayment = PumpOperatorPayment::create($pp_data);
                if(!is_null($meter_sale)){
                    $meter_sale->p_o_payment_id = $PumpOperatorPayment->id;
                    $meter_sale->update();
                }
                
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
                    'is_from_pumper' => 1,
                    'collection_form_no' => $collection_form_no,
                );
                $credit_sale_payment = SettlementCreditSalePayment::create($dt);
                $PumpOperatorPayment->collection_form_no = $collection_form_no;
                $PumpOperatorPayment->update();
                
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
                
                // $daily_vouchers_no = (DailyVoucher::where('business_id', $business_id)->count()) + 1;
                $daily_vouchers_no = $collection_form_no;
                
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
                $daily_voucher_item = DailyVoucherItem::create($details);
                
                $daily_voucher_item_ids[] = $daily_voucher_item->id;
                
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
                    'customer_reference' => $credit_sale_payment->customer_reference,
                );
                $this->notificationUtil->sendPetroNotification('pumper_dashboard_credit_sales_customer',$sms_data,implode(',',$phones));
                $this->notificationUtil->sendPetroNotification('pumper_dashboard_credit_sales',$sms_data);
                
            }

            $print = false;
            if($request->print){
                $print = $request->print;
            }

            $html_content = "";
            if ($print){
                $pump_operator = PumpOperator::findOrFail($pump_operator_id);
                $location_id = $pump_operator->location_id;

                $location_details = BusinessLocation::find($location_id);
                $invoice_layout = $this->businessUtil->invoiceLayout($business_id, $location_id, $location_details->invoice_layout_id);

                $printer_type = null;
                $business_details = $this->businessUtil->getDetails($business_id);
                $receipt_printer_type = is_null($printer_type) ? $location_details->receipt_printer_type : $printer_type;
                $receipt_details = $this->transactionUtil->getCreditSaleReceiptDetails($daily_voucher_item_ids, $location_id, $invoice_layout, $business_details, $location_details, $receipt_printer_type);

                $currency_details = [
                    'symbol'             => $business_details->currency_symbol,
                    'thousand_separator' => $business_details->thousand_separator,
                    'decimal_separator'  => $business_details->decimal_separator,
                ];
                $receipt_details->currency = $currency_details;
                //$layout = !empty($receipt_details->design) ? 'sale_pos.receipts.' . $receipt_details->design : 'sale_pos.receipts.classic';
                $layout = !empty($receipt_details->design) ? 'sale_pos.receipts.classic-other-sale' : 'sale_pos.receipts.classic';

                $html_content = view($layout, compact('receipt_details'))->render();
            }

            $output = [
                'success' => true,
                'msg' => __('petro::lang.success'),
                'collection_form_no' => $collection_form_no,
                'print' => $print,
                'html_content' => $html_content,
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
            ->join('pumps', 'meter_sales.pump_id', '=', 'pumps.id')
            ->join('products', 'meter_sales.product_id', '=', 'products.id')
            ->whereIn('meter_sales.shift_id', $request->shift_ids)
            ->where('pump_operator_assignments.status', 'close');
        
        $query = $query->where('meter_sales.settlement_no', $request->active_settlement_id);

        // Check if shift_ids are provided
        if ($request->shift_ids) {
            $query = $query->whereIn('pump_operator_assignments.shift_id', $request->shift_ids); // Fixed table reference
        }

        // Retrieve results
        $results = $query->select('meter_sales.*', 'products.name as product_name', 'products.sku as product_sku', 'pumps.pump_name')->distinct();

        $meter_sales = DataTables::of($results)
            ->addColumn('quantity', function($row) {
                $pump = Pump::where('id', $row->pump_id)->first();
                $quantity = $row->closing_meter - $row->starting_meter - $row->testing_qty;               
                if($pump->bulk_sale_meter == 1){
                    $quantity = $row->qty;
                }
                return number_format($quantity,3,'.',',');
            })
            ->addColumn('total_qty', function($row) {
                $pump = Pump::where('id', $row->pump_id)->first();
                $quantity = $row->closing_meter - $row->starting_meter - $row->testing_qty;               
                if($pump->bulk_sale_meter == 1){
                    $quantity = $row->qty;
                }
                return number_format(($row->testing_qty + $quantity),3,'.',',');
            })
            ->editColumn('starting_meter', function ($row) {
                return number_format($row->starting_meter,3,'.',',');
            })
            ->editColumn('closing_meter', function ($row) {
                return number_format($row->closing_meter,3,'.',',');
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
    
    public function otherSalesList(Request $request)
    {

        $business_id = Auth::user()->business_id;
    
        if (request()->ajax()) 
        {
            $business_details = Business::find($business_id);


            // $active_settlement = Settlement::where('status', 1)
            // ->where('business_id', $business_id)
            // ->select('settlements.*')
            // ->with(['other_sales'])->first();

            // $userSales = $active_settlement->other_sales;

            // $query = PumpOperatorOtherSale::join('products', 'products.id', '=', 'pump_operator_other_sales.product_id');
            // $query = $query->leftjoin('variations', 'products.id', 'variations.product_id')
            // ->leftjoin('variation_location_details', 'variations.id', 'variation_location_details.variation_id');
        
            // if ($request->shift_ids) {
            //     $query = $query->whereIn('pump_operator_other_sales.shift_id', $request->shift_ids);
            // } else {
            //     $query = $query->where('pump_operator_other_sales.shift_id', $request->shift_id);
            // }

            // $print = false;
            // if($request->print_other_sale_ids){
            //     // \Log::debug("otherSalesList ajax", ["print_other_sale_ids" => $request->print_other_sale_ids]);
            //     $print = ($request->print == "1");
            //     $query = $query->whereIn('pump_operator_other_sales.id', $request->print_other_sale_ids);
            // }

            // $query = $query->join('pump_operator_assignments', function ($join) {
            //     $join->on('pump_operator_assignments.shift_id', '=', 'pump_operator_other_sales.shift_id')
            //          ->where('pump_operator_assignments.status', 'close')
            //          ->whereRaw('pump_operator_assignments.id = (
            //              SELECT MAX(poa.id)
            //              FROM pump_operator_assignments poa
            //              WHERE poa.shift_id = pump_operator_other_sales.shift_id AND poa.status = "close"
            //          )');
            // })
            // ->select('pump_operator_other_sales.*', 'products.name as product_name', 'products.sku as product_sku', 'pump_operator_assignments.shift_number', 'qty_available');

            // $query->groupBy('pump_operator_other_sales.id');

            // if($request->get_total){
            //     return [
            //         'success' => 1,
            //         'total'=> $query->sum("sub_total")
            //     ];
            // }

            // $other_sales = DataTables::of($query)
            //     ->addColumn('quantity', function($row) {
            //         return number_format($row->qty);
            //     })
            //     ->editColumn(
            //         'price',
            //         function ($row) use ($business_details) {
            //             return '<span class="display_currency amount" data-orig-value="' . $row->price . '" data-currency_symbol=false>' .
            //                 $this->productUtil->num_f($row->price, false, $business_details, true) .
            //                 '</span>';
            //         }
            //     )
            //     ->editColumn(
            //         'created_at',
            //         function ($row) {
            //             return (new \DateTime($row->created_at))->format('Y-m-d H:i:s');
            //         }
            //     )
            //     ->editColumn(
            //         'qty_available',
            //         function ($row) {
            //             return number_format($row->qty_available,4,'.',',');
            //         }
            //     )
            //     ->editColumn(
            //         'sub_total',
            //         function ($row) use ($business_details) {
            //             return '<span class="display_currency sub_total" data-orig-value="' . $row->sub_total . '" data-currency_symbol=false>' .
            //                 $this->productUtil->num_f($row->sub_total, false, $business_details, true) .
            //                 '</span>';
            //         }
            //     );

            // return $other_sales->rawColumns(['price', 'sub_total'])->make(true);


            $currency_precision = !empty($business_details->currency_precision) ? $business_details->currency_precision : 2;

            $otherSaleFinalTotal = 0.00;

            $active_settlement = Settlement::where('status', 1)
                ->where('business_id', $business_id)
                ->select('settlements.*')
                ->with(['other_sales'])
                ->first();

    
            $userSales = [];
   
    
   
            $query = PumpOperatorOtherSale::join('products', 'products.id', '=', 'pump_operator_other_sales.product_id')
                ->leftJoin('variations', 'products.id', 'variations.product_id')
                ->leftJoin('variation_location_details', 'variations.id', 'variation_location_details.variation_id');
            
            if ($request->shift_ids) {
                $query->whereIn('pump_operator_other_sales.shift_id', $request->shift_ids);
            } else {
                $query->where('pump_operator_other_sales.shift_id', $request->shift_id);
            }
    
            $print = false;
            if ($request->print_other_sale_ids) {
                $print = ($request->print == "1");
                $query->whereIn('pump_operator_other_sales.id', $request->print_other_sale_ids);
            }
            
            $query->join('pump_operator_assignments', function ($join) {
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


            // Safe check: Initialize userSales as empty collection first
            $userSales = collect();

            // Check and load other_sales only if active_settlement is not null
            if (!empty($active_settlement) && $active_settlement->other_sales) {
                $userSales = $active_settlement->other_sales->map(function ($item) use($currency_precision, &$otherSaleFinalTotal) {
                    $product = \App\Product::find($item->product_id); 

                    $discount_amount = $item->discount_amount ?? 0.00;
                    $withDiscount = ($item->sub_total ?? 0.00) - $discount_amount;
                    
                    //$pump_other_sale_final_total += $withDiscount;
                    $otherSaleFinalTotal += $withDiscount;

                    return (object)[
                        'id'              => $item->id ?? null,
                        'product_sku'     => $product->sku ?? '',
                        'product_name'    => $product->name ?? '',
                        'balance_stock'   => number_format($item->balance_stock ?? 0, 4, '.', ','), // Safe number_format
                        'price'           => $item->price ?? 0,
                        'qty'             => $item->qty ?? 0,
                        'discount_type'   => $item->discount_type ?? '',
                        'discount'        => $item->discount ?? 0,
                        'sub_total'       => $item->sub_total ?? 0,
                        'with_discount'   => $withDiscount,
                        'created_at'      => $item->created_at ?? '',
                        'qty_available'   => number_format($item->balance_stock ?? 0, 4, '.', ','), // Not applicable for user sales
                        'user_check'      => 1  // Mark as user entry
                    ];
                });
            }


            // Safe fetch: If query has result or not, will always be a collection
            $pumpSalesCollection = $query->get();

            // Safe check: If empty, keep as empty collection
            $pumpSales = collect();
            if (!$pumpSalesCollection->isEmpty()) {
                $pumpSales = $pumpSalesCollection->map(function ($item) use($currency_precision, &$otherSaleFinalTotal) {

                    $discount_amount = $item->discount ?? 0;
                    $withDiscount = ($item->sub_total ?? 0) - $discount_amount;
                
                    //$pump_other_sale_final_total += $withDiscount;
                    $otherSaleFinalTotal += $withDiscount;


                    return (object)[
                        'id'              => $item->id,
                        'product_sku'     => $item->product_sku ?? '',
                        'product_name'    => $item->product_name ?? '',
                        'balance_stock'   => number_format((float) ($item->qty_available ?? 0), 4, '.', ','),
                        'price'           => $item->price ?? 0,
                        'qty'             => $item->qty ?? 0,
                        'discount_type'   => $item->discount_type ?? '',
                        'discount'        => $item->discount ?? 0,
                        'sub_total'       => $item->sub_total ?? 0,
                        'with_discount'   => $withDiscount,
                        'created_at'      => $item->created_at ?? '',
                        'qty_available'   => $item->qty_available ?? '',
                        'user_check'      => 0  // Mark as pump sale entry
                    ];
                });
            }
            $pump_nos = Pump::whereIn('id', function($query) use ($request) {
                $query->select('pump_id')
                    ->from('pump_operator_assignments')
                    ->whereIn('shift_id', $request->shift_ids);
            })->pluck('pump_name', 'id');

            // If total requested
            if ($request->get_total) {
                return [
                    'success' => 1,
                    'pump_nos' => $pump_nos,
                    'total' => $otherSaleFinalTotal
                ];
            }

            // ✅ Combine both collections safely
            $combinedSales = collect($userSales)->merge(collect($pumpSales));

            $other_sales = DataTables::of($combinedSales)
            ->addColumn('quantity', function ($row) {
                return number_format($row->qty); // ✅ as object
            })
            ->editColumn(
                'price',
                function ($row) use ($business_details) {
                    return '<span class="display_currency amount" data-orig-value="' . $row->price . '" data-currency_symbol=false>' .
                        $this->productUtil->num_f($row->price, false, $business_details, true) .
                        '</span>';
                }
            )
            ->editColumn('created_at', function ($row) {
                return (new \DateTime($row->created_at))->format('Y-m-d H:i:s');
            })
            ->editColumn('qty_available', function ($row) {
                return number_format((float) ($row->qty_available ?? 0), 4, '.', ',');
            })
            ->editColumn(
                'sub_total',
                function ($row) use ($business_details) {
                    return '<span class="display_currency sub_total" data-orig-value="' . $row->sub_total . '" data-currency_symbol=false>' .
                        $this->productUtil->num_f($row->sub_total, false, $business_details, true) .
                        '</span>';
                }
            )
            ->editColumn(
                'with_discount',
                function ($row) use($currency_precision) {
                    return '<span class="display_currency with_discount" data-orig-value="' . $row->with_discount . '" data-currency_symbol=false>' .
                        number_format($row->with_discount, $currency_precision) .
                        '</span>';
                }
            )
            ->addColumn('action', function ($row) {
                if (isset($row->user_check) && $row->user_check == 1) {
                    return '<button class="btn btn-xs btn-danger delete_other_sale" data-href="/petro/settlement/delete-other-sale/' . $row->id . '"><i class="fa fa-times"></i></button>';
                }
                return '';
            });
            return $other_sales->rawColumns(['price', 'sub_total', 'with_discount'])->make(true);

        }

        $print = false;
        $print_other_sale_ids = [];
        if($request->print_other_sale_ids){
            // \Log::debug("otherSalesList", ["print_other_sale_ids" => $request->print_other_sale_ids]);
            $print_other_sale_ids = explode(",", $request->print_other_sale_ids);
            $print = true;
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

        return view('petro::pump_operators.other_sales_list')->with(compact('shift_number', 'shifts', 'layout', 'print_other_sale_ids', 'print'));
    }

public function pumpOtherSalesList(Request $request){

    $business_id = Auth::user()->business_id;
    
    if (request()->ajax()) {
        $business_details = Business::find($business_id);
        $query = PumpOperatorOtherSale::join('products', 'products.id', '=', 'pump_operator_other_sales.product_id');
        $query = $query->leftjoin('variations', 'products.id', 'variations.product_id')
        ->leftjoin('variation_location_details', 'variations.id', 'variation_location_details.variation_id');
        
        if ($request->shift_ids) {
            $query = $query->whereIn('pump_operator_other_sales.shift_id', $request->shift_ids);
        } else {
            $query = $query->where('pump_operator_other_sales.shift_id', $request->shift_id);
        }

        $print = false;
        if($request->print_other_sale_ids){
            // \Log::debug("otherSalesList ajax", ["print_other_sale_ids" => $request->print_other_sale_ids]);
            $print = ($request->print == "1");
            $query = $query->whereIn('pump_operator_other_sales.id', $request->print_other_sale_ids);
        }

        $query = $query->join('pump_operator_assignments', function ($join) {
            $join->on('pump_operator_assignments.shift_id', '=', 'pump_operator_other_sales.shift_id')
                 ->whereRaw('pump_operator_assignments.id = (
                     SELECT MAX(poa.id)
                     FROM pump_operator_assignments poa
                     WHERE poa.shift_id = pump_operator_other_sales.shift_id
                 )');
        })
        ->select('pump_operator_other_sales.*', 'products.name as product_name', 'products.sku as product_sku', 'pump_operator_assignments.shift_number', 'qty_available');

        $query = $query->groupBy('pump_operator_other_sales.id');

        if($request->get_total){
            return [
                'success' => 1,
                'total'=> $query->sum("sub_total")
            ];
        }

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
                'created_at',
                function ($row) {
                    return (new \DateTime($row->created_at))->format('Y-m-d H:i:s');
                }
            )
            ->editColumn(
                'qty_available',
                function ($row) {
                    return number_format($row->qty_available,4,'.',',');
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

    $print = false;
    $print_other_sale_ids = [];
    if($request->print_other_sale_ids){
        // \Log::debug("otherSalesList", ["print_other_sale_ids" => $request->print_other_sale_ids]);
        $print_other_sale_ids = explode(",", $request->print_other_sale_ids);
        $print = true;
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

    return view('petro::pump_operators.other_sales_list')->with(compact('shift_number', 'shifts', 'layout', 'print_other_sale_ids', 'print'));
}

    public function othersalespage(Request $request) 
    {
        $user = auth()->user();
        if ($user->is_pump_operator ) {
            $permission = UserStorePermission::where('user_id', $user->id)->first();
            if (!$permission || !$permission->sell) {
                return back()->with('status', 'Please Request Sale Permission from Owner');
            }
        }
        $products = Product::leftJoin('variations', 'variations.product_id', '=', 'products.id')
        ->leftJoin('units', 'products.unit_id', '=', 'units.id')
        ->leftJoin('variation_location_details', 'variation_location_details.variation_id', '=', 'variations.id')
        ->where('products.category_id', '!=', self::FUEL_CATEGORY_ID)
        ->select(
            'products.*',
            'units.actual_name as unit',
            DB::raw('SUM(variation_location_details.qty_available) as current_stock'),
        )
        ->groupBy('products.id')
        ->get();
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

    public function updateOtherSaleItem(Request $request, $id)
    {
        try {
            $sale_item = PumpOperatorOtherSale::find($id);
    
            if ($sale_item) {
                $product = Product::find($sale_item->product_id);
    
                if (!$product) {
                    return [
                        'success' => false,
                        'msg' => __('Product not found.'),
                    ];
                }
    
                $variation = Variation::where('product_id', $product->id)->first();
                if (!$variation) {
                    return [
                        'success' => false,
                        'msg' => __('Variation not found.'),
                    ];
                }
    
                $variation_details = VariationLocationDetails::where('variation_id', $variation->id)->first();

                if (!$variation_details) {
                    return [
                        'success' => false,
                        'msg' => __('Stock details not found.'),
                    ];
                }
    
    
               // Calculate difference
                $old_quantity = (float) $sale_item->qty;
                $new_quantity = (float) $request->quantity;
                $difference = $new_quantity - $old_quantity;

                // Check if increasing and stock is enough
                if ($difference > 0 && $variation_details->qty_available < $difference) {
                    return [
                        'success' => false,
                        'msg' => __('Not enough stock available.'),
                    ];
                }

                // Update stock properly
                $variation_details->qty_available -= $difference; // Handles both +ve and -ve
                $variation_details->save();

                // Update sale item
                $sale_item->qty = $new_quantity;
                $sale_item->balance_stock += ($old_quantity - $new_quantity); // Add back the returned qty
                $sale_item->sub_total = round($variation->sell_price_inc_tax * $new_quantity, 2);
                $sale_item->save();
            }
    
            $output = [
                'success' => true,
                'msg' => __('petro::lang.success'),
            ];
        } catch (\Throwable $e) {
            Log::debug($e);
            Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
    
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }
    
        return $output;
    }
    
    public function deleteOtherSaleItem($id)
    {
        try {
            $sale_item = PumpOperatorOtherSale::find($id);
    
            if ($sale_item) {
                $product = Product::find($sale_item->product_id);
    
                if ($product) {
                    $variation = Variation::where('product_id', $product->id)->first();
    
                    if ($variation) {
                        $variation_details = VariationLocationDetails::where('variation_id', $variation->id)->first();
    
                        if ($variation_details) {
                            $variation_details->qty_available += $product->qty;
                            $variation_details->save();
                        }
                    }
                }
    
                $sale_item->delete();
            }
    
            $output = [
                'success' => true,
                'msg' => __('petro::lang.success'),
            ];
        } catch (\Throwable $e) {
            Log::debug($e);
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
    
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
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
            // discount default to 0
            $discount = 0;
            $pump_operator_id = Auth::user()->pump_operator_id;
            $shift_id = PetroShift::where('pump_operator_id',$pump_operator_id)->get()->last()->id ?? 0;

            $print_other_sale_ids = [];
            $print = false;
            foreach($request->items as $item){
                $product = Product::leftjoin('variations', 'products.id', 'variations.product_id')
                ->leftjoin('variation_location_details', 'variations.id', 'variation_location_details.variation_id')
                ->where('products.id', $item['product_id'])->select('qty_available')->first();
                $data = array(
                    'business_id' => $business_id,
                    'store_id' => $store_id,
                    'product_id' => $item['product_id'],
                    'price' => $item['price'],
                    'qty' => $item['amount'],
                    'balance_stock' => $product->qty_available,
                    'discount' => $discount,
                    'sub_total' => ($item['price'] * $item['amount']),
                    'shift_id' => $shift_id
                );
                $other_sale = PumpOperatorOtherSale::create($data);
                $databaseName = DB::connection()->getDatabaseName();
                if($request->print){
                    $print = $request->print;
                    $print_other_sale_ids[] = $other_sale->id;
                }
            }

            $html_content = "";
            if ($print){
                $pump_operator = PumpOperator::findOrFail($pump_operator_id);
                $location_id = $pump_operator->location_id;

                $location_details = BusinessLocation::find($location_id);
                $invoice_layout = $this->businessUtil->invoiceLayout($business_id, $location_id, $location_details->invoice_layout_id);

                $printer_type = null;
                $business_details = $this->businessUtil->getDetails($business_id);
                $receipt_printer_type = is_null($printer_type) ? $location_details->receipt_printer_type : $printer_type;
                $receipt_details = $this->transactionUtil->getOtherSaleReceiptDetails($print_other_sale_ids, $location_id, $invoice_layout, $business_details, $location_details, $receipt_printer_type);

                $currency_details = [
                    'symbol'             => $business_details->currency_symbol,
                    'thousand_separator' => $business_details->thousand_separator,
                    'decimal_separator'  => $business_details->decimal_separator,
                ];
                $receipt_details->currency = $currency_details;
                //$layout = !empty($receipt_details->design) ? 'sale_pos.receipts.' . $receipt_details->design : 'sale_pos.receipts.classic';
                $layout = !empty($receipt_details->design) ? 'sale_pos.receipts.classic-other-sale' : 'sale_pos.receipts.classic';

                $html_content = view($layout, compact('receipt_details'))->render();
            }

            $output = [
                'success' => true,
                'msg' => __('petro::lang.success'),
                'print' => $print,
                'print_other_sale_ids' => $print_other_sale_ids,
                'html_content' => $html_content,
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

            $daily_collection = PumpOperatorPayment::where('business_id', $business_id)->whereNotNull('collection_form_no')->orderBy('id', 'DESC')->select('collection_form_no')->first();
            if(!is_null($daily_collection)){
                $collection_form_no = (int) $daily_collection->collection_form_no + 1;
            } else {
                $collection_form_no = 1;
            }
            $DailyCollection = DailyCollection::where('business_id', $business_id)->whereNotNull('collection_form_no')->orderBy('id', 'DESC')->select('collection_form_no')->first();
            if(!is_null($DailyCollection)){
                if($DailyCollection->collection_form_no >= $collection_form_no){
                    $collection_form_no = (int) $DailyCollection->collection_form_no + 1;
                }
            }

            $shift_id = PumpOperatorAssignment::where('pump_operator_id', $pump_operator_id)->orderBy('id', 'DESC')->select('shift_id')->first()->shift_id;
            
            $meter_sale = PumpOperatorMeterSale::create(array(
                'business_id' => $business_id,	
                'date_time' => date('Y-m-d H:i'),	
                'pump_operator_id' => $pump_operator_id,	
                'amount' => $request->grand_total,	
                'deposited' => $request->today_deposited,	
                'balance' => $request->balance_to_deposit,	
                'collection_form_no' => $collection_form_no,
                'shift_id' => $shift_id,
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

    public function metersWithPayments()
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
            $query = PumpOperatorPayment::leftjoin('pump_operators', 'pump_operator_payments.pump_operator_id', 'pump_operators.id')
            ->leftjoin('pump_operator_meter_sales', 'pump_operator_payments.collection_form_no', 'pump_operator_meter_sales.collection_form_no')
            ->leftjoin('pump_operator_meter_sale_details', 'pump_operator_meter_sales.id', 'pump_operator_meter_sale_details.sale_id')
            ->where('pump_operators.business_id', $business_id)
            ->select('pump_operator_payments.*', 'pump_operators.name as pump_operator_name');

            if($only_pumper){
                $query->where('pump_operators.id', $pump_operator_id);
            }

            if (!empty(request()->pump_id)) {
                $query->where('pump_operator_meter_sale_details.pump_id', request()->pump_id);
            }

            if (!empty(request()->pump_operator_id)) {
                $query->where('pump_operators.id', request()->pump_operator_id);
            }
            if($only_pumper){
                $shift_id = PumpOperatorAssignment::where('pump_operator_id', $pump_operator_id)->orderBy('id', 'DESC')->select('shift_id')->first()->shift_id;
            }
            if (!empty($shift_id)) {
                $query->where('pump_operator_payments.shift_id', $shift_id);
            }

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $query->whereDate('date_and_time', '>=', request()->start_date);
                $query->whereDate('date_and_time', '<=', request()->end_date);
            }


            $query->groupBy('pump_operator_payments.id');

            $pump_operators_payments = DataTables::of($query)
                ->addColumn('date', '{{@format_date($date_and_time)}}')
                ->addColumn('time', '{{@format_time($date_and_time)}}')
                ->addColumn(
                    'pumps',
                    function ($row) {
                        $pumps = "";
                        $meter_sale = PumpOperatorMeterSale::where('collection_form_no', $row->collection_form_no)->first();
                        if(!is_null($meter_sale)){
                            $meter_sale_details = PumpOperatorMeterSaleDetail::where('sale_id', $meter_sale->id)->get();
                            foreach($meter_sale_details as $meter_sale_detail){
                                $pump = Pump::where('id', $meter_sale_detail->pump_id)->select('pump_name')->first();
                                if($pumps == ""){
                                    $pumps = $pump->pump_name;
                                } else {
                                    $pumps = $pumps . ", " .$pump->pump_name;
                                }
                            }
                        }
                        return  $pumps;
                    }
                )
                ->addColumn(
                    'unit_price',
                    function ($row) use ($business_details) {
                        $unit_price = "";
                        $meter_sale = PumpOperatorMeterSale::where('p_o_payment_id', $row->id)->first();
                        if(!is_null($meter_sale)){
                            $meter_sale_details = PumpOperatorMeterSaleDetail::where('sale_id', $meter_sale->id)->get();
                            foreach($meter_sale_details as $meter_sale_detail){
                                if($unit_price == ""){
                                    $unit_price = number_format($meter_sale_detail->unit_price, $business_details->currency_precision, ".", ",");
                                } else {
                                    $unit_price = $unit_price . ", " . number_format($meter_sale_detail->unit_price, "2", ".", ",");
                                }
                            }
                        }
                        return  $unit_price;
                    }
                )
                ->addColumn(
                    'last_meter',
                    function ($row) {
                        $received_meter = "";
                        $meter_sale = PumpOperatorMeterSale::where('p_o_payment_id', $row->id)->first();
                        if(!is_null($meter_sale)){
                            $meter_sale_details = PumpOperatorMeterSaleDetail::where('sale_id', $meter_sale->id)->get();
                            foreach($meter_sale_details as $meter_sale_detail){
                                if($received_meter == ""){
                                    $received_meter = number_format($meter_sale_detail->received_meter, "3", ".", ",");
                                } else {
                                    $received_meter = $received_meter . ", " . number_format($meter_sale_detail->received_meter, "3", ".", ",");
                                }
                            }
                        }
                        return  $received_meter;
                    }
                )
                ->addColumn(
                    'new_meter',
                    function ($row) {
                        $new_meter = "";
                        $meter_sale = PumpOperatorMeterSale::where('p_o_payment_id', $row->id)->first();
                        if(!is_null($meter_sale)){
                            $meter_sale_details = PumpOperatorMeterSaleDetail::where('sale_id', $meter_sale->id)->get();
                            foreach($meter_sale_details as $meter_sale_detail){
                                if($new_meter == ""){
                                    $new_meter = number_format($meter_sale_detail->new_meter, "3", ".", ",");
                                } else {
                                    $new_meter = $new_meter . ", " .number_format($meter_sale_detail->new_meter, "3", ".", ",");
                                }
                            }
                        }
                        return  $new_meter;
                    }
                )
                ->addColumn(
                    'qty_sold',
                    function ($row) {
                        $sold_qty = "";
                        $meter_sale = PumpOperatorMeterSale::where('p_o_payment_id', $row->id)->first();
                        if(!is_null($meter_sale)){
                            $meter_sale_details = PumpOperatorMeterSaleDetail::where('sale_id', $meter_sale->id)->get();
                            foreach($meter_sale_details as $meter_sale_detail){
                                if($sold_qty == ""){
                                    $sold_qty = number_format($meter_sale_detail->sold_qty, "3", ".", ",");
                                } else {
                                    $sold_qty = $sold_qty . ", " .number_format($meter_sale_detail->sold_qty, "3", ".", ",");
                                }
                            }
                        }
                        return  $sold_qty;
                    }
                )
                ->addColumn(
                    'total_sold_amount',
                    function ($row) use ($business_details) {
                        $amount = "";
                        $meter_sale = PumpOperatorMeterSale::where('p_o_payment_id', $row->id)->first();
                        if(!is_null($meter_sale)){
                            $amount = number_format($meter_sale->amount, $business_details->currency_precision, ".", ",");
                        }
                        return  $amount;
                    }
                )
                ->removeColumn('id')
                ->editColumn('payment_type', '{{ucfirst($payment_type)}}')
                ->editColumn(
                    'amount',
                    function ($row) use ($business_details) {
                        return  '<span class="display_currency amount" data-orig-value="' . $row->payment_amount . '" data-currency_symbol = false>' . $this->productUtil->num_f($row->payment_amount, false, $business_details, true) . '</span>';
                    }
                );

            return $pump_operators_payments->rawColumns(['amount', 'action'])
                ->make(true);
        }

        $layout = 'app';
        if ($only_pumper) {
            $layout = 'pumper';
        }
        
        $shifts = PetroShift::join('pump_operators','pump_operators.id','petro_shifts.pump_operator_id')->where('petro_shifts.business_id',$business_id)->select('pump_operators.name','petro_shifts.*')->orderBy('id','DESC');
        if ($only_pumper) {
            $shifts->where('pump_operator_id', $pump_operator_id);
        }
        $shifts = $shifts->get();

        $shift_number = PumpOperatorAssignment::where('pump_operator_id', $pump_operator_id)->max('shift_number');

        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');

        return view('petro::pump_operators.meters_with_payments')->with(compact(
            'only_pumper',
            'layout',
            'shifts',
            'shift_number',
            'pump_operators'
        ));
    }
}
