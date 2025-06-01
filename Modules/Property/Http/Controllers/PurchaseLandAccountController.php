<?php

namespace Modules\Property\Http\Controllers;

use App\Account;
use App\AccountType;
use App\BusinessLocation;
use App\TransactionPayment;
use App\Utils\Util;
;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Property\Entities\PurchaseLandAccount;
use Modules\Property\Entities\PropertyTax;
use Yajra\DataTables\Facades\DataTables;

class PurchaseLandAccountController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil)
    {
        $this->commonUtil = $commonUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('property.settings.tax')) {
            abort(403, 'Unauthorized action.');
        }



        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');


            // $peroperty_taxes2 = PurchaseLandAccount::where('business_id', $business_id)->where('payment_option', 'Non-Refundable Advance')->get()->toArray();
            // if(!count($peroperty_taxes2) > 0){
            //     $PaymentOption = ['Non-Refundable Advance', 'Advance Payment', 'Agreement Charges', 'Stamp Fees', 'Notary Fees', 'Penalty Amount' ];

            //     foreach($PaymentOption as $option){
            //         $input['payment_option'] = $option;
            //         $input['credit_account_type'] = '';
            //         $input['credit_sub_account_type'] = '';
            //         $input['credit_account'] = '';


            //         $input['business_id'] = $business_id;
            //         $input['location_id'] = request()->session()->get('user.business_id');
            //         $input['created_by'] = request()->session()->get('user.id');
            //         $input['date'] = date('Y-m-d');

            //         PurchaseLandAccount::create($input);
            //     }

            // }

            $peroperty_taxes = PurchaseLandAccount::leftjoin('business_locations', 'purchase_land_accounts.location_id', 'business_locations.id')
                ->leftjoin('users', 'purchase_land_accounts.created_by', 'users.id')
                ->leftjoin('accounts', 'purchase_land_accounts.credit_account', 'accounts.id')
                ->where('purchase_land_accounts.business_id', $business_id)
                ->select([
                    'purchase_land_accounts.*',
                    'business_locations.name as location_name',
                    'users.username as created_by',
                    'accounts.name as credit_account',
                ]);
            logger($peroperty_taxes->get()->toArray());
            
            return DataTables::of($peroperty_taxes)
                ->addColumn(
                    'action',
                    '@if($is_default == 0)
                        <button data-href="{{action(\'\Modules\Property\Http\Controllers\PaymentOptionController@edit\', [$id])}}" data-container=".view_modal" class="btn btn-xs btn-modal btn-primary edit_payment_option_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                             &nbsp;
        
                         @if (in_array($payment_option,  []))
        
                            <button data-href="#" class="btn btn-xs btn-danger delete_payment_option_button" disabled ><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                            @else
                            <button data-href="{{action(\'\Modules\Property\Http\Controllers\PaymentOptionController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_payment_option_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                         @endif
                     @endif
                    '
                )
                ->editColumn('date', '{{@format_date($date)}}')
                ->editColumn('payment_only', '{{ $payment_only ? "Yes" : "No" }}')
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);

                /* return DataTables::of($peroperty_taxes)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'\Modules\Property\Http\Controllers\PaymentOptionController@edit\', [$id])}}" data-container=".view_modal" class="btn btn-xs btn-modal btn-primary edit_payment_option_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                     &nbsp;
                    <button data-href="{{action(\'\Modules\Property\Http\Controllers\PaymentOptionController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_payment_option_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    '
                )
                ->editColumn('date', '{{@format_date($date)}}')
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);*/


        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('property.settings.tax')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $quick_add = false;
        if (!empty(request()->input('quick_add'))) {
            $quick_add = true;
        }

        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        // $credit_account_type = AccountType::where('business_id', $business_id) ->whereNull('parent_account_type_id')->pluck('name', 'id');
        $default_credit_account_type = AccountType::with('sub_types')->where('business_id', $business_id) ->whereName('Income')->select('name', 'id')->first();
        
        $credit_account_type = AccountType::where('business_id', $business_id) ->select('name', 'id')->whereNull('parent_account_type_id') ->pluck('name', 'id');
        // $credit_sub_account_type = AccountType::where('business_id', $business_id) ->whereNotNull('parent_account_type_id')->pluck('name', 'id');
        if($default_credit_account_type && $default_credit_account_type->sub_types->count() > 0 ){
            $credit_sub_account_type = AccountType::where('business_id', $business_id) ->where('parent_account_type_id',$default_credit_account_type->id)->pluck('name', 'id');
        }
        else
        {
            foreach($credit_account_type as $id=>$name){
            $credit_sub_account_type = AccountType::where('business_id', $business_id)->where('parent_account_type_id',$id)->pluck('name', 'id');
            $default_credit_account_type =  AccountType::where('id',$id)->where('business_id', $business_id)->select('name', 'id')->first();
            break;
            }    
        } 
        
        if($credit_sub_account_type && $credit_sub_account_type->count()>0){
             foreach($credit_sub_account_type as $id=>$name){
                $accounts = Account::where('business_id', $business_id)->where('account_type_id',$id)->pluck('name', 'id');
             }
        }else{
            $accounts = Account::where('business_id', $business_id)->where('account_type_id',$credit_sub_account_type->first()->id)->pluck('name', 'id');
        }       
       return view('property::setting.purchase_land_account.create')
            ->with(compact('quick_add', 'business_locations', 'credit_account_type', 'credit_sub_account_type', 'accounts', "default_credit_account_type"));
    }
    
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('property.settings.tax')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['payment_option', 'credit_account_type', 'credit_sub_account_type', 'credit_account', 'payment_only']);
            $input['payment_only'] = $request->payment_only ? $request->payment_only : false;
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['location_id'] = $request->session()->get('user.business_id');
            $input['created_by'] = $request->session()->get('user.id');
            $input['date'] = !empty($request->date) ? \Carbon::parse($request->date)->format('Y-m-d') : date('Y-m-d');

            PurchaseLandAccount::create($input);
            $output = [
                'success' => true,
                'tab' => 'purchase_land_accounts',
                'msg' => __("property::lang.payment_option_added_success")
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'tab' => 'purchase_land_accounts',
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return $output;
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
        if (!auth()->user()->can('property.settings.tax')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $payment_option = PurchaseLandAccount::where('business_id', $business_id)->find($id);

            $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
            ####TASK:2 (Sakhawat Kamran) ######  
            $default_cridit_account_type_id = $payment_option->credit_account_type;
            $credit_sub_account_type_id = $payment_option->credit_sub_account_type;
            $default_credit_account_type = AccountType::with('sub_types')->where('business_id', $business_id) ->where('id',$default_cridit_account_type_id)->select('name', 'id')->first();
            $credit_account_type = AccountType::where('business_id', $business_id) ->select('name', 'id')->whereNull('parent_account_type_id') ->pluck('name', 'id');
            if($default_credit_account_type && $default_credit_account_type->sub_types->count() > 0 ){
                $credit_sub_account_type = AccountType::where('business_id', $business_id) ->where('parent_account_type_id',$default_credit_account_type->id)->pluck('name', 'id');
            }
            else
            {
                foreach($credit_account_type as $id=>$name){
                $credit_sub_account_type = AccountType::where('business_id', $business_id)->where('parent_account_type_id',$id)->pluck('name', 'id');
                break;
                }    
            } 
            
            
           $accounts = Account::where('business_id', $business_id)->where('account_type_id',$credit_sub_account_type_id)->pluck('name', 'id');
          
            
            ######ENDOFTASK1 ###########
            // $credit_account_type = AccountType::where('business_id', $business_id) ->whereNull('parent_account_type_id')->pluck('name', 'id');
            // $credit_sub_account_type = AccountType::where('business_id', $business_id) ->whereNotNull('parent_account_type_id')->pluck('name', 'id');
            // $credit_account_type = AccountType::where('business_id', $business_id) ->whereName('Income')->select('name', 'id')->first();
             //$credit_sub_account_type = AccountType::where('business_id', $business_id) ->where('parent_account_type_id',$credit_account_type->id)->pluck('name', 'id');
            
            //$accounts = Account::where('id', $payment_option->credit_account)->pluck('name', 'id');

            return view('property::setting.purchase_land_account.edit')
                ->with(compact('payment_option', 'business_locations', 'credit_account_type', 'credit_sub_account_type', 'accounts'));
        }
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
        if (!auth()->user()->can('property.settings.tax')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {

                $input = $request->only(['payment_option', 'credit_account_type', 'credit_sub_account_type', 'credit_account', 'payment_only']);
                $input['payment_only'] = $request->payment_only ? $request->payment_only : false;
                $input['created_by'] = $request->session()->get('user.id');
                $input['date'] = !empty($request->date) ? \Carbon::parse($request->date)->format('Y-m-d') : date('Y-m-d');

                PurchaseLandAccount::where('id', $id)->update($input);

                $output = [
                    'success' => true,
                    'tab' => 'purchase_land_accounts',
                    'msg' => __("property::lang.payment_option_updated_success")
                ];
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'tab' => 'purchase_land_accounts',
                    'msg' => __("messages.something_went_wrong")
                ];
            }
        }
        return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('property.settings.tax')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $transactions = TransactionPayment::where('payment_option_id', $id)->first();
                if (!empty($transactions)) {
                    $output = [
                        'success' => false,
                        'msg' => __("property::lang.transaction_exit_deleted_error")
                    ];
                    return $output;
                }
                PurchaseLandAccount::findOrFail($id)->delete();

                $output = [
                    'success' => true,
                    'msg' => __("property::lang.payment_option_deleted_success")
                ];
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => '__("messages.something_went_wrong")'
                ];
            }

            return $output;
        }
    }
}
