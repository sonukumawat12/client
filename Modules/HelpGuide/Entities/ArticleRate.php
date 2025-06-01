<?php

namespace Modules\HelpGuide\Entities;

use Illuminate\Database\Eloquent\Model;

class ArticleRate extends Model
{
    protected $fillable = ['article_id','user_id','user_ip','last_rate'];
    protected $table = "article_rate";
}
