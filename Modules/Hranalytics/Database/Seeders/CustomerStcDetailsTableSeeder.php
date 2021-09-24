<?php

namespace Modules\Hranalytics\Database\Seeders;

use Illuminate\Database\Seeder;

class CustomerStcDetailsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('customer_stc_details')->delete();

        \DB::table('customer_stc_details')->insert(array(
            0 => array(
                'id' => 1,
                'customer_id' => 1,
                'job_description' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to making it look like readable English. ',
                'nmso_account' => 'yes',
                'security_clearance_lookup_id' => '4',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'customer_id' => 2,
                'job_description' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to making it look like readable English. ',
                'nmso_account' => 'yes',
                'security_clearance_lookup_id' => '3',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
                'deleted_at' => null,
            ),

        ));

    }
}
