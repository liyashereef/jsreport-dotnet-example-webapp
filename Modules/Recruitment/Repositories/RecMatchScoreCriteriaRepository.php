<?php

namespace Modules\Recruitment\Repositories;

use Modules\Recruitment\Models\RecCandidateJob;
use Modules\Recruitment\Models\RecScoreCriteria;
use Modules\Recruitment\Models\RecMatchScoreCriteria;
use Modules\Recruitment\Models\RecCandidateMatchScore;
use Modules\Recruitment\Models\RecMatchScoreCriteriaMapping;
use Modules\Recruitment\Models\RecCandidateScreeningPersonalityScore;
use Modules\Recruitment\Models\RecCandidateJobDetails;
use DateTime;
use Carbon;
use App\Services\LocationService;

class RecMatchScoreCriteriaRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model, $recScoreCriteriaModel;

    /**
     * Create a new RecMatchScoreCriteria instance.
     *
     * @param  \App\Models\RecMatchScoreCriteria $recMatchScoreCriteria
     */
    public function __construct(
        RecMatchScoreCriteria $recMatchScoreCriteria,
        RecScoreCriteria $recScoreCriteria,
        LocationService $locationService
    ) {
        $this->model = $recMatchScoreCriteria;
        $this->recScoreCriteriaModel = $recScoreCriteria;
        $this->locationService = $locationService;
    }


    /**
     * Get Experience  lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'criteria_id', 'weight', 'created_at', 'updated_at'])->with('scoreCriteriaLookup')->get();
    }

    /**
     * Get Experience lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model
            //->orderBy('criteria_name', 'asc')
            ->pluck('criteria_id', 'id')->toArray();
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
        $already_saved_data = $this->model->pluck('id')->toArray();
        $diff_arr = array_diff($already_saved_data, $data['step-id']);
        $this->model->whereIn('id', $diff_arr)->delete();
        foreach ($data['position'] as $key => $i) {
            $record = [
                'criteria_id' => $data['criteria_name'][$i],
                'weight' => $data['weight'][$i],
                'type_id' => $data['type_id'][$i],

            ];
            $this->model->updateOrCreate(array('id' => $data['step-id'][$i]), $record);
        }
        return true;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function setScore($job)
    {
        $candidatesList = RecCandidateJob::where('status', 'Applied')->with('candidate.availability', 'candidate.guardingExperience', 'candidate.miscellaneous')->get();
        $criterias = RecMatchScoreCriteria::whereHas('criteriaMapping')->get();
        foreach ($candidatesList as $key => $eachCandidate) {
            $jobDetails = array();
            $total_score = 0;
            foreach ($criterias as $key => $eachCriteria) {
                $candidateMatchScore = array();
                $jobDetails['candidate_id'] = $eachCandidate->candidate_id;
                $jobDetails['job_id'] = $job->id;
                $candidateMatchScore['candidate_id'] = $eachCandidate->candidate_id;
                $candidateMatchScore['job_id'] = $job->id;
                $candidateMatchScore['criteria_id'] = $eachCriteria->criteria_id;
                $candidateMatchScore['criteria_weight'] = $eachCriteria->weight;
                switch ($eachCriteria->criteria_id) {
                    case 1:  //Wage Premium
                        $comparingValue = (abs($eachCandidate->proposed_wage - $job->wage) / $eachCandidate->proposed_wage) * 100;
                        $total_score = $this->calcualtions($comparingValue, $eachCriteria->criteriaMapping, $candidateMatchScore, $total_score);
                        break;

                    case 2: //Travel Time
                        $inputs['destinations'] = [];
                        $orginPincodeLatLang = $this->locationService->getLatLongByAddress($eachCandidate->candidate->postal_code);
                        $inputs['origins'][0] = $orginPincodeLatLang;
                        $destinyPincodeLatLang = $this->locationService->getLatLongByAddress($job->customer->postal_code);
                        $destination = [];
                        if ($destinyPincodeLatLang != null) {
                            $destination['lat'] = $destinyPincodeLatLang['lat'];
                            $destination['long'] = $destinyPincodeLatLang['long'];
                            array_push($inputs['destinations'], $destination);
                        }
                        if (!empty($inputs['destinations']) && !empty($inputs['origins'])) {
                            //Finding distance and time.
                            $distances = $this->locationService->getDrivingDistance($inputs);
                        }

                        $matrixData = $distances['distanceMatrix']->rows[0]->elements[0];
                        if (!empty($matrixData) && $matrixData->status == 'OK') {
                            $distance = $matrixData->distance->text;
                            $duration = $matrixData->duration->text;
                        }
                        $jobDetails['estimated_travel_time'] = $duration;
                        $jobDetails['estimate_distance'] =  $distance;
                        $total_score = $this->calcualtions((int) filter_var($duration, FILTER_SANITIZE_NUMBER_INT), $eachCriteria->criteriaMapping, $candidateMatchScore, $total_score);
                        break;

                    case 3: //Shift match
                        $total_score = $this->shiftMatches($eachCandidate->candidate->availability->days_required, $job->days_required, $candidateMatchScore, $eachCriteria, $total_score);

                        break;
                    case 4: //Schedule match
                        $total_score = $this->shiftMatches($eachCandidate->candidate->availability->shifts, $job->shifts, $candidateMatchScore, $eachCriteria, $total_score);

                        break;
                    case 5: //HPW
                        $comparingValue = (abs($eachCandidate->prefered_hours_per_week - $job->hours_per_week) / $eachCandidate->prefered_hours_per_week) * 100;
                        $total_score = $this->calcualtions($comparingValue, $eachCriteria->criteriaMapping, $candidateMatchScore, $total_score);

                        break;
                    case 6: // Experience
                        $comparingValue = (abs($eachCandidate->candidate->guardingExperience->years_security_experience - $job->total_experience) / $eachCandidate->candidate->guardingExperience->years_security_experience) * 100;
                        $total_score = $this->calcualtions($comparingValue, $eachCriteria->criteriaMapping, $candidateMatchScore, $total_score);

                        break;
                    case 7: //CAse Study
                        $comparingValue = $eachCandidate->average_score;
                        $total_score = $this->calcualtions($comparingValue, $eachCriteria->criteriaMapping, $candidateMatchScore, $total_score);

                        break;
                    case 8: //pERSONALITY
                        $scoreRating = RecCandidateScreeningPersonalityScore::where('order', 1)->where('candidate_id', $eachCandidate->candidate_id)->first();
                        $total_score = $this->calcualtions($scoreRating->score, $eachCriteria->criteriaMapping, $candidateMatchScore, $total_score, true);


                        break;
                    case 9: //CAREER
                        $total_score = $this->calcualtions((int) filter_var($eachCandidate->candidate->miscellaneous->career_interest, FILTER_SANITIZE_NUMBER_INT), $eachCriteria->criteriaMapping, $candidateMatchScore, $total_score);

                        break;
                    case 10: //KNowledge of CGL
                        $total_score = $this->calcualtions($eachCandidate->brand_awareness_id, $eachCriteria->criteriaMapping, $candidateMatchScore, $total_score);

                        break;
                    case 11: //Age
                        $total_score = $this->calcualtions(Carbon::parse($eachCandidate->candidate->dob)->age, $eachCriteria->criteriaMapping, $candidateMatchScore, $total_score);
                        break;
                    default:
                        break;
                }
            }
            $jobDetails['recruiter_id'] = $job->hr_rep_id;
            $jobDetails['rec_match_score'] = $total_score;
            RecCandidateJobDetails::updateOrCreate(array('candidate_id' => $jobDetails['candidate_id'], 'job_id' => $jobDetails['job_id']), $jobDetails);
        }
    }
    /**
     * Display details of single resource
     *
     * @param $comparingValue - value which is compared
     * @param $maping - Criteria mapping object
     * @param $candidateMatchScore - candidateMatchScore array
     * @param $exactMatchCheck - if true,$comparing value is compared with mapping for exact match,if false,it check whether$ comparing value exists in between a range
     * @return object
     */

    private function calcualtions($comparingValue, $maping, $candidateMatchScore, $total_score, $exactMatchCheck = false)
    {

        for ($i = 0; $i < count($maping); $i++) {
            if ($exactMatchCheck) {
                if ($comparingValue == $maping[$i]->limit) {
                    $range = $maping[$i];
                }
            } else {
                if (($i) == count($maping) - 1 || ($comparingValue >= $maping[$i]->limit && $comparingValue <= $maping[$i + 1]->limit)) {
                    $range = $maping[$i];
                }
            }
        }
        $candidateMatchScore['premium'] = $comparingValue;
        $candidateMatchScore['mapping_value'] = $range->score;
        $candidateMatchScore['weighted_score'] = $range->score * ($candidateMatchScore['criteria_weight'] / 100);
        $total_score = $total_score + $candidateMatchScore['weighted_score'];
        $matchscore = RecCandidateMatchScore::updateOrCreate(array('candidate_id' => $candidateMatchScore['candidate_id'], 'job_id' => $candidateMatchScore['job_id'], 'criteria_id' => $candidateMatchScore['criteria_id']), $candidateMatchScore);
        return $total_score;
    }

    private function shiftMatches($candidate_availability, $job_availability, $candidateMatchScore, $eachCriteria, $total_score)
    {
        $candidateAvailability = str_replace(array('[', ']'), '', explode(',', $candidate_availability));
        $jobAvailability = str_replace(array('[', ']'), '', explode(',', $job_availability));
        $isArrEqual = array_diff($candidateAvailability, $jobAvailability) === array_diff($jobAvailability, $candidateAvailability);
        $shiftScore = RecMatchScoreCriteriaMapping::where('criteria', $eachCriteria->criteria_id)->pluck('score', 'limit')->toArray();
        $maping = $eachCriteria->criteriaMapping;
        $candidateMatchScore['mapping_value'] = $isArrEqual ? $shiftScore[1] : $shiftScore[0];
        $candidateMatchScore['premium'] = $isArrEqual ? 'Yes' : 'No';
        $candidateMatchScore['weighted_score'] = $candidateMatchScore['mapping_value'] * ($candidateMatchScore['criteria_weight'] / 100);
        $total_score = $total_score + $candidateMatchScore['weighted_score'];
        RecCandidateMatchScore::updateOrCreate(array('candidate_id' => $candidateMatchScore['candidate_id'], 'job_id' => $candidateMatchScore['job_id'], 'criteria_id' => $candidateMatchScore['criteria_id']), $candidateMatchScore);
        ;
        return $total_score;
    }

    /**
     * Get score criteria list
     *
     * @param empty
     * @return array
     */
    public function getScoreCriteriaAll()
    {
        $scoreCriteria = $this->recScoreCriteriaModel->select(['id', 'criteria_name', 'type_id'])->get();
        return $this->prepareDataForRoomList($scoreCriteria);
    }

    /**
     * Prepare datatable elements as array.
     * @param $scoreCriteria
     * @return array
     */
    public function prepareDataForRoomList($scoreCriteria)
    {
        $match_type = config('globals.match_type');
        $datatable_rows = array();
        foreach ($scoreCriteria as $key => $each_list) {
            $each_row["id"] = isset($each_list->id) ? $each_list->id : "--";
            $each_row["criteria_name"] = isset($each_list->criteria_name) ? $each_list->criteria_name : "--";
            $each_row["type_id"] = $match_type[$each_list->type_id];
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }
}
