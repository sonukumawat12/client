<?php

namespace Modules\AutoRepairServices\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Brands;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class VehicleBrandController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;
    const STATUS = [
        'active' => 1,
        'inactive' => 0
    ];

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('brand.view') && !auth()->user()->can('brand.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $brands = Brands::where('business_id', $business_id)
                        ->select(['name', 'description', 'id']);

            return Datatables::of($brands)
                ->addColumn(
                    'action',
                    '@can("brand.update")
                    <button data-href="{{action(\'BrandController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_brand_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                    @endcan
                    @can("brand.delete")
                        <button data-href="{{action(\'BrandController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_brand_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    @endcan'
                )
                ->removeColumn('id')
                ->rawColumns([2])
                ->make(false);
        }

        return view('autorepairservices::vehiclebrand.index');
    }
    
    public function table(Request $req)
    {
        $data = Brands::where('is_auto_repair', self::STATUS['active'])
                    ->where('business_id', session()->get('user.business_id'))
                    ->select(
                        'name as vehicle_brand',
                        'type as vehicle_type',
                        'model as vehicle_model',
                        'serial_nos as chassis_no',
                        'id'
                    )
                    ->orderBy('created_at', 'desc')
                    ->get();
                    
        return Datatables::of($data)
                ->addColumn('action', function ($row) {
                    $args = json_encode(['id' => $row->id]);
                    return '
                        <a href="#" class="btn btn-xs btn-primary text-info"
                            data-toggle="modal" data-target="#addBrandService"
                            data-id=' . $row->id . '
                            title="Edit" >
                            <i class="fa-lg i-Edit font-weight-bold text-primary"></i> Edit
                        </a>
                        <a href="#" class="btn btn-xs btn-danger text-info"
                            title="Delete Brand"
                            onClick=\'swalConfirm(' . $args . ');\' >
                            <i class="fa-lg i-Eraser-2 font-weight-bold text-danger"></i> Delete
                        </a>
                    ';
                })
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make();
    }
    
    public function populateData(Request $req) {
        return Brands::where([
                    'id' => $req->id,
                    'is_auto_repair' => self::STATUS['active'],
                    'business_id' => session()->get('user.business_id')
                ])
                ->select(
                    'name as vehicle_brand',
                    'type as vehicle_type',
                    'model as vehicle_model',
                    'serial_nos as chassis_no',
                    'id'
                )
                ->first();
    }
    
    public function saveData(Request $req) {
        \DB::beginTransaction();
        try {
            parse_str($req['args'], $data);
            
            if ((int) $data['mod-id'] > 0) {
                $store = Brands::where('id', $data['mod-id'])->first();
            } else {
                $store = new Brands();
            }
            $store->business_id = session()->get('user.business_id');
            $store->name = $data['vehicle_brand'];
            $store->type = $data['vehicle_type'];
            $store->model = $data['vehicle_model'];
            $store->serial_nos = $data['chassis_no'];
            $store->is_auto_repair = 1;
            $store->created_by = session()->get('user.id');
            $store->save();
    
            \DB::commit();
            
            return [
                'swalState' => 'success',
                'title' => __("lang_v1.success"),
                'msg' => __("lang_v1.data_has_been_saved")
            ];
        } catch (\Exception $e) {
            \DB::rollBack();
            throw new \Exception(__METHOD__ . ' Message:' . $e->getMessage() . ' Line:' . $e->getLine());
        }
    }
    
    public function deleteData(Request $req) {
        \DB::beginTransaction();
        try {
            $query = Brands::where('id', $req->id)->first();
            if ($query) {
                $query->delete();
                \DB::commit();
            }
            return [
                'swalState' => 'success',
                'title' => __("lang_v1.success"),
                'msg' => __("lang_v1.data_has_been_deleted")
            ];
        } catch (\Exception $e) {
            \DB::rollBack();
            throw new \Exception(__METHOD__ . ' Message:' . $e->getMessage() . ' Line:' . $e->getLine());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('brand.create')) {
            abort(403, 'Unauthorized action.');
        }

        $quick_add = false;
        if (!empty(request()->input('quick_add'))) {
            $quick_add = true;
        }

        $is_repair_installed = $this->moduleUtil->isModuleInstalled('Repair');

        return view('brand.create')
                ->with(compact('quick_add', 'is_repair_installed'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('brand.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['name', 'description']);
            $business_id = $request->session()->get('user.business_id');
            $input['business_id'] = $business_id;
            $input['created_by'] = $request->session()->get('user.id');

            if ($this->moduleUtil->isModuleInstalled('Repair')) {
                $input['use_for_repair'] = !empty($request->input('use_for_repair')) ? 1 : 0;
            }

            $brand = Brands::create($input);
            $output = ['success' => true,
                            'data' => $brand,
                            'msg' => __("brand.added_success")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
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
        if (!auth()->user()->can('brand.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $brand = Brands::where('business_id', $business_id)->find($id);

            $is_repair_installed = $this->moduleUtil->isModuleInstalled('Repair');

            return view('brand.edit')
                ->with(compact('brand', 'is_repair_installed'));
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
        if (!auth()->user()->can('brand.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['name', 'description']);
                $business_id = $request->session()->get('user.business_id');

                $brand = Brands::where('business_id', $business_id)->findOrFail($id);
                $brand->name = $input['name'];
                $brand->description = $input['description'];

                if ($this->moduleUtil->isModuleInstalled('Repair')) {
                    $brand->use_for_repair = !empty($request->input('use_for_repair')) ? 1 : 0;
                }
                
                $brand->save();

                $output = ['success' => true,
                            'msg' => __("brand.updated_success")
                            ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }

            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('brand.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->user()->business_id;

                $brand = Brands::where('business_id', $business_id)->findOrFail($id);
                $brand->delete();

                $output = ['success' => true,
                            'msg' => __("brand.deleted_success")
                            ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }

            return $output;
        }
    }

    public function getBrandsApi()
    {
        try {
            $api_token = request()->header('API-TOKEN');

            $api_settings = $this->moduleUtil->getApiSettings($api_token);
            
            $brands = Brands::where('business_id', $api_settings->business_id)
                                ->get();
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
        
            return $this->respondWentWrong($e);
        }

        return $this->respond($brands);
    }
}
