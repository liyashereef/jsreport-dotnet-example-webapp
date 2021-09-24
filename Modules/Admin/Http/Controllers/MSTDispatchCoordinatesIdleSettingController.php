<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Timetracker\Http\Requests\DispatchCoordinatesSettingsRequest;
use Modules\Timetracker\Repositories\DispatchCoordinatesIdleSettingRepository;

class MSTDispatchCoordinatesIdleSettingController extends Controller
{
    protected $repository;

    public function __construct(DispatchCoordinatesIdleSettingRepository $repository)
    {
        $this->repository = $repository;

    }

    /**
     * Display the Coordinate settings list page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('timetracker::admin.dispatch_coordinate_idle_settings.index');
    }

    /**
     * Get all idle settings as json /data table format
     * @return mixed
     * @throws \Exception
     */
    public function getIdleSettings()
    {
        return datatables()->of($this->repository->getAll())->toJson();
    }

    /**
     * Get the details of specific resource as json
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        return response()->json($this->repository->getById($id));
    }

    /**
     *Update the entity
     * @param DispatchCoordinatesSettingsRequest $request
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function update(DispatchCoordinatesSettingsRequest $request)
    {
        $id = $request->input('id');
        $data = $request->only(['idle_time']);
        return $this->repository->update($id, $data);

    }
}
