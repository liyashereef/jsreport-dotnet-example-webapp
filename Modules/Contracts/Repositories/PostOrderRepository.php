<?php

namespace Modules\Contracts\Repositories;

use App\Models\Attachment;
use Modules\Admin\Models\PostOrderGroup;
use Illuminate\Support\Facades\Auth;
use App\Repositories\AttachmentRepository;
use Modules\Contracts\Models\PostOrder;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use \Carbon\Carbon;

class PostOrderRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;
    protected $attachment_repository;

    /**
     * Create a new PostOrderRepository instance.
     *
     * @param  Modules\Admin\Models\PostOrder $postOrder
     */
    public function __construct(
        AttachmentRepository $attachment_repository,
        PostOrder $postOrder,
         CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository
    )
    {
        $this->model = $postOrder;
        $this->attachment_repository = $attachment_repository;
         $this->customerEmployeeAllocationRepository=$customerEmployeeAllocationRepository;
    }


    /**
     * Get post order group list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderby('group','asc')->pluck( 'group','id')->toArray();
    }

    /**
     * Get post order group list
     *
     * @param empty
     * @return array
     */
    public function getAll($allocated_customers=null,$created_user=null, $widgetRequest = false, $client_id=null)
    {
        $query =  $this->model
        ->when($allocated_customers!=null,function($query)use($allocated_customers){
            $query->whereIn('customer_id',$allocated_customers);
        })
        ->when($created_user!=null,function($query)use($created_user){
            $query->where('created_by',$created_user);
        })
            ->with('topicDetails','groupDetails','customerDetails','getCreatedby','getReviewedby')
            ->orderBy('created_at','desc');

            if ($widgetRequest) {
                $count = config('dashboard.post_order_row_limit');
                $query->limit($count);
            }
            if($client_id!=null){
                $query= $query->where('customer_id', $client_id);
            }
            return $query->get();
    }

    /**
     * Display details of single post order group
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->with('topicDetails','groupDetails','customerDetails','getCreatedby','getReviewedby','attachmentDetails')->find($id);
    }

    /**
     * Store a newly created post order group in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        if(!isset($data['id'])){
            $data['created_by'] = Auth::user()->id;
        }
        $data['updated_by'] = Auth::user()->id;
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Remove the post order group from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public static function getAttachmentPathArr($request)
    {
        return array(config('globals.post_order_attachment_folder'));
    }

    /**
     * Function handle save of incident report along with attachments
     * @param $request
     * @return bool
     */
    public function storePostOrder($request)
    {
        $postOrderId = $request->id ?? null;
        $postOrder['id'] = $postOrderId;
        $postOrder['topic_id'] = $request->postOrderTopic;
        $postOrder['group_id'] = $request->postOrderGroup;
        $postOrder['customer_id'] = $request->project;
        $postOrder['description'] = $request->postOrderDescription;
        $postOrder['attachment_id'] = json_decode($request->attachment_list[0])->id;
        //return $this->model->updateOrCreate(array('id' => $postOrderId), $postOrder);
        $attachment_arr = $attachment_id_array = $all_attachment_arr = array();
        $all_attachment_arr = $request->all_attachments;

        $postOrderResult = $this->save($postOrder);
        $this->attachment_repository->setFilePersistant($postOrder['attachment_id']);

        //delete all other temporary files from the system
        if (isset($all_attachment_arr) && count($all_attachment_arr) > 0) {
            foreach ($all_attachment_arr as $removed_file_id) {
                $this->attachment_repository->removeTempFile('post-order', $removed_file_id);
            }
        }
        return $postOrderResult;
    }

    public function changeStatus($request)
    {
    return $this->model->where('id',$request->id)->update(['reviewed_status'=>$request->status,'reviewed_by'=>\Auth::user()->id,'reviewed_at'=>Carbon::now()]);
    }

    public function preparePostOrderArray($postOrderData)
    {
        $allocated_customers=$this->customerEmployeeAllocationRepository->getAllocatedCustomers(\Auth::user());
        $datatable_rows = array();
        foreach ($postOrderData as $key => $each_record) {
            $each_row["id"] = $each_record->id;
            $each_row["reviewed_status_id"] = $each_record->reviewed_status;
            if(isset($each_record->reviewed_status) && $each_record->reviewed_status==0)
                 $each_row["reviewed_status"] = 'Rejected';
             else if($each_record->reviewed_status==1)
                $each_row["reviewed_status"] = 'Approved';
            else
            $each_row["reviewed_status"] = 'Pending';
            $each_row["attachment_id"] = $each_record->attachment_id;
            $each_row["description"] = $each_record->description;
            $each_row["topic"]=isset($each_record->topicDetails)?$each_record->topicDetails->topic:'--';
            $each_row["group"]=isset($each_record->groupDetails)?$each_record->groupDetails->group:'--';
            $each_row["client_name"] = isset($each_record->customerDetails)?$each_record->customerDetails->client_name:'--';
            $each_row["created_user_full_name"]=isset($each_record->getCreatedby->full_name)?$each_record->getCreatedby->full_name:'--';
            $each_row["reviewed_user_full_name"]=isset($each_record->getReviewedby->full_name)?$each_record->getReviewedby->full_name:'--';
            if(\Auth::user()->can('create_post_order'))
            {
              $each_row["show_edit_button"] = 1;
            }
            else if(\Auth::user()->can('create_allocated_post_order'))
            {
                if(in_array($each_record->customer_id,$allocated_customers) || ($each_record->created_by==\Auth::user()->id))
                {
                    $each_row["show_edit_button"] = 1;
                }
                else
                {
                    $each_row["show_edit_button"] = 0;
                }
            }
            array_push($datatable_rows, $each_row);
        }
    return $datatable_rows;
    }
}
