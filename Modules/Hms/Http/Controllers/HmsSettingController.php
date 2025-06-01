<?php

namespace Modules\Hms\Http\Controllers;

use App\Business;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\NotificationTemplate;
use App\Utils\ModuleUtil;


class HmsSettingController extends Controller
{
    protected $moduleUtil;

    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        
        // if (! (auth()->user()->can('user') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'hms_module'))) {
        //     abort(403, 'Unauthorized action.');
        // }

        $busines = Business::findOrFail($business_id);

        $tags = [
            '{business_name}, {business_logo}, {customer_name}, {booking_id}, {booking_status}, {adults}, {childrens}, {booking_details}, {additional_services}', '{arrival_date}', '{departure_date}'
        ];

        $template = NotificationTemplate::where('template_for', 'hms_new_booking')->where('business_id', $business_id)->first();

        return view('hms::settings.index', compact('busines', 'tags', 'template'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('hms::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
        // if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'hms_module'))) {
        //     abort(403, 'Unauthorized action.');
        // }
    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        
        
        try {
            // $setting = $request->post('hms');
            // $business_id = session()->get('user.business_id');
    
            // $busines = Business::findOrFail($business_id);


            // $hms_settings = json_decode($busines->hms_settings, true) ?? [];

            // $hms_settings['prefix'] = $request->booking_prefix;
            // $busines->hms_settings = json_encode($hms_settings);
  
            //   $busines->update();
            // Validate the request data
            
            $request->validate([
                'booking_prefix' => 'required|string|max:255',
            ]);
    
            $business_id = session()->get('user.business_id');
            $busines = Business::findOrFail($business_id);
    
            // Decode hms_settings, handle null or invalid JSON
            $hms_settings = !empty($busines->hms_settings) ? json_decode($busines->hms_settings, true) : [];
            if (json_last_error() !== JSON_ERROR_NONE) {
                $hms_settings = [];
            }
    
            $hms_settings['prefix'] = $request->booking_prefix;
            $busines->hms_settings = json_encode($hms_settings);
    
            $busines->update();
    
            $output = [
                'success' => 1,
                'msg' => __('lang_v1.success'),
            ];
    
            return redirect()
                ->action([\Modules\Hms\Http\Controllers\HmsSettingController::class, 'index'])
                ->with('status', $output);
                
        } catch (\Exception $e) {
            // \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            // $output = [
            //     'success' => 0,
            //     'msg' => __('messages.something_went_wrong'),
            // ];

            // return back()->with('status', $output)->withInput();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong') . ' Details: ' . $e->getMessage(),
            ];
    
            return back()->with('status', $output)->withInput();
        }

    }
    
    public function saveHmsSmsSettings(Request $request)
    {
        try{
        //  dd($request->all());
        $business_id = request()->session()->get('user.business_id');
        $request->validate([
            'booking_prefix' => 'nullable|string',
            'footer_text' => 'nullable|string',
            'all_countries' => 'nullable|boolean',
            'country_codes' => 'nullable|string',
            'visit_threshold_coupon_code' => 'nullable|integer|min:1',
            'enable_sms' => 'nullable|array',
            'enable_sms.*' => 'nullable|in:1'
        ]);

        $business = Business::findOrFail($business_id);
        
        // dd($business);

        $hms_settings = !empty($business->hms_settings) ? json_decode($business->hms_settings, true) : [];
        if (json_last_error() !== JSON_ERROR_NONE) {
            $hms_settings = [];
        }


        // Save country selection logic
        if (!isset($request->all_countries) || is_null($request->all_countries)) {
            $hms_settings['all_countries'] = $request->all_countries;

            if (!$request->all_countries && $request->filled('country_codes')) {
                // Clean and validate country codes
                $cleaned = preg_replace('/[^0-9,]/', '', $request->country_codes);
                $cleaned = preg_replace('/,+/', ',', $cleaned);
                $cleaned = trim($cleaned, ',');
                $hms_settings['applicable_country_codes'] = $cleaned;
            } elseif ($request->all_countries) {
                $hms_settings['applicable_country_codes'] = '';
            }
        }else{
            $hms_settings['all_countries'] = $request->all_countries;
        }
        
        if(isset($request->visit_threshold_coupon_code)){
            $hms_settings['visit_threshold_coupon_code'] = $request->visit_threshold_coupon_code;
        }
        
        $existing_settings = json_decode($business->hms_settings);
        $existing_enable_sms = (array) ($existing_settings->enable_sms ?? []);
        
        $hms_settings['enable_sms'] = $existing_enable_sms;
        
        $eventKeys = [
            'coupon_code',
            'departure_reminder',
            'payment_done',
            'booking_cancelled',
            'booking_extended',
            'booking_confirmed',
            'room_booked'
        ];
        
        foreach ($eventKeys as $eventKey) {
            // If checkbox was checked, save as '1'
            if (isset($request->enable_sms[$eventKey])) {
                $hms_settings['enable_sms'][$eventKey] = '1';
            }
            // Optional: unset or disable if unchecked
            else {
                unset($hms_settings['enable_sms'][$eventKey]);
            }
        }




        
        

        // SMS Templates
        $events = [
            'room_booked',
            'booking_confirmed',
            'booking_extended',
            'booking_cancelled',
            'payment_done',
            'departure_reminder',
            'coupon_code'
        ];
    
        foreach ($events as $event) {
            $field = "sms_$event";
            if ($request->filled($field)) {
                $hms_settings['sms_templates'][$event] = $request->$field;
            }
        }
    
        $business->hms_settings = json_encode($hms_settings);
        $business->save();

        $output = [
                'success' => 1,
                'msg' => __('lang_v1.success'),
            ];
    
            return redirect()
                ->action([\Modules\Hms\Http\Controllers\HmsSettingController::class, 'index'])
                ->with('status', $output);
                
    } catch (\Exception $e) {
            // \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            // $output = [
            //     'success' => 0,
            //     'msg' => __('messages.something_went_wrong'),
            // ];
            // dd($e);
            // return back()->with('status', $output)->withInput();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong') . ' Details: ' . $e->getMessage(),
            ];
    
            return back()->with('status', $output)->withInput();
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('hms::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('hms::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }

    public function store_email_template(Request $request){
    $business_id = session()->get('user.business_id');

        try{
            NotificationTemplate::updateOrCreate(
                ['template_for' => 'hms_new_booking', 'business_id' => $business_id],

                    [
                        'subject' => $request->post('subject'),
                        'bcc' => $request->post('bcc'),
                        'cc' => $request->post('cc'),
                        'auto_send' => ! empty($request->post('auto_send')) ? 1 : 0,
                        'email_body' => $request->post('email_body'),
                    ]
                );

                $output = [
                    'success' => 1,
                    'msg' => __('lang_v1.success'),
                ];

                return back()->with('status', $output);
                
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
            return back()->with('status', $output)->withInput();

        }
    }

    public function post_pdf(Request $request){
        $business_id = request()->session()->get('user.business_id');
        
        // if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'hms_module'))) {
        //     abort(403, 'Unauthorized action.');
        // }
        
        try {
            $setting = $request->post('hms');
            $business_id = session()->get('user.business_id');
    
            $busines = Business::findOrFail($business_id);

            $hms_settings = json_decode($busines->hms_settings, true);
            $hms_settings['booking_pdf'] = ['footer_text' => $request->footer_text];
            $busines->hms_settings = json_encode($hms_settings);
  
            $busines->update();
    
            $output = [
                'success' => 1,
                'msg' => __('lang_v1.success'),
            ];
    
            return redirect()
                ->action([\Modules\Hms\Http\Controllers\HmsSettingController::class, 'index'])
                ->with('status', $output);
                
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

             $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong') . ' Details: ' . $e->getMessage(),
            ];

            return back()->with('status', $output)->withInput();
        }

    }
}
