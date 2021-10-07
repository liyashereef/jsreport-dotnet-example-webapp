<?php

namespace App\Repositories;

use App\Models\MailQueue;
use Modules\Admin\Models\MailQueueMultipleAttachments;
use Mail;
use Carbon\Carbon;
use DB;
use App\Mail\MailQueueToSend;
use Illuminate\Support\Facades\Auth;
use App\Repositories\AttachmentRepository;
use App\Models\Attachment;
use Modules\Admin\Models\EmailNotificationType;
use Modules\Admin\Models\CustomerTemplateEmail;
use Modules\Admin\Models\CustomerTemplateUseridMapping;
use App\Services\HelperService;
use Modules\Admin\Models\EmailTemplate;
use Modules\Recruitment\Models\RecCandidate;
use Modules\Admin\Models\User;
use Spatie\Permission\Models\Role;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use App;

class MailQueueRepository
{
    protected $model, $attachmentRepository, $multipleAttachmentsmodel;

    public function __construct(
        // MailQueue $mailQueue,
        // AttachmentRepository $attachmentRepository,
        // EmailNotificationType $notificationType
    ) {
        $this->model = new MailQueue();
        $this->multipleAttachmentsmodel = new MailQueueMultipleAttachments();
        $this->notificationType = new EmailNotificationType();
        $this->attachmentRepository = new AttachmentRepository();
       // $repository = app()->make('Your\Namespaced\Repository');
        $this->customerEmployeeAllocationRepository =  app()->make('Modules\Admin\Repositories\CustomerEmployeeAllocationRepository');
    }

    /**
     * Use this function to queue mail.
     */
    public function storeMail($to, $subject, $message, $model_name, $from = null, $cc = null, $bcc = null, $mail_time = null, $created_by = null, $attachment_id = null, $s3Bucket = null, $s3FileName = null)
    {

        if ($to != null) {
            $mailQueue = new MailQueue;
            $mailQueue->from = ($from != null) ? $from : \Config::get('mail.from.address');
            $mailQueue->to = $to;
            $mailQueue->cc = ($cc) ? serialize($cc) : '';
            $mailQueue->bcc = ($bcc) ? $bcc : '';
            $mailQueue->subject = $subject;
            $mailQueue->message = $message;
            $mailQueue->mail_time = ($mail_time != null) ? $mail_time : Carbon::now();
            $mailQueue->created_by = ($created_by != null) ? $created_by : \Auth::id();
            // $mailQueue->attachment_id = $attachment_id;
            $mailQueue->attachment_id = $attachment_id ? $attachment_id : null;
            $mailQueue->model_name = $model_name ? $model_name : null;

            if (!empty($s3FileName)) {
                if (is_array($s3FileName)) {
                    $mailQueue->is_multiple_attachment = 1;
                } else {
                    $mailQueue->is_multiple_attachment = 0;
                    $mailQueue->s3_repo_filename = $s3FileName ? $s3FileName : null;
                    $mailQueue->s3_bucket_name = $s3Bucket ? $s3Bucket : null;
                }
            }

            $mailQueue->save();
            if ($mailQueue->is_multiple_attachment == 1) {
                foreach ($s3FileName as $files) {
                    $attachmentArr = array(
                        'mail_queue_id' => $mailQueue->id,
                        's3_bucket_name' => $s3Bucket ? $s3Bucket : null,
                        's3_repo_filename' => $files ? $files : null,
                    );
                    $this->multipleAttachmentsmodel->create($attachmentArr);
                }
            }
        } else {
            \Log::channel('mailQueueError')->info("To mailid not found. Model-Name: " . $model_name . ' Subject: ' . $subject . ' in ' . 'app/Repositories/MailQueueRepository.php, function storeMail()');
        }
    }

    /**
     * Service to prepare mail from mail template
     * @param $template Template type
     * @param $customer_id Customer Id
     * @param $helper_variable Dynamic variables in email templates
     * @param $model template name
     * @param int $requestor Requestor user id
     * @param int $assignee Assignee user id
     * @param null $from
     * @param null $cc
     * @param null $bcc
     * @param null $mail_time
     * @param null $created_by
     * @param null $attachment_id
     */
    public function prepareMailTemplate(
        $template,
        $customer_id,
        $helper_variable,
        $model,
        $requestor = 0,
        $assignee = 0,
        $from = null,
        $cc = null,
        $bcc = null,
        $mail_time = null,
        $created_by = null,
        $attachment_id = null,
        $toEmail = null,
        $rec_candidate_id = 0,
        $rec_dynamic_email_text = null,
        $s3Bucket = null,
        $s3FileName = null
    ) {

        try {
            $template = $this->notificationType->where('type', $template)->get()->first();

            if ($template->id) {
                $email_template = EmailTemplate::where('type_id', $template->id)->first();
                if ($customer_id != 0) {
                    $customer_template_id = CustomerTemplateEmail::where('customer_id', $customer_id)->where('template_id', $template->id)->first();
                } else {
                    $customer_template_id = CustomerTemplateEmail::where('customer_id', 0)->where('template_id', $template->id)->first();
                }

                if ($customer_template_id) {
                    $user_ids = CustomerTemplateUseridMapping::has('userDetails')->with('userDetails')->where('template_email_id', $customer_template_id->id)->get();
                    $full_name = data_get($user_ids, '*.userDetails.full_name');
                    $email = data_get($user_ids, '*.userDetails.email');
                    $email_ids = array_combine($full_name, $email);
                    //Newly Added-Start

                    if ($customer_template_id->send_to_areamanagers==1) {
                        $customer_id= $customer_id? $customer_id:0;
                        $areamanagerlist=$this->customerEmployeeAllocationRepository->allocationList($customer_id, ['area_manager'], false, true)->pluck( 'email','full_name')->toArray();
                        $email_ids=array_merge($email_ids,$areamanagerlist);

                    }
                    if ($customer_template_id->send_to_supervisors==1) {
                        $supervisorlist=$this->customerEmployeeAllocationRepository->allocationList($customer_id, ['area_manager'], false, true)->pluck( 'email','full_name')->toArray();
                         $email_ids=array_merge($email_ids,$supervisorlist);
                    }
                    //Newly Added-End
                    foreach ($email_ids as $name => $to) {
                        $mail_content = HelperService::replaceText($name, $email_template->email_subject, $email_template->email_body, $helper_variable);
                        $this->storeMail($to, $mail_content['subject'], $mail_content['body'], $model, $from = null, $cc = null, $bcc = null, $mail_time = null, $created_by = null, $attachment_id = null, $s3Bucket, $s3FileName);
                    }
                }

                $role_based_id = CustomerTemplateEmail::with('roleIdMapping.roleName')->where('customer_id', $customer_id)->where('template_id', $template->id)->where('role_based', 1)->get();
                if ($role_based_id) {
                    foreach ($role_based_id as $each_data) {
                        foreach ($each_data->roleIdMapping as $eachrole) {
                            $role_name = Role::find($eachrole->role_id);
                            $user_details = User::role($role_name->name)->select('id', 'first_name', 'last_name', 'email')->get();
                            foreach ($user_details as $key => $value) {
                                $name = $value->first_name . ' ' . $value->last_name;
                                $to = $value->email;
                                $mail_content = HelperService::replaceText($name, $email_template->email_subject, $email_template->email_body, $helper_variable);
                                $this->storeMail($to, $mail_content['subject'], $mail_content['body'], $model, $from = null, $cc = null, $bcc = null, $mail_time = null, $created_by = null, $attachment_id = null, $s3Bucket, $s3FileName);
                            }
                        }
                    }
                }


                if ($requestor > 0) {
                    $user = User::find($requestor);
                    $name = $user->first_name . " " . $user->last_name;
                    $to = $user->email;
                    $mail_content = HelperService::replaceText($name, $email_template->email_subject, $email_template->email_body, $helper_variable);
                    $this->storeMail($to, $mail_content['subject'], $mail_content['body'], $model, $from = null, $cc = null, $bcc = null, $mail_time = null, $created_by = null, $attachment_id, $s3Bucket, $s3FileName);
                }
                if ($assignee > 0) {
                    $user = User::find($assignee);
                    $name = $user->first_name . " " . $user->last_name;
                    $to = $user->email;
                    $mail_content = HelperService::replaceText($name, $email_template->email_subject, $email_template->email_body, $helper_variable);
                    $this->storeMail($to, $mail_content['subject'], $mail_content['body'], $model, $from = null, $cc = null, $bcc = null, $mail_time = null, $created_by = null, $attachment_id = null, $s3Bucket, $s3FileName);
                }
                if ($rec_candidate_id > 0) {
                    $user = RecCandidate::find($rec_candidate_id);
                    $name = $user->name;
                    $to = $user->email;
                    $email_body = (isset($rec_dynamic_email_text)) ? $rec_dynamic_email_text : $email_template->email_body;
                    $mail_content = HelperService::replaceText($name, $email_template->email_subject, $email_body, $helper_variable);
                    $this->storeMail($to, $mail_content['subject'], $mail_content['body'], $model, $from = null, $cc = null, $bcc = null, $mail_time = null, $created_by = null, $attachment_id, $s3Bucket, $s3FileName);
                }

                if ($toEmail != null) {
                    $name = $helper_variable['{receiverFullName}'];
                    unset($helper_variable['{receiverFullName}']);
                    $mail_content = HelperService::replaceText($name, $email_template->email_subject, $email_template->email_body, $helper_variable);
                    $this->storeMail($toEmail, $mail_content['subject'], $mail_content['body'], $model, $from = null, $cc = null, $bcc = null, $mail_time = null, $created_by = null, $attachment_id = null, $s3Bucket, $s3FileName);
                }
            }
            return true;
        } catch (\Exception $e) {
            $error = 'error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' at ' . $e->getLine();
            \Log::error($error);
            return $error;
        }
    }


    public function mailSendingWithQueue()
    {
        $mailWithNotActives = $this->model->with(['attachmentDetails', 'multipleAttachmentDetails']);

        $mailWithNotActives = $mailWithNotActives->where(function ($q) {
            $q->where('send_status', 0)
                ->where('active', 1)
                ->whereDate('mail_time', '<=', Carbon::now());
        });
        $mailWithNotActives = $mailWithNotActives->get();
        $current_time = Carbon::now();
        $send_time = $current_time->toDateTimeString();

        if (count($mailWithNotActives) > 0) {
            $time_delay = 10;
            $when = now()->addSeconds($time_delay);
            foreach ($mailWithNotActives as $mailWithNotActive) {
                try {
                    $toMail = $mailWithNotActive->to;
                    $ccMail = unserialize($mailWithNotActive->cc);
                    $bccMail = $mailWithNotActive->bcc;
                    if ($mailWithNotActive->is_multiple_attachment == 1) {
                        $s3FileName = array();
                        if ($mailWithNotActive->multipleAttachmentDetails) {
                            foreach ($mailWithNotActive->multipleAttachmentDetails as $fileName) {
                                $s3FileName[] = $fileName->s3_repo_filename ?? null;
                                $s3BucketName = $fileName->s3_bucket_name ?? null;
                            }
                        }
                    } else {
                        $s3FileName = array(
                            $mailWithNotActive->s3_repo_filename ?? null
                        );
                        $s3BucketName = $mailWithNotActive->s3_bucket_name ?? null;
                    }
                    $when = $when->addSeconds($time_delay);

                    if ($ccMail == '') {
                        if ($mailWithNotActive->attachmentDetails != '') {
                            $att = $this->attachmentRepository->downloadDetails(null, $mailWithNotActive->attachmentDetails->id, $mailWithNotActive->attachmentDetails->file_module);
                            if ($att != "") {
                                Mail::to($toMail)
                                    ->later($when, new MailQueueToSend($mailWithNotActive, $att['path'], $att['name'], null, null));
                            }
                        }
                        if ($s3BucketName != '' && !empty($s3FileName)) {
                            Mail::to($toMail)
                                ->later($when, new MailQueueToSend($mailWithNotActive, null, null, $s3BucketName, $s3FileName));
                        } else {
                            Mail::to($toMail)
                                ->later($when, new MailQueueToSend($mailWithNotActive, null, null, null, null));
                        }
                    } else {
                        Mail::to($toMail)
                            ->cc($ccMail)
                            ->later($when, new MailQueueToSend($mailWithNotActive, null, null, null, null));
                    }
                    if (count(Mail::failures()) > 0) {
                        $send = 0;
                    } else {
                        $send = 1;
                    }

                    DB::table('mail_queue')
                        ->where('id', $mailWithNotActive->id)
                        ->update(['send_status' => $send, 'send_time' => $send_time]);
                } catch (\Exception $e) {
                    \Log::channel('mailQueueError')->info("mailId: " . $mailWithNotActive->id . ", Error : " . $e);
                }
            }
        }
    }
}
