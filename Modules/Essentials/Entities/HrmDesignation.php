<?php

namespace Modules\Essentials\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HrmDesignation extends Model
{
    use HasFactory;

    protected $fillable = ['business_id','department_id','name','created_by','description'];

    /**
     * Define the relationship with the HrmDepartment model.
     * A designation belongs to a department.
     */
    public function department()
    {
        return $this->belongsTo(HrmDepartment::class, 'department_id');
    }

    /**
     * Define the relationship with the User model.
     * A designation was added by a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
