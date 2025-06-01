<?php
namespace App\Http\Controllers;
use App\Account;
use App\AccountType;
use App\AccountTransaction;
use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\ContactLedger;
use App\Customer;
use App\Product;
use App\Store;
use App\SupplierProductMapping;
use App\Media;
use App\PurchaseLine;
use App\ContactGroup;
use App\CustomerReference;
use App\System;
use App\Transaction;
use App\TransactionPayment;
use App\User;
use App\UserContactAccess;
use App\Utils\ModuleUtil;
use App\Utils\BusinessUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use App\Utils\ContactUtil;
use App\NotificationTemplate;


use Illuminate\Support\Facades\DB;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Milon\Barcode\DNS1D;
use Milon\Barcode\Facades\DNS1DFacade;
use Yajra\DataTables\Facades\DataTables;
use App\new_vehicle;
use App\ContactLinkedAccount;
use Maatwebsite\Excel\Facades\Excel as MatExcel;
use App\Exports\ContactOpeningBalanceExport;
use Modules\Airline\Entities\AirlineFormSettingCustomer;
use Spatie\Activitylog\Models\Activity;
class ContactController extends Controller
{
    protected $commonUtil;
    protected $transactionUtil;
    protected $moduleUtil;
    protected $businessUtil;
    protected $productUtil;
    protected $contactUtil;
    //protected $balance_duen;
    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(
        Util $commonUtil,
        ModuleUtil $moduleUtil,
        TransactionUtil $transactionUtil,
        BusinessUtil $businessUtil,
        ProductUtil $productUtil,
        ContactUtil $contactUtil
        //balance_duen $GLOBALS
    ) {

        $this->commonUtil = $commonUtil;
        $this->moduleUtil = $moduleUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
        $this->contactUtil = $contactUtil;
        //$this->balance_duen =& $GLOBALS;
    }
    /**
    public function getBalance() {
        return $this->balance_duen['n'];
    }
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getImportBalance(){
        if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }
        $zip_loaded = extension_loaded('zip') ? true : false;
        //Check if zip extension it loaded or not.
        if ($zip_loaded === false) {
            $output = [
                'success' => 0,
                'msg' => 'Please install/enable PHP Zip archive for import'
            ];
            return view('contact.import')
                ->with('notification', $output);
        } else {
            return view('contact.import-balance');
        }
    }

    public function exportBalance(Request $request){
        $selected_rows = explode(',', $request->input('selected_rows'));
        $business_id = $request->session()->get('user.business_id');
        $contacts = Contact::where('business_id', $business_id)
            ->whereIn('id', $selected_rows)
            ->get();

        $data = [];

        foreach($contacts as $one){
            $data[] = array("contact_id" => $one->contact_id, 'type' => $one->type, 'name' => $one->name);
        }

        // dd($data);

        $response = MatExcel::download(new ContactOpeningBalanceExport(
        $data
        ),"Contact-Opening-Balance.xls");

        if (ob_get_level() > 0) {
            ob_end_clean();
        }
            
        return $response;

    }
    public function settings(){
        $business_id = request()->session()->get('user.business_id');

        $data = ContactLinkedAccount::where('contact_linked_accounts.business_id',$business_id)
                    ->join('users as u','u.id','contact_linked_accounts.created_by')
                    ->join('accounts as c','c.id','contact_linked_accounts.customer_advance')
                    ->join('accounts as s','s.id','contact_linked_accounts.supplier_advance')
                    ->leftjoin('accounts as cdr_liability','cdr_liability.id','contact_linked_accounts.customer_deposit_refund_liability_account')
                    ->leftjoin('accounts as cdr_asset','cdr_asset.id','contact_linked_accounts.customer_deposit_refund_asset_account')
                    ->select('contact_linked_accounts.*','c.name as cust','s.name as sup','u.username','cdr_liability.name as _customer_deposit_refund_liability_account','cdr_asset.name as _customer_deposit_refund_asset_account')->first();


        $liability = AccountType::getAccountTypeIdByName('Current Liabilities', $business_id)->id;


        $liability_accounts = Account::where('business_id', $business_id)->where('account_type_id', $liability)->pluck('name', 'id');



        $asset = AccountType::getAccountTypeIdByName('Current Assets', $business_id)->id;
        $asset_accounts = Account::where('business_id', $business_id)->where('account_type_id', $asset)->pluck('name', 'id');
        return view('contact.settings')
            ->with(compact('data','liability_accounts','asset_accounts'));
    }

    public function save_settings(Request $request){

        try {
            $input = $request->except('_token');

            $input['business_id'] = $business_id = $request->session()->get('user.business_id');
            $input['created_by'] = auth()->user()->id;



            ContactLinkedAccount::updateOrCreate(['business_id' => $business_id],$input);

                $output = [
                    'success' => 1,
                    'msg' => __('message.success'),
                    'background' => 'alert-success'
                ];
                DB::commit();

                return back()->with('status', $output);


        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => $e->getMessage(),
                'background' => 'alert-danger'
            ];
            return back()->with('status', $output);
        }
    }
    
    public function addVatNumber($id){
        
        $is_single = request()->is_single ?? false;
        
        $contact = Contact::findOrFail($id);
        
        if(empty($is_single)){
            return view('contact.update_vat_number')
                ->with(compact('contact'));
        }else{
            return view('contact.update_single_fields')
                ->with(compact('contact'));
        }
            
    }

    public function updateVatNumber(Request $request,$id){

        try {
            $input = $request->except('_token','_method');
            
            if($request->hasFile('image')){
                $imageName = Media::uploadFile($request->file('image'));
                $input['image']=$imageName;
            }
            
            Contact::where('id',$id)->update($input);
            
            $contact = Contact::findOrFail($id);
            

            $output = [
                'success' => 1,
                'msg' => __('message.success'),
                'background' => 'alert-success',
                'contact' => $contact
            ];
            DB::commit();

            return $output;


        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => $e->getMessage(),
                'background' => 'alert-danger'
            ];
            return $output;
        }
    }


    public function postImportBalance(Request $request)
    {
        if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $notAllowed = $this->commonUtil->notAllowedInDemo();
            if (!empty($notAllowed)) {
                return $notAllowed;
            }
            //Set maximum php execution time
            ini_set('max_execution_time', 0);
            if ($request->hasFile('contacts_csv')) {
                $file = $request->file('contacts_csv');
                $parsed_array = Excel::toArray([], $file);
                //Remove header row
                $imported_data = array_splice($parsed_array[0], 1);
                $business_id = $request->session()->get('user.business_id');
                $user_id = $request->session()->get('user.id');
                $formated_data = [];
                $is_valid = true;
                $error_msg = '';
                DB::beginTransaction();
                $ob_data = array();
                foreach ($imported_data as $key => $value) {

                    if(!empty($value[0])){
                        $contact_id = $value[0];

                        $contact = Contact::where('contact_id',$contact_id)->first();
                        // if(count($value) % 2 == 0){
                        //      $is_valid =  false;
                        //      $error_msg = "Number of columns mismatch";
                        //      break;
                        // }

                        for ($i = 3; $i < count($value); $i += 3) {

                           if(!empty($contact) && !empty($value[$i + 2]) && !empty($value[$i + 1]) && !empty($value[$i])){
                                $opening_balance = $value[$i + 1];
                                $invoice_no = $value[$i + 2];

                                $currentDate = new \DateTime();
                                $currentDate->sub(new \DateInterval('P' . $value[$i] . 'D'));
                                $transaction_date = $currentDate->format('Y-m-d');
                                $ob_data[] = [$contact->id,$opening_balance, $transaction_date];
                                $this->transactionUtil->createOpeningBalanceTransaction($business_id, $contact->id, $opening_balance, $transaction_date, $invoice_no);
                            }
                        }
                    }
                }


                $output = [
                    'success' => 1,
                    'msg' => __('product.file_imported_successfully'),
                    'background' => 'alert-success'
                ];
                DB::commit();

                return back()->with('notification', $output);

            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => $e->getMessage(),
                'background' => 'alert-danger'
            ];
            return back()->with('notification', $output);
        }

    }


    public function index()
    {
        
        $type = request()->get('type');
        $module = request()->get('module') ?? 'other';
        $types = ['supplier', 'customer'];
        $business_id = request()->session()->get('user.business_id');
        if (empty($type) || !in_array($type, $types)) {
            return redirect()->back();
        }

        if (request()->ajax()) {
            return $type == 'supplier' ? $this->indexSupplier($module) : ($type == 'customer' ? $this->indexCustomer() : abort(404));
        }
        $reward_enabled = (request()->session()->get('business.enable_rp') == 1 && $type == 'customer');

        // Get contact fields from session or set to empty array
        $contact_fields = session('business.contact_fields', []);

        // Get user groups for dropdown
        $user_groups = User::forDropdown($business_id);

        // Check if it's a property customer
        $is_property = isset($is_property_customer);

        // Check customer code and get contact ID
        $contact_id = $this->businessUtil->check_customer_code($business_id);

        return view('contact.index', compact('type', 'reward_enabled', 'contact_fields', 'is_property', 'user_groups', 'contact_id'));
    }
    
    public function loan()
    
    {   
       $business_id = request()->session()->get('user.business_id');  
       $type ='customer';  
        $locations =BusinessLocation::where('business_id', $business_id)->pluck('name', 'id'); 
        $customer=Contact::where('business_id', $business_id)->pluck('name', 'id'); 
        $username=User::where('business_id', $business_id)->pluck('username', 'id'); 
   
          return view('customer_loan.index', compact('type','locations','customer','username'));
    }
    
    public function customer_loan_view(Request $request)
    
    {   
          
        $customer_id=$request->customer_id;
  
      $route_operations = DB::table('contacts')
                ->join('transactions', 'contacts.id', '=', 'transactions.contact_id')
                ->join('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
                 ->join('users', 'users.id', '=', 'transactions.business_id')
                ->join('business_locations', 'business_locations.id', '=', 'transactions.location_id')
                ->select('contacts.name as contact_name', 'account_transactions.amount as transaction_amount', 'account_transactions.created_at','users.username as usernames','business_locations.name as location_name','contacts.id as customer_id')
                ->where('contacts.type', '=', 'customer')
                ->where('account_transactions.type', '=', 'debit')
                 ->where('transactions.payment_status', '=', 'due')
                 ->where('contacts.id', '=', $customer_id)
                ->get();
   
          return view('customer_loan.print', compact('route_operations'));
    }
    public function customer_loans()
    
    { 
        
      $route_operations = DB::table('contacts')
            ->join('transactions', 'contacts.id', '=', 'transactions.contact_id')
            ->join('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
             ->join('users', 'users.id', '=', 'transactions.business_id')
            ->join('business_locations', 'business_locations.id', '=', 'transactions.location_id')
            ->select('contacts.name as contact_name', 'account_transactions.amount as transaction_amount', 'account_transactions.created_at','users.username as usernames','business_locations.name as location_name','contacts.id as customer_id')
            ->where('contacts.type', '=', 'customer')
            ->where('account_transactions.type', '=', 'debit')
             ->where('transactions.payment_status', '=', 'due')
            ->get();
         

 
        return $route_operations;
    }
    public function customer_loans_list(Request $request)
    
    { 
        $customer=$request->customer;
         $user=$request->user;
        $date=$request->date;
         
         if($customer)
         {
            $route_operations = DB::table('contacts')
            ->join('transactions', 'contacts.id', '=', 'transactions.contact_id')
            ->join('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
            ->join('users', 'users.id', '=', 'transactions.business_id')
            ->join('business_locations', 'business_locations.id', '=', 'transactions.location_id')
            ->select('contacts.name as contact_name', 'account_transactions.amount as transaction_amount', 'account_transactions.created_at','users.username as usernames','business_locations.name as location_name','contacts.id as customer_id')
            ->where('contacts.type', '=', 'customer')
            ->where('account_transactions.type', '=', 'debit')
            ->where('transactions.payment_status', '=', 'due')
            ->where('contacts.id', '=', $customer)
            ->get();
         }
         else if($user)
         {
              $route_operations = DB::table('contacts')
                ->join('transactions', 'contacts.id', '=', 'transactions.contact_id')
                ->join('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
                ->join('users', 'users.id', '=', 'transactions.business_id')
                ->join('business_locations', 'business_locations.id', '=', 'transactions.location_id')
                ->select('contacts.name as contact_name', 'account_transactions.amount as transaction_amount', 'account_transactions.created_at','users.username as usernames','business_locations.name as location_name','contacts.id as customer_id')
                ->where('contacts.type', '=', 'customer')
                ->where('account_transactions.type', '=', 'debit')
                ->where('transactions.payment_status', '=', 'due')
                ->where('users.id', '=', $user)
                ->get();
         }
         else if($date)
         {
            $dateRange =$date;
            
           
            $dates = explode(" - ", $dateRange);
            $startDate = $dates[0];  
            $endDate = $dates[1]; 
            $startTimestamp = date('Y-m-d H:i:s', strtotime($startDate . ' 00:00:00'));
            $endTimestamp = date('Y-m-d H:i:s', strtotime($endDate . ' 23:59:59'));
              $route_operations = DB::table('contacts')
                    ->join('transactions', 'contacts.id', '=', 'transactions.contact_id')
                    ->join('account_transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
                    ->join('users', 'users.id', '=', 'transactions.business_id')
                    ->join('business_locations', 'business_locations.id', '=', 'transactions.location_id')
                    ->select('contacts.name as contact_name', 'account_transactions.amount as transaction_amount', 'account_transactions.created_at','users.username as usernames','business_locations.name as location_name','contacts.id as customer_id')
                    ->where('contacts.type', '=', 'customer')
                    ->where('account_transactions.type', '=', 'debit')
                    ->where('transactions.payment_status', '=', 'due')
                    ->whereBetween('account_transactions.created_at', [$startTimestamp, $endTimestamp])
                    ->get();
         }
        
        
        return $route_operations;
    }
    
    /**
     * Returns the database object for supplier
     *
     * @return \Illuminate\Http\Response
     */
    private function indexSupplier($module = 'other')
    {
        if (!auth()->user()->can('supplier.view')) {
            abort(403, 'Unauthorized action.');
        }
        $businessId = request()->session()->get('user.business_id');

        $contactsQuery = $this->getSupplierContactsData($businessId,$module);

        $finalSql = DB::table(DB::raw("({$contactsQuery->toSql()}) as sub"))
            ->mergeBindings($contactsQuery->getQuery());

        return Datatables::of($finalSql)
            ->addColumn('action', function ($row) {
                return view('contact.supplier-actions', $this->getSupplierActionData($row))->render();
            })
            ->editColumn('due', function ($row) use ($businessId) {
                $due = $this->contactUtil->getSupplierBalance($row->id,$businessId,true);
                $html = '<h5 class="display_currency due" data-currency_symbol="true" data-orig-value="' .  $due . '">' . $this->commonUtil->num_f($due) . '</h5>';
                return $html;
            })
            ->editColumn(
                'return_due',
                '<span class="display_currency return_due" data-orig-value="{{$return_due}}" data-currency_symbol=true data-highlight=false>{{@num_format($return_due) }}</span>'
            )
            ->editColumn('opening_balance', function ($row) {
                $paidOpeningBalance = !empty($row->opening_balance_paid) ? $row->opening_balance_paid : 0;
                $openingBalance = !empty($row->opening_balance) ? $row->opening_balance : 0;
                $balanceValue = $openingBalance - $paidOpeningBalance;
                $html = '<span class="display_currency ob" data-currency_symbol="true" data-orig-value="' . $openingBalance . '">' . $this->commonUtil->num_f($openingBalance) . '</span>';
                return $html;
            })
            ->addColumn('mass_delete', function ($row) {
                return  '<input type="checkbox" class="row-select" value="' . $row->id . '">';
            })
            ->addColumn('pay_term', '
        @if(!empty($pay_term_type) && !empty($pay_term_number))
            <span class="display_currency pay_term" data-currency_symbol="true" data-orig-value={{$pay_term_number}}>{{$pay_term_number}}</span>
            @lang("lang_v1.".$pay_term_type)
        @endif
    ')
            ->editColumn('created_at',  function ($row) {
                $obTransaction = Transaction::where('type', 'opening_balance')->where('contact_id', $row->id)->first();
                if (!empty($obTransaction)) {
                    return $this->transactionUtil->format_date($obTransaction->transaction_date);
                }
                return $this->transactionUtil->format_date($row->created_at);
            })
            ->addColumn('assigned_to', function ($row) {
                $html = '<span data-orig-value="' . $row->full_name . '">' . $row->full_name . '</span>';
                return $html;
            })
            ->filter(function ($query) {
                if (request()->has('user_id') && !empty(request()->get('user_id'))) {
                    $query->where('user_id', request()->get('user_id'));
                }
            }, true)
            ->removeColumn(['opening_balance_paid', 'type', 'full_name', 'id', 'total_purchase', 'purchase_paid', 'total_purchase_return', 'purchase_return_paid', 'user_id'])
            ->rawColumns(['action', 'opening_balance', 'pay_term', 'due', 'return_due', 'mass_delete', 'assigned_to'])
            ->make(true);
    }
    private function getSupplierActionData($row)
    {
        $business_id = request()->session()->get('user.business_id');
        return [
            'id' => $row->id,
            'opening_balance' => $row->opening_balance,
            'opening_balance_paid' => $row->opening_balance_paid,
            'total_purchase' => $row->total_purchase,
            'purchase_paid' => $row->purchase_paid,
            'total_purchase_return' => $row->total_purchase_return,
            'purchase_return_paid' => $row->purchase_return_paid,
            'return_due' => $row->return_due,
            'should_notify' => $row->should_notify,
            'type' => $row->type,
            'active' => $row->active,
            'total_due' => $this->contactUtil->getSupplierBalance($row->id,$business_id,true)
        ];
    }
    private function getSupplierContactsData($business_id,$module = 'other')
    {
        return  $contact = Contact::leftjoin('transactions AS t', 'contacts.id', '=', 't.contact_id')
            ->leftjoin('contact_groups AS cg', 'contacts.supplier_group_id', '=', 'cg.id')
            ->leftjoin('user_contact_access AS uca','contacts.id','uca.contact_id')
            ->leftjoin('users','uca.user_id','users.id')
            ->where('contacts.business_id', $business_id)
            ->where('contacts.is_payee', 0)
            // ->where('t.status', '!=', 'draft')
            ->where(function($q) {
                $q->where('contacts.type', 'supplier')
                    ->orWhere('contacts.type', 'both');
            })
            ->where('register_module',$module)
            // ->where('contacts.is_property', 0)
            //->onlySuppliers()
            ->select([
                'contacts.contact_id', 'supplier_business_name', 'contacts.active', 'contacts.name', 'cg.name as supplier_group', 'contacts.created_at', 'contacts.mobile', 't.transaction_date',
                'contacts.type', 'contacts.id', 'uca.user_id','should_notify',
                DB::raw("CONCAT(COALESCE(users.surname, ''),' ',COALESCE(users.first_name, ''),' ',COALESCE(users.last_name,'')) as full_name"),
                DB::raw("SUM(IF(t.type = 'purchase' AND t.status != 'draft', final_total, 0)) as total_purchase"),
                DB::raw("SUM(IF(t.type = 'purchase' AND t.status != 'draft', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id AND transaction_payments.deleted_at IS NULL), 0)) as purchase_paid"),
                DB::raw("SUM(IF(t.type = 'purchase' AND t.status != 'draft', final_total, 0) - IF(t.type = 'purchase', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id AND transaction_payments.deleted_at IS NULL), 0)) as due"),
                DB::raw("SUM(IF(t.type = 'purchase_return' AND t.status != 'draft', final_total, 0)) as total_purchase_return"),
                DB::raw("SUM(IF(t.type = 'purchase_return' AND t.status != 'draft', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id AND transaction_payments.deleted_at IS NULL), 0)) as purchase_return_paid"),
                DB::raw("SUM(IF(t.type = 'purchase_return' AND t.status != 'draft', final_total, 0) - IF(t.type = 'purchase_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id AND transaction_payments.deleted_at IS NULL), 0)) as return_due"),
                DB::raw("SUM(IF(t.type = 'opening_balance' AND t.status != 'draft', final_total, 0)) as opening_balance"),
                DB::raw("SUM(IF(t.type = 'opening_balance' AND t.status != 'draft', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id AND transaction_payments.deleted_at IS NULL), 0)) as opening_balance_paid"),
                'contacts.email', 'contacts.tax_number', 'contacts.pay_term_number', 'contacts.pay_term_type', 'contacts.custom_field1', 'contacts.custom_field2', 'contacts.custom_field3', 'contacts.custom_field4'
            ])
            ->groupBy('contacts.id');

    }

    /**
     * Returns the database object for customer
     *
     * @return \Illuminate\Http\Response
     */

    private function getCustomerContact($businessId) {
        return  Contact::leftjoin('transactions AS t', 'contacts.id', '=', 't.contact_id')
            ->leftjoin('contact_groups AS cg', 'contacts.customer_group_id', '=', 'cg.id')
            ->leftjoin('user_contact_access AS uca', 'contacts.id', 'uca.contact_id')
            ->leftjoin('users', 'uca.user_id', 'users.id')
            ->where('contacts.business_id', $businessId)
            ->whereIn('contacts.is_property', [1, 0])
            ->where(function ($q) {
                $q->where('contacts.type', 'customer')
                    ->orWhere('contacts.type', 'both');
            })
            ->select([
                'should_notify', 'contacts.contact_id', 'contacts.name', 'contacts.created_at', 'contacts.active',
                'cg.name as customer_group', 'mobile', 'contacts.id', 'is_default', 'uca.user_id', 'contacts.image', 'contacts.signature',
                DB::raw("CONCAT(COALESCE(users.surname, ''),' ',COALESCE(users.first_name, ''),' ',COALESCE(users.last_name,'')) as full_name"),
                DB::raw("SUM(IF(t.type = 'sell_return', final_total, 0)) as total_sell_return"),
                DB::raw("SUM(IF(t.type = 'sell_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id AND transaction_payments.deleted_at IS NULL), 0)) as sell_return_paid"),
                'contacts.pay_term_type', 'contacts.credit_limit', 'contacts.type',
                DB::raw("(select sum( if(contact_ledgers.type = 'debit' AND transactions.type = 'opening_balance' AND transactions.business_id=" . $businessId . ",contact_ledgers.amount,0) 
                        + if(contact_ledgers.type = 'debit' AND transactions.type != 'opening_balance' and transactions.business_id=" . $businessId . ", contact_ledgers.amount,0)
                        - if(contact_ledgers.type = 'credit' ,contact_ledgers.amount,0) )
                        from contact_ledgers
                        left join transactions on contact_ledgers.transaction_id=transactions.id
                        where contact_ledgers.contact_id=contacts.id  
                        GROUP BY contact_ledgers.contact_id) as due")
            ])
            ->groupBy('contacts.id');
    }
    
    private function getCustomerContact1($businessId) {
        return Contact::leftjoin('transactions AS t', 'contacts.id', '=', 't.contact_id')
            ->leftjoin('contact_groups AS cg', 'contacts.customer_group_id', '=', 'cg.id')
            ->leftjoin('user_contact_access AS uca', 'contacts.id', 'uca.contact_id')
            ->leftjoin('users', 'uca.user_id', 'users.id')
            ->where('contacts.business_id', $businessId)
            ->whereIn('contacts.is_property', [1, 0])
            ->where(function ($q) {
                $q->where('contacts.type', 'customer')
                    ->orWhere('contacts.type', 'both');
            })
            // Add this line to exclude soft-deleted contacts
            ->whereNull('contacts.deleted_at')
            ->select([
                'should_notify', 'contacts.contact_id', 'contacts.name', 'contacts.created_at', 'contacts.active',
                'cg.name as customer_group', 'mobile', 'contacts.id', 'is_default', 'uca.user_id', 'contacts.image', 'contacts.signature',
                DB::raw("CONCAT(COALESCE(users.surname, ''),' ',COALESCE(users.first_name, ''),' ',COALESCE(users.last_name,'')) as full_name"),
                DB::raw("SUM(IF(t.type = 'sell_return', final_total, 0)) as total_sell_return"),
                DB::raw("SUM(IF(t.type = 'sell_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id AND transaction_payments.deleted_at IS NULL), 0)) as sell_return_paid"),
                'contacts.pay_term_type', 'contacts.credit_limit', 'contacts.type',
                DB::raw("(select sum( if(contact_ledgers.type = 'debit' AND transactions.type = 'opening_balance' AND transactions.business_id=" . $businessId . ",contact_ledgers.amount,0)
                                + if(contact_ledgers.type = 'debit' AND transactions.type != 'opening_balance' and transactions.business_id=" . $businessId . ", contact_ledgers.amount,0)
                                - if(contact_ledgers.type = 'credit' ,contact_ledgers.amount,0) )
                                from contact_ledgers
                                left join transactions on contact_ledgers.transaction_id=transactions.id
                                where contact_ledgers.contact_id=contacts.id
                                GROUP BY contact_ledgers.contact_id) as due")
            ])
            ->groupBy('contacts.id');
    }
    private function indexCustomer()
    {
        // dd('123');
        if (!auth()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $query = $this->getCustomerContact($business_id);

        $finalSql = DB::table(DB::raw("({$query->toSql()}) as sub"))
            ->mergeBindings($query->getQuery());
        $contacts = Datatables::of($finalSql)
            ->addColumn('action', function ($row) {
                return view('contact.customer-actions', $this->getCustomerActionData($row))->render();
            })

            ->addColumn('return_due', function ($row) {
                $html = '<span class="display_currency" data-currency_symbol="true" data-orig-value="' . json_encode($row) . '">' . $this->commonUtil->num_f($row->total_sell_return-$row->sell_return_paid) . '</span>';
                return $html;
            })
            ->addColumn('image', function ($row) {
                if(isset($row->image) && $row->image!=null ){
                    $image = url('public/uploads/media/'.$row->image);
                    return  '<img class="popup" src="'.$image.'" height="50" width="50" >';
                }else{
                    return '';
                }
            })
            ->addColumn('signature', function ($row) {
                if(isset($row->signature) && $row->signature!=null ){
                    $signature = url('public/uploads/media/'.$row->signature);
                    return  '<img class="popup" src="'.$signature.'" height="50" width="50" >';
                }else{
                    return '';
                }
            })
            ->editColumn('due', function ($row) use ($business_id) {
                $balance = $this->contactUtil->getCustomerBalance($row->id,$business_id,true);
                $html = '<h5 class="display_currency due" data-currency_symbol="true" data-orig-value="' .  $balance . '">' . $this->commonUtil->num_f($balance) . '</h5>';
                return $html;
            })
            ->addColumn('assigned_to', function ($r) {
                return '<span data-orig-value="' . $r->full_name . '">' . $r->full_name . '</span>';
            })
            ->editColumn('credit_limit', function ($row) {
                $html = __('lang_v1.no_limit');
                if (!is_null($row->credit_limit)) {
                    $html = '<span class="display_currency credit_limit" data-currency_symbol="true" data-orig-value="' . $row->credit_limit . '">' . $this->commonUtil->num_f($row->credit_limit) . '</span>';
                }
                return $html;
            })
            ->addColumn('mass_delete', function ($row) {
                return  '<input type="checkbox" class="row-select" value="' . $row->id . '">';
            })
            ->editColumn('pay_term', '
                    @if(!empty($pay_term_type) && !empty($pay_term_number))
                        <span class="display_currency pay_term" data-currency_symbol="true" data-orig-value={{$pay_term_number}}>{{$pay_term_number}}</span>
                        @lang("lang_v1.".$pay_term_type)
                    @endif
                ')
            ->editColumn('created_at', function ($row) {
                $obTransaction = Transaction::where('type', 'opening_balance')->where('contact_id', $row->id)->first();
                if (!empty($obTransaction)) {
                    return $this->transactionUtil->format_date($obTransaction->transaction_date);
                }
                return $this->transactionUtil->format_date($row->created_at);
            })
            ->filter(function ($query) {
                if (request()->has('user_id') && !empty(request()->get('user_id'))) {
                    $query->where('user_id', request()->get('user_id'));
                }
            }, true)
            ->removeColumn(['type', 'id', 'is_default', 'total_sell_return', 'sell_return_paid', 'user_id', 'full_name']);

        return $contacts->rawColumns(['action', 'due', 'credit_limit', 'pay_term',  'return_due', 'mass_delete', 'assigned_to', 'image', 'signature'])
            ->make(true);
    }
    private function getCustomerActionData($row)
    {
        $business_id = request()->session()->get('user.business_id');
        return [
            'id' => $row->id,
            'total_sell_return' => $row->total_sell_return,
            'sell_return_paid' => $row->sell_return_paid,
            'should_notify' => $row->should_notify,
            'is_default' => $row->is_default,
            'type' => $row->type,
            'active' => $row->active,
            'total_due' => $this->contactUtil->getCustomerBalance($row->id,$business_id,true)
        ];
    }
    public function get_outstanding(){

                if (!auth()->user()->can('customer.view')) {
                    abort(403, 'Unauthorized action.');
                }

                $type = request()->get('type');

                $business_id = request()->session()->get('user.business_id');

                if($type == 'customer'){
                    $query = Contact::leftjoin('transactions AS t', 'contacts.id', '=', 't.contact_id')
                    ->leftjoin('contact_groups AS cg', 'contacts.customer_group_id', '=', 'cg.id')
                    ->leftjoin('user_contact_access AS uca','contacts.id','uca.contact_id')
                    ->leftjoin('users','uca.user_id','users.id')
                    ->where('contacts.business_id', $business_id)
                //            ->where('contacts.is_property', 0)
                            ->whereIn('contacts.is_property', [1,0])
                            ->select([
                                'contacts.contact_id', 'contacts.name', 'contacts.created_at', 'contacts.active','cg.name as customer_group', 'mobile', 'contacts.id', 'is_default','uca.user_id',
                                DB::raw("CONCAT(COALESCE(users.surname, ''),' ',COALESCE(users.first_name, ''),' ',COALESCE(users.last_name,'')) as full_name"),
                                DB::raw("SUM(IF(t.type = 'sell_return', final_total, 0)) as total_sell_return"),
                                DB::raw("SUM(IF(t.type = 'sell_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id AND transaction_payments.deleted_at IS NULL), 0)) as sell_return_paid"),
                                'contacts.pay_term_type', 'contacts.credit_limit',  'contacts.type',
                                DB::raw("(select sum( if(contact_ledgers.type = 'debit' AND transactions.type = 'opening_balance' AND transactions.business_id=".$business_id.",contact_ledgers.amount,0) 
                                    + if(contact_ledgers.type = 'debit' AND transactions.type != 'opening_balance' and transactions.business_id=".$business_id.", contact_ledgers.amount,0)
                                    - if(contact_ledgers.type = 'credit' ,contact_ledgers.amount,0) )
                                    from contact_ledgers
                                    left join transactions on contact_ledgers.transaction_id=transactions.id
                                    where contact_ledgers.contact_id=contacts.id  
                                    GROUP BY contact_ledgers.contact_id) as due")
                            ])
                            ->groupBy('contacts.id');
                    $query->where(function($q) {
                            $q->where('contacts.type', 'customer')
                            ->orWhere('contacts.type', 'both');
                    });

                }else{
                    $query = Contact::leftjoin('transactions AS t', 'contacts.id', '=', 't.contact_id')
                    ->leftjoin('contact_groups AS cg', 'contacts.supplier_group_id', '=', 'cg.id')
                    ->leftjoin('user_contact_access AS uca','contacts.id','uca.contact_id')
                    ->leftjoin('users','uca.user_id','users.id')
                    ->where('contacts.business_id', $business_id)
                    ->where('contacts.is_payee', 0)
                    ->where(function($q) {
                        $q->where('contacts.type', 'supplier')
                          ->orWhere('contacts.type', 'both');
                    })
                    // ->where('contacts.is_property', 0)
                    //->onlySuppliers()
                    ->select([
                        'contacts.contact_id', 'supplier_business_name', 'contacts.active', 'contacts.name', 'cg.name as supplier_group', 'contacts.created_at', 'contacts.mobile', 't.transaction_date',
                        'contacts.type', 'contacts.id', 'uca.user_id',
                        DB::raw("CONCAT(COALESCE(users.surname, ''),' ',COALESCE(users.first_name, ''),' ',COALESCE(users.last_name,'')) as full_name"),
                        DB::raw("SUM(IF(t.type = 'purchase', final_total, 0)) as total_purchase"),
                        DB::raw("SUM(IF(t.type = 'purchase', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id AND transaction_payments.deleted_at IS NULL), 0)) as purchase_paid"),
                        DB::raw("SUM(IF(t.type = 'purchase', final_total, 0) - IF(t.type = 'purchase', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id AND transaction_payments.deleted_at IS NULL), 0)) as due"),
                        DB::raw("SUM(IF(t.type = 'purchase_return', final_total, 0)) as total_purchase_return"),
                        DB::raw("SUM(IF(t.type = 'purchase_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id AND transaction_payments.deleted_at IS NULL), 0)) as purchase_return_paid"),
                        DB::raw("SUM(IF(t.type = 'purchase_return', final_total, 0) - IF(t.type = 'purchase_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id AND transaction_payments.deleted_at IS NULL), 0)) as return_due"),
                        DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                        DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id AND transaction_payments.deleted_at IS NULL), 0)) as opening_balance_paid"),
                        'contacts.email', 'contacts.tax_number', 'contacts.pay_term_number', 'contacts.pay_term_type', 'contacts.custom_field1', 'contacts.custom_field2', 'contacts.custom_field3', 'contacts.custom_field4'
                    ])
                    ->groupBy('contacts.id');
                }

                $final_sql = DB::table( DB::raw("({$query->toSql()}) as sub") )
                ->mergeBindings($query->getQuery());

                $dues = 0;
                $overpayments = 0;
                foreach($query->get()->toArray() as $one){

                    if($one['due'] > 0){
                        $dues += $one['due'];
                    }else{
                        $overpayments += $one['due'];
                    }
                    if($type == 'supplier'){
                        if($one['opening_balance'] > 0){
                            $dues += $one['opening_balance'];
                        }else{
                            $overpayments += $one['opening_balance'];
                        }
                    }

                }


                return response([
                    'total_outstanding' => $dues,
                    'total_overpayment' => $overpayments
                ]);
            }

    public function get_cus_due_bal($contact_id, $todayOnly = true)
    {
        
        $business_id = request()->session()->get('user.business_id');
        $contact = Contact::findOrFail($contact_id);

        if($contact->type == "customer"){
           $due = $this->contactUtil->getCustomerBalance($contact->id,$business_id,true);
        }else{
           $due = $this->contactUtil->getSupplierBalance($contact->id,$business_id,true);
        }
        
        return $due;


    }
    public function get_due_bal($contact_id, $todayOnly = true)
    {
        
        $business_id = request()->session()->get('user.business_id');
        $contact = Contact::find($contact_id);
        
        if($contact->type == "customer"){
           $due = $this->contactUtil->getCustomerBalance($contact->id,$business_id,true);
        }else{
           $due = $this->contactUtil->getSupplierBalance($contact->id,$business_id,true);
        }
        
        return $due;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }
        $mode = request()->mode;
        $type = request()->type;
        $module = request()->module ?? 'other';
        //add by sakhawat
        $business_id = request()->session()->get('user.business_id');
        $businessLocations = BusinessLocation::whereBusinessId($business_id)->pluck('name', 'id');
        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        }
        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        //set field value by supplier sakhawat
        if (auth()->user()->can('customer.create') && $type != 'supplier') {
            $types['customer'] = __('report.customer');
        }
        //set field value by supplier sakhawat
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')  && $type != 'supplier') {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }
        $customer_groups = ContactGroup::forDropdown($business_id);
        $supplier_groups = ContactGroup::forDropdown($business_id, true, false, 'supplier');
        $contact_id = $this->businessUtil->check_customer_code($business_id);
        $user_groups = User::forDropdown($business_id);

        if($type == 'customer'){
            $notifications = NotificationTemplate::customerNotifications();
        }else{
            $notifications = NotificationTemplate::supplierNotifications();
        }
        
        $customers = Contact::customersDropdown($business_id, false);
          $customerSettings = AirlineFormSettingCustomer::where('business_id', $business_id)->first();
          
        return view('contact.create')
            ->with(compact('notifications','types','customers', 'customer_groups', 'supplier_groups', 'contact_id', 'type','user_groups', 'mode','businessLocations','module', 'customerSettings'));
    }


    /**
     * Customer Form feild according to locations
     *
     * 
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getCustomerForm(Request $request)
    {
        $contact_fields = !empty(session('business.contact_fields')) ? session('business.contact_fields') : [];
        if(!empty($contact_fields) && ($contact_fields['location'] == $request->id || $contact_fields['location'] == 0))
       {
           //donothing
       }
       else
       {
            $contact_fields = defaultCustomerForm();
       }
        return response([
            'data' => $contact_fields,
         ]);
        
    }
    
     /**
     * Show the form for creating a new resource to supplier product mapping.
     *
     * @return \Illuminate\Http\Response
     */
    public function createMapping()
    {
          if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }
       
        $mode = request()->mode;
        $type = request()->type;
        $business_id = request()->session()->get('user.business_id');
        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        }
        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }
        $customer_groups = ContactGroup::forDropdown($business_id);
        $supplier_groups = ContactGroup::forDropdown($business_id, true, false, 'supplier');
        $contact_id = $this->businessUtil->check_customer_code($business_id);
        $user_groups = User::forDropdown($business_id);
        $name = Contact::where('business_id', $business_id)->where('type', 'supplier')->pluck('name', 'id');
       $names = Product::where('business_id', $business_id)->pluck('name', 'id');
       
       //$mappings=SupplierProductMapping::all();
       $mappings=SupplierProductMapping::leftjoin('products', 'products.id', 'supplier_product_mappings.product_id')
                                   ->leftjoin('contacts', 'contacts.id', 'supplier_product_mappings.supplier_id')
                                   -> select('products.name AS productname' ,'contacts.name AS suppliername')->get();
                          
        if($type == 'customer'){
            $notifications = NotificationTemplate::customerNotifications();
        }else{
            $notifications = NotificationTemplate::supplierNotifications();
        }
        return view('contact.create_mapping')
            ->with(compact('notifications','types', 'customer_groups', 'supplier_groups', 'name','names','mappings','contact_id', 'type','user_groups', 'mode'));
    }
    
     /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create_customer()
    {
        if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }
        $type = request()->type;
        $business_id = request()->session()->get('user.business_id');
        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        }
        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }
        $customer_groups = ContactGroup::forDropdown($business_id);
        $supplier_groups = ContactGroup::forDropdown($business_id, true, false, 'supplier');
        $contact_id = $this->businessUtil->check_customer_code($business_id);
        $user_groups = User::forDropdown($business_id);
       
        return view('airline::create_invoice.create_customer')
            ->with(compact('types', 'customer_groups', 'supplier_groups', 'contact_id', 'type','user_groups'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dump($request->all());exit;
        if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }
        try {

            $input['property_id']=$request->property_id;
            $business_id = $request->session()->get('user.business_id');
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse();
            }
            DB::beginTransaction();
            if ($request->type != 'supplier') {
                if (!$this->moduleUtil->isQuotaAvailable('customers', $business_id)) {
                    return $this->moduleUtil->quotaExpiredResponse('customers', $business_id, action('ContactController@index'));
                }
                if (!empty($request->password)) {
                    if(isset($request->image) || isset($request->signature)){
                        $validator = Validator::make($request->all(), [
                            'password' => 'required|min:4|max:255',
                            'confirm_password' => 'required|same:password',
                            // 'image' => 'required|mimes:jpeg,png,jpg,gif,svg|max:2048',
                            // 'signature' => 'required|mimes:jpeg,png,jpg,gif,svg|max:2048',
                        ]);
                    }else{
                        $validator = Validator::make($request->all(), [
                            'password' => 'required|min:4|max:255',
                            'confirm_password' => 'required|same:password',
                        ]);
                    }
                    if ($validator->fails()) {
                        $output = [
                            'success' => false,
                            'msg' => 'Password does not match'
                        ];
                    }
                    return redirect()->back()->with('status', $output);
                }
                 //point 4b done updated by dushyant
                $customer_data = array(
                    'business_id' => $business_id,
                    'first_name' => $request->name,
                    'last_name' => '',
                    'email' => $request->email,
                    'username' => (is_null($request->contact_id))?$request->name:$request->contact_id,
                    'password' => Hash::make($request->password),
                    'mobile' => $request->mobile ?? ' ',
                    'contact_number' => $request->alternate_number,
                    'landline' => $request->landline,
                    'geo_location' => $request->country ?? ' ',
                    'address' => $request->address ?? ' ',
                    'town' => $request->state ?? ' ',
                    'district' => $request->city ?? ' ',
                    'is_company_customer' => 1
                );
                $userData=Customer::create($customer_data);
            }
             //point 4b done updated by dushyant
             //add whatsapp_number field sakhawat
            $input = $request->only(['address_2','address_3','sub_customer','sub_customers','vat_number','credit_notification','transaction_date',
                'should_notify','type', 'supplier_business_name','nic_number','manual_bill_settlement',
                'name', 'pay_term_number', 'pay_term_type', 'mobile', 'landline', 'alternate_number', 'city', 'address', 'state', 'country', 'landmark', 'customer_group_id', 'supplier_group_id', 'contact_id', 'email','whatsapp_number'
            ]);
            
            
            $input['sub_customers'] = json_encode($request->sub_customers ?? []);
            
            if(isset($input['transaction_date'])){
            $input['contact_transaction_date'] = @$this->transactionUtil->uf_date($input['transaction_date']);
                unset($input['transaction_date']);
            }
            
            $input['business_id'] = $business_id;
            $input['created_by'] = $request->session()->get('user.id');
            $input['credit_limit'] = $request->input('credit_limit') != '' ? $this->commonUtil->num_uf($request->input('credit_limit')) : null;
            if ($request->transaction_date && $request->type == 'supplier') {
                $input['created_at'] = date('Y-m-d H:i:s', strtotime($request->transaction_date));
            }
            //Check Contact id
            $count = 0;
            if (!empty($input['contact_id'])) {
                $count = Contact::where('business_id', $input['business_id'])
                    ->where('contact_id', $input['contact_id'])
                    ->count();
            }
            if ($count == 0) {
                //Update reference count
                $ref_count = $this->commonUtil->setAndGetReferenceCount('contacts');
                if (empty($input['contact_id'])) {
                    //Generate reference number
                    $input['contact_id'] = $this->commonUtil->generateReferenceNumber('contacts', $ref_count);
                }

                $input['is_property']=$request->property_id_value;
                $imageName = null;
                $signatureName = null;

                if($request->hasFile('image')){
                    $imageName = Media::uploadFile($request->file('image'));
                    $input['image']=$imageName;
                }if($request->hasFile('signature')){
                    $signatureName = Media::uploadFile($request->file('signature'));
                    $input['signature']=$signatureName;
                }
                //add by sakhawat 
                $input['register_module'] = $request->module ?? 'other';
                $contact = Contact::create($input);

                $data=new new_vehicle();
                $data->customer_id=$contact->id;
                $data->vehicle_no=$request->vehicle_no;
                $data->save();

                $notification_parameters = json_decode($request->notification_parameters);
                $contact->notification_contacts = json_encode($notification_parameters);
                $contact->save();

                //Assign User to Contact
                if( $request->assigned_to){
                    $user_contact_access = new UserContactAccess();
                    $user_contact_access->contact_id = $contact->id;
                    $user_contact_access->user_id = $request->assigned_to;
                    $user_contact_access->save();
                }
                //Add opening balance
                if (!empty($request->input('opening_balance'))) {
                    $this->transactionUtil->createOpeningBalanceTransaction($business_id, $contact->id, $request->input('opening_balance'), $request->transaction_date);
                }
                $output = [
                    'success' => true,
                    'data' => $contact,
                    'msg' => __("contact.added_success")
                ];
            } else {
                throw new \Exception("Error Processing Request", 1);
            }
            DB::commit();
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong"),
                'error' => $e->getMessage()
            ];
        }
        if(isset(request()->is_modal)){
            return $output;
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
        if (!auth()->user()->can('supplier.view') && !auth()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        
        $contact = Contact::where('contacts.id', $id)
            ->where('contacts.business_id', $business_id)
            ->leftJoin('transactions AS t', 'contacts.id', '=', 't.contact_id')
            ->with(['business'])
            ->select(
                DB::raw("SUM(IF(t.type = 'purchase', final_total, 0)) as total_purchase"),
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', final_total, 0)) as total_invoice"),
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final' AND t.payment_status != 'paid', final_total, 0)) as total_sale_due"),
                DB::raw("SUM(IF(t.type = 'purchase', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id AND transaction_payments.deleted_at IS NULL), 0)) as purchase_paid"),
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id AND transaction_payments.deleted_at IS NULL), 0)) as invoice_received"),
                DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id AND transaction_payments.deleted_at IS NULL), 0)) as opening_balance_paid"),
                'contacts.*'
            )->first();


        $reward_enabled = (request()->session()->get('business.enable_rp') == 1 && in_array($contact->type, ['customer', 'both'])) ? true : false;
        $contact_dropdown = Contact::contactDropdown($business_id, false, false, true, request()->type);
        $references = CustomerReference::where('business_id', $business_id)->where('contact_id', $id)->pluck('reference', 'reference');
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        $account_types = AccountType::where('business_id', $business_id)->whereNull('parent_account_type_id')->pluck('name','id');
        $discount_accounts = Account::whereNull('deleted_at')
                        ->where('disabled',0)
                        ->pluck('name','id');
        //get contact view type : ledger, notes etc.
        $view_type = request()->get('view');
        if (is_null($view_type)) {
            $view_type = 'contact_info';
        }
        
        $products = $q = Product::active()
                ->where('business_id', $business_id)->pluck('name','id');
                
        
        $due_invoices = Transaction::where('business_id',$business_id)->whereIn('type',['property_sell','route_operation','expense','sell','purchase'])->where('contact_id',$id)->whereIn('payment_status',['partial','due'])->pluck('invoice_no','id');
        
        $due_invoice_amts = Transaction::where('business_id',$business_id)->whereIn('type',['property_sell','route_operation','expense','sell','purchase'])->where('contact_id',$id)->whereIn('payment_status',['partial','due'])->pluck('final_total','id');
        
        $last_discount = Transaction::where('business_id',$business_id)->where('type','ledger_discount')->get()->last();
        if(!empty($last_discount) &&!empty($last_discount->invoice_no)){
            $discount_no = str_pad((((int)$last_discount->invoice_no)+1), 4, '0', STR_PAD_LEFT);
        }else{
            $discount_no = "0001";
        }
        
        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name','id');
        
        $ledger_discount_subscribed = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'ledger_discount');
        
        
        $transaction_amounts = ContactLedger::where('contact_id', $id)->distinct('amount')->pluck('amount');
        return view('contact.show')
            ->with(compact('ledger_discount_subscribed','due_invoice_amts','business_locations','due_invoices','discount_no','contact','products', 'reward_enabled', 'contact_dropdown', 'business_locations', 'view_type', 'references', 'transaction_amounts','account_types','discount_accounts'));
    }
    
     public function balanceDetails($id)
    {
        if (!auth()->user()->can('supplier.view') && !auth()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $contact = Contact::findOrFail($id);
        
        if($contact->type == 'supplier'){
            $balance_details = $this->contactUtil->getSupplierBalance($id,$business_id);
            
            return view('contact.balanceDetailsSupplier')
                ->with(compact('balance_details'));
        }elseif($contact->type == 'customer'){
            $balance_details = $this->contactUtil->getCustomerBalance($id,$business_id);
            
            return view('contact.balanceDetails')
                ->with(compact('balance_details'));
        }
        
       
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('supplier.update') && !auth()->user()->can('customer.update')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $contact = Contact::leftjoin('user_contact_access','contacts.id','user_contact_access.contact_id')
            ->leftjoin('users','user_contact_access.user_id','users.id')
            ->select([
                    'contacts.*',
                    'user_contact_access.user_id'
                    ])
            ->where('contacts.business_id', $business_id)->find($id);
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse();
            }
            $types = [];
            if (auth()->user()->can('supplier.create')) {
                $types['supplier'] = __('report.supplier');
            }
            if (auth()->user()->can('customer.create')) {
                $types['customer'] = __('report.customer');
            }
            if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
                $types['both'] = __('lang_v1.both_supplier_customer');
            }
            $customer_groups = ContactGroup::forDropdown($business_id);
            $supplier_groups = ContactGroup::forDropdown($business_id, true, false, 'supplier');
            $ob_transaction =  Transaction::where('contact_id', $id)
                ->where('type', 'opening_balance')
                ->first();
            $opening_balance = !empty($ob_transaction->final_total) ? $ob_transaction->final_total : 0;
            //Deduct paid amount from opening balance.
            if (!empty($opening_balance)) {
                $opening_balance_paid = $this->transactionUtil->getTotalAmountPaid($ob_transaction->id);
                if (!empty($opening_balance_paid)) {
                    $opening_balance = $opening_balance - $opening_balance_paid;
                }
                $opening_balance = $this->commonUtil->num_f($ob_transaction->final_total);
            }

            if($contact->type == 'customer'){
                $notifications = NotificationTemplate::customerNotifications();
            }else{
                $notifications = NotificationTemplate::supplierNotifications();
            }
            
            $customers = Contact::customersDropdown($business_id, false);

            $user_groups = User::forDropdown($business_id);
            $contact_id = $this->businessUtil->check_customer_code($business_id);
             $customerSettings = AirlineFormSettingCustomer::where('business_id', $business_id)->first();
            return view('contact.edit')
                ->with(compact('notifications','contact','customers', 'types', 'customer_groups', 'supplier_groups', 'opening_balance', 'ob_transaction','user_groups', 'contact_id', 'customerSettings'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateold(Request $request, $id)
    {
    //  dd('test');   
        if (!auth()->user()->can('supplier.update') && !auth()->user()->can('customer.update')) {
              
            abort(403, 'Unauthorized action.');
        }
        if(request()->ajax()) {
            try {                                                                              // removed below line
            //   dd('test try');
                $input = $request->only(['address_2','address_3','sub_customer','sub_customers','vat_number','address','credit_notification','transaction_date','should_notify','contact_id','nic_number', 'type', 'supplier_business_name', 'name', 'tax_number', 'pay_term_number', 'pay_term_type', 'mobile', 'landline', 'alternate_number', 'city', 'state', 'country', 'landmark', 'customer_group_id', 'supplier_group_id', 'custom_field1', 'custom_field2', 'custom_field3', 'custom_field4', 'email']);
                $input['contact_transaction_date'] = $this->transactionUtil->uf_date($input['transaction_date']);
                unset($input['transaction_date']);
            
                $input['sub_customers'] = json_encode($request->sub_customers ?? []);
                $input['credit_limit'] = $request->input('credit_limit') != '' ? $this->commonUtil->num_uf($request->input('credit_limit')) : null;
                $business_id = $request->session()->get('user.business_id');
                if (!$this->moduleUtil->isSubscribed($business_id)) {
                    return $this->moduleUtil->expiredResponse();
                }
                $contact_user = User::where('username', $input['contact_id'])->first();
                if (request()->type == 'customer') {

                    if($request->hasFile('image')){
                        $imageName = Media::uploadFile($request->file('image'));
                        $input['image']=$imageName;
                    }if($request->hasFile('signature')){
                        $signatureName = Media::uploadFile($request->file('signature'));
                        $input['signature']=$signatureName;
                    }

                    if (!empty(request()->password)) {
                        $validator = Validator::make(request()->all(), [
                            'password' => 'required|min:4|max:255',
                            'confirm_password' => 'required|same:password'
                        ]);
                        if ($validator->fails()) {
                            $output = [
                                'success' => false,
                                'msg' => 'Password does not match'
                            ];
                            return $output;
                        }
                    }
                    if (empty($contact_user)) {
                        if (!$this->moduleUtil->isQuotaAvailable('customers', $business_id)) {
                            return $this->moduleUtil->quotaExpiredResponse('customers', $business_id, action('ContactController@index'));
                        }
                        // it is company customer
                        $customer_details = request()->only(['email', 'password']);
                        $customer_details['language'] = env('APP_LOCALE');
                        $customer_details['surname'] = '';
                        $customer_details['first_name'] = request()->name;
                        $customer_details['last_name'] = '';
                        $customer_details['username'] = request()->contact_id;
                        $customer_details['is_customer'] = 1;
                        $customer_details['business_id'] = $business_id;
                        if (!empty(request()->password)) {
                            $customer_details['password'] = Hash::make(request()->password);
                        }
                        $user = User::create_user($customer_details);
                        $user->business_id = $business_id;
                        $user->is_customer = 1;
                        $enable_customer_login = System::getProperty('enable_customer_login');
                        if (!$enable_customer_login) {
                            $user->status = 'inactive';
                        }
                        $user->save();
                    } else {
                        $contact_user->first_name = request()->name;
                        if (!empty(request()->password)) {
                            $contact_user->password = Hash::make(request()->password);
                        }
                        $contact_user->save();
                    }
                }
                $count = 0;
                //Check Contact id
                if (!empty($input['contact_id'])) {
                    $count = Contact::where('business_id', $business_id)
                        ->where('contact_id', $input['contact_id'])
                        ->where('id', '!=', $id)
                        ->count();
                }
                if ($count == 0) {
                    $contact = Contact::where('business_id', $business_id)->findOrFail($id);
                    foreach ($input as $key => $value) {
                        $contact->$key = $value;
                    }
                    $contact->save();
                    //update data of user access table
                    if( $request->assigned_to ){
                        $user_contact_access = UserContactAccess::updateOrCreate(
                                    ['contact_id' => $id],
                                    ['user_id' => $request->assigned_to, 'contact_id' => $id]
                                );

                    }else{

                        UserContactAccess::where('contact_id',$id)->delete();
                    }
                    //Get opening balance if exists
                    $ob_transaction =  Transaction::where('contact_id', $id)
                        ->where('type', 'opening_balance')
                        ->first();
                    if (!empty($ob_transaction)) {
                        $amount = $this->commonUtil->num_uf($request->input('opening_balance'));
                        // $opening_balance_paid = $this->transactionUtil->getTotalAmountPaid($ob_transaction->id);
                        // if (!empty($opening_balance_paid)) {
                        //     $amount -= $opening_balance_paid;
                        // }
                        $ob_transaction->final_total = $amount;
                        $ob_transaction->total_before_tax = $amount;
                        $ob_transaction->transaction_date = $this->transactionUtil->uf_date($request->transaction_date);
                        $ob_transaction->save();
                        $payable_account_id = $this->transactionUtil->account_exist_return_id('Accounts Payable');
                        $receivealbe_account_id = $this->transactionUtil->account_exist_return_id('Accounts Receivable');
                        $account_transaction = AccountTransaction::where('transaction_id', $ob_transaction->id)->whereIn('account_id', [$payable_account_id, $receivealbe_account_id])->first();
                        
                        if(!empty($account_transaction)){
                            $account_transaction->amount = $ob_transaction->final_total;
                            $account_transaction->save();
                        }
                        
                        $contact_ledger_trnsaction = ContactLedger::where('transaction_id', $ob_transaction->id)->first();
                        $contact_ledger_trnsaction->amount = $ob_transaction->final_total;
                        $contact_ledger_trnsaction->save();
                        //Update opening balance payment status
                        $this->transactionUtil->updatePaymentStatus($ob_transaction->id, $ob_transaction->final_total);
                    } else {
                        //Add opening balance
                        if (!empty($request->input('opening_balance'))) {
                            $this->transactionUtil->createOpeningBalanceTransaction($business_id, $contact->id, $request->input('opening_balance'));
                        }
                    }

                    $notification_parameters = json_decode($request->notification_parameters);
                    $contact->notification_contacts = json_encode($notification_parameters);
                    $contact->save();

                    $output = [
                        'success' => true,
                        'msg' => __("contact.updated_success")
                    ];
                } else {
                    throw new \Exception("Error Processing Request", 1);
                }
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }
            // return $output;
            // dd('testf');
            return redirect()->back()->with('status', $output);
        }
    }
    
    public function update(Request $request, $id)
{
    if (!auth()->user()->can('supplier.update') && !auth()->user()->can('customer.update')) {
        abort(403, 'Unauthorized action.');
    } 
    // dump('ajax');
    // if(request()->ajax()) {
        // dd('ajax');
        try {
            // $input = $request->only(['address_2','address_3','sub_customer','sub_customers','vat_number','address','credit_notification','transaction_date','should_notify','contact_id','nic_number', 'type', 'supplier_business_name', 'name', 'tax_number', 'pay_term_number', 'pay_term_type', 'mobile', 'landline', 'alternate_number', 'city', 'state', 'country', 'landmark', 'customer_group_id', 'supplier_group_id', 'custom_field1', 'custom_field2', 'custom_field3', 'custom_field4', 'email', 'credit_limit', 'opening_balance', 'assigned_to', 'notification_parameters', 'password', 'confirm_password', 'image', 'signature']); // Include all fillable fields
                            $input = $request->only(['address_2','manual_bill_settlement','address_3','sub_customer','sub_customers','vat_number','address','credit_notification','transaction_date','should_notify','contact_id','nic_number', 'type', 'supplier_business_name', 'name', 'tax_number', 'pay_term_number', 'pay_term_type', 'mobile', 'landline', 'alternate_number', 'city', 'state', 'country', 'landmark', 'customer_group_id', 'supplier_group_id', 'custom_field1', 'custom_field2', 'custom_field3', 'custom_field4', 'email']);
            $input['contact_transaction_date'] = $this->transactionUtil->uf_date($input['transaction_date']);
            unset($input['transaction_date']);

            $input['sub_customers'] = json_encode($request->sub_customers ?? []);
            $input['credit_limit'] = $request->input('credit_limit') != '' ? $this->commonUtil->num_uf($request->input('credit_limit')) : null;
            $business_id = $request->session()->get('user.business_id');
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse();
            }
            $contact_user = User::where('username', $input['contact_id'])->first();
            if (request()->type == 'customer') {
                if($request->hasFile('image')){
                    $imageName = Media::uploadFile($request->file('image'));
                    $input['image']=$imageName;
                }if($request->hasFile('signature')){
                    $signatureName = Media::uploadFile($request->file('signature'));
                    $input['signature']=$signatureName;
                }

                if (!empty(request()->password)) {
                    $validator = Validator::make(request()->all(), [
                        'password' => 'required|min:4|max:255',
                        'confirm_password' => 'required|same:password'
                    ]);
                    if ($validator->fails()) {
                        $output = [
                            'success' => false,
                            'msg' => 'Password does not match'
                        ];
                        return $output;
                    }
                }
                if (empty($contact_user)) {
                    if (!$this->moduleUtil->isQuotaAvailable('customers', $business_id)) {
                        return $this->moduleUtil->quotaExpiredResponse('customers', $business_id, action('ContactController@index'));
                    }
                    $customer_details = request()->only(['email', 'password']);
                    $customer_details['language'] = env('APP_LOCALE');
                    $customer_details['surname'] = '';
                    $customer_details['first_name'] = request()->name;
                    $customer_details['last_name'] = '';
                    $customer_details['username'] = request()->contact_id;
                    $customer_details['is_customer'] = 1;
                    $customer_details['business_id'] = $business_id;
                    if (!empty(request()->password)) {
                        $customer_details['password'] = Hash::make(request()->password);
                    }
                    $user = User::create_user($customer_details);
                    $user->business_id = $business_id;
                    $user->is_customer = 1;
                    $enable_customer_login = System::getProperty('enable_customer_login');
                    if (!$enable_customer_login) {
                        $user->status = 'inactive';
                    }
                    $user->save();
                } else {
                    $contact_user->first_name = request()->name;
                    if (!empty(request()->password)) {
                        $contact_user->password = Hash::make(request()->password);
                    }
                    $contact_user->save();
                }
            }
            $count = 0;
            // dump($count,'testing');
            //Check Contact id
            if (!empty($input['contact_id'])) {
                $count = Contact::where('business_id', $business_id)
                    ->where('contact_id', $input['contact_id'])
                    ->where('id', '!=', $id)
                    ->count();
            }
            
            // dump($count,'testing 00');
            if ($count == 0) {
                $contact = Contact::where('business_id', $business_id)->findOrFail($id);
                $originalContact = $contact->toArray(); // Get original data before update

                foreach ($input as $key => $value) {
                    $contact->$key = $value;
                }
                
                $contact->save();
                
                // dd($contact,$originalContact);
                $updatedContact = $contact->toArray(); // Get updated data

                // Log the changes
                $changes = array_diff($updatedContact, $originalContact);
                // dd($changes,'test change');
                if (!empty($changes)) {
                    $changed_msg = "Contact #" . $contact->name . " has been updated by " . auth()->user()->username . ". Changes: " . json_encode($changes);

                    $activity = new Activity();
                    $activity->log_name = "Contact Update";
                    $activity->description = "update";
                    $activity->subject_id = $id;
                    $activity->subject_type = "App\Contact";
                    $activity->causer_id = auth()->user()->id;
                    $activity->causer_type = 'App\User';
                    $activity->properties = $changed_msg;
                    $activity->created_at = now();
                    $activity->updated_at = now();
                    $activity->save();
                }

                //update data of user access table
                if( $request->assigned_to ){
                    UserContactAccess::updateOrCreate(
                        ['contact_id' => $id],
                        ['user_id' => $request->assigned_to, 'contact_id' => $id]
                    );
                } else{
                    UserContactAccess::where('contact_id',$id)->delete();
                }
                //Get opening balance if exists
                $ob_transaction =  Transaction::where('contact_id', $id)
                    ->where('type', 'opening_balance')
                    ->first();
                if (!empty($ob_transaction)) {
                    $amount = $this->commonUtil->num_uf($request->input('opening_balance'));
                    $ob_transaction->final_total = $amount;
                    $ob_transaction->total_before_tax = $amount;
                    $ob_transaction->transaction_date = $this->transactionUtil->uf_date($request->transaction_date);
                    $ob_transaction->save();
                    $payable_account_id = $this->transactionUtil->account_exist_return_id('Accounts Payable');
                    $receivealbe_account_id = $this->transactionUtil->account_exist_return_id('Accounts Receivable');
                    $account_transaction = AccountTransaction::where('transaction_id', $ob_transaction->id)->whereIn('account_id', [$payable_account_id, $receivealbe_account_id])->first();

                    if(!empty($account_transaction)){
                        $account_transaction->amount = $ob_transaction->final_total;
                        $account_transaction->save();
                    }

                    $contact_ledger_trnsaction = ContactLedger::where('transaction_id', $ob_transaction->id)->first();
                    $contact_ledger_trnsaction->amount = $ob_transaction->final_total;
                    $contact_ledger_trnsaction->save();
                    //Update opening balance payment status
                    $this->transactionUtil->updatePaymentStatus($ob_transaction->id, $ob_transaction->final_total);
                } else {
                    //Add opening balance
                    if (!empty($request->input('opening_balance'))) {
                        $this->transactionUtil->createOpeningBalanceTransaction($business_id, $contact->id, $request->input('opening_balance'));
                    }
                }

                $notification_parameters = json_decode($request->notification_parameters);
                $contact->notification_contacts = json_encode($notification_parameters);
                $contact->save();

                $output = [
                    'success' => true,
                    'msg' => __("contact.updated_success")
                ];
            } else {
                throw new \Exception("Error Processing Request", 1);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }
        if(isset($request->is_modal) && $request->is_modal == 1){
            return $output;
        }else{
            return redirect()->back()->with('status', $output);
        }
    // }
    dd('ajax not working');
}
    /** 
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyold($id)
    {
        if (!auth()->user()->can('supplier.delete') && !auth()->user()->can('customer.delete')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            try {
                $business_id = request()->user()->business_id;
                //Check if any transaction related to this contact exists
                $count = Transaction::where('business_id', $business_id)
                    ->where('contact_id', $id)->where('final_total', '>', 0)
                    ->count();
                if ($count == 0) {
                    $contact = Contact::where('business_id', $business_id)->findOrFail($id);
                    $transactions = Transaction::where('business_id', $business_id)
                        ->where('contact_id', $id)->get();
                    foreach ($transactions as $transaction) {
                        AccountTransaction::where('transaction_id', $transaction->id)->forcedelete();
                        $transaction->delete();
                    }
                    if (!$contact->is_default) {
                        $contact->delete();
                    }
                    $output = [
                        'success' => true,
                        'msg' => __("contact.deleted_success")
                    ];
                } else {
                    $output = [
                        'success' => false,
                        'msg' => __("lang_v1.you_cannot_delete_this_contact")
                    ];
                }
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }
            return $output;
        }
    }
    
    public function destroy($id)
    {
        if (!auth()->user()->can('supplier.delete') && !auth()->user()->can('customer.delete')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            try {
                $business_id = request()->user()->business_id;
                $user_id = auth()->user()->id;
                //Check if any transaction related to this contact exists
                $count = Transaction::where('business_id', $business_id)
                    ->where('contact_id', $id)->where('final_total', '>', 0)
                    ->count();
                    // dump($count);
                if ($count >= 0) {
                                    // if ($count == 0) {
                    // dd('testing 00');
                    $contact = Contact::where('business_id', $business_id)->findOrFail($id);
                    $transactions = Transaction::where('business_id', $business_id)
                        ->where('contact_id', $id)->get();
                        // dd($transactions,$contact);
                    if($transactions){
                        foreach ($transactions as $transaction) {
                            AccountTransaction::where('transaction_id', $transaction->id)->forcedelete();
                            // $transaction->delete();
                            $transaction->deleted_at = now();
                            $transaction->deleted_by = $user_id;
                            $transaction->save();
                            $changed_msg = "Contact #".$contact->name."Transaction has been deleted by ".auth()->user()->username;
                    
                            // dump($changed_msg,'testing start');
                            $activity = new Activity();
                            $activity->log_name = "Contact Payment";
                            $activity->description = "delete";
                            $activity->subject_id = $id;
                            $activity->subject_type = "";
                            $activity->causer_id = auth()->user()->id;
                            $activity->causer_type = 'App\AccountTransaction';
                            $activity->properties = $changed_msg ;
                            $activity->created_at = date('Y-m-d H:i');
                            $activity->updated_at = date('Y-m-d H:i');
                            
                            // Save the activity
                            $activity->save();
                            // dump($activity,'testing done');
                        }
                    }
                    
                    if (!$contact->is_default) {
                        // $contact->delete();
                        $contact->deleted_at = now();
                        $contact->save();
                         $changed_msg = "Contact #".$contact->name." has been deleted by ".auth()->user()->username;
                
                        // dump($changed_msg,'testing start');
                        $activity = new Activity();
                        $activity->log_name = "Contact";
                        $activity->description = "delete";
                        $activity->subject_id = $id;
                        $activity->subject_type = "";
                        $activity->causer_id = auth()->user()->id;
                        $activity->causer_type = 'App\Contact';
                        $activity->properties = $changed_msg ;
                        $activity->created_at = date('Y-m-d H:i');
                        $activity->updated_at = date('Y-m-d H:i');
                        
                        // Save the activity
                        $activity->save();
                    }
                    $output = [
                        'success' => true,
                        'msg' => __("contact.deleted_success")
                    ];
                } else {
                    $output = [
                        'success' => false,
                        'msg' => __("lang_v1.you_cannot_delete_this_contact")
                    ];
                }
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }
            return $output;
        }
    }
    /**
     * Mass deletes contact.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request)
    {
        
        if (!auth()->user()->can('product.delete')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $purchase_exist = false;
            if (!empty($request->input('selected_rows'))) {
                $business_id = $request->session()->get('user.business_id');
                $selected_rows = explode(',', $request->input('selected_rows'));
                $contacts = Contact::where('business_id', $business_id)
                    ->whereIn('id', $selected_rows)
                    ->get();
                DB::beginTransaction();
                $not_deleted_contact = []; // not deleted contact names
                foreach ($contacts  as $contact) {
                    $transactions = Transaction::where('contact_id', $contact->id)->whereIn('type', ['sell', 'purchase'])->where('deleted_at', null)->first();
                    if (!empty($transactions)) {
                        array_push($not_deleted_contact, $contact->name);
                    } else {
                        $contact->delete();
                    }
                }
                DB::commit();
            }
            if (empty($not_deleted_contact)) {
                $output = [
                    'success' => 1,
                    'msg' => __('lang_v1.deleted_success')
                ];
            } else {
                $not_deleted_contact_name =  implode(',', $not_deleted_contact);
                $output = [
                    'success' => 0,
                    'msg' => __('lang_v1.contacts') . ' ' . $not_deleted_contact_name . ' ' . __('lang_v1.contact_could_not_be_deleted')
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];
        }
    }
    /**
     * Retrieves list of customers, if filter is passed then filter it accordingly.
     *
     * @param  string  $q
     * @return JSON
     */
    public function getCustomers()
    {
        if (request()->ajax()) {
            $term = request()->input('q', '');
            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');
            $contacts = Contact::where('business_id', $business_id)->where('active', 1);
            $selected_contacts = User::isSelectedContacts($user_id);
            if ($selected_contacts) {
                $contacts->join('user_contact_access AS uca', 'contacts.id', 'uca.contact_id')
                    ->where('uca.user_id', $user_id);
            }
            if (!empty($term)) {
                $contacts->where(function ($query) use ($term) {
                    $query->where('name', 'like', '%' . $term . '%')
                        ->orWhere('supplier_business_name', 'like', '%' . $term . '%')
                        ->orWhere('mobile', 'like', '%' . $term . '%')
                        ->orWhere('contacts.contact_id', 'like', '%' . $term . '%');
                });
            }
            $contacts->select(
                'contacts.id',
                DB::raw("IF(contacts.contact_id IS NULL OR contacts.contact_id='', name, CONCAT(name, ' (', contacts.contact_id, ')')) AS text"),
                'mobile',
                'landmark',
                'city',
                'state',
                'pay_term_number',
                'pay_term_type'
            )
                ->onlyCustomers();
            if (request()->session()->get('business.enable_rp') == 1) {
                $contacts->addSelect('total_rp');
            }
            $contacts = $contacts->get();
            return json_encode($contacts);
        }
    }
    /**
     * Retrieves list of customers, if filter is passed then filter it accordingly.
     *
     * @param  string  $q
     * @return JSON
     */
    public function getSuppliers()
    {
        if (request()->ajax()) {
            $term = request()->input('q', '');
            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');
            $contacts = Contact::where('business_id', $business_id)->where('active', 1);
            $selected_contacts = User::isSelectedContacts($user_id);
            if ($selected_contacts) {
                $contacts->join('user_contact_access AS uca', 'contacts.id', 'uca.contact_id')
                    ->where('uca.user_id', $user_id);
            }
            if (!empty($term)) {
                $contacts->where(function ($query) use ($term) {
                    $query->where('name', 'like', '%' . $term . '%')
                        ->orWhere('supplier_business_name', 'like', '%' . $term . '%')
                        ->orWhere('mobile', 'like', '%' . $term . '%')
                        ->orWhere('contacts.contact_id', 'like', '%' . $term . '%');
                });
            }
            $contacts->select(
                'contacts.id',
                DB::raw("IF(contacts.contact_id IS NULL OR contacts.contact_id='', name, CONCAT(name, ' (', contacts.contact_id, ')')) AS text"),
                'mobile',
                'landmark',
                'city',
                'state',
                'pay_term_number',
                'pay_term_type'
            )
                ->onlySuppliers();
            if (request()->session()->get('business.enable_rp') == 1) {
                $contacts->addSelect('total_rp');
            }
            $contacts = $contacts->get();
            return json_encode($contacts);
        }
    }
    /**
     * Checks if the given contact id already exist for the current business.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkContactId(Request $request)
    {
        $contact_id = $request->input('contact_id');
        $valid = 'true';
        if (!empty($contact_id)) {
            $business_id = $request->session()->get('user.business_id');
            $hidden_id = $request->input('hidden_id');
            $query = Contact::where('business_id', $business_id)
                ->where('contact_id', $contact_id);
            if (!empty($hidden_id)) {
                $query->where('id', '!=', $hidden_id);
            }
            $count = $query->count();
            if ($count > 0) {
                $valid = 'false';
            }
        }
        echo $valid;
        exit;
    }
    /**
     * Shows import option for contacts
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */

    public function add_notification_numbers($id){
        $contact = Contact::findOrFail($id);
        if($contact->type == 'customer'){
            $notifications = NotificationTemplate::customerNotifications();
        }else{
            $notifications = NotificationTemplate::supplierNotifications();
        }


        return view('contact.partials.notification_numbers')
            ->with(compact('notifications', 'contact'));
    }

    public function save_notification_numbers(Request $request,$id){
        try {
            $input = $request->formadata;
            $contact = Contact::findOrFail($id);
            $contact->notification_contacts = json_encode($input);
            $contact->save();

            $output = [
                'success' => 1,
                'msg' => __("contact.updated_success")
            ];

            return $output;

        } catch (\Exception $e) {
            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")

            ];

            return $output;
        }

    }

    public function getImportContacts()
    {
        if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }
        $zip_loaded = extension_loaded('zip') ? true : false;
        //Check if zip extension it loaded or not.
        if ($zip_loaded === false) {
            $output = [
                'success' => 0,
                'msg' => 'Please install/enable PHP Zip archive for import'
            ];
            return view('contact.import')
                ->with('notification', $output);
        } else {
            return view('contact.import');
        }
    }
    /**
     * Imports contacts
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function postImportContacts(Request $request)
    {
        if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $notAllowed = $this->commonUtil->notAllowedInDemo();
            if (!empty($notAllowed)) {
                return $notAllowed;
            }
            //Set maximum php execution time
            ini_set('max_execution_time', 0);
            if ($request->hasFile('contacts_csv')) {
                $file = $request->file('contacts_csv');
                $parsed_array = Excel::toArray([], $file);
                //Remove header row
                $imported_data = array_splice($parsed_array[0], 1);
                $business_id = $request->session()->get('user.business_id');
                $user_id = $request->session()->get('user.id');
                $formated_data = [];
                $is_valid = true;
                $error_msg = '';
                DB::beginTransaction();
                foreach ($imported_data as $key => $value) {
                    //Check if 26 no. of columns exists
                    if (count($value) != 26) {
                        $is_valid =  false;
                        $error_msg = "Number of columns mismatch";
                        break;
                    }
                    $row_no = $key + 1;
                    $contact_array = [];
                    //Check contact type
                    $contact_type = '';
                    $contact_types = [
                        1 => 'customer',
                        2 => 'supplier',
                        3 => 'both'
                    ];
                    if (!empty($value[0])) {
                        $contact_type = strtolower(trim($value[0]));
                        if (in_array($contact_type, [1, 2, 3])) {
                            $contact_array['type'] = $contact_types[$contact_type];
                        } else {
                            $is_valid =  false;
                            $error_msg = "Invalid contact type in row no. $row_no";
                            break;
                        }
                    } else {
                        $is_valid =  false;
                        $error_msg = "Contact type is required in row no. $row_no";
                        break;
                    }
                    //Check contact name
                    if (!empty($value[1])) {
                        $contact_array['name'] = $value[1];
                    } else {
                        $is_valid =  false;
                        $error_msg = "Contact name is required in row no. $row_no";
                        break;
                    }
                    //Check supplier fields
                    if (in_array($contact_type, ['supplier', 'both'])) {
                        //Check business name
                        if (!empty(trim($value[2]))) {
                            $contact_array['supplier_business_name'] = $value[2];
                        } else {
                            $is_valid =  false;
                            $error_msg = "Business name is required in row no. $row_no";
                            break;
                        }
                        //Check pay term
                        if (trim($value[6]) != '') {
                            $contact_array['pay_term_number'] = trim($value[6]);
                        } else {
                            $is_valid =  false;
                            $error_msg = "Pay term is required in row no. $row_no";
                            break;
                        }
                        //Check pay period
                        $pay_term_type = strtolower(trim($value[7]));
                        if (in_array($pay_term_type, ['days', 'months'])) {
                            $contact_array['pay_term_type'] = $pay_term_type;
                        } else {
                            $is_valid =  false;
                            $error_msg = "Pay term period is required in row no. $row_no";
                            break;
                        }
                    }
                    //Check contact ID
                    if (!empty(trim($value[3]))) {
                        $count = Contact::where('business_id', $business_id)
                            ->where('contact_id', $value[3])
                            ->count();
                        if ($count == 0) {
                            $contact_array['contact_id'] = $value[3] . '-' . $business_id;
                        } else {
                            $is_valid =  false;
                            $error_msg = "Contact ID already exists in row no. $row_no";
                            break;
                        }
                    }
                    //Tax number
                    if (!empty(trim($value[4]))) {
                        $contact_array['tax_number'] = $value[4];
                    }
                    //Check opening balance
                    if (!empty(trim($value[5])) && $value[5] != 0) {
                        $contact_array['opening_balance'] = $this->productUtil->num_uf(trim($value[5]));
                    }
                    //Check credit limit
                    if (trim($value[8]) != '' && in_array($contact_type, ['customer', 'both'])) {
                        $contact_array['credit_limit'] = trim($value[8]);
                    }
                    //Check email
                    if (!empty(trim($value[9]))) {
                        if (filter_var(trim($value[9]), FILTER_VALIDATE_EMAIL)) {
                            $contact_array['email'] = $value[9];
                        } else {
                            $is_valid =  false;
                            $error_msg = "Invalid email id in row no. $row_no";
                            break;
                        }
                    }
                    //Mobile number
                    if (!empty(trim($value[10]))) {
                        $contact_array['mobile'] = $value[10];
                    } else {
                        $is_valid =  false;
                        $error_msg = "Mobile number is required in row no. $row_no";
                        break;
                    }
                    //Alt contact number
                    $contact_array['alternate_number'] = $value[11];
                    //Landline
                    $contact_array['landline'] = $value[12];
                    //City
                    $contact_array['city'] = $value[13];
                    //State
                    $contact_array['state'] = $value[14];
                    //Country
                    $contact_array['country'] = $value[15];
                    //Landmark
                    $contact_array['landmark'] = $value[16];
                    //Cust fields
                    $contact_array['custom_field1'] = $value[17];
                    $contact_array['custom_field2'] = $value[18];
                    $contact_array['custom_field3'] = $value[19];
                    $contact_array['custom_field4'] = $value[20];
                    $contact_array['transaction_date'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value[21]);
                    $contact_array['security_deposit'] = $value[22];
$contact_array['security_deposit_asset_account'] = $value[23];
                    $contact_array['security_deposit_liability_account'] = $value[24];
                    $contact_array['payment_account'] = $value[25];
                    $formated_data[] = $contact_array;
                }
                if (!$is_valid) {
                    throw new \Exception($error_msg);
                }
                if (!empty($formated_data)) {
                    foreach ($formated_data as $contact_data) {
                        $ref_count = $this->transactionUtil->setAndGetReferenceCount('contacts');
                        //Set contact id if empty
                        if (empty($contact_data['contact_id'])) {
                            $contact_data['contact_id'] = $this->commonUtil->generateReferenceNumber('contacts', $ref_count) . '-' . $business_id;
                        }
                        $opening_balance = 0;
                        if (isset($contact_data['opening_balance'])) {
                            $opening_balance = $contact_data['opening_balance'];
                            $transaction_date = $contact_data['transaction_date'];
                            unset($contact_data['opening_balance']);
                            unset($contact_data['transaction_date']);
                        }
                        if (isset($contact_data['transaction_date'])) {
                            $transaction_date = $contact_data['transaction_date'];
                            unset($contact_data['transaction_date']);
                        }
                        $security_deposit = 0;
                        if (isset($contact_data['security_deposit'])) {
                            $security_deposit = $contact_data['security_deposit'];
                            unset($contact_data['security_deposit']);
                        } else {
                            unset($contact_data['security_deposit']);
                        }
                        $contact_data['business_id'] = $business_id;
                        $contact_data['created_by'] = $user_id;
                        $contact = Contact::create($contact_data);
                        if (!empty($opening_balance)) {
                            $this->transactionUtil->createOpeningBalanceTransaction($business_id, $contact->id, $opening_balance, $transaction_date);
                        }
                        if (!empty($security_deposit)) {
                            $business_location = BusinessLocation::where('business_id', $business_id)->first();
                            $payment_type = 'security_deposit';
                            $final_amount = $this->transactionUtil->num_uf($security_deposit);
                            $ob_data = [
                                'business_id' => $business_id,
                                'location_id' => $business_location->id,
                                'type' => $payment_type,
                                'status' => 'final',
                                'payment_status' => 'due',
                                'contact_id' => $contact->id,
                                'transaction_date' => $transaction_date,
                                'total_before_tax' => $final_amount,
                                'final_total' => $final_amount,
                                'created_by' => request()->session()->get('user.id')
                            ];
                            //Update reference count
                            $ob_ref_count = $this->transactionUtil->setAndGetReferenceCount($payment_type);
                            //Generate reference number
                            $ob_data['ref_no'] = $this->transactionUtil->generateReferenceNumber($payment_type, $ob_ref_count);
                            //Create opening balance transaction
                            $transaction = Transaction::create($ob_data);
                        }
                    }
                }
                $output = [
                    'success' => 1,
                    'msg' => __('product.file_imported_successfully')
                ];
                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];
            return redirect()->route('contacts.import')->with('notification', $output);
        }
        return redirect()->action('ContactController@index', ['type' => 'supplier'])->with('status', $output);
    }
    /**
     * Shows ledger for contacts
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */

    public function getReturnedCheques()
    {
        $types = ['supplier', 'customer'];
        $business_id = request()->session()->get('user.business_id');
        $contacts = Contact::where('business_id', $business_id)->pluck('name','id');
// $accounts = Transaction::where('transactions.type', 'cheque_return')
//                 ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')
//                 ->leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
//                 ->where('transactions.business_id', $business_id)
//                 ->with(['accountTransactionsRelation' => function ($query) {
//                     $query->select('transaction_id', 'cheque_number', DB::raw('ROUND(amount, 2) as amount')); // Select relevant columns from accountTransactionsRelation
//                 }]) // Eager load the accountTransactionsRelation
//                 ->select([
//                     'contacts.name as customer',
//                     'transaction_payments.cheque_number as cheque_number',
//                     'transaction_payments.cheque_date',
//                     'transaction_payments.amount',
//                     'transaction_payments.bank_name',
//                     'transactions.transaction_date',
//                     'contacts.type as contact_type',
//                     'transactions.id as id'
//                 ])->get();
                // dd($accounts);
        if (request()->ajax()) {
            $accounts = Transaction::where('transactions.type', 'cheque_return')
                ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')
                ->leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->where('transactions.business_id', $business_id)
                ->with(['accountTransactionsRelation' => function ($query) {
                    $query->select('transaction_id', 'cheque_number', DB::raw('ROUND(amount, 2) as amount'), 'cheque_date', 'bank_name', 'cheque_ref_no'); 
                }]) 
                ->select([
                    'contacts.name as customer',
                    'transaction_payments.cheque_number as cheque_number',
                    'transaction_payments.cheque_date',
                    'transaction_payments.amount',
                    'transaction_payments.bank_name as bank_name',
                    'transactions.transaction_date',
                    'contacts.type as contact_type',
                    'transactions.id as id'
                ]);
            
            foreach ($accounts as $account) {
                $account->payment_cheque_number = $account->accountTransactionsRelation->first() ? $account->accountTransactionsRelation->first()->cheque_number : null;
            }


            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $accounts->whereDate('transactions.transaction_date', '>=', request()->start_date);
                $accounts->whereDate('transactions.transaction_date', '<=', request()->end_date);
            }

            if (!empty(request()->contact_type)) {
                $accounts->where('contacts.type', request()->contact_type);
            }

            if (!empty(request()->user_id)) {
                $accounts->where('transactions.contact_id', request()->user_id);
            }
            // if (!empty(request()->cheque_number)) {
            //     $accounts->where('transaction_payments.cheque_number', request()->cheque_number);
            // }

            // if (!empty(request()->bank_name)) {
            //     $accounts->where('transaction_payments.bank_name', request()->bank_name);
            // }

            if (!empty(request()->amount)) {
                $accounts->where('transaction_payments.amount', request()->amount);
            }

            // if (!empty(request()->cheque_date)) {
            //     $accounts->where('transaction_payments.cheque_date', request()->cheque_date);
            // }


            return DataTables::of($accounts)

                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')

                // ->editColumn('cheque_date', '{{@format_date($cheque_date)}}')

                ->addColumn('amount', function ($account) {
                    return '<span class="display_currency" data-currency_symbol="false">' . $this->productUtil->num_f($account->accountTransactionsRelation->amount) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<span class="btn btn-info dropdown-toggle btn-xs" data-id="'.$row->id.'"><i class="glyphicon glyphicon-edit"></i> Edit</span>';
                })
                ->addColumn('payment_cheque_number', function ($account) {
                    return $account->accountTransactionsRelation->cheque_number;
                })
                ->addColumn('bank_name', function ($account) {
                    return $account->accountTransactionsRelation->bank_name == 0 ? '' : $account->accountTransactionsRelation->bank_name;
                })
                ->addColumn('cheque_date', function ($account) {
                    return $account->accountTransactionsRelation->cheque_date;
                })
                ->addColumn('cheque_ref_no', function ($account) {
                    return $account->accountTransactionsRelation->cheque_ref_no;
                })
                ->removeColumn('id')
                ->rawColumns(['amount', 'action', 'cheque_ref_no'])
                ->make(true);
        }

        return view('contact.returned_cheques')
            ->with(compact('types', 'contacts'));

    }
    private function calculateBalance($contactId, $operator, $date)
    {
        return Transaction::where('contact_id', $contactId)
            ->whereDate('transaction_date', $operator, $date)
            ->where('payment_status', 'due')
            ->sum('final_total');
    }

    public function getLedger_()
    {
        
        if (!auth()->user()->can('supplier.view') && !auth()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $asset_account_id = Account::leftjoin('account_types', 'accounts.account_type_id', 'accounts.id')
            ->where('account_types.name', 'like', '%Assets%')
            ->where('accounts.business_id', $business_id)
            ->pluck('accounts.id')->toArray();
        $contact_id = request()->input('contact_id');

        $start_date = request()->start_date;
        $end_date =  request()->end_date;
        $transaction_type =  request()->transaction_type;
        $transaction_amount =  request()->transaction_amount;

        $contact = Contact::find($contact_id);
        $business_details = $this->businessUtil->getDetails($contact->business_id);
        $location_details = BusinessLocation::where('business_id', $contact->business_id)->first();

        $opening_balance = Transaction::where('contact_id', $contact_id)
            ->whereDate('transaction_date', '>=', $start_date)
            ->whereDate('transaction_date', '<=', $end_date)
            ->where('type', 'opening_balance')
            ->sum('final_total');

        $beginning_balance = 0;
        $lastTransaction = Transaction::where('contact_id', $contact_id)
            ->orderBy('transaction_date', 'desc')
            ->first();
        $firstTransaction = Transaction::where('contact_id', $contact_id)
            ->orderBy('transaction_date', 'asc')
            ->first();
        if ($lastTransaction) {
            $lastTransactionDate = $lastTransaction->transaction_date;
            if($start_date >= $lastTransactionDate) {
                $date = \Carbon::now();
                if($end_date >= $date) {
                    $date = $end_date;
                }
                $ledgerDetails = $this->__getLedgerDetails($contact_id, $start_date, $end_date);
                $beginning_balance = $ledgerDetails['opening_balance'] + $ledgerDetails['beginning_balance'] + $ledgerDetails['total_invoice'] + $ledgerDetails['returned_cheques'] + $ledgerDetails['returned_cheque_charges'] - $ledgerDetails['total_paid'];
            } else {
                if($opening_balance == 0 ) {
                    $beginning_balance = Transaction::where('contact_id', $contact_id)
                        ->whereDate('transaction_date', '<=', $end_date)
                        ->where('type', 'opening_balance')
                        ->sum('final_total');
                }
            }

        }
        $ledger_details = $this->__getLedgerDetails($contact_id, $start_date, $end_date);
        $ledger_details['opening_balance'] = $opening_balance;
        $ledger_details['beginning_balance'] = $this->contactUtil->getCustomerBf($contact_id,$business_id,$start_date);
        if ($contact->type == 'supplier') {
            $query = ContactLedger::leftjoin('transactions', 'contact_ledgers.transaction_id', 'transactions.id')
                ->leftjoin('transaction_payments', 'contact_ledgers.transaction_payment_id', 'transaction_payments.id')
                ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
                ->leftjoin('account_transactions', 'transactions.id', 'account_transactions.transaction_id')
                ->leftjoin('accounts', 'account_transactions.account_id', 'accounts.id')
                ->where('transactions.contact_id', $contact_id)
                ->where('transactions.business_id', $business_id)
                ->select(
                    'contact_ledgers.*',
                    'contact_ledgers.type as acc_transaction_type',
                    'business_locations.name as location_name',
                    'transactions.ref_no',
                    'transactions.invoice_no',
                    'transactions.transaction_date',
                    'transactions.payment_status',
                    'transactions.pay_term_number',
                    'transactions.pay_term_type',
                    'transaction_payments.method as payment_method',
                    'transaction_payments.bank_name',
                    'transaction_payments.cheque_date',
                    'transaction_payments.cheque_number',
                    'transactions.type as transaction_type',
                    'accounts.account_number',
                    'accounts.name as account_name'
                )->groupBy('contact_ledgers.id')->orderBy('contact_ledgers.id', 'asc');;
        }
        if ($contact->type == 'customer') {
            $opening_balance_new = DB::select("select `cl`.`amount` as opening_balance
            from `contact_ledgers` cl left join `transactions` t on `cl`.`transaction_id` = `t`.`id`
            left join `business_locations` bl on `t`.`location_id` = `bl`.`id`
             where `cl`.`contact_id` = " . $contact_id . "
             and `cl`.`type` = 'debit'
             and `t`.`business_id` = " . $business_id . "
            and `t`.`type` = 'opening_balance'
             and date(`cl`.`operation_date`) >= '" . $start_date . "'
             and date(`cl`.`operation_date`) <= '" . $end_date . "'
            order by `cl`.`operation_date`");
            if (count($opening_balance_new) == 0) {
                $opening_balance_new = DB::select(" select ( select
                sum(`bc_cl`.`amount`) as total_paid
                from `contact_ledgers` bc_cl left join `transactions` bc_t on `bc_cl`.`transaction_id` = `bc_t`.`id`
               left join `business_locations` bc_bl on `bc_t`.`location_id` = `bc_bl`.`id`
               where `bc_cl`.`contact_id` =  " . $contact_id . "
               and `bc_cl`.`type` = 'credit'
               and `bc_t`.`business_id` = " . $business_id . "
               and date(`bc_cl`.`operation_date`)  <= '" . $start_date . "'
               group by `bc_cl`.`id` and `bc_cl`.`contact_id` order by bc_cl.operation_date) as before_purchase,
               (select sum(`cl`.`amount`)
               from `contact_ledgers` cl left join `transactions` t on `cl`.`transaction_id` = `t`.`id`
               left join `business_locations` bl on `t`.`location_id` = `bl`.`id`
                where `cl`.`contact_id` = " . $contact_id . "
                and `cl`.`type` = 'debit'
                and `t`.`business_id` = " . $business_id . "
                and date(`cl`.`operation_date`) < '" . $start_date . "'
                group by `cl`.`id` and `cl`.`contact_id` order by cl.operation_date)  as before_sell,
               (select(IFNULL(before_sell,0) - IFNULL(before_purchase,0))) as opening_balance");
            }
            $total_paid = DB::select("select
            sum(`bc_cl`.`amount`) as total_paid
            from `contact_ledgers` bc_cl left join `transactions` bc_t on `bc_cl`.`transaction_id` = `bc_t`.`id`
           left join `business_locations` bc_bl on `bc_t`.`location_id` = `bc_bl`.`id`
           where `bc_cl`.`contact_id` =  " . $contact_id . "
           and `bc_cl`.`type` = 'credit'
           and `bc_t`.`business_id` = " . $business_id . "
           and date(`bc_cl`.`operation_date`)  >= '" . $start_date . "'
           and date(`bc_cl`.`operation_date`)  <= '" . $end_date . "'
           group by `bc_cl`.`id` and `bc_cl`.`contact_id` ");
            $total_sell = DB::select("select
            sum(`bc_cl`.`amount`) as total_sell
            from `contact_ledgers` bc_cl left join `transactions` bc_t on `bc_cl`.`transaction_id` = `bc_t`.`id`
           left join `business_locations` bc_bl on `bc_t`.`location_id` = `bc_bl`.`id`
           where `bc_cl`.`contact_id` =  " . $contact_id . "
           and `bc_cl`.`type` = 'debit'
           and `bc_t`.`type` != 'opening_balance'
           and `bc_t`.`business_id` = " . $business_id . "
           and date(`bc_cl`.`operation_date`)  >= '" . $start_date . "'
           and date(`bc_cl`.`operation_date`)  <= '" . $end_date . "'
           group by `bc_cl`.`id` and `bc_cl`.`contact_id` ");
            // $ledger_details['total_invoice'] = count($total_sell) > 0 ? $total_sell[0]->total_sell : 0;
            // $ledger_details['total_paid'] = count($total_paid) > 0 ? $total_paid[0]->total_paid : 0;
            // $ledger_details['beginning_balance'] = count($opening_balance_new) > 0 ? $opening_balance_new[0]->opening_balance : 0;
            // $ledger_details['balance_due'] = $ledger_details['beginning_balance'] + $ledger_details['total_invoice'] - $ledger_details['total_paid'];

            $query = ContactLedger::leftjoin('transactions', 'contact_ledgers.transaction_id', 'transactions.id')
                ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
                ->leftjoin('transaction_payments', 'contact_ledgers.transaction_payment_id', 'transaction_payments.id')
                ->leftjoin('air_ticket_invoices', 'transactions.id', 'air_ticket_invoices.transaction_id')
                ->where('contact_ledgers.contact_id', $contact_id)
                ->where('transactions.business_id', $business_id)
                ->select(
                    'contact_ledgers.*',
                    'contact_ledgers.type as acc_transaction_type',
                    'business_locations.name as location_name',
                    'transactions.sub_type as t_sub_type',
                    'transactions.final_total',
                    'transactions.ref_no',
                    'transactions.invoice_no',
                    'transactions.is_direct_sale',
                    'transactions.is_credit_sale',
                    'transactions.is_settlement',
                    'transactions.transaction_date',
                    'transactions.payment_status',
                    'transactions.pay_term_number',
                    'transactions.pay_term_type',
                    'transactions.type as transaction_type',
                    'transaction_payments.method as payment_method',
                    'transaction_payments.transaction_id as tp_transaction_id',
                    'transaction_payments.paid_on',
                    'transaction_payments.bank_name',
                    'transaction_payments.cheque_date',
                    'transaction_payments.cheque_number',
                    'transactions.fleet_id',
                    'air_ticket_invoices.*',
                    DB::raw("(select
                    sum(`bc_cl`.`amount`)
                    from `contact_ledgers` bc_cl left join `transactions` bc_t on `bc_cl`.`transaction_id` = `bc_t`.`id`
                   left join `business_locations` bc_bl on `bc_t`.`location_id` = `bc_bl`.`id`
                   where `bc_cl`.`contact_id` =  `contact_ledgers`.`contact_id`
                   and `bc_cl`.`type` = 'credit'
                   and `bc_t`.`business_id` = `transactions`.`business_id`
                   and `bc_cl`.`id`  <= `contact_ledgers`.`id`
                   group by `bc_cl`.`id` and `bc_cl`.`contact_id`) as balance_credit"),
                    DB::raw("(select
                    sum(`cl`.`amount`)
                   from `contact_ledgers` cl left join `transactions` t on `cl`.`transaction_id` = `t`.`id`
                   left join `business_locations` bl on `t`.`location_id` = `bl`.`id`
                   where `cl`.`contact_id` =  `contact_ledgers`.`contact_id`
                   and `cl`.`type` = 'debit'
                   and `t`.`business_id` = `transactions`.`business_id`
                   and `cl`.`id`  <= `contact_ledgers`.`id`
                   group by `cl`.`id` and `cl`.`contact_id`) as balance_debit"),
                    DB::raw("(select(IFNULL(balance_debit,0) - IFNULL(balance_credit,0)) ) as balance")
                )->groupBy('contact_ledgers.id')->orderBy('contact_ledgers.id', 'asc');
        }
        if (!empty($start_date)  && !empty($end_date)) {
            $query->whereDate('contact_ledgers.operation_date', '>=', $start_date);
            $query->whereDate('contact_ledgers.operation_date', '<=', $end_date);
        }
        if (!empty($transaction_type)) { // debit / credit type filter
            $query->where('contact_ledgers.type', $transaction_type);
        }
        if (!empty($transaction_amount)) {
            $query->where('contact_ledgers.amount', $transaction_amount);
        }
        $query->orderby('contact_ledgers.operation_date');
        $ledger_transactions = $query->get();
        
        if (request()->input('action') == 'pdf') {
            $for_pdf = true;
            $html = view('contact.ledger')
                ->with(compact('ledger_details', 'contact', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details'))->render();
            $mpdf = $this->getMpdf();
            $mpdf->WriteHTML($html);
            $mpdf->Output();
        }
        if (request()->input('action') == 'print') {
            $for_pdf = true;
            return view('contact.ledger')
                ->with(compact('ledger_details', 'contact', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details'))->render();
        }
        return view('contact.ledger')
            ->with(compact('ledger_details', 'contact', 'opening_balance', 'ledger_transactions', 'business_details', 'location_details'));
    }
    
    public function updateDuePayments($contact_id){
        $due = $this->contactUtil->getSupplierBalance($contact_id,request()->session()->get('user.business_id'),true);
        
        if($due <= 0){
            Transaction::where('contact_id',$contact_id)->whereIn('payment_status',['partial','due'])->update(['payment_status' => 'paid']);
        }
        
        $transactions = Transaction::leftjoin('contacts','contacts.id','transactions.contact_id')->where('transactions.business_id',request()->session()->get('user.business_id'))->where('contacts.type','supplier')->where('transactions.contact_id',$contact_id)->whereIn('payment_status',['partial','due'])->select('transactions.*')->get();
        
        
        foreach($transactions as$transaction){
            $this->transactionUtil->updatePaymentStatus($transaction->id);
        }
    }
    

//pp changes 
// public function getLedger()
// {
//     if (!auth()->user()->can('supplier.view') && !auth()->user()->can('customer.view')) {
//         abort(403, 'Unauthorized action.');
//     }

//     $business_id = request()->session()->get('user.business_id');
//     $contact_id = request()->input('contact_id');
//     $start_date = request()->start_date;
//     $end_date = request()->end_date;

//     Log::info('Fetching ledger details', [
//         'user_id' => auth()->id(),
//         'business_id' => $business_id,
//         'contact_id' => $contact_id,
//         'start_date' => $start_date,
//         'end_date' => $end_date
//     ]);

//     $contact = Contact::find($contact_id);

//     if (!$contact) {
//         Log::error('Contact not found', ['contact_id' => $contact_id]);
//         return response()->json(['error' => 'Contact not found'], 404);
//     }

//     $transaction_amounts = request()->transaction_amount;
//     $transaction_type = request()->transaction_type;

//     Log::debug('Transaction filters applied', [
//         'transaction_amounts' => $transaction_amounts,
//         'transaction_type' => $transaction_type
//     ]);

//     $business_details = $this->businessUtil->getDetails($contact->business_id);
//     $location_details = BusinessLocation::where('business_id', $contact->business_id)->first();

//     $ledger_details['opening_balance'] = 0;

//     if ($contact->type == 'customer') {
//         Log::info('Fetching customer ledger', ['contact_id' => $contact_id]);

//         $ledger_details['beginning_balance'] = $this->contactUtil->getCustomerBf($contact_id, $business_id, $start_date);
//         $ledger_transactions = $this->contactUtil->getCustomerLedger($contact_id, $business_id, $start_date, $end_date);

//         Log::debug('Customer ledger transactions', ['transactions' => $ledger_transactions->toArray()]);

//         $amts = array_unique($ledger_transactions->pluck('amount')->toArray());

//         if (request()->input('action') == 'pdf') {
//             Log::info('Generating PDF for customer ledger');
//             $for_pdf = true;
//             $html = view('contact.ledger_new')
//                 ->with(compact('ledger_details', 'contact', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details', 'start_date', 'end_date', 'transaction_amounts', 'transaction_type'))
//                 ->render();
//             $mpdf = $this->getMpdf();
//             $mpdf->WriteHTML($html);
//             $mpdf->Output();
//         }

//         if (request()->input('action') == 'print') {
//             Log::info('Printing customer ledger');
//             $for_pdf = true;
//             return view('contact.ledger_new')
//                 ->with(compact('ledger_details', 'contact', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details', 'start_date', 'end_date', 'transaction_amounts', 'transaction_type'))
//                 ->render();
//         }

//         return response()->json([
//             'html' => view('contact.ledger_new')
//                 ->with(compact('ledger_details', 'contact', 'ledger_transactions', 'business_details', 'location_details', 'start_date', 'end_date', 'transaction_amounts', 'transaction_type'))
//                 ->render(),
//             'amounts' => $amts
//         ]);
//     }

//     if ($contact->type == 'supplier') {
//         Log::info('Fetching supplier ledger', ['contact_id' => $contact_id]);

//         $ledger_details['beginning_balance'] = $this->contactUtil->getSupplierBf($contact_id, $business_id, $start_date);
//         $ledger_transactions = $this->contactUtil->getSupplierLedger($contact_id, $business_id, $start_date, $end_date);

//         Log::debug('Supplier ledger transactions', ['transactions' => $ledger_transactions->toArray()]);

//         $this->updateDuePayments($contact_id);

//         if (request()->input('action') == 'pdf') {
//             Log::info('Generating PDF for supplier ledger');
//             $for_pdf = true;
//             $html = view('contact.ledger_new_supplier')
//                 ->with(compact('ledger_details', 'contact', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details', 'start_date', 'end_date', 'transaction_amounts', 'transaction_type'))
//                 ->render();
//             $mpdf = $this->getMpdf();
//             $mpdf->WriteHTML($html);
//             $mpdf->Output();
//         }

//         if (request()->input('action') == 'print') {
//             Log::info('Printing supplier ledger');
//             $for_pdf = true;
//             return view('contact.ledger_new_supplier')
//                 ->with(compact('ledger_details', 'contact', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details', 'start_date', 'end_date', 'transaction_amounts', 'transaction_type'))
//                 ->render();
//         }

//         return response()->json([
//             'html' => view('contact.ledger_new_supplier')
//                 ->with(compact('ledger_details', 'contact', 'ledger_transactions', 'business_details', 'location_details', 'start_date', 'end_date', 'transaction_amounts', 'transaction_type'))
//                 ->render(),
//             'amounts' => array_unique($ledger_transactions->pluck('amount')->toArray())
//         ]);
//     }
// }


// public function getLedger()
// {
//     if (!auth()->user()->can('supplier.view') && !auth()->user()->can('customer.view')) {
//         abort(403, 'Unauthorized action.');
//     }

//     $business_id = request()->session()->get('user.business_id');
//     $contact_id = request()->input('contact_id');
//     $start_date = request()->start_date;
//     $end_date = request()->end_date;

//     Log::info('Fetching ledger details', [
//         'user_id' => auth()->id(),
//         'business_id' => $business_id,
//         'contact_id' => $contact_id,
//         'start_date' => $start_date,
//         'end_date' => $end_date
//     ]);

//     $contact = Contact::find($contact_id);

//     if (!$contact) {
//         Log::error('Contact not found', ['contact_id' => $contact_id]);
//         return response()->json(['error' => 'Contact not found'], 404);
//     }

//     $transaction_amounts = request()->transaction_amount;
//     $transaction_type = request()->transaction_type;

//     Log::debug('Transaction filters applied', [
//         'transaction_amounts' => $transaction_amounts,
//         'transaction_type' => $transaction_type
//     ]);

//     $business_details = $this->businessUtil->getDetails($contact->business_id);
//     $location_details = BusinessLocation::where('business_id', $contact->business_id)->first();

//     $ledger_details['opening_balance'] = 0;

//     if ($contact->type == 'customer') {
//         Log::info('Fetching customer ledger', ['contact_id' => $contact_id]);

//         $ledger_details['beginning_balance'] = $this->contactUtil->getCustomerBf($contact_id, $business_id, $start_date);
//         $ledger_transactions = $this->contactUtil->getCustomerLedger($contact_id, $business_id, $start_date, $end_date)
//             ->sortByDesc('transaction_date')
//             ->sortByDesc('created_at'); // Sort by transaction_date and then created_at

//         Log::debug('Customer ledger transactions', ['transactions' => $ledger_transactions->toArray()]);

//         $amts = array_unique($ledger_transactions->pluck('amount')->toArray());

//         if (request()->input('action') == 'pdf') {
//             Log::info('Generating PDF for customer ledger');
//             $for_pdf = true;
//             $html = view('contact.ledger_new')
//                 ->with(compact('ledger_details', 'contact', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details', 'start_date', 'end_date', 'transaction_amounts', 'transaction_type'))
//                 ->render();
//             $mpdf = $this->getMpdf();
//             $mpdf->WriteHTML($html);
//             $mpdf->Output();
//         }

//         if (request()->input('action') == 'print') {
//             Log::info('Printing customer ledger');
//             $for_pdf = true;
//             return view('contact.ledger_new')
//                 ->with(compact('ledger_details', 'contact', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details', 'start_date', 'end_date', 'transaction_amounts', 'transaction_type'))
//                 ->render();
//         }

//         Log::info('Returning customer ledger details');
//         return response()->json([
//             'html' => view('contact.ledger_new')
//                 ->with(compact('ledger_details', 'contact', 'ledger_transactions', 'business_details', 'location_details', 'start_date', 'end_date', 'transaction_amounts', 'transaction_type'))
//                 ->render(),
//             'amounts' => $amts
//         ]);
//     }

//     if ($contact->type == 'supplier') {
//         Log::info('Fetching supplier ledger', ['contact_id' => $contact_id]);

//         $ledger_details['beginning_balance'] = $this->contactUtil->getSupplierBf($contact_id, $business_id, $start_date);
//         $ledger_transactions = $this->contactUtil->getSupplierLedger($contact_id, $business_id, $start_date, $end_date)
//             ->sortByDesc('transaction_date')
//             ->sortByDesc('created_at'); // Sort by transaction_date and then created_at

//         Log::debug('Supplier ledger transactions', ['transactions' => $ledger_transactions->toArray()]);

//         $this->updateDuePayments($contact_id);

//         if (request()->input('action') == 'pdf') {
//             Log::info('Generating PDF for supplier ledger');
//             $for_pdf = true;
//             $html = view('contact.ledger_new_supplier')
//                 ->with(compact('ledger_details', 'contact', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details', 'start_date', 'end_date', 'transaction_amounts', 'transaction_type'))
//                 ->render();
//             $mpdf = $this->getMpdf();
//             $mpdf->WriteHTML($html);
//             $mpdf->Output();
//         }

//         if (request()->input('action') == 'print') {
//             Log::info('Printing supplier ledger');
//             $for_pdf = true;
//             return view('contact.ledger_new_supplier')
//                 ->with(compact('ledger_details', 'contact', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details', 'start_date', 'end_date', 'transaction_amounts', 'transaction_type'))
//                 ->render();
//         }

//         Log::info('Returning supplier ledger details');
//         return response()->json([
//             'html' => view('contact.ledger_new_supplier')
//                 ->with(compact('ledger_details', 'contact', 'ledger_transactions', 'business_details', 'location_details', 'start_date', 'end_date', 'transaction_amounts', 'transaction_type'))
//                 ->render(),
//             'amounts' => array_unique($ledger_transactions->pluck('amount')->toArray())
//         ]);
//     }
// }

public function getLedger()
{
 
    if (!auth()->user()->can('supplier.view') && !auth()->user()->can('customer.view')) {
        abort(403, 'Unauthorized action.');
    }

    $business_id = request()->session()->get('user.business_id');
    $contact_id = request()->input('contact_id');
    $start_date = request()->start_date;
    $end_date = request()->end_date;

    Log::info('Fetching ledger details', [
        'user_id' => auth()->id(),
        'business_id' => $business_id,
        'contact_id' => $contact_id,
        'start_date' => $start_date,
        'end_date' => $end_date
    ]);

    $contact = Contact::find($contact_id);

    if (!$contact) {
        Log::error('Contact not found', ['contact_id' => $contact_id]);
        return response()->json(['error' => 'Contact not found'], 404);
    }

    if($contact->manual_bill_settlement == 1){
        return response()->json(['error' => 'Manul Bill settlement is turned on!'], 404);
    }

    $transaction_amounts = request()->transaction_amount;
    $transaction_type = request()->transaction_type;

    Log::debug('Transaction filters applied', [
        'transaction_amounts' => $transaction_amounts,
        'transaction_type' => $transaction_type
    ]);

    $business_details = $this->businessUtil->getDetails($contact->business_id);
    $location_details = BusinessLocation::where('business_id', $contact->business_id)->first();

    $ledger_details['opening_balance'] = 0;

    // Helper function to process transactions (no time filtering)
    $processTransactions = function ($transactions) {
        return $transactions; // No time exclusion logic anymore
    };

    // If the contact is a customer
    if ($contact->type == 'customer') {
        Log::info('Fetching customer ledger', ['contact_id' => $contact_id]);

        $ledger_details['beginning_balance'] = $this->contactUtil->getCustomerBf($contact_id, $business_id, $start_date);
        $ledger_transactions = $this->contactUtil->getCustomerLedger($contact_id, $business_id, $start_date, $end_date)
            ->sortBy('transaction_date')  // Sort by transaction_date in ascending order
            ->sortBy('created_at');  // Then sort by created_at in ascending order

        // Process transactions without time exclusion
        $ledger_transactions = $processTransactions($ledger_transactions);

        Log::debug('Customer ledger transactions', ['transactions' => $ledger_transactions->toArray()]);

        $amts = array_unique($ledger_transactions->pluck('amount')->toArray());

        if (request()->input('action') == 'pdf') {
            Log::info('Generating PDF for customer ledger');
            $for_pdf = true;
            $html = view('contact.ledger_new')
                ->with(compact('ledger_details', 'contact', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details', 'start_date', 'end_date', 'transaction_amounts', 'transaction_type'))
                ->render();
            $mpdf = $this->getMpdf();
            $mpdf->WriteHTML($html);
            $mpdf->Output();
        }

        if (request()->input('action') == 'print') {
            Log::info('Printing customer ledger');
            $for_pdf = true;
            return view('contact.ledger_new')
                ->with(compact('ledger_details', 'contact', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details', 'start_date', 'end_date', 'transaction_amounts', 'transaction_type'))
                ->render();
        }
        
        Log::info('Returning customer ledger details');
        
        return response()->json([
            'html' => view('contact.ledger_new')
                ->with(compact('ledger_details', 'contact', 'ledger_transactions', 'business_details', 'location_details', 'start_date', 'end_date', 'transaction_amounts', 'transaction_type'))
                ->render(),
            'amounts' => $amts
        ]);
    }

    // If the contact is a supplier
    if ($contact->type == 'supplier') {
        Log::info('Fetching supplier ledger', ['contact_id' => $contact_id]);

        $ledger_details['beginning_balance'] = $this->contactUtil->getSupplierBf($contact_id, $business_id, $start_date);
        $ledger_transactions = $this->contactUtil->getSupplierLedger($contact_id, $business_id, $start_date, $end_date)
            ->sortBy('transaction_date')  // Sort by transaction_date in ascending order
            ->sortBy('created_at');  // Then sort by created_at in ascending order

        // Process transactions without time exclusion
        $ledger_transactions = $processTransactions($ledger_transactions);

        Log::debug('Supplier ledger transactions', ['transactions' => $ledger_transactions->toArray()]);

        $this->updateDuePayments($contact_id);

        if (request()->input('action') == 'pdf') {
            Log::info('Generating PDF for supplier ledger');
            $for_pdf = true;
            $html = view('contact.ledger_new_supplier')
                ->with(compact('ledger_details', 'contact', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details', 'start_date', 'end_date', 'transaction_amounts', 'transaction_type'))
                ->render();
            $mpdf = $this->getMpdf();
            $mpdf->WriteHTML($html);
            $mpdf->Output();
        }

        if (request()->input('action') == 'print') {
            Log::info('Printing supplier ledger');
            $for_pdf = true;
            return view('contact.ledger_new_supplier')
                ->with(compact('ledger_details', 'contact', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details', 'start_date', 'end_date', 'transaction_amounts', 'transaction_type'))
                ->render();
        }

        Log::info('Returning supplier ledger details');
        return response()->json([
            'html' => view('contact.ledger_new_supplier')
                ->with(compact('ledger_details', 'contact', 'ledger_transactions', 'business_details', 'location_details', 'start_date', 'end_date', 'transaction_amounts', 'transaction_type'))
                ->render(),
            'amounts' => array_unique($ledger_transactions->pluck('amount')->toArray())
        ]);
    }
}




    
    public function getPayment()
    {
        if (!auth()->user()->can('supplier.view') && !auth()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        
        $business_id = request()->session()->get('user.business_id');
        $contact_id = request()->input('contact_id');

        $start_date = request()->start_date;
        $end_date =  request()->end_date;
        
        $contact = Contact::find($contact_id);
        
        $business_details = $this->businessUtil->getDetails($contact->business_id);
        $location_details = BusinessLocation::where('business_id', $contact->business_id)->first();
        
        $ledger_details['opening_balance'] = 0;
        
        if($contact->type == 'customer'){
            $ledger_details['beginning_balance'] = $this->contactUtil->getCustomerBf($contact_id,$business_id,$start_date);
            $ledger_transactions = $this->contactUtil->getCustomerLedger($contact_id,$business_id,$start_date,$end_date);
            
            if (request()->input('action') == 'pdf') {
                $for_pdf = true;
                $html = view('contact.payments')
                    ->with(compact('ledger_details', 'contact', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details','start_date','end_date'))->render();
                $mpdf = $this->getMpdf();
                $mpdf->WriteHTML($html);
                $mpdf->Output();
            }
            if (request()->input('action') == 'print') {
                $for_pdf = true;
                return view('contact.payments')
                    ->with(compact('ledger_details', 'contact', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details','start_date','end_date'))->render();
            }
            return view('contact.payments')
                ->with(compact('ledger_details', 'contact',  'ledger_transactions', 'business_details', 'location_details','start_date','end_date'));
        }
        
        if($contact->type == 'supplier'){
            $ledger_details['beginning_balance'] = $this->contactUtil->getSupplierBf($contact_id,$business_id,$start_date);
            $ledger_transactions = $this->contactUtil->getSupplierLedger($contact_id,$business_id,$start_date,$end_date);
            
            if (request()->input('action') == 'pdf') {
                $for_pdf = true;
                $html = view('contact.payments')
                    ->with(compact('ledger_details', 'contact', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details','start_date','end_date'))->render();
                $mpdf = $this->getMpdf();
                $mpdf->WriteHTML($html);
                $mpdf->Output();
            }
            if (request()->input('action') == 'print') {
                $for_pdf = true;
                return view('contact.payments')
                    ->with(compact('ledger_details', 'contact', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details','start_date','end_date'))->render();
            }
            return view('contact.payments')
                ->with(compact('ledger_details', 'contact',  'ledger_transactions', 'business_details', 'location_details','start_date','end_date'));
        }
    }
    
    public function postCustomersApi(Request $request)
    {
        try {
            $api_token = $request->header('API-TOKEN');
            $api_settings = $this->moduleUtil->getApiSettings($api_token);
            $business = Business::find($api_settings->business_id);
            $data = $request->only(['name', 'email']);
            $customer = Contact::where('business_id', $api_settings->business_id)
                ->where('email', $data['email'])
                ->whereIn('type', ['customer', 'both'])
                ->first();
            if (empty($customer)) {
                $data['type'] = 'customer';
                $data['business_id'] = $api_settings->business_id;
                $data['created_by'] = $business->owner_id;
                $data['mobile'] = 0;
                $ref_count = $this->commonUtil->setAndGetReferenceCount('contacts', $business->id);
                $data['contact_id'] = $this->commonUtil->generateReferenceNumber('contacts', $ref_count, $business->id);
                $customer = Contact::create($data);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            return $this->respondWentWrong($e);
        }
        return $this->respond($customer);
    }
    /**
     * Function to get ledger details
     *
     */
    private function __getLedgerDetails($contact_id, $start, $end)
    {
        $contact = Contact::where('id', $contact_id)->first();
        //Get transaction totals between dates
        $transactions = $this->__transactionQuery($contact_id, $start, $end)
            ->with(['location'])->get();
        $transaction_types = Transaction::transactionTypes();
        //Get sum of totals before start date
        $previous_transaction_sums = $this->__transactionQuery($contact_id, $start)
            ->select(
                DB::raw("SUM(IF(type = 'purchase', final_total, 0)) as total_purchase"),
                DB::raw("SUM(IF(type = 'sell' AND status = 'final', final_total, 0)) as total_invoice"),
                DB::raw("SUM(IF(type = 'sell_return', final_total, 0)) as total_sell_return"),
                DB::raw("SUM(IF(type = 'purchase_return', final_total, 0)) as total_purchase_return"),
                DB::raw("SUM(IF(type = 'opening_balance', final_total, 0)) as opening_balance"),
                DB::raw("SUM(IF(type = 'fleet_opening_balance', final_total, 0)) as fleet_opening_balance")
            )->first();


        $ledger = [];
        $ob = 0;
        foreach ($transactions as $transaction) {
            $ledger[] = [
                'date' => $transaction->transaction_date,
                'ref_no' => in_array($transaction->type, ['sell', 'sell_return']) ? $transaction->invoice_no : $transaction->ref_no,
                'type' => $transaction_types[$transaction->type],
                'location' => $transaction->location->name,
                'payment_status' =>  __('lang_v1.' . $transaction->payment_status),
                'total' => $transaction->final_total,
                'payment_method' => '',
                'debit' => '',
                'credit' => '',
                'others' => $transaction->additional_notes
            ];

            if($transaction->type == "opening_balance"){
                $ob += $transaction->final_total;
            }
        }
        $invoice_sum = $transactions->whereIn('type', ['sell','route_operation'])->sum('final_total');
        $purchase_sum = $transactions->where('type', 'purchase')->sum('final_total');
        $sell_return_sum = $transactions->where('type', 'sell_return')->sum('final_total');
        $purchase_return_sum = $transactions->where('type', 'purchase_return')->sum('final_total');
        $opening_balance_sum = $transactions->where('type', 'opening_balance')->sum('final_total');
        $returned_cheques = $transactions->where('type','cheque_return')->sum('final_total');
        $returned_cheque_charges = $transactions->where('type','cheque_return')->sum('cheque_return_charges');

        //Get payment totals between dates
        $payments = $this->__paymentQuery($contact_id, $start, $end)
            ->select('transaction_payments.*', 'bl.name as location_name', 't.type as transaction_type', 't.ref_no', 't.invoice_no')->get();


        $paymentTypes = $this->transactionUtil->payment_types();
        //Get payment totals before start date
        $prev_payments_sum = $this->__paymentQuery($contact_id, $start)
            ->select(DB::raw("SUM(transaction_payments.amount) as total_paid"))
            ->first();
        foreach ($payments as $payment) {
            $ref_no = in_array($payment->transaction_type, ['sell', 'sell_return']) ?  $payment->invoice_no :  $payment->ref_no;
            $ledger[] = [
                'date' => $payment->paid_on,
                'ref_no' => $payment->payment_ref_no,
                'type' => $transaction_types['payment'],
                'location' => $payment->location_name,
                'payment_status' => '',
                'total' => '',
                'payment_method' => !empty($paymentTypes[$payment->method]) ? $paymentTypes[$payment->method] : '',
                'debit' => in_array($payment->transaction_type, ['purchase', 'sell_return']) ? $payment->amount : '',
                'credit' => in_array($payment->transaction_type, ['sell', 'purchase_return', 'opening_balance']) ? $payment->amount : '',
                'others' => $payment->note . '<small>' . __('account.payment_for') . ': ' . $ref_no . '</small>'
            ];
            if ($contact->type == "supplier") {
            }
        }

        $obDate = Transaction::where('type','opening_balance')->where('contact_id',$contact_id)->select('transaction_date')->first();
        if(!empty($obDate)){
            $opening_date = $obDate->transaction_date;
        }else{
            $opening_date = null;
        }

        $total_ob_paid = $payments->where('transaction_type', 'opening_balance')->sum('amount');
        $total_invoice_paid = $payments->whereIn('transaction_type', ['sell','route_operation'])->sum('amount');
        $total_purchase_paid = $payments->where('transaction_type', 'purchase')->sum('amount');
        $start_date = $this->commonUtil->format_date($start);
        $end_date = $this->commonUtil->format_date($end);
        $total_invoice = $invoice_sum - $sell_return_sum;
        $total_purchase = $purchase_sum - $purchase_return_sum;
        $total_prev_invoice = $previous_transaction_sums->total_purchase + $previous_transaction_sums->total_invoice -  $previous_transaction_sums->total_sell_return -  $previous_transaction_sums->total_purchase_return;
        $total_prev_paid = $prev_payments_sum->total_paid;
        $beginning_balance = ($previous_transaction_sums->opening_balance + $previous_transaction_sums->fleet_opening_balance + $total_prev_invoice) - $prev_payments_sum->amount - $total_prev_paid;
        $total_paid = $total_invoice_paid + $total_purchase_paid + $total_ob_paid;
        $curr_due =  ($beginning_balance + $total_invoice + $total_purchase + $opening_balance_sum + $returned_cheques + $returned_cheque_charges) - $total_paid;

        //Sort by date
        if (!empty($ledger)) {
            usort($ledger, function ($a, $b) {
                $t1 = strtotime($a['date']);
                $t2 = strtotime($b['date']);
                return $t2 - $t1;
            });
        }
        $output = [
            'ledger' => $ledger,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'total_invoice' => $total_invoice,
            'total_purchase' => $total_purchase,
            'beginning_balance' => $beginning_balance,
            'opening_balance' => $ob,
            'total_paid' => $total_paid,
            'balance_due' => $curr_due,
            'opening_date' => $opening_date,
            'returned_cheques' => $returned_cheques,
            'returned_cheque_charges' => $returned_cheque_charges
        ];


        return $output;
    }

    /**
     * Query to get transaction totals for a customer
     *
     */
    private function __transactionQuery($contact_id, $start, $end = null)
    {
        $business_id = request()->session()->get('user.business_id');
        $transaction_type_keys = array_keys(Transaction::transactionTypes());
        $query = Transaction::where('transactions.contact_id', $contact_id)
            ->where('transactions.business_id', $business_id)
            ->where('status', '!=', 'draft')
            ->whereIn('type', $transaction_type_keys);
        if (!empty($start)  && !empty($end)) {
            $query->whereDate(
                'transactions.transaction_date',
                '>=',
                $start
            )
                ->whereDate('transactions.transaction_date', '<=', $end)->get();
        }
        if (!empty($start)  && empty($end)) {
           $query->whereDate('transactions.transaction_date', '<', $start);
        }
        return $query;
    }
    /**
     * Query to get payment details for a customer
     *
     */
    private function __paymentQuery($contact_id, $start, $end = null)
    {
        $business_id = request()->session()->get('user.business_id');
        $query = TransactionPayment::join(
            'transactions as t',
            'transaction_payments.transaction_id',
            '=',
            't.id'
        )
            ->leftJoin('business_locations as bl', 't.location_id', '=', 'bl.id')
            ->where('t.contact_id', $contact_id)
            ->where('t.business_id', $business_id)
            ->where('t.status', '!=', 'draft');
        if (!empty($start)  && !empty($end)) {
            $query->whereDate('transaction_payments.paid_on', '>=', $start)
                ->whereDate('transaction_payments.paid_on', '<=', $end);
        }
        if (!empty($start)  && empty($end)) {
            $query->whereDate('transaction_payments.paid_on', '<', $start);
        }
        return $query;
    }
    /**
     * Function to send ledger notification
     *
     */
    public function sendLedger(Request $request)
    {
        $notAllowed = $this->notificationUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            return $notAllowed;
        }
        try {
            $data = $request->only(['to_email', 'subject', 'email_body', 'cc', 'bcc']);
            $emails_array = array_map('trim', explode(',', $data['to_email']));
            $contact_id = $request->input('contact_id');
            $business_id = request()->session()->get('business.id');
            $start_date = request()->input('start_date');
            $end_date =  request()->input('end_date');
            $contact = Contact::find($contact_id);
            $asset_account_id = Account::leftjoin('account_types', 'accounts.account_type_id', 'accounts.id')
                ->where('account_types.name', 'like', '%Assets%')
                ->where('accounts.business_id', $business_id)
                ->pluck('accounts.id')->toArray();
            $ledger_details = $this->__getLedgerDetails($contact_id, $start_date, $end_date);
            $business_details = $this->businessUtil->getDetails($contact->business_id);
            $location_details = BusinessLocation::where('business_id', $contact->business_id)->first();
            $opening_balance = Transaction::where('contact_id', $contact_id)->where('type', 'opening_balance')->where('payment_status', 'due')->sum('final_total');
            if ($contact->type == 'supplier') {
                $query = AccountTransaction::leftjoin('transactions', 'account_transactions.transaction_id', 'transactions.id')
                    ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                    ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
                    ->where('transactions.type', 'purchase')
                    ->orWhere('transactions.type', 'opening_balance')
                    ->where('contact_id', $contact_id)
                    ->whereNotIn('account_transactions.account_id', $asset_account_id)
                    ->select(
                        'account_transactions.*',
                        'account_transactions.type as acc_transaction_type',
                        'business_locations.name as location_name',
                        'transactions.ref_no',
                        'transactions.transaction_date',
                        'transactions.payment_status',
                        'transaction_payments.method as payment_method',
                        'transactions.type as transaction_type',
                        DB::raw('(SELECT SUM(IF(AT.type="credit", -1 * AT.amount, AT.amount)) from account_transactions as AT WHERE AT.operation_date <= account_transactions.operation_date AND AT.account_id  =account_transactions.account_id AND AT.deleted_at IS NULL AND AT.id <= account_transactions.id) as balance')
                    );
            }
            if ($contact->type == 'customer') {
                $query = AccountTransaction::leftjoin('transactions', 'account_transactions.transaction_id', 'transactions.id')
                    ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                    ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
                    ->leftjoin('accounts', 'account_transactions.account_id', 'accounts.id')
                    ->where('transactions.type', 'sell')
                    ->orWhere('transactions.type', 'opening_balance')
                    // ->orWhere('transactions.type', 'sell_return')
                    ->where('contact_id', $contact_id)
                    // ->whereNull('accounts.asset_type')
                    ->select(
                        'account_transactions.*',
                        'account_transactions.type as acc_transaction_type',
                        'business_locations.name as location_name',
                        'transactions.ref_no',
                        'transactions.transaction_date',
                        'transactions.payment_status',
                        'transaction_payments.method as payment_method',
                        'transactions.type as transaction_type',
                        DB::raw('(SELECT SUM(IF(AT.type="credit", -1 * AT.amount, AT.amount)) from account_transactions as AT WHERE AT.operation_date <= account_transactions.operation_date AND AT.account_id  =account_transactions.account_id AND AT.deleted_at IS NULL AND AT.id <= account_transactions.id) as balance')
                    );
            }
            if (!empty($start_date)  && !empty($end_date)) {
                $query->whereDate(
                    'transactions.transaction_date',
                    '>=',
                    $start_date
                )->whereDate('transactions.transaction_date', '<=', $end_date)->get();
            }
            $ledger_transactions = $query->get();
            $orig_data = [
                'email_body' => $data['email_body'],
                'subject' => $data['subject']
            ];
            $tag_replaced_data = $this->notificationUtil->replaceTags($business_id, $orig_data, null, $contact);
            $data['email_body'] = $tag_replaced_data['email_body'];
            $data['subject'] = $tag_replaced_data['subject'];
            //replace balance_due
            $data['email_body'] = str_replace('{balance_due}', $this->notificationUtil->num_f($ledger_details['balance_due']), $data['email_body']);
            $data['email_settings'] = request()->session()->get('business.email_settings');
            $for_pdf = true;
            $html = view('contact.ledger')
                ->with(compact('ledger_details', 'contact', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details'))->render();
            $mpdf = $this->getMpdf();
            $mpdf->WriteHTML($html);
            $file = config('constants.mpdf_temp_path') . '/' . time() . '_ledger.pdf';
            $mpdf->Output($file, 'F');
            $data['attachment'] =  $file;
            $data['attachment_name'] =  'ledger.pdf';
            \Notification::route('mail', $emails_array)
                ->notify(new CustomerNotification($data));
            if (file_exists($file)) {
                unlink($file);
            }
            $output = ['success' => 1, 'msg' => __('lang_v1.notification_sent_successfully')];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => "File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage()
            ];
        }
        return $output;
    }
    /**
     * Function to get product stock details for a supplier
     *
     */
    public function getSupplierStockReport($supplier_id)
    {
        $pl_query_string = $this->commonUtil->get_pl_quantity_sum_string();
        $query = PurchaseLine::join('transactions as t', 't.id', '=', 'purchase_lines.transaction_id')
            ->join('products as p', 'p.id', '=', 'purchase_lines.product_id')
            ->join('variations as v', 'v.id', '=', 'purchase_lines.variation_id')
            ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
            ->join('units as u', 'p.unit_id', '=', 'u.id')
            ->where('t.type', 'purchase')
            ->where('t.contact_id', $supplier_id)
            ->select(
                'p.name as product_name',
                'v.name as variation_name',
                'pv.name as product_variation_name',
                'p.type as product_type',
                'u.short_name as product_unit',
                'v.sub_sku',
                't.invoice_no',
                DB::raw('SUM(quantity) as purchase_quantity'),
                DB::raw('SUM(quantity_returned) as total_quantity_returned'),
                DB::raw('SUM(quantity_sold) as total_quantity_sold'),
                DB::raw("SUM( COALESCE(quantity - ($pl_query_string), 0) * purchase_price_inc_tax) as stock_price"),
                DB::raw("SUM( COALESCE(quantity - ($pl_query_string), 0)) as current_stock")
            )->groupBy('purchase_lines.variation_id');
        if (!empty(request()->location_id)) {
            $query->where('t.location_id', request()->location_id);
        }
        
        if (!empty(request()->product_id)) {
            $query->where('p.id', request()->product_id);
        }
        
        $product_stocks =  Datatables::of($query)
            ->editColumn('product_name', function ($row) {
                $name = $row->product_name;
                if ($row->product_type == 'variable') {
                    $name .= ' - ' . $row->product_variation_name . '-' . $row->variation_name;
                }
                return $name . ' (' . $row->sub_sku . ')';
            })
            ->editColumn('purchase_quantity', function ($row) {
                $purchase_quantity = 0;
                if ($row->purchase_quantity) {
                    $purchase_quantity =  (float) $row->purchase_quantity;
                }
                return '<span data-is_quantity="true" class="display_currency" data-currency_symbol=false  data-orig-value="' . $purchase_quantity . '" data-unit="' . $row->product_unit . '" >' . $purchase_quantity . '</span> ' . $row->product_unit;
            })
            ->editColumn('total_quantity_sold', function ($row) {
                $total_quantity_sold = 0;
                if ($row->total_quantity_sold) {
                    $total_quantity_sold =  (float) $row->total_quantity_sold;
                }
                return '<span data-is_quantity="true" class="display_currency" data-currency_symbol=false  data-orig-value="' . $total_quantity_sold . '" data-unit="' . $row->product_unit . '" >' . $total_quantity_sold . '</span> ' . $row->product_unit;
            })
            ->editColumn('stock_price', function ($row) {
                $stock_price = 0;
                if ($row->stock_price) {
                    $stock_price =  (float) $row->stock_price;
                }
                return '<span class="display_currency" data-currency_symbol=true >' . $stock_price . '</span> ';
            })
            ->editColumn('current_stock', function ($row) {
                $current_stock = 0;
                if ($row->current_stock) {
                    $current_stock =  (float) $row->current_stock;
                }
                return '<span data-is_quantity="true" class="display_currency" data-currency_symbol=false  data-orig-value="' . $current_stock . '" data-unit="' . $row->product_unit . '" >' . $current_stock . '</span> ' . $row->product_unit;
            });
        return $product_stocks->rawColumns(['current_stock', 'stock_price', 'total_quantity_sold', 'purchase_quantity'])->make(true);
    }
    public function toggleActivate($contact_id)
    {
        $contact = Contact::findOrFail($contact_id);
        $active_status = $contact->active;
        $contact->active = !$active_status;
        $contact->save();
        if ($active_status) {
            $output = ['success' => 1, 'msg' => __('lang_v1.contact_deactivate_success')];
        } else {
            $output = ['success' => 1, 'msg' => __('lang_v1.contact_activate_success')];
        }
        return redirect()->back()->with('status', $output);
    }
    public function listSecurityDeposit($contact_id)
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $security_deposit = Transaction::leftjoin('users', 'transactions.created_by', 'users.id')
                ->where('transactions.business_id', $business_id)->where('transactions.contact_id', $contact_id)
                ->whereIn('transactions.type', ['security_deposit', 'refund_security_deposit','security_deposit_refund'])
                ->select('transactions.transaction_date', 'users.username', 'transactions.final_total', 'transactions.type', 'transactions.ref_no');

            $business_details = Business::find($business_id);
            return Datatables::of($security_deposit)
                // ->editColumn('final_total', function ($row) use ($business_details) {
                //     $amount = 0;

                //     if ($row->type == "security_deposit") {
                //         $amount = $row->final_total;
                //     }
                //     if ($row->type == "refund_security_deposit") {
                //         $amount = -1 * $row->final_total;
                //     }
                //     return '<span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="' . $amount . '">' . $amount . '</sapn>';
                // })
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->addColumn('description', function ($row) {
                    $details = '';
                    if ($row->type == 'security_deposit') {
                        $details .= 'Security Deposit <br>';
                    }
                    if ($row->type == 'refund_security_deposit' || $row->type == 'security_deposit_refund') {
                        $details .= 'Security Deposit Refund <br>';
                    }
                    $details .= 'Ref No: ' . $row->ref_no;
                    return $details;
                })->addColumn('debit', function ($row)  use ($business_details) {
                    $amount = 0;

                    if ($row->type == "refund_security_deposit" || $row->type == 'security_deposit_refund') {
                        $amount =  $row->final_total;
                        return '<span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="' . $amount . '">' . $amount . '</sapn>';
                    }
                    return '';

                })->addColumn('credit', function ($row)  use ($business_details) {
                    $amount = 0;

                    if ($row->type == "security_deposit") {
                        $amount = $row->final_total;
                        return '<span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="' . $amount . '">' . $amount . '</sapn>';
                    }

                    return '';
                })

                ->rawColumns(['debit','credit', 'description'])
                ->make(true);
        }
    }
    public function getOutstandingReceivedReport()
    {
        $business_id = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id);
        $suppliers = Contact::suppliersDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);
        $payment_types = $payment_types =  $this->transactionUtil->payment_types(null, false, false, false, false, true, "is_sale_enabled");
        $customer_group = ContactGroup::forDropdown($business_id, false, true);
        $types = Contact::typeDropdown(true);
        // $bill_nos = Transaction::invoiveNumberDropDown('sell');
        // $payment_ref_nos = Transaction::paymentRefNumberDropDown('sell');
        // $cheque_numbers = Transaction::chequeNumberDropDown('sell');
        
        $payment_pages = $this->transactionUtil->payment_transaction_types;
        
        return view('contact.outstanding_received_report')->with(compact(
            'suppliers',
            'business_locations',
            'customers',
            'customer_group',
            'types',
            'payment_types',
            // 'bill_nos',
            // 'payment_ref_nos',
            // 'cheque_numbers',
            'payment_pages'
        ));
    }
    
    public function getOutstandingFilters(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        // $bill_nos = $this->transactionUtil->getOutstandingInvoices($start_date,$end_date);
        $payment_ref_nos = $this->transactionUtil->getOutstandingPaymentRefs($start_date,$end_date);
        $cheque_numbers = $this->transactionUtil->getOutstandingCheques($start_date,$end_date);
        
        return array(
            // 'bill_nos' => $bill_nos,
            'payment_ref_nos' => $payment_ref_nos,
            'cheque_numbers' => $cheque_numbers,
        );
    }
    
    public function getIssuedPaymentDetails()
    {
        $business_id = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id);
        $suppliers = Contact::suppliersDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);
        $payment_types = $this->transactionUtil->payment_types();
        $customer_group = ContactGroup::forDropdown($business_id, false, true, 'supplier');
        $types = Contact::typeDropdown(true);
        $bill_nos = Transaction::invoiveNumberDropDown('purchase');
        $payment_ref_nos = Transaction::paymentRefNumberDropDown('purchase');
        $cheque_numbers = Transaction::chequeNumberDropDown('purchase');
        $business_details = Business::find($business_id);
        if (request()->ajax()) {
            $purchase = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->leftJoin('transaction_payments as tp', 'transactions.id', '=', 'tp.transaction_id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'purchase')
                ->where('contacts.type', 'supplier')
                ->whereIn('transactions.payment_status', ['paid', 'partial'])
                ->select(
                    'transactions.id',
                    'transactions.transaction_date',
                    'transactions.invoice_no',
                    'contacts.name',
                    'transactions.payment_status',
                    'transactions.final_total',
                    'tp.paid_on',
                    'tp.method',
                    'tp.cheque_number',
                    'tp.card_number',
                    'tp.account_id',
                    'tp.payment_ref_no',
                    DB::raw('SUM(tp.amount) as total_paid'),
                    'tp.created_at'
                );
            if (!empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $purchase->where('contacts.id', $customer_id);
            }
            if (!empty(request()->bill_no)) {
                $purchase->where('transactions.invoice_no', request()->bill_no);
            }
            if (!empty(request()->payment_ref_no)) {
                $purchase->where('tp.payment_ref_no', request()->payment_ref_no);
            }
            if (!empty(request()->cheque_number)) {
                $purchase->where('tp.cheque_number', request()->cheque_number);
            }
            if (!empty(request()->payment_type)) {
                $purchase->where('tp.method', request()->payment_type);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $purchase->whereDate('tp.paid_on', '>=', $start)
                    ->whereDate('tp.paid_on', '<=', $end);
            }
            $purchase->orderBy('tp.paid_on', 'desc')->groupBy('tp.id');
            $datatable = Datatables::of($purchase)
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
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">';
                                    
                        if (auth()->user()->can("purchase.view")) {
                            $html .= '<li><a href="#" data-href="' . action('PurchaseController@show', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>';
                        }
                    
                        
                        if (auth()->user()->can("purchase.view")) {
                            $html .= '<li><a href="#" class="print-invoice" data-href="' . action('PurchaseController@printInvoice', [$row->id]) . '"><i class="fa fa-print" aria-hidden="true"></i>' . __("messages.print") . '</a></li>';
                        }
                        if (auth()->user()->can("purchase.update") && empty($row->purchase_entry_no)) {
                            $html .= '<li><a href="' . action('PurchaseController@edit', [$row->id]) . '"><i class="glyphicon glyphicon-edit"></i>' . __("messages.edit") . '</a></li>';
                        }
                        
                        if (auth()->user()->can("purchase.delete")) {
                            $html .= '<li><a href="' . action('PurchaseController@destroy', [$row->id]) . '" class="delete-purchase"><i class="fa fa-trash"></i>' . __("messages.delete") . '</a></li>';
                        }
    
                        $html .= '<li><a href="' . action('LabelsController@show') . '?purchase_id=' . $row->id . '" data-toggle="tooltip" title="Print Barcode/Label"><i class="fa fa-barcode"></i>' . __('barcode.labels') . '</a></li>';
    

                        $html .= '</ul></div>';
                        return $html;
                    }
                )
                ->removeColumn('id')
                ->editColumn('final_total', function ($row) use ($business_details) {
                    return '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="' . $row->final_total . '">' . $this->productUtil->num_f($row->final_total, false, $business_details, false) . '</span>';
                })
                ->editColumn('total_paid', function ($row) use ($business_details) {
                    if ($row->total_paid == '') {
                        $total_paid_html = '<span class="display_currency total-paid" data-currency_symbol="true" data-orig-value="0.00">' . $this->productUtil->num_f(0, false, $business_details, false) . '</span>';
                    } else {
                        $total_paid_html = '<span class="display_currency total-paid" data-currency_symbol="true" data-orig-value="' . $row->total_paid . '">' . $this->productUtil->num_f($row->total_paid, false, $business_details, false) . '</span>';
                    }
                    return $total_paid_html;
                })
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('paid_on', '{{@format_date($paid_on)}}')
                ->editColumn('created_at', '{{@format_datetime($created_at)}}')
                ->editColumn('method', function ($row) {
                    $html = '';
                    if (strtolower($row->method) == 'bank_transfer' || strtolower($row->method) == 'direct_bank_deposit' || strtolower($row->method) == 'bank' || strtolower($row->method) == 'cheque') {
                        $html .= "Bank";

                        $bank_acccount = Account::find($row->account_id);
                        if (!empty($bank_acccount)) {
                            $html .= '<br><b>Bank Name:</b> ' . $bank_acccount->name . '</br>';
                        }
                        if(!empty($row->cheque_number)){
                            $html .= '<b>Cheque Number:</b> ' . $row->cheque_number . '</br>';
                        }
                        if(!empty($row->cheque_date)){
                            $html .= '<b>Cheque Date:</b> ' . $this->productUtil->format_date($row->cheque_date) . '</br>';
                        }

                    } else {
                        $html .= ucfirst(str_replace("_"," ",$row->method));
                    }

                    return $html;
                })
                ->editColumn('cheque_number', function ($row) {
                    if ($row->method == 'bank_transfer' || $row->method == 'cheque') {
                        return $row->cheque_number;
                    }
                    if ($row->method == 'card') {
                        return $row->card_number;
                    }
                    return '';
                })
                ->editColumn('invoice_no', function ($row) {
                    $invoice_no = $row->invoice_no;
                    if (!empty($row->woocommerce_order_id)) {
                        $invoice_no .= ' <i class="fa fa-wordpress text-primary no-print" title="' . __('lang_v1.synced_from_woocommerce') . '"></i>';
                    }
                    if (!empty($row->return_exists)) {
                        $invoice_no .= ' &nbsp;<small class="label bg-red label-round no-print" title="' . __('lang_v1.some_qty_returned_from_sell') . '"><i class="fa fa-undo"></i></small>';
                    }
                    if (!empty($row->is_recurring)) {
                        $invoice_no .= ' &nbsp;<small class="label bg-red label-round no-print" title="' . __('lang_v1.subscribed_invoice') . '"><i class="fa fa-recycle"></i></small>';
                    }
                    if (!empty($row->recur_parent_id)) {
                        $invoice_no .= ' &nbsp;<small class="label bg-info label-round no-print" title="' . __('lang_v1.subscription_invoice') . '"><i class="fa fa-recycle"></i></small>';
                    }
                    return $invoice_no;
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view") || auth()->user()->can("view_own_sell_only")) {
                            return  action('SellController@show', [$row->id]);
                        } else {
                            return '';
                        }
                    }
                ]);
            $rawColumns = ['method','final_total', 'action', 'total_paid', 'total_remaining', 'payment_status', 'invoice_no', 'discount_amount', 'tax_amount', 'total_before_tax', 'shipping_status'];
            return $datatable->rawColumns($rawColumns)
                ->make(true);
        }
        return view('contact.issued_payment_details')->with(compact(
            'suppliers',
            'business_locations',
            'customers',
            'customer_group',
            'types',
            'payment_types',
            'bill_nos',
            'payment_ref_nos',
            'cheque_numbers'
        ));
    }
     /**
     * Retrieves list of account sub types based on account types.
     *
     * @param  string  $acc_type
     * @return JSON
     */
    public function getAccSubType()
    {
        if (request()->ajax()) {
            $acc_type_id = request()->input('acc_type_id', '');
            $business_id = request()->session()->get('user.business_id');
            $accounts = AccountType::select('name','id')
                        ->where('business_id', $business_id)
                        ->where('parent_account_type_id',$acc_type_id);
            $acc_sub_types = $accounts->get();
            return json_encode($acc_sub_types);
        }
    }
    /**
     * Retrieves list of discount account sub types based on account sub types.
     *
     * @param  string  $acc_type
     * @return JSON
     */
    public function getDiscountAcc()
    {
        if (request()->ajax()) {
            $acc_type_id = request()->input('acc_type_id', '');
            $acc_sub_type_id = request()->input('acc_sub_type_id', '');
            $business_id = request()->session()->get('user.business_id');
            $accounts = Account::select('name','id')
                        ->where('business_id', $business_id)
                        ->whereNull('deleted_at')
                        ->where('disabled',0);
            if( (!empty($acc_sub_type_id) && $acc_sub_type_id) && $acc_type_id ){
                $accounts->where('account_type_id',$acc_sub_type_id);
            }
            if( (!empty($acc_type_id) && $acc_type_id) && !$acc_sub_type_id ){
                $accounts->where('account_type_id',$acc_type_id);
            }
            $discount_accounts = $accounts->get();
            return json_encode($discount_accounts);
        }
    }

    public function checkMobile(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');

        $mobile_number = $request->input('mobile_number');
        //update by sakhawt 
        $contacts = [];
        if($mobile_number){

            $query = Contact::where('business_id', $business_id)
                            ->where('mobile', 'like', "%{$mobile_number}");

            if (!empty($request->input('contact_id'))) {
                $query->where('id', '!=', $request->input('contact_id'));
            }

            $contacts = $query->pluck('name')->toArray();
        }
        return [
            'is_mobile_exists' => !empty($contacts),
            'msg' => __('lang_v1.mobile_already_registered', ['contacts' => implode(', ', $contacts), 'mobile' => $mobile_number])
        ];
    }

    public function getContacts(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $search = $request->input('search', '');
        $page = $request->input('page', 1);
        $perPage = 10; // Number of contacts per page

        $query = Contact::where('business_id', $business_id);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                ->orWhere('contact_id', 'like', "%$search%")
                ->orWhere('supplier_business_name', 'like', "%$search%");
            });
        }

        $query->select(
            DB::raw("IF(contact_id IS NULL OR contact_id='', name, CONCAT(name, ' - ', COALESCE(supplier_business_name, ''), '(', contact_id, ')')) AS supplier"),
            'id'
        );

        $total = $query->count();
        $contacts = $query->skip(($page - 1) * $perPage)
        ->take($perPage)
        ->get();

        return response()->json([
            'results' => $contacts->map(function ($contact) {
                return [
                    'id' => $contact->id,
                    'text' => $contact->supplier,
                ];
            }),
            'pagination' => [
                'more' => ($page * $perPage) < $total, // Indicates if there are more results
            ],
        ]);
    }
}