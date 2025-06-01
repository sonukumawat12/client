<?php

namespace Modules\Fleet\Http\Controllers;

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
use Modules\Fleet\Entities\FleetLogo;
use Modules\Fleet\Entities\RouteOperation;
use Yajra\DataTables\Facades\DataTables;
use App\System;
use Intervention\Image\Facades\Image;

class FleetLogoController extends Controller
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

            $drivers = FleetLogo::leftjoin('users','users.id','fleet_logos.created_by')->where('fleet_logos.business_id',$business_id)->select(['users.username','fleet_logos.*']);
            
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
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\FleetLogoController@edit', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\FleetLogoController@destroy', [$row->id]) . '" class="delete_button"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        
                        
                        return $html;
                    }
                )
                ->editColumn('logo', function ($row) {
                    $action = '';
                    

                    if (!empty($row->logo)) {
                        if (strpos($row->logo, 'jpg') || strpos($row->logo, 'jpeg') || strpos($row->logo, 'png')) {
                            $action = '<a href="#"
                            data-href="' . action("AccountController@imageModal", ["title" => "View", "url" => url($row->logo)]) . '"
                            class="btn-modal btn-xs btn btn-primary"
                            data-container=".view_modal">' . __("messages.view") . '</a>';
                        }
                    }
                    return $action;
                })
                ->editColumn('created_at', '{{@format_date($created_at)}}')
                ->removeColumn('id')
                ->rawColumns(['action','logo'])
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
        

        return view('fleet::settings.fleet_logos.create')->with(compact(
            'business_id'
        ));
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
            $data = $request->except('_token','attachment');
            $data['business_id'] = $business_id;
            $data['created_by'] = Auth::user()->id;
            
            //upload file
            if (!file_exists('./public/img/fleet_logos/' . $business_id)) {
                mkdir('./public/img/fleet_logos/' . $business_id, 0777, true);
            }
            if ($request->hasfile('attachment')) {
                $image_width = (int) System::getProperty('upload_image_width');
                $image_hieght = (int) System::getProperty('upload_image_height');
                $file = $request->file('attachment');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $file->move('public/img/fleet_logos/' . $business_id . '/', $filename);
                $uploadFile = 'public/img/fleet_logos/' . $business_id . '/' . $filename;
                $data['logo'] = $uploadFile;
            }

            
            
            FleetLogo::create($data);

            $output = [
                'success' => true,
                'tab' => 'fleet_logos',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'fleet_logos',
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
        $business_id = request()->session()->get('business.id');
        $driver = FleetLogo::find($id);
        
        return view('fleet::settings.fleet_logos.show')->with(compact(
            'driver'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $driver = FleetLogo::find($id);

        return view('fleet::settings.fleet_logos.edit')->with(compact(
            'driver'
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
        $business_id = request()->session()->get('business.id');
        
        try {
            $data = $request->except('_token', '_method','attachment');
            
            if (!file_exists('./public/img/fleet_logos/' . $business_id)) {
                mkdir('./public/img/fleet_logos/' . $business_id, 0777, true);
            }
            if ($request->hasfile('attachment')) {
                $image_width = (int) System::getProperty('upload_image_width');
                $image_hieght = (int) System::getProperty('upload_image_height');
                $file = $request->file('attachment');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $file->move('public/img/fleet_logos/' . $business_id . '/', $filename);
                $uploadFile = 'public/img/fleet_logos/' . $business_id . '/' . $filename;
                $data['logo'] = $uploadFile;
            }

            
            FleetLogo::where('id', $id)->update($data);

            $output = [
                'success' => true,
                'tab' => 'fleet_logos',
                'msg' => __('lang_v1.success')
            ];
            
            
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'fleet_logos',
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
            FleetLogo::where('id', $id)->delete();

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
