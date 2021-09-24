<?php

namespace Modules\Recruitment\Repositories;

use Modules\Recruitment\Models\RecOnboardingDocuments;
use Carbon;

class RecOnboardingDocumentsRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new RecOnboardingDocuments instance.
     *
     * @param  \App\Models\RecOnboardingDocuments $RecOnboardingDocuments
     */
    public function __construct(RecOnboardingDocuments $recOnboardingDocuments)
    {
        $this->model = $recOnboardingDocuments;
    }

    /**
     * Get lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id','document_name','created_at'])->orderBy('document_name', 'asc')->get();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        $data=$this->model->with('attachments')->find($id);
        return $this->prepareArray($data);
    }

    public function prepareArray($documents)
    {
        $arr=$datatable_rows=array();
        $arr['attachments']=array();
        $arr['id']=$documents['id'];
        $arr['document_name']=$documents['document_name'];
        foreach ($documents->attachments as $key => $each_attachment) {
            $arr1=array();
            $arr1['id']=$each_attachment->id;
            $arr1['file_type']=$each_attachment->file_type;
            $arr1['file_name']=$each_attachment->file_name;
            $arr1['file_id']=$each_attachment->id;
            $arr1['file_url']=\Storage::disk('s3-recruitment')->temporaryUrl($each_attachment->file_name, Carbon::now()->addMinutes(60));
           // $arr1[]=$arr3;
            array_push($arr['attachments'], $arr1);
        }
        return $arr;
     //$each_attachment['submitted_file_path'] = \Storage::disk('s3-recruitment')->temporaryUrl($val['attachment_file_name'], Carbon::now()->addMinutes(60));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }
}
