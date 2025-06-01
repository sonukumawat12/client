<?php

namespace Modules\HelpGuide\Entities;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Role extends Model
{
    // Permissions that must be assigned maunally
    private static $disallowedPermissions = [
        'assign_role',
        'create_role',
        'edit_role',
        'delete_role',
        'view_role',
        'permanently_delete_role',
        'assign_permissions',
        'manage_acl',
        'upload_module',
        'list_modules',
        'manage_modules',
        'update_application'
    ];

    public static function disallowed($permission)
    {
        return in_array($permission, self::$disallowedPermissions);
    }

    public function users()
    {
        return $this->morphedByMany(User::class, 'model', 'model_has_roles', 'role_id', 'model_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions', 'role_id', 'permission_id');
    }
}
