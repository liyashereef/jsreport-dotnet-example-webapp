<?php

namespace Modules\Documents\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DocumentTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('document_types')->delete();

        \DB::table('document_types')->insert(array(
            0 => array(
                'id' => 1,
                'document_type' =>'Employee',
                'is_editable' => 0,
                'created_by' => null,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
      
            ),
            1 => array(
                'id' => 2,
                'document_type' =>'Client',
                'is_editable' => 0,
                'created_by' => null,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
        ),
        2 => array(
            'id' => 3,
            'document_type' =>'Other',
            'is_editable' => 0,
            'created_by' => null,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
    )
       
            ));
        // $this->call("OthersTableSeeder");
    }
}
