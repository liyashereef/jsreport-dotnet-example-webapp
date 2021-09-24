<?php

namespace Modules\Osgc\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class OsgcModuleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $module_id = \App\Services\HelperService::getModuleId('Osgc');
        if(empty($module_id)){
            \DB::table('modules')->insert([
                [
                    'name' => 'Osgc',
                    'created_at' => \Carbon::now(),
                    'updated_at' => \Carbon::now(),
                ]
            ]);
        }
    }
}
