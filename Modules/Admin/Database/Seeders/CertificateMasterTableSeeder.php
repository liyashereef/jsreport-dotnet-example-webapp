<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class CertificateMasterTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('certificate_masters')->delete();

        \DB::table('certificate_masters')->insert(array(
            0 => array(
                'id' => 1,
                'certificate_name' => 'Security Guard Licence',
                'created_at' => date('y_m-d H:i:s'),
                'updated_at' => date('y_m-d H:i:s'),
                'deleted_at' => null,
                'is_deletable' => 0,
            ),
            1 => array(
                'id' => 2,
                'certificate_name' => 'First Aid Certificate',
                'created_at' => date('y_m-d H:i:s'),
                'updated_at' => date('y_m-d H:i:s'),
                'deleted_at' => null,
                'is_deletable' => 0,
            ),
            2 => array(
                'id' => 3,
                'certificate_name' => 'CPR Certificate',
                'created_at' => date('y_m-d H:i:s'),
                'updated_at' => date('y_m-d H:i:s'),
                'deleted_at' => null,
                'is_deletable' => 0,
            ),
        ));

    }
}
