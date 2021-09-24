<?php

namespace Modules\ProjectManagement\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Modules\ProjectManagement\Models\PmTaskUpdateInterval;
use Modules\ProjectManagement\Repositories\TaskFollowerConfigurationRepository;

class SettingsController extends Controller
{

    protected $helperService, $taskFollowerConfigurationRepository;

    public function __construct(
        HelperService $helperService,
        TaskFollowerConfigurationRepository $taskFollowerConfigurationRepository
    ) {
        $this->helperService = $helperService;
        $this->taskFollowerConfigurationRepository = $taskFollowerConfigurationRepository;
    }
    /**
     * Display Form for adding interval.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $followerConfigurationValue = 0;
        $followerConfiguration = $this->taskFollowerConfigurationRepository->getFirstData();
        if (!empty($followerConfiguration)) {
            $followerConfigurationValue = $followerConfiguration->value;
        }
        $interval = PmTaskUpdateInterval::pluck('interval')->toArray();
        return view('projectmanagement::admin.task-update-interval', compact('interval', 'followerConfigurationValue'));
    }

    /**
     * Store a newly created interval in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'interval.*' => 'required|between:1,3|distinct',
        ], [
            'interval.*.required' => 'Interval is required',
            'interval.*.between' => 'Interval should be maximum 3 digits.',
            'interval.*.distinct' => 'Interval values should be distinct.',
        ]);
        try {
            \DB::beginTransaction();
            PmTaskUpdateInterval::whereNull('deleted_at')->delete();
            $inputs = $request->all();
            foreach ($inputs['interval'] as $key => $each_interval) {
                $data['interval'] = $each_interval;
                PmTaskUpdateInterval::create($data);
            }

            //save task follower configurations
            $data = [
                'id' => 0,
                'value' => $request->get('task_followers_config_value'),
            ];
            $followerConfiguration = $this->taskFollowerConfigurationRepository->getFirstData();
            if (!empty($followerConfiguration)) {
                $data['id'] = $followerConfiguration->id;
            }
            $this->taskFollowerConfigurationRepository->saveOrCreate($data);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }

    }

}
