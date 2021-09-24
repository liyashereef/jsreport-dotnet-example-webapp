<?php

namespace Modules\Recruitment\Repositories;

use Modules\Recruitment\Models\RecCandidateScreeningPersonalityScore;
use Modules\Recruitment\Models\RecMyersBriggsIndicator;
use Modules\Recruitment\Models\RecMyersBriggsPersonalityType;
use Modules\Recruitment\Repositories\RecCandidateScreeningPersonalityInventoryRepository;
use Modules\Recruitment\Repositories\RecCandidateScreeningPersonalitySumRepository;

class RecCandidateScreeningPersonalityScoreRepository
{

    /**
     * Create a new CandidateScreeningPersonalityScoreRepository instance.
     *
     *
     */
    public function __construct()
    {
        $this->model = new RecCandidateScreeningPersonalityScore;
        $this->myers_briggs_indicator_model = new RecMyersBriggsIndicator;
        $this->inventory_repository = new RecCandidateScreeningPersonalityInventoryRepository;
        $this->sum_repository = new RecCandidateScreeningPersonalitySumRepository;
        $this->myers_briggs_personality_type_model = new RecMyersBriggsPersonalityType;
    }

    /**
     * Check score is calculated of a  candidate
     *
     * @param candidate_id integer
     * @return integer count
     */
    public function checkScore($candidate_id)
    {
        return $this->model->where('candidate_id', $candidate_id)->count();
    }

    /**
     * Calculate score of a candidate
     *
     * @param candidate_id integer
     * @return boolean
     */
    public function calculateScore($candidate_id)
    {
        try {
            \DB::beginTransaction();
            $this->getAllSum($candidate_id);
           
            for ($column = 1; $column <= 7; $column = $column + 2) {
                $optionWithMoreWeightage = ($column == 1) ? $this->getColumnCompare($candidate_id, $column) : $this->getColumnSumCompare($candidate_id, $column);
                if ($optionWithMoreWeightage) {
                     $myers_briggs_value = $this->myers_briggs_indicator_model->where('option', $optionWithMoreWeightage)->where('column', $column)->pluck('initial');
                }
                switch ($column) {
                    case 1:
                        $ei = ($optionWithMoreWeightage) ? $myers_briggs_value[0] : 0;
                        break;
                    case 3:
                        $sn = ($optionWithMoreWeightage) ? $myers_briggs_value[0] : 0;
                        break;
                    case 5:
                        $tf = ($optionWithMoreWeightage) ? $myers_briggs_value[0] : 0;
                        break;
                    case 7:
                        $jp = ($optionWithMoreWeightage) ? $myers_briggs_value[0] : 0;
                        break;
                }
            }
            $personality_types = $this->findPersonalityType($ei, $sn, $tf, $jp);
            $score = $this->storeScore($candidate_id, $personality_types);
            \DB::commit();
            return true;
            //return response()->json(['success' => true, 'message' => 'Saved score successfully']);
        } catch (\Exception $e) {
            \DB::rollback();
            return false;
            //return response()->json(['success' => false,'message' => $e->getMessage()]);
        }
    }

    /**
     * Store personality score
     *
     *  @param candidate_id integer
     *  @param personality_types array
     *
     */
    public function storeScore($candidate_id, $personality_types)
    {

        $i = 1;
        foreach ($personality_types as $personality_type) {
            $score['candidate_id'] = $candidate_id;
            $score['EI'] = $personality_type['ei'];
            $score['SN'] = $personality_type['sn'];
            $score['TF'] = $personality_type['tf'];
            $score['JP'] = $personality_type['jp'];
            $score['score'] = $personality_type['type'];
            $score['order'] = ($i == 1) ? 1 : 0;
            $i++;
            $this->model->create($score);
        }
        return true;
    }

    /***
     * To get all the personality types
     *
     * ******************************************
     *  Parameters
     * ***********
     *
     * @param integer ei - Extraversion/Introversion
     * @param integer sn - Sensing/Intuitive
     * @param integer tf - Thinking/Feeling
     * @param integer jp - Judging/Perceiving
     * *******************************************
     *
     * @return array
     *
     */

    public function findPersonalityType($ei, $sn, $tf, $jp)
    {
        $query = $this->myers_briggs_personality_type_model->when($ei, function ($query, $ei) {
            $query->where('ei', $ei);
        })
            ->when($sn, function ($query, $sn) {
                $query->where('sn', $sn);
            })
            ->when($tf, function ($query, $tf) {
                $query->where('tf', $tf);
            })
            ->when($jp, function ($query, $jp) {
                $query->where('jp', $jp);
            });
        return $query->get();
    }

    /**
     * Compares the sum of two column sums
     *
     * @param candidate_id integer
     * @param column integer
     * @return string
     */
    public function getColumnSumCompare($candidate_id, $column)
    {
        $column1_sum_a = $this->sum_repository->get($candidate_id, $column, 'a');
        $column1_sum_b = $this->sum_repository->get($candidate_id, $column, 'b');

        $column2_sum_a = $this->sum_repository->get($candidate_id, ($column - 1), 'a');
        $column2_sum_b = $this->sum_repository->get($candidate_id, ($column - 1), 'b');
        //dd($column1_sum_a[0]);
        $total_sum_column_a = $column1_sum_a[0] + $column2_sum_a[0];
        $total_sum_column_b = $column1_sum_b[0] + $column2_sum_b[0];

        return $weightage_column = $this->compare($total_sum_column_a, $total_sum_column_b);
    }

    /**
     * Compares sum of two column
     *
     * @param candidate_id integer
     * @param column integer
     * @return string
     */
    public function getColumnCompare($candidate_id, $column)
    {
        $column_sum_a = $this->sum_repository->get($candidate_id, $column, 'a');
        $column_sum_b = $this->sum_repository->get($candidate_id, $column, 'b');
        return $weightage_column = $this->compare($column_sum_a[0], $column_sum_b[0]);
    }

    /**
     * Returns the greater column sum
     *
     * @param column_sum_a integer
     * @param column_sum_b integer
     * @return integer
     */
    public function compare($column_sum_a, $column_sum_b)
    {
        if ($column_sum_a == $column_sum_b) {
            return false;
        } elseif ($column_sum_a > $column_sum_b) {
            return 'a';
        } else {
            return 'b';
        }
        //return ($column_sum_a > $column_sum_b) ? 'a' : 'b';
    }

    /**
     * Calculate sum of all the same columns with option a and b
     *
     * @param candidate_id integer
     * @return array
     */
    public function getAllSum($candidate_id)
    {
        for ($column = 1; $column <= 7; $column++) {
            $this->sumStore($candidate_id, $column, 'a');
            $this->sumStore($candidate_id, $column, 'b');
        }
    }

    /**
     * Calculate column sum of the given column and save it in db
     *
     * @param candidate_id integer
     * @param column integer
     * @param  option string
     * @return array
     */
    public function sumStore($candidate_id, $column, $option)
    {

        $column_sum = $this->inventory_repository->calculateColumnSum($candidate_id, $column, $option);
        $sum = [
            'candidate_id' => $candidate_id,
            'column' => $column,
            'option' => $option,
            'sum' => $column_sum,
        ];
        $this->sum_repository->store($sum);
    }
}
