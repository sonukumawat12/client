<?php



namespace App\Http\Controllers;



use App\User;

use App\Account;

use App\Contact;

use App\TaxRate;

use App\Business;
use App\AccountType;
use App\Transaction;
use App\ContactLedger;
use App\ExpenseCategory;

use App\BusinessLocation;

use App\Utils\ModuleUtil;

use App\AccountTransaction;

use App\TransactionPayment;

use App\Utils\BusinessUtil;

use Illuminate\Http\Request;

use App\NotificationTemplate;

use App\Utils\TransactionUtil;

use App\Utils\NotificationUtil;

use Modules\Fleet\Entities\Fleet;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Auth;

use App\Providers\AppServiceProvider;

use Modules\Superadmin\Entities\Package;

use Yajra\DataTables\Facades\DataTables;

use Modules\Fleet\Entities\RouteOperation;
use Modules\Property\Entities\PaymentOption;
use Modules\Superadmin\Entities\Subscription;
use Modules\Petro\Entities\SettlementExpensePayment;
use Modules\Essentials\Entities\EssentialsEmployee;

class ExpenseController extends Controller

{

    protected $transactionUtil;

    protected $moduleUtil;
    
    protected $notificationUtil;
    
    protected $businessUtil;

    /**

     * Constructor

     *

     * @param TransactionUtil $transactionUtil

     * @return void

     */

    public function __construct(TransactionUtil $transactionUtil, ModuleUtil $moduleUtil,BusinessUtil $businessUtil,NotificationUtil $notificationUtil)

    {

        $this->transactionUtil = $transactionUtil;

        $this->moduleUtil = $moduleUtil;
        
        $this->notificationUtil = $notificationUtil;
        
        $this->businessUtil = $businessUtil;



        $this->dummyPaymentLine = [

            'method' => '', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'cheque_date' => '', 'bank_account_number' => '',

            'is_return' => 0, 'transaction_no' => ''

        ];

    }
    private function __payment_status($status){
        
        // dd($status);
        if($status == 'partial'){
            return 'bg-aqua';
        }elseif($status == 'due'){
            return 'bg-yellow';
        }elseif ($status == 'paid') {
            return 'bg-light-green';
        }elseif ($status == 'overdue') {
            return 'bg-red';
        }elseif ($status == 'partial-overdue') {
            return 'bg-red';
        }elseif ($status == 'pending') {
            return 'bg-info';
        }elseif ($status == 'over-payment') {
            return 'bg-light-green';
        }elseif ($status == 'price-later') {
            return 'bg-orange';
        }
    }


    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()

    {
        
        // return 123;
        if (!auth()->user()->can('expense.access')) {

            abort(403, 'Unauthorized action.');

        }
        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {

            $expenses = Transaction::leftJoin('expense_categories AS ec', 'transactions.expense_category_id', '=', 'ec.id')
                ->leftjoin(
                    'business_locations AS bl',
                    'transactions.location_id',
                    '=',

                    'bl.id'

                )
                ->leftjoin('contacts', 'contacts.id', '=', 'ec.payee_id')

                ->leftJoin('tax_rates as tr', 'transactions.tax_id', '=', 'tr.id')

                ->leftJoin('users AS U', 'transactions.expense_for', '=', 'U.id')
                
                ->leftJoin('essentials_employees AS EE', 'transactions.expense_for', '=', 'EE.id')

                ->leftJoin('users AS m', 'transactions.created_by', '=', 'm.id')

                ->leftjoin('transaction_payments AS TP', function ($join) {

                    $join->on('transactions.id', 'TP.transaction_id')->where('TP.amount', '!=', 0);

                })
                
                ->leftjoin('users as deleted','transactions.deleted_by','deleted.id')

                ->where('transactions.business_id', $business_id)

                ->where(function ($query) {

                    $query->whereIn('transactions.type', ['expense','ro_advance','ro_salary'])->orWhere('sub_type', 'expense');

                })
                ->withTrashed()

                ->select(
                    'deleted.username as deletedBy',

                    'transactions.id',

                    'transactions.document',

                    'transaction_date',

                    'ref_no',

                    'contacts.name as payee_name',

                    'ec.name as category',

                    'payment_status',

                    'additional_notes',

                    'final_total',

                    'is_settlement',

                    'bl.name as location_name',

                    'TP.method',

                    'TP.cheque_date',

                    'TP.cheque_number',

                    'TP.account_id',
                    'transactions.business_id',
                    DB::raw("CONCAT(COALESCE(U.surname, ''),' ',COALESCE(U.first_name, ''),' ',COALESCE(EE.name,'')) as expense_for"),

                    DB::raw("CONCAT(tr.name ,' (', tr.amount ,' )') as tax"),

                    DB::raw('SUM(TP.amount) as amount_paid'),

                    DB::raw("CONCAT(COALESCE(m.surname, ''),' ',COALESCE(m.first_name, ''),' ',COALESCE(m.last_name,'')) as created_by")

                )

                ->groupBy('transactions.id');



            //Add condition for expense for,used in sales representative expense report & list of expense

            if (request()->has('expense_for') && !empty(request()->get('expense_for'))) {

                $expense_for = request()->get('expense_for');

                if (!empty($expense_for)) {

                    $expenses->where('transactions.expense_for', $expense_for);

                }

            }
            
            if (request()->has('payee_name') && !empty(request()->get('payee_name'))) {

                $payee_name = request()->get('payee_name');

                if (!empty($payee_name)) {

                    $expenses->where('ec.payee_id', $payee_name);

                }

            }



            //Add condition for location,used in sales representative expense report & list of expense

            if (request()->has('location_id') && !empty(request()->get('location_id'))) {

                $location_id = request()->get('location_id');

                if (!empty($location_id)) {

                    $expenses->where('transactions.location_id', $location_id);

                }

            }



            //Add condition for expense category, used in list of expense,

            if (request()->has('expense_category_id') && !empty(request()->get('expense_category_id'))) {

                $expense_category_id = request()->get('expense_category_id');

                if (!empty($expense_category_id)) {

                    $expenses->where('transactions.expense_category_id', $expense_category_id);

                }

            }



            //Add condition for start and end date filter, uses in sales representative expense report & list of expense

            if (!empty(request()->start_date) && !empty(request()->end_date)) {

                $start = request()->start_date;

                $end =  request()->end_date;

                $expenses->whereDate('transaction_date', '>=', $start)

                    ->whereDate('transaction_date', '<=', $end);

            }



            //Add condition for expense category, used in list of expense,

            if (request()->has('expense_category_id') && !empty(request()->get('expense_category_id'))) {

                $expense_category_id = request()->get('expense_category_id');

                if (!empty($expense_category_id)) {

                    $expenses->where('transactions.expense_category_id', $expense_category_id);

                }

            }

            //Add condition for payment methods

            if (request()->has('method') && !empty(request()->get('method'))) {

                $method = request()->get('method');

                if (!empty($method)) {

                    $expenses->where('TP.method', $method);

                }

            }

            if (request()->has('fleet_id') && !empty(request()->get('fleet_id'))) {

                $fleet_id = request()->get('fleet_id');

                if (!empty($fleet_id)) {

                    $expenses->where('transactions.fleet_id', $fleet_id);

                }

            }



            $permitted_locations = auth()->user()->permitted_locations();

            if ($permitted_locations != 'all') {

                $expenses->whereIn('transactions.location_id', $permitted_locations);

            }



            //Add condition for payment status for the list of expense

            if (request()->has('payment_status') && !empty(request()->get('payment_status'))) {

                $payment_status = request()->get('payment_status');

                if (!empty($payment_status)) {

                    $expenses->where('transactions.payment_status', $payment_status);

                }

            }



            return Datatables::of($expenses)

                ->addColumn(

                    'action',

                    '<div class="btn-group">

                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" 

                            data-toggle="dropdown" aria-expanded="false"> @lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown

                                </span>

                        </button>

                    <ul class="dropdown-menu dropdown-menu-left" role="menu">
                    
                    @if(empty($deletedBy))

                    @can("expense.update")

                    <li><a href="{{action(\'ExpenseController@edit\', [$id])}}"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a></li>

                    @endcan

                    @if($document)

                        <li><a href="{{ url(\'uploads/documents/\' . $document)}}" 

                        download=""><i class="fa fa-download" aria-hidden="true"></i> @lang("purchase.download_document")</a></li>

                        @if(isFileImage($document))

                            <li><a href="#" data-href="{{ url(\'uploads/documents/\' . $document)}}" class="view_uploaded_document"><i class="fa fa-picture-o" aria-hidden="true"></i>@lang("lang_v1.view_document")</a></li>

                        @endif

                    @endif

                    @can("expense.delete")

                        <li><a data-href="{{action(\'ExpenseController@destroy\', [$id])}}" class="delete_expense"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</a></li>

                    @endcan

                    <li class="divider"></li> 

                    @if($payment_status != "paid")

                        @can("add.payments")

                            <li><a href="{{action("TransactionPaymentController@addPayment", [$id])}}" class="add_payment_modal"><i class="fa fa-money" aria-hidden="true"></i> @lang("purchase.add_payment")</a></li>

                        @endcan

                    @endif
                    
                    @endif
                    
                    <li><a href="{{action("TransactionPaymentController@print", [$id])}}" class="view_payment_modal"><i class="fa fa-money" aria-hidden="true" ></i> @lang("purchase.view_print")</a></li>

                    <li><a href="{{action("TransactionPaymentController@show", [$id])}}" class="view_payment_modal"><i class="fa fa-money" aria-hidden="true" ></i> @lang("purchase.view_payments")</a></li>

                    </ul></div>'

                )

                ->removeColumn('id')

                ->editColumn(

                    'final_total',

                    function($row){
                        $html = '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="'.(empty($row->deletedBy) ? $row->final_total : 0).'">'.$this->transactionUtil->num_f($row->final_total).'</span>';
                        
                        
                        if($this->moduleUtil->hasThePermissionInSubscription(request()->session()->get('user.business_id'), 'individual_expense')){
                            if(strtotime($this->transactionUtil->__getVatEffectiveDate(request()->session()->get('user.business_id'))) <= strtotime($row->transaction_date)){
                                $html .= '<br><a href="#" data-href="' . action('\Modules\Vat\Http\Controllers\VatController@updateSingleVats', ['transaction_id' => $row->id]) . '" class="regenerate-vat text-danger">' . __("superadmin::lang.regenerate_vat") . '</a>';
                            }
                            
                        }
                        
                        return $html;
                    }

                )

                ->editColumn(

                    'for_sum_total',

                    '{{empty($deletedBy) ? $final_total : 0}}'

                )

                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')

                ->editColumn('ref_no', function ($row) {

                    $ref = $row->ref_no." ".$row->deletedBy;



                    if ($row->is_settlement) {

                        $settlement_expense = SettlementExpensePayment::where('transaction_id', $row->id)->first();

                        if (!empty($settlement_expense)) {

                            $ref .= '<br><b>Reference No: </b>' . $settlement_expense->reference_no . '<br><b>Reason: </b>' . $settlement_expense->reason;

                        }

                    }

                    return $ref;

                })
                ->editColumn('location_name', function ($row) {


                    return '<td class="clickable_td sorting_1">'.$row->location_name.'</td>';


                })
                ->editColumn('payment_status', function ($row) {
                    return '<a href="'.action("TransactionPaymentController@show", [$row->id]).'" class="view_payment_modal payment-status no-print" data-orig-value="'.$row->payment_status.'" data-status-name="'.__('lang_v1.'.$row->payment_status).'"><span class="label '.$this->__payment_status($row->payment_status).'">'.__('lang_v1.' . $row->payment_status).'</span></a><span class="print_section">'.__('lang_v1.' . $row->payment_status).'</span>';
                })

                ->addColumn('payment_due', function ($row) {

                    $due = empty($row->deletedBy) ? ($row->final_total - $row->amount_paid) : 0;

                    return '<span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="' . $due . '">' . $due . '</span>';

                })
                ->addColumn('notes', function ($row) {
                    $routeOperation = RouteOperation::where("transaction_id",$row->id)->first();
                    $id = $routeOperation ? $routeOperation->id : null;
                    return $id ? '<button type="button" class="btn btn-primary btn-modal pull-center" id="add_expense_btn" data-href="'.action('\Modules\Fleet\Http\Controllers\RouteOperationController@addExpense', [$id]).'" data-container=".fleet_model"> <i class="fa fa-plus"></i> '.__('fleet::lang.notes').'</button>': "Route Not Found";
                })

                ->addColumn('payment_method', function ($row) {

                    $html = '';

                    if ($row->payment_status == 'due') {

                        return 'Credit Expense';

                    }

                    
                    
                    if (strtolower($row->method) == 'bank_transfer' || strtolower($row->method) == 'direct_bank_deposit' || strtolower($row->method) == 'bank' || strtolower($row->method) == 'cheque') {
                        $html .= ucfirst(str_replace("_"," ",$row->method));
                        
                        $bank_acccount = Account::find($row->account_id);
                        if (!empty($bank_acccount)) {
                            $html .= '<br><b>Bank Name:</b> ' . $bank_acccount->name . '</br>';
                        }
                        if(!empty($row->cheque_number)){
                            $html .= '<b>Cheque Number:</b> ' . $row->cheque_number . '</br>';
                        }
                        if(!empty($row->cheque_date)){
                            $html .= '<b>Cheque Date:</b> ' . $this->transactionUtil->format_date($row->cheque_date) . '</br>';
                        }
                        
                    } else {
                        $html .= ucfirst(str_replace("_"," ",$row->method));
                    }

                    return $html;


                })
                
                ->setRowAttr([
                    'class' => function($row){
                        if(!empty($row->deletedBy)){
                            return 'deleted-row';
                        }else{
                            return '';
                        }
                    },
                    'title' => function($row){
                        if(!empty($row->deletedBy)){
                            return __('sale.deleted_by')." ".$row->deletedBy;
                        }else{
                            return '';
                        }
                    },
                ])

                ->rawColumns(['final_total', 'action', 'payment_status', 'payment_due', 'ref_no','location_name', 'payment_method'])

                ->make(true);

        }



        $business_id = request()->session()->get('user.business_id');



        $categories = ExpenseCategory::where('business_id', $business_id)

            ->pluck('name', 'id');



        $users = User::forDropdown($business_id, false, true, true);
        $employees = EssentialsEmployee::pluck('name', 'id');
        $payee_names = DB::table('contacts')
                    ->where('business_id', $business_id)
                    ->distinct()
                    ->orderBy('id', 'DESC')
                    ->pluck('name','id');



        $business_locations = BusinessLocation::forDropdown($business_id, true);



        return view('expense.index')

            ->with(compact('categories', 'business_locations', 'users','payee_names', 'employees'));

    }
    
    public function routeperationExpenses($id)

    {
        if (!auth()->user()->can('expense.access')) {

            abort(403, 'Unauthorized action.');

        }
        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {

            $expenses = Transaction::leftJoin('expense_categories AS ec', 'transactions.expense_category_id', '=', 'ec.id')
                ->leftjoin(
                    'business_locations AS bl',
                    'transactions.location_id',
                    '=',

                    'bl.id'

                )
                ->leftjoin('contacts', 'contacts.id', '=', 'ec.payee_id')

                ->leftJoin('tax_rates as tr', 'transactions.tax_id', '=', 'tr.id')

                ->leftJoin('users AS U', 'transactions.expense_for', '=', 'U.id')

                ->leftJoin('users AS m', 'transactions.created_by', '=', 'm.id')

                ->leftjoin('transaction_payments AS TP', function ($join) {

                    $join->on('transactions.id', 'TP.transaction_id')->where('TP.amount', '!=', 0);

                })

                ->where('transactions.business_id', $business_id)
                ->where('transactions.parent_transaction_id',$id)

                ->where(function ($query) {

                    $query->whereIn('transactions.type', ['expense','ro_advance','ro_salary'])->orWhere('sub_type', 'expense');

                })

                ->select(

                    'transactions.id',

                    'transactions.document',

                    'transaction_date',

                    'ref_no',

                    'contacts.name as payee_name',

                    'ec.name as category',

                    'payment_status',

                    'additional_notes',

                    'final_total',

                    'is_settlement',

                    'bl.name as location_name',

                    'TP.method',

                    'TP.cheque_date',

                    'TP.cheque_number',

                    'TP.account_id',
                    'transactions.business_id',
                    DB::raw("CONCAT(COALESCE(U.surname, ''),' ',COALESCE(U.first_name, ''),' ',COALESCE(U.last_name,'')) as expense_for"),

                    DB::raw("CONCAT(tr.name ,' (', tr.amount ,' )') as tax"),

                    DB::raw('SUM(TP.amount) as amount_paid'),

                    DB::raw("CONCAT(COALESCE(m.surname, ''),' ',COALESCE(m.first_name, ''),' ',COALESCE(m.last_name,'')) as created_by")

                )

                ->groupBy('transactions.id');



            //Add condition for expense for,used in sales representative expense report & list of expense

            if (request()->has('expense_for') && !empty(request()->get('expense_for'))) {

                $expense_for = request()->get('expense_for');

                if (!empty($expense_for)) {

                    $expenses->where('transactions.expense_for', $expense_for);

                }

            }
            
            if (request()->has('payee_name') && !empty(request()->get('payee_name'))) {

                $payee_name = request()->get('payee_name');

                if (!empty($payee_name)) {

                    $expenses->where('ec.payee_id', $payee_name);

                }

            }



            //Add condition for location,used in sales representative expense report & list of expense

            if (request()->has('location_id') && !empty(request()->get('location_id'))) {

                $location_id = request()->get('location_id');

                if (!empty($location_id)) {

                    $expenses->where('transactions.location_id', $location_id);

                }

            }



            //Add condition for expense category, used in list of expense,

            if (request()->has('expense_category_id') && !empty(request()->get('expense_category_id'))) {

                $expense_category_id = request()->get('expense_category_id');

                if (!empty($expense_category_id)) {

                    $expenses->where('transactions.expense_category_id', $expense_category_id);

                }

            }



            //Add condition for start and end date filter, uses in sales representative expense report & list of expense

            if (!empty(request()->start_date) && !empty(request()->end_date)) {

                $start = request()->start_date;

                $end =  request()->end_date;

                $expenses->whereDate('transaction_date', '>=', $start)

                    ->whereDate('transaction_date', '<=', $end);

            }



            //Add condition for expense category, used in list of expense,

            if (request()->has('expense_category_id') && !empty(request()->get('expense_category_id'))) {

                $expense_category_id = request()->get('expense_category_id');

                if (!empty($expense_category_id)) {

                    $expenses->where('transactions.expense_category_id', $expense_category_id);

                }

            }

            //Add condition for payment methods

            if (request()->has('method') && !empty(request()->get('method'))) {

                $method = request()->get('method');

                if (!empty($method)) {

                    $expenses->where('TP.method', $method);

                }

            }

            if (request()->has('fleet_id') && !empty(request()->get('fleet_id'))) {

                $fleet_id = request()->get('fleet_id');

                if (!empty($fleet_id)) {

                    $expenses->where('transactions.fleet_id', $fleet_id);

                }

            }



            $permitted_locations = auth()->user()->permitted_locations();

            if ($permitted_locations != 'all') {

                $expenses->whereIn('transactions.location_id', $permitted_locations);

            }



            //Add condition for payment status for the list of expense

            if (request()->has('payment_status') && !empty(request()->get('payment_status'))) {

                $payment_status = request()->get('payment_status');

                if (!empty($payment_status)) {

                    $expenses->where('transactions.payment_status', $payment_status);

                }

            }



            return Datatables::of($expenses)

                ->addColumn(

                    'action',

                    '<div class="btn-group">

                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" 

                            data-toggle="dropdown" aria-expanded="false"> @lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown

                                </span>

                        </button>

                    <ul class="dropdown-menu dropdown-menu-left" role="menu">

                    @can("expense.update")

                    <li><a href="{{action(\'ExpenseController@edit\', [$id])}}"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a></li>

                    @endcan

                    @if($document)

                        <li><a href="{{ url(\'uploads/documents/\' . $document)}}" 

                        download=""><i class="fa fa-download" aria-hidden="true"></i> @lang("purchase.download_document")</a></li>

                        @if(isFileImage($document))

                            <li><a href="#" data-href="{{ url(\'uploads/documents/\' . $document)}}" class="view_uploaded_document"><i class="fa fa-picture-o" aria-hidden="true"></i>@lang("lang_v1.view_document")</a></li>

                        @endif

                    @endif

                    @can("expense.delete")

                        <li><a data-href="{{action(\'ExpenseController@destroy\', [$id])}}" class="delete_expense"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</a></li>

                    @endcan

                    <li class="divider"></li> 

                    @if($payment_status != "paid")

                        @can("add.payments")

                            <li><a href="{{action("TransactionPaymentController@addPayment", [$id])}}" class="add_payment_modal"><i class="fa fa-money" aria-hidden="true"></i> @lang("purchase.add_payment")</a></li>

                        @endcan

                    @endif

                    <li><a href="{{action("TransactionPaymentController@show", [$id])}}" class="view_payment_modal"><i class="fa fa-money" aria-hidden="true" ></i> @lang("purchase.view_payments")</a></li>

                    </ul></div>'

                )

                ->removeColumn('id')

                ->editColumn(

                    'final_total',

                    '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="{{$final_total}}">{{$final_total}}</span>'

                )

                ->editColumn(

                    'for_sum_total',

                    '{{$final_total}}'

                )

                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')

                ->editColumn('ref_no', function ($row) {

                    $ref = $row->ref_no;



                    if ($row->is_settlement) {

                        $settlement_expense = SettlementExpensePayment::where('transaction_id', $row->id)->first();

                        if (!empty($settlement_expense)) {

                            $ref .= '<br><b>Reference No: </b>' . $settlement_expense->reference_no . '<br><b>Reason: </b>' . $settlement_expense->reason;

                        }

                    }

                    return $ref;

                })
                ->editColumn('location_name', function ($row) {


                    return '<td class="clickable_td sorting_1">'.$row->location_name.'</td>';


                })
                ->editColumn('payment_status', function ($row) {
                    return '<a href="'.action("TransactionPaymentController@show", [$row->id]).'" class="view_payment_modal payment-status no-print" data-orig-value="'.$row->payment_status.'" data-status-name="'.__('lang_v1.'.$row->payment_status).'"><span class="label '.$this->__payment_status($row->payment_status).'">'.__('lang_v1.' . $row->payment_status).'</span></a><span class="print_section">'.__('lang_v1.' . $row->payment_status).'</span>';
                })

                ->addColumn('payment_due', function ($row) {

                    $due = $row->final_total - $row->amount_paid;

                    return '<span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="' . $due . '">' . $due . '</span>';

                })

                ->addColumn('payment_method', function ($row) {

                    $html = '';

                    if ($row->payment_status == 'due') {

                        return 'Credit Expense';

                    }

                    
                    
                    if (strtolower($row->method) == 'bank_transfer' || strtolower($row->method) == 'direct_bank_deposit' || strtolower($row->method) == 'bank' || strtolower($row->method) == 'cheque') {
                        $html .= ucfirst(str_replace("_"," ",$row->method));
                        
                        $bank_acccount = Account::find($row->account_id);
                        if (!empty($bank_acccount)) {
                            $html .= '<br><b>Bank Name:</b> ' . $bank_acccount->name . '</br>';
                        }
                        if(!empty($row->cheque_number)){
                            $html .= '<b>Cheque Number:</b> ' . $row->cheque_number . '</br>';
                        }
                        if(!empty($row->cheque_date)){
                            $html .= '<b>Cheque Date:</b> ' . $this->transactionUtil->format_date($row->cheque_date) . '</br>';
                        }
                        
                    } else {
                        $html .= ucfirst(str_replace("_"," ",$row->method));
                    }

                    return $html;

                })

                ->rawColumns(['final_total', 'action', 'payment_status', 'payment_due', 'ref_no','location_name', 'payment_method'])

                ->make(true);

        }



    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        if (!auth()->user()->can('expense.create')) {

            abort(403, 'Unauthorized action.');

        }



        $business_id = request()->session()->get('user.business_id');



        //Check if subscribed or not

        if (!$this->moduleUtil->isSubscribed($business_id)) {

            return $this->moduleUtil->expiredResponse(action('ExpenseController@index'));

        }



        $account_module = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');

        $payment_line = $this->dummyPaymentLine;
        $first_location = BusinessLocation::where('business_id', $business_id)->first();

        $payment_types = $this->transactionUtil->payment_types($first_location->id, true, false, true, false, true,"is_expense_enabled");



        unset($payment_types['credit_sale']);

        $accounts = [];

        $expense_account_type_id = AccountType::where('business_id', $business_id)->where('name', 'Expenses')->first();

        $current_account_type_id = AccountType::where('business_id', $business_id)->where('name', 'Current Assets')->first();

        $current_liability_account_type = AccountType::where('business_id', $business_id)->where('name', 'Current Liabilities')->first();

        $current_liability_account_type_id = !empty($current_liability_account_type) ? $current_liability_account_type->id : 0;

        $expense_accounts = [];

        $expense_account_id = null;

        $payee_name = Contact::select('name')->where('business_id', $business_id)->first();



        if ($account_module) {

            if (!empty($expense_account_type_id)) {
                
                $expense_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')
                ->where('accounts.business_id', $business_id)
                ->where('account_groups.name', 'CPC')
                ->orWhere('accounts.account_type_id', $expense_account_type_id->id)
                ->select('accounts.id', 'accounts.name')->get()->pluck('name','id');

                 
            }

        } else {

            $expense_account_id = Account::where('name', 'Expenses')->where('business_id', $business_id)->first()->id;

            $expense_accounts = Account::where('business_id', $business_id)->where('name', 'Expenses')->pluck('name', 'id');

        }
        
        $current_liabilities_accounts =  Account::where('business_id', $business_id)->where('account_type_id', $current_liability_account_type_id)->pluck('name', 'id');



        $business_locations = BusinessLocation::forDropdown($business_id);

        $contacts = Contact::contactDropdown($business_id, false, false);

        $expense_categories = ExpenseCategory::where('business_id', $business_id)

            ->pluck('name', 'id');

        $users = User::forDropdown($business_id, true, true);
        $employees = EssentialsEmployee::pluck('name', 'id');

        $fleets = Fleet::where('business_id', $business_id)->pluck('vehicle_number', 'id');



        $taxes = TaxRate::forBusinessDropdown($business_id, true, true);



        $ref_count = $this->transactionUtil->onlyGetReferenceCount('expense', null, false);

        //Generate reference number

        $ref_no = $this->transactionUtil->generateReferenceNumber('expense', $ref_count);





        $temp_data = DB::table('temp_data')->where('business_id', $business_id)->select('add_expense_data')->first();

        if (!empty($temp_data)) {

            $temp_data = json_decode($temp_data->add_expense_data);

        }

        if (!request()->session()->get('business.popup_load_save_data')) {

            $temp_data = [];

        }

        $cash_account_id = Account::getAccountByAccountName('Cash')->id;



        $fleet_module = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'fleet_module');

$bank_group_accounts = Account::leftJoin('account_groups', 'accounts.asset_type', 'account_groups.id')
    ->where('accounts.business_id', $business_id)
    ->where('account_groups.name', 'Bank Account')
    ->pluck('accounts.name', 'accounts.id');

// Remove "Issued Post Dated Cheques"
$bank_group_accounts = $bank_group_accounts->reject(function ($name) {
    return trim($name) === "Issued Post Dated Cheques";
});

// Debugging to check the filtered collection


        $cpc_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')
            ->where('accounts.business_id', $business_id)
            ->where('account_groups.name', 'CPC')
            ->pluck('accounts.name', 'accounts.id');
        

        return view('expense.create')

            ->with(compact(
                'cpc_accounts',
                
                'bank_group_accounts',
                'cash_account_id',

                'cash_account_id',

                'ref_no',

                'account_module',

                'accounts',

                'expense_accounts',

                'payment_types',

                'payment_line',

                'expense_categories',

                'business_locations',

                'users',
                
                'employees',

                'fleets',

                'fleet_module',

                'taxes',

                'temp_data',

                'contacts',

                'current_liabilities_accounts',

                'expense_account_id',
                'payee_name'

            ));

    }



    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function storeold(Request $request)

    {
        // dd('123');

        if (!auth()->user()->can('expense.create')) {

            abort(403, 'Unauthorized action.');

        }



        try {

            $business_id = $request->session()->get('user.business_id');



            DB::table('temp_data')->where('business_id', $business_id)->update(['add_expense_data' => '']);

            //Check if subscribed or not

            if (!$this->moduleUtil->isSubscribed($business_id)) {

                return $this->moduleUtil->expiredResponse(action('ExpenseController@index'));

            }



            //Validate document size

            $request->validate([

                'document' => 'file|max:' . (config('constants.document_size_limit') / 1000)

            ]);



            $transaction_data = $request->only(['is_vat', 'ref_no', 'transaction_date', 'location_id', 'final_total', 'expense_for', 'fleet_id', 'additional_notes', 'expense_category_id', 'tax_id', 'contact_id']);
            $transaction_data['transaction_date'] = $transaction_data['transaction_date'] ?? $request->expense_transaction_date;
            $has_reviewed = $this->transactionUtil->hasReviewed($transaction_data['transaction_date']);
        
            if(!empty($has_reviewed)){
                $output              = [
                    'success' => 0,
                    'msg'     =>__('lang_v1.review_first'),
                ];
                
                return redirect()->back()->with(['status' => $output]);
            }
            
            $reviewed = $this->transactionUtil->get_review($transaction_data['transaction_date'],$transaction_data['transaction_date']);
            
        
            if(!empty($reviewed)){
                $output = [
                    'success' => 0,
                    'msg'     =>"You can't add an expense for an already reviewed date",
                ];
                
                return redirect('expenses')->with('status', $output);
            }
            
            
            
            

            $user_id = $request->session()->get('user.id');

            $transaction_data['business_id'] = $business_id;

            $transaction_data['created_by'] = $user_id;

            $transaction_data['type'] = 'expense';

            $transaction_data['status'] = 'final';

            $transaction_data['payment_status'] = 'due';

            $transaction_data['expense_account'] = $request->expense_account;

            $transaction_data['controller_account'] = $request->controller_account;
// dd($transaction_data['controller_account']);
            $transaction_data['transaction_date'] = $this->transactionUtil->uf_date($transaction_data['transaction_date'], true);

            $transaction_data['final_total'] = $this->transactionUtil->num_uf(

                $transaction_data['final_total']

            );



            $transaction_data['total_before_tax'] = $transaction_data['final_total'];

            if (!empty($transaction_data['tax_id'])) {

                $tax_details = TaxRate::find($transaction_data['tax_id']);

                $transaction_data['total_before_tax'] = $this->transactionUtil->calc_percentage_base($transaction_data['final_total'], $tax_details->amount);

                $transaction_data['tax_amount'] = $transaction_data['final_total'] - $transaction_data['total_before_tax'];

            }



            //Update reference count

            $ref_count = $this->transactionUtil->setAndGetReferenceCount('expense');

            //Generate reference number

            if (empty($transaction_data['ref_no'])) {

                $transaction_data['ref_no'] = $this->transactionUtil->generateReferenceNumber('expense', $ref_count);

            }



            //upload document

            $document_name = $this->transactionUtil->uploadFile($request, 'document', 'documents');

            if (!empty($document_name)) {

                $transaction_data['document'] = $document_name;

            }



            if ($request->has('is_recurring')) {

                $transaction_data['is_recurring'] = 1;

                $transaction_data['recur_interval'] = !empty($request->input('recur_interval')) ? $request->input('recur_interval') : 1;

                $transaction_data['recur_interval_type'] = $request->input('recur_interval_type');

                $transaction_data['recur_repetitions'] = $request->input('recur_repetitions');

                $transaction_data['subscription_repeat_on'] = $request->input('recur_interval_type') == 'months' && !empty($request->input('subscription_repeat_on')) ? $request->input('subscription_repeat_on') : null;

            }
            
            // dump($transaction_data,'$transaction_data');



            DB::beginTransaction();

            $transaction = Transaction::create($transaction_data);
            
            // add VAT components
            $this->transactionUtil->calculateAndUpdateVAT($transaction);
            
            
            $transaction_id =  $transaction->id;
            // dd($request->payment[0],'payment 00');

            $tp = null;

            if (!empty($request->payment[0])) {

                $inputs = $request->payment[0];
                
                
                // handle update post dated cheques logic
                if(!empty($inputs['update_post_dated_cheque'])){
                    $inputs['related_account_id'] = $inputs['account_id'];
                    $inputs['account_id'] = $this->transactionUtil->account_exist_return_id('Issued Post Dated Cheques'); 
                }
                

                $inputs['paid_on'] = $transaction->transaction_date;

                $inputs['transaction_id'] = $transaction->id;

                $inputs['cheque_date'] = !empty($inputs['cheque_date']) ? $inputs['cheque_date'] : $transaction->transaction_date;

                
                $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);

                $amount = $inputs['amount'];
                    
                    
                    // dd( $amount ,$inputs['method'] , $amount > 0 && $inputs['method'] != 'credit_expense');
                if ($amount > 0 && $inputs['method'] != 'credit_expense') {
                    
                    // Check payment method (Cash, Credit, etc.)
                        if ($inputs['method'] == 'cash') {
                            // If the full amount is paid
                            if ($amount >= $transaction->final_total) {
                                $transaction->payment_status = 'paid'; // Set status to paid
                            } else {
                                $transaction->payment_status = 'partial'; // Set status to partial if it's less than the total
                            }
                        }

                    $transaction->save();
                    // dd($transaction);
                    $inputs['created_by'] = auth()->user()->id;

                    $inputs['payment_for'] = $transaction->contact_id;
                    // $transaction->save();
                    // dd($inputs['method'] , $transaction->payment_status , $transaction ,$transaction->save());
                    $prefix_type = 'expense_payment';
                    if ($transaction->type == 'expense') {
                        $prefix_type = 'expense_payment';

                    }

                    // $transaction->controller_account = !empty($inputs['controller_account']) ? $inputs['controller_account'] : null;




                    $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type);
// dd($ref_count);
                    //Generate reference number

                    $inputs['payment_ref_no'] = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);



                    $inputs['business_id'] = $request->session()->get('business.id');

                    $inputs['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');


                    // post dated cheque input
                    $inputs['post_dated_cheque'] = $inputs['post_dated_cheque'] ?? 0;
                    $inputs['update_post_dated_cheque'] = $inputs['update_post_dated_cheque'] ?? 0;

                    $inputs['is_return'] =  0; //added by 

                    unset($inputs['transaction_no_1']);

                    unset($inputs['transaction_no_2']);

                    unset($inputs['transaction_no_3']);

                    unset($inputs['controller_account']);
                    
                    
                    $cheque_nos = "";
                    // dd($request->select_cheques);
                    if(!empty($request->select_cheques)){
                        foreach ($request->select_cheques as $select_cheque) {
                            if (!empty($select_cheque)) {
                                $account_transaction = AccountTransaction::find($select_cheque);
                                
                                $transaction_payment = TransactionPayment::find($account_transaction->transaction_payment_id);
                                // dd($transaction_payment);
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
                                        
                                        $cheque_nos .= !empty($transaction_payment->cheque_number) ? $transaction_payment->cheque_number."," : "";
                                        
                                        $transaction_payment->is_deposited = 1;
                                        $transaction_payment->save();
                                    }
                                }
                            }
                        }
                        
                        $inputs['cheque_number'] = $cheque_nos;
                    }
                    
                    $tp = TransactionPayment::create($inputs);
// dd($tp,'tp tp tp');


                    //update payment status

                    $this->transactionUtil->updatePaymentStatus($transaction_id, $transaction->final_total, $inputs['method']);
// dd('store');
                }

            }

            $this->addAccountTransaction($transaction, $request, $business_id, $tp);
            
            $newReview = ["created_by" => request()->session()->get('user.id'),  "description" => "Created a new expense: ".$transaction_data['ref_no'],"module" => "expense"];
            $reviewed = $this->transactionUtil->reviewChange($transaction_data['transaction_date'],$newReview);
            
            
            $accountName = Account::find($request->expense_account);
            $expense_category = ExpenseCategory::find($request->expense_category_id)->name ?? '';
            $sms_data = array(
                    'transaction_date' => $this->transactionUtil->format_date($transaction_data['transaction_date']),
                    'ref' => $transaction_data['ref_no'],
                    'amount' => $this->transactionUtil->num_f($transaction->final_total),
                    'account' => !empty($accountName) ? $accountName->name : "",
                    'staff' => auth()->user()->username,
                    'expense_category' => $expense_category,
            );
            $this->notificationUtil->sendGeneralNotification('expense_created',$sms_data);
            
            

            DB::commit();
            
            $output = [
                
                'success' => 1,

                'msg' => __('expense.expense_add_success')

            ];

            if($request->is_print == 1)
           {
               return redirect()->route('expense-print',[$transaction_id]); 
           }

        } catch (\Exception $e) {

            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());



            $output = [

                'success' => 0,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return redirect('expenses')->with('status', $output);

    }

    
    public function store(Request $request)

    {
        // dd('123');

        if (!auth()->user()->can('expense.create')) {

            abort(403, 'Unauthorized action.');

        }



        try {

            $business_id = $request->session()->get('user.business_id');



            DB::table('temp_data')->where('business_id', $business_id)->update(['add_expense_data' => '']);

            //Check if subscribed or not

            if (!$this->moduleUtil->isSubscribed($business_id)) {

                return $this->moduleUtil->expiredResponse(action('ExpenseController@index'));

            }



            //Validate document size

            $request->validate([

                'document' => 'file|max:' . (config('constants.document_size_limit') / 1000)

            ]);



            $transaction_data = $request->only(['is_vat', 'ref_no', 'transaction_date', 'location_id', 'final_total', 'expense_for', 'fleet_id', 'additional_notes', 'expense_category_id', 'tax_id', 'contact_id']);
            $transaction_data['transaction_date'] = $transaction_data['transaction_date'] ?? $request->expense_transaction_date;
            $has_reviewed = $this->transactionUtil->hasReviewed($transaction_data['transaction_date']);
        
            if(!empty($has_reviewed)){
                $output              = [
                    'success' => 0,
                    'msg'     =>__('lang_v1.review_first'),
                ];
                
                return redirect()->back()->with(['status' => $output]);
            }
            
            $reviewed = $this->transactionUtil->get_review($transaction_data['transaction_date'],$transaction_data['transaction_date']);
            
        
            if(!empty($reviewed)){
                $output = [
                    'success' => 0,
                    'msg'     =>"You can't add an expense for an already reviewed date",
                ];
                
                return redirect('expenses')->with('status', $output);
            }
            
            
            
            

            $user_id = $request->session()->get('user.id');

            $transaction_data['business_id'] = $business_id;

            $transaction_data['created_by'] = $user_id;

            $transaction_data['type'] = 'expense';

            $transaction_data['status'] = 'final';

            $transaction_data['payment_status'] = 'due';

            $transaction_data['expense_account'] = $request->expense_account;

            $transaction_data['controller_account'] = $request->controller_account;
// dd($transaction_data['controller_account']);
            $transaction_data['transaction_date'] = $this->transactionUtil->uf_date($transaction_data['transaction_date'], true);

            $transaction_data['final_total'] = $this->transactionUtil->num_uf(

                $transaction_data['final_total']

            );



            $transaction_data['total_before_tax'] = $transaction_data['final_total'];

            if (!empty($transaction_data['tax_id'])) {

                $tax_details = TaxRate::find($transaction_data['tax_id']);

                $transaction_data['total_before_tax'] = $this->transactionUtil->calc_percentage_base($transaction_data['final_total'], $tax_details->amount);

                $transaction_data['tax_amount'] = $transaction_data['final_total'] - $transaction_data['total_before_tax'];

            }



            //Update reference count

            $ref_count = $this->transactionUtil->setAndGetReferenceCount('expense');

            //Generate reference number

            if (empty($transaction_data['ref_no'])) {

                $transaction_data['ref_no'] = $this->transactionUtil->generateReferenceNumber('expense', $ref_count);

            }



            //upload document

            $document_name = $this->transactionUtil->uploadFile($request, 'document', 'documents');

            if (!empty($document_name)) {

                $transaction_data['document'] = $document_name;

            }



            if ($request->has('is_recurring')) {

                $transaction_data['is_recurring'] = 1;

                $transaction_data['recur_interval'] = !empty($request->input('recur_interval')) ? $request->input('recur_interval') : 1;

                $transaction_data['recur_interval_type'] = $request->input('recur_interval_type');

                $transaction_data['recur_repetitions'] = $request->input('recur_repetitions');

                $transaction_data['subscription_repeat_on'] = $request->input('recur_interval_type') == 'months' && !empty($request->input('subscription_repeat_on')) ? $request->input('subscription_repeat_on') : null;

            }
            
            // dump($transaction_data,'$transaction_data');



            DB::beginTransaction();

            $transaction = Transaction::create($transaction_data);
            // dd($transaction,'$transaction');
            // add VAT components
            $this->transactionUtil->calculateAndUpdateVAT($transaction);
            
            
            $transaction_id =  $transaction->id;
            // dd($request->payment[0],'payment 00');

            $tp = null;

            if (!empty($request->payment[0])) {

                $inputs = $request->payment[0];
                
                
                // handle update post dated cheques logic
                if(!empty($inputs['update_post_dated_cheque'])){
                    $inputs['related_account_id'] = $inputs['account_id'];
                    $inputs['account_id'] = $this->transactionUtil->account_exist_return_id('Issued Post Dated Cheques'); 
                }
                
 
                $inputs['paid_on'] = $transaction->transaction_date;

                $inputs['transaction_id'] = $transaction->id;

                $inputs['cheque_date'] = !empty($inputs['cheque_date']) ? $inputs['cheque_date'] : $transaction->transaction_date;

                
                $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);

                $amount = $inputs['amount'];
                    
                    // dd( $amount ,$inputs['method'] , $amount > 0 && $inputs['method'] != 'credit_expense');
                
                if ($amount > 0) {
                    if ($inputs['method'] != 'credit_expense') {
                            
                        
                        // Check payment method (Cash, Credit, etc.)
                            if ($inputs['method'] == 'cash') {
                                // If the full amount is paid
                                if ($amount >= $transaction->final_total) {
                                    $transaction->payment_status = 'paid'; // Set status to paid
                                } else {
                                    $transaction->payment_status = 'partial'; // Set status to partial if it's less than the total
                                }
                            }
    
                        $transaction->save();
                        // dd($transaction);
                        $inputs['created_by'] = auth()->user()->id;
    
                        $inputs['payment_for'] = $transaction->contact_id;
                        // $transaction->save();
                        // dd($inputs['method'] , $transaction->payment_status , $transaction ,$transaction->save());
                        $prefix_type = 'expense_payment';
                        if ($transaction->type == 'expense') {
                            $prefix_type = 'expense_payment';
    
                        }
    
                        // $transaction->controller_account = !empty($inputs['controller_account']) ? $inputs['controller_account'] : null;
    
    
    
    
                        $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type);
    // dd($ref_count);
                        //Generate reference number
    
                        $inputs['payment_ref_no'] = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);
    
    
    
                        $inputs['business_id'] = $request->session()->get('business.id');
    
                        $inputs['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');
    
    
                        // post dated cheque input
                        $inputs['post_dated_cheque'] = $inputs['post_dated_cheque'] ?? 0;
                        $inputs['update_post_dated_cheque'] = $inputs['update_post_dated_cheque'] ?? 0;
    
                        $inputs['is_return'] =  0; //added by 
    
                        unset($inputs['transaction_no_1']);
    
                        unset($inputs['transaction_no_2']);
    
                        unset($inputs['transaction_no_3']);
    
                        unset($inputs['controller_account']);
                        
                        
                        $cheque_nos = "";
                        // dd($request->select_cheques);
                        if(!empty($request->select_cheques)){
                            foreach ($request->select_cheques as $select_cheque) {
                                if (!empty($select_cheque)) {
                                    $account_transaction = AccountTransaction::find($select_cheque);
                                    
                                    $transaction_payment = TransactionPayment::find($account_transaction->transaction_payment_id);
                                    // dd($transaction_payment);
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
                                            
                                            $cheque_nos .= !empty($transaction_payment->cheque_number) ? $transaction_payment->cheque_number."," : "";
                                            
                                            $transaction_payment->is_deposited = 1;
                                            $transaction_payment->save();
                                        }
                                    }
                                }
                            }
                            
                            $inputs['cheque_number'] = $cheque_nos;
                        }
                        
                        $tp = TransactionPayment::create($inputs);
    
    
                        //update payment status
    
                        $this->transactionUtil->updatePaymentStatus($transaction_id, $transaction->final_total, $inputs['method']);
    // dd('store');
                    } else {
                        // Special credit expense payment logic
                        $inputs['created_by'] = auth()->user()->id;
                        $inputs['payment_for'] = $transaction->contact_id;
                        $inputs['business_id'] = $request->session()->get('business.id');
                        
                        // Generate proper reference number (don't use time())
                        $prefix_type = 'expense_payment';
                        // $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type);
                        // $inputs['payment_ref_no'] = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);
                        
                        // Essential fields for any payment
                        $inputs['paid_on'] = $transaction->transaction_date;
                        $inputs['transaction_id'] = $transaction->id;
                        // $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);
                        $inputs['method'] = 'credit_expense';
                        $inputs['cheque_date'] = !empty($inputs['cheque_date']) ? $inputs['cheque_date'] : $transaction->transaction_date;
                        
                        // Document handling if exists
                        // $inputs['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');
                        
                        // Default values
                        $inputs['is_return'] = 0;
                        $inputs['post_dated_cheque'] = $inputs['post_dated_cheque'] ?? 0;
                        $inputs['update_post_dated_cheque'] = $inputs['update_post_dated_cheque'] ?? 0;
                        
                        $tp = TransactionPayment::create($inputs);
                        
                        // Update payment status
                        // $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);
                    }
                }

            }

            $this->addAccountTransaction($transaction, $request, $business_id, $tp);
            
            $newReview = ["created_by" => request()->session()->get('user.id'),  "description" => "Created a new expense: ".$transaction_data['ref_no'],"module" => "expense"];
            $reviewed = $this->transactionUtil->reviewChange($transaction_data['transaction_date'],$newReview);
            
            
            $accountName = Account::find($request->expense_account);
            $expense_category = ExpenseCategory::find($request->expense_category_id)->name ?? '';
            $sms_data = array(
                    'transaction_date' => $this->transactionUtil->format_date($transaction_data['transaction_date']),
                    'ref' => $transaction_data['ref_no'],
                    'amount' => $this->transactionUtil->num_f($transaction->final_total),
                    'account' => !empty($accountName) ? $accountName->name : "",
                    'staff' => auth()->user()->username,
                    'expense_category' => $expense_category,
            );
            $this->notificationUtil->sendGeneralNotification('expense_created',$sms_data);
            
            

            DB::commit();
            
            $output = [
                
                'success' => 1,

                'msg' => __('expense.expense_add_success')

            ];

            if($request->is_print == 1)
           {
               return redirect()->route('expense-print',[$transaction_id]); 
           }

        } catch (\Exception $e) {

            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());



            $output = [

                'success' => 0,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return redirect('expenses')->with('status', $output);

    }

    /**
     * Make Print of Save Expense
     *
     *  
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function print($transaction_id)
    {
        $id = $transaction_id;
        $transaction = Transaction::leftJoin('expense_categories AS ec', 'transactions.expense_category_id', '=', 'ec.id')
        ->where('transactions.id', $transaction_id)
        ->withTrashed()
        ->with(['contact', 'business', 'transaction_for'])
        ->first();

    $transaction_type = $transaction->type;

    $payments_query = TransactionPayment::where('transaction_id', $transaction_id);

    $accounts_enabled = false;

    if ($this->moduleUtil->isModuleEnabled('account')) {

        $accounts_enabled = true;

        $payments_query->with(['payment_account']);

    }

    $payments = $payments_query->get();

    $ref_nos = TransactionPayment::where('transaction_id', $transaction_id)->whereNotNull('payment_ref_no')->distinct('payment_ref_no')->pluck('payment_ref_no','payment_ref_no');

    $payment_types = $this->transactionUtil->payment_types();

    $on_account_ofs = PaymentOption::where('business_id', $transaction->business_id)->pluck('payment_option', 'id');

    $users = User::where('business_id', $transaction->business_id)->pluck('username', 'id');

    $business_id = request()->session()->get('user.business_id');
    $business = Business::where('id', $business_id)->first();

    $business_locations = BusinessLocation::where('business_id', $business_id)->first();

        
        return view('expense.invoice',compact(

            'transaction',

            'payments',

            'payment_types',

            'ref_nos',

            'id',

            'accounts_enabled',

            'users',
            
            'business',

            'business_locations',

            'on_account_ofs'

        ));
    }


    /**

     * Add Account Transactions

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function addAccountTransaction($transaction, $request, $business_id,  $tp)

    {
        // dd('addAccountTransaction');

        if (!empty($request->expense_account)) {
            $ob_transaction_data = [

                'amount' => $request->final_total,

                'account_id' => $request->expense_account,

                'type' => 'debit',

                'sub_type' => 'expense',

                'operation_date' => $transaction->transaction_date,

                'created_by' => Auth::user()->id,

                'transaction_id' => $transaction->id,

                'transaction_payment_id' => !empty($tp) ? $tp->id : null,

                'post_dated_cheque' =>  0
            ];
            AccountTransaction::createAccountTransaction($ob_transaction_data);
        }
        
        $payment = $request->payment[0];
        // dump($payment,'payment');
            $payment['amount'] = $this->transactionUtil->num_uf($payment['amount']);


            $account_payable_id = !empty($payment['controller_account']) ? $payment['controller_account'] : Account::where('business_id', $business_id)->where('name', 'Accounts Payable')->first()->id;
// dd($account_payable_id,'00');
            $ap_transaction_data = [

                'operation_date' => $transaction->transaction_date,

                'created_by' => Auth::user()->id,

                'transaction_id' => $transaction->id,

                'transaction_payment_id' => !empty($tp) ? $tp->id : null,

                'post_dated_cheque' => !empty($tp) ? $tp->post_dated_cheque : 0,
                
                'update_post_dated_cheque' => !empty($tp) ? $tp->update_post_dated_cheque : 0,
                
                'operation_date' =>  $transaction->transaction_date

            ];

            if ($payment['method'] == 'credit_expense') {
                $payment['amount'] = 0;
            }

            //if no amount paid; insert to Account Payable

            if ($payment['amount'] == 0) {

                $ap_transaction_data['amount'] = $request->final_total;

                $ap_transaction_data['account_id'] = $account_payable_id;

                $ap_transaction_data['type'] =  'credit';
                
                AccountTransaction::createAccountTransaction($ap_transaction_data);

            }
            //if partial amount paid; insert paid amount to the payment account and the balance in account payable

            else if ($payment['amount'] < $request->final_total) {

                $ap_transaction_data['amount'] = $payment['amount'];  //paid amount
                
                $ap_transaction_data['account_id'] = $tp->account_id;

                $ap_transaction_data['type'] =  'credit';
                
                AccountTransaction::createAccountTransaction($ap_transaction_data);
                
               
                $ap_transaction_data['amount'] = $request->final_total - $payment['amount']; //unpaid amount

                $ap_transaction_data['account_id'] = $account_payable_id;

                $ap_transaction_data['type'] = 'credit';

                AccountTransaction::createAccountTransaction($ap_transaction_data);

            }

// dd($payment['amount'],$request->final_total);
            // if full amount paid; insert amount in payment account
// dump('working this 000');
            if ($payment['amount'] == $request->final_total) {
// dd('working this 00009');
                $ap_transaction_data['amount'] = $payment['amount'];  
                
                $ap_transaction_data['account_id'] = $tp->account_id;

                $ap_transaction_data['type'] = 'credit';

               
                AccountTransaction::createAccountTransaction($ap_transaction_data);
                

            }
    // dd('working this');
    }



    /**

     * Add Account Transactions

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function reverseAccountTransaction($transaction, $request, $business_id)

    {
        // dd($transaction->id);
        // dd(AccountTransaction::where('transaction_id', $transaction->id)->get());
// dd($transaction, $request, $business_id);
$records = AccountTransaction::where('transaction_id', $transaction->id);

// dd($records->count()); // Shows how many rows match before deletion
// dd($records->forceDelete());
$records->forceDelete(); // Actually deletes them

        // AccountTransaction::where('transaction_id', $transaction->id)->forcedelete();

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
        // dd('12223');

        if (!auth()->user()->can('expense.update')) {

            abort(403, 'Unauthorized action.');

        }



        $business_id = request()->session()->get('user.business_id');



        //Check if subscribed or not

        if (!$this->moduleUtil->isSubscribed($business_id)) {

            return $this->moduleUtil->expiredResponse(action('ExpenseController@index'));

        }



        $business_locations = BusinessLocation::forDropdown($business_id);



        $expense_categories = ExpenseCategory::where('business_id', $business_id)

            ->pluck('name', 'id');

        $expense = Transaction::where('business_id', $business_id)

            ->where('id', $id)->with(['purchase_lines'])

            ->first();
        
        

        $users = User::forDropdown($business_id, true, true);
        $employees = EssentialsEmployee::pluck('name', 'id');

        $fleets = Fleet::where('business_id', $business_id)->pluck('vehicle_number', 'id');

        $first_location = BusinessLocation::where('business_id', $business_id)->first();

        $payment_types = $this->transactionUtil->payment_types($first_location->id, true, false, true, false, true,"is_expense_enabled");

        unset($payment_types['credit_sale']);

        $taxes = TaxRate::forBusinessDropdown($business_id, true, true);

        $account_module = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');

        $payment_line = $this->dummyPaymentLine;

        $accounts = [];

        $expense_account_type_id = AccountType::where('business_id', $business_id)->where('name', 'Expenses')->first();

        $current_account_type_id = AccountType::where('business_id', $business_id)->where('name', 'Current Assets')->first();

        $current_liability_account_type = AccountType::where('business_id', $business_id)->where('name', 'Current Liabilities')->first();

        $current_liability_account_type_id = !empty($current_liability_account_type) ? $current_liability_account_type->id : 0;



        $contacts = Contact::contactDropdown($business_id, false, false);

        $expense_accounts = [];



        if ($account_module) {

            if (!empty($expense_account_type_id)) {

                $expense_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')
                ->where('accounts.business_id', $business_id)
                ->where('account_groups.name', 'CPC')
                ->orWhere('accounts.account_type_id', $expense_account_type_id->id)
                ->select('accounts.id', 'accounts.name')->get()->pluck('name','id');

            }

        } else {

            $expense_accounts = Account::where('business_id', $business_id)->where('name', 'Expenses')->pluck('name', 'id');

        }

        $current_liabilities_accounts =  Account::where('business_id', $business_id)->where('account_type_id', $current_liability_account_type_id)->pluck('name', 'id');

        $cash_account_id = Account::getAccountByAccountName('Cash')->id;
        
        return view('expense.edit')

            ->with(compact(

                'cash_account_id',

                'expense',

                'expense_categories',

                'business_locations',

                'users',
                
                'employees',

                'fleets',

                'taxes',

                'payment_types',

                'account_module',

                'payment_line',

                'accounts',

                'current_liabilities_accounts',

                'expense_accounts',

                'contacts'

            ));

    }



    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  int  $id

     * @return \Illuminate\Http\Response
 
     */
    public function update1(Request $request, $id)

    {
        // dump($request->payment[0]['method']); 
        // dd($request->all(),$id, 'testing');
        $paymentMethod = $request->payment[0]['method'];
        $transaction_id ='1';
        // $transaction->final_total ='2';
        $this->transactionUtil->updatePaymentStatus($transaction_id, '2',$paymentMethod);
    }

    public function update_old(Request $request, $id)

    {
        dd($request->all());

        if (!auth()->user()->can('expense.update')) {

            abort(403, 'Unauthorized action.');

        }



        try {

            //Validate document size

            $request->validate([

                'document' => 'file|max:' . (config('constants.document_size_limit') / 1000)

            ]);



            $transaction_data = $request->only(['is_vat','ref_no', 'transaction_date', 'location_id', 'final_total', 'expense_for', 'additional_notes', 'expense_category_id', 'tax_id', 'contact_id', 'expense_account']);
            $transaction_data['transaction_date'] = $transaction_data['transaction_date'] ?? $request->expense_transaction_date;
            $has_reviewed = $this->transactionUtil->hasReviewed($transaction_data['transaction_date']);
        
            if(!empty($has_reviewed)){
                $output              = [
                    'success' => 0,
                    'msg'     =>__('lang_v1.review_first'),
                ];
                
                return redirect()->back()->with(['status' => $output]);
            }
        
            $reviewed = $this->transactionUtil->get_review($transaction_data['transaction_date'],$transaction_data['transaction_date']);
            
        // dd($reviewed,'$reviewed');
            if(!empty($reviewed)){
                $output = [
                    'success' => 0,
                    'msg'     =>"You can't modify an expense for an already reviewed date",
                ];
                
                return redirect('expenses')->with('status', $output);
            }
            
            
            

            $business_id = $request->session()->get('user.business_id');

        

            //Check if subscribed or not

            if (!$this->moduleUtil->isSubscribed($business_id)) {

                return $this->moduleUtil->expiredResponse(action('ExpenseController@index'));

            }



            $transaction_data['transaction_date'] = $this->transactionUtil->uf_date($transaction_data['transaction_date'], true);

            $transaction_data['final_total'] = $this->transactionUtil->num_uf(

                $transaction_data['final_total']

            );



            //upload document

            $document_name = $this->transactionUtil->uploadFile($request, 'document', 'documents');

            if (!empty($document_name)) {

                $transaction_data['document'] = $document_name;

            }



            $transaction_data['total_before_tax'] = $transaction_data['final_total'];

            if (!empty($transaction_data['tax_id'])) {

                $tax_details = TaxRate::find($transaction_data['tax_id']);

                $transaction_data['total_before_tax'] = $this->transactionUtil->calc_percentage_base($transaction_data['final_total'], $tax_details->amount);

                $transaction_data['tax_amount'] = $transaction_data['final_total'] - $transaction_data['total_before_tax'];

            }

            DB::beginTransaction();



            $transaction = Transaction::findOrFail($id);
            
            $expense = $transaction;
            
            $prevTot = $expense->final_total;
            $prevRef = $expense->ref_no;
// dump($prevTot,$prevRef);
// dd($transaction,'$business_id',$expense);

            $transaction_data['is_recurring'] = $request->has('is_recurring') ? 1 : $transaction->is_recurring;

            $transaction_data['recur_interval'] = $request->has('is_recurring') && !empty($request->input('recur_interval')) ? $request->input('recur_interval') : $transaction->recur_interval;

            $transaction_data['recur_interval_type'] = !empty($request->input('recur_interval_type')) ? $request->input('recur_interval_type') : $transaction->recur_interval_type;

            $transaction_data['recur_repetitions'] = !empty($request->input('recur_repetitions')) ? $request->input('recur_repetitions') : $transaction->recur_repetitions;

            $transaction_data['subscription_repeat_on'] = !empty($request->input('subscription_repeat_on')) ? $request->input('subscription_repeat_on') : $transaction->subscription_repeat_on;



            $transaction->update($transaction_data);
            
            
            $business_id = request()->session()->get('user.business_id');
            $business = Business::where('id', $business_id)->first();
            $sms_settings = empty($business->sms_settings) ? $this->businessUtil->defaultSmsSettings() : $business->sms_settings;
            $accountName = null;
            $msg_template = NotificationTemplate::where('business_id',$business_id)->where('template_for','expense_changed')->first();
            if(!empty($msg_template)){
                $msg = $msg_template->sms_body;
            
                $msg = str_replace('{account}',!empty($accountName) ? $accountName->name : "",$msg);
                $msg = str_replace('{amount}',$this->transactionUtil->num_f($transaction->final_total),$msg);
                $msg = str_replace('{ref}',$transaction->ref_no,$msg);
                $msg = str_replace('{staff}',auth()->user()->username,$msg);
                
                $phones = [];
                if(!empty($business->sms_settings)){
                    $phones = explode(',',str_replace(' ','',$business->sms_settings['msg_phone_nos']));
                }
                
                if(!empty($phones)){
                    $data = [
                        'sms_settings' => $sms_settings,
                        'mobile_number' => implode(',',$phones),
                        'sms_body' => $msg
                    ];
                    
                    $response = $this->businessUtil->sendSms($data,'expense_changed');
                }
            }
            



            $transaction_id =  $transaction->id;



            $inputs = $request->payment[0];
            
            
            // handle update post dated cheques logic
            if(!empty($inputs['update_post_dated_cheque'])){
                $inputs['related_account_id'] = $inputs['account_id'];
                $inputs['account_id'] = $this->transactionUtil->account_exist_return_id('Issued Post Dated Cheques'); 
            }


            $inputs['paid_on'] = $transaction->transaction_date;

            $inputs['transaction_id'] = $transaction->id;



            $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);

            $inputs['created_by'] = auth()->user()->id;

            $inputs['payment_for'] = $transaction->contact_id;

            // post dated cheque input
            $inputs['post_dated_cheque'] = $inputs['post_dated_cheque'] ?? 0;
            $inputs['update_post_dated_cheque'] = $inputs['update_post_dated_cheque'] ?? 0;

           
           
            $prefix_type = 'expense_payment';

            if ($transaction->type == 'expense') {
                $prefix_type = 'expense_payment';
            }



            $transaction->controller_account = !empty($inputs['controller_account']) ? $inputs['controller_account'] : null;

            $transaction->save();



            $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type);

            //Generate reference number

            $inputs['payment_ref_no'] = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);



            $inputs['business_id'] = $request->session()->get('business.id');

            $inputs['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');



            $inputs['is_return'] =  0; //added by ahmed

            $inputs['cheque_date'] = !empty($inputs['cheque_date']) ? $inputs['cheque_date'] : $transaction->transaction_date;

            
            unset($inputs['transaction_no_1']);

            unset($inputs['transaction_no_2']);

            unset($inputs['transaction_no_3']);

            unset($inputs['controller_account']);

            $tp = null;
            
            $this->reverseAccountTransaction($transaction, $request, $business_id); // reverse previous account transaction
            
            
            $cheque_nos = "";
            if(!empty($request->select_cheques)){
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
                                
                                $cheque_nos .= !empty($transaction_payment->cheque_number) ? $transaction_payment->cheque_number."," : "";
                                
                                $transaction_payment->is_deposited = 1;
                                $transaction_payment->save();
                            }
                        }
                    }
                }
                
                $inputs['cheque_number'] = $cheque_nos;
            }
            
            
            if ($inputs['method'] != 'credit_expense') {
                $tp = TransactionPayment::updateOrCreate(['transaction_id' => $transaction_id], $inputs);
            }


            //update payment status
            $paymentMethod = $request->payment[0]['method'];

            $this->transactionUtil->updatePaymentStatus($transaction_id, $transaction->final_total,$paymentMethod);
// dump('001');

            
          dd('1001');
            if($transaction_data['final_total'] != $prevTot){
                $newReview = ["created_by" => request()->session()->get('user.id'),  "description" => "Changed expense amount from: ".$this->transactionUtil->num_f($prevTot)." to: ".$this->transactionUtil->num_f($transaction_data['final_total'])." for expense ".$expense->ref_no,"module" => "expense"];
                $reviewed = $this->transactionUtil->reviewChange($transaction_data['transaction_date'],$newReview);
            }
            
            dd('00111');
            if($transaction_data['ref_no'] != $prevRef){
                $newReview = ["created_by" => request()->session()->get('user.id'),  "description" => "Changed expense reference no from : ".$prevRef." to: ".$transaction_data['ref_no']." for expense ".$expense->ref_no,"module" => "expense"];
                $reviewed = $this->transactionUtil->reviewChange($transaction_data['transaction_date'],$newReview);     
            }

            $this->transactionUtil->calculateAndUpdateVAT($transaction);

            DB::commit();

            $output = [

                'success' => 1,

                'msg' => __('expense.expense_update_success')

            ];
            if($request->is_print == 1)
           {
               return redirect()->route('expense-print',[$transaction_id]); 
           }
           $this->addAccountTransaction($transaction, $request, $business_id, $tp); // add new transactions

        } catch (\Exception $e) {

            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());



            $output = [

                'success' => 0,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return redirect('expenses')->with('status', $output);

    }
public function update(Request $request, $id)
{
    
    if (!auth()->user()->can('expense.update')) {
        abort(403, 'Unauthorized action.');
    }

    try {
        $request->validate([
            'document' => 'file|max:' . (config('constants.document_size_limit') / 1000)
        ]);

        $transaction_data = $request->only(['is_vat','ref_no', 'transaction_date', 'location_id', 'final_total', 'expense_for', 'additional_notes', 'expense_category_id', 'tax_id', 'contact_id', 'expense_account']);
        $transaction_data['transaction_date'] = $transaction_data['transaction_date'] ?? $request->expense_transaction_date;

        $has_reviewed = $this->transactionUtil->hasReviewed($transaction_data['transaction_date']);
        if (!empty($has_reviewed)) {
            return redirect()->back()->with([
                'status' => ['success' => 0, 'msg' => __('lang_v1.review_first')]
            ]);
        }

        $reviewed = $this->transactionUtil->get_review($transaction_data['transaction_date'], $transaction_data['transaction_date']);
        if (!empty($reviewed)) {
            return redirect('expenses')->with('status', [
                'success' => 0,
                'msg' => "You can't modify an expense for an already reviewed date"
            ]);
        }

        $business_id = $request->session()->get('user.business_id');
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse(action('ExpenseController@index'));
        }

        $transaction_data['transaction_date'] = $this->transactionUtil->uf_date($transaction_data['transaction_date'], true);
        $transaction_data['final_total'] = $this->transactionUtil->num_uf($transaction_data['final_total']);

        $document_name = $this->transactionUtil->uploadFile($request, 'document', 'documents');
        if (!empty($document_name)) {
            $transaction_data['document'] = $document_name;
        }

        $transaction_data['total_before_tax'] = $transaction_data['final_total'];
        if (!empty($transaction_data['tax_id'])) {
            $tax_details = TaxRate::find($transaction_data['tax_id']);
            $transaction_data['total_before_tax'] = $this->transactionUtil->calc_percentage_base($transaction_data['final_total'], $tax_details->amount);
            $transaction_data['tax_amount'] = $transaction_data['final_total'] - $transaction_data['total_before_tax'];
        }

        DB::beginTransaction();


        $transaction = Transaction::findOrFail($id);
        $expense = $transaction;
        $prevTot = $expense->final_total;
        $prevRef = $expense->ref_no;

        $transaction_data['is_recurring'] = $request->has('is_recurring') ? 1 : $transaction->is_recurring;
        $transaction_data['recur_interval'] = $request->has('is_recurring') && !empty($request->input('recur_interval')) ? $request->input('recur_interval') : $transaction->recur_interval;
        $transaction_data['recur_interval_type'] = !empty($request->input('recur_interval_type')) ? $request->input('recur_interval_type') : $transaction->recur_interval_type;
        $transaction_data['recur_repetitions'] = !empty($request->input('recur_repetitions')) ? $request->input('recur_repetitions') : $transaction->recur_repetitions;
        $transaction_data['subscription_repeat_on'] = !empty($request->input('subscription_repeat_on')) ? $request->input('subscription_repeat_on') : $transaction->subscription_repeat_on;

        $transaction->update($transaction_data);

        // Detect changes
        $old_payment_method = $expense->payment_lines->first()->method ?? null;
        $new_payment_method = $request->payment[0]['method'];

        $old_expense_account = $expense->expense_account;
        $new_expense_account = $transaction_data['expense_account'];

        if ($old_payment_method !== $new_payment_method && $new_payment_method === 'credit_expense') {
            $transaction->payment_status = 'due';
                $transaction->update();
        }
        $business = Business::find($business_id);
        $sms_settings = empty($business->sms_settings) ? $this->businessUtil->defaultSmsSettings() : $business->sms_settings;

        $msg_template = NotificationTemplate::where('business_id', $business_id)->where('template_for', 'expense_changed')->first();
        if (!empty($msg_template)) {
            $msg = $msg_template->sms_body;
            $msg = str_replace('{account}', '', $msg);
            $msg = str_replace('{amount}', $this->transactionUtil->num_f($transaction->final_total), $msg);
            $msg = str_replace('{ref}', $transaction->ref_no, $msg);
            $msg = str_replace('{staff}', auth()->user()->username, $msg);

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
                $this->businessUtil->sendSms($data, 'expense_changed');
            }
        }

        $transaction_id = $transaction->id;
        $inputs = $request->payment[0];
        // dd($inputs);

        if (!empty($inputs['update_post_dated_cheque'])) {
            $inputs['related_account_id'] = $inputs['account_id'];
            $inputs['account_id'] = $this->transactionUtil->account_exist_return_id('Issued Post Dated Cheques');
        }

        $inputs['paid_on'] = $transaction->transaction_date;
        $inputs['transaction_id'] = $transaction->id;
        $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);
        $inputs['created_by'] = auth()->user()->id;
        $inputs['payment_for'] = $transaction->contact_id;
        $inputs['post_dated_cheque'] = $inputs['post_dated_cheque'] ?? 0;
        $inputs['update_post_dated_cheque'] = $inputs['update_post_dated_cheque'] ?? 0;

        $transaction->controller_account = $inputs['controller_account'] ?? null;
        $transaction->save();
        // dd($transaction);

        $ref_count = $this->transactionUtil->setAndGetReferenceCount('expense_payment');
       
        $inputs['payment_ref_no'] = $this->transactionUtil->generateReferenceNumber('expense_payment', $ref_count);
        //  dd($ref_count);
        $inputs['business_id'] = $request->session()->get('business.id');
        $inputs['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');
        $inputs['is_return'] = 0;
        $inputs['cheque_date'] = $inputs['cheque_date'] ?? $transaction->transaction_date;

        unset($inputs['transaction_no_1'], $inputs['transaction_no_2'], $inputs['transaction_no_3'], $inputs['controller_account']);

        $this->reverseAccountTransaction($transaction, $request, $business_id);
// dd($test);
        // Handle cheque linking
        $cheque_nos = "";
        if (!empty($request->select_cheques)) {
            foreach ($request->select_cheques as $select_cheque) {
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
                            'operation_date' => $transaction_data['transaction_date'],
                            'created_by' => session()->get('user.id'),
                            'transaction_payment_id' => $transaction_payment->id
                        ];
                        AccountTransaction::createAccountTransaction($credit_data);
                        $cheque_nos .= $transaction_payment->cheque_number . ",";
                        $transaction_payment->is_deposited = 1;
                        $transaction_payment->save();
                    }
                }
            }
            $inputs['cheque_number'] = rtrim($cheque_nos, ',');
        }

        $tp = null;
        if ($inputs['method'] != 'credit_expense') {
            $tp = TransactionPayment::updateOrCreate(['transaction_id' => $transaction_id], $inputs);
        }

        // update payment status
        $this->transactionUtil->updatePaymentStatus($transaction_id, $transaction->final_total, $inputs['method']);

        // update note/description if expense account changed
        if ($old_expense_account != $new_expense_account) {
            $new_account_name = Account::find($new_expense_account)->name ?? '';
            $inputs['note'] = 'Expense for: ' . $new_account_name;
        }

        $this->addAccountTransaction($transaction, $request, $business_id, $tp);

        // change review notes
        if ($transaction_data['final_total'] != $prevTot) {
            $this->transactionUtil->reviewChange($transaction_data['transaction_date'], [
                "created_by" => auth()->id(),
                "description" => "Changed expense amount from: " . $this->transactionUtil->num_f($prevTot) . " to: " . $this->transactionUtil->num_f($transaction_data['final_total']) . " for expense " . $expense->ref_no,
                "module" => "expense"
            ]);
        }

        if ($transaction_data['ref_no'] != $prevRef) {
            $this->transactionUtil->reviewChange($transaction_data['transaction_date'], [
                "created_by" => auth()->id(),
                "description" => "Changed expense reference no from : $prevRef to: " . $transaction_data['ref_no'] . " for expense " . $expense->ref_no,
                "module" => "expense"
            ]);
        }

        $this->transactionUtil->calculateAndUpdateVAT($transaction);

        DB::commit();

        $output = ['success' => 1, 'msg' => __('expense.expense_update_success')];

        if ($request->is_print == 1) {
            return redirect()->route('expense-print', [$transaction_id]);
        }

    } catch (\Exception $e) {
        Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
        $output = ['success' => 0, 'msg' => __('messages.something_went_wrong')];
    }

    return redirect('expenses')->with('status', $output);
}


    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {

        if (!auth()->user()->can('expense.delete')) {

            abort(403, 'Unauthorized action.');

        }



        if (request()->ajax()) {

            try {

                $business_id = request()->session()->get('user.business_id');



                $expense = Transaction::where('business_id', $business_id)->where('id', $id)

                    ->first();
                
                $has_reviewed = $this->transactionUtil->hasReviewed($expense->transaction_date);
        
                if(!empty($has_reviewed)){
                    $output              = [
                        'success' => 0,
                        'msg'     =>__('lang_v1.review_first'),
                    ];
                    
                    return redirect()->back()->with(['status' => $output]);
                }
                
                    
                $reviewed = $this->transactionUtil->get_review($expense->transaction_date,$expense->transaction_date);
            
        
                if(!empty($reviewed)){
                    $output = [
                        'success' => 0,
                        'msg'     =>"You can't delete an expense for an already reviewed date",
                    ];
                    
                    return $output;
                }
                
                
                $changes = DB::table('reviewed_changes')
                    ->where('business_id',$business_id)
                    ->whereDate('date',date('Y-m-d',strtotime($expense->transaction_date)))
                    ->select('id')
                    ->first();
                if(!empty($changes)){
                    // dd($changes);
                    $reviewID = $changes->id;
                    $newReview = ["created_by" => request()->session()->get('user.id'),  "description" => "Deleted an expense: ".$expense->ref_no,"module" => "expense"];
                    
                    DB::table('reviewed_changes_description')->insert($newReview);
    
                    
                }else{
                    
                    $reviewID = DB::table('reviewed_changes')->insertGetId([
                        'business_id' => $business_id,
                        'date' => $expense->transaction_date
                    ]);
                    
                    
                    $newReview = ["created_by" => request()->session()->get('user.id'),  "description" => "Deleted an expense: ".$expense->ref_no,"module" => "expense"];
                    
                    DB::table('reviewed_changes_description')->insert($newReview);
    
                    
                }
                $transaction = $expense;
                $contact = Contact::find($transaction->contact_id);
                
                if(!empty($contact)){
                    $transaction->contact = $contact;
                    $this->notificationUtil->autoSendNotification($transaction->business_id,'supplier_expense_deleted' , $transaction, $transaction->contact,true);
                
                }
                
                
                $business_id = request()->session()->get('user.business_id');
                $business = Business::where('id', $business_id)->first();
                $sms_settings = empty($business->sms_settings) ? $this->businessUtil->defaultSmsSettings() : $business->sms_settings;
                $accountName = null;
                $msg_template = NotificationTemplate::where('business_id',$business_id)->where('template_for','expense_deleted')->first();
                
                if(!empty($msg_template)){
                    $msg = $msg_template->sms_body;
                
                    $msg = str_replace('{account}',!empty($accountName) ? $accountName->name : "",$msg);
                    $msg = str_replace('{amount}',$this->transactionUtil->num_f($expense->final_total),$msg);
                    $msg = str_replace('{ref}',$expense->ref_no,$msg);
                    $msg = str_replace('{staff}',auth()->user()->username,$msg);
                    
                    $phones = [];
                    if(!empty($business->sms_settings)){
                        $phones = explode(',',str_replace(' ','',$business->sms_settings['msg_phone_nos']));
                    }
                    
                    if(!empty($phones)){
                        $data = [
                            'sms_settings' => $sms_settings,
                            'mobile_number' => implode(',',$phones),
                            'sms_body' => $msg
                        ];
                        
                        $response = $this->businessUtil->sendSms($data,'expense_delete');
                    }
                }
                
                $expense->deleted_by = auth()->user()->id;
                $expense->save();
                

                $expense->delete();



                //Delete account transactions

                AccountTransaction::where('transaction_id', $expense->id)->delete();

                ContactLedger::where('transaction_id', $expense->id)->delete();



                $output = [

                    'success' => true,

                    'msg' => __("expense.expense_delete_success")

                ];

            } catch (\Exception $e) {

                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());



                $output = [

                    'success' => false,

                    'msg' => __("messages.something_went_wrong")

                ];

            }



            return $output;

        }

    }



    public function getPaymentMethodByLocationDropDown($location_id)

    {

        $payment_methods = $this->transactionUtil->payment_types($location_id, true, true, false, false, true,"is_expense_enabled");

        unset($payment_methods['credit_sale']);



        return $this->transactionUtil->createDropdownHtml($payment_methods, 'Please Select');

    }

}

