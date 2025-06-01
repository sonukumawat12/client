<?php

namespace Modules\Superadmin\Http\Controllers;

use Modules\Superadmin\Entities\RefillBusiness;
use Illuminate\Http\Request;
use App\Utils\TransactionUtil;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;

use Modules\Superadmin\Entities\SmsApiClient;
use Modules\Superadmin\Entities\SmsRefillPackage;
use App\Business;
use Illuminate\Support\Facades\DB;
use App\SmsLog;

class RefillBusinessController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $transactionUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil)
    {
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        if (request()->ajax()) {
            
            $drivers = RefillBusiness::leftjoin('sms_refill_packages','sms_refill_packages.id','refill_business.package_id')
                ->leftjoin('users', 'users.id', 'refill_business.created_by')
                ->select([
                    'sms_refill_packages.name as package_name',
                    'sms_refill_packages.amount',
                    'sms_refill_packages.no_of_sms',
                    'refill_business.*',
                    'users.username',
                ]);
                
            if(!empty(request()->start_date) && !empty(request()->end_date)){
                $drivers->whereDate('refill_business.date','>=',request()->start_date)->whereDate('refill_business.date','<=',request()->end_date);
            }
            
            if(!empty(request()->business_id) && !empty(request()->type)){
                $drivers->where('refill_business.business_id',request()->business_id)->where('refill_business.type',request()->type);
            }
            
            if(!empty(request()->package_id)){
                $drivers->where('refill_business.package_id',request()->package_id);
            }
            
            if(!empty(request()->payment_method)){
                $drivers->where('refill_business.payment_method',request()->payment_method);
            }
            
            if(!empty(request()->created_by)){
                $drivers->where('refill_business.created_by',request()->created_by);
            }
            
            if(!empty(request()->business_type)){
                $drivers->where('refill_business.type',request()->business_type);
            }

            
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
                        
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Superadmin\Http\Controllers\RefillBusinessController@edit', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Superadmin\Http\Controllers\RefillBusinessController@destroy', [$row->id]) . '" class="delete_record"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        
                         
                        return $html;
                    }
                )
                ->editColumn('type','{{ucfirst($type)}}')
                ->addColumn('business_name',function($row){
                    if($row->type == 'business'){
                        $business = Business::findOrFail($row->business_id);
                    }else{
                        $business = SmsApiClient::findOrFail($row->business_id);
                    }
                    
                    return $business->name;
                })
                
                ->editColumn('payment_method',function($row){
                    if($row->payment_method == 'Cheque'){
                        $html = $row->payment_method."<br><b>Bank Name</b>: ".$row->bank_name."<br><b>Cheque No</b>: ".$row->cheque_no."<br><b>Cheque Date</b>: ".$this->transactionUtil->format_date($row->cheque_date);
                    
                        return $html;
                    }
                    
                    return $row->payment_method;
                })
                ->editColumn('date','{{@format_date($date)}}')
                ->editColumn('expiry_date','{{@format_date($expiry_date)}}')
                ->editColumn('no_of_sms','{{@num_format($no_of_sms)}}')
                ->editColumn('amount',function($row){
                    $html = $this->transactionUtil->num_f($row->amount)."<br>";
                    
                    if(!empty($row->note)){
                        $html .= "<button class='btn btn-primary note_btn' data-string='".$row->note."'>Note</button>";
                    }
                    
                    return $html;
                })
                ->removeColumn('id')
                ->rawColumns(['action','payment_method','amount'])
                ->make(true);
        }
        
    }
    
    public function businessSMSSummary()
    {
        
        if (request()->ajax()) {
            
            $drivers = Business::select(DB::raw("'business' as type"),'business.id as id','business.name as name');
            
            $external = SmsApiClient::select(DB::raw("'client' as type"),'sms_api_clients.id as id','sms_api_clients.name as name');
            
            $data = $drivers->unionAll($external);
            
            
            return DataTables::of($data)
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
                        
                            $html .= '<li><a href="#" data-href="' . action('\Modules\SMS\Http\Controllers\SmsListInterestController@index', ['business_id'=>$row->id,'type' => $row->type]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("sms::lang.interest") . '</a></li>';
                        
                        
                        return $html;
                    }
                )
                ->editColumn('type','{{ucfirst($type)}}')
                ->addColumn('sms_balance',function($row){
                    return $this->transactionUtil->num_f($this->transactionUtil->__getSMSBalance(date('Y-m-d'),$row->id, $row->type));
                })
                ->rawColumns(['action','payment_method','amount'])
                ->make(true);
        }
        
    }
    
    public function smsHistory(){
        if (request()->ajax()) {
            
            $drivers =SmsLog::orderBy('id','DESC');
            
            
            if(!empty(request()->start_date) && !empty(request()->end_date)){
                $drivers->whereDate('created_at','>=',request()->start_date)->whereDate('created_at','<=',request()->end_date);
            }
            
            if(!empty(request()->business_id) && !empty(request()->type)){
                $drivers->where('business_id',request()->business_id)->where('business_type',request()->type);
            }
            
            if(!empty(request()->business_type)){
                $drivers->where('business_type',request()->business_type);
            }
            
            if(!empty(request()->username)){
                $drivers->where('username',request()->username);
            }
            
            if(!empty(request()->sender_name)){
                $drivers->where('sender_name',request()->sender_name);
            }
            
            if(!empty(request()->sms_status)){
                $drivers->where('sms_status',request()->sms_status);
            }
            
            if(!empty(request()->sms_type_)){
                $drivers->where('sms_type_',request()->sms_type_);
            }
            
            return DataTables::of($drivers)
                ->addColumn('business_name',function($row){
                    if($row->business_type == 'business'){
                        $business = Business::findOrFail($row->business_id);
                    }else{
                        $business = SmsApiClient::findOrFail($row->business_id);
                    }
                    
                    return $business->name;
                })
                ->editColumn('message',function($row){
                    $html = "<button class='btn btn-primary msg_btn btn-sm' data-string='".nl2br($row->message)."'>".__('superadmin::lang.message')."</button>";
                    return $html;
                })
                ->editColumn('created_at','{{@format_datetime($created_at)}}')
                ->rawColumns(['action','message'])
                ->make(true);
        }
    }

    public function create()
    {
        $data = array();
        $drivers = Business::select(DB::raw("'business' as type"),'business.id as id','business.name as name');
        $external = SmsApiClient::select(DB::raw("'client' as type"),'sms_api_clients.id as id','sms_api_clients.name as name');
        $business = $drivers->unionAll($external)->get();
            
        $packages = SmsRefillPackage::all();
        
        return view('superadmin::sms_refill_packages.refill_business.create')
                ->with(compact('data','business','packages'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        try {
            							
            $data = array('date' => $request->date,'business_id' => $request->business_id,'package_id' => $request->package_id,'expiry_date' => $request->expiry_date,'note' => $request->note,
                'payment_method' => $request->payment_method, 'bank_name' => $request->bank_name,'cheque_no' => $request->cheque_no, 'cheque_date' => $request->cheque_date, 'type' => $request->type
            );
            
            $data['created_by'] = auth()->user()->id;
            
            RefillBusiness::create($data);
            
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
                'tab' => 'refill_business'
            ];
            
        } catch (\Exception $e) {

            logger($e);
            
            $output = [

                'success' => 0,

                'msg' => __('messages.something_went_wrong'),
                'tab' => 'refill_business'

            ];

        }
        

        return redirect()->back()->with('status', $output);;
    }
    
    public function edit($id)
    {
        $drivers = Business::select(DB::raw("'business' as type"),'business.id as id','business.name as name');
        $external = SmsApiClient::select(DB::raw("'client' as type"),'sms_api_clients.id as id','sms_api_clients.name as name');
        $business = $drivers->unionAll($external)->get();
        
        $packages = SmsRefillPackage::all();
        
        $data = RefillBusiness::findOrFail($id);
        return view('superadmin::sms_refill_packages.refill_business.edit')
                ->with(compact('data','business','packages'));
    }

   

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        
        try {
            $data = array('date' => $request->date,'business_id' => $request->business_id,'package_id' => $request->package_id,'expiry_date' => $request->expiry_date,'note' => $request->note,
                'payment_method' => $request->payment_method, 'bank_name' => $request->bank_name,'cheque_no' => $request->cheque_no, 'cheque_date' => $request->cheque_date, 'type' => $request->type
            );
            
            $data['created_by'] = auth()->user()->id;
            
            RefillBusiness::where('id',$id)->update($data);
            
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
                'tab' => 'refill_business'
            ];
            
        } catch (\Exception $e) {

            logger($e);
            
            $output = [

                'success' => 0,

                'msg' => __('messages.something_went_wrong'),
                'tab' => 'refill_business'

            ];

        }
        

        return redirect()->back()->with('status', $output);;
    }
    
    public function destroy($id)
    {
        try {
            
            RefillBusiness::where('id', $id)->delete();


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
