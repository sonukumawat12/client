<?php



namespace Modules\Vat\Http\Controllers;



use App\Business;

use App\AccountType;

use App\Account;

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
use App\Contact;
use App\ContactGroup;

use Illuminate\Support\Facades\Log;

use Modules\Vat\Entities\VatCreditBill;

class VatCreditBillController extends Controller

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
     
    
    public function index()

    {

        $business_id = request()->session()->get('user.business_id');


        if (request()->ajax()) {

                $query = VatCreditBill::leftjoin('users', 'vat_credit_bills.created_by', 'users.id')
                    ->leftJoin('contacts','contacts.id','vat_credit_bills.customer_id')
                    ->leftJoin('contact_groups','contact_groups.id','vat_credit_bills.customer_group')
                    ->where('vat_credit_bills.business_id', $business_id)
                    ->select([

                        'vat_credit_bills.*',

                        'users.username as user_created',
                        'contacts.name as customer_name',
                        'contact_groups.name as cg_name'

                    ]);

                

                $fuel_tanks = Datatables::of($query)
                    ->addColumn(
                        'action',
                        function ($row) {
                            $html = '<div class="btn-group">
                                <button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">'.__('messages.actions').'<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">';
    
                            $html .= '<li><a href="#" data-href="'.action([\Modules\Vat\Http\Controllers\VatCreditBillController::class, 'edit'], [$row->id]).'" class="btn-modal" data-container=".fuel_tank_modal"><i class="glyphicon glyphicon-edit"></i> '.__('messages.edit').'</a></li>';
                            $html .= '<li><a href="#" data-href="'.action([\Modules\Vat\Http\Controllers\VatCreditBillController::class, 'destroy'], [$row->id]).'" class="delete_task" ><i class="fa fa-trash"></i> '.__('messages.delete').'</a></li>';
    
                            $html .= '</ul></div>';
    
                            return $html;
                        }
                    )
                    ->editColumn('created_at','{{@format_datetime($created_at)}}')
                    ->editColumn('linked_accounts','{{ucfirst($linked_accounts)}}')
                    
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
        $customers = Contact::customersDropdown($business_id, false);
        $customer_group = ContactGroup::forDropdown($business_id);
        
        return view('vat::vat_credit_bill.create')->with(compact('business_id','customer_group','customers'));

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
            $data['created_by'] = auth()->user()->id;
            $data['business_id'] = $business_id;
            
            
            VatCreditBill::create($data);
            
            
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
        
        $customers = Contact::customersDropdown($business_id, false);
        $customer_group = ContactGroup::forDropdown($business_id);
        

        $data = VatCreditBill::findOrFail($id);

        return view('vat::vat_credit_bill.edit')->with(compact('data','customer_group','customers'));
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
            $data = $request->only('customer_id','customer_group','linked_accounts');
            $data['created_by'] = auth()->user()->id;
            $data['business_id'] = $business_id;
            
            VatCreditBill::where('id', $id)
                            ->update($data);

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
                
                VatCreditBill::where('id', $id)->delete();

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

