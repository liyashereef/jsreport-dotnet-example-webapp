<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class AuthorisedAccessDocumentManagementTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $hr_document_id     = \App\Services\HelperService::getPermissionId('hr_document_management');

        $finance_controller_document_id = \App\Services\HelperService::getPermissionId('finance_controller_document_management');
        $payroll_document_id            = \App\Services\HelperService::getPermissionId('payroll_document_management');
        $clerical_document_id           = \App\Services\HelperService::getPermissionId('clerical_document_management');
        $customer_document_id           = \App\Services\HelperService::getPermissionId('customer_document_management');
        $other_document_id              = \App\Services\HelperService::getPermissionId('other_document_management');
        $it_document_id                 = \App\Services\HelperService::getPermissionId('it_document_management');
        $quarter_master_document_id     = \App\Services\HelperService::getPermissionId('quarter_master_document_management');
        $ceo_document_id                = \App\Services\HelperService::getPermissionId('ceo_document_management');
        $cfo_document_id                = \App\Services\HelperService::getPermissionId('cfo_document_management');
        $coo_document_id                = \App\Services\HelperService::getPermissionId('coo_document_management');
        $board_of_director_document_id  = \App\Services\HelperService::getPermissionId('board_of_director_document_management');
        $finance_director_document_id   = \App\Services\HelperService::getPermissionId('finance_director_document_management');
        $it_director_document_id        = \App\Services\HelperService::getPermissionId('it_director_document_management');
        \DB::table('authorised_access_document_managements')->truncate();
        \DB::table('authorised_access_document_managements')->insert(array 
        (
            0 => 
            array (
                'id' => 1,
                'name' => 'HR Documents',
                'permission_id' =>$hr_document_id,
                'created_at' => '2019-10-15 06:04:47',
                'updated_at' => '2019-10-15 06:04:47',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Finance Documents',
                'permission_id' =>$finance_controller_document_id ,
                'created_at' => '2019-10-15 06:04:47',
                'updated_at' => '2019-10-15 06:04:47',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Payroll Documents',
                'permission_id' =>$payroll_document_id,
                'created_at' => '2019-10-15 06:04:47',
                'updated_at' => '2019-10-15 06:04:47',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Clerical Documents',
                'permission_id' =>$clerical_document_id,
                'created_at' => '2019-10-15 06:04:47',
                'updated_at' => '2019-10-15 06:04:47',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Customer Documents',
                'permission_id' =>$customer_document_id,
                'created_at' => '2019-10-15 06:04:47',
                'updated_at' => '2019-10-15 06:04:47',
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Other Documents',
                'permission_id' => $other_document_id ,
                'created_at' => '2019-10-15 06:04:47',
                'updated_at' => '2019-10-15 06:04:47',
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'IT Documents',
                'permission_id' => $it_document_id,
                'created_at' => '2019-10-15 06:04:47',
                'updated_at' => '2019-10-15 06:04:47',
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Quarter Master Documents',
                'permission_id' =>$quarter_master_document_id ,
                'created_at' => '2019-10-15 06:04:47',
                'updated_at' => '2019-10-15 06:04:47',
                'deleted_at' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'CEO Documents',
                'permission_id' =>$ceo_document_id ,
                'created_at' => '2019-10-15 06:04:47',
                'updated_at' => '2019-10-15 06:04:47',
                'deleted_at' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'CFO Documents',
                'permission_id' =>$cfo_document_id,
                'created_at' => '2019-10-15 06:04:47',
                'updated_at' => '2019-10-15 06:04:47',
                'deleted_at' => NULL,
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'COO Documents',
                'permission_id' =>$coo_document_id,
                'created_at' => '2019-10-15 06:04:47',
                'updated_at' => '2019-10-15 06:04:47',
                'deleted_at' => NULL,
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'Board of Directors Documents',
                'permission_id' =>$board_of_director_document_id,
                'created_at' => '2019-10-15 06:04:47',
                'updated_at' => '2019-10-15 06:04:47',
                'deleted_at' => NULL,
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'Finance Director Documents',
                'permission_id' =>$finance_director_document_id ,
                'created_at' => '2019-10-15 06:04:47',
                'updated_at' => '2019-10-15 06:04:47',
                'deleted_at' => NULL,
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'IT Director Documents',
                'permission_id' =>$it_director_document_id ,
                'created_at' => '2019-10-15 06:04:47',
                'updated_at' => '2019-10-15 06:04:47',
                'deleted_at' => NULL,
            ),

        ));       
    }
}
