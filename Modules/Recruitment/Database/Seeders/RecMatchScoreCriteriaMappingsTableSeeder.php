<?php

namespace Modules\Recruitment\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RecMatchScoreCriteriaMappingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::connection('mysql_rec')->table('rec_match_score_criteria_mappings')->whereIn('criteria', [9,10])->delete();

        \DB::connection('mysql_rec')->table('rec_match_score_criteria_mappings')->insert(array(
            0 => array(
                //'id' => 1,
                'criteria' => 9,
                'limit' => 1,
                'score'=>1.25,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ),
            1 => array(
                //'id' => 1,
                'criteria' => 9,
                'limit' => 2,
                'score'=>2.50,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ),
            2 => array(
                //'id' => 1,
                'criteria' => 9,
                'limit' => 3,
                'score'=>3.75,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ),
            3 => array(
                //'id' => 1,
                'criteria' => 9,
                'limit' => 4,
                'score'=>5.00,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ),
            4 => array(
                //'id' => 1,
                'criteria' => 10,
                'limit' => 1,
                'score'=>1.67,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ),
            5 => array(
                //'id' => 1,
                'criteria' => 10,
                'limit' => 2,
                'score'=>3.34,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ),
            6 => array(
                //'id' => 1,
                'criteria' => 10,
                'limit' => 3,
                'score'=>5.00,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ),
        ));
    }
}
