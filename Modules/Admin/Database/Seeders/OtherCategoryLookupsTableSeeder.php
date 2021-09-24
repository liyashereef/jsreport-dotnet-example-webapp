<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class OtherCategoryLookupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('other_category_lookups')->delete();
        \DB::table('other_category_lookups')->insert(array(
            0 => array(
                'id' => 1,
                'category_name' => 'Vendors',
                'shortname' => 'VNDS',
                'created_at' => '2019-07-09 18:30:00',
                'updated_at' => '2019-07-09 18:30:00',
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'category_name' => 'Government',
                'shortname' => 'GOVT',
                'created_at' => '2019-07-09 18:30:00',
                'updated_at' => '2019-07-09 18:30:00',
                'deleted_at' => null,
            )
            
       
        ));
    }
}
