<?php

namespace Modules\KeyManagement\Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class KeyManagementDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(KeyManagementModuleTableSeeder::class);
        $this->call(RolesAndPermissionsKeyManagementTableSeeder::class);
        $this->call(ModulePermissionsKeyManagementTableSeeder::class);
        $this->call(RolesAndPermissionsViewAllocatedKeyLogSummaryTableSeeder::class);
        $this->call(ModulePermissionsViewAllocatedKeyLogSummaryTableSeeder::class);
    }
}
