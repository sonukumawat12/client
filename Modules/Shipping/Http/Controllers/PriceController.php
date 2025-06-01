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
use Modules\Shipping\Entities\ShippingPrice;
use Modules\Shipping\Entities\ShippingPackage;
use Modules\Shipping\Entities\RouteOperation;
use Yajra\DataTables\Facades\DataTables;
use Modules\Shipping\Entities\ShippingPartner;

use Modules\Shipping\Entities\ShippingMode;

class PriceController extends Controller
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
            $types = ShippingPrice::leftjoin('users', 'shipping_prices.created_by', 'users.id')
                ->leftJoin('shipping_packages', 'shipping_prices.shipping_package', 'shipping_packages.id')
                ->leftJoin('shipping_mode', 'shipping_prices.shipping_mode', 'shipping_mode.id')
                ->leftJoin('shipping_partners', 'shipping_prices.shipping_partner', 'shipping_partners.id')
                ->where('shipping_prices.business_id', $business_id)
                ->select(['shipping_prices.*', 'users.username as created_by','shipping_mode.shipping_mode','shipping_partners.name as shipping_partner','shipping_packages.package_name']);
        
            if (!empty(request()->shipping_mode_price)) {
                $types->where('shipping_mode.shipping_mode', request()->shipping_mode_price);
            }
            if (!empty(request()->shipping_mode_partner)) {
                $types->where('shipping_partners.name', request()->shipping_mode_partner);
            }   
            if (!empty(request()->user_id)) {
                $types->where('shipping_prices.created_by', request()->user_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $types->whereDate('shipping_prices.added_date', '>=', request()->start_date);
                $types->whereDate('shipping_prices.added_date', '<=', request()->end_date);
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
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Shipping\Http\Controllers\PriceController@edit', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> ' . __('messages.edit') . '</a></li>';
                    }

                    if (
                        auth()
                            ->user()
                            ->can('shipping.helpers.delete')
                    ) {
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Shipping\Http\Controllers\PriceController@destroy', [$row->id]) . '" class="delete_button"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</a></li>';
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
                ->editColumn('per_kg', '{{ @num_format($per_kg) }}')
                ->editColumn('constant_value', '{{ @num_format($constant_value) }}')
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
        $shipping_packages = ShippingPackage::where('business_id', $business_id)
            ->select('package_name', 'id')->get()->pluck('package_name', 'id');
        $shipping_modes = ShippingMode::where('shipping_mode.business_id', $business_id)
            ->select('shipping_mode','id')->get()->pluck('shipping_mode','id');
            
        $shipping_partners = ShippingPartner::where('business_id',$business_id)->select('name','id')->pluck('name','id');
            
            
        return view('shipping::settings.price.create')->with(compact('business_id','shipping_modes','shipping_partners', 'shipping_packages'));
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
            $data['added_date'] = $this->commonUtil->uf_date($data['added_date']);
            $data['business_id'] = $business_id;
            $data['created_by'] = Auth::user()->id;
            $data['created_at'] = now();
            $data['updated_at'] = now();
            ShippingPrice::create($data);

            $output = [
                'success' => true,
                'tab' => 'price',
                'msg' => __('lang_v1.success'),
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'price',
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
            
        $type = ShippingPrice::find($id);
        $shipping_packages = ShippingPackage::where('business_id', $business_id)
            ->select('package_name', 'id')->get()->pluck('package_name', 'id');
        $shipping_modes = ShippingMode::where('shipping_mode.business_id', $business_id)
            ->select('shipping_mode','id')->get()->pluck('shipping_mode','id');
        $shipping_partners = ShippingPartner::where('business_id',$business_id)->select('name','id')->pluck('name','id');

        return view('shipping::settings.price.edit')->with(compact('type','shipping_modes','shipping_partners', 'shipping_packages'));
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
            
            ShippingPrice::where('id', $id)->update($data);

            $output = [
                'success' => true,
                'tab' => 'price',
                'msg' => __('lang_v1.success'),
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'price',
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
            ShippingPrice::where('id', $id)->delete();

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
