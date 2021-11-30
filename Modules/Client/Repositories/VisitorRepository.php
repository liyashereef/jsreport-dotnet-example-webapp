<?php

namespace Modules\Client\Repositories;

use App\Services\HelperService;
use Illuminate\Http\Request;
use Modules\Client\Models\Visitor;
use Modules\Admin\Models\VisitorLogTypeLookup;

class VisitorRepository
{

    public function __construct()
    {
        $this->helper_service = new HelperService();
        $this->model = new Visitor();
    }

    /**
     * Store visitor details for data sync
     * create and update based on uid.
     * @param json
     */
    public function store(Request $request)
    {
        $payloads = $request->input('payload');

        if ($payloads) {
            foreach ($payloads as $payload) {
                $input = json_decode($payload, true);
                $input['created_at'] = \Carbon\Carbon::parse($input['createdAt'])->format('Y-m-d H:i:s');

                if (!empty($input['updatedAt'])) {
                    $input['updated_at'] = \Carbon\Carbon::parse($input['updatedAt'])->format('Y-m-d H:i:s');
                } else {
                    $input['updated_at'] = null;
                }
                if (!empty($input['deletedAt'])) {
                    $input['deleted_at'] = \Carbon\Carbon::parse($input['deletedAt'])->format('Y-m-d H:i:s');
                }

                if ($input['visitorType']) {
                    $visitorType = json_decode($input['visitorType'], true);
                    $input['visitorTypeId'] = $visitorType['id'];
                    unset($input['visitorType']);
                } else {
                    $typeId = VisitorLogTypeLookup::where('type', 'Employee')
                        ->select('id')
                        ->first();

                    $input['visitorTypeId'] = $typeId->id;
                }

                unset($input['createdAt']);
                unset($input['updatedAt']);
                unset($input['deletedAt']);
                unset($input['id']);
                // \Log::channel('customlog')->info('Visitor create Inputs'.json_encode($input));
                if ($input['uid']) {
                    $isExists = $this->getByUID($input['uid']);
                    // \Log::channel('customlog')->info('Visitor store data of isExists'.json_encode($isExists));
                    if (sizeof($isExists) == 0) {
                        $this->model->create($input);
                    } else {
                        \Log::channel('customlog')->info('Visitor store update' . json_encode($payloads));
                        $this->model->where('uid', $input['uid'])->update($input);
                    }
                    // \Log::channel('customlog')->info('Visitor store create'.json_encode($input));
                }

                $input = [];
            }
        }
        return true;
    }

    /**
     * fetch visitor data by uid for checking duplication.
     * @param id
     */
    public function getByUID($uid)
    {
        return $this->model->where('uid', $uid)->withTrashed()->get();
    }

    public function getByFilters($inputs)
    {
        $query = $this->model;

        $query = $query->when(isset($inputs['x-ci']), function ($query) use ($inputs) {
            $query->where('customerId', $inputs['x-ci']);
        });
        $query = $query->when(isset($inputs['ts']) && $inputs['ts'] !== 'null' && !empty($inputs['ts']), function ($query) use ($inputs) {
            $query->where('updated_at', '>=', $inputs['ts']);
        });
        $query = $query->with([
            'visitorType' => function ($query) {
                $query->select('id', 'type as name');
            },

            'visitorStatus' => function ($query) {
                $query->select('id', 'name', 'is_authorised', 'is_default');
            }
        ]);
        return $query->withTrashed()->get();

        // return Visitor::where('customerId', '=', $customerId)
        // ->select('customerId','uid','barCode','firstName','lastName','email','phone','created_at as createdAt',
        // 'updated_at as updatedAt','deleted_at as deletedAt','visitorTypeId','avatar')
        // ->with(['visitorType'=> function($query){
        //     $query->select('id','type');
        // }])->get();
    }

    public function getAllMyVisitors($inputs, $client_id = null)
    {
        $data = Visitor::whereIn('customerId', $inputs['customer_id'])
            ->select(
                'id',
                'customerId',
                'uid',
                'barCode',
                'firstName',
                'lastName',
                'email',
                'phone',
                'created_at as createdAt',
                'updated_at as updatedAt',
                'deleted_at as deletedAt',
                'visitorTypeId',
                'avatar',
                'notes',
                'visitorStatusId'
            )
            ->orderBy('id', 'DESC')
            ->with([
                'visitorType' => function ($query) {
                    $query->select('id', 'type');
                },
                'customer' => function ($query) {
                    $query->select('id', 'project_number', 'client_name');
                },
                'visitorStatus' => function ($query) {
                    $query->select('id', 'name', 'is_authorised', 'is_default');
                }
            ]);
        if ($client_id != null) {
            $data = $data->whereHas('customer', function ($query) use ($client_id) {
                return $query->where('id', '=', $client_id);
            });
        }
        $data->get();
        return $data;
    }

    public function storeFromWeb($inputs)
    {
        return $this->model->updateOrCreate(array('id' => $inputs['id']), $inputs);
    }

    public function delete($id)
    {
        return $this->model->where('id', $id)->delete();
    }

    public function getById($id)
    {
        return $this->model->find($id);
    }

    public function updateCount($inputs)
    {
        return Visitor::where('customerId', $inputs['x-ci'])
        ->when(isset($inputs) && !empty($inputs['visitor']), function ($q) use ($inputs) {
            return $q->where('updated_at','>=', $inputs['visitor'])
            ->OrWhere('deleted_at','>=', $inputs['visitor'])->withTrashed();
        })->count();
    }
}
