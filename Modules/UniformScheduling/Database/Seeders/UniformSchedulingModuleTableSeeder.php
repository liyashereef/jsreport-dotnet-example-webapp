<?php

namespace Modules\UniformScheduling\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class UniformSchedulingModuleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $module_id = \App\Services\HelperService::getModuleId('Uniform Scheduling');

        if(empty($module_id)){
            \DB::table('modules')->insert([
                [
                    'name' => 'Uniform Scheduling',
                    'created_at' => \Carbon::now(),
                    'updated_at' => \Carbon::now(),
                ]
            ]);
        }
    }
}
