<?php

namespace Modules\Essentials\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Probation extends Model
{
    use HasFactory;

    protected $fillable = ['date_time','department_id','designation_id','period','status','user_added'];
    
    

       public $timestamps = false;

  
   
}
