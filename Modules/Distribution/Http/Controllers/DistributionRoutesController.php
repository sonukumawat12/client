<?php

namespace Modules\Distribution\Http\Controllers;

use App\Transaction;
use App\TransactionPayment;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Distribution\Entities\Helper;
use Modules\Distribution\Entities\Distribution_provinces;
use Modules\Distribution\Entities\Distribution_districts;
use Modules\Distribution\Entities\Distribution_areas;
use Modules\Distribution\Entities\Distribution_routes;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class DistributionRoutesController extends Controller
{
    protected $commonUtil;
    protected $moduleUtil;
    protected $productUtil;
    protected $transactionUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil =  $moduleUtil;
        $this->productUtil =  $productUtil;
        $this->transactionUtil =  $transactionUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $helpers = Distribution_routes::leftJoin('distribution_districts', function($join) {
                $join->on(DB::raw("JSON_CONTAINS(distribution_routes.district_id, CONCAT('[\"', distribution_districts.id, '\"]'))"), '=', DB::raw("1"));
            })
            ->leftJoin('distribution_areas', function($join) {
                $join->on(DB::raw("JSON_CONTAINS(distribution_routes.area_id, CONCAT('[\"', distribution_areas.id, '\"]'))"), '=', DB::raw("1"));
            })
            ->leftJoin('distribution_provinces', function($join) {
                $join->on(DB::raw("JSON_CONTAINS(distribution_routes.province_id, CONCAT('[\"', distribution_provinces.id, '\"]'))"), '=', DB::raw("1"));
            })
            ->leftJoin('users', 'distribution_provinces.added_by', '=', 'users.id')
            ->where('distribution_routes.business_id', $business_id);
            


            if (!empty(request()->province_id)) {
                $helpers->where('distribution_provinces.id', request()->province_id);
            }
            if (!empty(request()->district_id)) {
                $helpers->where('distribution_districts.id', request()->district_id);
            }
            
            if (!empty(request()->area_id)) {
                $helpers->where('distribution_areas.id', request()->area_id);
            }
            
            $helpers = $helpers->select([
                'distribution_routes.*',
                'users.username as user_names',
                DB::raw("GROUP_CONCAT(DISTINCT distribution_districts.name SEPARATOR ', ') as district_name"),
                DB::raw("GROUP_CONCAT(DISTINCT distribution_provinces.name SEPARATOR ', ') as province_name"),
                DB::raw("GROUP_CONCAT(DISTINCT distribution_areas.name SEPARATOR ', ') as area_name")
            ])->groupBy('distribution_routes.id');
            
            return DataTables::of($helpers)
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
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Distribution\Http\Controllers\DistributionRoutesController@edit', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Distribution\Http\Controllers\DistributionRoutesController@destroy', [$row->id]) . '" class="delete_button"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        
                        return $html;
                    }
                )
                ->editColumn('date', '{{@format_date($created_at)}}')
                ->editColumn('route_no', '{{str_pad($id,4,"0",STR_PAD_LEFT)}}')
                ->removeColumn('id')
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
        
        $business_id = request()->session()->get('user.business_id');
        
        $route = Distribution_routes::orderBy('id','DESC')->first();
        
        $id = !empty($route) ? ($route->id + 1) : 1;
        
        $id = str_pad($id,4,"0",STR_PAD_LEFT);

        $provinces = Distribution_provinces::where('business_id', $business_id)->pluck('name', 'id');
        $districts = Distribution_districts::leftjoin('distribution_provinces','distribution_districts.province_id','distribution_provinces.id')->where('distribution_provinces.business_id', $business_id)->pluck('distribution_districts.name', 'distribution_districts.id', 'distribution_districts.province_id');
        $areas = Distribution_areas::leftjoin('distribution_districts','distribution_areas.district_id','distribution_districts.id')
                ->leftjoin('distribution_provinces','distribution_districts.province_id','distribution_provinces.id')
                ->leftjoin('users', 'distribution_provinces.added_by', 'users.id')
                ->where('distribution_provinces.business_id', $business_id)
                ->pluck('distribution_areas.name','distribution_areas.id','distribution_areas.district_id');
                
        return view('distribution::settings.routes.create')->with(compact(
            'provinces','districts','areas','id'
        ));
    }
    
    public function getProvincesDistricts($pid){
        $districts = Distribution_districts::leftjoin('distribution_provinces','distribution_districts.province_id','distribution_provinces.id');
        if($pid > 0){
           $districts->where('distribution_provinces.id', $pid);
        }
        
        $districts = $districts->select('distribution_districts.name', 'distribution_districts.id', 'distribution_districts.province_id')->get();
        
        $html = "<option value=''>All</option>";
        foreach($districts as $one){
            $html .= "<option value='".$one->id."'>".$one->name."</option>";
        }
        
         return response()->json(['options' => $html]);
    }
    
    public function getDistrictAreas($did){
        $areas = Distribution_areas::leftjoin('distribution_districts','distribution_areas.district_id','distribution_districts.id')
                ->leftjoin('distribution_provinces','distribution_districts.province_id','distribution_provinces.id')
                ->leftjoin('users', 'distribution_provinces.added_by', 'users.id');
        if($did > 0){
          $areas->where('distribution_districts.id', $did);  
        }    
        $areas = $areas->select('distribution_areas.name','distribution_areas.id','distribution_areas.district_id')->get();
        $html = "<option value=''>All</option>";
        foreach($areas as $one){
            $html .= "<option value='".$one->id."'>".$one->name."</option>";
        }
        
         return response()->json(['options' => $html]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        try {
            $data = $request->except('_token');
            $data['added_by'] = Auth::user()->id;
            
            $data['province_id'] = json_encode($data['province_id']);
            $data['district_id'] = json_encode($data['district_id']);
            $data['area_id'] = json_encode($data['area_id']);

            
            Distribution_routes::create($data);

            $output = [
                'success' => true,
                'tab' => 'helpers',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'helpers',
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with(['status'=> $output,"page" => "routes"]);
    }

   

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $route = Distribution_routes::find($id);
        
        $business_id = request()->session()->get('user.business_id');

        $provinces = Distribution_provinces::where('business_id', $business_id)->pluck('name', 'id');
        $districts = Distribution_districts::leftjoin('distribution_provinces','distribution_districts.province_id','distribution_provinces.id')->where('distribution_provinces.business_id', $business_id)->pluck('distribution_districts.name', 'distribution_districts.id', 'distribution_districts.province_id');
        $areas = Distribution_areas::leftjoin('distribution_districts','distribution_areas.district_id','distribution_districts.id')
                ->leftjoin('distribution_provinces','distribution_districts.province_id','distribution_provinces.id')
                ->leftjoin('users', 'distribution_provinces.added_by', 'users.id')
                ->where('distribution_provinces.business_id', $business_id)
                ->pluck('distribution_areas.name','distribution_areas.id','distribution_areas.district_id');
                

        return view('distribution::settings.routes.edit')->with(compact(
            'route','provinces','districts','areas'
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
            $data = $request->except('_token', '_method');
            
            $data['province_id'] = json_encode($data['province_id']);
            $data['district_id'] = json_encode($data['district_id']);
            $data['area_id'] = json_encode($data['area_id']);
            
            Distribution_routes::where('id', $id)->update($data);

            $output = [
                'success' => true,
                'tab' => 'helpers',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'helpers',
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with(['status'=> $output,"page" => "routes"]);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {

            Distribution_routes::where('id', $id)->delete();

            
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
