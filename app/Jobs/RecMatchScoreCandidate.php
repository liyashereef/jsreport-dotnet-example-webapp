<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Recruitment\Models\RecMatchScoreCriteria;
use Modules\Recruitment\Models\RecMatchScoreCriteriaMapping;
use Modules\Recruitment\Models\RecCandidateScreeningPersonalityScore;
use Modules\Recruitment\Models\RecCandidateJobDetails;
use Modules\Recruitment\Models\RecJob;
use Modules\Recruitment\Models\RecCandidateMatchScore;
use App\Services\LocationService;
use Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\HelperService;
use Modules\Admin\Models\Customer;
use App\Repositories\MailQueueRepository;

class RecMatchScoreCandidate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $reccandidate, $mailQueueRepository;
    public $timeout = 0;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($candidate)
    {
        $this->reccandidate = $candidate;
        $this->mailQueueRepository = new MailQueueRepository();
    }

    public function calculations($comparingValue, $maping, $candidateMatchScore, $total_score, $exactMatchCheck = false)
    {
       
        for ($i = 0; $i < count($maping); $i++) {
            if ($exactMatchCheck) {
                if ($comparingValue == $maping[$i]->limit) {
                    $range = $maping[$i];
                    $score = $range->score;
                    break;
                } else {
                    $score = 0;
                }
            } else {
                if (($i) == count($maping) - 1 || ($comparingValue >= $maping[$i]->limit && $comparingValue <= $maping[$i + 1]->limit)) {
                    $range = $maping[$i];
                    $score = $range->score;
                    break;
                }
            }
        }
        $candidateMatchScore['premium'] = $comparingValue;
        $candidateMatchScore['mapping_value'] = $score;
        $number=$score * ($candidateMatchScore['criteria_weight'] / 100);
        $candidateMatchScore['weighted_score'] =number_format(floor($number*100)/100, 2);
        $total_score = $total_score + $candidateMatchScore['weighted_score'];

        $matchscore = RecCandidateMatchScore::updateOrCreate(array('candidate_id' => $candidateMatchScore['candidate_id'], 'job_id' => $candidateMatchScore['job_id'], 'criteria_id' => $candidateMatchScore['criteria_id']), $candidateMatchScore);
        return $total_score;
    }

    public function shiftMatches($candidate_availability, $job_availability, $candidateMatchScore, $eachCriteria, $total_score)
    {
        $candidateAvailability =str_replace(array('"','[',']'), '', explode(',', $candidate_availability));
        $jobAvailability = str_replace(array('"','[',']'), '', explode(',', $job_availability));

        $isArrEqual = count($candidateAvailability) == count($jobAvailability) && !array_udiff($candidateAvailability, $jobAvailability, 'strcasecmp');
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
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $jobList = RecJob::where('status', 'approved')->with('customer')->get();
             $criterias = RecMatchScoreCriteria::whereHas('criteriaMapping')->orWhereHas('scoreCriteriaLookup', function ($q) {
                    $q->where('type_id', 6);
             })->with('criteriaMapping')
            ->get();
            $reccandidate = $this->reccandidate;
            foreach ($jobList as $key => $eachJob) {
                $jobDetails = array();
                $total_score = 0;
                foreach ($criterias as $key => $eachCriteria) {
                    $candidateMatchScore = array();
                  
                    $jobDetails['candidate_id'] = $reccandidate['id'];
                    $jobDetails['job_id'] = $eachJob->id;
                    $candidateMatchScore['candidate_id'] = $reccandidate['id'];
                    $candidateMatchScore['job_id'] =  $eachJob->id;
                    $candidateMatchScore['criteria_id'] = $eachCriteria->criteria_id;
                    $candidateMatchScore['criteria_weight'] = $eachCriteria->weight;
                    switch ($eachCriteria->criteria_id) {
                        case 1:
                            $comparingValue = (abs($reccandidate['wage_expectation']['wageExpectation'] -  $eachJob->wage) / $reccandidate['wageExpectation']['wage_expectations']) * 100;
                            $total_score = $this->calculations($comparingValue, $eachCriteria->criteriaMapping, $candidateMatchScore, $total_score);
                            break;

                        case 2:
                            if ($reccandidate['postal_code'] != null && $eachJob->customer->postal_code != null) {
                                $inputs['destinations'] = [];
                                $destinyPincodeLatLang = null;
                                $locationService = new LocationService();
                                $orginPincodeLatLang = $locationService->getLatLongByAddress(urlencode($reccandidate['postal_code']));
                                 $destinyPincodeLatLang = $locationService->getLatLongByAddress(urlencode($eachJob->customer->postal_code));
                                if ($orginPincodeLatLang['postal_code_address']!=null &&  $destinyPincodeLatLang['postal_code_address']!=null) {
                                    // removed google api call
                                    /*  $inputs['origins'][0] = $orginPincodeLatLang;
                                    $destination = [];
                                     if ($destinyPincodeLatLang != null) {
                                        $destination['lat'] = $destinyPincodeLatLang['lat'];
                                        $destination['long'] = $destinyPincodeLatLang['long'];
                                        array_push($inputs['destinations'], $destination);
                                    }
                                    if (!empty($inputs['destinations']) && !empty($inputs['origins'])) {
                                        //Finding distance and time.
                                        $distances = $locationService->getDrivingDistance($inputs);
                                    }
                               
                                   $matrixData = $distances['distanceMatrix']->rows[0]->elements[0];
                                    if (!empty($matrixData) && $matrixData->status == 'OK') {
                                        $distance = $matrixData->distance->value/1000;
                                        $duration = $matrixData->duration->value / 60;
                                    }*/

                                    $distance = HelperService::distanceBetweenCordinates($orginPincodeLatLang['lat'],$orginPincodeLatLang['long'],$destinyPincodeLatLang['lat'],$destinyPincodeLatLang['long']);
                                    if($distance != 0){
                                        $duration = ($distance / REC_MATCHSCORE_SPEED) * 60 ;
                                    }else{
                                        $duration = 0;
                                    }
                                    
                                    $jobDetails['estimated_travel_time'] = number_format((float)$duration, 2, '.', '');
                                    $jobDetails['estimate_distance'] = number_format((float)$distance, 2, '.', '');
                                    $total_score = $this->calculations($duration, $eachCriteria->criteriaMapping, $candidateMatchScore, $total_score);
                                }
                            }

                            break;
                        case 3://shift match
                            $total_score = $this->shiftMatches($reccandidate['availability']['shifts'], $eachJob->shifts, $candidateMatchScore, $eachCriteria, $total_score);

                            break;
                        case 4: //schedule match
                            $total_score = $this->shiftMatches($reccandidate['availability']['days_required'], $eachJob->days_required, $candidateMatchScore, $eachCriteria, $total_score);
                            break;
                        
                        case 5:
                            $comparingValue = (abs($reccandidate['awareness']['prefered_hours_per_week'] -  $eachJob->hours_per_week) / $reccandidate['awareness']['prefered_hours_per_week']) * 100;
                            $total_score = $this->calculations($comparingValue, $eachCriteria->criteriaMapping, $candidateMatchScore, $total_score);
                            
                            break;
                        case 6:
                            if (!empty($reccandidate['guardingExperience']['years_security_experience'])) {
                                $comparingValue = (abs($reccandidate['guardingExperience']['years_security_experience'] -  $eachJob->total_experience));
                                $total_score = $this->calculations($comparingValue, $eachCriteria->criteriaMapping, $candidateMatchScore, $total_score);
                            }

                            break;
                        case 7:
                            $comparingValue = $reccandidate['awareness']['average_score'];
                            $candidateMatchScore['premium'] = $comparingValue;
                             $candidateMatchScore['mapping_value'] = $comparingValue;
                             $number=$comparingValue * ($candidateMatchScore['criteria_weight'] / 100);
                             $candidateMatchScore['weighted_score'] =number_format(floor($number*100)/100, 2);
                            $total_score = $total_score + $candidateMatchScore['weighted_score'];

                            $matchscore = RecCandidateMatchScore::updateOrCreate(array('candidate_id' => $candidateMatchScore['candidate_id'], 'job_id' => $candidateMatchScore['job_id'], 'criteria_id' => $candidateMatchScore['criteria_id']), $candidateMatchScore);
                            break;
                        case 8:
                            $scoreRating = RecCandidateScreeningPersonalityScore::where('order', 1)->where('candidate_id', $reccandidate['id'])->first();
                            if (isset($scoreRating)) {
                                $total_score = $this->calculations($scoreRating->score, $eachCriteria->criteriaMapping, $candidateMatchScore, $total_score, true);
                            }
                            break;
                        case 9:
                            $total_score = $this->calculations((int) filter_var($reccandidate['miscellaneous']['career_interest'], FILTER_SANITIZE_NUMBER_INT), $eachCriteria->criteriaMapping, $candidateMatchScore, $total_score, true);

                            break;
                        case 10:
                            $total_score = $this->calculations($reccandidate['awareness']['brand_awareness_id'], $eachCriteria->criteriaMapping, $candidateMatchScore, $total_score, true);

                            break;
                        case 11:
                            $total_score = $this->calculations(Carbon::parse($reccandidate['dob'])->age, $eachCriteria->criteriaMapping, $candidateMatchScore, $total_score);

                            break;
                        default:
                            break;
                    }
                }
                $jobDetails['recruiter_id'] =  $eachJob->hr_rep_id;
                $jobDetails['rec_match_score'] = $total_score;
                RecCandidateJobDetails::updateOrCreate(array('candidate_id' => $jobDetails['candidate_id'], 'job_id' => $jobDetails['job_id']), $jobDetails);

                $customer = Customer::where('id', $eachJob->customer_id)->first();
                if (isset($customer) && isset($customer['recruiting_match_score_for_sending_mail']) && $total_score > $customer['recruiting_match_score_for_sending_mail']) {
                    $helper_variable = array(
                        '{receiverFullName}' => HelperService::sanitizeInput($reccandidate['full_name']),
                        '{client}' => HelperService::sanitizeInput($customer['client_name']),
                        '{projectNumber}' => HelperService::sanitizeInput($customer['project_number']),
                        '{total_score}' => HelperService::sanitizeInput($total_score),
                    );
                    $emailResult = $this->mailQueueRepository->prepareMailTemplate(
                        'rec_criteria_threshold_mail',
                        null,
                        $helper_variable,
                        "Modules\Recruitment\Models\RecCandidateJobDetails",
                        0,
                        0,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        $jobDetails['candidate_id']
                    );
                }
            }
        } catch (\Exception $e) {
              Log::channel('matchScoreLog')->info("Error: " . $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile());
        }
    }
}
