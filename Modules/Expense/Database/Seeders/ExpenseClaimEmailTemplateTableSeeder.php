<?php

namespace Modules\Expense\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ExpenseClaimEmailTemplateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('email_templates')->whereIn('type_id', array(
            69, 70, 71, 72
        ))->delete();

        DB::table('email_templates')->insert(array(
            0 =>
            array(
                'type_id' => 69,
                'email_subject' => 'Expense claim has been rejected',
                'email_body' => '<p>Expense claim has been rejected by {claimRejectedByName}.
                Please log into your CGL360 account to review the transaction.<br /><br />
                Expense Cost: {claimTotalAmount} <br />Rejection Date: {claimRejectedDate}</p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => NULL,
            ),
            1 =>
            array(
                'type_id' => 70,
                'email_subject' => 'Expense claim has been approved',
                'email_body' => '<p>Expense claim has been approved by {claimApprovedByName}.
                 Please log into your CGL360 account to review the transaction.<br /><br />
                 Expense Cost: {claimTotalAmount} <br />Approval Date: {claimApprovedDate} </p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => NULL,
            ),
            2 =>
            array(
                'type_id' => 71,
                'email_subject' => 'Expense claim has been approved',
                'email_body' => '<p>Expense claim has been approved by {claimApprovedByName} and forwarded to Financial controller.
                Please log into your CGL360 account to review the transaction.<br /><br />
                Expense Cost: {claimTotalAmount} <br />Approval Date: {claimApprovedDate}</p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => NULL,
            ),
            3 =>
            array(
                'type_id' => 72,
                'email_subject' => 'Expense claim has been submitted',
                'email_body' => '<p>A new claim has been submitted by {claimCreatedByName} and approved by {claimApprovedByName}.
                Please log into your CGL360 account to review the transaction.<br /><br />
                Expense Cost: {claimTotalAmount} <br />Submitted Date: {claimSubmittedDate}</p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => NULL,
            ),

        ));
    }
}
