<?php

namespace Modules\Supervisorpanel\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class SupervisorpanelDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call([
            TemplateQuestionsCategoriesTableSeeder::class,
            AnswerTypeLookupTableSeeder::class,
            AdditionalAnswerTypeLookupTableSeeder::class,
            TemplateSettingsTableSeeder::class,
            TemplateSettingRulesTableSeeder::class,
            IncidentStatusListTableSeeder::class,
        ]);
    }
}
