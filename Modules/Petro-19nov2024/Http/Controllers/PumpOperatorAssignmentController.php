<?php

namespace Modules\Petro\Http\Controllers;

use App\Product;
use App\Utils\BusinessUtil;
use App\Utils\Util;
;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Petro\Entities\Pump;

use Modules\Petro\Entities\PetroShift;

use Modules\Petro\Entities\PumpOperatorAssignment;
use Modules\Petro\Entities\PumpOperator;
use Yajra\DataTables\Facades\DataTables;

class PumpOperatorAssignmentController extends Controller
{

    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;
    protected $businessUtil;

    private $barcode_types;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, BusinessUtil $businessUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->businessUtil = $businessUtil;
    }


    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('petro::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $business_id = Auth::user()->business_id;
        
        $assigned = PumpOperatorAssignment::where('business_id',$business_id)->where('status','open')->pluck('pump_id')->toArray();
        
        $pumps = Pump::whereNotIn('pumps.id', $assigned)
            ->where('pumps.business_id', $business_id)->pluck('pump_name','id');
        
        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');
        
        $shift_number = PumpOperatorAssignment::max('shift_number');
        
        return view('petro::pump_operators.partials.bulk_pumper_assignment')->with(compact(
            'pumps',
            'pump_operators',
            'shift_number'
        ));
    }
    
    public function storeBulk(Request $request)
    {
        $business_id = Auth::user()->business_id;
        try {
            
            $petro_shift = PetroShift::updateOrCreate(array(
                            'shift_date' => date('Y-m-d',strtotime($request->date)),
                            'status' => 0,
                            'pump_operator_id' => $request->pump_operator
                         ),
                         array(
                            'business_id' => $business_id,
                            'pump_operator_id' => $request->pump_operator,
                            'status' => 0,
                            'shift_date' => date('Y-m-d',strtotime($request->date))
                        ));
                        
            $shift_number = PumpOperatorAssignment::max('shift_number');
            foreach($request->pump as $one){
                $pump = Pump::findOrFail($one);
                
                $starting_meter = !empty($pump->pod_last_meter) ? ($pump->pod_last_meter >= $pump->last_meter_reading ?  ($pump->pod_last_meter) : ($pump->last_meter_reading)) :  ($pump->last_meter_reading);
                $input = array(
                        'business_id' => $business_id,
                        'pump_id' => $one,
                        'pump_operator_id' => $request->pump_operator,
                        'starting_meter' => $starting_meter,	
                        'date_and_time' => $request->date,
                        'status' => 'open',
                        'assigned_by' => auth()->user()->id,
                        'shift_id' => $petro_shift->id,
                        'shift_number' => $shift_number + 1
                );
    
                PumpOperatorAssignment::create($input);
            }
            
            

            $output = [
                'success' => true,
                'msg' => __('petro::lang.pump_operator_assigned_success')
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

    public function getPumperAssignment($pump_id, $pump_operator_id)
    {
        $business_id = Auth::user()->business_id;
        
        if (empty(Auth::user()->pump_operator_id)) {

            // if is admin
            $pump_op = PumpOperator::where('business_id', Auth::user()->business_id)->where('is_default', '1')->first();

            if (!empty($pump_op)) {
                $pump_operator_id =  $pump_op->id;
            }
        }

        $pump = Pump::leftjoin('products', 'pumps.product_id', 'products.id')
            ->where('pumps.id', $pump_id)
            ->where('pumps.business_id', $business_id)
            ->select('pumps.*', 'products.name')->first();

        if(empty(session()->get('pump_operator_main_system'))){
            $layout = 'pumper';
        }else{
            $layout = 'app';
        }

        return view('petro::pump_operators.partials.pumper_assignment')->with(compact(
            'pump',
            'pump_operator_id',
            'layout'
        ));
    }
    
    public function confirmAssignment($id)
    {
        $pump = PumpOperatorAssignment::leftjoin('pumps','pumps.id','pump_operator_assignments.pump_id')
                                    ->leftjoin('products','products.id','pumps.product_id')
                                    ->where('pump_operator_assignments.id',$id)
                                    ->select('pump_operator_assignments.*','pumps.pump_name','products.name')
                                    ->first();

        return view('petro::pump_operators.partials.pumper_assignment_pumper')->with(compact(
            'pump'
        ));
    }
    
    public function postConfirmAssignment(Request $request,$id)
    {
        
        try {
            
            $input = array('is_confirmed' => 1,'confirmed_at' => date('Y-m-d H:i:s'));
            
            PumpOperatorAssignment::where('id',$id)->update($input);

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
    

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $business_id = Auth::user()->business_id;
        try {
            $input = $request->except('_token');
            $input['date_and_time'] = \Carbon::now()->format('Y-m-d H:i:s');
            $input['status'] = !empty($input['status']) ? 'open' : 'close';
            $input['business_id'] = $business_id;

            if (!empty($input['closing_meter'])) {
                if ($input['closing_meter'] < $input['starting_meter']) {
                    $output = [
                        'success' => 0,
                        'msg' => __('petro::lang.closing_meter_cannot_be_smaller')
                    ];
                    return redirect()->back()->with('status', $output);
                }
            }
            if (!empty($input['status'])) {
                if (empty($input['closing_meter'])) {
                    $output = [
                        'success' => 0,
                        'msg' => __('petro::lang.closing_meter_cannot_be_empty')
                    ];
                    return redirect()->back()->with('status', $output);
                }
            }
            
             $petro_shift = PetroShift::updateOrCreate(array(
                                'shift_date' => date('Y-m-d'),
                                'status' => 0,
                                'pump_operator_id' => $request->pump_operator_id
                             ),
                             array(
                                'business_id' => $business_id,
                                'pump_operator_id' => $request->pump_operator_id,
                                'status' => 0,
                                'shift_date' => date('Y-m-d')
                            ));
                            
            $input['shift_id'] = $petro_shift->id;
            

            PumpOperatorAssignment::create($input);

            $output = [
                'success' => true,
                'msg' => __('petro::lang.pump_operator_assigned_success')
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
        $pump_assignment = PumpOperatorAssignment::findOrFail($id);

        if(empty(session()->get('pump_operator_main_system'))){
            $layout = 'pumper';
        }else{
            $layout = 'app';
        }

        return view('petro::pump_operators.partials.pumper_assignment_edit')->with(compact('pump_assignment', 'layout'));
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
            $input = $request->except('_token', '_method');
            $input['status'] = !empty($input['status']) ? 'open' : 'close';

            if (!empty($input['closing_meter'])) {
                if ($input['closing_meter'] < $input['starting_meter']) {
                    $output = [
                        'success' => 0,
                        'msg' => __('petro::lang.closing_meter_cannot_be_smaller')
                    ];
                    return redirect()->back()->with('status', $output);
                }
            }
            if (!empty($input['status'])) {
                if (empty($input['closing_meter'])) {
                    $output = [
                        'success' => 0,
                        'msg' => __('petro::lang.closing_meter_cannot_be_empty')
                    ];
                    return redirect()->back()->with('status', $output);
                }
            }

            PumpOperatorAssignment::where('id', $id)->update($input);

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

        return redirect()->back()->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            PumpOperatorAssignment::findOrFail($id)->delete();
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

}
