<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Models\DocumentExpiryColorSettings;

class AddDocumentExpryColorSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DocumentExpiryColorSettings::updateOrCreate([
            "id" => 1
        ], [
            "grace_period_in_days" => 100,
            "grace_period_color_code" => "#f0d528",
            "grace_period_font_color_code" => "#ffffff",
            "alert_period_in_days" => 10,
            "alert_period_color_code" => "#ff0000",
            "alert_period_font_color_code" => "#ffffff",
            "schedule_grace_period_days" => 10,
            "schedule_grace_period_color_code" => "#ff0000",
            "schedule_grace_period_font_color_code" => "#ffffff",
            "schedule_alert_period_days" => 11,
            "schedule_alert_color_code" => "#ff0000",
            "schedule_alert_period_font_color_code" => "#ffffff"
        ]);
    }
}
