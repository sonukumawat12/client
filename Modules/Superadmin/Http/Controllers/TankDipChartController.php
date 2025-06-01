<?php

namespace Modules\Superadmin\Http\Controllers;

use App\Unit;
;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Superadmin\Entities\TankDipChart;
use Modules\Superadmin\Entities\TankDipChartDetail;
use Yajra\DataTables\Facades\DataTables;
use Modules\Petro\Entities\FuelTank;
use Spatie\Activitylog\Models\Activity;

use App\Utils\Util;
use App\Utils\ProductUtil;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use App\Utils\NotificationUtil;
use App\Utils\BusinessUtil;

class TankDipChartController extends Controller
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
     * @return Renderable
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = DB::table('tank_dip_charts')
                ->leftjoin('fuel_tanks', 'tank_dip_charts.tank_id', 'fuel_tanks.id')
                ->leftjoin('users','users.id','tank_dip_charts.created_by')
                ->select([
                    'tank_dip_charts.*',
                    'fuel_tanks.tank_manufacturer','fuel_tanks.tank_manufacturer_phone','fuel_tanks.storage_volume','fuel_tanks.fuel_tank_number',
                    'users.username'
                ]);
            

            if (!empty(request()->sheet_name)) {
                $tank_dip_chart->where('sheet_name', request()->sheet_name);
            }
            if (!empty(request()->tank_manufacturer)) {
                $tank_dip_chart->where('fuel_tanks.tank_manufacturer', request()->tank_manufacturer);
            }
            if (!empty(request()->tank_capacity)) {
                $tank_dip_chart->where('fuel_tanks.storage_volume', request()->tank_capacity);
            }

            return DataTables::of($query)
                ->addColumn('action', function ($row) {
                    $action = '';
                        $action .= '<a data-href="' . action('\Modules\Superadmin\Http\Controllers\TankDipChartController@edit', [$row->id]) . '" data-container=".tank_dip_chart_model" class="btn btn-modal btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a>';

                        $action .= '&nbsp
                                <button type="button" data-href="' . action('\Modules\Superadmin\Http\Controllers\TankDipChartController@destroy', [$row->id]) . '" class="btn btn-xs btn-danger delete_tank_dip_chart_button"><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</button>';


                        return $action;
                })
                ->editColumn('date', '{{@format_datetime($date)}}')
                
                ->editColumn('storage_volume', '{{@num_format($storage_volume)}}')
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $already_added = TankDipChart::pluck('tank_id')->toArray();
        $tanks = FuelTank::whereNotIn('id',$already_added)->get();

        return view('superadmin::superadmin_settings.tank_dip_chart.create')->with(compact(
            'tanks'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        
        try {
            
            DB::beginTransaction();
            
            $date_time = $this->transactionUtil->uf_date($request->date_time,true);
            if(empty($request->tank_id) && !empty($request->tank_ids)){
                $tank_ids_arr = explode(',',$request->tank_ids);
                foreach($tank_ids_arr as $tank_id){
                    $tank_manufacturer = $request->tank_manufacturer;
                    $tank_manufacturer_contact = $request->tank_manufacturer_contact;
                    
                    $sheet_name = $request->sheet_name;
                    
                    $tank = FuelTank::findOrFail($tank_id);
                    $business_id = $tank->business_id;
                    
                    // update manufacuteres
                    FuelTank::where('id',$tank_id)->update(array('tank_manufacturer' => $tank_manufacturer, 'tank_manufacturer_phone' => $tank_manufacturer_contact));
                    
                    $dip_chart = TankDipChart::create(array('business_id' => $business_id, 'date' => $date_time,'sheet_name' => $sheet_name,'tank_id' => $tank_id,'created_by' => auth()->user()->id));
                }
            } else {
                $tank_id = $request->tank_id;
                $tank_manufacturer = $request->tank_manufacturer;
                $tank_manufacturer_contact = $request->tank_manufacturer_contact;
                
                $sheet_name = $request->sheet_name;
                
                $tank = FuelTank::findOrFail($tank_id);
                $business_id = $tank->business_id;
                
                // update manufacuteres
                FuelTank::where('id',$tank_id)->update(array('tank_manufacturer' => $tank_manufacturer, 'tank_manufacturer_phone' => $tank_manufacturer_contact));
                
                $dip_chart = TankDipChart::create(array('business_id' => $business_id, 'date' => $date_time,'sheet_name' => $sheet_name,'tank_id' => $tank_id,'created_by' => auth()->user()->id));
            }
            
            DB::commit();
            
            $output = [
                'success' => true,
                'msg' => __('petro::lang.success'),
                'tank_dip_chart' => true,
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tank_dip_chart' => true,
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
        return view('superadmin::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $data = DB::table('tank_dip_charts')
                ->leftjoin('fuel_tanks', 'tank_dip_charts.tank_id', 'fuel_tanks.id')
                ->leftjoin('users','users.id','tank_dip_charts.created_by')
                ->where('tank_dip_charts.id', $id)
                ->select([
                    'tank_dip_charts.*',
                    'fuel_tanks.tank_manufacturer','fuel_tanks.tank_manufacturer_phone','fuel_tanks.storage_volume','fuel_tanks.fuel_tank_number',
                    'users.username'
                ])->first();

        return view('superadmin::superadmin_settings.tank_dip_chart.edit')->with(compact(
            'data'
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
            $tank_manufacturer = $request->tank_manufacturer;
            $tank_manufacturer_contact = $request->tank_manufacturer_contact;
            
            $sheet_name = $request->sheet_name;
            
            $dip_chart = TankDipChart::findOrFail($id);
            
            $fuel_tank = FuelTank::findOrFail($dip_chart->tank_id);
            
            $is_changed = false;
            $changed_msg = false;
            
            if($fuel_tank->tank_manufacturer != $tank_manufacturer){
                $is_changed = true;
                $changed_msg .= "tank manufacturer changed from ".$fuel_tank->tank_manufacturer." to ".$tank_manufacturer.PHP_EOL;
            }
            
            if($fuel_tank->tank_manufacturer_phone != $tank_manufacturer_contact){
                $is_changed = true;
                $changed_msg .= "tank manufacturer phone changed from ".$fuel_tank->tank_manufacturer_phone." to ".$tank_manufacturer_contact.PHP_EOL;
            }
            
            if($dip_chart->sheet_name != $sheet_name){
                $is_changed = true;
                $changed_msg .= "Sheet name changed from ".$dip_chart->sheet_name." to ".$sheet_name.PHP_EOL;
            }
            
            // update manufacuteres
            FuelTank::where('id',$dip_chart->tank_id)->update(array('tank_manufacturer' => $tank_manufacturer, 'tank_manufacturer_phone' => $tank_manufacturer_contact));
            
            $dip_chart = TankDipChart::where('id',$id)->update(array('sheet_name' => $sheet_name));
            
            if(!empty($is_changed) && !empty($changed_msg)){
                        
                        $activity = new Activity();
                        $activity->log_name = "Dip Chart";
                        $activity->description = "update";
                        $activity->subject_id = $id;
                        $activity->subject_type = "Modules\Superadmin\Entities\TankDipChart";
                        $activity->causer_id = auth()->user()->id;
                        $activity->causer_type = 'App\User';
                        $activity->properties = $changed_msg ;
                        $activity->created_at = date('Y-m-d H:i');
                        $activity->updated_at = date('Y-m-d H:i');
                        
                        // Save the activity
                        $activity->save();
                        
                    }
            

            DB::commit();

            $output = [
                'success' => true,
                'tank_dip_chart' => true,
                'msg' => __('superadmin::lang.tank_dip_chart_update_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tank_dip_chart' => true,
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
            DB::beginTransaction();
            
            $dip_chart = TankDipChart::findOrFail($id);
            $dip_chart_details = TankDipChartDetail::where('tank_dip_chart_id',$id)->first();
            
            $changed_msg = "";
            $is_changed = true;
            $changed_msg .= "Deleted Dip Sheet name ".$dip_chart->sheet_name.PHP_EOL;
            $changed_msg .= "Deleted Dip Reading ".$this->transactionUtil->num_f($dip_chart_details->dip_reading).PHP_EOL;
            $changed_msg .= "Deleted Dip Readinng in Lts ".$this->transactionUtil->num_f($dip_chart_details->dip_reading_value).PHP_EOL;
            
            
            if(!empty($is_changed) && !empty($changed_msg)){
                        
                        $activity = new Activity();
                        $activity->log_name = "Dip Chart";
                        $activity->description = "delete";
                        $activity->subject_id = $id;
                        $activity->subject_type = "Modules\Superadmin\Entities\TankDipChart";
                        $activity->causer_id = auth()->user()->id;
                        $activity->causer_type = 'App\User';
                        $activity->properties = $changed_msg ;
                        $activity->created_at = date('Y-m-d H:i');
                        $activity->updated_at = date('Y-m-d H:i');
                        
                        // Save the activity
                        $activity->save();
                        
                    }
            $dip_chart->delete();
            $dip_chart_details->delete();
            
            DB::commit();

            $output = [
                'success' => true,
                'msg' => __('superadmin::lang.tank_dip_chart_delete_success')
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
     * Show the form for importing new resources.
     * @return Renderable
     */
    public function getImport()
    {
        $business_id = request()->session()->get('business.id');
        $units = Unit::where('business_id', $business_id)->pluck('actual_name', 'id');

        return view('superadmin::superadmin_settings.tank_dip_chart.import')->with(compact(
            'units'
        ));
    }

    /**
     * Store a newly imported resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function postImport(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        try {
            DB::beginTransaction();
            //Set maximum php execution time
            ini_set('max_execution_time', 0);

            if ($request->hasFile('tank_dip_chart_csv')) {
                $input = $request->only('date', 'sheet_name', 'tank_manufacturer', 'tank_capacity', 'unit_id');
                $input['date'] = !empty($input['date']) ? \Carbon::parse($input['date'])->format('Y-m-d') : date('Y-m-d');
                $input['business_id'] = $business_id;
                $tank_dip_cahrt = TankDipChart::create($input);



                $file = $request->file('tank_dip_chart_csv');
                $parsed_array = Excel::toArray([], $file);
                //Remove header row
                $imported_data = array_splice($parsed_array[0], 1);

                $formated_data = [];

                $is_valid = true;
                $error_msg = '';

                foreach ($imported_data as $key => $value) {
                    //Check if 2 no. of columns exists
                    if (count($value) < 2) {
                        $is_valid =  false;
                        $error_msg = "Number of columns mismatch";
                        break;
                    }

                    $row_no = $key + 1;
                    $tank_dip_chart_array = [];

                    //Check dip reading
                    if (!empty($value[0])) {
                        $tank_dip_chart_array['dip_reading'] = $value[0];
                    } else {
                        $is_valid =  false;
                        $error_msg = "dip reading is required in row no. $row_no";
                        break;
                    }
                    //Check dip reading value
                    if (!empty($value[1])) {
                        $tank_dip_chart_array['dip_reading_value'] = $value[1];
                    } else {
                        $is_valid =  false;
                        $error_msg = "dip reading value is required in row no. $row_no";
                        break;
                    }

                    $formated_data[] = $tank_dip_chart_array;
                }
                if (!$is_valid) {
                    throw new \Exception($error_msg);
                }

                if (!empty($formated_data)) {
                    foreach ($formated_data as $dip_reading_data) {


                        $data['tank_dip_chart_id'] =  $tank_dip_cahrt->id;
                        $data['dip_reading'] = $dip_reading_data['dip_reading'];
                        $data['dip_reading_value'] = $dip_reading_data['dip_reading_value'];

                        TankDipChartDetail::create($data);
                    }
                }

                $output = [
                    'success' => 1,
                    'msg' => __('product.file_imported_successfully')
                ];
            }


            DB::commit();

            $output = [
                'success' => true,
                'tank_dip_chart' => true,
                'msg' => __('superadmin::lang.tank_dip_chart_import_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tank_dip_chart' => true,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }
    public function getTankDipById($id)
    {
        // $tank_dip_chart = TankDipChart::leftjoin('units', 'tank_dip_charts.unit_id', 'units.id')->where('tank_dip_charts.id', $id)->first();
        // $tank_dip_chart->tank_capacity = number_format($tank_dip_chart->tank_capacity, 3, '.', '');
        $tank_dip_chart = TankDipChart::leftjoin('fuel_tanks', 'tank_dip_charts.tank_id', 'fuel_tanks.id')->where('tank_dip_charts.id', $id)->first();
        $tank_dip_chart->tank_capacity = number_format($tank_dip_chart->storage_volume, 3, '.', '');

        return $tank_dip_chart;
    }
    public function getDipReadingValue($id)
    {
        $tank_dip_chart_detail = TankDipChartDetail::findOrFail($id);

        return ['dip_reading_value' => number_format($tank_dip_chart_detail->dip_reading_value, 3, '.', '')];
    }
}
