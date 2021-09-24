<?php

namespace Modules\Hranalytics\Database\Seeders;

use Illuminate\Database\Seeder;

class CandidateAttachmentsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('candidate_attachments')->delete();

        \DB::table('candidate_attachments')->insert(array(
            0 => array(
                'id' => 1,
                'candidate_id' => 1,
                'attachment_id' => 1,
                'attachment_file_name' => 'haviva_1532523187__1_Book2GSDGSG.xls',
                'created_at' => '2018-07-25 12:53:07',
                'updated_at' => '2018-07-25 12:53:07',
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'candidate_id' => 1,
                'attachment_id' => 2,
                'attachment_file_name' => 'haviva_1532523187__2_Book1GSFG.xlsx',
                'created_at' => '2018-07-25 12:53:07',
                'updated_at' => '2018-07-25 12:53:07',
                'deleted_at' => null,
            ),
        ));

    }
}
