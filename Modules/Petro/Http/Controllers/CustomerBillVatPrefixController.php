<?php



namespace Modules\Petro\Http\Controllers;



use App\Business;

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

use Modules\Petro\Entities\CustomerBillVatPrefix;

class CustomerBillVatPrefixController extends Controller

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


        
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module')) {
            
            abort(403, 'Unauthorized Access');
            
        }



        if (request()->ajax()) {

                $query = CustomerBillVatPrefix::leftjoin('users', 'customer_bill_vat_prefixes.created_by', 'users.id')

                    ->where('customer_bill_vat_prefixes.business_id', $business_id)

                    ->select([

                        'customer_bill_vat_prefixes.*',

                        'users.username as user_created'

                    ]);

                

                $fuel_tanks = Datatables::of($query)
                    ->addColumn(
                        'action',
                        function ($row) {
                            $html = '<div class="btn-group">
                                <button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">'.__('messages.actions').'<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">';
    
                            $html .= '<li><a href="#" data-href="'.action([\Modules\Petro\Http\Controllers\CustomerBillVatPrefixController::class, 'edit'], [$row->id]).'" class="btn-modal" data-container=".fuel_tank_modal"><i class="glyphicon glyphicon-edit"></i> '.__('messages.edit').'</a></li>';
                            $html .= '<li><a href="#" data-href="'.action([\Modules\Petro\Http\Controllers\CustomerBillVatPrefixController::class, 'destroy'], [$row->id]).'" class="delete_task" ><i class="fa fa-trash"></i> '.__('messages.delete').'</a></li>';
    
                            $html .= '</ul></div>';
    
                            return $html;
                        }
                    )

                    ->removeColumn('id');



                return $fuel_tanks->rawColumns(['action'])

                    ->make(true);

            }


        
        return view('petro::customer_bill_vat_prefixes.index');

    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        $business_id = request()->session()->get('business.id');

        return view('petro::customer_bill_vat_prefixes.create')->with(compact('business_id'));

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
            
            
            CustomerBillVatPrefix::create($data);
            
            
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

        $data = CustomerBillVatPrefix::findOrFail($id);

        return view('petro::customer_bill_vat_prefixes.edit')->with(compact('data'));
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
            $data = $request->only('starting_no','prefix');
            $data['created_by'] = auth()->user()->id;
            $data['business_id'] = $business_id;
            
            CustomerBillVatPrefix::where('id', $id)
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
                
                CustomerBillVatPrefix::where('id', $id)->delete();

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

