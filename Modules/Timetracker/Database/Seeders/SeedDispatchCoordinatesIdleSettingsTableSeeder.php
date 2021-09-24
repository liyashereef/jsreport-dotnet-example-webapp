<?php

namespace Modules\Timetracker\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class SeedDispatchCoordinatesIdleSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();


        \DB::table('dispatch_coordinates_idle_settings')->delete();

        \DB::table('dispatch_coordinates_idle_settings')->insert([
            [
                'id' => 1,
                'idle_time' => 5,
                'user_id' => 1,
                'created_at' => \Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => \Carbon::now()->format('Y-m-d H:i:s')
            ]
        ]);
    }
}
