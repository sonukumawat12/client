<?php


namespace App\Http\Controllers;


use App\Account;

use App\Contact;

;

use App\Utils\Util;

use App\Transaction;

use App\ContactLedger;

use App\BusinessLocation;

use App\Utils\ModuleUtil;

use App\CustomerReference;

use App\CustomerStatement;

use App\Utils\ProductUtil;

use App\TransactionPayment;

use App\Utils\BusinessUtil;

use App\Utils\ContactUtil;

use Illuminate\Http\Request;

use App\Utils\TransactionUtil;

use App\CustomerStatementDetail;

use App\CustomerStatementLogo;

use App\CustomerStatementSetting;

use Mpdf\Mpdf;

use Illuminate\Http\Response;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Modules\Superadmin\Entities\HelpExplanation;
use Illuminate\Support\Str;

use Modules\Fleet\Entities\RouteProduct;


class CustomerStatementController extends Controller

{

    /**
     * All Utils instance.
     *

     */

    protected $transactionUtil;

    protected $productUtil;

    protected $moduleUtil;

    protected $commonUtil;

    protected $businessUtil;
    
    protected $contactUtil;


    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct(BusinessUtil $businessUtil, Util $commonUtil, TransactionUtil $transactionUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, ContactUtil $contactUtil)

    {

        $this->transactionUtil = $transactionUtil;

        $this->productUtil = $productUtil;

        $this->moduleUtil = $moduleUtil;

        $this->commonUtil = $commonUtil;

        $this->businessUtil = $businessUtil;
        
        $this->contactUtil = $contactUtil;

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)

    {
        $business_id = request()->session()->get('business.id');


        $default_start = new \Carbon('first day of this month');

        $default_end = new \Carbon('last day of this month');


        $start_date = !empty($request->get('start_date')) ? date('Y-m-d', strtotime($request->get('start_date'))) : $default_start->format('Y-m-d');

        $end_date = !empty($request->get('end_date')) ? date('Y-m-d', strtotime($request->get('end_date'))) : $default_end->format('Y-m-d');


        $edit_customer_statement = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'edit_customer_statement');

        //Return the details in ajax call

        if ($request->ajax()) {

            $query = $this->__getStatement($business_id);
            
            // filters:
            $permitted_locations = auth()->user()->permitted_locations();
            $location_filter = '';
            if ($permitted_locations != 'all') {
                $query->whereIn('transactions.location_id', $permitted_locations);
                $locations_imploded = implode(', ', $permitted_locations);
                $location_filter .= "AND transactions.location_id IN ($locations_imploded) ";
            }


            if (!empty($request->input('location_id'))) {
                $location_id = $request->input('location_id');
                $query->where('transactions.location_id', $location_id);
                $location_filter .= "AND transactions.location_id=$location_id";
                $query->join('product_locations as pls', 'pls.product_id', '=', 'p.id')
                    ->where(function ($q) use ($location_id) {
                        $q->where('pls.location_id', $location_id);

                    });

            }
            

            if (!empty($start_date) && !empty($end_date)) {
                $query->whereDate('transactions.transaction_date', '>=', $start_date);
                $query->whereDate('transactions.transaction_date', '<=', $end_date);
            }

            $query->where('transactions.contact_id', '!=', null);
            if (!empty($request->input('customer_id'))) {
                $cid = $request->input('customer_id');
                $query->where('transactions.contact_id', $cid);
            }


            $type = request()->get('type', null);
            if (!empty($type)) {
                $query->where('p.type', $type);
            }
            
            $products = $query->get();
            
            

            $datatable = DataTables::of($products)
                ->editColumn('product', function ($row) {
                    
                    if ($row->tran_type == 'route_operation') {
                        $amounts = "";
                        if(!empty($row->product_id)){
                            $prod_array = json_decode($row->product_id);
                            
                            if (is_array($prod_array)) {
                                foreach ($prod_array as $key => $one) {
                                    $product = RouteProduct::where('id',$one)->first();
                                    if(!empty($product)){
                                       $amounts .= $product->name;
                                       if($key != sizeof($prod_array) -1){
                                           $amounts .= " + ";
                                       }
                                    }
                                }
                            } else {
                                $product = RouteProduct::where('id',$prod_array)->first();
                                if(!empty($product)){
                                       $amounts .= $product->name;
                                }
                            }
                            
                        }
                            
                        return $amounts;
                    }
                    
                    else{
                        $name = $row->product;

                        if ($row->type == 'variable') {
    
                            $name .= ' - ' . $row->product_variation . '-' . $row->variation_name;
    
                        }
    
                        return $name;
                    }
                    
                    

                })
                ->removeColumn('enable_stock')
                ->removeColumn('unit')
                ->removeColumn('id')
                ->addColumn('quantity', function ($row) {

                    if ($row->tran_type == 'sell' || $row->tran_type == 'route_operation') {
                        
                        $amounts = "";
                        if(!empty($row->sold_qty)){
                            $qty_array = json_decode($row->sold_qty) ;
                            if (is_array($qty_array)) {
                                
                                foreach ($qty_array as $key => $one) {
                                    $amounts .=  number_format((int)$one,2,'.',',');
                                    if($key != sizeof($qty_array) - 1 ){
                                       $amounts .= " + ";
                                    }
                                }
                            } else {
                                $amounts .=  number_format((int)$qty_array,2,'.',',');
                            }
                        }
                            
                        return $amounts;
                            
                        // return number_format($row->sold_qty,'2','.',',');

                    } else {

                        return '';

                    }

                })
                ->addColumn('action', function ($row) use ($edit_customer_statement) {

                    $html = '<div class="btn-group">

                            <button type="button" class="btn btn-info dropdown-toggle btn-xs"

                                data-toggle="dropdown" aria-expanded="false">' .

                        __("messages.actions") .

                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown

                                </span>

                            </button>

                            <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                    $html .= '<li><a href="#" data-href="' . action("SellController@show", [$row->transaction_id]) . '" class="btn-modal" data-container=".customer_statement_modal"><i class="fa fa-external-link" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';

                    if ($edit_customer_statement) {

                        if (auth()->user()->can('edit_customer_statement')) {

                            $html .= '<li><a href="#" data-href="' . action("CustomerStatementController@edit", [$row->transaction_id]) . '" class="btn-modal" data-container=".customer_statement_modal"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i> ' . __("messages.edit") . '</a></li>';

                        }

                    }

                    return $html;

                })
                ->editColumn(

                    'transaction_date',

                    '{{@format_date($transaction_date)}}'

                )
                ->editColumn(

                    'order_no',
                    
                    function ($row){
                        if(!empty($row->order_no)){
                            return $row->order_no;
                        }else{
                            return $row->order_number;
                        }   
                    }
                )
                ->editColumn(

                    'invoice_no',
                    
                    function ($row){
                       $html = $row->invoice_no;
                       if($row->sub_type == 'customer_loan'){
                           $html .= "<br><b>(".__('petro::lang.customer_loans').")</b>";
                       }
                       
                       if($row->tran_type == 'direct_customer_loan'){
                           $html .= "<br><b>(".__('lang_v1.direct_loan_to_customer').")</b>";
                       }
                       
                       return $html;
                    }
                )
                ->editColumn(

                    'route_name',

                    function ($row){
                        if(!empty($row->route_name)){
                            return $row->route_name;
                        }  
                    }

                )
                ->editColumn(

                    'vehicle_number',

                    function ($row){
                        if(!empty($row->vehicle_number)){
                            return $row->vehicle_number;
                        }  
                    }

                )
                ->editColumn(

                    'order_date',
                    
                    function ($row){
                        if(!empty($row->order_date)){
                            return $this->commonUtil->format_date($row->order_date);
                        }
                    }

                    

                )
                
                ->addColumn(

                    'due_amount', function ($row) {
                        
                    $due = 0;

                    if ($row->tran_type == 'sell') {
                        
                        $due = ($row->sold_qty*$row->p_unit_price)-$row->total_paid;

                    } else {

                        $due = $row->final_total - $row->total_paid;

                    }
                    
                    
                    return '<span class="display_currency due" data-currency_symbol="true" data-orig-value="'.number_format($due,'2','.',',').'">'.number_format($due,'2','.',',').'</span>';

                })
                
                ->editColumn(

                    'final_total', function ($row) {

                    if ($row->tran_type == 'sell') {
                        
                        return number_format($row->sold_qty*$row->p_unit_price,'2','.',',');

                    } else {

                        return number_format($row->final_total,'2','.',',');

                    }

                })
                ->editColumn(

                    'unit_price',
                    
                    function ($row){
                        if(!empty($row->p_unit_price)){
                            return $this->commonUtil->num_uf($row->p_unit_price);
                        } 
                    }

                    
                )
                ->editColumn(

                    'qty',
                    
                    function ($row){
                        if(!empty($row->qty)){
                            return $this->commonUtil->num_uf($row->qty);
                        } 
                    }

                )
                ->editColumn(

                    'final_due_amount',

                    '{{$final_total - $total_paid}}'

                );

            $raw_columns = [

                'action',

                'final_total',

                'due_amount',
                
                'invoice_no'

            ];


            return $datatable->rawColumns($raw_columns)->make(true);

        }


        $customers = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');

        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

        $enable_separate_customer_statement_no = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_separate_customer_statement_no');

        $help_explanations = HelpExplanation::pluck('value', 'help_key');

        $statement_no = CustomerStatement::where('business_id', $business_id)->count();
        
        $logos = CustomerStatementLogo::where('business_id',$business_id)->select('*')->pluck('image_name','id');

        return view('customer_statement.index')->with(compact('logos', 'customers', 'enable_separate_customer_statement_no', 'business_locations', 'statement_no', 'help_explanations'));

    }
    
    public function __getStatement($business_id){
        $query = DB::table('transactions')
            ->leftJoin('transaction_sell_lines as tsl', 'transactions.id', '=', 'tsl.transaction_id')
            ->leftJoin('settlement_credit_sale_payments', 'transactions.credit_sale_id', '=', 'settlement_credit_sale_payments.id')
            ->leftJoin('route_operations', 'transactions.id', '=', 'route_operations.transaction_id')
            ->leftJoin('business_locations', 'transactions.location_id', '=', 'business_locations.id')
            ->leftJoin('products as p', function ($join) {
                $join->on(function ($query) {
                        $query->where('transactions.is_settlement', 1)
                            ->whereColumn('p.id', 'settlement_credit_sale_payments.product_id');
                    })
                    ->orWhere(function ($query) {
                        $query->where('transactions.is_settlement', '<>', 1)
                            ->whereColumn('p.id', 'tsl.product_id');
                    });
            })
            ->leftJoin('routes as r', 'route_operations.route_id', '=', 'r.id')
            ->leftJoin('fleets as f', 'route_operations.fleet_id', '=', 'f.id')
            ->leftJoin('route_products as rp', 'route_operations.product_id', '=', 'rp.id')
            ->leftJoin('variations', 'p.id', '=', 'variations.product_id')
            ->leftJoin('units', 'p.unit_id', '=', 'units.id')
            ->leftJoin('product_variations as pv', 'variations.product_variation_id', '=', 'pv.id')
            ->selectRaw('
                business_locations.name as location_name,
                variations.sub_sku as sku,
                COALESCE(p.name, rp.name) as product,
                COALESCE(tsl.unit_price, settlement_credit_sale_payments.price) as p_unit_price,
                p.type,
                transactions.contact_id as customer_id,
                p.id as product_id,
                units.short_name as unit,
                p.enable_stock as enable_stock,
                variations.sell_price_inc_tax as unit_price,
                pv.name as product_variation,
                variations.name as variation_name,
                COALESCE(tsl.quantity, route_operations.qty, settlement_credit_sale_payments.qty) as sold_qty,
                route_operations.product_id,
                transactions.transaction_date as transaction_date,
                transactions.type as tran_type,
                transactions.ref_no,
                transactions.invoice_no,
                transactions.customer_ref,
                transactions.order_no,
                transactions.order_date,
                transactions.contact_id,
                transactions.sub_type,
                route_operations.order_number,
                route_operations.date_of_operation,
                f.vehicle_number,
                r.route_name,
                transactions.final_total,
                transactions.id as transaction_id,
                tsl.id as tsl_id,
                (SELECT SUM(IF(TP.is_return = 1, -1*TP.amount, TP.amount))
                 FROM transaction_payments AS TP
                 WHERE TP.transaction_id = transactions.id) as total_paid')
            ->where(function ($query) use ($business_id) {
                $query->where('p.business_id', $business_id)
                    ->orWhere('route_operations.business_id', $business_id)
                    ->orWhere('transactions.business_id', $business_id);
            })
            ->where(function ($query) {
                $query->whereIn('transactions.type', ['sell', 'opening_balance', 'route_operation','direct_customer_loan'])
                      ->orWhere(function ($query) {
                          $query->where('transactions.type', 'settlement')->where('transactions.sub_type','customer_loan');
                });
            })

            ->whereIn('transactions.payment_status', ['due', 'partial']);
                
            
            return $query;
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
    
    public function downloadPdf( Request $request){
        $html = $request->get('html');
        $mpdf = new Mpdf();
        $mpdf->SetFont('Calibri', '', 12);
        $mpdf->WriteHTML($html);
        
        $directoryPath = config('constants.reports_directory');
        if (!is_dir($directoryPath)) {
            mkdir($directoryPath, 0755, true);
        }
        
        $filename = Str::random(40).".pdf";
        $filePath = config('constants.reports_directory').$filename;
        $mpdf->Output($filePath, 'F');
        
        return response()->json(['path' => url("reports/".$filename)]);

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)

    {

        $business_id = request()->session()->get('business.id');

        try {

            $default_start = new \Carbon('first day of this month');

            $default_end = new \Carbon('last day of this month');


            $start_date = !empty($request->get('start_date')) ? date('Y-m-d', strtotime($request->get('start_date'))) : $default_start->format('Y-m-d');

            $end_date = !empty($request->get('end_date')) ? date('Y-m-d', strtotime($request->get('end_date'))) : $default_end->format('Y-m-d');

            $query = $this->__getStatement($business_id);
            
            // filters:
            $permitted_locations = auth()->user()->permitted_locations();
            $location_filter = '';
            if ($permitted_locations != 'all') {
                $query->whereIn('transactions.location_id', $permitted_locations);
                $locations_imploded = implode(', ', $permitted_locations);
                $location_filter .= "AND transactions.location_id IN ($locations_imploded) ";
            }


            if (!empty($request->input('location_id'))) {
                $location_id = $request->input('location_id');
                $query->where('transactions.location_id', $location_id);
                $location_filter .= "AND transactions.location_id=$location_id";
                $query->join('product_locations as pls', 'pls.product_id', '=', 'p.id')
                    ->where(function ($q) use ($location_id) {
                        $q->where('pls.location_id', $location_id);

                    });

            }
            

            if (!empty($start_date) && !empty($end_date)) {
                $query->whereDate('transactions.transaction_date', '>=', $start_date);
                $query->whereDate('transactions.transaction_date', '<=', $end_date);
            }

            $query->where('transactions.contact_id', '!=', null);
            if (!empty($request->input('customer_id'))) {
                $cid = $request->input('customer_id');
                $query->where('transactions.contact_id', $cid);
            }


            $type = request()->get('type', null);
            if (!empty($type)) {
                $query->where('p.type', $type);
            }
            
            $transactions = $query->get();
            

            DB::beginTransaction();
            
            $contact = Contact::findOrFail($request->customer_id);
            
            $custAbbreviation = strtoupper(substr($contact->name,0,2));
            
            $customer_statement = CustomerStatement::where('customer_id', $request->customer_id)
                    ->orderBy('id', 'desc')
                    ->first();
                    
            if($customer_statement && sizeof(explode('-',$customer_statement->statement_no)) > 1){
                $previousStaement = explode('-',$customer_statement->statement_no)[1];
            }else{
                $previousStaement = 0;
            }
            
            $currentStatement = $custAbbreviation."-".($previousStaement+1);
            

            $statement = CustomerStatement::create([

                'business_id' => $business_id,
                
                'logo' => $request->logo,

                'customer_id' => $request->customer_id,

                'statement_no' => $currentStatement,

                'print_date' => date('Y-m-d'),

                'date_from' => \Carbon::parse($start_date)->format('Y-m-d'),

                'date_to' => \Carbon::parse($end_date)->format('Y-m-d'),

                'added_by' => Auth::user()->id

            ]);
            
            

            foreach ($transactions as $transaction) {
               $invoice_no = $transaction->invoice_no;
               if($transaction->sub_type == 'customer_loan'){
                   $invoice_no = __('petro::lang.customer_loans');
               }
               
               if($transaction->tran_type == 'direct_customer_loan'){
                   $invoice_no = __('lang_v1.direct_loan_to_customer');
               }
               
               $due = 0;

                if ($transaction->tran_type == 'sell') {
                    $due = ($transaction->sold_qty*$transaction->p_unit_price)-$transaction->total_paid;
                } else {
                    $due = $transaction->final_total - $transaction->total_paid;
                }
                
                $final_total = 0;
                if ($transaction->tran_type == 'sell') {
                    $final_total = $transaction->sold_qty*$transaction->p_unit_price;
                } else {
                    $final_total = $transaction->final_total;
                }

                CustomerStatementDetail::create([

                    'business_id' => $business_id,

                    'statement_id' => $statement->id,

                    'date' => \Carbon::parse($transaction->transaction_date)->format('Y-m-d'),

                    'location' => $transaction->location_name,

                    'invoice_no' => $invoice_no,

                    'customer_reference' => $transaction->customer_ref,

                    'order_no' => !empty($transaction->order_no) ? $transaction->order_no : $transaction->order_number, // ceck this
                    'vehicle_number' => !empty($transaction->vehicle_number) ? $transaction->vehicle_number : "",
                    'route_name' => !empty($transaction->route_name) ? $transaction->route_name : "",

                    'order_date' => !empty($transaction->order_date) ? $transaction->order_date : $transaction->date_of_operation,

                    'product' => $transaction->product,

                    'unit_price' => !empty($transaction->p_unit_price) ? $transaction->p_unit_price : "1",

                    'qty' => $transaction->sold_qty,

                    'invoice_amount' => $final_total,

                    'due_amount' => $due

                ]);

            }


            $default_location = BusinessLocation::where('business_id', $business_id)->first();


            $statement->location_id = !empty($transaction->location_id) ? $transaction->location_id : $default_location->id;


            DB::commit();

            $output = [

                'success' => 1,

                'msg' => __('messages.success')

            ];

        } catch (\Exception $e) {

            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => 0,

                'msg' => __('messages.something_went_wrong')

            ];

        }


        return $output;

    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        $statement = CustomerStatement::findOrFail($id);
        $contact = Contact::findOrFail($statement->customer_id);
        $start_date = $statement->date_from;
        $end_date = $statement->date_to;
        $statement_details = CustomerStatementDetail::where('statement_id', $id)->get();
        
        
        $contact_id = $contact->id;
        $business_id = $contact->business_id;
        $ledger_details = [];
        $ledger_details['opening_balance'] = 0;
        
        if($contact->type == 'customer'){
            $ledger_details['beginning_balance'] = $this->contactUtil->getCustomerBf($contact_id,$business_id,$start_date);
            $ledger_details['balance_details'] = $this->contactUtil->getCustomerBalance($contact_id,$business_id);
        }
        
        if($contact->type == 'supplier'){
            $ledger_details['beginning_balance'] = $this->contactUtil->getSupplierBf($contact_id,$business_id,$start_date);
            $ledger_details['balance_details'] = $this->contactUtil->getSupplierBalance($contact_id,$business_id);
        }
        
        $business_details = $this->businessUtil->getDetails($contact->business_id);
        $location_details = BusinessLocation::where('business_id', $contact->business_id)->first();
        $for_pdf = 1;
        
        $logo = CustomerStatementLogo::find($statement->logo);

        return view('customer_statement.show')->with(compact('id','logo','contact', 'ledger_details', 'business_details', 'for_pdf', 'location_details', 'statement_details', 'statement'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)

    {

        $transaction = Transaction::findOrFail($id);

        $customer_references = CustomerReference::where('contact_id', $transaction->contact_id)->pluck('reference', 'reference');


        return view('customer_statement.edit')->with(compact('transaction', 'customer_references'));

    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)

    {

        try {

            $data = array(

                'order_no' => $request->order_no,

                'order_date' => !empty($request->order_date) ? \Carbon::parse($request->order_date)->format('Y-m-d') : null,

                'customer_ref' => $request->customer_ref,

            );


            Transaction::where('id', $id)->update($data);


            $output = [

                'success' => 1,

                'msg' => __('message.success')

            ];

        } catch (\Exception $e) {

            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => 0,

                'msg' => __('messages.something_went_wrong')

            ];

        }


        return redirect()->back()->with('status', $output);

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)

    {

        //

    }


    /**
     * print the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    
    public function getMinimumDate(Request $r){
        $customer_id = $r->get('id');
        
        $customer_date = CustomerStatement::where('customer_id', $customer_id)
                    ->orderBy('date_to', 'desc')
                    ->first();
        // logger('error --->'.json_encode($customer_date));
        return response()->json(['date' => $customer_date ? $customer_date->date_to : null]);
        
    }
    
    public function rePrint($id)

    {

        $statement = CustomerStatement::findOrFail($id);
        
        $reprint_no = $statement->reprint_no;
        
        $statement->reprint_no = $reprint_no + 1;
        $statement->save();

        $contact = Contact::findOrFail($statement->customer_id);
        
        $start_date = $statement->date_from;

        $end_date = $statement->date_to;


        $statement_details = CustomerStatementDetail::where('statement_id', $id)->get();

        $contact_id = $contact->id;
        $business_id = $contact->business_id;
        $ledger_details = [];
        $ledger_details['opening_balance'] = 0;
        
        if($contact->type == 'customer'){
            $ledger_details['beginning_balance'] = $this->contactUtil->getCustomerBf($contact_id,$business_id,$start_date);
            $ledger_details['balance_details'] = $this->contactUtil->getCustomerBalance($contact_id,$business_id);
        }
        
        if($contact->type == 'supplier'){
            $ledger_details['beginning_balance'] = $this->contactUtil->getSupplierBf($contact_id,$business_id,$start_date);
            $ledger_details['balance_details'] = $this->contactUtil->getSupplierBalance($contact_id,$business_id);
        }

        $business_details = $this->businessUtil->getDetails($contact->business_id);

        $location_details = BusinessLocation::where('business_id', $contact->business_id)->first();

        $for_pdf = 1;

        $reprint = 1;
        
        $logo = CustomerStatementLogo::find($statement->logo);


        return view('customer_statement.print')->with(compact(
            
            'logo',

            'contact',

            'ledger_details',

            'business_details',

            'for_pdf',

            'location_details',

            'statement_details',

            'statement',

            'reprint',
            'start_date',
            'end_date',
            'reprint_no'

        ));

    }


    public function getStatementHeader(Request $request, $statement_no)

    {

        $contact_id = $request->customer_id;

        $start_date = $request->start_date;

        $end_date = $request->end_date;
        
        
        $contact = Contact::findOrFail($contact_id);
        
        $business_id = $contact->business_id;


        $ledger_details = [];

        $ledger_details['opening_balance'] = 0;
        
        if($contact->type == 'customer'){
            $ledger_details['beginning_balance'] = $this->contactUtil->getCustomerBf($contact_id,$business_id,$start_date);
            $ledger_details['balance_details'] = $this->contactUtil->getCustomerBalance($contact_id,$business_id);
        }
        
        if($contact->type == 'supplier'){
            $ledger_details['beginning_balance'] = $this->contactUtil->getSupplierBf($contact_id,$business_id,$start_date);
            $ledger_details['balance_details'] = $this->contactUtil->getSupplierBalance($contact_id,$business_id);
        }
        


        $business_details = $this->businessUtil->getDetails($contact->business_id);

        $location_details = BusinessLocation::where('business_id', $contact->business_id)->first();

        $opening_balance = Transaction::where('contact_id', $contact_id)->where('type', 'opening_balance')->where('payment_status', 'due')->sum('final_total');

        $for_pdf = 1;

        return view('customer_statement.partials.print_statement_header')->with(compact('contact', 'ledger_details', 'business_details', 'for_pdf', 'location_details', 'statement_no', 'opening_balance'))->render();

    }


    public function getCustomerStatementNo(Request $request)

    {

        $customer_id = $request->customer_id;


        $customer_settings = CustomerStatementSetting::where('customer_id', $customer_id)->first();


        if (!empty($customer_settings)) {

            $starting_no = $customer_settings->starting_no;

        } else {

            $starting_no = 1;

        }


        $count = CustomerStatement::where('customer_id', $customer_id)->count();


        $statement_no = $starting_no + $count;


        $header = (string)$this->getStatementHeader($request, $statement_no);


        return ['statement_no' => $statement_no, 'header' => $header];

    }



    public function getCustomerStatementList(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            // ini_set('memory_limit', -1);
            // ini_set('max_execution_time', '0');

            $query = CustomerStatement::with(['contact', 'user', 'location'])->where('business_id', $business_id);
            if (!empty($request->start_date)) {
                $query->whereDate('date_from', '>=', $request->start_date);
            }

            if (!empty($request->end_date)) {
                $query->whereDate('date_to', '<=', $request->end_date);
            }
            
            if (!empty($request->printed_start)) {
                $query->whereDate('print_date', '>=', $request->printed_start);
            }

            if (!empty($request->printed_end)) {
                $query->whereDate('print_date', '<=', $request->printed_end);
            }

            if (!empty($request->location_id)) {
                $query->where('location_id', $request->location_id);
            }

            if (!empty($request->customer_id)) {
                $query->where('customer_id', $request->customer_id);
            }

            $fuel_tanks = Datatables::of($query)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs"
                            data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                    $html .= '<li><a href="#" data-href="' . action("CustomerStatementController@show", [$row->id]) . '" class="btn-modal" data-container=".customer_statement_modal"><i class="fa fa-external-link" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';
                    $html .= '<li><a href="#" data-href="' . action("CustomerStatementController@rePrint", [$row->id]) . '" class="reprint_statement"><i class="fa fa-print" aria-hidden="true"></i> ' . __("contact.print") . '</a></li>';
                    $html .= '<li><a href="#" data-href="' . action("CustomerStatementController@rePrint", [$row->id]) . '" class="pdf_statement"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> ' . __("business.pdf") . '</a></li>';
                    $html .= '<li><a href="#" data-href="' . action("CustomerStatementController@rePrint", [$row->id]) . '" class="email_statement"><i class="fa fa-envelope" aria-hidden="true"></i> ' . __("business.email") . '</a></li>'; 
                    
                    return $html;
                })
                ->addColumn('customer', function ($row) {
                    return $row->contact ? $row->contact->name : null;
                })
                ->addColumn('username', function ($row) {
                    return $row->user ? $row->user->username : null;
                })
                ->addColumn('location', function ($row) {
                    return $row->location ? $row->location->name : null;
                })
                ->addColumn('amount', function ($row) {
                    return '<span class="display_currency amount" data-currency_symbol="true" data-orig-value="'.$row->details()->sum('invoice_amount').'">'.$this->commonUtil->num_f($row->details()->sum('invoice_amount')).'</span>';
                    
                })
                ->removeColumn('id');

            return $fuel_tanks->rawColumns(['action', 'amount'])->make(true);
        }
    }
}
