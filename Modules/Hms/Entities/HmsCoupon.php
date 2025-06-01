<?php

namespace Modules\Hms\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HmsCoupon extends Model
{
    use HasFactory;
    protected $guarded = ['id']; 
    public function hmsRoomTypes()
    {
        // Assuming the foreign key is hms_room_type_id in hms_coupons that links to the room_types table
        return $this->hasMany(HmsRoomType::class, 'id', 'hms_room_type_id');
    }
}
