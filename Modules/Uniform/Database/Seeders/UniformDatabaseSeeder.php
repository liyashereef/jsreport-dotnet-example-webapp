<?php

namespace Modules\Uniform\Database\Seeders;

use Illuminate\Database\Seeder;
class UniformDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UniformModuleTableSeeder::class);
        $this->call(RolesAndPermissionUniformTableSeeder::class);
        $this->call(ModulesPermissionUniformTableSeeder::class);
        $this->call(UraSettingsTableSeeder::class);
        $this->call(UraRateTableSeeder::class);
        $this->call(UraOperationTypeTableSeeder::class);
        $this->call(UniformOrderStatusTableSeeder::class);
        $this->call(UniformEmailTemplateSeeder::class);
        // $this->call(UniformDeleteRateAppliedPermissionSeeder::class);
    }
}