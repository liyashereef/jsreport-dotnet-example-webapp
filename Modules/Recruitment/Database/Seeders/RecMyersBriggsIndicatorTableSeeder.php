<?php

namespace Modules\Recruitment\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RecMyersBriggsIndicatorTableSeeder extends Seeder
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
        \DB::connection('mysql_rec')->table('rec_myers_briggs_indicators')->delete();
        \DB::connection('mysql_rec')->table('rec_myers_briggs_indicators')->insert([
            0 => [
                'id' => 1,
                'value' => 'Extraversion',
                'initial' => 'E',
                'column' => 1,
                'option' => 'a',
            ],
            1 => [
                'id' => 2,
                'value' => 'Introversion',
                'initial' => 'I',
                'column' => 1,
                'option' => 'b',
            ],
            2 => [
                'id' => 3,
                'value' => 'Sensing',
                'initial' => 'S',
                'column' => 3,
                'option' => 'a',
            ],
            3 => [
                'id' => 4,
                'value' => 'Intuitive',
                'initial' => 'N',
                'column' => 3,
                'option' => 'b',
            ],
            4 => [
                'id' => 5,
                'value' => 'Thinking',
                'initial' => 'T',
                'column' => 5,
                'option' => 'a',
            ],
            5 => [
                'id' => 6,
                'value' => 'Feeling',
                'initial' => 'F',
                'column' => 5,
                'option' => 'b',
            ],
            6 => [
                'id' => 7,
                'value' => 'Judging',
                'initial' => 'J',
                'column' => 7,
                'option' => 'a',
            ],
            7 => [
                'id' => 8,
                'value' => 'Perceiving',
                'initial' => 'P',
                'column' => 7,
                'option' => 'b',
            ],


        ]);
    }
}
