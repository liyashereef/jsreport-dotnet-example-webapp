<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EmailTemplateNotificationTypeTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */


    public function run()
    {
        \DB::table('email_templates')->whereIn('type_id', array(
            3, 4, 5, 6, 7, 26, 27, 31, 36, 37, 38, 39, 40,
            41, 42, 43, 44, 45, 47, 48, 49, 50 ,51
        ))->delete();

        \DB::table('email_templates')->insert(array(
            0 =>
            array(
                'type_id' => 3,
                'email_subject' => 'Maintenance Due',
                'email_body' => '<p>Hi {receiverFullName},</p><p>Please note than following services are due for vehicle with reg no&nbsp;{vehicleNumber}-{region}</p><p>{maintenanceDetails}</p>',
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
                'deleted_at' => null,
            ),
            1 =>
            array(
                'type_id' => 4,
                'email_subject' => 'Maintenance Critical',
                'email_body' => '<p>Hi {receiverFullName},</p><p>This is to inform that the following services are critical for the vehicle with reg no: {vehicleNumber}- region: {region}</p><p><br />{maintenanceDetails}</p>',
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
                'deleted_at' => null,
            ),
            2 =>
            array(
                'type_id' => 5,
                'email_subject' => 'Client scheduling request',
                'email_body' => '<p>Hello {receiverFullName},</p><p>A Scheduling request against {client} - {projectNumber} has been raised.</p><p>Please verify and choose to Approve/Deny request</p><p>{thresholdexceededlist}</p><p>Regards,<br />{loggedInUser} - {loggedInUserEmployeeNumber} &nbsp;</p>',
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
                'deleted_at' => null,
            ),
            3 =>
            array(
                'type_id' => 6,
                'email_subject' => 'Client scheduling approved/rejected notification',
                'email_body' => '<p>Hello {receiverFullName},</p><p>A Scheduling request {scheduleStatus} against {client} - {projectNumber}</p><p>{scheduleReasonNote}</p><p>Regards,<br />{loggedInUser} - {loggedInUserEmployeeNumber} &nbsp;</p>',
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
                'deleted_at' => null,
            ),
            4 =>
            array(
                'type_id' => 7,
                'email_subject' => 'Client scheduling request user notification',
                'email_body' => '<p>Hello {receiverFullName},</p><p>A Scheduling request against {client} - {projectNumber} has been raised successfully.</p><p>Regards,<br />{loggedInUser} - {loggedInUserEmployeeNumber} &nbsp;</p>',
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
                'deleted_at' => null,
            ),
            5 =>
            array(
                'type_id' => 26,
                'email_subject' => 'CGL Meet scheduling request user notification',
                'email_body' => '<p>Hello {receiverFullName},</p><p>A CGL Meet Scheduling request
                 has been raised against you .Please find the details below</p><p><a href="{maillink}" target="_blank">Click here</a></p>',
                'created_at' => '2020-12-17 06:04:47',
                'updated_at' => '2020-12-17 06:04:47',
                'deleted_at' => null,
            ),
            6 =>
            array(
                'type_id' => 27,
                'email_subject' => 'CGL Meet scheduling cancellation notification',
                'email_body' => '<p>Hello {receiverFullName},</p><p>A CGL Meet Scheduling request
                 raised against you has been cancelled</p><p>
                 </p>',
                'created_at' => '2020-12-17 06:04:47',
                'updated_at' => '2020-12-17 06:04:47',
                'deleted_at' => null,
            ),
            7 =>
            array(
                'type_id' => 31,
                'email_subject' => 'Daily Health Screen Report',
                'email_body' => '<p>Hello {receiverFullName},</p><p>Please find the attached daily health screen report</p><p>
                 </p>',
                'created_at' => '2021-02-26 06:04:47',
                'updated_at' => '2021-02-26 06:04:47',
                'deleted_at' => null,
            ),
            8 =>
            array(
                'type_id' => 36,
                'email_subject' => 'Contract Expiry Reminder Email 1',
                'email_body' => '<p>Hello {receiverFullName},</p><p>This is a gentle reminder that your Contract against a {contractName} is going to expire on {expiryDate}.</p><p> It will expires with in {contractExpiryDueInDays} days </p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            9 =>
            array(
                'type_id' => 37,
                'email_subject' => 'Contract Expiry Reminder Email 2',
                'email_body' => '<p>Hello {receiverFullName},</p><p>This is a gentle reminder that your Contract against a {contractName} is going to expire on {expiryDate}.</p><p> It will expires with in {contractExpiryDueInDays} days </p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            10 =>
            array(
                'type_id' => 38,
                'email_subject' => 'Contract Expiry Reminder Email 3',
                'email_body' => '<p>Hello {receiverFullName},</p><p>This is a gentle reminder that your Contract against a {contractName} is going to expire on {expiryDate}.</p><p> It will expires with in {contractExpiryDueInDays} days </p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            11 =>
                array(
                    'type_id' => 39,
                    'email_subject' => 'Rejection Note notification',
                    'email_body' => '<p>Hello {receiverFullName},</p><p>Thank you for applying. Unfortunately we have proceeded  with other candidate. Keep Looking.</p>',
                    'created_at' => '2019-05-21 06:04:47',
                    'updated_at' => '2019-05-21 06:04:47',
                    'deleted_at' => null,
                ),
            12 =>
                array(
                    'type_id' => 40,
                    'email_subject' => 'Selected for Interview notification',
                    'email_body' => '<p>Hello {receiverFullName},</p><p>You have been selected for an interview by {recruiterFullName} .</p><p>Date :</p><p>Time :</p><p>Job : {jobCode}</p><p>Wage : ${jobWage} </p><p>Zoom link  :</p>',
                    'created_at' => '2019-05-21 06:04:47',
                    'updated_at' => '2019-05-21 06:04:47',
                    'deleted_at' => null,
                ),

            13 =>

                array(
                    'type_id' => 41,
                    'email_subject' => 'Rejected For Role',
                    'email_body' => '<p>Hello {receiverFullName},</p><p>Thank you. You interviewed well but we decided to go with  with another. Keep Looking.</p>',
                    'created_at' => '2019-05-21 06:04:47',
                    'updated_at' => '2019-05-21 06:04:47',
                    'deleted_at' => null,
                ),

            14 =>

                array(
                    'type_id' => 42,
                    'email_subject' => 'Begin Onboarding Notification',
                    'email_body' => '<p>Hello {receiverFullName},</p><p>Your Job Application have  begin Onboarding by {recruiterFullName} .</p>',
                    'created_at' => '2019-05-21 06:04:47',
                    'updated_at' => '2019-05-21 06:04:47',
                    'deleted_at' => null,
                ),

            15 =>
                array(
                    'type_id' => 43,
                    'email_subject' => 'Password Reset',
                    'email_body' => '<p>Hello {receiverFullName},</p><p>You recently requested for a password change. Click the link to reset it.</p><p><a href="{activationUrl}" target="_blank">Click here</a></p>',
                    'created_at' => '2019-05-21 06:04:47',
                    'updated_at' => '2019-05-21 06:04:47',
                    'deleted_at' => null,
                ),

            16 =>

                array(
                    'type_id' => 44,
                    'email_subject' => 'Register Email',
                    'email_body' => '<p>Hello {receiverFullName},</p><p>Please Register your profile by clicking the link below.</p><p><a href="{activationUrl}" target="_blank">Click here</a></p>',
                    'created_at' => '2019-05-21 06:04:47',
                    'updated_at' => '2019-05-21 06:04:47',
                    'deleted_at' => null,
                ),


            17 =>

                array(
                    'type_id' => 45,
                    'email_subject' => 'Login Remainder',
                    'email_body' => '<p>Hello {receiverFullName},</p><p>You haven\'t login to our system yet. Please login.</p>',
                    'created_at' => '2019-05-21 06:04:47',
                    'updated_at' => '2019-05-21 06:04:47',
                    'deleted_at' => null,
                ),

            18 =>

                array(
                    'type_id' => 46,
                    'email_subject' => 'Good Score',
                    'email_body' => '<p>Hello {receiverFullName},</p><p>Congratulations! You have total score of {total_score} for the client {client}-{projectNumber}.</p>',
                    'created_at' => '2019-05-21 06:04:47',
                    'updated_at' => '2019-05-21 06:04:47',
                    'deleted_at' => null,
                ),

            19 =>
                array(
                    'type_id' => 47,
                    'email_subject' => 'Uniform Shipped',
                    'email_body' => '<p>Hello {receiverFullName},</p><p>Your Uniform Kit for the client {client}-{projectNumber} has been shipped.</p>',
                    'created_at' => '2019-05-21 06:04:47',
                    'updated_at' => '2019-05-21 06:04:47',
                    'deleted_at' => null,
                ),

            20 =>

                array(
                    'type_id' => 48,
                    'email_subject' => 'Application Process Completed',
                    'email_body' => '<p>Hello {receiverFullName},</p><p>Application Process has been completed by {candidate}.</p>',
                    'created_at' => '2019-05-21 06:04:47',
                    'updated_at' => '2019-05-21 06:04:47',
                    'deleted_at' => null,
                ),
            21 =>

                array(
                    'type_id' => 49,
                    'email_subject' => 'Onboarding Due Remainder',
                    'email_body' => '<p>Hello {receiverFullName},</p><p>Your application for job {jobCode} has been Onboarded and require further submission of details. The due date of job is on {dueDate}.</p>',
                    'created_at' => '2019-05-21 06:04:47',
                    'updated_at' => '2019-05-21 06:04:47',
                    'deleted_at' => null,
                ),
            22 =>
                array(
                    'type_id' => 50,
                    'email_subject' => 'Application Process Completed',
                    'email_body' =>'<p>Hello {receiverFullName},</p><p>Thank you for taking the time to complete your candidate profile. Attached is your application screen. I know you invested a great deal of time. In return, you have my commitment we will do our part to find an appropriate fit for you if you pass our candidate screen. At a minimum, we use algorithms to evaluate your experience, wage expectations, license renewal dates, distance to the job site, among 90 other criteria. As I stated in my recorded message, we are very selective of the applicants we invite to join our organization. However, when you get in – you have a world of opportunity and we take care of our guards.</p><p>I would ask you to be patient. Now that you are in our database, I or a member of our recruiting team will contact you if there is a good job match. If you don\’t hear from us, it likely means there were a number of other applicants who were better suited for the position you applied for but don’t fret! You will still be considered for new jobs coming up each day.</p><p>Now, as the SVP/COO of Commissionaires Great Lakes, I have a company to run. However, if you don\’t hear anything from us over the next 3 months, I will give you my email to contact me. I or my assistant will do our best to get back to you and let you know what is available. My personal email address is:</p><p>benjamin.alexander@commissionaires-cgl.ca</p><p>Please do not abuse this. If you send me multiple emails or contact me within 3 months – I will have no choice but to block you. We have over 1,700 employees so you can appreciate my inbox often gets flooded. However, recruiting is so strategic to what we do that I do take time to get back to legitimate inquiries if you are looking to find out about the status of your application. Remember, give it a minimum of 3 months before you email me if you haven\'t been contacted and I\'ll inquire about the status of your application.</p><p>Regards,</p><p>Ben Alexander <br>SVP/COO <br>Commissionaires Great Lakes,</p>',
                    'created_at' => '2019-05-21 06:04:47',
                    'updated_at' => '2019-05-21 06:04:47',
                    'deleted_at' => null,
                ),
                23 =>
                array(
                    'type_id' => 51,
                    'email_subject' => 'Application Evaluation Acknowledgement',
                    'email_body' =>'<p>Hello {receiverFullName},</p><p>Thank you for taking the time to complete your candidate application.Your application has been evaluated by {recruiterFullName}.You can now apply for the openings with the company.</p>',
                    'created_at' => '2019-05-21 06:04:47',
                    'updated_at' => '2019-05-21 06:04:47',
                    'deleted_at' => null,
                )
                ));
    }
}
