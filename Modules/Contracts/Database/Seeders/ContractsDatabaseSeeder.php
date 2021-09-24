<?php

namespace Modules\Contracts\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ContractsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $this->call(RolesAndPermissionsPostOrderTableSeeder::class);
        $this->call(ModulePermissionPostOrderTableSeeder::class);
        $this->call(RolesAndPermissionsRFPCatalogueTableSeeder::class);
        $this->call(ModulePermissionRFPCatalogueTableSeeder::class);
        $this->call(RolesAndPermissionsStatusApprovalTableSeeder::class);
        $this->call(ModulePermissionStatusApprovalTableSeeder::class);
        $this->call(Onboarding\RolesAndPermissionsClientOnboadingTrackingPermissionSeeder::class);
        $this->call(Onboarding\ModulePermissionClientOnboadingTrackingSeeder::class);        
    }
}
