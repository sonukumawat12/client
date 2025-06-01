<?php

namespace Modules\Property\Http\Controllers;
use App\Account;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\AccountType;

class AjaxController extends Controller
{
    public function credit_sub_account_type_ajax(Request $request){
        if (!auth()->user()->can('property.settings.tax')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
           
            $accounts = Account::where('account_type_id',$request->value)->pluck('name', 'id');
            return view('property::setting.payment_options.credit_sub_account_type_ajax')
                ->with(compact('accounts'));
        }
    }
    
    public function getCreditSubAccountType(Request $request){
           if (!auth()->user()->can('property.settings.tax')) {
            abort(403, 'Unauthorized action.');
            }
            
            if(request()->ajax()){
                $business_id = request()->session()->get('user.business_id');
                $creditSubAccountTypes = AccountType::where('business_id', $business_id) ->where('parent_account_type_id',$request->value)->pluck('name', 'id');
                return view('property::setting.payment_options.credit_sub_account_options_ajax')
                ->with(compact('creditSubAccountTypes'));
            }
        
        
    }
    public function paymentOptionChange(Request $request){
        
         if (!auth()->user()->can('property.settings.tax')) {
            abort(403, 'Unauthorized action.');
            }
            
            if(request()->ajax()){
                $business_id = request()->session()->get('user.business_id');
                $credit_account_type = AccountType::where('business_id', $business_id)->when($request->value == "true", function ($q) {
                        return $q->where("name","Liabilities");
                    }, function ($q) {
                        return $q->where("name","Income");
                    })->select('id','name')->first();
                return $credit_account_type;
            }
        
    }

}