<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class RemoveLandingPageTabConfigurationsSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('landing_page_widget_fields')->delete();
        \DB::table('landing_page_tab_details')->delete();
        \DB::table('landing_page_tabs')->delete();
    }

}
