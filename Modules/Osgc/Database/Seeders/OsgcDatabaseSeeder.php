<?php

namespace Modules\Osgc\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Osgc\Database\Seeders\OsgcModuleTableSeeder;
class OsgcDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $this->call(OsgcModuleTableSeeder::class);
    }
}
