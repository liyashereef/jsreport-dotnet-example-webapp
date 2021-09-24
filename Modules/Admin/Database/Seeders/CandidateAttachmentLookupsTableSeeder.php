<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class CandidateAttachmentLookupsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('candidate_attachment_lookups')->delete();

        \DB::table('candidate_attachment_lookups')->insert(array(
            0 => array(
                'id' => 1,
                'attachment_name' => 'Copy Of Security Guard License (Front and Back)',
                'created_at' => '2018-01-03 18:30:00',
                'updated_at' => '2018-01-03 18:30:00',
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'attachment_name' => 'Copy Of First Aid Certificate',
                'created_at' => '2018-01-03 18:30:00',
                'updated_at' => '2018-01-03 18:30:00',
                'deleted_at' => null,
            ),
            2 => array(
                'id' => 3,
                'attachment_name' => 'Void Check (Or Bank Printout Showing Account Information)',
                'created_at' => '2018-01-03 18:30:00',
                'updated_at' => '2018-01-03 18:30:00',
                'deleted_at' => null,
            ),
            3 => array(
                'id' => 4,
                'attachment_name' => 'Copy Of Your Driver\'s License (Front and Back)',
                'created_at' => '2018-01-03 18:30:00',
                'updated_at' => '2018-01-03 18:30:00',
                'deleted_at' => null,
            ),
            4 => array(
                'id' => 5,
                'attachment_name' => 'Copy Of Your Birth Certificate Or Passport (Front and Back)',
                'created_at' => '2018-01-03 18:30:00',
                'updated_at' => '2018-01-03 18:30:00',
                'deleted_at' => null,
            ),
            5 => array(
                'id' => 6,
                'attachment_name' => 'Copy Of Your Social Insurance Number (Front and Back)',
                'created_at' => '2018-01-03 18:30:00',
                'updated_at' => '2018-01-03 18:30:00',
                'deleted_at' => null,
            ),
            6 => array(
                'id' => 7,
                'attachment_name' => 'Copy Of Your Wallet Card (Record of Service)',
                'created_at' => '2018-01-03 18:30:00',
                'updated_at' => '2018-01-03 18:30:00',
                'deleted_at' => null,
            ),
            7 => array(
                'id' => 8,
                'attachment_name' => 'Copy Of Your Current Resume',
                'created_at' => '2018-01-03 18:30:00',
                'updated_at' => '2018-01-03 18:30:00',
                'deleted_at' => null,
            ),
            8 => array(
                'id' => 9,
                'attachment_name' => 'Copy Of Military Service',
                'created_at' => '2018-01-03 18:30:00',
                'updated_at' => '2018-01-03 18:30:00',
                'deleted_at' => null,
            ),
            9 => array(
                'id' => 10,
                'attachment_name' => 'CBRE Physical Fitness Form',
                'created_at' => '2019-09-19 18:30:00',
                'updated_at' => '2019-09-19 18:30:00',
                'deleted_at' => null,
            ),
        ));

    }
}
