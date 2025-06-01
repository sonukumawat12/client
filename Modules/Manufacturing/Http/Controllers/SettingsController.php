<?php

namespace Modules\Manufacturing\Http\Controllers;

use Auth;
use App\Business;
use App\System;
use App\Product;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Manufacturing\Utils\ManufacturingUtil;
use Modules\Manufacturing\Entities\MfgSettings;
use Yajra\DataTables\Facades\DataTables;
use DB;

class SettingsController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $mfgUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil, ManufacturingUtil $mfgUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->mfgUtil = $mfgUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'mf_module'))) {
            abort(403, 'Unauthorized action.');
        }
        $manufacturing_settings = $this->mfgUtil->getSettings($business_id);

        $version = System::getProperty('manufacturing_version');
        return view('manufacturing::settings.index')->with(compact('manufacturing_settings', 'version'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'mf_module'))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $settings = $request->only(['ref_no_prefix']);

            $settings['disable_editing_ingredient_qty'] = !empty($request->input('disable_editing_ingredient_qty')) ? true : false;

            $settings['enable_updating_product_price'] = !empty($request->input('enable_updating_product_price')) ? true : false;
            
            $business = Business::where('id', $business_id)
                                ->update(['manufacturing_settings' => json_encode($settings)]);

            $output = ['success' => 1,
                            'msg' => __("lang_v1.updated_success")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return redirect()->back()->with('status', $output);
    }

    public function saveWastage(Request $request){

       
        try {
            $name=$request->wastage_name;
            $data=[
                'type'=>'wastage',
                'name'=>$name,
                'created_by'=>Auth::user()->id
            ];
    
            MfgSettings::insert($data);
    
            $output = ['success' => 1,
                            'msg' => __("lang_v1.added_success")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return response($output);
   
    }
    public function saveExtraCost(Request $request){

       
        try {
            $name=$request->extracost_name;
            $data=[
                'type'=>'extracost',
                'name'=>$name,
                'created_by'=>Auth::user()->id
            ];
    
            MfgSettings::insert($data);
    
            $output = ['success' => 1,
                            'msg' => __("lang_v1.added_success")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return response($output);
   
    }
    public function saveByProducts(Request $request){

       
        try {
            $name=$request->by_products_name;
            $data=[
                'type'=>'by_products',
                'name'=>$name,
                'created_by'=>Auth::user()->id
            ];
    
            MfgSettings::insert($data);
    
            $output = ['success' => 1,
                            'msg' => __("lang_v1.added_success")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return response($output);
   
    }
    public function enableItem(Request $request){

       
        try {
            $id=$request->id;
            $data=[
                'is_active'=>'1',
            ];
    
            MfgSettings::where('id',$id)->update($data);
    
            $output = ['success' => 1,
                            'msg' => __("lang_v1.updated_success")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return response($output);
   
    }
    public function disableItem(Request $request){
     
       
        try {
            $id=$request->id;
            $data=[
                'is_active'=>'0',
            ];
    
            MfgSettings::where('id',$id)->update($data);
    
            $output = ['success' => 1,
                            'msg' => __("lang_v1.updated_success")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return response($output);
   
    }


    public function getWastage()
    {
        if (request()->ajax()) {
            $wastages=MfgSettings::select('mfg_settings.*','u.username')->join('users as u','u.id','mfg_settings.created_by')->where('type','wastage')->get();
            return DataTables::of($wastages)
               
                ->editColumn('date', '{{date("Y-m-d",strtotime($created_at))}}')
                ->editColumn('name', '{{$name}}')
                ->editColumn('user', '{{$username}}')
                ->addColumn(
                    'action',
                    function ($row) {

                        if($row->is_active=='1'){
                            $html='<button type="button" onclick="disableWastageItem('.$row->id.')" class="btn btn-danger" aria-label="Left Align">
                            Disable</button>';
                        } else {
                            $html='<button type="button" onclick="enableWastageItem('.$row->id.')" class="btn btn-primary" aria-label="Left Align">
                            Enable</button>';
                        }

                        return $html;
                        
                    }
                )->rawColumns(['action'])
                ->make(true);
        }
    }
    public function getExtraCost()
    {
        if (request()->ajax()) {
            $wastages=MfgSettings::select('mfg_settings.*','u.username')->join('users as u','u.id','mfg_settings.created_by')->where('type','extracost')->get();
            return DataTables::of($wastages)
               
                ->editColumn('date', '{{date("Y-m-d",strtotime($created_at))}}')
                ->editColumn('name', '{{$name}}')
                ->editColumn('user', '{{$username}}')
                ->addColumn(
                    'action',
                    function ($row) {

                        if($row->is_active=='1'){
                            $html='<button type="button" onclick="disableExtraCostItem('.$row->id.')" class="btn btn-danger" aria-label="Left Align">
                            Disable</button>';
                        } else {
                            $html='<button type="button" onclick="enableExtraCostItem('.$row->id.')" class="btn btn-primary" aria-label="Left Align">
                            Enable</button>';
                        }

                        return $html;
                        
                    }
                )->rawColumns(['action'])
                ->make(true);
        }
    }
    public function getByProducts()
    {
        if (request()->ajax()) {
            $wastages=MfgSettings::select('mfg_settings.*','u.username')->join('users as u','u.id','mfg_settings.created_by')->where('type','by_products')->get();
            return DataTables::of($wastages)
               
                ->editColumn('date', '{{date("Y-m-d",strtotime($created_at))}}')
                ->editColumn('name', '{{$name}}')
                ->editColumn('user', '{{$username}}')
                ->addColumn(
                    'action',
                    function ($row) {

                        if($row->is_active=='1'){
                            $html='<button type="button" onclick="disableByProductsItem('.$row->id.')" class="btn btn-danger" aria-label="Left Align">
                            Disable</button>';
                        } else {
                            $html='<button type="button" onclick="enableByProductsItem('.$row->id.')" class="btn btn-primary" aria-label="Left Align">
                            Enable</button>';
                        }

                        return $html;
                        
                    }
                )->rawColumns(['action'])
                ->make(true);
        }
    }
    
    // new add by rmtemplate
    public function getByLotNumbers() {
        if (request()->ajax()) {
            $wastages=MfgSettings::select('mfg_settings.*','u.username')->join('users as u','u.id','mfg_settings.created_by')->where('type','by_lot_numbers')->get();
            return DataTables::of($wastages)
               
                ->editColumn('date', '{{date("Y-m-d",strtotime($created_at))}}')
                ->editColumn('product', function($row) {
                    $dataProduct = Product::where('id', $row->id_product)->first();
                    
                    return $dataProduct->name . ' ' . '('. $dataProduct->sku . ')';
                })
                ->editColumn('lot_prefix', '{{$lot_prefix}}')
                ->editColumn('lot_no', '{{$lot_no}}')
                ->editColumn('user', '{{$username}}')
                ->addColumn(
                    'action',
                    function ($row) {

                        if($row->is_active=='1'){
                            $html='<button type="button" onclick="disableByLotNumbersItem('.$row->id.')" class="btn btn-danger" aria-label="Left Align">
                            Disable</button>';
                        } else {
                            $html='<button type="button" onclick="enableByLotNumbersItem('.$row->id.')" class="btn btn-primary" aria-label="Left Align">
                            Enable</button>';
                        }

                        return $html;
                        
                    }
                )->rawColumns(['action'])
                ->make(true);
        }
    }
    
    public function saveByLotNumbers(Request $request){

       
        try {
            $product=$request->id_product;
            $lot_prefix=$request->lot_prefix;
            $lot_no=$request->lot_no;
            $data=[
                'type'=>'by_lot_numbers',
                'id_product'=>$product,
                'lot_prefix'=>$lot_prefix,
                'lot_no'=>$lot_no,
                'created_by'=>Auth::user()->id
            ];
    
            MfgSettings::insert($data);
    
            $output = ['success' => 1,
                            'msg' => __("lang_v1.added_success")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return response($output);
   
    }
    
    public function getByProductsActive()
    {
        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');
                $dataProduct = Product::latest()->get();
                
                $output = [
                    'data' => $dataProduct];
            } catch(\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }
            
            return response($output);
        }
    }
    
    // end added by rmtemplate
}
