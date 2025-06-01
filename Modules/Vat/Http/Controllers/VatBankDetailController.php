<?php



namespace Modules\Vat\Http\Controllers;



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

use Modules\Vat\Entities\VatBankDetail;

class VatBankDetailController extends Controller

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


    public function index()

    {

        $business_id = request()->session()->get('user.business_id');


        if (request()->ajax()) {

                $query = VatBankDetail::leftjoin('users', 'vat_bank_details.user_id', 'users.id')

                    ->where('vat_bank_details.business_id', $business_id)

                    ->select([

                        'vat_bank_details.*',

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
    
                            $html .= '<li><a href="#" data-href="'.action([\Modules\Vat\Http\Controllers\VatBankDetailController::class, 'edit'], [$row->id]).'" class="btn-modal" data-container=".fuel_tank_modal"><i class="glyphicon glyphicon-edit"></i> '.__('messages.edit').'</a></li>';
                            $html .= '<li><a href="#" data-href="'.action([\Modules\Vat\Http\Controllers\VatBankDetailController::class, 'destroy'], [$row->id]).'" class="delete_task" ><i class="fa fa-trash"></i> '.__('messages.delete').'</a></li>';
    
                            $html .= '</ul></div>';
    
                            return $html;
                        }
                    )
                    
                    ->editColumn('created_at', '{{@format_datetime($created_at)}}')
                    ->editColumn('status', function ($row) {
                        if($row->status == 1){
                            $html = "<span class='badge bg-success'>".__('vat::lang.active')."</span>";
                        }else{
                            $html = "<span class='badge bg-danger'>".__('vat::lang.inactive')."</span>";
                        }
                        
                        return $html;
                    })

                    ->removeColumn('id');



                return $fuel_tanks->rawColumns(['action','status'])

                    ->make(true);

            }
            
            return view('vat::customized_invoices.index');


    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        $business_id = request()->session()->get('business.id');

        return view('vat::vat_bank_details.create')->with(compact('business_id'));

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
            $data['user_id'] = auth()->user()->id;
            $data['business_id'] = $business_id;
            
            
            VatBankDetail::create($data);
            
            
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

        $data = VatBankDetail::findOrFail($id);

        return view('vat::vat_bank_details.edit')->with(compact('data'));
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
            $data = $request->only('bank_name','bank_branch','account_number','account_name','special_instructions','status');
            $data['user_id'] = auth()->user()->id;
            $data['business_id'] = $business_id;
            
            VatBankDetail::where('id', $id)
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
                
                VatBankDetail::where('id', $id)->delete();

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

