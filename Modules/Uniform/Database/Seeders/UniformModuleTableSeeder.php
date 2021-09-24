<?php

namespace Modules\Uniform\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UniformModuleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('modules')->insert([
            [
                'name' => 'Uniform',
                'parent_module_id' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]
        );
    }
}
