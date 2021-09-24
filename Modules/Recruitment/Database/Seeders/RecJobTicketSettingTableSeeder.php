<?php

namespace Modules\Recruitment\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RecJobTicketSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::connection('mysql_rec')->table('rec_job_ticket_settings')->delete();

        \DB::connection('mysql_rec')->table('rec_job_ticket_settings')->insert(array(
            0 => array(
                'id' => 1,
                'setting' => 'minNoticePeriodDays',
                'value' => 7,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ),
            1 => array(
                'id' => 2,
                'setting' => 'maxNoticePeriodDays',
                'value' => 14,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ),
        ));
    }
}
