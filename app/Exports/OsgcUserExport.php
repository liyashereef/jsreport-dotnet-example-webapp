<?php

namespace App\Exports;

use Modules\Osgc\Models\OsgcUser;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Modules\Osgc\Models\UserCourseCompletion;
use Modules\Osgc\Models\OsgcCourseContentSection;
use Modules\Osgc\Models\OsgcCourseContentHeader;
use Modules\Osgc\Models\AllocatedUserCourses;
use Carbon\Carbon;
class OsgcUserExport implements WithColumnFormatting, FromArray, WithHeadings, ShouldAutoSize, WithEvents, WithStrictNullComparison
{

    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        $data= OsgcUser::with('userSuccessPayments','userSuccessPayments.osgcCourses')
        ->get();
        return $this->getUserData($data);
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Registered On',
            'Veteran Status',
            'Aboriginal descent Status',
            'Referral',
            'Payment Status',
            'Amount',
            'Course Name',
            'Last Module completed',
            '% Completed',
            'Days Tracker',
            'Registered Month',
            'Status',
            
        ];
        
    }

    public function getUserData($data)
    {
        $datatable_rows = array();
        $each_row = [];
        
        foreach ($data as $key => $each_record) {   
            
            $each_row["name"] = $each_record->first_name.' '.$each_record->last_name;
            $each_row["email"] = $each_record->email;
            $each_row["created_at"] = \Carbon::parse($each_record->created_at)->format('Y-m-d h:i:s');
            
            if($each_record->is_veteran==1)
            {
                $each_row["is_veteran"] = 'Yes';

            }else{
                $each_row["is_veteran"] = 'No';
            }
            if($each_record->indian_status==1)
            {
                $each_row["indian_status"] = 'Yes';

            }else{
                $each_row["indian_status"] = 'No';
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
                    if($each_payment->status==1)
                    {
                        $each_row["status"] = 'Paid';

                    }else{
                        $each_row["status"] = 'Failed';
                    }
                    $each_row["amount"] = $each_payment->amount;
                    $each_row["course_title"] = $each_payment->osgcCourses->title;
                    if($each_payment->course_id)
                    {
                        // $totalheaders=OsgcCourseContentHeader::where('course_id',$each_record->course_id)->where('active',1)->pluck('id');
                        // $sectionids=OsgcCourseContentSection::whereIn('header_id',$totalheaders)->where('active',1)
                        // ->where('completion_mandatory',1)
                        // ->pluck('id');
                        // $checkCouseCompletion=UserCourseCompletion::whereIn('course_section_id',$sectionids)
                        // ->where('user_id',$each_record->user_id)
                        // ->where('status',1)->count();//dd($sectionids .'/'.$checkCouseCompletion);
                        // if(count($sectionids) == $checkCouseCompletion)
                        // {
                    
                        //     $each_row["course_completion"]='Yes';
                        // }else{
                        //     $each_row["course_completion"]='No';
                        // }
                        
                        $userCourse=AllocatedUserCourses::with('courseSection')->where('course_id',$each_payment->course_id)->where('user_id',$each_payment->user_id)->first();
                        $each_row["last_course_completion"]=$userCourse->courseSection->name ?? '';
                        $totalCourseContentIds=OsgcCourseContentSection::where('course_id',$each_payment->course_id)->where('active',1)->where('completion_mandatory',1)->pluck('id');
                        if(count($totalCourseContentIds) ==0)
                        {
                            $totalCourseContentIds=OsgcCourseContentSection::where('course_id',$each_payment->course_id)->where('active',1)->pluck('id');
                        
                        }
                        $totalCourseContent=count($totalCourseContentIds);
                        $courseWatched=UserCourseCompletion::whereIn('course_section_id',$totalCourseContentIds)
                        ->where('user_id',$each_payment->user_id)
                        ->where('status',1)->count();
                        if($courseWatched !=0)
                        {
                            $percentageCount=round(($courseWatched/$totalCourseContent)*100);
                        }else{
                            $percentageCount=0;
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
                        $diff = $paiddate->diffInDays($now);
                        $each_row["days_tracker"]=$diff;
                        
                    }else{
                        
                        $each_row["last_course_completion"]='';
                        $each_row["percentage_completion"]='';
                        $each_row["days_tracker"]='';
                    }
                    $each_row["paid_date"] = \Carbon::parse($each_payment->paid_date)->format('M Y');
                    if($each_record->active ==1)
                    {
                        $each_row["active"] = 'Active';
        
                    }else{
                        $each_row["active"] = 'Inactive';
                    }
                    array_push($datatable_rows, $each_row);
                }
            }else{
                $each_row["status"] = 'Unpaid';
                $each_row["amount"] = '';
                $each_row["paid_date"] = '';
                $each_row["course_title"] = '';
                $each_row["last_course_completion"]='';
                $each_row["percentage_completion"]='';
                $each_row["days_tracker"]='';
                if($each_record->active ==1)
                {
                    $each_row["active"] = 'Active';
    
                }else{
                    $each_row["active"] = 'Inactive';
                }
                array_push($datatable_rows, $each_row);
            }
            
           
            
        }
        return $datatable_rows;
    }
    


    /**
     * @return array
     */

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:AH1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12)->setBold(true);
            },
        ];
    }

    public function columnFormats(): array
    {
        return [];
    }
}
