<?php

namespace Modules\Petro\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;

use Yajra\DataTables\Facades\DataTables;
use App\Vehicle;
use App\FuelType;
use App\VehicleCategory;
use App\VehicleClassification;
use App\VehicleFuelQuota;






class VehicleController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //
    }
    public function show(Request $request) {
        die;
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'number' => 'required|unique:vehicles',
            'vehicle_category_id' => 'required',
            'fuel_type_id' => 'required',
            'name' => 'required',
            'district_id' => 'required',
            'town' => 'required',
            'mobile' => 'required|numeric',
            'password' => 'required',
            'passcode' => 'required',
            'created_at' => 'required',
        ]);
  
        if ($validator->fails()) {
            $output = [
                'success' => 0,
                'msg' => $validator->errors()->all()[0]
            ];
            // dd(redirect()->back()->with('status', $output));
            return redirect()->back()->with('status', $output);
        }
        try {
            $vehicle_details  = $request->except('_token');
            if($request->file('image')){
                $image = $request->file('image')->store('public');
                //Save url to image
                $vehicle_details['image'] = asset($image);
            }
            // dd($vehicle_details);
            $vehicle_details['password'] = Hash::make($vehicle_details['password']);
            $vehicleData = Vehicle::Create($vehicle_details );        

            // dd($vehicleData);
            $output = [
                'success' => 1,
                'msg' => 'Vehicle registered successfully'
            ];
            return view('petro::vehicle.print')->with(compact('vehicleData'));

            // return redirect()->back()->with('status', $vehicleData);

        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];
            return redirect()->back()->with('status', $output);
        }

    }

    public function index() {
    
    }

    public function vehicles_list() {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {

            $vehicle_arr = Vehicle::leftjoin('fuel_types', 'vehicles.fuel_type_id', 'fuel_types.id')
            ->leftjoin('vehicle_categories', 'vehicles.vehicle_category_id', 'vehicle_categories.id')
            ->leftjoin('districts', 'vehicles.district_id', 'districts.id')

            ->select('vehicles.id', 'vehicles.created_at', 'number', 'vehicle_categories.category as vehicle_category_id', 'fuel_types.fuel_sub_type as fuel_type', 'vehicles.name', 'vehicles.town', 'districts.name as district', 'mobile');

            return DataTables::of($vehicle_arr)
                ->addColumn(
                    'action',
                    '<button data-href ="{{url(\'vehicle/edit\', $id)}}" class="btn btn-info btn-xs update_vehicle" data-toggle="modal" data-target="#statusModal">Edit</button>'
                )
                ->editColumn('created_at', '@if(!empty($created_at)){{@format_date($created_at)}}@endif')
                ->rawColumns([0, 8])
                ->removeColumn('id')
                ->make(false);
        }

        return view('petro::vehicle.manage_vehicles');
    }

    public function edit($id) {
        if( !empty($id) ) {
            $vehicle_data = Vehicle::find($id);
            $currencies = [];

            return view('petro::vehicle.edit')
            ->with(compact('vehicle_data', 'currencies'));
        }
    }

    public function update(Request $request, $id) {
        try {
            if( !empty($id) ) {
                $vehicle_details  = $request->except('_token');
                $vehicle = Vehicle::find($id);

                foreach($vehicle_details as $key => $data) {
                    $vehicle->$key = $data;
                }

                $vehicle->save();

                $output = [
                    'success' => 1,
                    'msg' => __("lang_v1.updated_succesfully")
                ];

            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];
        }
        return $output;
    }

    public function petro_qouta_setting(){
        $vehicleCategories = VehicleCategory::pluck('category', 'id');
        $vehicleClassification = VehicleClassification::pluck('classification', 'id');
        $fuelReFillingCycle =  $this->fuelReFillingCycle();

      return view('petro::vehicle.petro_qouta_setting')->with(compact('vehicleCategories','vehicleClassification','fuelReFillingCycle'));
    }

    public function petro_qouta_setting_ajax(Request $request){

        $validator = Validator::make($request->all(), [
            'action'=>'required'
        ]);

        if ($validator->fails()) {
            $output = [
                'success' => 0,
                'msg' => $validator->errors()->all()[0]
            ];
            return redirect()->back()->with('status', $output);
        }

        switch($request->action){

            case 'list_fuel_type':
                $fuelCatArr = FuelType::select('id', 'fuel_type','fuel_sub_type');

                return DataTables::of($fuelCatArr)
                ->addColumn(
                    'action',
                    '<button data-href ="{{url(\'vehicle/edit\', $id)}}" class="btn btn-info btn-xs update_vehicle" data-toggle="modal" data-target="#statusModal">Edit</button>
                    <a data-href ="{{url(\'superadmin/petro-quota-setting/delete_fuel_type\',$id)}}" class="btn btn-danger btn-xs delete_fuel_type" data-toggle="modal" data-target="#statusModal">Delete</a>'
                )
                ->rawColumns(['action'])
                ->removeColumn('id')
                ->make(true);

                break;
       
            case 'list_vehicle_category':
                    $vehicleCatArr = VehicleCategory::select('id', 'category');
    
                    return DataTables::of($vehicleCatArr)
                    ->addColumn(
                        'action',
                        '<button data-href ="{{url(\'vehicle/edit\', $id)}}" class="btn btn-info btn-xs update_vehicle" data-toggle="modal" data-target="#statusModal">Edit</button>
                        <a data-href ="{{url(\'superadmin/petro-quota-setting/delete_vehicle_category\',$id )}}" class="btn btn-danger btn-xs delete_vehicle_category" data-toggle="modal" data-target="#statusModal">Delete</a>'
                    )
                    ->rawColumns(['action'])
                    ->removeColumn('id')
                    ->make(true);
    
                    break;   
            case 'list_vehicle_fuel_quota':
                    $vehicleFuelQuota = VehicleFuelQuota::leftjoin('vehicle_categories','vehicle_fuel_quota.vehicle_category_id','vehicle_categories.id')
                    ->leftjoin('vehicle_classifications','vehicle_fuel_quota.vehicle_classification_id','vehicle_classifications.id');
  
                    $vehicleFuelQuota->select('vehicle_fuel_quota.id', 'date','vehicle_categories.category as category','vehicle_classifications.classification as classification','fuel_litters_allowed','re_fill_cycle_in_hrs' );
                    
                    
                    $fuelReFillingCycle = $this->fuelReFillingCycle();
                    return DataTables::of($vehicleFuelQuota)
                    ->editColumn(
                        're_fill_cycle_in_hrs',
                        function($row) use($fuelReFillingCycle){
                            return $fuelReFillingCycle[$row->re_fill_cycle_in_hrs];
                        }
                    )->editColumn(
                        'classification',
                        function($row) {
                            if(empty($row->classification)){
                                return 'General';
                            }
                            return $row->classification;
                        }
                    )
                    ->addColumn(
                        'action',
                        '<button data-href ="{{url(\'vehicle/edit\', $id)}}" class="btn btn-info btn-xs update_vehicle" data-toggle="modal" data-target="#statusModal">Edit</button>
                        <a data-href ="{{url(\'superadmin/petro-quota-setting/delete_petro_fuel_quota\',$id )}}" class="btn btn-danger btn-xs delete_petro_fuel_quota" data-toggle="modal" data-target="#statusModal">Delete</a>'
                    )
                    ->rawColumns(['action'])
                    ->removeColumn('id')
                    ->make(true);
    
                    break;                                   
            case 'list_vehicle_classification':
                $vehicleClaArr = VehicleClassification::select('id', 'classification','created_at');

                if(!empty($request->filter_classification_by_date)){
                    $vehicleClaArr = VehicleClassification::where('vehicle_fuel_quota.created_at',$request->filter_classification_by_date)->select('id', 'classification','created_at');
                }

                return DataTables::of($vehicleClaArr)
                ->addColumn(
                    'action',
                    '<button data-href ="{{url(\'vehicle/edit\', $id)}}" class="btn btn-info btn-xs update_vehicle" data-toggle="modal" data-target="#statusModal">Edit</button>
                    <a data-href ="{{url(\'superadmin/petro-quota-setting/delete_vehicle_classification\',$id)}}" class="btn btn-danger btn-xs delete_vehicle_classification" data-toggle="modal" data-target="#statusModal">Delete</a>'
                )
                ->rawColumns(['action'])
                ->removeColumn('id')
                ->make(true);

                break;          

            case 'add_fuel_type':
                $validator = Validator::make($request->all(), [
                    'fuel_type'=>'required',
                    'fuel_sub_type'=>'required'

                ]);
        
                if ($validator->fails()) {
                    $output = [
                        'success' => 0,
                        'msg' => $validator->errors()->all()[0]
                    ];
                    return $output;

                }

                try {
                    $package_variables = array(
                        'fuel_type' => $request->fuel_type,
                        'fuel_sub_type' => $request->fuel_sub_type,
                    );
        
                    FuelType::create($package_variables);
        
        
                    $output = [
                        'success' => 1,
                        'msg' => __('vehicle.fuel_type_add'),
                    ];
        
                    return $output;
                } catch (\Exception $e) {
                    \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
        
                    $output = [
                        'success' => 0,
                        'msg' => __('messages.something_went_wrong'),
                        
                    ];
        
                    return $output;
                }
                break;
            case 'add_vehicle_category':
                    $validator = Validator::make($request->all(), [
                        'vehicle_category'=>'required',
    
                    ]);
            
                    if ($validator->fails()) {
                        $output = [
                            'success' => 0,
                            'msg' => $validator->errors()->all()[0]
                        ];
                        return $output;
    
                    }
    
                    try {
                        $package_variables = array(
                            'category' => $request->vehicle_category,
                        );            
                        VehicleCategory::create($package_variables);          
            
                        $output = [
                            'success' => 1,
                            'msg' => __('vehicle.vehicle_category_add'),
                        ];
            
                        return $output;
                    } catch (\Exception $e) {
                        \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            
                        $output = [
                            'success' => 0,
                            'msg' => __('messages.something_went_wrong'),
                            
                        ];
            
                        return $output;
                    }
                    break;
            case 'add_vehicle_classification':
                $validator = Validator::make($request->all(), [
                    'vehicle_classification'=>'required',

                ]);
        
                if ($validator->fails()) {
                    $output = [
                        'success' => 0,
                        'msg' => $validator->errors()->all()[0]
                    ];
                    return $output;

                }

                try {
                    $package_variables = array(
                        'classification' => $request->vehicle_classification,
                    );
        
                    VehicleClassification::create($package_variables);
        
        
                    $output = [
                        'success' => 1,
                        'msg' => __('vehicle.vehicle_classification_add'),
                    ];
        
                    return $output;
                } catch (\Exception $e) {
                    \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
        
                    $output = [
                        'success' => 0,
                        'msg' => __('messages.something_went_wrong'),
                        
                    ];
        
                    return $output;
                }
                break;        
            case 'add_petro_fuel_quota' :
                $validator = Validator::make($request->all(), [
                    'date'=>'required|date',
                    'vehicle_category_id'=>'required|int',
                    'fuel_litters_allowed'=>'required',
                    're_fill_cycle_in_hrs'=>'required',
                ]);
        
                if ($validator->fails()) {
                    $output = [
                        'success' => 0,
                        'msg' => $validator->errors()->all()[0]
                    ];
                    return $output;

                }

                try {
                    $petro_fuel_quota = array(
                        'date'=>$request->date,
                        'vehicle_category_id'=>$request->vehicle_category_id,
                        'vehicle_classification_id'=>$request->vehicle_classification_id,
                        'fuel_litters_allowed'=>$request->fuel_litters_allowed,
                        're_fill_cycle_in_hrs'=>$request->re_fill_cycle_in_hrs,
                    );        
                    VehicleFuelQuota::create($petro_fuel_quota);
                    $output = [
                        'success' => 1,
                        'msg' => __('vehicle.petro_fuel_quota_added'),
                    ];
        
                    return $output;
                } catch (\Exception $e) {
                    \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
        
                    $output = [
                        'success' => 0,
                        'msg' => __('messages.something_went_wrong'),
                        
                    ];
        
                    return $output;
                }
                break;                

        }


    }

    public function petro_qouta_setting_destory($type,$id){
        $output = [
            'success' => false,
            'msg' => __("messages.something_went_wrong")
        ];
        try {
            switch($type){

                case 'delete_fuel_type':
                    FuelType::find($id)->delete();
                    $output = [
                        'success' => true,
                        'msg' => __("vehicle.fuel_type_deleted_success")
                    ];
                    break;
    
                case 'delete_vehicle_category':
                    VehicleCategory::find($id)->delete();
                    $output = [
                        'success' => true,
                        'msg' => __("vehicle.vehicle_cat_deleted_success")
                    ];
                    break;
    
                case 'delete_vehicle_classification':
                    VehicleClassification::find($id)->delete();
                    $output = [
                        'success' => true,
                        'msg' => __("vehicle.vehicle_cla_deleted_success")
                    ];
                    break;  
                    
                case 'delete_petro_fuel_quota':    
                    VehicleFuelQuota::find($id)->delete();
                    $output = [
                        'success' => true,
                        'msg' => __("vehicle.petro_fuel_quota_deleted")
                    ];
                    break;  
            }
            
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => true,
                'msg' => $e->getMessage()
            ];

        }
        
     
        return $output; 

    }

    /**
     * print resources
     * @param settlement_id
     * @return Response
     */
    public function print($vehicle)
    {
        

        return view('petro::vehicle.print')->with(compact('vehicle'));
    }

    private function fuelReFillingCycle (){
        return [
            '0'=>'No Limit' ,
            '1' => '1 Hrs'
            ,'2' => '2 Hrs'
            ,'3' => '3 Hrs'
            ,'4' => '4 Hrs'
            ,'5' => '5 Hrs'
            ,'6' => '6 Hrs'
            ,'7' => '7 Hrs'
            ,'8' => '8 Hrs'
            ,'9' => '9 Hrs'
            ,'10' => '10 Hrs'
            ,'11' => '11 Hrs'
            ,'12' => '12 Hrs'
        ];
    }


    
}
