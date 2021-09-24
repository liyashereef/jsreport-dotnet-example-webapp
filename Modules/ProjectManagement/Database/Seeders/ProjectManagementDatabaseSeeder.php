<?php

namespace Modules\ProjectManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ProjectManagementDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $this->call(ProjectManagementModuleSeeder::class);
        $this->call(RolesAndPermissionsProjectManagementSeeder::class);
        $this->call(ModulePermissionsProjectManagementSeeder::class);
         $this->call(RolesAndPermissionsProjectManagementUpdateSeeder::class);
        $this->call(ModulePermissionsProjectManagementUpdateSeeder::class);
        
    }
}
