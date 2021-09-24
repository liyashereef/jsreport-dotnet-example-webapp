<?php

namespace Modules\Admin\Repositories;
use Modules\Admin\Models\OtherCategoryName;
use Modules\Admin\Models\OtherCategoryLookup;


class OtherCategoryNamesRepository
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
    public function __construct(OtherCategoryName $otherCategoryname,OtherCategoryLookup $otherCategorylookup)
    {
        $this->otherCategoryname = $otherCategoryname;
        $this->otherCategorylookup = $otherCategorylookup;
        
    }

     /**
     *  Function to get all records for datatable listing
     * 
     *  @param array 
     *  @return array 
     * 
     */

    public function getAll()
    {
        $other_category_name_details =  $this->otherCategoryname->select(['id','name','document_type_id','other_category_lookup_id'])->with(['documentTypes','otherCategory'])->get();
       
       
        return $this->prepareDataForDocumentNames($other_category_name_details);
    }

    /**
     *  Function to format the Other Category Name   record for datatable listing
     * 
     *  @param array $other_category_name_details
     *  @return array 
     * 
     */

    public function prepareDataForDocumentNames($other_category_name_details)
    {
        $datatable_rows = array();
        foreach($other_category_name_details as $key => $each_list){
           
            $each_row["id"]              = $each_list->id;
            $each_row["name"]            = $each_list->name;
            $each_row["document_type"]   = $each_list->documentTypes['document_type'];
            $each_row["category_name"]   = $each_list->otherCategory['category_name'];
            array_push($datatable_rows, $each_row);

    }
    
    return $datatable_rows;
         }

          /**
     * Display details of single resource
     *
     * @param $id
     * @return json
     */
   public function getNames($id)
   {
      return $this->otherCategoryname->find($id);

}
 /**
     * Storing  resource
     *
     * @param $id
     * @return json
     */

    public function save($data)
    {
        return $this->otherCategoryname->updateOrCreate(array('id' => $data['id']), $data);
    }
 /**
     * Deleting details of single resource
     *
     * @param $id
     * @return json
     */
    public function delete($id)
    {
        return $this->otherCategoryname->destroy($id);
    }
    
    public function getCategoryDetails($id)
    {
        return $this->otherCategorylookup->orderBy('category_name','asc')->where('document_type_id',$id)->get();
       
    }
}


