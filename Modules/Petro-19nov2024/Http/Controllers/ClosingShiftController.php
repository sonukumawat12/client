<?php

namespace Modules\Petro\Http\Controllers;

use App\Business;
use App\BusinessLocation;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Petro\Entities\Pump;
use Modules\Petro\Entities\PumperDayEntry;
use Modules\Petro\Entities\PetroShift;
use Modules\Petro\Entities\PumpOperator;
use Modules\Petro\Entities\PumpOperatorAssignment;
use Modules\Petro\Entities\PumpOperatorPayment;
use Yajra\DataTables\Facades\DataTables;

class ClosingShiftController extends Controller
{

    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $moduleUtil;
    protected $transactionUtil;
    protected $commonUtil;

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
     * @return Renderable
     */
    public function index()
    {
        $business_id =  Auth::user()->business_id;

        $only_pumper = request()->only_pumper;
        $pump_operator_id = Auth::user()->pump_operator_id;
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module')) {
            abort(403, 'Unauthorized Access');
        }

        if (request()->ajax()) {
            
            $already_added_shortage = [];
            $already_added_excess = [];
            
            
            $business_details = Business::find($business_id);

            $query = PumperDayEntry::leftjoin('pump_operators', 'pumper_day_entries.pump_operator_id', 'pump_operators.id')
                ->leftjoin('pump_operator_assignments','pump_operator_assignments.id','pumper_day_entries.pumper_assignment_id')
                ->leftjoin('business_locations','business_locations.id','pump_operators.location_id')
                ->where('pumper_day_entries.business_id', $business_id)
                ->select('pump_operators.name', 'pumper_day_entries.*','business_locations.name as location_name');

            if ($only_pumper) {
                $query->where('pumper_day_entries.pump_operator_id', $pump_operator_id);
            }
            if (!empty(request()->shift_id)) {
                $query->where('pump_operator_assignments.shift_id', request()->shift_id);
            }
            if (!empty(request()->pump_operator_id)) {
                $query->where('pump_operator_id', request()->pump_operator_id);
            }
            if (!empty(request()->pump_id)) {
                $query->where('pump_id', request()->pump_id);
            }
            if (!empty(request()->payment_method)) {
                // $query->where('pump_operator_id', request()->payment_method);
            }
            if (!empty(request()->difference)) {
                // $query->where('pump_operator_id', request()->difference);
            }
            $fuel_tanks = DataTables::of($query)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                        if (empty(auth()->user()->pump_operator_id)) {
                            if (!empty($row->settlement_no)) {
                                $disabled = 'disabled';
                                $html .= ' <li><a class="btn" disabled><i class="fa fa-pencil-square-o"></i> ' . __("messages.edit") . '</a></li>';
                            } else {
                                $html .= ' <li><a data-href="' . action('\Modules\Petro\Http\Controllers\PumperDayEntryController@edit', [$row->id]) . '" class="btn btn-modal edit_day_entry_button" data-container=".view_modal"><i class="fa fa-pencil-square-o"></i> ' . __("messages.edit") . '</a></li>';
                            }
                        }
                        if (empty($row->settlement_no)) {
                            $html .= ' <li><a data-href="' . action('\Modules\Petro\Http\Controllers\PumperDayEntryController@postAddSettlementNo', [$row->id]) . '" class="btn btn-modal edit_day_entry_button" data-container=".view_modal"><i class="fa fa-plus"></i> ' . __("petro::lang.add_settlement_no") . '</a></li>';
                        }

                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->addColumn(
                    'date',
                    '{{@format_date($date)}}'
                )
                ->editColumn(
                    'time',
                    '{{@format_time($time)}}'
                )
                ->editColumn(
                    'settlement_no',
                    function ($row) use ($business_details) {
                        if (!empty($row->settlement_no)) {
                            return  '<a data-href="' . action('\Modules\Petro\Http\Controllers\PumperDayEntryController@viewAddSettlementNo', [$row->id]) . '" class="btn btn-modal edit_day_entry_button" data-container=".view_modal">' . $row->settlement_no . '</a>';
                        }
                    }
                )
                ->editColumn(
                    'sold_ltr',
                    function ($row) use ($business_details) {

                        return  '<span class="display_currency sold_ltr" data-orig-value="' . $row->sold_ltr . '" data-currency_symbol = false>' . $this->productUtil->num_f($row->sold_ltr, false, $business_details, true) . '</span>';
                    }
                )
                ->editColumn('testing_ltr', '{{@format_quantity($testing_ltr)}}')
                ->addColumn('credit_sale', function ($row) use ($business_details) {
                    return  '<span class="display_currency credit_sale" data-orig-value="' . $row->credit_sale . '" data-currency_symbol = false>' . $this->productUtil->num_f($row->credit_sale, false, $business_details, true) . '</span>';
                })
                ->editColumn(
                    'amount',
                    function ($row) use ($business_details) {

                        return  '<span class="display_currency sold_amount" data-orig-value="' . $row->amount . '" data-currency_symbol = false>' . $this->productUtil->num_f($row->amount, false, $business_details, true) . '</span>';
                    }
                )
                ->addColumn('short_amount', function ($row) use ($business_details,&$already_added_excess,&$already_added_shortage) {
                    
                    $payments = PumpOperatorPayment::whereDate('date_and_time', date('Y-m-d'))
                                ->where('pump_operator_id', $row->pump_operator_id)
                                ->select(
                                    DB::raw('SUM(IF(payment_type="shortage", payment_amount, 0)) as short_amount'),
                                    DB::raw('SUM(IF(payment_type="excess", payment_amount, 0)) as excess_amount')
                                )->first();
                    
                    if (!empty($payments->excess_amount)) {
                        if(in_array($row->pump_operator_id,$already_added_excess)){
                            return '';
                        }else{
                            $already_added_excess[] = $row->pump_operator_id;
                        }
                        
                        return  '<span class="display_currency short_amount" data-orig-value="' . $payments->excess_amount . '" data-currency_symbol = false>' . $this->productUtil->num_f($payments->excess_amount, false, $business_details, true) . '</span>';
                    }
                    if (!empty($payments->short_amount)) {
                        if(in_array($row->pump_operator_id,$already_added_shortage)){
                            return '';
                        }else{
                            $already_added_shortage[] = $row->pump_operator_id;
                        }
                        
                        return  '<span class="display_currency short_amount text-red" data-orig-value="' . $payments->short_amount . '" data-currency_symbol = false>' . $this->productUtil->num_f($payments->short_amount, false, $business_details, true) . '</span>';
                    }
                })
                ->removeColumn('id');


            return $fuel_tanks->rawColumns(['action', 'sold_ltr', 'amount', 'short_amount', 'short_amount', 'cash', 'cheque', 'total_amount', 'difference', 'settlement_no'])
                ->make(true);
        }
        
        $business_locations = BusinessLocation::forDropdown($business_id);
        $pumps = Pump::where('business_id', $business_id)->get();
        if ($only_pumper) {
            $pump_operators = PumpOperator::where('business_id', $business_id)->where('id', $pump_operator_id)->pluck('name', 'id');
        } else {
            $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');
        }
        $payment_types = $this->transactionUtil->payment_types();

        $pump_operator = PumpOperator::find($pump_operator_id);
        $pump_operator_name = "";
        if(!empty($pump_operator)){
            $pump_operator_name = $pump_operator->name;
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
        
        $user = Auth::user();
        
        $pump_operator_id = $user->pump_operator_id;
        $shift_number = PumpOperatorAssignment::where('pump_operator_id', $pump_operator_id)->max('shift_number');
        
        return view('petro::pump_operators.actions.closing_shift')->with(compact(
            'layout',
            'business_locations',
            'pumps',
            'pump_operators',
            'pump_operator',
            'payment_types',
            'only_pumper',
            'pump_operator_name',
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
        return view('petro::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
    

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        
        $pump = PumperDayEntry::leftjoin('pumps','pumps.id','pumper_day_entries.pump_id')
                ->leftjoin('products', 'pumps.product_id', 'products.id')
                ->leftjoin('variations', 'products.id', 'variations.product_id')
                ->leftjoin('variation_location_details', 'variations.id', 'variation_location_details.variation_id')
                ->where('pumper_day_entries.id', $id)
                ->select('sell_price_inc_tax', 'pumps.pump_no', 'variation_location_details.qty_available','pumper_day_entries.*')->first();
        

        return view('petro::pump_operators.actions.edit_closing_shift')->with(compact(
            'pump',
            'id'
        ));
    }
    
    public function show($id)
    {
        
        
        $pump = PumperDayEntry::leftjoin('pumps','pumps.id','pumper_day_entries.pump_id')
                ->leftjoin('products', 'pumps.product_id', 'products.id')
                ->leftjoin('variations', 'products.id', 'variations.product_id')
                ->leftjoin('variation_location_details', 'variations.id', 'variation_location_details.variation_id')
                ->where('pumper_day_entries.pumper_assignment_id', $id)
                ->select('sell_price_inc_tax', 'pumps.pump_no', 'variation_location_details.qty_available','pumper_day_entries.*')->first();
        if (empty(session()->get('pump_operator_main_system'))) {
            $layout = 'pumper';
        } else {
            $layout = 'app';
        }

        return view('petro::pump_operators.actions.view_closing_shift')->with(compact(
            'pump',
            'id',
            'layout'
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
        try {
            $entry = PumperDayEntry::findOrFail($id);
            $data = array(
                'closing_meter' => $request->closing_meter,
                'testing_ltr' => $request->testing_ltr,
                'sold_ltr' => $request->sold_ltr,
                'amount' => $request->amount_hidden,
            );

            DB::beginTransaction();

            PumperDayEntry::where('id',$id)->update($data);
            Pump::where('id', $entry->pump_id)->update(['pod_starting_meter' => $request->starting_meter, 'pod_last_meter' => $request->closing_meter]);
            PumpOperatorAssignment::where('pump_id', $entry->pump_id)->where('starting_meter',$request->starting_meter)->update(['closing_meter' => $request->closing_meter]);

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('petro::lang.success')
            ];

             return redirect()->back()->with('status', $output);
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];

            return redirect()->back()->with('status', $output);
        }
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

    public function closeShift($shift_id)
    {
        $business_id =  Auth::user()->business_id;
        PumpOperatorAssignment::where('shift_id', $shift_id)->update(['status' => 'close', 'close_date_and_time' => \Carbon::now(),'is_manually_closed' => 1]);
        
        $shift = PetroShift::findOrFail($shift_id);
        $shift->status = 2;
        $shift->closed_time = date('Y-m-d H:i');
        $shift->save();

        $output = [
            'success' => 1,
            'msg' => __('lang_v1.success')
        ];
        return redirect()->to('/petro/pump-operators/dashboard')->with('status', $output);
    }
}
