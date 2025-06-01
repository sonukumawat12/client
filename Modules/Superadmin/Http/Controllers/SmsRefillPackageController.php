<?php

namespace Modules\Superadmin\Http\Controllers;

use Modules\Superadmin\Entities\SmsRefillPackage;
use Illuminate\Http\Request;
use App\Utils\ModuleUtil;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Business;
use App\User;
use Modules\Superadmin\Entities\RefillBusiness;
use Modules\Superadmin\Entities\SmsApiClient;
use Illuminate\Support\Facades\DB;
use App\SmsLog;
use Modules\Superadmin\Entities\SmsReminderSetting;

class SmsRefillPackageController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        if (request()->ajax()) {
            
            $drivers = SmsRefillPackage::leftjoin('users', 'users.id', 'sms_refill_packages.created_by')
                ->select([
                    'sms_refill_packages.*',
                    'users.username',
                ]);

            
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
                        
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Superadmin\Http\Controllers\SmsRefillPackageController@edit', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Superadmin\Http\Controllers\SmsRefillPackageController@destroy', [$row->id]) . '" class="delete_record"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        
                        $html .= '<li class="divider"></li>';
                        
                        return $html;
                    }
                )
                ->editColumn('unit_cost','{{@num_format($unit_cost)}}')
                ->editColumn('date','{{@format_date($date)}}')
                ->editColumn('no_of_sms','{{@num_format($no_of_sms)}}')
                ->editColumn('amount','{{@num_format($amount)}}')
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }
        
        $data = array();
        
        
        $payment_methods = RefillBusiness::distinct('payment_method')->pluck('payment_method','id');
        $packages = SmsRefillPackage::pluck('name','id');
        $users = User::pluck('username','id');
        
        $drivers = Business::select(DB::raw("'business' as type"),'business.id as id','business.name as name');
        $external = SmsApiClient::select(DB::raw("'client' as type"),'sms_api_clients.id as id','sms_api_clients.name as name');
        $business = $drivers->unionAll($external)->get();
        
        
        $usernames = SmsLog::whereNotNull('username')->distinct('username')->pluck('username','username');
        $sender_names = SmsLog::whereNotNull('sender_name')->distinct('sender_name')->pluck('sender_name','sender_name');
        $sms_type = SmsLog::whereNotNull('sms_type_')->distinct('sms_type_')->pluck('sms_type_','sms_type_');
        $sms_status = SmsLog::whereNotNull('sms_status')->distinct('sms_status')->pluck('sms_status','sms_status');
        
        $templates = SmsReminderSetting::first();
        
        return view('superadmin::sms_refill_packages.index')
                ->with(compact('data','business','payment_methods','packages','users','usernames','sender_names','sms_type','sms_status','templates'));
    }

    public function create()
    {
        $data = array();
        return view('superadmin::sms_refill_packages.create')
                ->with(compact('data'));
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
            $data = array('date' => $request->date,'name' => $request->name,'unit_cost' => $request->unit_cost,'amount' => $request->amount,'no_of_sms' => $request->no_of_sms);
            $data['created_by'] = auth()->user()->id;
            
            SmsRefillPackage::create($data);
            
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
            
        } catch (\Exception $e) {

            logger($e);
            
            $output = [

                'success' => 0,

                'msg' => __('messages.something_went_wrong')

            ];

        }
        

        return redirect()->back()->with('status', $output);;
    }
    
    public function edit($id)
    {
        $data = SmsRefillPackage::findOrFail($id);
        return view('superadmin::sms_refill_packages.edit')
                ->with(compact('data'));
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
            $data = array('date' => $request->date,'name' => $request->name,'unit_cost' => $request->unit_cost,'amount' => $request->amount,'no_of_sms' => $request->no_of_sms);
            $data['created_by'] = auth()->user()->id;
            
            SmsRefillPackage::where('id',$id)->update($data);
            
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
            
        } catch (\Exception $e) {

            logger($e);
            
            $output = [

                'success' => 0,

                'msg' => __('messages.something_went_wrong')

            ];

        }
        

        return redirect()->back()->with('status', $output);;
    }
    
    public function destroy($id)
    {
        try {
            
            SmsRefillPackage::where('id', $id)->delete();


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
