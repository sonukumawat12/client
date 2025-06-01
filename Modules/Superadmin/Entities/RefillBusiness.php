<?php

namespace Modules\Superadmin\Entities;

use Illuminate\Database\Eloquent\Model;

class RefillBusiness extends Model
{
    protected $fillable = [];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $table = 'refill_business';
    protected $guarded = ['id'];
}
