<?php

namespace Modules\Documents\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DocumentCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('document_categories')->delete();

        \DB::table('document_categories')->insert(array(
            0 => array(
                'id' => 1,
                'document_type_id' =>1,
                'document_category' =>'Security Clearance',
                'is_editable' => 0,
                'created_by' => null,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
      
            ),

            1 => array(
                'id' => 2,
                'document_type_id' =>1,
                'document_category' =>'Certificates',
                'is_editable' => 0,
                'created_by' => null,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
      
            ),
            2 => array(
                'id' => 3,
                'document_type_id' =>1,
                'document_category' =>'Others',
                'is_editable' => 0,
                'created_by' => null,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
      
            ),
            3 => array(
                'id' => 4,
                'document_type_id' =>2,
                'document_category' =>'Category 1',
                'is_editable' => 0,
                'created_by' => null,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
      
            ),
            4 => array(
                'id' => 5,
                'document_type_id' =>2,
                'document_category' =>'Category 2',
                'is_editable' => 0,
                'created_by' => null,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
      
            ),
            5 => array(
                'id' => 6,
                'document_type_id' =>2,
                'document_category' =>'Others',
                'is_editable' => 0,
                'created_by' => null,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
      
            ),
        ));
        // $this->call("OthersTableSeeder");
    }
}
