<?php

namespace Modules\Member\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Province extends Model
{
    use HasFactory;

    protected $fillable = ['country_id','name','created_by','business_id'];

    // protected static function newFactory()
    // {
    //     return \Modules\Dsr\Database\factories\ProvinceFactory::new();
    // }
    /**
     * Get all of the Electrorate for the Province
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function electrorate()
    {
        return $this->hasMany(Electrorate::class, 'province_id');
    }


}
