<?php

namespace Modules\Superadmin\Http\Controllers;

use Modules\Superadmin\Entities\DefaultNotificationTemplate;
use Illuminate\Http\Request;
use App\Utils\ModuleUtil;
use Illuminate\Routing\Controller;

class DefaultNotificationTemplateController extends Controller
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
        if (!auth()->user()->can('send_notification')) {
            abort(403, 'Unauthorized action.');
        }

        $type = request()->get('type');

        $general_notifications = DefaultNotificationTemplate::generalNotifications();

        $general_notifications = $this->__getTemplateDetails($general_notifications);

        $customer_notifications = DefaultNotificationTemplate::customerNotifications();

        $customer_notifications = $this->__getTemplateDetails($customer_notifications);

        $supplier_notifications = DefaultNotificationTemplate::supplierNotifications();

        $supplier_notifications = $this->__getTemplateDetails($supplier_notifications);

        return view('superadmin::default_notification_template.index')
                ->with(compact('customer_notifications', 'supplier_notifications', 'general_notifications','type'));
    }

    private function __getTemplateDetails($notifications)
    {
        foreach ($notifications as $key => $value) {
            $notification_template = DefaultNotificationTemplate::getTemplate($key);
            $notifications[$key]['subject'] = $notification_template['subject'];
            $notifications[$key]['email_body'] = $notification_template['email_body'];
            $notifications[$key]['sms_body'] = $notification_template['sms_body'];
            $notifications[$key]['whatsapp_text'] = $notification_template['whatsapp_text'];
            $notifications[$key]['auto_send'] = $notification_template['auto_send'];
            $notifications[$key]['auto_send_sms'] = $notification_template['auto_send_sms'];
            $notifications[$key]['auto_send_wa_notif'] = $notification_template['auto_send_wa_notif'];
            $notifications[$key]['cc'] = $notification_template['cc'];
            $notifications[$key]['bcc'] = $notification_template['bcc'];
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
        if (!auth()->user()->can('send_notification')) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            
            $template_data = $request->input('template_data');
            
            foreach ($template_data as $key => $value) {
                
                $not_data = [
                        'auto_send' => !empty($value['auto_send']) ? 1 : 0,
                        'auto_send_sms' => !empty($value['auto_send_sms']) ? 1 : 0,
                        'auto_send_wa_notif' => !empty($value['auto_send_wa_notif']) ? 1 : 0
                    ];
                
                if(!empty($value['subject'])){
                   $not_data['subject'] = $value['subject'];
                }
                
                if(!empty($value['email_body'])){
                    $not_data['email_body'] = $value['email_body'];
                }
                
                if(!empty($value['sms_body'])){
                    $not_data['sms_body'] = $value['sms_body'];
                }
                
                if(!empty($value['whatsapp_text'])){
                    $not_data['whatsapp_text'] = $value['whatsapp_text'];
                }
                
                if(!empty($value['cc'])){
                    $not_data['cc'] = $value['cc'];
                }
                
                if(!empty($value['bcc'])){
                    $not_data['bcc'] = $value['bcc'];
                }
                
                DefaultNotificationTemplate::updateOrCreate(
                    [
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
