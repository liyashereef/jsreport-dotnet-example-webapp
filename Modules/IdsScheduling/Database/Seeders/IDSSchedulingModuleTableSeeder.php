<?php

namespace Modules\IdsScheduling\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class IDSSchedulingModuleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $module_id = \App\Services\HelperService::getModuleId('IDS Scheduling');
        
        if(empty($module_id)){
            \DB::table('modules')->insert([
                [
                    'name' => 'IDS Scheduling',
                    'created_at' => \Carbon::now(),
                    'updated_at' => \Carbon::now(),
                ]
            ]);
        }
       
        
        

    }
}
