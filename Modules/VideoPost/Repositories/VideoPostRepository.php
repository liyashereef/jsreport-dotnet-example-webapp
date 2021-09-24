<?php

namespace Modules\VideoPost\Repositories;
use Modules\Admin\Repositories\CustomerRepository;
use App\Repositories\AttachmentRepository;
use App\Services\HelperService;
use Auth;
use Modules\Admin\Models\Customer;
use Modules\VideoPost\Models\VideoPost;
use App\Helpers\S3HelperService;
use Modules\VideoPost\Models\VideoPostUserViewDetails;

class VideoPostRepository
{


    public function __construct(VideoPostUserViewDetails $videoPostUserViewDetails, S3HelperService $s3HelperService,CustomerRepository $customerRepository, HelperService $helperService, AttachmentRepository $attachmentRepository, VideoPost $videoPost)
    {
        $this->customerRepository = $customerRepository;
        $this->helperService = $helperService;
        $this->attachmentRepository = $attachmentRepository;
        $this->videopost = $videoPost;
        $this->s3HelperService = $s3HelperService;
        $this->videoPostUserViewDetails = $videoPostUserViewDetails;
    }


    public function save($request){
        $videoPath=$request->path ?? '';
        $logged_in_user = \Auth::id();
        $data = [
            'video_path'=>$videoPath,
            'type' => $request->type,
            'created_by' => $logged_in_user,
            'customer_id' => $request->customer_id,
            'file_name' => $request->file_name,
            'file_type' => $request->file_type,
            'description' => $request->description,
            'video_uploaded_date' => $request->uploaded_date
        ];
        $videoPostStore = $this->videopost->updateOrCreate(array('id' => $request->get('id')), $data);
        return $videoPostStore;

    }

    public static function getAttachmentPathArr($request)
    {
        if ($request->customer_id) {
            return array(config('globals.video_post'), $request->customer_id);
        }
    }

    public function getVideoPostSummarylist($customerId=null)
    {
        $customerKeyIds = array_keys($this->getCustomerList());
        if($customerId != null){
            $result =  $this->videopost->with(['customer','createdBy'])->whereIn('customer_id',$customerKeyIds)->where('customer_id',$customerId)->get();
        }else{
            $result =  $this->videopost->with(['customer','createdBy'])->whereIn('customer_id',$customerKeyIds)->get();
        }

        return $this->prepareDataForKeyDetailList($result);
    }

    public function getCustomerList()
    {
        if (\Auth::user()->can('view_all_customers_in_video_post')) {
            $project_list = $this->customerRepository->getProjectsDropdownList('all');
        } else if (\Auth::user()->can('view_allocated_customers_in_video_post')) {
            $project_list = $this->customerRepository->getProjectsDropdownList('allocated');
        } else {
            $project_list = [];
        }
        return $project_list;
    }

     /**
     * Prepare datatable elements as array.
     * @param  $result
     * @return array
     */
    public function prepareDataForKeyDetailList($result)
    {
        $datatable_rows = array();
        foreach ($result as $key => $each_list) {
            $each_row["id"] = isset($each_list->id) ? $each_list->id : "--";
            $each_row["project_number"] = isset($each_list->customer->project_number) ? $each_list->customer->project_number : "--";
            $each_row["client_name"] = isset($each_list->customer->client_name) ? $each_list->customer->client_name : "--";
            $each_row["video_path"] = isset($each_list->video_path) ? $each_list->video_path : "--";
            $each_row["file_name"] = isset($each_list->file_name) ? $each_list->file_name : "--";
            $each_row["file_type"] = isset($each_list->file_type) ? $each_list->file_type : "--";
            $each_row["description"] = isset($each_list->description) ? $each_list->description : "--";
            $each_row["uploaded_date"] = isset($each_list->video_uploaded_date) ?  date("Y-m-d", strtotime($each_list->video_uploaded_date)) : "--";
            $each_row["uploaded_by"] = isset($each_list->createdBy) ? $each_list->createdBy->first_name . ' ' . $each_list->createdBy->last_name : "--";
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }
    /**
     * Remove the specified training category from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
            $file = $this->videopost->where('id',$id)->first();
            $fileloc = $file->video_path;
            S3HelperService::trashFile("awsS3Bucket", $fileloc);
            $deleteFile = $this->videopost->destroy($id);
            return $deleteFile;
    }

    public function storeViewedUserDetails($userid,$videoPath){
        $videoPostId=$this->videopost->where('video_path','=',$videoPath)->select('id')->get();
        $viewedUser = $userid;
        $data = ['video_post_id'=>$videoPostId[0]['id'], 'viewed_user_id' => $viewedUser, 'status' => MOBILE];
        $videoPostStore = VideoPostUserViewDetails::Create($data);
        return $videoPostStore;
    }


}
