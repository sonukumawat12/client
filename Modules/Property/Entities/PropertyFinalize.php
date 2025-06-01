<?php

namespace Modules\Property\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PropertyFinalize extends Model
{
    protected $fillable = [];

    protected $guarded = ['id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'reason_id' => 'array'
    ];
    
    public function property(){
        $this->belongsTo(\Modules\Property\Entities\Property::class);
    }
    
    
 /*      
    public static function getInstallmentDetailsByPropertyBlock($property_id, $append_block = true){
      
   
        
    $cards = DB::select("SELECT first_installment_date, installment_amount from property_finalizes where block_id = $property_id);
    return $cards; 
       

    }*/
    
}
