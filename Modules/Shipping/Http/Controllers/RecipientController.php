<?php

namespace Modules\Shipping\Http\Controllers;

use App\Contact;
use App\Category;
use App\Utils\Util;
use App\Transaction;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\TransactionPayment;
use Illuminate\Http\Request;
use App\Utils\TransactionUtil;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Modules\Shipping\Entities\Shipment;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Contracts\Support\Renderable;
use Modules\Shipping\Entities\RouteOperation;

use Modules\Shipping\Entities\ShippingStatus;
use Modules\Shipping\Entities\ShippingRecipient;

class RecipientController extends Controller
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
        $business_id = request()->session()->get('user.business_id');
        
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $drivers = ShippingRecipient::leftjoin('users', 'shipping_recipients.created_by', 'users.id')
                ->where('shipping_recipients.business_id', $business_id)
                ->select([
                    'shipping_recipients.*',
                    'users.username as created_by',
                ]);

            if (!empty(request()->name)) {
                $drivers->where('name', request()->name);
            }
            if (!empty(request()->mobile)) {
                $drivers->where('mobile_1', request()->mobile);
                $drivers->orWhere('mobile_2', request()->mobile);
            }
            
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $drivers->whereDate('joined_date', '>=', request()->start_date);
                $drivers->whereDate('joined_date', '<=', request()->end_date);
            }
            
            
            $delivered_status = ShippingStatus::where('business_id', $business_id)->where('shipping_status','Delivered')->first();
            $pending_status = ShippingStatus::where('business_id', $business_id)->where('shipping_status','Pending')->first();
            
            return DataTables::of($drivers)
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
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Shipping\Http\Controllers\RecipientController@edit', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Shipping\Http\Controllers\RecipientController@shipmentDetails', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("shipping::lang.shipping_details") . '</a></li>';
                        
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Shipping\Http\Controllers\RecipientController@destroy', [$row->id]) . '" class="delete_button"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Shipping\Http\Controllers\RecipientController@destroy', [$row->id]) . '" class="delete_button"><i class="glyphicon glyphicon-edit"></i> ' . __("shipping::lang.package_name") . '</a></li>';
                        
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Shipping\Http\Controllers\RecipientController@destroy', [$row->id]) . '" class="delete_button"><i class="glyphicon glyphicon-edit"></i> ' . __("shipping::lang.received_delivery") . '</a></li>';
                        
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Shipping\Http\Controllers\RecipientController@destroy', [$row->id]) . '" class="delete_button"><i class="glyphicon glyphicon-edit"></i> ' . __("shipping::lang.package_delivered") . '</a></li>';
                        
                        return $html;
                    }
                )
                ->addColumn('pending_deliveries',function($row) use ($pending_status){
                    if(!empty($pending_status)){
                        $id = $pending_status->id;
                    }else{
                        $id = 0;
                    }
                    
                    
                    $count = Shipment::where('recipient_id',$row->id)->where('delivery_status',$id)->count();
                    return $this->commonUtil->num_uf($count);
                })
                ->addColumn('completed_deliveries',function($row) use ($delivered_status){
                    if(!empty($delivered_status)){
                        $id = $delivered_status->id;
                    }else{
                        $id = 0;
                    }
                    
                    
                    $count = Shipment::where('recipient_id',$row->id)->where('delivery_status',$id)->get();
                    
                    return $this->commonUtil->num_uf($count->count());
                })
                ->editColumn('joined_date', '{{@format_date($joined_date)}}')
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }
        
        $routes = [];
        
        $name = ShippingRecipient::where('business_id',$business_id)->select('name')->pluck('name','name');
        
        $mobile_1 = ShippingRecipient::where('business_id',$business_id)->select('mobile_1')->pluck('mobile_1','mobile_1');
        $mobile_2 = ShippingRecipient::where('business_id',$business_id)->select('mobile_2')->pluck('mobile_2','mobile_2');
        
        $mobile = [];
        foreach($mobile_1 as $key => $value){
            $mobile[$key] = $value;
        }
        
        foreach($mobile_2 as $key => $value){
            $mobile[$key] = $value;
        }
        
        
        return view('shipping::settings.recipients.index')->with(
            compact(
                'name','mobile'
            )
        );
    }
    
    public function shippingDetails($id)
    {
        $business_id = request()->session()->get('user.business_id');
        
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $drivers = Shipment::leftjoin('users', 'shipments.created_by', 'users.id')
                ->leftjoin('contacts', 'shipments.customer_id', 'contacts.id')
                ->leftjoin('shipping_agents', 'shipments.agent_id', 'shipping_agents.id')
                ->where('shipments.business_id', $business_id)
                ->where('shipments.recipient_id',$id)
                ->select([
                    'shipments.*',
                    'users.username as created_by',
                    'contacts.name as sender',
                    'shipping_agents.name as courier'
                ]);
            
            return DataTables::of($drivers)
                
                ->editColumn('operation_date', '{{@format_date($operation_date)}}')
                ->editColumn('delivery_time', '{{@format_datetime($delivery_time)}}')
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }
        
        return view('shipping::settings.recipients.shipping_details');
    }

     public function shipmentDetails($id)
    {
        $business_id = request()->session()->get('user.business_id');
    
        return view('shipping::settings.recipients.shipping_details')->with(compact(
            'id'
        ));
    }


    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        $prefix_type = 'employee_no';
        //Generate reference number
        $ref_count = $this->transactionUtil->onlyGetReferenceCount($prefix_type, $business_id, false);
        //Generate reference number
        $employee_no = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);

        $departments =  Category::where('business_id', $business_id)
                            ->where('category_type', 'hrm_department')
                            ->pluck('name','id');

        return view('shipping::settings.recipients.create')->with(compact(
            'employee_no','departments'
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
            $data = $request->except('_token');
            $data['joined_date'] = $this->commonUtil->uf_date($data['joined_date']);
            $data['business_id'] = $business_id;
            $data['created_by'] = Auth::user()->id;

            //update emploeyee count
            $this->transactionUtil->setAndGetReferenceCount('employee_no', $business_id);
            
            //@doc:6948 Chequer Module
            $data['contact_id'] = $this->createOrUpdateContact($request);

            $data = ShippingRecipient::create($data);

            $output = [
                'success' => true,
                'tab' => 'agents',
                'msg' => __('lang_v1.success'),
                'data' => $data
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'agents',
                'msg' => __('messages.something_went_wrong')
            ];
        }
        if (request()->ajax()){
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
        $business_id = request()->session()->get('business.id');
        $driver_dropdown = ShippingRecipient::where('business_id', $business_id)->pluck('driver_name', 'id');
        $view_type = request()->tab;
        $driver = ShippingRecipient::find($id);
        $contact_id = $id;

        return view('shipping::settings.recipients.show')->with(compact(
            'driver_dropdown',
            'view_type',
            'driver',
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
        $driver = ShippingRecipient::find($id);

        return view('shipping::settings.recipients.edit')->with(compact(
            'driver'
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
            $data['joined_date'] = $this->commonUtil->uf_date($data['joined_date']);
            
            //@doc:6948 Chequer Module
            $shippingRecipient = ShippingRecipient::find($id);
            $data['contact_id'] = $this->createOrUpdateContact($request,$shippingRecipient->contact_id);

            ShippingRecipient::where('id', $id)->update($data);

            $output = [
                'success' => true,
                'tab' => 'agents',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'agents',
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
            ShippingRecipient::where('id', $id)->delete();

            $route_operations = RouteOperation::where('driver_id', $id)->get();
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

    /**
     * @doc:6948 Chequer Module
     * @dev:Sakhawat Kamran
     * @dsc: Store contact against all agent,recipients,partners 
    **/
    function createOrUpdateContact(Request $request,$id=null){
       
        $business_id = request()->session()->get('user.business_id');
       
        if($id){
            $contact = Contact::where('id',$id)->update([
                'type'=> 'contact',
                'business_id' => $business_id,
                'opening_balance' =>  $request->opening_balance,
                'register_module' => 'shipping',
                'name' => $request->name,
                'address' => $request->address,
                'mobile' => $request->mobile_1,
                'landmark' => $request->landmarks,
            ]);
        }
        else
        {
            $contact = Contact::create([
                'type'=> 'contact',
                'business_id' => $business_id,
                'opening_balance' =>  $request->opening_balance,
                'register_module' => 'shipping',
                'name' => $request->name,
                'address' => $request->address,
                'landmark' => $request->landmarks,
            ]);
        }
        return $id ?? $contact->id;
    }
}
