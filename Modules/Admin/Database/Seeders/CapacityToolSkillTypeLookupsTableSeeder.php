<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class CapacityToolSkillTypeLookupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Model::unguard();

        // $this->call("OthersTableSeeder");
        \DB::table('capacity_tool_skill_type_lookups')->delete();

        \DB::table('capacity_tool_skill_type_lookups')->insert(array(
            0 => array(
                'id' => 1,
                'value' => 'Break Rival Strongholds',
                'created_at' => '2019-01-02 06:05:41',
                'updated_at' => '2019-01-02 06:05:41',
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'value' => 'Targeted RFPs And Non-Guarding Bids',
                'created_at' => '2019-01-02 06:05:48',
                'updated_at' => '2019-01-02 06:05:48',
                'deleted_at' => null,
            ),
            2 => array(
                'id' => 3,
                'value' => 'Use Analytics To Our Advantage',
                'created_at' => '2019-01-02 06:05:55',
                'updated_at' => '2019-01-02 06:05:55',
                'deleted_at' => null,
            ),
            3 => array(
                'id' => 4,
                'value' => 'Target Spending To Mitigate Loss Of Tax Status',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            4 => array(
                'id' => 5,
                'value' => 'Attractive Salaries/Wages/Employee Relations',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            5 => array(
                'id' => 6,
                'value' => 'Attract Veterans',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            6 => array(
                'id' => 7,
                'value' => 'Strengthen Client Relations And Divisional Relations',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            7 => array(
                'id' => 8,
                'value' => 'Flawlessly Execute To Maintain Delivery Excellence',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            8 => array(
                'id' => 9,
                'value' => 'Manage Costs And Capacity (Do More With Less)',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            9 => array(
                'id' => 10,
                'value' => 'Automate Where Possible',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            10 => array(
                'id' => 11,
                'value' => 'Reclaim Veteran Space',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            11 => array(
                'id' => 12,
                'value' => 'Diversify And Expand Brand Awareness',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            12 => array(
                'id' => 13,
                'value' => 'Administration',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
        ));
    }
}
