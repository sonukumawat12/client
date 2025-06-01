<?php

namespace Modules\Superadmin\Http\Controllers;

use Modules\Superadmin\Entities\MapBusinessTank;
use Illuminate\Http\Request;
use App\Utils\TransactionUtil;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\Superadmin\Entities\TankDipChart;

use Modules\Superadmin\Entities\SmsRefillPackage;
use App\Business;
use Illuminate\Support\Str;


class MapBusinessTankController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $transactionUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil)
    {
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
       if (request()->ajax()) {
            
            $drivers = MapBusinessTank::leftjoin('tank_dip_charts','map_business_tanks.sheet_id','tank_dip_charts.id')
                        ->leftjoin('fuel_tanks', 'tank_dip_charts.tank_id', 'fuel_tanks.id')
                        ->leftjoin('users','users.id','map_business_tanks.created_by')
                        ->leftjoin('business','business.id','map_business_tanks.business_id')
                        ->select('business.name as business_name','tank_dip_charts.sheet_name','fuel_tanks.storage_volume as tank_capacity','fuel_tanks.tank_manufacturer','map_business_tanks.*','users.username');
            
            if(!empty(request()->business_id)){
                $drivers->where('map_business_tanks.business_id',request()->business_id);
            }
            
            if(!empty(request()->sheet_name)){
                $drivers->where('sheet_id',request()->sheet_name);
            }
            
            if(!empty(request()->manufacturer)){
                $drivers->where('sheet_id',request()->manufacturer);
            }
            
            return DataTables::of($drivers)
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
                        
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Superadmin\Http\Controllers\MapBusinessTankController@edit', [$row->id]) . '" class="btn-modal" data-container=".tank_dip_chart_model"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Superadmin\Http\Controllers\MapBusinessTankController@destroy', [$row->id]) . '" class="delete_mbt"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                       
                        
                        return $html;
                    }
                )
                ->editColumn('tank_capacity','{{@num_format($tank_capacity)}}')
                ->make(true);
        }
        
    }
    
    
    public function create()
    {
        $sheet_names = TankDipChart::all();
        $businesses = Business::where('is_active', 1)->pluck('name', 'id');
        
        return view('superadmin::superadmin_settings.map_business_tanks.create')
                ->with(compact('sheet_names','businesses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
        try {
            							
            $data = $request->except('_token');
            $data['created_by'] = auth()->user()->id;
            
            MapBusinessTank::create($data);
            
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
                'tab' => 'external_api_clients'
            ];
            
        } catch (\Exception $e) {

            logger($e);
            
            $output = [

                'success' => 0,

                'msg' => __('messages.something_went_wrong'),
                'tab' => 'external_api_clients'

            ];

        }
        

        return $output;
    }
    
    public function edit($id)
    {
    
        $sheet_names = TankDipChart::all();
        $data = MapBusinessTank::findOrFail($id);
        $businesses = Business::where('is_active', 1)->pluck('name', 'id');
        return view('superadmin::superadmin_settings.map_business_tanks.edit')
                ->with(compact('data','sheet_names','businesses'));
    }

   

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        
        
        try {
            $data = $request->except('_token','_method');
            
            MapBusinessTank::where('id',$id)->update($data);
            
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
                'tab' => 'external_api_clients'
            ];
            
        } catch (\Exception $e) {

            logger($e);
            
            $output = [

                'success' => 0,

                'msg' => __('messages.something_went_wrong'),
                'tab' => 'external_api_clients'

            ];

        }
        

        return $output;
    }
    
    public function destroy($id)
    {
        try {
            
            MapBusinessTank::where('id', $id)->delete();


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
