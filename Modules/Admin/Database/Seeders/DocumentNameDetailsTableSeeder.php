<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DocumentNameDetailsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        \DB::table('document_name_details')->delete();

        \DB::table('document_name_details')->insert(array(
            0 => array(
                'id' => 1,
                'name' => 'CPR Certificate',
                'document_type_id' => '1',
                'document_category_id' => '2',
                'updated_at' => '2019-03-12 04:02:47',
                'deleted_at' => null,
               
            ),
            1 => array(
                'id' => 2,
                'name' => 'First Aid Certificate',
                'document_type_id' => '1',
                'document_category_id' => '2',
                'updated_at' => '2019-03-12 04:02:47',
                'deleted_at' => null,
               
            ),
            2 => array(
                'id' => 3,
                'name' => 'Security Guard Licence',
                'document_type_id' => '1',
                'document_category_id' => '2',
                'updated_at' => '2019-03-12 04:02:47',
                'deleted_at' => null,
               
            ),
            3 => array(
                'id' => 4,
                'name' => 'Enhanced Reliability',
                'document_type_id' => '1',
                'document_category_id' => '1',
                'updated_at' => '2019-03-12 04:02:47',
                'deleted_at' => null,
               
            ),
            4 => array(
                'id' => 5,
                'name' => 'No Clearance',
                'document_type_id' => '1',
                'document_category_id' => '1',
                'updated_at' => '2019-03-12 04:02:47',
                'deleted_at' => null,
               
            ),
        ));
    }
}
