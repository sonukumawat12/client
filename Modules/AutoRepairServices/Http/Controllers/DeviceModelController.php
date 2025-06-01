<?php

namespace Modules\AutoRepairServices\Http\Controllers;

use App\Brands;
use App\Category;
use App\Account;
use App\AccountGroup;
use App\AccountType;
use App\Transaction;
use App\Product;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Modules\AutoRepairServices\Entities\DeviceModel;
use Yajra\DataTables\Facades\DataTables;
use Modules\AutoRepairServices\Entities\JobSheet;
use Modules\AutoRepairServices\Utils\RepairUtil;
use Illuminate\Support\Facades\Log; // Import Log facade
use Modules\Superadmin\Entities\HelpExplanation;
use Illuminate\Support\Facades\Validator;

class DeviceModelController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;
    protected $repairUtil;

    /**
     * Constructor
     *
     */
    public function __construct(ModuleUtil $moduleUtil, RepairUtil $repairUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->repairUtil = $repairUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('user') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module')))) {
            abort(403, 'Unauthorized action.');
        }
        if ($request->ajax()) {
            Log::info('AJAX request received for device models.');
        
            $models = DeviceModel::with('Device', 'Brand')
                ->where('business_id', $business_id)
                ->select('*');

                
        
            Log::info('Initial query set.');
        
            if (!empty($request->get('brand_id'))) {
                Log::info('Filtering by brand_id: ' . $request->get('brand_id'));
                $models->where('brand_id', $request->get('brand_id'));
            }
        
            if (!empty($request->get('device_id'))) {
                Log::info('Filtering by device_id: ' . $request->get('device_id'));
                $models->where('device_id', $request->get('device_id'));
            }
        
            Log::info('Final Query: ', ['query' => $models->toSql()]);
        
            return Datatables::of($models)
                ->addColumn('action', function ($row) {
                    Log::info('Processing action column for model ID: ' . $row->id);
        
                    $html = '<div class="btn-group">
                                <button class="btn btn-info dropdown-toggle btn-xs" type="button" data-toggle="dropdown" aria-expanded="false">
                                    ' . __("messages.action") . '
                                    <span class="caret"></span>
                                    <span class="sr-only">' . __("messages.action") . '</span>
                                </button>';
        
                    $html .= '<ul class="dropdown-menu dropdown-menu-left" role="menu">
                                <li>
                                    <a data-href="' . action('\Modules\AutoRepairServices\Http\Controllers\DeviceModelController@edit', ['device_model' => $row->id]) . '" class="cursor-pointer edit_device_model">
                                        <i class="fa fa-edit"></i> ' . __("messages.edit") . '
                                    </a>
                                </li>
                                <li>
                                    <a data-href="' . action('\Modules\AutoRepairServices\Http\Controllers\DeviceModelController@destroy', ['device_model' => $row->id]) . '" id="delete_a_model" class="cursor-pointer">
                                        <i class="fas fa-trash"></i> ' . __("messages.delete") . '
                                    </a>
                                </li>
                            </ul>
                        </div>';
        
                    return $html;
                })
                ->editColumn('repair_checklist', function ($row) {
                    Log::info('Processing repair_checklist for model ID: ' . $row->id);
        
                    $checklist = '';
                    if (!empty($row->repair_checklist)) {
                        $checklist = explode('|', $row->repair_checklist);
                    }
        
                    return $checklist;
                })
                ->editColumn('device_id', function ($row) {
                    Log::info('Processing device_id for model ID: ' . $row->id);
                    return optional($row->Device)->name;
                })
                ->editColumn('brand_id', function ($row) {
                    Log::info('Processing brand_id for model ID: ' . $row->id);
                    return optional($row->Brand)->name;
                })
                ->removeColumn('id')
                ->rawColumns(['action', 'repair_checklist', 'device_id', 'brand_id'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('user') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module')))) {
            abort(403, 'Unauthorized action.');
        }

        $brands = Brands::forDropdownAutoRepair($business_id, false, true);

        $devices = Product::where('business_id', $business_id )
                    ->where('product_custom_field1','auto-device')
                  ->pluck('name', 'id');


        return view('autorepairservices::device_model.create')
            ->with(compact('devices', 'brands'));
    }

    public function create_device()
    {
        $business_id = request()->session()->get('user.business_id');
 
        if (!(auth()->user()->can('user') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module')))) {
            abort(403, 'Unauthorized action.');
        }

       

        return view('autorepairservices::device.create');
    }
 public function edit_device()
    {
        $business_id = request()->session()->get('user.business_id');
 
        if (!(auth()->user()->can('user') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module')))) {
            abort(403, 'Unauthorized action.');
        }

       

        return view('autorepairservices::device.edit');
    }
   
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('user') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module')))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only('name', 'brand_id', 'device_id', 'repair_checklist');
            $input['business_id'] = $business_id;
            $input['created_by'] = $request->user()->id;

            $device_model = DeviceModel::create($input);

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
                'data' => $device_model
            ];
        } catch (Exception $e) {

            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }


    public function store_device(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $category_type = 'auto-device';
        if ($category_type == 'product' && !auth()->user()->can('category.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            
            
            $input['name'] = $request->name;
             $input['product_description'] = $request->description;
            $input['business_id'] = $request->session()->get('user.business_id');
            
              
            
            $input['created_by'] = $request->session()->get('user.id');
            $input['product_custom_field1'] = 'auto-device';

            $exists = Product::where('business_id',$business_id)
            ->where('product_custom_field1', $category_type)
            ->where('name', $input['name'])
            ->exists();

        if ($exists) {
            return back()->withInput()->with([
                'status' => ['success' => false, 'msg' => __("Product exist")]
            ]);
        }
        
            $category = Product::create($input);
            $output = ['success' => true,
                            'data' => $category,
                            'msg' => __("Success")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return redirect()->back()->with('status', $output);
    }
   
    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('autorepairservices::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('user') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module')))) {
            abort(403, 'Unauthorized action.');
        }

        $model = DeviceModel::where('business_id', $business_id)
                    ->findOrFail($id);
 
        $brands = Brands::forDropdownAutoRepair($business_id, false, true);
        $devices = Product::where('business_id', $business_id )
                    ->where('product_custom_field1','auto-device')
                  ->pluck('name', 'id');

        return view('autorepairservices::device_model.edit')
            ->with(compact('devices', 'brands', 'model'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    // public function update(Request $request, $id)
    // {
    //     $business_id = request()->session()->get('user.business_id');

    //     if (!(auth()->user()->can('user') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module')))) {
    //         abort(403, 'Unauthorized action.');
    //     }

    //     try {
    //         $input = $request->only('name', 'brand_id', 'device_id', 'repair_checklist');

    //         $model = DeviceModel::where('business_id', $business_id)
    //                         ->findOrFail($id);

    //         $model->update($input);

    //         $output = [
    //             'success' => true,
    //             'msg' => __('lang_v1.success')
    //         ];
    //     } catch (Exception $e) {
    //         DB::rollBack();

    //         \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

    //         $output = [
    //             'success' => false,
    //             'msg' => __('messages.something_went_wrong')
    //         ];
    //     }

    //     return $output;
    // }
public function update(Request $request, $id)
    {
        if (!auth()->user()->can('category.update')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = session()->get('user.business_id');
        $account_access = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');

        if ($account_access) {
            $validator = Validator::make($request->all(), [
                'cogs_account_id' => 'required',
                'sales_income_account_id' => 'required',
                'add_related_account' => 'required'
            ]);

            if ($validator->fails()) {
                $output = [
                    'success' => 0,
                    'msg' => $validator->errors()->all()[0]
                ];
                return $output;
            }
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['price_reduction_acc','price_increment_acc','name', 'short_code', 'cogs_account_id', 'sales_income_account_id', 'add_related_account']);
                $business_id = $request->session()->get('user.business_id');

                $category = Category::where('business_id', $business_id)->findOrFail($id);
                $category->name = $request->name;
                $category->price_reduction_acc = $request->price_reduction_acc;
                $category->price_increment_acc = $request->price_increment_acc;
                //$category->remaining_stock_adjusts = $input['remaining_stock_adjusts'];
                
                $category->short_code = $request->short_code;
                $category->cogs_account_id = $request->cogs_account_id;
                $category->sales_income_account_id = $request->sales_income_account_id;
                $category->add_related_account = $request->add_related_account;
                
                $category->weight_excess_loss_applicable = !empty($request->weight_excess_loss_applicable) ? 1 : 0;
                $category->weight_loss_expense_account_id = $request->weight_loss_expense_account_id;
                $category->weight_excess_income_account_id = $request->weight_excess_income_account_id;
                $category->vat_based_on = '-';
                $category->apply_vat_on ='-';
                $category->description = $request->description;
                $category->vat_exempted = $request->vat_exempted;
                $category->profit_percentage = $request->profit_percentage;

                if (!empty($request->input('add_as_sub_cat')) &&  $request->input('add_as_sub_cat') == 1 && !empty($request->input('parent_id'))) {
                    $category->parent_id = $request->input('parent_id');
                } else {
                    $category->parent_id = 0;
                }
                $category->save();

                $output = [
                    'success' => true,
                    'msg' => __("category.updated_success")
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
    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('user') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module')))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $model = DeviceModel::where('business_id', $business_id)
                            ->findOrFail($id);

                $model->delete();

                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.success'),
                ];
            } catch (Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
            }
            return $output;
        }
    }

    /**
     * get models for particular device
     * @param $request
     * @return Response
     */
    public function getDeviceModels(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $device_id = $request->get('device_id');
        $brand_id = $request->get('brand_id');

        $query = DeviceModel::where('business_id', $business_id)
                    ->where('device_id', $device_id);

        if (!empty($brand_id)) {
            $query->where('brand_id', $brand_id);
        }

        $models = $query->pluck('name', 'id');

        //dynamically generate dropdown
        $model_html = View::make('autorepairservices::device_model.partials.device_model_drodown')
                        ->with(compact('models'))
                        ->render();

        return $model_html;
    }

    /**
     * get repair checklist for particular models
     * @param $request
     * @return Response
     */
    public function getRepairChecklists(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $model_id = $request->get('model_id');
        $transaction_id = $request->get('transaction_id');
        $job_sheet_id = $request->get('job_sheet_id');

        $device_model = DeviceModel::where('business_id', $business_id)
                            ->find($model_id);

        $selected_checklist = [];
        //used while editing and creating invoivce
        if (!empty($transaction_id)) {
            $transaction = Transaction::where('business_id', $business_id)
                            ->where('type', 'sell')
                            ->find($transaction_id);

            $selected_checklist = !empty($transaction->repair_checklist) ? json_decode($transaction->repair_checklist, true) : [];
        }

        //used while adding/editing/creating job sheet and its invoivce
        if (!empty($job_sheet_id)) {
            $job_sheet = JobSheet::where('business_id', $business_id)
                            ->find($job_sheet_id);

            $selected_checklist = !empty($job_sheet->checklist) ? $job_sheet->checklist : [];
        }

        $checklists = [];
        if (!empty($device_model) && !empty($device_model->repair_checklist)) {
            $checklists = explode('|', $device_model->repair_checklist);
        }

        //merge default checklist
        $repair_settings = $this->repairUtil->getRepairSettings($business_id);  
        if(!empty($repair_settings['default_repair_checklist'])) {
            $checklists = array_merge(explode('|', $repair_settings['default_repair_checklist']), $checklists);
        }

        //dynamically generate dropdown
        $checklists_html = View::make('autorepairservices::repair.partials.checklists')
                            ->with(compact('checklists', 'selected_checklist'))
                            ->render();

        return $checklists_html;
    }
}
