<?php


namespace Modules\Admin\Repositories;


use App\Services\HelperService;
use Modules\Admin\Models\RfpResponseTypeLookup;

class RfpResponseTypeLookupRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;
    protected $helperService;

    /**
     * Create a new RfpResponseTypeLookupRepository instance.
     *
     * @param RfpResponseTypeLookup $rfpResponseTypeLookup
     */
    public function __construct(
        RfpResponseTypeLookup $rfpResponseTypeLookup,
        HelperService $helperService
    )
    {
        $this->model = $rfpResponseTypeLookup;
        $this->helperService = $helperService;
    }


    /**
     * Get post order rfp_reponse_type list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderby('rfp_response_type','asc')
            ->pluck( 'rfp_response_type','id')->toArray();
    }

    /**
     * Get post order rfp_response_type list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->orderby('rfp_response_type','asc')->select(['id', 'rfp_response_type'])->get();
    }

    /**
     * Display details of single post order rfp_response_type
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created post order rfp_response_type in storage.
     *
     * @param  $data
     * @return object
     * @throws \Exception
     */
    public function save($data)
    {
        $data = $this->helperService->keySnakeCase($data);
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Remove the post order rfp_response_type from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }


}
