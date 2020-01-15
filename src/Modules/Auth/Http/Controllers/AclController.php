<?php
namespace IlyasDeckers\BaseModule\Modules\Auth\Http\Controllers;

use IlyasDeckers\BaseModule\Modules\Auth\Resources\RoleResource;
use Clockwork\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AclController extends Controller
{
    /**
     * List roles
     *
     * @param  Request request
     */
    public function assignPermissions(Request $request)
    {
        $role = Role::findOrFail($request->id);
        $permissions = $role->permissions;

        $permissions->each(function ($permission) use ($role) {
            $role->revokePermissionTo($permission->name);
        });

        foreach ($request->permissions as $permission) {
            $role->givePermissionTo($permission['name']);
        }

        return new RoleResource(
            Role::with('permissions')->findOrFail($request->id)
        );
    }
}