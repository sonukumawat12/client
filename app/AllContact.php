<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllContact extends Model
{
    use HasFactory;
    protected $table = 'all_contacts';
    public $timestamps = false; // Views don’t usually have timestamps

    public function showDropdown()
    {
        $contacts = AllContact::all();
        return $contacts;
    }
}


