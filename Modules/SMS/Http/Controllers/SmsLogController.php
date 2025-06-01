<?php

namespace Modules\SMS\Http\Controllers;

use App\Member;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Member\Entities\Balamandalaya;
use Modules\Member\Entities\GramasevaVasama;
use Modules\SMS\Entities\SmsList;
use Yajra\DataTables\Facades\DataTables;

use App\SmsLog;

class SmsLogController extends Controller
{
    protected $businessUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param Util $businessUtil
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->businessUtil = $businessUtil;
        $this->moduleUtil =  $moduleUtil;
    }


    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            
            $drivers =SmsLog::where('business_id',$business_id)->orderBy('id','DESC');
            
            
            if(!empty(request()->start_date) && !empty(request()->end_date)){
                $drivers->whereDate('created_at','>=',request()->start_date)->whereDate('created_at','<=',request()->end_date);
            }
            
            
            if(!empty(request()->username)){
                $drivers->where('username',request()->username);
            }
            
            if(!empty(request()->sender_name)){
                $drivers->where('sender_name',request()->sender_name);
            }
            
            if(!empty(request()->sms_status)){
                $drivers->where('sms_status',request()->sms_status);
            }
            
            if(!empty(request()->sms_type_)){
                $drivers->where('sms_type_',request()->sms_type_);
            }
            
            return DataTables::of($drivers)
                
                ->editColumn('message',function($row){
                    $html = "<button class='btn btn-primary msg_btn btn-sm' data-string='".nl2br($row->message)."'>".__('superadmin::lang.message')."</button>";
                    return $html;
                })
                ->editColumn('created_at','{{@format_datetime($created_at)}}')
                ->rawColumns(['action','message'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
       
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('sms::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }

    
}
