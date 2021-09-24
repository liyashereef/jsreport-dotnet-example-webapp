<?php

namespace Modules\Hranalytics\Database\Seeders;

use Illuminate\Database\Seeder;

class CandidatesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('candidates')->delete();

        \DB::table('candidates')->insert(array(
            0 => array(
                'id' => 1,
                'name' => 'Haviva',
                'email' => 'haviva@ymail.com',
                'phone_home' => '(444)456-8567',
                'phone_cellular' => '(535)444-4465',
                'address' => '647-9008 Parturient Road',
                'city' => '336-9023 Sem St.',
                'postal_code' => 'K1A0B1',
                'geo_location_lat' => null,
                'geo_location_long' => null,
                'created_at' => '2018-07-25 12:52:16',
                'updated_at' => '2018-07-25 12:52:16',
                'deleted_at' => null,
            ),
        ));

    }
}
