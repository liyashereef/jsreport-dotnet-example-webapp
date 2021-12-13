<?php

namespace Modules\Chat\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Services\HelperService;

class ModulePermissionChatTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $view_chat_menu = HelperService::getPermissionId('view_chat_menu');
        $view_chat_history = HelperService::getPermissionId('view_chat_history');
        $view_chat_in_api = HelperService::getPermissionId('view_chat_in_api');
        $view_all_customer_chatlist = HelperService::getPermissionId('view_all_customer_chatlist');
        $view_allocated_customer_chatlist = HelperService::getPermissionId('view_allocated_customer_chatlist');
        $module_id = HelperService::getModuleId('Chat');
         \DB::table('module_permissions')->where('module_id', $module_id)
        ->whereIn('permission_id', [
            $view_chat_menu,
            $view_chat_history,
            $view_chat_in_api,
            $view_all_customer_chatlist,
            $view_allocated_customer_chatlist
        ])->delete();
        \DB::table('module_permissions')->insert(array(
            0 => array(
                'module_id' => $module_id,
                'permission_description' => 'View Chat',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $view_chat_menu,
                'sequence_number' => 1,
            ),
            1 => array(
                'module_id' => $module_id,
                'permission_description' => 'View Chat History',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $view_chat_history,
                'sequence_number' => 2,
            ),
            2 => array(
                'module_id' => $module_id,
                'permission_description' => 'View Chat in API',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $view_chat_in_api,
                'sequence_number' => 3,
            ),
            3 => array(
                'module_id' => $module_id,
                'permission_description' => 'View All Customers in Chat list',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $view_all_customer_chatlist,
                'sequence_number' => 4,
            ),
            4 => array(
                'module_id' => $module_id,
                'permission_description' => 'View Allocated Customers in Chat list',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $view_allocated_customer_chatlist,
                'sequence_number' => 5,
            ),
        ));
    }
}
