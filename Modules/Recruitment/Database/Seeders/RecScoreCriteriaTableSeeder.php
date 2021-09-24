<?php

namespace Modules\Recruitment\Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class RecScoreCriteriaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call("OthersTableSeeder");
        \DB::connection('mysql_rec')->table('rec_score_criterias')->delete();
        \DB::connection('mysql_rec')->table('rec_score_criterias')->insert([
            0 => [
                'id' => 1,
                'criteria_name' => 'Wage Premium',
                'type_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ],
            1 => [
                'id' => 2,
                'criteria_name' => 'Travel Time',
                'type_id' => 4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ],
            2 => [
                'id' => 3,
                'criteria_name' => 'Shift Match',
                'type_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ],
             3 => [
                'id' => 4,
                'criteria_name' => 'Schedule Match',
                'type_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
             ],
             4 => [
                'id' => 5,
                'criteria_name' => 'Hours Per Week',
                'type_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
             ],
             5 => [
                'id' => 6,
                'criteria_name' => 'Experience',
                'type_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
             ],
             6 => [
                'id' => 7,
                'criteria_name' => 'Case Study',
                'type_id' => 6,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
             ],
             7 => [
                'id' => 8,
                'criteria_name' => 'Personality',
                'type_id' => 5,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
             ],
              8 => [
                'id' => 9,
                'criteria_name' => 'Career',
                'type_id' => 5,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
              ],
              9 => [
                'id' => 10,
                'criteria_name' => 'Knowledge of CGL',
                'type_id' => 5,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
              ],
               10 => [
                'id' => 11,
                'criteria_name' => 'Responsibility',
                'type_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
               ],
        ]);
    }
}
