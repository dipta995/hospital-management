<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Roles
        $roleSuperAdmin = Role::create(['name' => 'Super Admin', 'guard_name' => 'admin']);


        // Permission List as array
        $permissions = [

            [
                'group_name' => 'dashboards',
                'permissions' => [
                    'dashboards.view',
                ]
            ],
                    // admins Permissions
            [
                'group_name' => 'admins',
                'permissions' => [
                    'admins.create',
                    'admins.index',
                    'admins.edit',
                    'admins.delete',
                ]
            ],
                    // users Permissions
           [
               'group_name' => 'users',
               'permissions' => [
                   'users.create',
                   'users.index',
                   'users.edit',
                   'users.delete',
               ]
           ],

                    // roles Permissions
            [
                'group_name' => 'roles',
                'permissions' => [
                    'roles.create',
                    'roles.index',
                    'roles.edit',
                    'roles.delete',
                ]
            ],
            // settings Permissions
            [
                'group_name' => 'settings',
                'permissions' => [
                    'settings.edit',
                ]
            ],



//            products permissions
            [
                'group_name' => 'products',
                'permissions' => [
                    'products.index',
                    'products.create',
                    'products.edit',
                    'products.delete',
                ]
            ],

//            invoices permissions
            [
                'group_name' => 'invoices',
                'permissions' => [
                    'invoices.index',
                    'invoices.create',
                    'invoices.edit',
                    'invoices.delete',
                ]
            ],

//            reefers
            [
                'group_name' => 'reefers',
                'permissions' => [
                    'reefers.index',
                    'reefers.create',
                    'reefers.edit',
                    'reefers.delete',
                ]
            ],

            //prescriptions

            [
                'group_name' => 'prescriptions',
                'permissions' => [
                    'prescriptions.index',
                    'prescriptions.create',
                    'prescriptions.edit',
                    'prescriptions.delete',
                ]
            ],

            //             categories
            [
                'group_name' => 'categories',
                'permissions' => [
                    'categories.index',
                    'categories.create',
                    'categories.edit',
                    'categories.delete',
                ]
            ],
                        // service_categories
            [
                'group_name' => 'service_categories',
                'permissions' => [
                    'service_categories.index',
                    'service_categories.create',
                    'service_categories.edit',
                    'service_categories.delete',
                ]
            ],
                        // services
            [
                'group_name' => 'services',
                'permissions' => [
                    'services.index',
                    'services.create',
                    'services.edit',
                    'services.delete',
                ]
            ],
                        // admits
            [
                'group_name' => 'admits',
                'permissions' => [
                    'admits.index',
                    'admits.create',
                    'admits.edit',
                    'admits.delete',
                ]
            ],
            // Recepts
            [
                'group_name' => 'recepts',
                'permissions' => [
                    'recepts.index',
                    'recepts.create',
                    'recepts.edit',
                    'recepts.delete',
                ]
            ],
            // ReceptLists
            [
                'group_name' => 'recept_lists',
                'permissions' => [
                    'receptlists.index',
                    'receptlists.create',
                    'receptlists.edit',
                    'receptlists.delete',
                ]
            ],



            //             branches
            [
                'group_name' => 'branches',
                'permissions' => [
                    'branches.index',
                    'branches.create',
                    'branches.edit',
                    'branches.delete',
                ]
            ],

            //             employees
            [
                'group_name' => 'employees',
                'permissions' => [
                    'employees.index',
                    'employees.create',
                    'employees.edit',
                    'employees.delete',
                ]
            ],

            //            employee_salaries
            [
                'group_name' => 'employee_salaries',
                'permissions' => [
                    'employee_salaries.index',
                    'employee_salaries.create',
                    'employee_salaries.edit',
                    'employee_salaries.delete',
                ]
            ],


            //            pharmacy_categories
            [
                'group_name' => 'pharmacy_categories',
                'permissions' => [
                    'pharmacy_categories.index',
                    'pharmacy_categories.create',
                    'pharmacy_categories.edit',
                    'pharmacy_categories.delete',
                ]
            ],


            //            cost_categories
            [
                'group_name' => 'cost_categories',
                'permissions' => [
                    'cost_categories.index',
                    'cost_categories.create',
                    'cost_categories.edit',
                    'cost_categories.delete',
                ]
            ],

            //            costs
            [
                'group_name' => 'costs',
                'permissions' => [
                    'costs.index',
                    'costs.create',
                    'costs.edit',
                    'costs.delete',
                ]
            ],

            //            labs
            [
                'group_name' => 'labs',
                'permissions' => [
                    'labs.index',
                    'labs.create',
                    'labs.edit',
                    'labs.delete',
                ]
            ],

            //            doctor_serials
            [
                'group_name' => 'doctor_serials',
                'permissions' => [
                    'doctor_serials.index',
                    'doctor_serials.create',
                    'doctor_serials.edit',
                    'doctor_serials.delete',
                ]
            ],
            //            doctor_rooms
            [
                'group_name' => 'doctor_rooms',
                'permissions' => [
                    'doctor_rooms.index',
                    'doctor_rooms.create',
                    'doctor_rooms.edit',
                    'doctor_rooms.delete',
                ]
            ],
            //            items
            [
                'group_name' => 'items',
                'permissions' => [
                    'items.index',
                    'items.create',
                    'items.edit',
                    'items.delete',
                ]
            ],

            //            purchases
            [
                'group_name' => 'purchases',
                'permissions' => [
                    'purchases.index',
                    'purchases.create',
                    'purchases.edit',
                    'purchases.delete',
                ]
            ],

            //            purchases
            [
                'group_name' => 'purchase_items',
                'permissions' => [
                    'purchase_items.index',
                    'purchase_items.create',
                    'purchase_items.edit',
                    'purchase_items.delete',
                ]
            ],
            //            payments
            [
                'group_name' => 'payments',
                'permissions' => [
                    'payments.index',
                    'payments.create',
                    'payments.edit',
                    'payments.delete',
                ]
            ],

            //            suppliers
            [
                'group_name' => 'suppliers',
                'permissions' => [
                    'suppliers.index',
                    'suppliers.create',
                    'suppliers.edit',
                    'suppliers.delete',
                ]
            ],

            //            earns
            [
                'group_name' => 'earns',
                'permissions' => [
                    'earns.index',
                    'earns.create',
                    'earns.edit',
                    'earns.delete',
                ]
            ],
  //            test_reports
            [
                'group_name' => 'test_reports',
                'permissions' => [
                    'test_reports.index',
                    'test_reports.create',
                    'test_reports.edit',
                    'test_reports.delete',
                ]
            ],

            //            test_report_demos
            [
                'group_name' => 'test_report_demos',
                'permissions' => [
                    'test_report_demos.index',
                    'test_report_demos.create',
                    'test_report_demos.edit',
                    'test_report_demos.delete',
                ]
            ],



 //            reports
            [
                'group_name' => 'reports',
                'permissions' => [
                    'reports.index',
                    'reports.amounts',
                ]
            ],

            //            number_categories
            [
                'group_name' => 'number_categories',
                'permissions' => [
                    'number_categories.index',
                    'number_categories.create',
                    'number_categories.edit',
                    'number_categories.delete',
                ]
            ],
            //            phone_numbers
            [
                'group_name' => 'phone_numbers',
                'permissions' => [
                    'phone_numbers.index',
                    'phone_numbers.create',
                    'phone_numbers.edit',
                    'phone_numbers.delete',
                ]
            ],

            //            pharmacy_units
            [
                'group_name' => 'pharmacy_units',
                'permissions' => [
                    'pharmacy_units.index',
                    'pharmacy_units.create',
                    'pharmacy_units.edit',
                    'pharmacy_units.delete',
                ]
            ],

            //            pharmacy_types
            [
                'group_name' => 'pharmacy_types',
                'permissions' => [
                    'pharmacy_types.index',
                    'pharmacy_types.create',
                    'pharmacy_types.edit',
                    'pharmacy_types.delete',
                ]
            ],

            //            pharmacy_brands
            [
                'group_name' => 'pharmacy_brands',
                'permissions' => [
                    'pharmacy_brands.index',
                    'pharmacy_brands.create',
                    'pharmacy_brands.edit',
                    'pharmacy_brands.delete',
                ]
            ],

            //            pharmacy_purchases
            [
                'group_name' => 'pharmacy_purchases',
                'permissions' => [
                    'pharmacy_purchases.index',
                    'pharmacy_purchases.create',
                    'pharmacy_purchases.edit',
                    'pharmacy_purchases.delete',
                ]
            ],

            //            pharmacy_products
            [
                'group_name' => 'pharmacy_products',
                'permissions' => [
                    'pharmacy_products.index',
                    'pharmacy_products.create',
                    'pharmacy_products.edit',
                    'pharmacy_products.delete',
                ]
            ],

            //            pharmacy_sales
            [
                'group_name' => 'pharmacy_sales',
                'permissions' => [
                    'pharmacy_sales.index',
                    'pharmacy_sales.create',
                    'pharmacy_sales.edit',
                    'pharmacy_sales.delete',
                ]
            ],


        ];


        // Create and Assign Permissions
        for ($i = 0; $i < count($permissions); $i++) {
            $permissionGroup = $permissions[$i]['group_name'];
            for ($j = 0; $j < count($permissions[$i]['permissions']); $j++) {
                // Create Permission
                $permission = Permission::create([
                    'name' => $permissions[$i]['permissions'][$j], 'group_name' => $permissionGroup,
                    'guard_name' => 'admin'
                ]);
                $roleSuperAdmin->givePermissionTo($permission);
                $permission->assignRole($roleSuperAdmin);
            }
        }

        //to register super admin at model has roles table
        //here role_id =superadmin model_id=customer_id
        DB::table('model_has_roles')->insert([
            'role_id' => 1,
            'model_type' => 'App\Models\Admin',
            'model_id' => 1

        ]);
    }
}
