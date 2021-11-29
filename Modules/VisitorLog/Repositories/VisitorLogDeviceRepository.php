<?php

namespace Modules\VisitorLog\Repositories;

use Modules\VisitorLog\Transformers\VisitorLogDeviceResources;
use Modules\VisitorLog\Models\VisitorLogDevices;
use Modules\Admin\Repositories\VisitorLogTemplateRepository;
use Modules\Admin\Repositories\VisitorLogScreeningTemplateCustomerAllocationRepository;

class VisitorLogDeviceRepository
{

    protected $model;
    protected $visitorLogTemplateRepository;
    protected $visitorLogScreeningTemplateCustomerAllocationRepository;

    public function __construct(
        VisitorLogDevices $model,
        VisitorLogTemplateRepository $visitorLogTemplateRepository,
        VisitorLogScreeningTemplateCustomerAllocationRepository $visitorLogScreeningTemplateCustomerAllocationRepository
    ) {
        $this->model = $model;
        $this->visitorLogTemplateRepository = $visitorLogTemplateRepository;
        $this->screeningtemplateCustomerAllocationRepository = $visitorLogScreeningTemplateCustomerAllocationRepository;
    }

    /**
     * DB store
     * @return Response
     */

    public function store($inputs)
    {
        return $this->model->create($inputs);
    }

    /**
     * Update entry.
     * @param id, input array
     * @return Response
     */
    public function updateEntry($inputs)
    {
        return $this->model->updateOrCreate(['id' => $inputs['id']], $inputs);
    }

    /**
     * Delete entry.
     * @param id
     * @return Response
     */
    public function delete($id)
    {
        return $this->model->where('id', $id)->delete();
    }

    /**
     * Fetch all device info with customer and setting and templates.
     * @return Response
     */

    public function getAll()
    {
        return $this->model
            ->with([
                'activatedBy'=> function ($query) {
                    return $query->select('id', 'first_name', 'last_name');
                },
                'customer' => function ($query) {
                    return $query->select('id', 'project_number', 'client_name');
                },
                'visitorLogDeviceSettings' => function ($query) {
                    return $query->select('id', 'visitor_log_device_id', 'template_id', 'pin', 'camera_mode', 'scaner_camera_mode');
                },
                'visitorLogDeviceSettings.visitorLogTemplates' => function ($query) {
                    return $query->select('id', 'template_name');
                }
            ])
            ->get();
    }

    public function getByActivateCode($activation_code)
    {
        return $this->model->where('activation_code', $activation_code)
            // ->where('is_activated', 0)
            ->with('visitorLogDeviceSettings')
            ->first();
    }

    public function getByCodeAndDeviceId($inputs)
    {
        return $this->model
            ->where('activation_code', $inputs['activation_code'])
            ->where('device_id', $inputs['device_id'])
            ->where('is_activated', 0)
            ->first();
    }

    public function activateDevice($inputs)
    {
        return $this->model->updateOrCreate(['activation_code' => $inputs['activation_code']], $inputs);
    }

    public function getById($id)
    {
        return $this->model
            ->with([
                'customer' => function ($query) {
                    return $query->select('id', 'project_number', 'client_name');
                },
                'visitorLogDeviceSettings' => function ($query) {
                    // return $query->select('id','visitor_log_device_id','template_id','camera_mode','scaner_camera_mode');
                },
                'visitorLogDeviceSettings.visitorLogTemplates' => function ($query) {
                    return $query->select('id', 'template_name');
                },
                'visitorLogDeviceSettings.visitorLogTemplates.VisitorLogScreeningTemplateQuestion' => function ($query) {
                    return $query->select('id', 'visitor_log_screening_template_id', 'question', 'answer');
                }

            ])
            ->find($id);
    }

    public function fetchById($id)
    {
        return $this->model->with(['visitorLogDeviceSettings'])->find($id);
    }

    public function getByUID($uid)
    {
        return $this->model
            ->with([
                'visitorLogDeviceSettings'
            ])
            ->where('uid', $uid)->get();
    }

    public function setConfigData($id)
    {
        $device = $this->getById($id);
        $device->template = $this->visitorLogTemplateRepository->fetchTemplateDetails($device->visitorLogDeviceSettings->template_id);
        $filter['customerId'] = $device->customer_id;
        $device->screening = $this->screeningtemplateCustomerAllocationRepository->getTemplateByCustomerId($filter);
        return new VisitorLogDeviceResources($device);
    }
}
