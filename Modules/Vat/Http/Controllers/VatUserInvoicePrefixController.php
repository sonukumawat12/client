<?php



namespace Modules\Vat\Http\Controllers;



use App\Business;

use App\User;

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

use Modules\Vat\Entities\VatUserInvoicePrefix;
use Modules\Vat\Entities\VatPrefix;
use Modules\Vat\Entities\VatInvoice2Prefix;


class VatUserInvoicePrefixController extends Controller

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

                $query = VatUserInvoicePrefix::leftjoin('users as uc', 'vat_user_invoice_prefixes.created_by', 'uc.id')
                    ->leftjoin('users', 'vat_user_invoice_prefixes.user_id', 'users.id')
                    ->leftjoin('vat_prefixes as vp', 'vat_user_invoice_prefixes.prefix_id', 'vp.id')
                    ->leftjoin('vat_invoice2_prefixes as vp2', 'vat_user_invoice_prefixes.prefix_id2', 'vp2.id')
                    ->leftjoin('business_locations as bl', 'vat_user_invoice_prefixes.location_id', 'bl.id')
                    ->where('vat_user_invoice_prefixes.business_id', $business_id)
                    ->select([
                        'vat_user_invoice_prefixes.*',
                        'vp.prefix as prefix_name',
                        'vp2.prefix as prefix_name2',
                        'uc.username as user_created',
                        'users.username as username',
                        'bl.name as location_name'

                    ]);

                

                $fuel_tanks = Datatables::of($query)
                    ->addColumn(
                        'action',
                        function ($row) {
                            $html = '<div class="btn-group">
                                <button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">'.__('messages.actions').'<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">';
    
                            $html .= '<li><a href="#" data-href="'.action([\Modules\Vat\Http\Controllers\VatUserInvoicePrefixController::class, 'edit'], [$row->id]).'" class="btn-modal" data-container=".fuel_tank_modal"><i class="glyphicon glyphicon-edit"></i> '.__('messages.edit').'</a></li>';
                            $html .= '<li><a href="#" data-href="'.action([\Modules\Vat\Http\Controllers\VatUserInvoicePrefixController::class, 'destroy'], [$row->id]).'" class="delete_task" ><i class="fa fa-trash"></i> '.__('messages.delete').'</a></li>';
    
                            $html .= '</ul></div>';
    
                            return $html;
                        }
                    )
                    
                    ->editColumn('date_time','{{@format_datetime($date_time)}}')

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
        $business_locations = BusinessLocation::forDropdown($business_id,false);
        $prefixes = VatPrefix::where('business_id',$business_id)->pluck('prefix','id');
        $prefixes2 = VatInvoice2Prefix::where('business_id',$business_id)->pluck('prefix','id');
        
        $users = User::where('business_id', $business_id)
                // ->where('id', '!=', auth()->user()->id)
                ->where('is_cmmsn_agnt', 0)
                ->where('is_customer', 0)->select('id',DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"))->pluck('full_name','id');

        return view('vat::vat_userinvoice_prefixes.create')->with(compact('business_id','users','business_locations','prefixes','prefixes2'));

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
            
            
            VatUserInvoicePrefix::create($data);
            
            
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
        
        $business_locations = BusinessLocation::forDropdown($business_id,false);
        $prefixes = VatPrefix::where('business_id',$business_id)->pluck('prefix','id');
        $prefixes2 = VatInvoice2Prefix::where('business_id',$business_id)->pluck('prefix','id');
        
        $users = User::where('business_id', $business_id)
                // ->where('id', '!=', auth()->user()->id)
                ->where('is_cmmsn_agnt', 0)
                ->where('is_customer', 0)->select('id',DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"))->pluck('full_name','id');


        $data = VatUserInvoicePrefix::findOrFail($id);

        return view('vat::vat_userinvoice_prefixes.edit')->with(compact('data','users','business_locations','prefixes','prefixes2'));
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
            $data = $request->only('user_id','prefix_id','date_time','location_id','prefix_id2');
            $data['created_by'] = auth()->user()->id;
            $data['business_id'] = $business_id;
            
            VatUserInvoicePrefix::where('id', $id)
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
                
                VatUserInvoicePrefix::where('id', $id)->delete();

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

