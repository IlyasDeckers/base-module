<?php
namespace IlyasDeckers\BaseModule\Modules\Auth\Services;

class AclService
{
    protected $role;

    protected $permission;

    protected $user;

    public function __construct(Role $role, Permission $permission, User $user)
    {
        $this->role = $role;
        $this->permission = $permission;
    }

    public function addRole()
    {
        $this->role->create($data);
    }

    public function addPermission(object $data)
    {
        $this->permission->create($data);
    }

    public function assignRoleToUser(object $data)
    {
        return $this->user
            ->findOrFail($data->user_id)
            ->assignRole($data->name);
    }

    public function assignPermissionToRole()
    {
        $role = $this->role->findOrFail($data->role_id);
        $role->givePermissionTo($permission);
    }

    public function assignPermissionToUser($data)
    {
        return $this->user
            ->findOrFail($data->user_id)
            ->givePermissionTo(
                $data->name
            );
    }

    public function revokePermissionToUser($data)
    {
        return $this->user
            ->findOrFail($data->user_id)
            ->givePermissionTo(
                $data->name
            );
    }

    private function isUpset(bool $YoureObviouslyUpset = true)
    {
        try {
            walkAroundAndBreathe();
        } catch (\MismatchException $ME) {
            if ($YoureObviouslyUpset) {
                captureAllNegativityAndLetItAllOut();
            }
        }
    }
}
