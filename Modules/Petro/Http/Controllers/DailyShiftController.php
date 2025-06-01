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
use Modules\Petro\Entities\PetroDailyShift;
use Modules\Petro\Entities\PumpOperatorAssignment;

class DailyShiftController extends Controller
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
                    ->leftJoin('pump_operator_assignments', 'pump_operator_assignments.settlement_id', '=', 'settlements.id')
                    ->where('daily_cards.business_id', $business_id)
                    ->select([
                        'daily_cards.*',
                        'accounts.name as type_name',
                        'pump_operators.name as pump_operator_name',
                        'contacts.name as customer_name',
                        'business_locations.name as location_name',
                        'settlements.settlement_no as settlement_nos',
                        'settlements.status as settlement_status',
                        'pump_operator_assignments.shift_number'
                    ])
                    ->groupBy('daily_cards.id');
                
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
                // $query->orderBy(DB::raw('CAST(daily_cards.collection_no AS UNSIGNED)'), 'desc');
                $query->orderBy('daily_cards.date', 'desc');
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
                    ->editColumn('shift_number',function($row){
                        if(empty($row->shift_number)){
                            $assigned_pumps = PumpOperatorAssignment::where('pump_operator_id', $row->pump_operator_id)
                            ->select('shift_number')
                            ->orderBy('id','DESC')
                            ->first();
                            return optional($assigned_pumps)->shift_number;
                        }else{
                            return $row->shift_number;
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
        
        $data = $request->validate([
            'business_id' => 'required|integer',
            'pump_operator_pending' => 'nullable|string',
            'pump_operator_assigned' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'required',
            'user' => 'required|integer',
            'open_shift' => 'nullable|integer' // Add open_shift to validation
        ]);
        $data['status'] =1;
        //dd($data);
        // Generate shift number if creating new record
        if(is_null($request->open_shift) || empty($request->open_shift)){
            $data['open_shift']=$this->createIfNotExist($request);
        }

        // Update existing record
        $shift = PetroDailyShift::where('id', $data['open_shift'])
            ->where('business_id', $data['business_id'])
            ->firstOrFail();
            
        $shift->update($data);
        $action = 'updated';
      
    
        return response()->json([
            'success' => true,
            'action' => $action,
            'shift_id' => $shift->id,
            'shift_no' => $shift->shift_no,
            'pump_operators_assigned' => explode(',', $shift->pump_operator_assigned),
            'pump_operators_pending' => explode(',', $shift->pump_operator_pending)
        ]);
    }

    public function createIfNotExist(Request $request){
        $business_id = $request->input('business_id');
        $newShift = PetroDailyShift::create([
            'business_id' => $business_id,
            'shift_no' => $this->generateShiftNumber(),
            'date' => now()->format('Y-m-d'),
            'time' => now()->format('H:i:s'),
            'user' => auth()->id(),
            'status' => 1, // Assuming 1 means "open"
            'pump_operators_assigned' => explode(',', $request->pump_operator_assigned),
            'pump_operators_pending' => explode(',', $request->pump_operator_pending)
        ]);
        return $newShift->id;
    }

    public function shiftExists($id)
    {
        return PetroDailyShift::where('id', $id)->exists();
    }
// In your DailyShiftController.php
public function OpenShift(Request $request)
{
    $business_id = $request->input('business_id');
    $allOperators = PumpOperator::where('business_id', $business_id)
    ->where('status', 1)
    ->pluck('id')
    ->toArray();

    $allOperatorswithNames = PumpOperator::where('business_id', $business_id)
    ->where('status', 1)
    ->pluck('id','name')
    ->toArray();


    $assignedOperators = $request->input('dv_pump_operator_right', []);
    $pendingOperators = array_diff($allOperators, $assignedOperators);
    // Create new shift record
    $newShift = PetroDailyShift::create([
        'business_id' => $business_id,
        'shift_no' => $this->generateShiftNumber(),
        'date' => now()->format('Y-m-d'),
        'time' => now()->format('H:i:s'),
        'user' => auth()->id(),
        'status' => 1, // Assuming 1 means "open"
        'pump_operator_pending' => implode(',', $pendingOperators),
        'pump_operator_assigned' => implode(',', $assignedOperators)
    ]);
    
    return response()->json([
        'success' => true,
        'shift_id' => $newShift->id,
        'shift_no' => $newShift->shift_no,
        'pump_operators_assigned' => $assignedOperators,
        'pump_operators_pending' => $pendingOperators,
        'operators_with_name' => $allOperatorswithNames
    ]);
}
public function closeShift(Request $request)
{
    $data = $request->validate([
        'business_id' => 'required|integer',
        'open_shift' => 'required|integer', // Changed from nullable to required
        'pump_operator_pending' => 'nullable|string',
        'pump_operator_assigned' => 'nullable|string',
        'date' => 'required|date',
        'time' => 'required',
        'user' => 'required|integer'
    ]);
    
    $data['status'] = 0; // Set status to closed

    // if(is_null($request->open_shift) || empty($request->open_shift)){
    //     $data['open_shift']=$this->createIfNotExist($request);
    // }

    try {
        $shift = PetroDailyShift::where('id', $data['open_shift'])
            ->where('business_id', $data['business_id'])
            ->where('status', 1) // Only close if currently open
            ->firstOrFail();
            
        $shift->update($data);

        // Convert comma-separated string to array of IDs
        $pending_ids = array_filter(explode(',', $shift->pump_operator_pending));
        $assigned_ids = array_filter(explode(',', $shift->pump_operators_assigned));

        // Fetch operator names using the IDs
        $pending_operators = PumpOperator::whereIn('id', $pending_ids)
            ->pluck('name', 'id')
            ->toArray();

        $assigned_operators = PumpOperator::whereIn('id', $assigned_ids)
            ->pluck('name', 'id')
            ->toArray();
            
        // $newShift=$this->createIfNotExist($request);
        // $newShift = PetroDailyShift::where('id', $newShift)
        //     ->where('business_id', $data['business_id'])
        //     ->where('status', 1) // Only close if currently open
        //     ->firstOrFail();
            
            return response()->json([
            'success' => true,
            'shift_id' => $shift->id,
            'shift_no' => $shift->shift_no,
            'status' =>  $data['status'],
            'old_pending' => $pending_operators,
            'old_assigned' => $assigned_operators
        ]);
        
        // return response()->json([
        //     'success' => true,
        //     'shift_id' => $newShift->id,
        //     'shift_no' => $newShift->shift_no,
        //     'status' => $newShift->status,
        //     'old_pending' => $pending_operators,
        //     'old_assigned' => $assigned_operators
        // ]);
        
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Open shift not found or already closed'
        ], 404);
    }
}
public function shiftclose(Request $request)
{
    $request->validate([
        'shift_id' => 'required|string',
    ]);

    $business_id = auth()->user()->business_id;

    $shift = PetroDailyShift::where('shift_no', $request->shift_id)
        ->where('business_id', $business_id)
        ->where('status', 1) // Only close if currently open
        ->first();

    if (!$shift) {
        return response()->json(['message' => 'Shift not found or already closed.'], 404);
    }

    $shift->update([
        'status' => 0,
        'closed_at' => now(),
        'closed_by' => auth()->user()->id
    ]);

    return response()->json(['message' => 'Shift closed successfully.']);
}
public function getOperators(Request $request)
{
    $business_id = $request->input('business_id', auth()->user()->business_id);
    
    $operators = PumpOperator::where('business_id', $business_id)
        ->where('status', 1)
        ->pluck('name', 'id');
    
    return response()->json([
        'pump_operators' => $operators
    ]);
}

private function generateShiftNumber()
{
    $lastShift = PetroDailyShift::orderBy('id', 'desc')->first();
    return 'DSN-' . str_pad($lastShift ? (int)$lastShift->id + 1 : 1, 3, '0', STR_PAD_LEFT);
}
// private function generateShiftNumber()
//     {
//         $lastNumber = PetroDailyShift::select('shift_no')
//             ->orderBy('shift_no', 'desc')
//             ->value('shift_no');

//         if (!$lastNumber) {
//             return 'DSN-001';
//         }

//         $numericPart = (int) substr($lastNumber, 4); // Extract number after 'DSN-'
//         $nextNumber = $numericPart + 1;

//         return 'DSN-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
//     }

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
      

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
     

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
      
}
