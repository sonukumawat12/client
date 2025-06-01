<?php

namespace App\Http\Controllers;

use App\Account;
use App\User;
use App\AccountType;
use App\ContactGroup;
use App\SellingPriceGroup;
use App\UserContactAccess;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Modules\Superadmin\Entities\HelpExplanation;
use Yajra\DataTables\Facades\DataTables;


class ContactGroupController extends Controller
{
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function FetchAccount(Request $request)
    {
        $data['accounts'] = Account::where("account_type_id", $request->type_id)->get(["name", "id"]);
        return response()->json($data);
    }

    public function index()
    {
        if (!auth()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $type = request()->type;
            $groups = ContactGroup::leftJoin("accounts", "accounts.id", "=", "contact_groups.interest_account_id")
                ->leftJoin('account_types', 'account_types.id', '=', 'contact_groups.account_type_id')
                ->leftjoin('users','users.id','contact_groups.created_by')
                ->where('contact_groups.business_id', $business_id)
                ->where(function ($query) use ($type) {
                    $query->where('contact_groups.type', $type)
                        ->orWhere('contact_groups.type', 'both');
                })
                ->select(['contact_groups.name', 'contact_groups.amount','contact_groups.maximum_discount','contact_groups.last_maximum_discount', 'contact_groups.id', 'account_types.name as ATName', 'accounts.name as AName','users.username']);

            $dt =  Datatables::of($groups)
                ->addColumn(
                    'action',
                    '
                        @can("customer.update")
                        <button data-href="{{action(\'ContactGroupController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_contact_group_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                        @endcan
                        
                        @if($name != "Own Company")
                        @can("customer.delete")
                            <button data-href="{{action(\'ContactGroupController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_contact_group_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                        @endcan
                        @endif
                        '
                )
                ->editColumn('maximum_discount','{{@num_format($maximum_discount)}}')
                ->editColumn('last_maximum_discount','{{@num_format($last_maximum_discount)}}')
                ->removeColumn('id')
                ->rawColumns([7]);
               
                
                if($type == 'supplier'){
                    $dt->removeColumn('maximum_discount')->removeColumn('last_maximum_discount')->removeColumn('username') ->rawColumns([4]);
                }
                return $dt->make(false);
        }
        $contact_customer = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'contact_customer');
        $contact_supplier = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'contact_supplier');

        return view('contact_group.index')->with(compact(
            'contact_customer',
            'contact_supplier'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }
        $help_explanations = HelpExplanation::pluck('value', 'help_key');
        $type = request()->type;
        $allAccounts = Account::where('account_type_id', 3)->get();
        // if ($type == "customer") {
        // } else {
        //     $allAccounts = Account::where('account_type_id', 4)->get();
        // }
        $allAccountsType = AccountType::all();
        $business_id = request()->session()->get('user.business_id');
        $price_groups = SellingPriceGroup::forDropdown($business_id, false);
        $user_groups = User::forDropdown($business_id);
        $types = array('customer' => 'Customer', 'supplier' => 'Supplier', 'both' => 'Both (Customer & Supplier');
        return view('contact_group.create')->with(compact('types','help_explanations', 'price_groups', 'type', 'allAccounts', 'allAccountsType','user_groups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['name', 'amount', 'account_type_id', 'interest_account_id','maximum_discount']);
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['type'] = $request->type;
            // $input['price_type'] = $request->price_calculation_type;
            $input['supplier_group_id'] = $request->selling_price_group_id;
            $input['created_by'] = $request->session()->get('user.id');
            $input['amount'] = !empty($input['amount']) ? $this->commonUtil->num_uf($input['amount']) : 0;
            $input['maximum_discount'] = !empty($input['maximum_discount']) ? $this->commonUtil->num_uf($input['maximum_discount']) : 0;


            $contact_group = ContactGroup::create($input);
            // if($request->assigned_to){
            //     $obj = new UserContactAccess();
            //     $obj->contact_id = $contact_group->id;
            //     $obj->user_id = $request->assigned_to;
            //     $obj->save();
            // }
           
            $output = [
                'success' => true,
                'data' => $contact_group,
                'msg' => __("lang_v1.success")
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\ContactGroup $ContactGroup
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('customer.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $contact_group = ContactGroup::findOrFail($id);
            $type = $contact_group->type;
            $allAccounts = Account::where('account_type_id', $contact_group->account_type_id)->get();
            $allAccountsType = AccountType::all();
            
            $selectedSupGroupId = $contact_group->supplier_group_id;
            $price_groups = SellingPriceGroup::forDropdown($business_id, false);
            $user_groups = User::forDropdown($business_id);
            $types = array('customer' => 'Customer', 'supplier' => 'Supplier', 'both' => 'Both (Customer & Supplier');
            return view('contact_group.edit')
                ->with(compact('contact_group','types', 'type', 'selectedSupGroupId', 'price_groups', 'allAccounts', 'allAccountsType','user_groups'));
        }
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
        if (!auth()->user()->can('customer.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['name', 'amount', 'account_type_id', 'interest_account_id','maximum_discount']);
                $business_id = $request->session()->get('user.business_id');
                $input['type'] = $request->type;
                $input['amount'] = !empty($input['amount']) ? $this->commonUtil->num_uf($input['amount']) : 0;
                $input['maximum_discount'] = !empty($input['maximum_discount']) ? $this->commonUtil->num_uf($input['maximum_discount']) : 0;
                
                $contact_group = ContactGroup::where('business_id', $business_id)->findOrFail($id);

                $contact_group->name = $input['name'];
                $contact_group->last_maximum_discount =$contact_group->maximum_discount;
                // $contact_group->price_type = $request->price_calculation_type;
                $contact_group->supplier_group_id = $request->selling_price_group_id;
                $contact_group->amount = $input['amount'];
                $contact_group->account_type_id = $input['account_type_id'];
                $contact_group->interest_account_id = $input['interest_account_id'];
                $contact_group->maximum_discount = $input['maximum_discount'];
                $contact_group->save();
                
                // if( $request->assigned_to ){
                //     $user_contact_access = UserContactAccess::updateOrCreate(
                //                 ['contact_id' => $id],
                //                 ['user_id' => $request->assigned_to, 'contact_id' => $id]
                //             );

                // }else{

                //     UserContactAccess::where('contact_id',$id)->delete();
                // }

                $output = [
                    'success' => true,
                    'msg' => __("lang_v1.success")
                ];
            }catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }
                

        } 
            return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('customer.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->user()->business_id;

                $cg = ContactGroup::where('business_id', $business_id)->findOrFail($id);
                $cg->delete();

                $output = [
                    'success' => true,
                    'msg' => __("lang_v1.success")
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
    
    public function show($id, Request $request) {
        if ($id == 'search') {
            return $this->search($request);
        }
    }

    public function search(Request $request)
    {
        $business_id = $request->business_id;
        $search = $request->q;
        $type = $request->type;

        $contact_groups = ContactGroup::select('id', 'name')
            ->where('business_id', $business_id)
            ->where('type', $type)
            ->where(function ($query) use ($type) {
                $query->where('contact_groups.type', $type)
                ->orWhere('contact_groups.type', 'both');
            })
            ->where('name', 'like', '%' . $search . '%')
            ->get();

        return [
            'success' => true,
            'data' => $contact_groups
        ];
    }
}
