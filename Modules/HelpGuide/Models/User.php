<?php

namespace Modules\HelpGuide\Models;

use Modules\HelpGuide\Entities\User as BaseUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends BaseUser
{
    use HasFactory;
}
