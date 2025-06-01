<?php



namespace Modules\Airline\Http\Controllers;



use App\Customer;

use App\BusinessLocation;

use App\User;

use Illuminate\Http\Request;

use Illuminate\Routing\Controller;

use App\Account;

use App\AccountGroup;
use App\AccountType;
use App\Transaction;

use App\TransactionPayment;

use Illuminate\Support\Facades\Auth;

use App\ContactLedger;

use App\ContactGroup;

use App\Contact;

use App\AirlineSuppliers;

use App\Media;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\File; // Import the File class

use Illuminate\Support\Str;

use Modules\Airline\Entities\Airline;

use Modules\Airline\Entities\AirlineAgent;

use Modules\Airline\Entities\AirlineAirports;

use Modules\Airline\Entities\AirlineTicketInvoices;

use Modules\Airline\Entities\AirTicketInvoice;

use Modules\Airline\Entities\AirlinePassengers;
use Modules\Airline\Entities\AirlineFormSettingPassenger;

use Modules\Airline\Entities\AdditionalService;

use Illuminate\Support\Facades\DB;

use Modules\Airline\Entities\AirlineCommissionType;

use Modules\Airline\Entities\AirlineAddCommission;

use Modules\Airline\Entities\AirlineLinkedAccount;

use Modules\Airline\Entities\AirlinePrefixStarting;

use Modules\Airline\Entities\AirlinePassengerType;

use Modules\Airline\Entities\AirlineClasses;



use App\Utils\BusinessUtil;

use App\Utils\ModuleUtil;

use App\Utils\ProductUtil;

use App\Utils\TransactionUtil;

use App\Utils\Util;





use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Validator;

use Mpdf\Mpdf;

use Illuminate\Support\Facades\Mail;

use App\Mail\SendInvoiceMail;




use Yajra\DataTables\Facades\DataTables;



class AirlineTicketingController extends Controller

{

    protected $commonUtil;

    protected $moduleUtil;

    protected $productUtil;

    protected $transactionUtil;

    protected $businessUtil;

    

    

    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil)

    {

        $this->commonUtil = $commonUtil;

        $this->moduleUtil =  $moduleUtil;

        $this->productUtil =  $productUtil;

        $this->transactionUtil =  $transactionUtil;

        $this->businessUtil =  $businessUtil;



        $this->dummyPaymentLine = [

            'method' => 'cash', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'cheque_date' => '', 'bank_account_number' => '',

            'is_return' => 0, 'transaction_no' => '', 'account_id' => ''

        ];

    }





    /**

     * Display a listing of the resource.

     */

     

    public function get_transit($transaction_id){

        return AirlineTicketInvoices::where('transaction_id', $transaction_id)->select('note')->first();

    }

    

    public function fetchCountries(){

        $countries = DB::table('countries')->get();

        

        return $countries;

    }

    

    public function index()

    {

        

        $payment_status = ['partial' => 'Partial', 'due' => 'Due', 'paid' => 'Paid'];

        $business_id = request()->session()->get('user.business_id');

          

        if (request()->ajax()) {

       

            $route_operations = Transaction::leftjoin('air_ticket_invoices', 'transactions.id', 'air_ticket_invoices.transaction_id')

                ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')

                ->leftjoin('contact_groups', 'air_ticket_invoices.customer_group', 'contact_groups.id')

                ->leftjoin('contacts', 'air_ticket_invoices.customer', 'contacts.id')

                ->leftjoin('airlines', 'air_ticket_invoices.airline', 'airlines.id')

                ->leftjoin('airline_agents', 'air_ticket_invoices.airline_agent', 'airline_agents.id')

                ->where('transactions.type', 'airline_ticket')

                ->where('air_ticket_invoices.business_id', $business_id)

                ->select([

                    'transactions.payment_status',

                    'air_ticket_invoices.*',

                    'contact_groups.name as customer_grp',

                    'contacts.name as customer_name',

                    'airlines.airline as airline_name',

                    'airline_agents.agent as airline_agent_name',

                    'transactions.id as t_id',

                    'transactions.transaction_date'

                ])->groupBy('air_ticket_invoices.id');



            if (!empty(request()->start_date) && !empty(request()->end_date)) {

                $route_operations->whereDate('transactions.transaction_date', '>=', request()->start_date);

                $route_operations->whereDate('transactions.transaction_date', '<=', request()->end_date);

            }

            

            if (!empty(request()->payment_status)) {

                $route_operations->where('transactions.payment_status', request()->payment_status);

            }

            

            if (!empty(request()->customer_group)) {

                $route_operations->where('air_ticket_invoices.customer_group', request()->customer_group);

            }

            

            if (!empty(request()->customer)) {

                $route_operations->where('air_ticket_invoices.customer', request()->customer);

            }

            

            if (!empty(request()->airline_agent)) {

                $route_operations->where('air_ticket_invoices.airline_agent', request()->airline_agent);

            }

            

            if (!empty(request()->departure_country)) {

                $route_operations->where('air_ticket_invoices.departure_country', request()->departure_country);

            }

            

            if (!empty(request()->arrival_country)) {

                $route_operations->where('air_ticket_invoices.arrival_country', request()->arrival_country);

            }

            

            if (isset(request()->transit)) {

                $route_operations->where('air_ticket_invoices.transit', request()->transit);

            }

            

            

            



            return DataTables::of($route_operations)

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

                        $html .= '<li><a data-href="' . action('\Modules\Airline\Http\Controllers\AirlineTicketingController@show', [$row->id]) . '" " class="btn-modal" data-container=".fleet_model"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.view") . '</a></li>';

                        $html .= '<li><a href="#" data-href="' . action('\Modules\Airline\Http\Controllers\AirlineTicketingController@destroy', [$row->t_id]) . '" class="delete-fleet"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';

                        

                       

                        

                        if ($row->payment_status != 'paid') {

                            $html .= '<li class="divider"></li>';

                            $html .= '<li><a href="' . action('TransactionPaymentController@addPayment', [$row->t_id]) . '" class="add_payment_modal"><i class="fa fa-money" aria-hidden="true"></i>' . __("purchase.add_payment") . '</a></li>';

                        }

                        

                        

                        $html .= '<li><a href="' . action('TransactionPaymentController@show', [$row->t_id]) . '"  class="view_payment_modal"><i class="fa fa-money" aria-hidden="true"></i> ' . __("airline::lang.payments") . '</a></li>';

                        

                        return $html;

                    }

                )

                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')

                

                ->editColumn('payment_status', function ($row) {

                    $payment_status = Transaction::getPaymentStatus($row);

                    return (string) view('sell.partials.payment_status', ['payment_status' => $payment_status, 'id' => $row->t_id, 'for_purchase' => true]);

                })

                

                ->editColumn('method', function ($row) {

                    $html = '';

                    

                    if ($row->payment_status == 'due') {

                        return 'Credit Sale';

                    }

                    

                    

                    $mthds = TransactionPayment::where('transaction_id',$row->t_id)->get();

                    

                    foreach($mthds as $mthd){

                        if ($mthd->method == 'bank_transfer' || $mthd->method == 'bank') {

                            $html .= ucfirst($mthd->method)."<br>";

                            $bank_acccount = Account::find($mthd->account_id);

                            if (!empty($bank_acccount)) {

                                $html .= '<b>Bank Name:</b> ' . $bank_acccount->name . '</br>';

                                $html .= '<b>Account Number:</b> ' . $bank_acccount->account_number . '</br>';

                            }

                        } else {

                            $html .= ucfirst($mthd->method)."<br>";

                        }

                    }

                    

                    



                    return $html;

                })

                

                ->editColumn('transit',function($row){

                    if($row->transit == 1){

                        return "Yes";

                    }else{

                        return "No";

                    }

                })

                

                ->addColumn('passengers',function($row){

                    $passengers = AirlinePassengers::where('invoice_id',$row->id)->count();

                    

                    return $passengers;

                })

                ->rawColumns(['action', 'payment_status', 'method'])

                ->make(true);

        }

      

        return view('airline::airline.index')->with(compact(

            'payment_status'

        ));

    }

     public function list_commission(Request $request)

    {

       

       

       

        $business_id = request()->session()->get('user.business_id');

          

        

        $business_locations = BusinessLocation::forDropdown($business_id);

        $agent= AirlineAgent::select('id', 'agent')->pluck('agent', 'id');

        

          $invoice_no= AirlineTicketInvoices::select('id', 'airline_invoice_no')->pluck('airline_invoice_no', 'id');

           $ticket_no= AirlineTicketInvoices::select('id', 'airticket_no')->pluck('airticket_no', 'id');

         // $location=   $request->location;

           $add_commission=AirlineAddCommission::all();//where('airline_add_commissions.location',$location)->get();

           

         

      

        return view('airline::add_commission.index')->with(compact(

            'business_locations','agent','invoice_no','ticket_no','add_commission'

        ));

    }

     public function Add_commission_filter(Request $request)

    {

       

       

        $business_id = request()->session()->get('user.business_id');

          

        

        $business_locations = BusinessLocation::forDropdown($business_id);

        $agent= AirlineAgent::select('id', 'agent')->pluck('agent', 'id');

        

        $invoice_no= AirlineTicketInvoices::select('id', 'airline_invoice_no')->pluck('airline_invoice_no', 'id');

        $ticket_no= AirlineTicketInvoices::select('id', 'airticket_no')->pluck('airticket_no', 'id');

        // dd( $request->agent);

        $agent=   $request->agent;

        $location=   $request->location;

        $prt_ticket_no=$request->prt_ticket_no;

        $invoice_no=$request->invoice_no;

         if($agent)

         {

              $add_commission=AirlineAddCommission::where('airline_add_commissions.airline_agent',$agent)->get();

         }

         else if($location)

         {

                $add_commission=AirlineAddCommission::where('airline_add_commissions.location',$location)->get();

         }

          else if($prt_ticket_no)

         {

                $add_commission=AirlineAddCommission::where('airline_add_commissions.ticket_no',$prt_ticket_no)->get();

         }

          else if($invoice_no)

         {

                $add_commission=AirlineAddCommission::where('airline_add_commissions.invoice_no',$invoice_no)->get();

         }

         //  dd($add_commission);

           

         

      

        return response()->json($add_commission);

       

    }

 public function airline_suppliers()

    {



        $type ='supplier';// request()->get('type');

        $types ='supplier';// ['supplier', 'customer'];

        $business_id = request()->session()->get('user.business_id');

         



       /* if (request()->ajax()) {

           return $type == 'supplier' ? $this->indexSupplier() : ($type == 'customer' ? $this->indexCustomer() : abort(404));

        } */

        $reward_enabled = (request()->session()->get('business.enable_rp') == 1 && $type == 'customer');



        // Get contact fields from session or set to empty array

        $contact_fields = session('business.contact_fields', []);



        // Get user groups for dropdown

        $user_groups = User::forDropdown($business_id);



        // Check if it's a property customer

        $is_property = isset($is_property_customer);



        // Check customer code and get contact ID

        $contact_id = $this->businessUtil->check_customer_code($business_id);
        
        
        // Get user groups for dropdown

        $account_groups = AccountGroup::where('business_id', $business_id)->get();



        return view('airline::supplier.index', compact('type', 'reward_enabled', 'contact_fields', 'is_property', 'user_groups', 'contact_id', 'account_groups'));

    }

    /**

     * Show the form for creating a new resource.

     */

    public function create()

    {

        //

    }



    /**

     * Store a newly created resource in storage.

     */

    public function commision_store(Request $request)

    {

          if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {

            abort(403, 'Unauthorized action.');

        }



try {

        $business_id = $request->session()->get('user.business_id');

        if (!$this->moduleUtil->isSubscribed($business_id)) {

        return $this->moduleUtil->expiredResponse();

        }

        $business_id = $request->session()->get('user.business_id');

        $username = User::where('business_id', $business_id)->pluck('username')->first();

        DB::beginTransaction();

        $commisiontype =  $request->commissionType;

        $user=$username;

        //dd($commisiontype);

        if ($commisiontype) {

            

            

            $Data = [

            'commsion_type' => $commisiontype,

            'user' => $user

            ];

            

            $commision = AirlineCommissionType::create($Data);

            $output = [

            'success' => true,

            'data' => $commision,

            'msg' => __("contact.added_success")

            ];

            DB::commit();



       

    } 

} catch (\Exception $e) {

    Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

    $output = [

        'success' => false,

        'msg' => __("messages.something_went_wrong"),

        'error' => $e->getMessage()

    ];

}



return $output;

    }

    

    

    /**

     * Store a newly created resource in storag  adding commission

     */

    public function add_commision_store(Request $request)

    {

          if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {

            abort(403, 'Unauthorized action.');

        }



try {

    

   //dd($request->tableData);

        $business_id = $request->session()->get('user.business_id');

        if (!$this->moduleUtil->isSubscribed($business_id)) {

        return $this->moduleUtil->expiredResponse();

        }

        $business_id = $request->session()->get('user.business_id');

      

         $tableData = json_decode($request->tableData, true);



    foreach ($tableData as $data) {

        $date = $data['dates'];

        $location = $data['location'];

        $airline_agent = $data['airline_agent'];

        $invoice_no = $data['invoice_no'];

        $ticket_no = $data['prt_ticket_no'];

        $air_ticket_date = $data['airTicketDate'];

        $commision_amount = $data['amount'];



        if ($airline_agent) {

            $Data = [

                'date' => $date,

                'location' => $location,

                'airline_agent' => $airline_agent,

                'invoice_no' => $invoice_no,

                'ticket_no' => $ticket_no,

                'air_ticket_date' => $air_ticket_date,

                'commision_amount' => $commision_amount

            ];



            $add_commision = AirlineAddCommission::create($Data);

        }

    }

     $output = [

        'success' => true,

        'msg' => __("Success"),

        'error' => $e->getMessage()

    ];

}

catch (\Exception $e) {

    Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

    $output = [

        'success' => false,

        'msg' => __("messages.something_went_wrong"),

        'error' => $e->getMessage()

    ];

}

 

/* $business_locations = BusinessLocation::forDropdown($business_id);

        $agent= AirlineAgent::select('id', 'agent')->pluck('agent', 'id');

        

          $invoice_no= AirlineTicketInvoices::select('id', 'airline_invoice_no')->pluck('airline_invoice_no', 'id');

           $ticket_no= AirlineTicketInvoices::select('id', 'airticket_no')->pluck('airticket_no', 'id');

           $add_commission=AirlineAddCommission::all();

      

        return view('airline::add_commission.index')->with(compact(

            'business_locations','agent','invoice_no','ticket_no','add_commission'

        ));  */

return $output;

    }

     /**

     * Store a newly created airline ticket accpunt in storage.

     */

    public function save_account(Request $request)

    {

          if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {

            abort(403, 'Unauthorized action.');

        }



try {

    $business_id = $request->session()->get('user.business_id');

   $username = User::where('business_id', $business_id)->pluck('username')->first();

   



    DB::beginTransaction();



    $account_id =  $request->account_id;

    $account_type=$request->account_type;

    $account_name=$request->account_name;

    $accountNumber = Account::where('id', $account_id)->pluck('account_number')->first();

    $existingAccount = AirlineLinkedAccount::where('AccountNumber', $accountNumber)->first();



 



    if (!$existingAccount) {

            

            

                $Data = [

                    'AccountType' => $account_type,

                     'AcoountName' => $account_name,

                      'AccountNumber' => $accountNumber,

                        'user' => $username

                ];



                $account = AirlineLinkedAccount::create($Data);

                    if ($account) {
                        $output = [
                            'success' => true,
                            'data' => $account,
                            'msg' => __("Linked Account sent successfully"),
                        ];
                    } else {
                        // Handle the case where account creation fails
                        $output = [
                            'success' => false,
                            'msg' => __("Failed to create Linked Account")
                        ];
                    }

                DB::commit();

            

       

    } 

    else

    {

          $output = [

        'success' => true,

        'msg' => __("Linked Account Exist!"),

        'error' => $e->getMessage()

    ];

    }

} catch (\Exception $e) {

    Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

    $output = [

        'success' => false,

        'msg' => __("messages.something_went_wrong"),

        'error' => $e->getMessage()

    ];

}



return $output;

    }

    /**

     * Display the specified resource.

     */

    public function show(string $id)

    {

        $ticket = Transaction::leftjoin('air_ticket_invoices', 'transactions.id', 'air_ticket_invoices.transaction_id')

                ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')

                ->leftjoin('contact_groups', 'air_ticket_invoices.customer_group', 'contact_groups.id')
                
                ->leftjoin('contacts', 'air_ticket_invoices.customer', 'contacts.id')

                ->leftjoin('customers', 'air_ticket_invoices.customer', 'customers.id')

                ->leftjoin('airlines', 'air_ticket_invoices.airline', 'airlines.id')

                ->leftjoin('airline_agents', 'air_ticket_invoices.airline_agent', 'airline_agents.id')

                

                ->leftjoin('airline_airports as da','air_ticket_invoices.departure_airport','da.id')

                ->leftjoin('airline_airports as ta','air_ticket_invoices.transit_airport','ta.id')

                ->leftjoin('airline_airports as aa','air_ticket_invoices.arrival_airport','aa.id')

                

                ->where('transactions.type', 'airline_ticket')

                ->where('air_ticket_invoices.id', $id)

                ->select([

                    'aa.airport_name as arrival_airport_name',

                    'ta.airport_name as transit_airport_name',

                    'da.airport_name as departure_airport_name',

                    

                    'transactions.payment_status',

                    'transactions.final_total',

                    'air_ticket_invoices.*',

                    'contact_groups.name as customer_grp',
                    
                    'contacts.name as customer_name',

                    // 'customers.first_name as customer_name',

                    'airlines.airline as airline_name',

                    'airline_agents.agent as airline_agent_name',

                    'transactions.id as t_id',

                    'transactions.transaction_date'

                ])->groupBy('air_ticket_invoices.id')->first();

                

        $passengers = AirlinePassengers::leftjoin('customers','airline_passengers.name','customers.id')->where('airline_passengers.invoice_id',$id)

        ->select(['airline_passengers.*','customers.first_name','customers.nic_number'])

        ->get();

        $tps = TransactionPayment::where('transaction_id',$ticket->t_id)->get();

       

        return view('airline::airline.show')->with(compact(

            'ticket','passengers','tps'

        ));

    }



    /**

     * Show the form for editing the specified resource.

     */

    public function edit(string $id)

    {

        //

    }



    /**

     * Update the specified resource in storage.

     */

    public function update(Request $request, string $id)

    {

        //

    }



    /**

     * Remove the specified resource from storage.

     */

    public function destroy(string $id)

    {

        //

        try {

            

            $transaction = Transaction::where('id', $id)->first();

            

            $ticket = AirlineTicketInvoices::where('transaction_id', $id)->first();

            

            AirlinePassengers::where('invoice_id',$ticket->id)->delete();

            

            Transaction::where('id', $id)->delete();

            AirlineTicketInvoices::where('transaction_id', $id)->delete();

            TransactionPayment::where('transaction_id', $id)->delete();



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



    public function create_invoice() {

        if (!auth()->user()->can('airline.view_setting')) {

            abort(403, 'Unauthorized action.');

        }

        $type = request()->type;

        $business_id = request()->session()->get('user.business_id');

        

        $invoice_prefixes = AirlinePrefixStarting::orderBy('id', 'desc')->select('id', 'mode_id', 'value')->with(['mode' => function($query){

            $query->select('id', 'name');

        }])->get();

        // $airline_supplier= AirlineSuppliers::where('type', 'supplier')->pluck('name', 'id');
        $airline_class = AIrlineClasses::orderBy('id', 'desc')->get();

        $airline_supplier= Contact::where('type', 'supplier')->where('register_module','airline')->pluck('name', 'id');

        // dd($airline_supplier)

        

        $customers = Contact::customersDropdown($business_id, false, true, 'customer');

 

        return view('airline::create_invoice.index')

            ->with(compact('type', 'invoice_prefixes','airline_supplier','customers','airline_class'));

            

            

    }


    public function print_invoice(Request $request) {

        try{

        
             $payment_data = array();
 
             $payment = array();
 
           
 
             foreach(is_null($request->payment_methods_data)?[]:json_decode($request->payment_methods_data,true) as $one){
 
                 $variableName = str_replace(['[', ']', ' '], '-', $one['name']);
 
                 $varArr = explode('-',$variableName);
 
                 $payment[$varArr[1]][$varArr[3]] = $one['value'];
 
             }
 
              foreach(is_null($request->payment_methods_data_supplier)?[]:json_decode($request->payment_methods_data_supplier,true) as $one){
 
                 $variableName = str_replace(['[', ']', ' '], '-', $one['name']);
 
                 $varArr = explode('-',$variableName);
 
                 $payment[$varArr[1]][$varArr[3]] = $one['value'];
 
             }
 
             
 
             $passengers = array();
 
             foreach(isset($request->passengers)?$request->passengers:[] as $one){
 
                 $pass_data = json_decode($one,true);
 
                 $one_passenger = array();
 
                 foreach($pass_data as $i){
 
                     $one_passenger[$i['name']] = $i['value'];
 
                 }
 
                 
 
                 $passengers[] = $one_passenger;
 
             }
 
           
 
          
 
             
 
             $business_id = request()->session()->get('user.business_id');
 
        if(!empty($request->air_ticket_invoice_ids)){
            $air_ticket_invoice_ids_arr = explode(',', $request->air_ticket_invoice_ids);
            $transaction = Transaction::where('id', $request->transaction_id)->first();
            $invoice_new = array();
            foreach($air_ticket_invoice_ids_arr as $air_ticket_invoice_id){
                $invoice_new[] = AirlineTicketInvoices::where('id', $air_ticket_invoice_id)->first();
            }
            $passengers_new = array();
            $airline_passengers_ids_arr = explode(',', $request->airline_passengers_ids);
            foreach($airline_passengers_ids_arr as $airline_passengers_id){
                $passengers_new[] = AirlinePassengers::where('id', $air_ticket_invoice_id)->first();
            }
        } else {
                 $transaction = Transaction::create(
 
                     [
 
                         'type' => 'airline_ticket',
 
                         'status' => 'received',
 
                         'invoice_no' => request('airticket_no'),
 
                         'business_id' => $business_id,
 
                         'transaction_date' => date('Y-m-d'),
 
                         'total_before_tax' => $request->tot_price??0,
 
                         'final_total' => $request->tot_price??0,
 
                         'payment_status' => 'due',
 
                         'contact_id' => $request->customer,
 
                         'parent_transaction_id'=> $request->airline_agent,
 
                         'created_by' => Auth::user()->id
 
                     ]
 
                 );
 
             $this->transactionUtil->createOrUpdatePaymentLines($transaction, $payment);
 
             $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);
 

               if(request('travel_mode') == 'Multicity'){
                
                $multi_class = request('multi_class');
                $multi_adults = request('multi_adults');
                $multi_children = request('multi_children');
                $multi_infants = request('multi_infants');



                $multicity_data = request('multicity');

                $passengers_new = array();
                $invoice_new = array();

                if (is_array($multicity_data) && isset($multicity_data[0])) {
                    $multicity = json_decode($multicity_data[0], true);

                    if (is_array($multicity)) {
                        foreach ($multicity as $multi_data) {
                            $invoice = AirlineTicketInvoices::create([
                                'airticket_no' => request('airticket_no'),
                                'business_id' => $business_id,
                                'transaction_id' => $transaction->id,
                                'customer_group' => request('customer_group'),
                                'customer' => request('customer'),
                                'supplier' => request('supplier'),
                                'airline'  => request('airline'),
                                'airline_invoice_no' => request('airline_invoice_no'),
                                'airline_agent' => request('airline_agent'),
                                'travel_mode' => request('travel_mode'),
                                'departure_country' => $multi_data['origin'], 
                                'departure_airport' => request('departure_airport'),
                                'departure_date' => \DateTime::createFromFormat('m/d/Y', $multi_data['departureDate'])->format('Y-m-d'), 
                                'transit' => request('transit'),
                                'transit_airport' => (request('transit')) ? request('transit_airport') : '',
                                'arrival_country' => $multi_data['destination'], 
                                'arrival_airport' => request('arrival_airport'),
                                'arrival_date' => \DateTime::createFromFormat('m/d/Y', request('arrival_date')),
                                'arrival_time' => \DateTime::createFromFormat('h:m', request('arrival_time')),
                                'total_time' => \DateTime::createFromFormat('h:m', request('total_time')),
                                'transit_time' => \DateTime::createFromFormat('h:m', request('transit_time')),
                                'note' => request('note') ?? null
                            ]);


                            $invoice_new[] = $invoice;
                            

                            foreach($passengers as $passenger_data) {
                
                                $contact = Contact::where('business_id',$business_id)->where('id',$passenger_data['passenger_name'])->first(); 
                
                                    $passenger = AirlinePassengers::create([
                
                                        'invoice_id' => $invoice->id,
                
                                        'name' => $contact->name,
                
                                        'passport_number' => $passenger_data['passport_number'],
                
                                        'passport_image' => $passenger_data['passport_image_link'],
                
                                        'airline_itinerary' => $passenger_data['airline_itinerary_link'] ?? '',
                
                                        'airticket_no' => $passenger_data['airticket_no'],
                
                                        'frequent_flyer_no' => $passenger_data['frequent_flyer_no'],
                
                                        'child' => $passenger_data['child'],
                
                                        'price' => ($passenger_data['child']=='yes')? $passenger_data['price']:0,
                
                                        'passenger_type' => $passenger_data['passenger_type']??null,
                
                                        'expiry_date' => $passenger_data['expiry_date']??null,
                
                                        'additional_service' => $passenger_data['additional_service']??null,
                
                                        'amount' => $passenger_data['amount']
                
                                    ]);
                
                                    $passengers_new[] = $passenger;
                
                                }
                        

                                
                        }
                    } else {
                    }
                } else {
                    
                }


                
               }else{

                $invoice_new = array();

                $invoice = AirlineTicketInvoices::create([
 
                    'airticket_no' => request('airticket_no'),
    
                    'business_id' => $business_id,
    
                    'transaction_id' => $transaction->id,
    
                    'customer_group' => request('customer_group'),
    
                    'customer' => request('customer'),
    
                    'supplier' => request('supplier'),
    
                    'airline'  => request('airline'),
    
                    'airline_invoice_no' => request('airline_invoice_no'),
    
                    'airline_agent' => request('airline_agent'),
    
                    'travel_mode' => request('travel_mode'),
    
                    'departure_country' => request('departure_country'),
    
                    'departure_airport' => request('departure_airport'),

                    'departure_date' => \DateTime::createFromFormat('m/d/Y', request('departure_date'))->format('Y-m-d'),
    
                    'transit' => request('transit'),
    
                    'transit_airport' => (request('transit'))?request('transit_airport'):'',
    
                    'arrival_country' => request('arrival_country'),
    
                    'arrival_airport' => request('arrival_airport'),
    
                    'arrival_date' => \DateTime::createFromFormat('m/d/Y', request('arrival_date')),
    
                    'arrival_time' => \DateTime::createFromFormat('h:m', request('arrival_time')),
    
                    'total_time' => \DateTime::createFromFormat('h:m', request('total_time')),
    
                    'transit_time' => \DateTime::createFromFormat('h:m', request('transit_time')),
    
                    'note' => request('note')??null
    
                ]);     

                $invoice_new[] = $invoice;


                $passengers_new = array();

                foreach($passengers as $passenger_data) {
    
                    $contact = Contact::where('business_id',$business_id)->where('id',$passenger_data['passenger_name'])->first(); 
     
                     $passenger = AirlinePassengers::create([
     
                         'invoice_id' => $invoice->id,
     
                         'name' => $contact->name,
     
                         'passport_number' => $passenger_data['passport_number'],
     
                         'passport_image' => $passenger_data['passport_image_link'],
     
                         'airline_itinerary' => $passenger_data['airline_itinerary_link'] ?? '',
     
                         'airticket_no' => $passenger_data['airticket_no'],
     
                         'frequent_flyer_no' => $passenger_data['frequent_flyer_no'],
     
                         'child' => $passenger_data['child'],
     
                         'price' => ($passenger_data['child']=='yes')? $passenger_data['price']:0,
     
                         'passenger_type' => $passenger_data['passenger_type']??null,
     
                         'expiry_date' => $passenger_data['expiry_date']??null,
     
                         'additional_service' => $passenger_data['additional_service']??null,
     
                         'amount' => $passenger_data['amount']
     
                     ]);
     
                     $passengers_new[] = $passenger;
     
                 }
     



               }
        }
 
             
 
           
            
 
 
             $output = [
 
                 'success' => 1,
 
                 'msg' => __('lang_v1.success')
 
             ];
 
         } catch (QueryException $e) {
 
             $output = [
 
                 'success' => false,
 
                 'msg' => __('messages.something_went_wrong')
 
             ];
 
         }
 
        $excludeSupplierDetails = filter_var($request->input('exclude_supplier_details', false), FILTER_VALIDATE_BOOLEAN);

        $supplier = null; 

        if (!$excludeSupplierDetails) {
            $supplier = Contact::where('id', request('supplier'))->first();
            Log::info('Supplier:', ['supplier' => $supplier]);
        } else {
            Log::info('Skipping supplier fetch because excludeSupplierDetails is true');
        }

        return view('airline::create_invoice.print_invoice', [
            'invoice' => $invoice_new,
            'passenger' => $passengers_new,
            'supplier' => $supplier,
            'payment' => $payment,
            'transaction' => $transaction
        ]);

    }

  public function sendEmailInvoicePDF(Request $request)
    {
        try {
        // Step 1: Get the data for the invoice
        $invoiceData = $request->all(); 
        $customer = Contact::find($request->input('customer'));
    
    
        // Ensure the business ID is available
        $business_id = $request->input('business_id');

        // Step 2: Process passengers' data
        $passengers_new = [];

        // Decode the JSON if it’s a string
        $passengers = is_string($invoiceData['passengers']) 
                ? json_decode($invoiceData['passengers'], true) 
                : $invoiceData['passengers'];
    
        // Transform the array into a more usable format
        $passengerArray = [];
        foreach ($passengers as $passenger_data) {
            // Create a new associative array for each passenger
             $pass_data = json_decode($passenger_data,true);
         
            $passengerItem = [];
            foreach ($pass_data as $item) {
                $passengerItem[$item['name']] = $item['value']; // Map name to value
            }
            $passengerArray[] = $passengerItem; // Collect transformed passenger data
        }

        // Now iterate over the transformed passenger data
        foreach ($passengerArray as $passenger_data) {
    $contact = Contact::where('business_id', $business_id)
                      ->where('id', $passenger_data['passenger_name'])
                      ->first(); 

    if ($contact) {
        $passengers_new[] = [
            'name' => $contact->name,
            'passport_number' => $passenger_data['passport_number'],
            'passport_image' => $passenger_data['passport_image_link'],
            'airline_itinerary' => $passenger_data['airline_itinerary_link'] ?? '',
            'airticket_no' => $passenger_data['airticket_no'],
            'frequent_flyer_no' => $passenger_data['frequent_flyer_no'],
            'child' => $passenger_data['child'],
            'price' => ($passenger_data['child'] == 'yes') ? $passenger_data['price'] : 0,
            'passenger_type' => $passenger_data['passenger_type'] ?? null,
            'expiry_date' => $passenger_data['expiry_date'] ?? null,
            'additional_service' => $passenger_data['additional_service'] ?? null,
            'amount' => $passenger_data['amount']
        ];
    }
}



        // Step 3: Render the Blade view to HTML
        $html = view('airline::create_invoice.invoice_template', compact('invoiceData', 'customer', 'passengers_new'))->render();

        // Step 4: Initialize mPDF and write the HTML content into the PDF
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);

        // Step 5: Save the PDF to a temporary location
        $pdfFileName = 'invoice_' . time() . '.pdf';
        // Define the public invoices directory path
        $directoryPath = public_path('invoices/');

        // Check if the directory exists; if not, create it
        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true); // 0755 permissions and create directories recursively
        }

        // Save the PDF to the public/invoices directory
        $pdfPath = $directoryPath . $pdfFileName;
        $mpdf->Output($pdfPath, 'F'); // 'F' saves the file to the server

        // Step 6: Get the recipient's email and check if it's available
        $email =$customer->email;
        if (empty($email)) {
            // If no email is provided, return error
            return response()->json(['success' => false, 'msg' => 'No Email is Associated']);
        }

        // Step 7: Send the email with the PDF attached
        Mail::to($email)->send(new SendInvoiceMail($pdfPath));

        // Step 8: Return a success response
        return response()->json(['success' => true, 'msg' => 'Invoice sent successfully']);
        } catch (\Exception $e){
                 // Log the error for debugging
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            // Return a JSON error response
            return response()->json([
                'success' => false,
                'msg' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    
    public function sendWhatsappInvoicePDF(Request $request)
    {
        try {
        $business_id = $request->session()->get('user.business_id');
        $invoiceData = $request->all(); // Use the form data or fetch from database
        $customer = Contact::find($request->input('customer'));
        
        // Step 2: Decode payment_methods_data
    $paymentMethodsData = json_decode($invoiceData['payment_methods_data'], true);

   // Step 3: Decode payment_methods_data
    $paymentMethodsData = json_decode($invoiceData['payment_methods_data'], true);

    // Step 4: Transform the payment data keys and filter out empty values
    $formattedPayments = [];
    foreach ($paymentMethodsData as $payment) {
        // Use regex to extract the key without the "payment[0][" prefix
        $nameKey = preg_replace('/^payment\[\d+\]\[/', '', $payment['name']);
        $nameKey = rtrim($nameKey, ']'); // Remove the trailing ']'
        
        // Only add to formattedPayments if the value is not empty
        if (!empty($payment['value'])) {
            $formattedPayments[$nameKey] = $payment['value'];
        }
    }

    // Update the invoiceData with the new payment structure
    $invoiceData['formatted_payment_methods_data'] = $formattedPayments;
    
        // Ensure the business ID is available
        $business_id = $request->input('business_id');
    
        // Step 2: Process passengers' data
        $passengers_new = [];
    
        // Decode the JSON if it’s a string
        $passengers = is_string($invoiceData['passengers']) 
                        ? json_decode($invoiceData['passengers'], true) 
                        : $invoiceData['passengers'];
    
        // Transform the array into a more usable format
        $passengerArray = [];
        foreach ($passengers as $passenger_data) {
            $pass_data = json_decode($passenger_data,true);
            // Create a new associative array for each passenger
            $passengerItem = [];
            foreach ($pass_data as $item) {
                $passengerItem[$item['name']] = $item['value']; // Map name to value
            }
            $passengerArray[] = $passengerItem; // Collect transformed passenger data
        }
    
        // Now iterate over the transformed passenger data
        foreach ($passengerArray as $passenger_data) {
            $contact = Contact::where('business_id', $business_id)
                              ->where('id', $passenger_data['passenger_name'])
                              ->first(); 
    
            if ($contact) {
                $passengers_new[] = [
                    'name' => $contact->name,
                    'passport_number' => $passenger_data['passport_number'],
                    'passport_image' => $passenger_data['passport_image_link'],
                    'airline_itinerary' => $passenger_data['airline_itinerary_link'] ?? '',
                    'airticket_no' => $passenger_data['airticket_no'],
                    'frequent_flyer_no' => $passenger_data['frequent_flyer_no'],
                    'child' => $passenger_data['child'],
                    'price' => ($passenger_data['child'] == 'yes') ? $passenger_data['price'] : 0,
                    'passenger_type' => $passenger_data['passenger_type'] ?? null,
                    'expiry_date' => $passenger_data['expiry_date'] ?? null,
                    'additional_service' => $passenger_data['additional_service'] ?? null,
                    'amount' => $passenger_data['amount']
                ];
            }
        }
        Log:info($invoiceData);
    
        // Load your view and pass invoice data
        $html = view('airline::create_invoice.invoice_template', compact('invoiceData', 'customer', 'passengers_new'))->render();
    
        // Initialize mPDF
        $mpdf = new Mpdf();
    
        // Write the HTML content into the PDF
        $mpdf->WriteHTML($html);
    
        // Generate unique filename based on the invoice number
        $pdfFileName = 'invoice_' . $invoiceData['airline_invoice_no'] . '.pdf';
         // Define the public invoices directory path
        $directoryPath = public_path('invoices/');
    
        // Check if the directory exists; if not, create it
        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true); // 0755 permissions and create directories recursively
        }
    
        // Save the PDF to the public/invoices directory
        $pdfPath = $directoryPath . $pdfFileName;
        // Save the PDF file to server
        $mpdf->Output($pdfPath, 'F');
    
        // Check if the PDF was generated successfully
        if (!file_exists($pdfPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate PDF.',
            ]);
        }
    
        // Return the PDF link for WhatsApp
        return response()->json([
            'contact' => $customer->mobile,
            'pdf_url' => asset('invoices/' . $pdfFileName),
            'success' => true,
            'message' => 'PDF generated successfully',
        ]); 
        }catch(\Exception $e){
                 // Log the error for debugging
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            // Return a JSON error response
            return response()->json([
                'success' => false,
                'msg' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }


    
       public function get_airline_commission_print(Request $request) {

        if (!auth()->user()->can('airline.view_setting')) {

            abort(403, 'Unauthorized action.');
 
        }

        $ticket_no=$request->ticket_no;

        

   $add_commission = AirlineAddCommission::where('ticket_no', $ticket_no)->get();

  

       

       $location = $add_commission->pluck('location')->first();

       $agent = $add_commission->pluck('airline_agent')->first();

      

         return view('airline::add_commission.print')->with(compact(

            'add_commission','agent','location'

        ));    

            

    }

    public function get_incremental_prefix_value_by_prefix_starting_id(Request $request, $id){

        //return $id;

        

        $airlinePrefixStarting = AirlinePrefixStarting::findOrFail($id);

        

        return response($airlinePrefixStarting, 200); 

        

    }

    

    public function get_customer_fin_information_by_contact_id(Request $request){

        //return request('contactId');

        $id = request('contactId');

        

        $customerWithFinInformation = Contact::findOrFail($id);

        

        return response($customerWithFinInformation, 200); 

        

    }



    public function customers_by_group_id() {

        $business_id = request()->session()->get('user.business_id');



        $customer_group_id = request('customer_group_id');

        

        if($customer_group_id > 0){

            $customers = Contact::customersDropdownByGroupId($business_id, $customer_group_id);

        }else{

            $customers = Contact::customersDropdown($business_id, false, true, 'customer');

        }



        return $customers;

    }

   public function get_commission_types()

    {

        $business_id = request()->session()->get('user.business_id');

      

        $commisionType = AirlineCommissionType::all()->pluck('commsion_type', 'id');

          

        return response()->json([

            'commissionTypes' => $commisionType

        ]);

    }

   public function commision_type_get()

    {

        $business_id = request()->session()->get('user.business_id');

        

        $commisionType = AirlineCommissionType::all();

        

     

        return  $commisionType;

       

    }

    public function airline_classes_get()

    {
        return AirlineClasses::all();
    }

    public function airline_classes_store(Request $request)
    {
       
        $request->validate([
            'name' => 'required|string|max:50',
        ]);

        $classes = new AirlineClasses();
        $classes->name = $request->input('name');
        $classes->save();

        return response()->json([
            'message' => 'Airline Class type stored successfully',
            'data' => $classes
        ], 201);
    }


    public function passenger_type_get()

    {
        return AirlinePassengerType::all();
    }
    public function additional_service_get()

    {
        return AdditionalService::where('user_id', auth()->user()->id)->get();

       
    }

    public function passenger_type_store(Request $request)
    {
       
        $request->validate([
            'type_name' => 'required|string|max:50',
            'description' => 'nullable|string|max:100',
        ]);

        $passengerType = new AirlinePassengerType();
        $passengerType->type_name = $request->input('type_name');
        $passengerType->description = $request->input('description');
        $passengerType->save();

        return response()->json([
            'message' => 'Passenger type stored successfully',
            'data' => $passengerType
        ], 201);
    }

   public function getInvoiceno(Request $request)

    {

        $business_id = request()->session()->get('user.business_id');

      

       // $invoiceno = AirlineTicketInvoices::where('commsion_type', 'id');

         $ticket = AirlineTicketInvoices::leftjoin('airline_passengers', 'air_ticket_invoices.id', 'airline_passengers.invoice_id')

                     ->where('air_ticket_invoices.airline_invoice_no', $request->invoice_no)->first();

        

        $RTR_ticket_Number = AirlineTicketInvoices::leftJoin('airline_passengers', 'air_ticket_invoices.id', 'airline_passengers.invoice_id')

        ->where('air_ticket_invoices.airline_invoice_no',$request->invoice_no)

        ->pluck('air_ticket_invoices.airticket_no'); 

       

         return response()->json([

        'RTR_ticket_Number' => $RTR_ticket_Number,

        'amount' => $ticket->amount]);

       

}

 public function getInvoiceNumbers(Request $request)

    {

        $business_id = request()->session()->get('user.business_id');

        

     

          $invoiceNumbers = AirlineTicketInvoices::leftJoin('airline_passengers', 'air_ticket_invoices.id', 'airline_passengers.invoice_id')

                            ->leftJoin('airline_add_commissions', 'airline_passengers.airticket_no', 'airline_add_commissions.ticket_no')

                            ->where('air_ticket_invoices.airline_agent', $request->agent)

                            ->where(function ($query) {

                                $query->whereNull('airline_add_commissions.ticket_no') 

                                ->orWhereColumn('airline_add_commissions.commision_amount', '<', 'airline_passengers.amount');})

        ->pluck('air_ticket_invoices.airline_invoice_no');

            

        return response()->json(['invoiceNumbers' => $invoiceNumbers]);           



       

}

   public function airport_linked_get()

{

    $business_id = request()->session()->get('user.business_id');

    

    $airlineLinked = AirlineLinkedAccount::all();

  //  dd($airlineLinked);

    return  $airlineLinked;

   

}

 public function get_wallet(Request $request)

{

    $business_id = request()->session()->get('user.business_id');

    $supplierid=$request->supplierId;

    

     $amounts = ContactLedger::where('contact_id', $supplierid)->pluck('amount');

    // dd($amounts);

    

   return response()->json([

        'amounts' => $amounts

    ]);

}



    public function airlines() {



        return Airline::select('id', 'airline')->pluck('airline', 'id');

    }



    public function airline_agents() {

        return AirlineAgent::select('id', 'agent')->pluck('agent', 'id');

    }

    

    public function airline_airports() {

        $country = request('country');

        $province = request('province');

        if($country){

            $airports = AirlineAirports::where('country', $country);

        }else{

            $airports = AirlineAirports::whereNotNull('country');

        }

        

        





        if ($province) {

            $airports->where('province', $province);

        }

        

        $airports = $airports->select('id', 'province', 'airport_name')->get()->mapWithKeys(function ($airport) {

            return [$airport->id => ['airport_name' => $airport->airport_name, 'province' => $airport->province]];

        });



        return $airports;

    }



    public function create_passenger() {

        $additionalServices = AdditionalService::where('user_id', auth()->user()->id)

            ->pluck('name', 'id');


// Retrieve the field settings
    $fieldSettings = AirlineFormSettingPassenger::where('business_id', auth()->user()->business_id)->first();

        if(is_null($fieldSettings)){
            return response()->json([
                'success' => false,
                'msg' => "Passenger Form settings required in the Form Settings page",
            ], 500);
        }

        return view('airline::create_invoice.create_passenger', compact('additionalServices', 'fieldSettings'));

    }

      public function add_commission(Request $request) {

        

        $business_id = $request->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id);

        $agent= AirlineAgent::select('id', 'agent')->pluck('agent', 'id');

        

          $invoice_no= AirlineTicketInvoices::select('id', 'airline_invoice_no')->pluck('airline_invoice_no', 'id');

           $ticket_no= AirlineTicketInvoices::select('id', 'airticket_no')->pluck('airticket_no', 'id');

        

        return view('airline::add_commission.create_commission')->with(compact('business_locations','agent','invoice_no','ticket_no'));;

    }







    public function store_passenger(Request $request) {

        

        if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {

            abort(403, 'Unauthorized action.');

        }



        try {

            $business_id = $request->session()->get('user.business_id');



            $passenger = Contact::findOrFail($request->passenger_name);

            $input = array(


                'name' => $passenger->name,

                'passport_number' => $request->passport_number,

                'passenger_vat_number' => $request->passenger_vat_number,

                'passenger_mobile' => $request->passenger_mobile,

                'should_notify' => $request->should_notify,

                'airticket_no' => $request->airticket_no,

                'frequent_flyer_no' => $request->frequent_flyer_no,

                'child' => $request->child,

                'price' => $request->price,

                'passenger_id' => $request->passenger_name,

                'expiry_date' => $request->expiry_date,

                'passenger_type' => $request->passenger_type,

                 'commision_type' => $request->commission_type,

                'additional_services' => $request->additional_service??null,

                'amount' => $request->amount??null,

                'final_amount' => $request->amount+$request->price

                

            );

            

            // update passport no.

            

            $input['created_by'] = $request->session()->get('user.id');

            

            if($request->hasFile('passport_image')){

                $imageName = Media::uploadFile($request->file('passport_image')); 

                $input['image']=$imageName;

                $passenger->image = $input['image'];

                $passenger->save();

               
               
            }else{

                $input['image'] = $passenger->image;

            }


            $input['id_row'] =  $request->id_row;


            $output = [

                'success' => true,

                'data' => $input,

            ];


            

        } catch (\Exception $e) {

            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __("messages.something_went_wrong"),

                'error' => $e->getMessage()

            ];

        }

        return $output;

    }



    public function store_invoice(Request $request) {

        try{
            

            $payment_data = array();

            $payment = array();

          

            foreach(is_null($request->payment_methods_data)?[]:json_decode($request->payment_methods_data,true) as $one){

                $variableName = str_replace(['[', ']', ' '], '-', $one['name']);

                $varArr = explode('-',$variableName);

                $payment[$varArr[1]][$varArr[3]] = $one['value'];

            }

             foreach(is_null($request->payment_methods_data_supplier)?[]:json_decode($request->payment_methods_data_supplier,true) as $one){

                $variableName = str_replace(['[', ']', ' '], '-', $one['name']);

                $varArr = explode('-',$variableName);

                $payment[$varArr[1]][$varArr[3]] = $one['value'];

            }

            

            $passengers = array();

            foreach(isset($request->passengers)?$request->passengers:[] as $one){

                $pass_data = json_decode($one,true);

                $one_passenger = array();

                foreach($pass_data as $i){

                    $one_passenger[$i['name']] = $i['value'];

                }

                

                $passengers[] = $one_passenger;

            }

          

         

            

            $business_id = request()->session()->get('user.business_id');

            

            $transaction = Transaction::create(

                    [

                        'type' => 'airline_ticket',

                        'status' => 'received',

                        'invoice_no' => request('airticket_no'),

                        'business_id' => $business_id,

                        'transaction_date' => date('Y-m-d'),

                        'total_before_tax' => $request->tot_price??0,

                        'final_total' => $request->tot_price ?? 0,

                        'payment_status' => 'due',

                        'contact_id' => $request->customer,

                        'parent_transaction_id'=> $request->airline_agent,

                        'created_by' => Auth::user()->id

                    ]

                );

               

            $this->transactionUtil->createOrUpdatePaymentLines($transaction, $payment);

            $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);



              //dd($request->supplier);

            $air_ticket_invoice_ids = [];
            $airline_passengers_ids = [];

            if(request('travel_mode') == 'Multicity'){
                
                $multi_class = request('multi_class');
                $multi_adults = request('multi_adults');
                $multi_children = request('multi_children');
                $multi_infants = request('multi_infants');



                $multicity_data = request('multicity');

                $passengers_new = array();
                $invoice_new = array();

                if (is_array($multicity_data) && isset($multicity_data[0])) {
                    $multicity = json_decode($multicity_data[0], true);

                    if (is_array($multicity)) {
                        foreach ($multicity as $multi_data) {
                            $invoice = AirlineTicketInvoices::create([
                                'airticket_no' => request('airticket_no'),
                                'business_id' => $business_id,
                                'transaction_id' => $transaction->id,
                                'customer_group' => request('customer_group'),
                                'customer' => request('customer'),
                                'supplier' => request('supplier'),
                                'airline'  => request('airline'),
                                'airline_invoice_no' => request('airline_invoice_no'),
                                'airline_agent' => request('airline_agent'),
                                'travel_mode' => request('travel_mode'),
                                'departure_country' => $multi_data['origin'], 
                                'departure_airport' => request('departure_airport'),
                                'departure_date' => \DateTime::createFromFormat('m/d/Y', $multi_data['departureDate'])->format('Y-m-d'), 
                                'transit' => request('transit'),
                                'transit_airport' => (request('transit')) ? request('transit_airport') : '',
                                'arrival_country' => $multi_data['destination'], 
                                'arrival_airport' => request('arrival_airport'),
                                'arrival_date' => \DateTime::createFromFormat('m/d/Y', request('arrival_date')),
                                'arrival_time' => \DateTime::createFromFormat('h:m', request('arrival_time')),
                                'total_time' => \DateTime::createFromFormat('h:m', request('total_time')),
                                'transit_time' => \DateTime::createFromFormat('h:m', request('transit_time')),
                                'note' => request('note') ?? null
                            ]);


                            $air_ticket_invoice_ids[] = $invoice->id;
                            

                            foreach($passengers as $passenger_data) {
                
                                $contact = Contact::where('business_id',$business_id)->where('id',$passenger_data['passenger_name'])->first(); 
                
                                    $passenger = AirlinePassengers::create([
                
                                        'invoice_id' => $invoice->id,
                
                                        'name' => $contact->name,
                
                                        'passport_number' => $passenger_data['passport_number'],
                
                                        'passport_image' => $passenger_data['passport_image_link'],
                
                                        'airline_itinerary' => $passenger_data['airline_itinerary_link'] ?? '',
                
                                        'airticket_no' => $passenger_data['airticket_no'],
                
                                        'frequent_flyer_no' => $passenger_data['frequent_flyer_no'],
                
                                        'child' => $passenger_data['child'],
                
                                        'price' => ($passenger_data['child']=='yes')? $passenger_data['price']:0,
                
                                        'passenger_type' => $passenger_data['passenger_type']??null,
                
                                        'expiry_date' => $passenger_data['expiry_date']??null,
                
                                        'additional_service' => $passenger_data['additional_service']??null,
                
                                        'amount' => $passenger_data['amount']
                
                                    ]);
                
                                    $airline_passengers_ids[] = $passenger->id;
                
                                }
                        

                                
                        }
                    } else {
                    }
                } else {
                    
                }


                
               }else{

                $invoice_new = array();

                $invoice = AirlineTicketInvoices::create([
 
                    'airticket_no' => request('airticket_no'),
    
                    'business_id' => $business_id,
    
                    'transaction_id' => $transaction->id,
    
                    'customer_group' => request('customer_group'),
    
                    'customer' => request('customer'),
    
                    'supplier' => request('supplier'),
    
                    'airline'  => request('airline'),
    
                    'airline_invoice_no' => request('airline_invoice_no'),
    
                    'airline_agent' => request('airline_agent'),
    
                    'travel_mode' => request('travel_mode'),
    
                    'departure_country' => request('departure_country'),
    
                    'departure_airport' => request('departure_airport'),

                    'departure_date' => \DateTime::createFromFormat('m/d/Y', request('departure_date'))->format('Y-m-d'),
    
                    'transit' => request('transit'),
    
                    'transit_airport' => (request('transit'))?request('transit_airport'):'',
    
                    'arrival_country' => request('arrival_country'),
    
                    'arrival_airport' => request('arrival_airport'),
    
                    'arrival_date' => \DateTime::createFromFormat('m/d/Y', request('arrival_date')),
    
                    'arrival_time' => \DateTime::createFromFormat('h:m', request('arrival_time')),
    
                    'total_time' => \DateTime::createFromFormat('h:m', request('total_time')),
    
                    'transit_time' => \DateTime::createFromFormat('h:m', request('transit_time')),
    
                    'note' => request('note')??null
    
                ]);     

                $air_ticket_invoice_ids[] = $invoice->id;


                $passengers_new = array();

                foreach($passengers as $passenger_data) {
    
                    $contact = Contact::where('business_id',$business_id)->where('id',$passenger_data['passenger_name'])->first(); 
     
                     $passenger = AirlinePassengers::create([
     
                         'invoice_id' => $invoice->id,
     
                         'name' => $contact->name,
     
                         'passport_number' => $passenger_data['passport_number'],
     
                         'passport_image' => $passenger_data['passport_image_link'],
     
                         'airline_itinerary' => $passenger_data['airline_itinerary_link'] ?? '',
     
                         'airticket_no' => $passenger_data['airticket_no'],
     
                         'frequent_flyer_no' => $passenger_data['frequent_flyer_no'],
     
                         'child' => $passenger_data['child'],
     
                         'price' => ($passenger_data['child']=='yes')? $passenger_data['price']:0,
     
                         'passenger_type' => $passenger_data['passenger_type']??null,
     
                         'expiry_date' => $passenger_data['expiry_date']??null,
     
                         'additional_service' => $passenger_data['additional_service']??null,
     
                         'amount' => $passenger_data['amount']
     
                     ]);
     
                     $airline_passengers_ids[] = $passenger->id;
     
                 }
     



               }



            $output = [

                'success' => 1,

                'msg' => __('lang_v1.success'),
                'air_ticket_invoice_ids' => $air_ticket_invoice_ids,
                'airline_passengers_ids' => $airline_passengers_ids,
                'transaction_id' => $transaction->id
            ];

        } catch (QueryException $e) {

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }

        if (request()->ajax()){
            return $output;
        }

        return redirect()->to('/airline/ticketing')->with('status', $output);

        

    }



    public function create_payment() {

        $payment_types = $this->productUtil->payment_types(null,false, false, false, false,"is_sale_enabled");

        return view('airline::create_invoice.create_payment')->with(compact('payment_types'));

    }

    public function create_payment_supplier() {

        $payment_types = $this->productUtil->payment_types(null,false, false, false, false,"is_sale_enabled");

        return view('airline::create_invoice.create_payment_supplier')->with(compact('payment_types'));

    }

    public function getCustomer (){

        $business_id = request()->session()->get('user.business_id');

        $customers = Contact::where('business_id',$business_id)->where('type','customer')->get();

        return $customers;

    }
    
    public function create_linked_supplier_account ($supplier_id) {
        $contact = Contact::findOrFail($supplier_id);
        $business_id = $contact->business_id;
        $account_types = AccountType::where('business_id', $business_id)
                        ->whereNull('parent_account_type_id')
                        ->pluck('name', 'id');
        $sub_account_types = AccountType::where('business_id', $business_id)
                        ->whereNotNull('parent_account_type_id')
                        ->pluck('name', 'id');
        $account_groups = AccountGroup::where('business_id', $business_id)->pluck('name', 'id');
        $accounts = Account::where('business_id', $business_id)->pluck('name', 'id');

         return view('airline::supplier.linkedSupplierAccount')->with(compact('account_types', 'account_groups', 'sub_account_types', 'accounts', 'business_id', 'supplier_id'));
    }
    
    public function getAccountsByGroup($accountGroupId)
    {
        // Fetch accounts that belong to the specified account group
        $accounts = Account::where('asset_type', $accountGroupId)->get(['id', 'name']);
    
        return response()->json([
            'accounts' => $accounts
        ]);
    }
    
    public function getSubAccountTypes($businessId, $accountTypeId = null)
    {
        // Fetch accounts that belong to the specified account group
        $account_types = AccountType::where('business_id', $businessId)
                    ->when($accountTypeId, function ($query) use ($accountTypeId) {
                        return $query->where('parent_account_type_id', $accountTypeId);
                    })
                    ->whereNotNull('parent_account_type_id')
                    ->get(['id', 'name']);
    
        return response()->json([
            'account_subs' => $account_types
        ]);
    }
    
    public function getAccountByAccountSubType($businessId, $accountTypeId)
    {
        // Fetch accounts that belong to the specified account group
        $accounts = Account::where('account_type_id', $accountTypeId)
                    ->where('business_id', $businessId)
                    ->get(['id', 'name']);
    
        return response()->json([
            'accounts' => $accounts
        ]);
    }

    public function getLinkedSupplierAccount($accountId)
    {
        $business_id = request()->session()->get('user.business_id');
        // dd($accountId);

       $results = DB::table('airline_linked_accounts')
        ->join('accounts', 'airline_linked_accounts.AcoountName', '=', 'accounts.name')
        ->join('account_groups', 'accounts.asset_type', '=', 'account_groups.id')
        ->where('airline_linked_accounts.id', $accountId)
        ->where('accounts.business_id', $business_id)
        ->select('airline_linked_accounts.*', 'accounts.id as accountId', 'accounts.account_type_id as accountTypeId', 'account_groups.id as accountGroupId')
        ->first();

        
        


        if (!$results) {
            return response()->json(['error' => 'Account not found'], 404);
        }

        return response()->json([
            'account' => $results
        ]);
    }
    
    public function getLinkedSupplierAccounts()
    {
        try {
            // Fetch all the linked supplier accounts from the database with account group and account details
            $linkedAccounts = AirlineLinkedAccount::select('airline_linked_accounts.*', 'sub.name as SubType', 'parent.name as TypeName')
                            ->join('accounts', 'airline_linked_accounts.AcoountName', '=', 'accounts.name')
                            ->join('account_types as sub', 'sub.id', '=', 'accounts.account_type_id')
                            ->join('account_types as parent', 'parent.id', '=', 'sub.parent_account_type_id')
                            ->where('supplier_id', request()->supplier_id)
                            ->whereNotNull('date')->groupBy('AcoountName', 'AccountType')->get();

    
            // Prepare the response data
            $responseData = $linkedAccounts->map(function ($linkedAccount) {
                return [
                    'id' => $linkedAccount->id,
                    'dateTime' => $linkedAccount->date,
                    'accountGroupName' => $linkedAccount->AccountType,
                    'accountName' => $linkedAccount->AcoountName,
                    'subType' => $linkedAccount->SubType,
                    'typeName' => $linkedAccount->TypeName,
                    'user' => $linkedAccount->user,
                    'accountNumber' => $linkedAccount->AccountNumber
                ];
            });
    
            // Return a successful JSON response with the fetched data
            return response()->json([
                'success' => true,
                'accounts' => $responseData
            ]);
            
        } catch (\Exception $e) {
            // Log any error that occurs during fetching
            Log::error('Error fetching linked supplier accounts: ' . $e->getMessage());
    
            // Return an error response
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while fetching the linked accounts.'
            ], 500);
        }
    }



    public function submitLinkedSupplierAccount(Request $request)
    {
        // Log the incoming request data
        Log::info('Submitting linked supplier account', ['request_data' => $request->all()]);
    
        // Check if it's an update or a new record
        $account = null;
        if ($request->has('id') && !empty($request->id)) {
            Log::info('Updating existing account with ID:', ['id' => $request->id]);
            $account = AirlineLinkedAccount::find($request->id);
    
            if ($account) {
                Log::info('Existing account found for update:', ['account' => $account]);
            } else {
                Log::error('No account found with the given ID for update:', ['id' => $request->id]);
                return response()->json(['success' => false, 'message' => 'Account not found'], 404);
            }
        }
    
        // Get account group and account details
        $accountType = AccountType::where('id', $request->sub_account_type)->first();
        $accountDetails = Account::where('id', $request->account_id)->first();
        Log::info('Fetched accountType and Account:', ['accountType' => $accountType, 'accountDetails' => $accountDetails]);
    
        // Get the business ID from the session and the username
        $business_id = $request->business_id;
        $username = User::where('business_id', $business_id)->pluck('username')->first();
        Log::info('Business ID and Username fetched:', ['business_id' => $business_id, 'username' => $username]);
    
        // Prepare data for creation or update
        $data = [
            'date' => $request->date_time,
            'AccountType' => $accountType->name,
            'AcoountName' => $accountDetails->name,
            'AccountNumber' => $accountDetails->account_number,
            'user' => $username,
            'supplier_id' => $request->supplier_id,
            'business_id' => $request->business_id,
        ];
        Log::info('Data prepared for submission:', ['data' => $data]);
    
        // Update or create the linked account
        if ($account) {
            $account->update($data);
            Log::info('AirlineLinkedAccount updated:', ['account' => $account]);
        } else {
            $airlineLinkedAccount = AirlineLinkedAccount::where('AccountNumber', $accountDetails->account_number)
            ->where('business_id', $request->business_id)
            ->whereNotNull('supplier_id')
            ->first();
            if($airlineLinkedAccount){
                return response()->json(['success' => false, 'msg' => 'Account already linked']);
            }
            $account = AirlineLinkedAccount::create($data);
            Log::info('AirlineLinkedAccount created:', ['account' => $account]);
        }

        $linkedAccounts = AirlineLinkedAccount::select('airline_linked_accounts.*', 'sub.name as SubType', 'parent.name as TypeName')
        ->join('accounts', 'airline_linked_accounts.AcoountName', '=', 'accounts.name')
        ->join('account_types as sub', 'sub.id', '=', 'accounts.account_type_id')
        ->join('account_types as parent', 'parent.id', '=', 'sub.parent_account_type_id')
        ->where('supplier_id', $request->supplier_id)
        ->whereNotNull('date')->groupBy('AcoountName', 'AccountType')->get();

        // Prepare the response data
        $responseData = $linkedAccounts->map(function ($linkedAccount) {
            return [
                'id' => $linkedAccount->id,
                'dateTime' => $linkedAccount->date,
                'accountGroupName' => $linkedAccount->AccountType,
                'accountName' => $linkedAccount->AcoountName,
                'subType' => $linkedAccount->SubType,
                'typeName' => $linkedAccount->TypeName,
                'user' => $linkedAccount->user,
                'accountNumber' => $linkedAccount->AccountNumber
            ];
        });
    
        // Return response based on the operation
        return response()->json([
            'success' => true,
            'account' => $account,
            'accountTypeName' => $accountType->name,
            'accountName' => $account->AcoountName,
            'dateTime' => $account->date,
            'accounts' => $responseData
        ]);
    }



    public function deleteLinkedSupplierAccount($id)
    {
         Log::info('AirlineLinkedAccount deleted:', ['id' => $id]);
        // Find the account
        $account = AirlineLinkedAccount::where('id', $id);

        if (!$account) {
            return response()->json(['error' => 'Account not found'], 404);
        }

        // Delete the account
        $account->delete();

        return response()->json(['success' => true]);
    }




}
