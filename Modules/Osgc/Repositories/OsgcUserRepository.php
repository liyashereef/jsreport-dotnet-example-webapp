<?php

namespace Modules\Osgc\Repositories;

use Carbon\Carbon;
use Auth;
use Modules\Osgc\Models\OsgcUser;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Mail;
use Modules\Osgc\Mail\CourseMail;
use App\Repositories\MailQueueRepository;
use Modules\Osgc\Models\CoursePayment;
use Modules\Osgc\Repositories\OsgcUserCourseCompletionRepository;
class OsgcUserRepository
{
    protected $model;

    public function __construct(MailQueueRepository $mailQueueRepository, OsgcUser $model,CoursePayment $coursePayment,OsgcUserCourseCompletionRepository $courseCompletionRepository)
    {
        $this->model = $model;
        $this->coursePayment = $coursePayment;
        $this->courseCompletionRepository=$courseCompletionRepository;
        $this->mailQueueRepository =$mailQueueRepository;

    }


    public function addUsers($request)
    {
        $userarray = [];
        $userarray["first_name"] = $request->first_name;
        $userarray["last_name"] = $request->last_name;
        $userarray["email"] = $request->email;
        $userarray["password"] = bcrypt($request->password);
        $userarray["is_veteran"] = $request->is_veteran;
        $userarray["indian_status"] = $request->indian_status;
        $userarray["referral"] = $request->referral;

        $random = Str::random(40);
        $random_password = Str::random(6);
        $userarray['verification_token'] = $random;
        $activationUrl = URL::to('/osgc/activate-account' . '/' . $random);
        $userid = $this->model->create($userarray);
        $user = $userid->id;
        $userarray["activationUrl"]=$activationUrl;
        $userarray["password"]=$request->password;

        if ($user > 0) {
            $this->sendMail($userarray);
        }
        return $userid->id;
    }

    public function checkUserActivationLink($token)
    {
        $checkLink=$this->model->where('email_verified',0)->where('verification_token',$token)->first();
        if($checkLink)
        {
            $checkLink->email_verified=1;
            $checkLink->active=1;
            $checkLink->update();
        }
        return $checkLink;
    }

    public function sendMail($userarray)
    {
        $to = $userarray['email'];
        $model_name = 'Modules\Osgc\Models\OsgcUser';
        $subject = 'User Registration';
        $message =  "<p> Hi " . $userarray['first_name'] . ' ' . $userarray['last_name'] . ',</p>';
        $message .=  '<p> <a style="color:#000;" href="' . $userarray['activationUrl'] . '">Please click the link to activate your account </a> </p>';

        //$mail = Mail::to($to);
        $name=$userarray['first_name'] . ' ' . $userarray['last_name'];
        //$mail->send(new CourseMail($userarray, 'mail.create',$subject,$message,''));

        $helper_variables = array(
            '{activationUrl}' => $userarray['activationUrl'],
            '{receiverFullName}'=> $name
        );
        $this->mailQueueRepository->prepareMailTemplate(
            "osgc_user_registration",
            0,
            $helper_variables,
            $model_name,
            $requestor = 0,
            $assignee = 0,
            $from = null,
            $cc = null,
            $bcc = null,
            $mail_time = null,
            $created_by = null,
            $attachment_id = null,
            $to
        );
    }

    public function updateUserCourseStatus($arr)
    {
        $this->model
        ->where('id',\Auth::guard('osgcuser')->user()->id)
        ->update($arr);
    }
    public function getRegisteredUsers($inputs=[]) {

        $data= $this->model->with(['userSuccessPayments','userSuccessPayments.osgcCourses'])
        ->when(isset($inputs['course_completion_status']) && ($inputs['course_completion_status'] == 1), function ($query) use ($inputs) {
            $query->whereHas(
                    'userAllocatedCourses' ,function ($q) use($inputs){
                        return $q->where('status', $inputs['course_completion_status']);
             });
        })
        ->get();
        return $this->getUserArray($data,$inputs);
    }
    public function getUserArray($data,$inputs)
    { 
        $datatable_rows = array();
        foreach ($data as $key => $each_record) {
            $each_row["id"] = $each_record->id;
            $each_row["name"] = $each_record->first_name.' '.$each_record->last_name;
            $each_row["email"] = $each_record->email;
            $each_row["created_at"] = Carbon::parse($each_record->created_at)->format('Y-m-d h:i:s');

            if($each_record->active ==1)
            {
                $each_row["active"] = 'Active';

            }else{
                $each_row["active"] = 'Inactive';
            }
            if($each_record->indian_status==1)
            {
                $each_row["indian_status"] = 'Yes';

            }else{
                $each_row["indian_status"] = 'No';
            }
            if($each_record->is_veteran==1)
            {
                $each_row["is_veteran"] = 'Yes';

            }else{
                $each_row["is_veteran"] = 'No';
            }
            if($each_record->referral)
            {
                $referralArr =config('globals.referral');
                $each_row["referral"] =  $referralArr[$each_record->referral];

            }else{
                $each_row["referral"] = '';
            }
            if(count($each_record->userSuccessPayments) >0)
            {

                foreach($each_record->userSuccessPayments as $each_payment)
                {
                    $each_row["course_title"] = $each_payment->osgcCourses->title;
                    $each_row["amount"] = $each_payment->amount;
                    $each_row["payment_intent"] = $each_payment->payment_intent;
                    $each_row["paid_date"] = Carbon::parse($each_payment->created_at)->format('M Y');
                    if($each_payment->status==1)
                    {
                        $each_row["status"] = 'Paid';

                    }else{
                        $each_row["status"] = 'Failed';
                    }
                    if($each_payment->course_id)
                    {
                        $flag=1;
                        $completedFlag=false;
                        if(isset($inputs['course_completion_status']) && $inputs['course_completion_status'] ==1)
                        {
                            $completedFlag=true;
                            $flag=0;
                        }
                        $userCourse=$this->courseCompletionRepository->getUserAllocatedCourse($each_payment->course_id,$each_payment->user_id,$completedFlag);
                        if(!empty($userCourse))
                        {
                            $flag=1;
                        }
                        $each_row["last_course_completion"]=$userCourse->courseSection->name ?? '';
                        $percentageCount=$this->courseCompletionRepository->userCourseCompletionPercentage($each_payment->course_id,$each_payment->user_id);
                        if($percentageCount >= 0 &&  $percentageCount <=33)
                        {
                            $background='red';
                            $color='white';
                        }else if($percentageCount >33 &&  $percentageCount <=67)
                        {
                            $background='yellow';
                            $color='black';
                        }else if($percentageCount >67 &&  $percentageCount <=100)
                        {
                            $background='green';
                            $color='white';
                        }else{
                            $background='';
                            $color='';
                        }
                        $each_row["percentage_completion"]=$percentageCount.'%';
                        $paiddate = Carbon::parse($each_payment->created_at);
                        if(isset($userCourse->completed_time) && !empty($userCourse->completed_time))
                        {
                            $now = Carbon::parse($userCourse->completed_time);
                        }else{
                            $now = Carbon::now();

                        }
                        //    dd($each_payment);

                        $diff = $paiddate->diffInDays($now);//dd($diff);
                        $each_row["days_tracker"]=$diff;
                        $each_row["background_color"]=$background;
                        $each_row["color"]=$color;
                    }else{
                        $each_row["course_completion"]='';
                        $each_row["last_course_completion"]='';
                        $each_row["percentage_completion"]='';
                        $each_row["days_tracker"]='';
                        $each_row["background_color"]='';
                        $each_row["color"]='';
                    }
                    if($flag ==1)
                    {
                        array_push($datatable_rows, $each_row);
                    }
                    
                }
            }else{
                    $each_row["course_title"] = '';
                    $each_row["amount"] = '';
                    $each_row["payment_intent"] = '';
                    $each_row["paid_date"] = '';
                    $each_row["status"] = 'Unpaid';
                    $each_row["course_completion"]='';
                    $each_row["last_course_completion"]='';
                    $each_row["percentage_completion"]='';
                    $each_row["days_tracker"]='';
                    $each_row["background_color"]='';
                    $each_row["color"]='';
                    array_push($datatable_rows, $each_row);
                }
              
        }

        return $datatable_rows;
    }

    public function updateUserStatus($arr,$id)
    {
        $this->model
        ->where('id',$id)
        ->update($arr);
        return true;
    }
     /**
     * Reset password
     *
     * @param \App\Models\OsgcUser $user
     * @return $content
     */
    public function resetPassword($email)
    {
        $user = $this->model->where(['email' => $email])->first();
        if ($user) {
            if($user->active ==1){
                $random_password = Str::random(8);
                $user->password = bcrypt($random_password);
                $user->save();
                /* send email to user */
                $this->sendResetPasswordMail($user,$random_password);

                $content['success'] = true;
                $content['message'] = 'Your password has been reset and the same is sent to your registered mail id';
            } else{
                $content['success'] = false;
                $content['message'] = 'Account is not activated';
            }
        } else {
            $content['success'] = false;
            $content['message'] = 'Account does not exist. Please use your registered email address to reset your password';

        }
        return $content;
    }
    public function sendResetPasswordMail($user,$random_password)
    {
        $to = $user['email'];
        $model_name = 'Modules\Osgc\Models\OsgcUser';
        $subject = 'Reset Password';
        $message =  "<p> Hi " . $user['first_name'] . ' ' . $user['last_name'] . ',</p>';
        $message .=  "<p>You password has been changed and new password is ".$random_password." </p>";

           // $mail = Mail::to($to);
            $name=$user['first_name'] . ' ' . $user['last_name'];
            //$mail->send(new CourseMail($user, 'mail.create',$subject,$message,''));

            $helper_variables = array(
                '{randomPassword}' => $random_password,
                '{receiverFullName}'=> $name
            );

            $this->mailQueueRepository->prepareMailTemplate(
                "osgc_reset_password",
                0,
                $helper_variables,
                $model_name,
                $requestor = 0,
                $assignee = 0,
                $from = null,
                $cc = null,
                $bcc = null,
                $mail_time = null,
                $created_by = null,
                $attachment_id = null,
                $to
            );
    }

     /**
     * Update password
     *
     * @param \App\Models\OsgcUser $user
     * @return $content
     */
    public function updatePassword($request)
    {
        $oldPassword = $request->post('old_password');
        $newPassword = $request->post('password');
        if ((\Hash::check($request->post('old_password'), \Auth::guard('osgcuser')->user()->password))) {
            $user = \Auth::guard('osgcuser')->user();
            $user->password = bcrypt($newPassword);
            $user->save();

            $content['success'] = true;
            $content['message'] = 'Your password has been updated Successfully';

        } else {
            $content['success'] = false;
            $content['message'] = 'Incorrect old password';

        }
        return $content;
    }
    public function getUserByEmail($email) {
        return $this->model->where('email',$email)->first();
    }
    public function get($id) {
        return $this->model->get()->find($id);
    }
    public function update($data) {

        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }
    
    
}
