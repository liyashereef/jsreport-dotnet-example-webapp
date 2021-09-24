<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class DivisionLookupsTableDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        \DB::table('division_lookups')->delete();

        \DB::table('division_lookups')->insert(array(
            0 => array(
                'id' => 1,
                'division_name' => 'British Columbia',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'division_name' => 'Great Lakes',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => null,
            ),
            2 => array(
                'id' => 3,
                'division_name' => 'Hamilton',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => null,
            ),
            3 => array(
                'id' => 4,
                'division_name' => 'Kingston',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => null,
            ),
            4 => array(
                'id' => 5,
                'division_name' => 'Manitoba',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => null,
            ),
            5 => array(
                'id' => 6,
                'division_name' => 'New Brunswick & P.E.I',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => null,
            ),
            6 => array(
                'id' => 7,
                'division_name' => ' Newfoundland and Labrador',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => null,
            ),
            7 => array(
                'id' => 8,
                'division_name' => 'North Alberta,NWT & Nunavut',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => null,
            ),
            8 => array(
                'id' => 9,
                'division_name' => 'North Saskatchewan',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => null,
            ),
            9 => array(
                'id' => 10,
                'division_name' => 'Nova Scotia',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => null,
            ),
            10 => array(
                'id' => 11,
                'division_name' => 'Ottawa',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => null,
            ),
            11 => array(
                'id' => 12,
                'division_name' => 'Quebec',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => null,
            ),
            12 => array(
                'id' => 13,
                'division_name' => 'Southern Alberta',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => null,
            ),
            13 => array(
                'id' => 14,
                'division_name' => 'South Saskatchewan',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => null,
            ),
            14 => array(
                'id' => 15,
                'division_name' => 'Victoria, The Islands and Yukon',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => null,
            ),
        ));
    }
}
