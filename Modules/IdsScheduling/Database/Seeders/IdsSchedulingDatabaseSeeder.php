<?php

namespace Modules\IdsScheduling\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Database\Seeders\IdsCustomQuestionOptionSeeder;
use Modules\IdsScheduling\Database\Seeders\IDSSchedulingModuleTableSeeder;
use Modules\IdsScheduling\Database\Seeders\RolesAndPermissionsOfIDSSchedulingTableSeeder;
use Modules\IdsScheduling\Database\Seeders\ModulePermissionsIDSSchedulingTableSeeder;
use Modules\IdsScheduling\Database\Seeders\IdsPaymentMethodsTableSeeder;

class IdsSchedulingDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $this->call(IDSSchedulingModuleTableSeeder::class);
        $this->call(RolesAndPermissionsOfIDSSchedulingTableSeeder::class);
        $this->call(ModulePermissionsIDSSchedulingTableSeeder::class);
        $this->call(IdsPaymentMethodsTableSeeder::class);
        $this->call(IdsCustomQuestionOptionSeeder::class);
    }
}
