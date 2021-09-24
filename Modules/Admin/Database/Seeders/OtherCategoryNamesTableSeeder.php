<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class OtherCategoryNamesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('other_category_names')->delete();
        \DB::table('other_category_names')->insert(array(
            0 => array(
                'id' => 1,
                'name' => 'Best Buy',
                'document_type_id' => 3,
                'other_category_lookup_id' => 1,
                'created_at' => '2019-07-10 14:30:00',
                'updated_at' => '2019-07-10 14:30:00',
                'deleted_at' => null,
            ),

            1 => array(
                'id' => 2,
                'name' => 'IMF',
                'document_type_id' => 3,
                'other_category_lookup_id' => 2,
                'created_at' => '2019-07-10 14:30:00',
                'updated_at' => '2019-07-10 14:30:00',
                'deleted_at' => null,
            ),
        ));
        // $this->call("OthersTableSeeder");
    }
}
