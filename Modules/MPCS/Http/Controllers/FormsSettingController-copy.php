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
use Illuminate\Support\Facades\DB;
use Modules\MPCS\Entities\Form9cSubCategory;
use Modules\MPCS\Entities\FormOpeningValue;
use Modules\MPCS\Entities\Mpcs15FormSettings;
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

        return view('mpcs::forms.partials.create_16a_form_settings');

    }

    public function store16aFormSetting(Request $request)
    {
        $business_id = session()->get('user.business_id');

        $data = array(
            'business_id' => $business_id,
            'date' => date('Y-m-d'),
            'starting_number' => $request->input('starting_number'),
            'ref_pre_form_number' => $request->input('ref_pre_form_number'),
            'total_purchase_price_with_vat' => $request->input('total_purchase_price_with_vat'),
            'total_sale_price_with_vat' => $request->input('total_sale_price_with_vat'),
            'created_by' => auth()->user()->id,
            'created_at' => date('Y-m-d H:i'),
            'updated_at' => date('Y-m-d H:i'),
        );

        Mpcs16aFormSettings::insertGetId($data);

        $output = [
            'success' => 1,
            'msg' => __('mpcs::lang.form_16a_settings_add_success')
        ];

        return $output;
    }

    public function mpcs16aFormSettings()
    {
        if (request()->ajax()) {
            $header = Mpcs16aFormSettings::select('*');
         
            return DataTables::of($header)
                ->removeColumn('id')
                ->removeColumn('business_id')
                ->removeColumn('date')
                ->removeColumn('ref_pre_form_number')
                ->removeColumn('created_at')
                ->removeColumn('updated_at')
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

        return view('mpcs::forms.form_9a')->with(compact(
            'business_locations',
            'settings'
        ));
    }

    public function edit16aFormSetting($id) {
        $business_id = request()->session()->get('user.business_id');

        $settings = Mpcs16aFormSettings::where('business_id', $business_id)->where('id', $id)->first();
        return view('mpcs::forms.partials.edit_16a_form_settings')->with(compact(
                    'settings'
        ));
    }

    public function mpcs16Update(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        $data = array(
            'business_id' => $business_id,
            'date' => date('Y-m-d'),
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
    
    //By Zamaluddin : Time 09:00 AM : 29 January 2025
     public function get15FormSetting() {

        return view('mpcs::forms.partials.create_15_form_settings');

    }


public function store15FormSetting(Request $request)
{
    DB::beginTransaction();

    try {
        $business_id = request()->session()->get('business.id');
        $input = $request->except('_token'); // Ambil semua input kecuali _token
        $input['business_id'] = $business_id;

        // Menambahkan Date & Time yang tidak bisa diubah
        $input['dated_at'] = Carbon::now()->format('Y-m-d H:i:s'); // Waktu sekarang, tidak bisa diubah 

        // Mengambil data input untuk kolom lainnya
        $input['f15_form_start_number'] = $request->input('f15_form_start_number');
        $input['ref_previous_form'] = $request->input('ref_previous_form');
        $input['store_purchase_up_to_previous_day'] = $request->input('store_purchase_up_to_previous_day');
        $input['total_up_to_previous_day'] = $request->input('total_up_to_previous_day');
        $input['opening_stock_up_to_previous_day'] = $request->input('opening_stock_up_to_previous_day');
        $input['grand_total_up_to_previous_day'] = $request->input('grand_total_up_to_previous_day');
        $input['cash_sales_up_to_previous_day'] = $request->input('cash_sales_up_to_previous_day');
        $input['card_sales_up_to_previous_day'] = $request->input('card_sales_up_to_previous_day');
        $input['credit_sales_up_to_previous_day'] = $request->input('credit_sales_up_to_previous_day');
        $input['total_31_up_to_previous_day'] = $request->input('total_31_up_to_previous_day');
        $input['balance_stock_in_sale_price_up_to_previous_day'] = $request->input('balance_stock_in_sale_price_up_to_previous_day');
        $input['grand_total_again'] = $request->input('grand_total_again');

        // Update or Create setting untuk form F15
        Mpcs15FormSettings::updateOrCreate(['business_id' => $business_id], $input);

        DB::commit();

        return response()->json([
            'success' => 1,
            'msg' => __('mpcs::lang.form_15_settings_add_success')
        ]);
    } catch (\Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        DB::rollBack();
        return response()->json([
            'success' => 0,
            'msg' => __('mpcs::lang.form_15_settings_add_fail'),
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function mpcs15FormSettings()
{
    if (request()->ajax()) {
        $business_id = session()->get('business.id');
        
        $header = Mpcs15FormSettings::select([
                'mpcs_form_f15_details.id',
                'mpcs_form_f15_details.dated_at',
                'mpcs_form_f15_details.f15_form_start_number',
                'mpcs_form_f15_details.ref_previous_form',
                'mpcs_form_f15_details.store_purchase_up_to_previous_day',
                'mpcs_form_f15_details.total_up_to_previous_day',
                'mpcs_form_f15_details.opening_stock_up_to_previous_day',
                'mpcs_form_f15_details.grand_total_up_to_previous_day',
                'mpcs_form_f15_details.cash_sales_up_to_previous_day',
                'mpcs_form_f15_details.card_sales_up_to_previous_day',
                'mpcs_form_f15_details.credit_sales_up_to_previous_day',
                'mpcs_form_f15_details.total_31_up_to_previous_day',
                'mpcs_form_f15_details.balance_stock_in_sale_price_up_to_previous_day',
                'mpcs_form_f15_details.grand_total_again',
                'form_f15_transaction_data.description as transaction_description'
            ])
            ->leftJoin('form_f15_transaction_data', function($join) {
                $join->on('mpcs_form_f15_details.description_id', '=', 'form_f15_transaction_data.id');
            })
            ->where('mpcs_form_f15_details.business_id', $business_id)
            ->orderBy('mpcs_form_f15_details.id', 'DESC');

        return DataTables::of($header)
            ->addColumn('action', function ($row) {
                $html = '';
                    $html = '<div class="btn-group">
                        <button type="button" 
                            data-href="' . url('/mpcs/edit-15-form-settings/' . $row->id) . '" 
                            class="btn-modal btn btn-primary btn-xs" 
                            data-container=".update_form_15_settings_modal">
                            <i class="fa fa-edit"></i> ' . __("messages.edit") . '
                        </button>
                        <button type="button" data-href="' . url('/mpcs/delete-15-form-settings/' . $row->id) . '"
                            class="btn btn-danger btn-xs" 
                            onclick="deleteFormSetting(this)">
                            <i class="fa fa-trash"></i> ' . __("messages.delete") . '
                        </button>
                    </div>';
                return $html;
            })
            ->editColumn('dated_at', function ($row) {
                return !empty($row->dated_at) ? date('Y-m-d', strtotime($row->dated_at)) : '';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    $business_id = session()->get('business.id');
    $settings = MpcsFormF15Details::select([
            'mpcs_form_f15_details.*',
            'form_f15_transaction_data.description as transaction_description'
        ])
        ->leftJoin('form_f15_transaction_data', function($join) {
            $join->on('mpcs_form_f15_details.description_id', '=', 'form_f15_transaction_data.id');
        })
        ->where('mpcs_form_f15_details.business_id', $business_id)
        ->first();

    $business_locations = BusinessLocation::forDropdown($business_id);
    return view('mpcs::forms.form_15', compact('business_locations', 'settings'));
}


    public function edit15FormSetting($id)
    {
        if (request()->ajax()) {
            $business_id = session()->get('business.id');
            
            $settings = Mpcs15FormSettings::select([
                    'mpcs_form_f15_details.*',
                    'form_f15_transaction_data.description as transaction_description'
                ])
                ->leftJoin('form_f15_transaction_data', function($join) {
                    $join->on('mpcs_form_f15_details.description_id', '=', 'form_f15_transaction_data.id');
                })
                ->where('mpcs_form_f15_details.business_id', $business_id)
                ->where('mpcs_form_f15_details.id', $id)
                ->first();
    
            return view('mpcs::forms.partials.edit_15_form_settings')
                ->with(compact('settings'));
        }
    }
    
    public function mpcs15Update($id)
    {
        try {
            $business_id = session()->get('business.id');
            $input = request()->all();
            
            $formSettings = Mpcs15FormSettings::where('business_id', $business_id)
                                            ->where('id', $id)
                                            ->first();
            
            if (!empty($formSettings)) {
                $formSettings->description_id = $input['description_id'];
                $formSettings->ref_book_no = $input['ref_book_no'];
                $formSettings->rupees = $input['rupees'];
                $formSettings->cts = $input['cts'];
                $formSettings->save();
    
                $output = [
                    'success' => true,
                    'msg' => __('mpcs::lang.settings_updated_success')
                ];
            } else {
                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }
    
        return $output;
    }
    
    public function delete15FormSetting($id){
    try {
        $business_id = session()->get('business.id');
        $formSettings = Mpcs15FormSettings::where('id', $id)
                                          ->delete();

        if ($formSettings) {
            $output = [
                'success' => true,
                'msg' => __('Delete Success')
            ];
        } else {
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }
    } catch (\Exception $e) {
        \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
        $output = [
            'success' => false,
            'msg' => __('messages.something_went_wrong')
        ];
    }

    return response()->json($output);
}


}
