<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class QrPatrolSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('qr_patrol_settings')->delete();

        \DB::table('qr_patrol_settings')->insert(
            [
                0 => [
                    'id' => 1,
                    'days_prior' => 7,
                    'critical_level_percentage' => 20,
                    'acceptable_level_percentage' => 70,
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('y-m-d'),
                ],
            ]
        );
    }
}
