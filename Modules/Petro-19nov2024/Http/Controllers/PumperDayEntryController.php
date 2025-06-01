<?php

namespace Modules\Petro\Http\Controllers;

use App\Business;
use App\BusinessLocation;
use App\Product;
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
use Illuminate\Support\Facades\Log;
use Modules\Petro\Entities\Pump;
use Modules\Petro\Entities\PumperDayEntry;
use Modules\Petro\Entities\PumpOperator;
use Modules\Petro\Entities\PumpOperatorOtherSale;
use Modules\Petro\Entities\PumpOperatorAssignment;
use Modules\Petro\Entities\PumpOperatorPayment;
use Yajra\DataTables\Facades\DataTables;
use Modules\Petro\Entities\PetroShift;

class PumperDayEntryController extends Controller
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
                ->select('pump_operators.name', 'pumper_day_entries.*','business_locations.name as location_name','pump_operator_assignments.shift_id');

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
        
        return view('petro::pump_operators.pumper_day_entries')->with(compact(
            'layout',
            'business_locations',
            'pumps',
            'pump_operators',
            'pump_operator',
            'payment_types',
            'only_pumper',
            'shifts',
            'shift_number'
        ));
    }
    
    public function getPumperDayEntrySummary(){
        $only_pumper = request()->only_pumper;
        $shift_id = request()->shift_id;
        $business_id =  Auth::user()->business_id;
        $pump_operator_id = Auth::user()->pump_operator_id;
        
         $day_entries_query = PumperDayEntry::leftjoin('pump_operators', 'pumper_day_entries.pump_operator_id', 'pump_operators.id')
            ->leftjoin('pump_operator_assignments','pumper_day_entries.pumper_assignment_id','pump_operator_assignments.id')
            ->leftjoin('pumps', 'pumper_day_entries.pump_id', 'pumps.id')
            ->where('shift_id', $shift_id)->where('pumper_day_entries.business_id', $business_id)
            ->where('pump_operator_assignments.status','close')
            ->select('pump_operators.name', 'pumper_day_entries.*', 'pumps.pump_name');
        if ($only_pumper) {
            $day_entries_query->where('pumper_day_entries.pump_operator_id', $pump_operator_id);
        }

        $day_entries = $day_entries_query->get();

        $today_pumps = implode(', ', $day_entries->pluck('pump_name')->unique()->toArray());
        
        $other_sale = PumpOperatorOtherSale::where('shift_id', $shift_id)
                        ->select(DB::raw('SUM(sub_total - discount_amount) as total'))
                        ->value('total');

        $payments = PumpOperatorPayment::leftjoin('pump_operators', 'pump_operator_payments.pump_operator_id', 'pump_operators.id')
            ->where('shift_id', $shift_id)->select(
                DB::raw('SUM(IF(payment_type="cash", payment_amount, 0)) as cash'),
                DB::raw('SUM(IF(payment_type="card", payment_amount, 0)) as card'),
                DB::raw('SUM(IF(payment_type="cheque", payment_amount, 0)) as cheque'),
                DB::raw('SUM(IF(payment_type="credit", payment_amount, 0)) as credit'),
                DB::raw('SUM(IF(payment_type="other", payment_amount, 0)) as other'),
                DB::raw('SUM(IF(payment_type="shortage" OR payment_type="excess", payment_amount, 0)) as shortage_excess'),
                DB::raw('SUM(payment_amount) as total')
            );
        if ($only_pumper) {
            $payments->where('pump_operator_payments.pump_operator_id', $pump_operator_id);
        }
        $payments = $payments->first();
        
        return view('petro::pump_operators.partials.pumper_day_entry_summary')->with(compact(
            'day_entries',
            'today_pumps',
            'payments',
            'only_pumper',
            'other_sale'
        ));
    }
    
    public function getClosingShiftSummary(){
        $only_pumper = request()->only_pumper;
        $shift_id = request()->shift_id;
        $business_id =  Auth::user()->business_id;
        $pump_operator_id = Auth::user()->pump_operator_id;
        
         $day_entries_query = PumperDayEntry::leftjoin('pump_operators', 'pumper_day_entries.pump_operator_id', 'pump_operators.id')
            ->leftjoin('pump_operator_assignments','pumper_day_entries.pumper_assignment_id','pump_operator_assignments.id')
            ->leftjoin('pumps', 'pumper_day_entries.pump_id', 'pumps.id')
            ->where('shift_id', $shift_id)->where('pumper_day_entries.business_id', $business_id)
            ->where('pump_operator_assignments.status','close')
            ->select('pump_operators.name', 'pumper_day_entries.*', 'pumps.pump_name');
        if ($only_pumper) {
            $day_entries_query->where('pumper_day_entries.pump_operator_id', $pump_operator_id);
        }

        $day_entries = $day_entries_query->get();

        $today_pumps = implode(', ', $day_entries->pluck('pump_name')->unique()->toArray());

        $payments = PumpOperatorPayment::leftjoin('pump_operators', 'pump_operator_payments.pump_operator_id', 'pump_operators.id')
            ->where('shift_id', $shift_id)->select(
                DB::raw('SUM(IF(payment_type="cash", payment_amount, 0)) as cash'),
                DB::raw('SUM(IF(payment_type="card", payment_amount, 0)) as card'),
                DB::raw('SUM(IF(payment_type="cheque", payment_amount, 0)) as cheque'),
                DB::raw('SUM(IF(payment_type="credit", payment_amount, 0)) as credit'),
                DB::raw('SUM(IF(payment_type="other", payment_amount, 0)) as other'),
                DB::raw('SUM(IF(payment_type="shortage" OR payment_type="excess", payment_amount, 0)) as shortage_excess'),
                DB::raw('SUM(payment_amount) as total')
            );
        if ($only_pumper) {
            $payments->where('pump_operator_payments.pump_operator_id', $pump_operator_id);
        }
        $payments = $payments->first();
        
        $this_shift = PetroShift::findOrFail($shift_id);
        
        $other_sale = PumpOperatorOtherSale::where('shift_id', $shift_id)
                        ->select(DB::raw('SUM(sub_total - discount_amount) as total'))
                        ->value('total');
        
        $shift_number = PumpOperatorAssignment::where('pump_operator_id', $pump_operator_id)->max('shift_number');
        $unconfirmed_pumps_count = PumpOperatorAssignment::join('pumps', 'pumps.id', 'pump_operator_assignments.pump_id')
        ->where('pump_operator_assignments.shift_number', $shift_number)
        ->where('pumps.business_id', $business_id)
        ->where('pump_operator_assignments.pump_operator_id', $pump_operator_id)
        ->where('pump_operator_assignments.is_confirmed', 0)
        ->count();

        return view('petro::pump_operators.partials.closing_shift_summary')->with(compact(
            'day_entries',
            'today_pumps',
            'payments',
            'only_pumper',
            'this_shift',
            'other_sale',
            'unconfirmed_pumps_count'
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
        $business_id =  Auth::user()->business_id;
        $day_entry = PumperDayEntry::find($id);
        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');
        $pumps = Pump::where('business_id', $business_id)->pluck('pump_no', 'id');
        $pump_details = Pump::leftjoin('products', 'pumps.product_id', 'products.id')
            ->leftjoin('variations', 'products.id', 'variations.product_id')
            ->leftjoin('variation_location_details', 'variations.id', 'variation_location_details.variation_id')
            ->where('pumps.id', $day_entry->pump_id)
            ->select('default_sell_price', 'pumps.*', 'variation_location_details.qty_available')->first();

        return view('petro::pump_operators.partials.edit_pumper_day_entry')->with(compact(
            'day_entry',
            'pump_operators',
            'pumps',
            'pump_details'
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
            $pump = Pump::find($request->pump_id);
            $data = [
                'date' => $this->transactionUtil->uf_date($request->date),
                'pump_operator_id' => $request->pump_operator_id,
                'pump_id' => $request->pump_id,
                'pump_no' => !empty($pump) ? $pump->pump_no : null,
                'starting_meter' => $request->starting_meter,
                'closing_meter' => $request->closing_meter,
                'testing_ltr' => $this->transactionUtil->num_uf($request->testing_ltr),
                'sold_ltr' => $this->transactionUtil->num_uf($request->sold_ltr),
                'amount' => $this->transactionUtil->num_uf($request->amount),
            ];

            PumperDayEntry::where('id', $id)->update($data);
            $output = [
                'success' => true,
                'tab' => 'pumper_day_entries',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'pumper_day_entries',
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


    public function viewAddSettlementNo($id)
    {
        $day_entry = PumperDayEntry::leftjoin('users', 'pumper_day_entries.settlement_added_by', 'users.id')->where('pumper_day_entries.id', $id)->select('pumper_day_entries.settlement_no', 'pumper_day_entries.settlement_datetime', 'users.username')->first();

        return view('petro::pump_operators.partials.view_settlement_no')->with(compact('day_entry'));
    }

    public function getAddSettlementNo($id)
    {
        return view('petro::pump_operators.partials.add_settlement_no')->with(compact('id'));
    }

    public function postAddSettlementNo($id, Request $request)
    {

        try {
            $data = [
                'settlement_datetime' => \Carbon::now(),
                'settlement_no' => $request->settlement_no,
                'settlement_added_by' => Auth::user()->id,
            ];

            PumperDayEntry::where('id', $id)->update($data);
            $output = [
                'success' => true,
                'tab' => 'pumper_day_entries',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'pumper_day_entries',
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

public function getDailyCollection()
{
    // dump("wetwet");exit;
    $business_id = request()->session()->get('user.business_id');
    $business_details = $this->businessUtil->getDetails($business_id);

    $only_pumper = request()->only_pumper;
    $pump_operator_id = Auth::user()->pump_operator_id;
    
    // dump($only_pumper);exit;
    
    if (request()->ajax()) {
        // Calculate total sold_ltr, testing_ltr and sold_amount
        $totals = Pump::leftjoin('pumper_day_entries', function ($join) {
                $join->on('pumps.id', 'pumper_day_entries.pump_id')->whereDate('date', date('Y-m-d'));
            })
            ->where('pumps.business_id', $business_id)
            ->whereDate('pumper_day_entries.date', date('Y-m-d'));

        if(!empty($only_pumper)){
            $totals->where('pumper_day_entries.pump_operator_id', $pump_operator_id);
        }

        $totals = $totals->select(
            DB::raw('SUM(pumper_day_entries.sold_ltr) as total_sold_ltr'),
            DB::raw('SUM(pumper_day_entries.testing_ltr) as total_testing_ltr')
        )->first();

        // Calculate total sold_amount
        $total_sold_amount = 0;
        if ($totals->total_sold_ltr > 0) {
            $product = Product::leftjoin('variations', 'products.id', 'variations.product_id')
                ->where('products.business_id', $business_id)
                ->first();
            $total_sold_amount = $totals->total_sold_ltr * $product->sell_price_inc_tax;
        }

        // $pumps = Pump::leftjoin('pumper_day_entries', function ($join) {
        //     $join->on('pumps.id', 'pumper_day_entries.pump_id')->whereDate('date', date('Y-m-d'));
        // })->leftjoin('pump_operators', 'pumper_day_entries.pump_operator_id', 'pump_operators.id')
        //     ->leftjoin('business_locations','business_locations.id','pump_operators.location_id')
        //     ->leftjoin('pump_operator_assignments','pumper_day_entries.pump_operator_id','pump_operator_assignments.pump_operator_id')
        //     ->leftjoin('pump_operator_assignments','pumper_day_entries.pump_id','pump_operator_assignments.pump_id')
        //     ->where('pumps.business_id', $business_id)
        //     ->whereDate('pumper_day_entries.date', date('Y-m-d'))
        //     ->select('pumps.product_id', 'pumper_day_entries.*', 'pump_operators.name','business_locations.name as location_name', 'pump_operator_assignments.shift_number', 'pump_operator_assignments.id as assignment_id')
        //     ->groupBy('pumper_day_entries.id')
        //     ->orderBy('pumps.id');
        
         $date = date('Y-m-d');

        $pumps = DB::select("select t1.*,t5.date,t5.testing_ltr,t5.sold_ltr,t5.amount,t2.product_id,t3.name,t2.pump_no from pump_operator_assignments as t1 left join pumps as t2 on t1.pump_id = t2.id left join pump_operators as t3 on t1.pump_operator_id = t3.id left join business_locations as t4 on t3.location_id = t4.id left join pumper_day_entries as t5 on t1.id = t5.pumper_assignment_id group by t1.id order by t2.id");
        
        if(!empty($only_pumper)){
            $pumps->where('pumper_day_entries.pump_operator_id', $pump_operator_id);
        }
        
        $daily_collections = DataTables::of($pumps)
            ->addColumn('action', function ($row) {
                $html = '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                            data-toggle="dropdown" aria-expanded="false">' .
                    __("messages.actions") .
                    '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-left" role="menu"> ';
                if (auth()->user()->can('daily_pump_status.edit')) {
                    $html .= '<li><a class="btn-modal" data-container=".pump_operator_modal" data-href="' . action('\Modules\Petro\Http\Controllers\PumpOperatorAssignmentController@edit', $row->id) . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>' . __("messages.edit") . '</a></li> ';
                }
                if (auth()->user()->can('daily_pump_status.delete')) {
                    $html .= '<li><a href="' . action('\Modules\Petro\Http\Controllers\PumpOperatorAssignmentController@destroy', $row->id) . '" class="delete_daily_collection"><i class="fa fa-trash"></i>' . __("messages.delete") . '</a></li>';
                }

                $html .= '</ul></div>';
                return $html;
            })
            ->addColumn('sold_ltr', function($row){
                return '<span class="display_currency footer_sold_fuel_qty" data-orig-value="' . $row->sold_ltr . '" data-currency_symbol = false>' . $this->productUtil->num_f($row->sold_ltr) . '</span>';
            })
            ->addColumn('testing_ltr', function($row){
                return '<span class="display_currency footer_testing_qty" data-orig-value="' . $row->testing_ltr . '" data-currency_symbol = false>' . $this->productUtil->num_f($row->testing_ltr) . '</span>';
            })
            ->addColumn('sold_amount', function ($row) use ($business_details) {
                $product = Product::leftjoin('variations', 'products.id', 'variations.product_id')
                    ->where('products.id', $row->product_id)->select('variations.sell_price_inc_tax')->first();
                $amt = ($row->sold_ltr) * $product->sell_price_inc_tax;
                return '<span class="display_currency footer_sold_fuel_amount" data-orig-value="' . $amt . '" data-currency_symbol = false>' . $this->commonUtil->num_f($amt, false, $business_details, false) . '</span>';
            })
            ->removeColumn('id')
            ->addColumn('date_and_time', function($row) {
                return $row->date_and_time;
            });
            
            // dump($daily_collections);exit;

        
        return $daily_collections->rawColumns(['action', 'sold_ltr', 'testing_ltr', 'sold_amount'])
            ->with('total_sold_ltr', $this->round_normal($totals->total_sold_ltr))
            ->with('total_testing_ltr', $this->round_normal($totals->total_testing_ltr))
            ->with('total_sold_amount', $this->round_normal($total_sold_amount))
            ->make(true);
    }
}

private function round_normal($value)
{
    return number_format((float)$value, 2, '.', '');
}



    // /**
    //  * get the specified resource from storage.
    //  * 
    //  * @return Renderable
    //  */
    // public function getDailyCollection()
    // {

    //     $business_id = request()->session()->get('user.business_id');
    //     $business_details = $this->businessUtil->getDetails($business_id);
        
    //     $only_pumper = request()->only_pumper;
    //     $pump_operator_id = Auth::user()->pump_operator_id;
        
    //     if (request()->ajax()) {
    //         $pumps = Pump::leftjoin('pumper_day_entries', function ($join) {
    //             $join->on('pumps.id', 'pumper_day_entries.pump_id')->whereDate('date', date('Y-m-d'));
    //         })->leftjoin('pump_operators', 'pumper_day_entries.pump_operator_id', 'pump_operators.id')
    //             ->leftjoin('business_locations','business_locations.id','pump_operators.location_id')
    //             ->where('pumps.business_id', $business_id)
    //             ->whereDate('pumper_day_entries.date', date('Y-m-d'))
    //             ->select('pumps.product_id', 'pumper_day_entries.*', 'pump_operators.name','business_locations.name as location_name')->groupBy('pumper_day_entries.id')
    //             ->orderBy('pumps.id');
                
    //         if(!empty($only_pumper)){
    //             $pumps->where('pumper_day_entries.pump_operator_id',$pump_operator_id);
    //         }


    //         $daily_collections = DataTables::of($pumps)
    //             ->addColumn(
    //                 'action',
    //                 function ($row) {
                        
    //                     $html = '<div class="btn-group">
    //                             <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
    //                                 data-toggle="dropdown" aria-expanded="false">' .
    //                         __("messages.actions") .
    //                         '<span class="caret"></span><span class="sr-only">Toggle Dropdown
    //                                 </span>
    //                             </button>
    //                             <ul class="dropdown-menu dropdown-menu-left" role="menu"> ';
    //                     if (auth()->user()->can('daily_pump_status.edit')) {
    //                         $html .= '<li><a class="btn-modal" data-container=".pump_operator_modal" data-href="' . action('\Modules\Petro\Http\Controllers\PumpOperatorAssignmentController@edit', $row->id) . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>' . __("messages.edit") . '</a></li> ';
    //                     }
    //                     if (auth()->user()->can('daily_pump_status.delete')) {
    //                         $html .= '<li><a href="' . action('\Modules\Petro\Http\Controllers\PumpOperatorAssignmentController@destroy', $row->id) . '" class="delete_daily_collection"><i class="fa fa-trash"></i>' . __("messages.delete") . '</a></li>';
    //                     }

    //                     $html .= '</ul></div>';


    //                     return $html;
    //                 }
    //             )
    //             ->addColumn('sold_ltr', function($row){
    //                 return  '<span class="display_currency footer_sold_fuel_qty" data-orig-value="' . $row->sold_ltr . '" data-currency_symbol = false>' . $this->productUtil->num_f($row->sold_ltr) . '</span>';
    //             })
    //             ->addColumn('testing_ltr', function($row){
    //                 return  '<span class="display_currency footer_testing_qty" data-orig-value="' . $row->testing_ltr . '" data-currency_symbol = false>' . $this->productUtil->num_f($row->testing_ltr) . '</span>';
    //             })
    //             ->addColumn('sold_amount', function ($row) use ($business_details) {
                    
    //                 $product = Product::leftjoin('variations', 'products.id', 'variations.product_id')
    //                     ->where('products.id', $row->product_id)->select('variations.sell_price_inc_tax')->first();
                        
    //                 $amt = ($row->sold_ltr) * $product->sell_price_inc_tax;
                    
    //                 return  '<span class="display_currency footer_sold_fuel_amount" data-orig-value="' . $amt . '" data-currency_symbol = false>' . $this->commonUtil->num_f($amt, false, $business_details, false) . '</span>';

    //             })
    //             ->removeColumn('id')
    //             ->addColumn('date_and_time', '{{@format_date($date)}}');

    //         return $daily_collections->rawColumns(['action','sold_ltr','testing_ltr','sold_amount'])
    //             ->make(true);
    //     }
    // }
}
