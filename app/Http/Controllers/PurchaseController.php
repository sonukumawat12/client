<?php



namespace App\Http\Controllers;



use App\User;

use App\Store;
use App\Utils\Util;
use App\Brands;

use App\Account;

use App\Contact;

use App\Product;

use App\TaxRate;

use App\Business;

use App\Category;

use App\Variation;

use Carbon\Carbon;

use App\Transaction;

use Mpdf\Tag\Option;

use App\ContactGroup;

use App\PurchaseLine;

use App\ContactLedger;

use App\BusinessLocation;

use App\Utils\ModuleUtil;

use App\Utils\ContactUtil;

use App\Utils\ProductUtil;

use App\AccountTransaction;

use App\TransactionPayment;

use App\Utils\BusinessUtil;



use Illuminate\Http\Request;

use App\NotificationTemplate;

use App\Utils\TransactionUtil;

use App\SupplierProductMapping;

use Illuminate\Validation\Rule;

use App\Utils\NotificationUtil;;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Auth;



use Modules\Petro\Entities\FuelTank;

use Yajra\DataTables\Facades\DataTables;

use Illuminate\Support\Facades\Validator;



use Modules\Petro\Entities\TankPurchaseLine;
use Illuminate\Validation\ValidationException;



class PurchaseController extends Controller

{

    /**

     * All Utils instance.

     *

     */

    protected $productUtil;

    protected $transactionUtil;

    protected $moduleUtil;

    protected $contactUtil;



    protected $notificationUtil;



    /**

     * Constructor

     *

     * @param ProductUtils $product

     * @return void

     */

    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil, ModuleUtil $moduleUtil, ContactUtil $contactUtil, NotificationUtil $notificationUtil)

    {
        $this->productUtil = $productUtil;

        $this->transactionUtil = $transactionUtil;

        $this->businessUtil = $businessUtil;

        $this->moduleUtil = $moduleUtil;

        $this->contactUtil = $contactUtil;

        $this->notificationUtil = $notificationUtil;



        $this->dummyPaymentLine = [

            'method' => 'cash',
            'amount' => 0,
            'note' => '',
            'card_transaction_number' => '',
            'card_number' => '',
            'card_type' => '',
            'card_holder_name' => '',
            'card_month' => '',
            'card_year' => '',
            'card_security' => '',
            'cheque_number' => '',
            'cheque_date' => '',
            'bank_account_number' => '',

            'is_return' => 0,
            'transaction_no' => '',
            'account_id' => ''

        ];
    }



    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()

    {


        if (!auth()->user()->can('purchase.view') && !auth()->user()->can('purchase.create')) {

            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {

            $purchases = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')

                ->join(

                    'business_locations AS BS',

                    'transactions.location_id',

                    '=',

                    'BS.id'

                )

                ->leftJoin('transaction_payments AS TP', function ($join) {

                    $join->on('transactions.id', '=', 'TP.transaction_id')

                        ->whereNull('TP.deleted_at');
                })

                ->leftJoin(

                    'transactions AS PR',

                    'transactions.id',

                    '=',

                    'PR.return_parent_id'

                )

                ->leftjoin('users as deleted', 'transactions.deleted_by', 'deleted.id')

                ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')

                ->leftJoin('purchase_lines as pl', 'pl.transaction_id', '=', 'transactions.id')

                ->where('transactions.business_id', $business_id)

                ->where('transactions.type', 'purchase')

                ->select(

                    'deleted.username as deletedBy',

                    'transactions.id',

                    'transactions.document',

                    'transactions.transaction_date',

                    'transactions.invoice_date',

                    'transactions.ref_no',

                    'transactions.invoice_no',

                    'transactions.purchase_entry_no',

                    'contacts.name',

                    'transactions.status',

                    'transactions.payment_status',

                    'transactions.final_total',

                    'BS.name as location_name',

                    'transactions.pay_term_number',

                    'transactions.pay_term_type',

                    'transactions.overpayment_setoff',

                    'PR.id as return_transaction_id',

                    'TP.method',

                    'TP.id as tp_id',

                    'TP.account_id',

                    'TP.cheque_number',

                    'pl.lot_number',

                    DB::raw('(SELECT SUM(transaction_payments.amount) FROM transaction_payments WHERE

                    transaction_payments.transaction_id = transactions.id AND transaction_payments.deleted_at IS NULL) as amount_paid'),

                    DB::raw('(SELECT SUM(TP2.amount) FROM transaction_payments AS TP2 WHERE

                        TP2.transaction_id=PR.id ) as return_paid'),

                    DB::raw('COUNT(PR.id) as return_exists'),

                    DB::raw('COALESCE(PR.final_total, 0) as amount_return'),

                    DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by"),
                    DB::raw('(SELECT SUM(pl.purchase_price * pl.quantity)) as total_before_tax'),
                    DB::raw('(SELECT SUM(pl.item_tax * pl.quantity)) as item_tax'),

                )

                // ->withTrashed()

                ->groupBy('transactions.id');



            $permitted_locations = auth()->user()->permitted_locations();

            if ($permitted_locations != 'all') {

                $purchases->whereIn('transactions.location_id', $permitted_locations);
            }



            if (!empty(request()->supplier_id)) {

                $purchases->where('contacts.id', request()->supplier_id);
            }

            if (!empty(request()->supplier_id)) {

                $purchases->where('contacts.id', request()->supplier_id);
            }

            if (!empty(request()->purchase_list_order_no)) {

                $purchases->where('transactions.order_no', request()->purchase_list_order_no);
            }

            if (!empty(request()->input('payment_status')) && request()->input('payment_status') != 'overdue') {

                $purchases->where('transactions.payment_status', request()->input('payment_status'));
            } elseif (request()->input('payment_status') == 'overdue') {

                $purchases->whereIn('transactions.payment_status', ['due', 'partial'])

                    ->whereNotNull('transactions.pay_term_number')

                    ->whereNotNull('transactions.pay_term_type')

                    ->whereRaw("IF(transactions.pay_term_type='days', DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number DAY) < CURDATE(), DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number MONTH) < CURDATE())");
            }



            if (!empty(request()->status)) {

                $purchases->where('transactions.status', request()->status);
            }



            if (!empty(request()->start_date) && !empty(request()->end_date)) {

                $start = request()->start_date;

                $end =  request()->end_date;

                $purchases->whereDate('transactions.transaction_date', '>=', $start)

                    ->whereDate('transactions.transaction_date', '<=', $end);
            }

            if (!empty(request()->suspended)) {

                $with = ['purchase_lines'];



                $purchases = $purchases->where('transactions.is_suspend', 1)

                    ->with($with)

                    ->addSelect('transactions.is_suspend', 'transactions.res_table_id', 'transactions.res_waiter_id', 'transactions.additional_notes')

                    ->get();



                return view('purchase_pos.partials.suspended_purchases_modal')->with(compact('purchases'));
            }

            return Datatables::of($purchases)

                ->addColumn('action', function ($row) {

                    $html = '<div class="btn-group">

                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" 

                                data-toggle="dropdown" aria-expanded="false">' .

                        __("messages.actions") .

                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown

                                </span>

                            </button>

                            <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                    if (auth()->user()->can("purchase.view")) {

                        $html .= '<li><a href="#" data-href="' . action('PurchaseController@show', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>';



                        $html .= '<li><a href="' . action('TransactionPaymentController@show', [$row->id]) .

                            '" class="view_payment_modal"><i class="fa fa-money" aria-hidden="true" ></i>' . __("purchase.view_payments") . '</a></li>';
                    }



                    if ($this->moduleUtil->hasThePermissionInSubscription(request()->session()->get('user.business_id'), 'individual_purchase')) {

                        if (strtotime($this->transactionUtil->__getVatEffectiveDate(request()->session()->get('user.business_id'))) <= strtotime($row->transaction_date)) {

                            $html .= '<li><a href="#" data-href="' . action('\Modules\Vat\Http\Controllers\VatController@updateSingleVats', ['transaction_id' => $row->id]) . '" class="regenerate-vat"><i class="fa fa-pencil"></i> ' . __("superadmin::lang.regenerate_vat") . '</a></li>';
                        }
                    }





                    if (empty($row->deletedBy)) {

                        if (auth()->user()->can("purchase.view")) {

                            $html .= '<li><a href="#" class="print-invoice" data-href="' . action('PurchaseController@printInvoice', [$row->id]) . '"><i class="fa fa-print" aria-hidden="true"></i>' . __("messages.print") . '</a></li>';
                        }

                        if (auth()->user()->can("purchase.update") && empty($row->purchase_entry_no)) {

                            $html .= '<li><a href="' . action('PurchaseController@edit', [$row->id]) . '"><i class="glyphicon glyphicon-edit"></i>' . __("messages.edit") . '</a></li>';
                        }

                        if (auth()->user()->can("purchase.update") && !empty($row->purchase_entry_no)) {

                            $html .= '<li><a href="' . action('PurchasePosController@edit', [$row->id]) . '"><i class="glyphicon glyphicon-edit"></i>' . __("messages.edit") . '</a></li>';
                        }

                        if (auth()->user()->can("purchase.delete")) {

                            $html .= '<li><a href="' . action('PurchaseController@destroy', [$row->id]) . '" class="delete-purchase"><i class="fa fa-trash"></i>' . __("messages.delete") . '</a></li>';
                        }



                        $html .= '<li><a href="' . action('LabelsController@show') . '?purchase_id=' . $row->id . '" data-toggle="tooltip" title="Print Barcode/Label"><i class="fa fa-barcode"></i>' . __('barcode.labels') . '</a></li>';



                        if (auth()->user()->can("purchase.view") && !empty($row->document)) {

                            $document_name = !empty(explode("_", $row->document, 2)[1]) ? explode("_", $row->document, 2)[1] : $row->document;

                            $html .= '<li><a href="' . url('uploads/documents/' . $row->document) . '" download="' . $document_name . '"><i class="fa fa-download" aria-hidden="true"></i>' . __("purchase.download_document") . '</a></li>';

                            if (isFileImage($document_name)) {

                                $html .= '<li><a href="#" data-href="' . url('uploads/documents/' . $row->document) . '" class="view_uploaded_document"><i class="fa fa-picture-o" aria-hidden="true"></i>' . __("lang_v1.view_document") . '</a></li>';
                            }
                        }



                        if (auth()->user()->can("add.payments") || auth()->user()->can("purchase.edit.payments") || auth()->user()->can("purchase.delete.payments")) {

                            $html .= '<li class="divider"></li>';

                            if ($row->payment_status != 'paid') {

                                $html .= '<li><a href="' . action('TransactionPaymentController@addPayment', [$row->id]) . '" class="add_payment_modal"><i class="fa fa-money" aria-hidden="true"></i>' . __("purchase.add_payment") . '</a></li>';
                            }
                        }



                        if (auth()->user()->can("purchase.update")) {

                            $html .= '<li><a href="' . action('PurchaseReturnController@add', [$row->id]) .

                                '"><i class="fa fa-undo" aria-hidden="true" ></i>' . __("lang_v1.purchase_return") . '</a></li>';
                        }



                        if (auth()->user()->can("purchase.update") || auth()->user()->can("purchase.update_status")) {

                            $html .= '<li><a href="#" data-purchase_id="' . $row->id .

                                '" data-status="' . $row->status . '" class="update_status"><i class="fa fa-edit" aria-hidden="true" ></i>' . __("lang_v1.update_status") . '</a></li>';
                        }



                        if (auth()->user()->can("send_notification")) {

                            if ($row->status == 'ordered') {

                                $html .= '<li><a href="#" data-href="' . action('NotificationController@getTemplate', ["transaction_id" => $row->id, "template_for" => "new_order"]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-envelope" aria-hidden="true"></i> ' . __("lang_v1.new_order_notification") . '</a></li>';
                            } elseif ($row->status == 'received') {

                                $html .= '<li><a href="#" data-href="' . action('NotificationController@getTemplate', ["transaction_id" => $row->id, "template_for" => "items_received"]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-envelope" aria-hidden="true"></i> ' . __("lang_v1.item_received_notification") . '</a></li>';
                            } elseif ($row->status == 'pending') {

                                $html .= '<li><a href="#" data-href="' . action('NotificationController@getTemplate', ["transaction_id" => $row->id, "template_for" => "items_pending"]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-envelope" aria-hidden="true"></i> ' . __("lang_v1.item_pending_notification") . '</a></li>';
                            }
                        }
                    }



                    $html .=  '</ul></div>';

                    return $html;
                })

                ->addColumn('purchase_no', function ($row) {

                    return $row->id;
                })

                ->editColumn('invoice_no', function ($row) {

                    if (!empty($row->purchase_entry_no)) {

                        $invoice_no = $row->purchase_entry_no;
                    } else {

                        $invoice_no = $row->invoice_no;
                    }



                    if (!empty($row->deletedBy)) {

                        $invoice_no .= "<br><span class='text-danger'>" . __('sale.deleted_by') . " " . $row->deletedBy . "</span>";
                    }



                    return $invoice_no;
                })

                ->removeColumn('id')

                ->editColumn('ref_no', function ($row) {

                    return !empty($row->return_exists) ? $row->invoice_no . ' <small class="label bg-red label-round no-print" title="' . __('lang_v1.some_qty_returned') . '"><i class="fa fa-undo"></i></small>' : $row->ref_no;
                })

                ->editColumn(

                    'final_total',



                    function ($row) {

                        return '<span class="display_currency final_total" data-currency_symbol="true" data-orig-value="' . (empty($row->deletedBy) ? $row->final_total : 0) . '">' . $this->transactionUtil->num_f($row->final_total) . '</span>';
                    }

                )

                ->editColumn('transaction_date', function ($row) {
                    $html = Carbon::parse($row->transaction_date)->format('Y-m-d H:i');
                    $html .= '<a href="#" data-href="' . action('PurchaseController@show', [$row->id, 'isInvoiceDate' => true, 'invoiceDate' => $row->invoice_date]) . '" class="btn-modal badge btn-info pull-down" data-container=".view_modal">' . __("purchase.invoice_date_details") . '</a>';
                    return $html;
                })

                // ->editColumn('invoice_date', '{{!empty($invoice_date) ? @format_date($invoice_date) : ""}}')

                ->editColumn(

                    'status',

                    '<a href="#" @if(auth()->user()->can("purchase.update") || auth()->user()->can("purchase.update_status")) class="update_status no-print" data-purchase_id="{{$id}}" data-status="{{$status}}" @endif><span class="label @transaction_status($status) status-label" data-status-name="{{__(\'lang_v1.\' . $status)}}" data-orig-value="{{$status}}">{{__(\'lang_v1.\' . $status)}}

                        </span></a>'

                )

                ->editColumn(

                    'payment_status',

                    function ($row) {

                        $payment_status = Transaction::getPaymentStatus($row);



                        $html = '';

                        // if ($row->overpayment_setoff == 1) {

                        //     $html = "<br><small><span class='badge bg-danger' style='font-size: 9px;'>" . __('lang_v1.overpayment_setoff') . "</span></small>";
                        // }
                        $html .= '<a href="#" data-href="' . action('PurchaseController@show', [$row->id, 'isPaymentMethod' => true]) . '" class="btn-modal badge btn-info pull-down" data-container=".view_modal">' . __("messages.details") . '</a>';



                        return (string) view('sell.partials.payment_status', ['payment_status' => $payment_status, 'id' => $row->id, 'for_purchase' => true]) . $html;
                    }

                )

                ->addColumn('payment_due', function ($row) {

                    // logger("total ----> ".$row->final_total);

                    // logger("paid ----> ".$row->amount_paid);





                    $due = empty($row->deletedBy) ? ($row->final_total - $row->amount_paid) : 0;

                    $due_html = '<strong>' . __('lang_v1.purchase') . ':</strong> <span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="' . $due . '">' . $due . '</span>';



                    if (!empty($row->return_exists)) {

                        $return_due = empty($deletedBy) ? ($row->amount_return - $row->return_paid) : 0;

                        $due_html .= '<br><strong>' . __('lang_v1.purchase_return') . ':</strong> <a href="' . action("TransactionPaymentController@show", [$row->return_transaction_id]) . '" class="view_purchase_return_payment_modal no-print"><span class="display_currency purchase_return" data-currency_symbol="true" data-orig-value="' . $return_due . '">' . $return_due . '</span></a><span class="display_currency print_section" data-currency_symbol="true">' . $return_due . '</span>';
                    }

                    return $due_html;
                })

                ->addColumn('payment_method', function ($row) {



                    $html = '';

                    if ($row->payment_status == 'due' || (empty($row->method) && $row->payment_status == 'paid')) {

                        return 'Credit Purchase';
                    }





                    if (strtolower($row->method) == 'bank_transfer' || strtolower($row->method) == 'direct_bank_deposit' || strtolower($row->method) == 'bank' || strtolower($row->method) == 'cheque') {

                        $html .= ucfirst(str_replace("_", " ", $row->method));



                        $bank_acccount = Account::find($row->account_id);

                        if (!empty($bank_acccount)) {

                            $html .= '<br><b>Bank Name:</b> ' . $bank_acccount->name . '</br>';
                        }

                        if (!empty($row->cheque_number)) {

                            $html .= '<b>Cheque Number:</b> ' . $row->cheque_number . '</br>';
                        }

                        if (!empty($row->cheque_date)) {

                            $html .= '<b>Cheque Date:</b> ' . $this->productUtil->format_date($row->cheque_date) . '</br>';
                        }
                    } else {

                        $html .= ucfirst(str_replace("_", " ", $row->method));
                    }



                    return $html;
                })
                ->removeColumn('payment_method')
                ->addColumn('total_before_tax', function ($row) {
                    return '<span class="display_currency final_total" data-currency_symbol="true" data-orig-value="' . (empty($row->deletedBy) ? $row->total_before_tax : 0) . '">' . $this->transactionUtil->num_f($row->total_before_tax) . '</span>';
                })
                ->addColumn('item_tax', function ($row) {
                    return '<span class="display_currency final_total" data-currency_symbol="true" data-orig-value="' . (empty($row->deletedBy) ? $row->item_tax : 0) . '">' . $this->transactionUtil->num_f($row->item_tax) . '</span>';
                })
                ->addColumn('total_after_tax', function ($row) {
                    return '<span class="display_currency final_total1" data-currency_symbol="true" data-orig-value="' . (empty($row->deletedBy) ? $row->total_before_tax + $row->item_tax : 0) . '">' . $this->transactionUtil->num_f($row->total_before_tax + $row->item_tax) . '</span>';
                })
                ->setRowAttr([

                    'data-href' => function ($row) {

                        if (auth()->user()->can("purchase.view")) {

                            return  action('PurchaseController@show', [$row->id]);
                        } else {

                            return '';
                        }
                    },

                    'class' => function ($row) {

                        if (!empty($row->deletedBy)) {

                            return 'deleted-row';
                        } else {

                            return '';
                        }
                    },

                    'title' => function ($row) {

                        if (!empty($row->deletedBy)) {

                            return __('sale.deleted_by') . " " . $row->deletedBy;
                        } else {

                            return '';
                        }
                    },

                ])

                ->rawColumns(['final_total', 'action', 'payment_due', 'payment_status', 'status', 'ref_no', 'payment_method', 'invoice_no', 'transaction_date', 'total_before_tax', 'item_tax', 'total_after_tax'])

                ->make(true);
        }



        $business_locations = BusinessLocation::forDropdown($business_id);

        $suppliers = Contact::suppliersDropdown($business_id, false);

        $orderStatuses = $this->productUtil->orderStatuses();

        $ordernos = [];

        $ordernos = DB::table('transactions')

            ->select('order_no')

            ->where('business_id', $business_id)

            ->where('order_no', '!=', null)

            ->distinct()

            ->orderBy('id', 'DESC')

            // ->limit(5)

            ->pluck('order_no')

            ->toArray();

        // dd($ordernos);            



        return view('purchase.index')

            ->with(compact('business_locations', 'suppliers', 'orderStatuses', 'ordernos'));
    }



    /**

     * Show the form for creating a bulk resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function addPurchaseBulk()

    {

        if (!auth()->user()->can('purchase.create')) {

            abort(403, 'Unauthorized action.');
        }



        $business_id = request()->session()->get('user.business_id');



        //Check if subscribed or not

        if (!$this->moduleUtil->isSubscribed($business_id)) {

            return $this->moduleUtil->expiredResponse();
        }



        $taxes = TaxRate::where('business_id', $business_id)

            ->get();

        $orderStatuses = $this->productUtil->orderStatuses();

        $business_locations = BusinessLocation::forDropdown($business_id);



        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);



        $default_purchase_status = null;

        if (request()->session()->get('business.enable_purchase_status') != 1) {

            $default_purchase_status = 'received';
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



        $business_details = $this->businessUtil->getDetails($business_id);

        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);



        $payment_line = $this->dummyPaymentLine;

        $payment_types =  $this->productUtil->payment_types(null, true, true, false, false, true, "is_purchase_enabled");





        // dd($payment_types);



        // no need thease methods in purchase page

        unset($payment_types['card']);

        unset($payment_types['credit_sale']);

        //Accounts

        $accounts = $this->moduleUtil->accountsDropdown($business_id, true);



        $contact_id = $this->businessUtil->check_customer_code($business_id, 1);



        $temp_data = DB::table('temp_data')->where('business_id', $business_id)->select('pos_create_data')->first();

        if (!empty($temp_data)) {

            $temp_data = json_decode($temp_data->pos_create_data); //name by mistake it is purchase

        }

        if (!request()->session()->get('business.popup_load_save_data')) {

            $temp_data = [];
        }





        $tanks = FuelTank::where('business_id', $business_id)->pluck('fuel_tank_number', 'id');

        $is_petro_enable =  $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module');



        $purchase_no = $this->businessUtil->getFormNumber('purchase');



        $bank_group_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')

            ->where('accounts.business_id', $business_id)

            ->where('account_groups.name', 'Bank Account')

            ->pluck('accounts.name', 'accounts.id');



        $cpc_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')

            ->where('accounts.business_id', $business_id)

            ->where('account_groups.name', 'CPC')

            ->pluck('accounts.name', 'accounts.id');



        return view('purchase.add_purchase_bulk')

            ->with(compact('cpc_accounts', 'purchase_no', 'is_petro_enable', 'tanks', 'contact_id', 'temp_data', 'taxes', 'orderStatuses', 'business_locations', 'currency_details', 'default_purchase_status', 'customer_groups', 'types', 'shortcuts', 'payment_line', 'payment_types', 'accounts', 'bank_group_accounts'));
    }



    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function savePurchaseBulk(Request $request)

    {

        if (!auth()->user()->can('purchase.create')) {

            abort(403, 'Unauthorized action.');
        }



        $payments = $request->payment;

        $all_purchases = $request->purchases;



        $col = array_column($all_purchases, 'ref_no');

        $unique_invoices = array_unique($col);

        try {

            $business_id = request()->session()->get('user.business_id');



            $business_id = $request->session()->get('user.business_id');







            $reviewed = $this->transactionUtil->get_review(date('Y-m-d'), date('Y-m-d'));



            if (!empty($reviewed)) {

                $output              = [

                    'success' => 0,

                    'msg'     => "You can't make a purchase for an already reviewed date",

                ];



                return redirect('purchases')->with('status', $output);
            }





            //Check if subscribed or not

            if (!$this->moduleUtil->isSubscribed($business_id)) {

                return $this->moduleUtil->expiredResponse(action('PurchaseController@index'));
            }

            $store_id = $request->input('store_id');



            //TODO: Check for "Undefined index: total_before_tax" issue

            //Adding temporary fix by validating

            $request->validate([

                'status' => 'required',

                'contact_id' => 'required',

                'transaction_date' => 'required',

                'total_before_tax' => 'required',

                'location_id' => 'required',

                'final_total' => 'required',

                'store_id' => 'required',

                'document' => 'file|max:' . (config('constants.document_size_limit') / 1000)

            ]);

            $user_id = $request->session()->get('user.id');

            $enable_product_editing = $request->session()->get('business.enable_editing_product_from_purchase');



            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

            DB::beginTransaction();

            foreach ($unique_invoices as $invoice) {

                $transaction_data = $request->only(['invoice_no', 'ref_no', 'status', 'contact_id', 'transaction_date', 'total_before_tax', 'location_id', 'discount_type', 'discount_amount', 'tax_id', 'tax_amount', 'shipping_details', 'shipping_charges', 'final_total', 'additional_notes', 'exchange_rate', 'pay_term_number', 'pay_term_type']);

                //Update business exchange rate.

                Business::update_business($business_id, ['p_exchange_rate' => ($transaction_data['exchange_rate'])]);

                $exchange_rate = $transaction_data['exchange_rate'];

                $purchase_lines_arr = [];

                $purchases = $all_purchases;

                $purchase_lines_arr = array_filter($purchases, function ($arr) use ($invoice) {

                    return $arr['ref_no'] == $invoice;
                });

                $purchase_lines_arr =  array_values($purchase_lines_arr); //reset the array indexes



                $transaction_data['invoice_no'] = $invoice;

                //unformat input

                $transaction_data['total_before_tax'] = 0;

                foreach ($purchase_lines_arr as $purchase_line_arr) {

                    $transaction_data['total_before_tax'] += $this->productUtil->num_uf($purchase_line_arr['row_subtotal_after_tax_hidden'], $currency_details) * $exchange_rate;
                }



                // If discount type is fixed them multiply by exchange rate, else don't

                if ($transaction_data['discount_type'] == 'fixed') {

                    $transaction_data['discount_amount'] = $this->productUtil->num_uf($transaction_data['discount_amount'], $currency_details) * $exchange_rate;
                } elseif ($transaction_data['discount_type'] == 'percentage') {

                    $transaction_data['discount_amount'] = $this->productUtil->num_uf($transaction_data['discount_amount'], $currency_details);
                } else {

                    $transaction_data['discount_amount'] = 0;
                }



                $transaction_data['tax_amount'] = $this->productUtil->num_uf($transaction_data['tax_amount'], $currency_details) * $exchange_rate;

                $transaction_data['shipping_charges'] = $this->productUtil->num_uf($transaction_data['shipping_charges'], $currency_details) * $exchange_rate;

                $transaction_data['final_total'] = $this->productUtil->num_uf($transaction_data['total_before_tax'] + $transaction_data['tax_amount'] + $transaction_data['shipping_charges'], $currency_details) * $exchange_rate;



                $transaction_data['business_id'] = $business_id;

                $transaction_data['created_by'] = $user_id;

                $transaction_data['type'] = 'purchase';

                $transaction_data['payment_status'] = 'due';

                $transaction_data['store_id'] = $request->input('store_id');

                $transaction_data['transaction_date'] = $this->productUtil->uf_date($transaction_data['transaction_date'], true);



                //upload document

                $transaction_data['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');



                //Update reference count

                $ref_count = $this->productUtil->setAndGetReferenceCount($transaction_data['type']);

                //Generate reference number

                if (empty($transaction_data['ref_no'])) {

                    $transaction_data['ref_no'] = $this->productUtil->generateReferenceNumber($transaction_data['type'], $ref_count);
                }



                $transaction = Transaction::create($transaction_data);

                // dd($transaction);
                
                $purchase_lines = [];

                $purchases = $purchase_lines_arr;

                $this->productUtil->createOrUpdatePurchaseLines($transaction, $purchases, $currency_details, $enable_product_editing, $store_id);

                //Add qty to sepcific tank if fuel category tank

                if (!empty($request->tanks)) {

                    $tanks = $request->tanks;

                    foreach ($purchases as $pur) {

                        if (!empty($tanks[$pur['row_count']])) {

                            foreach ($tanks[$pur['row_count']] as $key => $tank) {

                                if (!empty($tank['qty'])) {

                                    FuelTank::where('id', $key)->increment('current_balance', !empty($tank['qty']) ? $tank['qty'] : 0);

                                    $product_id = FuelTank::where('id', $key)->first()->product_id;

                                    TankPurchaseLine::create([

                                        'business_id' => $business_id,

                                        'transaction_id' => $transaction->id,

                                        'tank_id' => $key,

                                        'product_id' => $product_id,

                                        'quantity' => !empty($tank['qty']) ? $tank['qty'] : 0,

                                        'instock_qty' => !empty($tank['instock_qty']) ? $tank['instock_qty'] : 0,

                                    ]);
                                }
                            }
                        }
                    }
                }

                $payments = $request->input('payment');

                $this_invoice_payment_array = [];

                foreach ($purchase_lines_arr as $purchase_line) {

                    $this_invoice_payments = $payments[$purchase_line['row_count']];

                    foreach ($this_invoice_payments as $this_invoice_payment) {

                        if (!empty($this_invoice_payment['method']) && !empty($this_invoice_payment['amount']) && $this_invoice_payment['amount'] > 0) {

                            $this_invoice_payment_array[$this_invoice_payment['method']]['method'] = $this_invoice_payment['method'];

                            $amount = 0;

                            if (!empty($this_invoice_payment_array[$this_invoice_payment['method']]['amount'])) {

                                $amount = $this_invoice_payment_array[$this_invoice_payment['method']]['amount'];
                            }

                            $this_invoice_payment_array[$this_invoice_payment['method']]['amount'] =  $amount + $this_invoice_payment['amount'];

                            $this_invoice_payment_array[$this_invoice_payment['method']]['account_id'] = $this_invoice_payment['account_id'] ?? null;

                            $this_invoice_payment_array[$this_invoice_payment['method']]['card_number'] = $this_invoice_payment['card_number'];

                            $this_invoice_payment_array[$this_invoice_payment['method']]['card_holder_name'] = $this_invoice_payment['card_holder_name'];

                            $this_invoice_payment_array[$this_invoice_payment['method']]['card_transaction_number'] = $this_invoice_payment['card_transaction_number'];

                            $this_invoice_payment_array[$this_invoice_payment['method']]['card_type'] = $this_invoice_payment['card_type'];

                            $this_invoice_payment_array[$this_invoice_payment['method']]['card_month'] = $this_invoice_payment['card_month'];

                            $this_invoice_payment_array[$this_invoice_payment['method']]['card_year'] = $this_invoice_payment['card_year'];

                            $this_invoice_payment_array[$this_invoice_payment['method']]['card_security'] = $this_invoice_payment['card_security'];

                            $this_invoice_payment_array[$this_invoice_payment['method']]['cheque_number'] = $this_invoice_payment['cheque_number'];

                            $this_invoice_payment_array[$this_invoice_payment['method']]['cheque_date'] = $this_invoice_payment['cheque_date'];

                            $this_invoice_payment_array[$this_invoice_payment['method']]['transaction_no_1'] = $this_invoice_payment['transaction_no_1'];

                            $this_invoice_payment_array[$this_invoice_payment['method']]['transaction_no_2'] = $this_invoice_payment['transaction_no_2'];

                            $this_invoice_payment_array[$this_invoice_payment['method']]['transaction_no_3'] = $this_invoice_payment['transaction_no_3'];

                            $this_invoice_payment_array[$this_invoice_payment['method']]['note'] = $this_invoice_payment['note'];
                        }
                    }
                }

                $this_invoice_payment_array = array_values($this_invoice_payment_array); // reset array indexes



                if (!empty($this_invoice_payment_array)) {

                    //Add Purchase payments

                    $this->transactionUtil->createOrUpdatePaymentLines($transaction, $this_invoice_payment_array);
                }







                //update payment status

                $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);



                //Adjust stock over selling if found

                $this->productUtil->adjustStockOverSelling($transaction);
            }

            DB::commit();



            $output = [

                'success' => 1,

                'msg' => __('purchase.purchase_add_success')

            ];
        } catch (\Exception $e) {

            DB::rollBack();

            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());



            $output = [

                'success' => 0,

                'msg' => __('messages.something_went_wrong')

            ];
        }



        return redirect('purchases')->with('status', $output);
    }

    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        if (!auth()->user()->can('purchase.create')) {

            abort(403, 'Unauthorized action.');
        }



        $business_id = request()->session()->get('user.business_id');



        //Check if subscribed or not

        if (!$this->moduleUtil->isSubscribed($business_id)) {

            return $this->moduleUtil->expiredResponse();
        }

        $active = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'price_changes_module');



        $taxes = TaxRate::where('business_id', $business_id)

            ->get();

        $orderStatuses = $this->productUtil->orderStatuses();

        $business_locations = BusinessLocation::forDropdown($business_id);



        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);



        $default_purchase_status = null;

        if (request()->session()->get('business.enable_purchase_status') != 1) {

            $default_purchase_status = 'received';
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



        $business_details = $this->businessUtil->getDetails($business_id);

        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);



        $first_location = BusinessLocation::where('business_id', $business_id)->first();

        $payment_line = $this->dummyPaymentLine;

        $payment_types = $this->productUtil->payment_types($first_location, true, true, false, false, true, "is_purchase_enabled");



        // dd($payment_types);



        // no need thease methods in purchase page

        unset($payment_types['card']);

        unset($payment_types['credit_sale']);

        unset($payment_types['location_id']);

        //Accounts

        $accounts = $this->moduleUtil->accountsDropdown($business_id, true);



        $contact_id = $this->businessUtil->check_customer_code($business_id, 1);

        $type = 'supplier'; //contact type /used in quick add contact

        $temp_data = DB::table('temp_data')->where('business_id', $business_id)->select('pos_create_data')->first();

        if (!empty($temp_data)) {

            $temp_data = json_decode($temp_data->pos_create_data); //name by mistake it is purchase

        }

        if (!request()->session()->get('business.popup_load_save_data')) {

            $temp_data = [];
        }




        $tanks = FuelTank::where('business_id', $business_id)->pluck('fuel_tank_number', 'id');

        $is_petro_enable =  $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module');



        $purchase_no = $this->businessUtil->getFormNumber('purchase');



        $bank_group_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')

            ->where('accounts.business_id', $business_id)

            ->where('account_groups.name', 'Bank Account')

            ->pluck('accounts.name', 'accounts.id');

        $cpc_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')

            ->where('accounts.business_id', $business_id)

            ->where('account_groups.name', 'CPC')

            ->pluck('accounts.name', 'accounts.id');



        $cash_account_id = Account::getAccountByAccountName('Cash')->id;



        return view('purchase.create')

            ->with(compact(

                'active',

                'cpc_accounts',

                'cash_account_id',

                'purchase_no',

                'is_petro_enable',

                'tanks',

                'contact_id',

                'type',

                'temp_data',

                'taxes',

                'orderStatuses',

                'business_locations',

                'currency_details',

                'default_purchase_status',

                'customer_groups',

                'types',

                'shortcuts',

                'payment_line',

                'payment_types',

                'accounts',

                'bank_group_accounts'

            ));
    }



    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

     public function store(Request $request)

     {
        //  dd($request->all());
 
         if (!auth()->user()->can('purchase.create')) {
 
             abort(403, 'Unauthorized action.');
         }
         
         try {
 
             $validator = Validator::make($request->all(), [
 
                 'ref_no' => [
 
                     'required',
 
                     Rule::unique('transactions', 'ref_no')
 
                         ->where(function ($query) use ($request) {
 
                             return $query->where('contact_id', $request->contact_id);
                         }),
 
                 ],
 
             ]);
 
 
 
             if ($validator->fails()) {
 
                 return redirect()
 
                     ->back()
 
                     ->withErrors($validator)
 
                     ->withInput();
             }
 
 
 
             $business_id = request()->session()->get('user.business_id');
 
 
 
             $reviewed = $this->transactionUtil->get_review(date('Y-m-d'), date('Y-m-d'));
 
 
 
             if (!empty($reviewed)) {
 
                 $output              = [
 
                     'success' => 0,
 
                     'msg'     => "You can't make a purchase for an already reviewed date",
 
                 ];
 
 
 
                 return redirect('purchases')->with('status', $output);
             }
 
 
 
 
 
             DB::table('temp_data')->where('business_id', $business_id)->update(['pos_create_data' => '']);
 
 
 
             $business_id = $request->session()->get('user.business_id');
 
 
 
             //Check if subscribed or not
 
             if (!$this->moduleUtil->isSubscribed($business_id)) {
 
                 return $this->moduleUtil->expiredResponse(action('PurchaseController@index'));
             }
 
             $store_id = $request->input('store_id');
 
             $transaction_data = $request->only(['is_vat', 'invoice_no', 'invoice_date', 'ref_no', 'status', 'contact_id', 'transaction_date', 'total_before_tax', 'location_id', 'discount_type', 'discount_amount', 'tax_id', 'tax_amount', 'shipping_details', 'shipping_charges', 'price_adjustment', 'final_total', 'additional_notes', 'exchange_rate', 'pay_term_number', 'pay_term_type']);
 
 
 
             $exchange_rate = $transaction_data['exchange_rate'];
 
 
 
             //Adding temporary fix by validating
 
             $request->validate([
 
                 'status' => 'required',
 
                 'contact_id' => 'required',
 
                 'transaction_date' => 'required',
 
                 'invoice_date' => 'required',
 
                 'total_before_tax' => 'required',
 
                 'location_id' => 'required',
 
                 'final_total' => 'required',
 
                 'store_id' => 'required',
 
                 'document' => 'file|max:' . (config('constants.document_size_limit') / 1000)
 
             ]);
 
 
 
             $user_id = $request->session()->get('user.id');
 
             $enable_product_editing = $request->session()->get('business.enable_editing_product_from_purchase');
 
 
 
             //Update business exchange rate.
 
             Business::update_business($business_id, ['p_exchange_rate' => ($transaction_data['exchange_rate'])]);
 
 
 
             $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
 
 
 
             $taxes = TaxRate::where('business_id', $business_id)->first();
 
             $tax_id = !empty($taxes) ? $taxes->id : 1;
 
             $transaction_data['tax_id'] = $tax_id;
 
 
 
             //unformat input values
 
             $transaction_data['total_before_tax'] = $this->productUtil->num_uf($transaction_data['total_before_tax'], $currency_details) * $exchange_rate;
 
 
 
             $transaction_data['discount_amount'] = $this->productUtil->num_uf($transaction_data['discount_amount'], $currency_details) * $exchange_rate;
 
 
 
             $transaction_data['tax_amount'] = $this->productUtil->num_uf($transaction_data['tax_amount'], $currency_details) * $exchange_rate;
 
             $transaction_data['shipping_charges'] = $this->productUtil->num_uf($transaction_data['shipping_charges'], $currency_details) * $exchange_rate;
 
             $transaction_data['price_adjustment'] = $this->productUtil->num_uf($transaction_data['price_adjustment'], $currency_details) * $exchange_rate;
 
             $transaction_data['final_total'] = $this->productUtil->num_uf($transaction_data['final_total'], $currency_details) * $exchange_rate;
 
 
 
             $transaction_data['business_id'] = $business_id;
 
             $transaction_data['created_by'] = $user_id;
 
             $transaction_data['type'] = 'purchase';
 
             $transaction_data['payment_status'] = 'due';
 
             $transaction_data['store_id'] = $request->input('store_id');
 
             $transaction_data['transaction_date'] = $this->productUtil->uf_date($transaction_data['transaction_date'], true);
 
 
 
 
 
             //upload document
 
             $transaction_data['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');
 
 
 
             DB::beginTransaction();
 
 
 
             //Update reference count
 
             $ref_count = $this->productUtil->setAndGetReferenceCount($transaction_data['type']);
 
             //Generate reference number
 
             if (empty($transaction_data['ref_no'])) {
 
                 $transaction_data['ref_no'] = $this->productUtil->generateReferenceNumber($transaction_data['type'], $ref_count);
             }
 
 
 
             $transaction = Transaction::create($transaction_data);
 
 

             $purchase_lines = [];
 
             $purchases = $request->input('purchases');
 
 
 
 
 
 
 
             $this->productUtil->createOrUpdatePurchaseLines($transaction, $purchases, $currency_details, $enable_product_editing, $store_id);
 
                 
 
             $cheque_nos = "";
  \Log::info("Prepayment amount{$request}");
             if (!empty($request->select_cheques)) {

                // foreach ($request->select_cheques as $select_cheque) {
                    $counter=0;
                     if (!empty($select_cheque)) {
 
                         $account_transaction = AccountTransaction::find($select_cheque);
 
 
 
                         $transaction_payment = TransactionPayment::find($account_transaction->transaction_payment_id);
 
 
 
                         if (!empty($transaction_payment)) {
 
                             $amount = $this->transactionUtil->num_uf($account_transaction->amount);
 
                             if (!empty($amount)) {
 
                                 $credit_data = [
 
                                     'amount' => $amount,
 
                                     'account_id' => $account_transaction->account_id,
 
                                     'transaction_id' => $transaction->id,
 
                                     'type' => 'credit',
 
                                     'sub_type' => null,
 
                                     'operation_date' => $transaction_data['transaction_date'],
 
                                     'created_by' => session()->get('user.id'),
 
                                     'transaction_payment_id' => $transaction_payment->id,
 
                                     'note' => null,
 
                                     'attachment' => null
 
                                 ];
 
                                 $credit = AccountTransaction::createAccountTransaction($credit_data);
 
 
 
                                 $cheque_nos .= !empty($transaction_payment->cheque_number) ? $transaction_payment->cheque_number . "," : "";
 
 
 
                                 $transaction_payment->is_deposited = 1;
 
                                 $transaction_payment->save();
                             }
                         }
                     }
                     $counter=$counter+1;
                 //}
             }
 
 
 
 
 
 
 
             //Add qty to sepcific tank if fuel category tank
 
             if (!empty($request->tanks)) {
 
                 foreach ($request->tanks as $key => $tank) {
 
                     if (!empty($tank['qty'])) {
 
                         FuelTank::where('id', $key)->increment('current_balance', !empty($tank['qty']) ? $tank['qty'] : 0);
 
                         $product_id = FuelTank::where('id', $key)->first()->product_id;
 
                         TankPurchaseLine::create([
 
                             'business_id' => $business_id,
 
                             'transaction_id' => $transaction->id,
 
                             'tank_id' => $key,
 
                             'product_id' => $product_id,
 
                             'quantity' => !empty($tank['qty']) ? $tank['qty'] : 0,
 
                             'instock_qty' => !empty($tank['instock_qty']) ? $tank['instock_qty'] : 0,
 
                         ]);
                     }
                 }
             }
 
 
 
 
 
 
 
             $payments = $request->input('payment') ?? [];
 
 
 
             $total_paying = 0;
             $credit_purchase = false;
             foreach ($payments as $payment_arr) {
 
                 if ($payment_arr['method'] == 'credit_purchase') {
                     $credit_purchase = true;
                     $amount = $payment_arr['amount'];
                 }
                    $total_paying += $payment_arr['amount'];
             }
 
 
 
             //Add Purchase payments
 
             if (!empty($payments)) {
 
                 $this->transactionUtil->createOrUpdatePaymentLines($transaction, $payments, null, null,  true, $transaction_data['status']);
             }
 
 
 
 
 
             // update post dated check input for payment
 
             // $this->transactionUtil->updatePostdatedCheque($transaction);
 
 
 
             //add stock account transactions
 
             $this->transactionUtil->manageStockAccount($transaction, [], 'debit', $transaction->final_total, null, $transaction_data['status']);
 
            //  dd($amount);
             if($credit_purchase){
                 //update payment status
                 $status = $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total,null,$amount);
                // dd($status);
             }else{
                 $status = $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);
             }
 
 
 
             // check payment status; compare with supplier balance; if there is an overpaymet; then update the payment status
 
             if ($status == 'partial' || $status == 'due') {
 
                 $supplier_balance =  $this->contactUtil->getSupplierBalance($transaction->contact_id, $business_id, true);
 
 
 
                 // the above balance is after the purchase, so get the balance before the purchase was made.
 
                 $pmt_total_difference = $transaction->final_total - $total_paying;
 
                 $balance_before_purchase = $supplier_balance - $pmt_total_difference;
 
 
 
                 if ($balance_before_purchase < 0) {
 
                     if (abs($balance_before_purchase) >= $pmt_total_difference) {
 
                         $transaction->payment_status = 'paid';
 
                         $transaction->overpayment_setoff = 1;
                     } elseif (abs($balance_before_purchase) < $pmt_total_difference) {
 
                         $transaction->payment_status = 'partial';
 
                         $transaction->overpayment_setoff = 1;
                     }
 
 
 
                     $transaction->save();
                 }
             }
 
 
 
 
 
             //Adjust stock over selling if found
 
             $this->productUtil->adjustStockOverSelling($transaction);
 
 
 
             // add VAT
            // dd($transaction);
             $this->transactionUtil->calculateAndUpdateVAT($transaction);
 
            //  dd("i am here");
            
 
             DB::commit();
 
            if($transaction->payment_status == 'paid'){
                //send sms
                Util::print_sms($transaction->id, $transaction->mobile_no ?? '', 'payment_done');
                Util::print_sms($transaction->id, $transaction->whatsapp_no ?? '', 'payment_done');
            }
            // dd("done");
 
             if (!empty($request->tanks)) {
 
 
 
                 $prod_summary = "";
 
 
 
                 foreach ($request->tanks as $key => $tank) {
 
                     if (!empty($tank['qty'])) {
 
 
 
                         $tank = FuelTank::findOrFail($key);
 
                         if ($key > 0) {
 
                             $prod_summary .= PHP_EOL . PHP_EOL;
                         }
 
 
 
                         $date_obj = \Carbon::parse($transaction_data['transaction_date']);
 
                         $tank->start_date = $date_obj;
 
                         $tank->end_date = $date_obj->endOfDay();
 
 
 
                         $prod_summary .= "Tank No: " . $tank->fuel_tank_number . PHP_EOL;
 
                         $prod_summary .= "Starting Qty: " . $this->transactionUtil->num_f($this->transactionUtil->getTankBalanceByDate($tank->id, $tank->start_date)) . PHP_EOL;
 
                         $prod_summary .= "Received Qty: " . $this->transactionUtil->num_f($this->transactionUtil->__totalPurchaseAndTransferIn($business_id, $tank->start_date, $tank->end_date, $tank->id)) . PHP_EOL;
 
                         $prod_summary .= "Sold Qty: " . $this->transactionUtil->num_f($this->transactionUtil->__totalSellAndTransferOut($business_id, $tank->start_date, $tank->end_date, $tank->id)) . PHP_EOL;
 
                         $prod_summary .= "Balance Qty: " . $this->transactionUtil->num_f($this->transactionUtil->getTankBalanceByDateInclude($tank->id, $tank->end_date));
                     }
                 }
 
 
 
                 $sms_data = array(
 
                     'date' => $this->transactionUtil->format_date($transaction_data['transaction_date']),
 
                     'load_details' => $prod_summary
 
                 );
 
                 $this->notificationUtil->sendPetroNotification('load_received', $sms_data);
             }
 
 
 
 
 
             $contact = Contact::find($transaction->contact_id)->name ?? '';
 
 
 
             $sms_data = array(
 
                 'transaction_date' => $this->transactionUtil->format_date($transaction_data['transaction_date']),
 
                 'contact_name' => $contact,
 
                 'purchase_ref_number' => $transaction->ref_no,
 
                 'amount' =>  $this->transactionUtil->num_f($transaction->final_total),
 
             );
 
             $this->notificationUtil->sendGeneralNotification('general_purchase_created', $sms_data);
 
 
 
 
 
             $output = [
 
                 'success' => 1,
 
                 'msg' => __('purchase.purchase_add_success')
 
             ];
         }
         catch (\Exception $e) {
 
             DB::rollBack();
 
 
 
            //  dd($e);
 
 
 
             Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
 
 
 
             $output = [
 
                 'success' => 0,
 
                 'msg' => __('messages.something_went_wrong')
 
             ];
         }
 
 
 
         return redirect('purchases')->with('status', $output);
     }


    /**

     * Display the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */



    public function show($id, Request $request)

    {

        if (!auth()->user()->can('purchase.view')) {

            abort(403, 'Unauthorized action.');
        }



        $business_id = request()->session()->get('user.business_id');

        $taxes = TaxRate::where('business_id', $business_id)

            ->pluck('name', 'id');

        $purchase = Transaction::where('business_id', $business_id)

            ->where('id', $id)

            ->withTrashed()

            ->with(

                'contact',

                'purchase_lines',

                'purchase_lines.product',

                'purchase_lines.product.unit',

                'purchase_lines.variations',

                'purchase_lines.variations.product_variation',

                'purchase_lines.sub_unit',

                'location',

                'payment_lines',

                'tax',

                'purchase_lines.product.category'

            )

            ->firstOrFail();



        foreach ($purchase->purchase_lines as $key => $value) {

            if (!empty($value->sub_unit_id)) {

                $formated_purchase_line = $this->productUtil->changePurchaseLineUnit($value, $business_id);

                $purchase->purchase_lines[$key] = $formated_purchase_line;
            }
        }



        $payment_methods = $this->productUtil->payment_types(null, false, false, false, false, true);



        $purchase_taxes = [];

        if (!empty($purchase->tax)) {

            if ($purchase->tax->is_tax_group) {

                $purchase_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($purchase->tax, $purchase->tax_amount));
            } else {

                $purchase_taxes[$purchase->tax->name] = $purchase->tax_amount;
            }
        }

        $transaction = Transaction::withTrashed()->findOrFail($id);
        $transactionPayment = TransactionPayment::with('transaction', 'payment_account')->where('transaction_id', $id)->first();
        $isPaymentMethod = $request->query('isPaymentMethod', false);
        $invoiceDate = Carbon::parse($request->query('invoiceDate'))->format('Y-m-d');
        $isInvoiceDate = $request->query('isInvoiceDate', false);

        return view('purchase.show')

            ->with(compact('taxes', 'purchase', 'payment_methods', 'purchase_taxes', 'transaction', 'transactionPayment', 'isPaymentMethod', 'invoiceDate', 'isInvoiceDate'));
    }


    /**

     * Show the form for editing the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {

        if (!auth()->user()->can('purchase.update')) {

            abort(403, 'Unauthorized action.');
        }



        $business_id = request()->session()->get('user.business_id');



        //Check if subscribed or not

        if (!$this->moduleUtil->isSubscribed($business_id)) {

            return $this->moduleUtil->expiredResponse(action('PurchaseController@index'));
        }



        //Check if the transaction can be edited or not.

        $edit_days = request()->session()->get('business.transaction_edit_days');

        if (!$this->transactionUtil->canBeEdited($id, $edit_days)) {

            return back()

                ->with('status', [

                    'success' => 0,

                    'msg' => __('messages.transaction_edit_not_allowed', ['days' => $edit_days])

                ]);
        }



        //Check if return exist then not allowed

        if ($this->transactionUtil->isReturnExist($id)) {

            return back()->with('status', [

                'success' => 0,

                'msg' => __('lang_v1.return_exist')

            ]);
        }



        $business = Business::find($business_id);



        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);



        $taxes = TaxRate::where('business_id', $business_id)

            ->get();



        $is_admin = $this->transactionUtil->is_admin(auth()->user(), $business_id);

        $purchase = Transaction::where('business_id', $business_id)

            ->where('id', $id)

            ->with(

                'contact',

                'payment_lines',

                'purchase_lines',

                'purchase_lines.product',

                'purchase_lines.product.unit',

                //'purchase_lines.product.unit.sub_units',

                'purchase_lines.variations',

                'purchase_lines.variations.product_variation',

                'location',

                'purchase_lines.sub_unit'

            )

            ->first();



        foreach ($purchase->purchase_lines as $key => $value) {

            if (!empty($value->sub_unit_id)) {

                $formated_purchase_line = $this->productUtil->changePurchaseLineUnit($value, $business_id);

                $purchase->purchase_lines[$key] = $formated_purchase_line;
            }
        }





        $taxes = TaxRate::where('business_id', $business_id)

            ->get();

        $orderStatuses = $this->productUtil->orderStatuses();

        $business_locations = BusinessLocation::forDropdown($business_id);



        $default_purchase_status = null;

        if (request()->session()->get('business.enable_purchase_status') != 1) {

            $default_purchase_status = 'received';
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

        $type = 'supplier'; //contact type /used in quick add contact

        $payment_line = $this->dummyPaymentLine;

        $payment_types =  $this->productUtil->payment_types($purchase->location_id, true, true, false, false, true, "is_purchase_enabled");

        $bank_group_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')

            ->where('accounts.business_id', $business_id)

            ->where('account_groups.name', 'Bank Account')

            ->pluck('accounts.name', 'accounts.id');



        $business_details = $this->businessUtil->getDetails($business_id);

        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

        $contact_id = $this->businessUtil->check_customer_code($business_id, 1);

        $temp_data = json_decode('[]');



        $cash_account_id = Account::getAccountByAccountName('Cash')->id;



        return view('purchase.edit')

            ->with(compact(

                'cash_account_id',

                'taxes',

                'purchase',

                'taxes',

                'orderStatuses',

                'business_locations',

                'business',

                'currency_details',

                'default_purchase_status',

                'customer_groups',

                'type',

                'types',

                'shortcuts',

                'temp_data',

                'payment_line',

                'payment_types',

                'bank_group_accounts',

                'contact_id',

                'is_admin'

            ));
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

        if (!auth()->user()->can('purchase.update')) {

            abort(403, 'Unauthorized action.');
        }

        try {

            $validator = Validator::make($request->all(), [

                'ref_no' => [

                    'required',

                    Rule::unique('transactions', 'ref_no')

                        ->where(function ($query) use ($request) {

                            return $query->where('contact_id', $request->contact_id);
                        })

                        ->ignore($id),

                ],

            ]);



            if ($validator->fails()) {

                return redirect()

                    ->back()

                    ->withErrors($validator)

                    ->withInput();
            }





            $transaction = Transaction::findOrFail($id);



            $has_reviewed = $this->transactionUtil->hasReviewed($transaction->transaction_date);



            if (!empty($has_reviewed)) {

                $output              = [

                    'success' => 0,

                    'msg'     => __('lang_v1.review_first'),

                ];



                return redirect()->back()->with(['status' => $output]);
            }



            $reviewed = $this->transactionUtil->get_review($transaction->transaction_date, $transaction->transaction_date);





            if (!empty($reviewed)) {

                $output = [

                    'success' => 0,

                    'msg'     => "You can't edit a purchase for an already reviewed date",

                ];



                return redirect('purchases')->with('status', $output);
            }







            //Validate document size

            $request->validate([

                'document' => 'file|max:' . (config('constants.document_size_limit') / 1000)

            ]);



            $transaction = Transaction::findOrFail($id);

            $before_status = $transaction->status;

            $oldamount = $transaction->final_total;

            $before_pmtstatus = $transaction->payment_status;



            $business_id = request()->session()->get('user.business_id');

            $enable_product_editing = $request->session()->get('business.enable_editing_product_from_purchase');



            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);



            $update_data = $request->only([

                'is_vat',

                'ref_no',
                'status',
                'contact_id',

                'transaction_date',
                'total_before_tax',

                'discount_type',
                'discount_amount',
                'tax_id',

                'tax_amount',
                'shipping_details',

                'shipping_charges',
                'price_adjustment',
                'final_total',

                'additional_notes',
                'exchange_rate',
                'pay_term_number',
                'pay_term_type',

                'invoice_date'

            ]);





            $exchange_rate = $update_data['exchange_rate'];



            $update_data['transaction_date'] = $this->productUtil->uf_date($update_data['transaction_date'], true);

            $taxes = TaxRate::where('business_id', $business_id)->first();

            $tax_id = !empty($taxes) ? $taxes->id : 1;

            $update_data['tax_id'] = $tax_id;



            //unformat input values

            $update_data['total_before_tax'] = $this->productUtil->num_uf($update_data['total_before_tax'], $currency_details) * $exchange_rate;



            $update_data['discount_amount'] = $this->productUtil->num_uf($update_data['discount_amount'], $currency_details) * $exchange_rate;

            $update_data['tax_amount'] = $this->productUtil->num_uf($update_data['tax_amount'], $currency_details) * $exchange_rate;

            $update_data['shipping_charges'] = $this->productUtil->num_uf($update_data['shipping_charges'], $currency_details) * $exchange_rate;

            $update_data['price_adjustment'] = $this->productUtil->num_uf($update_data['price_adjustment'], $currency_details) * $exchange_rate;

            $update_data['final_total'] = $this->productUtil->num_uf($update_data['final_total'], $currency_details) * $exchange_rate;

            //unformat input values ends



            //upload document

            $document_name = $this->transactionUtil->uploadFile($request, 'document', 'documents');

            if (!empty($document_name)) {

                $update_data['document'] = $document_name;
            }



            DB::beginTransaction();



            //update transaction

            $transaction->update($update_data);





            $change = $oldamount - $update_data['final_total'];

            $fga_id = $this->transactionUtil->account_exist_return_id('Finished Goods Account');

            $account_payable_id = $this->productUtil->account_exist_return_id('Accounts Payable');





            $payments = $request->input('payment') ?? [];

            if (!empty($payments)) {

                // Add Purchase payments

                $this->transactionUtil->createOrUpdatePaymentLines($transaction, $payments, null, null,  true, null, null);
            }



            $cheque_nos = "";

            if (!empty($request->select_cheques)) {

                foreach ($request->select_cheques as $select_cheque) {

                    if (!empty($select_cheque)) {

                        $account_transaction = AccountTransaction::find($select_cheque);



                        $transaction_payment = TransactionPayment::find($account_transaction->transaction_payment_id);



                        if (!empty($transaction_payment)) {

                            $amount = $this->transactionUtil->num_uf($account_transaction->amount);

                            if (!empty($amount)) {

                                $credit_data = [

                                    'amount' => $amount,

                                    'account_id' => $account_transaction->account_id,

                                    'transaction_id' => $transaction->id,

                                    'type' => 'credit',

                                    'sub_type' => null,

                                    'operation_date' => $transaction_data['transaction_date'],

                                    'created_by' => session()->get('user.id'),

                                    'transaction_payment_id' => $transaction_payment->id,

                                    'note' => null,

                                    'attachment' => null

                                ];

                                $credit = AccountTransaction::createAccountTransaction($credit_data);



                                $cheque_nos .= !empty($transaction_payment->cheque_number) ? $transaction_payment->cheque_number . "," : "";



                                $transaction_payment->is_deposited = 1;

                                $transaction_payment->save();
                            }
                        }
                    }
                }
            }



            // Update transaction payment status

            $this->transactionUtil->updatePaymentStatus($transaction->id);



            $purchases = $request->input('purchases');



            $delete_purchase_lines = $this->productUtil->createOrUpdatePurchaseLines($transaction, $purchases, $currency_details, $enable_product_editing, $transaction->store_id, $before_status);



            //Update mapping of purchase & Sell.

            $this->transactionUtil->adjustMappingPurchaseSellAfterEditingPurchase($before_status, $transaction, $delete_purchase_lines);



            //Adjust stock over selling if found

            $this->productUtil->adjustStockOverSelling($transaction);





            if (!empty($request->tanks)) {

                foreach ($request->tanks as $key => $tank) {

                    if (!empty($tank['tank_purchase_line_id'])) {

                        $tank_purchase_line = TankPurchaseLine::findOrFail($tank['tank_purchase_line_id']);

                        $qty_difference = $tank['qty'] - $tank_purchase_line->quantity;



                        FuelTank::where('id', $key)->increment('current_balance', !empty($qty_difference) ? $qty_difference : 0);

                        $tank_purchase_line->quantity = $tank['qty'];

                        $tank_purchase_line->save();
                    } else {

                        FuelTank::where('id', $key)->increment('current_balance', !empty($tank['qty']) ? $tank['qty'] : 0);

                        $product_id = FuelTank::where('id', $key)->first()->product_id;

                        $tank_purchase_line = TankPurchaseLine::create([

                            'business_id' => $business_id,

                            'transaction_id' => $transaction->id,

                            'tank_id' => $key,

                            'product_id' => $product_id,

                            'quantity' => !empty($tank['qty']) ? $tank['qty'] : 0

                        ]);
                    }
                }
            }







            $this->transactionUtil->updateManageStockAccount($transaction);



            $this->transactionUtil->calculateAndUpdateVAT($transaction);



            DB::commit();



            $output = [

                'success' => 1,

                'msg' => __('purchase.purchase_update_success')

            ];
        } catch (\Exception $e) {

            DB::rollBack();

            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());



            $output = [

                'success' => 0,

                'msg' => $e->getMessage()

            ];

            return back()->with('status', $output);
        }



        return redirect('purchases')->with('status', $output);
    }



    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {

        if (!auth()->user()->can('purchase.delete')) {

            abort(403, 'Unauthorized action.');
        }



        try {

            if (request()->ajax()) {

                $business_id = request()->session()->get('user.business_id');

                $transaction = Transaction::where('id', $id)

                    ->where('business_id', $business_id)

                    ->with(['purchase_lines'])

                    ->first();



                $has_reviewed = $this->transactionUtil->hasReviewed($transaction->transaction_date);



                if (!empty($has_reviewed)) {

                    $output              = [

                        'success' => 0,

                        'msg'     => __('lang_v1.review_first'),

                    ];



                    return redirect()->back()->with(['status' => $output]);
                }





                $reviewed = $this->transactionUtil->get_review($transaction->transaction_date, $transaction->transaction_date);



                if (!empty($reviewed)) {



                    $output = [

                        'success' => 0,

                        'msg'     => "You can't delete a purchase for an already reviewed date",

                    ];



                    return $output;
                }







                //Check if return exist then not allowed

                if ($this->transactionUtil->isReturnExist($id)) {

                    $output = [

                        'success' => false,

                        'msg' => __('lang_v1.return_exist')

                    ];

                    return $output;
                }





                //Check if lot numbers from the purchase is selected in sale

                if (request()->session()->get('business.enable_lot_number') == 1 && $this->transactionUtil->isLotUsed($transaction)) {

                    $output = [

                        'success' => false,

                        'msg' => __('lang_v1.lot_numbers_are_used_in_sale')

                    ];

                    return $output;
                }





                DB::beginTransaction();







                $delete_purchase_lines = $transaction->purchase_lines;


                // map a duplicate purchase deleted record

                $purchase_deleted = Transaction::where('id', $id)->first();

                $purchase_deleted_txn = new Transaction();

                $purchase_deleted_txn->fill($purchase_deleted->toArray());

                $purchase_deleted_txn->id = null; // Ensure the ID is null so a new one is generated

                $purchase_deleted_txn->type = "_deleted_purchase";

                $purchase_deleted_txn->transaction_date = date('Y-m-d H:i');

                $purchase_deleted_txn->new_deleted_by = auth()->user()->id;

                $purchase_deleted_txn->new_deleted_at = date('Y-m-d H:i');

                $purchase_deleted_txn->save();



                $transaction_status = $transaction->status;



                $store_id = Store::where('business_id', $business_id)->first()->id;



                //Delete purchase lines first



                foreach ($delete_purchase_lines as $purchase_line) {



                    $this->productUtil->decreaseProductQuantity(

                        $purchase_line->product_id,

                        $purchase_line->variation_id,

                        $transaction->location_id,

                        $purchase_line->quantity,

                        0,

                        'decrease'

                    );

                    $this->productUtil->decreaseProductQuantityStore(

                        $purchase_line->product_id,

                        $purchase_line->variation_id,

                        $transaction->location_id,

                        $purchase_line->quantity,

                        $store_id,

                        "decrease",

                        0

                    );





                    $purchase_line_deleted = $purchase_line;

                    $purchase_line_deleted_txn = new PurchaseLine();

                    $purchase_line_deleted_txn->fill($purchase_line_deleted->toArray());

                    $purchase_line_deleted_txn->id = null; // Ensure the ID is null so a new one is generated

                    $purchase_line_deleted_txn->new_deleted_by = auth()->user()->id;

                    $purchase_line_deleted_txn->new_deleted_at = date('Y-m-d H:i');

                    $purchase_line_deleted_txn->transaction_id = $purchase_deleted_txn->id;

                    $purchase_line_deleted_txn->save();
                }

                // reduce quantity from related tanks

                $tank_purchase_lines = TankPurchaseLine::where('transaction_id', $transaction->id)->get();

                foreach ($tank_purchase_lines as $tank_purchase_line) {

                    FuelTank::where('id', $tank_purchase_line->tank_id)->decrement('current_balance', $tank_purchase_line->quantity);



                    $tank_purchase_line_deleted = $tank_purchase_line;

                    $tank_purchase_line_deleted_txn = new TankPurchaseLine();

                    $tank_purchase_line_deleted_txn->fill($tank_purchase_line_deleted->toArray());

                    $tank_purchase_line_deleted_txn->id = null; // Ensure the ID is null so a new one is generated

                    $tank_purchase_line_deleted_txn->new_deleted_by = auth()->user()->id;

                    $tank_purchase_line_deleted_txn->new_deleted_at = date('Y-m-d H:i');

                    $tank_purchase_line_deleted_txn->transaction_id = $purchase_deleted_txn->id;

                    $tank_purchase_line_deleted_txn->save();
                }



                //Update mapping of purchase & Sell.

                $this->transactionUtil->adjustMappingPurchaseSellAfterEditingPurchase($transaction_status, $transaction, $delete_purchase_lines);



                $transaction_id = $transaction->id;



                //get payment transaction

                $transaction_payments = TransactionPayment::where('transaction_id', $transaction->id)->where('amount', '>', 0)->get();


                //Delete account transactions

                foreach ($transaction_payments  as $payment) {

                    // since now purchases are entered as a reverse entry instead of deleting; payments shouldn't be deleted for the original purchase entry



                    $account_id = $payment->account_id;

                    $contact_id = $purchase_deleted_txn->contact_id;



                    if (empty($account_id)) {

                        $parent_transaction = TransactionPayment::where('id', $payment->parent_id)->withTrashed()->first();

                        $account_id = !empty($parent_transaction) ? $parent_transaction->id : null;
                    }



                    if (!empty($account_id)) {

                        $account_transaction_data = [

                            'amount' => $payment->amount,

                            'contact_id' => $contact_id,

                            'account_id' => $account_id,

                            'type' => 'debit',

                            'operation_date' => date('Y-m-d H:i:s'),

                            'created_by' => Auth::user()->id,

                            'transaction_id' => $purchase_deleted_txn->id,

                            'transaction_payment_id' => null,

                            'note' => null,

                            'new_deleted_by' => Auth::user()->id,

                            'new_deleted_at' => date('Y-m-d H:i:s')

                        ];



                        $new_account_transaction = AccountTransaction::create($account_transaction_data);
                    }
                }


                // reverse stock accounts

                $purchased_product = Transaction::leftjoin('purchase_lines', 'transactions.id', 'purchase_lines.transaction_id')

                    ->leftjoin('products', 'purchase_lines.product_id', 'products.id')

                    ->where('transactions.id', $transaction->id)

                    ->select('products.id', 'purchase_lines.id as purchase_line_id', 'enable_stock', 'stock_type', DB::raw('SUM(purchase_lines.quantity * purchase_lines.purchase_price_inc_tax) as amount'))->groupBy('stock_type')->get();



                foreach ($purchased_product as $product_detail) {

                    if ($product_detail->enable_stock && $transaction->status == "received") {

                        if (!empty($product_detail->stock_type)) {

                            $stock_acc_txn = AccountTransaction::where('transaction_id', $transaction->id)->where('account_id', $product_detail->stock_type)->first();

                            $new_stock_acc_txn_ = $stock_acc_txn;

                            $new_stock_acc_txn = new AccountTransaction();

                            $new_stock_acc_txn->fill($new_stock_acc_txn_->toArray());

                            $new_stock_acc_txn->id = null; // Ensure the ID is null so a new one is generated

                            $new_stock_acc_txn->new_deleted_by = auth()->user()->id;

                            $new_stock_acc_txn->new_deleted_at = date('Y-m-d H:i');

                            $new_stock_acc_txn->type = 'credit';

                            $new_stock_acc_txn->transaction_id = $purchase_deleted_txn->id;

                            $new_stock_acc_txn->operation_date = date('Y-m-d H:i');

                            $new_stock_acc_txn->save();
                        }
                    }
                }


                if (in_array($transaction->payment_status, ['due', 'partial'])) {

                    $paid_amount = TransactionPayment::where('transaction_id', $transaction->id)->sum('amount');



                    $account_transaction_data = [

                        'account_id' => $account_id,

                        'operation_date' => \Carbon::now(),

                        'created_by' => Auth::user()->id,

                        'transaction_payment_id' => null,

                        'note' => null,

                        'new_deleted_by' => Auth::user()->id,

                        'transaction_id' => $purchase_deleted_txn->id,

                        'operation_date' => date('Y-m-d H:i'),

                        'new_deleted_at' => date('Y-m-d H:i')

                    ];



                    if ($paid_amount > 0) {

                        $account_transaction_data['type'] = 'credit';

                        $account_transaction_data['amount'] = $paid_amount;

                        $new_account_transaction = AccountTransaction::create($account_transaction_data);
                    }



                    $account_transaction_data['type'] = 'debit';

                    $account_transaction_data['amount'] = $transaction->final_total;

                    $new_account_transaction = AccountTransaction::create($account_transaction_data);
                }





                // <<<<<<<<<<,----------------->>>>>>>>>>>>>>>>>>>



                $contact = Contact::findOrFail($transaction->contact_id);

                $transaction->contact = $contact;



                $this->notificationUtil->autoSendNotification($transaction->business_id, 'supplier_purchase_deleted', $transaction, $transaction->contact, true);





                $business_id = request()->session()->get('user.business_id');

                $business = Business::where('id', $business_id)->first();

                $sms_settings = empty($business->sms_settings) ? $this->businessUtil->defaultSmsSettings() : $business->sms_settings;

                $accountName = null;

                $msg_template = NotificationTemplate::where('business_id', $business_id)->where('template_for', 'general_purchase_deleted')->first();



                if (!empty($msg_template)) {

                    $msg = $msg_template->sms_body;

                    $msg = str_replace('{transaction_date}', $this->transactionUtil->format_date($transaction->transaction_date), $msg);

                    $msg = str_replace('{amount}', $this->transactionUtil->num_f($transaction->final_total), $msg);

                    $msg = str_replace('{business_name}', $business->name, $msg);

                    $msg = str_replace('{purchase_ref_number}', $transaction->ref_no, $msg);

                    $msg = str_replace('{contact_name}', $contact->name, $msg);



                    $phones = [];



                    if (!empty($business->sms_settings)) {

                        $phones = explode(',', str_replace(' ', '', $business->sms_settings['msg_phone_nos']));
                    }

                    if (!empty($phones)) {

                        $data = [

                            'sms_settings' => $sms_settings,

                            'mobile_number' => implode(',', $phones),

                            'sms_body' => $msg

                        ];



                        $response = $this->businessUtil->sendSms($data, 'general_purchase_deleted');
                    }
                }





                DB::commit();

                if ($transaction) {
                    $transaction->delete();
                }

                $output = [

                    'success' => true,

                    'msg' => __('lang_v1.purchase_delete_success')

                ];
            }
        } catch (\Exception $e) {

            DB::rollBack();

            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());



            $output = [

                'success' => false,

                'msg' => $e->getMessage()

            ];
        }



        return $output;
    }



    public function getProductsPurchases()

    {



        if (request()->ajax()) {

            $business_id = request()->session()->get('user.business_id');

            $term = request()->term;

            $module = request()->module ?? null;



            $fuel_category_id = null;

            $supplier_id = request()->supplier_id;



            $check_enable_stock = true;

            if (isset(request()->check_enable_stock)) {

                $check_enable_stock = filter_var(request()->check_enable_stock, FILTER_VALIDATE_BOOLEAN);
            }



            $suppliermapped = SupplierProductMapping::where('supplier_product_mappings.supplier_id', $supplier_id)->get();



            if (!empty(request()->supplier_id) && $suppliermapped->count() > 0) {

                $q = Product::leftJoin(

                    'variations',

                    'products.id',

                    '=',

                    'variations.product_id'

                )

                    ->leftJoin('supplier_product_mappings', 'products.id', '=', 'supplier_product_mappings.product_id')

                    ->where('supplier_product_mappings.supplier_id', '=', $supplier_id)

                    ->where(function ($query) use ($term) {

                        $query->where('products.name', 'like', '%' . $term . '%');

                        $query->orWhere('sku', 'like', '%' . $term . '%');

                        $query->orWhere('sub_sku', 'like', '%' . $term . '%');
                    })

                    ->active()

                    ->where('business_id', $business_id)

                    ->whereNull('variations.deleted_at')

                    ->select(

                        'products.id as product_id',

                        'products.name',

                        'products.type',

                        // 'products.sku as sku',

                        'variations.id as variation_id',

                        'variations.name as variation',

                        'variations.sub_sku as sub_sku'

                    )

                    ->groupBy('variation_id');
            } else {

                $q = Product::leftJoin(

                    'variations',

                    'products.id',

                    '=',

                    'variations.product_id'

                )



                    ->where(function ($query) use ($term) {

                        $query->where('products.name', 'like', '%' . $term . '%');

                        $query->orWhere('sku', 'like', '%' . $term . '%');

                        $query->orWhere('sub_sku', 'like', '%' . $term . '%');
                    })

                    ->active()

                    ->where('business_id', $business_id)

                    ->whereNull('variations.deleted_at')

                    ->select(

                        'products.id as product_id',

                        'products.name',

                        'products.type',

                        // 'products.sku as sku',

                        'variations.id as variation_id',

                        'variations.name as variation',

                        'variations.sub_sku as sub_sku'

                    )

                    ->groupBy('variation_id');
            }



            if (!empty($module)) {

                $q->forModule($module);
            }



            if ($check_enable_stock) {

                $q->where('enable_stock', 1);
            }

            if (!empty(request()->location_id)) {

                $q->ForLocation(request()->location_id);
            }

            $products = $q->get();



            $products_array = [];

            foreach ($products as $product) {

                $products_array[$product->product_id]['name'] = $product->name;

                $products_array[$product->product_id]['sku'] = $product->sub_sku;

                $products_array[$product->product_id]['type'] = $product->type;

                $products_array[$product->product_id]['variations'][]

                    = [

                        'variation_id' => $product->variation_id,

                        'variation_name' => $product->variation,

                        'sub_sku' => $product->sub_sku

                    ];
            }



            $result = [];

            $i = 1;

            $no_of_records = $products->count();

            if (!empty($products_array)) {

                foreach ($products_array as $key => $value) {

                    if ($no_of_records > 1 && $value['type'] != 'single') {

                        $result[] = [

                            'id' => $i,

                            'text' => $value['name'] . ' - ' . $value['sku'],

                            'variation_id' => 0,

                            'product_id' => $key

                        ];
                    }

                    $name = $value['name'];

                    foreach ($value['variations'] as $variation) {

                        $text = $name;

                        if ($value['type'] == 'variable') {

                            if ($variation['variation_name'] != 'DUMMY') {

                                $text = $text . ' (' . $variation['variation_name'] . ')';
                            }
                        }

                        $i++;

                        $result[] = [

                            'id' => $i,

                            'text' => $text . ' - ' . $variation['sub_sku'],

                            'product_id' => $key,

                            'variation_id' => $variation['variation_id'],

                        ];
                    }

                    $i++;
                }
            }



            return json_encode($result);
        }
    }





    /**

     * Retrieves supliers list.

     *

     * @return \Illuminate\Http\Response

     */

    public function getSuppliers()

    {

        if (request()->ajax()) {

            $term = request()->q;

            if (empty($term)) {

                return json_encode([]);
            }



            $business_id = request()->session()->get('user.business_id');

            $user_id = request()->session()->get('user.id');



            $query = Contact::where('business_id', $business_id)->where('active', 1);



            $selected_contacts = User::isSelectedContacts($user_id);

            if ($selected_contacts) {

                $query->join('user_contact_access AS uca', 'contacts.id', 'uca.contact_id')

                    ->where('uca.user_id', $user_id);
            }

            $suppliers = $query->where(function ($query) use ($term) {

                $query->where('name', 'like', '%' . $term . '%')

                    ->orWhere('supplier_business_name', 'like', '%' . $term . '%')

                    ->orWhere('contacts.contact_id', 'like', '%' . $term . '%');
            })

                ->select('contacts.id', 'name as text', 'supplier_business_name as business_name', 'contact_id', 'contacts.pay_term_type', 'contacts.pay_term_number')

                ->onlySuppliers()

                ->get();

            return json_encode($suppliers);
        }
    }



    /**

     * Retrieves products list.

     *

     * @return \Illuminate\Http\Response

     */



    public function getSuppliertId($id)

    {



        if (request()->ajax()) {

            $business_id = request()->session()->get('user.business_id');

            $term = request()->term;

            $fuel_category_id = null;

            $module = request()->module ?? null;



            $check_enable_stock = true;

            if (isset(request()->check_enable_stock)) {

                $check_enable_stock = filter_var(request()->check_enable_stock, FILTER_VALIDATE_BOOLEAN);
            }



            if (empty($term)) {

                return json_encode([]);
            }



            $q = Product::leftJoin(

                'variations',

                'products.id',

                '=',

                'variations.product_id'

            )

                ->leftJoin('supplier_product_mappings', 'products.id', '=', 'supplier_product_mappings.product_id')

                ->where('supplier_product_mappings.supplier_id', '=', $id)

                ->where(function ($query) use ($term) {

                    $query->where('products.name', 'like', '%' . $term . '%');

                    $query->orWhere('sku', 'like', '%' . $term . '%');

                    $query->orWhere('sub_sku', 'like', '%' . $term . '%');
                })

                ->active()

                ->where('business_id', $business_id)

                ->whereNull('variations.deleted_at')

                ->select(

                    'products.id as product_id',

                    'products.name',

                    'products.type',

                    // 'products.sku as sku',

                    'variations.id as variation_id',

                    'variations.name as variation',

                    'variations.sub_sku as sub_sku'

                )

                ->groupBy('variation_id');



            if (!empty($module)) {

                $q->forModule($module);
            }



            if ($check_enable_stock) {

                $q->where('enable_stock', 1);
            }

            if (!empty(request()->location_id)) {

                $q->ForLocation(request()->location_id);
            }

            $products = $q->get();



            $products_array = [];

            foreach ($products as $product) {

                $products_array[$product->product_id]['name'] = $product->name;

                $products_array[$product->product_id]['sku'] = $product->sub_sku;

                $products_array[$product->product_id]['type'] = $product->type;

                $products_array[$product->product_id]['variations'][]

                    = [

                        'variation_id' => $product->variation_id,

                        'variation_name' => $product->variation,

                        'sub_sku' => $product->sub_sku

                    ];
            }



            $result = [];

            $i = 1;

            $no_of_records = $products->count();

            if (!empty($products_array)) {

                foreach ($products_array as $key => $value) {

                    if ($no_of_records > 1 && $value['type'] != 'single') {

                        $result[] = [

                            'id' => $i,

                            'text' => $value['name'] . ' - ' . $value['sku'],

                            'variation_id' => 0,

                            'product_id' => $key

                        ];
                    }

                    $name = $value['name'];

                    foreach ($value['variations'] as $variation) {

                        $text = $name;

                        if ($value['type'] == 'variable') {

                            if ($variation['variation_name'] != 'DUMMY') {

                                $text = $text . ' (' . $variation['variation_name'] . ')';
                            }
                        }

                        $i++;

                        $result[] = [

                            'id' => $i,

                            'text' => $text . ' - ' . $variation['sub_sku'],

                            'product_id' => $key,

                            'variation_id' => $variation['variation_id'],

                        ];
                    }

                    $i++;
                }
            }



            return json_encode($result);
        }
    }

    /**

     * Retrieves products list.

     *

     * @return \Illuminate\Http\Response

     */

    public function getPurchaseEntryRow(Request $request)

    {

        if (request()->ajax()) {

            $product_id = $request->input('product_id');

            $variation_id = $request->input('variation_id');

            $location_id = $request->input('location_id');



            $business_id = request()->session()->get('user.business_id');



            $product = Product::leftJoin('categories as c1', 'products.category_id', '=', 'c1.id')

                ->where('products.id', $product_id)

                ->select('c1.name as category_name')

                ->first();



            if (!empty($product) && $product->category_name == "Fuel") {

                $fuel_tanks = FuelTank::where('product_id', $product_id)->where('location_id', $location_id)->get();

                $current_stock = 0;

                foreach ($fuel_tanks as $tank) {

                    $current_stock +=  $this->transactionUtil->getTankBalanceById($tank->id);
                }
            } else {

                $current_stock = DB::table('variation_location_details')->where('variation_id', $variation_id)->select('qty_available')->first();

                $current_stock = !empty($current_stock) ? $current_stock->qty_available : 0;
            }





            $hide_tax = 'hide';

            if ($request->session()->get('business.enable_inline_tax') == 1) {

                $hide_tax = '';
            }



            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);



            if (!empty($product_id)) {

                $row_count = $request->input('row_count');

                $product = Product::where('id', $product_id)

                    ->with(['unit'])

                    ->first();

                $fuel_category_id = Category::where('business_id', $business_id)->where('name', 'Fuel')->first();

                $is_fuel_category = 0;

                if (!empty($fuel_category_id)) {

                    if ($product->category->id == $fuel_category_id->id) {

                        $is_fuel_category = 1;
                    }
                }

                $sub_units = $this->productUtil->getSubUnits($business_id, $product->unit->id, false, $product_id);



                $query = Variation::where('product_id', $product_id)

                    ->with(['product_variation']);

                if ($variation_id !== '0') {

                    $query->where('id', $variation_id);
                }



                $variations =  $query->get();



                $taxes = TaxRate::where('business_id', $business_id)

                    ->get();

                $temp_qty = null;

                $purchase_pos = (bool)$request->purchase_pos ? 1 : 0;

                $enable_petro_module =  $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module');

                //If brands, category are enabled then send else false.

                $categories = (request()->session()->get('business.enable_category') == 1) ? Category::catAndSubCategories($business_id, $enable_petro_module) : false;

                $brands = (request()->session()->get('business.enable_brand') == 1) ? Brands::where('business_id', $business_id)

                    ->pluck('name', 'id')

                    ->prepend(__('lang_v1.all_brands'), 'all') : false;





                $active = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'price_changes_module');

                $purchase_zero = auth()->user()->can('purchase_zero');

                return view('purchase.partials.purchase_entry_row')

                    ->with(compact(

                        'active',

                        'categories',

                        'brands',

                        'purchase_pos',

                        'product',

                        'variations',

                        'row_count',

                        'variation_id',

                        'taxes',

                        'currency_details',

                        'hide_tax',

                        'sub_units',

                        'current_stock',

                        'temp_qty',

                        'is_fuel_category',

                        'purchase_zero'

                    ));
            }
        }
    }





    /**

     * Retrieves products list.

     *

     * @return \Illuminate\Http\Response

     */

    public function getPurchaseEntryRowBulk(Request $request)

    {

        if (request()->ajax()) {

            $product_id = $request->input('product_id');

            $variation_id = $request->input('variation_id');

            $business_id = request()->session()->get('user.business_id');



            $current_stock = DB::table('variation_location_details')->where('variation_id', $variation_id)->select('qty_available')->first();

            $current_stock = !empty($current_stock) ? $current_stock->qty_available : 0;



            $hide_tax = 'hide';

            if ($request->session()->get('business.enable_inline_tax') == 1) {

                $hide_tax = '';
            }



            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);



            if (!empty($product_id)) {

                $row_count = $request->input('row_count');

                $product = Product::where('id', $product_id)

                    ->with(['unit'])

                    ->first();

                $fuel_category_id = Category::where('business_id', $business_id)->where('name', 'Fuel')->first();

                $is_fuel_category = 0;

                if (!empty($fuel_category_id)) {

                    if ($product->category->id == $fuel_category_id->id) {

                        $is_fuel_category = 1;
                    }
                }

                $sub_units = $this->productUtil->getSubUnits($business_id, $product->unit->id, false, $product_id);



                $query = Variation::where('product_id', $product_id)

                    ->with(['product_variation']);

                if ($variation_id !== '0') {

                    $query->where('id', $variation_id);
                }



                $variations =  $query->get();



                $taxes = TaxRate::where('business_id', $business_id)

                    ->get();

                $temp_qty = null;



                $payment_lines[] = $this->dummyPaymentLine;

                $default_location = BusinessLocation::findOrFail($request->location_id);



                $payment_types =  $this->productUtil->payment_types($default_location, true, false, false, false, true, "is_purchase_enabled");



                $removable = false;

                $bank_group_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')

                    ->where('accounts.business_id', $business_id)

                    ->where('account_groups.name', 'Bank Account')

                    ->pluck('accounts.name', 'accounts.id');

                $active = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'price_changes_module');

                return view('purchase.partials.purchase_entry_row_bulk')

                    ->with(compact(

                        'active',

                        'product',

                        'variations',

                        'row_count',

                        'variation_id',

                        'taxes',

                        'currency_details',

                        'hide_tax',

                        'sub_units',

                        'current_stock',

                        'temp_qty',

                        'is_fuel_category',

                        'payment_types',

                        'removable',

                        'bank_group_accounts',

                        'payment_lines'

                    ));
            }
        }
    }



    /**

     * Retrieves Unload Tank Row

     *

     * @return \Illuminate\Http\Response

     */

    public function getUnloadTankRowBulk(Request $request)

    {

        if (!empty($request->product_id)) {

            $product = Product::findOrFail($request->product_id);

            $fuel_tanks = FuelTank::where('product_id', $request->product_id)->where('location_id', $request->location_id)->get();

            $row_count = $request->row_count;



            foreach ($fuel_tanks as $fuel_tank) {

                $current_balance[$fuel_tank->id] = $this->transactionUtil->getTankBalanceById($fuel_tank->id);
            }

            return view('purchase.partials.unload_tank_row_bulk')->with(compact('product', 'fuel_tanks', 'row_count', 'current_balance'));
        }



        return null;
    }

    /**

     * Retrieves Unload Tank Row

     *

     * @return \Illuminate\Http\Response

     */

    public function getUnloadTankRow(Request $request)

    {

        if (!empty($request->product_id)) {

            $product = Product::findOrFail($request->product_id);

            $fuel_tanks = FuelTank::where('product_id', $request->product_id)->where('location_id', $request->location_id)->get();

            $row_count = $request->row_count;



            $current_balance = array();

            foreach ($fuel_tanks as $fuel_tank) {

                $current_balance[$fuel_tank->id] = $this->transactionUtil->getTankBalanceById($fuel_tank->id);
            }

            return view('purchase.partials.unload_tank_row')->with(compact('product', 'fuel_tanks', 'row_count', 'current_balance'));
        }



        return null;
    }



    /**

     * Retrieves Unload Tank Row

     *

     * @return \Illuminate\Http\Response

     */

    public function getEditUnloadTankRow(Request $request)

    {

        if (!empty($request->product_id) && !empty($request->transaction_id)) {

            $transaction_id = $request->transaction_id;

            $product = Product::findOrFail($request->product_id);

            $purchase_lines = PurchaseLine::where('product_id', $request->product_id)->where('transaction_id', $request->transaction_id)->first();

            $fuel_tanks = FuelTank::where('product_id', $request->product_id)->where('location_id', $request->location_id)->get();



            $row_count = $request->row_count;

            $is_view = $request->is_view;



            $current_balance = [];



            foreach ($fuel_tanks as $fuel_tank) {

                $current_balance[$fuel_tank->id] = $this->transactionUtil->getTankBalanceById($fuel_tank->id);
            }



            return view('purchase.partials.edit_unload_tank_row')->with(compact('product', 'purchase_lines', 'fuel_tanks', 'transaction_id', 'row_count', 'current_balance', 'is_view'));
        }



        return null;
    }





    /**

     * Retrieves products list.

     *

     * @return \Illuminate\Http\Response

     */

    public function getPurchaseEntryRowTemp(Request $request)

    {

        if (request()->ajax()) {

            $product_id = $request->input('product_id');

            $variation_id = $request->input('variation_id');

            $business_id = request()->session()->get('user.business_id');



            $current_stock = DB::table('variation_location_details')->where('variation_id', $variation_id)->select('qty_available')->first();

            $current_stock = !empty($current_stock) ? $current_stock->qty_available : 0;



            $hide_tax = 'hide';

            if ($request->session()->get('business.enable_inline_tax') == 1) {

                $hide_tax = '';
            }



            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);



            if (!empty($product_id)) {

                $row_count = $request->input('row_count');

                $product = Product::where('id', $product_id)

                    ->with(['unit'])

                    ->first();



                $sub_units = $this->productUtil->getSubUnits($business_id, $product->unit->id, false, $product_id);



                $query = Variation::where('product_id', $product_id)

                    ->with(['product_variation']);

                if ($variation_id !== '0') {

                    $query->where('id', $variation_id);
                }



                $fuel_category_id = Category::where('business_id', $business_id)->where('name', 'Fuel')->first();

                $is_fuel_category = 0;

                if (!empty($fuel_category_id)) {

                    if ($product->category->id == $fuel_category_id->id) {

                        $is_fuel_category = 1;
                    }
                }



                $variations =  $query->get();



                $taxes = TaxRate::where('business_id', $business_id)

                    ->get();



                $temp_qty = $request->input('quantity');

                return view('purchase.partials.purchase_entry_row')

                    ->with(compact(

                        'product',

                        'variations',

                        'row_count',

                        'variation_id',

                        'taxes',

                        'currency_details',

                        'hide_tax',

                        'sub_units',

                        'current_stock',

                        'is_fuel_category',

                        'temp_qty'

                    ));
            }
        }
    }



    /**

     * Checks if ref_number and supplier combination already exists.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function checkRefNumber(Request $request)

    {

        $business_id = $request->session()->get('user.business_id');

        $contact_id = $request->input('contact_id');

        $ref_no = $request->input('ref_no');

        $purchase_id = $request->input('purchase_id');



        $count = 0;

        if (!empty($contact_id) && !empty($ref_no)) {

            //check in transactions table

            $query = Transaction::where('business_id', $business_id)

                ->where('ref_no', $ref_no)

                ->where('contact_id', $contact_id);

            if (!empty($purchase_id)) {

                $query->where('id', '!=', $purchase_id);
            }

            $count = $query->count();
        }

        if ($count == 0) {

            echo "true";

            exit;
        } else {

            echo "false";

            exit;
        }
    }



    /**

     * Checks if ref_number and supplier combination already exists.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function printInvoice($id)

    {

        try {

            $business_id = request()->session()->get('user.business_id');

            $taxes = TaxRate::where('business_id', $business_id)

                ->pluck('name', 'id');

            $purchase = Transaction::where('business_id', $business_id)

                ->where('id', $id)

                ->with(

                    'contact',

                    'purchase_lines',

                    'purchase_lines.product',

                    'purchase_lines.variations',

                    'purchase_lines.variations.product_variation',

                    'location',

                    'payment_lines'

                )

                ->first();



            $payment_methods =  $this->productUtil->payment_types(null, false, false, false, false, true, "is_purchase_enabled");



            $output = ['success' => 1, 'receipt' => []];

            $output['receipt']['html_content'] = view('purchase.partials.show_details', compact('taxes', 'purchase', 'payment_methods'))->render();
        } catch (\Exception $e) {

            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());



            $output = [

                'success' => 0,

                'msg' => __('messages.something_went_wrong')

            ];
        }



        return $output;
    }



    /**

     * Update purchase status.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function updateStatus(Request $request)

    {

        if (!auth()->user()->can('purchase.update') && !auth()->user()->can('purchase.update_status')) {

            abort(403, 'Unauthorized action.');
        }

        //Check if the transaction can be edited or not.

        $edit_days = request()->session()->get('business.transaction_edit_days');

        if (!$this->transactionUtil->canBeEdited($request->input('purchase_id'), $edit_days)) {

            return [

                'success' => 0,

                'msg' => __('messages.transaction_edit_not_allowed', ['days' => $edit_days])

            ];
        }



        try {

            $business_id = request()->session()->get('user.business_id');



            $transaction = Transaction::where('business_id', $business_id)

                ->where('type', 'purchase')

                ->with(['purchase_lines'])

                ->findOrFail($request->input('purchase_id'));



            $has_reviewed = $this->transactionUtil->hasReviewed($transaction->transaction_date);



            if (!empty($has_reviewed)) {

                $output              = [

                    'success' => 0,

                    'msg'     => __('lang_v1.review_first'),

                ];



                return redirect()->back()->with(['status' => $output]);
            }



            $reviewed = $this->transactionUtil->get_review($transaction->transaction_date, $transaction->transaction_date);



            if (!empty($reviewed)) {



                $output = [

                    'success' => 0,

                    'msg'     => "You can't update a purchase for an already reviewed date",

                ];



                return $output;
            }





            $before_status = $transaction->status;





            $update_data['status'] = $request->input('status');





            DB::beginTransaction();



            //update transaction

            $transaction->update($update_data);



            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

            foreach ($transaction->purchase_lines as $purchase_line) {

                $this->productUtil->updateProductStock($before_status, $transaction, $purchase_line->product_id, $purchase_line->variation_id, $purchase_line->quantity, $purchase_line->quantity, $currency_details);
            }



            //Update mapping of purchase & Sell.

            $this->transactionUtil->adjustMappingPurchaseSellAfterEditingPurchase($before_status, $transaction, null);



            //Adjust stock over selling if found

            $this->productUtil->adjustStockOverSelling($transaction);



            DB::commit();



            $output = [

                'success' => 1,

                'msg' => __('purchase.purchase_update_success')

            ];
        } catch (\Exception $e) {

            DB::rollBack();

            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());



            $output = [

                'success' => 0,

                'msg' => $e->getMessage()

            ];
        }



        return $output;
    }



    public function getSupplierDue(Request $request)

    {

        $supplier_id = $request->supplier_id;

        $business_id = request()->session()->get('user.business_id');



        $contact = Contact::leftjoin('transactions AS t', 'contacts.id', '=', 't.contact_id')

            // ->where('contacts.business_id', $business_id)

            ->where('contacts.contact_id', $supplier_id)

            ->onlySuppliers()

            ->select([

                'contacts.name',

                DB::raw("SUM(IF(t.type = 'purchase', final_total, 0)) as total_purchase"),

                DB::raw("SUM(IF(t.type = 'purchase', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id AND transaction_payments.deleted_at IS NULL), 0)) as purchase_paid"),

            ])->first();





        if ($contact->total_purchase && $contact->purchase_paid) {

            $supplier_due =    $contact->total_purchase - $contact->purchase_paid;
        } else {

            $supplier_due = 0;
        }



        return ['supplier_due' =>  $supplier_due];
    }



    public function getPaymentMethodByLocationId($location_id)

    {

        $location = BusinessLocation::findOrFail($location_id);

        $payment_types = $this->productUtil->payment_types($location, true, true, false, false, true, "is_purchase_enabled");

        // no need thease methods in purchase page

        unset($payment_types['card']);

        unset($payment_types['credit_sale']);
      



        $html = '<option value="" selected>Please Select</option>';



        foreach ($payment_types as $key => $value) {

            $html .= '<option value="' . $key . '">' . $value . '</option>';
        }



        return ['html' => $html];
    }



    /**

     * Returns the HTML row for a payment in Purchase

     *

     * @param  \Illuminate\Http\Request $request

     * @return \Illuminate\Http\Response

     */

    public function getPaymentRow(Request $request)

    {

        $business_id = request()->session()->get('user.business_id');



        $location_id = $request->input('location_id');

        $row_index = $request->input('row_index');

        $removable = true;

        $payment_types =  $this->productUtil->payment_types($location_id, true, true, false, false, true, "is_purchase_enabled");



        $payment_line = $this->dummyPaymentLine;



        //Accounts

        $accounts = [];

        if ($this->moduleUtil->isModuleEnabled('account')) {

            $accounts = Account::forDropdown($business_id, true, false);
        }



        $bank_group_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')

            ->where('accounts.business_id', $business_id)

            ->where('account_groups.name', 'Bank Account')

            ->pluck('accounts.name', 'accounts.id');



        return view('purchase.partials.payment_row')

            ->with(compact('payment_types', 'row_index', 'removable', 'payment_line', 'accounts', 'bank_group_accounts'));
    }



    /**

     * Returns the HTML row for a payment in Purchase Bulk

     *

     * @param  \Illuminate\Http\Request $request

     * @return \Illuminate\Http\Response

     */

    public function getPaymentRowBulk(Request $request)

    {

        $business_id = request()->session()->get('user.business_id');



        $row_count = $request->input('row_count');

        $location_id = $request->input('location_id');

        $row_index = $request->input('row_index');

        $removable = true;

        $payment_types =  $this->productUtil->payment_types($location_id, true, false, false, false, true, "is_purchase_enabled");



        $payment_lines[] = $this->dummyPaymentLine;



        //Accounts

        $accounts = [];

        if ($this->moduleUtil->isModuleEnabled('account')) {

            $accounts = Account::forDropdown($business_id, true, false);
        }



        $bank_group_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')

            ->where('accounts.business_id', $business_id)

            ->where('account_groups.name', 'Bank Account')

            ->pluck('accounts.name', 'accounts.id');



        return view('purchase.partials.payment_row_bulk')

            ->with(compact('payment_types', 'row_count', 'row_index', 'removable', 'payment_lines', 'accounts', 'bank_group_accounts'));
    }





    public function getSupplierDetails(Request $request, $contact_id = null)

    {

        if (!empty($contact_id)) {

            $supplier_id = $contact_id;
        } else {

            $supplier_id = $request->supplier_id;
        }

        $business_id = request()->session()->get('business.id');

        $query = Contact::leftjoin('transactions AS t', 'contacts.id', '=', 't.contact_id')

            ->leftjoin('contact_groups AS cg', 'contacts.supplier_group_id', '=', 'cg.id')

            ->where('contacts.business_id', $business_id)

            ->where('contacts.id', $supplier_id)

            ->onlySuppliers()

            ->select([

                'contacts.contact_id',
                'contacts.name',
                'contacts.created_at',
                'total_rp',
                'cg.name as supplier_group',
                'sol_with_approval',
                'state',
                'country',
                'landmark',
                'mobile',
                'contacts.id',
                'is_default',

                DB::raw("SUM(IF(t.type = 'purchase' AND t.status = 'final', final_total, 0)) as total_invoice"),

                DB::raw("SUM(IF(t.type = 'purchase' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id AND transaction_payments.deleted_at IS NULL), 0)) as invoice_received"),

                DB::raw("SUM(IF(t.type = 'purchase_return', final_total, 0)) as total_purchase_return"),

                DB::raw("SUM(IF(t.type = 'purchase_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id AND transaction_payments.deleted_at IS NULL), 0)) as purchase_return_paid"),

                DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),

                DB::raw("SUM(IF(t.type = 'advance_payment', -1*final_total, 0)) as advance_payment"),

                DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id AND transaction_payments.deleted_at IS NULL), 0)) as opening_balance_paid"),

                'email',
                'tax_number',
                'contacts.pay_term_number',
                'contacts.pay_term_type',
                'contacts.credit_limit',
                'contacts.custom_field1',
                'contacts.custom_field2',
                'contacts.custom_field3',
                'contacts.custom_field4',
                'contacts.type'

            ])

            ->groupBy('contacts.id')->first();

        $due = $query->total_invoice - $query->invoice_received + $query->advance_payment;

        $return_due = $query->total_purchase_return - $query->purchase_return_paid;

        $opening_balance = $query->opening_balance;



        $total_outstanding =  $due -  $return_due + $opening_balance;

        if (empty($total_outstanding)) {

            $total_outstanding = 0.00;
        }

        $total_outstanding = $this->transactionUtil->num_f($total_outstanding, false);

        return ['due_amount' => $total_outstanding, 'supplier_name' => $query->name, 'sol_with_approval' => $query->sol_with_approval];
    }





    public function getInvoiceNo()

    {

        $business_id = request()->session()->get('business.id');

        $purchase_no = $this->businessUtil->getFormNumber('purchase');



        $purchase_count = Transaction::where('business_id', $business_id)->where('type', 'purchase')->count();



        if (!empty($purchase_count)) {

            $number = $purchase_count + 1;

            $purchase_entry_no = 'PE' . $number;
        } else {

            $purchase_entry_no = 'PE' . 1;
        }



        return ['invoice_no' => $purchase_no, 'purchase_entry_no' => $purchase_entry_no];
    }
}