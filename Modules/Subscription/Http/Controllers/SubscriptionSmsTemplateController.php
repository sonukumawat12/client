<?php

namespace Modules\Subscription\Http\Controllers;


use App\Utils\ModuleUtil;
;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Modules\Subscription\Entities\SubscriptionSmsTemplate;
use Modules\Subscription\Entities\SubscriptionSetting;
use Modules\Subscription\Entities\SubscriptionPrice;
use Yajra\DataTables\DataTables;

class SubscriptionSmsTemplateController extends Controller
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
        $business_id = request()->session()->get('business.id');
        
        $templates = SubscriptionSmsTemplate::where('business_id',$business_id)->first();

        return view('subscription::templates.index')->with(compact('business_id','templates'));
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
            $business_id = request()->session()->get('business.id');
            
            $data = $request->only(['sms_body','days_1','days_2','days_3','days_4']);
            $data['business_id'] = $business_id;
            
            $data['days_1_status'] = $request->days_1_status ?? 0;
            $data['days_2_status'] = $request->days_2_status ?? 0;
            $data['days_3_status'] = $request->days_3_status ?? 0;
            $data['days_4_status'] = $request->days_4_status ?? 0;
            
            
            SubscriptionSmsTemplate::updateOrCreate(['business_id' => $business_id],$data);

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
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
        return view('subscription::templates.show');
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
