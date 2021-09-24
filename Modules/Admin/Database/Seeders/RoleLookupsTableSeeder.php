<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class RoleLookupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Model::unguard();

        // $this->call("OthersTableSeeder");
        \DB::table('role_lookups')->delete();

        \DB::table('role_lookups')->insert(array(
            0 => array(
                'id' => 1,
                'value' => 'CEO',
                'created_at' => '2019-01-02 06:05:41',
                'updated_at' => '2019-01-02 06:05:41',
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'value' => 'CFO',
                'created_at' => '2019-01-02 06:05:48',
                'updated_at' => '2019-01-02 06:05:48',
                'deleted_at' => null,
            ),
            2 => array(
                'id' => 3,
                'value' => 'VP - Support Services',
                'created_at' => '2019-01-02 06:05:55',
                'updated_at' => '2019-01-02 06:05:55',
                'deleted_at' => null,
            ),
            3 => array(
                'id' => 4,
                'value' => 'Director - NMSO and GTAA',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            4 => array(
                'id' => 5,
                'value' => 'Director - Commercial',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            5 => array(
                'id' => 6,
                'value' => 'VP - Operations',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            6 => array(
                'id' => 7,
                'value' => 'Area Manager - NMSO',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            7 => array(
                'id' => 8,
                'value' => 'Associate Director - Commercial',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            8 => array(
                'id' => 9,
                'value' => 'Area Manager - London',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            9 => array(
                'id' => 10,
                'value' => 'Area Manager - Commercial - Central',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            10 => array(
                'id' => 11,
                'value' => 'Area Manager - Central',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            11 => array(
                'id' => 12,
                'value' => 'Assistant Area Manager - London',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            12 => array(
                'id' => 13,
                'value' => 'Assistant Area Manager - Central',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            13 => array(
                'id' => 14,
                'value' => 'Operations Assisstant',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            14 => array(
                'id' => 15,
                'value' => 'Operations Assistant - Barrie',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            15 => array(
                'id' => 16,
                'value' => 'Director - IT And PMO',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            16 => array(
                'id' => 17,
                'value' => 'Director - Finance And Administration',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            17 => array(
                'id' => 18,
                'value' => 'Director - Credit Management',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            18 => array(
                'id' => 19,
                'value' => 'Senior Financial Analyst',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            19 => array(
                'id' => 20,
                'value' => 'Payroll Clerk',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            20 => array(
                'id' => 21,
                'value' => 'Commercial AR Analyst',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            21 => array(
                'id' => 22,
                'value' => 'NMSO AR Analyst',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            22 => array(
                'id' => 23,
                'value' => 'Junior Financial Analyst',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            23 => array(
                'id' => 24,
                'value' => 'Intermediate Financial Analyst',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            24 => array(
                'id' => 25,
                'value' => 'Human Resources Manager',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            25 => array(
                'id' => 26,
                'value' => 'HR Manager - London',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            26 => array(
                'id' => 27,
                'value' => 'HR Manager - Barrie',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            27 => array(
                'id' => 28,
                'value' => 'HR Manager - Central',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            28 => array(
                'id' => 29,
                'value' => 'OH&S Manager',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            29 => array(
                'id' => 30,
                'value' => 'Executive Assistant',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            30 => array(
                'id' => 31,
                'value' => 'Receptionist',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            31 => array(
                'id' => 32,
                'value' => 'HR Associate',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            32 => array(
                'id' => 33,
                'value' => 'Quartermaster',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            33 => array(
                'id' => 34,
                'value' => 'Lead Corporate Trainer',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            34 => array(
                'id' => 35,
                'value' => 'Assisstant Trainer',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            35 => array(
                'id' => 36,
                'value' => 'Director - Investigations',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            36 => array(
                'id' => 37,
                'value' => 'Vision SME',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            37 => array(
                'id' => 38,
                'value' => 'Marketing Manager',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            38 => array(
                'id' => 39,
                'value' => 'Management Consultant',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            39 => array(
                'id' => 40,
                'value' => 'Payroll Manager',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            40 => array(
                'id' => 41,
                'value' => 'Accounts Payable Clerk',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
            41 => array(
                'id' => 42,
                'value' => 'Chief Clerk',
                'created_at' => '2019-01-02 06:06:01',
                'updated_at' => '2019-01-02 06:06:01',
                'deleted_at' => null,
            ),
        ));
    }
}
