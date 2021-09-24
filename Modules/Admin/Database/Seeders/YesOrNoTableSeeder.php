<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class YesOrNoTableSeeder extends Seeder
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
        \DB::table('yes_or_no_lookups')->delete();
        
        \DB::table('yes_or_no_lookups')->insert(
            [
                0 => [
                    'id' => 1,
                    'value' => 'Yes',
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('y-m-d')
                ],
                1 => [
                    'id' => 2,
                    'value' => 'No',
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('y-m-d')
                ],
            ]
        );
    }
}
