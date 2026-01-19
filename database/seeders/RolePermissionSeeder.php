<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Role;
use App\Models\RolePermission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$modelWithAllActions = [
			'Profile',
			'Account',
			'Appearance',
			'Setting',
			'User',
			'Role',
			'RolePermission',
			'Translation',
		];
		
		$permissions = [];
		foreach ($modelWithAllActions as $modelWithAllAction) {
			$actions = ['CREATE', 'READ', 'UPDATE', 'DELETE', 'AUDIT'];
			foreach ($actions as $action) {
				$modelName = preg_replace('/([a-z0-9])([A-Z])/', '$1-$2', $modelWithAllAction);
				$permissions[] = [
					'id' => Str::uuid(),
					'module' => $modelName,
					'name' => $action,
					'slug' => strtolower($modelName) . ':' . strtolower($action),
					'developer_only' => 0,
					'status' => 'ACTIVE',
				];
			}
		}
		
        DB::table('permissions')->insert($permissions);

        $role = Role::where('name', 'Admin')->first();
        if ($role && $role->name === 'Admin') {
            foreach ($permissions as $permission) {
                RolePermission::create([
                    'id' => Str::uuid()->toString(),
                    'role_id' => $role->id,
                    'permission_id' => $permission['id'],
                ]);
            }
        }

		$developerRole = Role::where('name', 'Developer')->first();
		if ($developerRole && $developerRole->name === 'Developer') {
			foreach ($permissions as $permission) {
				RolePermission::create([
					'id' => Str::uuid()->toString(),
					'role_id' => $developerRole->id,
					'permission_id' => $permission['id'],
				]);
			}
		}
    }
}
