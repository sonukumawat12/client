<?php

namespace Modules\HelpGuide\Entities;

use Modules\HelpGuide\Entities\Article;
use Modules\HelpGuide\Entities\ArticleTranslation;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class EmployeeGroup extends Model
{
    
    protected $fillable = ['name'];
    
}
