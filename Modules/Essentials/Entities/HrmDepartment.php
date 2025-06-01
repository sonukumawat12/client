<?php

namespace Modules\Essentials\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HrmDepartment extends Model
{
    use HasFactory;

    protected $fillable = ['business_id','name','short_code','description','created_by'];



    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function designations()
    {
        return $this->hasMany(HrmDesignation::class, 'department_id');
    }

}
