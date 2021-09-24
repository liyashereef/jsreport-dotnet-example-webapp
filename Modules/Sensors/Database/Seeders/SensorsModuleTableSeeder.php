<?php

namespace Modules\Sensors\Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class SensorsModuleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         Module::create(['name' => 'Sensors']);
    }
}
