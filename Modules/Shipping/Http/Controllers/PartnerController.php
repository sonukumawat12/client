<?php

namespace Modules\Shipping\Http\Controllers;

use App\Transaction;
use App\Contact;
use App\Account;
use App\AccountTransaction;
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
use Modules\Shipping\Entities\ShippingPartner;
use Modules\Shipping\Entities\RouteOperation;
use Yajra\DataTables\Facades\DataTables;
use Modules\Shipping\Entities\ShippingAccount;
use Modules\Shipping\Entities\ShippingPartnerCommission;

use App\BusinessLocation;
use App\Category;
use Illuminate\Support\Facades\DB;
class PartnerController extends Controller
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

            $drivers = ShippingPartner::leftjoin('users', 'shipping_partners.created_by', 'users.id')
                ->where('shipping_partners.business_id', $business_id)
                ->select([
                    'shipping_partners.*',
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
                        if (auth()->user()->can('fleet.drivers.edit')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Shipping\Http\Controllers\PartnerController@edit', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        }
                        if (auth()->user()->can('fleet.drivers.edit')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Shipping\Http\Controllers\PartnerController@destroy', [$row->id]) . '" class="delete_button"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        }
                        $html .= '<li class="divider"></li>';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Shipping\Http\Controllers\PartnerController@addPayment', [$row->id]) . '" class="btn-modal" data-container=".view_modal"> + ' . __("shipping::lang.add_payment") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Shipping\Http\Controllers\PartnerController@viewCommissions', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-eye"></i> ' . __("shipping::lang.view_commission") . '</a></li>';
                        
                        $html .= '<li><a href="' . action('\Modules\Shipping\Http\Controllers\PartnerController@show', [$row->id]) . '?tab=ledger" class=""><i class="fa fa-anchor"></i> ' . __("lang_v1.ledger") . '</a></li>';

                        return $html;
                    }
                )
                ->addColumn('current_due',function($row){
                    $due = $this->transactionUtil->num_f($this->transactionUtil->__getPartnerBalance($row->id));
                    return $due;
                })
                ->editColumn('joined_date', '{{@format_date($joined_date)}}')
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }
        
        $routes = [];
        
        $name = ShippingPartner::where('business_id',$business_id)->select('name')->pluck('name','name');
        
        $mobile_1 = ShippingPartner::where('business_id',$business_id)->select('mobile_1')->pluck('mobile_1','mobile_1');
        $mobile_2 = ShippingPartner::where('business_id',$business_id)->select('mobile_2')->pluck('mobile_2','mobile_2');
        
        $mobile = [];
        foreach($mobile_1 as $key => $value){
            $mobile[$key] = $value;
        }
        
        foreach($mobile_2 as $key => $value){
            $mobile[$key] = $value;
        }
        
        
        return view('shipping::settings.partners.index')->with(
            compact(
                'name','mobile'
            )
        );
    }
    
    public function viewCommissionsTable($id){
        if (request()->ajax()) {
            $shipment = ShippingPartnerCommission::leftjoin('shipments','shipping_partner_commission.shipment_id','shipments.id')
                                ->leftjoin('contacts', 'shipments.customer_id', 'contacts.id')
                                ->leftjoin('shipping_partners','shipping_partners.id','shipping_partner_commission.partner_id')
                                ->leftjoin('users','users.id','shipping_partner_commission.created_by')
                                ->leftjoin('shipping_mode','shipping_mode.id','shipments.shipping_mode')
                                ->leftjoin('shipping_packages','shipping_packages.id','shipments.package_type_id')
                                ->leftjoin('shipping_agents','shipping_agents.id','shipments.agent_id')
                                ->where('shipping_partner_commission.partner_id', $id)
                                ->select([
                                    'shipping_partner_commission.*',
                                    'shipments.tracking_no',
                                    'shipping_mode.shipping_mode',
                                    'shipping_packages.package_name',
                                    'shipping_partners.name as partner_name',
                                    'contacts.name as customer_name',
                                    'users.username as createdBy',
                                    'shipping_agents.name as agent_name'
                                ])->orderBy('id','DESC')->get();
                                
            return DataTables::of($shipment)
                ->editColumn('joined_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn('amount', '{{@num_format($amount)}}')
                ->editColumn('payment_status',function($row){
                    $html =  (string) view('shipping::shipping.payment_status', ['payment_status' => $row->payment_status]);
                    return $html;
                })
                ->removeColumn('id')
                ->rawColumns(['action','payment_status'])
                ->make(true);
        }
    }
    
    public function viewCommissions($id){
        $business_id = request()->session()->get('business.id');
        
        return view('shipping::settings.partners.viewCommission')->with(compact(
            'business_id','id'
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

        return view('shipping::settings.partners.create')->with(compact(
            'employee_no','departments'
        ));
    }
    
     public function viewLedger($id){
        $start_date = request()->start_date;
        $end_date =  request()->end_date;
  
        $ledger_transactions = $this->transactionUtil->__getPartnerLedger($id,$start_date,$end_date);
        $ledger_details = [
                'start_date' => $start_date,
                'end_date' => $end_date,
                'beginning_balance' => $this->transactionUtil->__getPartnerBfBalance($id,$start_date)
            ];
        
        return view('shipping::settings.partners.partials.ledger_tab')->with(
            compact(
                'ledger_transactions','ledger_details'
            )
        );

    }
    
    
    public function addPayment($id)
    {
        $business_id = request()->session()->get('business.id');
        
        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

        $business_location_id = BusinessLocation::where('business_id', $business_id)->first()->id;
        
        $payment_types = $this->transactionUtil->payment_types($business_location_id);
        
        unset($payment_types['credit_sale']);  // removing credit sale method from array

        //Accounts

        $accounts = $this->moduleUtil->accountsDropdown($business_id, true)->toArray();
        
        $prefix_type = "sell_payment";
        
        $ref_count = $this->transactionUtil->onlyGetReferenceCount($prefix_type, $business_id, false);

        //Generate reference number

        $payment_ref_no = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);
            
        $agent = ShippingPartner::find($id);
        
        $pending_invoices = ShippingPartnerCommission::leftjoin('shipments','shipments.id','shipping_partner_commission.shipment_id')
                            ->where('shipping_partner_commission.partner_id',$id)
                            ->whereIn('shipping_partner_commission.payment_status',array('due','partial'))
                            ->select('shipments.tracking_no','shipping_partner_commission.amount','shipping_partner_commission.id')->get();
        
        return view('shipping::settings.partners.addPayment')->with(compact(
            'agent','business_locations','business_location_id','payment_ref_no','payment_types','accounts','pending_invoices'
        ));
    }
    
    public function postPayment(Request $request,$id){
        

        if (!auth()->user()->can('purchase.create') && !auth()->user()->can('sell.create')) {

            abort(403, 'Unauthorized action.');

        }

        $business_id = $request->session()->get('business.id');

        try {
            
            
            $inputs = $request->only([

                'amount', 'method', 'note', 'card_number', 'card_holder_name',

                'card_transaction_number', 'card_type', 'card_month', 'card_year', 'card_security',

                'cheque_number', 'bank_account_number','bank_name'

            ]);
            
            
            if($inputs['method'] == 'cheque'){
                    if(empty($inputs['cheque_number']) || empty($inputs['bank_name'])){
                        $output = [
                                        'success' => false,
                                        'msg' => 'Bank name and Cheque number are required for Cheque payments'
                                    ];
                        return redirect()->back()->with('status', $output);
                    }else{
                        // check duplicates
                        $chequesAdded = $this->transactionUtil->checkCheques($inputs['cheque_number'], $inputs['bank_name']);
                        
                        if($chequesAdded > 0){
                            $output = [
                                        'success' => false,
                                        'msg' => 'Cheque with the same number and bank name already exists!'
                                    ];
                            return redirect()->back()->with('status', $output);
                        }
                    }
                }
            

            $inputs['paid_on'] = $this->transactionUtil->uf_date($request->input('paid_on'), true);

            $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);

            $inputs['created_by'] = auth()->user()->id;

            $inputs['business_id'] = $request->session()->get('business.id');

            $inputs['cheque_date'] = !empty($request->cheque_date) ? \Carbon::parse($request->cheque_date)->format('Y-m-d') : null;


            $inputs['payment_ref_no'] = $request->payment_ref_no;
            
            $transaction_data = [];

            $transaction_data['status'] = 'final';
            $transaction_data['total_before_tax'] = $inputs['amount'];
            $transaction_data['final_total'] = $inputs['amount'];
            $transaction_data['ref_no'] = $request->payment_ref_no;
            $transaction_data['business_id'] = $business_id;
            $transaction_data['created_by'] = auth()->user()->id;
            $transaction_data['type'] = 'partner_payment';
            $transaction_data['payment_status'] = 'paid';
            
            $transaction_data['parent_transaction_id'] = $id;
            
            $transaction_data['transaction_date'] = $inputs['paid_on'];
            $transaction = Transaction::create($transaction_data);
            
            

            if (!empty($request->input('account_id'))) {

                $inputs['account_id'] = $request->input('account_id');

            }

            
            $inputs['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');


            DB::beginTransaction();

            $inputs['transaction_id'] = $transaction->id;
            
            $parent_payment = TransactionPayment::create($inputs);

            
            $account_transaction_data = [

                'amount' => $parent_payment->amount,

                'account_id' => $parent_payment->account_id,

                'type' => 'credit',

                'operation_date' => $parent_payment->paid_on,

                'created_by' => Auth::user()->id,

                'transaction_id' => $transaction->id,

                'transaction_payment_id' => $parent_payment->id,

                'note' => null

            ];

            $location_id = BusinessLocation::where('business_id', $business_id)->first();

            $account_transaction_data['account_id'] = $request->account_id;
            
            AccountTransaction::createAccountTransaction($account_transaction_data);
            
            foreach($request->pending_invoices as $one){
                ShippingPartnerCommission::where('id',$one)->update(['payment_status' => 'paid','payment_tid'  => $transaction->id]);
            }

            

            DB::commit();
            
            $output = [

                'success' => true,

                'msg' => __('lang_v1.success')

            ];

        } catch (\Exception $e) {

            DB::rollBack();

            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }

        return redirect()->back()->with(['status' => $output]);

    
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('business.id');
       // try {
            $data = $request->except('_token');
            $data['joined_date'] = $this->commonUtil->uf_date($data['joined_date']);
            $data['business_id'] = $business_id;
            $data['created_by'] = Auth::user()->id;

            //update emploeyee count
            $this->transactionUtil->setAndGetReferenceCount('employee_no', $business_id);
             
            //@doc:6948 Chequer Module
            $data['contact_id'] = $this->createOrUpdateContact($request);

            $shipping_agent = ShippingPartner::create($data);
            
            if($request->opening_balance != 0){
                $transaction_data = [];
                $transaction_data['parent_transaction_id'] = $shipping_agent->id;
                $transaction_data['status'] = 'final';
                $transaction_data['total_before_tax'] = $request->opening_balance;
                $transaction_data['final_total'] = $request->opening_balance;
                
                $transaction_data['business_id'] = $business_id;
                $transaction_data['created_by'] = auth()->user()->id;
                $transaction_data['type'] = 'shipping_partner_ob';
                $transaction_data['payment_status'] = 'due';
                
                $transaction_data['transaction_date'] = date('Y-m-d');
                $transaction = Transaction::create($transaction_data);
    
                
                $account_payable = Account::where('business_id', $business_id)->where('name', 'Accounts Payable')->where('is_closed', 0)->first();
                $credit_data = [
                        'amount' => abs($request->opening_balance),
                        'transaction_id' => $transaction->id,
                        'type' => $request->opening_balance > 0 ? 'credit' : 'debit',
                        'sub_type' => null,
                        'operation_date' => $transaction_data['transaction_date'],
                        'created_by' => session()->get('user.id')
                    ];
                if(!empty($account_payable)){
                    $credit_data['account_id'] = $account_payable->id;
                    $credit = AccountTransaction::createAccountTransaction($credit_data);
                }
                
                $shipping_account = ShippingAccount::where('business_id',$business_id)
                                    ->first();
                                        
                if(!empty($shipping_account)){
                    $credit_data['account_id'] = $shipping_account->expense;
                    $credit_data['type'] = $request->opening_balance > 0 ? 'debit' : 'credit';
                    $debit = AccountTransaction::createAccountTransaction($credit_data);
                }
            }
                


            $output = [
                'success' => true,
                'tab' => 'agents',
                'msg' => __('lang_v1.success')
            ];
        // } catch (\Exception $e) {
        //     Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
        //     $output = [
        //         'success' => false,
        //         'tab' => 'agents',
        //         'msg' => __('messages.something_went_wrong')
        //     ];
        // }

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
        $driver_dropdown = ShippingPartner::where('business_id', $business_id)->pluck('name', 'id');
        $view_type = request()->tab;
        $driver = ShippingPartner::find($id);
        $id = $id;

        return view('shipping::settings.partners.show')->with(compact(
            'driver_dropdown',
            'view_type',
            'driver',
            'id'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $driver = ShippingPartner::find($id);

        return view('shipping::settings.partners.edit')->with(compact(
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
        $business_id = request()->session()->get('business.id');
        try {
            $data = $request->except('_token', '_method');
            $data['joined_date'] = $this->commonUtil->uf_date($data['joined_date']);
            $shipping_agent = ShippingPartner::findOrFail($id);
            
            //@doc:6948 Chequer Module
            $data['contact_id'] = $this->createOrUpdateContact($request,$shipping_agent->contact_id);

            ShippingPartner::where('id', $id)->update($data);
            
            $transaction = Transaction::where('type','shipping_partner_ob')->where('parent_transaction_id',$shipping_agent->id)->first();
            
            if($request->opening_balance != 0){
                if(!empty($transaction)){
                    $transaction->final_total = $request->opening_balance;
                    $transaction->total_before_tax = $request->opening_balance;
                    $transaction->save();
                    
                    AccountTransaction::where('transaction_id',$transaction->id)->delete();
                }else{
                    $transaction_data = [];
                    $transaction_data['parent_transaction_id'] = $shipping_agent->id;
                    $transaction_data['status'] = 'final';
                    $transaction_data['total_before_tax'] = $request->opening_balance;
                    $transaction_data['final_total'] = $request->opening_balance;
                    
                    $transaction_data['business_id'] = $business_id;
                    $transaction_data['created_by'] = auth()->user()->id;
                    $transaction_data['type'] = 'shipping_partner_ob';
                    $transaction_data['payment_status'] = 'due';
                    
                    $transaction_data['transaction_date'] = date('Y-m-d');
                    $transaction = Transaction::create($transaction_data);
        
                }
                
                $account_payable = Account::where('business_id', $business_id)->where('name', 'Accounts Payable')->where('is_closed', 0)->first();
                $credit_data = [
                        'amount' => abs($request->opening_balance),
                        'transaction_id' => $transaction->id,
                        'type' => $request->opening_balance > 0 ? 'credit' : 'debit',
                        'sub_type' => null,
                        'operation_date' => $transaction->transaction_date,
                        'created_by' => session()->get('user.id')
                    ];
                if(!empty($account_payable)){
                    $credit_data['account_id'] = $account_payable->id;
                    $credit = AccountTransaction::createAccountTransaction($credit_data);
                }
                
                $shipping_account = ShippingAccount::where('business_id',$business_id)
                                    ->first();
                                        
                if(!empty($shipping_account)){
                    $credit_data['account_id'] = $shipping_account->expense;
                    $credit_data['type'] = $request->opening_balance > 0 ? 'debit' : 'credit';
                    $debit = AccountTransaction::createAccountTransaction($credit_data);
                }
            }
           

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
            ShippingPartner::where('id', $id)->delete();

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
                'mobile' => $request->mobile_1,
                'address' => $request->address,
            ]);
        }
        return $id ?? $contact->id;
    }
}
