<?php

namespace Modules\Recruitment\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RecCompetencyMatrixCategoryLookupTableSeederTableSeeder extends Seeder
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
        \DB::connection('mysql_rec')->table('rec_competency_matrix_category_lookups')->delete();
        \DB::connection('mysql_rec')->table('rec_competency_matrix_category_lookups')->insert([
            0 => [
                'id' => 1,
                'category_name' => 'Personal Skills',
                'short_name' => 'Personal Skills',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            1 => [
                'id' => 2,
                'category_name' => 'Leadership Skills',
                'short_name' => 'Leadership Skills',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
        ]);
    }
}
