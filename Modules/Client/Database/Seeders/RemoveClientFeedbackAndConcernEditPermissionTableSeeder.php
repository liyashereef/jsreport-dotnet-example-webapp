<?php

namespace Modules\Client\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Services\HelperService;
use App\Services\SeederService;

class RemoveClientFeedbackAndConcernEditPermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $deletePermissionArr = [
            'edit_client_feedback',
            'edit_client_concern',
        ];

        SeederService::deletePermission($deletePermissionArr);
    }
}
