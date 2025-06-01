<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;
use App\Utils\Util;
use App\Utils\TransactionUtil;
use App\Contact;
use App\ContactLedger;
use App\Account;
use App\AccountType;
use Illuminate\Support\Facades\DB;
use App\AccountTransaction;
use App\User;
use App\BusinessLocation;
use Yajra\DataTables\Facades\DataTables;

class LedgerDiscountController extends Controller
{
    protected $commonUtil;
    protected $transactionUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(
        Util $commonUtil, TransactionUtil $transactionUtil
    ) {
        $this->commonUtil = $commonUtil;
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $query = Transaction::leftjoin('users','users.id','transactions.created_by')
                                ->leftjoin('contacts','contacts.id','transactions.contact_id')
                                ->leftjoin('business_locations','business_locations.id','transactions.location_id')
                                ->where('transactions.business_id',$business_id)->where('transactions.type','ledger_discount')
                                ->select(
                                    'transactions.*',
                                    'business_locations.name as location',
                                    'contacts.name as contact_name',
                                    'users.username'
                                );
            if(!empty(request()->start_date) && !empty(request()->end_date)){
                $query->whereDate('transactions.transaction_date','>=',request()->start_date)->whereDate('transactions.transaction_date','<=',request()->end_date); 
            }
            
            if(!empty(request()->form_no)){
                $query->where('transactions.invoice_no',request()->form_no);
            }
            
            if(!empty(request()->location)){
                $query->where('transactions.location_id',request()->location);
            }
            
            if(!empty(request()->customer)){
                $query->where('transactions.contact_id',request()->customer);
            }
            
            if(!empty(request()->against_invoice)){
               if(request()->against_invoice == 'yes'){
                    $query->whereNotNull('transactions.transaction_note');
                }else{
                    $query->whereNull('transactions.transaction_note');
                }
            }
            
            if(!empty(request()->user_id)){
                $query->where('transactions.created_by',request()->user_id);
            }
                                
            $fuel_tanks = Datatables::of($query)
                    ->addColumn(
                        'action',
                        function ($row) {
                            $html = '<div class="btn-group">
                                <button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">'.__('messages.actions').'<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">';
    
                            $html .= '<li><a href="#" data-href="'.action([LedgerDiscountController::class, 'edit'], [$row->id]).'" class="btn-modal" data-container=".account_model"><i class="glyphicon glyphicon-edit"></i> '.__('messages.edit').'</a></li>';
                            $html .= '<li><a href="#" data-href="'.action([LedgerDiscountController::class, 'show'], [$row->id]).'" class="btn-modal" data-container=".account_model"><i class="fa fa-eye"></i> '.__('messages.view').'</a></li>';
                            
                            $html .= '</ul></div>';
    
                            return $html;
                        }
                    )
                    
                    ->editColumn('transaction_date','{{@format_date($transaction_date)}}')
                    ->addColumn('against_invoice',function($row){
                        if(!empty($row->transaction_note)){
                            return __('ledger_discount.yes');
                        }else{
                            return __('ledger_discount.no');
                        }
                    })
                    
                    ->editColumn('final_total','{{@num_format($final_total)}}')
                    
                    ->addColumn('invoice_nos',function($row){
                        if(!empty($row->transaction_note)){
                            $html = "";
                            foreach(json_decode($row->transaction_note) as $one){
                                $html .= "$one,";
                            }
                            
                            return $html;
                        }
                    })

                    ->removeColumn('id');



                return $fuel_tanks->rawColumns(['action'])

                    ->make(true);
                    
        }
        
        $users = User::where('business_id', $business_id)
                ->where('is_cmmsn_agnt', 0)
                ->where('is_customer', 0)->pluck('username','id');
        $business_locations = BusinessLocation::forDropdown($business_id,false);
        $customers = Contact::customersDropdown($business_id, false);
        
        $form_nos = Transaction::where('business_id',$business_id)->where('type','ledger_discount')->distinct('invoice_no')->pluck('invoice_no','invoice_no');
        $discounts = Transaction::where('business_id',$business_id)->where('type','ledger_discount')->select('transaction_note')->get();
        $invoices = [];
        foreach($discounts as $one){
            if(!empty($one->transaction_note)){
                foreach(json_decode($one->transaction_note) as $inv){
                    $invoices[] = $inv;
                }
            }
        }
        
        
        return view('ledger_discount.index',compact('users','business_locations','customers','form_nos','invoices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            $business_id = $request->session()->get('user.business_id');
            
            $contact = Contact::find($request->contact_id);

            $transaction_data = [
                'invoice_no' => request()->discount_no,
                'location_id' => $request->location_id,
                'business_id' => $business_id,
                'final_total' => $this->commonUtil->num_uf($request->discount_amount),
                'total_before_tax' => $this->commonUtil->num_uf($request->discount_amount),
                'status' => 'final',
                'type' => 'ledger_discount',
                'sub_type' => 'sell_discount',
                'contact_id' => $request->contact_id,
                'created_by' => auth()->user()->id,
                'additional_notes' => $request->discount_note,
                'transaction_date' => $this->commonUtil->uf_date($request->date, true)
            ];
            
            if($request->against_invoices == 'yes'){
                $transaction_data['transaction_note'] = json_encode(request()->selected_invoice_nos);
            }
            
            DB::beginTransaction();
            
            $transaction = Transaction::create($transaction_data);
            $this->createContactLedger($transaction, 'credit');
            $receivealbe_account_id = $this->transactionUtil->account_exist_return_id('Accounts Receivable');
            
            $account_transaction_data = [
                'amount' =>  abs($transaction->final_total), // @eng 11/2
                'account_id' => $receivealbe_account_id,
                'type' => "credit",
                'sub_type' => null,
                'operation_date' => $transaction->transaction_date,
                'created_by' => $transaction->created_by,
                'transaction_id' =>  $transaction->id,
                'note' => null
            ];
            AccountTransaction::createAccountTransaction($account_transaction_data);
            
            $discount_account_id = $this->transactionUtil->account_exist_return_id('Sales Discount');
            if(!empty($discount_account_id)){
                $account_transaction_data['account_id'] = $discount_account_id;
                $account_transaction_data['type'] = 'debit';   
                AccountTransaction::createAccountTransaction($account_transaction_data);
            }
            
            
            
            if($request->against_invoices == 'yes'){
                
                foreach(request()->selected_invoice as $key => $invoice){
                    $trans = Transaction::findOrFail($invoice);
                    $trans->discount_type = 'fixed';
                    $trans->discount_amount += $request->tbl_discount_amount[$key];
                    $trans->save();
                    $this->transactionUtil->updatePaymentStatus($trans->id, ($trans->final_total-$trans->discount_amount));
                }
            }
            
            DB::commit();
            
            
            $output = ['success' => true, 'msg' => __('lang_v1.success')];

        } catch (\Exception $e) {
             DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __('messages.something_went_wrong')
                        ];
        }

        return $output;  
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $business_id = request()->session()->get('user.business_id');
        
        $discount = Transaction::leftjoin('users','users.id','transactions.created_by')
                                ->leftjoin('contacts','contacts.id','transactions.contact_id')
                                ->leftjoin('business_locations','business_locations.id','transactions.location_id')
                                ->where('transactions.business_id',$business_id)->where('transactions.type','ledger_discount')
                                ->where('transactions.id',$id)
                                ->select(
                                    'transactions.*',
                                    'business_locations.name as location',
                                    'contacts.name as contact_name',
                                    'users.username'
                                )->first();
        
        return view('ledger_discount.show')->with(compact('discount'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->commonUtil->is_admin(auth()->user(),$business_id);

        if (!$is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        
        $discount = Transaction::where('business_id', $business_id)
                    ->where('type', 'ledger_discount')
                    ->find($id);

        $contact = Contact::find($discount->contact_id);
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        
        return view('ledger_discount.edit')->with(compact('discount', 'contact','business_locations'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      
        try {
            $business_id = $request->session()->get('user.business_id');
            
            $input= request()->all();

            $transaction_data = [
                'final_total' => $this->commonUtil->num_uf($request->discount_amount),
                'total_before_tax' => $this->commonUtil->num_uf($request->discount_amount),
                'additional_notes' => $request->discount_note,
                'transaction_date' => $this->commonUtil->uf_date($input['date']),
                'invoice_no' => request()->discount_no,
            ];
            
            
            $contact_ledger_data = [
                'amount' => $this->commonUtil->num_uf($request->discount_amount),
                'operation_date' => $this->commonUtil->uf_date($input['date'])
            ];
            
            Transaction::where('business_id', $business_id)
                    ->where('type', 'ledger_discount')
                    ->where('id', $id)
                    ->update($transaction_data);

            ContactLedger::where('transaction_id', $id)
                    ->update($contact_ledger_data);  
                    
            $account_transaction_data = [
                'amount' =>  $this->commonUtil->num_uf($request->discount_amount),
                'operation_date' => $this->commonUtil->uf_date($input['date']),
            ];
            
            AccountTransaction::where('transaction_id',$id)->update($account_transaction_data);
            
            
            
            $output = ['success' => true, 'msg' => __('lang_v1.success')];

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __('messages.something_went_wrong')
                        ];
        }

        return $output;  
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
        $is_admin = $this->commonUtil->is_admin(auth()->user(),$business_id);

        if (!$is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        
        try {
            #delete from transaction
            Transaction::where('business_id', $business_id)
                    ->where('type', 'ledger_discount')
                    ->where('id', $id)
                    ->delete();
                    #delete from contact ledger
            ContactLedger::where('transaction_id', $id)
                    ->delete();        

            
            $output = ['success' => true, 'msg' => __('lang_v1.success')];

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
                        ];
        }

        return $output;
    }
    public function createContactLedger($transaction, $type)
    {
        $account_transaction_data = [
            'contact_id' => !empty($transaction) ? $transaction->contact_id : null,
            'amount' => $transaction->final_total,
            'type' => $type,
            'operation_date' =>  $transaction->transaction_date,
            'created_by' => $transaction->created_by,
            'transaction_id' => $transaction->id,
            'transaction_payment_id' =>  null
        ];
        ContactLedger::createContactLedger($account_transaction_data);
    }

}
