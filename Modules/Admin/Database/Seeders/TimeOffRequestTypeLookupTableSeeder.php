<?php


namespace Modules\Admin\Database\Seeders;
use Illuminate\Database\Seeder;

class TimeOffRequestTypeLookupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        \DB::table('time_off_request_type_lookups')->delete();
        
        \DB::table('time_off_request_type_lookups')->insert(array (
            0 => 
            array (
                'id' => 1,
                'request_type' => 'Leave of Absence',
                'color' => '#ACD6BD',
                'is_deletable' => 0,
                'is_editable' => 0,
                'created_at' => '2018-11-27 06:05:06',
                'updated_at' => '2018-11-27 06:05:06',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'request_type' => 'Vacation Request',
                'color' => '#ACCFD6',
                'is_deletable' => 0,
                'is_editable' => 0,
                'created_at' => '2018-11-27 06:05:06',
                'updated_at' => '2018-11-27 06:05:06',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'request_type' => 'Personal Emergency Request',
                'color' => '#D6D68F',
                'is_deletable' => 0,
                'is_editable' => 0,
                'created_at' => '2018-11-27 06:05:06',
                'updated_at' => '2018-11-27 06:05:06',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'request_type' => 'Sick Leave',
                'color' => '#E4B3D6',
                'is_deletable' => 0,
                'is_editable' => 0,
                'created_at' => '2018-11-27 06:05:06',
                'updated_at' => '2018-11-27 06:05:06',
                'deleted_at' => NULL,
            ),
        ));
        
    }
}
