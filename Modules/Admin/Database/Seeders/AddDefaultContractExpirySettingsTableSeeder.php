<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AddDefaultContractExpirySettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('contract_expiry_settings')->truncate();
        \DB::table('contract_expiry_settings')->insert([
            0=>[
                "id"=>1,
                'alert_period_1'=>'150',
                'alert_period_2' => '100',
                'alert_period_3' => '50',
                'email_1_time' => '06:00:00',
                'email_2_time' => '08:00:00',
                'email_3_time' => '10:00:00',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

        ]);
    }
}
