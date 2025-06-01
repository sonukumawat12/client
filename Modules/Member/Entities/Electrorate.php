<?php

namespace Modules\Member\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Electrorate extends Model
{
    use HasFactory;

    protected $fillable = ['business_id','name','district_id','province_id','created_by'];

    /**
     * Get the district associated with the Electrorate
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function district()
    {
        return $this->hasOne(District::class,'id', 'district_id');
    }

    /**
     * Get the province associated with the Electrorate
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function province()
    {
        return $this->hasOne(Province::class, 'id', 'province_id');
    }

    
}
