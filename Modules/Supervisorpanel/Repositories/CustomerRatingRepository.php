<?php

namespace Modules\Supervisorpanel\Repositories;

use Modules\Supervisorpanel\Models\CustomerRating;

class CustomerRatingRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new CustomerRatingRepository instance.
     *
     * @param  \App\Models\CustomerRating $customerRating
     */
    public function __construct(CustomerRating $customerRating)
    {
        $this->model = $customerRating;
    }

    /**
     * Store a newly created rating in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        return $this->model->create($data);
    }

}
