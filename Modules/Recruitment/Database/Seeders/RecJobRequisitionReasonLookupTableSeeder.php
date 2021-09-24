<?php

namespace Modules\Recruitment\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RecJobRequisitionReasonLookupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::connection('mysql_rec')->table('rec_job_requisition_reason_lookups')->delete();

        \DB::connection('mysql_rec')->table('rec_job_requisition_reason_lookups')->insert(array(
            0 => array(
                'id' => 1,
                'parent_id' => 0,
                'reason' => 'A permanent position has  opened up',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'parent_id' => 0,
                'reason' => 'A temporary requirement has opened up',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            2 => array(
                'id' => 3,
                'parent_id' => 1,
                'reason' => 'A new position has been created and approved by the client.',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            3 => array(
                'id' => 4,
                'parent_id' => 1,
                'reason' => 'A full time employee has retired',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            4 => array(
                'id' => 10,
                'parent_id' => 1,
                'reason' => 'A new position has been created and approved by CGL\'s COO.',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            5 => array(
                'id' => 11,
                'parent_id' => 1,
                'reason' => 'A full time employee has resigned',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            6 => array(
                'id' => 12,
                'parent_id' => 1,
                'reason' => 'A full time employee has been terminated',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            7 => array(
                'id' => 13,
                'parent_id' => 1,
                'reason' => 'We won a new contract (RFP)',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            8 => array(
                'id' => 14,
                'parent_id' => 1,
                'reason' => 'Other Reason',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            9 => array(
                'id' => 15,
                'parent_id' => 2,
                'reason' => 'A Commissionaire has taken sick leave',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            10 => array(
                'id' => 16,
                'parent_id' => 2,
                'reason' => 'A Commissionaire has taken vacation',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            11 => array(
                'id' => 17,
                'parent_id' => 2,
                'reason' => 'A Commissionaire has taken a leave of absence',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            12 => array(
                'id' => 18,
                'parent_id' => 2,
                'reason' => 'Other Reason',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            13 => array(
                'id' => 19,
                'parent_id' => 11,
                'reason' => 'Found Another Job At Higher Wage',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            14 => array(
                'id' => 20,
                'parent_id' => 11,
                'reason' => 'Work Related Stress',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            15 => array(
                'id' => 21,
                'parent_id' => 11,
                'reason' => 'Did Not Like Supervisor',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            16 => array(
                'id' => 22,
                'parent_id' => 11,
                'reason' => 'Did Not Like Client',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            17 => array(
                'id' => 23,
                'parent_id' => 11,
                'reason' => 'Wanted Career Opportunity',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            18 => array(
                'id' => 24,
                'parent_id' => 11,
                'reason' => 'Back To School',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            19 => array(
                'id' => 25,
                'parent_id' => 11,
                'reason' => 'No Reason Provided',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            20 => array(
                'id' => 26,
                'parent_id' => 11,
                'reason' => 'Other Reason',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            21 => array(
                'id' => 27,
                'parent_id' => 12,
                'reason' => 'Sleeping on the job',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            22 => array(
                'id' => 28,
                'parent_id' => 12,
                'reason' => 'Client had concerns with Commissionaire',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            23 => array(
                'id' => 29,
                'parent_id' => 12,
                'reason' => 'Insubordination with supervisor',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            24 => array(
                'id' => 30,
                'parent_id' => 12,
                'reason' => 'Unreliable, always taking time off',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            25 => array(
                'id' => 31,
                'parent_id' => 12,
                'reason' => 'Caught in unauthorized activity while on the job',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            26 => array(
                'id' => 32,
                'parent_id' => 12,
                'reason' => 'Customer service skills were lacking',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            27 => array(
                'id' => 33,
                'parent_id' => 12,
                'reason' => 'Other reason',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
        ));

    }
}
