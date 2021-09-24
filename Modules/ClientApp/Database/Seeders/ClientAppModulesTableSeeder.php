<?php

namespace Modules\ClientApp\Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ClientAppModulesTableSeeder extends Seeder
{
    public function run()
    {
        // create Module
        Module::create(['name' => 'Client App']);

    }
}
