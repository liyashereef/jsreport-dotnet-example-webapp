<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class MobileAppSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call("OthersTableSeeder");
        \DB::table('mobile_app_settings')->delete();

        \DB::table('mobile_app_settings')->insert(array(
            0 => array(
                'id' => 1,
                'time_interval' => 2,
                'speed_limit' => 10,
                'trip_show_speed' => 10,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ),
        ));
    }
}
