<?php

namespace Modules\HelpGuide\Entities;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    // Override the getTable method to automatically apply the prefix
    public function getTable()
    {
        $table = parent::getTable();

        // Add the prefix only if it's not already present
        if (strpos($table, 'helpguide_') !== 0) {
            $table = 'helpguide_' . $table;
        }

        return $table;
    }
}