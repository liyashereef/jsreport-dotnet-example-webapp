<?php

namespace Modules\KeyManagement\Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class KeyManagementModuleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       // create Module
          Module::create(['name' => 'Key Management']);
    }
}
