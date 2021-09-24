<?php

namespace Modules\Client\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Services\HelperService;

class ModulePermissionClientReviewTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $review_client_feedback = HelperService::getPermissionId('review_client_feedback');
        $review_client_concern = HelperService::getPermissionId('review_client_concern');
        $module_id = HelperService::getModuleId('Client');

        \DB::table('module_permissions')->insert(array(
            0 => array(
                'module_id' => $module_id,
                'permission_description' => 'Review Client Feedback',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $review_client_feedback,
                'sequence_number' => 251,
            ),
            1 => array(
                'module_id' => $module_id,
                'permission_description' => 'Review Client Concern',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $review_client_concern,
                'sequence_number' => 252,
            ),
        ));
    }
}
