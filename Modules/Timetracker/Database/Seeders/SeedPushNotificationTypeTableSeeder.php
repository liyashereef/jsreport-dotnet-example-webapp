<?php

namespace Modules\Timetracker\Database\Seeders;

use Illuminate\Database\Seeder;

class SeedPushNotificationTypeTableSeeder extends  Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('push_notification_types')->delete();

        \DB::table('push_notification_types')->insert([
            [
                'id' => 1,
                'name' => 'MST Dispatch',
                'created_at' => \Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => \Carbon::now()->format('Y-m-d H:i:s')
            ],
        ]);
    }

}