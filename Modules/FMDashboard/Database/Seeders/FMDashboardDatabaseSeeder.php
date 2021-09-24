<?php

namespace Modules\FMDashboard\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class FMDashboardDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        
        $this->call(FMDashboardWidgetTableSeeder::class);
        $this->call(ModuleTableSeeder::class);
        $this->call(RoleAndPermissionsTableSeeder::class);
        $this->call(ModulePermissionsTableSeeder::class);
        $this->call(AddCoursesWidgetSeeder::class);
        // $this->call(SeedCoursesWidgetTableSeeder::class);
        $this->call(AddTrainingCompliancedWidgetPermissionSeederTableSeeder::class);
        
    }
}
