<?php

namespace Modules\Bakery\Http\Controllers;

use App\Business;
use App\Vehicle;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Bakery\Entities\BakeryDriver;
use Modules\Bakery\Entities\BakeryLoading;
use Modules\Bakery\Entities\BakeryProduct;
use Modules\Bakery\Entities\BakeryRoute;
use Modules\Fleet\Entities\Route;
use App\Account;
use App\AccountType;
use App\BusinessLocation;
use Modules\Bakery\Entities\BakeryFleet;
use App\Utils\ModuleUtil;
use App\Utils\BusinessUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use App\Contact;
use Modules\Superadmin\Entities\Subscription;
use Illuminate\Support\Facades\DB;
use App\Product;
use App\Brands;
use App\Category;
use App\TaxRate;
use App\Unit;
use Modules\PriceChanges\Entities\PriceChangesDetail;
use Modules\PriceChanges\Entities\PriceChangesHeader;
use Modules\PriceChanges\Entities\MpcsFormSetting;
use App\Store;
use App\User;

class LoadingController extends Controller
{

    protected $commonUtil;
    protected $moduleUtil;
    protected $productUtil;
    protected $transactionUtil;
    protected $businessUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil = $moduleUtil;
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('bakery::loading.index');
    }

    public function settings()
    {
//        $business_id = request()->session()->get('business.id');

        $vehicles = BakeryFleet::all()->pluck('vehicle_number', 'id');
        $drivers = BakeryDriver::all()->pluck('driver_name', 'id');
        $routes = BakeryRoute::all()->pluck('route', 'id');
        $products = BakeryProduct::all()->pluck('name', 'id');

        return view('bakery::loading_settings')->with(compact(
            'vehicles',
            'drivers',
            'routes',
            'products',
        ));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        try {
            $data = $request->except('_token');
            $data['date'] = $this->commonUtil->uf_date($data['date']);
            $business_id = request()->session()->get('user.business_id');
            $business = Business::where('id', $business_id)->first();
            $data['form_no'] = !empty($business->ref_no_prefixes['form_no']) ? $business->ref_no_prefixes['form_no'] : '' . $request->form_no;
            BakeryLoading::create($data);
            $output = [
                'success' => true,
                'tab' => 'routes',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'routes',
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
        return view('bakery::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('bakery::edit');
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
}
