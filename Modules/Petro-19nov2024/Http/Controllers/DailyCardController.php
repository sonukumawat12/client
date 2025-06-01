<?php

namespace Modules\Petro\Http\Controllers;

use App\Business;
use App\Contact;
use App\Account;
use App\AccountGroup;
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
use App\Utils\TransactionUtil;
use App\Utils\BusinessUtil;
use Illuminate\Support\Facades\Auth;
use Modules\Petro\Entities\DailyCard;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Modules\Petro\Entities\PetroShift;

class DailyCardController extends Controller
{

    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $moduleUtil;
    protected $transactionUtil;
    protected $commonUtil;

    private $barcode_types;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
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
                $query = DailyCard::leftjoin('pump_operators', 'daily_cards.pump_operator_id', 'pump_operators.id')
                    ->leftjoin('business_locations','business_locations.id','pump_operators.location_id')
                    ->leftjoin('contacts', 'daily_cards.customer_id', 'contacts.id')
                    ->leftjoin('settlements','settlements.id','daily_cards.settlement_no')
                    ->leftjoin('accounts', 'daily_cards.card_type', 'accounts.id')
                    ->where('daily_cards.business_id', $business_id)
                    ->select([
                        'daily_cards.*',
                        'accounts.name as type_name',
                        'pump_operators.name as pump_operator_name',
                        'contacts.name as customer_name',
                        'business_locations.name as location_name',
                        'settlements.settlement_no as settlement_nos',
                        'settlements.status as settlement_status'
                    ]);
                
                if (!empty(request()->pump_operator_id)) {
                    $query->where('daily_cards.pump_operator_id', request()->pump_operator_id);
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
                
                if (!empty(request()->location_id)) {
                    $query->where('pump_operators.location_id', request()->location_id);
                }
                
                if (!empty(request()->customer_id)) {
                    $query->where('daily_cards.customer_id', request()->customer_id);
                }
                if (!empty(request()->card_type)) {
                    $query->where('daily_cards.card_type', request()->card_type);
                }
                
                if (!empty(request()->slip_no)) {
                    $query->where('daily_cards.slip_no', request()->slip_no);
                }
                if (!empty(request()->card_number)) {
                    $query->where('daily_cards.card_number', request()->card_number);
                }
                
                if (!empty(request()->start_date) && !empty(request()->end_date)) {
                    $query->whereDate('daily_cards.date', '>=', request()->start_date);
                    $query->whereDate('daily_cards.date', '<=', request()->end_date);
                }
                $query->orderBy('daily_cards.collection_no', 'desc');
                $fuel_tanks = Datatables::of($query)
                    ->addColumn(
                        'action',
                        '
                        @if(empty($settlement_no))@can("daily_card.edit") &nbsp; <button data-href="{{action(\'\Modules\Petro\Http\Controllers\DailyCardController@edit\', [$id])}}" data-container=".pump_modal" class="btn btn-success btn-xs btn-modal edit_reference_button"><i class="fa fa-pencil" aria-hidden="true"></i> @lang("lang_v1.edit")</button> &nbsp; @endcan @endif
                        @if($used_status == 0) <a class="btn btn-danger btn-xs delete_daily_card" data-href="{{action(\'\Modules\Petro\Http\Controllers\DailyCardController@destroy\', [$id])}}"><i class="fa fa-trash" aria-hidden="true"></i> @lang("petro::lang.delete")</a>@endif'
                    )
                    ->addColumn('total_collection', function ($id) {
                        $total = DB::table('daily_cards')
                                ->where('pump_operator_id', $id->pump_operator_id)
                                ->where('id', '<=', $id->id)
                                ->whereNull('settlement_no')
                                ->sum('amount') ?? 0;
                            
                            return $this->productUtil->num_f($total);
                    })
                
                    /**
                     * @ChangedBy Afes
                     * @Date 25-05-2021 
                     * @Task 12700
                     */
                    ->editColumn('amount', '{{@num_format($amount)}}')
                    ->addColumn('status',function($row){
                        if(empty($row->settlement_nos) || $row->settlement_status == 1){
                            return 'Pending';
                        }else{
                            return 'Completed';
                        }
                    })
                    ->editColumn('date', '{{@format_date($date)}}');

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

        $collection_form_no = (int) (DailyCard::where('business_id', $business_id)->count()) + 1;
        
        $customers = Contact::customersDropdown($business_id, false, true, 'customer');
        $card_types = [];
        $card_group = AccountGroup::where('business_id', $business_id)->where('name', 'Card')->first();
        if (!empty($card_group)) {
            $card_types = Account::where('business_id', $business_id)->where('asset_type', $card_group->id)->where(DB::raw("REPLACE(`name`, '  ', ' ')"), '!=', 'Cards (Credit Debit) Account')->pluck('name', 'id');
        }


        return view('petro::daily_collection.partials.create_daily_cards')->with(compact('card_types','customers','locations', 'pump_operators', 'collection_form_no','default_location'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'collection_no' => 'required',
            'pump_operator_id' => 'required',
        ]);
        
        $date = $this->productUtil->uf_date($request->date);

        if ($validator->fails()) {
            $output = [
                'success' => 0,
                'msg' => $validator->errors()->all()[0]
            ];

            return redirect()->back()->with('status', $output);
        }
        $business_id = request()->session()->get('business.id');
        
        $dynamic_customer_id = $request->dynamic_customer_id;
        $dynamic_card_type = $request->dynamic_card_type;
        $dynamic_card_number = $request->dynamic_card_number;
        $dynamic_slip_no = $request->dynamic_slip_no;
        $dynamic_card_note = $request->dynamic_card_note;
        $dynamic_amount = $request->dynamic_amount;
        
        
        foreach($dynamic_slip_no as $slip_no){
            $slip_no = trim(str_replace(' ', '', $slip_no));
            $existingRecord = DailyCard::where('slip_no', $slip_no)
                                ->whereDate('date', $date)
                                ->exists();
            
            if (!empty($slip_no) && $existingRecord) {
               $output = [
                    'success' => false,
                    'msg' => __('messages.duplicate_slip')
                ];
                
                return redirect()->back()->with(['status' => $output]);
            }
        }
        
        $has_reviewed = $this->transactionUtil->hasReviewed($request->input('date'));
        
        
        if(!empty($has_reviewed)){
            $output              = [
                'success' => 0,
                'msg'     =>__('lang_v1.review_first'),
            ];
            
            return redirect()->back()->with(['status' => $output]);
        }
        
        
        $reviewed = $this->transactionUtil->get_review($request->input('date'),$request->input('date'));
            
            if(!empty($reviewed)){
                $output = [
                    'success' => 0,
                    'msg'     =>"You can't add a collection for an already reviewed date",
                ];
                
                return redirect()->back()->with(['status' => $output]);
            }
        
        DB::beginTransaction();
        
        try {
            
            foreach($dynamic_slip_no as $key => $slip_no){
                $data = array(
                    'location_id' => $request->location_id,
                    'collection_no' => $request->collection_no,
                    'business_id' => $business_id,
                    'amount' => $dynamic_amount[$key],
                    'card_type' => $dynamic_card_type[$key],
                    'card_number' => $dynamic_card_number[$key],
                    'customer_id' => $dynamic_customer_id[$key],
                    'note' => $dynamic_card_note[$key],
                    'slip_no' => $slip_no,
                    'date' => $date,
                    'pump_operator_id' => $request->pump_operator_id
                );
    
                DailyCard::create($data);
            }
            
            

            $output = [
                'success' => 1,
                'msg' => __('petro::lang.daily_card_add_success')
            ];
            
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
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
        $default_location = current(array_keys($locations->toArray()));
        $data = DailyCard::findOrFail($id);
        
        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');

        $collection_form_no = (int) (DailyCard::where('business_id', $business_id)->count()) + 1;
        
        $customers = Contact::customersDropdown($business_id, false, true, 'customer');
        $card_types = [];
        $card_group = AccountGroup::where('business_id', $business_id)->where('name', 'Card')->first();
        if (!empty($card_group)) {
            $card_types = Account::where('business_id', $business_id)->where('asset_type', $card_group->id)->where(DB::raw("REPLACE(`name`, '  ', ' ')"), '!=', 'Cards (Credit Debit) Account')->pluck('name', 'id');
        }


        return view('petro::daily_collection.partials.edit_daily_cards')->with(compact('data','card_types','customers','locations', 'pump_operators', 'collection_form_no','default_location'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'collection_no' => 'required',
            'pump_operator_id' => 'required',
            'amount' => 'required'
        ]);
        
        $date = $this->productUtil->uf_date($request->date);

        if ($validator->fails()) {
            $output = [
                'success' => 0,
                'msg' => $validator->errors()->all()[0]
            ];

            return redirect()->back()->with('status', $output);
        }
        $business_id = request()->session()->get('business.id');
        
        $has_reviewed = $this->transactionUtil->hasReviewed($request->input('date'));
        
        
        if(!empty($has_reviewed)){
            $output              = [
                'success' => 0,
                'msg'     =>__('lang_v1.review_first'),
            ];
            
            return redirect()->back()->with(['status' => $output]);
        }
        
        
        $reviewed = $this->transactionUtil->get_review($request->input('date'),$request->input('date'));
            
            if(!empty($reviewed)){
                $output = [
                    'success' => 0,
                    'msg'     =>"You can't add a collection for an already reviewed date",
                ];
                
                return redirect()->back()->with(['status' => $output]);
            }
        
        try {
            
            $slip_no = trim(str_replace(' ', '', $request->slip_no));
            
            
            $data = array(
                'collection_no' => $request->collection_no,
                'business_id' => $business_id,
                'amount' => $request->amount,
                'card_type' => $request->card_type,
                'card_number' => $request->card_number,
                'customer_id' => $request->customer_id,
                'note' => $request->note,
                'slip_no' => $slip_no,
                'date' => $date,
                'pump_operator_id' => $request->pump_operator_id
            );

            DailyCard::where('id',$id)->update($data);

            $output = [
                'success' => 1,
                'msg' => __('petro::lang.daily_card_add_success')
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
            DailyCard::where('id', $id)->delete();
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

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function print($pump_operator_id)
    {
        $daily_collection = DailyCard::findOrFail($pump_operator_id);
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

        $balance_collection = DailyCard::where('business_id', $business_id)->where('pump_operator_id', $pump_operator_id)->sum('current_amount');
        $settlement_collection = DailyCard::where('business_id', $business_id)->where('pump_operator_id', $pump_operator_id)->sum('balance_collection');

        return ['balance_collection' => $balance_collection - $settlement_collection];
    }
}
