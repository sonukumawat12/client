<?php


namespace App\Http\Controllers;



use App\AccountTransaction;
use App\Account;
use App\AccountType;

use App\Contact;

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
use Modules\Petro\Entities\CustomerPayment;

use Maatwebsite\Excel\Facades\Excel as MatExcel;
use App\Exports\CustomerStatement as CustomerStatementExport;

use App\Exports\CustomerStatementPmt as CustomerStatementPmtExport;
use Spatie\Activitylog\Models\Activity;
use App\User;
use Modules\Superadmin\Entities\Subscription;
use Modules\Vat\Entities\VatCustomerStatementDetail;

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

    public function getUserActivityReport(Request $request)

    {


        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {

            $business_users = User::where('business_id', $business_id)->pluck('id')->toArray();

            $activity = Activity::whereIn('causer_id', $business_users);

            if (!empty(request()->user) && request()->user != 'All') {

                $user = request()->user;

                $activity->where('causer_id', $user);
            }

            if (!empty(request()->type) && request()->type != 'All') {
                $type = request()->type;

                $activity->where('description', $type);
            }
            if (!empty(request()->subject) && request()->subject != 'All') {

                $subject = request()->subject;

                $activity->where('log_name', $subject);
            }

            if (!empty(request()->startDate) && !empty(request()->endDate)) {


                $activity->whereDate('created_at', '>=', request()->startDate);

                $activity->whereDate('created_at', '<=', request()->endDate);
            }

            $datatable = Datatables::of($activity)

                ->editColumn('created_at', '{{ @format_datetime($created_at) }}')

                ->removeColumn('id')

                ->editColumn('causer_id', function ($row) {

                    $causer_id = $row->causer_id;

                    $username = User::where('id', $causer_id)->select('username')->first()->username;

                    return $username;
                })

                ->addColumn('description_details', function ($row) {
                    $attributes = json_decode($row->properties, true);

                    $new = $attributes['attributes'] ?? [];
                    $old = $attributes['old'] ?? [];
                    $html = "";

                    if ($row->description == 'updated') {

                        foreach ($new as $key => $newValue) {
                            if ($key != 'created_at' && $key != 'updated_at' && $key != 'id') {
                                $oldValue = $old[$key] ?? null;

                                if ($newValue !== $oldValue) {
                                    $originalKey = str_replace('_', ' ', ucfirst($key));
                                    if ($originalKey !== 'Account' and $oldValue !== 'Cash') {
                                        $html .= "Original $originalKey $oldValue changed to $newValue <br>";
                                    }
                                }
                            }
                        }
                    } elseif ($row->description == 'deleted') {
                        if ($row->subject_type == 'App\TransactionPayment') {
                            $contact = Contact::find($new['payment_for']);
                            if (!empty($contact)) {
                                $html .= "Contact Name: " . $contact->name . "<br>";
                            }

                            if (!empty($new['amount'])) {
                                $html .= 'Amount: ' . $this->productUtil->num_f($new['amount']) . "<br>";
                            }

                            if (!empty($new['payment_ref_no'])) {
                                $html .= 'Ref No: ' . $new['payment_ref_no'] . "<br>";
                            }
                        } else {
                            return "";
                        }
                    } elseif (($row->description == 'update' || $row->description == 'delete')) {
                        $jsonProperties = $row->properties;

                        $decodedProperties = json_decode($jsonProperties);

                        $text = $decodedProperties[0];

                        $html .= nl2br($text);
                        // $html .= $row->properties;

                    } else {
                        $html = "";
                    }

                    return nl2br($html);
                });

            $rawColumns = ['description_details'];

            return $datatable->rawColumns($rawColumns)

                ->make(true);
        }

        $users = User::where('business_id', $business_id)->pluck('username', 'id');

        $type = Activity::distinct()->pluck('description');
        $subject = Activity::distinct()->pluck('log_name');

        return view('customer_statement.user_activity')

            ->with(compact('users', 'type', 'subject'));
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
            if (!empty($request->input('reference'))) {
                // return $request->input('reference');
                $reference_id = $request->input('reference');
                $customer_ref = CustomerReference::find($reference_id);
                $query->where('transactions.customer_ref', $customer_ref->reference);
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
                        if (!empty($row->product_id)) {
                            $prod_array = json_decode($row->product_id);

                            if (is_array($prod_array)) {
                                foreach ($prod_array as $key => $one) {
                                    $product = RouteProduct::where('id', $one)->first();
                                    if (!empty($product)) {
                                        $amounts .= $product->name;
                                        if ($key != sizeof($prod_array) - 1) {
                                            $amounts .= " + ";
                                        }
                                    }
                                }
                            } else {
                                $product = RouteProduct::where('id', $prod_array)->first();
                                if (!empty($product)) {
                                    $amounts .= $product->name;
                                }
                            }
                        }

                        return $amounts;
                    } else {
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
                        if (!empty($row->sold_qty)) {
                            $qty_array = json_decode($row->sold_qty);
                            if (is_array($qty_array)) {

                                foreach ($qty_array as $key => $one) {
                                    $amounts .=  $this->productUtil->num_f((int)$one);
                                    if ($key != sizeof($qty_array) - 1) {
                                        $amounts .= " + ";
                                    }
                                }
                            } else {
                                $amounts .=  $this->productUtil->num_f((int)$qty_array);
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

                    function ($row) {
                        if (!empty($row->order_no)) {
                            return $row->order_no;
                        } else {
                            return $row->order_number;
                        }
                    }
                )
                ->editColumn(

                    'invoice_no',

                    function ($row) {
                        $html = $row->invoice_no;
                        if ($row->sub_type == 'customer_loan') {
                            $html .= "<br><b>(" . __('petro::lang.customer_loans') . ")</b>";
                        }

                        if ($row->tran_type == 'direct_customer_loan') {
                            $html .= "<br><b>(" . __('lang_v1.direct_loan_to_customer') . ")</b>";
                        }

                        return $html;
                    }
                )
                ->editColumn(

                    'route_name',

                    function ($row) {
                        if (!empty($row->route_name)) {
                            return $row->route_name;
                        }
                    }

                )
                ->editColumn(

                    'vehicle_number',

                    function ($row) {
                        if (!empty($row->vehicle_number)) {
                            return $row->vehicle_number;
                        }
                    }

                )
                ->editColumn(

                    'order_date',

                    function ($row) {
                        if (!empty($row->order_date)) {
                            return $this->commonUtil->format_date($row->order_date);
                        }
                    }



                )

                ->addColumn(

                    'due_amount',
                    function ($row) {

                        $due = 0;

                        if ($row->tran_type == 'sell') {

                            $due = ($row->sold_qty * $row->p_unit_price) - $row->total_paid;
                        } else {

                            $due = $row->final_total - $row->total_paid;
                        }

                        if (!empty($row->total_discount)) {
                            $due = $due - $row->total_discount;
                        }


                        return '<span class="display_currency due" data-currency_symbol="true" data-orig-value="' . $due . '">' . $this->productUtil->num_f($due) . '</span>';
                    }
                )

                ->editColumn(

                    'final_total',
                    function ($row) {

                        if ($row->tran_type == 'sell') {

                            $due = ($row->sold_qty * $row->p_unit_price);
                        } else {

                            $due = ($row->final_total);
                        }
                        if (!empty($row->total_discount)) {
                            $due = $due - $row->total_discount;
                        }

                        return '<span class="display_currency total" data-currency_symbol="true" data-orig-value="' . $due . '">' . $this->productUtil->num_f($due) . '</span>';
                    }
                )
                ->editColumn(

                    'unit_price',

                    function ($row) {
                        if (!empty($row->p_unit_price)) {
                            if (!empty($row->product_discount)) {
                                return $this->commonUtil->num_f($row->p_unit_price - $row->product_discount);
                            } else {
                                return $this->commonUtil->num_f($row->p_unit_price);
                            }
                        }
                    }


                )
                ->editColumn(

                    'qty',

                    function ($row) {
                        if (!empty($row->qty)) {
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

        $reference = CustomerReference::where(['business_id' => $business_id])->pluck('reference', 'id');

        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

        $enable_separate_customer_statement_no = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_separate_customer_statement_no');

        $help_explanations = HelpExplanation::pluck('value', 'help_key');

        $statement_no = CustomerStatement::where('business_id', $business_id)->count();

        $logos = CustomerStatementLogo::where('business_id', $business_id)->select('*')->pluck('image_name', 'id');


        $business_location_id = BusinessLocation::where('business_id', $business_id)->first()->id;
        $payment_methods = $this->transactionUtil->payment_types($business_location_id);

        $statement_nos = TransactionPayment::join('customer_statements', 'customer_statements.id', 'transaction_payments.linked_customer_statement')
            ->whereNull('transaction_id')
            ->whereNotNull('linked_customer_statement')
            ->where('transaction_payments.business_id', $business_id)->pluck('customer_statements.statement_no', 'customer_statements.id');

        return view('customer_statement.index')->with(compact('statement_nos', 'payment_methods', 'logos', 'customers', 'enable_separate_customer_statement_no', 'business_locations', 'statement_no', 'help_explanations', 'reference'));
    }

    public function listStatementPayments(Request $request)

    {
        $business_id = request()->session()->get('business.id');


        $default_start = new \Carbon('first day of this month');

        $default_end = new \Carbon('last day of this month');


        $start_date = !empty($request->get('start_date')) ? date('Y-m-d', strtotime($request->get('start_date'))) : $default_start->format('Y-m-d');

        $end_date = !empty($request->get('end_date')) ? date('Y-m-d', strtotime($request->get('end_date'))) : $default_end->format('Y-m-d');




        if ($request->ajax()) {

            $query = TransactionPayment::leftjoin('users', 'users.id', 'transaction_payments.created_by')
                ->leftjoin('customer_statements', 'customer_statements.id', 'transaction_payments.linked_customer_statement')
                ->leftjoin('contacts', 'contacts.id', 'transaction_payments.payment_for')
                ->where('transaction_payments.business_id', $business_id)
                ->whereNull('transaction_id')
                ->whereNotNull('linked_customer_statement')
                ->whereDate('paid_on', '>=', $start_date)
                ->whereDate('paid_on', '<=', $end_date)
                ->select('contacts.name as customer_name', 'customer_statements.statement_no', 'users.username', 'transaction_payments.*');


            if (!empty($request->input('customer_id'))) {
                $cid = $request->input('customer_id');
                $query->where('transaction_payments.payment_for', $cid);
            }

            if (!empty($request->input('statement_no'))) {
                $query->where('customer_statements.id', $request->input('statement_no'));
            }


            if (!empty($request->input('payment_method'))) {
                $query->where('transaction_payments.method', $request->input('payment_method'));
            }

            $products = $query->get();



            $datatable = DataTables::of($products)
                ->removeColumn('id')
                ->editColumn(
                    'paid_on',
                    '{{@format_datetime($paid_on)}}'
                )
                ->editColumn(
                    'created_at',
                    '{{@format_datetime($created_at)}}'
                )
                ->addColumn('statement_amount', function ($row) {
                    $amt = CustomerStatementDetail::where('statement_id', $row->linked_customer_statement)->sum('invoice_amount');
                    return $this->transactionUtil->num_f($amt);
                })

                ->editColumn('method', function ($row) {
                    $payment_method_html = ucfirst(str_replace('_', ' ', $row->method));

                    if (in_array(strtolower($row->method), ['bank_transfer', 'direct_bank_deposit', 'bank', 'cheque'])) {
                        $acc_id = $row->account_id;
                        $bank_account = Account::find($acc_id);
                        if (!empty($bank_account)) {
                            $payment_method_html .= '<br><b>Bank Name:</b> ' . $bank_account->name . '</br>';
                        }

                        if (!empty($row->cheque_number)) {
                            $payment_method_html .= '<b>Cheque Number:</b> ' . $row->cheque_number . '</br>';
                        }
                    }

                    return $payment_method_html;
                })
                ->editColumn(

                    'amount',

                    '{{@num_format($amount)}}'

                );


            $raw_columns = [
                'method'
            ];


            return $datatable->rawColumns($raw_columns)->make(true);
        }
    }


    public function __getStatement($business_id)
    {
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
            ->join('customer_references as cRef', function ($join) {
                $join->on(
                    DB::raw("CONVERT(cRef.reference USING utf8mb4) COLLATE utf8mb4_unicode_ci"),
                    '=',
                    'transactions.customer_ref'
                );
            })
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
                settlement_credit_sale_payments.discount as product_discount,
                settlement_credit_sale_payments.total_discount as total_discount,
                cRef.reference,
                (SELECT SUM(IF(TP.is_return = 1, -1*TP.amount, TP.amount))
                 FROM transaction_payments AS TP
                 WHERE TP.transaction_id = transactions.id) as total_paid')
            ->where(function ($query) use ($business_id) {
                $query->where('p.business_id', $business_id)
                    ->orWhere('route_operations.business_id', $business_id)
                    ->orWhere('transactions.business_id', $business_id);
            })
            ->where(function ($query) {
                $query->whereIn('transactions.type', ['direct_customer_loan', 'fleet_opening_balance', 'cheque_return', 'property_sell', 'route_operation', 'expense', 'sell', 'opening_balance', 'sell_return'])
                    ->orWhere(function ($query) {
                        $query->where('transactions.type', 'settlement')->where('transactions.sub_type', 'customer_loan');
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

    public function downloadPdf(Request $request)
    {
        $html = $request->get('html');
        $mpdf = new Mpdf();
        $mpdf->SetFont('Calibri', '', 12);
        $mpdf->WriteHTML($html);

        $directoryPath = config('constants.reports_directory');
        if (!is_dir($directoryPath)) {
            mkdir($directoryPath, 0755, true);
        }

        $filename = Str::random(40) . ".pdf";
        $filePath = config('constants.reports_directory') . $filename;
        $mpdf->Output($filePath, 'F');

        return response()->json(['path' => url("reports/" . $filename)]);
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

            $custAbbreviation = strtoupper(substr($contact->name, 0, 2));

            $customer_statement = CustomerStatement::where('customer_id', $request->customer_id)
                ->orderBy('id', 'desc')
                ->first();

            if ($customer_statement && sizeof(explode('-', $customer_statement->statement_no)) > 1) {
                $previousStaement = explode('-', $customer_statement->statement_no)[1];
            } else {
                $previousStaement = 0;
            }

            $currentStatement = $custAbbreviation . "-" . ($previousStaement + 1);


            $statement = CustomerStatement::create([

                'business_id' => $business_id,

                'logo' => $request->logo,

                'customer_id' => $request->customer_id,

                'statement_no' => $currentStatement,

                'print_date' => date('Y-m-d'),

                'date_from' => \Carbon::parse($start_date)->format('Y-m-d'),

                'date_to' => \Carbon::parse($end_date)->format('Y-m-d'),

                'added_by' => Auth::user()->id,

                'is_transaction_linked' => 1,

            ]);



            foreach ($transactions as $transaction) {
                $invoice_no = $transaction->invoice_no;
                if ($transaction->sub_type == 'customer_loan') {
                    $invoice_no = __('petro::lang.customer_loans');
                }

                if ($transaction->tran_type == 'direct_customer_loan') {
                    $invoice_no = __('lang_v1.direct_loan_to_customer');
                }

                $due = 0;

                if ($transaction->tran_type == 'sell') {
                    $due = ($transaction->sold_qty * $transaction->p_unit_price) - $transaction->total_paid;
                } else {
                    $due = $transaction->final_total - $transaction->total_paid;
                }

                $final_total = 0;
                if ($transaction->tran_type == 'sell') {
                    $final_total = $transaction->sold_qty * $transaction->p_unit_price;
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

                    'due_amount' => $due,

                    'transaction_id' => $transaction->transaction_id

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

        if ($contact->type == 'customer') {
            $ledger_details['beginning_balance'] = $this->contactUtil->getCustomerBf($contact_id, $business_id, $start_date);
            $ledger_details['balance_details'] = $this->contactUtil->getCustomerBalance($contact_id, $business_id);
        }

        if ($contact->type == 'supplier') {
            $ledger_details['beginning_balance'] = $this->contactUtil->getSupplierBf($contact_id, $business_id, $start_date);
            $ledger_details['balance_details'] = $this->contactUtil->getSupplierBalance($contact_id, $business_id);
        }

        $business_details = $this->businessUtil->getDetails($contact->business_id);
        $location_details = BusinessLocation::where('business_id', $contact->business_id)->first();
        $for_pdf = 1;

        $logo = CustomerStatementLogo::find($statement->logo);

        return view('customer_statement.show')->with(compact('logo', 'contact', 'ledger_details', 'business_details', 'for_pdf', 'location_details', 'statement_details', 'statement', 'id'));
    }

    public function payTotalStatement($id)
    {
        $statement = CustomerStatement::findOrFail($id);
        $statement_details = CustomerStatementDetail::where('statement_id', $id)->get();
        $business_id = request()->session()->get('user.business_id');
        $contact =  Contact::findOrFail($statement->customer_id);

        $payment = TransactionPayment::join('users', 'users.id', 'transaction_payments.created_by')->whereNull('transaction_id')->where('linked_customer_statement', $id)->select('users.username', 'transaction_payments.*')->first();

        $transaction_ids = $statement_details->pluck('transaction_id')->toArray();
        $paid_transactions = Transaction::whereIn('id', $transaction_ids)->where('payment_status', 'paid')->pluck('invoice_no')->toArray();

        $statement_amount = Transaction::whereIn('id', $transaction_ids)->sum('final_total');
        $statement_paid_amount = TransactionPayment::whereIn('transaction_id', $transaction_ids)->sum('amount');
        $statement_due_amount = $statement_amount - $statement_paid_amount;

        $prefix_type = 'sell_payment';
        $business_location_id = BusinessLocation::where('business_id', $business_id)->first()->id;
        $payment_types = $this->transactionUtil->payment_types($business_location_id);

        $payment_line = new TransactionPayment();

        $payment_line->amount = $statement_due_amount;

        $payment_line->method = 'cash';

        $payment_line->paid_on = \Carbon::now()->toDateTimeString();

        $accounts = $this->moduleUtil->accountsDropdown($business_id, true);

        $ref_count = $this->transactionUtil->onlyGetReferenceCount($prefix_type, $business_id, false);
        $payment_ref_no = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);

        unset($payment_types['credit_sale']);

        return view('customer_statement.pay_total_statement')->with(compact('payment', 'contact', 'paid_transactions', 'statement_due_amount', 'payment_types', 'payment_line', 'accounts', 'payment_ref_no', 'statement', 'business_location_id'));
    }

    public function postPayTotalStatement(Request  $request, $id)

    {

        $business_id = $request->session()->get('business.id');

        try {
            $statement = CustomerStatement::findOrFail($id);
            $statement_details = CustomerStatementDetail::where('statement_id', $id)->get();
            $transaction_ids = $statement_details->pluck('transaction_id')->toArray();

            $contact_id = $statement->customer_id;

            $has_reviewed = $this->transactionUtil->hasReviewed($request->input('paid_on'));

            if (!empty($has_reviewed)) {
                $output              = [
                    'success' => 0,
                    'msg'     => __('lang_v1.review_first'),
                ];

                return redirect()->back()->with(['status' => $output]);
            }

            $reviewed = $this->transactionUtil->get_review($request->input('paid_on'), $request->input('paid_on'));

            if (!empty($reviewed)) {
                $output = [
                    'success' => 0,
                    'msg'     => "You can't add a payment for an already reviewed date",
                ];

                return redirect()->back()->with(['status' => $output]);
            }


            $inputs = $request->only([

                'amount',
                'method',
                'note',
                'card_number',
                'card_holder_name',

                'card_transaction_number',
                'card_type',
                'card_month',
                'card_year',
                'card_security',

                'cheque_number',
                'bank_account_number',
                'bank_name',
                'post_dated_cheque',
                'update_post_dated_cheque'

            ]);


            if ($inputs['method'] == 'cheque') {
                if (empty($inputs['cheque_number']) || empty($inputs['bank_name'])) {
                    $output = [
                        'success' => false,
                        'msg' => 'Bank name and Cheque number are required for Cheque payments'
                    ];
                    return redirect()->back()->with('status', $output);
                } else {
                    // check duplicates
                    $chequesAdded = $this->transactionUtil->checkCheques($inputs['cheque_number'], $inputs['bank_name']);

                    if ($chequesAdded > 0) {
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

            $inputs['payment_for'] = $contact_id;

            $inputs['business_id'] = $request->session()->get('business.id');

            $inputs['linked_customer_statement'] = $statement->id;

            $inputs['cheque_date'] =  !empty($request->cheque_date) ? $this->transactionUtil->uf_date($request->cheque_date) : null;


            $prefix_type = 'sell_payment';
            $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type);

            //Generate reference number
            $payment_ref_no = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);

            $inputs['payment_ref_no'] = $payment_ref_no;

            if (!empty($request->input('account_id'))) {

                $inputs['account_id'] = $request->input('account_id');
            }




            //Upload documents if added

            $inputs['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');

            $inputs['paid_in_type'] = 'customer_statement';
            $due_payment_type = 'sell';

            DB::beginTransaction();

            $contact = Contact::findOrFail($contact_id);
            $post_dated =  $this->transactionUtil->account_exist_return_id('Post Dated Cheques');
            $issued_post_dated =  $this->transactionUtil->account_exist_return_id('Issued Post Dated Cheques');
            if (!empty($inputs['update_post_dated_cheque'])) {
                $inputs['related_account_id'] = $request->input('account_id');

                if ($due_payment_type == 'sell_return') {
                    $inputs['account_id'] = $issued_post_dated;
                } else {
                    $inputs['account_id'] = $post_dated;
                }
            }


            $parent_payment = TransactionPayment::create($inputs);

            $inputs['transaction_type'] = $due_payment_type;

            $account_payable = Account::where('business_id', $business_id)->where('name', 'Accounts Payable')->where('is_closed', 0)->first();

            $account_payable_id = !empty($account_payable) ? $account_payable->id : 0;

            $account_transaction_data = [

                'contact_id' => $contact_id,

                'amount' => $parent_payment->amount,

                'account_id' => $parent_payment->account_id,

                'type' => 'credit',

                'operation_date' => $parent_payment->paid_on,

                'created_by' => Auth::user()->id,


                'transaction_payment_id' => $parent_payment->id,

                'note' => null,

                'post_dated_cheque' => $request->post_dated_cheque,

                'update_post_dated_cheque' => $request->update_post_dated_cheque

            ];

            $location_id = BusinessLocation::where('business_id', $business_id)->first();

            $account_transaction_data['account_id'] = $request->account_id;


            $account_transaction_data['type'] = 'debit';
            if (!empty($inputs['update_post_dated_cheque'])) {
                $account_transaction_data['related_account_id'] = $request->input('account_id');
                $account_transaction_data['account_id'] = $post_dated;
            }


            AccountTransaction::createAccountTransaction($account_transaction_data);

            $account_receivable = Account::where('business_id', $business_id)->where('name', 'Accounts Receivable')->where('is_closed', 0)->first();

            $account_receivable_id = !empty($account_receivable) ? $account_receivable->id : 0;

            $account_transaction_data['account_id'] = $account_receivable_id;

            $account_transaction_data['type'] = 'credit';

            $account_transaction_data['sub_type'] = 'ledger_show';

            AccountTransaction::createAccountTransaction($account_transaction_data);

            $account_transaction_data['contact_id'] = $contact_id;

            $account_transaction_data['sub_type'] = 'payment';

            ContactLedger::createContactLedger($account_transaction_data);

            //Distribute above payment among unpaid transactions

            $this->transactionUtil->payCustomerStatementAtOnce($parent_payment, $transaction_ids);

            DB::commit();


            $output = [

                'success' => true,

                'msg' => __('purchase.payment_added_success')

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

    public function destroyPayments($id)
    {

        if (request()->ajax()) {
            try {
                DB::beginTransaction();
                $customer_statement = CustomerStatement::findOrFail($id);

                $changed_msg = "Payments for Customer Statement #" . $customer_statement->statement_no . " has been deleted by " . auth()->user()->username;

                $activity = new Activity();
                $activity->log_name = "Customer Statement";
                $activity->description = "delete";
                $activity->subject_id = $id;
                $activity->subject_type = "App\CustomerStatement";
                $activity->causer_id = auth()->user()->id;
                $activity->causer_type = 'App\User';
                $activity->properties = $changed_msg;
                $activity->created_at = date('Y-m-d H:i');
                $activity->updated_at = date('Y-m-d H:i');

                // Save the activity
                $activity->save();

                $this->transactionUtil->deleteStatementBulkPayments($id);

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

    public function destroy($id)
    {

        if (request()->ajax()) {
            try {

                $customer_statement = CustomerStatement::findOrFail($id);

                $changed_msg = "Customer Statement #" . $customer_statement->statement_no . " has been deleted by " . auth()->user()->username;

                $activity = new Activity();
                $activity->log_name = "Customer Statement";
                $activity->description = "delete";
                $activity->subject_id = $id;
                $activity->subject_type = "App\CustomerStatement";
                $activity->causer_id = auth()->user()->id;
                $activity->causer_type = 'App\User';
                $activity->properties = $changed_msg;
                $activity->created_at = date('Y-m-d H:i');
                $activity->updated_at = date('Y-m-d H:i');

                // Save the activity
                $activity->save();

                $customer_statement->delete();
                CustomerStatementDetail::where('statement_id', $id)->delete();

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


    /**
     * print the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    public function getMinimumDate(Request $r)
    {
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

        if ($contact->type == 'customer') {
            $ledger_details['beginning_balance'] = $this->contactUtil->getCustomerBf($contact_id, $business_id, $start_date);
            $ledger_details['balance_details'] = $this->contactUtil->getCustomerBalance($contact_id, $business_id);
        }

        if ($contact->type == 'supplier') {
            $ledger_details['beginning_balance'] = $this->contactUtil->getSupplierBf($contact_id, $business_id, $start_date);
            $ledger_details['balance_details'] = $this->contactUtil->getSupplierBalance($contact_id, $business_id);
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

    public function exportExcel($id)

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

        if ($contact->type == 'customer') {
            $ledger_details['beginning_balance'] = $this->contactUtil->getCustomerBf($contact_id, $business_id, $start_date);
            $ledger_details['balance_details'] = $this->contactUtil->getCustomerBalance($contact_id, $business_id);
        }

        if ($contact->type == 'supplier') {
            $ledger_details['beginning_balance'] = $this->contactUtil->getSupplierBf($contact_id, $business_id, $start_date);
            $ledger_details['balance_details'] = $this->contactUtil->getSupplierBalance($contact_id, $business_id);
        }

        $business_details = $this->businessUtil->getDetails($contact->business_id);

        $location_details = BusinessLocation::where('business_id', $contact->business_id)->first();

        $for_pdf = 1;

        $reprint = 1;

        $logo = CustomerStatementLogo::find($statement->logo);

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
        ), "CustomerStatement.xls");

        ob_end_clean();
        return $response;
    }

    public function exportExcelPmt($id)

    {

        $statement = CustomerStatement::findOrFail($id);

        $reprint_no = $statement->reprint_no;

        $statement->reprint_no = $reprint_no + 1;
        $statement->save();

        $contact = Contact::findOrFail($statement->customer_id);

        $start_date = $statement->date_from;

        $end_date = $statement->date_to;
        $contact_id = $contact->id;
        $business_id = $contact->business_id;


        $statement_details = CustomerStatementDetail::where('statement_id', $id)->select(
            'id',
            'business_id',
            'statement_id',
            'date',
            'location',
            'invoice_no',
            'customer_reference',
            'order_no',
            'order_date',
            'product',
            'unit_price',
            'qty',
            'vehicle_number',
            'route_name',
            'invoice_amount',
            'due_amount',
            'type'
        );
        $pmts = $this->__getPayments($start_date, $end_date, $business_id, $contact_id);
        $custpmts = $this->__getCustomerPayments($start_date, $end_date, $business_id, $contact_id);
        $statement_details->unionAll($pmts)->unionAll($custpmts)->orderBy('date', 'asc');

        $statement_details = $statement_details->get();


        $ledger_details = [];
        $ledger_details['opening_balance'] = 0;

        if ($contact->type == 'customer') {
            $ledger_details['beginning_balance'] = $this->contactUtil->getCustomerBf($contact_id, $business_id, $start_date);
            $ledger_details['balance_details'] = $this->contactUtil->getCustomerBalance($contact_id, $business_id);
        }

        if ($contact->type == 'supplier') {
            $ledger_details['beginning_balance'] = $this->contactUtil->getSupplierBf($contact_id, $business_id, $start_date);
            $ledger_details['balance_details'] = $this->contactUtil->getSupplierBalance($contact_id, $business_id);
        }

        $business_details = $this->businessUtil->getDetails($contact->business_id);

        $location_details = BusinessLocation::where('business_id', $contact->business_id)->first();

        $for_pdf = 1;

        $reprint = 1;

        $logo = CustomerStatementLogo::find($statement->logo);


        $response = MatExcel::download(new CustomerStatementPmtExport(
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
        ), "CustomerStatementPayment.xls");

        ob_end_clean();
        return $response;
    }

    public function rePrintPmt($id)

    {

        $statement = CustomerStatement::findOrFail($id);

        $reprint_no = $statement->reprint_no;

        $statement->reprint_no = $reprint_no + 1;
        $statement->save();

        $contact = Contact::findOrFail($statement->customer_id);

        $start_date = $statement->date_from;

        $end_date = $statement->date_to;
        $contact_id = $contact->id;
        $business_id = $contact->business_id;


        $statement_details = CustomerStatementDetail::where('statement_id', $id)->select(
            'id',
            'business_id',
            'statement_id',
            'date',
            'location',
            'invoice_no',
            'customer_reference',
            'order_no',
            'order_date',
            'product',
            'unit_price',
            'qty',
            'vehicle_number',
            'route_name',
            'invoice_amount',
            'due_amount',
            'type'
        );
        $pmts = $this->__getPayments($start_date, $end_date, $business_id, $contact_id);
        $custpmts = $this->__getCustomerPayments($start_date, $end_date, $business_id, $contact_id);
        $statement_details->unionAll($pmts)->unionAll($custpmts)->orderBy('date', 'asc');

        $statement_details = $statement_details->get();


        $ledger_details = [];
        $ledger_details['opening_balance'] = 0;

        if ($contact->type == 'customer') {
            $ledger_details['beginning_balance'] = $this->contactUtil->getCustomerBf($contact_id, $business_id, $start_date);
            $ledger_details['balance_details'] = $this->contactUtil->getCustomerBalance($contact_id, $business_id);
        }

        if ($contact->type == 'supplier') {
            $ledger_details['beginning_balance'] = $this->contactUtil->getSupplierBf($contact_id, $business_id, $start_date);
            $ledger_details['balance_details'] = $this->contactUtil->getSupplierBalance($contact_id, $business_id);
        }

        $business_details = $this->businessUtil->getDetails($contact->business_id);

        $location_details = BusinessLocation::where('business_id', $contact->business_id)->first();

        $for_pdf = 1;

        $reprint = 1;

        $logo = CustomerStatementLogo::find($statement->logo);


        return view('customer_statement.print-pmt')->with(compact(

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

    public function showPmt($id)
    {
        $statement = CustomerStatement::findOrFail($id);
        $contact = Contact::findOrFail($statement->customer_id);
        $start_date = $statement->date_from;
        $end_date = $statement->date_to;

        $contact_id = $contact->id;
        $business_id = $contact->business_id;

        $statement_details = CustomerStatementDetail::where('statement_id', $id)->select(
            'id',
            'business_id',
            'statement_id',
            'date',
            'location',
            'invoice_no',
            'customer_reference',
            'order_no',
            'order_date',
            'product',
            'unit_price',
            'qty',
            'vehicle_number',
            'route_name',
            'invoice_amount',
            'due_amount',
            'type'
        );
        $pmts = $this->__getPayments($start_date, $end_date, $business_id, $contact_id);
        $custpmts = $this->__getCustomerPayments($start_date, $end_date, $business_id, $contact_id);
        $statement_details->unionAll($pmts)->unionAll($custpmts)->orderBy('date', 'asc');

        $statement_details = $statement_details->get();

        $ledger_details = [];
        $ledger_details['opening_balance'] = 0;

        if ($contact->type == 'customer') {
            $ledger_details['beginning_balance'] = $this->contactUtil->getCustomerBf($contact_id, $business_id, $start_date);
            $ledger_details['balance_details'] = $this->contactUtil->getCustomerBalance($contact_id, $business_id);
        }

        if ($contact->type == 'supplier') {
            $ledger_details['beginning_balance'] = $this->contactUtil->getSupplierBf($contact_id, $business_id, $start_date);
            $ledger_details['balance_details'] = $this->contactUtil->getSupplierBalance($contact_id, $business_id);
        }

        $business_details = $this->businessUtil->getDetails($contact->business_id);
        $location_details = BusinessLocation::where('business_id', $contact->business_id)->first();
        $for_pdf = 1;

        $logo = CustomerStatementLogo::find($statement->logo);

        return view('customer_statement.show-pmt')->with(compact('id', 'logo', 'contact', 'ledger_details', 'business_details', 'for_pdf', 'location_details', 'statement_details', 'statement'));
    }

    public function __getPayments($start_date, $end_date, $business_id, $contact_id)
    {

        $pmts = TransactionPayment::leftjoin('transactions', 'transaction_payments.transaction_id', 'transactions.id')
            ->leftjoin('business_locations', 'business_locations.id', 'transactions.location_id')
            ->where('transaction_payments.business_id', $business_id)
            ->whereNull('transaction_payments.deleted_at')
            ->whereNull('transaction_payments.parent_id')
            ->where(function ($query) {
                $query->whereNull('transaction_payments.transaction_id')
                    ->orWhere(function ($query) {
                        $query->whereNotIn('transactions.type', ['security_deposit', 'refund_security_deposit', 'security_deposit_refund']);
                    });
            })
            ->where('transaction_payments.payment_for', $contact_id)
            ->whereDate('transaction_payments.paid_on', '>=', $start_date)
            ->whereDate('transaction_payments.paid_on', '<=', $end_date)
            ->withTrashed()
            ->select([
                'transactions.id',
                'transactions.business_id',
                DB::raw('transaction_payments.id as payment_row'),
                'transaction_payments.paid_on as date',
                'business_locations.name as location',
                'transaction_payments.payment_ref_no as invoice_no',
                DB::raw('transaction_payments.account_id as customer_reference'), //customer_reference
                DB::raw('"" as order_no'),
                'transaction_payments.paid_on as order_date',
                DB::raw('"" as product'),
                DB::raw('0 as unit_price'),
                DB::raw('0 as qty'),
                DB::raw('"" as vehicle_number'),
                DB::raw('"" as route_name'),
                'transaction_payments.amount as invoice_amount',
                DB::raw('0 as due_amount'),
                DB::raw('"payment" as type'),
            ]);

        $txnResult = $pmts;
        return $txnResult;
    }
    public function __getCustomerPayments($start_date, $end_date, $business_id, $contact_id)
    {
        $customer_payments = CustomerPayment::leftjoin('settlements', 'customer_payments.settlement_no', 'settlements.id')
            ->leftjoin('transactions', 'transactions.invoice_no', 'settlements.settlement_no')
            ->leftjoin('business_locations', 'business_locations.id', 'transactions.location_id')
            ->whereDate('settlements.transaction_date', '>=', $start_date)
            ->whereDate('settlements.transaction_date', '<=', $end_date)
            ->where('customer_payments.customer_id', $contact_id)
            ->select([
                'transactions.id',
                'transactions.business_id',
                DB::raw('customer_payments.id as payment_row'),
                'settlements.transaction_date as date',
                'business_locations.name as location',
                'settlements.settlement_no as invoice_no',
                DB::raw('customer_payments.bank_name as customer_reference'),
                DB::raw('"" as order_no'),
                'settlements.transaction_date as order_date',
                DB::raw('"" as product'),
                DB::raw('0 as unit_price'),
                DB::raw('0 as qty'),
                DB::raw('"" as vehicle_number'),
                DB::raw('"" as route_name'),
                'customer_payments.sub_total as invoice_amount',
                DB::raw('0 as due_amount'),
                DB::raw('"customer_payment" as type'),
            ])->groupBy('customer_payments.id');

        $txnResult = $customer_payments;
        return $txnResult;
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

        if ($contact->type == 'customer') {
            $ledger_details['beginning_balance'] = $this->contactUtil->getCustomerBf($contact_id, $business_id, $start_date);
            $ledger_details['balance_details'] = $this->contactUtil->getCustomerBalance($contact_id, $business_id);
        }

        if ($contact->type == 'supplier') {
            $ledger_details['beginning_balance'] = $this->contactUtil->getSupplierBf($contact_id, $business_id, $start_date);
            $ledger_details['balance_details'] = $this->contactUtil->getSupplierBalance($contact_id, $business_id);
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

    public function getCustomerStatementListPmt(Request $request)
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
                ->addColumn('action', function ($row) use ($business_id) {
                    $html = '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs"
                            data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                    $html .= '<li><a href="#" data-href="' . action("CustomerStatementController@showPmt", [$row->id]) . '" class="btn-modal" data-container=".customer_statement_modal"><i class="fa fa-external-link" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';
                    $html .= '<li><a href="#" data-href="' . action("CustomerStatementController@rePrintPmt", [$row->id]) . '" class="reprint_statement"><i class="fa fa-print" aria-hidden="true"></i> ' . __("contact.print") . '</a></li>';
                    $html .= '<li><a href="' . action("CustomerStatementController@exportExcelPmt", [$row->id]) . '" ><i class="fa fa-print" aria-hidden="true"></i> ' . __("contact.download_excel") . '</a></li>';
                    $html .= '<li><a href="#" data-href="' . action("CustomerStatementController@rePrintPmt", [$row->id]) . '" class="pdf_statement"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> ' . __("business.pdf") . '</a></li>';
                    $html .= '<li><a href="#" data-href="' . action("CustomerStatementController@rePrintPmt", [$row->id]) . '" class="email_statement"><i class="fa fa-envelope" aria-hidden="true"></i> ' . __("business.email") . '</a></li>';

                    $paid_customer_statement = TransactionPayment::where('linked_customer_statement', $row->id)->count();
                    $pacakge_details = [];
                    $subscription = Subscription::active_subscription($business_id);
                    if (!empty($subscription)) {
                        $pacakge_details = $subscription->package_details;
                    }

                    if ($paid_customer_statement > 0) {
                        if (auth()->user()->can('contact.delete_statement_payment') && (!empty($pacakge_details['contact.delete_statement_payment']) || !array_key_exists('contact.delete_statement_payment', $pacakge_details))) {
                            $html .= '<li><a data-href="' . action('CustomerStatementController@destroyPayments', [$row->id]) . '" class="delete_customer_statement"><i class="fa fa-trash"></i>' . __("lang_v1.delete_payments") . '</a></li>';
                        }
                    } else {
                        if (auth()->user()->can('contact.delete_customer_statement') && (!empty($pacakge_details['contact.delete_customer_statement']) || !array_key_exists('contact.delete_customer_statement', $pacakge_details))) {
                            $html .= '<li><a data-href="' . action('CustomerStatementController@destroy', [$row->id]) . '" class="delete_customer_statement"><i class="fa fa-trash"></i>' . __("messages.delete") . '</a></li>';
                        }
                    }
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
                    return '<span class="display_currency amount" data-currency_symbol="true" data-orig-value="' . $row->details()->sum('invoice_amount') . '">' . $this->commonUtil->num_f($row->details()->sum('invoice_amount')) . '</span>';
                })
                ->removeColumn('id');

            return $fuel_tanks->rawColumns(['action', 'amount'])->make(true);
        }

        $customers = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');

        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

        $enable_separate_customer_statement_no = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_separate_customer_statement_no');

        $help_explanations = HelpExplanation::pluck('value', 'help_key');

        $statement_no = CustomerStatement::where('business_id', $business_id)->count();

        $logos = CustomerStatementLogo::where('business_id', $business_id)->select('*')->pluck('image_name', 'id');

        return view('customer_statement.index-payments')->with(compact('logos', 'customers', 'enable_separate_customer_statement_no', 'business_locations', 'statement_no', 'help_explanations'));
    }


    public function getCustomerStatementList(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            // ini_set('memory_limit', -1);
            // ini_set('max_execution_time', '0');

            $query = CustomerStatement::leftjoin('vat_customer_statements', 'vat_customer_statements.id', 'customer_statements.linked_vat_statement')
                ->leftjoin('users as u', 'u.id', 'customer_statements.converted_by')
                ->with(['contact', 'user', 'location'])->where('customer_statements.business_id', $business_id)
                ->select('customer_statements.*', 'u.username as user_converted', 'vat_customer_statements.statement_no as vat_statement', 'vat_customer_statements.print_date as vat_date');
            if (!empty($request->start_date)) {
                $query->whereDate('customer_statements.date_from', '>=', $request->start_date);
            }

            if (!empty($request->end_date)) {
                $query->whereDate('customer_statements.date_to', '<=', $request->end_date);
            }

            if (!empty($request->printed_start)) {
                $query->whereDate('customer_statements.print_date', '>=', $request->printed_start);
            }

            if (!empty($request->printed_end)) {
                $query->whereDate('customer_statements.print_date', '<=', $request->printed_end);
            }

            if (!empty($request->location_id)) {
                $query->where('customer_statements.location_id', $request->location_id);
            }

            if (!empty($request->customer_id)) {
                $query->where('customer_statements.customer_id', $request->customer_id);
            }

            $fuel_tanks = Datatables::of($query)
                ->addColumn('action', function ($row) use ($business_id) {
                    $html = '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs"
                            data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                    if ($row->is_converted == 0) {
                        $html .= '<li><a href="#" data-href="' . action("\Modules\Vat\Http\Controllers\CustomerStatementController@convertVAT", [$row->id]) . '" class="btn-convert"><i class="fa fa-external-link" aria-hidden="true"></i> ' . __("contact.convert_vat_statement") . '</a></li>';
                    }

                    $html .= '<li><a href="#" data-href="' . action("CustomerStatementController@show", [$row->id]) . '" class="btn-modal" data-container=".customer_statement_modal"><i class="fa fa-external-link" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';

                    $individual_ids = CustomerStatementDetail::where('statement_id', $row->id)->pluck('transaction_id') ?? [];
                    $due_transactions = Transaction::whereIn('id', $individual_ids)->whereIn('payment_status', ['due', 'partial'])->pluck('id') ?? [];

                    if (sizeof($due_transactions) != 0) {
                        $html .= '<li><a href="#" data-href="' . action("CustomerStatementController@payTotalStatement", [$row->id]) . '" class="btn-modal" data-container=".customer_statement_modal"><i class="fa fa-external-link" aria-hidden="true"></i> ' . __("contact.pay_total_statement") . '</a></li>';
                    }

                    $html .= '<li><a href="#" data-href="' . action("CustomerStatementController@rePrint", [$row->id]) . '" class="reprint_statement"><i class="fa fa-print" aria-hidden="true"></i> ' . __("contact.print") . '</a></li>';
                    $html .= '<li><a href="' . action("CustomerStatementController@exportExcel", [$row->id]) . '" ><i class="fa fa-print" aria-hidden="true"></i> ' . __("contact.export_excel") . '</a></li>';
                    $html .= '<li><a href="#" data-href="' . action("CustomerStatementController@rePrint", [$row->id]) . '" class="pdf_statement"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> ' . __("business.pdf") . '</a></li>';
                    $html .= '<li><a href="#" data-href="' . action("CustomerStatementController@rePrint", [$row->id]) . '" class="email_statement"><i class="fa fa-envelope" aria-hidden="true"></i> ' . __("business.email") . '</a></li>';

                    $paid_customer_statement = TransactionPayment::where('linked_customer_statement', $row->id)->count();

                    $pacakge_details = [];
                    $subscription = Subscription::active_subscription($business_id);
                    if (!empty($subscription)) {
                        $pacakge_details = $subscription->package_details;
                    }

                    if ($paid_customer_statement > 0) {
                        if (auth()->user()->can('contact.delete_statement_payment') && (!empty($pacakge_details['contact.delete_statement_payment']) || !array_key_exists('contact.delete_statement_payment', $pacakge_details))) {
                            $html .= '<li><a data-href="' . action('CustomerStatementController@destroyPayments', [$row->id]) . '" class="delete_customer_statement"><i class="fa fa-trash"></i>' . __("lang_v1.delete_payments") . '</a></li>';
                        }
                    } else {
                        if (auth()->user()->can('contact.delete_customer_statement') && (!empty($pacakge_details['contact.delete_customer_statement']) || !array_key_exists('contact.delete_customer_statement', $pacakge_details))) {
                            $html .= '<li><a data-href="' . action('CustomerStatementController@destroy', [$row->id]) . '" class="delete_customer_statement"><i class="fa fa-trash"></i>' . __("messages.delete") . '</a></li>';
                        }
                    }


                    return $html;
                })
                ->addColumn('customer', function ($row) {
                    return $row->contact ? $row->contact->name : null;
                })
                ->addColumn('description', function ($row) {
                    if ($row->is_converted == 1) {
                        $html = "<span class='badge bg-success'>" . __('contact.is_converted') . "</span><br>";
                        $html .= "<span>" . __('contact.created_statement') . "<b> " . $row->vat_statement . "</b> " . __('contact.on') . "<b> " . $this->commonUtil->format_date($row->vat_date) . "</b></span><br>";
                        $html .= "<span>" . $row->user_converted . "</span><br>";

                        return $html;
                    }
                })
                ->addColumn('username', function ($row) {
                    return $row->user ? $row->user->username : null;
                })
                ->addColumn('location', function ($row) {
                    return $row->location ? $row->location->name : null;
                })
                ->addColumn('amount', function ($row) {
                    return '<span class="display_currency amount" data-currency_symbol="true" data-orig-value="' . $row->details()->sum('invoice_amount') . '">' . $this->commonUtil->num_f($row->details()->sum('invoice_amount')) . '</span>';
                })
                ->addColumn('payment_status', function ($row) {
                    $individual_ids = CustomerStatementDetail::where('statement_id', $row->id)->pluck('transaction_id') ?? [];
                    $due_transactions = Transaction::whereIn('id', $individual_ids)->whereIn('payment_status', ['due', 'partial'])->pluck('id') ?? [];

                    if (sizeof($due_transactions) == 0) {
                        // paid
                        $html = "<span class='badge bg-success'>" . __('vat::lang.paid') . "</span>";
                    } else {
                        if (sizeof($individual_ids) == sizeof($due_transactions)) {
                            // due
                            $html = "<span class='badge bg-danger'>" . __('vat::lang.due') . "</span>";
                        } else {
                            // partial
                            $html = "<span class='badge bg-warning'>" . __('vat::lang.partial') . "</span>";
                        }
                    }

                    return $html;
                })
                ->removeColumn('id');

            return $fuel_tanks->rawColumns(['action', 'amount', 'description', 'payment_status'])->make(true);
        }
    }
}
