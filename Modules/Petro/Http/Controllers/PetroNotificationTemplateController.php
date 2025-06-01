<?php

namespace Modules\Petro\Http\Controllers;

use Modules\Petro\Entities\PetroNotificationTemplate;
use Illuminate\Http\Request;
use App\Utils\ModuleUtil;
use Illuminate\Routing\Controller;


class PetroNotificationTemplateController extends Controller
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
        if (!auth()->user()->can('petro_sms_notification')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        
        $notifications = PetroNotificationTemplate::notifications();

        $notifications = $this->__getTemplateDetails($notifications);
        
        
        return view('petro::notification_template.index')
                ->with(compact('notifications'));
    }

    private function __getTemplateDetails($notifications)
    {
        $business_id = request()->session()->get('user.business_id');
        foreach ($notifications as $key => $value) {
            $notification_template = PetroNotificationTemplate::getTemplate($business_id, $key);
            $notifications[$key]['sms_body'] = $notification_template['sms_body'];
            $notifications[$key]['auto_send_sms'] = $notification_template['auto_send_sms'];
            $notifications[$key]['template_for'] = $notification_template['template_for'];
            $notifications[$key]['phone_nos'] = $notification_template['phone_nos'];
        }
        return $notifications;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('petro_sms_notification')) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            
            $template_data = $request->input('template_data');
            $business_id = request()->session()->get('user.business_id');
    
            foreach ($template_data as $key => $value) {
                
                $not_data = [
                        'auto_send_sms' => !empty($value['auto_send_sms']) ? 1 : 0
                    ];
                
                
                if(!empty($value['sms_body'])){
                    $not_data['sms_body'] = $value['sms_body'];
                }
                
                if(!empty($value['phone_nos'])){
                    $not_data['phone_nos'] = $value['phone_nos'];
                }
                
                
                
                PetroNotificationTemplate::updateOrCreate(
                    [
                        'business_id' => $business_id,
                        'template_for' => $key
                    ],$not_data
                );
                
                $output = [

                    'success' => 1,
    
                    'msg' => __('messages.success')
    
                ];
            }
            
        } catch (\Exception $e) {

            logger($e);
            
            $output = [

                'success' => 0,

                'msg' => __('messages.something_went_wrong')

            ];

        }
        

        return redirect()->back()->with('status', $output);;
    }
}
