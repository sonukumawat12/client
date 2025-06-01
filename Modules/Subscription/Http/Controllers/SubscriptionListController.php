<?php

namespace Modules\Subscription\Http\Controllers;


use App\Utils\ModuleUtil;
use App\Utils\BusinessUtil;
;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Business;

use Modules\Subscription\Entities\SubscriptionUserActivity;
use Modules\Subscription\Entities\SubscriptionSmsTemplate;
use Modules\Subscription\Entities\SubscriptionList;
use Modules\Subscription\Entities\SubscriptionSetting;
use Modules\Subscription\Entities\SubscriptionPrice;
use Modules\Subscription\Entities\SubscriptionPayment;
use Yajra\DataTables\DataTables;
use App\Contact;
use Modules\Superadmin\Entities\Subscription;
use Illuminate\Support\Facades\DB;
use App\BusinessLocation;

class SubscriptionListController extends Controller
{
    protected $moduleUtil;
    protected $businessUtil;
    protected $subscription_cycles;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil, BusinessUtil $businessUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->businessUtil = $businessUtil;
        $this->subscription_cycles = array(
                'daily' => 1,
                'weekly' => 7,
                'monthly' => 30,
                'quarterly' => 90,
                'bi_annually' => 180,
                'annually' => 365
            );
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        
        if (request()->ajax()) {
            $settings = SubscriptionList::leftjoin('users','users.id','subscription_lists.created_by')
                                    ->leftjoin('contacts','contacts.id','subscription_lists.contact_id')
                                    ->where('subscription_lists.business_id', $business_id)
                                    ->orderBy('subscription_lists.id','DESC')
                                    ->select('users.username as user','subscription_lists.*','contacts.name as contact_name');
            
            if(!empty(request()->start_date) && !empty(request()->end_date)){
                $settings->whereDate('subscription_lists.transaction_date','>=',request()->start_date)->whereDate('subscription_lists.transaction_date','<=',request()->end_date);
            }
            
            if(!empty(request()->product_id)){
                $settings_id = SubscriptionSetting::where('product',request()->product_id)->pluck('id');
                $settings = $settings->where(function ($query) use ($settings_id) {
                    foreach ($settings_id as $id) {
                        $query->orWhereRaw("FIND_IN_SET('$id', REPLACE(REPLACE(REPLACE(settings_id, '[', ''), ']', ''), '\"', '')) > 0");
                    }
                });
            }
            
            if(!empty(request()->contact_id)){
                $settings->where('subscription_lists.contact_id',request()->contact_id);
            }
            
            if(!empty(request()->subscription_cycle)){
                $settings_id = SubscriptionSetting::where('subscription_cycle',request()->subscription_cycle)->pluck('id');
                $settings = $settings->where(function ($query) use ($settings_id) {
                    foreach ($settings_id as $id) {
                        $query->orWhereRaw("FIND_IN_SET('$id', REPLACE(REPLACE(REPLACE(settings_id, '[', ''), ']', ''), '\"', '')) > 0");
                    }
                });
            }

            return DataTables::of($settings->get())
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
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Subscription\Http\Controllers\SubscriptionListController@edit', [$row->id]) . '" class="btn-modal" data-container=".subscription_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';

                        $html .= '<li><a href="#" data-href="' . action('\Modules\Subscription\Http\Controllers\SubscriptionListController@destroy', [$row->id]) . '" class="delete-button"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        $html .= '<li class="divider"></li>';
                        
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Subscription\Http\Controllers\SubscriptionListController@edit_status', [$row->id]) . '" class="btn-modal" data-container=".subscription_modal"> ' . __("subscription::lang.active_inactive") . '</a></li>';

                        $html .= '<li><a href="#" data-href="' . action('\Modules\Subscription\Http\Controllers\SubscriptionListController@add_payment', [$row->id]) . '" class="btn-modal" data-container=".subscription_modal"> ' . __("subscription::lang.add_payment") . '</a></li>';

                        $html .= '<li><a href="#" data-href="' . action('\Modules\Subscription\Http\Controllers\SubscriptionListController@view_history', [$row->id]) . '" class="btn-modal" data-container=".subscription_modal"> ' . __("subscription::lang.view_history") . '</a></li>';
                        
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Subscription\Http\Controllers\SubscriptionListController@print', $row->id) . '?start_date='.request()->start_date.'&end_date='.request()->end_date.'" class="print_bill" >' . __("messages.print") . '</a></li>';
                        

                        return $html;
                    }
                )
                
                ->addColumn('subscription_amount',function($row){
                    $total_price = 0;
                    if (is_int($row->settings_id)) {
                        $price = SubscriptionPrice::where('settings_id',$row->settings_id)->latest()->first();
                        if(!empty($price)){
                            $total_price += $price->new_amount ;
                        }
                    }else{
                        foreach(json_decode($row->settings_id,true) as $settings_id){
                            
                            $price = SubscriptionPrice::where('settings_id',$settings_id)->latest()->first();
                            if(!empty($price)){
                                $total_price += $price->new_amount ;
                            }
                        }
                    }
                    
                    
                    return $this->moduleUtil->num_f($total_price);
                        
                })
                
                ->addColumn('product',function($row){
                    $html = "";
                    
                    if (is_int($row->settings_id)) {
                        $product = SubscriptionSetting::find($row->settings_id)->product ?? '';
                        $price = SubscriptionPrice::where('settings_id',$row->settings_id)->latest()->first();
                        if(!empty($price)){
                            $html .= "<b>".$product.": </b>".$this->moduleUtil->num_f($price->new_amount)."<br>";
                        }
                    }else{
                        foreach(json_decode($row->settings_id,true) as $settings_id){
                            $product = SubscriptionSetting::find($settings_id)->product ?? '';
                            $price = SubscriptionPrice::where('settings_id',$settings_id)->latest()->first();
                            if(!empty($price)){
                                $html .= "<b>".$product.": </b>".$this->moduleUtil->num_f($price->new_amount)."<br>";
                            }
                        }
                    }
                    
                    
                    
                    return $html;
                        
                })
                
                ->editColumn('send_sms',function($row){
                    if($row->send_sms == 1){
                        return __('lang_v1.yes');
                    }else{
                         return __('lang_v1.no');
                    }
                })
                
                ->editColumn('status',function($row){
                    if($row->status == 1){
                        return "<span class='badge bg-success'>".__('subscription::lang.active')."</span>";
                    }else{
                         return "<span class='badge bg-danger'>".__('subscription::lang.inactive')."</span>";
                    }
                })
                
                ->addColumn('subscription_cycle',function($row){
                    
                    if (is_int($row->settings_id)) {
                        $settings_id = $row->settings_id;
                    }else{
                        $settings_id = json_decode($row->settings_id,true)[0];
                    }
                    
                    
                    $price = SubscriptionSetting::find($settings_id);
                    
                    return __('subscription::lang.'.$price->subscription_cycle);
                })
                
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('expiry_date', function($row){
                    $payments = SubscriptionPayment::where('list_id',$row->id)->latest()->first();
                    if(!empty($payments)){
                        return $this->moduleUtil->format_date($payments->expiry_date);
                    }else{
                        return $this->moduleUtil->format_date($row->expiry_date);
                    }
                })
                ->removeColumn('id')
                ->rawColumns(['action','status','product'])
                ->make(true);
        }
        
        $customers = Contact::customersDropdown($business_id, false);
        $products = SubscriptionSetting::where('business_id',$business_id)->distinct('product')->pluck('product','product');
        $cycles_arr = SubscriptionSetting::where('business_id',$business_id)->distinct('subscription_cycle')->pluck('subscription_cycle','subscription_cycle');
        
        $subscription_cycle = [];
        foreach($cycles_arr as $key => $cycle){
                $subscription_cycle[$key] =  __('subscription::lang.'.$cycle);
            }

        return view('subscription::list.index')->with(compact('business_id','subscription_cycle','products','customers'));
    }
    
    public function listInvoices()
    {
        $business_id = request()->session()->get('business.id');
        
        if (request()->ajax()) {
            $latest_payments = SubscriptionPayment::select(DB::raw('MAX(id) as max_id'))
                                        ->groupBy('list_id')->pluck('max_id');
           logger($latest_payments);
    
            $settings = SubscriptionList::leftjoin('users','users.id','subscription_lists.created_by')
                                    ->leftJoin('subscription_payments', function ($join) use ($latest_payments) {
                                        $join->on('subscription_payments.list_id', '=', 'subscription_lists.id')
                                             ->whereIn('subscription_payments.id', $latest_payments);
                                    })
                                    ->leftjoin('contacts','contacts.id','subscription_lists.contact_id')
                                    ->where('subscription_lists.business_id', $business_id)
                                    ->whereDate('subscription_payments.expiry_date','<', date('Y-m-d'))
                                    ->orderBy('subscription_lists.id','DESC')
                                    ->select('users.username as user','subscription_lists.*','contacts.name as contact_name','subscription_payments.expiry_date as exp_date');
            
            
            if(!empty(request()->product_id)){
                $settings_id = SubscriptionSetting::where('product',request()->product_id)->pluck('id');
                $settings = $settings->where(function ($query) use ($settings_id) {
                    foreach ($settings_id as $id) {
                        $query->orWhereRaw("FIND_IN_SET('$id', REPLACE(REPLACE(REPLACE(settings_id, '[', ''), ']', ''), '\"', '')) > 0");
                    }
                });
            }
            
            if(!empty(request()->contact_id)){
                $settings->where('subscription_lists.contact_id',request()->contact_id);
            }
            
            if(!empty(request()->subscription_cycle)){
                $settings_id = SubscriptionSetting::where('subscription_cycle',request()->subscription_cycle)->pluck('id');
                $settings = $settings->where(function ($query) use ($settings_id) {
                    foreach ($settings_id as $id) {
                        $query->orWhereRaw("FIND_IN_SET('$id', REPLACE(REPLACE(REPLACE(settings_id, '[', ''), ']', ''), '\"', '')) > 0");
                    }
                });
            }

            return DataTables::of($settings->get())
                ->addColumn('subscription_amount',function($row){
                    $total_price = 0;
                    foreach(json_decode($row->settings_id,true) as $settings_id){
                        
                        $price = SubscriptionPrice::where('settings_id',$settings_id)->latest()->first();
                        if(!empty($price)){
                            $total_price += $price->new_amount ;
                        }
                    }
                    
                    return $this->moduleUtil->num_f($total_price);
                        
                })
                
                ->addColumn('product',function($row){
                    $html = "";
                    foreach(json_decode($row->settings_id,true) as $settings_id){
                        $product = SubscriptionSetting::find($settings_id)->product ?? '';
                        $price = SubscriptionPrice::where('settings_id',$settings_id)->latest()->first();
                        if(!empty($price)){
                            $html .= "<b>".$product.": </b>".$this->moduleUtil->num_f($price->new_amount)."<br>";
                        }
                    }
                    
                    return $html;
                        
                })
                
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('expiry_date', function($row){
                    return $this->moduleUtil->format_date($row->exp_date);
                })
                ->removeColumn('id')
                ->rawColumns(['product'])
                ->make(true);
        }
        
        $customers = Contact::customersDropdown($business_id, false);
        $products = SubscriptionSetting::where('business_id',$business_id)->distinct('product')->pluck('product','product');
        $cycles_arr = SubscriptionSetting::where('business_id',$business_id)->distinct('subscription_cycle')->pluck('subscription_cycle','subscription_cycle');
        
        $subscription_cycle = [];
        foreach($cycles_arr as $key => $cycle){
                $subscription_cycle[$key] =  __('subscription::lang.'.$cycle);
            }

        return view('subscription::invoices.index')->with(compact('business_id','subscription_cycle','products','customers'));
    }
    
 

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('business.id');
        $customers = Contact::customersDropdown($business_id, false);
        $cycles_arr = SubscriptionSetting::where('business_id',$business_id)->distinct('subscription_cycle')->pluck('subscription_cycle','subscription_cycle');
        
        $cycles = [];
        foreach($cycles_arr as $key => $cycle){
            $cycles[$key] = __('subscription::lang.'.$cycle);
        }
        
        return view('subscription::list.create')->with(compact('customers','cycles'));
    }
    
    public function getproductCycles($product){
        $products = [];
        $business_id = request()->session()->get('business.id');
        if(!empty($product)){
            $cycles_arr = SubscriptionSetting::where('business_id',$business_id)->where('subscription_cycle',$product)->distinct('product')->pluck('product','id');
            
            foreach($cycles_arr as $key => $cycle){
                $price = SubscriptionPrice::where('settings_id',$key)->latest()->first();
                
                $price_key = !empty($price) ? $price->id : 0;
                $price_amount = !empty($price) ? $price->new_amount : 0;
                
                $cycles[$key] = array('id' => $key, 'name' => $cycle." (".$this->moduleUtil->num_f($price_amount).")", 'days' => $this->subscription_cycles[$product],'price' => $price_amount, 'price_key' => $price_key);
            }
        }
        
        return $cycles;
            
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            
            $business_id = request()->session()->get('business.id');
            
            $data = $request->only(['send_sms','note','transaction_date','contact_id']);
            $data['settings_id']= json_encode($request->product_id);
            $data['business_id'] = $business_id;
            $data['created_by'] = auth()->user()->id;
            $data['status'] = 1;
            
            $transaction_date = \Carbon::parse($request->transaction_date);
            $data['expiry_date'] = $transaction_date->addDays($this->subscription_cycles[$request->subscription_cycle]);
            
            
            
            $setting = SubscriptionList::create($data);

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }
    
   

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('subscription::list.show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        
        $list = SubscriptionList::findOrFail($id);
        
        $total_price = 0;
        $subscription_cycle = null;
        
        foreach(json_decode($list->settings_id,true) as $settings_id){
            $price = SubscriptionPrice::where('settings_id',$settings_id)->latest()->first();
            if(!empty($price)){
                $total_price += $price->new_amount ;
            }
            
            $subscription_cycle = SubscriptionSetting::find($settings_id)->subscription_cycle;
            
        }
        
        $list->subscription_amount = $total_price;
        
        $list->product = json_decode($list->settings_id,true);
        
        $business_id = request()->session()->get('business.id');
        
        $cycles_arr = SubscriptionSetting::where('business_id',$business_id)->distinct('subscription_cycle')->pluck('subscription_cycle','subscription_cycle');
        
        $cycles = [];
        foreach($cycles_arr as $key => $cycle){
            $cycles[$key] = __('subscription::lang.'.$cycle);
        }
        
        $customers = Contact::customersDropdown($business_id, false);
        
        $cycle_details = $this->getproductCycles($subscription_cycle);
        
        return view('subscription::list.edit')->with(compact('cycles','list','customers','cycle_details','subscription_cycle'));
    }
    
    public function add_payment($id)
    {
        
        $list = SubscriptionList::findOrFail($id);
        
        $total_price = 0;
        foreach(json_decode($list->settings_id,true) as $settings_id){
            
            $price = SubscriptionPrice::where('settings_id',$settings_id)->latest()->first();
            if(!empty($price)){
                $total_price += $price->new_amount ;
            }
        }
        
        $list->subscription_amount = $total_price;
        
        $expiry_date = SubscriptionPayment::where('list_id',$id)->latest()->first()->expiry_date ?? $list->expiry_date;
        
        $list->expiry_date = $expiry_date;
        
        $settings = SubscriptionSetting::findOrFail(json_decode($list->settings_id,true)[0]);
            
        $transaction_date = \Carbon::parse($expiry_date);
        $list->new_expiry_date = $transaction_date->addDays($this->subscription_cycles[$settings->subscription_cycle]);
        
       
        return view('subscription::list.add_payment')->with(compact('list'));
    }
    
    public function edit_status($id)
    {
        
        $list = SubscriptionList::findOrFail($id);
        
        return view('subscription::list.edit_status')->with(compact('list'));
    }
    
    public function view_history($id)
    {
        
        $payments = SubscriptionPayment::leftjoin('subscription_lists','subscription_lists.id','subscription_payments.list_id')->where('list_id',$id)->orderBy('subscription_payments.id','DESC')->get();
        
        return view('subscription::list.view_history')->with(compact('payments'));
    }
    
    public function view_expiring()
    {
        $business_id = request()->session()->get('business.id');
        $settings = SubscriptionList::leftjoin('contacts','contacts.id','subscription_lists.contact_id')
                                    ->leftjoin('subscription_settings','subscription_settings.id','subscription_lists.settings_id')
                                    ->where('subscription_lists.business_id', $business_id)
                                    ->orderBy('subscription_lists.id','DESC')
                                    ->select('subscription_lists.*','contacts.name as contact_name','subscription_settings.product')->get();
        
        return view('subscription::list.view_expiring')->with(compact('settings'));
    }
    
    public function notify_expiring(){
        $subscriptions = Subscription::groupBy('business_id')->orderBy('end_date','DESC')->get();
        
        if(!empty($subscriptions)){
            foreach($subscriptions as $sub){
               
                $business = Business::where('id', $sub->business_id)->first();
                
                $sms_settings = empty($business->sms_settings) ? $this->businessUtil->defaultSmsSettings() : $business->sms_settings;
                
                
                $settings = SubscriptionList::leftjoin('contacts','contacts.id','subscription_lists.contact_id')
                                    ->where('subscription_lists.business_id', $sub->business_id)
                                    ->orderBy('subscription_lists.id','DESC')
                                    ->select('subscription_lists.*','contacts.name as contact_name')->get();
                                    
                foreach($settings as $one){
                    $payments = SubscriptionPayment::where('list_id',$one->id)->latest()->first();
                    
                    $total_price = 0;
                    $product = '';
                    foreach(json_decode($one->settings_id,true) as $key => $settings_id){
                        $prod = SubscriptionSetting::find($settings_id)->product ?? '';
                        $price = SubscriptionPrice::where('settings_id',$settings_id)->latest()->first();
                        if(!empty($price)){
                            $total_price += $price->new_amount ;
                            
                            if(!empty($prod)){
                                if($key > 0){
                                    $product .= ', ';
                                    $product .= $prod."(".$this->moduleUtil->num_f($price->new_amount).")";
                                }
                            }
                        }
                    }
        
        
                    $price = $total_price;
                    
                    $contact = Contact::find($one->contact_id);
                    
                    if(!empty($payments)){
                        $expiry_date = $payments->expiry_date;
                    }else{
                        $expiry_date =  $one->expiry_date;
                    }
                    
                    // Calculate the difference in days between the expiry date and today's date
                    $daysUntilExpiry = \Carbon::parse($expiry_date)->diffInDays(\Carbon::now());
                    
                    $template = SubscriptionSmsTemplate::where('business_id',$sub->business_id)->first();
                    if(!empty($template) && $one->send_sms == 1){
                        $send_sms = 0;
                        
                        // notification #1
                        if($template->days_1_status == 1 && $template->days_1 == $daysUntilExpiry){
                            $send_sms = 1;
                        }
                        // notification #1
                        if($template->days_2_status == 1 && $template->days_2 == $daysUntilExpiry){
                            $send_sms = 1;
                        }
                        
                        // notification #1
                        if($template->days_3_status == 1 && $template->days_3 == $daysUntilExpiry){
                            $send_sms = 1;
                        }
                        
                        // notification #1
                        if($template->days_4_status == 1 && $template->days_4 == $daysUntilExpiry){
                            $send_sms = 1;
                        }
                       
                        $msg = $template->sms_body;
                        $msg = str_replace('{business_name}',$business->name,$msg);
                        $msg = str_replace('{amount}',$this->moduleUtil->num_f($price, false, $business, true),$msg);
                        $msg = str_replace('{product_name}',$product,$msg);
                        $msg = str_replace('{expiry_date}',date($business->date_format,strtotime($expiry_date)),$msg);
                        
                        if(!empty($contact) && $send_sms == 1){
                            $data = [
                                'sms_settings' => $sms_settings,
                                'mobile_number' => $contact->mobile,
                                'sms_body' => $msg
                            ];
                            
                            $response = $this->businessUtil->superadminSendSms($data);
                            
                            $data['mobile_number'] = $contact->alternate_number;
                            
                            $response = $this->businessUtil->superadminSendSms($data);
                        }
                    }
                }
                
                
            }
        }
        
        
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request,$id)
    {
        try {
            $business_id = request()->session()->get('business.id');
            
            $data = $request->only(['send_sms','note','transaction_date','contact_id']);
            $data['settings_id']= json_encode($request->product_id);
            $data['business_id'] = $business_id;
            $data['created_by'] = auth()->user()->id;
            
            $transaction_date = \Carbon::parse($request->transaction_date);
            $data['expiry_date'] = $transaction_date->addDays($this->subscription_cycles[$request->subscription_cycle]);
            
            $subscription = SubscriptionList::findOrFail($id);
            
            $exists = SubscriptionList::where('id',$id)->update($data);
            
            $is_editted = 0;
            $description = "";
            
            if($subscription->transaction_date != $data['transaction_date']){
                $is_editted = 1;
                $description .= "Date changed from ".$this->moduleUtil->format_date($subscription->transaction_date)." to ".$this->moduleUtil->format_date($data['transaction_date']).PHP_EOL;
            }
            
            if($subscription->note != $data['note']){
                $is_editted = 1;
                $description .= "Note changed from ".($subscription->note)." to ".($data['note']).PHP_EOL;
            }
            
            
            
            if($subscription->contact_id != $data['contact_id']){
                
                $old_contact = Contact::findOrFail($subscription->contact_id);
                $new_contact = Contact::findOrFail($data['contact_id']);
                
                $description .= "Client changed from ".($old_contact->name)." to ".($new_contact->name).PHP_EOL;
                
                $is_editted = 1;
            }
            
            if($subscription->send_sms != $data['send_sms']){
                
                $old = $subscription->send_sms == 1 ? __('lang_v1.yes') : __('lang_v1.no');
                $new = $data['send_sms'] == 1 ? __('lang_v1.yes') : __('lang_v1.no');
                
                $description .= "Send SMS changed from ".($old)." to ".($new).PHP_EOL;
                
                $is_editted = 1;
            }
            
           
           
            
            if($is_editted == 1){
                SubscriptionUserActivity::create(array('business_id' => $business_id, 'model' => 'SubscriptionList','description' => 'update', 'subject_id' => $subscription->id, 'properties' => $description, 'created_by' => auth()->user()->id));
            }
            
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }
    
    public function save_payment(Request $request,$id)
    {
        try {
            $business_id = request()->session()->get('business.id');
            
            $data = $request->only(['note','transaction_date']);
            $data['amount']= $this->moduleUtil->num_uf($request->subscription_amount);
            $data['list_id'] = $id;
            $data['created_by'] = auth()->user()->id;
            $data['expiry_date'] = $request->new_expiry_date;
            
            
            SubscriptionPayment::create($data);
           
            
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }
    
    public function update_status(Request $request,$id)
    {
        try {
            $business_id = request()->session()->get('business.id');
            
            $data = $request->only(['status']);
            
            $subscription = SubscriptionList::findOrFail($id);
            
            $exists = SubscriptionList::where('id',$id)->update($data);
            
            $is_editted = 0;
            $description = "";
            
            
            if($subscription->status != $data['status']){
                
                $old = $subscription->status == 1 ? __('subscription::lang.active') : __('subscription::lang.inactive');
                $new = $data['status'] == 1 ? __('subscription::lang.active') : __('subscription::lang.inactive');
                
                $description .= "Status changed from ".($old)." to ".($new).PHP_EOL;
                
                $is_editted = 1;
            }
            
           
           
            
            if($is_editted == 1){
                SubscriptionUserActivity::create(array('business_id' => $business_id, 'model' => 'SubscriptionList','description' => 'update', 'subject_id' => $subscription->id, 'properties' => $description, 'created_by' => auth()->user()->id));
            }
            
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }
    
    public function print($id)
    {
        $settings = SubscriptionList::leftjoin('users','users.id','subscription_lists.created_by')
                                    ->leftjoin('contacts','contacts.id','subscription_lists.contact_id')
                                    ->where('subscription_lists.id',$id)
                                    ->orderBy('subscription_lists.id','DESC')
                                    ->select('users.username as user','subscription_lists.*','contacts.name as customer_name')->first();
        
        $business_details = $this->businessUtil->getDetails($settings->business_id);
        $location_details = BusinessLocation::where('business_id',$settings->business_id)->first();
        $settings->customer = Contact::find($settings->contact_id);
        
        $products = array();
        foreach(json_decode($settings->settings_id,true) as $settings_id){
            $product = SubscriptionSetting::find($settings_id)->product ?? '';
            $price = SubscriptionPrice::where('settings_id',$settings_id)->latest()->first();
            $products[] = array('product' => $product, 'price' => $price->new_amount);
        }
    
        return view('subscription::list.print')->with(compact('settings', 'business_details','location_details','products'));
    }
    
   
    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
         try {
            SubscriptionSetting::where('id', $id)->delete();
            SubscriptionPrice::where('settings_id', $id)->delete();
            $output = [
                'success' => true,
                'msg' => __('fleet::lang.success')
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
