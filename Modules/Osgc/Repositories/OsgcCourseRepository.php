<?php

namespace Modules\Osgc\Repositories;

use Carbon\Carbon;
use Modules\Osgc\Models\OsgcCourse;
use Modules\Osgc\Models\OsgcCourseContentHeader;
use Modules\Osgc\Models\OsgcCourseContentSection;
use Modules\Osgc\Models\UserCourseCompletion;
use Modules\Osgc\Models\UserCourseCertification;
use Modules\Osgc\Repositories\OsgcUserCourseCompletionRepository;
use PDF;
use File;
use Mail;
use Modules\Osgc\Mail\CourseMail;
use App\Repositories\MailQueueRepository;

class OsgcCourseRepository
{
    protected $courseModel;
    protected $courseSectionModel;
    protected $courseHeaderModel;
    public function __construct(MailQueueRepository $mailQueueRepository,UserCourseCompletion $userCourseCompletion,OsgcUserCourseCompletionRepository $osgcUserCourseCompletionRepository)
    {
        $this->directory_seperator = "/";
        $this->extension_seperator = ".";
        $this->courseModel = new OsgcCourse();
        $this->courseSectionModel = new OsgcCourseContentSection();
        $this->courseHeaderModel = new OsgcCourseContentHeader();
        $this->userCourseCompletion =$userCourseCompletion;
        $this->osgcUserCourseCompletionRepository =$osgcUserCourseCompletionRepository;
        $this->mailQueueRepository = $mailQueueRepository;

    }


    public function getCourseDetails($id)
    {
        return $this->courseModel->with(['ActiveCourseHeaders','ActiveCourseSections','ActiveCourseHeaders.courseUserCompletion','ActiveCourseSections.courseUserCompletion','ActiveCourseSections.studyGuide'])
        ->where('id',$id)->first();
    }
    public function getCourseSectionDetails($sectionId)
    {
        return $this->courseSectionModel->with(['courseContent'])->where('id',$sectionId)->first();
    }
    public function checkCourseCompletion($request)
    {
        $return=false;
        $sectionDet=$this->courseSectionModel->where('id',$request->section_id)->first();
        $headerDet=$this->courseHeaderModel->where('id',$sectionDet->header_id)->first();//dd($headerDet);
        $firstHeader=$this->courseHeaderModel->where('course_id',$request->course_id)->orderBy('sort_order','ASC')->where('active',1)->first();//dd($headerDet);
        $firstSectionId=$this->courseSectionModel->where('header_id',$firstHeader->id)->orderBy('sort_order','ASC')->where('active',1)->first();//dd($firstSectionId);


            if(isset($firstSectionId->id) && $firstSectionId->id == $request->section_id)
            {
                $return=true;
            }else{
                $isExist=$this->osgcUserCourseCompletionRepository->getAlreadyExist($request->section_id);
                if($isExist !=0)
                {
                    $return=true;
                }else{
                     $prevHeaderDet=$this->courseHeaderModel->where('course_id',$request->course_id)->where('sort_order', '<', $headerDet->sort_order)->where('active',1)->orderBy('sort_order','ASC')->pluck('id');

                     $sectionids=OsgcCourseContentSection::whereIn('header_id',$prevHeaderDet)->where('active',1)
                     ->where('completion_mandatory',1)
                     ->pluck('id');
                     $courseId=$request->course_id;
                     $checkCouseCompletion=$this->userCourseCompletion->where('user_id',\Auth::guard('osgcuser')->user()->id)
                    ->whereIn('course_section_id',$sectionids)
                   // ->whereIn('course_header_id',$prevHeaderDet)
                    ->where('status',1)->count();//dd($checkCouseCompletion);
                    if(count($sectionids) <= $checkCouseCompletion)
                    {
                        return true;
                    }




                }
            }


        return $return;

    }
    /**
     * Get first section id
     * @return Response
     */
    public function getFirstSectionId($course_id)
    {
        $firstHeader=$this->courseHeaderModel->where('course_id',$course_id)->where('active',1)->orderBy('sort_order','ASC')->first();//dd($headerDet);
        $firstSectionId=$this->courseSectionModel->where('header_id',$firstHeader->id)->where('active',1)->orderBy('sort_order','ASC')->first();
        return $firstSectionId;
    }
    /**
     * generate certificate
     * @return Response
     */
    public function generatePdf($courseId)
    {
        $checkCourseCompleted=$this->osgcUserCourseCompletionRepository->checkAllCourseAndTestCompleted($courseId,\Auth::guard('osgcuser')->user()->id);
        if($checkCourseCompleted == true){
            $courseDetails=$this->getCourseDetails($courseId);
            $name=\Auth::guard('osgcuser')->user()->first_name.' '.\Auth::guard('osgcuser')->user()->last_name ?? '';
            $result=array('course_id'=>$courseId,
            'course_name'=>$courseDetails->title,
            'user_name'=>$name,
            'course_date'=>\Carbon::now()->format('M d, Y'),
            );
            $testpath=storage_path('certificate/certificate-img.png');//dd($testpath);
            $bg = base64_encode(file_get_contents(storage_path('certificate/certificate-img.png')));
            $bg = "data:image/png;base64,".$bg;
            $customPaper = array(0,0,700,920);
            $pdf = PDF::loadView('osgc::course.certificate-content',compact('result','bg'));
            $pdf->setPaper($customPaper,'landscape');
            $cerificateFilename = uniqid('course_certificate_') . ".pdf";
            $certificatePath='osgc/certificate/'.$cerificateFilename;

            $disk = \Storage::disk('awsS3Bucket');
            $savedData=$disk->put($certificatePath, $pdf->output());


        //    $file_path = 'osgc';
        //    $cerificateFilename = uniqid('course_report_') . ".pdf";
        //    $path = storage_path('app') . $this->directory_seperator . $file_path;
        //     File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
        //    $filename = $this->directory_seperator . $cerificateFilename; //dd($filename);
        //     $pdf->save($path . $filename);
            $consentFormPath='osgc/consent-form.pdf';
            $certificationArr=array('course_id'=>$courseId,'user_id'=>\Auth::guard('osgcuser')->user()->id,'certificate_name'=>$cerificateFilename);
            $this->storeCourseCertification($certificationArr);
            if ($disk->exists($certificatePath)) {
                $fileDet = $disk->get($certificatePath);
                $multipleFilePath=array($certificatePath,$consentFormPath);
                $this->sendCertificateToMail($courseId,$fileDet,$multipleFilePath);
            }

            return $result;
        }
    }
    /**
     * sending coursee certificate to mail
     * @return Response
     */
    public function sendCertificateToMail($courseId,$fileDet,$certificatePath)
    {
        $courseDetails=$this->getCourseDetails($courseId);
        //$file_url = $this->getCertificateFromS3($certicateDetails->certificate_name);//storage_path('app') . $this->directory_seperator . $file_path . $this->directory_seperator . $certicateDetails->certificate_name;

        //$mail = Mail::to(\Auth::guard('osgcuser')->user()->email);
        $subject='Course Certificate';
        $message =  "<p> Hi " .\Auth::guard('osgcuser')->user()->first_name. ' ' . \Auth::guard('osgcuser')->user()->last_name . ',</p>';


        $message .='<p> Your course has been completed.</p>';
       // $mail->send(new CourseMail($courseDetails, 'mail.create',$subject,$message, $fileDet));
       $to =\Auth::guard('osgcuser')->user()->email;
       $model_name = 'Modules\Osgc\Models\OsgcUser';
       $aws_bucket_name = 'awsS3Bucket';
       $name = \Auth::guard('osgcuser')->user()->first_name. ' ' . \Auth::guard('osgcuser')->user()->last_name;

      $helper_variables = array(
        '{receiverFullName}'=> $name,
        '{courseName}'=>$courseDetails->title
        );
      $this->mailQueueRepository->prepareMailTemplate(
        "osgc_course_certificate",
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
        $to,
        $rec_candidate_id = 0,
        $rec_dynamic_email_text = null,
        $aws_bucket_name,
        $certificatePath
    );

        return true;
    }
    /**
     * Save course certification details
     * @return Response
     */
    public function storeCourseCertification($data)
    {
        return UserCourseCertification::create($data);
    }
    /**
     * get Course certification details
     * @return Response
     */
    public function getCourseCertificationByUser($courseId)
    {
        return UserCourseCertification::where('course_id',$courseId)->where('user_id',\Auth::guard('osgcuser')->user()->id)->orderBy('id','desc')->first();
    }
    /**
     * Find Last course details
     * @return Response
     */
    public function isLastCourseComplete($sectionId)
    {
        $sectionDet=$this->courseSectionModel->get()->find($sectionId);
        return $this->osgcUserCourseCompletionRepository->checkAllCourseAndTestCompleted($sectionDet->course_id,\Auth::guard('osgcuser')->user()->id);

    }


      /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function getCourseContent($id) {
        return $this->courseSectionModel->with(['courseContent','courseContent.courseContentType','CourseHeading'])->get()->find($id);
    }

    // public function getCertificateFromS3($fileName)
    // {
    //     $url ='';
    //     $file='osgc/pdf/'.$fileName;
    //     $disk = \Storage::disk('awsS3Bucket');
    //     if ($disk->exists($file)) {
    //     $data = $disk->getDriver()->getAdapter()->getClient()->getCommand('GetObject', [
    //         'Bucket'                     => \Config::get('filesystems.disks.awsS3Bucket.bucket'),
    //         'Key'                        => $file,
    //         'ResponseContentDisposition' => 'attachment;'
    //     ]);
    //     $request = $disk->getDriver()->getAdapter()->getClient()->createPresignedRequest($data, '+5 minutes');
    //     $url = (string)$request->getUri();
    //     }

    //     return $url;
    // }
    public function getActiveCourse()
    {
        return $this->courseModel->with(['CoursePrice','CoursePayment'])
        ->with(["CoursePayment" => function ($query) {
            return $query->where('user_id', \Auth::guard('osgcuser')->user()->id);
        }])

        ->where('active',1)->first();

    }
    public function getActiveAndOwnCourse()
    {
        $paidCourse=$this->courseModel->with(['CoursePrice'])

        ->whereHas("CoursePayment", function ($query){
            return $query->where('user_id', \Auth::guard('osgcuser')->user()->id);
        })
        ->get();
        $activeCourse=$this->courseModel->with(['CoursePrice','CoursePayment'])
        ->where('active',1)->get();
        $merged = $activeCourse->merge($paidCourse);

        return $result = $merged->all();

    }

    public function isheaderComplted($data)
    {
        $result=array();
        $headers=$data->ActiveCourseHeaders ?? [];//  dd($headers);
        if(count($headers) >0){
        foreach($headers as $row)
        {
            $headerId=$row->id;
            $sectionids=OsgcCourseContentSection::where('header_id',$headerId)->where('active',1)->pluck('id');
            $checkCouseCompletion=$this->userCourseCompletion->where('user_id',\Auth::guard('osgcuser')->user()->id)
           ->whereIn('course_section_id',$sectionids)
           ->where('status',1)->count();//dd($sectionids);
           if(count($sectionids) <= $checkCouseCompletion && count($sectionids) !=0)
           {
                 $result[$headerId]=true;
           }else{
                $result[$headerId]=false;
           }

        }
        }
        return $result;
    }
    public function getSignedUrl($file,$attachement='')
    {

        $url='';
        $disk = \Storage::disk('awsS3Bucket');
        if ($disk->exists($file)) {
            $command = $disk->getDriver()->getAdapter()->getClient()->getCommand('GetObject', [
                'Bucket'                     => \Config::get('filesystems.disks.awsS3Bucket.bucket'),
                'Key'                        => $file,

            ]);
            if($attachement)
            {
                $command['ResponseContentDisposition'] = 'attachment;';
            }
            $request = $disk->getDriver()->getAdapter()->getClient()->createPresignedRequest($command, '+120 minutes');
            $url = (string)$request->getUri();
        }
        return $url;

    }
}
