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
use Modules\Fleet\Entities\Route;
use Modules\Fleet\Entities\RouteOperation;
use Modules\Fleet\Entities\RouteIncentives;
use Yajra\DataTables\Facades\DataTables;

use Modules\Fleet\Entities\FleetAccountNumber;
use Modules\Fleet\Entities\TripCategory;

class RouteController extends Controller
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

            $routes = Route::leftjoin('users', 'routes.created_by', 'users.id')
                ->leftjoin('trip_categories','trip_categories.id','routes.trip_category')
                ->leftjoin('fleet_account_numbers','fleet_account_numbers.id','routes.delivered_to_acc')
                ->where('routes.business_id', $business_id)
                ->select([
                    'routes.*',
                    'fleet_account_numbers.delivered_to_acc_no as delivered_accno',
                    'trip_categories.name as category_name',
                    'users.username as created_by',
                ]);

            if (!empty(request()->route_name)) {
                $routes->where('routes.route_name', request()->route_name);
            }
            if (!empty(request()->orignal_location)) {
                $routes->where('routes.orignal_location', request()->orignal_location);
            }
            if (!empty(request()->destination)) {
                $routes->where('routes.destination', request()->destination);
            }
            if (!empty(request()->user_id)) {
                $routes->where('routes.created_by', request()->user_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $routes->whereDate('routes.date', '>=', request()->start_date);
                $routes->whereDate('routes.date', '<=', request()->end_date);
            }
            return DataTables::of($routes)
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
                        if (auth()->user()->can('fleet.routes.edit')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\RouteController@edit', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        }
                        if (auth()->user()->can('fleet.routes.delete')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\RouteController@destroy', [$row->id]) . '" class="delete_button"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        }
                        if (auth()->user()->can('fleet.routes.view')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\RouteController@viewIncentive', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-eye"></i> ' . __("View Incentives") . '</a></li>';
                        }
                        return $html;
                    }
                )
                ->editColumn('rate', '{{@num_format($rate)}}')
                ->editColumn('route_amount', '{{@num_format($route_amount)}}')
                ->editColumn('distance', '{{@num_format($distance)}}')
                ->editColumn('actual_distance', '{{@num_format($actual_distance)}}')
                ->addColumn(
                    'helper_incentive',
                    function($row){
                        $amount=0;
                        $incentives=RouteIncentives::where('route_id',$row->id)->get();
                        foreach($incentives as $incentive){
                            if($incentive->applicable_to == 'helper' || $incentive->applicable_to == 'both'){
                                $amount+=$incentive->amount;
                                if(!empty($incentive->percentage) && !empty($incentive->percentage_amount)){
                                    $amount+=($incentive->percentage/100)*$incentive->percentage_amount;
                                }
                                
                            }
                        }

                        $amount=$row->helper_incentive+$amount;
                        if (auth()->user()->can('fleet.routes.view')) {
                            return '<a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\RouteController@viewIncentive', [$row->id]) . '?type=helper" class="btn-modal" data-container=".view_modal">' . number_format($amount) . '</a>';
                        } else {
                            return number_format($amount);
                        }
                    }
                )
                ->addColumn(
                    'driver_incentive',
                    function($row){
                        $amount=0;
                        $incentives=RouteIncentives::where('route_id',$row->id)->get();
                        foreach($incentives as $incentive){
                            if($incentive->applicable_to == 'driver' || $incentive->applicable_to == 'both'){
                                $amount+=$incentive->amount;
                                if(!empty($incentive->percentage) && !empty($incentive->percentage_amount)){
                                    $amount+=($incentive->percentage/100)*$incentive->percentage_amount;
                                }
                            }
                        }

                        $amount=$row->driver_incentive+$amount;
                        if (auth()->user()->can('fleet.routes.view')) {
                            return '<a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\RouteController@viewIncentive', [$row->id]) . '?type=driver" class="btn-modal" data-container=".view_modal">' . number_format($amount) . '</a>';
                        } else {
                            return number_format($amount);
                        }
                    }
                )
                // ->editColumn('helper_incentive', '{{@num_format($helper_incentive)}}')
                // ->editColumn('driver_incentive', '{{@num_format($driver_incentive)}}')
                ->editColumn('date', '{{@format_date($date)}}')
                ->removeColumn('id')
                ->rawColumns(['action','driver_incentive','helper_incentive'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $quick_add = request()->quick_add;
        
        $applicable_to=[
            'driver'=>'Driver',
            'helper'=>'Helper',
            'both'=>'Both',
        ];
        $incentive_type=[
            'fixed'=>'Fixed',
            'percentage'=>'Percentage',
        ];
        $based_on=[
            'trip_amount'=>'Trip Amount',
            'company_decision'=>'Company Decision',
        ];
        
        $business_id = request()->session()->get('business.id');
        $trip_categories = TripCategory::where('business_id',$business_id)->pluck('name','id');
        $delivered_to = FleetAccountNumber::where('business_id',$business_id)->pluck('delivered_to_acc_no','id');

        return view('fleet::settings.routes.create')->with(compact(
            'applicable_to',
            'incentive_type',
            'based_on',
            'quick_add',
            'delivered_to',
            'trip_categories'
            
        ));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $output = [];
        $business_id = request()->session()->get('business.id');
        try {
            $data = $request->except('_token', 'quick_add');
            $data['date'] = $this->commonUtil->uf_date($data['date']);
            $data['business_id'] = $business_id;
            $data['created_by'] = Auth::user()->id;
            $data['distance'] = $this->commonUtil->num_uf($data['distance']);
            $data['rate'] = $this->commonUtil->num_uf($data['rate']);
            // $data['route_amount'] = $this->commonUtil->num_uf($data['route_amount']);
            $data['route_amount'] = $data['distance'] * $data['rate'];
            $data['driver_incentive'] = $this->commonUtil->num_uf($data['driver_incentive']);
            $data['helper_incentive'] = $this->commonUtil->num_uf($data['helper_incentive']);
            $route['delivered_to_acc'] = $data['delivered_to_acc'];
            $route['trip_category'] = $data['trip_category'];
            $route['actual_distance'] = $this->commonUtil->num_uf($data['actual_distance']);

            $route=Route::create($data);

            if(!empty($data['incentive_name'])) {
                $incentives=[];
                if(is_array($data['incentive_name'])){
                foreach($data['incentive_name'] as $key=>$name){
                    $amount=$this->commonUtil->num_uf($data['fixed_amount'][$key]);
                    $perc_amount=$this->commonUtil->num_uf($data['company_decision'][$key]);
                        
                    $temp=[
                        'route_id'=>$route->id,
                        'incentive_name'=>$name,
                        'incentive_type'=>$data['incentive_type'][$key],
                        'applicable_to'=>$data['applicable_to'][$key],
                        'percentage'=>$this->commonUtil->num_uf($data['percentage'][$key]),
                        'based_on'=>$data['based_on'][$key],
                        'amount'=>$this->commonUtil->num_uf($amount),
                        'created_by'=>Auth::user()->id,
                        'percentage_amount' => $perc_amount
                    ];
                    $incentives[]=$temp;
                }
                }else{
                    $amount=$this->commonUtil->num_uf($data['fixed_amount']);
                        
                    $temp=[
                        'route_id'=>$route->id,
                        'incentive_name'=>$data['incentive_name'],
                        'incentive_type'=>$data['incentive_type'],
                        'applicable_to'=>$data['applicable_to'],
                        'percentage'=>$this->commonUtil->num_uf($data['percentage']),
                        'based_on'=>$data['based_on'],
                        'amount'=>$this->commonUtil->num_uf($amount),
                        'created_by'=>Auth::user()->id,
                        'percentage_amount' => null
                    ];
                    $incentives[]=$temp;
                }
                RouteIncentives::insert($incentives);
            }
            
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
                'route' => $route,
                'data' => $data
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
                'error' => $e->getMessage()
            ];
            if($request->quick_add){
                return $output;
            }
        }
        if($request->quick_add){
            return $output;
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
        $route = Route::find($id);
        $incentives=RouteIncentives::where('route_id',$id)->get();
        
        $applicable_to=[
            'driver'=>'Driver',
            'helper'=>'Helper',
            'both'=>'Both',
        ];
        $incentive_type=[
            'fixed'=>'Fixed',
            'percentage'=>'Percentage',
        ];
        $based_on=[
            'trip_amount'=>'Trip Amount',
            'company_decision'=>'Company Decision',
        ];
        
        $business_id = request()->session()->get('business.id');
        $trip_categories = TripCategory::where('business_id',$business_id)->pluck('name','id');
        $delivered_to = FleetAccountNumber::where('business_id',$business_id)->pluck('delivered_to_acc_no','id');

        return view('fleet::settings.routes.edit')->with(compact(
            'route',
            'incentives',
            'applicable_to',
            'incentive_type',
            'based_on',
            'delivered_to',
            'trip_categories'
        ));
    }

    public function viewIncentive(Request $request,$id)
    {
        $route = Route::findOrFail($id);
        $incentives=RouteIncentives::select('u.username','route_incentives.*')->where('route_id',$id)->join('users as u','u.id','route_incentives.created_by');
        if($request->has('type')){
            $incentives=$incentives->where('applicable_to',$request->type)->orWhere('applicable_to','both');
        }
        $incentives=$incentives->where('route_id',$id)->get();
        $incentiveTotalAmount = 0;
        foreach($incentives as $data) {
            $incentiveTotalAmount += ($data->percentage_amount + $data->amount);
        }

        return view('fleet::settings.routes.view-incentives')->with(compact(
            'incentives','route','incentiveTotalAmount'
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
        // dd($request->except('_token', '_method'));
        try {


            $data = $request->except('_token', '_method');

            $route = [];
            $route['date'] = $this->commonUtil->uf_date($data['date']);
            $route['distance'] = $this->commonUtil->num_uf($data['distance']);
            $route['rate'] = $this->commonUtil->num_uf($data['rate']);
            $route['route_amount'] = $data['rate'] * $data['distance'];
            $route['driver_incentive'] = $this->commonUtil->num_uf($data['driver_incentive']);
            $route['helper_incentive'] = $this->commonUtil->num_uf($data['helper_incentive']);
            
            $route['delivered_to_acc'] = $data['delivered_to_acc'];
            $route['trip_category'] = $data['trip_category'];
            $route['actual_distance'] = $this->commonUtil->num_uf($data['actual_distance']);
            
           
            RouteIncentives::where('route_id',$id)->delete();
            
            if(!empty($data['incentive_name'])) {
                $incentives=[];
                if(is_array($data['incentive_name'])){
                foreach($data['incentive_name'] as $key=>$name){
                    $amount=$this->commonUtil->num_uf($data['fixed_amount'][$key]);
                    $perc_amount=$this->commonUtil->num_uf($data['company_decision'][$key]);
                        
                    $temp=[
                        'route_id'=>$id,
                        'incentive_name'=>$name,
                        'incentive_type'=>$data['incentive_type'][$key],
                        'applicable_to'=>$data['applicable_to'][$key],
                        'percentage'=>$this->commonUtil->num_uf($data['percentage'][$key]),
                        'based_on'=>$data['based_on'][$key],
                        'amount'=>$this->commonUtil->num_uf($amount),
                        'created_by'=>Auth::user()->id,
                        'percentage_amount' => $perc_amount
                    ];
                    $incentives[]=$temp;
                }
                }else{
                    $amount=$this->commonUtil->num_uf($data['fixed_amount']);
                        
                    $temp=[
                        'route_id'=>$id,
                        'incentive_name'=>$data['incentive_name'],
                        'incentive_type'=>$data['incentive_type'],
                        'applicable_to'=>$data['applicable_to'],
                        'percentage'=>$this->commonUtil->num_uf($data['percentage']),
                        'based_on'=>$data['based_on'],
                        'amount'=>$this->commonUtil->num_uf($amount),
                        'created_by'=>Auth::user()->id,
                        'percentage_amount' => null
                    ];
                    $incentives[]=$temp;
                }
                RouteIncentives::insert($incentives);
            }
            
            Route::where('id', $id)->update($route);
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
                'error'=>$e->getMessage()
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
            Route::where('id', $id)->delete();

            $route_operations = RouteOperation::where('route_id', $id)->get();
            foreach ($route_operations as $route_operation) {
                Transaction::where('id', $route_operation->transaction_id)->delete();
                TransactionPayment::where('transaction_id', $route_operation->transaction_id)->delete();
            }
            RouteOperation::where('helper_id', $id)->delete();

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

    public function getDetails($id)
    {
        $route = Route::find($id);
        
        $route->acc_no = FleetAccountNumber::find($route->delivered_to_acc)->delivered_to_acc_no ?? "";
        
        $amount_driver=0;
        $amount_helper=0;
        $incentives=RouteIncentives::where('route_id',$id)->get();
        foreach($incentives as $incentive){
            if($incentive->applicable_to == 'helper' || $incentive->applicable_to == 'both'){
                $amount_helper+=$incentive->amount;
                if(!empty($incentive->percentage) && !empty($incentive->percentage_amount)){
                    $amount_helper+=($incentive->percentage/100)*$incentive->percentage_amount;
                }
                
            }
            
            if($incentive->applicable_to == 'driver' || $incentive->applicable_to == 'both'){
                $amount_driver +=$incentive->amount;
                if(!empty($incentive->percentage) && !empty($incentive->percentage_amount)){
                    $amount_driver +=($incentive->percentage/100)*$incentive->percentage_amount;
                }
            }
            
        }

        $amount_helper=$route->helper_incentive+$amount_helper;
        $amount_driver=$route->driver_incentive+$amount_driver;
        
        $route->helper_incentive = $amount_helper;
        $route->driver_incentive = $amount_driver;

        return $route;
    }

    public function getRouteDropdown(){
        $business_id = request()->session()->get('business.id');

        $routes = Route::where('business_id', $business_id)->pluck('route_name', 'id');
        $route_dp = $this->transactionUtil->createDropdownHtml($routes, 'Please Select');

        return $route_dp;
    }
}
