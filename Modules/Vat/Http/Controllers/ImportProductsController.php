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
use Modules\Vat\Entities\VatProduct;
use Modules\Vat\Entities\VatProductVariation;
use Modules\Vat\Entities\VatVariation;
use Modules\Vat\Entities\VatUnit;

class ImportProductsController extends Controller
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

            return view('vat::import_products.index')
                ->with('notification', $output);
        } else {
            return view('vat::import_products.index');
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

            $notAllowed = $this->productUtil->notAllowedInDemo();
            if (!empty($notAllowed)) {
                return $notAllowed;
            }

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
                $default_profit_percent = $request->session()->get('business.default_profit_percent');

                $formated_data = [];

                $is_valid = true;
                $error_msg = '';

                $total_rows = count($imported_data);

                DB::beginTransaction();
                // $remove_last_row = array_pop($imported_data);
                foreach ($imported_data as $key => $value) {

                    //Check if any column is missing
                    if (count($value) < 15) {
                        $is_valid =  false;
                        $error_msg = "Some of the columns are missing. Please, use latest CSV file template.";
                        break;
                    }

                    $row_no = $key + 1;
                    $product_array = [];
                    $product_array['business_id'] = $business_id;
                    $product_array['created_by'] = $user_id;

                    //Add name
                    $product_name = trim($value[0]);
                    if (!empty($product_name)) {
                        $product_array['name'] = $product_name;
                    } else {
                        $is_valid =  false;
                        $error_msg = "Product name is required in row no. $row_no";
                        break;
                    }

                    //image name
                    $image_name = trim($value[12]);
                    if (!empty($image_name)) {
                        $product_array['image'] = $image_name;
                    } else {
                        $product_array['image'] = '';
                    }

                    $product_array['product_description'] = isset($value[13]) ? $value[13] : null;

                    


                    //Add product type
                    $product_type = strtolower(trim($value[5]));
                    
                    // dd($value);
                    
                    if (in_array($product_type, ['single', 'variable'])) {
                        $product_array['type'] = $product_type;
                    } else {
                        $is_valid =  false;
                        $error_msg = "Invalid value for PRODUCT TYPE in row no. $row_no";
                        break;
                    }

                    //Add unit
                    $unit_name = trim($value[1]);
                    if (!empty($unit_name)) {
                        $unit = VatUnit::where('business_id', $business_id)
                            ->where(function ($query) use ($unit_name) {
                                $query->where('actual_name', $unit_name);
                            })->first();
                        if (!empty($unit)) {
                            $product_array['unit_id'] = $unit->id;
                        } else {
                            $is_valid = false;
                            $error_msg = "UNIT not found in row no. $row_no";
                            break;
                        }
                    } else {
                        $is_valid =  false;
                        $error_msg = "UNIT is required in row no. $row_no";
                        break;
                    }

                    

                    //Add Tax
                    $tax_name = trim($value[3]);
                    $tax_amount = 0;
                    if (!empty($tax_name)) {
                        $tax = TaxRate::where('business_id', $business_id)
                            ->where('name', $tax_name)
                            ->first();
                        if (!empty($tax)) {
                            $product_array['tax'] = $tax->id;
                            $tax_amount = $tax->amount;
                        } else {
                            $is_valid = false;
                            $error_msg = "Invalid value for APPLICABLE TAX in row no. $row_no";
                            break;
                        }
                    }

                    //Add tax type
                    $tax_type = strtolower(trim($value[4]));
                    if (in_array($tax_type, ['inclusive', 'exclusive'])) {
                        $product_array['tax_type'] = $tax_type;
                    } else {
                        $is_valid = false;
                        $error_msg = "Invalid value for Selling Price Tax Type in row no. $row_no";
                        break;
                    }

                    
                    //Add SKU
                    $sku = trim($value[2]);
                    if (!empty($sku)) {
                        $product_array['sku'] = $sku;
                        //Check if product with same SKU already exist
                        $is_exist = VatProduct::where('sku', $product_array['sku'])
                            ->where('business_id', $business_id)
                            ->exists();
                        if ($is_exist) {
                            $is_valid = false;
                            $error_msg = "$sku SKU already exist in row no. $row_no";
                            break;
                        }
                    } else {
                        $product_array['sku'] = ' ';
                    }

                   

                    if ($product_array['type'] == 'single') {
                        //Calculate profit margin
                        $profit_margin = trim($value[10]);
                        if (empty($profit_margin)) {
                            $profit_margin = $default_profit_percent;
                        } else {
                            $profit_margin = trim($value[10]);
                        }
                        $product_array['variation']['profit_percent'] = $profit_margin;

                        //Calculate purchase price
                        $dpp_inc_tax = trim($value[8]);
                        $dpp_exc_tax = trim($value[9]);
                        if ($dpp_inc_tax == '' && $dpp_exc_tax == '') {
                            $is_valid = false;
                            $error_msg = "PURCHASE PRICE is required in row no. $row_no";
                            break;
                        } else {
                            $dpp_inc_tax = ($dpp_inc_tax != '') ? $dpp_inc_tax : 0;
                            $dpp_exc_tax = ($dpp_exc_tax != '') ? $dpp_exc_tax : 0;
                        }

                        //Calculate Selling price
                        $selling_price = !empty(trim($value[11])) ? trim($value[11]) : 0;

                        //Calculate product prices
                        $product_prices = $this->calculateVariationPrices($dpp_exc_tax, $dpp_inc_tax, $selling_price, $tax_amount, $tax_type, $profit_margin);

                        //Assign Values
                        $product_array['variation']['dpp_inc_tax'] = $product_prices['dpp_inc_tax'];
                        $product_array['variation']['dpp_exc_tax'] = $product_prices['dpp_exc_tax'];
                        $product_array['variation']['dsp_inc_tax'] = $product_prices['dsp_inc_tax'];
                        $product_array['variation']['dsp_exc_tax'] = $product_prices['dsp_exc_tax'];

                    } elseif ($product_array['type'] == 'variable') {
                        $variation_name = trim($value[6]);
                        if (empty($variation_name)) {
                            $is_valid = false;
                            $error_msg = "VARIATION NAME is required in row no. $row_no";
                            break;
                        }
                        $variation_values_string = trim($value[7]);
                        if (empty($variation_values_string)) {
                            $is_valid = false;
                            $error_msg = "VARIATION VALUES are required in row no. $row_no";
                            break;
                        }

                        $dpp_inc_tax_string = trim($value[8]);
                        $dpp_exc_tax_string = trim($value[9]);
                        $selling_price_string = trim($value[11]);
                        $profit_margin_string = trim($value[10]);

                        if (empty($dpp_inc_tax_string) && empty($dpp_exc_tax_string)) {
                            $is_valid = false;
                            $error_msg = "PURCHASE PRICE is required in row no. $row_no";
                            break;
                        }

                        //Variation values
                        $variation_values = array_map('trim', explode(
                            '|',
                            $variation_values_string
                        ));

                        //Map Purchase price with variation values
                        $dpp_inc_tax = [];
                        if (!empty($dpp_inc_tax_string)) {
                            $dpp_inc_tax = array_map('trim', explode(
                                '|',
                                $dpp_inc_tax_string
                            ));
                        } else {
                            foreach ($variation_values as $k => $v) {
                                $dpp_inc_tax[$k] = 0;
                            }
                        }

                        $dpp_exc_tax = [];
                        if (!empty($dpp_exc_tax_string)) {
                            $dpp_exc_tax = array_map('trim', explode(
                                '|',
                                $dpp_exc_tax_string
                            ));
                        } else {
                            foreach ($variation_values as $k => $v) {
                                $dpp_exc_tax[$k] = 0;
                            }
                        }

                        //Map Selling price with variation values
                        $selling_price = [];
                        if (!empty($selling_price_string)) {
                            $selling_price = array_map('trim', explode(
                                '|',
                                $selling_price_string
                            ));
                        } else {
                            foreach ($variation_values as $k => $v) {
                                $selling_price[$k] = 0;
                            }
                        }

                        //Map profit margin with variation values
                        $profit_margin = [];
                        if (!empty($profit_margin_string)) {
                            $profit_margin = array_map('trim', explode(
                                '|',
                                $profit_margin_string
                            ));
                        } else {
                            foreach ($variation_values as $k => $v) {
                                $profit_margin[$k] = $default_profit_percent;
                            }
                        }

                        //Check if length of prices array is equal to variation values array length
                        $array_lengths_count = [count($variation_values), count($dpp_inc_tax), count($dpp_exc_tax), count($selling_price), count($profit_margin)];
                        $same = array_count_values($array_lengths_count);

                        if (count($same) != 1) {
                            $is_valid = false;
                            $error_msg = "Prices mismatched with VARIATION VALUES in row no. $row_no";
                            break;
                        }
                        $product_array['variation']['name'] = $variation_name;

                        //Check if variation exists or create new
                        $variation = $this->productUtil->createOrNewVariation($business_id, $variation_name);
                        $product_array['variation']['variation_template_id'] = $variation->id;

                        foreach ($variation_values as $k => $v) {
                            $variation_prices = $this->calculateVariationPrices($dpp_exc_tax[$k], $dpp_inc_tax[$k], $selling_price[$k], $tax_amount, $tax_type, $profit_margin[$k]);

                            //Assign Values
                            $product_array['variation']['variations'][] = [
                                'value' => $v,
                                'default_purchase_price' => $variation_prices['dpp_exc_tax'],
                                'dpp_inc_tax' => $variation_prices['dpp_inc_tax'],
                                'profit_percent' => $this->productUtil->num_f($profit_margin[$k]),
                                'default_sell_price' => $variation_prices['dsp_exc_tax'],
                                'sell_price_inc_tax' => $variation_prices['dsp_inc_tax']
                            ];
                        }

                    }
                    //Assign to formated array
                    $formated_data[] = $product_array;
                }

                if (!$is_valid) {
                    throw new \Exception($error_msg);
                }

                if (!empty($formated_data)) {
                    foreach ($formated_data as $index => $product_data) {
                        $variation_data = $product_data['variation'];
                        unset($product_data['variation']);
                        $transaction_date = date('Y-m-d');
                       

                        //Create new product
                        $product = VatProduct::create($product_data);
                        //If auto generate sku generate new sku
                        if ($product->sku == ' ') {
                            $sku = $this->productUtil->generateProductSku($product->id);
                            $product->sku = $sku;
                            $product->save();
                        }

                        //Create single product variation
                        if ($product->type == 'single') {
                            
                            $this->productUtil->createSingleVatProductVariation(
                                $product,
                                $product->sku,
                                $variation_data['dpp_exc_tax'],
                                $variation_data['dpp_inc_tax'],
                                $variation_data['profit_percent'],
                                $variation_data['dsp_exc_tax'],
                                $variation_data['dsp_inc_tax']
                            );
                            
                        } 
                        
                        
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
            return redirect('vat-module/import-products')->with('notification', $output);
        }

        return redirect('vat-module/import-products')->with('status', $output);
    }

    private function calculateVariationPrices($dpp_exc_tax, $dpp_inc_tax, $selling_price, $tax_amount, $tax_type, $margin)
    {

        //Calculate purchase prices
        if ($dpp_inc_tax == 0) {
            $dpp_inc_tax = $this->productUtil->calc_percentage(
                $dpp_exc_tax,
                $tax_amount,
                $dpp_exc_tax
            );
        }

        if ($dpp_exc_tax == 0) {
            $dpp_exc_tax = $this->productUtil->calc_percentage_base($dpp_inc_tax, $tax_amount);
        }

        if ($selling_price != 0) {
            if ($tax_type == 'inclusive') {
                $dsp_inc_tax = $selling_price;
                $dsp_exc_tax = $this->productUtil->calc_percentage_base(
                    $dsp_inc_tax,
                    $tax_amount
                );
            } elseif ($tax_type == 'exclusive') {
                $dsp_exc_tax = $selling_price;
                $dsp_inc_tax = $this->productUtil->calc_percentage(
                    $selling_price,
                    $tax_amount,
                    $selling_price
                );
            }
        } else {
            $dsp_exc_tax = $this->productUtil->calc_percentage(
                $dpp_exc_tax,
                $margin,
                $dpp_exc_tax
            );
            $dsp_inc_tax = $this->productUtil->calc_percentage(
                $dsp_exc_tax,
                $tax_amount,
                $dsp_exc_tax
            );
        }

        return [
            'dpp_exc_tax' => $dpp_exc_tax,
            'dpp_inc_tax' => $dpp_inc_tax,
            'dsp_exc_tax' => $dsp_exc_tax,
            'dsp_inc_tax' => $dsp_inc_tax
        ];
    }

    /**
     * Adds opening stock of a single product
     *
     * @param array $opening_stock
     * @param obj $product
     * @param int $business_id
     * @return obj $product
     */
   
}
