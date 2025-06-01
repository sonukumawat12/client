<?php

namespace Modules\Shipping\Http\Controllers;
use Mpdf\Mpdf;
use App\ContactLedger;
use App\Account;
use App\AccountType;
use App\BusinessLocation;
use App\ExpenseCategory;
use App\Transaction;
use App\OpeningBalance;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Modules\Superadmin\Entities\Subscription;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Modules\Shipping\Entities\Fleet;
use Modules\Shipping\Entities\Route;
use App\Contact;
use Yajra\DataTables\Facades\DataTables;
use Modules\Fleet\Entities\RouteOperation;
use App\AccountTransaction;
use Modules\Shipping\Entities\ShippingAgentLedger;
use Modules\Shipping\Entities\ShippingAgent;

use Modules\Shipping\Entities\ShippingPartner;

use Modules\Shipping\Entities\ShippingInvoiceSend;
use Modules\Shipping\Entities\Shipment;
use Modules\Shipping\Entities\ShipmentPackage;
use Illuminate\Support\Facades\Mail;
use Modules\Shipping\Entities\ShippingDelivery;
use Modules\Shipping\Entities\ShippingStatus;
use Modules\Shipping\Entities\ShippingChangeStatus;
use Modules\Shipping\Entities\ShippingAgentCommission;

use Modules\Shipping\Entities\ShippingPartnerCommission;

use App\Currency;
use Modules\Shipping\Entities\ShippingAccount;

class ShippingController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    protected $transactionUtil;
    
    public function __construct(TransactionUtil $transactionUtil)
    {
        $this->transactionUtil = $transactionUtil;
    }
    
    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        
        if (request()->ajax()) {
            $drivers = Shipment::with('location.currency')->leftjoin('users', 'shipments.created_by', 'users.id')
                ->leftjoin('contacts', 'shipments.customer_id', 'contacts.id')
                ->leftjoin('shipping_agents', 'shipments.agent_id', 'shipping_agents.id')
                ->leftjoin('shipping_recipients', 'shipments.recipient_id', 'shipping_recipients.id')
                ->leftjoin('shipping_mode', 'shipments.shipping_mode', 'shipping_mode.id')
                ->leftjoin('shipping_packages', 'shipments.package_type_id', 'shipping_packages.id')
                ->leftjoin('shipping_delivery', 'shipments.schedule_id', 'shipping_delivery.id')
                ->leftjoin('shipping_partners', 'shipments.shipping_partner', 'shipping_partners.id')
                ->leftjoin('shipping_status', 'shipments.delivery_status', 'shipping_status.id')
                ->leftjoin('transactions','transactions.id','shipments.transaction_id')
                ->leftjoin('transaction_payments','transactions.id','transaction_payments.transaction_id')
                ->where('shipments.business_id', $business_id)
                ->select([
                    'shipments.*',
                    'users.username as created_by',
                    'contacts.name as sender',
                    'shipping_agents.name as agent',
                    'shipping_recipients.name as recipient',
                    'shipping_mode.shipping_mode as mode',
                    'shipping_packages.package_name as package',
                    'shipping_delivery.shipping_delivery as delivery',
                    'shipping_partners.name as partner',
                    'shipping_status.shipping_status as status',
                    DB::raw('(SELECT SUM(transaction_payments.amount) FROM transaction_payments WHERE
                        transaction_payments.transaction_id=transactions.id) as total_paid')
                ]);
            
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
                         if (

                            auth()

                                ->user()

                                ->can('shipping.helpers.view')

                        ) {

                            $html .= '<li><a href="' . action('\Modules\Shipping\Http\Controllers\AddShipmentController@show', [$row->id]) . '" ><i class="glyphicon glyphicon-eye-open"></i> ' . __('messages.view') . '</a></li>';

                        }
                        
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Shipping\Http\Controllers\AddShipmentController@updatePackages', ['id' => $row->id]) . '" class="btn-modal" data-container=".scancode_modal"><i class="fa fa-pencil"></i> ' . __('shipping::lang.add_dimensions') . '</a></li>';
                        
                        if (

                            auth()

                                ->user()

                                ->can('shipping.helpers.view')

                        ) {

                            $html .= '<li><a href="#" data-href="' . action('\Modules\Shipping\Http\Controllers\BarQrCodeController@scanCode', ['id' => $row->id]) . '" class="btn-modal" data-container=".scancode_modal"><i class="glyphicon glyphicon-qrcode"></i> ' . __('messages.barandqrcode') . '</a></li>';

                        }
                        if (

                            auth()

                                ->user()

                                ->can('shipping.helpers.edit') 

                        ) {

                            $html .= '<li><a href="' . action('\Modules\Shipping\Http\Controllers\AddShipmentController@edit', [$row->id]) . '" ><i class="glyphicon glyphicon-edit"></i> ' . __('messages.edit') . '</a></li>';

                        }                   
                        $html .= '<li><a href="#"  class="btn-modal" onclick="openSendInvoiceModal('.$row->id.')" ><i class="glyphicon glyphicon-file"></i> ' . __('messages.send_invoice') . '</a></li>';
                        $html .= '<li><a href="#"  class="btn-modal"   onclick=\'openChangeStatusModal('.$row->id.',"'.$row->status.'","'.date("Y-m-d", strtotime($row->delivery_time)).'")\' ><i class="glyphicon glyphicon-tag"></i> ' . __('messages.change_status') . '</a></li>';
                        $html .= '<li class="divider"></li>';
                        
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Shipping\Http\Controllers\ShippingController@addAgentCommission', [$row->id]) . '" class="btn-modal" data-container=".scancode_modal" >' . __('shipping::lang.add_agent_commission') . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Shipping\Http\Controllers\ShippingController@addPartnerCommission', [$row->id]) . '" class="btn-modal" data-container=".scancode_modal" >' . __('shipping::lang.add_partner_commission') . '</a></li>';
                        
                        return $html;
                    }
                )
                ->editColumn('shipper_tracking_no',function($row){
                    $html ="";
                    if(is_null($row->shipper_tracking_no)){
                        if (auth()->user()->can('shipping.shipper_tracking_no.add')) {
                            $html .= '<button type="button" class="btn btn-primary btn-modal"  data-value="'.$row->shipper_tracking_no.'" onclick="openShipperTrackingModal(\''.$row->shipper_tracking_no.'\','.$row->id.')">
                       Enter</button>';
                        }else{
                            $html .= '';
                        }                       
                    }else{
                        if (auth()->user()->can('shipping.shipper_tracking_no.edit')) {
                            $html .= '<button type="button" class="btn btn-primary btn-modal"   data-value="'.$row->shipper_tracking_no.'" onclick="openShipperTrackingModal(\''.$row->shipper_tracking_no.'\','.$row->id.')">
                            Edit</button>';
                        }else{
                            $html .= $row->shipper_tracking_no;
                        }   
                    }
                    
                    $html .= "<br><br>";
                    
                    $packages = ShipmentPackage::where('shipment_id',$row->id)->get();
                    if(!empty($packages)){
                        $package_html = "";
                        foreach($packages as $pac){
                            $package_html .= "<b>".__( 'shipping::lang.package_name' )."</b>: ".$pac->package_name."<br>";
                            
                            $package_html .= "<b>".__( 'shipping::lang.length_cm' )."</b>: ".$this->transactionUtil->num_uf($pac->length)."<br><b>".__( 'shipping::lang.width_cm' )."</b>: ".$this->transactionUtil->num_uf($pac->width). "<br><b>".__( 'shipping::lang.height_cm' )."</b>: ".$this->transactionUtil->num_uf($pac->height)."<br><b>".__( 'shipping::lang.weight_cm' )."</b>: ".$this->transactionUtil->num_uf($pac->weight);
                            
                            if(!empty($pac->package_description)){
                                $package_html .=  "<br><b>".__( 'shipping::lang.package_description' )."</b>: <br>".nl2br($pac->package_description);
                            }
                           
                            
                            $package_html .= "<hr>";
                        }
                        
                        $html .= "<button class='btn btn-success note_btn btn-sm' data-string='".$package_html."'>".__('shipping::lang.package_description')."</button>";
                    }
                    
                    
                    return $html;
                })
                ->editColumn('operation_date', '{{@format_date($operation_date)}}')
                // ->editColumn('total', '{{@num_format($total)}}')
                ->editColumn('total', function($row){
                    return locationCurrency($row->total,$row->location->currency);
                })
                ->editColumn('delivery_time', '{{@format_date($delivery_time)}}')
                ->editColumn('sender',function($row){
                    $html = $row->sender."&nbsp;";
                    if($row->total_paid > 0){
                            if($row->final_total <= $row->total_paid){
                                $pstatus = "paid";
                            }else{
                                $pstatus = "partial";
                            } 
                        }else{
                            $pstatus = "due";
                        }
                    $html .=  (string) view('shipping::shipping.payment_status', ['payment_status' => $pstatus, 'id' => $row->transaction_id]);
                    
                    return $html;
                })
                ->editColumn('agent',function($row){
                    $html = $row->agent."&nbsp;";
                    
                    if(empty($row->agent)){
                        $html .= '<button type="button" class="btn btn-primary add-agent-btn-modal"  data-id="'.$row->id.'">Enter</button><br>'; 
                    }
                    
                    
                    $in = ShippingAgentLedger::where('agent_id',$row->agent_id)->where('shipment_id',$row->id)->where('type','debit')->sum('amount');
                    $out = ShippingAgentLedger::where('agent_id',$row->agent_id)->where('shipment_id',$row->id)->where('type','credit')->sum('amount');
                    
                    if($out > 0){
                            if($in <= $out){
                                $pstatus = "paid";
                            }else{
                                $pstatus = "partial";
                            } 
                    }else{
                        $pstatus = "due";
                    }
                    $html .=  (string) view('shipping::shipping.payment_status', ['payment_status' => $pstatus, 'id' => $row->transaction_id]);
                    
                    return $html;
                    
                })
                ->editColumn('partner',function($row){
                    $html = $row->partner."&nbsp;";
                    
                    if(empty($row->partner)){
                        $html .= '<button type="button" class="btn btn-primary add-partner-btn-modal"  data-id="'.$row->id.'">Enter</button><br>'; 
                    }
                    
                    return $html;
                    
                })
                ->removeColumn('id')
                ->rawColumns(['shipper_tracking_no','action','sender','agent','partner'])
                ->make(true);
        }
        $id         = $request->input('id');
        $flag_scan = 0;
        $delivery_scan = '';
        $delivery_time = '';

        $business_id = request()
        ->session()
        ->get('business.id');

        $shipping_delivery = ShippingDelivery::where('shipping_delivery.business_id', $business_id)
        ->select('shipping_delivery','id')
        ->get()->pluck('shipping_delivery','id');

        if(isset($id)){
            $data = Shipment::leftjoin('shipping_status', 'shipments.delivery_status', 'shipping_status.id')->where(["shipments.id"=>$id])->select([
                'shipments.*',
                'shipping_status.shipping_status as status'
            ])->get();
            if(isset($data[0])){
                $flag_scan = 1;
                $delivery_scan = $data[0]->status;
                $delivery_time = date("Y-m-d", strtotime($data[0]->delivery_time));
            }
        }

        $currency = Currency::where('id', request()->session()->get('business.currency_id'))->first();
        

        $status = ShippingStatus::where('shipping_status.business_id', $business_id)
            ->select('shipping_status.*')
            ->get();
        $shipping_status = $status->pluck('shipping_status', 'id');
        
        $shipping_agents = ShippingAgent::where('shipping_agents.business_id', $business_id)
            ->select('name','id')->get()->pluck('name','id');
        
        $shipping_partners = ShippingPartner::where('business_id', $business_id)
            ->select('name','id')->get()->pluck('name','id');
       
        return view('shipping::shipping.index')->with(
            compact('shipping_delivery','shipping_status','delivery_scan','delivery_time','flag_scan','id','currency','shipping_agents','shipping_partners'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('shipping::create');
    }
    
    public function addAgentCommission($id)
    {
        $business_id = request()->session()->get('business.id');
        
        $shipment = Shipment::findOrFail($id);
        
        if($shipment->commission_status == 1){
            $commission = ShippingAgentCommission::where('shipment_id',$id)->first();
        }else{
            $commission = null;
        }
        
        $shipment_details = Shipment::leftjoin('contacts', 'shipments.customer_id', 'contacts.id')
                ->leftjoin('shipping_mode','shipping_mode.id','shipments.shipping_mode')
                ->leftjoin('shipping_packages','shipping_packages.id','shipments.package_type_id')
                ->leftjoin('shipping_agents','shipping_agents.id','shipments.agent_id')
                ->leftjoin('shipping_partners','shipping_partners.id','shipments.shipping_partner')
                ->where('shipments.id', $id)
                ->select([
                    'shipments.tracking_no',
                    'shipping_mode.shipping_mode',
                    'shipping_packages.package_name',
                    'shipping_agents.name as agent_name',
                    'shipping_partners.name as partner_name',
                    'contacts.name as customer_name'
                ])->first();
            
        return view('shipping::shipping.add_agent_commission')->with(compact(
            'shipment','commission','shipment_details'
        ));
    }
    
    public function storeAgentCommission(Request $request, $id){
        $business_id = request()->session()->get('business.id');
        try {
            
            DB::beginTransaction();
            
            $shipment = Shipment::findOrFail($id);
            
            Shipment::where('id',$id)->update(['commission_status' => 1]);
            
            $data = $request->only('shipment_id','amount','transaction_date');
            $data['transaction_date'] = $this->transactionUtil->uf_date($data['transaction_date'],true);
            $data['business_id'] = $business_id;
            $data['created_by'] = Auth::user()->id;
            $data['agent_id'] = $shipment->agent_id;
            
            $commission = ShippingAgentCommission::create($data);
            
            $transaction_data = [];
            $transaction_data['ref_no'] = $commission->id;
            $transaction_data['status'] = 'final';
            $transaction_data['total_before_tax'] = $request->amount;
            $transaction_data['final_total'] = $request->amount;
            
            $transaction_data['business_id'] = $business_id;
            $transaction_data['created_by'] = auth()->user()->id;
            $transaction_data['type'] = 'shipping_agent_commission';
            $transaction_data['payment_status'] = 'due';
            
            $transaction_data['transaction_date'] = date('Y-m-d');
            $transaction = Transaction::create($transaction_data);

            $commission->transaction_id = $transaction->id;
            $commission->save();
            
            $account_payable = Account::where('business_id', $business_id)->where('name', 'Accounts Payable')->where('is_closed', 0)->first();
            $credit_data = [
                    'amount' => $request->amount,
                    'transaction_id' => $transaction->id,
                    'type' => 'credit',
                    'sub_type' => null,
                    'operation_date' => $transaction_data['transaction_date'],
                    'created_by' => session()->get('user.id')
                ];
            if(!empty($account_payable)){
                $credit_data['account_id'] = $account_payable->id;
                $credit = AccountTransaction::createAccountTransaction($credit_data);
            }
            
            $shipment = Shipment::find($request->shipment_id);
            $shipping_account = ShippingAccount::where('business_id',$business_id)
                                ->where(function ($query) use($shipment) {
                                    $query->whereNull('shipping_mode')
                                          ->orWhere('shipping_mode', $shipment->shipping_mode);
                                })
                                ->where(function ($query) use($shipment) {
                                    $query->whereNull('shipping_partner')
                                          ->orWhere('shipping_partner', $shipment->shipping_partner);
                                })
                                ->first();
                                    
            if(!empty($shipping_account)){
                $credit_data['account_id'] = $shipping_account->income;
                $credit_data['type'] = 'debit';
                $debit = AccountTransaction::createAccountTransaction($credit_data);
            }
            
            DB::commit();
            

            $output = [
                'success' => true,
                'tab' => 'agents',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            DB::rollback();
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'agents',
                'msg' => __('messages.something_went_wrong')
            ];
        }
        
        return redirect()->back()->with('status', $output);

    }
    
    
    public function addPartnerCommission($id)
    {
        $business_id = request()->session()->get('business.id');
        
        $shipment = Shipment::findOrFail($id);
        
        if($shipment->partner_commission_status == 1){
            $commission = ShippingPartnerCommission::where('shipment_id',$id)->first();
        }else{
            $commission = null;
        }
        
        $shipment_details = Shipment::leftjoin('contacts', 'shipments.customer_id', 'contacts.id')
                ->leftjoin('shipping_mode','shipping_mode.id','shipments.shipping_mode')
                ->leftjoin('shipping_packages','shipping_packages.id','shipments.package_type_id')
                ->leftjoin('shipping_agents','shipping_agents.id','shipments.agent_id')
                ->leftjoin('shipping_partners','shipping_partners.id','shipments.shipping_partner')
                ->where('shipments.id', $id)
                ->select([
                    'shipments.tracking_no',
                    'shipping_mode.shipping_mode',
                    'shipping_packages.package_name',
                    'shipping_agents.name as agent_name',
                    'shipping_partners.name as partner_name',
                    'contacts.name as customer_name'
                ])->first();
            
        return view('shipping::shipping.add_partner_commission')->with(compact(
            'shipment','commission','shipment_details'
        ));
    }
    
    public function storePartnerCommission(Request $request, $id){
        $business_id = request()->session()->get('business.id');
        try {
            
            DB::beginTransaction();
            
            $shipment = Shipment::findOrFail($id);
            
            Shipment::where('id',$id)->update(['partner_commission_status' => 1]);
            
            $data = $request->only('shipment_id','amount','transaction_date');
            $data['transaction_date'] = $this->transactionUtil->uf_date($data['transaction_date'],true);
            $data['business_id'] = $business_id;
            $data['created_by'] = Auth::user()->id;
            $data['partner_id'] = $shipment->shipping_partner;
           
            $commission = ShippingPartnerCommission::create($data);
            
            $transaction_data = [];
            $transaction_data['ref_no'] = $commission->id;
            $transaction_data['status'] = 'final';
            $transaction_data['total_before_tax'] = $request->amount;
            $transaction_data['final_total'] = $request->amount;
            
            $transaction_data['business_id'] = $business_id;
            $transaction_data['created_by'] = auth()->user()->id;
            $transaction_data['type'] = 'shipping_partner_commission';
            $transaction_data['payment_status'] = 'due';
            
            $transaction_data['transaction_date'] = date('Y-m-d');
            $transaction = Transaction::create($transaction_data);

            $commission->transaction_id = $transaction->id;
            $commission->save();
            
            $account_payable = Account::where('business_id', $business_id)->where('name', 'Accounts Payable')->where('is_closed', 0)->first();
            $credit_data = [
                    'amount' => $request->amount,
                    'transaction_id' => $transaction->id,
                    'type' => 'credit',
                    'sub_type' => null,
                    'operation_date' => $transaction_data['transaction_date'],
                    'created_by' => session()->get('user.id')
                ];
            if(!empty($account_payable)){
                $credit_data['account_id'] = $account_payable->id;
                $credit = AccountTransaction::createAccountTransaction($credit_data);
            }
            
            $shipment = Shipment::find($request->shipment_id);
            $shipping_account = ShippingAccount::where('business_id',$business_id)
                                ->where(function ($query) use($shipment) {
                                    $query->whereNull('shipping_mode')
                                          ->orWhere('shipping_mode', $shipment->shipping_mode);
                                })
                                ->where(function ($query) use($shipment) {
                                    $query->whereNull('shipping_partner')
                                          ->orWhere('shipping_partner', $shipment->shipping_partner);
                                })
                                ->first();
                                    
            if(!empty($shipping_account)){
                $credit_data['account_id'] = $shipping_account->income;
                $credit_data['type'] = 'debit';
                $debit = AccountTransaction::createAccountTransaction($credit_data);
            }
            
            DB::commit();
            

            $output = [
                'success' => true,
                'tab' => 'agents',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            DB::rollback();
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
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $id                     = $request->shipment_id_hidden;
        $shipper_tracking_no    = $request->shipper_tracking_no;
        Shipment::find($id)->update(['shipper_tracking_no' => $shipper_tracking_no]);
        return redirect()->back();
    }
    
    public function addAgent(Request $request){
        $business_id = request()->session()->get('business.id');
        try {
            
            $id = $request->shipment_id;
            $agent_no = $request->agent_no;
            
            $shipment = Shipment::where('id',$id)->update(['agent_id' => $agent_no]);
            $output = [
                'success' => true,
                'tab' => 'agent_details',
                'msg' => __('lang_v1.success')
            ];
            
            return $output;
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            DB::rollBack();
            $output = [
                'success' => false,
                'tab' => 'agent_details',
                'msg' => __('messages.something_went_wrong')
            ];
            
            return $output;
        }
    }
    
    public function addPartner(Request $request){
        $business_id = request()->session()->get('business.id');
        try {
            
            $id = $request->shipment_id;
            $shipping_partner = $request->shipping_partner;
            
            $shipment = Shipment::where('id',$id)->update(['shipping_partner' => $shipping_partner]);
            
            logger($request->shipment_id);
            
            $output = [
                'success' => true,
                'tab' => 'agent_details',
                'msg' => __('lang_v1.success')
            ];
            
            return $output;
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            DB::rollBack();
            $output = [
                'success' => false,
                'tab' => 'agent_details',
                'msg' => __('messages.something_went_wrong')
            ];
            
            return $output;
        }
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
       

            

        return $this->index();



        return view('shipping::shipping.edit');
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

    public function sendWhatsappInvoice(){
        $business_id        = request()->session()->get('user.business_id');
        $fetch_data         = request()->fetch_data;
        $whatsapp_number    = request()->whatsapp_number;
        $shipment_id        = request()->shipment_id;    
        if($fetch_data == 0) {
            ShippingInvoiceSend::create([
                'business_id'   => $business_id,
                'shipment_id'   => $shipment_id,
                'created_by'    => auth()->user()->id,
                'whatsapp_number' => $whatsapp_number,
                'email_id'      => ''
            ]);
        }  
        $data = Shipment::leftjoin('users', 'shipments.created_by', 'users.id')
                ->leftjoin('contacts', 'shipments.customer_id', 'contacts.id')
                ->leftjoin('business', 'shipments.business_id', 'business.id')
                ->leftjoin('shipping_agents', 'shipments.agent_id', 'shipping_agents.id')
                ->leftjoin('shipping_recipients', 'shipments.recipient_id', 'shipping_recipients.id')
                ->leftjoin('shipping_mode', 'shipments.shipping_mode', 'shipping_mode.id')
                ->leftjoin('shipment_packages', 'shipment_packages.shipment_id', 'shipments.id')
                ->leftjoin('shipping_packages', 'shipments.package_type_id', 'shipping_packages.id')
                ->leftjoin('shipping_delivery', 'shipments.schedule_id', 'shipping_delivery.id')
                ->leftjoin('shipping_partners', 'shipments.shipping_partner', 'shipping_partners.id')
                ->leftjoin('shipping_status', 'shipments.delivery_status', 'shipping_status.id')
                ->join('business_locations', 'shipments.business_id', 'business_locations.business_id')
                ->where('shipments.id', $shipment_id)
                ->select([
                    'shipments.*',
                    'users.username as created_by',
                    'contacts.name as sender',
                    'business_locations.name as bl_name',
                    'business_locations.mobile as bl_mobile',
                    'business.name as business_name',
                    'business.company_number as business_number',
                    'contacts.address as address',
                    'contacts.mobile as mobile',
                    'contacts.city as c_city',
                    'contacts.country as c_country',
                    'contacts.state as c_state',
                    'contacts.landmark as c_landmark',
                    'shipping_recipients.address as rec_address',
                    'shipping_recipients.mobile_1 as rec_mobile_1',
                    'shipping_recipients.mobile_2 as rec_mobile_2',
                    'shipping_recipients.land_no as rec_land_no',
                    'shipping_recipients.postal_code as rec_postal_code',
                    'shipping_recipients.landmarks as rec_landmarks',
                    'shipping_agents.name as agent',
                    'shipping_recipients.name as recipient',
                    'shipping_mode.shipping_mode as mode',
                    'shipping_packages.package_name as package',
                    'shipment_packages.fixed_price as fixed_price_value',
                    'shipment_packages.package_description as package_description',
                    'shipment_packages.length as length',
                    'shipment_packages.width as width',
                    'shipment_packages.height as height',
                    'shipment_packages.weight as weight',
                    'shipment_packages.rate_per_kg as rate_per_kg',
                    'shipment_packages.volumetric_weight as volumetric_weight',
                    'shipment_packages.price_type as price_type',
                    'shipment_packages.shipping_charge as shipping_charge',
                    'shipment_packages.declared_value as declared_value',
                    'shipment_packages.service_fee as service_fee',
                    'shipping_delivery.shipping_delivery as delivery',
                    'shipping_partners.name as partner',
                    'shipping_status.shipping_status as status'
                ])->get();   
        if (!empty($data)) {
            $logo = '<img src="https://vimi14.online/public/img/awb.jpg" alt="Logo">';
            $title   = $data[0]->tracking_no;
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A5',
                'orientation' => 'L',
                'autoPageBreak' => false,
                'allow_unsafe_image_resizing' => true,

            ]);  
            $invoiceData = [
                'invoice_number' => $title,
                // Add more invoice details as needed
            ];
            $html=view('sale_pos.receipts.shipping_print_receipt')
            ->with(compact('data', 'title','logo'))->render();
            $mpdf->WriteHTML($html);
            $directoryPath = config('constants.invoice_directory');
            if (!is_dir($directoryPath)) {
                mkdir($directoryPath, 0755, true);
            }
        
            $filename = "invoice_".$title.".pdf";
            $filePath = config('constants.invoice_directory').$filename;
            $mpdf->Output($filePath, 'F');
        
        }

        $shippingInvoiceSend = ShippingInvoiceSend::where('shipment_id',$shipment_id) ->join('users', 'shipping_invoice_send.created_by', '=', 'users.id')->select('shipping_invoice_send.*', 'users.username', DB::raw('DATE_FORMAT(shipping_invoice_send.created_at, "%Y-%m-%d %H:%i:%s") as formatted_created_at'))
        ->get();
        return response()->json(['shippingInvoiceSend' => $shippingInvoiceSend,'path' => url("invoices/".$filename)]);
    }

    public function sendEmailIdsInvoice(){
        $business_id        = request()->session()->get('user.business_id');
        $fetch_data         = request()->fetch_data;
        $emailids           = request()->emailids;
        $shipment_id        = request()->shipment_id;    
        if($fetch_data == 0) {
            ShippingInvoiceSend::create([
                'business_id'   => $business_id,
                'shipment_id'   => $shipment_id,
                'created_by'    => auth()->user()->id,
                'whatsapp_number' => '',
                'email_id'      => $emailids
            ]);
        }  
        $data = Shipment::leftjoin('users', 'shipments.created_by', 'users.id')
                ->leftjoin('contacts', 'shipments.customer_id', 'contacts.id')
                ->leftjoin('business', 'shipments.business_id', 'business.id')
                ->leftjoin('shipping_agents', 'shipments.agent_id', 'shipping_agents.id')
                ->leftjoin('shipping_recipients', 'shipments.recipient_id', 'shipping_recipients.id')
                ->leftjoin('shipping_mode', 'shipments.shipping_mode', 'shipping_mode.id')
                ->leftjoin('shipment_packages', 'shipment_packages.shipment_id', 'shipments.id')
                ->leftjoin('shipping_packages', 'shipments.package_type_id', 'shipping_packages.id')
                ->leftjoin('shipping_delivery', 'shipments.schedule_id', 'shipping_delivery.id')
                ->leftjoin('shipping_partners', 'shipments.shipping_partner', 'shipping_partners.id')
                ->leftjoin('shipping_status', 'shipments.delivery_status', 'shipping_status.id')
                ->join('business_locations', 'shipments.business_id', 'business_locations.business_id')
                ->where('shipments.id', $shipment_id)
                ->select([
                    'shipments.*',
                    'users.username as created_by',
                    'contacts.name as sender',
                    'business_locations.name as bl_name',
                    'business_locations.mobile as bl_mobile',
                    'business.name as business_name',
                    'business.company_number as business_number',
                    'contacts.address as address',
                    'contacts.mobile as mobile',
                    'contacts.city as c_city',
                    'contacts.country as c_country',
                    'contacts.state as c_state',
                    'contacts.landmark as c_landmark',
                    'shipping_recipients.address as rec_address',
                    'shipping_recipients.mobile_1 as rec_mobile_1',
                    'shipping_recipients.mobile_2 as rec_mobile_2',
                    'shipping_recipients.land_no as rec_land_no',
                    'shipping_recipients.postal_code as rec_postal_code',
                    'shipping_recipients.landmarks as rec_landmarks',
                    'shipping_agents.name as agent',
                    'shipping_recipients.name as recipient',
                    'shipping_mode.shipping_mode as mode',
                    'shipping_packages.package_name as package',
                    'shipment_packages.fixed_price as fixed_price_value',
                    'shipment_packages.package_description as package_description',
                    'shipment_packages.length as length',
                    'shipment_packages.width as width',
                    'shipment_packages.height as height',
                    'shipment_packages.weight as weight',
                    'shipment_packages.rate_per_kg as rate_per_kg',
                    'shipment_packages.volumetric_weight as volumetric_weight',
                    'shipment_packages.price_type as price_type',
                    'shipment_packages.shipping_charge as shipping_charge',
                    'shipment_packages.declared_value as declared_value',
                    'shipment_packages.service_fee as service_fee',
                    'shipping_delivery.shipping_delivery as delivery',
                    'shipping_partners.name as partner',
                    'shipping_status.shipping_status as status'
                ])->get();   
        if (!empty($data)) {
            $logo = '<img src="https://vimi14.online/public/img/awb.jpg" alt="Logo">';
            $title   = $data[0]->tracking_no;
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A5',
                'orientation' => 'L',
                'autoPageBreak' => false,
                'allow_unsafe_image_resizing' => true,

            ]);  
            $invoiceData = [
                'invoice_number' => $title,
                // Add more invoice details as needed
            ];
            $html=view('sale_pos.receipts.shipping_print_receipt')
            ->with(compact('data', 'title','logo'))->render();
            
            $mpdf->WriteHTML($html);
            $pdfContent = $mpdf->Output('', 'S');

            // Send email with the PDF invoice attachment
            Mail::send('emails.send_invoice', ['data' => $invoiceData], function ($message) use ($pdfContent) {
                $message->to('punjabidushyant@gmail.com')
                    ->subject('Invoice')
                    ->attachData($pdfContent, 'invoice.pdf', [
                        'mime' => 'application/pdf',
                    ]);
            });
        } 
        
        $shippingInvoiceSend = ShippingInvoiceSend::where('shipment_id',$shipment_id)->join('users', 'shipping_invoice_send.created_by', '=', 'users.id')->select('shipping_invoice_send.*', 'users.username', DB::raw('DATE_FORMAT(shipping_invoice_send.created_at, "%Y-%m-%d %H:%i:%s") as formatted_created_at'))
        ->get();
        return response()->json(['shippingInvoiceSend' => $shippingInvoiceSend]);
    }

    public function saveShippingDetails(Request $request)
    {
        $shipping_id        =  $request->input('shipping_id');
        $delivery_time      =  $request->input('delivery_time');
        $shipping_delivery  =  $request->input('shipping_delivery');    
        $delivery_status    =  $request->input('new_status');
        $type_of_changes    =  $request->input('type_of_changes'); 
        $typeChange         = implode("|",$type_of_changes);
        $prev_status = Shipment::find($shipping_id)->delivery_status;
        if($shipping_delivery != '')
            Shipment::find($shipping_id)->update(['schedule_id' => $shipping_delivery,'updated_by'=>auth()->user()->id]);
        if($delivery_time != '')
            Shipment::find($shipping_id)->update(['delivery_time' => $delivery_time,'updated_by'=>auth()->user()->id]);
        if($delivery_status != '')
            Shipment::find($shipping_id)->update(['delivery_status' => $delivery_status,'updated_by'=>auth()->user()->id]);

       
        ShippingChangeStatus::create([
            'type_change'   => $typeChange,
            'shipping_id'   => $shipping_id,
            'shipping_delivery' => $shipping_delivery,
            'new_status'    => $delivery_status,
            'delivery_time' => $delivery_time,
            'prev_status'   => $prev_status,
            'created_by'    => auth()->user()->id,
        ]);

        return response()->json(['status' => "SUCCESS"]);
    }

    public function getShippingChangeDetails(Request $request)
    {
        $shipping_id        =  $request->input('shipment_id');
       

        $shipping_change_status = ShippingChangeStatus::where(['shipping_id' => $shipping_id])
                                    ->join('users', 'shipping_change_status.created_by', '=', 'users.id')
                                    ->leftjoin('shipping_status', 'shipping_change_status.new_status', 'shipping_status.id')
                                    ->leftJoin('shipping_status as prev', 'shipping_change_status.prev_status', 'prev.id')
                                    ->select('shipping_change_status.*', DB::raw('DATE_FORMAT(shipping_change_status.delivery_time, "%Y-%m-%d") as formatted_delivery_time'), 'shipping_status.shipping_status','prev.shipping_status as prev_shipping_status', 'users.username', DB::raw('DATE_FORMAT(shipping_change_status.created_at, "%Y-%m-%d %H:%i:%s") as formatted_created_at'))
                                    ->get();

        return response()->json(['shipping_change_status' => $shipping_change_status]);
    }
    
}
