<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class OfficeAddressesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('office_addresses')->delete();
        
        \DB::table('office_addresses')->insert(array (
            0 => 
            array (
                'id' => 1,
                'addresstitle' => 'Oakville HQ - 2947 Portland Drive, Oakville ON L6H5S4',
                'address' => 'Oakville HQ - 2947 Portland Drive, Oakville ON L6H5S4',
                'status' => 1,
                'createdby' => 1,
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'addresstitle' => 'Barrie Office - 5 Bell Farm Road, Unit 5, Barrie ON MSL39D',
                'address' => 'Barrie Office - 5 Bell Farm Road, Unit 5, Barrie ON MSL39D',
                'status' => 1,
                'createdby' => 1,
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'addresstitle' => 'London Office - 1112 Dearness Street, Unit 12, London ON  M3K3K3',
                'address' => 'London Office - 1112 Dearness Street, Unit 12, London ON  M3K3K3',
                'status' => 1,
                'createdby' => 1,
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
                'deleted_at' => NULL,
            )
        ));
    }
}
