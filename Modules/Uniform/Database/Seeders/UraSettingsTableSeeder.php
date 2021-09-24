<?php

namespace Modules\Uniform\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UraSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('ura_settings')->delete();

        DB::table('ura_settings')->insert([
            'key' => 'uniform-purchase-threshold',
            'value' => 100,
            'created_at' => Carbon::now(),
        ]);
    }
}
