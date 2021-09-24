<?php

namespace Modules\Hranalytics\Repositories;

use DB;
use Modules\Hranalytics\Models\CandidateSecurityGuardingExperince;

class RecruitmentAnalyticsDashboardRepository
{
    public function __construct()
    {

    }

    public function getCandidateMilitaryExperienceData()
    {
        $sql = "select cm.veteran_of_armedforce as label,count(*) as total
        from candidate_jobs cj
        join candidate_miscellaneouses cm
        on cj.candidate_id=cm.candidate_id
        where cj.status='Applied'
        AND cj.deleted_at IS NULL
        group by cm.veteran_of_armedforce";

        $data = $this->prepareDataForGraph($sql);
        return $data;
    }

    public function prepareDataForGraph($sql)
    {
        $elements = $labels = [];
        $data = \DB::select(\DB::raw($sql));
        foreach ($data as $each_data) {
            $elements[] = $each_data->total;
            $labels[] = ucfirst($each_data->label);
        }
        return ['elements' => $elements, 'labels' => $labels];
    }

    public function getCandidateLanguage()
    {
        $results = \DB::select(\DB::raw("select ll.language,cl.speaking,cl.reading,cl.writing
                    from candidate_jobs cj
                    join candidate_languages cl
                    join language_lookups ll
                    on cj.candidate_id=cl.candidate_id
                    AND ll.id = cl.language_id
                    where cj.status='Applied'
                    AND cj.deleted_at IS NULL"));

        $language['English']['Reading']['C - Fluent - this is my native language.'] = $language['English']['Writing']['C - Fluent - this is my native language.'] = $language['English']['Speaking']['C - Fluent - this is my native language.'] = $language['English']['Reading']['B - Functional - this is my second language but I can get by.'] = $language['English']['Writing']['B - Functional - this is my second language but I can get by.'] = $language['English']['Speaking']['B - Functional - this is my second language but I can get by.'] = $language['English']['Reading']['A - Limited - I am just learning the language.'] = $language['English']['Writing']['A - Limited - I am just learning the language.'] = $language['English']['Speaking']['A - Limited - I am just learning the language.'] = 0;

        foreach ($results as $result) {
            $language[$result->language]['Reading'][$result->reading] = isset($language[$result->language]['Reading'][$result->reading]) ? $language[$result->language]['Reading'][$result->reading] + 1 : 1;
            $language[$result->language]['Speaking'][$result->speaking] = isset($language[$result->language]['Speaking'][$result->speaking]) ? $language[$result->language]['Speaking'][$result->speaking] + 1 : 1;
            $language[$result->language]['Writing'][$result->writing] = isset($language[$result->language]['Writing'][$result->writing]) ? $language[$result->language]['Writing'][$result->writing] + 1 : 1;
        }

        return $language;
    }

    public function candidateFluencyEnglish()
    {
        $language = $this->getCandidateLanguage();

        $fluent = [
            $language['English']['Reading']['C - Fluent - this is my native language.'],
            $language['English']['Writing']['C - Fluent - this is my native language.'],
            $language['English']['Speaking']['C - Fluent - this is my native language.'],
        ];

        $functional = [
            $language['English']['Reading']['B - Functional - this is my second language but I can get by.'],
            $language['English']['Writing']['B - Functional - this is my second language but I can get by.'],
            $language['English']['Speaking']['B - Functional - this is my second language but I can get by.'],
        ];

        $limited = [
            $language['English']['Reading']['A - Limited - I am just learning the language.'],
            $language['English']['Writing']['A - Limited - I am just learning the language.'],
            $language['English']['Speaking']['A - Limited - I am just learning the language.'],
        ];

        $labels = ['Reading', 'Writing', 'Speaking'];

        return [
            'elements' =>
            [
                'fluent' => $fluent,
                'functional' => $functional,
                'limited' => $limited,
            ],
            'labels' => $labels,
        ];
    }

    public function candidateFluencyFrench()
    {
        $language = $this->getCandidateLanguage();

        $fluent = [
            $language['French']['Reading']['C - Fluent - this is my native language.'],
            $language['French']['Writing']['C - Fluent - this is my native language.'],
            $language['French']['Speaking']['C - Fluent - this is my native language.'],
        ];

        $functional = [
            $language['French']['Reading']['B - Functional - this is my second language but I can get by.'],
            $language['French']['Writing']['B - Functional - this is my second language but I can get by.'],
            $language['French']['Speaking']['B - Functional - this is my second language but I can get by.'],
        ];

        $limited = [
            $language['French']['Reading']['A - Limited - I am just learning the language.'],
            $language['French']['Writing']['A - Limited - I am just learning the language.'],
            $language['French']['Speaking']['A - Limited - I am just learning the language.'],
        ];

        $labels = ['Reading', 'Writing', 'Speaking'];

        return [
            'elements' =>
            [
                'fluent' => $fluent,
                'functional' => $functional,
                'limited' => $limited,
            ],
            'labels' => $labels,
        ];
    }

    public function candidateComputerSkill()
    {
        $query = "select s.skills,cs.skill_level from candidate_jobs cj
                join candidate_skills cs ON cj.candidate_id=cs.candidate_id
                JOIN skill_lookups s ON cs.skill_id=s.id
                where cj.status='Applied'
                AND s.category='Special Skills'
                Order By Field(cs.skill_level,'No Knowledge', 'Basic Knowledge', 'Good Knowledge','Advanced Knowledge','Expert Knowledge')
                ";
        $results = \DB::select(\DB::raw($query));
        $skill_levels = ['No Knowledge', 'Basic Knowledge', 'Good Knowledge', 'Advanced Knowledge', 'Expert Knowledge'];
        $special_skills = array_fill_keys($skill_levels, array());
        foreach ($results as $result) {
            foreach ($skill_levels as $each_skill_level) {
                @$special_skills[$each_skill_level][$result->skills] += ($result->skill_level == $each_skill_level) ? 1 : 0;
            }
        }

        $noKnowledge = [
            $special_skills['No Knowledge']['Microsoft Excel'],
            $special_skills['No Knowledge']['Microsoft Powerpoint'],
            $special_skills['No Knowledge']['Microsoft Word'],
        ];

        $basicKnowledge = [
            $special_skills['Basic Knowledge']['Microsoft Excel'],
            $special_skills['Basic Knowledge']['Microsoft Powerpoint'],
            $special_skills['Basic Knowledge']['Microsoft Word'],
        ];

        $goodKnowledge = [
            $special_skills['Good Knowledge']['Microsoft Excel'],
            $special_skills['Good Knowledge']['Microsoft Powerpoint'],
            $special_skills['Good Knowledge']['Microsoft Word'],
        ];

        $advancedKnowledge = [
            $special_skills['Advanced Knowledge']['Microsoft Excel'],
            $special_skills['Advanced Knowledge']['Microsoft Powerpoint'],
            $special_skills['Advanced Knowledge']['Microsoft Word'],
        ];

        $expertKnowledge = [
            $special_skills['Expert Knowledge']['Microsoft Excel'],
            $special_skills['Expert Knowledge']['Microsoft Powerpoint'],
            $special_skills['Expert Knowledge']['Microsoft Word'],
        ];

        $labels = ['Microsoft Excel', 'Microsoft Powerpoint', 'Microsoft Word'];

        return [
            'elements' =>
            [
                'noKnowledge' => $noKnowledge,
                'basicKnowledge' => $basicKnowledge,
                'goodKnowledge' => $goodKnowledge,
                'advancedKnowledge' => $advancedKnowledge,
                'expertKnowledge' => $expertKnowledge,
            ],
            'labels' => $labels,
        ];
    }

    public function candidateSoftSkills()
    {
        $query = "select s.skills,cs.skill_level from candidate_jobs cj
                join candidate_skills cs ON cj.candidate_id=cs.candidate_id
                JOIN skill_lookups s ON cs.skill_id=s.id
                where cj.status='Applied'
                AND s.category='Soft Skills'
                Order By Field(cs.skill_level,'No Knowledge', 'Basic Knowledge', 'Good Knowledge','Advanced Knowledge','Expert Knowledge')
                ";
        $results = \DB::select(\DB::raw($query));
        $skill_levels = ['No Knowledge', 'Basic Knowledge', 'Good Knowledge', 'Advanced Knowledge', 'Expert Knowledge'];
        @$soft_skills = array_fill_keys($skill_levels, array());
        foreach ($results as $result) {
            foreach ($skill_levels as $each_skill_level) {
                @$soft_skills[$each_skill_level][$result->skills] += ($result->skill_level == $each_skill_level) ? 1 : 0;
            }
        }

        $noKnowledge = [
            $soft_skills['No Knowledge']['Customer Service'],
            $soft_skills['No Knowledge']['Leadership'],
            $soft_skills['No Knowledge']['Time Management'],
            $soft_skills['No Knowledge']['Problem Solving And Critical Thinking'],
        ];

        $basicKnowledge = [
            $soft_skills['Basic Knowledge']['Customer Service'],
            $soft_skills['Basic Knowledge']['Leadership'],
            $soft_skills['Basic Knowledge']['Time Management'],
            $soft_skills['Basic Knowledge']['Problem Solving And Critical Thinking'],
        ];

        $goodKnowledge = [
            $soft_skills['Good Knowledge']['Customer Service'],
            $soft_skills['Good Knowledge']['Leadership'],
            $soft_skills['Good Knowledge']['Time Management'],
            $soft_skills['Good Knowledge']['Problem Solving And Critical Thinking'],
        ];

        $advancedKnowledge = [
            $soft_skills['Advanced Knowledge']['Customer Service'],
            $soft_skills['Advanced Knowledge']['Leadership'],
            $soft_skills['Advanced Knowledge']['Time Management'],
            $soft_skills['Advanced Knowledge']['Problem Solving And Critical Thinking'],
        ];

        $expertKnowledge = [
            $soft_skills['Expert Knowledge']['Customer Service'],
            $soft_skills['Expert Knowledge']['Leadership'],
            $soft_skills['Expert Knowledge']['Time Management'],
            $soft_skills['Expert Knowledge']['Problem Solving And Critical Thinking'],
        ];

        $labels = [
            ['Customer Service'],
            ['Leadership'],
            ['Time Management'],
            ['Problem Solving And', ' Critical Thinking'],
        ];

        return [
            'elements' =>
            [
                'noKnowledge' => $noKnowledge,
                'basicKnowledge' => $basicKnowledge,
                'goodKnowledge' => $goodKnowledge,
                'advancedKnowledge' => $advancedKnowledge,
                'expertKnowledge' => $expertKnowledge,
            ],
            'labels' => $labels,
        ];
    }

    public function employmentEntities()
    {
        $elements = [];
        $sql1 = "select count(*) as total_current_employees
                    from candidate_jobs cj
                    join candidate_experiences ce ON ce.candidate_id=cj.candidate_id
                    where cj.status='Applied'
                    AND cj.deleted_at IS NULL
                    AND ce.current_employee_commissionaries='Yes'";
        $sql2 = "select count(*) as total_former_employees
                    from candidate_jobs cj
                    join candidate_experiences ce ON ce.candidate_id=cj.candidate_id
                    where cj.status='Applied'
                    AND cj.deleted_at IS NULL
                    AND ce.applied_employment='Yes'";
        $sql3 = "select count(*) as total_nationally_employees
                    from candidate_jobs cj
                    join candidate_experiences ce ON ce.candidate_id=cj.candidate_id
                    where cj.status='Applied'
                    AND cj.deleted_at IS NULL
                    AND ce.employed_by_corps='Yes'";
        $elements['Commissionaires'] = \DB::select(\DB::raw($sql1))[0]->total_current_employees;
        $elements['Former'] = \DB::select(\DB::raw($sql2))[0]->total_former_employees;
        $elements['Nationally'] = \DB::select(\DB::raw($sql3))[0]->total_nationally_employees;
        $labels = ['Commissionaires', 'Former', 'Nationally'];

        return [
            'elements' => [$elements['Commissionaires'], $elements['Former'], $elements['Nationally']],
            'labels' => $labels,
        ];
    }

    public function firedConvictedCandidates()
    {
        /* Candidates By Fired Status */
        $sql1 = "select count(*) as total_convicted
         from candidate_jobs cj
         join candidate_miscellaneouses cm
         on cj.candidate_id=cm.candidate_id
         where cj.status='Applied'
         AND cj.deleted_at IS NULL
         AND cm.dismissed='Yes'";

        /* Candidates By Fired/Convicted Status */
        $sql2 = "select count(*) as total_fired
                    from candidate_jobs cj
                    join candidate_miscellaneouses cm
                    on cj.candidate_id=cm.candidate_id
                    where cj.status='Applied'
                    AND cj.deleted_at IS NULL
                    AND cm.criminal_convicted='Yes'";
        $elements = [];
        $elements['Convicted'] = \DB::select(\DB::raw($sql1))[0]->total_convicted;
        $elements['Fired'] = \DB::select(\DB::raw($sql2))[0]->total_fired;
        $labels = ['Convicted', 'Fired'];

        return ['elements' => [$elements['Convicted'], $elements['Fired']], 'labels' => $labels];
    }

    public function candidatesByCareerInterestInCgl()
    {
        $sql = "select cm.career_interest as label,count(*) as total
                    from candidate_jobs cj
                    join candidate_miscellaneouses cm
                    on cj.candidate_id=cm.candidate_id
                    where cj.status='Applied'
                    AND cj.deleted_at IS NULL
                    group by cm.career_interest";

        $elements = $this->prepareDataForGraph($sql);
        $labels = [
            ['1 - Commissionaires is a', 'temporary stop in my career.', ' I have no long term plans.'],
            ['2 - I would be interested', ' in exploring a longer term ', 'career at Commissionaires.'],
            ['3 - I am interested in a ', 'long term career with ', 'Commissionaires.'],
            ['4 - Commissionaires is ', 'strategic to my long term', 'career in security.'],
        ];

        return ['elements' => $elements['elements'], 'labels' => $labels];
    }

    public function candidatesByAverageScore()
    {
        $sql = "select csql.category as label,ROUND(AVG(csq.score),2) as total
                    from candidate_screening_question_lookups csql
                    join candidate_screening_questions csq
                    on csq.question_id=csql.id
                    group by csql.category";

        $elements = $this->prepareDataForGraph($sql);
        $labels = [
            ['Initiative'],
            ['Stress Tolerance'],
            ['Teamwork / ', 'Interpersonal Group Dynamics'],
            ['Scenarios / ', 'Problem Solving'],
        ];

        return ['elements' => $elements['elements'], 'labels' => $labels];
    }

    public function loadingDocuments()
    {
        $no_count = 0;
        $full_count = 0;
        $partial_count = 0;
        $sql = \DB::select(\DB::raw("select j.id,count(cal.id) as attachment_count from `jobs` j left join candidate_attachment_lookups cal on j.id=cal.job_id or cal.job_id IS NULL where j.status='approved' OR  j.status='completed'
            GROUP BY j.id"));
        foreach ($sql as $id => $result) {
            $sql1 = \DB::select(\DB::raw("select count(ca.attachment_id) as count from candidate_jobs cj JOIN jobs j ON cj.job_id=j.id AND j.id=$result->id AND cj.status='applied' Left JOIN candidate_attachments ca ON cj.candidate_id=ca.candidate_id
              where j.status IN ('approved','completed') GROUP BY cj.candidate_id"));

            foreach ($sql1 as $id => $result1) {
                $count_value = $result1->count;

                if ($count_value == $result->attachment_count) {
                    $full_count++;
                } elseif ($count_value == 0) {
                    $no_count++;
                } else {
                    $partial_count++;
                }
            }
        }

        $elements = [];
        $elements['All Document'] = $full_count;
        $elements['Partially'] = $partial_count;
        $elements['None'] = $no_count;
        $labels = ['All Document', 'Partially', 'None'];

        return [
            'elements' => [$elements['All Document'], $elements['Partially'], $elements['None']],
            'labels' => $labels,
        ];
    }

    public function averageCycleTime()
    {
        $sql = "select tpl.process_steps as label,count(*) as total
        from candidate_trackings ct
        join tracking_process_lookups tpl
        on ct.lookup_id=tpl.id
        group by tpl.process_steps";

        return $this->prepareDataForGraph($sql);
    }

    public function positionByReasonsAnalytics()
    {
        $sql = "select id,reason from job_requisition_reason_lookups where deleted_at IS NULL order by reason asc";
        $reasons = \DB::select(\DB::raw($sql));

        foreach ($reasons as $each_reason) {
            $sql = "select jrrl.reason as label,count(*) as total
                    from jobs j
                    join job_requisition_reason_lookups jrrl
                    on j.temp_code_id=jrrl.id AND jrrl.id='" . $each_reason->id . "'
                    AND j.deleted_at IS NULL
                    AND j.status IN ('Approved','Completed')
                    group by jrrl.reason";
            $data = \DB::select(\DB::raw($sql));
            foreach ($data as $each_data) {
                $elements[$each_data->label] = $each_data->total;
            }
        }

        foreach ($reasons as $each_reason) {
            $sql = "select jrrl.reason as label,count(*) as total
                    from jobs j
                    join job_requisition_reason_lookups jrrl
                    on j.permanent_id=jrrl.id AND jrrl.id='" . $each_reason->id . "'
                    AND j.deleted_at IS NULL
                    AND j.status IN ('Approved','Completed')
                    group by jrrl.reason";
            $data = \DB::select(\DB::raw($sql));
            foreach ($data as $each_data) {
                $elements[$each_data->label] = $each_data->total;
            }
        }

        foreach ($reasons as $each_reason) {
            $sql = "select jrrl.reason as label,count(*) as total
                    from jobs j
                    join job_requisition_reason_lookups jrrl
                    on j.resign_id=jrrl.id AND jrrl.id='" . $each_reason->id . "'
                    AND j.deleted_at IS NULL
                    AND j.status IN ('Approved','Completed')
                    group by jrrl.reason";
            $data = \DB::select(\DB::raw($sql));
            foreach ($data as $each_data) {
                $elements[$each_data->label] = $each_data->total;
            }
        }

        $elements = [];
        foreach ($reasons as $each_reason) {
            $sql = "select jrrl.reason as label,count(*) as total
                    from jobs j
                    join job_requisition_reason_lookups jrrl
                    on j.terminate_id=jrrl.id AND jrrl.id='" . $each_reason->id . "'
                    AND j.deleted_at IS NULL
                    AND j.status IN ('Approved','Completed')
                    group by jrrl.reason";
            $data = \DB::select(\DB::raw($sql));
            foreach ($data as $each_data) {
                $elements[$each_data->label] = $each_data->total;
            }
        }
        ksort($elements);

        $labels = [
            ["Caught in unauthorized ", "activity while on the job"],
            ["Customer service skills", "were lacking"],
            "No Reason Provided",
            "Other Reason",
        ];

        return [
            'elements' => array_values($elements),
            'labels' => $labels,
        ];
    }

    public function candidateResidentStatusAnalytics()
    {
        $sql = "select csc.work_status_in_canada as label,count(*) as total
        from candidate_jobs cj
        join candidate_security_clearances csc
        on cj.candidate_id=csc.candidate_id
        where cj.status='Applied'
        AND cj.deleted_at IS NULL
        group by csc.work_status_in_canada";

        return $this->prepareDataForGraph($sql);
    }

    public function guardDriversLicense()
    {
        $sql = "select csp.driver_license as label,count(*) as total
                    from candidate_jobs cj
                    join candidate_security_proximities csp
                    on cj.candidate_id=csp.candidate_id
                    where cj.status='Applied'
                    AND cj.deleted_at IS NULL
                    group by csp.driver_license";

        return $this->prepareDataForGraph($sql);
    }

    public function accessToPublicTransit()
    {
        $sql = "select csp.access_public_transport as label,count(*) as total
                    from candidate_jobs cj
                    join candidate_security_proximities csp
                    on cj.candidate_id=csp.candidate_id
                    where cj.status='Applied'
                    AND cj.deleted_at IS NULL
                    group by csp.access_public_transport";

        $elements = $this->prepareDataForGraph($sql);

        $labels = [
            ['I have little access to the client site,', 'via public tranist'],
            ['I have some access to the client site,', 'via public transit'],
            ['I have ready access to the client site,', 'via public transit'],
        ];
        return ['elements' => $elements['elements'], 'labels' => $labels];
    }

    public function limitedTransportation()
    {
        $sql = "select r.region_name as label,count(*) as total
                    from candidate_jobs cj
                    join jobs j ON cj.job_id=j.id
                    join customers c ON j.customer_id=c.id
                    join region_lookups r ON c.region_lookup_id=r.id
                    join candidate_security_proximities csp ON cj.candidate_id=csp.candidate_id
                    where cj.status='Applied'
                    AND csp.transportation_limitted ='Yes'
                    AND cj.deleted_at IS NULL
                    group by r.region_name";

        return $this->prepareDataForGraph($sql);
    }

    public function candidatesExperiences()
    {
        /* Candidates experience(Categories) */
        $candidateSecurityGuardingExperinces = CandidateSecurityGuardingExperince::with(['candidate.jobs' => function ($query) {
            $query->where('status', 'Applied');
        }])
            ->select('candidate_id', 'positions_experinces')
            ->get();
        $final = $positions_experinces = array();
        foreach ($candidateSecurityGuardingExperinces as $candidateSecurityGuardingExperince) {
            if (null != $candidateSecurityGuardingExperince->positions_experinces) {
                $positions_experinces[] = json_decode($candidateSecurityGuardingExperince->positions_experinces);
            }
        }
        //dd(array_filter($positions_experinces));
        foreach ($positions_experinces as $each_positions_experince) {
            array_walk_recursive($each_positions_experince, function ($item, $key) use (&$final) {
                $final[$key] = isset($final[$key]) ? $item + $final[$key] : $item;
            });
        }
        $total_candidates = count($positions_experinces);
        array_walk_recursive($final, function ($item, $key) use (&$final, $total_candidates) {
            $final[$key] = $final[$key] / $total_candidates;
        });
        return $final;
    }

    public function prepareWageByCompetitorDataForGraph($sql)
    {
        $elements = $labels = $highValue = $lowValue = $sampleSize = [];
        $data = \DB::select(\DB::raw($sql));
        foreach ($data as $each_data) {
            $elements[] = number_format($each_data->average, 2);
            $highValue[] = number_format($each_data->high, 2);
            $lowValue[] = number_format($each_data->low, 2);
            $sampleSize[] = $each_data->candidateSize;
            $labels[] = ucfirst($each_data->label);
        }

        return ['elements' => $elements, 'labels' => $labels, 'highValue' => $highValue, 'lowValue' => $lowValue, 'sampleSize' => $sampleSize];
    }

    public function candidateWageByCompetitor()
    {
        /* Wage by Competitor */
        $sql = "select sp.security_provider as label,
        ROUND(AVG(cwe.wage_last_hourly),2) as average,
        ROUND(MAX(cwe.wage_last_hourly),2) as high,
        ROUND(MIN(cwe.wage_last_hourly),2) as low,
        COUNT(*) as candidateSize
                 from candidate_wage_expectations cwe
                 join candidate_jobs cj
                 join jobs j
                 join security_provider_lookups sp
                 on cwe.candidate_id=cj.candidate_id
                 AND cwe.wage_last_provider = sp.id
                 AND cj.job_id=j.id
                 AND cj.status='Applied'
                 AND cj.deleted_at IS NULL
                 AND j.deleted_at IS NULL
                 group by sp.security_provider";

        return $this->prepareWageByCompetitorDataForGraph($sql);
    }

    public function candidateWageExpectationsByPosition()
    {
        /* Wage by position */
        $sql = "select p.position as label,
        ROUND(AVG(cwe.wage_expectations_from),2) as wage_low ,
        ROUND(MAX(cwe.wage_expectations_from),2) as wage_low_max ,
        ROUND(MIN(cwe.wage_expectations_from),2) as wage_low_min ,
        ROUND(AVG(cwe.wage_expectations_to),2) as wage_high,
        ROUND(MAX(cwe.wage_expectations_to),2) as wage_high_max ,
        ROUND(MIN(cwe.wage_expectations_to),2) as wage_high_min,
        COUNT(*) as sampleSize
                 from candidate_wage_expectations cwe
                 join candidate_jobs cj
                 join jobs j
                 join position_lookups p
                 on cwe.candidate_id=cj.candidate_id
                 AND j.open_position_id = p.id
                 AND cj.job_id=j.id
                 AND cj.status='Applied'
                 AND cj.deleted_at IS NULL
                 AND j.deleted_at IS NULL
                 group by p.position";

        $data = \DB::select(\DB::raw($sql));
        $highWage = $lowWage = $labels = $additionalData = [];
        foreach ($data as $each_data) {
            $lowWage[] = $each_data->wage_low;
            $highWage[] = $each_data->wage_high;
            $labels[] = ucfirst($each_data->label);
            $additionalData[] = [
                'wage_low_min' => $each_data->wage_low_min,
                'wage_low_max' => $each_data->wage_low_max,
                'wage_high_max' => $each_data->wage_high_max,
                'wage_high_min' => $each_data->wage_high_min,
                'sampleSize' => $each_data->sampleSize,
                'wage_low_average' => $each_data->wage_low,
                'wage_high_average' => $each_data->wage_high,
            ];
        }

        return ['chartDetails' => ['elements' => ['highWage' => $highWage, 'lowWage' => $lowWage], 'labels' => $labels, 'additionalData' => $additionalData]];
    }

    public function candidateWageExpectationsByRegion()
    {
        /* Wage by Region */
        $sql = "select r.region_name as label,
        ROUND(AVG(cwe.wage_expectations_from),2) as wage_low ,
        ROUND(MAX(cwe.wage_expectations_from),2) as wage_low_max ,
        ROUND(MIN(cwe.wage_expectations_from),2) as wage_low_min ,
        ROUND(AVG(cwe.wage_expectations_to),2) as wage_high,
        ROUND(MAX(cwe.wage_expectations_to),2) as wage_high_max ,
        ROUND(MIN(cwe.wage_expectations_to),2) as wage_high_min,
        COUNT(*) as sampleSize
                 from candidate_wage_expectations cwe
                 join candidate_jobs cj
                 join jobs j
                 join region_lookups r
                 join customers c
                 on cwe.candidate_id=cj.candidate_id
                 AND c.region_lookup_id = r.id
                 AND cj.job_id=j.id
                 AND cj.status='Applied'
                 AND cj.deleted_at IS NULL
                 AND j.deleted_at IS NULL
                 group by r.region_name";

        $data = \DB::select(\DB::raw($sql));
        $highWage = $lowWage = $labels = $additionalData = [];
        foreach ($data as $each_data) {
            $lowWage[] = $each_data->wage_low;
            $highWage[] = $each_data->wage_high;
            $labels[] = ucfirst($each_data->label);
            $additionalData[] = [
                'wage_low_min' => $each_data->wage_low_min,
                'wage_low_max' => $each_data->wage_low_max,
                'wage_high_max' => $each_data->wage_high_max,
                'wage_high_min' => $each_data->wage_high_min,
                'sampleSize' => $each_data->sampleSize,
                'wage_low_average' => $each_data->wage_low,
                'wage_high_average' => $each_data->wage_high,
            ];
        }

        return ['chartDetails' => ['elements' => ['highWage' => $highWage, 'lowWage' => $lowWage], 'labels' => $labels, 'additionalData' => $additionalData]];

    }

    public function candidatesExperiencesByRegions()
    {
        $sql = "select r.region_name as label,ROUND(AVG(csge.years_security_experience),2) as total
        from candidate_security_guarding_experinces csge
        join candidate_jobs cj
        join jobs j
        join region_lookups r
        join customers c
        on csge.candidate_id=cj.candidate_id
        AND c.region_lookup_id = r.id
        AND cj.job_id=j.id
        AND cj.status='Applied'
        AND cj.deleted_at IS NULL
        AND j.deleted_at IS NULL
        group by r.region_name";

        return $this->prepareDataForGraph($sql);
    }

    public function candidatesCertificates()
    {
        /* Candidates certificates */
        $sql = "select csge.guard_licence as label,count(*) as total
                    from candidate_security_guarding_experinces csge
                    join candidate_jobs cj
                    join jobs j
                    on csge.candidate_id=cj.candidate_id
                    AND cj.job_id=j.id
                    where cj.status='Applied'
                    AND cj.deleted_at IS NULL
                    AND j.deleted_at IS NULL
                    group by csge.guard_licence";

        return $this->prepareDataForGraph($sql);

    }

    public function candidatesAnalytics()
    {
        /* Candidates */
        $sql = "select c.client_name as label,count(*) as total
                    from candidate_jobs cj
                    join jobs j
                    join customers c
                    on cj.job_id=j.id
                    AND j.customer_id=c.id
                    AND j.deleted_at IS NULL
                    AND j.status IN ('Approved','Completed')
                    AND cj.deleted_at IS NULL
                    group by c.client_name";

        return $this->prepareDataForGraph($sql);
    }

    public function candidatesRegions()
    {
        /* Candidates regions */
        $sql = "select r.region_name as label,count(*) as total
                    from candidate_jobs cj
                    join jobs j
                    join customers c
                    join region_lookups r
                    on cj.job_id=j.id
                    AND j.customer_id=c.id
                    AND c.region_lookup_id = r.id
                    where cj.status='Applied'
                    AND cj.deleted_at IS NULL
                    AND j.deleted_at IS NULL
                    group by r.region_name";

        return $this->prepareDataForGraph($sql);
    }

    public function JobRequisitionAnalytics()
    {
        $sql = "select j.status as label,count(*) as total
                from jobs j
                WHERE j.deleted_at IS NULL
                group by j.status";

        return $this->prepareDataForGraph($sql);
    }

    public function PositionByRegionAnalytics()
    {
        $sql = "select rl.region_name as label,count(*) as total
        from jobs j
        join customers c
        join region_lookups rl
        on j.customer_id=c.id
        AND c.region_lookup_id = rl.id
        AND j.deleted_at IS NULL
        AND j.status IN ('Approved','Completed')
        group by rl.region_name";

        return $this->prepareDataForGraph($sql);
    }

    public function HighestTurnoverAnalytics()
    {
        $sql = "select rl.region_name as label,count(*) as total
        from jobs j
        join customers c
        join region_lookups rl
        join job_requisition_reason_lookups jrrl
        on j.customer_id=c.id
        AND c.region_lookup_id = rl.id
        AND j.deleted_at IS NULL
        AND j.status IN ('Approved','Completed')
        AND j.permanent_id=jrrl.id
        AND jrrl.reason like '%resigned%'
        group by rl.region_name";

        return $this->prepareDataForGraph($sql);
    }

    public function WidgetPlannedOJT()
    {
        $sql = "select c.project_number as label,count(*) as total
        from jobs j
        join customers c
        join training_timing_lookups tl
        on j.training_id=tl.id
        AND j.customer_id=c.id
        AND j.deleted_at IS NULL
        AND j.status IN ('Approved','Completed')
        AND tl.training like '%ojt%'
        group by c.project_number";

        return $this->prepareDataForGraph($sql);
    }

    public function WageByRegionAnalytics()
    {
        $sql = "select rl.region_name as label,
        ROUND(AVG(j.wage_low),2) as wage_low,
        ROUND(MAX(j.wage_low),2) as wage_low_max ,
        ROUND(MIN(j.wage_low),2) as wage_low_min ,
        ROUND(AVG(j.wage_high),2) as wage_high,
        ROUND(MAX(j.wage_high),2) as wage_high_max ,
        ROUND(MIN(j.wage_high),2) as wage_high_min,
        COUNT(*) as sampleSize
                from jobs j
                join customers c
                join region_lookups rl
                join job_requisition_reason_lookups jrrl
                on j.customer_id=c.id
                AND c.region_lookup_id = rl.id
                AND j.deleted_at IS NULL
                AND j.status IN ('Approved','Completed')
                group by rl.region_name";
        $data = \DB::select(\DB::raw($sql));
        $highWage = $lowWage = $labels = $additionalData = [];
        foreach ($data as $each_data) {
            $lowWage[] = $each_data->wage_low;
            $highWage[] = $each_data->wage_high;
            $labels[] = ucfirst($each_data->label);
            $additionalData[] = [
                'wage_low_min' => $each_data->wage_low_min,
                'wage_low_max' => $each_data->wage_low_max,
                'wage_high_max' => $each_data->wage_high_max,
                'wage_high_min' => $each_data->wage_high_min,
                'sampleSize' => $each_data->sampleSize,
                'wage_low_average' => $each_data->wage_low,
                'wage_high_average' => $each_data->wage_high,
            ];
        }

        return ['chartDetails' => ['elements' => ['highWage' => $highWage, 'lowWage' => $lowWage], 'labels' => $labels, 'additionalData' => $additionalData]];
    }

    public function WageByIndustrySector()
    {
        $sql = "select isl.industry_sector_name as label,
        ROUND(AVG(j.wage_low),2) as wage_low,
        ROUND(MAX(j.wage_low),2) as wage_low_max ,
        ROUND(MIN(j.wage_low),2) as wage_low_min ,
        ROUND(AVG(j.wage_high),2) as wage_high,
        ROUND(MAX(j.wage_high),2) as wage_high_max ,
        ROUND(MIN(j.wage_high),2) as wage_high_min,
        COUNT(*) as sampleSize
                from jobs j
                join customers c
                join region_lookups rl
                join industry_sector_lookups isl
                on j.customer_id=c.id
                AND c.industry_sector_lookup_id = isl.id
                AND j.deleted_at IS NULL
                AND j.status IN ('Approved','Completed')
                group by isl.industry_sector_name";
        $data = \DB::select(\DB::raw($sql));
        $highWage = $lowWage = $labels = $additionalData = [];
        foreach ($data as $each_data) {
            $lowWage[] = $each_data->wage_low;
            $highWage[] = $each_data->wage_high;
            $labels[] = ucfirst($each_data->label);
            $additionalData[] = [
                'wage_low_min' => $each_data->wage_low_min,
                'wage_low_max' => $each_data->wage_low_max,
                'wage_high_max' => $each_data->wage_high_max,
                'wage_high_min' => $each_data->wage_high_min,
                'sampleSize' => $each_data->sampleSize,
                'wage_low_average' => $each_data->wage_low,
                'wage_high_average' => $each_data->wage_high,
            ];
        }

        return ['chartDetails' => ['elements' => ['highWage' => $highWage, 'lowWage' => $lowWage], 'labels' => $labels, 'additionalData' => $additionalData]];

    }
}
