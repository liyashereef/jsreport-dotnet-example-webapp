<?php

namespace Modules\Timetracker\Database\Seeders;

use Illuminate\Database\Seeder;

class SeedDispatchRequestStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('dispatch_request_statuses')->delete();

        \DB::table('dispatch_request_statuses')->insert([
            [
                'id' => 1,
                'name' => 'Open',
                'created_at' => \Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => \Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'id' => 2,
                'name' => 'In Progress',
                'created_at' => \Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => \Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'id' => 3,
                'name' => 'Arrived & Started Investigation',
                'created_at' => \Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => \Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'id' => 4,
                'name' => 'Closed',
                'created_at' => \Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => \Carbon::now()->format('Y-m-d H:i:s')
            ],
        ]);

    }
}
