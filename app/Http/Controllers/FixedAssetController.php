<?php

namespace App\Http\Controllers;

use App\Account;
use App\Contact;
use App\AccountTransaction;
use App\Transaction;
use App\AccountType;
use App\ContactLedger;
use App\Business;
use App\BusinessLocation;
use App\Http\Requests\JournalRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Journal;
use App\System;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\Superadmin\Entities\ModulePermissionLocation;
use App\FixedAsset;
use App\User;

class FixedAssetController extends Controller
{
    protected $moduleUtil;
    protected $transactionUtil;

    public function __construct(ModuleUtil $moduleUtil, TransactionUtil $transactionUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        $account_access = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');
        if (request()->ajax()) {

            //Check if subscribed or not, then check for location quota
            if (!$this->moduleUtil->isSubscribed(request()->session()->get('business.id'))) {
                return $this->moduleUtil->expiredResponse();
            }
            $journal = FixedAsset::leftjoin('accounts', 'fixed_assets.account_id', 'accounts.id')
                ->where('fixed_assets.business_id', $business_id)
                ->select(
                    'fixed_assets.*',
                    'accounts.name as account_name',
                    'accounts.account_number as account_no'
                );


            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $journal->whereDate('date_of_operation', '>=', $start)
                    ->whereDate('date_of_operation', '<=', $end);
            }
            
            
            
            if (!empty(request()->asset_name)) {
                $journal->where('fixed_assets.asset_name', request()->asset_name);
            }
            
            if (!empty(request()->created_by)) {
                $journal->where('fixed_assets.created_by', request()->created_by);
            }
            
            if (!empty(request()->location_id)) {
                $journal->where('fixed_assets.asset_location', request()->location_id);
            }

            
            return Datatables::of($journal)
                ->addColumn('action', function ($row) {

                    $html = '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                    <li><a href="' . action('FixedAssetController@edit', [$row->id]) . '" class="journal_edit"><i class="glyphicon glyphicon-edit"></i> Edit</a></li>
                    
                    <li><a data-href="' . action('FixedAssetController@destroy', [$row->id]) . '" class="delete_journal"><i class="glyphicon glyphicon-trash" style="color:brown; cursor: pointer;"></i> Delete</a></li>
                    ';

                    $html .=  '</ul></div>';
                    return $html;
                })
                ->editColumn('amount', '@if(!empty($amount)){{@num_format($amount)}}@endif')
                ->editColumn('date_of_operation', '{{\Carbon::parse($date_of_operation)->format("Y-m-d H:i")}}')
                ->rawColumns(['action'])
                ->make(true);
        }
        
        $locations = FixedAsset::where('business_id',$business_id)->distinct('asset_location')->select('asset_location')->pluck('asset_location','asset_location');
        $names = FixedAsset::where('business_id',$business_id)->distinct('asset_location')->select('asset_name')->pluck('asset_name','asset_name');
        $accounts = Account::where('business_id', $business_id)->notClosed()->whereNull('default_account_id')->pluck('name', 'id');
        $users = User::where('business_id',$business_id)->select('username','id')->pluck('username','id');


        return view('fixed_assets.index')->with(compact('users','locations', 'account_access', 'accounts','names'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $business_id = request()->session()->get('business.id');
        $accounts = Account::leftjoin('account_types','account_types.id','accounts.account_type_id')->where('accounts.business_id', $business_id)->where('account_types.name','Fixed Assets')->select('accounts.name','accounts.id')->pluck('name', 'id');
        

        return view('fixed_assets.create')->with(compact('accounts'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        

        try {
            
            $data = $request->except('_token');
            $business_id = request()->session()->get('business.id');
            $data['business_id'] = $business_id;
            $data['created_by'] = Auth::user()->id;
            
            $fixed_asset = FixedAsset::create($data);
            
            
            $account_transaction_data = [
                'amount' => $fixed_asset->amount,
                'account_id' => $fixed_asset->account_id,
                'type' => 'debit',
                'operation_date' => date('Y-m-d H:i:s'),
                'fixed_asset_id' => $fixed_asset->id,
                'created_by' => Auth::user()->id,
            ];
            
            
            AccountTransaction::createAccountTransaction($account_transaction_data);
            
            $account_transaction_data['type'] = 'credit';
            $account_transaction_data['account_id'] = $this->transactionUtil->account_exist_return_id('Opening Balance Equity Account');
            
            AccountTransaction::createAccountTransaction($account_transaction_data);

            $output = [
                'success' => 1,
                'msg' => __('messages.success')
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
        $business_id = request()->session()->get('business.id');
        $accounts = Account::leftjoin('account_types','account_types.id','accounts.account_type_id')->where('accounts.business_id', $business_id)->where('account_types.name','Fixed Assets')->select('accounts.name','accounts.id')->pluck('name', 'id');
        
        $fixed_asset = FixedAsset::findOrFail($id);

        return view('fixed_assets.edit')->with(compact('accounts','fixed_asset'));
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
        $business_id = request()->session()->get('business.id');
        

        try {
            
            $fixed_asset = FixedAsset::findOrFail($id);
            AccountTransaction::where('transaction_id',$fixed_asset->transaction_id)->delete();
            Transaction::where('id',$fixed_asset->transaction_id)->delete();
            
            
            $data = $request->except('_token','_method');
            
            FixedAsset::where('id',$id)->update($data);
            
            $fixed_asset = FixedAsset::findOrFail($id);
            
            
            $account_transaction_data = [
                'amount' => $fixed_asset->amount,
                'account_id' => $fixed_asset->account_id,
                'type' => 'debit',
                'operation_date' => date('Y-m-d H:i:s'),
                'fixed_asset_id' => $fixed_asset->id,
                'created_by' => Auth::user()->id,
            ];
            
            
            AccountTransaction::createAccountTransaction($account_transaction_data);
            
            $account_transaction_data['type'] = 'credit';
            $account_transaction_data['account_id'] = $this->transactionUtil->account_exist_return_id('Opening Balance Equity Account');
            
            AccountTransaction::createAccountTransaction($account_transaction_data);

            $output = [
                'success' => 1,
                'msg' => __('messages.success')
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {

            $journal = FixedAsset::findOrFail($id);
            AccountTransaction::where('transaction_id',$journal->transaction_id)->delete();
            Transaction::where('id',$journal->transaction_id)->delete();
            $journal->delete();
            
            
            $output = [
                'success' => 1,
                'msg' => __('messages.success')
            ];
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
     * Get row for journals enteries
     *
     * 
     * @return \Illuminate\Http\Response
     */
    
}
