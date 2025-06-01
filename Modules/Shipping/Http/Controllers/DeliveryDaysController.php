<?php

namespace Modules\Shipping\Http\Controllers;

use App\Transaction;
use App\TransactionPayment;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\DB;
use Modules\Shipping\Entities\ShippingDeliveryDays;
use Modules\Shipping\Entities\RouteOperation;
use Yajra\DataTables\Facades\DataTables;

use Modules\Shipping\Entities\ShippingMode;

class DeliveryDaysController extends Controller
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
        $this->moduleUtil = $moduleUtil;
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (request()->ajax()) {
            $business_id = request()
                ->session()
                ->get('user.business_id');

            $types = ShippingDeliveryDays::leftjoin('users', 'shipping_delivery_days.created_by', 'users.id')
                ->leftJoin('shipping_mode', 'shipping_delivery_days.shipping_mode', 'shipping_mode.id')
                ->where('shipping_delivery_days.business_id', $business_id)
                ->select(['shipping_delivery_days.*', 'users.username as created_by','shipping_mode.shipping_mode']);

            if (!empty(request()->days)) {
                $types->where('days', request()->days);
            }
            if (!empty(request()->user_id)) {
                $types->where('shipping_delivery_days.created_by', request()->user_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $types->whereDate('shipping_delivery_days.added_date', '>=', request()->start_date);
                $types->whereDate('shipping_delivery_days.added_date', '<=', request()->end_date);
            }
            return DataTables::of($types)
                ->addColumn('action', function ($row) {
                    $html =
                        '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs"
                            data-toggle="dropdown" aria-expanded="false">' .
                        __('messages.actions') .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                    if (
                        auth()
                            ->user()
                            ->can('shipping.helpers.edit')
                    ) {
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Shipping\Http\Controllers\DeliveryDaysController@edit', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> ' . __('messages.edit') . '</a></li>';
                    }

                    if (
                        auth()
                            ->user()
                            ->can('shipping.helpers.delete')
                    ) {
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Shipping\Http\Controllers\DeliveryDaysController@destroy', [$row->id]) . '" class="delete_button"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</a></li>';
                    }
                    $html .= '<li class="divider"></li>';
                    // $html .= '<li><a href="' . action('\Modules\Fleet\Http\Controllers\HelperController@show', [$row->id]) . '?tab=ledger" class=""><i class="fa fa-anchor"></i> ' . __('lang_v1.ledger') . '</a></li>';
                    return $html;
                })
                ->editColumn('status',function($row){
                    if($row->status == 1){
                        $html = "<span class='badge bg-success'>Active</span>";
                    }else{
                        $html = "<span class='badge bg-danger'>Not Active</span>";
                    }
                    
                    return $html;
                })
                ->editColumn('added_date', '{{ @format_date($added_date) }}')
                ->removeColumn('id')
                ->rawColumns(['action','status'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $business_id = request()
            ->session()
            ->get('user.business_id');
        
        $shipping_modes = ShippingMode::where('shipping_mode.business_id', $business_id)
            ->select('shipping_mode','id')->get()->pluck('shipping_mode','id');
        return view('shipping::settings.delivery_days.create')->with(compact('business_id','shipping_modes'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request): RedirectResponse
    {
        $business_id = request()
            ->session()
            ->get('business.id');
        try {
            $data = $request->except('_token');
            // dd($data);
            $data['added_date'] = $this->commonUtil->uf_date($data['added_date']);
            $data['business_id'] = $business_id;
            $data['created_by'] = Auth::user()->id;
            $data['created_at'] = now();
            $data['updated_at'] = now();

            ShippingDeliveryDays::create($data);

            $output = [
                'success' => true,
                'tab' => 'delivery_days',
                'msg' => __('lang_v1.success'),
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'delivery_days',
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return redirect()
            ->back()
            ->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $business_id = request()
            ->session()
            ->get('business.id');
            
        $type = ShippingDeliveryDays::find($id);
        
        $shipping_modes = ShippingMode::where('shipping_mode.business_id', $business_id)
            ->select('shipping_mode','id')->get()->pluck('shipping_mode','id');

        return view('shipping::settings.delivery_days.edit')->with(compact('type','shipping_modes'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->except('_token', '_method');
            $data['added_date'] = $this->commonUtil->uf_date($data['added_date']);
             if(empty($data['status'])){
                $data['status'] = 0;
            }
            
            // dd($data);

            ShippingDeliveryDays::where('id', $id)->update($data);

            $output = [
                'success' => true,
                'tab' => 'delivery_days',
                'msg' => __('lang_v1.success'),
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'delivery_days',
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return redirect()
            ->back()
            ->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            ShippingDeliveryDays::where('id', $id)->delete();

            $route_operations = RouteOperation::where('helper_id', $id)->get();
            foreach ($route_operations as $route_operation) {
                Transaction::where('id', $route_operation->transaction_id)->delete();
                TransactionPayment::where('transaction_id', $route_operation->transaction_id)->delete();
            }
            RouteOperation::where('helper_id', $id)->delete();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }
}
