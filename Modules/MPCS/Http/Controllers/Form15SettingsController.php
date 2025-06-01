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
use Modules\MPCS\Entities\Mpcs15FormSettings;

class Form15SettingsController extends Controller
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
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        } 
        if (request()->ajax()) {
            $header = Mpcs15FormSettings::select('*');
         
            return Datatables::of($header)
                ->removeColumn('id')
                ->removeColumn('business_id')
                ->removeColumn('date')
                ->removeColumn('ref_pre_form_number')
                ->removeColumn('created_at')
                ->removeColumn('updated_at')
                ->editColumn('action', function ($row) {
                    $html = '<button href="#" data-href="' . url('/mpcs/edit-form-settings/' . $row->id) . '" class="btn-modal btn btn-primary btn-xs" data-container=".update_form_9_a_settings_modal"><i class="fa fa-edit" aria-hidden="true"></i> ' . __("messages.edit") . '</button>';
                    return $html;
                })

                ->rawColumns(['action'])
                ->make(true);
        }
        $business_id = request()->session()->get('business.id');
        $settings = Mpcs15FormSettings::where('business_id', $business_id)->first();
        $business_locations = BusinessLocation::forDropdown($business_id);

        return view('mpcs::forms.form_15')->with(compact(
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
        return view('mpcs::forms.partials.create_9a_form_settings');
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
            'date' => date('Y-m-d'),
            'starting_number' => $request->input('form_starting_number'),
            'ref_pre_form_number' => $request->input('ref_previous_form_number'),
            'total_sale_to_pre' => $request->input('total_sale_up_to_previous_day'),
            'pre_day_cash_sale' => $request->input('previous_day_cash_sale'),
            'pre_day_card_sale' => $request->input('previous_day_card_sale'),
            'pre_day_credit_sale' => $request->input('previous_day_credit_sale'),
            'pre_day_cash' => $request->input('previous_day_cash'),
            'pre_day_cheques' => $request->input('previous_day_cheques_cards'),
            'pre_day_total' => $request->input('previous_day_total'),
            'pre_day_balance' => $request->input('previous_day_balance_in_hand'),
            'pre_day_grand_total' => $request->input('previous_day_grand_total'),
            'created_at' => date('Y-m-d H:i'),
            'updated_at' => date('Y-m-d H:i'),
        );

        Mpcs15FormSettings::insertGetId($data);

        $output = [
            'success' => 1,
            'msg' => __('mpcs::lang.form_15_settings_add_success')
        ];

        return $output;
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $settings = Mpcs15FormSettings::where('business_id', $business_id)->where('id', $id)->first();
        return view('mpcs::forms.partials.edit_9a_form_settings')->with(compact(
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
            'date' => $request->input('datepicker'),
            'starting_number' => $request->input('form_starting_number'),
            'ref_pre_form_number' => $request->input('ref_previous_form_number'),
            'total_sale_to_pre' => $request->input('total_sale_up_to_previous_day'),
            'pre_day_cash_sale' => $request->input('previous_day_cash_sale'),
            'pre_day_card_sale' => $request->input('previous_day_card_sale'),
            'pre_day_credit_sale' => $request->input('previous_day_credit_sale'),
            'pre_day_cash' => $request->input('previous_day_cash'),
            'pre_day_cheques' => $request->input('previous_day_cheques_cards'),
            'pre_day_total' => $request->input('previous_day_total'),
            'pre_day_balance' => $request->input('previous_day_balance_in_hand'),
            'pre_day_grand_total' => $request->input('previous_day_grand_total'),
            'created_at' => date('Y-m-d H:i'),
            'updated_at' => date('Y-m-d H:i'),
        );

        Mpcs15FormSettings::where('id', $id)->update($data);

        $output = [
            'success' => 1,
            'msg' => __('mpcs::lang.form_15_settings_update_success')
        ];

        return $output;
    }

    public function get9AForm(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $selected_date = $request->input('selected_date');
        $pre_day = Carbon::parse($selected_date)->subDay()->format('Y-m-d');

        $form_15 = DB::table(
                    function ($query) use ($business_id, $selected_date) {
                        $query->from('mpcs_9a_form')
                            ->where('business_id', $business_id)
                            ->whereDate('date', $selected_date);
                        }, 'a')
                ->leftJoinSub(
                    DB::table('mpcs_9a_form')
                        ->select('business_id', 'total_sale as total_sale_pre_day')
                        ->where('business_id', $business_id)
                        ->whereDate('date', $pre_day),
                    'b',
                    'a.business_id',
                    '=',
                    'b.business_id'
                )
                ->get()
                ->first();

        return $form_15;
    }
}
