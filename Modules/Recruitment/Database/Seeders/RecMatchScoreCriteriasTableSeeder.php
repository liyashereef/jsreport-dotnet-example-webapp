<?php


namespace Modules\Recruitment\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RecMatchScoreCriteriasTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::connection('mysql_rec')->table('rec_match_score_criterias')->delete();
        
        \DB::connection('mysql_rec')->table('rec_match_score_criterias')->insert(array (
            0 =>
            array (
                'id' => 1,
                'criteria_id' => 1,
                'weight' => '10',
                'created_at' => '2021-03-20 20:58:07',
                'updated_at' => '2021-03-20 20:58:07',
                'deleted_at' => null,
            ),
            1 =>
            array (
                'id' => 2,
                'criteria_id' => 2,
                'weight' => '10',
                'created_at' => '2021-03-20 20:58:07',
                'updated_at' => '2021-03-20 20:58:07',
                'deleted_at' => null,
            ),
            2 =>
            array (
                'id' => 3,
                'criteria_id' => 3,
                'weight' => '10',
                'created_at' => '2021-03-20 20:58:07',
                'updated_at' => '2021-03-20 20:58:07',
                'deleted_at' => null,
            ),
            3 =>
            array (
                'id' => 4,
                'criteria_id' => 4,
                'weight' => '10',
                'created_at' => '2021-03-20 20:58:07',
                'updated_at' => '2021-03-20 20:58:07',
                'deleted_at' => null,
            ),
            4 =>
            array (
                'id' => 5,
                'criteria_id' => 5,
                'weight' => '10',
                'created_at' => '2021-03-20 20:58:07',
                'updated_at' => '2021-03-20 20:58:07',
                'deleted_at' => null,
            ),
            5 =>
            array (
                'id' => 6,
                'criteria_id' => 6,
                'weight' => '10',
                'created_at' => '2021-03-20 20:58:07',
                'updated_at' => '2021-03-20 20:58:07',
                'deleted_at' => null,
            ),
            6 =>
            array (
                'id' => 7,
                'criteria_id' => 7,
                'weight' => '5',
                'created_at' => '2021-03-20 20:58:07',
                'updated_at' => '2021-03-20 20:58:07',
                'deleted_at' => null,
            ),
            7 =>
            array (
                'id' => 8,
                'criteria_id' => 8,
                'weight' => '10',
                'created_at' => '2021-03-20 20:58:07',
                'updated_at' => '2021-03-20 20:58:07',
                'deleted_at' => null,
            ),
            8 =>
            array (
                'id' => 9,
                'criteria_id' => 9,
                'weight' => '10',
                'created_at' => '2021-03-20 20:58:07',
                'updated_at' => '2021-03-20 20:58:07',
                'deleted_at' => null,
            ),
            9 =>
            array (
                'id' => 10,
                'criteria_id' => 10,
                'weight' => '5',
                'created_at' => '2021-03-20 20:58:07',
                'updated_at' => '2021-03-20 20:58:07',
                'deleted_at' => null,
            ),
            10 =>
            array (
                'id' => 11,
                'criteria_id' => 11,
                'weight' => '10',
                'created_at' => '2021-03-20 20:58:07',
                'updated_at' => '2021-03-20 20:58:07',
                'deleted_at' => null,
            ),
        ));

        \DB::connection('mysql_rec')->table('rec_match_score_criteria_mappings')->delete();
        
        \DB::connection('mysql_rec')->table('rec_match_score_criteria_mappings')->insert(array (
            0 =>
            array (
                'id' => 1,
                'criteria' => 1,
                'limit' => '0',
                'score' => '1.00',
                'created_at' => '2021-03-20 20:59:36',
                'updated_at' => '2021-04-01 20:18:16',
                'deleted_at' => null,
            ),
            1 =>
            array (
                'id' => 2,
                'criteria' => 1,
                'limit' => '5',
                'score' => '2.00',
                'created_at' => '2021-03-20 20:59:36',
                'updated_at' => '2021-04-01 20:18:16',
                'deleted_at' => null,
            ),
            2 =>
            array (
                'id' => 3,
                'criteria' => 1,
                'limit' => '10',
                'score' => '3.00',
                'created_at' => '2021-03-20 20:59:36',
                'updated_at' => '2021-03-23 20:27:05',
                'deleted_at' => null,
            ),
            3 =>
            array (
                'id' => 4,
                'criteria' => 1,
                'limit' => '15',
                'score' => '4.00',
                'created_at' => '2021-03-20 20:59:36',
                'updated_at' => '2021-04-01 20:18:16',
                'deleted_at' => null,
            ),
            4 =>
            array (
                'id' => 5,
                'criteria' => 1,
                'limit' => '20',
                'score' => '5.00',
                'created_at' => '2021-03-20 20:59:36',
                'updated_at' => '2021-04-01 20:18:16',
                'deleted_at' => null,
            ),
            5 =>
            array (
                'id' => 6,
                'criteria' => 2,
                'limit' => '0',
                'score' => '5.00',
                'created_at' => '2021-03-20 21:00:51',
                'updated_at' => '2021-03-20 21:00:51',
                'deleted_at' => null,
            ),
            6 =>
            array (
                'id' => 7,
                'criteria' => 2,
                'limit' => '15',
                'score' => '4.00',
                'created_at' => '2021-03-20 21:00:51',
                'updated_at' => '2021-03-31 21:11:58',
                'deleted_at' => null,
            ),
            7 =>
            array (
                'id' => 8,
                'criteria' => 2,
                'limit' => '40',
                'score' => '3.00',
                'created_at' => '2021-03-20 21:00:51',
                'updated_at' => '2021-04-07 16:03:59',
                'deleted_at' => null,
            ),
            8 =>
            array (
                'id' => 9,
                'criteria' => 2,
                'limit' => '45',
                'score' => '2.00',
                'created_at' => '2021-03-20 21:00:51',
                'updated_at' => '2021-03-20 21:00:51',
                'deleted_at' => null,
            ),
            9 =>
            array (
                'id' => 10,
                'criteria' => 2,
                'limit' => '60',
                'score' => '1.00',
                'created_at' => '2021-03-20 21:00:51',
                'updated_at' => '2021-03-20 21:00:51',
                'deleted_at' => null,
            ),
            10 =>
            array (
                'id' => 11,
                'criteria' => 3,
                'limit' => '0',
                'score' => '0.00',
                'created_at' => '2021-03-20 21:01:13',
                'updated_at' => '2021-03-20 21:01:13',
                'deleted_at' => null,
            ),
            11 =>
            array (
                'id' => 12,
                'criteria' => 3,
                'limit' => '1',
                'score' => '5.00',
                'created_at' => '2021-03-20 21:01:13',
                'updated_at' => '2021-03-20 21:01:13',
                'deleted_at' => null,
            ),
            12 =>
            array (
                'id' => 13,
                'criteria' => 4,
                'limit' => '0',
                'score' => '0.00',
                'created_at' => '2021-03-20 21:01:45',
                'updated_at' => '2021-03-20 21:01:45',
                'deleted_at' => null,
            ),
            13 =>
            array (
                'id' => 14,
                'criteria' => 4,
                'limit' => '1',
                'score' => '5.00',
                'created_at' => '2021-03-20 21:01:45',
                'updated_at' => '2021-03-20 21:01:45',
                'deleted_at' => null,
            ),
            14 =>
            array (
                'id' => 15,
                'criteria' => 6,
                'limit' => '0',
                'score' => '1.00',
                'created_at' => '2021-03-20 21:03:23',
                'updated_at' => '2021-03-20 21:03:23',
                'deleted_at' => null,
            ),
            15 =>
            array (
                'id' => 16,
                'criteria' => 6,
                'limit' => '1',
                'score' => '2.00',
                'created_at' => '2021-03-20 21:03:23',
                'updated_at' => '2021-03-20 21:03:23',
                'deleted_at' => null,
            ),
            16 =>
            array (
                'id' => 17,
                'criteria' => 6,
                'limit' => '3',
                'score' => '3.00',
                'created_at' => '2021-03-20 21:03:23',
                'updated_at' => '2021-03-20 21:03:23',
                'deleted_at' => null,
            ),
            17 =>
            array (
                'id' => 18,
                'criteria' => 6,
                'limit' => '5',
                'score' => '4.00',
                'created_at' => '2021-03-20 21:03:23',
                'updated_at' => '2021-03-20 21:03:23',
                'deleted_at' => null,
            ),
            18 =>
            array (
                'id' => 19,
                'criteria' => 6,
                'limit' => '10',
                'score' => '5.00',
                'created_at' => '2021-03-20 21:03:23',
                'updated_at' => '2021-03-20 21:03:23',
                'deleted_at' => null,
            ),
            19 =>
            array (
                'id' => 20,
                'criteria' => 11,
                'limit' => '0',
                'score' => '1.00',
                'created_at' => '2021-03-20 21:06:08',
                'updated_at' => '2021-03-20 21:06:08',
                'deleted_at' => null,
            ),
            20 =>
            array (
                'id' => 21,
                'criteria' => 11,
                'limit' => '21',
                'score' => '2.00',
                'created_at' => '2021-03-20 21:06:08',
                'updated_at' => '2021-03-20 21:06:08',
                'deleted_at' => null,
            ),
            21 =>
            array (
                'id' => 22,
                'criteria' => 11,
                'limit' => '29',
                'score' => '3.00',
                'created_at' => '2021-03-20 21:06:08',
                'updated_at' => '2021-03-20 21:06:08',
                'deleted_at' => null,
            ),
            22 =>
            array (
                'id' => 23,
                'criteria' => 11,
                'limit' => '39',
                'score' => '4.00',
                'created_at' => '2021-03-20 21:06:08',
                'updated_at' => '2021-03-20 21:06:08',
                'deleted_at' => null,
            ),
            23 =>
            array (
                'id' => 24,
                'criteria' => 11,
                'limit' => '50',
                'score' => '5.00',
                'created_at' => '2021-03-20 21:06:08',
                'updated_at' => '2021-03-20 21:06:08',
                'deleted_at' => null,
            ),
            24 =>
            array (
                'id' => 25,
                'criteria' => 5,
                'limit' => '0',
                'score' => '5.00',
                'created_at' => '2021-03-21 11:03:16',
                'updated_at' => '2021-03-21 11:03:16',
                'deleted_at' => null,
            ),
            25 =>
            array (
                'id' => 26,
                'criteria' => 5,
                'limit' => '10',
                'score' => '4.00',
                'created_at' => '2021-03-21 11:03:16',
                'updated_at' => '2021-03-21 11:03:16',
                'deleted_at' => null,
            ),
            26 =>
            array (
                'id' => 27,
                'criteria' => 5,
                'limit' => '20',
                'score' => '3.00',
                'created_at' => '2021-03-21 11:03:16',
                'updated_at' => '2021-03-21 11:03:16',
                'deleted_at' => null,
            ),
            27 =>
            array (
                'id' => 28,
                'criteria' => 5,
                'limit' => '30',
                'score' => '2.00',
                'created_at' => '2021-03-21 11:03:16',
                'updated_at' => '2021-03-21 11:03:16',
                'deleted_at' => null,
            ),
            28 =>
            array (
                'id' => 29,
                'criteria' => 5,
                'limit' => '40',
                'score' => '1.00',
                'created_at' => '2021-03-21 11:03:16',
                'updated_at' => '2021-03-21 11:03:16',
                'deleted_at' => null,
            ),
            29 =>
            array (
                'id' => 30,
                'criteria' => 8,
                'limit' => 'ISTJ',
                'score' => '5.00',
                'created_at' => '2021-03-21 11:19:50',
                'updated_at' => '2021-03-21 11:19:50',
                'deleted_at' => null,
            ),
            30 =>
            array (
                'id' => 31,
                'criteria' => 8,
                'limit' => 'ISFJ',
                'score' => '5.00',
                'created_at' => '2021-03-21 11:19:50',
                'updated_at' => '2021-03-21 11:19:50',
                'deleted_at' => null,
            ),
            31 =>
            array (
                'id' => 32,
                'criteria' => 8,
                'limit' => 'ESTJ',
                'score' => '5.00',
                'created_at' => '2021-03-21 11:19:50',
                'updated_at' => '2021-03-21 11:19:50',
                'deleted_at' => null,
            ),
            32 =>
            array (
                'id' => 33,
                'criteria' => 8,
                'limit' => 'ESFJ',
                'score' => '4.00',
                'created_at' => '2021-03-21 11:19:50',
                'updated_at' => '2021-03-21 11:19:50',
                'deleted_at' => null,
            ),
            33 =>
            array (
                'id' => 34,
                'criteria' => 8,
                'limit' => 'INTP',
                'score' => '4.00',
                'created_at' => '2021-03-21 11:19:50',
                'updated_at' => '2021-03-21 11:19:50',
                'deleted_at' => null,
            ),
            34 =>
            array (
                'id' => 35,
                'criteria' => 8,
                'limit' => 'ENTJ',
                'score' => '4.00',
                'created_at' => '2021-03-21 11:19:50',
                'updated_at' => '2021-03-21 11:19:50',
                'deleted_at' => null,
            ),
            35 =>
            array (
                'id' => 36,
                'criteria' => 8,
                'limit' => 'INTJ',
                'score' => '3.00',
                'created_at' => '2021-03-21 11:19:50',
                'updated_at' => '2021-03-21 11:19:50',
                'deleted_at' => null,
            ),
            36 =>
            array (
                'id' => 37,
                'criteria' => 8,
                'limit' => 'INFJ',
                'score' => '3.00',
                'created_at' => '2021-03-21 11:19:50',
                'updated_at' => '2021-03-21 11:19:50',
                'deleted_at' => null,
            ),
            37 =>
            array (
                'id' => 38,
                'criteria' => 8,
                'limit' => 'INFP',
                'score' => '3.00',
                'created_at' => '2021-03-21 11:19:50',
                'updated_at' => '2021-03-21 11:19:50',
                'deleted_at' => null,
            ),
            38 =>
            array (
                'id' => 39,
                'criteria' => 8,
                'limit' => 'ENFJ',
                'score' => '3.00',
                'created_at' => '2021-03-21 11:19:50',
                'updated_at' => '2021-03-21 11:19:50',
                'deleted_at' => null,
            ),
            39 =>
            array (
                'id' => 40,
                'criteria' => 8,
                'limit' => 'ENFP',
                'score' => '3.00',
                'created_at' => '2021-03-21 11:19:50',
                'updated_at' => '2021-03-21 11:19:50',
                'deleted_at' => null,
            ),
            40 =>
            array (
                'id' => 41,
                'criteria' => 8,
                'limit' => 'ENTP',
                'score' => '2.00',
                'created_at' => '2021-03-21 11:19:50',
                'updated_at' => '2021-03-21 11:19:50',
                'deleted_at' => null,
            ),
            41 =>
            array (
                'id' => 42,
                'criteria' => 8,
                'limit' => 'ISTP',
                'score' => '2.00',
                'created_at' => '2021-03-21 11:19:50',
                'updated_at' => '2021-03-21 11:19:50',
                'deleted_at' => null,
            ),
            42 =>
            array (
                'id' => 43,
                'criteria' => 8,
                'limit' => 'ESFP',
                'score' => '2.00',
                'created_at' => '2021-03-21 11:19:50',
                'updated_at' => '2021-03-21 11:19:50',
                'deleted_at' => null,
            ),
            43 =>
            array (
                'id' => 44,
                'criteria' => 8,
                'limit' => 'ISFP',
                'score' => '1.00',
                'created_at' => '2021-03-21 11:19:50',
                'updated_at' => '2021-03-21 11:19:50',
                'deleted_at' => null,
            ),
            44 =>
            array (
                'id' => 45,
                'criteria' => 8,
                'limit' => 'ESTP',
                'score' => '1.00',
                'created_at' => '2021-03-21 11:19:50',
                'updated_at' => '2021-03-21 11:19:50',
                'deleted_at' => null,
            ),
            45 =>
            array (
                'id' => 53,
                'criteria' => 9,
                'limit' => '1',
                'score' => '1.25',
                'created_at' => '2021-04-01 14:55:58',
                'updated_at' => '2021-04-01 14:55:58',
                'deleted_at' => null,
            ),
            46 =>
            array (
                'id' => 54,
                'criteria' => 9,
                'limit' => '2',
                'score' => '2.50',
                'created_at' => '2021-04-01 14:55:58',
                'updated_at' => '2021-04-01 14:55:58',
                'deleted_at' => null,
            ),
            47 =>
            array (
                'id' => 55,
                'criteria' => 9,
                'limit' => '3',
                'score' => '3.75',
                'created_at' => '2021-04-01 14:55:58',
                'updated_at' => '2021-04-01 14:55:58',
                'deleted_at' => null,
            ),
            48 =>
            array (
                'id' => 56,
                'criteria' => 9,
                'limit' => '4',
                'score' => '5.00',
                'created_at' => '2021-04-01 14:55:58',
                'updated_at' => '2021-04-01 14:55:58',
                'deleted_at' => null,
            ),
            49 =>
            array (
                'id' => 57,
                'criteria' => 10,
                'limit' => '1',
                'score' => '1.00',
                'created_at' => '2021-04-01 14:55:58',
                'updated_at' => '2021-04-09 15:31:12',
                'deleted_at' => null,
            ),
            50 =>
            array (
                'id' => 58,
                'criteria' => 10,
                'limit' => '2',
                'score' => '3.00',
                'created_at' => '2021-04-01 14:55:58',
                'updated_at' => '2021-04-09 15:31:12',
                'deleted_at' => null,
            ),
            51 =>
            array (
                'id' => 59,
                'criteria' => 10,
                'limit' => '3',
                'score' => '5.00',
                'created_at' => '2021-04-01 14:55:58',
                'updated_at' => '2021-04-01 14:55:58',
                'deleted_at' => null,
             ),
        ));
    }
}
