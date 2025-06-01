<?php

namespace Modules\Bakery\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Bakery\Entities\BakeryDriver;
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
use Modules\Bakery\Entities\BakeryUser;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\DataTables;

class BakeryController extends Controller
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
        $this->moduleUtil =  $moduleUtil;
        $this->productUtil =  $productUtil;
        $this->transactionUtil =  $transactionUtil;
        $this->businessUtil = $businessUtil;
    }
    
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('bakery::index');
    }
    
    public function getUserActivityReport(Request $request)

    {

        
        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {

            $with = [];

            $shipping_statuses = $this->transactionUtil->shipping_statuses();

            $business_users = User::where('business_id', $business_id)->pluck('id')->toArray();

            $activity = Activity::whereIn('causer_id', $business_users)->whereIn('subject_type',$this->transactionUtil->bakery_classes);
            
            if (!empty(request()->user) && request()->user !='All') {

                $user = request()->user;

                $activity->where('causer_id', $user);
            }
            
            if(!empty(request()->type) && request()->type !='All') {
                $type = request()->type;
        
                $activity->where('description', $type);
            }
            if(!empty(request()->subject) && request()->subject !='All') {
               
                $subject = request()->subject;

                $activity->where('log_name', $subject);
            }
            
           if(!empty(request()->startDate) && !empty(request()->endDate) ) {

 
                $activity->whereDate('created_at', '>=', request()->startDate);

                $activity->whereDate('created_at', '<=',request()->endDate);
            }

            $datatable = Datatables::of($activity)

                ->editColumn('created_at', '{{ @format_datetime($created_at) }}')

                ->removeColumn('id')

                ->editColumn('causer_id', function ($row) {

                    $causer_id = $row->causer_id;

                    $username = User::where('id', $causer_id)->select('username')->first()->username;

                    return $username;
                })
                ->addColumn('ref_no',function($row){
                    $attributes = json_decode($row->properties,true);
                    $new = $attributes['attributes'] ?? [];
                    
                    $html = "";
                    if($row->subject_type == 'App\TransactionPayment'){
                        if(!empty($new['payment_ref_no'])){
                            $html .= $new['payment_ref_no'];
                        }
                    }else{
                        if(!empty($new['invoice_no'])){
                            $html .= $new['invoice_no'];
                        }else{
                            if($row->subject_type == 'Modules\Petro\Entities\Settlement'){
                                $html .= $new['settlement_no'];
                            }
                        }
                    }
                    return $html;
                })
                ->addColumn('description_details',function($row){
                    $attributes = json_decode($row->properties,true);
                    
                    $new = $attributes['attributes'] ?? [];
                    $old = $attributes['old'] ?? [];
                    $html = "";
                    
                    if ($row->description == 'updated') {
                            foreach ($new as $key => $newValue) {
                                if (!in_array($key, ['created_at', 'updated_at', 'id'])) {
                                    $oldValue = $old[$key] ?? null;

                                    // Convert objects/arrays to string for display if needed
                                    if (is_array($oldValue) || is_object($oldValue)) $oldValue = json_encode($oldValue);
                                    if (is_array($newValue) || is_object($newValue)) $newValue = json_encode($newValue);

                                    if ($newValue !== $oldValue) {
                                        $originalKey = str_replace('_', ' ', ucfirst($key));
                                        $html .= "Original $originalKey <strong>$oldValue</strong> changed to <strong>$newValue</strong><br>";
                                    }
                                }
                            }
                        }elseif($row->description == 'deleted'){
                        if($row->subject_type == 'App\TransactionPayment'){
                            $contact = Contact::find($new['payment_for']);
                            if(!empty($contact)){
                                $html .= "Contact Name: ".$contact->name."<br>";
                            }
                            
                            if(!empty($new['amount'])){
                                $html .= 'Amount: '.$this->productUtil->num_f($new['amount'])."<br>";
                            }
                            
                            if(!empty($new['payment_ref_no'])){
                                $html .= 'Ref No: '.$new['payment_ref_no']."<br>";
                            }
                            
                        }else{
                            return "";
                        }
                    }
                     elseif($row->description == 'update' && $row->log_name == 'Settlement'){
                        $jsonProperties = $row->properties;
                        
                        $decodedProperties = json_decode($jsonProperties);
                        
                        $text = $decodedProperties[0];
                        
                        $html .= $text;
                                    // $html .= $row->properties;
                            
                    }
                     elseif(($row->description == 'update' || $row->description == 'delete') && ($row->log_name == 'Day End Settlement' || $row->log_name == 'Dip Chart' || $row->log_name == 'Dip Report')){
                        $jsonProperties = $row->properties;
                        
                        $decodedProperties = json_decode($jsonProperties);
                        
                        $text = $decodedProperties[0];
                        
                        $html .= nl2br($text);
                                    // $html .= $row->properties;
                            
                    }
                    else{
                        $html = "";
                    }
                    
                    return nl2br($html); 
                });

            $rawColumns = ['description_details'];

            return $datatable->rawColumns($rawColumns)

                ->make(true);
        }

        $users = User::where('business_id', $business_id)->pluck('username', 'id');
        
        $type =Activity::distinct()->pluck('description');
        $subject =Activity::distinct()->pluck('log_name');

        return view('bakery::report.user_activity')

            ->with(compact('users','type','subject'));
    }

    public function settings()
    {
        $business_id = request()->session()->get('business.id');
        
        $drivers = BakeryDriver::leftjoin('users', 'bakery_drivers.created_by', 'users.id')->where('bakery_drivers.business_id', $business_id)->select('users.username as added_by', 'bakery_drivers.*')->get();
        $employee_nos = $drivers->pluck('employee_no', 'employee_no');
        $driver_names = $drivers->pluck('driver_name', 'driver_name');
        $nic_numbers = $drivers->pluck('nic_number', 'nic_number');
        
        $data = $this->getData();
        $business_locations = $data['business_locations'];
        $vehicle_numbers = $data['vehicle_numbers'];
        $vehicle_types = $data['vehicle_types'];
        $vehicle_brands = $data['vehicle_brands'];
        $access_account = $data['access_account'];
        $income_accounts = $data['income_accounts'];
        $vehicle_models = $data['vehicle_models'];
        $customers = $data['customers'];
        $max_vehicles = $data['max_vehicles'];
        $vehicles_added = $data['vehicles_added'];
        
        $business_locations->prepend(__('lang_v1.all'), '');
        
        $pump_operators = BakeryUser::where('business_id', $business_id)->pluck('name', 'id');
        return view('bakery::settings')->with(compact(
            'employee_nos',
            'driver_names',
            'nic_numbers',
            'business_locations',
            'vehicle_numbers',
            'vehicle_types',
            'vehicle_brands',
            'access_account',
            'income_accounts',
            'vehicle_models',
            'customers',
            'max_vehicles',
            'vehicles_added',
            'pump_operators'
        ));
    }

    public function getData(): array {
        $business_id = request()->session()->get('business.id');

        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

        $fleets = BakeryFleet::where('business_id', $business_id)->get();
        $vehicle_numbers = $fleets->pluck('vehicle_number', 'vehicle_number');
        $vehicle_types = $fleets->pluck('vehicle_type', 'vehicle_type');
        $vehicle_brands = $fleets->pluck('vehicle_brand', 'vehicle_brand');
        $vehicle_models = $fleets->pluck('vehicle_model', 'vehicle_model');

        $access_account = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');
        $income_type_id = AccountType::getAccountTypeIdByName('Income', $business_id);
        $income_accounts = Account::where('business_id', $business_id)->where('account_type_id', $income_type_id)->pluck('name', 'id');
        $customers = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');
        // Maximum vehicles allowed
        $subscriptions = Subscription::active_subscription($business_id);

        $max_vehicles = $subscriptions ? $subscriptions->package_details['vehicle_count'] :0;

        // Vehicles added
        $vehicles_added = DB::table('fleets')->where('business_id', $business_id)->count();
        
        return [
            'business_locations' => $business_locations,
            'vehicle_numbers' => $vehicle_numbers,
            'vehicle_types' => $vehicle_types,
            'vehicle_brands' => $vehicle_brands,
            'access_account' => $access_account,
            'income_accounts' => $income_accounts,
            'vehicle_models' => $vehicle_models,
            'customers' => $customers,
            'max_vehicles' => $max_vehicles,
            'vehicles_added' => $vehicles_added
        ];
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('bakery::partials.products');
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
