<?php

namespace Modules\Vat\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class VatVariation extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;

    protected static $logName = 'Variation';

    use SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['fillable', 'some_other_attribute']);
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'combo_variations' => 'array',
        'default_multiple_unit_price' => 'array',
    ];

    public function product_variation()
    {
        return $this->belongsTo(\Modules\Vat\Entities\VatProductVariation::class, 'product_variation_id');
    }

    public function product()
    {
        return $this->belongsTo(Modules\Vat\Entities\VatProduct::class, 'product_id');
    }

    

    public function media()
    {
        return $this->morphMany(\App\Media::class, 'model');
    }

    public function getFullNameAttribute()
    {
        $name = $this->product->name;
        if ($this->product->type == 'variable') {
            $name .= ' - ' . $this->product_variation->name . ' - ' . $this->name;
        }
        $name .= ' (' . $this->sub_sku . ')';

        return $name;
    }
    public static function getVariationDropdown($business_id, $variation_id = null)
    {
        $q = VatProduct::leftJoin(
            'vat_variations',
            'vat_products.id',
            '=',
            'vat_variations.product_id'
        )
            ->active()
            ->where('business_id', $business_id)
            ->whereNull('vat_variations.deleted_at')
            ->select(
                'vat_products.id as product_id',
                'vat_products.name',
                'vat_products.type',
                'vat_variations.id as variation_id',
                'vat_variations.name as variation',
                'vat_variations.sub_sku as sub_sku'
            )->whereIn('vat_products.type', ['variable', 'variable_only_in_sale'])
            ->groupBy('variation_id');

        
        if (!empty($variation_id)) {
            $vari = VatVariation::where('id', $variation_id)->first();
            if (!empty($vari)) {
                $q->where('vat_products.id', $vari->product_id);
            }
            $q->where('vat_variations.id', '!=', $variation_id);
        }

        $products = $q->get();

        $products_array = [];
        foreach ($products as $product) {
            $products_array[$product->product_id]['name'] = $product->name;
            $products_array[$product->product_id]['sku'] = $product->sub_sku;
            $products_array[$product->product_id]['type'] = $product->type;
            $products_array[$product->product_id]['variations'][]
                = [
                    'variation_id' => $product->variation_id,
                    'variation_name' => $product->variation,
                    'sub_sku' => $product->sub_sku
                ];
        }

        $result = [];
        $i = 1;
        $no_of_records = $products->count();
        if (!empty($products_array)) {
            foreach ($products_array as $key => $value) {
                if ($no_of_records > 1 && $value['type'] != 'single') {
                    $result[$key] = $value['name'] . ' - ' . $value['sku'];
                }
                $name = $value['name'];
                foreach ($value['variations'] as $variation) {
                    $text = $name;
                    if ($value['type'] == 'variable' || $value['type'] == 'variable_only_in_sale') {
                        if ($variation['variation_name'] != 'DUMMY') {
                            $text = $text . ' (' . $variation['variation_name'] . ')';
                        }
                    }
                    $i++;
                    $result[$variation['variation_id']] = $text . ' - ' . $variation['sub_sku'];
                }
                $i++;
            }
        }

        return $result;
    }
}
