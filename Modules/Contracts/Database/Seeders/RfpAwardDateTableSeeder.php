<?php

namespace Modules\Contracts\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RfpAwardDateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('rfp_award_dates')->truncate();
        \DB::table('rfp_award_dates')->insert([
            0=>[
                "award_dates"=>1,
                'created_at' => '2019-10-15 06:04:47',
                'updated_at' => '2019-10-15 06:04:47',
                'deleted_at' => NULL,
            ]
        ]);
        // $this->call("OthersTableSeeder");
    }
}
