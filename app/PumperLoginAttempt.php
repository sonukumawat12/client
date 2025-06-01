<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PumperLoginAttempt extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $table = 'pumper_login_attempts';

    protected $guarded = ['id'];
}
