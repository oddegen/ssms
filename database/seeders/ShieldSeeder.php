<?php

namespace Database\Seeders;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[
            {"name":"Admin","guard_name":"web","permissions":["view_role","view_any_role","create_role","update_role","delete_role","delete_any_role","view_student","view_any_student","create_student","update_student","restore_student","restore_any_student","replicate_student","reorder_student","delete_student","delete_any_student","force_delete_student","force_delete_any_student","page_Marks","view_grade","view_any_grade","create_grade","update_grade","restore_grade","restore_any_grade","replicate_grade","reorder_grade","delete_grade","delete_any_grade","force_delete_grade","force_delete_any_grade","view_subject","view_any_subject","create_subject","update_subject","restore_subject","restore_any_subject","replicate_subject","reorder_subject","delete_subject","delete_any_subject","force_delete_subject","force_delete_any_subject","view_teacher","view_any_teacher","create_teacher","update_teacher","restore_teacher","restore_any_teacher","replicate_teacher","reorder_teacher","delete_teacher","delete_any_teacher","force_delete_teacher","force_delete_any_teacher","view_score","view_any_score","create_score","update_score","restore_score","restore_any_score","replicate_score","reorder_score","delete_score","delete_any_score","force_delete_score","force_delete_any_score","page_MyStudents"]},
            {"name":"Teacher","guard_name":"web","permissions":["view_score","view_any_score","create_score","update_score","restore_score","restore_any_score","replicate_score","reorder_score","delete_score","delete_any_score","force_delete_score","force_delete_any_score","page_MyStudents"]},
            {"name":"Student","guard_name":"web","permissions":["page_Marks"]}
        ]';
        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
