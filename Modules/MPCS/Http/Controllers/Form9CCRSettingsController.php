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
use Modules\MPCS\Entities\Mpcs9cCreditFormSettings;

class Form9CCRSettingsController extends Controller
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
    //         $user = auth()->user(); 

    //        $header = Mpcs9cCreditFormSettings::query();


    //         return Datatables::of($header)
    //             ->removeColumn('id')
    //             ->removeColumn('business_id')
    //             ->removeColumn('created_at')
    //             ->removeColumn('updated_at')
    //             ->editColumn('action', function ($row) {
    //                 $html = '<button href="#" data-href="' . url('/mpcs/edit-form-9ccr-settings/' . $row->id) . '" class="btn-modal btn btn-primary btn-xs" data-container=".update_form_9_ccr_settings_modal"><i class="fa fa-edit" aria-hidden="true"></i> ' . __("messages.edit") . '</button>';
    //                 return $html;
    //             })

    //             ->rawColumns(['action'])
    //             ->make(true);
    //     }

    //     $business_id = request()->session()->get('business.id');

    //     $settings = Mpcs9cCreditFormSettings::where('business_id', $business_id)->first();
    //     $business_locations = BusinessLocation::forDropdown($business_id);
    //     return view('mpcs::forms.form_9ccr')->with(compact(
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
            $user = auth()->user();

            $header = Mpcs9cCreditFormSettings::query();

            return Datatables::of($header)
                ->removeColumn('id')
                ->removeColumn('business_id')
                ->removeColumn('created_at')
                ->removeColumn('updated_at')
                ->editColumn('action', function ($row) use ($user) {
                    if ($user->is_superadmin_default == 1) {
                        // Show active edit button for Super Admin
                        return '<button href="#" data-href="' . url('/mpcs/edit-form-9ccr-settings/' . $row->id) . '" 
                        class="btn-modal btn btn-primary btn-xs" 
                        data-container=".update_form_9_ccr_settings_modal">
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

        $settings = Mpcs9cCreditFormSettings::where('business_id', $business_id)->first();
        $business_locations = BusinessLocation::forDropdown($business_id);

        return view('mpcs::forms.form_9ccr')->with(compact(
            'business_locations',
            'settings'
        ));
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    // public function create()
    // {
    //     $business_id = request()->session()->get('business.id');
    //     $form_number = Mpcs9cCreditFormSettings::where('business_id', $business_id)
    //                         ->orderBy('id', 'desc')
    //                         ->first();

    //       if (!empty($form_number)) {
    //         $form_9a_no = $form_number->ref_pre_form_number;
    //     }   
    //     return view('mpcs::forms.partials.create_9ccr_form_settings')->with(compact(
    //                 'form_9a_no'
    //     ));
    // }
    public function create()
    {
        $business_id = request()->session()->get('business.id');
        $form_number = Mpcs9cCreditFormSettings::where('business_id', $business_id)
            ->orderBy('id', 'desc')
            ->first();

        $form_9a_no = null; // Initialize the variable to avoid undefined errors

        if (!empty($form_number)) {
            $form_9a_no = $form_number->ref_pre_form_number;
        }

        return view('mpcs::forms.partials.create_9ccr_form_settings')->with(compact('form_9a_no'));
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

        Mpcs9cCreditFormSettings::insertGetId($data);

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

        $settings = Mpcs9cCreditFormSettings::where('business_id', $business_id)->where('id', $id)->first();
        return view('mpcs::forms.partials.edit_9ccr_form_settings')->with(compact(
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


        Mpcs9cCreditFormSettings::where('id', $id)->update($data);

        $output = [
            'success' => 1,
            'msg' => __('mpcs::lang.form_9a_settings_update_success')
        ];

        return redirect()->back()->with('success', __('mpcs::lang.form_9a_settings_add_success'));
    }

   public function get9CCRForm(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $selected_date = $request->input('selected_date');
    
        // Fetch credit sales data from F14 page for the selected date
        $credit_sales = DB::table('f14_page')
            ->where('business_id', $business_id)
            ->whereDate('date', $selected_date)
            ->select('bill_no', 'product_name', 'qty', 'page', 'total_amount_rs', 'total_amount_cents', 'goods_rs', 'goods_cents', 'loading_rs', 'loading_cents', 'empty_rs', 'empty_cents', 'transport_rs', 'transport_cents', 'others_rs', 'others_cents')
            ->get();
    
        // Calculate totals
        $this_document_total = $credit_sales->sum(function($sale) {
            return $sale->total_amount_rs + ($sale->total_amount_cents / 100);
        });
    
        $previous_day_total = 0; // Assuming previous day total is zero for stock taking date
    
        $total = $this_document_total + $previous_day_total;
    
        return response()->json([
            'credit_sales' => $credit_sales,
            'this_document_total' => $this_document_total,
            'previous_day_total' => $previous_day_total,
            'total' => $total
        ]);
    }
}
