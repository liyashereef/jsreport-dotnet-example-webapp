<?php

namespace Modules\Reports\Database\Seeders;

use App\Services\HelperService;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class DeleteDocumentExpiryPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $documentExpirysOldPermissionId = HelperService::getPermissionId('view_certificate_expiry_report');

        if ($documentExpirysOldPermissionId) {
            $module_id = \App\Services\HelperService::getModuleId('Reports');

            $perm = Permission::findByName("view_certificate_expiry_report");
            if ($perm) {
                $perm->delete();
            }

            \DB::table('module_permissions')
                ->where('module_id', $module_id)
                ->where('permission_id', $documentExpirysOldPermissionId)
                ->delete();

            \DB::table('role_has_permissions')
                ->where('permission_id', $documentExpirysOldPermissionId)
                ->delete();
        }
    }
}
