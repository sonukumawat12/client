<?php

namespace Modules\MPCS\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\BusinessLocation;
use App\Utils\BusinessUtil;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Modules\MPCS\Entities\Mpcs9cCashFormSettings;

class Form9CSettingsController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;
    protected $businessUtil;

    private $barcode_types;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil)
    {

        $this->businessUtil = $businessUtil;
        $this->middleware('web');
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    // public function index()
    // {
    //     if (request()->ajax()) {


    //        $header = Mpcs9cCashFormSettings::query();


    //         return Datatables::of($header)
    //             ->removeColumn('id')
    //             ->removeColumn('business_id')
    //             ->removeColumn('created_at')
    //             ->removeColumn('updated_at')
    //             ->editColumn('action', function ($row) {
    //                 $html = '<button href="#" data-href="' . url('/mpcs/edit-form-9c-settings/' . $row->id) . '" class="btn-modal btn btn-primary btn-xs" data-container=".update_form_9_c_settings_modal"><i class="fa fa-edit" aria-hidden="true"></i> ' . __("messages.edit") . '</button>';
    //                 return $html;
    //             })

    //             ->rawColumns(['action'])
    //             ->make(true);
    //     }

    //     $business_id = request()->session()->get('business.id');

    //     $settings = Mpcs9cCashFormSettings::where('business_id', $business_id)->first();
    //     $business_locations = BusinessLocation::forDropdown($business_id);
    //     return view('mpcs::forms.form_9c')->with(compact(
    //         'business_locations',
    //         'settings'
    //     ));
    // }

    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        } 
        if (request()->ajax()) {
            $user = auth()->user(); // Get logged-in user

            $header = Mpcs9cCashFormSettings::query();

            return Datatables::of($header)
                ->removeColumn('id')
                ->removeColumn('business_id')
                ->removeColumn('created_at')
                ->removeColumn('updated_at')
                ->editColumn('action', function ($row) use ($user) {
                    if ($user->is_superadmin_default == 1) {
                        // Show active edit button for Super Admin
                        return '<button href="#" data-href="' . url('/mpcs/edit-form-9c-settings/' . $row->id) . '" 
                        class="btn-modal btn btn-primary btn-xs" 
                        data-container=".update_form_9_c_settings_modal">
                        <i class="fa fa-edit" aria-hidden="true"></i> ' . __("messages.edit") . '
                    </button>';
                    } else {
                        // Show disabled button for non-Super Admins
                        return '<button class="btn btn-primary btn-xs" disabled>
                        <i class="fa fa-edit" aria-hidden="true"></i> ' . __("messages.edit") . '
                    </button>';
                    }
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $business_id = request()->session()->get('business.id');

        $settings = Mpcs9cCashFormSettings::where('business_id', $business_id)->first();
        $business_locations = BusinessLocation::forDropdown($business_id);

        return view('mpcs::forms.form_9c')->with(compact(
            'business_locations',
            'settings'
        ));
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function create()
    {
        return view('mpcs::forms.partials.create_9c_form_settings');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {

        $business_id = session()->get('user.business_id');

        $data = array(
            'business_id' => $business_id,
            'date_time' => $request->input('datepicker'),
            'starting_number' => $request->input('form_starting_number'),
            'ref_pre_form_number' => $request->input('ref_previous_form_number'),
            'added_user' => auth()->user()->username,
            'created_at' => date('Y-m-d H:i'),
            'updated_at' => date('Y-m-d H:i'),
        );

        Mpcs9cCashFormSettings::insertGetId($data);

        $output = [
            'success' => 1,
            'msg' => __('mpcs::lang.form_9a_settings_add_success')
        ];

        return redirect()->back()->with('success', __('mpcs::lang.form_9a_settings_add_success'));
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $settings = Mpcs9cCashFormSettings::where('business_id', $business_id)->where('id', $id)->first();
        return view('mpcs::forms.partials.edit_9c_form_settings')->with(compact(
            'settings'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function update(Request $request, $id)
    {

        $business_id = request()->session()->get('user.business_id');

        $data = array(
            'business_id' => $business_id,
            'date_time' => $request->input('datepicker'),
            'starting_number' => $request->input('form_starting_number'),
            'ref_pre_form_number' => $request->input('ref_previous_form_number'),
            'added_user' => auth()->user()->username,
            'created_at' => date('Y-m-d H:i'),
            'updated_at' => date('Y-m-d H:i'),
        );


        Mpcs9cCashFormSettings::where('id', $id)->update($data);

        $output = [
            'success' => 1,
            'msg' => __('mpcs::lang.form_9a_settings_update_success')
        ];

        return redirect()->back()->with('success', __('mpcs::lang.form_9a_settings_add_success'));
    }

   private function getSalesData($selected_date)
    {
        $business_id = request()->session()->get('user.business_id');
    
        // Fetch cash sales data product sub-category wise
        $cash_sales = DB::table('form9c_sub_categories')
            ->select('sub_category_id', DB::raw('SUM(amount) as total_sale'))
            ->where('business_id', $business_id)
            ->whereDate('created_at', $selected_date)
            ->groupBy('sub_category_id')
            ->get();
    
        // Fetch credit sales data product sub-category wise
        $credit_sales = DB::table('form9c_sub_categories')
            ->select('sub_category_id', DB::raw('SUM(amount) as total_credit_sale'))
            ->where('business_id', $business_id)
            ->whereDate('created_at', $selected_date)
            ->where('is_credit', 1) // Assuming there's a column `is_credit` to differentiate credit sales
            ->groupBy('sub_category_id')
            ->get();
    
        // Combine cash and credit sales data
        $sales_data = [];
        foreach ($cash_sales as $cash_sale) {
            $sales_data[$cash_sale->sub_category_id]['total_sale'] = $cash_sale->total_sale;
        }
    
        foreach ($credit_sales as $credit_sale) {
            $sales_data[$credit_sale->sub_category_id]['total_credit_sale'] = $credit_sale->total_credit_sale;
        }
    
        // Calculate Total Amount for each product sub-category
        foreach ($sales_data as $sub_category_id => $data) {
            $total_sale = $data['total_sale'] ?? 0;
            $total_credit_sale = $data['total_credit_sale'] ?? 0;
            $sales_data[$sub_category_id]['total_amount'] = $total_sale - $total_credit_sale;
        }
    
        return $sales_data;
    }
    
}
