<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    private array $permissions = [
        'ai.reports',
        'ai.health',
        'ai.chat',
        'ai.analytics',
    ];

    public function up(): void
    {
        foreach ($this->permissions as $name) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'admin'],
                ['group_name' => 'ai']
            );
        }

        $superAdmin = Role::where('name', 'Super Admin')->where('guard_name', 'admin')->first();

        if ($superAdmin) {
            $superAdmin->givePermissionTo($this->permissions);
        }
    }

    public function down(): void
    {
        foreach ($this->permissions as $name) {
            Permission::where('name', $name)->where('guard_name', 'admin')->delete();
        }
    }
};
