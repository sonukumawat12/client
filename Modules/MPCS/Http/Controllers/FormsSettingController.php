<?php

namespace Modules\MPCS\Http\Controllers;

use App\BusinessLocation;
use App\Category;
use App\Employee;
use App\MergedSubCategory;
use App\Utils\ModuleUtil;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\MPCS\Entities\Form9cSubCategory;
use Modules\MPCS\Entities\FormOpeningValue;
use Modules\MPCS\Entities\Mpcs16aFormSettings;
use Modules\MPCS\Entities\Mpcs9aFormSettings;
use Modules\MPCS\Entities\MpcsFormSetting;
use Yajra\DataTables\Facades\DataTables;

class FormsSettingController extends Controller
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
        if (!auth()->check()) {
            return redirect()->route('login');
        } 
        $business_id = request()->session()->get('business.id');
        $business_locations = BusinessLocation::forDropdown($business_id);

        $settings = MpcsFormSetting::where('business_id', $business_id)->first();

        $mpcs_form_settings_permission = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'mpcs_form_settings');
        $list_opening_values_permission = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'list_opening_values');
        // dump($settings,'settings');
        return view('mpcs::forms_setting.index')->with(compact('business_locations', 'settings', 'mpcs_form_settings_permission', 'list_opening_values_permission'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('mpcs::create');
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
        $input = $request->except('_token');
        $input['business_id'] =  $business_id;

        // Existing data (if any)
        $existingSetting = MpcsFormSetting::where('business_id', $business_id)->first();

        // Convert dates
        $input['F22_form_tdate'] = !empty($request->F22_form_tdate)
            ? Carbon::parse($request->F22_form_tdate)->format('Y-m-d')
            : null;

        // Serial logic for F22
        $newDate = $input['F22_form_tdate'];
        $formSerial = 1;
// dd($newDate);
        if ($existingSetting) {
            $oldDate = $existingSetting->F22_form_tdate;
            $formSerial = ($oldDate === $newDate)
                ? $existingSetting->F22_form_sn
                : $existingSetting->F22_form_sn + 1;
        }

        $input['F22_form_sn'] = $formSerial;

        // Save data
        MpcsFormSetting::updateOrCreate(['business_id' => $business_id], $input);

        $output = [
            'success' => true,
            'msg' => __('mpcs::lang.settings_update_success')
        ];
    } catch (\Exception $e) {
        \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
        $output = [
            'success' => false,
            'msg' => __('messages.something_went_wrong')
        ];
    }

    return redirect()->back()->with('status', $output);
}

    public function store_old(Request $request)
    {
        // dd($request->all());
        try {
            $business_id = request()->session()->get('business.id');
            $input = $request->except('_token');
            $input['business_id'] =  $business_id;


            $input['F9C_tdate'] = !empty($request->F_9_C_tdate) ? Carbon::parse($request->F_9_C_tdate)->format('Y-m-d') : null;
            $input['F159ABC_form_tdate'] = !empty($request->F159ABC_form_tdate) ? Carbon::parse($request->F159ABC_form_tdate)->format('Y-m-d') : null;
            $input['F16A_form_tdate'] = !empty($request->F16A_form_tdate) ? Carbon::parse($request->F16A_form_tdate)->format('Y-m-d') : null;
            $input['F21C_form_tdate'] = !empty($request->F21C_form_tdate) ? Carbon::parse($request->F21C_form_tdate)->format('Y-m-d') : null;
            $input['F14_form_tdate'] = !empty($request->F14_form_tdate) ? Carbon::parse($request->F14_form_tdate)->format('Y-m-d') : null;
            $input['F17_form_tdate'] = !empty($request->F17_form_tdate) ? Carbon::parse($request->F17_form_tdate)->format('Y-m-d') : null;
            $input['F20_form_tdate'] = !empty($request->F20_form_tdate) ? Carbon::parse($request->F20_form_tdate)->format('Y-m-d') : null;
            $input['F21_form_tdate'] = !empty($request->F21_form_tdate) ? Carbon::parse($request->F21_form_tdate)->format('Y-m-d') : null;
            $input['F22_form_tdate'] = !empty($request->F22_form_tdate) ? Carbon::parse($request->F22_form_tdate)->format('Y-m-d') : null;


            MpcsFormSetting::updateOrCreate(['business_id' => $business_id], $input);

            $output = [
                'success' => true,
                'msg' => __('mpcs::lang.settings_update_success')
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
        return view('mpcs::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('mpcs::edit');
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


    public function getForm9CSetting()
    {
        $business_id = request()->session()->get('business.id');
        $sub_categories = Category::where('business_id', $business_id)->where('parent_id', '!=', 0)->get();
        $settings = MpcsFormSetting::where('business_id', $business_id)->first();
        $months = MpcsFormSetting::getMonthArray();
        // dd($settings,'setings');
        return view('mpcs::forms_setting.partials.form_9c_modal')->with(compact('sub_categories', 'settings', 'business_id', 'months'));
    }
    public function postForm9CSetting(Request $request)
    {
        try {
            $business_id = request()->session()->get('business.id');
            $data = array(
                'F9C_sn' => $request->F9C_setting_sn,
                'F9C_tdate' => !empty($request->F9C_setting_tdate) ? Carbon::parse($request->F9C_setting_tdate)->format('Y-m-d') : null,
                'F9C_first_day_after_stock_taking' => !empty($request->F9C_first_day_after_stock_taking) ? 1 : 0,
                'F9C_first_day_of_next_month' => !empty($request->F9C_first_day_of_next_month) ? 1 : 0,
                'F9C_first_day_of_next_month_selected' => !empty($request->F9C_first_day_of_next_month_selected) ? $request->F9C_first_day_of_next_month_selected : null
            );
            $setting = MpcsFormSetting::where('business_id', $business_id)->update($data);
            $save_sub_cat_data = [];
            if (!empty($request->sub_cat_9c)) {
                foreach ($request->sub_cat_9c as $key => $item) {
                    $sub_cat_data = [
                        'business_id' => $business_id,
                        'sub_category_id' => $key,
                        'qty' => !empty($item['qty']) ? $item['qty'] : 0.00,
                        'amount' => !empty($item['amount']) ?  $item['amount'] : 0.00
                    ];
                    Form9cSubCategory::updateOrCreate(
                        ['business_id' => $business_id, 'sub_category_id' => $key],
                        $sub_cat_data
                    );
                    $save_sub_cat_data[] =  $sub_cat_data;
                }



                $data['cat_data'] =  $save_sub_cat_data;

                $form_id = MpcsFormSetting::where('business_id', $business_id)->first();
                FormOpeningValue::create([
                    'business_id' => $business_id,
                    'form_name' => '9C',
                    'form_id' => !empty($form_id) ? $form_id->id : 0,
                    'data' => $data,
                    'edited_by' => Auth::user()->id,
                    'date' => date('Y-m-d')
                ]);
            }

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

        return redirect()->back()->with('status', $output);
    }

    public function getForm16ASetting()
    {
        $business_id = request()->session()->get('business.id');
        $months_numbers = MpcsFormSetting::getMonthArray();

        $setting = MpcsFormSetting::where('business_id', $business_id)->first();

        return view('mpcs::forms_setting.partials.form_16a_modal')->with(compact('months_numbers', 'setting'));
    }
    public function postForm16ASetting(Request $request)
    {
        try {
            $business_id = request()->session()->get('business.id');
            $data = array(
                'F16A_form_tdate' => !empty($request->F16A_form_tdate) ? Carbon::parse($request->F16A_form_tdate)->format('Y-m-d') : null,
                'F16A_form_sn' => $request->F16A_form_sn,
                'F16A_total_pp' => $request->F16A_total_pp,
                'F16A_total_sp' => $request->F16A_total_sp,
                'F16A_first_day_after_stock_taking' => !empty($request->F16A_first_day_after_stock_taking) ? $request->F16A_first_day_after_stock_taking : 0,
                'F16A_first_day_of_next_month' => !empty($request->F16A_first_day_of_next_month) ? $request->F16A_first_day_of_next_month : 0,
                'F16A_first_day_of_next_month_selected' => !empty($request->F16A_first_day_of_next_month_selected) ? $request->F16A_first_day_of_next_month_selected : null,
            );
            MpcsFormSetting::where('business_id', $business_id)->update($data);
            $form_id = MpcsFormSetting::where('business_id', $business_id)->first();
            FormOpeningValue::create([
                'business_id' => $business_id,
                'form_name' => 'F16A',
                'form_id' => !empty($form_id) ? $form_id->id : 0,
                'data' => $data,
                'edited_by' => Auth::user()->id,
                'date' => date('Y-m-d')
            ]);

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

    public function getFormF22Setting()
    {
        $business_id = request()->session()->get('business.id');
        $settings = MpcsFormSetting::where('business_id',  $business_id)->select('F22_no_of_product_per_page')->first();

        return view('mpcs::forms_setting.partials.form_f22_modal')->with(compact('settings'));
    }
    public function postFormF22Setting(Request $request)
    {
        $business_id = request()->session()->get('business.id');

        $input['F22_no_of_product_per_page'] = !empty($request->F22_no_of_product_per_page) ? $request->F22_no_of_product_per_page : null;

        MpcsFormSetting::updateOrCreate(['business_id' => $business_id], $input);

        $output = [
            'success' => 1,
            'msg' => __('mpcs::lang.setting_save_success')
        ];

        return redirect()->back()->with('status', $output);
    }
    public function getForm159ABCSetting()
    {
        $business_id = request()->session()->get('business.id');
        $months = MpcsFormSetting::getMonthArray();

        $setting = MpcsFormSetting::where('business_id', $business_id)->first();

        return view('mpcs::forms_setting.partials.form_15_9_abc_modal')->with(compact('months', 'setting'));
    }
    public function saveForm159ABCSetting(Request $request)
    {
        try {
            $business_id = request()->session()->get('business.id');
            $data = array(
                'F159ABC_form_tdate' => !empty($request->F159ABC_form_tdate) ? Carbon::parse($request->F159ABC_form_tdate)->format('Y-m-d') : null,
                'F159ABC_form_sn' => $request->F159ABC_form_sn,
                'F159ABC_first_day_after_stock_taking' => !empty($request->F159ABC_first_day_after_stock_taking) ? $request->F159ABC_first_day_after_stock_taking : 0,
                'F159ABC_first_day_of_next_month' => !empty($request->F159ABC_first_day_of_next_month) ? $request->F159ABC_first_day_of_next_month : 0,
                'F159ABC_first_day_of_next_month_selected' => !empty($request->F159ABC_first_day_of_next_month_selected) ? $request->F159ABC_first_day_of_next_month_selected : null,
            );
            MpcsFormSetting::where('business_id', $business_id)->update($data);

            $form_id = MpcsFormSetting::where('business_id', $business_id)->first();
            FormOpeningValue::create([
                'business_id' => $business_id,
                'form_name' => 'F159ABC',
                'form_id' => !empty($form_id) ? $form_id->id : 0,
                'data' => $data,
                'edited_by' => Auth::user()->id,
                'date' => date('Y-m-d')
            ]);

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

    public function getForm21CSetting()
    {
        $business_id = request()->session()->get('business.id');
        $merged_sub_categories = MergedSubCategory::where('business_id', $business_id)->get();
        $settings = MpcsFormSetting::where('business_id', $business_id)->first();
        $months = MpcsFormSetting::getMonthArray();

        return view('mpcs::forms_setting.partials.form_21c_modal')->with(compact('merged_sub_categories', 'settings', 'months'));
    }
    public function postForm21CSetting(Request $request)
    {
        try {
            $business_id = request()->session()->get('business.id');
            $data = array(
                'F21C_form_sn' => $request->F21C_form_sn,
                'F21C_form_tdate' => !empty($request->F21C_form_tdate) ? Carbon::parse($request->F21C_form_tdate)->format('Y-m-d') : null,
                'F21C_first_day_after_stock_taking' => !empty($request->F21C_first_day_after_stock_taking) ? 1 : 0,
                'F21C_first_day_of_next_month' => !empty($request->F21C_first_day_of_next_month) ? 1 : 0,
                'F21C_first_day_of_next_month_selected' => $request->F21C_first_day_of_next_month
            );

            $setting = MpcsFormSetting::where('business_id', $business_id)->update($data);

            $form_id = MpcsFormSetting::where('business_id', $business_id)->first();
            FormOpeningValue::create([
                'business_id' => $business_id,
                'form_name' => 'F21C',
                'form_id' => !empty($form_id) ? $form_id->id : 0,
                'data' => $data,
                'edited_by' => Auth::user()->id,
                'date' => date('Y-m-d')
            ]);

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

        return redirect()->back()->with('status', $output);
    }

     public function get16AFormSetting() {
        
        $businessId = session()->get('user.business_id');
        $currentDate = now()->toDateString(); 
        $existingSetting = Mpcs16aFormSettings::where('business_id', $businessId)
            ->where('date', $currentDate)
            ->first();
    
        $startingNumber = $existingSetting
            ? $existingSetting->starting_number
            : Mpcs16aFormSettings::where('business_id', $businessId)
                ->orderByDesc('created_at')
                ->value('starting_number') + 1;

        return view('mpcs::forms.partials.create_16a_form_settings')->with(compact('startingNumber'));

}
 
    public function store16aFormSetting(Request $request)
    {
        $business_id = session()->get('user.business_id');
        $data = array(
            'business_id' => $business_id,
            'date' => $request->input('datepicker'),
            'time' => $request->input('time'),
            'starting_number' => $request->input('starting_number'),
            // 'ref_pre_form_number' => $request->input('ref_pre_form_number'),
            'no_of_rows_per_page' => $request->input('no_of_rows_per_page'),
            'total_purchase_price_with_vat' => $request->input('total_purchase_price_with_vat'),
            'total_sale_price_with_vat' => $request->input('total_sale_price_with_vat'),
            'created_by' => auth()->user()->id,
            'created_at' => date('Y-m-d H:i'),
            'updated_at' => date('Y-m-d H:i'),
        );
        // dd($data,'data');

        Mpcs16aFormSettings::insertGetId($data);

        $output = [
            'success' => 1,
            'msg' => __('mpcs::lang.form_16a_settings_add_success')
        ];

        return $output;
    }
    
    public function store16aFormSettingnew(Request $request)
    { 
        $businessId = session()->get('user.business_id');
        $currentDate = now()->toDateString(); 
        //"2025-04-19";
        $requestedStartNumber = $request->input('starting_number');
    // dump($requestedStartNumber,'request',$currentDate);
        $existingSetting = Mpcs16aFormSettings::where('business_id', $businessId)
            ->where('date', $currentDate)
            ->first();
    
        $startingNumber = $existingSetting
            ? $existingSetting->starting_number
            : Mpcs16aFormSettings::where('business_id', $businessId)
                ->orderByDesc('created_at')
                ->value('starting_number') + 1 ?? $requestedStartNumber;
    // dd($startingNumber,'starting number');
        Mpcs16aFormSettings::create([
            'business_id' => $businessId,
            'date' => $currentDate,
            'time' => $request->input('time'),
            'starting_number' => $startingNumber,
            'no_of_rows_per_page' => $request->input('no_of_rows_per_page'),
            'total_purchase_price_with_vat' => $request->input('total_purchase_price_with_vat'),
            'total_sale_price_with_vat' => $request->input('total_sale_price_with_vat'),
            'created_by' => auth()->id(), // Use auth()->id() directly
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
        return [
            'success' => 1,
            'msg' => __('mpcs::lang.form_16a_settings_add_success')
        ];
    }

    public function mpcs16aFormSettings()
    {
        if (request()->ajax()) {
            $header = Mpcs16aFormSettings::select('*');
         
            return DataTables::of($header)
                ->removeColumn('id')
                ->removeColumn('business_id')
                ->removeColumn('ref_pre_form_number')
                ->removeColumn('created_at')
                ->removeColumn('updated_at')
                ->editColumn('date', function($row) {
                    $formattedTime = Carbon::parse($row->time)->format('H:i');
                    return $row->date.' '.$formattedTime;
                })
                ->editColumn('total_purchase_price_with_vat', function($row) {
                    return number_format($row->total_purchase_price_with_vat, 2, '.', ',');
                })
                ->editColumn('total_sale_price_with_vat', function($row) {
                    return number_format($row->total_sale_price_with_vat, 2, '.', ',');
                })
                ->editColumn('action', function ($row) {
                    if (auth()->user()->can('superadmin')) {
                    $html = '<button href="#" data-href="' . url('/mpcs/edit-16-a-form-settings/' . $row->id) . '" class="btn-modal btn btn-primary btn-xs" data-container=".update_form_16_a_settings_modal"><i class="fa fa-edit" aria-hidden="true"></i> ' . __("messages.edit") . '</button>';
                    return $html;
                    } else return '';
                })

                ->rawColumns(['action'])
                ->make(true);
        }
        $business_id = request()->session()->get('business.id');
        $settings = Mpcs16aFormSettings::where('business_id', $business_id)->first();
        $business_locations = BusinessLocation::forDropdown($business_id);

        $setting = MpcsFormSetting::where('business_id', $business_id)->first();

        return view('mpcs::forms.form_9a')->with(compact(
            'business_locations',
            'settings'
        ));
    }

    public function edit16aFormSetting($id) {
        $business_id = request()->session()->get('user.business_id');

        if(auth()->user()->can('superadmin'))
            $settings = Mpcs16aFormSettings::first();
        else 
            $settings = Mpcs16aFormSettings::where('business_id', $business_id)->where('id', $id)->first();        $settings = Mpcs16aFormSettings::first();

        return view('mpcs::forms.partials.edit_16a_form_settings')->with(compact(
                    'settings'
        ));
    }

    public function mpcs16Update(Request $request, $id)
    {
        $data = array(
            // 'business_id' => $business_id,
            'date' => date('Y-m-d'),
            'time' => $request->input('time'),
            'starting_number' => $request->input('starting_number'),
            'ref_pre_form_number' => $request->input('ref_pre_form_number'),
            'total_purchase_price_with_vat' => $request->input('total_purchase_price_with_vat'),
            'total_sale_price_with_vat' => $request->input('total_sale_price_with_vat'),
            'created_by' => auth()->user()->id,
            'created_at' => date('Y-m-d H:i'),
            'updated_at' => date('Y-m-d H:i'),
       );

        Mpcs16aFormSettings::where('id', $id)->update($data);

        $output = [
            'success' => 1,
            'msg' => __('mpcs::lang.form_16a_settings_update_success')
        ];

        return $output;
    }

}
