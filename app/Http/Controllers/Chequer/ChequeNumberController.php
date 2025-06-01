<?php



namespace App\Http\Controllers\Chequer;



use App\Account;

use App\Contact;

use App\DefaultSettings;

use App\Utils\ModuleUtil;

use Illuminate\Http\Request;

use App\Chequer\ChequeNumber;

use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\Log;

use App\Chequer\PrintedChequeDetail;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Chequer\ChequerDefaultSetting;



class ChequeNumberController extends Controller

{

    protected $moduleUtil;



    /**

     * Constructor

     *

     * @param ProductUtils $product

     * @return void

     */

    public function __construct(ModuleUtil $moduleUtil)

    {

        $this->moduleUtil = $moduleUtil;

    }



    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        $defaultVal = [];
    
        if (request()->ajax()) {
            // Check if subscribed or not
            if (!$this->moduleUtil->isSubscribed(request()->session()->get('business.id'))) {
                return $this->moduleUtil->expiredResponse();
            }
    
            $cheque_number = ChequeNumber::leftJoin('accounts', 'cheque_numbers.account_no', 'accounts.id')
                ->leftJoin('users', 'cheque_numbers.user_id', 'users.id')
                ->where('accounts.business_id', $business_id)
                ->select('cheque_numbers.*', 'users.username', 'accounts.name');
    
            // Apply filters
            if (request()->has('bank_account_no') && !empty(request()->bank_account_no)) {
                $cheque_number->where('cheque_numbers.account_no', request()->bank_account_no);
            }
    
            if (request()->has('cheque_no') && !empty(request()->cheque_no)) {
                $cheque_number->where('cheque_numbers.id', request()->cheque_no);
            }
    
            if (request()->has('date_range') && !empty(request()->date_range) && request()->date_range !== 'All') {
                $dates = explode(' - ', request()->date_range);
                $start_date = date('Y-m-d', strtotime($dates[0]));
                $end_date = date('Y-m-d', strtotime($dates[1]));
                $cheque_number->whereBetween('cheque_numbers.date_time', [$start_date, $end_date]);
            }
    
            return Datatables::of($cheque_number)
                ->addColumn('action', function ($row) {
                    return '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            <li><a href="' . action('Chequer\ChequeNumberController@edit', [$row->id]) . '"><i class="glyphicon glyphicon-edit"></i> Edit</a></li>
                        </ul></div>';
                            // <li><a data-href="' . action('Chequer\ChequeNumberController@destroy', [$row->id]) . '" class="delete_employee"><i class="glyphicon glyphicon-trash"></i> Delete</a></li>
                })
                ->removeColumn('created_at')
                ->removeColumn('updated_at')
                ->rawColumns(['action']) 
                ->make(true);
        }
    
        $accounts = Account::where('business_id', $business_id)->where('is_need_cheque', 'Y')->notClosed()->pluck('name', 'id');
        $chequeNumbers = PrintedChequeDetail::join('cheque_numbers', 'printed_cheque_details.cheque_no', '=', 'cheque_numbers.id')
            ->where('printed_cheque_details.business_id', $business_id)
            ->groupBy('printed_cheque_details.cheque_no')
            ->pluck('printed_cheque_details.cheque_no', 'printed_cheque_details.cheque_no');
    
        return view('chequer.cheque_number.index')->with(compact('accounts', 'chequeNumbers', 'defaultVal'));
    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {
        $business_id = request()->session()->get('business.id'); 
        $no_of_cheque_leaves = 0; 
        $check_book_number = 0;
        $accounts = Account::where('business_id', $business_id)->where('is_need_cheque', 'Y')->notClosed()->pluck('name', 'id');
        $checkbook = ChequeNumber::where('business_id', $business_id)->latest()->first();
        $checkbook_first = ChequeNumber::where('business_id', $business_id)->first();
        if($checkbook){
            $check_book_number =  $checkbook->reference_no + 1;
            //(Last Cheque Number - First Cheque Number) + 1
            $no_of_cheque_leaves =($checkbook->reference_no - $checkbook_first->reference_no) + 1;
        }
        else
        {    
        
            $settings = DefaultSettings::where('business_id',$business_id)->first();
            $check_book_number = ($settings && $settings->def_autostart_chbk_no)?$settings->def_autostart_chbk_no:1;
        }    
        return view('chequer/cheque_number/create')->with(compact('accounts','check_book_number','no_of_cheque_leaves'));

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

            $business_id = $request->session()->get('business.id');

            $data = array(

                'date_time' => $request->date_time,

                'reference_no' => $request->reference_no,

                'business_id' => $business_id,

                'account_no' => $request->account_number,

                'first_cheque_no' => $request->first_cheque_no,

                'last_cheque_no' => $request->last_cheque_no,

                'no_of_cheque_leaves' => $request->no_of_cheque_leaves,

                'user_id' => Auth::user()->id

            );

            

            ChequeNumber::create($data);

            $output = [

                'success' => 1,

                'msg' => __('cheque.cheque_number_add_succuss')

            ];

            

        } catch (\Exception $e) {

            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [

                'success' => 0,

                'msg' => __('messages.something_went_wrong')

            ];

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

        return 'asldkfj';

    }



    /**

     * Show the form for editing the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

 public function edit($id)
{
    $business_id = request()->session()->get('business.id');
    $cheque = ChequeNumber::where('business_id', $business_id)->where('id', $id)->firstOrFail();
    $accounts = Account::where('business_id', $business_id)->where('is_need_cheque', 'Y')->notClosed()->pluck('name', 'id');

    return view('chequer/cheque_number/edit', compact('cheque', 'accounts'));
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
        $business_id = $request->session()->get('business.id');
        $cheque = ChequeNumber::where('business_id', $business_id)->where('id', $id)->firstOrFail();

        $cheque->update([
            'date_time' => $request->date_time,
            'reference_no' => $request->reference_no,
            'account_no' => $request->account_number,
            'first_cheque_no' => $request->first_cheque_no,
            'last_cheque_no' => $request->last_cheque_no,
            'no_of_cheque_leaves' => $request->no_of_cheque_leaves,
            'user_id' => Auth::user()->id
        ]);

        return redirect()->route('cheque-numbers.index')->with('success', __('cheque.cheque_number_update_success'));
    } catch (\Exception $e) {
        Log::error("Update Error: " . $e->getMessage());

        return redirect()->back()->with('error', __('messages.something_went_wrong'));
    }
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

    public function printedcheque(Request $request)

    {

        $defaultVal=null;

        if($request){

            $defaultVal=array();

            $defaultVal['bank_acount_no'] = $request->bank_acount_no;

            $defaultVal['cheque_no'] = $request->cheque_no;

            $defaultVal['payment_status'] = $request->payment_status;

            $defaultVal['payee_no'] = $request->payee_no;

            $defaultVal['startDate'] = date('m/01/Y');

            $defaultVal['endDate'] = date("m/t/Y");

            if($request->date_range){

                $dates = explode(' - ', $request->date_range);

                $defaultVal['startDate'] = $dates[0];

                $defaultVal['endDate'] = $dates[1];

            }

        } 

        $business_id = request()->session()->get('business.id');
        $getvoucher = PrintedChequeDetail::where('business_id', $business_id)->orderBy('id', 'desc')->get();
        $get_defultvalu = ChequerDefaultSetting::where('business_id', $business_id)->get();

        $bankAcounts = Account::where('accounts.business_id', $business_id)
        ->where('is_need_cheque', 'Y')
        ->leftJoin('account_groups', 'accounts.asset_type', '=', 'account_groups.id')
        ->select('accounts.name AS account_name', 'account_groups.name AS group_name', 'accounts.id')
        ->pluck('account_name', 'id'); 


        $payeeList = Contact::where('business_id', $business_id)->where('type', 'supplier')->pluck('name','id');

        $chequeNumbers = PrintedChequeDetail::groupBy('cheque_no')->pluck('cheque_no','cheque_no');

        $paymentStatus = array('Full Payment'=>'Full Payment','Partial Payment'=>'Partial Payment','Last Payment'=>'Last Payment');

        // \DB::connection()->enableQueryLog();

        $printedcheque = PrintedChequeDetail::where('printed_cheque_details.business_id', $business_id)

                                            //->where('printed_cheque_details.status','!=','Cancelled' )

                                             ->leftjoin('users', 'printed_cheque_details.user_id', 'users.id')

                                             ->leftjoin('chequer_bank_accounts', 'printed_cheque_details.bank_account_no', 'chequer_bank_accounts.id')

                                             ->leftjoin('contacts', 'printed_cheque_details.payee', 'contacts.id')

                                            ->leftjoin('transaction_payments', 'printed_cheque_details.cheque_no', 'transaction_payments.cheque_number')
                                             ->leftjoin('transactions', 'transaction_payments.transaction_id', 'transactions.id');

        if($request->bank_acount_no && $request->bank_acount_no!="")

            $printedcheque = $printedcheque->where('printed_cheque_details.bank_account_no', $request->bank_acount_no);

        if($request->payment_status && $request->payment_status!="")

            $printedcheque = $printedcheque->where('printed_cheque_details.supplier_paid_amount',$request->payment_status);

        if($request->payee_no && $request->payee_no!="")

            $printedcheque = $printedcheque->where('printed_cheque_details.payee',$request->payee_no);

        if($request->cheque_no && $request->cheque_no!="")

            $printedcheque = $printedcheque->where('printed_cheque_details.cheque_no',$request->cheque_no);

        if($request->date_range){

            $printedcheque = $printedcheque->where('printed_cheque_details.cheque_date','>=',date('Y-m-d',strtotime($defaultVal['startDate'])));

            $printedcheque = $printedcheque->where('printed_cheque_details.cheque_date','<=',date('Y-m-d',strtotime($defaultVal['endDate'])));

        }


        $printedcheque = $printedcheque->select('printed_cheque_details.*', 'chequer_bank_accounts.bank', 'chequer_bank_accounts.account_number', 'chequer_bank_accounts.branch','users.username','transactions.type','transactions.ref_no','transactions.invoice_no', 'transactions.payment_status','contacts.name')

                                                ->orderBy('printed_cheque_details.id','DESC')

                                                ->get();

        // $queries = \DB::getQueryLog();

        // print_r($queries);

        return view('chequer/printedcheque/index')->with(compact('printedcheque','bankAcounts','payeeList','chequeNumbers','paymentStatus','defaultVal', 'getvoucher', 'get_defultvalu'));

    }

}

