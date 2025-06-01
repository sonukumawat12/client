<?php


namespace Modules\Vat\Http\Controllers;



use App\Account;

use Illuminate\Routing\Controller;

use App\Contact;

;

use App\Utils\Util;

use App\Transaction;

use App\ContactLedger;

use App\BusinessLocation;

use App\Utils\ModuleUtil;

use App\CustomerReference;

use Modules\Vat\Entities\VatCustomerStatement;

use App\CustomerStatement;

use App\Utils\ProductUtil;

use App\TransactionPayment;

use App\Utils\BusinessUtil;

use App\Utils\ContactUtil;

use Illuminate\Http\Request;

use App\Utils\TransactionUtil;

use Modules\Vat\Entities\VatCustomerStatementDetail;

use Modules\Vat\Entities\CustomerStatementFontSetting;  

// use App\VatStatementLogo;

use App\CustomerStatementSetting;

use Mpdf\Mpdf;

use Illuminate\Http\Response;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Modules\Superadmin\Entities\HelpExplanation;
use Illuminate\Support\Str;

use Modules\Fleet\Entities\RouteProduct;
use Modules\Petro\Entities\CustomerPayment;

use Maatwebsite\Excel\Facades\Excel as MatExcel;
use App\Exports\CustomerStatement as CustomerStatementExport;

use App\Exports\CustomerStatementPmt as CustomerStatementPmtExport;

use Modules\Vat\Entities\VatStatementPrefix;
use Modules\Vat\Entities\VatStatementLogo;
use Spatie\Activitylog\Models\Activity;
use Modules\Superadmin\Entities\Subscription;

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
        
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereDate('transactions.transaction_date', '>=', $start_date);
                $query->whereDate('transactions.transaction_date', '<=', $end_date);
            }
            
            if (!empty($request->input('reference'))) {
                $reference_id = $request->input('reference');
                $customer_ref = CustomerReference::find($reference_id);
                $query->where('transactions.customer_ref', $customer_ref->reference);
            }

            $query->where('transactions.contact_id', '!=', null);
            if (!empty($request->input('customer_id'))) {
                $cid = $request->input('customer_id');
                $query->where('transactions.contact_id', $cid);
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
                ->addColumn('customer_name', function ($row) {
                    return $row->customer_name;
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
                        if(!empty($row->reference)){
                            return $row->reference;
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
                        
                        $final_total = $row->sold_qty*$row->p_unit_price;

                    } else {

                        $final_total = $row->final_total;

                    }

                    if(!empty($row->total_discount)){
                        $final_total = $final_total - $row->total_discount;
                    }
                    return number_format($final_total,'2','.',',');
                })
                ->editColumn(

                    'unit_price',
                    
                    function ($row){
                        if(!empty($row->p_unit_price)){
                            if(!empty($row->product_discount)){
                                return $this->commonUtil->num_uf($row->p_unit_price - $row->product_discount);
                            } else {
                                return $this->commonUtil->num_uf($row->p_unit_price);
                            }
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

        $statement_no = $this->__getStatementNo();
        
        $logos = VatStatementLogo::where('business_id',$business_id)->select('*')->pluck('image_name','id');
        
        $business_id = request()->session()->get('business.id');
        $invoice2_settings = CustomerStatementFontSetting::where('business_id',$business_id)->first()->settings ?? json_encode(array());
        $invoice2_settings = (object) json_decode($invoice2_settings);
        $reference = CustomerReference::where('business_id',$business_id)->pluck('reference','id');
        
        
        return view('vat::customer_statement.index')->with(compact('customers','reference', 'statement_no','logos','invoice2_settings'));

    }
    
    

    public function updateSetting(Request $request)
    {
        try {
            
            $business_id = request()->session()->get('business.id');
            $data  = request()->except('_token');
            DB::beginTransaction();
            
            $tdata = array('business_id' => $business_id,'settings' => json_encode($data));
            CustomerStatementFontSetting::updateOrCreate(['business_id' => $business_id],$tdata);
               

            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
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
    
    public function updatePrefixes(){
        $prefixes = VatCustomerStatement::selectRaw("SUBSTRING_INDEX(statement_no, '-', 1) AS prefix")
                                ->distinct()
                                ->pluck('prefix');

        foreach($prefixes as $prefix){
            $stms = VatCustomerStatement::where('statement_no', 'like', "$prefix-%")
                                         ->orderBy('id', 'ASC')
                                         ->get();
            $starting = 0;
            foreach($stms as $st){
                $current_bill = $st->statement_no;
                $curr_arr = explode('-',$current_bill);
                $current_no = (int) $curr_arr[sizeof($curr_arr)-1];
                
                if($starting == 0){
                    $starting = $current_no;
                }
                
                $st->statement_no =  $curr_arr[0]."-".$starting;
                $st->save();
                
                $starting++;
            }
        }
        
        echo "done";
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
            ->leftJoin('fleets as f', 'route_operations.fleet_id', '=', 'f.id')
            ->leftJoin('variations', 'p.id', '=', 'variations.product_id')
            ->leftJoin('units', 'p.unit_id', '=', 'units.id')
            ->leftJoin('product_variations as pv', 'variations.product_variation_id', '=', 'pv.id')
            ->leftJoin('customers as cut', 'transactions.contact_id', '=', 'cut.id')
            ->leftJoin('customer_references as cRef', 'transactions.customer_ref', '=', 'cRef.reference')
            ->selectRaw('
                p.name as product,
                COALESCE(tsl.unit_price, settlement_credit_sale_payments.price) as p_unit_price,
                p.id as product_id,
                
                variations.sell_price_inc_tax as unit_price,
                
                variations.default_sell_price as unit_price_before_tax,
                
                COALESCE(tsl.quantity, route_operations.qty, settlement_credit_sale_payments.qty) as sold_qty,
                
                transactions.transaction_date as transaction_date,
                transactions.type as tran_type,
                transactions.ref_no,
                transactions.invoice_no,
                transactions.customer_ref,
                transactions.order_no,
                transactions.order_date,
                transactions.contact_id,
                transactions.sub_type,
                f.vehicle_number,
                transactions.final_total,
                transactions.id as transaction_id,
                route_operations.order_number,
                tsl.id as tsl_id,
                settlement_credit_sale_payments.discount as product_discount,
                settlement_credit_sale_payments.total_discount as total_discount,
                cut.first_name as customer_name,
                cRef.reference,
                (SELECT SUM(IF(TP.is_return = 1, -1*TP.amount, TP.amount))
                 FROM transaction_payments AS TP
                 WHERE TP.transaction_id = transactions.id) as total_paid')
            ->where(function ($query) use ($business_id) {
                $query->where('p.business_id', $business_id)
                    ->orWhere('transactions.business_id', $business_id);
            })
            ->whereIn('transactions.type', ['property_sell','sell'])
           
            ->whereIn('transactions.payment_status', ['due', 'partial'])
            ->whereNotNull('p.name');
                
            
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
            
            
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereDate('transactions.transaction_date', '>=', $start_date);
                $query->whereDate('transactions.transaction_date', '<=', $end_date);
            }

            $query->where('transactions.contact_id', '!=', null);
            if (!empty($request->input('customer_id'))) {
                $cid = $request->input('customer_id');
                $query->where('transactions.contact_id', $cid);
            }

            
            $transactions = $query->get();
            

            DB::beginTransaction();
            
            $contact = Contact::findOrFail($request->customer_id);
            
            $statement_no = $this->__getStatementNo();
            

            $statement = VatCustomerStatement::create([

                'business_id' => $business_id,
                
                'logo' => $request->logo,
                
                'customer_id' => $request->customer_id,

                'statement_no' => $statement_no,

                'print_date' => date('Y-m-d'),

                'date_from' => \Carbon::parse($start_date)->format('Y-m-d'),

                'date_to' => \Carbon::parse($end_date)->format('Y-m-d'),

                'added_by' => Auth::user()->id,
                
                'price_adjustment' => request()->price_adjustment

            ]);
            
            $pa_transaction = $this->transactionUtil->createOrUpdatePriceAdjustment($statement,$statement_no);
            
            

            foreach ($transactions as $transaction) {
               $invoice_no = $transaction->invoice_no;

                VatCustomerStatementDetail::create([

                    'business_id' => $business_id,

                    'statement_id' => $statement->id,

                    'date' => \Carbon::parse($transaction->transaction_date)->format('Y-m-d'),

                    'invoice_no' => $invoice_no,

                    'order_no' => $transaction->order_no, 
                    
                    'vehicle_number' => !empty($transaction->vehicle_number) ? $transaction->vehicle_number : "",
                    
                    'product' => $transaction->product,

                    'unit_price' => !empty($transaction->p_unit_price) ? $transaction->p_unit_price : "1",

                    'qty' => $transaction->sold_qty,

                    'invoice_amount' => $transaction->final_total,

                    'product_id' => $transaction->product_id,
                    
                    'transaction_id' => $transaction->transaction_id,
                    
                    'unit_price_before_tax' => $transaction->unit_price_before_tax

                ]);

            }



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
    
    
    public function convertVAT(Request $request,$id){
        try {
            
            
            $default_start = new \Carbon('first day of this month');

            $default_end = new \Carbon('last day of this month');
            
            $customer_statement = CustomerStatement::findOrFail($id);
            
            $business_id = $customer_statement->business_id;
            
            $logo = VatStatementLogo::where('business_id',$business_id)->first()->id ?? null;


            $start_date = !empty($customer_statement->date_from) ? $customer_statement->date_from : $default_start->format('Y-m-d');

            $end_date = !empty($customer_statement->date_to) ? $customer_statement->date_to : $default_end->format('Y-m-d');

            $query = $this->__getStatement($business_id);
            
            
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereDate('transactions.transaction_date', '>=', $start_date);
                $query->whereDate('transactions.transaction_date', '<=', $end_date);
            }

            $query->where('transactions.contact_id', '!=', null);
            if (!empty($customer_statement->customer_id)) {
                $cid = $customer_statement->customer_id;
                $query->where('transactions.contact_id', $cid);
            }

            
            $transactions = $query->get();
            

            DB::beginTransaction();
            
            $contact = Contact::findOrFail($customer_statement->customer_id);
            
            $statement_no = $this->__getStatementNo();
            

            $statement = VatCustomerStatement::create([

                'business_id' => $business_id,
                
                'logo' => $logo,
                
                'customer_id' => $customer_statement->customer_id,

                'statement_no' => $statement_no,

                'print_date' => date('Y-m-d'),

                'date_from' => $start_date,

                'date_to' => $end_date,

                'added_by' => Auth::user()->id,
                
                'price_adjustment' => 0,
                
                'is_converted' => 1,
                
                'converted_by' => auth()->user()->id,
                
                'linked_vat_statement' => $customer_statement->id

            ]);
            
            

            foreach ($transactions as $transaction) {
               $invoice_no = $transaction->invoice_no;

                VatCustomerStatementDetail::create([

                    'business_id' => $business_id,

                    'statement_id' => $statement->id,

                    'date' => \Carbon::parse($transaction->transaction_date)->format('Y-m-d'),

                    'invoice_no' => $invoice_no,

                    'order_no' => $transaction->order_no, 
                    
                    'vehicle_number' => !empty($transaction->vehicle_number) ? $transaction->vehicle_number : "",
                    
                    'product' => $transaction->product,

                    'unit_price' => !empty($transaction->p_unit_price) ? $transaction->p_unit_price : "1",

                    'qty' => $transaction->sold_qty,

                    'invoice_amount' => $transaction->final_total,

                    'product_id' => $transaction->product_id,
                    
                    'transaction_id' => $transaction->transaction_id,
                    
                    'unit_price_before_tax' => $transaction->unit_price_before_tax

                ]);

            }
            
            
            $customer_statement->is_converted = 1;
            $customer_statement->converted_by = auth()->user()->id;
            $customer_statement->linked_vat_statement = $statement->id;
            $customer_statement->save();



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
        $statement = VatCustomerStatement::findOrFail($id);
        
        $reprint_no = $statement->reprint_no;
        
        $statement->reprint_no = $reprint_no + 1;
        $statement->save();

        $contact = Contact::findOrFail($statement->customer_id);
        
        $start_date = $statement->date_from;

        $end_date = $statement->date_to;


        $statement_details = VatCustomerStatementDetail::leftjoin('transactions','transactions.id','vat_customer_statement_details.transaction_id')->where('statement_id', $id)->select('vat_customer_statement_details.*','transactions.transaction_date as tdate')->get();

        $business_id = $contact->business_id;

        $location_details = BusinessLocation::where('business_id', $contact->business_id)->first();
        
        $business_details = $this->businessUtil->getDetails($contact->business_id);
        
        $logo = VatStatementLogo::find($statement->logo);
        $reference = CustomerReference::where('business_id',$business_id)->pluck('reference','id');

        return view('vat::customer_statement.show')->with(compact(
            'logo',
            'reference',
            
            'contact',

            'business_details',

            'location_details',

            'statement_details',

            'statement',
            
            'start_date',
            
            'end_date',
            
            'reprint_no',
            'id'

        ));
    }
    
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)

    {

        $statement = VatCustomerStatement::findOrFail($id);

        return view('vat::customer_statement.edit')->with(compact('statement'));

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

                'date_from' => $request->date_from,

                'date_to' => $request->date_to,

            );
            
            DB::beginTransaction();


            VatCustomerStatement::where('id', $id)->update($data);
            VatCustomerStatementDetail::where('statement_id',$id)->forceDelete();
            $statement = VatCustomerStatement::findOrFail($id);
            $business_id = $statement->business_id;
            
            $start_date = $request->date_from;

            $end_date = $request->date_to;

            $cid = $statement->customer_id;
            
            $query = $this->__getStatement($business_id);
            
            
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereDate('transactions.transaction_date', '>=', $start_date);
                $query->whereDate('transactions.transaction_date', '<=', $end_date);
            }

            $query->where('transactions.contact_id', '!=', null);
            if (!empty($cid)) {
                $query->where('transactions.contact_id', $cid);
            }

            
            $transactions = $query->get();
            
            
            $contact = Contact::findOrFail($cid);
            

            foreach ($transactions as $transaction) {
               $invoice_no = $transaction->invoice_no;

                VatCustomerStatementDetail::create([

                    'business_id' => $business_id,

                    'statement_id' => $statement->id,

                    'date' => \Carbon::parse($transaction->transaction_date)->format('Y-m-d'),

                    'invoice_no' => $invoice_no,

                    'order_no' => $transaction->order_no, 
                    
                    'vehicle_number' => !empty($transaction->vehicle_number) ? $transaction->vehicle_number : "",
                    
                    'product' => $transaction->product,

                    'unit_price' => !empty($transaction->p_unit_price) ? $transaction->p_unit_price : "1",

                    'qty' => $transaction->sold_qty,

                    'invoice_amount' => $transaction->final_total,

                    'product_id' => $transaction->product_id,
                    
                    'transaction_id' => $transaction->transaction_id,
                    
                    'unit_price_before_tax' => $transaction->unit_price_before_tax

                ]);

            }



            DB::commit();

            $output = [

                'success' => 1,

                'msg' => __('messages.success')

            ];


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
        
        if (request()->ajax()) {
            try {
                
                $customer_statement = VatCustomerStatement::findOrFail($id);
                
                $changed_msg = "VAT Customer Statement #".$customer_statement->statement_no." has been deleted by ".auth()->user()->username;
                
                $activity = new Activity();
                $activity->log_name = "VAT Customer Statement";
                $activity->description = "delete";
                $activity->subject_id = $id;
                $activity->subject_type = "Modules\Vat\Entities\VatCustomerStatement";
                $activity->causer_id = auth()->user()->id;
                $activity->causer_type = 'App\User';
                $activity->properties = $changed_msg ;
                $activity->created_at = date('Y-m-d H:i');
                $activity->updated_at = date('Y-m-d H:i');
                
                // Save the activity
                $activity->save();
                
                $customer_statement->delete();
                VatCustomerStatementDetail::where('statement_id',$id)->delete();
                
                $output = [
                    'success' => true,
                    'msg' => __("lang_v1.success")
                ];
                
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
    
    public function destroyPayments($id)
    {
        
        if (request()->ajax()) {
            try {
                DB::beginTransaction();
                $customer_statement = VatCustomerStatement::findOrFail($id);
                
                $changed_msg = "Payments for VAT Customer Statement #".$customer_statement->statement_no." has been deleted by ".auth()->user()->username;
                
                $activity = new Activity();
                $activity->log_name = "VAT Customer Statement";
                $activity->description = "delete";
                $activity->subject_id = $id;
                $activity->subject_type = "Modules\Vat\Entities\VatCustomerStatement";
                $activity->causer_id = auth()->user()->id;
                $activity->causer_type = 'App\User';
                $activity->properties = $changed_msg ;
                $activity->created_at = date('Y-m-d H:i');
                $activity->updated_at = date('Y-m-d H:i');
                
                // Save the activity
                $activity->save();
                
                $this->transactionUtil->deleteVatBulkPayments($id);
                
                DB::commit();
                
                $output = [
                    'success' => true,
                    'msg' => __("lang_v1.success")
                ];
                
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
                
                DB::rollback();
                
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }
            return $output;
        }
    }

    /**
     * print the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    
    public function getMinimumDate(Request $r){
        $customer_id = $r->get('id');
        
        $customer_date = VatCustomerStatement::where('customer_id', $customer_id)
                    ->orderBy('date_to', 'desc')
                    ->first();
        // logger('error --->'.json_encode($customer_date));
        return response()->json(['date' => $customer_date ? $customer_date->date_to : null]);
        
    }
    
    public function rePrint(Request $request,$id)

    {
        
        $statement = VatCustomerStatement::findOrFail($id);
        
        $reprint_no = $statement->reprint_no;
        
        $statement->reprint_no = $reprint_no + 1;
        $statement->save();

        $contact = Contact::findOrFail($statement->customer_id);
        
        $start_date = $statement->date_from;

        $end_date = $statement->date_to;


        $statement_details = VatCustomerStatementDetail::leftjoin('transactions','transactions.id','vat_customer_statement_details.transaction_id')->where('statement_id', $id)->select('vat_customer_statement_details.*','transactions.transaction_date as tdate')->get();

        $business_id = $contact->business_id;

        $location_details = BusinessLocation::where('business_id', $contact->business_id)->first();
        
        $business_details = $this->businessUtil->getDetails($contact->business_id);
        
        $logo = VatStatementLogo::find($statement->logo);
        

        return view('vat::customer_statement.print')->with(compact(
            
            'logo',
            
            'contact',

            'business_details',

            'location_details',

            'statement_details',

            'statement',
            
            'start_date',
            
            'end_date',
            
            'reprint_no'

        ));

    }
    
    public function exportExcel($id)

    {

        $statement = VatCustomerStatement::findOrFail($id);
        
        $reprint_no = $statement->reprint_no;
        
        $statement->reprint_no = $reprint_no + 1;
        $statement->save();

        $contact = Contact::findOrFail($statement->customer_id);
        
        $start_date = $statement->date_from;

        $end_date = $statement->date_to;


        $statement_details = VatCustomerStatementDetail::leftjoin('transactions','transactions.id','vat_customer_statement_details.transaction_id')->where('statement_id', $id)->select('vat_customer_statement_details.*','transactions.transaction_date as tdate')->get();

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
        
        $logo = VatStatementLogo::find($statement->logo);
        
        $response = MatExcel::download(new CustomerStatementExport(
            $logo,
            $contact,
            $ledger_details,
            $business_details,
            $for_pdf,
            $location_details,
            $statement_details,
            $statement,
            $reprint,
            $start_date,
            $end_date,
            $reprint_no
        ),"CustomerStatement.xls");

        ob_end_clean();
        return $response;
      

    }
  
    public function getStatementHeader(Request $request, $statement_no)

    {

        $contact_id = $request->customer_id;

        $start_date = $request->start_date;

        $end_date = $request->end_date;
        
        
        $contact = Contact::findOrFail($contact_id);
        
        $business_id = $contact->business_id;

        $location_details = BusinessLocation::where('business_id', $contact->business_id)->first();
        
        $business_details = $this->businessUtil->getDetails($contact->business_id);

        $for_pdf = 1;

        return view('vat::customer_statement.partials.print_statement_header')->with(compact('contact',  'for_pdf', 'location_details', 'statement_no','business_details'))->render();

    }
    
    public function getStatementFooter(Request $request, $statement_no)

    {

        $contact_id = $request->customer_id;

        $start_date = $request->start_date;

        $end_date = $request->end_date;
        
        $price_adjustment = $request->price_adjustment ?? 0;
        
        
        $contact = Contact::findOrFail($contact_id);
        
        $business_id = $contact->business_id;

        
        $query = $this->__getStatement($business_id);
        if (!empty($start_date) && !empty($end_date)) {
            $query->whereDate('transactions.transaction_date', '>=', $start_date);
            $query->whereDate('transactions.transaction_date', '<=', $end_date);
        }
        $query->where('transactions.contact_id', '!=', null);
        if (!empty($request->input('customer_id'))) {
            $cid = $request->input('customer_id');
            $query->where('transactions.contact_id', $cid);
        }
        $total = 0;
        $transactions = $query->get();
        
        foreach($transactions as $row){
            if ($row->tran_type == 'sell') {
                        
                $total += ($row->sold_qty*$row->p_unit_price);

            } else {

                $total += ($row->final_total);

            }
        }
    
        return view('vat::customer_statement.partials.print_statement_footer')->with(compact('total','price_adjustment'))->render();

    }
    public function getPriceAdjustment(Request $request, $statement_no)
    {
        $contact_id = $request->customer_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $price_adjustment = $request->price_adjustment ?? 0;

        $contact = Contact::findOrFail($contact_id);
        $business_id = $contact->business_id;

        $query = $this->__getStatement($business_id);
        
        if (!empty($start_date) && !empty($end_date)) {
            $query->whereDate('transactions.transaction_date', '>=', $start_date);
            $query->whereDate('transactions.transaction_date', '<=', $end_date);
        }

        $query->where('transactions.contact_id', '!=', null);
        
        if (!empty($request->input('customer_id'))) {
            $cid = $request->input('customer_id');
            $query->where('transactions.contact_id', $cid);
        }

        $total = 0;
        $transactions = $query->get();

        foreach ($transactions as $row) {
            if ($row->tran_type == 'sell') {
                $total += ($row->sold_qty * $row->p_unit_price);
            } else {
                $total += ($row->final_total);
            }
        }

        $tax_rate = \App\TaxRate::where('business_id',request()->session()->get('business.id'))->first()->amount ?? 0;
        $pre_tax = $total / (1+ ($tax_rate/100));
        $tax_total = ($tax_rate/100) * $pre_tax;
        $grand_total = $tax_total + $pre_tax;
        // Return the JSON response
        $totalAmount = $grand_total+$price_adjustment;
        $fTotalAmount = round($totalAmount, 2);
        $adjustmentAmount = $fTotalAmount - $totalAmount;
        return $adjustmentAmount;
    }
    public function __getStatementNo(){
        $prefixes = VatStatementPrefix::get()->last();
        $business_id = request()->session()->get('business.id');
        
       if (!empty($prefixes))
       {
        $existing = VatCustomerStatement::where('business_id',$business_id)->where('statement_no', 'like', '%'.$prefixes->prefix.'%')->get()->last();
        
        if(!empty($existing)){
            $current_bill = $existing->statement_no;
            $curr_arr = explode('-',$current_bill);
            $current_no = (int) $curr_arr[sizeof($curr_arr)-1];
            $new_no = $current_no > $prefixes->starting_no ? $current_no + 1 : $prefixes->starting_no + 1;
        }else{
            $current_no = $prefixes->starting_no;
            $new_no = $current_no;
        }
        
        $statement_no = $prefixes->prefix."-".($new_no);
        
        return $statement_no;
       }
       else
          return "";
    }


    public function getCustomerStatementNo(Request $request)

    {
        
        $statement_no = $this->__getStatementNo();

        $header = (string)$this->getStatementHeader($request, $statement_no);
        $footer = (string)$this->getStatementFooter($request, $statement_no);
        $priceAdjustmentAmt = (string)$this->getPriceAdjustment($request, $statement_no);

        return ['statement_no' => $statement_no, 'header' => $header,'footer' => $footer, 'priceAdjustmentAmt' => $priceAdjustmentAmt];

    }
    

    public function getCustomerStatementList(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            
            $query = VatCustomerStatement::leftjoin('customer_statements','customer_statements.id','vat_customer_statements.linked_vat_statement')
                    ->leftjoin('users as u','u.id','vat_customer_statements.converted_by')
                    ->with(['contact', 'user', 'location'])->where('vat_customer_statements.business_id', $business_id)
                    ->select('vat_customer_statements.*','u.username as user_converted','customer_statements.statement_no as vat_statement','vat_customer_statements.print_date as vat_date');;
            if (!empty($request->start_date)) {
                $query->whereDate('vat_customer_statements.date_from', '>=', $request->start_date);
            }

            if (!empty($request->end_date)) {
                $query->whereDate('vat_customer_statements.date_to', '<=', $request->end_date);
            }
            
            if (!empty($request->printed_start)) {
                $query->whereDate('vat_customer_statements.print_date', '>=', $request->printed_start);
            }

            if (!empty($request->printed_end)) {
                $query->whereDate('vat_customer_statements.print_date', '<=', $request->printed_end);
            }

            if (!empty($request->location_id)) {
                $query->where('vat_customer_statements.location_id', $request->location_id);
            }

            if (!empty($request->customer_id)) {
                $query->where('vat_customer_statements.customer_id', $request->customer_id);
            }

            $fuel_tanks = Datatables::of($query)
                ->addColumn('action', function ($row) use($business_id){
                    $html = '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs"
                            data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                    $html .= '<li><a href="#" data-href="' . action("\Modules\Vat\Http\Controllers\CustomerStatementController@show", [$row->id]) . '" class="btn-modal" data-container=".customer_statement_modal"><i class="fa fa-external-link" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';
                    $html .= '<li><a href="#" data-href="' . action("\Modules\Vat\Http\Controllers\CustomerStatementController@rePrint", [$row->id]) . '" class="reprint_statement"><i class="fa fa-print" aria-hidden="true"></i> ' . __("contact.print") . '</a></li>';
                    $html .= '<li><a href="#" data-href="' . action("\Modules\Vat\Http\Controllers\CustomerStatementController@rePrint", [$row->id]) . '" class="pdf_statement"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> ' . __("business.pdf") . '</a></li>';
                    
                    $individual_ids = VatCustomerStatementDetail::where('statement_id',$row->id)->pluck('transaction_id') ?? [];
                    $due_transactions = Transaction::whereIn('id',$individual_ids)->whereIn('payment_status',['due','partial'])->pluck('id') ?? [];
                    
                    if(sizeof($due_transactions) != 0){
                        $html .= '<li><a href="'.action("TransactionPaymentController@getPayVatDue", [$row->id]).'?type=sell" class="pay_sale_due"><i class="fa fa-credit-card" aria-hidden="true"></i> '.__("contact.pay_due_amount").'</a></li>';
                        if(auth()->user()->can('edit.vat_statement')){
                            $html .= '<li><a href="#" data-href="' . action("\Modules\Vat\Http\Controllers\CustomerStatementController@edit", [$row->id]) . '" class="btn-modal" data-container=".customer_statement_modal"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i> ' . __("messages.edit") . '</a></li>';
                        }
                    }
                    
                    $paid_customer_statement = TransactionPayment::where('linked_vat_customer_statement',$row->id)->count();
                    
                    $pacakge_details = [];
                    $subscription = Subscription::active_subscription($business_id);
                    if (!empty($subscription)) {
                        $pacakge_details = $subscription->package_details;
                    }
                    
                    if($paid_customer_statement > 0){
                        if(auth()->user()->can('vat.delete_customer_statement') && (!empty($pacakge_details['vat.delete_customer_statement']) || !array_key_exists('vat.delete_customer_statement',$pacakge_details))){
                            $html .= '<li><a data-href="' . action('\Modules\Vat\Http\Controllers\CustomerStatementController@destroyPayments', [$row->id]) . '" class="delete_customer_statement"><i class="fa fa-trash"></i>' . __("lang_v1.delete_payments") . '</a></li>';
                        }
                        
                    }else{
                        if(auth()->user()->can('vat.delete_statement_payment') && (!empty($pacakge_details['vat.delete_statement_payment']) || !array_key_exists('vat.delete_statement_payment',$pacakge_details))){
                            $html .= '<li><a data-href="' . action('\Modules\Vat\Http\Controllers\CustomerStatementController@destroy', [$row->id]) . '" class="delete_customer_statement"><i class="fa fa-trash"></i>' . __("messages.delete") . '</a></li>';
                        }
                        
                    }
                    
                    
                    
                    return $html;
                })
                ->addColumn('description',function($row){
                    if($row->is_converted == 1){
                        $html = "<span class='badge bg-success'>".__('contact.converted_from_vat')."</span><br>";
                        $html .= "<span>".__('contact.statement_no')."<b> ".$row->vat_statement."</b> ".__('contact.on')."<b> ".$this->commonUtil->format_date($row->vat_date)."</b></span><br>";
                        $html.= "<span>".$row->user_converted."</span><br>";
                        
                        return $html;
                    }
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
                ->addColumn('payment_status',function($row){
                    $individual_ids = VatCustomerStatementDetail::where('statement_id',$row->id)->pluck('transaction_id') ?? [];
                    $due_transactions = Transaction::whereIn('id',$individual_ids)->whereIn('payment_status',['due','partial'])->pluck('id') ?? [];
                    
                    if(sizeof($due_transactions) == 0){
                        // paid
                        $html = "<span class='badge bg-success'>".__('vat::lang.paid')."</span>";
                    }else{
                        if(sizeof($individual_ids) == sizeof($due_transactions)){
                            // due
                            $html = "<span class='badge bg-danger'>".__('vat::lang.due')."</span>";
                        }else{
                            // partial
                            $html = "<span class='badge bg-warning'>".__('vat::lang.partial')."</span>";
                        }
                    }
                    
                    return $html;
                })
                ->addColumn('amount', function ($row) {
                    return '<span class="display_currency amount" data-currency_symbol="true" data-orig-value="'.$row->details()->sum('invoice_amount').'">'.$this->commonUtil->num_f($row->details()->sum('invoice_amount')).'</span>';
                    
                })
                ->removeColumn('id');

            return $fuel_tanks->rawColumns(['action', 'amount','payment_status','description'])->make(true);
        }
    }
}
