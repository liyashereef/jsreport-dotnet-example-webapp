<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ExitTerminationReasonLookupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Model::unguard();
        \DB::table('exit_termination_reason_lookups')->delete();

        \DB::table('exit_termination_reason_lookups')->insert(array(
            0 => array(
                'id' => 1,
                'reason' => 'Does not meet expectations',
                'shortname' => 'dd',
                'created_at' => '2019-03-12 04:02:47',
                'updated_at' => '2019-03-12 04:02:47',
                'deleted_at' => null,
               
            ),
            1 => array(
                'id' => 2,
                'reason' => 'Not Perfect',
                'shortname' => 'NP',
                'created_at' => '2019-03-12 04:02:47',
                'updated_at' => '2019-03-12 04:02:47',
                'deleted_at' => null,
                
            ),
            2 => array(
                'id' => 3,
                'reason' => 'Not Well',
                'shortname' => 'NW',
                'created_at' => '2019-03-12 04:02:47',
                'updated_at' => '2019-03-12 04:02:47',
                'deleted_at' => null,
        
            ),
            3 => array(
                'id' => 4,
                'reason' => 'Exceeds expectations',
                'shortname' => 4,
                'created_at' => '2019-03-12 04:02:47',
                'updated_at' => '2019-03-12 04:02:47',
                'deleted_at' => null,
            
            ),
            4 => array(
                'id' => 5,
                'rating' => 'Far exceeds expectations',
                'score' => 5,
                'created_at' => '2019-03-12 04:02:47',
                'updated_at' => '2019-03-12 04:02:47',
                'deleted_at' => null,
                
            ),
        ));

    }
        // $this->call("OthersTableSeeder");
    }

