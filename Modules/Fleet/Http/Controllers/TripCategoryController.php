<?php

namespace Modules\Fleet\Http\Controllers;

use App\Transaction;
use App\TransactionPayment;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Fleet\Entities\Driver;
use Modules\Fleet\Entities\TripCategory;
use Yajra\DataTables\Facades\DataTables;

use App\Category;

class TripCategoryController extends Controller
{
    protected $commonUtil;
    protected $moduleUtil;
    protected $productUtil;
    protected $transactionUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil =  $moduleUtil;
        $this->productUtil =  $productUtil;
        $this->transactionUtil =  $transactionUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $fuel_types = TripCategory::leftJoin('users', 'trip_categories.created_by', 'users.id')
                ->where('trip_categories.business_id', $business_id)
                ->select([
                    'trip_categories.*',
                    'users.first_name as created_by',
                ])
                ->orderBy('trip_categories.id', 'desc')
                ->get();


            return DataTables::of($fuel_types)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                            data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        if (auth()->user()->can('fleet.edit_trip_category')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\TripCategoryController@edit', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        }
                        if (auth()->user()->can('fleet.delete_trip_category')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\TripCategoryController@destroy', [$row->id]) . '" class="delete_button"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        }

                        return $html;
                    }
                )
                
                ->editColumn('date', '{{@format_datetime($date,true)}}')
                ->editColumn('amount_method',function($row){
                    if($row->amount_method == 'km_distance_qty'){
                        return "(Per km Rate) * (Distance) * (Quantity)";
                    }else{
                        return "(Per km Rate) * (Distance)";
                    }
                })
                ->removeColumn('id')
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {

        return view('fleet::settings.trip_categories.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        try {
            $data = $request->except('_token');
            $data['date'] = date('Y-m-d H:i');
            $data['business_id'] = $business_id;
            $data['created_by'] = Auth::user()->id;


            TripCategory::create($data);

            $output = [
                'success' => true,
                'tab' => 'trip_categories',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'trip_categories',
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $business_id = request()->session()->get('business.id');
        $view_type = request()->tab;
        $fuel = TripCategory::find($id);
        $contact_id = $id;

        return view('fleet::settings.trip_categories.show')->with(compact(
            'view_type',
            'fuel',
            'contact_id'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $fuel = TripCategory::find($id);

        return view('fleet::settings.trip_categories.edit')->with(compact(
            'fuel'
        ));
    }


   public function update(Request $request, $id)
    {
        try {
            // Log incoming request data for debugging
            Log::info('TripCategory Update Request Data: ', $request->all());

            // Validate the incoming request data
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'manual_entry' => 'required|in:manual_entry,automatic',
                'amount_method' => 'nullable|in:km_distance,km_distance_qty',
                'trip_amount' => 'nullable|numeric|required_if:manual_entry,manual_entry',
            ]);

            $data = [
                'name' => $validated['name'],
                'manual_entry' => $validated['manual_entry'],
                'amount_method' => $validated['manual_entry'] === 'automatic' ? $validated['amount_method'] : null,
                'trip_amount' => $validated['manual_entry'] === 'manual_entry' ? $validated['trip_amount'] : null,
                'business_id' => $request->session()->get('business.id'),
                'created_by' => Auth::user()->id,
            ];

            // Log the data being updated
            // Log::info('TripCategory Update Data: ', $data);

            $tripCategory = TripCategory::findOrFail($id);
            $tripCategory->update($data);

            return response()->json([
                'success' => true,
                'tab' => 'trip_categories',
                'msg' => __('lang_v1.success'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'tab' => 'trip_categories',
                'msg' => $e->validator->errors()->first(),
            ], 422);
        } catch (\Exception $e) {
            Log::emergency('TripCategory Update Failed: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine());
            return response()->json([
                'success' => false,
                'tab' => 'trip_categories',
                'msg' => 'Something went wrong: ' . $e->getMessage(), // Detailed error for now
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            TripCategory::where('id', $id)->delete();

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

        return $output;
    }
}
