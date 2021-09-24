<?php

namespace Modules\Admin\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class UserEmergencyContactRelationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('user_emergency_contact_relations')->delete();

        \DB::table('user_emergency_contact_relations')->insert(
            array(
                0 => array(
                    'id' => 1,
                    'relations' => 'Spouse',
                    'apogee_code' => '0',
                    'created_by' => 1,
                    'updated_by' => 1,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                    'deleted_at' => null,
                ),
                1 => array(
                    'id' => 2,
                    'relations' => 'Parent',
                    'apogee_code' => '1',
                    'created_by' => 1,
                    'updated_by' => 1,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                    'deleted_at' => null,
                ),
                2 => array(
                    'id' => 3,
                    'relations' => 'Sibling',
                    'apogee_code' => '2',
                    'created_by' => 1,
                    'updated_by' => 1,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                    'deleted_at' => null,
                ),
                3 => array(
                    'id' => 4,
                    'relations' => 'Uncle',
                    'apogee_code' => '3',
                    'created_by' => 1,
                    'updated_by' => 1,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                    'deleted_at' => null,
                ),
            )
        );
    }
}
