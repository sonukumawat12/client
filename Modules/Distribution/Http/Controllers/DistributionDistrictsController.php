<?php

namespace Modules\Distribution\Http\Controllers;

use App\Account;
use App\AccountType;
use App\BusinessLocation;
use App\ExpenseCategory;
use App\Transaction;
use App\OpeningBalance;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Modules\Superadmin\Entities\Subscription;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Modules\Distribution\Entities\Distribution_districts;
use Modules\Distribution\Entities\Distribution_provinces;
use App\Contact;
use Yajra\DataTables\Facades\DataTables;

class DistributionDistrictsController extends Controller
{
    protected $commonUtil;
    protected $moduleUtil;
    protected $productUtil;
    protected $transactionUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil =  $moduleUtil;
        $this->productUtil =  $productUtil;
        $this->transactionUtil =  $transactionUtil;
    }


    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        // if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'fleet_module')) {
        //     abort(403, 'Unauthorized action.');
        // }
        if (request()->ajax()) {
            $fleets = Distribution_districts::leftjoin('users', 'distribution_districts.added_by', 'users.id')
                ->leftjoin('distribution_provinces', 'distribution_districts.province_id', 'distribution_provinces.id')
                ->where('distribution_provinces.business_id', $business_id)
                ->select([
                    'distribution_districts.*',
                    'distribution_provinces.name as province_name',
                    'users.username as added_by'
                ]);

           
            if (!empty(request()->province_id)) {
                $fleets->where('distribution_districts.province_id', request()->province_id);
            }
            
            return DataTables::of($fleets)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Distribution\Http\Controllers\DistributionDistrictsController@edit', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';

                        $html .= '<li><a href="#" data-href="' . action('\Modules\Distribution\Http\Controllers\DistributionDistrictsController@destroy', [$row->id]) . '" class="delete_button"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        $html .= '<li class="divider"></li>';
                        
                        return $html;
                    }
                )
                ->editColumn('date', '{{@format_date($created_at)}}')
                
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }


        $provinces = Distribution_provinces::where('business_id', $business_id)->pluck('name', 'id');
        
        return view('distribution::settings.districts.index')->with(compact(
            'provinces'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    
    public function create()
    {
        $business_id = request()->session()->get('business.id');

        $provinces = Distribution_provinces::where('business_id', $business_id)->pluck('name', 'id');
        
        return view('distribution::settings.districts.create')->with(compact(
            'provinces'
        ));
    }

    

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        try {
            $business_id = request()->session()->get('business.id');

            $inputs = $request->except('_token');
            $inputs['added_by'] = Auth::user()->id;
            
            Distribution_districts::create($inputs);

            $output = [
                'success' => true,
                'msg' => __('Distribution_districts::lang.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with(['status'=> $output,"page" => "districts"]);
    }

    
    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $district = Distribution_districts::find($id);
        $business_id = request()->session()->get('business.id');
        $provinces = Distribution_provinces::where('business_id', $business_id)->pluck('name', 'id');

        return view('distribution::settings.districts.edit')->with(compact(
            'provinces','district'
        ));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        try {
            $inputs = $request->except('_token', '_method');
            
            Distribution_districts::where('id', $id)->update($inputs);

            $output = [
                'success' => true,
                'msg' => __('Distribution_districts::lang.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with(['status'=> $output,"page" => "districts"]);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            Distribution_districts::where('id', $id)->delete();
            $output = [
                'success' => true,
                'msg' => __('Distribution_districts::lang.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    
}
