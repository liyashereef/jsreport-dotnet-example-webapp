<?php

namespace Modules\Recruitment\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RecCompetencyMatrixRatingLookupTableSeederTableSeeder extends Seeder
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
        \DB::connection('mysql_rec')->table('rec_competency_matrix_rating_lookups')->delete();
        \DB::connection('mysql_rec')->table('rec_competency_matrix_rating_lookups')->insert([
            0 => [
                'id' => 1,
                'rating' => 'Mastery of Skill Acquired',
                'order_sequence' => 1,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            1 => [
                'id' => 2,
                'rating' => 'Significant Experience Acquired',
                'order_sequence' => 2,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            2 => [
                'id' => 3,
                'rating' => 'Adequate Experience Acquired',
                'order_sequence' => 3,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            3 => [
                'id' => 4,
                'rating' => 'Little Experience Acquired',
                'order_sequence' => 4,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            4 => [
                'id' => 5,
                'rating' => 'No Experience Acquired',
                'order_sequence' => 5,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],

        ]);
    }
}
