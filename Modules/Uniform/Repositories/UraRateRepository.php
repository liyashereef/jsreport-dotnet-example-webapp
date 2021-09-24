<?php

namespace Modules\Uniform\Repositories;

use Illuminate\Http\Request;
use Modules\Uniform\Models\UraRate;

class UraRateRepository
{
    protected $model;
    public function __construct(UraRate $uraRate)
    {
        $this->model = $uraRate;
    }

    public function store(Request $request)
    {
        $userId = auth()->user()->id;
        //Remove current rate
        $this->model->whereNull('deleted_at')->delete();
        //Create a new rate
        $this->model->create([
            'amount' => $request->input('amount'),
            'created_by' => $userId
        ]);
    }

    public function getCurrentRateObject(){
        return $this->model->first();
    }

    public function getCurrentRate()
    {
        $rec = $this->model->first();
        if ($rec != null) {
            return $rec->amount;
        }
        return 0.00;
    }

    public function getAll(){
        return $this->model::withTrashed()->orderBy('id','DESC')->get();
    }
}
