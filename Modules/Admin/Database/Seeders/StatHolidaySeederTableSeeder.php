<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class StatHolidaySeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('stat_holidays')->truncate();

        \DB::table('stat_holidays')->insert(array(
            0 => array(
                'id' => 1,
                'holiday' => 'New Years Day	',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'holiday' => "Boxing's Day",
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            2 => array(
                'id' => 3,
                'holiday' => 'Christmas Day	',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            3 => array(
                'id' => 4,
                'holiday' => 'Thanks giving Day	',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            4 => array(
                'id' => 5,
                'holiday' => 'Labour Day	',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),

        ));
    }
}
