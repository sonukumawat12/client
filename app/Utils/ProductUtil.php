<?php
namespace App\Utils;
use App\Business;
use App\BusinessLocation;
use App\Category;
use App\Discount;
use App\Media;
use App\Product;
use App\ProductRack;
use App\ProductVariation;
use App\PurchaseLine;
use App\TaxRate;
use App\Transaction;
use App\TransactionSellLine;
use App\TransactionSellLinesPurchaseLines;
use App\Unit;
use App\Store;
use App\Variation;
use App\VariationGroupPrice;
use App\VariationLocationDetails;
use App\VariationTemplate;
use App\VariationValueTemplate;
use App\VariationStoreDetail;
use Illuminate\Support\Facades\DB;
use Modules\Petro\Entities\FuelTank;
use Modules\Vat\Entities\VatProduct;
class ProductUtil extends Util
{
    /**
     * Create single type product variation
     *
     * @param (int or object) $product
     * @param $sku
     * @param $purchase_price
     * @param $dpp_inc_tax (default purchase pric including tax)
     * @param $profit_percent
     * @param $selling_price
     * @param $combo_variations = []
     *
     * @return boolean
     */
    
    public $deactivated_in = array(
        'ezyinvoice_invoices',
        'petro_settlements',
        'billtocustomer_issuecustomerbills',
        'billtocustomer_issuecustomerbillsvat',
        'pricechange_pricechanges',
        'stocktaking_stocktaking',
        'production_production_process',
        'production_production_addproduction',
        'purchases_addpurchases',
        'purchases_listpurchasereturn_add',
        'sales_addsales',
        'sales_pos',
        'sales_listquotations_add',
        'sales_listorders_add',
        'sales_listsalereturns_add',
        'tpos_add',
        'tpos_fpos',
        'repair_add',
        'autorepair_add',
        'stocktransfer_add',
        'stockadjustment_add',
        'vat_vatinvoice',
        'vat_vatinvoice2',
        'vat_vatinvoice127',
        'production_stockreport',
        'production_itemreport',
        'reports_stockreport',
        'reports_stocksummary',
        'reports_itemreport',
        'dailysummary_stocksummary_qty',
        'dailysummary_stocksummary_value'
    );
    
    public function createSingleProductVariation($product, $sku, $purchase_price, $dpp_inc_tax, $profit_percent, $selling_price, $selling_price_inc_tax, $combo_variations = [], $multiple_unit_price = null)
    {
        if (!is_object($product)) {
            $product = Product::find($product);
        }
        //create product variations
        $product_variation_data = [
            'name' => 'DUMMY',
            'is_dummy' => 1
        ];
        $product_variation = $product->product_variations()->create($product_variation_data);
        //create variations
        $variation_data = [
            'name' => 'DUMMY',
            'product_id' => $product->id,
            'sub_sku' => $sku,
            'default_purchase_price' => $this->num_uf($purchase_price),
            'dpp_inc_tax' => $this->num_uf($dpp_inc_tax),
            'profit_percent' => $this->num_uf($profit_percent),
            'default_sell_price' => $this->num_uf($selling_price),
            'sell_price_inc_tax' => $this->num_uf($selling_price_inc_tax),
            'combo_variations' => $combo_variations,
            'default_multiple_unit_price' => $multiple_unit_price
        ];
        $variation = $product_variation->variations()->create($variation_data);
        Media::uploadMedia($product->business_id, $variation, request(), 'variation_images');
        return true;
    }
    
    public function createSingleVatProductVariation($product, $sku, $purchase_price, $dpp_inc_tax, $profit_percent, $selling_price, $selling_price_inc_tax, $combo_variations = [], $multiple_unit_price = null)
    {
        if (!is_object($product)) {
            $product = VatProduct::find($product);
        }
        //create product variations
        $product_variation_data = [
            'name' => 'DUMMY',
            'is_dummy' => 1
        ];
        $product_variation = $product->product_variations()->create($product_variation_data);
        //create variations
        $variation_data = [
            'name' => 'DUMMY',
            'product_id' => $product->id,
            'sub_sku' => $sku,
            'default_purchase_price' => $this->num_uf($purchase_price),
            'dpp_inc_tax' => $this->num_uf($dpp_inc_tax),
            'profit_percent' => $this->num_uf($profit_percent),
            'default_sell_price' => $this->num_uf($selling_price),
            'sell_price_inc_tax' => $this->num_uf($selling_price_inc_tax),
            'combo_variations' => $combo_variations,
            'default_multiple_unit_price' => $multiple_unit_price
        ];
        $variation = $product_variation->variations()->create($variation_data);
        Media::uploadMedia($product->business_id, $variation, request(), 'variation_images');
        return true;
    }
    
    public function filterVatProduct($business_id, $search_term, $search_fields = [])
    {
        
        $query = VatProduct::join('vat_variations', 'vat_products.id', '=', 'vat_variations.product_id')
                ->active()
                ->whereNull('vat_variations.deleted_at')
                ->leftjoin('vat_units as U', 'vat_products.unit_id', '=', 'U.id')
                ->where('vat_products.business_id', $business_id)
                ->where('vat_products.type', '!=', 'modifier');

        //Include search
        if (! empty($search_term)) {
            $query->where(function ($query) use ($search_term, $search_fields) {
                if (in_array('name', $search_fields)) {
                    $query->where('vat_products.name', 'like', '%'.$search_term.'%');
                }

                if (in_array('sku', $search_fields)) {
                    $query->orWhere('sku', 'like', '%'.$search_term.'%');
                }
                
                if (in_array('sub_sku', $search_fields)) {
                    $query->orWhere('sub_sku', 'like', '%'.$search_term.'%');
                }

                
            });
            
        }

        
        $query->select(
                'vat_products.id as product_id',
                'vat_products.name',
                'vat_products.type',
                'vat_variations.id as variation_id',
                'vat_variations.name as variation',
                'vat_variations.sell_price_inc_tax as selling_price',
                'vat_variations.sub_sku',
                'U.actual_name as unit'
            );


        $query->groupBy('vat_variations.id');

        return $query->get();
    }
    
     public function filterProduct($business_id, $search_term, $location_id = null, $not_for_selling = null, $price_group_id = null, $product_types = [], $search_fields = [], $check_qty = false, $search_type = 'like',$module = null)
    {
        
        $query = Product::join('variations', 'products.id', '=', 'variations.product_id')
                ->active()
                ->whereNull('variations.deleted_at')
                ->leftjoin('units as U', 'products.unit_id', '=', 'U.id')
                ->leftjoin(
                    'variation_location_details AS VLD',
                    function ($join) use ($location_id) {
                        $join->on('variations.id', '=', 'VLD.variation_id');

                        //Include Location
                        if (! empty($location_id)) {
                            $join->where(function ($query) use ($location_id) {
                                $query->where('VLD.location_id', '=', $location_id);
                                //Check null to show products even if no quantity is available in a location.
                                //TODO: Maybe add a settings to show product not available at a location or not.
                                $query->orWhereNull('VLD.location_id');
                            });
                        }
                    }
                );
        if(!empty($module)){
            $query->forModule($module);
        }

        if (! is_null($not_for_selling)) {
            $query->where('products.not_for_selling', $not_for_selling);
        }

        if (! empty($price_group_id)) {
            $query->leftjoin(
                'variation_group_prices AS VGP',
                function ($join) use ($price_group_id) {
                    $join->on('variations.id', '=', 'VGP.variation_id')
                        ->where('VGP.price_group_id', '=', $price_group_id);
                }
            );
        }

        $query->where('products.business_id', $business_id)
                ->where('products.type', '!=', 'modifier');

        if (! empty($product_types)) {
            $query->whereIn('products.type', $product_types);
        }

        if (in_array('lot', $search_fields)) {
            $query->leftjoin('purchase_lines as pl', 'variations.id', '=', 'pl.variation_id');
        }

        //Include search
        if (! empty($search_term)) {
            
            if (strlen($search_term) < 2 && is_numeric($search_term)) {
                
                //Search with like condition
                if ($search_type == 'like') {
                    $query->where(function ($query) use ($search_term, $search_fields) {
    
                        if (in_array('sku', $search_fields)) {
                            $query->orWhere('sku', 'like', '%'.$search_term.'%');
                        }
                    });
                }
    
                //Search with exact condition
                if ($search_type == 'exact') {
                    $query->where(function ($query) use ($search_term, $search_fields) {
                        
                        if (in_array('sku', $search_fields)) {
                            $query->orWhere('sku', $search_term);
                        }
                    });
                }
            
            }else{
                
                //Search with like condition
                if ($search_type == 'like') {
                    $query->where(function ($query) use ($search_term, $search_fields) {
                        if (in_array('name', $search_fields)) {
                            $query->where('products.name', 'like', '%'.$search_term.'%');
                        }
    
                        if (in_array('sku', $search_fields)) {
                            $query->orWhere('sku', 'like', '%'.$search_term.'%');
                        }
    
                        if (in_array('sub_sku', $search_fields)) {
                            $query->orWhere('sub_sku', 'like', '%'.$search_term.'%');
                        }
    
                        if (in_array('lot', $search_fields)) {
                            $query->orWhere('pl.lot_number', 'like', '%'.$search_term.'%');
                        }
    
                        if (in_array('product_custom_field1', $search_fields)) {
                            $query->orWhere('product_custom_field1', 'like', '%'.$search_term.'%');
                        }
                        if (in_array('product_custom_field2', $search_fields)) {
                            $query->orWhere('product_custom_field2', 'like', '%'.$search_term.'%');
                        }
                        if (in_array('product_custom_field3', $search_fields)) {
                            $query->orWhere('product_custom_field3', 'like', '%'.$search_term.'%');
                        }
                        if (in_array('product_custom_field4', $search_fields)) {
                            $query->orWhere('product_custom_field4', 'like', '%'.$search_term.'%');
                        }
                    });
                }
    
                //Search with exact condition
                if ($search_type == 'exact') {
                    $query->where(function ($query) use ($search_term, $search_fields) {
                        if (in_array('name', $search_fields)) {
                            $query->where('products.name', $search_term);
                        }
    
                        if (in_array('sku', $search_fields)) {
                            $query->orWhere('sku', $search_term);
                        }
    
                        if (in_array('sub_sku', $search_fields)) {
                            $query->orWhere('sub_sku', $search_term);
                        }
    
                        if (in_array('lot', $search_fields)) {
                            $query->orWhere('pl.lot_number', $search_term);
                        }
                    });
                }
                
            }

            
        }

        //Include check for quantity
        if ($check_qty) {
            $query->where('VLD.qty_available', '>', 0);
        }

        if (! empty($location_id)) {
            $query->ForLocation($location_id);
        }

        $query->select(
                'products.id as product_id',
                'products.name',
                'products.type',
                'products.enable_stock',
                'variations.id as variation_id',
                'variations.name as variation',
                'VLD.qty_available',
                'variations.sell_price_inc_tax as selling_price',
                'variations.sub_sku',
                'U.short_name as unit'
            );

        if (! empty($price_group_id)) {
            $query->addSelect(DB::raw('IF (VGP.price_type = "fixed", VGP.price_inc_tax, VGP.price_inc_tax * variations.sell_price_inc_tax / 100) as variation_group_price'));
        }

        if (in_array('lot', $search_fields)) {
            $query->addSelect('pl.id as purchase_line_id', 'pl.lot_number');
        }

        $query->groupBy('variations.id');

        return $query->orderBy('VLD.qty_available', 'desc')
                        ->get();
    }
    
     public function filterProductStockAdjustment($business_id, $search_term, $location_id = null, $not_for_selling = null, $price_group_id = null, $product_types = [], $search_fields = [], $check_qty = false, $search_type = 'like', $store_id = null,$module = null)
    {
          $search_type = $search_type ?? 'like';
        
        $query = Product::join('variations', 'products.id', '=', 'variations.product_id')
                ->active()
                ->leftJoin('categories','categories.id','products.category_id')
                ->where('categories.name','!=','Fuel')
                ->whereNull('variations.deleted_at')
                ->leftjoin('units as U', 'products.unit_id', '=', 'U.id')
                ->leftjoin(
                    'variation_location_details AS VLD',
                    function ($join) use ($location_id) {
                        $join->on('variations.id', '=', 'VLD.variation_id');

                        //Include Location
                        if (! empty($location_id)) {
                            $join->where(function ($query) use ($location_id) {
                                $query->where('VLD.location_id', '=', $location_id);
                                //Check null to show products even if no quantity is available in a location.
                                //TODO: Maybe add a settings to show product not available at a location or not.
                                $query->orWhereNull('VLD.location_id');
                            });
                        }
                    }
                )
                ->leftjoin(
                    'variation_store_details AS VSD',
                    function ($join) use ($store_id) {
                        $join->on('variations.id', '=', 'VSD.variation_id');
                        if (! empty($store_id)) {
                            $join->where(function ($query) use ($store_id) {
                                $query->where('VSD.store_id', '=', $store_id);
                                $query->orWhereNull('VSD.store_id');
                            });
                        }
                    }
                );
        if(!empty($module)){
            $query->forModule($module);
        }

        if (! is_null($not_for_selling)) {
            $query->where('products.not_for_selling', $not_for_selling);
        }

        if (! empty($price_group_id)) {
            $query->leftjoin(
                'variation_group_prices AS VGP',
                function ($join) use ($price_group_id) {
                    $join->on('variations.id', '=', 'VGP.variation_id')
                        ->where('VGP.price_group_id', '=', $price_group_id);
                }
            );
        }

        $query->where('products.business_id', $business_id)
                ->where('products.type', '!=', 'modifier');

        if (! empty($product_types)) {
            $query->whereIn('products.type', $product_types);
        }

        if (in_array('lot', $search_fields)) {
            $query->leftjoin('purchase_lines as pl', 'variations.id', '=', 'pl.variation_id');
        }

        //Include search
        if (! empty($search_term)) {
            
            if (strlen($search_term) < 2 && is_numeric($search_term)) {
                
                //Search with like condition
                if ($search_type == 'like') {
                    $query->where(function ($query) use ($search_term, $search_fields) {
    
                        if (in_array('sku', $search_fields)) {
                            $query->orWhere('sku', 'like', '%'.$search_term.'%');
                        }
                    });
                }
    
                //Search with exact condition
                if ($search_type == 'exact') {
                    $query->where(function ($query) use ($search_term, $search_fields) {
                        
                        if (in_array('sku', $search_fields)) {
                            $query->orWhere('sku', $search_term);
                        }
                    });
                }
            
            }else{
                
                //Search with like condition
                if ($search_type == 'like') {
                    $query->where(function ($query) use ($search_term, $search_fields) {
                        if (in_array('name', $search_fields)) {
                            $query->where('products.name', 'like', '%'.$search_term.'%');
                        }
    
                        if (in_array('sku', $search_fields)) {
                            $query->orWhere('sku', 'like', '%'.$search_term.'%');
                        }
    
                        if (in_array('sub_sku', $search_fields)) {
                            $query->orWhere('sub_sku', 'like', '%'.$search_term.'%');
                        }
    
                        if (in_array('lot', $search_fields)) {
                            $query->orWhere('pl.lot_number', 'like', '%'.$search_term.'%');
                        }
    
                        if (in_array('product_custom_field1', $search_fields)) {
                            $query->orWhere('product_custom_field1', 'like', '%'.$search_term.'%');
                        }
                        if (in_array('product_custom_field2', $search_fields)) {
                            $query->orWhere('product_custom_field2', 'like', '%'.$search_term.'%');
                        }
                        if (in_array('product_custom_field3', $search_fields)) {
                            $query->orWhere('product_custom_field3', 'like', '%'.$search_term.'%');
                        }
                        if (in_array('product_custom_field4', $search_fields)) {
                            $query->orWhere('product_custom_field4', 'like', '%'.$search_term.'%');
                        }
                    });
                }
    
                //Search with exact condition
                if ($search_type == 'exact') {
                    $query->where(function ($query) use ($search_term, $search_fields) {
                        if (in_array('name', $search_fields)) {
                            $query->where('products.name', $search_term);
                        }
    
                        if (in_array('sku', $search_fields)) {
                            $query->orWhere('sku', $search_term);
                        }
    
                        if (in_array('sub_sku', $search_fields)) {
                            $query->orWhere('sub_sku', $search_term);
                        }
    
                        if (in_array('lot', $search_fields)) {
                            $query->orWhere('pl.lot_number', $search_term);
                        }
                    });
                }
                
            }

            
        }

        //Include check for quantity
        if ($check_qty) {
            $query->where('VLD.qty_available', '>', 0);
        }

        if (! empty($location_id)) {
            $query->ForLocation($location_id);
        }
        
        if (! empty($store_id)) {
            $query->where('VSD.store_id',$store_id);
        }

        $query->select(
                'products.id as product_id',
                'products.name',
                'products.type',
                'products.enable_stock',
                'variations.id as variation_id',
                'variations.name as variation',
                'VLD.qty_available',
                'VSD.store_id',
                'VSD.qty_available as store_qty',
                'variations.sell_price_inc_tax as selling_price',
                'variations.sub_sku',
                'U.short_name as unit'
            );

        if (! empty($price_group_id)) {
            $query->addSelect(DB::raw('IF (VGP.price_type = "fixed", VGP.price_inc_tax, VGP.price_inc_tax * variations.sell_price_inc_tax / 100) as variation_group_price'));
        }

        if (in_array('lot', $search_fields)) {
            $query->addSelect('pl.id as purchase_line_id', 'pl.lot_number');
        }

        $query->groupBy('variations.id');
        
        
        return $query->orderBy('VLD.qty_available', 'desc')
                        ->get();
    }
    
    public function filterProductPos($business_id, $search_term, $location_id = null, $not_for_selling = null, $price_group_id = null, $product_types = [], $search_fields = [], $check_qty = false, $search_type = 'like', $store_id = null,$brand_id = null,$module)
    {
        
        $search_type = $search_type ?? 'like';
        
        if(empty($store_id)){
            $store_id = request()->session()->get('business.default_store');
        }
        
        $query = Product::join('variations', 'products.id', '=', 'variations.product_id')
                ->active()
                ->whereNull('variations.deleted_at')
                ->leftjoin('units as U', 'products.unit_id', '=', 'U.id')
                ->leftjoin(
                    'variation_location_details AS VLD',
                    function ($join) use ($location_id) {
                        $join->on('variations.id', '=', 'VLD.variation_id');

                        //Include Location
                        if (! empty($location_id)) {
                            $join->where(function ($query) use ($location_id) {
                                $query->where('VLD.location_id', '=', $location_id);
                                //Check null to show products even if no quantity is available in a location.
                                //TODO: Maybe add a settings to show product not available at a location or not.
                                $query->orWhereNull('VLD.location_id');
                            });
                        }
                    }
                )
                ->leftjoin(
                    'variation_store_details AS VSD',
                    function ($join) use ($store_id) {
                        $join->on('variations.id', '=', 'VSD.variation_id');
                        //Include Location
                        if (!empty($store_id)) {
                            $join->where(function ($query) use ($store_id) {
                                $query->where('VSD.store_id', '=', $store_id);
                                //Check null to show products even if no quantity is available in a location.
                                //TODO: Maybe add a settings to show product not available at a location or not.
                                $query->orWhereNull('VSD.store_id');
                            });;
                        }
                    }
                );
                
        if(!empty($module)){
            $query->forModule($module);
        }

        if (! is_null($not_for_selling)) {
            $query->where('products.not_for_selling', $not_for_selling);
        }

        if (! empty($price_group_id)) {
            $query->leftjoin(
                'variation_group_prices AS VGP',
                function ($join) use ($price_group_id) {
                    $join->on('variations.id', '=', 'VGP.variation_id')
                        ->where('VGP.price_group_id', '=', $price_group_id);
                }
            );
        }

        $query->where('products.business_id', $business_id)
                ->where('products.type', '!=', 'modifier');

        if (! empty($product_types)) {
            $query->whereIn('products.type', $product_types);
        }
        if(!is_array($search_fields)){
            $search_fields = [];
        }
        if (in_array('lot', $search_fields)) {
            $query->leftjoin('purchase_lines as pl', 'variations.id', '=', 'pl.variation_id');
        }

        //Include search
        if (! empty($search_term)) {
            
            if (strlen($search_term) < 2 && is_numeric($search_term) && false) {
                
                //Search with like condition
                if ($search_type == 'like') {
                    $query->where(function ($query) use ($search_term, $search_fields) {
    
                        if (in_array('sku', $search_fields)) {
                            $query->orWhere('sku', 'like', '%'.$search_term.'%');
                        }
                    });
                }
    
                //Search with exact condition
                if ($search_type == 'exact') {
                    $query->where(function ($query) use ($search_term, $search_fields) {
                        
                        if (in_array('sku', $search_fields)) {
                            $query->orWhere('sku', $search_term);
                        }
                    });
                }
            
            }else{
                
                
                //Search with like condition
                if ($search_type == 'like') {
                    $query->where(function ($query) use ($search_term, $search_fields) {
                        if (in_array('name', $search_fields)) {
                            
                            $query->where('products.name', 'like', '%'.$search_term.'%');
                        }
    
                        if (in_array('sku', $search_fields)) {
                            $query->orWhere('sku', 'like', '%'.$search_term.'%');
                        }
    
                        if (in_array('sub_sku', $search_fields)) {
                            $query->orWhere('sub_sku', 'like', '%'.$search_term.'%');
                        }
    
                        if (in_array('lot', $search_fields)) {
                            $query->orWhere('pl.lot_number', 'like', '%'.$search_term.'%');
                        }
    
                        if (in_array('product_custom_field1', $search_fields)) {
                            $query->orWhere('product_custom_field1', 'like', '%'.$search_term.'%');
                        }
                        if (in_array('product_custom_field2', $search_fields)) {
                            $query->orWhere('product_custom_field2', 'like', '%'.$search_term.'%');
                        }
                        if (in_array('product_custom_field3', $search_fields)) {
                            $query->orWhere('product_custom_field3', 'like', '%'.$search_term.'%');
                        }
                        if (in_array('product_custom_field4', $search_fields)) {
                            $query->orWhere('product_custom_field4', 'like', '%'.$search_term.'%');
                        }
                    });
                }
    
                //Search with exact condition
                if ($search_type == 'exact') {
                    $query->where(function ($query) use ($search_term, $search_fields) {
                        if (in_array('name', $search_fields)) {
                            $query->where('products.name', $search_term);
                        }
    
                        if (in_array('sku', $search_fields)) {
                            $query->orWhere('sku', $search_term);
                        }
    
                        if (in_array('sub_sku', $search_fields)) {
                            $query->orWhere('sub_sku', $search_term);
                        }
    
                        if (in_array('lot', $search_fields)) {
                            $query->orWhere('pl.lot_number', $search_term);
                        }
                    });
                }
                
            }

            
        }

        //Include check for quantity
        if ($check_qty) {
            $query->where('VSD.qty_available', '>', 0);
        }
        
        if($brand_id && $brand_id != 'all'){
            $query->where('products.brand_id',$brand_id);
        }

        if (! empty($location_id)) {
            $query->ForLocation($location_id);
        }

        $query->select(
                'products.id as product_id',
                'products.name',
                'products.type',
                'products.enable_stock',
                'variations.id as variation_id',
                'variations.name as variation',
                'VSD.qty_available',
                'variations.sell_price_inc_tax as selling_price',
                'variations.sub_sku',
                'U.short_name as unit'
            );

        if (! empty($price_group_id)) {
            $query->addSelect(DB::raw('IF (VGP.price_type = "fixed", VGP.price_inc_tax, VGP.price_inc_tax * variations.sell_price_inc_tax / 100) as variation_group_price'));
        }

        if (in_array('lot', $search_fields)) {
            $query->addSelect('pl.id as purchase_line_id', 'pl.lot_number');
        }

        $query->groupBy('variations.id');

        $products =  $query->orderBy('VSD.qty_available', 'desc')
                        ->get();
        return $products;
    }

    
    /**
     * Create variable type product variation
     *
     * @param (int or object) $product
     * @param $input_variations
     *
     * @return boolean
     */
    public function createVariableProductVariations($product, $input_variations, $business_id = null)
    {
        if (!is_object($product)) {
            $product = Product::find($product);
        }
        //create product variations
        foreach ($input_variations as $key => $value) {
            $images = [];
            $variation_template_name = !empty($value['name']) ? $value['name'] : null;
            $variation_template_id = !empty($value['variation_template_id']) ? $value['variation_template_id'] : null;
            if (empty($variation_template_id)) {
                if ($variation_template_name != 'DUMMY') {
                    $variation_template = VariationTemplate::where('business_id', $business_id)
                        ->whereRaw('LOWER(name)="' . strtolower($variation_template_name) . '"')
                        ->with(['values'])
                        ->first();
                    if (empty($variation_template)) {
                        $variation_template = VariationTemplate::create([
                            'name' => $variation_template_name,
                            'business_id' => $business_id
                        ]);
                    }
                    $variation_template_id = $variation_template->id;
                }
            } else {
                $variation_template = VariationTemplate::with(['values'])->find($value['variation_template_id']);
                $variation_template_id = $variation_template->id;
                $variation_template_name = $variation_template->name;
            }
            $product_variation_data = [
                'name' => $variation_template_name,
                'product_id' => $product->id,
                'is_dummy' => 0,
                'variation_template_id' => $variation_template_id
            ];
            $product_variation = ProductVariation::create($product_variation_data);
            //create variations
            if (!empty($value['variations'])) {
                $variation_data = [];
                $c = Variation::withTrashed()
                    ->where('product_id', $product->id)
                    ->count() + 1;
                foreach ($value['variations'] as $k => $v) {
                    $sub_sku = empty($v['sub_sku']) ? $this->generateSubSku($product->sku, $c, $product->barcode_type) : $v['sub_sku'];
                    $variation_value_id = !empty($v['variation_value_id']) ? $v['variation_value_id'] : null;
                    $variation_value_name = !empty($v['value']) ? $v['value'] : null;
                    if (!empty($variation_value_id)) {
                        $variation_value = $variation_template->values->filter(function ($item) use ($variation_value_id) {
                            return $item->id == $variation_value_id;
                        })->first();
                        $variation_value_name = $variation_value->name;
                    } else {
                        if (!empty($variation_template)) {
                            $variation_value =  VariationValueTemplate::where('variation_template_id', $variation_template->id)
                                ->whereRaw('LOWER(name)="' . $variation_value_name . '"')
                                ->first();
                            if (empty($variation_value)) {
                                $variation_value =  VariationValueTemplate::create([
                                    'name' => $variation_value_name,
                                    'variation_template_id' => $variation_template->id
                                ]);
                            }
                            $variation_value_id = $variation_value->id;
                            $variation_value_name = $variation_value->name;
                        } else {
                            $variation_value_id = null;
                            $variation_value_name = $variation_value_name;
                        }
                    }
                    $variation_data[] = [
                        'name' => $variation_value_name,
                        'variation_value_id' => $variation_value_id,
                        'product_id' => $product->id,
                        'sub_sku' => $sub_sku,
                        'default_purchase_price' => !empty($v['default_purchase_price']) ? $this->num_uf($v['default_purchase_price']) : 0.00,
                        'dpp_inc_tax' =>  !empty($v['dpp_inc_tax']) ?  $this->num_uf($v['dpp_inc_tax'])  : 0.00,
                        'profit_percent' => !empty($v['profit_percent']) ? $this->num_uf($v['profit_percent']) : 0.00,
                        'default_sell_price' => $this->num_uf($v['default_sell_price']),
                        'sell_price_inc_tax' => $this->num_uf($v['sell_price_inc_tax'])
                    ];
                    $c++;
                    $images[] = 'variation_images_' . $key . '_' . $k;
                }
                $variations = $product_variation->variations()->createMany($variation_data);
                $i = 0;
                foreach ($variations as $variation) {
                    Media::uploadMedia($product->business_id, $variation, request(), $images[$i]);
                    $i++;
                }
            }
        }
    }
    /**
     * Update variable type product variation
     *
     * @param $product_id
     * @param $input_variations_edit
     *
     * @return boolean
     */
    public function updateVariableProductVariations($product_id, $input_variations_edit)
    {
        $product = Product::find($product_id);
        //Update product variations
        $product_variation_ids = [];
        foreach ($input_variations_edit as $key => $value) {
            $product_variation_ids[] = $key;
            $product_variation = ProductVariation::find($key);
            $product_variation->name = $value['name'];
            $product_variation->save();
            //Update existing variations
            $variations_ids = [];
            if (!empty($value['variations_edit'])) {
                foreach ($value['variations_edit'] as $k => $v) {
                    $data = [
                        'name' => $v['value'],
                        'default_purchase_price' => !empty($v['default_purchase_price']) ? $this->num_uf($v['default_purchase_price']) : 0.00,
                        'dpp_inc_tax' =>  !empty($v['dpp_inc_tax']) ?  $this->num_uf($v['dpp_inc_tax'])  : 0.00,
                        'profit_percent' => !empty($v['profit_percent']) ? $this->num_uf($v['profit_percent']) : 0.00,
                        'default_sell_price' => $this->num_uf($v['default_sell_price']),
                        'sell_price_inc_tax' => $this->num_uf($v['sell_price_inc_tax'])
                    ];
                    if (!empty($v['sub_sku'])) {
                        $data['sub_sku'] = $v['sub_sku'];
                    }
                    $variation = Variation::where('id', $k)
                        ->where('product_variation_id', $key)
                        ->first();
                    $variation->update($data);
                    Media::uploadMedia($product->business_id, $variation, request(), 'edit_variation_images_' . $key . '_' . $k);
                    $variations_ids[] = $k;
                }
            }
            //Check if purchase or sell exist for the deletable variations
            $count_purchase = PurchaseLine::join(
                'transactions as T',
                'purchase_lines.transaction_id',
                '=',
                'T.id'
            )
                ->where('T.type', 'purchase')
                ->where('T.status', 'received')
                ->where('T.business_id', $product->business_id)
                ->where('purchase_lines.product_id', $product->id)
                ->whereNotIn('purchase_lines.variation_id', $variations_ids)
                ->count();
            $count_sell = TransactionSellLine::join(
                'transactions as T',
                'transaction_sell_lines.transaction_id',
                '=',
                'T.id'
            )
                ->where('T.type', 'sell')
                ->where('T.status', 'final')
                ->where('T.business_id', $product->business_id)
                ->where('transaction_sell_lines.product_id', $product->id)
                ->whereNotIn('transaction_sell_lines.variation_id', $variations_ids)
                ->count();
            $is_variation_delatable = $count_purchase > 0 || $count_sell > 0 ? false : true;
            if ($is_variation_delatable) {
                Variation::whereNotIn('id', $variations_ids)
                    ->where('product_variation_id', $key)
                    ->delete();
            } else {
                throw new \Exception(__('lang_v1.purchase_already_exist'));
            }
            //Add new variations
            if (!empty($value['variations'])) {
                $variation_data = [];
                $c = Variation::withTrashed()
                    ->where('product_id', $product->id)
                    ->count() + 1;
                $media = [];
                foreach ($value['variations'] as $k => $v) {
                    $sub_sku = empty($v['sub_sku']) ? $this->generateSubSku($product->sku, $c, $product->barcode_type) : $v['sub_sku'];
                    $variation_value_name = !empty($v['value']) ? $v['value'] : null;
                    $variation_value_id = null;
                    if (!empty($product_variation->variation_template_id)) {
                        $variation_value =  VariationValueTemplate::where('variation_template_id', $product_variation->variation_template_id)
                            ->whereRaw('LOWER(name)="' . $v['value'] . '"')
                            ->first();
                        if (empty($variation_value)) {
                            $variation_value =  VariationValueTemplate::create([
                                'name' => $v['value'],
                                'variation_template_id' => $product_variation->variation_template_id
                            ]);
                        }
                        $variation_value_id = $variation_value->id;
                    }
                    $variation_data[] = [
                        'name' => $variation_value_name,
                        'variation_value_id' => $variation_value_id,
                        'product_id' => $product->id,
                        'sub_sku' => $sub_sku,
                        'default_purchase_price' => $this->num_uf($v['default_purchase_price']),
                        'dpp_inc_tax' => $this->num_uf($v['dpp_inc_tax']),
                        'profit_percent' => $this->num_uf($v['profit_percent']),
                        'default_sell_price' => $this->num_uf($v['default_sell_price']),
                        'sell_price_inc_tax' => $this->num_uf($v['sell_price_inc_tax'])
                    ];
                    $c++;
                    $media[] = 'variation_images_' . $key . '_' . $k;
                }
                $new_variations = $product_variation->variations()->createMany($variation_data);
                $i = 0;
                foreach ($new_variations as $new_variation) {
                    Media::uploadMedia($product->business_id, $new_variation, request(), $media[$i]);
                    $i++;
                }
            }
        }
        ProductVariation::where('product_id', $product_id)
            ->whereNotIn('id', $product_variation_ids)
            ->delete();
    }
    /**
     * Checks if products has manage stock enabled then Updates quantity for product and its
     * variations
     *
     * @param $location_id
     * @param $product_id
     * @param $variation_id
     * @param $new_quantity
     * @param $old_quantity = 0
     * @param $number_format = null
     * @param $uf_data = true, if false it will accept numbers in database format
     *
     * @return boolean
     */
    public function updateProductQuantity($location_id, $product_id, $variation_id, $new_quantity, $old_quantity = 0, $number_format = null, $uf_data = true, $tank_id = null)
    {
        if ($uf_data) {
            $qty_difference = $this->num_uf($new_quantity, $number_format) - $this->num_uf($old_quantity, $number_format);
        } else {
            $qty_difference = $this->num_uf($new_quantity) - $this->num_uf($old_quantity);
        }
        $product = Product::find($product_id);
        //Check if stock is enabled or not.
        if ($product->enable_stock == 1 && $qty_difference != 0) {
            $variation = Variation::where('id', $variation_id)
                ->where('product_id', $product_id)
                ->first();
            //Add quantity in VariationLocationDetails
            $variation_location_d = VariationLocationDetails
                ::where('variation_id', $variation->id)
                ->where('product_id', $product_id)
                ->where('product_variation_id', $variation->product_variation_id)
                ->where('location_id', $location_id)
                ->first();
            if (empty($variation_location_d)) {
                $variation_location_d = new VariationLocationDetails();
                $variation_location_d->variation_id = $variation->id;
                $variation_location_d->product_id = $product_id;
                $variation_location_d->location_id = $location_id;
                $variation_location_d->product_variation_id = $variation->product_variation_id;
                $variation_location_d->qty_available = 0;
            }
            $variation_location_d->qty_available += $qty_difference;
            $variation_location_d->save();
            
            //add qty to fuel tank current stock 
            if (!empty($tank_id)) {
                FuelTank::where('id', $tank_id)->increment('current_balance', $qty_difference);
            }
           
            
            
        }
        return true;
    }
    /**
     * Checks if products has manage stock enabled then Decrease quantity for product and its variations
     *
     * @param $product_id
     * @param $variation_id
     * @param $location_id
     * @param $new_quantity
     * @param $old_quantity = 0
     *
     * @return boolean
     */
    public function decreaseProductQuantity($product_id, $variation_id, $location_id, $new_quantity, $old_quantity = 0, $adjustment_type = null, $store_id = false) //$adjustment_type for stock adjustment
    {
        if($new_quantity != $old_quantity){
            $qty_difference = $this->num_uf($new_quantity) - $this->num_uf($old_quantity);
            if($qty_difference < 0){
                $qty_difference = $qty_difference * -1;
            }
        
            $adjustment_type = empty($adjustment_type) ? ($new_quantity > $old_quantity ? "increase" : "decrease") : $adjustment_type;
            
            $product = Product::find($product_id);
            //Check if stock is enabled or not.
            if ($product->enable_stock == 1) {
                //Decrement Quantity in variations location table
                $details = VariationLocationDetails::where('variation_id', $variation_id)
                    ->where('product_id', $product_id)
                    ->where('location_id', $location_id)
                    ->first();
                
                //If location details not exists create new one
                if (empty($details)) {
                    $variation = Variation::find($variation_id);
                    $details = VariationLocationDetails::create([
                        'product_id' => $product_id,
                        'location_id' => $location_id,
                        'variation_id' => $variation_id,
                        'product_variation_id' => $variation->product_variation_id,
                        'qty_available' => 0
                    ]);
                }
                
                
                if (!empty($adjustment_type)) {
                    if ($adjustment_type == 'increase') {
                        $details->increment('qty_available', $qty_difference);
                    }
                    if ($adjustment_type == 'decrease') {
                        $details->decrement('qty_available', $qty_difference);
                    }
                } else {
                    $details->decrement('qty_available', $qty_difference);
                }
            }
        }
        
        
        return true;
    }
    /**
     * Decrease the product quantity of combo sub-products
     *
     * @param $variation_id
     * @param $location_id
     * @param $decrease_qty (factor by which qty will be decreased)
     *
     * @return boolean
     */
    public function decreaseProductQuantityCombo($combo_details, $location_id)
    {
        foreach ($combo_details as $details) {
            $this->decreaseProductQuantity(
                $details['product_id'],
                $details['variation_id'],
                $location_id,
                $details['quantity'],
                0,
                'decrease'
            );
            
            $store_id = request()->session()->get('business.default_store');
			$this->decreaseProductQuantityStore(
                $details['product_id'],
                $details['variation_id'],
                $location_id,
                $details['quantity'],
                $store_id,
                "decrease",
                0
            );
                    
        }
    }
     /**
     * create adjustment for weight excess or loss
     *
     * @param $product array
     *
     * @return boolean
     */
    public function createWeightExcessLossAdjustment($product, $location_id){
          //Decrease available quantity
          $this->decreaseProductQuantity(
            $product['product_id'],
            $product['variation_id'],
            $location_id,
            $this->num_uf($product['quantity']),
            0,
            $product['addjustment_type']
        );
        
        $store_id = request()->session()->get('business.default_store');
		$this->decreaseProductQuantityStore(
            $product['product_id'],
            $product['variation_id'],
            $location_id,
            $this->num_uf($product['quantity']),
            $store_id,
            "decrease",
            0
        );
            
    }
    /**
     * Get all details for a product from its variation id
     *
     * @param int $variation_id
     * @param int $business_id
     * @param int $location_id
     * @param bool $check_qty (If false qty_available is not checked)
     *
     * @return object
     */
    public function getDetailsFromVariation($variation_id, $business_id, $location_id = null, $check_qty = true, $store_id = null)
    {
        $query = Variation::join('products AS p', 'variations.product_id', '=', 'p.id')
            ->join('product_variations AS pv', 'variations.product_variation_id', '=', 'pv.id')
            ->leftJoin('variation_location_details AS vld', 'variations.id', '=', 'vld.variation_id')
            ->leftJoin('variation_store_details AS vsd', 'variations.id', '=', 'vsd.variation_id')
            ->leftJoin('units', 'p.unit_id', '=', 'units.id')
            ->leftJoin('product_racks', 'variations.product_id', '=', 'product_racks.product_id')
            ->leftJoin('categories', 'p.category_id', '=', 'categories.id')
            ->leftJoin('categories as sub_category', 'p.sub_category_id', '=', 'sub_category.id')
            ->leftJoin('purchase_lines', 'variations.product_id', '=', 'purchase_lines.product_id')
            ->leftJoin('transactions', 'purchase_lines.transaction_id', '=', 'transactions.id')
            ->leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
            ->leftJoin('brands', function ($join) {
                $join->on('p.brand_id', '=', 'brands.id')
                    ->whereNull('brands.deleted_at');
            })
            ->where('p.business_id', $business_id)
            ->where('variations.id', $variation_id);
    
        if (!empty($location_id)) {
            $query->where(function ($query) use ($location_id) {
                $query->where('vld.location_id', $location_id)
                      ->orWhereNull('vld.location_id'); // Ensure products with no location details are included
            });
        }
    
        if (!empty($store_id)) {
            $query->where(function ($query) use ($store_id) {
                $query->where('vsd.store_id', $store_id);
            });
        }
    
        $product = $query->select(
            DB::raw("IF(pv.is_dummy = 0, CONCAT(p.name, ' (', pv.name, ':', variations.name, ')'), p.name) AS product_name"),
            'p.id as product_id',
            'p.brand_id',
            'p.category_id',
            'p.tax as tax_id',
            'p.enable_stock',
            'p.enable_sr_no',
            'p.min_sell_price',
            'p.type as product_type',
            'p.name as product_actual_name',
            'p.warranty_id',
            'pv.name as product_variation_name',
            'pv.is_dummy as is_dummy',
            'variations.name as variation_name',
            'variations.sub_sku',
            'p.barcode_type',
            DB::raw("IFNULL(vld.qty_available, 0) as current_stock"),
            DB::raw("IFNULL(vld.qty_available, 0) as qty_available"),
            'variations.default_sell_price',
            'variations.sell_price_inc_tax',
            'variations.id as variation_id',
            'variations.combo_variations',
            'units.short_name as unit',
            'units.id as unit_id',
            'units.allow_decimal as unit_allow_decimal',
            'brands.name as brand',
            'product_racks.rack as rack_number',
            'contacts.name as supplier_name',
            'purchase_lines.purchase_price',
            'categories.weight_excess_loss_applicable',
            'sub_category.name as subcat_name',
            'p.sub_category_id',
            'variations.dpp_inc_tax as last_purchased_price'
        )->first();
    
        if (!empty($product->product_type) && $product->product_type == 'combo') {
            if ($check_qty) {
                $product->qty_available = $this->calculateComboQuantity($location_id, $product->combo_variations);
            }
            $product->combo_products = $this->calculateComboDetails($location_id, $product->combo_variations);
        }
    
        return $product;
    }


    /**
     * Calculates the quantity of combo products based on
     * the quantity of variation items used.
     *
     * @param int $location_id
     * @param array $combo_variations
     *
     * @return int
     */
    public function calculateComboQuantity($location_id, $combo_variations)
    {
        //get stock of the items and calcuate accordingly.
        $combo_qty = 0;
        foreach ($combo_variations as $key => $value) {
            $vld = VariationLocationDetails::where('variation_id', $value['variation_id'])
                ->where('location_id', $location_id)
                ->first();
            $product = Product::find($vld->product_id);
            $variation_qty = !empty($vld) ? $vld->qty_available : 0;
            $multiplier = $this->getMultiplierOf2Units($product->unit_id, $value['unit_id']);
            if ($key == 0) {
                $combo_qty = ($variation_qty / $multiplier) / $combo_variations[$key]['quantity'];
            } else {
                $combo_qty = min($combo_qty, ($variation_qty / $multiplier) / $combo_variations[$key]['quantity']);
            }
        }
        return floor($combo_qty);
    }
    /**
     * Calculates the quantity of combo products based on
     * the quantity of variation items used.
     *
     * @param int $location_id
     * @param array $combo_variations
     *
     * @return int
     */
    public function calculateComboDetails($location_id, $combo_variations)
    {
        $details = [];
        foreach ($combo_variations as $key => $value) {
            $variation = Variation::with('product')->findOrFail($value['variation_id']);
            $vld = VariationLocationDetails::where('variation_id', $value['variation_id'])
                ->where('location_id', $location_id)
                ->first();
            $variation_qty = !empty($vld) ? $vld->qty_available : 0;
            $multiplier = $this->getMultiplierOf2Units($variation->product->unit_id, $value['unit_id']);
            $details[] = [
                'variation_id' => $value['variation_id'],
                'product_id' => $variation->product_id,
                'qty_required' => $this->num_uf($value['quantity']) * $multiplier
            ];
        }
        return $details;
    }
    /**
     * Calculates the total amount of invoice
     *
     * @param array $products
     * @param int $tax_id
     * @param array $discount['discount_type', 'discount_amount']
     *
     * @return Mixed (false, array)
     */
    public function calculateInvoiceTotal($products, $tax_id, $discount = null)
    {
        if (empty($products)) {
            return false;
        }
        $output = ['total_before_tax' => 0, 'tax' => 0, 'discount' => 0, 'final_total' => 0];
        //Sub Total
        foreach ($products as $product) {
            $output['total_before_tax'] += $this->num_uf($product['unit_price_inc_tax']) * $this->num_uf($product['quantity']);
            //Add modifier price to total if exists
            if (!empty($product['modifier_price'])) {
                foreach ($product['modifier_price'] as $modifier_price) {
                    $output['total_before_tax'] += $this->num_uf($modifier_price);
                }
            }
        }
        //Calculate discount
        if (is_array($discount)) {
            if ($discount['discount_type'] == 'fixed') {
                $output['discount'] = $this->num_uf($discount['discount_amount']);
            } else {
                $output['discount'] = ($this->num_uf($discount['discount_amount']) / 100) * $output['total_before_tax'];
            }
        }
        //Tax
        $output['tax'] = 0;
        if (!empty($tax_id)) {
            $tax_details = TaxRate::find($tax_id);
            if (!empty($tax_details)) {
                $output['tax_id'] = $tax_id;
                $output['tax'] = ($tax_details->amount / 100) * ($output['total_before_tax'] - $output['discount']);
            }
        }
        //Calculate total
        $output['final_total'] = $output['total_before_tax'] + $output['tax'] - $output['discount'];
        return $output;
    }
    /**
     * Generates product sku
     *
     * @param string $string
     *
     * @return generated sku (string)
     */
    public function generateProductSku($string)
    {
        $business_id = request()->session()->get('user.business_id');
        $sku_prefix = Business::where('id', $business_id)->value('sku_prefix');
        return $sku_prefix . str_pad($string, 4, '0', STR_PAD_LEFT);
    }
    /**
     * Gives list of trending products
     *
     * @param int $business_id
     * @param array $filters
     *
     * @return Obj
     */
    public function getTrendingProducts($business_id, $filters = [])
    {
        $query = Transaction::join(
            'transaction_sell_lines as tsl',
            'transactions.id',
            '=',
            'tsl.transaction_id'
        )
            ->join('products as p', 'tsl.product_id', '=', 'p.id')
            ->leftjoin('units as u', 'u.id', '=', 'p.unit_id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final');
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('transactions.location_id', $permitted_locations);
        }
        if (!empty($filters['location_id'])) {
            $query->where('transactions.location_id', $filters['location_id']);
        }
        if (!empty($filters['category'])) {
            $query->where('p.category_id', $filters['category']);
        }
        if (!empty($filters['sub_category'])) {
            $query->where('p.sub_category_id', $filters['sub_category']);
        }
        if (!empty($filters['brand'])) {
            $query->where('p.brand_id', $filters['brand']);
        }
        if (!empty($filters['unit'])) {
            $query->where('p.unit_id', $filters['unit']);
        }
        if (!empty($filters['limit'])) {
            $query->limit($filters['limit']);
        } else {
            $query->limit(5);
        }
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween(DB::raw('date(transaction_date)'), [
                $filters['start_date'],
                $filters['end_date']
            ]);
        }
        // $sell_return_query = "(SELECT SUM(TPL.quantity) FROM transactions AS T JOIN purchase_lines AS TPL ON T.id=TPL.transaction_id WHERE TPL.product_id=tsl.product_id AND T.type='sell_return'";
        // if ($permitted_locations != 'all') {
        //     $sell_return_query .= ' AND T.location_id IN ('
        //      . implode(',', $permitted_locations) . ') ';
        // }
        // if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
        //     $sell_return_query .= ' AND date(T.transaction_date) BETWEEN \'' . $filters['start_date'] . '\' AND \'' . $filters['end_date'] . '\'';
        // }
        // $sell_return_query .= ')';
        $products = $query->select(
            DB::raw("(SUM(tsl.quantity) - COALESCE(SUM(tsl.quantity_returned), 0)) as total_unit_sold"),
            'p.name as product',
            'u.short_name as unit'
        )
            ->groupBy('tsl.product_id')
            ->orderBy('total_unit_sold', 'desc')
            ->get();
        return $products;
    }
    /**
     * Gives list of products based on products id and variation id
     *
     * @param int $business_id
     * @param int $product_id
     * @param int $variation_id = null
     *
     * @return Obj
     */
    public function getDetailsFromProduct($business_id, $product_id, $variation_id = null)
    {
        $product = Product::leftjoin('variations as v', 'products.id', '=', 'v.product_id')
            ->whereNull('v.deleted_at')
            ->where('products.business_id', $business_id);
        if (!is_null($variation_id) && $variation_id !== '0') {
            $product->where('v.id', $variation_id);
        }
        $product->where('products.id', $product_id);
        $products = $product->select(
            'products.id as product_id',
            'products.name as product_name',
            'v.id as variation_id',
            'v.name as variation_name'
        )
            ->get();
        return $products;
    }
    /**
     * F => D (Previous product Increase)
     * D => F (All product decrease)
     * F => F (Newly added product drerease)
     *
     * @param  object $transaction_before
     * @param  object  $transaction
     * @param  array  $input
     *
     * @return void
     */
    public function adjustProductStockForInvoice($status_before, $transaction, $input, $uf_data = true)
    {
        if ($status_before == 'final' && $transaction->status == 'draft') {
            foreach ($input['products'] as $product) {
                if (!empty($product['transaction_sell_lines_id'])) {
                    $this->updateProductQuantity($input['location_id'], $product['product_id'], $product['variation_id'], $product['quantity'], 0, null, false);
                    $this->updateProductQuantityStore($input['location_id'], $product['product_id'], $product['variation_id'], $product['quantity']);
                    //Adjust quantity for combo items.
                    if (isset($product['product_type']) && $product['product_type'] == 'combo') {
                        //Giving quantity in minus will increase the qty
                        foreach ($product['combo'] as $value) {
                            $this->updateProductQuantity($input['location_id'], $value['product_id'], $value['variation_id'], $value['quantity'], 0, null, false);
                            $this->updateProductQuantityStore($input['location_id'], $value['product_id'], $value['variation_id'], $value['quantity']);
                        }
                        // $this->updateEditedSellLineCombo($product['combo'], $input['location_id']);
                    }
                }
            }
        } elseif ($status_before == 'draft' && $transaction->status == 'final') {
            foreach ($input['products'] as $product) {
                $uf_quantity = $uf_data ? $this->num_uf($product['quantity']) : $product['quantity'];
                $this->decreaseProductQuantity(
                    $product['product_id'],
                    $product['variation_id'],
                    $input['location_id'],
                    $uf_quantity,
                    0,
                    'decrease'
                );
                
                $store_id = request()->session()->get('business.default_store');
        		$this->decreaseProductQuantityStore(
                    $product['product_id'],
                    $product['variation_id'],
                    $input['location_id'],
                    $uf_quantity,
                    $store_id,
                    "decrease",
                    0
                );
                
                
                
                
                //Adjust quantity for combo items.
                if (isset($product['product_type']) && $product['product_type'] == 'combo') {
                    $this->decreaseProductQuantityCombo($product['combo'], $input['location_id']);
                    //$this->decreaseProductQuantityCombo($product['variation_id'], $input['location_id'], $uf_quantity);
                }
            }
        } elseif ($status_before == 'final' && $transaction->status == 'final') {
            foreach ($input['products'] as $product) {
                if (empty($product['transaction_sell_lines_id'])) {
                    $uf_quantity = $uf_data ? $this->num_uf($product['quantity']) : $product['quantity'];
                    $this->decreaseProductQuantity(
                        $product['product_id'],
                        $product['variation_id'],
                        $input['location_id'],
                        $uf_quantity,
                        0,
                        'decrease'
                    );
                    
                    
                    $store_id = request()->session()->get('business.default_store');
            		$this->decreaseProductQuantityStore(
                        $product['product_id'],
                        $product['variation_id'],
                        $input['location_id'],
                        $uf_quantity,
                        $store_id,
                        "decrease",
                        0
                    );
                    
                    //Adjust quantity for combo items.
                    if (isset($product['product_type']) && $product['product_type'] == 'combo') {
                        $this->decreaseProductQuantityCombo($product['combo'], $input['location_id']);
                        //$this->decreaseProductQuantityCombo($product['variation_id'], $input['location_id'], $uf_quantity);
                    }
                }
            }
        }
    }
    /**
     * Updates variation from purchase screen
     *
     * @param array $variation_data
     *
     * @return void
     */
    public function updateProductFromPurchase($variation_data)
    {
        $variation_details = Variation::where('id', $variation_data['variation_id'])
            ->with(['product', 'product.product_tax'])
            ->first();
        $tax_rate = 0;
        if (!empty($variation_details->product->product_tax->amount)) {
            $tax_rate = $variation_details->product->product_tax->amount;
        }
        if (!isset($variation_data['sell_price_inc_tax'])) {
            $variation_data['sell_price_inc_tax'] = $variation_details->sell_price_inc_tax;
        }
        if (($variation_details->default_purchase_price != $variation_data['pp_without_discount']) ||
            ($variation_details->sell_price_inc_tax != $variation_data['sell_price_inc_tax'])
        ) {
            //Set default purchase price exc. tax
            $variation_details->default_purchase_price = $variation_data['pp_without_discount'];
            //Set default purchase price inc. tax
            $variation_details->dpp_inc_tax = $this->calc_percentage($variation_details->default_purchase_price, $tax_rate, $variation_details->default_purchase_price);
            //Set default sell price inc. tax
            $variation_details->sell_price_inc_tax = $variation_data['sell_price_inc_tax'];
            //set sell price inc. tax
            $variation_details->default_sell_price = $this->calc_percentage_base($variation_details->sell_price_inc_tax, $tax_rate);
            //set profit margin
            $variation_details->profit_percent = $this->get_percent($variation_details->default_purchase_price, $variation_details->default_sell_price);
            $variation_details->save();
        }
    }
    /**
     * Generated SKU based on the barcode type.
     *
     * @param string $sku
     * @param string $c
     * @param string $barcode_type
     *
     * @return void
     */
    public function generateSubSku($sku, $c, $barcode_type)
    {
        $sub_sku = $sku . $c;
        if (in_array($barcode_type, ['C128', 'C39'])) {
            $sub_sku = $sku . '-' . $c;
        }
        return $sub_sku;
    }
    /**
     * Add rack details.
     *
     * @param int $business_id
     * @param int $product_id
     * @param array $product_racks
     * @param array $product_racks
     *
     * @return void
     */
    public function addRackDetails($business_id, $product_id, $product_racks)
    {
        if (!empty($product_racks)) {
            $data = [];
            foreach ($product_racks as $location_id => $detail) {
                $data[] = [
                    'business_id' => $business_id,
                    'location_id' => $location_id,
                    'product_id' => $product_id,
                    'rack' => !empty($detail['rack']) ? $detail['rack'] : null,
                    'row' => !empty($detail['row']) ? $detail['row'] : null,
                    'position' => !empty($detail['position']) ? $detail['position'] : null,
                    'created_at' => \Carbon::now()->toDateTimeString(),
                    'updated_at' => \Carbon::now()->toDateTimeString()
                ];
            }
            ProductRack::insert($data);
        }
    }
    /**
     * Get rack details.
     *
     * @param int $business_id
     * @param int $product_id
     *
     * @return void
     */
    public function getRackDetails($business_id, $product_id, $get_location = false)
    {
        $query = ProductRack::where('product_racks.business_id', $business_id)
            ->where('product_id', $product_id);
        if ($get_location) {
            $racks = $query->join('business_locations AS BL', 'product_racks.location_id', '=', 'BL.id')
                ->select([
                    'product_racks.rack',
                    'product_racks.row',
                    'product_racks.position',
                    'BL.name'
                ])
                ->get();
        } else {
            $racks = collect($query->select(['rack', 'row', 'position', 'location_id'])->get());
            $racks = $racks->mapWithKeys(function ($item, $key) {
                return [$item['location_id'] => $item->toArray()];
            })->toArray();
        }
        return $racks;
    }
    /**
     * Update rack details.
     *
     * @param int $business_id
     * @param int $product_id
     * @param array $product_racks
     *
     * @return void
     */
    public function updateRackDetails($business_id, $product_id, $product_racks)
    {
        if (!empty($product_racks)) {
            foreach ($product_racks as $location_id => $details) {
                ProductRack::where('business_id', $business_id)
                    ->where('product_id', $product_id)
                    ->where('location_id', $location_id)
                    ->update([
                        'rack' => !empty($details['rack']) ? $details['rack'] : null,
                        'row' => !empty($details['row']) ? $details['row'] : null,
                        'position' => !empty($details['position']) ? $details['position'] : null
                    ]);
            }
        }
    }
    /**
     * Retrieves selling price group price for a product variation.
     *
     * @param int $variation_id
     * @param int $price_group_id
     * @param int $tax_id
     *
     * @return decimal
     */
    public function getVariationGroupPrice($variation_id, $price_group_id, $tax_id)
    {
        $price_inc_tax =
            VariationGroupPrice::where('variation_id', $variation_id)
            ->where('price_group_id', $price_group_id)
            ->value('price_inc_tax');
        $price_exc_tax = $price_inc_tax;
        if (!empty($price_inc_tax) && !empty($tax_id)) {
            $tax_amount = TaxRate::where('id', $tax_id)->value('amount');
            $price_exc_tax = $this->calc_percentage_base($price_inc_tax, $tax_amount);
        }
        return [
            'price_inc_tax' => $price_inc_tax,
            'price_exc_tax' => $price_exc_tax
        ];
    }
    /**
     * Creates new variation if not exists.
     *
     * @param int $business_id
     * @param string $name
     *
     * @return obj
     */
    public function createOrNewVariation($business_id, $name)
    {
        $variation = VariationTemplate::where('business_id', $business_id)
            ->where('name', 'like', $name)
            ->with(['values'])
            ->first();
        if (empty($variation)) {
            $variation = VariationTemplate::create([
                'business_id' => $business_id,
                'name' => $name
            ]);
        }
        return $variation;
    }
    /**
     * Adds opening stock to a single product.
     *
     * @param int $business_id
     * @param obj $product
     * @param array $input
     * @param obj $transaction_date
     * @param int $user_id
     *
     * @return void
     */
    public function addSingleProductOpeningStock($business_id, $product, $input, $transaction_date, $user_id)
    {
        $locations = BusinessLocation::forDropdown($business_id)->toArray();
        $tax_percent = !empty($product->product_tax->amount) ? $product->product_tax->amount : 0;
        $tax_id = !empty($product->product_tax->id) ? $product->product_tax->id : null;
        foreach ($input as $key => $value) {
            $location_id = $key;
            $purchase_total = 0;
            //Check if valid location
            if (array_key_exists($location_id, $locations)) {
                $purchase_lines = [];
                $purchase_price = $this->num_uf(trim($value['purchase_price']));
                $item_tax = $this->calc_percentage($purchase_price, $tax_percent);
                $purchase_price_inc_tax = $purchase_price + $item_tax;
                $qty = $this->num_uf(trim($value['quantity']));
                $exp_date = null;
                if (!empty($value['exp_date'])) {
                    $exp_date = \Carbon::createFromFormat('d-m-Y', $value['exp_date'])->format('Y-m-d');
                }
                $lot_number = null;
                if (!empty($value['lot_number'])) {
                    $lot_number = $value['lot_number'];
                }
                if ($qty > 0) {
                    $qty_formated = $this->num_f($qty);
                    //Calculate transaction total
                    $purchase_total += ($purchase_price_inc_tax * $qty);
                    $variation_id = $product->variations->first()->id;
                    $purchase_line = new PurchaseLine();
                    $purchase_line->product_id = $product->id;
                    $purchase_line->variation_id = $variation_id;
                    $purchase_line->item_tax = $item_tax;
                    $purchase_line->tax_id = $tax_id;
                    $purchase_line->quantity = $qty;
                    $purchase_line->pp_without_discount = $purchase_price;
                    $purchase_line->purchase_price = $purchase_price;
                    $purchase_line->purchase_price_inc_tax = $purchase_price_inc_tax;
                    $purchase_line->exp_date = $exp_date;
                    $purchase_line->lot_number = $lot_number;
                    $purchase_lines[] = $purchase_line;
                    $this->updateProductQuantity($location_id, $product->id, $variation_id, $qty_formated);
                    $this->updateProductQuantityStore($location_id, $product->id, $variation_id, $qty_formated);
                }
                //create transaction & purchase lines
                if (!empty($purchase_lines)) {
                    $transaction = Transaction::create(
                        [
                            'type' => 'opening_stock',
                            'opening_stock_product_id' => $product->id,
                            'status' => 'received',
                            'business_id' => $business_id,
                            'transaction_date' => $transaction_date,
                            'total_before_tax' => $purchase_total,
                            'location_id' => $location_id,
                            'final_total' => $purchase_total,
                            'payment_status' => 'paid',
                            'created_by' => $user_id
                        ]
                    );
                    $transaction->purchase_lines()->saveMany($purchase_lines);
                    $opening_balance_equity_id = $this->account_exist_return_id('Opening Balance Equity Account');
                    $this->createAccountTransaction($transaction, 'credit', $opening_balance_equity_id, $purchase_total);
                }
            }
        }
    }
    /**
     * Add/Edit transaction purchase lines
     *
     * @param object $transaction
     * @param array $input_data
     * @param array $currency_details
     * @param boolean $enable_product_editing
     * @param string $before_status = null
     *
     * @return array
     */
    public function createOrUpdatePurchaseLines($transaction, $input_data, $currency_details, $enable_product_editing, $store_id, $before_status = null)
    {
        $updated_purchase_lines = [];
        $updated_purchase_line_ids = [0];
        $exchange_rate = !empty($transaction->exchange_rate) ? $transaction->exchange_rate : 1;
        foreach ($input_data as $data) {
            $multiplier = 1;
            if (isset($data['sub_unit_id']) && $data['sub_unit_id'] == $data['product_unit_id']) {
                unset($data['sub_unit_id']);
            }
            if (!empty($data['sub_unit_id'])) {
                $unit = Unit::find($data['sub_unit_id']);
                $multiplier = !empty($unit->base_unit_multiplier) ? $unit->base_unit_multiplier : 1;
            }
            $free_qty = !empty($data['free_qty']) ? $data['free_qty'] : 0;
            $new_quantity = ($this->num_uf($data['quantity']) * $multiplier) + $free_qty;
            $new_quantity_f = $this->num_f($new_quantity);
            //update existing purchase line
            if (isset($data['purchase_line_id'])) {
                $purchase_line = PurchaseLine::findOrFail($data['purchase_line_id']);
                $updated_purchase_line_ids[] = $purchase_line->id;
                $old_qty = $this->num_f($purchase_line->quantity);
                $this->updateProductStock($before_status, $transaction, $data['product_id'], $data['variation_id'], $new_quantity, $purchase_line->quantity, $currency_details, $store_id);
            } else {
                //create newly added purchase lines
                $purchase_line = new PurchaseLine();
                $purchase_line->product_id = $data['product_id'];
                $purchase_line->variation_id = $data['variation_id'];
                //Increase quantity only if status is received
                if ($transaction->status == 'received') {
                    $this->updateProductQuantity($transaction->location_id, $data['product_id'], $data['variation_id'], $new_quantity_f, 0, $currency_details);
                    $this->updateProductQuantityStore($transaction->location_id, $data['product_id'], $data['variation_id'], $new_quantity_f, $store_id, 0, $currency_details);
                }
            }
            $purchase_line->quantity = $new_quantity;
            $purchase_line->bonus_qty = $free_qty;
            $purchase_line->pp_without_discount = ($this->num_uf($data['pp_without_discount'], $currency_details) * $exchange_rate) / $multiplier;
            $purchase_line->discount_percent = $this->num_uf($data['discount_percent'], $currency_details);
            $purchase_line->purchase_price = ($this->num_uf($data['purchase_price'], $currency_details) * $exchange_rate) / $multiplier;
            $purchase_line->purchase_price_inc_tax = ($this->num_uf($data['purchase_price_inc_tax'], $currency_details) * $exchange_rate) / $multiplier;
            $purchase_line->item_tax = ($this->num_uf($data['item_tax'], $currency_details) * $exchange_rate) / $multiplier;
            $purchase_line->tax_id = $data['purchase_line_tax_id'];
            $purchase_line->lot_number = !empty($data['lot_number']) ? $data['lot_number'] : null;
            $purchase_line->mfg_date = !empty($data['mfg_date']) ? $this->uf_date($data['mfg_date']) : null;
            $purchase_line->exp_date = !empty($data['exp_date']) ? $this->uf_date($data['exp_date']) : null;
            $purchase_line->sub_unit_id = !empty($data['sub_unit_id']) ? $data['sub_unit_id'] : null;
            $updated_purchase_lines[] = $purchase_line;
            //Edit product price
            if ($enable_product_editing == 1) {
                if (isset($data['default_sell_price'])) {
                    $variation_data['sell_price_inc_tax'] = ($this->num_uf($data['default_sell_price'], $currency_details)) / $multiplier;
                }
                $variation_data['pp_without_discount'] = ($this->num_uf($data['pp_without_discount'], $currency_details) * $exchange_rate) / $multiplier;
                $variation_data['variation_id'] = $purchase_line->variation_id;
                $variation_data['purchase_price'] = $purchase_line->purchase_price;
                $this->updateProductFromPurchase($variation_data);
            }
        }
        //unset deleted purchase lines
        $delete_purchase_line_ids = [];
        $delete_purchase_lines = null;
        if (!empty($updated_purchase_line_ids)) {
            $delete_purchase_lines = PurchaseLine::where('transaction_id', $transaction->id)
                ->whereNotIn('id', $updated_purchase_line_ids)
                ->get();
            if ($delete_purchase_lines->count()) {
                foreach ($delete_purchase_lines as $delete_purchase_line) {
                    $delete_purchase_line_ids[] = $delete_purchase_line->id;
                    //decrease deleted only if previous status was received
                    if ($before_status == 'received') {
                        $this->decreaseProductQuantity(
                            $delete_purchase_line->product_id,
                            $delete_purchase_line->variation_id,
                            $transaction->location_id,
                            $delete_purchase_line->quantity,
                            0,
                            'decrease'
                        );
                        
                        $store_id = request()->session()->get('business.default_store');
                		$this->decreaseProductQuantityStore(
                            $delete_purchase_line->product_id,
                            $delete_purchase_line->variation_id,
                            $transaction->location_id,
                            $delete_purchase_line->quantity,
                            $store_id,
                            "decrease",
                            0
                        );
                        
                    }
                }
                //Delete deleted purchase lines
                PurchaseLine::where('transaction_id', $transaction->id)
                    ->whereIn('id', $delete_purchase_line_ids)
                    ->delete();
            }
        }
        //update purchase lines
        if (!empty($updated_purchase_lines)) {
            $transaction->purchase_lines()->saveMany($updated_purchase_lines);
        }
        return $delete_purchase_lines;
    }
    /**
     * Updates product stock after adding or updating purchase
     *
     * @param string $status_before
     * @param obj $transaction
     * @param integer $product_id
     * @param integer $variation_id
     * @param decimal $new_quantity in database format
     * @param decimal $old_quantity in database format
     * @param array $currency_details
     *
     */
    public function updateProductStock($status_before, $transaction, $product_id, $variation_id, $new_quantity, $old_quantity, $currency_details, $store_id = null)
    {
        $new_quantity_f = $this->num_f($new_quantity);
        $old_qty = $this->num_f($old_quantity);
        //Update quantity for existing products
        if ($status_before == 'received' && $transaction->status == 'received') {
            //if status received update existing quantity
            $this->updateProductQuantity($transaction->location_id, $product_id, $variation_id, $new_quantity_f, $old_qty, $currency_details);
            $this->updateProductQuantityStore($transaction->location_id, $product_id, $variation_id, $new_quantity_f, $store_id, $old_qty, $currency_details);
        } elseif ($status_before == 'received' && $transaction->status != 'received') {
            //decrease quantity only if status changed from received to not received
            $this->decreaseProductQuantity(
                $product_id,
                $variation_id,
                $transaction->location_id,
                $old_quantity,
                0,
                'decrease'
            );
            $this->decreaseProductQuantityStore(
                $product_id,
                $variation_id,
                $transaction->location_id,
                $old_quantity,
                $store_id,
                "decrease",
                0
            );
        } elseif ($status_before != 'received' && $transaction->status == 'received') {
            $this->updateProductQuantity($transaction->location_id, $product_id, $variation_id, $new_quantity_f, 0, $currency_details);
            $this->updateProductQuantityStore($transaction->location_id, $product_id, $variation_id, $new_quantity_f, $store_id, $old_qty, $currency_details);
        
        }
    }
    /**
     * Recalculates purchase line data according to subunit data
     *
     * @param integer $purchase_line
     * @param integer $business_id
     *
     * @return array
     */
    public function changePurchaseLineUnit($purchase_line, $business_id)
    {
        $base_unit = $purchase_line->product->unit;
        $sub_units = $base_unit->sub_units;
        $sub_unit_id = $purchase_line->sub_unit_id;
        $sub_unit = $sub_units->filter(function ($item) use ($sub_unit_id) {
            return $item->id == $sub_unit_id;
        })->first();
        if (!empty($sub_unit)) {
            $multiplier = $sub_unit->base_unit_multiplier;
            $purchase_line->quantity = $purchase_line->quantity / $multiplier;
            $purchase_line->pp_without_discount = $purchase_line->pp_without_discount * $multiplier;
            $purchase_line->purchase_price = $purchase_line->purchase_price * $multiplier;
            $purchase_line->purchase_price_inc_tax = $purchase_line->purchase_price_inc_tax * $multiplier;
            $purchase_line->item_tax = $purchase_line->item_tax * $multiplier;
            $purchase_line->quantity_returned = $purchase_line->quantity_returned / $multiplier;
            $purchase_line->quantity_sold = $purchase_line->quantity_sold / $multiplier;
            $purchase_line->quantity_adjusted = $purchase_line->quantity_adjusted / $multiplier;
        }
        //SubUnits
        $purchase_line->sub_units_options = $this->getSubUnits($business_id, $base_unit->id, false, $purchase_line->product_id);
        return $purchase_line;
    }
    /**
     * Recalculates sell line data according to subunit data
     *
     * @param integer $unit_id
     *
     * @return array
     */
    public function changeSellLineUnit($business_id, $sell_line)
    {
        $unit_details = $this->getSubUnits($business_id, $sell_line->unit_id, false, $sell_line->product_id);
        $sub_unit = null;
        $sub_unit_id = $sell_line->sub_unit_id;
        foreach ($unit_details as $key => $value) {
            if ($key == $sub_unit_id) {
                $sub_unit = $value;
            }
        }
        if (!empty($sub_unit)) {
            $multiplier = $sub_unit['multiplier'];
            $sell_line->quantity_ordered = $sell_line->quantity_ordered / $multiplier;
            $sell_line->item_tax = $sell_line->item_tax * $multiplier;
            $sell_line->default_sell_price = $sell_line->default_sell_price * $multiplier;
            $sell_line->unit_price_before_discount = $sell_line->unit_price_before_discount * $multiplier;
            $sell_line->sell_price_inc_tax = $sell_line->sell_price_inc_tax * $multiplier;
            $sell_line->sub_unit_multiplier = $multiplier;
            $sell_line->unit_details = $unit_details;
        }
        return $sell_line;
    }
    /**
     * Retrieves current stock of a variation for the given location
     *
     * @param int $variation_id, int location_id
     *
     * @return float
     */
    public function getCurrentStock($variation_id, $location_id)
    {
        $current_stock = VariationLocationDetails::where('variation_id', $variation_id)
            ->where('location_id', $location_id)
            ->value('qty_available');
        if (null == $current_stock) {
            $current_stock = 0;
        }
        return $current_stock;
    }
    /**
     * Adjusts stock over selling with purchases, opening stocks andstock transfers
     * Also maps with respective sells
     *
     * @param obj $transaction
     *
     * @return void
     */
    public function adjustStockOverSelling($transaction)
    {
        if ($transaction->status != 'received') {
            return false;
        }
        foreach ($transaction->purchase_lines as $purchase_line) {
            if ($purchase_line->product->enable_stock == 1) {
                //Available quantity in the purchase line
                $purchase_line_qty_avlbl = $purchase_line->quantity_remaining;
                if ($purchase_line_qty_avlbl <= 0) {
                    continue;
                }
                //update sell line purchase line mapping
                $sell_line_purchase_lines =
                    TransactionSellLinesPurchaseLines::where('purchase_line_id', 0)
                    ->join('transaction_sell_lines as tsl', 'tsl.id', '=', 'transaction_sell_lines_purchase_lines.sell_line_id')
                    ->join('transactions as t', 'tsl.transaction_id', '=', 't.id')
                    ->where('t.location_id', $transaction->location_id)
                    ->where('tsl.variation_id', $purchase_line->variation_id)
                    ->where('tsl.product_id', $purchase_line->product_id)
                    ->select('transaction_sell_lines_purchase_lines.*')
                    ->get();
                foreach ($sell_line_purchase_lines as $slpl) {
                    if ($purchase_line_qty_avlbl > 0) {
                        if ($slpl->quantity <= $purchase_line_qty_avlbl) {
                            $purchase_line_qty_avlbl -= $slpl->quantity;
                            $slpl->purchase_line_id = $purchase_line->id;
                            $slpl->save();
                            //update purchase line quantity sold
                            $purchase_line->quantity_sold += $slpl->quantity;
                            $purchase_line->save();
                        } else {
                            $diff = $slpl->quantity - $purchase_line_qty_avlbl;
                            $slpl->purchase_line_id = $purchase_line->id;
                            $slpl->quantity = $purchase_line_qty_avlbl;
                            $slpl->save();
                            //update purchase line quantity sold
                            $purchase_line->quantity_sold += $slpl->quantity;
                            $purchase_line->save();
                            TransactionSellLinesPurchaseLines::create([
                                'sell_line_id' => $slpl->sell_line_id,
                                'purchase_line_id' => 0,
                                'quantity' => $diff
                            ]);
                            break;
                        }
                    }
                }
            }
        }
    }
    /**
     * Finds out most relevant descount for the product
     *
     * @param obj $product, int $business_id, int $location_id, bool $is_cg,
     * bool $is_spg
     *
     * @return obj discount
     */
    public function getProductDiscount($product, $business_id, $location_id, $is_cg = false, $is_spg = false)
    {
        $now = \Carbon::now()->toDateTimeString();
        //Search if both category and brand matches
        $query1 = Discount::where('business_id', $business_id)
            ->where('location_id', $location_id)
            ->where('is_active', 1)
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>=', $now)
            ->where('brand_id', $product->brand_id)
            ->where('category_id', $product->category_id)
            ->orderBy('priority', 'desc')
            ->latest();
        if ($is_cg) {
            $query1->where('applicable_in_cg', 1);
        }
        if ($is_spg) {
            $query1->where('applicable_in_spg', 1);
        }
        $discount = $query1->first();
        //Search if either category or brand matches
        if (empty($discount)) {
            $query2 = Discount::where('business_id', $business_id)
                ->where('location_id', $location_id)
                ->where('is_active', 1)
                ->where('starts_at', '<=', $now)
                ->where('ends_at', '>=', $now)
                ->where(function ($q) use ($product) {
                    $q->whereRaw('(brand_id="' . $product->brand_id . '" AND category_id IS NULL)')
                        ->orWhereRaw('(category_id="' . $product->category_id . '" AND brand_id IS NULL)');
                })
                ->orderBy('priority', 'desc');
            if ($is_cg) {
                $query2->where('applicable_in_cg', 1);
            }
            if ($is_spg) {
                $query2->where('applicable_in_spg', 1);
            }
            $discount = $query2->first();
        }
        if (!empty($discount)) {
            $discount->formated_starts_at = $this->format_date($discount->starts_at->toDateTimeString(), true);
            $discount->formated_ends_at = $this->format_date($discount->ends_at->toDateTimeString(), true);
        }
        return $discount;
    }
    /**
     * Checks if products has manage stock enabled then Updates quantity for product and its
     * variations
     *
     * @param $location_id
     * @param $product_id
     * @param $variation_id
     * @param $new_quantity
     * @param $old_quantity = 0
     * @param $number_format = null
     * @param $uf_data = true, if false it will accept numbers in database format
     *
     * @return boolean
     */
    public function updateProductQuantityStore($location_id, $product_id, $variation_id, $new_quantity, $store_id = null, $old_quantity = 0,  $number_format = null, $uf_data = true)
    {
        if ($uf_data) {
            $qty_difference = $this->num_uf($new_quantity, $number_format) - $this->num_uf($old_quantity, $number_format);
        } else {
            $qty_difference = $this->num_uf($new_quantity) - $this->num_uf($old_quantity);
        }
        
        if(empty($store_id)){
            $store_id = request()->session()->get('business.default_store');
        }
        
        
        $product = Product::find($product_id);
        //Check if stock is enabled or not.
        if ($qty_difference != 0) {
            $variation = Variation::where('id', $variation_id)
                ->where('product_id', $product_id)
                ->first();
            $variation_store_d = VariationStoreDetail
                ::where('variation_id', $variation->id)
                ->where('product_id', $product_id)
                ->where('product_variation_id', $variation->product_variation_id)
                ->where('store_id', $store_id)
                ->first();
            if (empty($variation_store_d)) {
                $variation_store_d = new VariationStoreDetail();
                $variation_store_d->variation_id = $variation->id;
                $variation_store_d->product_id = $product_id;
                $variation_store_d->store_id = $store_id;
                $variation_store_d->product_variation_id = $variation->product_variation_id;
                $variation_store_d->qty_available = 0;
            }
            $variation_store_d->qty_available += $qty_difference;
            $variation_store_d->save();
        }
        return true;
    }
    
    
    
    /**
     * Checks if products has manage stock enabled then Updates quantity for product and its
     * variations in Opening Stock only
     *
     * @param $location_id
     * @param $product_id
     * @param $variation_id
     * @param $new_quantity
     * @param $old_quantity = 0
     * @param $number_format = null
     * @param $uf_data = true, if false it will accept numbers in database format
     *
     * @return boolean
     */
    public function updateProductQuantityStoreForOpeningStock($location_id, $product_id, $variation_id, $new_quantity, $store_id, $old_quantity = 0,  $number_format = null, $uf_data = true)
    {
        // if ($uf_data) {
        //     $qty_difference = $this->num_uf($new_quantity, $number_format) - $this->num_uf($old_quantity, $number_format);
        // } else {
        //     $qty_difference = $new_quantity - $old_quantity;
        // }
        $qty_difference = $new_quantity;
        $product = Product::find($product_id);
        //Check if stock is enabled or not.
        if ($qty_difference != 0) {
            $variation = Variation::where('id', $variation_id)
                ->where('product_id', $product_id)
                ->first();
            $variation_store_d = VariationStoreDetail
                ::where('variation_id', $variation->id)
                ->where('product_id', $product_id)
                ->where('product_variation_id', $variation->product_variation_id)
                ->where('store_id', $store_id)
                ->first();
            if (empty($variation_store_d)) {
                $variation_store_d = new VariationStoreDetail();
                $variation_store_d->variation_id = $variation->id;
                $variation_store_d->product_id = $product_id;
                $variation_store_d->store_id = $store_id;
                $variation_store_d->product_variation_id = $variation->product_variation_id;
                $variation_store_d->qty_available = 0;
            }
            $variation_store_d->qty_available = $qty_difference;
            $variation_store_d->save();
        }
        return true;
    }
    
    
    
    /**
     * Checks if products has manage stock enabled then Decrease quantity for product and its variations
     *
     * @param $product_id
     * @param $variation_id
     * @param $location_id
     * @param $new_quantity
     * @param $old_quantity = 0
     *
     * @return boolean
     */
    public function decreaseProductQuantityStore($product_id, $variation_id, $location_id, $new_quantity, $store_id, $type = "decrease", $old_quantity = 0)
    {
        $qty_difference = $this->num_uf($new_quantity) - $this->num_uf($old_quantity);
        if($qty_difference < 0){
            $qty_difference = $qty_difference * -1;
        }
        $product = Product::find($product_id);
        //Check if stock is enabled or not.
        if ($product->enable_stock == 1) {
            $store_details = VariationStoreDetail::where('variation_id', $variation_id)
                ->where('product_id', $product_id)
                ->where('store_id', $store_id)
                ->first();

            /*dd($store_details);*/
            //If location details not exists create new one
            if (empty($store_details)) {
                $variation = Variation::find($variation_id);
                $store_details = VariationStoreDetail::create([
                    'product_id' => $product_id,
                    'store_id' => $store_id,
                    'variation_id' => $variation_id,
                    'product_variation_id' => $variation->product_variation_id,
                    'qty_available' => 0
                ]);
            }
            
            
            if ($type == 'increase') {
                $store_details->increment('qty_available', $qty_difference);
            }
            if ($type == 'decrease') {
                $store_details->decrement('qty_available', $qty_difference);
            }
            
        }
        return true;
    }
    public function getProductUnitsDropdown($product_id)
    {
        $business_id = request()->session()->get('user.business_id');
        $product = Product::find($product_id);
        $html = '';
        if(!empty($product)){
            $sub_units = $this->getSubUnits($business_id, $product->unit_id, true, $product_id);
            $variation = Variation::where('product_id', $product_id)->first();
            $default_multiple_unit_price = [];
            if (!empty($variation)) {
                $default_multiple_unit_price = (array) json_decode($variation->default_multiple_unit_price);
            }
            if (count($sub_units) > 0) {
                $html .= '<select name="sub_unit_id" class="form-control input-sm sub_unit">';
                foreach ($sub_units as $key => $value) {
                    $html .= '<option value="' . $key . '" data-multiplier="' . $value["multiplier"] . '" data-unit_price="';
                    if (array_key_exists($key, $default_multiple_unit_price)) {
                        $html .= $default_multiple_unit_price[$key];
                    }
                    $html .= '"  data-unit_name="' . $value["name"] . '" data-allow_decimal="' . $value['allow_decimal'] . '" >
                            ' . $value["name"] . '
                        </option>';
                }
                $html .= '</select>';
            }
        }
        return $html;
    }
     
    public function getVariationStockDetails($business_id, $variation_id, $location_id, $store_id=null,$start_date = null, $end_date = null)
    {
        
        
        
        $sum = Transaction::leftJoin('purchase_lines', 'transactions.id', 'purchase_lines.transaction_id')
                    ->where('transactions.location_id', $location_id)
                    
                    ->where(function ($query) {
                        $query->where(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', 1)
                                       ->where('transactions.is_credit_sale', 0);
                        })->orWhere(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', '<>', 1);
                        });
                    })
                    
                    ->whereIn('transactions.type',['purchase','purchase_return']) 
                    ->where('purchase_lines.product_id', $variation_id)
                    ->where('purchase_lines.variation_id', $variation_id)
                    ->where('transactions.status','received')
                    ->whereNull('purchase_lines.deleted_at')
                    ->groupBy('purchase_lines.variation_id')
                    ->selectRaw('SUM(purchase_lines.quantity) AS sum_quantity, SUM(purchase_lines.quantity_returned) AS sum_quantity_returned')
                    ->withoutTrashed()
                    ->first();
                    
        $sumDeleted = Transaction::leftJoin('purchase_lines', 'transactions.id', 'purchase_lines.transaction_id')
                    ->where('transactions.location_id', $location_id)
                    
                    ->where(function ($query) {
                        $query->where(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', 1)
                                       ->where('transactions.is_credit_sale', 0);
                        })->orWhere(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', '<>', 1);
                        });
                    })
                    
                    ->whereIn('transactions.type',['_deleted_purchase']) 
                    ->where('purchase_lines.product_id', $variation_id)
                    ->where('purchase_lines.variation_id', $variation_id)
                    ->where('transactions.status','received')
                    ->whereNull('purchase_lines.deleted_at')
                    ->groupBy('purchase_lines.variation_id')
                    ->selectRaw('SUM(purchase_lines.quantity) AS sum_quantity, SUM(purchase_lines.quantity_returned) AS sum_quantity_returned')
                    ->withoutTrashed()
                    ->first();
                    
                $sumTransfer = Transaction::leftJoin('purchase_lines', 'transactions.id', 'purchase_lines.transaction_id')
                    
                    ->where('transactions.location_id', $location_id)
                    
                    ->where(function ($query) {
                        $query->where(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', 1)
                                       ->where('transactions.is_credit_sale', 0);
                        })->orWhere(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', '<>', 1);
                        });
                    })
                    
                    ->whereIn('transactions.type',['purchase_transfer'])
                    ->where('purchase_lines.product_id', $variation_id)
                    ->where('purchase_lines.variation_id', $variation_id)
                    ->where('transactions.status','received')
                    ->whereNull('purchase_lines.deleted_at')
                    ->groupBy('purchase_lines.variation_id')
                    ->selectRaw('SUM(purchase_lines.quantity) AS sum_quantity')
                    ->withoutTrashed()
                    ->first();
                
                $sumProd = Transaction::leftJoin('purchase_lines', 'transactions.id', 'purchase_lines.transaction_id')
                
                    ->where('transactions.location_id', $location_id)
                    ->where(function ($query) {
                        $query->where(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', 1)
                                       ->where('transactions.is_credit_sale', 0);
                        })->orWhere(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', '<>', 1);
                        });
                    })
                    
                    ->whereIn('transactions.type',['production_purchase'])
                    ->where('purchase_lines.product_id', $variation_id)
                    ->where('purchase_lines.variation_id', $variation_id)
                    ->where('transactions.status','received')
                    ->whereNull('purchase_lines.deleted_at')
                    ->groupBy('purchase_lines.variation_id')
                    ->selectRaw('SUM(purchase_lines.quantity) AS sum_quantity')
                    ->withoutTrashed()
                    ->first();
                
                    
                $sumOpening = Transaction::leftJoin('purchase_lines', 'transactions.id', 'purchase_lines.transaction_id')
                
                    ->where('transactions.location_id', $location_id)
                    ->where(function ($query) {
                        $query->where(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', 1)
                                       ->where('transactions.is_credit_sale', 0);
                        })->orWhere(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', '<>', 1);
                        });
                    })
                    
                    ->whereIn('transactions.type',['opening_stock'])
                    ->where('purchase_lines.product_id', $variation_id)
                    ->where('purchase_lines.variation_id', $variation_id)
                    ->where('transactions.status','received')
                    ->whereNull('purchase_lines.deleted_at')
                    ->groupBy('purchase_lines.variation_id')
                    ->selectRaw('SUM(purchase_lines.quantity) AS sum_quantity')
                    ->withoutTrashed()
                    ->first();
                    
                $sumAdjust = Transaction::leftJoin('stock_adjustment_lines', 'transactions.id', 'stock_adjustment_lines.transaction_id')
                
                    ->where('transactions.location_id', $location_id)
                    ->where(function ($query) {
                        $query->where(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', 1)
                                       ->where('transactions.is_credit_sale', 0);
                        })->orWhere(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', '<>', 1);
                        });
                    })
                    
                    ->whereIn('transactions.type',['stock_adjustment'])
                    ->where('stock_adjustment_lines.product_id', $variation_id)
                    ->where('stock_adjustment_lines.variation_id', $variation_id)
                    ->where('transactions.status','received')
                    ->select(
                        DB::raw("SUM(IF(stock_adjustment_lines.stock_adjustment_type='increase', stock_adjustment_lines.quantity, 0)) as increased"),
                        DB::raw("SUM(IF(stock_adjustment_lines.stock_adjustment_type='decrease', stock_adjustment_lines.quantity, 0)) as decreased")
                        )
                    ->withoutTrashed()
                    ->get()
                    ->first();
                
                $incr = !empty($sumAdjust) ? $sumAdjust->increased : 0;
                $decr = !empty($sumAdjust) ? $sumAdjust->decreased : 0;
                
                $sumOp = !empty($sumOpening) ? $sumOpening->sum_quantity : 0;
                $sumPt = !empty($sumTransfer) ? $sumTransfer->sum_quantity : 0;
                $sumPr = !empty($sumProd) ? $sumProd->sum_quantity : 0;
                
                $sumQuantity = !empty($sum) ? $sum->sum_quantity : 0;
                $sumQuantityReturned = !empty($sum) ? $sum->sum_quantity_returned : 0;
                
                $sumQuantityDeleted = !empty($sumDeleted) ? $sumDeleted->sum_quantity : 0;
                $sumQuantityReturnedDeleted = !empty($sumDeleted) ? $sum->sum_quantity_returned : 0;
                
                
                $sumSell = Transaction::leftJoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                
                    ->where('transactions.location_id', $location_id)
                    ->where(function ($query) {
                        $query->where(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', 1)
                                       ->where('transactions.is_credit_sale', 0);
                        })->orWhere(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', '<>', 1);
                        });
                    })
                    
                    ->whereIn('transactions.type',['sell','sell_return'])
                    ->where('transaction_sell_lines.product_id', $variation_id)
                    ->where('transaction_sell_lines.variation_id', $variation_id)
                    ->where('transactions.status','final')
                    ->whereNull('transaction_sell_lines.deleted_at')
                    ->groupBy('transaction_sell_lines.variation_id')
                    ->selectRaw('SUM(transaction_sell_lines.quantity) AS sum_quantity, SUM(transaction_sell_lines.quantity_returned) AS sum_quantity_returned')
                    ->withoutTrashed()
                    ->first();
                
                $sumSellTr = Transaction::leftJoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                
                    ->where('transactions.location_id', $location_id)
                    ->where(function ($query) {
                        $query->where(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', 1)
                                       ->where('transactions.is_credit_sale', 0);
                        })->orWhere(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', '<>', 1);
                        });
                    })
                    
                    ->whereIn('transactions.type',['sell_transfer'])
                    ->where('transaction_sell_lines.product_id', $variation_id)
                    ->where('transaction_sell_lines.variation_id', $variation_id)
                    ->where('transactions.status','final')
                    ->whereNull('transaction_sell_lines.deleted_at')
                    ->groupBy('transaction_sell_lines.variation_id')
                    ->selectRaw('SUM(transaction_sell_lines.quantity) AS sum_quantity')
                    ->withoutTrashed()
                    ->first();
                    
                $sumSellPr = Transaction::leftJoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                
                    ->where('transactions.location_id', $location_id)
                    ->where(function ($query) {
                        $query->where(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', 1)
                                       ->where('transactions.is_credit_sale', 0);
                        })->orWhere(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', '<>', 1);
                        });
                    })
                    
                    ->whereIn('transactions.type',['production_sell'])
                    ->where('transaction_sell_lines.product_id', $variation_id)
                    ->where('transaction_sell_lines.variation_id', $variation_id)
                    ->where('transactions.status','final')
                    ->whereNull('transaction_sell_lines.deleted_at')
                    ->groupBy('transaction_sell_lines.variation_id')
                    ->selectRaw('SUM(transaction_sell_lines.quantity) AS sum_quantity')
                    ->withoutTrashed()
                    ->first();
                
                
                $sumQuantitySell = !empty($sumSell) ? $sumSell->sum_quantity : 0;
                $sumQuantityTr = !empty($sumSellTr) ? $sumSellTr->sum_quantity : 0;
                $sumQuantityPr = !empty($sumSellPr) ? $sumSellPr->sum_quantity : 0;
                
                $sumQuantitySell_2 = !empty($sumSell) ? $sumSell->sum_quantity_sell : 0;
                $sumQuantityReturnedSell = !empty($sumSell) ? $sumSell->sum_quantity_returned : 0;
                
                $bal = $sumOp - $sumQuantityDeleted + $sumQuantityReturnedDeleted + $sumQuantity - $sumQuantityReturned  - $sumQuantitySell + $sumQuantityReturnedSell + $incr - $decr + $sumPt - $sumQuantityTr - $sumQuantityPr + $sumPr;
                
        
        
        
        $purchase_details = Variation::join('products as p', 'p.id', '=', 'variations.product_id')
                    ->join('units', 'p.unit_id', '=', 'units.id')
                    ->leftjoin('product_variations as pv', 'variations.product_variation_id', '=', 'pv.id')
                    ->leftjoin('purchase_lines as pl', 'pl.variation_id', '=', 'variations.id')
                    ->leftjoin('transactions as t', 'pl.transaction_id', '=', 't.id')
                    ->where('t.location_id', $location_id)
                    ->where(function ($query) {
                        $query->where(function ($innerQuery) {
                            $innerQuery->where('t.is_settlement', 1)
                                       ->where('t.is_credit_sale', 0);
                        })->orWhere(function ($innerQuery) {
                            $innerQuery->where('t.is_settlement', '<>', 1);
                        });
                    })
                    
                    ->where('t.status', 'received')
                    ->where('p.business_id', $business_id)
                    ->where('variations.id', $variation_id)
                    ->when($store_id != null, function ($query)use ($store_id){
                        return $query->where('t.store_id',$store_id);
                        
                    })
                     ->when($start_date != null, function ($query)use ($start_date){
                        return $query->whereDate('t.transaction_date','>=',$start_date);
                    })
                    ->when($end_date != null, function ($query)use ($end_date){
                        return $query->whereDate('t.transaction_date','<=',$end_date);
                    })
                    ->select(
                        DB::raw("SUM(IF(t.type='purchase' AND t.status='received', pl.quantity, 0)) as total_purchase"),
                        DB::raw("SUM(IF(t.type='production_purchase' AND t.status='received', pl.quantity, 0)) as manufactured"),
                        DB::raw("SUM(IF(t.type='purchase' OR t.type='purchase_return', pl.quantity_returned, 0)) as total_purchase_return"),
                        DB::raw("SUM(pl.quantity_adjusted) as total_adjusted"),
                        DB::raw("SUM(IF(t.type='opening_stock', pl.quantity, 0)) as total_opening_stock"),
                        DB::raw("SUM(IF(t.type='purchase_transfer', pl.quantity, 0)) as total_purchase_transfer"),
                        'variations.sub_sku as sub_sku',
                        'p.name as product',
                        'p.type',
                        'p.sku',
                        'p.id as product_id',
                        'units.short_name as unit',
                        'pv.name as product_variation',
                        'variations.name as variation_name',
                        'variations.id as variation_id'
                    )
                  ->withoutTrashed()
                  ->get()->first(); 

        $sell_details_query = Variation::join('products as p', 'p.id', '=', 'variations.product_id')
                ->leftjoin('transaction_sell_lines as sl', 'sl.variation_id', '=', 'variations.id')
                ->join('transactions as t', 'sl.transaction_id', '=', 't.id')
                ->where('t.location_id', $location_id)
                ->where('t.status', 'final')
                ->where(function ($query) {
                    $query->where(function ($innerQuery) {
                        $innerQuery->where('t.is_settlement', 1)
                                   ->where('t.is_credit_sale', 0);
                    })->orWhere(function ($innerQuery) {
                        $innerQuery->where('t.is_settlement', '<>', 1);
                    });
                })
                
                ->where('p.business_id', $business_id)
                ->where('variations.id', $variation_id)
                
                 ->when($start_date != null, function ($query)use ($start_date){
                        return $query->whereDate('t.transaction_date','>=',$start_date);
                    })
                    ->when($end_date != null, function ($query)use ($end_date){
                        return $query->whereDate('t.transaction_date','<=',$end_date);
                    })
                    
                ->withoutTrashed()
                ->select(
                    DB::raw("SUM(IF(t.type='sell', sl.quantity, 0)) as total_sold"),
                    DB::raw("SUM(IF(t.type='production_sell', sl.quantity, 0)) as input"),
                    DB::raw("SUM(IF(t.type='sell', sl.quantity_returned, 0)) as total_sell_return"),
                    DB::raw("SUM(IF(t.type='sell_transfer', sl.quantity, 0)) as total_sell_transfer")
                );

        if(!empty($store_id) && $store_id){
            $sell_details_query->leftjoin('variation_store_details as vsd','variations.id','=','vsd.variation_id')
                ->where('vsd.store_id',$store_id);
        }    
        $sell_details = $sell_details_query->get()->first();
        $current_stock = VariationLocationDetails::where('variation_id', 
                                            $variation_id)
                                        ->where('location_id', $location_id)
                                        ->first();

 

        if ($purchase_details->type == 'variable') {
            $product_name = $purchase_details->product . ' - ' . $purchase_details->product_variation . ' - ' . $purchase_details->variation_name . ' (' . $purchase_details->sub_sku . ')';
        } else {
            $product_name = $purchase_details->product . ' (' . $purchase_details->sku . ')';
        }

        $output = [
            'variation' => $product_name,
            'unit' => $purchase_details->unit,
            'second_unit' => $purchase_details->unit,
            'total_purchase' => $sumQuantity, //$purchase_details->total_purchase,
            'total_purchase_return' => $sumQuantityReturned + $sumQuantityDeleted - $sumQuantityReturnedDeleted, //$purchase_details->total_purchase_return,
            'total_adjusted' => ($incr - $decr), //$purchase_details->total_adjusted,
            'total_opening_stock' => $sumOp, //$purchase_details->total_opening_stock,
            'total_purchase_transfer' => $sumPt,//$purchase_details->total_purchase_transfer,
            'total_sold' => $sumQuantitySell, //$sell_details->total_sold,
            'total_sell_return' => $sumQuantityReturnedSell, //$sell_details->total_sell_return, 
            'total_sell_transfer' => $sumQuantityTr, //$sell_details->total_sell_transfer,
            'current_stock' => $current_stock->qty_available ?? 0,
            'input' => $sumQuantityPr, //$sell_details->input ?? 0,
            'manufactured' => $sumPr, //$purchase_details->manufactured ?? 0
        ];

        return $output;
    }

    public function getVariationStockHistory($business_id, $variation_id, $location_id, $store_id=null,$start_date = null, $end_date = null)
    {
        $stock_history = Transaction::leftJoin('transaction_sell_lines as sl', function ($join) {
                                    $join->on('sl.transaction_id', '=', 'transactions.id')
                                         ->whereNull('sl.deleted_at');
                                })
                                ->leftJoin('purchase_lines as pl', function ($join) {
                                    $join->on('pl.transaction_id', '=', 'transactions.id')
                                         ->whereNull('pl.deleted_at');
                                })
                                ->leftJoin('stock_adjustment_lines as al', function ($join) {
                                    $join->on('al.transaction_id', '=', 'transactions.id');
                                })
                                ->leftJoin('transactions as return', function ($join) {
                                    $join->on('transactions.return_parent_id', '=', 'return.id')
                                         ->whereNull('return.deleted_at');
                                })
                                ->leftJoin('purchase_lines as rpl', function ($join) {
                                    $join->on('rpl.transaction_id', '=', 'return.id')
                                         ->whereNull('rpl.deleted_at');
                                })
                                ->leftJoin('transaction_sell_lines as rsl', function ($join) {
                                    $join->on('rsl.transaction_id', '=', 'return.id')
                                         ->whereNull('rsl.deleted_at');
                                })
                                ->leftJoin('contacts as c', function ($join) {
                                    $join->on('transactions.contact_id', '=', 'c.id')
                                         ->whereNull('c.deleted_at');
                                })
                                ->leftJoin('pump_operators as po', function ($join) {
                                    $join->on('transactions.pump_operator_id', '=', 'po.id');
                                })
                                ->where('transactions.location_id', $location_id)
                                ->where(function ($query) {
                                    $query->where(function ($innerQuery) {
                                        $innerQuery->where('transactions.is_settlement', 1)
                                                   ->where('transactions.is_credit_sale', 0);
                                    })->orWhere(function ($innerQuery) {
                                        $innerQuery->where('transactions.is_settlement', '<>', 1);
                                    });
                                })
                                ->where( function($q) use ($variation_id){
                                    $q->where('sl.variation_id', $variation_id)
                                        ->orWhere('pl.variation_id', $variation_id)
                                        ->orWhere('al.variation_id', $variation_id)
                                        ->orWhere('rpl.variation_id', $variation_id)
                                        ->orWhere('rsl.variation_id', $variation_id);
                                })
                                ->whereIn('transactions.status',['final','received'])
                                ->whereIn('transactions.type', ['_deleted_purchase','sell', 'purchase', 'stock_adjustment', 'opening_stock', 'sell_transfer', 'purchase_transfer', 'production_purchase', 'purchase_return', 'sell_return', 'production_sell'])
                                ->when($store_id != null, function ($query)use ($store_id){
                                    return $query->leftJoin('variation_store_details as vsds', function ($join) {
                                        $join->on('vsds.variation_id', '=', 'sl.variation_id')
                                             ->whereNull('vsds.deleted_at');
                                    })  
                                    ->leftJoin('variation_store_details as vsdp', function ($join) {
                                        $join->on('vsdp.variation_id', '=', 'pl.variation_id')
                                             ->whereNull('vsdp.deleted_at');
                                    })
                                    ->where(function ($q) use ($store_id) {
                                        $q->where('vsdp.store_id', $store_id)
                                          ->orWhere('vsds.store_id', $store_id);
                                    });
                                })
                                 ->when($start_date != null, function ($query)use ($start_date){
                                        return $query->whereDate('transactions.transaction_date','>=',$start_date);
                                    })
                                    ->when($end_date != null, function ($query)use ($end_date){
                                        return $query->whereDate('transactions.transaction_date','<=',$end_date);
                                    })
                    
                                ->select(
                                    'transactions.id as transaction_id',
                                    'transactions.type as transaction_type',
                                    'sl.quantity as sell_line_quantity',
                                    'pl.quantity as purchase_line_quantity',
                                    'rsl.quantity_returned as sell_return',
                                    'rpl.quantity_returned as purchase_return',
                                    'al.quantity as stock_adjusted',
                                    'al.stock_adjustment_type as adjustment_type',
                                    'pl.quantity_returned as combined_purchase_return',
                                    'transactions.return_parent_id',
                                    'transactions.transaction_date',
                                    'transactions.status',
                                    'transactions.invoice_no',
                                    'transactions.ref_no',
                                    'transactions.additional_notes',
                                    \DB::raw('IFNULL(c.name, po.name) as contact_name'),
                                    //'c.name as contact_name',
                                    'c.supplier_business_name',
                                    \DB::raw('IFNULL(transactions.store_id, return.store_id) as store_id')
                                )
                                ->orderBy('transactions.transaction_date', 'asc')
                                ->withoutTrashed()
                                ->get();
        $stock_history_array = [];
        $stock = 0;
        $stock_in_second_unit = 0;
        foreach ($stock_history as $stock_line) {

            $temp_array = [
                'date' => $stock_line->transaction_date,
                'transaction_id' => $stock_line->transaction_id,
                'contact_name' => $stock_line->contact_name,
                'supplier_business_name' => $stock_line->supplier_business_name
            ];
            if ($stock_line->transaction_type == 'sell') {
                if ($stock_line->status != 'final') {
                    continue;
                }
                $quantity_change =  -1 * $stock_line->sell_line_quantity;
                $stock += $quantity_change;

                $stock_in_second_unit -= $stock_line->sell_line_quantity;
                $stock_history_array[] = array_merge($temp_array, [
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'sell',
                    'type_label' => __('sale.sale'),
                    'ref_no' => $stock_line->invoice_no,
                    'sell_secondary_unit_quantity' => $stock_line->sell_line_quantity,
                    'stock_in_second_unit' => $this->roundQuantity($stock_in_second_unit)
                ]);
            } elseif ($stock_line->transaction_type == 'purchase') {
                if ($stock_line->status != 'received') {
                    continue;
                }
                $quantity_change = $stock_line->purchase_line_quantity;
                $stock += $quantity_change;
                $stock_in_second_unit += $stock_line->purchase_line_quantity;
                $stock_history_array[] = array_merge($temp_array, [
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'purchase',
                    'type_label' => __('lang_v1.purchase'),
                    'ref_no' => $stock_line->ref_no,
                    'purchase_secondary_unit_quantity' => $stock_line->purchase_line_quantity,
                    'stock_in_second_unit' => $this->roundQuantity($stock_in_second_unit)
                ]);
            } elseif ($stock_line->transaction_type == '_deleted_purchase') {
                if ($stock_line->status != 'received') {
                    continue;
                }
                $quantity_change = -1* $stock_line->purchase_line_quantity;
                $stock += $quantity_change;
                $stock_in_second_unit += $stock_line->purchase_line_quantity;
                $stock_history_array[] = array_merge($temp_array, [
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'purchase_deleted',
                    'type_label' => "Deleted Purchase PO No ".$stock_line->invoice_no,
                    'ref_no' => $stock_line->ref_no,
                    'purchase_secondary_unit_quantity' => $stock_line->purchase_line_quantity,
                    'stock_in_second_unit' => $this->roundQuantity($stock_in_second_unit)
                ]);
            }
            
            elseif ($stock_line->transaction_type == 'stock_adjustment') {
                
                if($stock_line->adjustment_type == 'increase'){
                    $quantity_change = $stock_line->stock_adjusted;
                    $label = __('stock_adjustment.stock_adjustment_increase');
                }elseif($stock_line->adjustment_type == 'decrease'){
                    $quantity_change = -1 * $stock_line->stock_adjusted;
                    $label = __('stock_adjustment.stock_adjustment_decrease');
                }else{
                    continue;    
                }
                
                
                
                $stock += $quantity_change;
                $stock_history_array[] = array_merge($temp_array, [
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'stock_adjustment',
                    'type_label' => $label,
                    'ref_no' => $stock_line->ref_no,
                    'stock_in_second_unit' => $this->roundQuantity($stock_in_second_unit)
                ]);
            } elseif ($stock_line->transaction_type == 'opening_stock') {
                $quantity_change = $stock_line->purchase_line_quantity;
                $stock += $quantity_change;
                $stock_history_array[] = array_merge($temp_array, [
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'opening_stock',
                    'type_label' => __('report.opening_stock'),
                    'ref_no' => $stock_line->ref_no ?? '',
                    'additional_notes' => $stock_line->additional_notes,
                    'stock_in_second_unit' => $this->roundQuantity($stock_in_second_unit)
                ]);
            } elseif ($stock_line->transaction_type == 'sell_transfer') {
                if ($stock_line->status != 'final') {
                    continue;
                }
                $quantity_change = -1 * $stock_line->sell_line_quantity;
                $stock += $quantity_change;
                $stock_history_array[] = array_merge($temp_array, [
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'sell_transfer',
                    'type_label' => __('lang_v1.stock_transfers') . ' (' . __('lang_v1.out') . ')',
                    'ref_no' => $stock_line->ref_no,
                    'stock_in_second_unit' => $this->roundQuantity($stock_in_second_unit)
                ]);
            } elseif ($stock_line->transaction_type == 'purchase_transfer') {
                if ($stock_line->status != 'received') {
                    continue;
                }
                
                $quantity_change = $stock_line->purchase_line_quantity;
                $stock += $quantity_change;
                $stock_history_array[] = array_merge($temp_array, [
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'purchase_transfer',
                    'type_label' => __('lang_v1.stock_transfers') . ' (' . __('lang_v1.in') . ')',
                    'ref_no' => $stock_line->ref_no,
                    'stock_in_second_unit' => $this->roundQuantity($stock_in_second_unit)
                ]);
            } elseif ($stock_line->transaction_type == 'production_sell') {
                if ($stock_line->status != 'final') {
                    continue;
                }
                $quantity_change =  -1 * $stock_line->sell_line_quantity;
                $stock += $quantity_change;
                $stock_history_array[] = array_merge($temp_array, [
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'sell',
                    'type_label' => __('manufacturing::lang.ingredient'),
                    'ref_no' => '',
                    'stock_in_second_unit' => $this->roundQuantity($stock_in_second_unit)
                ]);
            } elseif ($stock_line->transaction_type == 'production_purchase') {
                $quantity_change = $stock_line->purchase_line_quantity;
                $stock += $quantity_change;
                $stock_history_array[] = array_merge($temp_array, [
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'production_purchase',
                    'type_label' => __('manufacturing::lang.manufactured'),
                    'ref_no' => $stock_line->ref_no,
                    'stock_in_second_unit' => $this->roundQuantity($stock_in_second_unit)
                ]);
            } elseif ($stock_line->transaction_type == 'purchase_return') {
                $quantity_change =  -1 * ($stock_line->combined_purchase_return + $stock_line->purchase_return);
                $stock += $quantity_change;
                $stock_history_array[] = array_merge($temp_array, [
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'purchase_return',
                    'type_label' => __('lang_v1.purchase_return'),
                    'ref_no' => $stock_line->ref_no,
                    'stock_in_second_unit' => $this->roundQuantity($stock_in_second_unit)
                ]);
            } elseif ($stock_line->transaction_type == 'sell_return') {
                $quantity_change = $stock_line->sell_return;
                $stock += $quantity_change;
                $stock_history_array[] = array_merge($temp_array, [
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'purchase_transfer',
                    'type_label' => __('lang_v1.sell_return'),
                    'ref_no' => $stock_line->invoice_no,
                    'stock_in_second_unit' => $this->roundQuantity($stock_in_second_unit)
                ]);
            } 
        }

        return array_reverse($stock_history_array);
    }

    
    public function getTankStockDetails($business_id, $product_id, $location_id, $tank_id = null)
    {
        $purchase_details = Product::leftjoin('units', 'products.unit_id', '=', 'units.id')
        ->leftjoin('tank_purchase_lines as pl', 'pl.product_id', '=', 'products.id')
        ->leftjoin('transactions as t', function ($join) use($location_id){
            
            return $join->on('pl.transaction_id', 't.id')
            ->on('t.location_id', \DB::raw($location_id));
            
        })
        ->leftjoin('stock_adjustment_lines', function ($join) use($tank_id, $product_id){
            
            return $join->on('t.id', 'stock_adjustment_lines.transaction_id')
            ->on('stock_adjustment_lines.product_id', \DB::raw($product_id))
            ->on('stock_adjustment_lines.tank_id',  \DB::raw($tank_id));
            
        })
        ->where('products.business_id', $business_id)
        ->where('products.id', $product_id)
        ->when($tank_id!= null, function ($query)use ($tank_id){
            return $query->where('pl.tank_id',$tank_id);
            
        })
        ->select(
            DB::raw("SUM(IF(t.type='purchase' AND t.status='received', pl.quantity, 0)) as total_purchase"),
            DB::raw("SUM(IF(t.type='opening_stock', pl.quantity, 0)) as total_opening_stock"),
            DB::raw("SUM(IF(t.type='purchase_transfer', pl.quantity, 0)) as total_purchase_transfer"),
            DB::raw("SUM(IF(stock_adjustment_lines.stock_adjustment_type='increase', stock_adjustment_lines.quantity, -1 * stock_adjustment_lines.quantity)) as total_adjusted"),
            'products.name as product_name',
            'products.type',
            'products.sku',
            'products.id as product_id',
            'units.short_name as unit'
        )
        ->get()
        ->first();
        
        
        $sell_details = Product::leftjoin('tank_sell_lines as sl', 'sl.product_id', '=', 'products.id')
        ->join('transactions as t', 'sl.transaction_id', '=', 't.id')
        ->where('t.location_id', $location_id)
        ->where('t.status', 'final')
        ->where('products.business_id', $business_id)
        ->where('products.id', $product_id)
        ->when($tank_id != null, function ($query)use ($tank_id){
            return $query->where('sl.tank_id',$tank_id);
            
        })
        ->select(
            DB::raw("SUM(IF(t.type='sell', sl.quantity, 0)) as total_sold"),
            DB::raw("SUM(IF(t.type='sell_transfer', sl.quantity, 0)) as total_sell_transfer")
        )
        ->get()
        ->first();
        
        $transactionUtil = new TransactionUtil();
            
        $balance = $transactionUtil->getTankProductBalanceByProductId($product_id);
        
        $output = [
            'product_name' => $purchase_details->product_name,
            'unit' => $purchase_details->unit,
            'total_purchase' => $purchase_details->total_purchase,
            'total_opening_stock' => $purchase_details->total_opening_stock,
            'total_adjusted' => $purchase_details->total_adjusted,
            'total_purchase_transfer' => $purchase_details->total_purchase_transfer,
            'total_sold' => $sell_details->total_sold,
            'total_sell_transfer' => $sell_details->total_sell_transfer,
            'current_stock' => $balance?? 0
        ];
        return $output;
    }
    
    public function getTankStockHistory($business_id, $product_id, $location_id, $tank_id = null)
    {
        
        $stock_history = Transaction::leftjoin('tank_sell_lines as sl',
            'sl.transaction_id', '=', 'transactions.id')
            ->leftjoin('tank_purchase_lines as pl', 'pl.transaction_id', '=', 'transactions.id')
            ->leftjoin('stock_adjustment_lines as al','al.transaction_id', '=', 'transactions.id')
            ->where('transactions.location_id', $location_id)
            ->where( function($q) use ($product_id){
                return $q->where('sl.product_id', $product_id)
                ->orWhere('pl.product_id', $product_id)
                ->orWhere('al.product_id', $product_id);
              
            })
            ->when($tank_id != null, function ($q) use($tank_id){
                return $q->where('sl.tank_id', $tank_id)
                ->orWhere('pl.tank_id', $tank_id);
            })
            ->whereIn('transactions.type', ['sell', 'purchase', 'opening_stock', 'sell_transfer', 'purchase_transfer', 'production_purchase'])
            ->select(
                'transactions.id as transaction_id',
                'transactions.type as transaction_type',
                'sl.quantity as sell_line_quantity',
                'pl.quantity as purchase_line_quantity',
                'al.quantity as stock_adjusted',
                'transactions.transaction_date',
                'transactions.status',
                'transactions.invoice_no',
                'transactions.ref_no',
                \DB::raw('IFNULL(sl.tank_id, pl.tank_id) as tank_id')
                )
                ->orderBy('transactions.id', 'asc');
                
                $stock_history = $stock_history->get();
                
                $stock_history_array = [];
                $stock = 0;
                foreach ($stock_history as $stock_line) {
                    if ($stock_line->transaction_type == 'sell') {
                        if ($stock_line->status != 'final') {
                            continue;
                        }
                        $quantity_change =  -1 * $stock_line->sell_line_quantity;
                        $stock += $quantity_change;
                        $stock_history_array[] = [
                            'date' => $stock_line->transaction_date,
                            'quantity_change' => $quantity_change,
                            'stock' => round($stock),
                            'type' => 'sell',
                            'type_label' => __('sale.sale'),
                            'ref_no' => $stock_line->invoice_no,
                            'transaction_id' => $stock_line->transaction_id
                        ];
                    } elseif ($stock_line->transaction_type == 'purchase') {
                        if ($stock_line->status != 'received') {
                            continue;
                        }
                        $quantity_change = $stock_line->purchase_line_quantity;
                        $stock += $quantity_change;
                        $stock_history_array[] = [
                            'date' => $stock_line->transaction_date,
                            'quantity_change' => $quantity_change,
                            'stock' => round($stock),
                            'type' => 'purchase',
                            'type_label' => __('lang_v1.purchase'),
                            'ref_no' => $stock_line->ref_no,
                            'transaction_id' => $stock_line->transaction_id
                        ];
                    
                    } elseif ($stock_line->transaction_type == 'opening_stock') {
                        $quantity_change = $stock_line->purchase_line_quantity;
                        $stock += $quantity_change;
                        $stock_history_array[] = [
                            'date' => $stock_line->transaction_date,
                            'quantity_change' => $quantity_change,
                            'stock' => round($stock),
                            'type' => 'opening_stock',
                            'type_label' => __('report.opening_stock'),
                            'ref_no' => $stock_line->ref_no ?? '',
                            'transaction_id' => $stock_line->transaction_id
                        ];
                    } elseif ($stock_line->transaction_type == 'sell_transfer') {
                        $quantity_change = -1 * $stock_line->sell_line_quantity;
                        $stock += $quantity_change;
                        $stock_history_array[] = [
                            'date' => $stock_line->transaction_date,
                            'quantity_change' => $quantity_change,
                            'stock' => round($stock),
                            'type' => 'sell_transfer',
                            'type_label' => __('lang_v1.stock_transfers') . ' (' . __('lang_v1.out') . ')',
                            'ref_no' => $stock_line->ref_no,
                            'transaction_id' => $stock_line->transaction_id
                        ];
                    } elseif ($stock_line->transaction_type == 'purchase_transfer') {
                        $quantity_change = $stock_line->purchase_line_quantity;
                        $stock += $quantity_change;
                        $stock_history_array[] = [
                            'date' => $stock_line->transaction_date,
                            'quantity_change' => $quantity_change,
                            'stock' => round($stock),
                            'type' => 'purchase_transfer',
                            'type_label' => __('lang_v1.stock_transfers') . ' (' . __('lang_v1.in') . ')',
                            'ref_no' => $stock_line->ref_no,
                            'transaction_id' => $stock_line->transaction_id
                        ];
                    } elseif ($stock_line->transaction_type == 'production_purchase') {
                        $quantity_change = $stock_line->purchase_line_quantity;
                        $stock += $quantity_change;
                        $stock_history_array[] = [
                            'date' => $stock_line->transaction_date,
                            'quantity_change' => $quantity_change,
                            'stock' => round($stock),
                            'type' => 'production_purchase',
                            'type_label' => __('manufacturing::lang.manufactured'),
                            'ref_no' => $stock_line->ref_no,
                            'transaction_id' => $stock_line->transaction_id
                        ];
                    }
                }
                return array_reverse($stock_history_array);
    }
    public function getProductStockDetails($business_id, $filters, $for,$module = null)
    {
        DB::enableQueryLog();
        $query = Variation::join('products as p', 'p.id', '=', 'variations.product_id')
                  ->join('units', 'p.unit_id', '=', 'units.id')
                  ->leftjoin('variation_location_details as vld', 'variations.id', '=', 'vld.variation_id')
                  ->leftjoin('business_locations as l', 'vld.location_id', '=', 'l.id')
                  ->leftjoin('categories as c', 'p.category_id', '=', 'c.id')
                  ->join('product_variations as pv', 'variations.product_variation_id', '=', 'pv.id')
                  ->leftjoin('variation_store_details as vsd', 'variations.id', '=', 'vsd.variation_id')
                  ->where('p.business_id', $business_id)
                  ->whereIn('p.type', ['single', 'variable']);
                  
        if(!empty($module)){
            $query->where(function($q) use($module){
                $q->whereNull('p.disabled_in')->orwhereRaw("NOT FIND_IN_SET(?, p.disabled_in)", [$module]);
            });
        }

        $permitted_locations = auth()->user()->permitted_locations();
        $location_filter = '';

        if ($permitted_locations != 'all') {
            $query->whereIn('vld.location_id', $permitted_locations);

            $locations_imploded = implode(', ', $permitted_locations);
            $location_filter .= "AND transactions.location_id IN ($locations_imploded) ";
        }

        if (!empty($filters['location_id'])) {
            $location_id = $filters['location_id'];

            $query->where('vld.location_id', $location_id);

            $location_filter .= "AND transactions.location_id=$location_id";

            //If filter by location then hide products not available in that location
            $query->join('product_locations as pl', 'pl.product_id', '=', 'p.id')
                  ->where(function ($q) use ($location_id) {
                      $q->where('pl.location_id', $location_id);
                  });
        }

        if (!empty($filters['category_id'])) {
            $query->where('p.category_id', $filters['category_id']);
        }
        if (!empty($filters['sub_category_id'])) {
            $query->where('p.sub_category_id', $filters['sub_category_id']);
        }
        if (!empty($filters['brand_id'])) {
            $query->where('p.brand_id', $filters['brand_id']);
        }
        if (!empty($filters['unit_id'])) {
            $query->where('p.unit_id', $filters['unit_id']);
        }

        if (!empty($filters['tax_id'])) {
            $query->where('p.tax', $filters['tax_id']);
        }

        if (!empty($filters['type'])) {
            $query->where('p.type', $filters['type']);
        }

        if (isset($filters['only_mfg_products']) && $filters['only_mfg_products'] == 1) {
            $query->join('mfg_recipes as mr', 'mr.variation_id', '=', 'variations.id');
        }

        if (isset($filters['active_state']) && $filters['active_state'] == 'active') {
            $query->where('p.is_inactive', 0);
        }
        if (isset($filters['active_state']) && $filters['active_state'] == 'inactive') {
            $query->where('p.is_inactive', 1);
        }
        if (isset($filters['not_for_selling']) && $filters['not_for_selling'] == 1) {
            $query->where('p.not_for_selling', 1);
        }

        if (!empty($filters['repair_model_id'])) {
            $query->where('p.repair_model_id', request()->get('repair_model_id'));
        }

        //TODO::Check if result is correct after changing LEFT JOIN to INNER JOIN
        $pl_query_string = $this->get_pl_quantity_sum_string('pl');

        if ($for == 'view_product' && !empty(request()->input('product_id'))) {
            $location_filter .= 'AND transactions.location_id=l.id';
        }
        
        if(!empty($filters['start_date']) && !empty($filters['end_date'])){
           
            $start_date = date('Y-m-d', strtotime($filters['start_date']));

            $end_date = date('Y-m-d', strtotime($filters['end_date']));
        }


        // $location_filter = '';

        if (!empty($start_date) && !empty($end_date)) {
           
            // \Log::error($end_date);

            // $location_filter .= " AND date(transactions.transaction_date) >= '$start_date' ";
            // $location_filter .= " AND date(transactions.transaction_date) <= '$end_date' ";
            $query->where('variations.created_at','>=', $start_date);
            $query->where('variations.created_at','>=', $start_date);
        } 
        if(!empty($filters['store_id'])){
            $location_filter .= " AND transactions.store_id =".$filters['store_id'];
            $query->where('vsd.store_id', $filters['store_id']);
        }

        
        
        $products = $query->select(
            // DB::raw("(SELECT SUM(quantity) FROM transaction_sell_lines LEFT JOIN transactions ON transaction_sell_lines.transaction_id=transactions.id WHERE transactions.status='final' $location_filter AND
            //     transaction_sell_lines.product_id=products.id) as total_sold"),

            DB::raw("(SELECT SUM(TSL.quantity - TSL.quantity_returned) FROM transactions 
                  JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id
                  WHERE transactions.status='final' AND transactions.type='sell' AND transactions.location_id=vld.location_id $location_filter
                  AND TSL.variation_id=variations.id) as total_sold"),
            DB::raw("(SELECT SUM(IF(transactions.type='sell_transfer', TSL.quantity, 0) ) FROM transactions 
                  JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id
                  WHERE transactions.status='final' AND transactions.type='sell_transfer' AND transactions.location_id=vld.location_id $location_filter AND (TSL.variation_id=variations.id)) as total_transfered"),
            DB::raw("(SELECT SUM(
                  CASE 
                      WHEN SAL.stock_adjustment_type = 'increase' THEN SAL.quantity 
                      WHEN SAL.stock_adjustment_type = 'decrease' THEN -SAL.quantity 
                      ELSE 0 
                  END)
                  FROM transactions 
                  JOIN stock_adjustment_lines AS SAL ON transactions.id=SAL.transaction_id
                  WHERE transactions.type='stock_adjustment' AND transactions.location_id=vld.location_id $location_filter
                    AND (SAL.variation_id=variations.id)) as total_adjusted"),
            // DB::raw("(SELECT SUM( COALESCE(pl.quantity - ($pl_query_string), 0) * purchase_price_inc_tax) FROM transactions 
            //       JOIN purchase_lines AS pl ON transactions.id=pl.transaction_id
            //       WHERE (transactions.status='received' OR transactions.type='purchase_return')  AND transactions.location_id=vld.location_id $location_filter
            //       AND (pl.variation_id=variations.id)) as stock_price"),
            DB::raw("(SELECT COALESCE(SUM(PL.quantity), 0) FROM transactions 
              JOIN purchase_lines AS PL ON transactions.id=PL.transaction_id
              WHERE transactions.status='received' AND transactions.type='purchase' AND transactions.location_id=vld.location_id  
              AND (PL.variation_id=variations.id) $location_filter) as total_purchased"),
              
            DB::raw("vld.qty_available as stock"),
            'variations.sub_sku as sku',
            'variations.dpp_inc_tax as stock_price',
            'variations.id as vid',
            'p.name as product',
            'p.type',
            'p.sku as prod_sku',
            'p.alert_quantity',
            'p.id as product_id',
            'units.short_name as unit',
            'p.enable_stock as enable_stock',
            'variations.sell_price_inc_tax as unit_price',
            'pv.name as product_variation',
            'variations.name as variation_name',
            'l.name as location_name',
            'l.id as location_id',
            'variations.id as variation_id',
            'c.name as category_name',
            'p.product_custom_field1',
            'p.product_custom_field2',
            'p.product_custom_field3',
            'p.product_custom_field4'
        )->groupBy('variations.id', 'vld.location_id');
            
        if (isset($filters['show_manufacturing_data']) && $filters['show_manufacturing_data']) {
            $pl_query_string = $this->get_pl_quantity_sum_string('PL');
            $products->addSelect(
                DB::raw("(SELECT COALESCE(SUM(PL.quantity - ($pl_query_string)), 0) FROM transactions 
                    JOIN purchase_lines AS PL ON transactions.id=PL.transaction_id
                    WHERE transactions.status='received' AND transactions.type='production_purchase' AND transactions.location_id=vld.location_id  
                    AND (PL.variation_id=variations.id)) as total_mfg_stock")
            );
        }

        if (!empty($filters['product_id'])) {
            $products->where('p.id', $filters['product_id'])
                    ->groupBy('l.id');
        }
        if ($for == 'view_product') {
            return $products->get();
        } else if ($for == 'api') {
            return $products->paginate();
        } else {
            return $products;
        }
    }

    public function updateProductVariationStock($purchases, $business_id, $store_id){
        $fuel_category_id = Category::where('business_id', $business_id)->where('name', 'Fuel')->first();
        foreach($purchases as $purchase){
            $product = Product::findOrFail($purchase['product_id']);
            if($product->category == $fuel_category_id){
                continue;
            }

            $variation = Variation::where('id', $purchase['variation_id'])
                ->where('product_id', $purchase['product_id'])
                ->first();
            $variation_store_d = VariationStoreDetail
                ::where('variation_id', $variation->id)
                ->where('product_id', $purchase['product_id'])
                ->where('product_variation_id', $variation->product_variation_id)
                ->where('store_id', $store_id)
                ->first();
            if (empty($variation_store_d)) {
                $variation_store_d = new VariationStoreDetail();
                $variation_store_d->variation_id = $variation->id;
                $variation_store_d->product_id = $purchase['product_id'];
                $variation_store_d->store_id = $store_id;
                $variation_store_d->product_variation_id = $variation->product_variation_id;
                $variation_store_d->qty_available = 0;
            }
            $variation_store_d->qty_available += $purchase['quantity'];
            $variation_store_d->save();
        }
        return true;
    }
}
