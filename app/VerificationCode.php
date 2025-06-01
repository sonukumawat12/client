<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VerificationCode extends Model
{
    use HasFactory;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;


   // use SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        VerificationCode::creating(function($model) {
            $model->code = self::getCode();
        });
    }

   static function getCode(){
        $random_code = rand(9999,100000);
        if(VerificationCode::where('code', $random_code)->first())
        {
            self::getCode();
        }
        return $random_code;
    }

    /**
     * Get the user associated with the VerificationCode
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
