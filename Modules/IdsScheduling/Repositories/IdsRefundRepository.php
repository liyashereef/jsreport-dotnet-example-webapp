<?php

namespace Modules\IdsScheduling\Repositories;

use Modules\IdsScheduling\Models\IdsOnlineRefund;

class IdsRefundRepository
{
    protected $model;

    public function __construct(
        IdsOnlineRefund $model
    ) {
        $this->model = $model;
    }

    public function store($inputs)
    {
        return $this->model->create($inputs);
    }
    public function update($id, $inputs)
    {
        return $this->model->where('id', $id)->update($inputs);
    }
    public function getByOnlineRefundId($input)
    {
        return $this->model->with('idsTransactionHistory')->where('ids_online_refund_id', $input['refund_id'])
            ->where('id', $input['id'])->first();
    }
    public function getById($id)
    {
        return $this->model->with('idsTransactionHistory')->find($id);
    }
}
