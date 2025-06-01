<?php

namespace Modules\Petro\Http\Controllers;

use App\Contact;
use App\Account;
use App\AccountGroup;
use App\Business;
use App\BusinessLocation;
;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Petro\Entities\PumpOperator;
use App\Utils\Util;
use App\Utils\ProductUtil;
use App\Utils\ModuleUtil;
use App\Utils\NotificationUtil;
use App\Utils\TransactionUtil;
use App\Utils\BusinessUtil;
use Illuminate\Support\Facades\Auth;
use Modules\Petro\Entities\DailyCollection;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Modules\Petro\Entities\DailyCard;
use Modules\Petro\Entities\DailyVoucher;
use Modules\Petro\Entities\PetroShift;
use Modules\Petro\Entities\PumpOperatorPayment;

class DailyCollectionController extends Controller
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
    public function __construct(Util $commonUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil, NotificationUtil $notificationUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
        $this->notificationUtil = $notificationUtil;
    }


    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');

        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module')) {
            abort(403, 'Unauthorized Access');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            if (request()->ajax()) {
                $query = DailyCollection::leftjoin('business_locations', 'daily_collections.location_id', 'business_locations.id')
                    ->leftjoin('pump_operators', 'daily_collections.pump_operator_id', 'pump_operators.id')
                    ->leftjoin('users', 'daily_collections.created_by', 'users.id')
                    ->leftjoin('settlements', 'daily_collections.settlement_id', 'settlements.id')
                    ->where('daily_collections.business_id', $business_id)
                    ->select([
                        'daily_collections.*',
                        'business_locations.name as location_name',
                        'pump_operators.name as pump_operator_name',
                        'settlements.settlement_no as settlement_no',
                        'settlements.status as settlement_status',
                        'users.username as user',
                        'settlements.transaction_date as settlement_dates'
                    ]);
                
                if (!empty(request()->location_id)) {
                    $query->where('daily_collections.location_id', request()->location_id);
                }
                
                if (!empty(request()->status)) {
                    if(request()->status == 'completed'){
                        $query->whereNotNull('settlements.settlement_no')->where('settlements.status',0);
                    }
                    
                    if(request()->status == 'pending'){
                        $query->where(function($q) {
                            $q->whereNull('settlements.settlement_no')
                              ->orWhere('settlements.status', 1);
                        });

                    }
                }
                
                if (!empty(request()->settlement_id)) {
                    $query->where('settlements.id', request()->settlement_id);
                }
                
                if (!empty(request()->pump_operator)) {
                    $query->where('daily_collections.pump_operator_id', request()->pump_operator);
                }
                if (!empty(request()->settlement_no)) {
                    $query->where('daily_collections.id', request()->settlement_no);
                }                    
                if (!empty(request()->start_date) && !empty(request()->end_date)) {
                    $query->whereDate('daily_collections.created_at', '>=', request()->start_date);
                    $query->whereDate('daily_collections.created_at', '<=', request()->end_date);
                }
                $query->orderBy('collection_form_no', 'desc');
                $fuel_tanks = Datatables::of($query)
                    ->addColumn(
                        'action',
                        '<button class="btn btn-primary btn-xs print_btn_pump_operator" data-href="{{action(\'\Modules\Petro\Http\Controllers\DailyCollectionController@print\', [$id])}}"><i class="fa fa-print" aria-hidden="true"></i> @lang("petro::lang.print")</button>
                        @if(empty($settlement_no))@can("daily_collection.edit") &nbsp; <button data-href="{{action(\'\Modules\Petro\Http\Controllers\DailyCollectionController@edit\', [$id])}}" data-container=".pump_modal" class="btn btn-success btn-xs btn-modal edit_reference_button"><i class="fa fa-pencil" aria-hidden="true"></i> @lang("lang_v1.edit")</button> &nbsp; @endcan @endif
                        @if(empty($settlement_no))@can("daily_collection.delete")<a class="btn btn-danger btn-xs delete_daily_collection" href="{{action(\'\Modules\Petro\Http\Controllers\DailyCollectionController@destroy\', [$id])}}"><i class="fa fa-trash" aria-hidden="true"></i> @lang("petro::lang.delete")</a>@endcan @endif'
                    )
                    /**
                     * @ChangedBy Afes
                     * @Date 25-05-2021
                     * @Task 12700
                     */
                     ->editColumn('current_amount','{{@num_format($current_amount)}}')
                    
                    ->editColumn('balance_collection','{{@num_format($balance_collection)}}')
                    ->addColumn('total_collection', function ($id) {
                        $total = DB::table('daily_collections')
                                ->where('pump_operator_id', $id->pump_operator_id)
                                ->where('id', '<=', $id->id)
                                ->whereNull('settlement_id')
                                ->sum('current_amount') ?? 0;
                            
                            return $this->productUtil->num_f($total);
                            
                        
                    })
                    ->addColumn('status',function($row){
                        if(empty($row->settlement_no) || $row->settlement_status == 1){
                            return 'Pending';
                        }else{
                            return 'Completed';
                        }
                    })
                    ->addColumn(
                        'created_at',
                        '{{@format_date($created_at)}}'
                    )
                    
                    ->editColumn(
                        'settlement_dates',
                        '{{!empty($settlement_dates) ? @format_date($settlement_dates) : ""}}'
                    )


                    ->removeColumn('id');


                return $fuel_tanks->rawColumns(['action','total_collection'])
                    ->make(true);
            }
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');
        $settlement_nos = [];

        $message = $this->transactionUtil->getGeneralMessage('general_message_pump_management_checkbox');
        
        $customers = Contact::customersDropdown($business_id, false, true, 'customer');
        $card_types = [];
        $card_group = AccountGroup::where('business_id', $business_id)->where('name', 'Card')->first();
        if (!empty($card_group)) {
            $card_types = Account::where('business_id', $business_id)->where('asset_type', $card_group->id)->where(DB::raw("REPLACE(`name`, '  ', ' ')"), '!=', 'Cards (Credit Debit) Account')->pluck('name', 'id');
        }
        
        $slip_nos = DailyCard::where('business_id',$business_id)->distinct('slip_no')->get()->pluck('slip_no','slip_no');
        $card_numbers = DailyCard::where('business_id',$business_id)->distinct('card_number')->get()->pluck('card_number','card_number');
        
        $daily_card_settlements = DailyCard::leftjoin('settlements','settlements.id','daily_cards.settlement_no')
                                            ->where('daily_cards.business_id',$business_id)
                                            ->whereNotNull('daily_cards.settlement_no')
                                            ->orderBy('settlements.id','DESC')
                                            ->distinct('settlements.settlement_no')
                                            ->pluck('settlements.settlement_no','settlements.id');
                                            
        $daily_collection_settlements = DailyCollection::leftjoin('settlements','settlements.id','daily_collections.settlement_id')
                                            ->where('daily_collections.business_id',$business_id)
                                            ->whereNotNull('daily_collections.settlement_id')
                                            ->orderBy('settlements.id','DESC')
                                            ->distinct('settlements.settlement_no')
                                            ->pluck('settlements.settlement_no','settlements.id');
                                            
        $daily_voucher_settlements = DailyVoucher::leftjoin('settlements','settlements.id','daily_vouchers.settlement_no')
                                            ->where('daily_vouchers.business_id',$business_id)
                                            ->whereNotNull('daily_vouchers.settlement_no')
                                            ->orderBy('settlements.id','DESC')
                                            ->distinct('settlements.settlement_no')
                                            ->pluck('settlements.settlement_no','settlements.id');
                                            
        $shortage_settlements = PumpOperatorPayment::leftjoin('settlements', 'pump_operator_payments.settlement_no', 'settlements.id')
                                            ->where('pump_operator_payments.business_id', $business_id)
                                            ->whereIn('payment_type',['shortage','excess'])
                                            ->whereNotNull('pump_operator_payments.settlement_no')
                                            ->orderBy('settlements.id','DESC')
                                            ->distinct('settlements.settlement_no')
                                            ->pluck('settlements.settlement_no','settlements.id');
        $cheques_settlements = PumpOperatorPayment::leftjoin('settlements', 'pump_operator_payments.settlement_no', 'settlements.id')
                                            ->where('pump_operator_payments.business_id', $business_id)
                                            ->whereIn('payment_type',['cheque'])
                                            ->whereNotNull('pump_operator_payments.settlement_no')
                                            ->orderBy('settlements.id','DESC')
                                            ->distinct('settlements.settlement_no')
                                            ->pluck('settlements.settlement_no','settlements.id');

        $others_settlements = PumpOperatorPayment::leftjoin('settlements', 'pump_operator_payments.settlement_no', 'settlements.id')
                                            ->where('pump_operator_payments.business_id', $business_id)
                                            ->whereIn('payment_type',['other'])
                                            ->whereNotNull('pump_operator_payments.settlement_no')
                                            ->orderBy('settlements.id','DESC')
                                            ->distinct('settlements.settlement_no')
                                            ->pluck('settlements.settlement_no','settlements.id');
                                            
        return view('petro::daily_collection.index')->with(compact(
            'card_types','customers',
            'business_locations',
            'pump_operators',
            'settlement_nos',
            'message',
            'slip_nos',
            'card_numbers',
            'daily_card_settlements',
            'daily_collection_settlements','daily_voucher_settlements','shortage_settlements','cheques_settlements','others_settlements'
        ));
    }
    
    public function collectionSummary()
    {
        $business_id = request()->session()->get('user.business_id');

        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module')) {
            abort(403, 'Unauthorized Access');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            
                $daily_cash = DailyCollection::leftJoin('pump_operators', 'daily_collections.pump_operator_id', '=', 'pump_operators.id')
                        ->leftjoin('settlements', 'daily_collections.settlement_id', 'settlements.id')
                        ->where('daily_collections.business_id', $business_id)
                        ->select([
                            DB::raw('"daily_cash" as type'),
                            DB::raw('MAX(daily_collections.collection_form_no) as collection_form_no'),
                            'daily_collections.created_at as date',
                            'pump_operators.name as pump_operator_name',
                            DB::raw('SUM(daily_collections.current_amount) as total_amount'),
                            DB::raw('GROUP_CONCAT(settlements.settlement_no SEPARATOR ", ") as settlement_nos')
                        ])
                        ->groupBy(DB::raw('DATE(daily_collections.created_at)'),'pump_operators.id');
                        
                $daily_cards = DailyCard::leftJoin('pump_operators', 'daily_cards.pump_operator_id', '=', 'pump_operators.id')
                        ->leftjoin('settlements', 'daily_cards.settlement_no', 'settlements.id')
                        ->where('daily_cards.business_id', $business_id)
                        ->select([
                            DB::raw('"daily_cards" as type'),
                            DB::raw('MAX(daily_cards.collection_no) as collection_form_no'),
                            'daily_cards.date as date',
                            'pump_operators.name as pump_operator_name',
                            DB::raw('SUM(daily_cards.amount) as total_amount'),
                            DB::raw('GROUP_CONCAT(settlements.settlement_no SEPARATOR ", ") as settlement_nos')
                        ])
                        ->groupBy('daily_cards.date','pump_operators.id');
                        
                $daily_credit_sales = DailyVoucher::leftJoin('pump_operators', 'daily_vouchers.operator_id', '=', 'pump_operators.id')
                        ->leftjoin('settlements', 'daily_vouchers.settlement_no', 'settlements.id')
                        ->where('daily_vouchers.business_id', $business_id)
                        ->select([
                            DB::raw('"daily_credit_sales" as type'),
                            DB::raw('MAX(daily_vouchers.daily_vouchers_no) as collection_form_no'),
                            'daily_vouchers.transaction_date as date',
                            'pump_operators.name as pump_operator_name',
                            DB::raw('SUM(daily_vouchers.total_amount) as total_amount'),
                            DB::raw('GROUP_CONCAT(settlements.settlement_no SEPARATOR ", ") as settlement_nos')
                        ])
                        ->groupBy('daily_vouchers.transaction_date','pump_operators.id');
                        
                        
                $shortage_excess = PumpOperatorPayment::leftjoin('pump_operators', 'pump_operator_payments.pump_operator_id', 'pump_operators.id')
                        ->leftjoin('settlements', 'pump_operator_payments.settlement_no', 'settlements.id')
                        ->where('pump_operator_payments.business_id', $business_id)
                        ->whereIn('payment_type',['shortage','excess'])
                        ->select([
                            DB::raw('"shortage_excess" as type'),
                            DB::raw('"" as collection_form_no'),
                            'pump_operator_payments.date_and_time as date',
                            'pump_operators.name as pump_operator_name',
                            DB::raw('SUM(pump_operator_payments.payment_amount) as total_amount'),
                            DB::raw('GROUP_CONCAT(settlements.settlement_no SEPARATOR ", ") as settlement_nos')
                        ])->groupBy(DB::raw('DATE(pump_operator_payments.date_and_time)'),'pump_operators.id');
                        
                $other = PumpOperatorPayment::leftjoin('pump_operators', 'pump_operator_payments.pump_operator_id', 'pump_operators.id')
                        ->leftjoin('settlements', 'pump_operator_payments.settlement_no', 'settlements.id')
                        ->where('pump_operator_payments.business_id', $business_id)
                        ->whereIn('payment_type',['other'])
                        ->select([
                            DB::raw('"other_payments" as type'),
                            DB::raw('"" as collection_form_no'),
                            'pump_operator_payments.date_and_time as date',
                            'pump_operators.name as pump_operator_name',
                            DB::raw('SUM(pump_operator_payments.payment_amount) as total_amount'),
                            DB::raw('GROUP_CONCAT(settlements.settlement_no SEPARATOR ", ") as settlement_nos')
                        ])->groupBy(DB::raw('DATE(pump_operator_payments.date_and_time)'),'pump_operators.id');
                        
                $cheque = PumpOperatorPayment::leftjoin('pump_operators', 'pump_operator_payments.pump_operator_id', 'pump_operators.id')
                        ->leftjoin('settlements', 'pump_operator_payments.settlement_no', 'settlements.id')
                        ->where('pump_operator_payments.business_id', $business_id)
                        ->whereIn('payment_type',['cheque'])
                        ->select([
                            DB::raw('"cheque" as type'),
                            DB::raw('"" as collection_form_no'),
                            'pump_operator_payments.date_and_time as date',
                            'pump_operators.name as pump_operator_name',
                            DB::raw('SUM(pump_operator_payments.payment_amount) as total_amount'),
                            DB::raw('GROUP_CONCAT(settlements.settlement_no SEPARATOR ", ") as settlement_nos')
                        ])->groupBy(DB::raw('DATE(pump_operator_payments.date_and_time)'),'pump_operators.id');
                        
                
                if (!empty(request()->pump_operator_id)) {
                    $daily_cash->where('daily_collections.pump_operator_id', request()->pump_operator_id);
                    $daily_cards->where('daily_cards.pump_operator_id', request()->pump_operator_id);
                    $shortage_excess->where('pump_operator_payments.pump_operator_id', request()->pump_operator_id);
                    
                    $other->where('pump_operator_payments.pump_operator_id', request()->pump_operator_id);
                    $cheque->where('pump_operator_payments.pump_operator_id', request()->pump_operator_id);
                    
                    $daily_credit_sales->where('daily_vouchers.operator_id', request()->pump_operator_id);
                }
                                   
                if (!empty(request()->start_date) && !empty(request()->end_date)) {
                    $daily_cash->whereDate('daily_collections.created_at', '>=', request()->start_date)->whereDate('daily_collections.created_at', '<=', request()->end_date);
                    
                    $daily_cards->whereDate('daily_cards.date', '>=', request()->start_date)->whereDate('daily_cards.date', '<=', request()->end_date);
                    $daily_credit_sales->whereDate('daily_vouchers.transaction_date', '>=', request()->start_date)->whereDate('daily_vouchers.transaction_date', '<=', request()->end_date);
                    
                    $shortage_excess->whereDate('pump_operator_payments.date_and_time', '>=', request()->start_date)->whereDate('pump_operator_payments.date_and_time', '<=', request()->end_date);
                    
                    $other->whereDate('pump_operator_payments.date_and_time', '>=', request()->start_date)->whereDate('pump_operator_payments.date_and_time', '<=', request()->end_date);
                    $cheque->whereDate('pump_operator_payments.date_and_time', '>=', request()->start_date)->whereDate('pump_operator_payments.date_and_time', '<=', request()->end_date);
                }
                
                switch(request()->daily_collection_type){
                    case 'daily_cash':
                        $query = $daily_cash->orderBy('date','DESC');
                        break;
                    case 'daily_voucher';
                        $query = $daily_credit_sales->orderBy('date','DESC');
                        break;
                    case 'daily_card':
                        $query = $daily_cards->orderBy('date','DESC');
                        break;
                    case 'shortage_excess';
                        $query = $shortage_excess->orderBy('date','DESC');
                        break;
                        
                    case 'other';
                        $query = $other->orderBy('date','DESC');
                        break;
                        
                    case 'cheque';
                        $query = $cheque->orderBy('date','DESC');
                        break;
                        
                    default:
                        $query = $daily_cash->unionAll($daily_cards)->unionAll($shortage_excess)->unionAll($daily_credit_sales)->unionAll($other)->unionAll($cheque)->orderBy('date','DESC');
                }
                
                
                $fuel_tanks = Datatables::of($query)
                    
                    ->editColumn('total_amount','{{@num_format($total_amount)}}')
                    ->editColumn('type', function ($row) {
                        return ucfirst(str_replace('_',' ',$row->type));
                    })
                    ->editColumn(
                        'date',
                        '{{@format_date($date)}}'
                    )
                    
                    ->removeColumn('id');


                return $fuel_tanks->rawColumns(['action','total_collection'])
                    ->make(true);
            
        }

    }
    
    public function indexShortageExcess()
    {
        $business_id = request()->session()->get('user.business_id');

        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module')) {
            abort(403, 'Unauthorized Access');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            if (request()->ajax()) {
                $query = PumpOperatorPayment::leftjoin('pump_operators', 'pump_operator_payments.pump_operator_id', 'pump_operators.id')
                    ->leftjoin('business_locations', 'pump_operators.location_id', 'business_locations.id')
                    ->leftjoin('users', 'pump_operator_payments.created_by', 'users.id')
                    ->leftjoin('settlements', 'pump_operator_payments.settlement_no', 'settlements.id')
                    ->where('pump_operator_payments.business_id', $business_id)
                    ->whereIn('payment_type',['shortage','excess'])
                    ->select([
                        'pump_operator_payments.*',
                        'business_locations.name as location_name',
                        'pump_operators.name as pump_operator_name',
                        'settlements.settlement_no as settlement_noo',
                        'settlements.transaction_date as settlement_date',
                        'settlements.status as settlement_status',
                        'users.username as user',
                    ]);
                
                if (!empty(request()->location_id)) {
                    $query->where('pump_operators.location_id', request()->location_id);
                }
                
                if (!empty(request()->settlement_id)) {
                    $query->where('settlements.id', request()->settlement_id);
                }
                
                if (!empty(request()->status)) {
                    if(request()->status == 'completed'){
                        $query->whereNotNull('settlements.settlement_no')->where('settlements.status',0);
                    }
                    
                    if(request()->status == 'pending'){
                        $query->where(function($q) {
                            $q->whereNull('settlements.settlement_no')
                              ->orWhere('settlements.status', 1);
                        });

                    }
                }
                
                if (!empty(request()->pump_operator)) {
                    $query->where('pump_operator_payments.pump_operator_id', request()->pump_operator);
                }
                if (!empty(request()->settlement_no)) {
                    $query->where('pump_operator_payments.settlement_no', request()->settlement_no);
                }                    
                if (!empty(request()->start_date) && !empty(request()->end_date)) {
                    $query->whereDate('pump_operator_payments.date_and_time', '>=', request()->start_date);
                    $query->whereDate('pump_operator_payments.date_and_time', '<=', request()->end_date);
                }
                $query->orderBy('pump_operator_payments.date_and_time', 'desc');
                $fuel_tanks = Datatables::of($query)
                    ->addColumn(
                        'action',
                        '
                        @if(empty($settlement_no))@can("daily_shortage.edit") &nbsp; <button data-href="{{action(\'\Modules\Petro\Http\Controllers\DailyCollectionController@editShortage\', [$id])}}" data-container=".pump_modal" class="btn btn-success btn-xs btn-modal edit_reference_button"><i class="fa fa-pencil" aria-hidden="true"></i> @lang("lang_v1.edit")</button> &nbsp; @endcan @endif
                        
                        @if(empty($is_used))@can("daily_collection.delete")<a class="btn btn-danger btn-xs delete_daily_collection" href="{{action(\'\Modules\Petro\Http\Controllers\DailyCollectionController@destroyShortageExcess\', [$id])}}"><i class="fa fa-trash" aria-hidden="true"></i> @lang("petro::lang.delete")</a>@endcan @endif'
                    )
                    /**
                     * @ChangedBy Afes
                     * @Date 25-05-2021
                     * @Task 12700
                     */
                     ->addColumn('shortage_amount','{{$payment_type == "shortage" ? @num_format($payment_amount) : ""}}')
                     
                     ->addColumn('excess_amount','{{$payment_type == "excess" ? @num_format(abs($payment_amount)) : ""}}')
                    
                    ->editColumn(
                        'date_and_time',
                        '{{@format_date($date_and_time)}}'
                    )
                    
                    ->addColumn('total_collection', function ($id) {
                        $total = DB::table('pump_operator_payments')
                                ->where('pump_operator_id', $id->pump_operator_id)
                                ->where('id', '<=', $id->id)
                                ->whereNull('settlement_no')
                                ->whereIn('payment_type',['shortage','excess'])
                                ->sum('payment_amount') ?? 0;
                            
                            return $this->productUtil->num_f($total);
                    })
                    
                    ->editColumn(
                        'settlement_date',
                        '{{!empty($settlement_date) ? @format_date($settlement_date) : ""}}'
                    )
                    
                    ->addColumn('status',function($row){
                        if(empty($row->settlement_noo) || $row->settlement_status == 1){
                            return 'Pending';
                        }else{
                            return 'Completed';
                        }
                    })


                    ->removeColumn('id');


                return $fuel_tanks->rawColumns(['action'])
                    ->make(true);
            }
        }
    }
    
    public function indexCheque()
    {
        $business_id = request()->session()->get('user.business_id');

        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module')) {
            abort(403, 'Unauthorized Access');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            if (request()->ajax()) {
                $query = PumpOperatorPayment::leftjoin('pump_operators', 'pump_operator_payments.pump_operator_id', 'pump_operators.id')
                    ->leftjoin('daily_cheque_payments','daily_cheque_payments.linked_payment_id','pump_operator_payments.id')
                    ->leftjoin('contacts','contacts.id','daily_cheque_payments.customer_id')
                    ->leftjoin('business_locations', 'pump_operators.location_id', 'business_locations.id')
                    ->leftjoin('users', 'pump_operator_payments.created_by', 'users.id')
                    ->leftjoin('settlements', 'pump_operator_payments.settlement_no', 'settlements.id')
                    ->where('pump_operator_payments.business_id', $business_id)
                    ->whereIn('payment_type',['cheque'])
                    ->select([
                        'pump_operator_payments.*',
                        'business_locations.name as location_name',
                        'pump_operators.name as pump_operator_name',
                        'settlements.settlement_no as settlement_noo',
                        'settlements.status as settlement_status',
                        'settlements.transaction_date as settlement_date',
                        'users.username as user',
                        'contacts.name as customer',
                        'daily_cheque_payments.cheque_date',
                       'daily_cheque_payments.bank_name',
                       'daily_cheque_payments.cheque_number',
                    ]);
                
                if (!empty(request()->location_id)) {
                    $query->where('pump_operators.location_id', request()->location_id);
                }
                
                if (!empty(request()->status)) {
                    if(request()->status == 'completed'){
                        $query->whereNotNull('settlements.settlement_no')->where('settlements.status',0);
                    }
                    
                    if(request()->status == 'pending'){
                        $query->where(function($q) {
                            $q->whereNull('settlements.settlement_no')
                              ->orWhere('settlements.status', 1);
                        });

                    }
                }
                
                if (!empty(request()->settlement_id)) {
                    $query->where('settlements.id', request()->settlement_id);
                }
                
                if (!empty(request()->pump_operator)) {
                    $query->where('pump_operator_payments.pump_operator_id', request()->pump_operator);
                }
                if (!empty(request()->settlement_no)) {
                    $query->where('pump_operator_payments.settlement_no', request()->settlement_no);
                }                    
                if (!empty(request()->start_date) && !empty(request()->end_date)) {
                    $query->whereDate('pump_operator_payments.date_and_time', '>=', request()->start_date);
                    $query->whereDate('pump_operator_payments.date_and_time', '<=', request()->end_date);
                }
                $query->orderBy('pump_operator_payments.date_and_time', 'desc');
                $fuel_tanks = Datatables::of($query)
                    ->addColumn(
                        'action',
                        '
                        
                        @if(empty($is_used))@can("daily_collection.delete")<a class="btn btn-danger btn-xs delete_daily_collection" href="{{action(\'\Modules\Petro\Http\Controllers\DailyCollectionController@destroyCheque\', [$id])}}"><i class="fa fa-trash" aria-hidden="true"></i> @lang("petro::lang.delete")</a>@endcan @endif'
                    )
                    /**
                     * @ChangedBy Afes
                     * @Date 25-05-2021
                     * @Task 12700
                     */
                     ->addColumn('payment_amount','{{ @num_format($payment_amount) }}')
                    
                    ->editColumn(
                        'date_and_time',
                        '{{@format_datetime($date_and_time)}}'
                    )
                    
                    ->editColumn(
                        'cheque_date',
                        '{{!empty($cheque_date) ? @format_date($cheque_date) : ""}}'
                    )
                    
                    ->addColumn('total_collection', function ($id) {
                        $total = DB::table('pump_operator_payments')
                                ->where('pump_operator_id', $id->pump_operator_id)
                                ->where('id', '<=', $id->id)
                                ->whereNull('settlement_no')
                                ->whereIn('payment_type',['cheque'])
                                ->sum('payment_amount') ?? 0;
                            
                            return $this->productUtil->num_f($total);
                    })
                    
                    ->editColumn(
                        'settlement_date',
                        '{{!empty($settlement_date) ? @format_date($settlement_date) : ""}}'
                    )
                    
                    ->addColumn('status',function($row){
                        if(empty($row->settlement_noo) || $row->settlement_status == 1){
                            return 'Pending';
                        }else{
                            return 'Completed';
                        }
                    })


                    ->removeColumn('id');


                return $fuel_tanks->rawColumns(['action'])
                    ->make(true);
            }
        }
    }
    
    public function indexOther()
    {
        $business_id = request()->session()->get('user.business_id');

        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module')) {
            abort(403, 'Unauthorized Access');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            if (request()->ajax()) {
                $query = PumpOperatorPayment::leftjoin('pump_operators', 'pump_operator_payments.pump_operator_id', 'pump_operators.id')
                    ->leftjoin('business_locations', 'pump_operators.location_id', 'business_locations.id')
                    ->leftjoin('users', 'pump_operator_payments.created_by', 'users.id')
                    ->leftjoin('settlements', 'pump_operator_payments.settlement_no', 'settlements.id')
                    ->where('pump_operator_payments.business_id', $business_id)
                    ->whereIn('payment_type',['other'])
                    ->select([
                        'pump_operator_payments.*',
                        'business_locations.name as location_name',
                        'pump_operators.name as pump_operator_name',
                        'settlements.settlement_no as settlement_noo',
                        'settlements.status as settlement_status',
                        'settlements.transaction_date as settlement_date',
                        'users.username as user',
                    ]);
                
                if (!empty(request()->location_id)) {
                    $query->where('pump_operators.location_id', request()->location_id);
                }
                
                if (!empty(request()->status)) {
                    if(request()->status == 'completed'){
                        $query->whereNotNull('settlements.settlement_no')->where('settlements.status',0);
                    }
                    
                    if(request()->status == 'pending'){
                        $query->where(function($q) {
                            $q->whereNull('settlements.settlement_no')
                              ->orWhere('settlements.status', 1);
                        });

                    }
                }
                
                if (!empty(request()->settlement_id)) {
                    $query->where('settlements.id', request()->settlement_id);
                }
                
                if (!empty(request()->pump_operator)) {
                    $query->where('pump_operator_payments.pump_operator_id', request()->pump_operator);
                }
                if (!empty(request()->settlement_no)) {
                    $query->where('pump_operator_payments.settlement_no', request()->settlement_no);
                }                    
                if (!empty(request()->start_date) && !empty(request()->end_date)) {
                    $query->whereDate('pump_operator_payments.date_and_time', '>=', request()->start_date);
                    $query->whereDate('pump_operator_payments.date_and_time', '<=', request()->end_date);
                }
                $query->orderBy('pump_operator_payments.date_and_time', 'desc');
                $fuel_tanks = Datatables::of($query)
                    ->addColumn(
                        'action',
                        '
                        @if(empty($is_used))@can("daily_collection.delete")<a class="btn btn-danger btn-xs delete_daily_collection" href="{{action(\'\Modules\Petro\Http\Controllers\DailyCollectionController@destroyOther\', [$id])}}"><i class="fa fa-trash" aria-hidden="true"></i> @lang("petro::lang.delete")</a>@endcan @endif'
                    )
                    /**
                     * @ChangedBy Afes
                     * @Date 25-05-2021
                     * @Task 12700
                     */
                     ->addColumn('payment_amount','{{ @num_format($payment_amount) }}')
                    
                    ->editColumn(
                        'date_and_time',
                        '{{@format_datetime($date_and_time)}}'
                    )
                    
                    ->addColumn('total_collection', function ($id) {
                        $total = DB::table('pump_operator_payments')
                                ->where('pump_operator_id', $id->pump_operator_id)
                                ->where('id', '<=', $id->id)
                                ->whereNull('settlement_no')
                                ->whereIn('payment_type',['other'])
                                ->sum('payment_amount') ?? 0;
                            
                            return $this->productUtil->num_f($total);
                    })
                    
                    ->editColumn(
                        'settlement_date',
                        '{{!empty($settlement_date) ? @format_date($settlement_date) : ""}}'
                    )
                    
                    ->addColumn('status',function($row){
                        if(empty($row->settlement_noo) || $row->settlement_status == 1){
                            return 'Pending';
                        }else{
                            return 'Completed';
                        }
                    })


                    ->removeColumn('id');


                return $fuel_tanks->rawColumns(['action'])
                    ->make(true);
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');        
        $locations = BusinessLocation::forDropdown($business_id);
        $default_location = current(array_keys($locations->toArray()));
        
        $open_shifts = PetroShift::where('business_id',$business_id)->where('status','0')->pluck('pump_operator_id')->toArray();
        $pump_operators = PumpOperator::where('business_id', $business_id)->whereNotIn('id',$open_shifts)->pluck('name', 'id');

        $collection_form_no = (int) (DailyCollection::where('business_id', $business_id)->count()) + 1;


        return view('petro::daily_collection.create')->with(compact('locations', 'pump_operators', 'collection_form_no','default_location'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'collection_form_no' => 'required',
            'pump_operator_id' => 'required',
            'balance_collection' => 'required',
            'current_amount' => 'required',
            'location_id' => 'required'
        ]);

        if ($validator->fails()) {
            $output = [
                'succcess' => 0,
                'msg' => $validator->errors()->all()[0]
            ];

            return redirect()->back()->with('status', $output);
        }
        $business_id = request()->session()->get('business.id');
        
        
        $has_reviewed = $this->transactionUtil->hasReviewed($request->input('transaction_date'));
        
        if(!empty($has_reviewed)){
            $output              = [
                'success' => 0,
                'msg'     =>__('lang_v1.review_first'),
            ];
            
            return redirect()->back()->with(['status' => $output]);
        }
        
        
        $reviewed = $this->transactionUtil->get_review($request->input('transaction_date'),$request->input('transaction_date'));
            
            if(!empty($reviewed)){
                $output = [
                    'success' => 0,
                    'msg'     =>"You can't add a collection for an already reviewed date",
                ];
                
                return redirect()->back()->with(['status' => $output]);
            }
        
        try {
            $data = array(
                'business_id' => $business_id,
                'collection_form_no' => $request->collection_form_no,
                'pump_operator_id' => $request->pump_operator_id,
                'location_id' => $request->location_id,
                'balance_collection' => $request->balance_collection,
                'current_amount' => $request->current_amount,
                'created_by' =>  Auth::user()->id
            );

            DailyCollection::create($data);
            
            $pump_operator = PumpOperator::where('id', $request->pump_operator_id)->first();
            $balance_collection = DailyCollection::where('business_id', $business_id)->where('pump_operator_id', $request->pump_operator_id)->sum('current_amount');
            
            $total_amount = DailyCollection::where('business_id', $business_id)->where('pump_operator_id', $request->pump_operator_id)->whereNull('settlement_id')->sum('current_amount');
            
            $settlement_collection = DailyCollection::where('business_id', $business_id)->where('pump_operator_id', $request->pump_operator_id)->sum('balance_collection');
            $cum_amount = $balance_collection - $settlement_collection;
                            
            
            $sms_data = array(
                'date' => $request->input('transaction_date'),
                'time' => date('H:i'),
                'pump_operator' => $pump_operator->name,
                'amount' => $this->transactionUtil->num_f($request->current_amount),
                'pumper_cummulative_amount' => $this->transactionUtil->num_f($cum_amount),
                'total_amount' => $this->transactionUtil->num_f($total_amount),
            );
            
            $this->notificationUtil->sendPetroNotification('daily_collection',$sms_data);

            $output = [
                'success' => 1,
                'msg' => __('petro::lang.daily_collection_add_success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');        
        $locations = BusinessLocation::forDropdown($business_id);
        $data = DailyCollection::findOrFail($id);
        
        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');


        return view('petro::daily_collection.edit')->with(compact('locations', 'pump_operators','data'));
    }
    
    public function editShortage($id)
    {
        $business_id = request()->session()->get('user.business_id');        
        $data = PumpOperatorPayment::findOrFail($id);
        
        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');


        return view('petro::daily_collection.edit_shortage')->with(compact('pump_operators','data'));
    }
    

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'collection_form_no' => 'required',
            'pump_operator_id' => 'required',
            'balance_collection' => 'required',
            'current_amount' => 'required',
            'location_id' => 'required'
        ]);

        if ($validator->fails()) {
            $output = [
                'succcess' => 0,
                'msg' => $validator->errors()->all()[0]
            ];

            return redirect()->back()->with('status', $output);
        }
        $business_id = request()->session()->get('business.id');
        
        
        $has_reviewed = $this->transactionUtil->hasReviewed($request->input('transaction_date'));
        
        if(!empty($has_reviewed)){
            $output              = [
                'success' => 0,
                'msg'     =>__('lang_v1.review_first'),
            ];
            
            return redirect()->back()->with(['status' => $output]);
        }
        
        
        $reviewed = $this->transactionUtil->get_review($request->input('transaction_date'),$request->input('transaction_date'));
            
            if(!empty($reviewed)){
                $output = [
                    'success' => 0,
                    'msg'     =>"You can't add a collection for an already reviewed date",
                ];
                
                return redirect()->back()->with(['status' => $output]);
            }
        
        try {
            $data = array(
                'business_id' => $business_id,
                'collection_form_no' => $request->collection_form_no,
                'pump_operator_id' => $request->pump_operator_id,
                'location_id' => $request->location_id,
                'balance_collection' => $request->balance_collection,
                'current_amount' => $request->current_amount,
                'created_by' =>  Auth::user()->id
            );

            DailyCollection::where('id',$id)->update($data);
            
            $output = [
                'success' => 1,
                'msg' => __('petro::lang.daily_collection_add_success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }
    
    public function updateShortage(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'pump_operator_id' => 'required',
            'payment_amount' => 'required'
        ]);

        if ($validator->fails()) {
            $output = [
                'succcess' => 0,
                'msg' => $validator->errors()->all()[0]
            ];

            return redirect()->back()->with('status', $output);
        }
        $business_id = request()->session()->get('business.id');
        
        
        $has_reviewed = $this->transactionUtil->hasReviewed($request->input('transaction_date'));
        
        if(!empty($has_reviewed)){
            $output              = [
                'success' => 0,
                'msg'     =>__('lang_v1.review_first'),
            ];
            
            return redirect()->back()->with(['status' => $output]);
        }
        
        
        $reviewed = $this->transactionUtil->get_review($request->input('transaction_date'),$request->input('transaction_date'));
            
            if(!empty($reviewed)){
                $output = [
                    'success' => 0,
                    'msg'     =>"You can't add a collection for an already reviewed date",
                ];
                
                return redirect()->back()->with(['status' => $output]);
            }
        
        try {
            $data = array(
                'pump_operator_id' => $request->pump_operator_id,
                'payment_amount' => $request->payment_amount
            );

            PumpOperatorPayment::where('id',$id)->update($data);
            
            $output = [
                'success' => 1,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        try {
            DailyCollection::where('id', $id)->delete();
            $output = [
                'success' => true,
                'msg' => __('petro::lang.daily_collection_delete_success')
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
    
    public function destroyShortageExcess($id)
    {
        try {
            PumpOperatorPayment::where('id', $id)->delete();
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

        return $output;
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function print($pump_operator_id)
    {
        $daily_collection = DailyCollection::findOrFail($pump_operator_id);
        $pump_operator = PumpOperator::findOrFail($daily_collection->pump_operator_id);
        $business_details = Business::where('id', $pump_operator->business_id)->first();

        return view('petro::daily_collection.partials.print')->with(compact('pump_operator', 'business_details', 'daily_collection'));
    }

    /**
     * get Balance Collection for pump operator
     * @return Response
     */
    public function getBalanceCollection($pump_operator_id)
    {
        $business_id = request()->session()->get('business.id');

        $balance_collection = DB::table('daily_collections')
                                ->where('pump_operator_id', $pump_operator_id)
                                ->whereNull('settlement_id')
                                ->sum('current_amount') ?? 0;

        return ['balance_collection' => $balance_collection];
    }
}
