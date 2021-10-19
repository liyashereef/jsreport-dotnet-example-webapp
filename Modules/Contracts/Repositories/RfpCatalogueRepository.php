<?php

namespace Modules\Contracts\Repositories;

use Illuminate\Support\Facades\Auth;
use App\Repositories\AttachmentRepository;
use Modules\Contracts\Models\RfpCatalogue;
use \Carbon\Carbon;
class RfpCatalogueRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;
    protected $attachment_repository;

    /**
     * Create a new RfpCatalogueRepository instance.
     *
     * @param  Modules\Admin\Models\RfpCatalogue $rfpCatalogue
     */
    public function __construct(
        AttachmentRepository $attachment_repository,
        RfpCatalogue $rfpCatalogue
    )
    {
        $this->model = $rfpCatalogue;
        $this->attachment_repository = $attachment_repository;
    }


    /**
     * Get post order group list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model
            ->orderby('group','asc')
            ->pluck( 'group','id')->toArray();
    }

    /**
     * Get post order group list
     *
     * @param empty
     * @return array
     */
    public function getAll($created_user=null)
    {
        return $this->model
        ->when($created_user!=null,function($query)use($created_user){
            $query->where(['created_by'=>$created_user]);
        })
        ->where('reviewed_status',ACTIVE)
        ->orwherenull('reviewed_status')
            ->with('groupDetails','getCreatedby','getReviewedby')
            ->orderBy('created_at','desc')
            ->get();
    }

    /**
     * Display details of single post order group
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model
            ->with('groupDetails','getCreatedby','getReviewedby','attachmentDetails')
            ->find($id);
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
        return array(config('globals.rfp_catalogue_attachment_folder'));
    }

    /**
     * Function handle save of incident report along with attachments
     * @param $request
     * @return bool
     */
    public function storeRfpCatalogue($request)
    {
        $rfpCatalogueId = $request->id ?? null;
        $rfpCatalogue['id'] = $rfpCatalogueId;
        $rfpCatalogue['topic'] = $request->rfpCatalogueTopic;
        $rfpCatalogue['group_id'] = $request->rfpCatalogueGroup;
        $rfpCatalogue['description'] = $request->rfpCatalogueDescription;
        $rfpCatalogue['attachment_id'] = json_decode($request->attachment_list[0])->id;
        //return $this->model->updateOrCreate(array('id' => $rfpCatalogueId), $rfpCatalogue);
        $attachment_arr = $attachment_id_array = $all_attachment_arr = array();
        $all_attachment_arr = $request->all_attachments;

        $rfpCatalogueResult = $this->save($rfpCatalogue);
        $this->attachment_repository->setFilePersistant($rfpCatalogue['attachment_id']);

        //delete all other temporary files from the system
        if (isset($all_attachment_arr) && count($all_attachment_arr) > 0) {
            foreach ($all_attachment_arr as $removed_file_id) {
                $this->attachment_repository->removeTempFile('post-order', $removed_file_id);
            }
        }
        return $rfpCatalogueResult;
    }

    public function changeStatus($request)
    {
    return $this->model
        ->where('id',$request->id)
        ->update(['reviewed_status'=>$request->status,'reviewed_by'=>\Auth::user()->id,'reviewed_at'=>Carbon::now()]);
    }

    
     public function prepareRfpCatalogueArray($rfpCatalogueData)
    {
        $datatable_rows = array();
        foreach ($rfpCatalogueData as $key => $each_record) {
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
            $each_row["topic"]=$each_record->topic;
            $each_row["group"]=isset($each_record->groupDetails)?$each_record->groupDetails->group:'--';  
            $each_row["created_user_full_name"]=isset($each_record->getCreatedby->full_name)?$each_record->getCreatedby->full_name:'--';
            $each_row["reviewed_user_full_name"]=isset($each_record->getReviewedby->full_name)?$each_record->getReviewedby->full_name:'--';
            array_push($datatable_rows, $each_row);
        }
    return $datatable_rows;
    }
}
