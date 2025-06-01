<?php

namespace Modules\Leads\Http\Controllers;

use App\Category;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Leads\Entities\Lead;
use Modules\Leads\Entities\LeadsCategory;
use App\Country;
use Modules\Leads\Entities\District;
use Modules\Leads\Entities\Town;
use Modules\Leads\Entities\LeadsLabel;


class ImportLeadsController extends Controller
{
    protected $moduleUtil;
    protected $productUtil;
    protected $importFields;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil, ProductUtil $productUtil)
    {
        $this->moduleUtil =  $moduleUtil;
        $this->productUtil =  $productUtil;
        
        $this->importFields = array(
                ['name' => __('leads::lang.date'), 'required' => true, 'explanation' => __('leads::lang.date')],
                ['name' => __('leads::lang.time'), 'required' => true, 'explanation' => ''],
                ['name' => __('leads::lang.sector'), 'required' => true, 'explanation' => __('leads::lang.sector_ins')],
                ['name' => __('leads::lang.category'), 'required' => true, 'explanation' => ''],
                ['name' => __('leads::lang.main_orgainization'), 'required' => true, 'explanation' => ''],
                ['name' => __('leads::lang.business'), 'required' => true, 'explanation' => ''],
                ['name' => __('leads::lang.address'), 'required' => true, 'explanation' => ''],
                ['name' => __('leads::lang.country'), 'required' => true, 'explanation' => ''],
                ['name' => __('leads::lang.town'), 'required' => true, 'explanation' => ''],
                ['name' => __('leads::lang.district'), 'required' => true, 'explanation' => ''],
                ['name' => __('leads::lang.mobile_no_1'), 'required' => true, 'explanation' => ''],
                ['name' => __('leads::lang.mobile_no_2'), 'required' => false, 'explanation' => ''],
                ['name' => __('leads::lang.mobile_no_3'), 'required' => false, 'explanation' => ''],
                ['name' => __('leads::lang.land_number'), 'required' => false, 'explanation' => ''],
                ['name' => __('leads::lang.email'), 'required' => true, 'explanation' => ''],
                ['name' => __('leads::lang.follow_up_date'), 'required' => false, 'explanation' => ''],
                ['name' => __('leads::lang.label_1'), 'required' => true, 'explanation' => ''],
                ['name' => __('leads::lang.label_2'), 'required' => false, 'explanation' => ''],
                ['name' => __('leads::lang.label_3'), 'required' => false, 'explanation' => ''],
                ['name' => __('brand.note'), 'required' => false, 'explanation' => ''],
        );
    }


    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'leads_import')) {
            abort(403, 'Unauthorized action.');
        }
        
        $fields = $this->importFields;
        
        return view('leads::import_leads.index')->with(compact('fields'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('leads::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'leads_import')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            //Set maximum php execution time
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);

            if ($request->hasFile('leads_csv')) {
                $file = $request->file('leads_csv');

                $parsed_array = Excel::toArray([], $file);

                //Remove header row
                $imported_data = array_splice($parsed_array[0], 1);

                $business_id = $request->session()->get('user.business_id');
                $user_id = $request->session()->get('user.id');

                $formated_data = [];

                $is_valid = true;
                $error_msg = '';

                $total_rows = count($imported_data);


                DB::beginTransaction();
                // $remove_last_row = array_pop($imported_data);
                foreach ($imported_data as $key => $value) {

                    //Check if any column is missing
                    if (count($value) < sizeof($this->importFields)) {
                        $is_valid =  false;
                        $error_msg = "Some of the columns are missing. Please, use latest CSV file template.";
                        break;
                    }

                    $row_no = $key + 1;
                    $leads_array = [];
                    $leads_array['business_id'] = $business_id;
                    $leads_array['created_by'] = $user_id;
                    
                    

                    //Add date
                    $date = trim($value[0]);
                    if (!empty($date)) {
                        $leads_array['date'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date);
                    } else {
                        $is_valid = false;
                        $error_msg = "Date is required. $row_no";
                        break;
                    }
                    
                    $time = trim($value[1]);
                    if (!empty($time)) {
                        $leads_array['time'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($time)->format('H:i:s');
                    } else {
                        $is_valid = false;
                        $error_msg = "Time is required. $row_no";
                        break;
                    }
                    
                    
                    //add sector
                    $sector = trim($value[2]);
                    if (!empty($sector)) {
                        $leads_array['sector'] = strtolower($sector);
                        
                        if(!in_array($sector,['private','government'])){
                            $is_valid = false;
                            $error_msg = "Unsupported Sector. $row_no";
                            break;
                        }
                        
                    } else {
                        $is_valid = false;
                        $error_msg = "sector is required. $row_no";
                        break;
                    }

                    $category_name = trim($value[3]);
                    if (!empty($category_name)) {
                        $category = LeadsCategory::firstOrCreate(
                            ['business_id' => $business_id, 'name' => $category_name],
                            ['business_id' => $business_id, 'name' => $category_name, 'created_by' => $user_id, 'date' => date('Y-m-d')]
                        );
                        $leads_array['category_id'] = $category->id;
                    }else{
                        $is_valid = false;
                        $error_msg = "Category is required. $row_no";
                        break;
                    }

                    //Add main_organization
                    $main_organization = trim($value[4]);
                    if (!empty($main_organization)) {
                        $leads_array['main_organization'] = $main_organization;
                    }else{
                        $is_valid = false;
                        $error_msg = "Main organization is required. $row_no";
                        break;
                    }
                    
                    //Add business
                    $business = trim($value[5]);
                    if (!empty($business)) {
                        $leads_array['business'] = $business;
                    }

                    //Add address
                    $address = trim($value[6]);
                    if (!empty($address)) {
                        $leads_array['address'] = $address;
                    }
                    
                    
                    $country = trim($value[7]);
                    if (!empty($country)) {
                        $country_id = Country::where('country',$country)->first()->id ?? null;
                        if(empty($country_id)){
                            $is_valid = false;
                            $error_msg = "Country not found. $row_no";
                            break;
                        }
                    }else{
                        $is_valid = false;
                        $error_msg = "Country is required. $row_no";
                        break;
                    }
                    
                    $district = trim($value[9]);
                    if (!empty($district)) {
                        $district_id = District::firstOrCreate(
                            ['business_id' => $business_id, 'name' => $district,'country_id' => $country_id],
                            ['business_id' => $business_id, 'name' => $district,'country_id' => $country_id,'created_by' => $user_id, 'date' => date('Y-m-d')]
                        );
                        $leads_array['district'] = $district;
                        
                    } else {
                        $is_valid = false;
                        $error_msg = "District is required. $row_no";
                        break;
                    }
                    

                    //add town
                    $town = trim($value[8]);
                    if (!empty($town)) {
                        $town_id = Town::firstOrCreate(
                            ['business_id' => $business_id, 'name' => $town,'district_id' => $district_id->id],
                            ['business_id' => $business_id, 'name' => $town,'district_id' => $district_id->id,'created_by' => $user_id,'date' => date('Y-m-d')]
                        );
                        $leads_array['town'] = $town;
                        
                    } else {
                        $is_valid = false;
                        $error_msg = "town is required. $row_no";
                        break;
                    }

                    
                    //add mobile_no_1
                    $mobile_no_1 = trim($value[10]);
                    if (!empty($mobile_no_1)) {
                        $leads_array['mobile_no_1'] = strtolower($mobile_no_1);
                    } else {
                        $is_valid = false;
                        $error_msg = "mobile no 1 is required. $row_no";
                        break;
                    }

                    //Add mobile_no_2
                    $mobile_no_2 = trim($value[11]);
                    if (!empty($mobile_no_2)) {
                        $leads_array['mobile_no_2'] = $mobile_no_2;
                    }


                    //Add mobile_no_3
                    $mobile_no_3 = trim($value[12]);
                    if (!empty($mobile_no_3)) {
                        $leads_array['mobile_no_3'] = $mobile_no_3;
                    }


                    //Add land_number
                    $land_number = trim($value[13]);
                    if (!empty($land_number)) {
                        $leads_array['land_number'] = $land_number;
                    }
                    
                    
                    $email = trim($value[14]);
                    if (!empty($email)) {
                        $leads_array['email'] = $email;
                    }

                    $follow_up_date = trim($value[15]);
                    if (!empty($follow_up_date)) {
                        $leads_array['follow_up_date'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($follow_up_date);
                    }

                    $label_1 = trim($value[16]);
                    $label_2 = trim($value[17]);
                    $label_3 = trim($value[18]);
                    if (!empty($label_1)) {
                        
                        $label_id = LeadsLabel::firstOrCreate(
                            ['business_id' => $business_id, 'label_1' => $label_1,'label_2' => $label_2,'label_3' => $label_3],
                            ['business_id' => $business_id, 'label_1' => $label_1,'label_2' => $label_2,'label_3' => $label_3,'created_by' => $user_id]
                        );
                        
                        $leads_array['label_id'] = $label_id->id;
                        
                    }else{
                        $is_valid = false;
                        $error_msg = "Label 1 is required. $row_no";
                        break;
                    }
                    $land_number = trim($value[17]);
                    $land_number = trim($value[18]);
                    

                    $note = trim($value[19]);
                    if (!empty($note)) {
                        $leads_array['note'] = $note;
                    }
                    
                    
                    $old_leads = Lead::where('business_id', $business_id)->orderBy('id','desc')->first();
                    $lead_no = 1;
                    
                   
                    if(!empty($old_leads)){
                        $currentID = explode('-',$old_leads->lead_no);
                        
                        
                        if(!empty($currentID) && sizeof($currentID) > 1){
                            $lead_no = (int) $currentID[1]+1;
                        }else{
                            $lead_no = 1;
                        }
                        
                    }else{
                        $lead_no = 1;
                    }
                    
                    
                    $leads_array['lead_no'] = date('Y')."-".$lead_no;
                    
                    
                    if (!$is_valid) {
                        throw new \Exception($error_msg);
                    }
                    
                    
                    
                    

                    Lead::create($leads_array);
                }
            }

            DB::commit();


            $output = [
                'success' => 1,
                'msg' => __('product.file_imported_successfully')
            ];
        } catch (\Exception $e) {
            logger($e);
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];
            return redirect('leads/import')->with('notification', $output);
        }

        return redirect('leads/leads')->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('leads::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('leads::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
