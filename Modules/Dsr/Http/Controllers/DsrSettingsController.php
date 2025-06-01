<?php
namespace Modules\Dsr\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Distribution\Entities\Distribution_districts;
use Modules\Distribution\Entities\provinces;
use Modules\Dsr\Entities\Areas;
use Modules\Dsr\Entities\BusinessDsrOfficer;
use Modules\Dsr\Entities\DsrSettings;
use Modules\Dsr\Entities\FuelProvider;
use Modules\Leads\Entities\District;
use Yajra\DataTables\DataTables;

class DsrSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $business_id= request()->session()->get('business.id');
        if(request()->ajax()){
            $dsr_settings = DsrSettings::leftjoin('countries','countries.id','=','dsr_settings.country_id')
                ->leftjoin('provinces','provinces.id','=','dsr_settings.province_id')
                ->leftjoin('districts','districts.id','=','dsr_settings.district_id')
                ->leftjoin('fuel_providers','fuel_providers.id','=','dsr_settings.fuel_provider_id')
                ->select('dsr_settings.*',
                    'dsr_settings.id as report_no',
                    'dsr_settings.created_at as date',
                    'dsr_settings.dealer_name as dealer_name',
                    'dsr_settings.dealer_number as dealer_number',
                    'dsr_settings.dsr_starting_number as dsr_starting_number',
                    'dsr_settings.dealer_number as dealer_number',
                    'countries.country',
                    'provinces.name as province',
                    'districts.name as district',
                    'fuel_providers.name as fuel_provider')
            ->where('dsr_settings.product_id', null);
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $dsr_settings = $dsr_settings->whereBetween('dsr_settings.created_at', [
                    date("Y-m-d", strtotime(request()->start_date)),
                    date("Y-m-d", strtotime(request()->end_date))
                ]);
            }
            if (!empty(request()->country_id)) {
                $dsr_settings = $dsr_settings->where('dsr_settings.country_id', request()->country_id);
            }
            if (!empty(request()->province_id)) {
                $dsr_settings = $dsr_settings->where('dsr_settings.province_id', request()->province_id);
            }
            if (!empty(request()->district_id)) {
                $dsr_settings = $dsr_settings->where('dsr_settings.district_id', request()->district_id);
            }
            return DataTables::of($dsr_settings)
                ->addColumn(
                    'action',
                    '
                    <button data-href="{{action(\'\Modules\Dsr\Http\Controllers\DsrSettingsController@edit\',[$id])}}" data-container=".dsr_settings_model" class="btn btn-xs btn-primary btn-modal edit_btn"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                   
                    '
                )
                ->editColumn('date',function($item){
                    $date = $item->date;
                    $date = date_create($date);
                    return $date->format('F j, Y g:i A');
                })
                ->rawColumns(['action','date'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $fuelProviders = FuelProvider::pluck('name', 'id')->toArray();
        $countries = DB::table('countries')->pluck('country', 'id')->toArray();
        $dsr_setting_country_ids = DsrSettings::pluck('business_id')->toArray();
        $provinces = DB::table('provinces')->pluck('name', 'id')->toArray();
        $districts = DB::table('districts')->pluck('name', 'id')->toArray();
        $areas = DB::Table('areas')->pluck('name','id')->toArray();

        if(!empty($dsr_setting_country_ids)){
            $businesses = DB::table('business')
                ->whereNotIn('id',$dsr_setting_country_ids)
                ->pluck('name','id')->toArray();
        } else {
            $businesses = DB::table('business')
                ->pluck('name','id')->toArray();
        }
        return view('dsr::settings.create', compact('fuelProviders', 'businesses','countries', 'provinces', 'districts', 'areas'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $postData = $request->all();
        $postData['user_id'] = auth()->user()->id;
        if (isset($postData['areas'])) {
            $postData['areas'] = json_encode($postData['areas']);
        }
        $dsr_settings = DsrSettings::updateOrCreate(
            ['business_id' => $postData['business_id']
            ], $postData);
        if ($dsr_settings) {
            $output = [
                'success' => 1,
                'msg' => __('dsr::lang.dsr_settings_added_success')
            ];
        } else {
            $output = [
                'success' => 0,
                'msg' => __('dsr::lang.dsr_settings_added_error')
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
        return view('dsr::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $fuelProviders = FuelProvider::pluck('name', 'id')->toArray();
        $countries = DB::table('countries')->pluck('country', 'id')->toArray();
        $provinces = DB::table('provinces')->pluck('name', 'id')->toArray();
        $districts = DB::table('districts')->pluck('name', 'id')->toArray();
        $allAreas = DB::Table('areas')->pluck('name','id')->toArray();

        $dsr_settings = DsrSettings::find($id);

        return view('dsr::settings.edit', compact('dsr_settings','fuelProviders', 'countries', 'provinces', 'districts', 'allAreas'));
    }
    
    public function editOB($id)
    {
        $fuel_products = DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->where('categories.name', 'Fuel')
            ->pluck('products.name','products.id')
            ->toArray();

        $dsr_settings = DsrSettings::find($id);

        return view('dsr::settings.dsr_opening_meter.edit', compact('dsr_settings','fuel_products'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {

                $dsr_settings = DsrSettings::find($id);
                $postData = $request->all();
                $postData['user_id'] = auth()->user()->id;
                if (isset($postData['areas'])) {
                    $postData['areas'] = json_encode($postData['areas']);
                }
                $dsr_settings->update($postData);
                if ($dsr_settings) {
                    $output = [
                        'success' => 1,
                        'msg' => __('dsr::lang.dsr_settings_updated_success')
                    ];
                } else {
                    $output = [
                        'success' => 0,
                        'msg' => __('dsr::lang.dsr_settings_updated_error')
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
        //
    }

    public function createArea() {
        $name = \request('name');
        $area = Areas::create([
            'name' => $name,
            'user_d' => auth()->user()->id
        ]);
        if ($area) {
            $output = [
                'success' => 1,
                'msg' => __('dsr::lang.area_added_success')
            ];
        } else {
            $output = [
                'success' => 0,
                'msg' => __('dsr::lang.area_added_error')
            ];
        }
        return redirect()->back()->with('status', $output);
    }
    public function createDistrict() {
        $name = \request('name');
        $area = District::create([
            'name' => $name,
            'created_by' => auth()->user()->id,
            'business_id' => request()->session()->get('business.id'),
            'date' => date('Y-m-d H:i:s'),
        ]);
        if ($area) {
            $output = [
                'success' => 1,
                'msg' => __('dsr::lang.district_added_success')
            ];
        } else {
            $output = [
                'success' => 0,
                'msg' => __('dsr::lang.district_added_error')
            ];
        }
        return redirect()->back()->with('status', $output);
    }

    public function createProvice()
    {
        $name = \request('name');
        $area = provinces::create([
            'name' => $name,
            'created_by' => auth()->user()->id,
            'business_id' => request()->session()->get('business.id'),
            'date' => date('Y-m-d H:i:s'),
        ]);
        if ($area) {
            $output = [
                'success' => 1,
                'msg' => __('dsr::lang.province_added_success')
            ];
        } else {
            $output = [
                'success' => 0,
                'msg' => __('dsr::lang.province_added_error')
            ];
        }
        return redirect()->back()->with('status', $output);

    }

    public function addAccumulativeSalePurchase()
    {
        $postData = \request()->only(['accumulative_sale', 'accumulative_purchase','product_id','date_time']);
        $validations = [
            'accumulative_sale' => 'required|numeric',
            'accumulative_purchase' => 'required|numeric',
            'product_id' => 'required|numeric',
            'date_time' => 'required',
        ];
        $validator = Validator::make($postData, $validations);
        if ($validator->fails()) {
            $output = [
                'success' => 0,
                'msg' => __('dsr::lang.accumulative_sale_purchase_validation_error')
            ];
            return redirect()->back()->with('status', $output);
        }
        $postData['business_id'] = request()->session()->get('business.id');
        $postData['user_id'] = auth()->user()->id;
        
        // dd($postData);
        
        $data = DsrSettings::create($postData);
        if ($data) {
            $output = [
                'success' => 1,
                'msg' => __('dsr::lang.accumulative_sale_purchase_added_success')
            ];
        } else {
            $output = [
                'success' => 0,
                'msg' => __('dsr::lang.accumulative_sale_purchase_added_error')
            ];
        }
        return redirect('/dsr/settings?tab=dsr_opening_meter')->with('status', $output);
    }
    public function listAccumulativeSalePurchase() {
        $business_id = request()->session()->get('business.id');
        if(request()->ajax()){
            $dsr_settings = DsrSettings::leftjoin('products','products.id','=','dsr_settings.product_id')
                ->leftjoin('users','users.id','=','dsr_settings.user_id')
                ->select('dsr_settings.*',
                    'dsr_settings.id as report_no',
                    'dsr_settings.date_time as date',
                    'dsr_settings.accumulative_sale as accumulative_sale',
                    'dsr_settings.accumulative_purchase as accumulative_purchase',
                    'users.first_name as user',
                    'products.name as product')
                ->where('dsr_settings.product_id', '!=', null)
                ->where('dsr_settings.business_id',$business_id);

            return DataTables::of($dsr_settings)
                ->addColumn(
                    'action',
                    '
                    <button data-href="{{action(\'\Modules\Dsr\Http\Controllers\DsrSettingsController@editOB\',[$id])}}" data-container=".dsr_settings_model" class="btn btn-xs btn-primary btn-modal edit_btn"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                   
                    '
                )
                ->editColumn('date','{{@format_date($date)}}')
                ->editColumn('accumulative_sale','{{@num_format($accumulative_sale)}}')
                ->editColumn('accumulative_purchase','{{@num_format($accumulative_purchase)}}')
                ->rawColumns(['action'])
                ->make(true);
        }
    }

}
