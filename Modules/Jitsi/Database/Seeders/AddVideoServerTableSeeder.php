<?php

namespace Modules\Jitsi\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use DB;
use Modules\Jitsi\Models\ConferenceRecordingServer;

class AddVideoServerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Model::unguard();
        DB::delete('delete from conference_recording_servers');
        //DB::insert('insert into conference_recording_servers (instanceid, ip) values (?, ?)', );
        ConferenceRecordingServer::insert([
            ["id" => 1, "instanceid" => "i-00ab2cebecaf4f171", "ip" => '18.217.118.92', "permanentonserver" => 1, "created_at" => now()],
            ["id" => 2, "instanceid" => "i-079e7ed9a1f5c25dc", "ip" => "3.137.116.33", "permanentonserver" => 0, "created_at" => now()],
            ["id" => 3, "instanceid" => "i-090450bc6c8694c31", "ip" => "3.136.38.210", "permanentonserver" => 0, "created_at" => now()],
            ["id" => 4, "instanceid" => "i-0d95a7403e129d699", "ip" => "3.18.96.178", "permanentonserver" => 1, "created_at" => now()]
        ]);
        // $this->call("OthersTableSeeder");
    }
}
