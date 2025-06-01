<?php

namespace App\Http\Controllers\Chequer;
use App\Account;
use App\AccountType;
use App\Contact;
use App\Currency;
use App\Transaction;
use App\Utils\ModuleUtil;
use App\AccountTransaction;
use App\TransactionPayment;
use Illuminate\Http\Request;
use App\Chequer\CancelCheque;
use App\Chequer\ChequeNumber;
use App\Chequer\ChequerStamp;
use App\Utils\TransactionUtil;
use App\Chequer\ChequeTemplate;
use App\Chequer\ChequerCurrency;
use App\Chequer\ChequerSupplier;
use Illuminate\Support\Facades\DB;
use App\Chequer\ChequerBankAccount;
use Illuminate\Support\Facades\Log;
use App\Chequer\PrintedChequeDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Chequer\ChequeNumbersMEntry;
use App\Chequer\ChequeNumberMaintain;
use App\Chequer\ChequerPurchaseOrder;
use App\Chequer\ChequerDefaultSetting;
use App\Events\TransactionPaymentAdded;
use Modules\Superadmin\Entities\Package;
use Modules\Superadmin\Entities\Subscription;
use Carbon\Carbon;


class ChequeWriteController extends Controller
{

    protected $transactionUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param TransactionUtil $transactionUtil
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ModuleUtil $moduleUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function filter_monthly(Request $request)
    {
       if($request->data=='today'){
         $current_day = PrintedChequeDetail::whereDate('created_at', \Carbon::today())->get();
         $count_day=$current_day->count();
         $i=0;
         $total=0;
         if($count_day==0){
            return '0';
         }
         foreach($current_day as $i){
            $html[$i]=`
            <tr class="product"><td class="product">'.$i->cheque_amount.'</td>
            <td class="product">'.$i.'</td></tr>`;
            $total=$i->cheque_amount;
             $i++;
                         }
                   $data=array("html"=> $html,"total"=> $total, "count" => $count_day);    
                  return $data;
       }
       if($request->data=='currentmonth'){
         $current_month = PrintedChequeDetail::select('*')->whereMonth('created_at', \Carbon::now()->month)->get();
         $count_month=$current_month->count();  
         $i=0;
         $total=0;
         if($count_month==0){
            return '0';
         }
         foreach($current_day as $i){
            $html[$i]=`
            <tr class="product"><td class="product">'.$i->cheque_amount.'</td>
            <td class="product">'.$i.'</td></tr>`;
            $total=$i->cheque_amount;
          $i++;
                         }
            $data=array("html"=> $html,"total"=> $total, "count" => $count_month);    

             return $data;
         
       }
       if($request->data=='Previousmonth'){
        $previous_month = PrintedChequeDetail::whereMonth('created_at', '=', \Carbon::now()->subMonth()->month);
        $count_month_previous=$previous_month->count();
        $i=0;
        $total=0;
        if($count_month_previous==0){
            return '0';
         }
        foreach($current_day as $i){
            $html[$i]=`
            <tr class="product"><td class="product">'.$i->cheque_amount.'</td>
            <td class="product">'.$i.'</td></tr>`;
            $total=$i->cheque_amount;
         $i++;
                        }    
        $data=array("html"=> $html,"total"=> $total, "count" => $count_month_previous);    
        return $count_month_previous;
            // return [$html, $total,$count_month_previous];
       }
       if($request->data=='currentyear'){
       
        $current_year=PrintedChequeDetail::whereYear('created_at', date('Y'))->get();
        $count_year=$current_year->count();
        $i=0;
        $total=0;
        if($count_year==0){
            return '0';
         }
        foreach($current_day as $i){
            $html[$i]=`
            <tr class="product"><td class="product">'.$i->cheque_amount.'</td>
            <td class="product">'.$i.'</td></tr>`;
            $total=$i->cheque_amount;
         $i++;
                        }
        $data=array("html"=> $html,"total"=> $total, "count" => $count_year);    

            return $data;
       }

       if($request->data=='previousyear'){
       
        $current_year = PrintedChequeDetail::whereYear('created_at', now()->subYear()->year)->get();
        $count_previous_year=$current_year->count();
        $i=0;
        $total=0;
        if($count_previous_year==0){
            return '0';
         }
        foreach($current_day as $i){
            $html[$i]=`
            <tr class="product"><td class="product">'.$i->cheque_amount.'</td>
            <td class="product">'.$i.'</td></tr>`;
            $total=$i->cheque_amount;
         $i++;
                        }
        $data=array("html"=> $html,"total"=> $total, "count" => $count_previous_year);    
             return $data;
            // return [$html, $total, $count_previous_year];
       }

       if($request->data=='currentweek'){
       
        $current_week = PrintedChequeDetail::select("*")->whereBetween('created_at',[\Carbon::now()->startOfWeek(), \Carbon::now()->endOfWeek()])->get();
        $count_week=$current_week->count();
        $i=0;
        $total=0;
        if($count_week==0){
            return '0';
         }
        foreach($current_day as $i){
        $html[$i]=`
        <tr class="product"><td class="product">'.$i->cheque_amount.'</td>
        <td class="product">'.$i.'</td></tr>`;
         $total=$i->cheque_amount;
         $i++;
                        }
        $data=array("html"=> $html,"total"=> $total, "count" => $count_week);    
        return $data;
       }
       
    }

    public function chequerDashboard()
    {        
        // $date=date('Y-m-d');
        return view('chequer/chequer_dashboard/chequer_dashboard');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $business_id = request()->session()->get('business.id');
        $getvoucher = PrintedChequeDetail::where('business_id', $business_id)->orderBy('id', 'desc')->get();
        $templates = ChequeTemplate::getTemplates($business_id);
        $results = Contact::where('business_id', $business_id)->where('type', 'supplier')->get();
        $suppliers = Contact::where('type', 'supplier')->get();
        $stamps = ChequerStamp::where('business_id', $business_id)->where('stamp_status', 1)->get();
        $get_defultvalu = ChequerDefaultSetting::where('business_id', $business_id)->get();
        $get_currency =Currency::orderBy('country','ASC')->get()->toArray();// ChequerCurrency::get();
        $get_bankacount = Account::where('business_id', $business_id)->where('is_need_cheque','Y')->get();
        //$package_manage = Package::where('only_for_business', $business_id)->first();
         //$package_details = "";
        //$subscription = Subscription::where('business_id', $business_id)->where('end_date','>=', date('Y-m-d'))->first();
       
       
        $post_date_enabled = false;
        // $package_details = json_encode($subscription->package_details);
        // $package_details_array = json_decode($package_details, true);
        // $post_date_enabled = isset($package_details_array['post_dated_cheque']) && $package_details_array['post_dated_cheque'] == 1;
        
       // $package_manage = Package::where('id', $subscription ? $subscription->package_id : 0)->first();
        $default_setting = ChequerDefaultSetting::where('business_id', $business_id)->first();
        $bank_accounts = ChequerBankAccount::where('is_visible', 1)->with('account')->get();
      // dd($subscription->package_details);
        return view('chequer/write_cheque/create')->with(compact(
            'suppliers',
            'getvoucher',
            'templates',
            'results',
            'stamps',
            'get_defultvalu',
            'get_currency',
            'get_bankacount',          
            'default_setting',
            'post_date_enabled',
            'bank_accounts'
        ));
    }
    public function getBankAccountsByTemplate(Request $request)
    {
        $template_id = $request->input('template_id');
        $business_id = request()->session()->get('business.id');
    
        // Validate the template ID
        if (!$template_id) {
            return response()->json(['error' => 'Template ID is required.'], 400);
        }
    
        // Get the bank accounts linked to the selected template and marked for "Show in Cheque Writing Module"
        $bankAccounts = BankAccount::where('template_id', $template_id)
            ->where('business_id', $business_id)
            ->where('is_need_cheque', 'Y') // Filter accounts that should be shown in the cheque writing module
            ->pluck('name', 'id'); // Retrieve only the name and ID
    
        // Return the bank accounts as a JSON response
        return response()->json($bankAccounts);
    }


    public function getNextChequeNumber(Request $request)
    {
        $business_id = request()->session()->get('business.id');
    
        $bankData = Account::where('business_id', $business_id)->where('account_number', $request->account_id)->first();

        if(is_null($bankData)){
            $bankData = Account::where('business_id', $business_id)->where('id', $request->account_id)->first();
        }
    
        $row = ChequeNumber::where('business_id', $business_id)
            ->where('account_no', $bankData->id)
            ->where('status', 'inused')
            ->first();

        if($request->cheque_number_id){
            $row = ChequeNumber::where('id', $request->cheque_number_id)->first();
        }

        if (is_null($row)) {
            $row = ChequeNumber::where('business_id', $business_id)
                ->where('account_no', $bankData->id)
                ->where('status', 'active')
                ->first();
        }
    
        if (is_null($row)) {
            return response()->json(['error' => 'No cheque numbers found for this account.'], 404);
        }
        
        $latestChequeIssue = $row->latest_cheque_issue;
    
        $cancelledChequeNumbers = CancelCheque::where('account_id', $bankData->id)
            ->pluck('cheque_no')
            ->toArray();
    
        if ($latestChequeIssue !== null) {
            $nextChequeNumber = $latestChequeIssue + 1;

            while (in_array($nextChequeNumber, $cancelledChequeNumbers)) {
                $nextChequeNumber++;
            }
        } else {
            $nextChequeNumber = $row->first_cheque_no;
    
            while (in_array($nextChequeNumber, $cancelledChequeNumbers)) {
                $nextChequeNumber++;
            }
        }
    
        return response()->json(['next_cheque_number' => $nextChequeNumber]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    //   dd('testing 123');
        $temp_id = null;
        $data['business_id']=$request->session()->get('user.business_id');
        $business_id = request()->session()->get('business.id');
      
        if($request->print_status){
              $data['print_type'] = $request->print_status;   
        }
        $bank_account = Account::where('id',$request->bankacount)->first(); 
        // dd($bank_account,'1234');
        $bank_name= $bank_account->id;       
        $data['template_id'] = $request->template_id;
        $data['user_id'] = Auth::user()->id;
        $data['payee'] = $request->payee;
        $data['cheque_no'] = $request->cheque_no;
        //$data['paymentFor'] = $request->paymentFor;
        $data['cheque_date'] = $request->cheque_date;
        $data['refrence'] = $request->purchse_bill_no;
        $data['cheque_amount'] = $request->cheque_amount;
        $data['supplier_paid_amount'] = $request->paid_to_supplier;
        $data['status'] = $request->paid_to_supplier;
         $data['purchase_order_id'] = $request->purchase_order_id;
        $data['bank_account_no'] = $bank_account->name;
        $data['amount_word'] = $request->cheque_amount;
        $accountInfo = Account::where('id',$request->bankacount)->first();
        $payable_amount = $request->payable_amount;
        
        DB::beginTransaction();         
            //transaction 
            $transaction_data['business_id'] = $request->session()->get('user.business_id');
            $transaction_data['location_id'] =$request->session()->get('user.business_id');        
            $transaction_data['type'] = "cheque";
            $transaction_data['status'] = "final";
            $transaction_data['contact_id'] = $request->payee;//$this->transactionUtil->uf_date($request->cheque_date, false);

            $transaction_store=Transaction::create($transaction_data);
          
            PrintedChequeDetail::create($data);

//Transaction payment       
      
    
        
            $inputs = [
                'business_id' =>$business_id,
                'amount' => $request->cheque_amount,
                'method' => $request->paymentType.'s',
                'note' =>  $request->double_entry_acc,
                'card_number' => null,
                'card_holder_name' => null,
                'card_transaction_number' => null,
                'card_type' => null,
                'card_month' => null,
                'card_year' => null,
                'card_security' => null,
                'cheque_number' => $request->cheque_no,
               
                'bank_name' => $bank_account->name, 
                'bank_account_number'=>null
            ];
           
            $inputs['paid_on'] = $request->cheque_date;//$this->transactionUtil->uf_date($request->cheque_date, false);
            $inputs['transaction_id'] = $transaction_store->id;    
            $inputs['account_id'] = $bank_account->id;   
            $inputs['cheque_date'] = Carbon::parse($request->cheque_date)->toDateString();              
            $inputs['created_by'] = auth()->user()->id;
            $inputs['payment_for'] = $request->payee; // we have ignored current system contact/suppplier
            $inputs['payment_type'] = $request->paymentFor; // we have ignored current system contact/suppplier
          $transaction_payment=  TransactionPayment::create($inputs);
         

//Acount transaction bank
//  $ob_transaction_data_bank = [
//                             'amount' => $request->cheque_amount,
//                             'account_id' => $bank_account->id,
//                             'type' => 'credit',
//                             'sub_type' => 'ledger',
//                              'transaction_id' =>$transaction_store->id,
//                              'transaction_payment_id' =>$transaction_payment->id,
//                             'created_by' => Auth::user()->id,
//                             'cheque_number' => $request->cheque_no,

//                         ];
//                         AccountTransaction::createAccountTransaction($ob_transaction_data_bank);
//     //Acount transaction bank
//     //$cheque_account = Account::whereIn('name', ['Pre payments', 'Pre_payments'])->first();
//     $ob_transaction_data_prepayment = [
//                         'amount' => $request->cheque_amount,
//                         'account_id' =>57,
//                         'type' => 'debit',
//                         'sub_type' => 'ledger',
//                         'transaction_id' =>$transaction_store->id,
//                         'transaction_payment_id' =>$transaction_payment->id,
//                         'created_by' => Auth::user()->id,
//                         'cheque_number' => $request->cheque_no,

//                     ];
// AccountTransaction::createAccountTransaction($ob_transaction_data_prepayment);
if ( $request->paymentFor==='purchases')
{ 
    // dd($bank_account,'009',$bank_account->id);
   // Acount transaction bank
    $ob_transaction_data_bank = [
                                'amount' => $request->cheque_amount,
                                'account_id' => $bank_account->id,
                                'type' => 'credit',
                                'sub_type' => 'ledger_show',
                                'transaction_id' =>$transaction_store->id,
                                'transaction_payment_id' =>$transaction_payment->id,
                                'created_by' => Auth::user()->id,
                                'cheque_number' => $request->cheque_no,

                            ];
                            // dd($ob_transaction_data_bank);
                            AccountTransaction::createAccountTransaction($ob_transaction_data_bank);
        //Acount transaction bank
       $account_name = Account::where('business_id', $business_id)
                    ->whereIn(\DB::raw('LOWER(name)'), ['pre_payments', 'pre payments', 'prepayments'])
                    ->first();
        $ob_transaction_data_prepayment = [
                            'amount' => $request->cheque_amount,
                            'account_id' => $account_name->id,
                            'type' => 'debit',
                            'sub_type' => 'ledger_show',
                            'transaction_id' =>$transaction_store->id,
                            'transaction_payment_id' =>$transaction_payment->id,
                            'created_by' => Auth::user()->id,
                            'cheque_number' => $request->cheque_no,

                        ];
    AccountTransaction::createAccountTransaction($ob_transaction_data_prepayment);
                    }
    elseif ($request->paymentFor==='expenses')
    {
    //Acount transaction bank
    
    $ob_transaction_data_bank = [
                                'amount' => $request->cheque_amount,
                                'account_id' => $bank_account->id,
                                'type' => 'credit',
                                'sub_type' => 'ledger_show',
                                'transaction_id' =>$transaction_store->id,
                                'transaction_payment_id' =>$transaction_payment->id,
                                'created_by' => Auth::user()->id,
                                'cheque_number' => $request->cheque_no,

                            ];
                            AccountTransaction::createAccountTransaction($ob_transaction_data_bank);
        //Acount transaction bank
       $account_name = Account::where('business_id', $business_id)
                ->whereIn(\DB::raw('LOWER(name)'), ['pre_payments', 'pre payments', 'prepayments'])
                ->first();
       \Log::info("Schema file: $account_name");
        $ob_transaction_data_prepayment = [
                            'amount' => $request->cheque_amount,
                            'account_id' =>$account_name->id,
                            'type' => 'debit',
                            'sub_type' => 'ledger_show',
                            'transaction_id' =>$transaction_store->id,
                            'transaction_payment_id' =>$transaction_payment->id,
                            'created_by' => Auth::user()->id,
                            'cheque_number' => $request->cheque_no,

                        ];
    AccountTransaction::createAccountTransaction($ob_transaction_data_prepayment);

    //expense transaction
     //Acount transaction bank
     $cheque_account = Account::where('id', $request->double_entry_acc)->first();
    $ob_transaction_data_bank = [
                                'amount' => $request->cheque_amount,
                                'account_id' => $cheque_account->id,
                                'type' => 'debit',
                                'sub_type' => 'ledger_show',
                                'transaction_id' =>$transaction_store->id,
                                'transaction_payment_id' =>$transaction_payment->id,
                                'created_by' => Auth::user()->id,
                                'cheque_number' => $request->cheque_no,

                            ];
                            AccountTransaction::createAccountTransaction($ob_transaction_data_bank);
        //Acount transaction bank
       $account_name = Account::where('business_id', $business_id)
                ->whereIn(\DB::raw('LOWER(name)'), ['pre_payments', 'pre payments', 'prepayments'])
                ->first();
       \Log::info("Schema file: $account_name");
        $ob_transaction_data_prepayment = [
                            'amount' => $request->cheque_amount,
                            'account_id' =>$account_name->id,
                            'type' => 'credit',
                            'sub_type' => 'ledger_show',
                            'transaction_id' =>$transaction_store->id,
                            'transaction_payment_id' =>$transaction_payment->id,
                            'created_by' => Auth::user()->id,
                            'cheque_number' => $request->cheque_no,

                        ];
    AccountTransaction::createAccountTransaction($ob_transaction_data_prepayment);
                    }
                        DB::commit();
    //   if($request->cheque_no){
                    
    //                 $chequeBook = $this->saveChequeNo($request->cheque_no,$request->bankacount);
    //                 $nextCheque = ChequeNumber::find($chequeBook->id);
    //                 $nextCheque->no_of_cheque_leaves = $nextCheque->no_of_cheque_leaves - 1;
                    
    //                 $nextCheque->save();
    //             }
    if ($request->cheque_no) {
    // Find the cheque book entry based on the bank account
    $chequeBook = ChequeNumber::where('account_no', $request->bankacount)->first();

    // If no existing cheque book entry, create a new one
            if (!$chequeBook) {
                $chequeBook = new ChequeNumber();
                $chequeBook->account_no = $request->bankacount;
                $chequeBook->no_of_cheque_leaves = 100; // Default value, adjust if needed
                $chequeBook->save();
            }
        
            // Store the cheque number in the cheque book
            $chequeBook->account_no = $request->bankacount;
            $chequeBook->business_id=$business_id;
            $chequeBook->date_time=$request->cheque_date;
         $ChequeNumbersMEntry = ChequeNumbersMEntry::where('business_id', $business_id)
                        ->where('bank_id',  $chequeBook->account_number)
                        ->latest()
                        ->first();
 
            if (!$ChequeNumbersMEntry) {
                // Create a new instance when no record exists
                $ChequeNumbersMEntry = new ChequeNumbersMEntry();
                
                $ChequeNumbersMEntry->date_time = $request->cheque_date;
                $ChequeNumbersMEntry->bank_id =$bank_name;
                $ChequeNumbersMEntry->cheque_number_id = $chequeBook->id;
                $ChequeNumbersMEntry->next_cheque_number_to_print =$request->cheque_no + 1;
                $ChequeNumbersMEntry->new_cheque_number_to_print = $chequeBook->cheque_no;
                $ChequeNumbersMEntry->next_cheque_number_to_auto_print = $request->cheque_no + 1;
                $ChequeNumbersMEntry->note = "Cheque";
                $ChequeNumbersMEntry->business_id = $business_id;
            } else {
                // Update existing record
                 $ChequeNumbersMEntry = new ChequeNumbersMEntry();
                
                $ChequeNumbersMEntry->date_time = $request->cheque_date;
                $ChequeNumbersMEntry->bank_id = $bank_name;
                $ChequeNumbersMEntry->cheque_number_id = $chequeBook->id;
                $ChequeNumbersMEntry->next_cheque_number_to_print =$request->cheque_no + 1;
                $ChequeNumbersMEntry->new_cheque_number_to_print = $chequeBook->cheque_no;
                $ChequeNumbersMEntry->next_cheque_number_to_auto_print = $request->cheque_no + 1;
                $ChequeNumbersMEntry->note = "Cheque";
                $ChequeNumbersMEntry->business_id = $business_id;
            }
            
            // Save the record (whether new or updated)
            $ChequeNumbersMEntry->save();
           
          
        }

        if ($request->purchase_id) {
           
            $purchase_data = ChequerPurchaseOrder::where('id', $request->purchase_id)->get();
            foreach ($purchase_data as $data) {
                $us_id      = Auth::user()->id;
                $tm       = date('Y-m-d H:i:s', time());
                $orderid    = $data->id;
                $supplier   = $data->supplier_name;
                $supplierid   = $data->supplier_id;
                $totalamount  = $payable_amount;
                $outletid   = $data->outlet_id;
                $outletname   = $data->outlet_name;
            }

            $value = array(
                'Bcheque' => $request->cheque_no,
                'Bcheque_bank' => '',
                'Bcheque_date' => $request->cheque_date,
                'addi_card_numb' => '',
                'card_numb' => '',
                'cheque' => '',
                'cheque_account_C' => '',
                'cheque_bank' => '',
                'cheque_date' => '',
                'customer' => '',
                'paid' => $request->paid_to_supplier,
                'paid_by' => Auth::user()->id
            );

            if (!empty($value)) {
                $cheque_bank = '';
                $totalpaudamount = 0;
                $payment = $value;
                // $totalpaudamount  = $totalpaudamount + $value['paid'];
                $paid_amt     = $value['paid'];
                $cheque       = $value['cheque'];
                $cheque_date = date('Y-m-d');
                $cheque_bank = $value['cheque_bank'];
                $addi_card_numb   = $value['addi_card_numb'];
                $giftcard_numb    = $value['card_numb'];
                $cheque_value   = $value['cheque_bank'];
                $payment_method_id  = $value['paid_by'];
                $cheque_bank = $value['Bcheque_bank'];

                if ($value['paid_by'] != 12) {
                    // $getPayMethodData = $this->Constant_model->getDataOneColumn('payment_method', 'id', $payment_method_id);
                    // if (count($getPayMethodData) == 1) {
                    //     $payMethod_name   = $getPayMethodData[0]->name;
                    //     $payMethod_balance  = $getPayMethodData[0]->balance;
                    // }
                } else {
                    $payMethod_name = "Cheque";
                    $cheque       = $value['Bcheque'];
                    $cheque_date = date('Y-m-d');
                    $cheque_bank = $value['Bcheque_bank'];
                    $cheque_value   = $value['Bcheque_bank'];
                }

                // $ins_order_data = array(
                //     'purchase_id'   => $orderid,
                //     'grandtotal'    => $totalamount,
                //     'supplier_id'   => $supplierid,
                //     'supplier_name'   => $supplier,
                //     'gift_card'     => $giftcard_numb,
                //     'payment_method'  => $payment_method_id,
                //     'payment_name'    => $payMethod_name,
                //     'cheque_number'   =>$request->cheque_no,
                //     'cheque_date'   => $cheque_date,
                //     'cheque_bank'   => $cheque_bank,
                //     'paid_amt'      => $paid_amt,
                //     'paid_date'     => $tm,
                //     'outlet_id'     => $outletid,
                //     'outlet_name'   => $outletname,
                //     'created_by'    => $us_id,
                //     "card_number"   => $addi_card_numb,
                //     'bank_number'    => $cheque_bank,
                //     'transaction_date' => date("Y-m-d")
                // );
                // $this->db->insert('purchase_bills', $ins_order_data);


                //creating payment transaction
                $business_id = $request->session()->get('user.business_id');
                $transaction_id = $request->purchase_id;
                $transaction = Transaction::where('business_id', $business_id)->findOrFail($transaction_id);
           
                if ($transaction->payment_status != 'paid') {
                    $inputs = [
                        'amount' => $request->cheque_amount,
                        'method' => 'cheque',
                        'note' => null,
                        'card_number' => null,
                        'card_holder_name' => null,
                        'card_transaction_number' => null,
                        'card_type' => null,
                        'card_month' => null,
                        'card_year' => null,
                        'card_security' => null,
                        'cheque_number' => $request->cheque_no,
                        'bank_account_number' => null
                    ];

                    $inputs['paid_on'] = $request->cheque_date;//$this->transactionUtil->uf_date($request->cheque_date, false);
                    $inputs['transaction_id'] = $transaction->id;
                    $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);
                    $inputs['created_by'] = auth()->user()->id;
                    $inputs['payment_for'] = $request->payee; // we have ignored current system contact/suppplier
                    $inputs['payment_type'] = $request->paymentType; // we have ignored current system contact/suppplier

                    if ($inputs['method'] == 'custom_pay_1') {
                        $inputs['transaction_no'] = $request->transaction_no_1;
                    } elseif ($inputs['method'] == 'custom_pay_2') {
                        $inputs['transaction_no'] = $request->transaction_no_2;
                    } elseif ($inputs['method'] == 'custom_pay_3') {
                        $inputs['transaction_no'] = $request->transaction_no_3;
                    }
                    
                    $inputs['account_id'] = $accountInfo->id;
                    
                    $prefix_type = 'purchase_payment';
                    if (in_array($transaction->type, ['sell', 'sell_return'])) {
                        $prefix_type = 'sell_payment';
                    } elseif ($transaction->type == 'expense') {
                        $prefix_type = 'expense_payment';
                    }

                    DB::beginTransaction();
                 
                    $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type);
                    //Generate reference number
                    $inputs['payment_ref_no'] = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);

                    $inputs['business_id'] = $request->session()->get('business.id');
                    $inputs['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');

                    $tp = TransactionPayment::create($inputs);
                    AccountTransaction::updateAccountTransaction($tp, $transaction->type);
                    // if($request->paymentFor=='purchases'){
                    //     $accountPayable = Account::where('account_number','201')->orderBy('created_at', 'desc')->first();
                    //     if($accountPayable){
                    //         $ob_transaction_data = [
                    //             'amount' => $this->transactionUtil->num_uf($inputs['amount']),
                    //             'account_id' => $accountPayable->id,
                    //             'type' => 'debit',
                    //             'sub_type' => 'ledger_show',
                    //             'operation_date' => \Carbon::now(),
                    //             'created_by' => Auth::user()->id,
                    //             'transaction_id' => $tp->transaction_id,
                    //             'transaction_payment_id' => $tp->id
                    //         ];
                    //         AccountTransaction::createAccountTransaction($ob_transaction_data);
                    //     }
                    // }else{
                    //     // $ob_transaction_data = [
                    //     //     'amount' => $this->transactionUtil->num_uf($inputs['amount']),
                    //     //     'account_id' => $accountInfo->id,
                    //     //     'type' => 'credit',
                    //     //     'sub_type' => 'ledger_show',
                    //     //     'operation_date' => \Carbon::now(),
                    //     //     'created_by' => Auth::user()->id
                    //     // ];
                    //     // AccountTransaction::createAccountTransaction($ob_transaction_data);
                    // }
                    
                    
                    //update payment status
                    $this->transactionUtil->updatePaymentStatus($transaction_id, $transaction->final_total);
                    $inputs['transaction_type'] = $transaction->type;
                    event(new TransactionPaymentAdded($tp, $inputs));
                    DB::commit();
                }


                // $purchase_bill_last_id = $this->db->insert_id();

                // if (!empty($giftcard_numb)) {
                //     $ckGiftResult = $this->db->query("SELECT * FROM gift_card WHERE card_number = '$giftcard_numb' ");
                //     $ckGiftRows = $ckGiftResult->num_rows();
                //     if ($ckGiftRows == 1) {
                //         $ckGiftData = $ckGiftResult->result();
                //         $ckGift_id = $ckGiftData[0]->id;
                //         $upd_gift_data = array(
                //             'status' => '1',
                //             'updated_user_id' => $us_id,
                //             'updated_datetime' => $tm,
                //         );
                //         $this->Constant_model->updateData('gift_card', $upd_gift_data, $ckGift_id);
                //     }
                // }

                // if ($value['paid_by'] != 12) {
                //     $pay_query = $this->db->get_where('payment_method', array('id' => $payment_method_id))->row();
                //     $pay_balance = $pay_query->balance;
                //     $now_balance = $pay_balance - $paid_amt;

                //     $pay_data  = array(
                //         'balance'     => $now_balance,
                //         'updated_user_id' => $us_id,
                //         'updated_datetime'  => $tm,
                //     );
                //     $this->db->update('payment_method', $pay_data, array('id' => $payment_method_id));

                //     $trans_ins = array(
                //         'order_id'      => $orderid,
                //         'account_number'  => $payment_method_id,
                //         'bring_forword '  => $pay_balance,
                //         'outlet_id'     => $outletid,
                //         'trans_type'    => 'payment_s',
                //         'amount'      => $value['paid'],
                //         'cheque_number'   => $this->input->post('cheque_no'),
                //         'card_number'   => $addi_card_numb,
                //         'cheque_date'   => date('Y-m-d'),
                //         'created_by'    => $us_id,
                //         'created'     => date('Y-m-d H:i:s'),
                //         'transaction_type' => 'supplier_payment_from_pm',
                //         'purchase_bill_last_id' => $purchase_bill_last_id,
                //         'transaction_date' => date('Y-m-d')
                //     );
                //     $this->db->insert('transactions', $trans_ins);
                // } else {
                //     $bank_bal = $this->db->get_where('bank_accounts', array('id' => $value['Bcheque_bank']))->row();
                //     $bank_val = $bank_bal->current_balance;
                //     $totalamt = $bank_val - $value['paid'];
                //     $paybal   = array('current_balance' => $totalamt);
                //     $this->Constant_model->updateData('bank_accounts', $paybal, $cheque_bank);

                //     $trans_ins = array(
                //         'order_id'      => $orderid,
                //         'account_number'  => $value['Bcheque_bank'],
                //         'bring_forword '  => $bank_val,
                //         'outlet_id'     => $outletid,
                //         'trans_type'    => 'payment_s',
                //         'amount'      => $value['paid'],
                //         'cheque_number'   => $cheque,
                //         'card_number'   => $addi_card_numb,
                //         'cheque_date'   => date('Y-m-d'),
                //         'created_by'    => $us_id,
                //         'transaction_type' => 'supplier_payment_from_bank',
                //         'created'     => date('Y-m-d H:i:s'),
                //         'transfer_status' => 1,
                //         'transaction_date' => date("Y-m-d")
                //     );
                //     $this->db->insert('transactions', $trans_ins);

                $cheque_no_for_update = $cheque;
                $cheque_status = array(
                    'status' => 1
                );
                
                
              
        
                // $this->Constant_model->updateChequeStatus($cheque_status, $cheque_no_for_update);
                // ChequeNumberMaintain::where('cheque_no', $cheque_no_for_update)->update($cheque_status);
                // }
            }

            // $purchaseorderdetails = $this->db->select('paid_amt')->where('id', $orderid)->get('purchase_order')->row_array();

            // $ins_order_data = array(
            //     'paid_amt' => $totalpaudamount,
            //     'updated_user_id' => $this->session->userdata('user_id'),
            //     'payment_method_name' => $payMethod_name,
            //     'updated_datetime' => $tm,
            // );
            // $this->Constant_model->updateData('purchase_order', $ins_order_data, $orderid);
        }
       
        // $this->session->set_flashdata('alert_msg', array('success', 'Add category status', "Template added successfully"));

        $response = array(
            'status' => 1,
            // 'url' => base_url() . 'printed_cheque_details'
        );
        // $response['payment_collection'] = $payment_collection;
        echo json_encode($response);
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getChequeNoUniqueOrNotCheck(Request $request)
    {
        $cheque_no = $request->chequeNo;
        $payee = $request->supplierid;
        $business_id = $request->session()->get('business.id');

        $json['cheque_check'] = PrintedChequeDetail::where('cheque_no', $cheque_no)->where('payee', $payee)->where('business_id', $business_id)->first();

        return json_encode($json);
    }

    public function getTempleteWiseBankAccounts(Request $request)
    {
        $templtID = $request->templtID;
        $getBankacounts = ChequerBankAccount::where('is_visible', 1)->where('cheque_templete_id', $templtID)->get();
        $banks = '';
        if (!empty($getBankacounts)) {
            foreach ($getBankacounts as $getbankacount) {
                $banks .= "<option value='" . $getbankacount->account->account_number . "' >" . $getbankacount->account->name . "</option>";
            }
        }
        $banks .= '<option value="">None</option>';
        $json['banks']    = $banks;

        echo json_encode($json);
    }

    public function listOfPayeeTemp(Request $request)
    {
        $supplier_id = $request->supplier_id;
        $paymentFor = $request->paymentFor;
        $paymentType = $request->paymentType;
        // $json['results']=$this->Common_model->get_data_by_query("select * from printed_cheque_details where payee='$myText' group by payee_tempname");
        // $json['get_purchase_order'] =$this->Common_model->get_data_by_query("select id from purchase_order where supplier_id='$myText'");

        // $json['results'] = PrintedChequeDetail::where('payee', $supplier_id)->groupBy('payee_tempname')->get();
        // transactions.type', 'expense'
        $total_balence_amount=0;
        if($paymentFor=='purchases')
            $collection = Transaction::where('contact_id', $supplier_id)->where('type', 'purchase')->where('payment_status', '!=', 'paid');
        else if($paymentFor=='expenses')
            $collection = Transaction::where('type', 'expense')->where('payment_status', '!=', 'paid');
        else
            $collection = Transaction::where('contact_id', $supplier_id)->where('type', 'purchase')->where('payment_status', '!=', 'paid');    
        
        if($paymentType == 'new_payment')
            $collection = $collection->where('payment_status', '=', 'due');
        if($paymentType == 'balance_payment')
            $collection = $collection->where('payment_status', '=', 'partial');
        $rows = $collection->get();
        foreach($rows as $row)
        {
            $total_balence_amount+=$row->final_total - $row->amount;
        }
        $json['get_purchase_order'] = $rows;
        $json['balence_amount'] = $total_balence_amount;
        echo json_encode($json);
    }

    public function getPurchaseOrderDataById(Request $request)
    {
        $purchase_id = $request->purchase_id;
        // $json['results'] =$this->Common_model->get_data_by_query("select * from purchase_order where id='$purchase_id'");
        // $json['results'] = ChequerPurchaseOrder::where('id', $purchase_id)->first(); 

        $amount = 0;
        $grandtotal = 0;
        // $purchase_bill = $this->db->query("SELECT purchase_id, SUM(paid_amt) as amount FROM purchase_bills WHERE purchase_id= ".$purchase_id)->result();
        // $purchase_data = $this->db->query("select * from purchase_order where id='$purchase_id'")->result();
        $transaction_date = Transaction::where('id', $purchase_id)->first();
        $purchase_data = TransactionPayment::where('transaction_id', $purchase_id)->get();
        // foreach ($purchase_bill as $value) {
        // 	$amount = $value->amount;
        // }
        if($transaction_date)
            $json['purchase_bill_no'] =  $transaction_date->ref_no;
        else
            $json['purchase_bill_no'] = 0;
        // foreach ($purchase_data as $data) {
        //     $grandtotal = $data->final_total;
        // }
        $unpaid_amt = $transaction_date->final_total;
        foreach($purchase_data as $row){
            $unpaid_amt -= $row->amount; // $amount - $grandtotal;
        }
        $json['dueamount'] = floatval($unpaid_amt);

        echo json_encode($json);
    }

    public function checkTemplateId(Request $request)
    {
        $printchaque_id = $request->printchaque_id;

        // $data['get_cheaquetemp']=$this->Common_model->get_data_by_query("select * from printed_cheque_details as pcd left join cheque_templates as ct on pcd.template_id=ct.id where pcd.id='$printchaque_id' ");
        $data['get_cheaquetemp'] = PrintedChequeDetail::join('cheque_templates', 'printed_cheque_details.template_id', 'cheque_templates.id')->where('printed_cheque_details.id', $printchaque_id)->get();
        foreach ($data['get_cheaquetemp'] as $chaquetempid) {
        }
        $stampvalu = $chaquetempid['stampvalu'];
        // $stamdata=$this->Common_model->get_data_by_query("select * from stamps_table where stamp_status='1' and stamp_id='$stampvalu'");
        $stamdata = ChequerStamp::where('id', $stampvalu)->where('stamp_status', 1)->get();
        foreach ($stamdata as $stamdetlials) {
        }
        echo $chaquetempid['template_id'] . ',' . $chaquetempid['id'], ',' . $chaquetempid['stampvalu'] . ',' . $chaquetempid['stamp_image'] . ',' . $chaquetempid['cheque_amount'] . ',' . $chaquetempid['is_strikeBearer'] . ',' . $chaquetempid['is_dublecross'] . ',' . $chaquetempid['amount_word'];
    }

    public function getTemplatechaque(Request $request)
    {
        $id = $request->id;
        // $values = $this->db->get_where('cheque_templates', array('id' => $id))->row();
        $values = ChequeTemplate::where('id', $id)->first();
        echo json_encode($values);
    }

    public function getNextChequeRecord($nextChequeno,$business_id,$bankData, $cancelChequeNoList){

        if(in_array($nextChequeno, $cancelChequeNoList))
        {
            return $this->getNextChequeRecord($nextChequeno + 1, $business_id,$bankData, $cancelChequeNoList);

        }else{
            return $nextChequeno;    
        }
    }

     public function getChequedNoTemplate(Request $request)
{
    $business_id = $request->session()->get('business.id');
    $bankAccount = $request->input('bank_account'); // Ensure correct parameter name
 
    // Find bank account
    $bankData = Account::where('id', $bankAccount)
        ->first();

    if ($bankData) {
        // Fetch cheque template based on bank account ID
        $row = ChequerBankAccount::where('account_id', $bankData->id)
            ->first();
          
        return response()->json([
            'cheque_template' => $row]);
    }

    return response()->json(['cheque_template' => null]); // Return empty template if no match
}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     
    
public function getAccountType(Request $request)
{
    $business_id = $request->session()->get('business.id');
    $paymentFor = $request->input('payment_for');

    // Initialize an empty accounts array
    $accounts = [];

    if ($paymentFor == 'purchases') {
        // Match multiple account types
        $accountTypes = AccountType::whereIn('name', ['Current Assets', 'Current Liabilities'])
        ->where('business_id',$business_id)
        ->pluck('id');

        if ($accountTypes->isNotEmpty()) {
            // Fetch accounts that match any of the account type IDs
           $accounts = Account::whereIn('account_type_id', $accountTypes)
                   ->whereIn('name', ['Finished Goods Account', 'Accounts Payable'])
                   ->where('business_id',$business_id)
                   ->groupBy('name') // Ensures only one of each name
                   ->get();
        }
    } elseif ($paymentFor == 'expenses') {
        $accountType = AccountType::where('name', $paymentFor)
        ->where('business_id',$business_id)
        ->first();

        $accounts = collect(); // Default to empty collection
        
        if ($accountType) {
            $accounts = Account::where([
                ['account_type_id', '=', $accountType->id],
                ['business_id', '=', $business_id],
            ])->get();
            
        }
        
      
    }

    // Generate option elements for the select dropdown
    $options = '<option value="">Select Account</option>';
    $options = '<option value="55">Not Applicable</option>';
    foreach ($accounts as $account) {
        $options .= "<option value='{$account->id}'>{$account->name}</option>";
    }

    return response()->json($options);
}
 /**
     * @doc: 6953 write Cheque page
     *
     * Send next cheque book no
     *
     * @dev sakhawat kamran
     **/
    public function getNextChequedNO(Request $request)
    {
        try {
           
            $business_id = request()->session()->get('business.id');
            $bankData = Account::where('business_id', $business_id)->where('id', $request->bankacount)->first();
             
            $row = ChequeNumber::where('business_id', $business_id)->where('account_no', $bankData->id)->whereStatus('active')->first();
            $templates = ChequeTemplate::getTemplates($business_id);
          
            if(is_null($row))
            {
                $row = ChequeNumber::where('business_id', $business_id)->where('account_no', $bankData->id)->whereStatus('active')->first();
            }
           
            if($row)
            {
                $last_cheque_no = $row->last_cheque_no; 
                $cancelCheque = CancelCheque::whereBusinessId($business_id)->whereBetween('cheque_no',[$row->first_cheque_no,$row->last_cheque_no])->where('account_id',$row->account_no)->pluck('cheque_no')->toArray();
                $next_check_no = is_null($row->latest_cheque_issue)? $row->first_cheque_no : $row->latest_cheque_issue + 1;
                $next_check_no = $this->nextCheque($next_check_no,$cancelCheque,$last_cheque_no);

                $nextChequeNumberMEntry = null;
                $ChequeNumbersMEntry = ChequeNumbersMEntry::where('business_id', $business_id)->where('bank_id', $bankData->id)->latest()->first();
                if(!is_null($ChequeNumbersMEntry)){
                    $nextChequeNumberMEntry = $ChequeNumbersMEntry->next_cheque_number_to_auto_print;
                    if(empty($nextChequeNumberMEntry)){
                        $nextChequeNumberMEntry = $ChequeNumbersMEntry->new_cheque_number_to_print;
                    }
                }
                if(!empty($nextChequeNumberMEntry)){
                    $ChequeNumber = ChequeNumber::where('id', $ChequeNumbersMEntry->cheque_number_id)->first();
                    //$reference_no = $ChequeNumber->reference_no;
                    $next_check_no = $nextChequeNumberMEntry;
                } else {
                    $reference_no = $row->reference_no;
                }
                if($next_check_no != 0){
                   return  $output = [
                        'success' => true,                       
                        'next_cheque_no' => $next_check_no,
                        'templates' => $templates
                    ];
                }
            }
            else
            {
            $output = [
                    'success' => false,
                    'msg' => __("cheque.cheque_book_no_found")
                    ];
            
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage().$bankData);

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return  $output;
    }
     function nextCheque($current_cueque_no,$cancelCheque,$last_cheque_no){
        if($current_cueque_no > $last_cheque_no )
           return 0;
        if(in_array($current_cueque_no,$cancelCheque))
        {
            $current_cueque_no =  $this->nextCheque($current_cueque_no++,$cancelCheque);
        }
        return $current_cueque_no;
    }

    
}
