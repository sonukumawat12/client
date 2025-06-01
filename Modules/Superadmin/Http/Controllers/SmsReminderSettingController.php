<?php

namespace Modules\Superadmin\Http\Controllers;


use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Modules\Superadmin\Entities\SmsReminderSetting;
use Yajra\DataTables\DataTables;

class SmsReminderSettingController extends Controller
{
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
     * @return Response
     */
    public function index()
    {
        
        $templates = SmsReminderSetting::first();

        return view('superadmin::templates.index')->with(compact('business_id','templates'));
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
        try {
        
            $data = $request->only(['sms_body','days_1','days_2','days_3','days_4']);
            
            $data['days_1_status'] = $request->days_1_status ?? 0;
            $data['days_2_status'] = $request->days_2_status ?? 0;
            $data['days_3_status'] = $request->days_3_status ?? 0;
            $data['days_4_status'] = $request->days_4_status ?? 0;
            
            
            $existingRecord = SmsReminderSetting::first();
            
            if ($existingRecord) {
                $existingRecord->update($data);
            } else {
                SmsReminderSetting::create($data);
            }

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
                'tab' => 'sms_reminder_settings'
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }
    
    
    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('superadmin::templates.show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request,$id)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
       
    }
}
