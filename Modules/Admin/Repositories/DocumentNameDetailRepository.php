<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\DocumentNameDetail;
use Modules\Admin\Models\DocumentCategory;
use Modules\Admin\Models\OtherCategoryLookup;
use Modules\Admin\Models\OtherCategoryName;
use Modules\Admin\Models\DocumentAccessPermission;



class DocumentNameDetailRepository
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
    public function __construct(DocumentNameDetail $model ,DocumentCategory $categoryModel,
                                OtherCategoryLookup $otherCategoryModel ,OtherCategoryName $otherCategoryNameModel)
    {
        $this->model = $model;
        $this->categorymodel = $categoryModel;
        $this->othercategorymodel = $otherCategoryModel;
        $this->othercategorynamesmodel = $otherCategoryNameModel;
    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        $document_name_details =  $this->model->select(['id','name','document_type_id','answer_type','document_category_id','is_editable','created_by','created_at','updated_at'])->with(['documentTypes','documentCategories','answerable'])->get();
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
            $each_row["document_name"]   = $each_list->name;
            $each_row["document_type"]   = $each_list->documentTypes['document_type'];
            $each_row["is_editable"]   = $each_list->is_editable;
            if($each_list->document_type_id == OTHER){
                $each_row["document_category"] = $each_list->answerable['category_name'];
            }else{
                $each_row["document_category"] = $each_list->documentCategories['document_category'];
            }

            array_push($datatable_rows, $each_row);
        }


        return $datatable_rows;
    }


    public function getNames($id)
    {
        return $this->model->with(['AuthorizedAccessDetails','AuthorizedAccessDetails.AuthorisedAccessName'])->find($id);

    }

    public function getCategoryDetails($id)
    {
        $categorynotinclude = [1,2];
        if($id == OTHER){
            return $this->othercategorymodel->orderBy('category_name','asc')->where('document_type_id',$id)->get();
        }else{
            return $this->categorymodel->orderBy('document_category','asc')->where('document_type_id',$id)->whereNotIn('id',$categorynotinclude)->get();
        }
    }

    public function getOtherCategoryNames($id)
    {
        return $this->othercategorynamesmodel->orderBy('name','asc')->where('other_category_lookup_id',$id)->get();
    }

    public function save($data)
    {
        $document_name=$this->model->updateOrCreate(array('id' => $data['id']), $data);
        DocumentAccessPermission::where('document_name_id',$document_name->id)->delete();
        if(isset($data['authorized_access']))
        {
            $access_arr=array();
            foreach ($data['authorized_access'] as $key => $row) {

                DocumentAccessPermission::create(['document_name_id' => $document_name->id,
                                'access_id' => $row]);
            }

        }
    }

    /**
     * Function to get answertype details
     * @param $id
     * @return array
     */

    public function getCategoryModels($type_id){
        if($type_id == OTHER) {
            $modelName = 'Modules\Admin\Models\OtherCategoryLookup';
        } else {
            $modelName = 'Modules\Admin\Models\DocumentCategory';
        }
        return $modelName;

    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }
 }
