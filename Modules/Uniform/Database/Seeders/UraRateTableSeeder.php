<?php

namespace Modules\Uniform\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UraRateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('ura_rates')->delete();

        DB::table('ura_rates')->insert([
            'id' => 1,
            'amount' => 1.00,
            'created_by' => 1,
            'created_at' => Carbon::now(),
        ]);
    }
}
