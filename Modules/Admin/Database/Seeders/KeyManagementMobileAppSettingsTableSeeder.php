<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class KeyManagementMobileAppSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

           // $this->call("OthersTableSeeder");
           \DB::table('key_management_mobile_app_settings')->delete();

           \DB::table('key_management_mobile_app_settings')->insert(array(
               0 => array(
                   'id' => 1,
                   'keymanagement_module_image_limit' => 5,
                   'created_at' => date('Y-m-d H:i:s'),
                   'updated_at' => date('Y-m-d H:i:s'),
                   'deleted_at' => null,
               ),
           ));
    }
}
