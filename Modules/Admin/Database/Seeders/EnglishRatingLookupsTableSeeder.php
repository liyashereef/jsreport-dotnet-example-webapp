<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class EnglishRatingLookupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call("OthersTableSeeder");
        \DB::table('english_rating_lookups')->delete();
        \DB::table('english_rating_lookups')->insert([
            0 => [
                'id' => 1,
                'english_ratings' => 'Candidate Demonstrates Poor English Language Skills',
                'order_sequence' => 1,
                'score' => 1,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
                'deleted_at' => null,
            ],
            1 => [
                'id' => 2,
                'english_ratings' => 'Candidate Demonstrates Adequate English Language Skills',
                'order_sequence' => 2,
                'score' => 2,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
                'deleted_at' => null,
            ],
            2 => [
                'id' => 3,
                'english_ratings' => 'Candidate Demonstrates Average English Language Skills',
                'order_sequence' => 3,
                'score' => 3,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
                'deleted_at' => null,
            ],
            3 => [
                'id' => 4,
                'english_ratings' => 'Candidate Demonstrates English Language Fluency',
                'order_sequence' => 4,
                'score' => 4,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
                'deleted_at' => null,
            ]

        ]);
    }
}
