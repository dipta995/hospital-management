<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SecurityPermissionService
{
    /**
     * @return array{installed:int, total:int, missing:string[], ready:bool, roles:array<string,int>}
     */
    public function status(): array
    {
        $names = array_keys(SecurityService::PERMISSIONS);
        $existing = Permission::where('guard_name', 'admin')->whereIn('name', $names)->pluck('name')->all();
        $missing = array_values(array_diff($names, $existing));

        $roles = Role::where('guard_name', 'admin')
            ->withCount(['permissions' => fn ($q) => $q->where('group_name', SecurityService::PERMISSION_GROUP)])
            ->get()
            ->filter(fn ($role) => $role->permissions_count > 0)
            ->mapWithKeys(fn ($role) => [$role->name => $role->permissions_count])
            ->all();

        return [
            'installed' => count($existing),
            'total' => count($names),
            'missing' => $missing,
            'ready' => empty($missing),
            'roles' => $roles,
        ];
    }

    /**
     * Additive only. Newly created permissions go to Owner so nobody loses access they had
     * before; permissions that already exist keep whatever roles an admin has chosen.
     *
     * @return array{success:bool, message:string}
     */
    public function install(): array
    {
        try {
            $created = DB::transaction(function () {
                $created = [];
                foreach (array_keys(SecurityService::PERMISSIONS) as $name) {
                    $permission = Permission::firstOrCreate(
                        ['name' => $name, 'guard_name' => 'admin'],
                        ['group_name' => SecurityService::PERMISSION_GROUP]
                    );
                    if ($permission->wasRecentlyCreated) {
                        $created[] = $permission;
                    } elseif ($permission->group_name !== SecurityService::PERMISSION_GROUP) {
                        $permission->update(['group_name' => SecurityService::PERMISSION_GROUP]);
                    }
                }

                $ownerGets = array_values(array_filter(
                    $created,
                    fn ($permission) => !in_array($permission->name, SecurityService::EXPLICIT_ONLY_PERMISSIONS, true)
                ));
                foreach (Role::where('guard_name', 'admin')->whereIn('name', SecurityService::APPROVER_ROLES)->get() as $role) {
                    $role->givePermissionTo($role->name === 'Super Admin' ? array_keys(SecurityService::PERMISSIONS) : $ownerGets);
                }

                return count($created);
            });
        } catch (\Throwable $e) {
            report($e);

            return ['success' => false, 'message' => 'Security permission install করা যায়নি: ' . $e->getMessage()];
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        SecurityService::flushCache();

        return [
            'success' => true,
            'message' => $created > 0
                ? "{$created}টি নতুন security permission যোগ হয়েছে। Trash মুছে ফেলার permission কাউকে দেওয়া হয়নি, বাকিগুলো Owner পেয়েছে। Roles থেকে যেকোনো role-এ দিতে/সরাতে পারবেন।"
                : 'সব security permission আগেই install করা আছে।',
        ];
    }
}
