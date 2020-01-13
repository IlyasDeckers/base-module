<?php
namespace Clockwork\Base\Modules\Auth\Http\Controllers;

use Clockwork\Base\Modules\Auth\Resources\RoleResource;
use Clockwork\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * List roles
     *
     * @param  Request request
     */
    public function index(Request $request)
    {
        return RoleResource::collection(
            Role::with('permissions')->get()
        );
    }

    /**
     * Show roles
     *
     * @param  Request request
     */
    public function show(Request $request, $id)
    {
        return new RoleResource(
            Role::with('permissions')->findOrFail($id)
        );
    }
}