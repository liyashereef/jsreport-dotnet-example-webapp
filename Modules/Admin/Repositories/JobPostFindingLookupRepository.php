<?php

namespace Modules\Admin\Repositories;

use Auth;
use Modules\Admin\Models\JobPostFindingLookup;

class JobPostFindingLookupRepository
{

    /**
     * @var JobPostFindingLookup
     */
    private $jobPostFindingLookup;

    /**
     * Create a new SecurityClearanceLookupRepository instance.
     *
     * @param JobPostFindingLookup $jobPostFindingLookup
     */
    public function __construct(JobPostFindingLookup $jobPostFindingLookup)
    {
        $this->jobPostFindingLookup = $jobPostFindingLookup;
    }

    /**
     * Get job finding lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->jobPostFindingLookup
            ->select(
                [
                    'id',
                    'job_post_finding',
                    'order_sequence',
                    'is_editable',
                    'created_at',
                    'updated_at'
                ])
            ->orderBy('order_sequence')
            ->get();
    }

    /**
     * Get job finding lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->jobPostFindingLookup
            ->orderBy('order_sequence')
            ->pluck('job_post_finding', 'id')
            ->toArray();
    }

    /**
     * Get job finding lookup list - with selected if deleted
     *
     * @param empty
     * @return array
     */
    public function getListWithSelected($selectedId = null)
    {
        $jobPostFindingCollection = JobPostFindingLookup::orderby('id', 'asc')
            ->get()->pluck('job_post_finding', 'id')->toArray();
        if(
            isset($selectedId)
            &&
            !isset($jobPostFindingCollection[$selectedId])
        ) {
            $selectedJobPostFinding = JobPostFindingLookup::where('id',$selectedId)
                ->withTrashed()
                ->get()
                ->first()
                ->toArray();
            $jobPostFindingCollection[$selectedJobPostFinding['id']] = $selectedJobPostFinding['job_post_finding'];
        }
        return $jobPostFindingCollection;
    }

    /**
     * Display details of single job finding
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        $singleRecord = $this->jobPostFindingLookup->find($id);
        return $singleRecord;
    }

    /**
     * Store a newly created job finding in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        $data['is_editable'] = true;
        if($data['id'] == null) {
            $data['created_by'] = Auth::user()->id;
        }
        $data['updated_by'] = Auth::user()->id;
        $lookup = $this->jobPostFindingLookup
            ->updateOrCreate(array('id' => $data['id']), $data);
        return $lookup;
    }

    /**
     * Remove the specified job finding from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        $lookup_delete = $this->jobPostFindingLookup->destroy($id);
        return $lookup_delete;
    }
 
}
