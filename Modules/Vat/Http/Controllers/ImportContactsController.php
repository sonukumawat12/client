<?php

namespace Modules\Vat\Http\Controllers;

use App\Account;
use App\AccountTransaction;
use App\Brands;
use App\Business;
use App\BusinessLocation;
use App\Category;
use App\Product;
use App\TaxRate;
use App\Transaction;
use App\Unit;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Variation;
use App\VariationValueTemplate;
;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Store;
use App\Variation_store_detail;
use App\ProductVariation;
use Illuminate\Routing\Controller;

use Modules\Vat\Entities\VatContact;

class ImportContactsController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $moduleUtil;
    protected $transactionUtil;

    private $barcode_types;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, ModuleUtil $moduleUtil, TransactionUtil $transactionUtil)
    {
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;

        //barcode types
        $this->barcode_types = $this->productUtil->barcode_types();
    }

    /**
     * Display import product screen.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        $zip_loaded = extension_loaded('zip') ? true : false;

        //Check if zip extension it loaded or not.
        if ($zip_loaded === false) {
            $output = [
                'success' => 0,
                'msg' => 'Please install/enable PHP Zip archive for import'
            ];

            return view('vat::import_contacts.index')
                ->with('notification', $output);
        } else {
            return view('vat::import_contacts.index');
        }
    }

    /**
     * Imports the uploaded file to database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {

            
            //Set maximum php execution time
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);

            if ($request->hasFile('products_csv')) {
                $file = $request->file('products_csv');

                $parsed_array = Excel::toArray([], $file);

                //Remove header row
                $imported_data = array_splice($parsed_array[0], 1);

                $business_id = $request->session()->get('user.business_id');
                $user_id = $request->session()->get('user.id');
                
                $formated_data = [];

                $is_valid = true;
                $error_msg = '';

                $total_rows = count($imported_data);
                
                // dd($imported_data);

                DB::beginTransaction();
                // $remove_last_row = array_pop($imported_data);
                foreach ($imported_data as $key => $value) {

                    //Check if any column is missing
                    if (count($value) < 6) {
                        $is_valid =  false;
                        $error_msg = "Some of the columns are missing. Please, use latest CSV file template.";
                        break;
                    }

                    $row_no = $key + 1;
                    $product_array = [];
                    $product_array['business_id'] = $business_id;
                    $product_array['created_by'] = $user_id;

                    //Add name
                    $contact_name = trim($value[0]);
                    if (!empty($contact_name)) {
                        $product_array['name'] = $contact_name;
                    } else {
                        $is_valid =  false;
                        $error_msg = "Contact name is required in row no. $row_no";
                        break;
                    }

                    
                    $contact_type = strtolower(trim($value[1]));
                    
                    // dd($value);
                    
                    if (in_array($contact_type, ['customer', 'supplier'])) {
                        $product_array['type'] = $contact_type;
                    } else {
                        $is_valid =  false;
                        $error_msg = "Invalid value for CONTACT TYPE in row no. $row_no";
                        break;
                    }
                    
                    $contact_id = trim($value[2]);
                    if(empty($contact_id)){
                        $contact_id = $this->check_vat_customer_code($business_id);
                    }
                    $product_array['contact_id']= $contact_id;
                    
                    $product_array['mobile']= trim($value[3]);
                    $product_array['alternate_number']= trim($value[4]);
                    $product_array['vat_no']= trim($value[5]);

                    
                    
                    //Assign to formated array
                    $formated_data[] = $product_array;
                }

                if (!$is_valid) {
                    throw new \Exception($error_msg);
                }
                
                // dd($formated_data);

                if (!empty($formated_data)) {
                    foreach ($formated_data as $index => $product_data) {
                        $product = VatContact::create($product_data);
                    }
                }
            }

           

            $output = [
                'success' => 1,
                'msg' => __('product.file_imported_successfully')
            ];

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];
            return redirect('vat-module/import-contacts')->with('notification', $output);
        }

        return redirect('vat-module/import-contacts')->with('status', $output);
    }

    public function check_vat_customer_code($business_id)
    {
        $ref_no_prefixes = request()->session()->get('business.ref_no_prefixes');
        $ref_no_starting_number = request()->session()->get('business.ref_no_starting_number');
        $prefix =   $ref_no_prefixes['contacts'];
        $starting_number =   $ref_no_starting_number['contacts'];
        $contact_id = '';
        $latest = VatContact::where('business_id', $business_id)->get()->last();
        if(empty($latest)){
            $next_number = $starting_number;
        }else{
            $next_number = (int) (explode('-',$latest->contact_id)[1]) + 1;
        }
        
        $next_number =  str_pad($next_number, 4, 0, STR_PAD_LEFT);
        $contact_id =  $prefix . '-' . $next_number . '-' . $business_id;

        return $contact_id;
    }
}
