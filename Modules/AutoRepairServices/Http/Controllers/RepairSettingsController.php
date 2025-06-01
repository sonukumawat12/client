<?php

namespace Modules\AutoRepairServices\Http\Controllers;

use App\Barcode;
use App\Brands;
use App\Business;
use App\Category;
use App\Utils\ModuleUtil;
use App\Variation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\AutoRepairServices\Entities\RepairStatus;
use Modules\AutoRepairServices\Utils\RepairUtil;
use App\Product;
class RepairSettingsController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $repairUtil;
    protected $moduleUtil;


    /**
     * Constructor
     *
     * @param RepairUtil $repairUtil
     * @return void
     */
    public function __construct(RepairUtil $repairUtil, ModuleUtil $moduleUtil)
    {
        $this->repairUtil = $repairUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('user') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module') && auth()->user()->can('repair.create')))) {
            abort(403, 'Unauthorized action.');
        }

        $barcode_settings = Barcode::where('business_id', $business_id)
                                ->orWhereNull('business_id')
                                ->pluck('name', 'id');

        $repair_settings = $this->repairUtil->getRepairSettings($business_id);

        $default_product_name = __('autorepairservices::lang.no_default_product_selected');
        if (!empty($repair_settings['default_product'])) {
            $default_product = Variation::where('id', $repair_settings['default_product'])
                                ->where('business_id',$business_id)
                        ->with(['product_variation', 'product'])
                        ->first();

            $default_product_name = $default_product->product->type == 'single' ? $default_product->product->name . ' - ' . $default_product->product->sku : $default_product->product->name . ' (' . $default_product->name . ') - ' . $default_product->sub_sku;
        }
        //barcode types
        $barcode_types = $this->moduleUtil->barcode_types();
        $repair_statuses = RepairStatus::getRepairSatuses($business_id);
       $repair_status = RepairStatus::getRepairSatuses($business_id)->toArray();

 
        $brands = Brands::forDropdownAutoRepair($business_id, false, true);
        $devices = Product::where('business_id',$business_id)
                            ->where('product_custom_field1','auto-device')
                            ->pluck('name','id');

        $module_category_data = $this->moduleUtil->getTaxonomyData('device');
  
        return view('autorepairservices::settings.index')
                ->with(compact('barcode_settings', 'repair_settings', 'default_product_name', 'barcode_types', 'repair_statuses', 'brands', 'devices', 'module_category_data','repair_status'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('user') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module') && auth()->user()->can('repair.create')))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['barcode_id', 'default_product', 'barcode_type', 'repair_tc_condition', 'job_sheet_prefix', 'problem_reported_by_customer', 'product_condition', 'product_configuration', 'job_sheet_custom_field_1', 'job_sheet_custom_field_2', 'job_sheet_custom_field_3', 'job_sheet_custom_field_4', 'job_sheet_custom_field_5', 'default_repair_checklist']);

            $default_status = $request->get('default_status');
            if (!empty($default_status) && is_numeric($default_status)) {
                $input['default_status'] = $default_status;
            } else {
                $input['default_status'] = '';
            }
            Business::where('id', $business_id)
                        ->update(['auto_repair_settings' => json_encode($input)]);

            $output = ['success' => true,
                            'msg' => __("lang_v1.updated_success")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return redirect()->back()->with(['status' => $output]);
    }
}
