<?php

namespace Modules\Shipping\Http\Controllers;

use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Shipping\Entities\Driver;
use Modules\Shipping\Entities\CollectionOfficer;
use Modules\Shipping\Entities\Route;
use Modules\Shipping\Entities\Type;
use Modules\Shipping\Entities\ShippingStatus;

use Modules\Shipping\Entities\ShippingMode;
use Modules\Shipping\Entities\ShippingDelivery;
use Modules\Shipping\Entities\ShippingDeliveryDays;
use Modules\Shipping\Entities\ShippingPrefix;
use Modules\Shipping\Entities\ShippingPackage;
use Modules\Shipping\Entities\ShippingCreditDay;
use Modules\Shipping\Entities\ShippingAccount;
use Modules\Shipping\Entities\ShippingPartner;


use App\Account;
use App\AccountType;

class SettingController extends Controller
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
    public function index(Request $request)
    {
        $business_id = request()
            ->session()
            ->get('business.id');

        $routes = Route::leftjoin('users', 'routes.created_by', 'users.id')
            ->where('routes.business_id', $business_id)
            ->select('users.username as added_by', 'routes.*')
            ->get();
        $route_names = $routes->pluck('route_name', 'route_name');
        $orignal_locations = $routes->pluck('orignal_location', 'orignal_location');
        $destinations = $routes->pluck('destination', 'destination');
        $users = $routes->pluck('added_by', 'id');

        $drivers = Driver::leftjoin('users', 'shipping_drivers.created_by', 'users.id')
            ->where('shipping_drivers.business_id', $business_id)
            ->select('users.username as added_by', 'shipping_drivers.*')
            ->get();
        $employee_nos = $drivers->pluck('employee_no', 'employee_no');
        $driver_names = $drivers->pluck('driver_name', 'driver_name');
        $nic_numbers = $drivers->pluck('nic_number', 'nic_number');
        
        $helpers = CollectionOfficer::leftjoin('users', 'collection_officers.created_by', 'users.id')->where('collection_officers.business_id', $business_id)->select('users.username as added_by', 'collection_officers.*')->get();
        $helper_employee_nos = $helpers->pluck('employee_no', 'employee_no');
        $helper_names = $helpers->pluck('helper_name', 'helper_name');
        $helper_nic_numbers = $helpers->pluck('nic_number', 'nic_number');

        $types = Type::leftjoin('users', 'types.created_by', 'users.id')
            ->where('types.business_id', $business_id)
            ->select('users.username as added_by', 'types.*')
            ->get();
        $shipping_types = $types->pluck('shipping_types', 'shipping_types');
        
        $status = ShippingStatus::leftjoin('users', 'shipping_status.created_by', 'users.id')
            ->where('shipping_status.business_id', $business_id)
            ->select('users.username as added_by', 'shipping_status.*')
            ->get();
        $shipping_status = $status->pluck('shipping_status', 'shipping_status');
        
        $shipping_mode = ShippingMode::where('shipping_mode.business_id', $business_id)
            ->select('shipping_mode')->get()->pluck('shipping_mode','shipping_mode');
        
            
        
        $shipping_delivery = ShippingDelivery::where('shipping_delivery.business_id', $business_id)
            ->select('shipping_delivery')
            ->get()->pluck('shipping_delivery','shipping_delivery');
            
        $shipping_delivery_days = ShippingDeliveryDays::where('shipping_delivery_days.business_id', $business_id)
            ->select('days')
            ->get()->pluck('days','days');
        
        $prefix = ShippingPrefix::where('shipping_prefix.business_id', $business_id)
            ->select('prefix')
            ->get()->pluck('prefix','prefix');
            
        $package = ShippingPackage::where('shipping_packages.business_id', $business_id)
            ->select('package_name')
            ->get()->pluck('package_name','package_name');
            
        $credit_days = ShippingCreditDay::where('shipping_credit_days.business_id', $business_id)
            ->select('credit_days')
            ->get()->pluck('credit_days','credit_days');
        
        
        $income_type_id = AccountType::getAccountTypeIdByName('Income', $business_id, true);
        $expense_type_id = AccountType::getAccountTypeIdByName('Expenses', $business_id, true);

        $income_accounts = Account::where('business_id', $business_id)->where('account_type_id', $income_type_id)->pluck('name', 'id');
        
        $expense_accounts = Account::where('business_id', $business_id)->where('account_type_id', $expense_type_id)->pluck('name', 'id');
        
            
        $shipping_partners = ShippingPartner::where('business_id',$business_id)->select('name','id')->pluck('name','id');
        $shipping_modes = ShippingMode::where('shipping_mode.business_id', $business_id)
            ->select('shipping_mode','id')->get()->pluck('shipping_mode','id');

        return view('shipping::settings.index')->with(
            compact(
                'routes',
                'route_names',
                'orignal_locations',
                'destinations',
                'users',
                'employee_nos',
                'driver_names',
                'nic_numbers',
                'helper_employee_nos',
                'helper_names',
                'helper_nic_numbers',
                'shipping_types',
                'shipping_status',
                'shipping_delivery',
                'shipping_delivery_days',
                'shipping_mode',
                'prefix',
                'package',
                'credit_days',
                'income_accounts',
                'expense_accounts',
                'shipping_partners',
                'shipping_modes'
            )
        );
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('shipping::create');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function createIncentives()
    {
        $applicable_to = [
            'driver' => 'Driver',
            'helper' => 'Helper',
            'both' => 'Both',
        ];
        $incentive_type = [
            'fixed' => 'Fixed',
            'percentage' => 'Percentage',
        ];
        $based_on = [
            'trip_amount' => 'Trip Amount',
            'company_decision' => 'Company Decision',
        ];

        return view('shipping::settings.routes.create-incentives')->with(compact('applicable_to', 'incentive_type', 'based_on'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('shipping::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('shipping::edit');
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
