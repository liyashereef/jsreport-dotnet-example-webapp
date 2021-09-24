<?php

namespace Modules\UniformScheduling\Repositories;

use Modules\Hranalytics\Models\Candidate;
use Modules\UniformScheduling\Models\UniformMeasurements;

class UniformMeasurementsRepository
{

    protected $model;

    /**
     * IdsCustomQuestionRepository constructor.
     * @param IdsCustomQuestion $idsCustomQuestion
     */
    public function __construct(UniformMeasurements $model)
    {
        $this->model = $model;
    }

    public function store($inputs)
    {
        $candidate_id = $inputs["candidate_id"];

        $user_id = $inputs["user_id"];
        $uniform_scheduling_entry_id = $inputs["uniform_scheduling_entry_id"];
        $measurements = $inputs["input"];
        $entered = 0;
        $returnarray = ["success" => false];
        $result = null;
        if (isset($measurements) && count($measurements) > 0) {
            foreach ($measurements as $key => $value) {
                $entryarray = [
                    "candidate_id" => $candidate_id,
                    "user_id" => $user_id,
                    "uniform_scheduling_entry_id" => $uniform_scheduling_entry_id,
                    "uniform_scheduling_measurement_point_id" => $key,
                    "measurement_values" => $value
                ];

                if ($candidate_id > 0 && $candidate_id != null) {
                    $result = $this->model->updateOrCreate(
                        [
                            "candidate_id" => $candidate_id,
                            "uniform_scheduling_measurement_point_id" => $key
                        ],
                        $entryarray
                    );
                } elseif ($user_id != null && $uniform_scheduling_entry_id != null) {
                    $result = $this->model->updateOrCreate(
                        [
                            "user_id" => $user_id,
                            "uniform_scheduling_measurement_point_id" => $key,
                            "uniform_scheduling_entry_id" => $uniform_scheduling_entry_id
                        ],
                        $entryarray
                    );
                }
                if ($result) {
                    $entered = $entered + 1;
                }
            }
            if ($entered > 0 && $candidate_id > 0) {
                $candidate = Candidate::find($candidate_id);
                if ($inputs["gender"] != "" || $inputs["gender"] != null) {
                    $candidate->gender = $inputs["gender"];
                    $candidate->shipping_address = $inputs["shipping_address"];
                    $candidate->save();
                }
                $returnarray = ["success" => true];
            } else {
            }
        } else {
        }
        //return $this->model->create($inputs);
        return json_encode($returnarray, true);
    }

    public function updateEntryIds($inputs)
    {
        return $this->model
            ->where('uniform_scheduling_entry_id', $inputs['old_uniform_scheduling_entry_id'])
            ->update(["uniform_scheduling_entry_id" => $inputs['uniform_scheduling_entry_id']]);
    }

    public function deleteHipData($inputs){
        return $this->model
        ->where('uniform_scheduling_entry_id', $inputs['uniform_scheduling_entry_id'])
        ->where('uniform_scheduling_measurement_point_id', 6)
        ->delete();
    }
}
