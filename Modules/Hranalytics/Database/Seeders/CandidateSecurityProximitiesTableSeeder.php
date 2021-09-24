<?php

namespace Modules\Hranalytics\Database\Seeders;

use Illuminate\Database\Seeder;

class CandidateSecurityProximitiesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('candidate_security_proximities')->delete();

        \DB::table('candidate_security_proximities')->insert(array(
            0 => array(
                'id' => 1,
                'candidate_id' => 1,
                'driver_license' => 'I have a valid G1 license',
                'access_vehicle' => 'I do not have access to a vehicle',
                'access_public_transport' => 'I have little access to the client site via public transit',
                'transportation_limitted' => 'Yes',
                'explanation_transport_limit' => 'The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from "de Finibus Bonorum et Malorum" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.',
                'created_at' => '2018-07-25 12:52:16',
                'updated_at' => '2018-07-25 12:52:16',
            ),
        ));

    }
}
