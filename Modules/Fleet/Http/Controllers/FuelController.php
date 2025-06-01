<?php

namespace Modules\Fleet\Http\Controllers;

use App\Transaction;
use App\TransactionPayment;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Fleet\Entities\Driver;
use Modules\Fleet\Entities\FleetFuelType;
use Modules\Fleet\Entities\RouteOperation;
use Yajra\DataTables\Facades\DataTables;

use App\Category;

class FuelController extends Controller
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
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $fuel_types = FleetFuelType::leftJoin('users', 'fleet_fuel_types.created_by', 'users.id')
                ->where('fleet_fuel_types.business_id', $business_id)
                ->where('fleet_fuel_types.status', '!=' , 0)
                ->select([
                    'fleet_fuel_types.*',
                    'users.first_name as created_by',
                ])
                ->orderBy('fleet_fuel_types.id', 'desc')
                ->get();


            return DataTables::of($fuel_types)
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
                        if (auth()->user()->can('fleet.edit_fuel_type')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\FuelController@newPrice', ['id' => $row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("fleet::lang.new_price") . '</a></li>';
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\FuelController@edit', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        }
                        if (auth()->user()->can('fleet.edit_fuel_type')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\FuelController@destroy', [$row->id]) . '" class="delete_button"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        }

                        return $html;
                    }
                )
                ->editColumn('status', function ($row) {
                    $html = '';

                    if ($row->status == 1) {
                        $html = "<i class='fas fa-check text-success'></i> Enabled";
                    } else if ($row->status == 0) {
                        $html = "<i class='fas fa-times text-danger'></i> Disabled";
                    } else if ($row->status == 3) {
                        $html = "<i class='fas fa-check text-warning'></i> New Price";
                    }
                    
                    return $html;
                })
                ->editColumn('date', '{{@format_date($date)}}')
                ->removeColumn('id')
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        $prefix_type = 'employee_no';
        //Generate reference number
        $ref_count = $this->transactionUtil->onlyGetReferenceCount($prefix_type, $business_id, false);
        //Generate reference number
        $employee_no = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);

        $departments =  Category::where('business_id', $business_id)
            ->where('category_type', 'hrm_department')
            ->pluck('name','id');

        return view('fleet::settings.fuel_types.create')->with(compact(
            'employee_no','departments'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        try {
            $data = $request->except('_token');
            $data['date'] = $this->commonUtil->uf_date($data['date']);
            $data['business_id'] = $business_id;
            $data['created_by'] = Auth::user()->id;

            //update emploeyee count
            //$this->transactionUtil->setAndGetReferenceCount('employee_no', $business_id);

            FleetFuelType::create($data);

            $output = [
                'success' => true,
                'tab' => 'fuel_types',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'fuel_types',
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $business_id = request()->session()->get('business.id');
        $view_type = request()->tab;
        $fuel = FleetFuelType::find($id);
        $contact_id = $id;

        return view('fleet::settings.fuel_types.show')->with(compact(
            'view_type',
            'fuel',
            'contact_id'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $fuel = FleetFuelType::find($id);

        return view('fleet::settings.fuel_types.edit')->with(compact(
            'fuel'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function newPrice(Request $request)
    {
        $business_id_user= request()->session()->get('user.business_id');
        $fuel = FleetFuelType::find($request->query('id'));
        $previousFuels = FleetFuelType::leftJoin('users', 'fleet_fuel_types.created_by', 'users.id')
            ->where('fleet_fuel_types.business_id', $business_id_user)
            ->where('fleet_fuel_types.status', 0)
            ->where('fleet_fuel_types.type',"like", "%".$fuel->type."%")
            ->orderBy('fleet_fuel_types.id','desc')
            ->get();
        
        $previousPrice = null;

        foreach ($previousFuels as $key => $data) {
            if ($previousPrice !== null) {
                $data->new_price = $previousPrice;
            } else {
                $data->new_price = $fuel->price_per_litre;
            }
            $previousPrice = $data->price_per_litre;
        }
        
        
        $newPrice = $fuel->price_per_litre;
        $currentUser = Auth::user()->first_name;
        return view('fleet::settings.fuel_types.new_price')->with(compact(
            'fuel',
            'previousFuels',
            'newPrice',
            'currentUser'
        ));
    }

    public function addNewPrice(Request $request)
    {
        try {
            $id = $request->query('id');
            $fuel = FleetFuelType::find($id);
            $fuel->status = 0;
            $fuel->save();
            
            
            $data = $request->except('_token', '_method');
            $today = now()->format('m/d/Y');
            $data['date'] = $this->commonUtil->uf_date($today);

            $business_id = request()->session()->get('business.id');
            $newRecord = FleetFuelType::create([
                "business_id" => $business_id,
                "date" => $fuel->date,
                "type" => $fuel->type,
                "price_per_litre" => $data["price_per_litre_new"],
                "status" => 1,
                "created_by" => Auth::user()->id
            ]);

            
            $output = [
                'success' => true,
                'tab' => 'fuel_types',
                'msg' => __('lang_v1.success')
            ];

        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'fuel_types',
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    // public function update(Request $request, $id)
    // {
    //     try {
    //         $data = $request->except('_token', '_method');
    //         $data['date'] = $this->commonUtil->uf_date($data['date']);

    //         FleetFuelType::where('id', $id)->update($data);
    //         FleetFuelType::create($data);

    //         $output = [
    //             'success' => true,
    //             'tab' => 'fuel_types',
    //             'msg' => __('lang_v1.success')
    //         ];
    //     } catch (\Exception $e) {
    //         Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
    //         $output = [
    //             'success' => false,
    //             'tab' => 'fuel_types',
    //             'msg' => __('messages.something_went_wrong')
    //         ];
    //     }

    //     return redirect()->back()->with('status', $output);
    // }
    public function update(Request $request, $id)
    {
        try {
            $data = $request->except('_token', '_method');
            $data['date'] = $this->commonUtil->uf_date($data['date']);

            $oldRecord = FleetFuelType::find($id);

            if ($oldRecord) {
                

                // Set the status to 1 in the data array before creating the new record
                $business_id = request()->session()->get('business.id');
                $data['business_id'] = $business_id;
                $data['created_by'] = Auth::user()->id;
                $data['status'] = $oldRecord->status;//for cases with new price
                
                
                $oldRecord->update(['status' => 0]);

                $newRecord = FleetFuelType::create($data);
                $output = [
                    'success' => true,
                    'tab' => 'fuel_types',
                    'msg' => __('lang_v1.success')
                ];
            } else {
                $output = [
                    'success' => false,
                    'tab' => 'fuel_types',
                    'msg' => __('lang_v1.record_not_found')
                ];
            }
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'fuel_types',
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }


    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            FleetFuelType::where('id', $id)->delete();

            $route_operations = RouteOperation::where('driver_id', $id)->get();
            foreach ($route_operations as $route_operation) {
                Transaction::where('id', $route_operation->transaction_id)->delete();
                TransactionPayment::where('transaction_id', $route_operation->transaction_id)->delete();
            }
            RouteOperation::where('helper_id', $id)->delete();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
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
