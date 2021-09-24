<?php

namespace Modules\ClientApp\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ClientAppDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(ClientAppModulesTableSeeder::class);
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(ModulePermissionsSeeder::class);
    }
}
