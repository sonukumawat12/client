<?php

namespace Modules\Bakery\Http\Controllers;

use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Bakery\Entities\BakeryProduct;
use Yajra\DataTables\Facades\DataTables;

class BakeryProductController extends Controller
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

            $route_products = BakeryProduct::leftjoin('users', 'bakery_products.created_by', 'users.id')
                ->where('bakery_products.business_id', $business_id)
                ->select([
                    'bakery_products.*',
                    'users.username as created_by',
                ]);

            return DataTables::of($route_products)
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
                        // if (auth()->user()->can('fleet.route_products.edit')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Bakery\Http\Controllers\BakeryProductController@edit', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        // }

                        // if (auth()->user()->can('fleet.route_products.delete')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Bakery\Http\Controllers\BakeryProductController@destroy', [$row->id]) . '" class="delete_button"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        // }
                        return $html;
                    }
                )
                ->editCOlumn('unit_cost','{{@num_format($unit_cost)}}')
                ->editColumn('date', '{{@format_date($date)}}')
                ->removeColumn('id')
                ->rawColumns(['action','unit_cost'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');

        return view('bakery::bakery_product.create')->with(compact(
            'business_id'
        ));
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
            foreach ($request->name as $key=> $productName) {
                $product = BakeryProduct::create([
                    'business_id' => $business_id,
                    'date' => $this->commonUtil->uf_date($request->date[$key]),
                    'name' => $productName,
                    'created_by' => Auth::user()->id,
                    'unit_cost' => $this->transactionUtil->num_uf($request->unit_cost[$key])
                ]);
            }

            $output = [
                'success' => true,
                'tab' => 'route_product',
                'msg' => __('lang_v1.success'),
                'data' => $product
            ];
            
            if (request()->ajax()) {
                return $output;
            }
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'route_product',
                'msg' => __('messages.something_went_wrong')
            ];
            
            if (request()->ajax()) {
                return $output;
            }
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
       
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $route_product = BakeryProduct::find($id);

        return view('bakery::bakery_product.edit')->with(compact(
            'route_product'
        ));
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
            $data['date'] = $this->commonUtil->uf_date($data['date']);

            BakeryProduct::where('id', $id)->update($data);

            $output = [
                'success' => true,
                'tab' => 'route_product',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'route_product',
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {

            BakeryProduct::where('id', $id)->delete();

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
