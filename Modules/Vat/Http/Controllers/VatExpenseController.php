<?php

namespace Modules\Vat\Http\Controllers;

use Modules\Vat\Entities\VatContact;

use App\Business;
use App\Account;


use App\BusinessLocation;
use Modules\Superadmin\Entities\Package;
use Modules\Superadmin\Entities\Subscription;

use Modules\Vat\Entities\VatExpenseCategory;
use Modules\Vat\Entities\VatExpensePayment;
use Modules\Vat\Entities\VatExpense;

use App\TaxRate;

use App\User;

use App\Utils\ModuleUtil;

use App\Utils\NotificationUtil;

use App\Utils\TransactionUtil;

use App\Utils\BusinessUtil;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Log;

use Yajra\DataTables\Facades\DataTables;
use App\Providers\AppServiceProvider;
use App\NotificationTemplate;
use Illuminate\Routing\Controller;

class VatExpenseController extends Controller

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
        
        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {

            $expenses = VatExpense::leftJoin('vat_expense_categories AS ec', 'vat_expenses.expense_category_id', '=', 'ec.id')
                ->leftjoin(
                    'business_locations AS bl',
                    'vat_expenses.location_id',
                    '=',

                    'bl.id'

                )
                ->leftjoin('vat_contacts', 'vat_contacts.id', '=', 'vat_expenses.contact_id')

                ->leftJoin('tax_rates as tr', 'vat_expenses.tax_id', '=', 'tr.id')

                ->leftJoin('users AS m', 'vat_expenses.created_by', '=', 'm.id')

                ->leftjoin('vat_expense_payments AS TP', function ($join) {

                    $join->on('vat_expenses.id', 'TP.transaction_id')->where('TP.amount', '!=', 0);

                })
                
                ->where('vat_expenses.business_id', $business_id)
                
                ->select(

                    'vat_expenses.id',

                    'transaction_date',

                    'ref_no',

                    'vat_contacts.name as payee_name',

                    'ec.name as category',

                    'payment_status',

                    'final_total',

                    'bl.name as location_name',

                    'TP.method',

                    'TP.cheque_date',

                    'TP.cheque_number',

                    'TP.account_id',
                    
                    'vat_expenses.business_id',
                    
                    DB::raw("CONCAT(tr.name ,' (', tr.amount ,' )') as tax"),

                    DB::raw('SUM(TP.amount) as amount_paid'),

                    DB::raw("CONCAT(COALESCE(m.surname, ''),' ',COALESCE(m.first_name, ''),' ',COALESCE(m.last_name,'')) as created_by")

                )

                ->groupBy('vat_expenses.id');



           

            //Add condition for location,used in sales representative expense report & list of expense

            if (request()->has('location_id') && !empty(request()->get('location_id'))) {

                $location_id = request()->get('location_id');

                if (!empty($location_id)) {

                    $expenses->where('vat_expenses.location_id', $location_id);

                }

            }



            //Add condition for expense category, used in list of expense,

            if (request()->has('expense_category_id') && !empty(request()->get('expense_category_id'))) {

                $expense_category_id = request()->get('expense_category_id');

                if (!empty($expense_category_id)) {

                    $expenses->where('vat_expenses.expense_category_id', $expense_category_id);

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

                    $expenses->where('vat_expenses.expense_category_id', $expense_category_id);

                }

            }

            //Add condition for payment methods

            if (request()->has('method') && !empty(request()->get('method'))) {

                $method = request()->get('method');

                if (!empty($method)) {

                    $expenses->where('TP.method', $method);

                }

            }

            


            $permitted_locations = auth()->user()->permitted_locations();

            if ($permitted_locations != 'all') {

                $expenses->whereIn('vat_expenses.location_id', $permitted_locations);

            }



            //Add condition for payment status for the list of expense

            if (request()->has('payment_status') && !empty(request()->get('payment_status'))) {

                $payment_status = request()->get('payment_status');

                if (!empty($payment_status)) {

                    $expenses->where('vat_expenses.payment_status', $payment_status);

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
                    

                    <li><a href="{{action(\'\Modules\Vat\Http\Controllers\VatExpenseController@edit\', [$id])}}"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a></li>



                    <li><a data-href="{{action(\'\Modules\Vat\Http\Controllers\VatExpenseController@destroy\', [$id])}}" class="delete_expense"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</a></li>

                    </ul></div>'

                )

                ->removeColumn('id')

                ->editColumn(

                    'final_total',

                    '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="{{empty($deletedBy) ? $final_total : 0}}">{{$final_total}}</span>'

                )

                ->editColumn(

                    'for_sum_total',

                    '{{empty($deletedBy) ? $final_total : 0}}'

                )

                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')

                ->editColumn('ref_no', function ($row) {

                    $ref = $row->ref_no;

                    return $ref;

                })
                ->editColumn('location_name', function ($row) {


                    return '<td class="clickable_td sorting_1">'.$row->location_name.'</td>';


                })
                ->editColumn('payment_status', function ($row) {
                    return '<a href="#" class="payment-status no-print" data-orig-value="'.$row->payment_status.'" data-status-name="'.__('lang_v1.'.$row->payment_status).'"><span class="label '.$this->__payment_status($row->payment_status).'">'.__('lang_v1.' . $row->payment_status).'</span></a><span class="print_section">'.__('lang_v1.' . $row->payment_status).'</span>';
                })

                ->addColumn('payment_due', function ($row) {

                    $due = ($row->final_total - $row->amount_paid );

                    return '<span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="' . $due . '">' . $due . '</span>';

                })

                ->addColumn('payment_method', function ($row) {

                    $html = '';
                    if ($row->payment_status == 'due') {
                        return 'Credit Expense';
                    }
                    
                    
                    if (strtolower($row->method) == 'bank_transfer' || strtolower($row->method) == 'direct_bank_deposit' || strtolower($row->method) == 'bank') {
                        $html .= "Bank";
                        
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



        $business_id = request()->session()->get('user.business_id');



        $categories = VatExpenseCategory::where('business_id', $business_id)

            ->pluck('name', 'id');



        $users = User::forDropdown($business_id, false, true, true);
        
        
        $business_locations = BusinessLocation::forDropdown($business_id, true);



        return view('vat::expense.index')

            ->with(compact('categories', 'business_locations', 'users'));

    }
    
   
    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {
        $business_id = request()->session()->get('user.business_id');

        $payment_line = $this->dummyPaymentLine;

        $first_location = BusinessLocation::where('business_id', $business_id)->first();

        $payment_types = $this->transactionUtil->payment_types($first_location->id, true, false, true, false, true,"is_expense_enabled");



        unset($payment_types['credit_sale']);


        $business_locations = BusinessLocation::forDropdown($business_id);

        $contacts = VatContact::contactDropdown($business_id, false, false);

        $expense_categories = VatExpenseCategory::where('business_id', $business_id)

            ->pluck('name', 'id');

        $users = User::forDropdown($business_id, true, true);

        $taxes = TaxRate::forBusinessDropdown($business_id, true, true);

        $recent = VatExpense::where('business_id',$business_id)->get()->last();
        if(!empty($recent)){
            $po = explode('-',$recent->ref_no) ?? [];
            if(!empty($po) && sizeof($po) > 0){
                $no = "EP-".((int) $po[1] + 1);
            }else{
                $no = "EP-1";
            }
        }else{
            $no = "EP-1";
        }
        
        $ref_no = $no;


        return view('vat::expense.create')

            ->with(compact(
                
                'ref_no',

                'payment_types',

                'payment_line',

                'expense_categories',

                'business_locations',

                'users',
                
                'taxes',

                'contacts'

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

        
        try {

            $business_id = $request->session()->get('user.business_id');


            $transaction_data = $request->only(['is_vat', 'ref_no', 'transaction_date', 'location_id', 'final_total',  'expense_category_id', 'tax_id', 'contact_id','additional_notes']);


            $user_id = $request->session()->get('user.id');

            $transaction_data['business_id'] = $business_id;

            $transaction_data['created_by'] = $user_id;

            $transaction_data['payment_status'] = 'due';

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



            DB::beginTransaction();

            $transaction = VatExpense::create($transaction_data);
            
            $amt_paid = 0;
            foreach($request->payment as $payment){
                
                if($payment['method'] != 'credit_expense'){
                    $amt_paid += $this->transactionUtil->num_uf($payment['amount']);
                }

                $payment_data = [
                    'transaction_id' => $transaction->id,
                    'account_id' => $payment['account_id'],
                    'business_id' => $business_id,
                    'amount' => $this->transactionUtil->num_uf($payment['amount']),
                    'method' => $payment['method'],
                    'card_transaction_number' => $payment['card_transaction_number'],
                    'cheque_number' => $payment['cheque_number'],
                    'cheque_date' => $payment['cheque_date'],
                    'bank_name' => $payment['bank_name'],
                    'paid_on' => $transaction_data['transaction_date'],
                    'created_by' => auth()->user()->id,
                    'payment_for' => $request->contact_id,
                    'note' => $payment['note']
                ];
                
                VatExpensePayment::create($payment_data);
            }
            
            if($amt_paid >= $transaction->final_total){
                 $transaction->payment_status = 'paid';
            }elseif($amt_paid > 0 && $amt_paid < $transaction->final_total){
                 $transaction->payment_status = 'partial';
            }else{
                $transaction->payment_status = 'due';
            }
            
            $transaction->save();
            
            

            DB::commit();

            $output = [

                'success' => 1,

                'msg' => __('expense.expense_add_success')

            ];

        } catch (\Exception $e) {

            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());



            $output = [

                'success' => 0,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return redirect('vat-module/vat-expense')->with('status', $output);

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

        

        $business_id = request()->session()->get('user.business_id');


        $business_locations = BusinessLocation::forDropdown($business_id);



        $expense_categories = VatExpenseCategory::where('business_id', $business_id)

            ->pluck('name', 'id');

        $expense = VatExpense::where('business_id', $business_id)

            ->where('id', $id)

            ->first();
        $expense->payment_lines = VatExpensePayment::where('transaction_id',$id)->get();

        $users = User::forDropdown($business_id, true, true);

        $first_location = BusinessLocation::where('business_id', $business_id)->first();

        $payment_types = $this->transactionUtil->payment_types($first_location->id, true, false, true, false, true,"is_expense_enabled");

        unset($payment_types['credit_sale']);

        $taxes = TaxRate::forBusinessDropdown($business_id, true, true);

        
        $payment_line = $this->dummyPaymentLine;


        $contacts = VatContact::contactDropdown($business_id, false, false);


        return view('vat::expense.edit')

            ->with(compact(

                
                'expense',

                'expense_categories',

                'business_locations',

                'users',
                
                'taxes',

                'payment_types',

                'payment_line',

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

    public function update(Request $request, $id)

    {

       


        try {

            $transaction_data = $request->only(['is_vat', 'ref_no', 'transaction_date', 'location_id', 'final_total',  'expense_category_id', 'tax_id', 'contact_id','additional_notes']);

           
            $business_id = $request->session()->get('user.business_id');



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

            DB::beginTransaction();



            $transaction = VatExpense::findOrFail($id);
            
            $transaction->update($transaction_data);
            VatExpensePayment::where('transaction_id',$id)->delete();
            
            $amt_paid = 0;
            foreach($request->payment as $payment){
                
                if($payment['method'] != 'credit_expense'){
                    $amt_paid += $this->transactionUtil->num_uf($payment['amount']);
                }

                $payment_data = [
                    'transaction_id' => $transaction->id,
                    'account_id' => $payment['account_id'],
                    'business_id' => $business_id,
                    'amount' => $this->transactionUtil->num_uf($payment['amount']),
                    'method' => $payment['method'],
                    'card_transaction_number' => $payment['card_transaction_number'],
                    'cheque_number' => $payment['cheque_number'],
                    'cheque_date' => $payment['cheque_date'],
                    'bank_name' => $payment['bank_name'],
                    'paid_on' => $transaction_data['transaction_date'],
                    'created_by' => auth()->user()->id,
                    'payment_for' => $request->contact_id,
                    'note' => $payment['note']
                ];
                
                VatExpensePayment::create($payment_data);
            }
            
            if($amt_paid >= $transaction->final_total){
                 $transaction->payment_status = 'paid';
            }elseif($amt_paid > 0 && $amt_paid < $transaction->final_total){
                 $transaction->payment_status = 'partial';
            }else{
                $transaction->payment_status = 'due';
            }
            

            DB::commit();

            $output = [

                'success' => 1,

                'msg' => __('expense.expense_update_success')

            ];

        } catch (\Exception $e) {

            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());



            $output = [

                'success' => 0,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return redirect('vat-module/vat-expense')->with('status', $output);

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {

        

        if (request()->ajax()) {

            try {

                $business_id = request()->session()->get('user.business_id');

                    VatExpense::where('id',$id)->delete();
                    VatExpensePayment::where('transaction_id',$id)->delete();

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

