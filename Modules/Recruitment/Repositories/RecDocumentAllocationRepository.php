<?php

namespace Modules\Recruitment\Repositories;

use Modules\Recruitment\Models\RecDocumentAllocation;
use Modules\Recruitment\Models\RecProcessSteps;

class RecDocumentAllocationRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new TrackingProcessLookupRepository instance.
     *
     * @param  \App\Models\TrackingProcessLookup $trackingProcessLookup
     */
    public function __construct(RecProcessSteps $recProcessSteps)
    {
        $this->model = $recProcessSteps;
    }

    /**
     * Get lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id','step_order', 'step_name','notes','created_at','updated_at'])->orderBy('step_order','asc')->get();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        try{
            for ($i = 0; $i < count($data->get('document_name')); $i++) {
                $allocation_data = [
                        'customer_id' => $data->customer_id,
                        'document_name' => $data->document_name[$i],
                        'document_id' => $data->document_id[$i],
                        'order' => $data->order[$i],
                    ];
                    $this->model->updateOrCreate(array('id' => $data->id[$i]), $allocation_data);
            }
    } catch (\Exception $e) {
        return response()->json(array('success' => 'false', 'error' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()));
    }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return RecDocumentAllocation::where('id', $id)->delete();
    }

    /**
     * Get customer document allocation
     */
    public function singleCustomerDocuments($customerId) {
        $document = RecDocumentAllocation::where('customer_id', $customerId)->with('processTab')->get()->toArray();
        if (empty($document)) {
            $document = RecDocumentAllocation::where('customer_id', 0)->with('processTab')->get()->toArray();
        }
        $collection = collect($document);
        $grouped = $collection->groupBy('process_tab.display_name');
        $collapsed = $grouped->collapse();

        return $collapsed;
    }
}
