<?php

namespace Modules\Vat\Http\Controllers;

use App\Business;
use App\Media;
use Modules\Vat\Entities\VatProduct;
use Modules\Vat\Entities\VatProductVariation;
use App\TaxRate;
use Modules\Vat\Entities\VatUnit;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use Modules\Vat\Entities\VatVariation;
use App\Warranty;
use App\Store;
use Modules\Superadmin\Entities\Subscription;

use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

use App\Transaction;
use Illuminate\Routing\Controller;


class VatProductController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $productUtil;

    protected $moduleUtil;
    
    protected $transactionUtil;
    
    private $barcode_types;

    /**
     * Constructor
     *
     * @param  ProductUtils  $product
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
    
   
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        
        if (request()->ajax()) {
            
            $query = VatProduct::with(['media'])
                ->join('vat_units', 'vat_products.unit_id', '=', 'vat_units.id')
                ->leftJoin('tax_rates', 'vat_products.tax', '=', 'tax_rates.id')
                ->join('vat_variations as v', 'v.product_id', '=', 'vat_products.id')
                ->whereNull('v.deleted_at')
                ->where('vat_products.business_id', $business_id)
                ->where('vat_products.type', '!=', 'modifier');


            $products = $query->select(
                'vat_products.semi_finished',
                'vat_products.id',
                'vat_products.name as product',
                'vat_products.type',
                'vat_units.actual_name as unit',
                'tax_rates.name as tax',
                'vat_products.sku',
                'vat_products.image',
                'vat_products.is_inactive',
                DB::raw('MAX(v.sell_price_inc_tax) as max_price'),
                DB::raw('MIN(v.sell_price_inc_tax) as min_price'),
                DB::raw('MAX(v.dpp_inc_tax) as max_purchase_price'),
                DB::raw('MIN(v.dpp_inc_tax) as min_purchase_price')
                );

            $products->groupBy('vat_products.id');

            $type = request()->get('type', null);
            if (! empty($type)) {
                $products->where('vat_products.type', $type);
            }

            
            $product_id = request()->get('product_id', null);
            if (!empty($product_id)) {
                $products->where('vat_products.id', $product_id);
            }
            
            $semi_finished = request()->get('semi_finished', null);
            if (isset($semi_finished)) {
                $products->where('vat_products.semi_finished', $semi_finished);
            }

            
            $unit_id = request()->get('unit_id', null);
            if (! empty($unit_id)) {
                $products->where('vat_products.unit_id', $unit_id);
            }

            $tax_id = request()->get('tax_id', null);
            if (! empty($tax_id)) {
                $products->where('vat_products.tax', $tax_id);
            }

            $active_state = request()->get('active_state', null);
            if ($active_state == 'active') {
                $products->Active();
            }
            if ($active_state == 'inactive') {
                $products->Inactive();
            }
            

            return Datatables::of($products)
                
                ->editColumn(
                    'semi_finished',
                    function ($row) {
                        if($row->semi_finished == 0){
                            return __('messages.no');
                        }else{
                            return __('messages.yes');
                        }
                    }
                )
                ->addColumn(
                    'action',
                    function ($row) {
                        $html =
                        '<div class="btn-group"><button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">'.__('messages.actions').'<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-left" role="menu" style="overflow-y: auto !important">
                       ';

                        
                            $html .=
                            '<li><a href="'.action([\Modules\Vat\Http\Controllers\VatProductController::class, 'view'], [$row->id]).'" class="view-product"><i class="fa fa-eye"></i> '.__('messages.view').'</a></li>';
                        
                            $html .=
                            '<li><a href="'.action([\Modules\Vat\Http\Controllers\VatProductController::class, 'edit'], [$row->id]).'"><i class="glyphicon glyphicon-edit"></i> '.__('messages.edit').'</a></li>';
                        
                            $html .=
                            '<li><a href="'.action([\Modules\Vat\Http\Controllers\VatProductController::class, 'destroy'], [$row->id]).'" class="delete-product"><i class="fa fa-trash"></i> '.__('messages.delete').'</a></li>';
                        

                        if ($row->is_inactive == 1) {
                            $html .=
                            '<li><a href="'.action([\Modules\Vat\Http\Controllers\VatProductController::class, 'activate'], [$row->id]).'" class="activate-product"><i class="fas fa-check-circle"></i> '.__('lang_v1.reactivate').'</a></li>';
                        }

                        
                        if (! empty($row->media->first())) {
                            $html .=
                                '<li><a href="'.$row->media->first()->display_url.'" download="'.$row->media->first()->display_name.'"><i class="fas fa-download"></i> '.__('lang_v1.product_brochure').'</a></li>';
                        }

                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->editColumn('product', function ($row){
                    $product = $row->is_inactive == 1 ? $row->product.' <span class="label bg-gray">'.__('lang_v1.inactive').'</span>' : $row->product;

                    $product = $row->not_for_selling == 1 ? $product.' <span class="label bg-gray">'.__('lang_v1.not_for_selling').
                        '</span>' : $product;

                    return $product;
                })
                ->editColumn('image', function ($row) {
                    return '<div style="display: flex;"><img src="'.$row->image_url.'" alt="Product image" class="product-thumbnail-small"></div>';
                })
                ->editColumn('type', '@lang("lang_v1." . $type)')
                ->addColumn('mass_delete', function ($row) {
                    return  '<input type="checkbox" class="row-select" value="'.$row->id.'">';
                })
                ->addColumn(
                    'purchase_price',
                    '<div style="white-space: nowrap;">@format_currency($min_purchase_price) @if($max_purchase_price != $min_purchase_price && $type == "variable") -  @format_currency($max_purchase_price)@endif </div>'
                )
                ->addColumn(
                    'selling_price',
                    '<div style="white-space: nowrap;">@format_currency($min_price) @if($max_price != $min_price && $type == "variable") -  @format_currency($max_price)@endif </div>'
                )
                ->filterColumn('vat_products.sku', function ($query, $keyword) {
                    $query->whereHas('vat_variations', function ($q) use ($keyword) {
                        $q->where('sub_sku', 'like', "%{$keyword}%");
                    })
                    ->orWhere('vat_products.sku', 'like', "%{$keyword}%");
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        return  action([\Modules\Vat\Http\Controllers\VatProductController::class, 'view'], [$row->id]);
                    }, ])
                ->rawColumns(['action', 'image', 'mass_delete', 'product', 'selling_price', 'purchase_price'])
                ->make(true);
        }

        

        $units = VatUnit::forDropdown($business_id);

        $tax_dropdown = TaxRate::forBusinessDropdown($business_id, false);
        $taxes = $tax_dropdown['tax_rates'];

        $products = VatProduct::where('business_id', $business_id)->pluck('name', 'id');
        
        return view('vat::product.index')
            ->with(compact(
                'products',
                'units',
                'taxes'
            ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $business_id = request()->session()->get('user.business_id');


        $units = VatUnit::forDropdown($business_id, true);

        $tax_dropdown = TaxRate::forBusinessDropdown($business_id, true, true);
        $taxes = $tax_dropdown['tax_rates'];
        $tax_attributes = $tax_dropdown['attributes'];
        
        $default_profit_percent = request()->session()->get('business.default_profit_percent');

        
        //Duplicate product
        $duplicate_product = null;

        $product_types = $this->product_types();

        $common_settings = session()->get('business.common_settings');
        $warranties = Warranty::forDropdown($business_id);
        

        return view('vat::product.create')
            ->with(compact( 'units', 'taxes', 'default_profit_percent', 'tax_attributes', 'duplicate_product',  'product_types', 'common_settings', 'warranties'));
    }

    private function product_types()
    {
        //Product types also includes modifier.
        return ['single' => __('lang_v1.single')
        ];
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
            $business_id = $request->session()->get('user.business_id');
            $form_fields = ['semi_finished','name','date', 'unit_id', 'tax','sale_tax', 'type', 'sku', 'tax_type', 'product_description', ];

            $product_details = $request->only($form_fields);
            
            if ($product_details['date']) {
                $product_details['date'] = date('Y-m-d', strtotime($product_details['date']));
            }
            
            $product_details['business_id'] = $business_id;
            $product_details['created_by'] = $request->session()->get('user.id');

            $product_details['vat_claimed'] = (! empty($request->input('vat_claimed')) && $request->input('vat_claimed') == 1) ? 1 : 0;
            
            if (empty($product_details['sku'])) {
                $product_details['sku'] = ' ';
            }

            
            $product_details['semi_finished'] = $request->semi_finished;

            //upload document
            $product_details['image'] = $this->productUtil->uploadFile($request, 'image', config('constants.product_img_path'), 'image');
            $common_settings = session()->get('business.common_settings');

            $product_details['warranty_id'] = ! empty($request->input('warranty_id')) ? $request->input('warranty_id') : null;

            DB::beginTransaction();

            $product = VatProduct::create($product_details);

            if (empty(trim($request->input('sku')))) {
                $sku = $this->productUtil->generateProductSku($product->id);
                $product->sku = $sku;
                $product->save();
            }

            

            if ($product->type == 'single') {
                $this->productUtil->createSingleVatProductVariation($product->id, $product->sku, $request->input('single_dpp'), $request->input('single_dpp_inc_tax'), $request->input('profit_percent'), $request->input('single_dsp'), $request->input('single_dsp_inc_tax'));
            }

            Media::uploadMedia($product->business_id, $product, $request, 'product_brochure', true);

            DB::commit();
            $output = ['success' => 1,
                'msg' => __('product.product_added_success'),
            ];
        } catch (\Exception $e) {
            logger($e);
            
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];

            return redirect('vat-module/vat-products')->with('status', $output);
        }


        return redirect('vat-module/vat-products')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
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
        
        $tax_dropdown = TaxRate::forBusinessDropdown($business_id, true, true);
        $taxes = $tax_dropdown['tax_rates'];
        $tax_attributes = $tax_dropdown['attributes'];

        $product = VatProduct::where('business_id', $business_id)
                            ->where('id', $id)
                            ->firstOrFail();

        
        $default_profit_percent = request()->session()->get('business.default_profit_percent');

        //Get units.
        $units = VatUnit::forDropdown($business_id, true);
        
        $product_types = $this->product_types();
        $common_settings = session()->get('business.common_settings');
        $warranties = Warranty::forDropdown($business_id);
        
        return view('vat::product.edit')
                ->with(compact( 'units', 'taxes', 'tax_attributes', 'product',  'default_profit_percent',  'product_types', 'common_settings', 'warranties'));
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
        
        try {
            $business_id = $request->session()->get('user.business_id');
            $product_details = $request->only(['semi_finished','date', 'name', 'unit_id', 'tax','sale_tax', 'sku', 'tax_type',  'product_description']);

            DB::beginTransaction();

            $product = VatProduct::where('business_id', $business_id)
                                ->where('id', $id)
                                ->with(['product_variations'])
                                ->first();
            $product->semi_finished = $product_details['semi_finished'];
            $product->name = $product_details['name'];
            $product->unit_id = $product_details['unit_id'];
            $product->tax = $product_details['tax'];
            $product->sale_tax = $product_details['sale_tax'];
            $product->sku = $product_details['sku'];
            $product->tax_type = $product_details['tax_type'];
            
            if ($product_details['date']) {
                $product->date = date('Y-m-d', strtotime($product_details['date']));
            }

            $product->product_description = $product_details['product_description'];
            
            $product->warranty_id = ! empty($request->input('warranty_id')) ? $request->input('warranty_id') : null;
            
            
            if (! empty($request->input('vat_claimed')) && $request->input('vat_claimed') == 1) {
                $product->vat_claimed = 1;
            } else {
                $product->vat_claimed = 0;
            }


            //upload document
            $file_name = $this->productUtil->uploadFile($request, 'image', config('constants.product_img_path'), 'image');
            if (! empty($file_name)) {

                //If previous image found then remove
                if (! empty($product->image_path) && file_exists($product->image_path)) {
                    unlink($product->image_path);
                }

                $product->image = $file_name;
                //If product image is updated update woocommerce media id
                if (! empty($product->woocommerce_media_id)) {
                    $product->woocommerce_media_id = null;
                }
            }

            $product->save();
            $product->touch();

            

            if ($product->type == 'single') {
                $single_data = $request->only(['single_variation_id', 'single_dpp', 'single_dpp_inc_tax', 'single_dsp_inc_tax', 'profit_percent', 'single_dsp']);
                $variation = VatVariation::find($single_data['single_variation_id']);

                $variation->sub_sku = $product->sku;
                $variation->default_purchase_price = $this->productUtil->num_uf($single_data['single_dpp']);
                $variation->dpp_inc_tax = $this->productUtil->num_uf($single_data['single_dpp_inc_tax']);
                $variation->profit_percent = $this->productUtil->num_uf($single_data['profit_percent']);
                $variation->default_sell_price = $this->productUtil->num_uf($single_data['single_dsp']);
                $variation->sell_price_inc_tax = $this->productUtil->num_uf($single_data['single_dsp_inc_tax']);
                $variation->save();

                Media::uploadMedia($product->business_id, $variation, $request, 'variation_images');
            } 

            

            Media::uploadMedia($product->business_id, $product, $request, 'product_brochure', true);

            DB::commit();
            $output = ['success' => 1,
                'msg' => __('product.product_updated_success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => $e->getMessage(),
            ];
        }

        

        return redirect('vat-module/vat-products')->with('status', $output);
    }
    
    
    public function getProductVariationFormPart(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $business = Business::findorfail($business_id);
        $profit_percent = $business->default_profit_percent;

        $action = $request->input('action');
        $product_id = $request->input('product_id');
        $action = $request->input('action');
        if ($request->input('type') == 'single') {
            $product_deatails = VatProductVariation::where('product_id', $product_id)
                ->with(['variations', 'variations.media'])
                ->first();

            return view('vat::product.partials.edit_single_product_form_part')
                        ->with(compact('product_deatails', 'action'));
        } 
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                $can_be_deleted = true;
                $error_msg = '';

                
                $product = VatProduct::where('id', $id)
                                ->where('business_id', $business_id)
                                ->with('variations')
                                ->first();

                
                    if (! empty($product)) {
                        DB::beginTransaction();
                        
                        $var = VatVariation::where('product_id', $id)->first();
                                                
                        $product->delete();

                        DB::commit();
                    }

                    $output = ['success' => true,
                        'msg' => __('lang_v1.product_delete_success'),
                    ];
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }
    
   
    public function getProducts()
    {
        if (request()->ajax()) {
            
            $search_term = request()->input('term', '');
            $location_id = request()->input('location_id', null);
            $check_qty = request()->input('check_qty', false);
            $price_group_id = request()->input('price_group', null);
            $business_id = request()->session()->get('user.business_id');
            $not_for_selling = request()->get('not_for_selling', null);
            $price_group_id = request()->input('price_group', '');
            $product_types = request()->get('product_types', []);

            $search_fields = request()->get('search_fields', ['name', 'sku']);
            if (in_array('sku', $search_fields)) {
                $search_fields[] = 'sub_sku';
            }
            
            $result = [];
            
            if (strlen($search_term) > 2) {
                $result = $this->productUtil->filterVatProduct($business_id, $search_term, $search_fields);
            } else {
                if (is_numeric($search_term)) {
                    $result = $this->productUtil->filterVatProduct($business_id, $search_term, $search_fields);
                }
            }

            
            return json_encode($result);
        }
    }
    
    
    
    public function checkProductSku(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $sku = $request->input('sku');
        $product_id = $request->input('product_id');

        //check in products table
        $query = VatProduct::where('business_id', $business_id)
                        ->where('sku', $sku);
        if (! empty($product_id)) {
            $query->where('id', '!=', $product_id);
        }
        $count = $query->count();

        //check in variation table if $count = 0
        if ($count == 0) {
            $query2 = VatVariation::where('sub_sku', $sku)
                            ->join('products', 'vat_variations.product_id', '=', 'vat_products.id')
                            ->where('business_id', $business_id);

            if (! empty($product_id)) {
                $query2->where('product_id', '!=', $product_id);
            }

            if (! empty($request->input('variation_id'))) {
                $query2->where('vat_variations.id', '!=', $request->input('variation_id'));
            }
            $count = $query2->count();
        }
        if ($count == 0) {
            echo 'true';
            exit;
        } else {
            echo 'false';
            exit;
        }
    }

    /**
     * Validates multiple variation skus
     */
    public function validateVaritionSkus(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $all_skus = $request->input('skus');

        $skus = [];
        foreach ($all_skus as $key => $value) {
            $skus[] = $value['sku'];
        }

        //check product table is sku present
        $product = VatProduct::where('business_id', $business_id)
                        ->whereIn('sku', $skus)
                        ->first();

        if (! empty($product)) {
            return ['success' => 0, 'sku' => $product->sku];
        }

        foreach ($all_skus as $key => $value) {
            $query = VatVariation::where('sub_sku', $value['sku'])
                            ->join('products', 'vat_variations.product_id', '=', 'vat_products.id')
                            ->where('business_id', $business_id);

            if (! empty($value['variation_id'])) {
                $query->where('vat_variations.id', '!=', $value['variation_id']);
            }
            $variation = $query->first();

            if (! empty($variation)) {
                return ['success' => 0, 'sku' => $variation->sub_sku];
            }
        }

        return ['success' => 1];
    }

    
    public function view($id)
    {
        
        try {
            $business_id = request()->session()->get('user.business_id');

            $product = VatProduct::where('business_id', $business_id)
                        ->with([ 'unit', 'product_tax', 'variations', 'variations.product_variation','variations.media',  'warranty', 'media'])
                        ->findOrFail($id);

            

            return view('vat::product.view-modal')->with(compact(
                'product'
            ));
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
        }
    }

    /**
     * Mass deletes products.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request)
    {
        
        try {
            
            if (! empty($request->input('selected_rows'))) {
                $business_id = $request->session()->get('user.business_id');

                $selected_rows = explode(',', $request->input('selected_rows'));
                $products = VatProduct::where('business_id', $business_id)
                                    ->whereIn('id', $selected_rows)
                                    ->with(['purchase_lines', 'variations'])
                                    ->get();

                DB::beginTransaction();

                foreach ($products as $product) {
                    $product->delete();
                }

                DB::commit();
            }

            $output = ['success' => 1,
                    'msg' => __('lang_v1.deleted_success'),
                ];
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return redirect()->back()->with(['status' => $output]);
    }

    
    /**
     * Mass deactivates products.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDeactivate(Request $request)
    {
        try {
            if (! empty($request->input('selected_products'))) {
                $business_id = $request->session()->get('user.business_id');

                $selected_products = explode(',', $request->input('selected_products'));

                DB::beginTransaction();

                $products = VatProduct::where('business_id', $business_id)
                                    ->whereIn('id', $selected_products)
                                    ->update(['is_inactive' => 1]);

                DB::commit();
            }

            $output = ['success' => 1,
                'msg' => __('lang_v1.products_deactivated_success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Activates the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function activate($id)
    {
        
        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');
                $product = VatProduct::where('id', $id)
                                ->where('business_id', $business_id)
                                ->update(['is_inactive' => 0]);

                $output = ['success' => true,
                    'msg' => __('lang_v1.updated_success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    /**
     * Deletes a media file from storage and database.
     *
     * @param  int  $media_id
     * @return json
     */
    public function deleteMedia($media_id)
    {
        
        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                Media::deleteMedia($business_id, $media_id);

                $output = ['success' => true,
                    'msg' => __('lang_v1.file_deleted_successfully'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }


}
