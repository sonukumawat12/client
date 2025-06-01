<?php



namespace Modules\Vat\Http\Controllers;



use App\Business;

use App\AccountType;

use App\Account;

use App\Transaction;

use App\AccountTransaction;

use App\BusinessLocation;

use Illuminate\Http\Request;

use Illuminate\Routing\Controller;

use App\Utils\ModuleUtil;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

use Yajra\DataTables\Facades\DataTables;

use App\Utils\ProductUtil;

use App\Utils\TransactionUtil;

;

use Illuminate\Support\Facades\Log;

use Modules\Vat\Entities\VatPayableToAccount;

class VatPayableToAccountController extends Controller

{

    /**

     * All Utils instance.

     *

     */

    protected $productUtil;

    protected $transactionUtil;

    protected $moduleUtil;



    /**

     * Constructor

     *

     * @param ProductUtils $product

     * @return void

     */

    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, ModuleUtil $moduleUtil)

    {

        $this->productUtil = $productUtil;

        $this->transactionUtil = $transactionUtil;

        $this->moduleUtil = $moduleUtil;

    }





    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */
     
    public function getTankProduct(){
        
    }

    public function index()

    {

        $business_id = request()->session()->get('user.business_id');


        if (request()->ajax()) {

                $query = VatPayableToAccount::leftjoin('users', 'vat_payable_to_accounts.created_by', 'users.id')
                    ->leftJoin('accounts','accounts.id','vat_payable_to_accounts.account_id')

                    ->where('vat_payable_to_accounts.business_id', $business_id)

                    ->select([

                        'vat_payable_to_accounts.*',

                        'users.username as user_created',
                        'accounts.name as account_name'

                    ]);

                

                $fuel_tanks = Datatables::of($query)
                    ->addColumn(
                        'action',
                        function ($row) {
                            $html = '<div class="btn-group">
                                <button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">'.__('messages.actions').'<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">';
    
                            $html .= '<li><a href="#" data-href="'.action([\Modules\Vat\Http\Controllers\VatPayableToAccountController::class, 'edit'], [$row->id]).'" class="btn-modal" data-container=".fuel_tank_modal"><i class="glyphicon glyphicon-edit"></i> '.__('messages.edit').'</a></li>';
                            $html .= '<li><a href="#" data-href="'.action([\Modules\Vat\Http\Controllers\VatPayableToAccountController::class, 'destroy'], [$row->id]).'" class="delete_task" ><i class="fa fa-trash"></i> '.__('messages.delete').'</a></li>';
    
                            $html .= '</ul></div>';
    
                            return $html;
                        }
                    )
                    ->editColumn('amount','{{@num_format($amount)}}')
                    ->editColumn('type','{{__("vat::lang.".$type)}}')
                    ->editColumn('created_at','{{@format_datetime($created_at)}}')
                    
                    ->removeColumn('id');



                return $fuel_tanks->rawColumns(['action'])

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
        $business_id = request()->session()->get('business.id');
        $current_liabilities = AccountType::where('business_id', $business_id)->where('name', 'Current Liabilities')->first();
        $accounts = Account::where('business_id', $business_id)->where('account_type_id', $current_liabilities->id)->pluck('name', 'id');
        
        $current_assets = AccountType::where('business_id', $business_id)->where('name', 'Current Assets')->first();
        $asset_accounts = Account::where('business_id', $business_id)->where('account_type_id', $current_assets->id)->pluck('name', 'id');
        
        return view('vat::vat_payable_accounts.create')->with(compact('business_id','accounts','asset_accounts'));

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

            $business_id = request()->session()->get('business.id');

            
            DB::beginTransaction();
            
            $data = $request->except('_token');
            
            if($request->type == 'vat_payable_account'){
                unset($data['rec_account_id']);
                $type = "credit";
                $eqt_type= "debit";
            }else{
                $data['account_id'] = $request->rec_account_id;
                unset($data['rec_account_id']);
                
                $type = "debit";
                $eqt_type= "credit";
            }
            
            $data['created_by'] = auth()->user()->id;
            $data['business_id'] = $business_id;
            
            
            $vat = VatPayableToAccount::create($data);
            
            
            $business_locations = BusinessLocation::forDropdown($business_id);
            $default_location = current(array_keys($business_locations->toArray()));
            
            $ob_data = [
                            'business_id' => $business_id,
                            'location_id' => $default_location,
                            'type' => 'vat_opening_balance',
                            'status' => 'final',
                            'payment_status' => 'due',
                            'transaction_date' => date('Y-m-d'),
                            'total_before_tax' => $request->amount,
                            'final_total' => $request->amount,
                            'created_by' => request()->session()->get('user.id')
                        ];
            $transaction = Transaction::create($ob_data);
            
            $vat->transaction_id = $transaction->id;
            $vat->save();
            
            $account_transaction_data = [
                        'amount' =>  $request->amount,
                        'account_id' => $data['account_id'],
                        'type' => $type,
                        'sub_type' => null,
                        'operation_date' => $transaction->transaction_date,
                        'created_by' => $transaction->created_by,
                        'transaction_id' =>  $transaction->id,
                        'note' => $data['note']
                    ];
            AccountTransaction::createAccountTransaction($account_transaction_data);
            
            $account_transaction_data['account_id'] = $this->transactionUtil->account_exist_return_id('Opening Balance Equity Account');;
            $account_transaction_data['type'] = $eqt_type;
            
            AccountTransaction::createAccountTransaction($account_transaction_data);
            
            
            DB::commit();

            $output = [

                'success' => true,

                'msg' => __('messages.success')

            ];

        } catch (\Exception $e) {

            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => false,

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
        
        $current_liabilities = AccountType::where('business_id', $business_id)->where('name', 'Current Liabilities')->first();
        $accounts = Account::where('business_id', $business_id)->where('account_type_id', $current_liabilities->id)->pluck('name', 'id');
        

        $data = VatPayableToAccount::findOrFail($id);
        
        $current_assets = AccountType::where('business_id', $business_id)->where('name', 'Current Assets')->first();
        $asset_accounts = Account::where('business_id', $business_id)->where('account_type_id', $current_assets->id)->pluck('name', 'id');

        return view('vat::vat_payable_accounts.edit')->with(compact('data','accounts','asset_accounts'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $business_id = $request->session()->get('user.business_id');
        
        
        try {
            $data = $request->except('_token','_method');
            $data['created_by'] = auth()->user()->id;
            $data['business_id'] = $business_id;
            
            if($request->type == 'vat_payable_account'){
                unset($data['rec_account_id']);
                $type = "credit";
                $eqt_type= "debit";
            }else{
                $data['account_id'] = $request->rec_account_id;
                unset($data['rec_account_id']);
                
                $type = "debit";
                $eqt_type= "credit";
            }
            
            VatPayableToAccount::where('id', $id)
                            ->update($data);
                            
            $vat = VatPayableToAccount::findOrFail($id);
            $transaction = Transaction::find($vat->transaction_id);
            AccountTransaction::where('transaction_id',$vat->transaction_id)->forceDelete();
            
            $business_locations = BusinessLocation::forDropdown($business_id);
            $default_location = current(array_keys($business_locations->toArray()));
            
            if(empty($transaction)){
                $ob_data = [
                            'business_id' => $business_id,
                            'location_id' => $default_location,
                            'type' => 'vat_opening_balance',
                            'status' => 'final',
                            'payment_status' => 'due',
                            'transaction_date' => date('Y-m-d'),
                            'total_before_tax' => $request->amount,
                            'final_total' => $request->amount,
                            'created_by' => request()->session()->get('user.id')
                        ];
                        
                $transaction = Transaction::create($ob_data);
                
                $vat->transaction_id = $transaction->id;
                $vat->save();
                
            }else{
                $transaction->total_before_tax = $request->amount;
                $transaction->final_total = $request->amount;
                $transaction->save();
            }            
            
            
            $account_transaction_data = [
                        'amount' =>  $request->amount,
                        'account_id' => $data['account_id'],
                        'type' => $type,
                        'sub_type' => null,
                        'operation_date' => $transaction->transaction_date,
                        'created_by' => $transaction->created_by,
                        'transaction_id' =>  $transaction->id,
                        'note' => $data['note']
                    ];
            AccountTransaction::createAccountTransaction($account_transaction_data);
            
            $account_transaction_data['account_id'] = $this->transactionUtil->account_exist_return_id('Opening Balance Equity Account');
            $account_transaction_data['type'] = $eqt_type;
            
            AccountTransaction::createAccountTransaction($account_transaction_data);
            

            $output = ['success' => true,
                'msg' => __('lang_v1.updated_success'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return redirect()->back()->with('status', $output);
    }


    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {
        $business_id = request()->session()->get('user.business_id');
        

        if (request()->ajax()) {
            try {
                
                $vat = VatPayableToAccount::findOrFail($id);
                VatPayableToAccount::where('id', $id)->delete();
                Transaction::where('id',$vat->transaction_id)->forceDelete();
                AccountTransaction::where('transaction_id',$vat->transaction_id)->forceDelete();

                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

}

