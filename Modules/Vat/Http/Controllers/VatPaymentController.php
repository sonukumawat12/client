<?php



namespace Modules\Vat\Http\Controllers;



use App\Business;

use App\AccountType;

use App\Account;

use App\AccountGroup;

use App\BusinessLocation;

use Illuminate\Http\Request;

use Illuminate\Routing\Controller;

use App\Utils\ModuleUtil;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

use Yajra\DataTables\Facades\DataTables;

use App\Utils\ProductUtil;

use App\Utils\TransactionUtil;

;

use Illuminate\Support\Facades\Log;

use Modules\Vat\Entities\VatPayment;
use Modules\Vat\Entities\VatPayableToAccount;
use App\Contact;
use App\AccountTransaction;

class VatPaymentController extends Controller

{

    /**

     * All Utils instance.

     *

     */

    protected $productUtil;

    protected $transactionUtil;

    protected $moduleUtil;



    /**

     * Constructor

     *

     * @param ProductUtils $product

     * @return void

     */

    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, ModuleUtil $moduleUtil)

    {

        $this->productUtil = $productUtil;

        $this->transactionUtil = $transactionUtil;

        $this->moduleUtil = $moduleUtil;

    }




    public function index()

    {

        $business_id = request()->session()->get('user.business_id');


        if (request()->ajax()) {

                $query = VatPayment::leftjoin('users', 'vat_payments.created_by', 'users.id')
                    ->leftjoin('vat_payable_to_accounts as ppta','ppta.id','vat_payments.payable_account_id')
                    ->leftJoin('accounts as pmt','pmt.id','vat_payments.payment_account_id')
                    ->leftJoin('accounts as pay','pay.id','ppta.account_id')
                    ->leftJoin('contacts','contacts.id','vat_payments.contact_id')
                    ->where('vat_payments.business_id', $business_id)

                    ->select([

                        'vat_payments.*',
                        'pmt.name as payment_name',
                        'pay.name as payable_name',
                        'contacts.name as contact_name',
                        'users.username as user_created'

                    ]);
                    
                    if(!empty(request()->start_date) && !empty(request()->end_date)){
                        $query->whereDate('date','>=',request()->start_date);
                        $query->whereDate('date','<=',request()->end_date);
                    }
                    
                    if(!empty(request()->contact_id)){
                        $query->where('vat_payments.contact_id',request()->contact_id);
                    }
                    
                    if(!empty(request()->account_id)){
                        $query->where('vat_payments.payment_account_id',request()->account_id);
                    }
                    
                    if(!empty(request()->payable_id)){
                        $query->where('vat_payments.payable_account_id',request()->payable_id);
                    }

                

                $fuel_tanks = Datatables::of($query)
                    ->addColumn(
                        'action',
                        function ($row) {
                            $html = '<div class="btn-group">
                                <button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">'.__('messages.actions').'<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">';
    
                            $html .= '<li><a href="#" data-href="'.action([\Modules\Vat\Http\Controllers\VatPaymentController::class, 'edit'], [$row->id]).'" class="btn-modal" data-container=".fuel_tank_modal"><i class="glyphicon glyphicon-edit"></i> '.__('messages.edit').'</a></li>';
                            $html .= '<li><a href="#" data-href="'.action([\Modules\Vat\Http\Controllers\VatPaymentController::class, 'destroy'], [$row->id]).'" class="delete_task" ><i class="fa fa-trash"></i> '.__('messages.delete').'</a></li>';
    
                            $html .= '</ul></div>';
    
                            return $html;
                        }
                    )
                    ->editColumn('date','{{@format_date($date)}}')
                    ->editColumn('amount','{{@num_format($amount)}}')
                    ->editColumn('payment_method',function($row){
                        if($row->payment_method == 'bank_transfer'){
                            return __('vat::lang.online_transfer');
                        }else{
                            return __('vat::lang.'.strtolower($row->payment_method));
                        }
                    })
                    ->removeColumn('id');



                return $fuel_tanks->rawColumns(['action'])

                    ->make(true);

            }

        $payable_accounts = VatPayableToAccount::leftJoin('accounts','accounts.id','vat_payable_to_accounts.account_id')
                                ->where('vat_payable_to_accounts.business_id', $business_id)
                                ->select([
                                    'vat_payable_to_accounts.id',
                                    'accounts.name as account_name'
            
                                ])->pluck('account_name','id');
        
        $customers = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');
        $bank_account_group_id = AccountGroup::getGroupByName('Bank Account');
        $bank_accounts = Account::where('business_id', $business_id)->where('asset_type', $bank_account_group_id->id)->pluck('name','id');
        
        return view('vat::vat_payments.index')->with(compact('business_id','payable_accounts','customers','bank_accounts'));
    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {
        $business_id = request()->session()->get('business.id');
        $payable_accounts = VatPayableToAccount::leftJoin('accounts','accounts.id','vat_payable_to_accounts.account_id')
                                ->where('vat_payable_to_accounts.business_id', $business_id)
                                ->select([
                                    'vat_payable_to_accounts.id',
                                    'accounts.name as account_name'
            
                                ])->pluck('account_name','id');
        
        $customers = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');
        $bank_account_group_id = AccountGroup::getGroupByName('Bank Account');
        $bank_accounts = Account::where('business_id', $business_id)->where('asset_type', $bank_account_group_id->id)->pluck('name','id');
        $latest =   VatPayment::where('business_id',$business_id)->get()->last();
        if(!empty($latest)){
            $form_no = str_pad(((int) $latest->form_no + 1),4,0,STR_PAD_LEFT);
        }else{
            $form_no = '0001';
        }
        
        $payment_methods = array('cash' => __('vat::lang.cash'),'Bank' => __('vat::lang.bank'),'card' => __('vat::lang.card'),'bank_transfer' => __('vat::lang.online_transfer'));
        
        return view('vat::vat_payments.create')->with(compact('business_id','bank_accounts','customers','payable_accounts','form_no','payment_methods'));

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

            $business_id = request()->session()->get('business.id');

            
            DB::beginTransaction();
            
            $data = $request->except('_token');
            $data['created_by'] = auth()->user()->id;
            $data['business_id'] = $business_id;
            $data['date'] =\Carbon::parse($request->date)->format('Y-m-d');
            
            $payment = VatPayment::create($data);
            
            // add account transactions
            $credit_data = [
                'amount' => $payment->amount,
                'account_id' => $request->payment_account_id,
                'type' => 'credit',
                'sub_type' => 'vat_payment',
                'operation_date' => $payment->date,
                'created_by' => session()->get('user.id'),
                'transfer_transaction_id' => $payment->id,
                'note' => $payment->note
            ];
            $credit = AccountTransaction::createAccountTransaction($credit_data);
            
            $credit_data['type'] = 'debit';
            $credit_data['account_id'] = $request->payable_account_id;
            
            $credit = AccountTransaction::createAccountTransaction($credit_data);
            
            DB::commit();

            $output = [

                'success' => true,

                'msg' => __('messages.success')

            ];

        } catch (\Exception $e) {
            DB::rollback();
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return redirect()->back()->with('status', $output);

    }



    /**

     * Display the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {

        //

    }



    /**

     * Show the form for editing the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

     public function edit($id)
    {
        $data = VatPayment::findOrFail($id);
        $business_id = request()->session()->get('business.id');
        $payable_accounts = VatPayableToAccount::leftJoin('accounts','accounts.id','vat_payable_to_accounts.account_id')
                                ->where('vat_payable_to_accounts.business_id', $business_id)
                                ->select([
                                    'vat_payable_to_accounts.id',
                                    'accounts.name as account_name'
            
                                ])->pluck('account_name','id');
        
        $customers = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');
        $bank_account_group_id = AccountGroup::getGroupByName('Bank Account');
        $bank_accounts = Account::where('business_id', $business_id)->where('asset_type', $bank_account_group_id->id)->pluck('name','id');
        $latest =   VatPayment::where('business_id',$business_id)->get()->last();
        if(!empty($latest)){
            $form_no = str_pad(((int) $latest->form_no + 1),4,0,STR_PAD_LEFT);
        }else{
            $form_no = '0001';
        }
        
        $payment_methods = array('cash' => __('vat::lang.cash'),'Bank' => __('vat::lang.bank'),'card' => __('vat::lang.card'),'bank_transfer' => __('vat::lang.online_transfer'));
        
        return view('vat::vat_payments.edit')->with(compact('business_id','bank_accounts','customers','payable_accounts','form_no','payment_methods','data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $business_id = $request->session()->get('user.business_id');
        
        
        try {
            $data = $request->only(
                        'form_no',
                        'date',
                        'contact_id',
                        'amount',
                        'payable_account_id',
                        'payment_method',
                        'payment_account_id',
                        'cheque_date',
                        'cheque_number',
                        'to_account_no',
                        'recipient_name',
                        'note'
                    );
            $data['date'] =\Carbon::parse($request->date)->format('Y-m-d');
            $data['created_by'] = auth()->user()->id;
            $data['business_id'] = $business_id;
            
            VatPayment::where('id', $id)
                            ->update($data);
                            
            $payment = VatPayment::findOrFail($id);
            AccountTransaction::where('sub_type','vat_payment')->where('transfer_transaction_id',$id)->forceDelete();
                            
            // add account transactions
            $credit_data = [
                'amount' => $payment->amount,
                'account_id' => $request->payment_account_id,
                'type' => 'credit',
                'sub_type' => 'vat_payment',
                'operation_date' => $payment->date,
                'created_by' => session()->get('user.id'),
                'transfer_transaction_id' => $payment->id,
                'note' => $payment->note
            ];
            $credit = AccountTransaction::createAccountTransaction($credit_data);
            
            $credit_data['type'] = 'debit';
            $credit_data['account_id'] = $request->payable_account_id;
            
            $credit = AccountTransaction::createAccountTransaction($credit_data);
            

            $output = ['success' => true,
                'msg' => __('lang_v1.updated_success'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return redirect()->back()->with('status', $output);
    }


    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {
        $business_id = request()->session()->get('user.business_id');
        

        if (request()->ajax()) {
            try {
                $payment = VatPayment::findOrFail($id);
                AccountTransaction::where('sub_type','vat_payment')->where('transfer_transaction_id',$id)->forceDelete();
                
                VatPayment::where('id', $id)->delete();

                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

}

