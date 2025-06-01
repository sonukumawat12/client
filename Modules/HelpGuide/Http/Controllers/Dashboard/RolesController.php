<?php

namespace Modules\HelpGuide\Http\Controllers\Dashboard;

use Modules\HelpGuide\Entities\Role;

use Modules\HelpGuide\Models\Module;
use Illuminate\Http\Request;
use Modules\HelpGuide\Http\Controllers\Controller;
use Modules\HelpGuide\Http\Resources\RoleResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class RolesController extends Controller
{
    public function listRoles(Request $request)
    {
        if($request->input('type') == "customer") $r = ['customer'];
        else $r = ['admin','agent','non-restricted_agent','super_admin'];

        $roles = Role::whereIn('name', $r)->get();
        return RoleResource::collection($roles);
    }

    public function roles()
    {
        $roles = Role::select('id', 'name')
                    // Exclude super admin
                    ->whereNotIn('id', ['1'])
                    ->withCount('users','permissions')
                    ->paginate(20);
        return $roles;
    }

    public function permissions(Request $request, $id)
    {
        $role = Role::select('id', 'name')->find($id);
        
        // Make sure the role is exists
        if( !$role ){
            return Response::json([
                'message' => __('Role not defined')
            ], 422);
        }

        // $this->authorize('view', $role);

        return [
            'role' => $role,
            'role_permissions' => $role->permissions()->select('id','name')->get(),
            'permissions' => config('app_list_permissions'),
            'modules_permissions' => Module::permissions()
        ];
    }
    public function togglePermissions(Request $request, $id, $permission)
    {
        $permissions = array_merge(config('app_list_permissions'), Module::permissions());
        
        $permissions = call_user_func_array('array_merge', array_values($permissions) );
        
        // Exclude super admin
        $role = Role::find($id);

        // Make sure the role is exists
        if( !$role ){
            return Response::json([
                'message' => __('Role not defined')
            ], 422);
        }

        // $this->authorize('update', $role);

        // Make sure the permission is exists
        if( !in_array($permission, $permissions) ){
            return Response::json([
                'message' => __('Permission not defined')
            ], 422);
        }

         // Is permission allowed
         if( $request->input('action') == "assign" && Role::disallowed($permission) ){
            return Response::json([
                'message' => __('Permission :permission can not be assigned', ['permission' => $permission])
            ], 422);
        }

        // Add permission to databse if not exists
        Permission::findOrCreate($permission, 'web');

        // Assign / remove permission
        if( $request->input('action') == "assign"){
            $msg = __('Permission :permission has been assigned to :role role', ['permission' => $permission, 'role' => $role->name]);
            $role->givePermissionTo($permission);
        } else {
            $msg = __('Permission :permission has been revoked from :role role', ['permission' => $permission, 'role' => $role->name]);
            $role->revokePermissionTo($permission);
        }

        return [
            'message' => $msg,
            'role' => $role,
            'role_permissions' => $role->permissions()->select('id','name')->get(),
            'permissions' => config('app_list_permissions'),
            'modules_permissions' => Module::permissions()
        ];
        
    }
}
