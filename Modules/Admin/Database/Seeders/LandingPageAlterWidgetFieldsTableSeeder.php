<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class LandingPageAlterWidgetFieldsTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        \DB::table('landing_page_widget_fields')->where('field_display_name', 'Date')->where('field_system_name', 'updated_at_date')->update([
            'field_display_name' => 'Updated Time',
            'field_system_name' => 'updated_time'
        ]);
    }

}
