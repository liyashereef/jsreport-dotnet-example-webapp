<?php

namespace Modules\Admin\Repositories;
use Modules\Admin\Models\DocumentCategory;



class DocumentCategoryRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new ExitTerminationReasonLookup instance.
     *
     * @param  \App\Models\ExitTerminationReasonLookup $positionLookup
     */
    public function __construct(DocumentCategory $model)
    {
        $this->model = $model;
    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        $document_name_details =  $this->model->select(['id','document_category','document_type_id','is_editable','created_by','created_at','updated_at'])->with(['documentType'])->get();
        return $this->prepareDataForDocumentNames($document_name_details);
    }

    /**
     *  Function to format the Document Name  record for datatable listing
     *
     *  @param array document name records
     *  @return array formatted document name records
     *
     */
    public function prepareDataForDocumentNames($document_name_details){
        $datatable_rows = array();
        foreach($document_name_details as $key => $each_list){

            $each_row["id"]              = $each_list->id;
            $each_row["document_category"]   = $each_list->document_category;
            $each_row["document_type"]   = $each_list->documentType['document_type'];
            $each_row["is_editable"]   = $each_list->is_editable;
            array_push($datatable_rows, $each_row);
        }


        return $datatable_rows;
    }


    public function get($id)
    {
        return $this->model->find($id);

    }

    public function getCategoryDetails($id)
    {
        return $this->categorymodel->orderBy('id')->where('document_type_id',$id)->get();
    }

    public function save($data)
    {

        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }
 }
