<?php

namespace Modules\Hranalytics\Http\Controllers;

use App\Http\Controllers\Controller;
use Charts;
use Illuminate\Http\Request;
use Modules\Admin\Repositories\UserRepository;
use Modules\Hranalytics\Models\CandidateSecurityGuardingExperince;
use Modules\Hranalytics\Models\Job;
use Modules\Hranalytics\Repositories\CandidateRepository;
use Modules\Hranalytics\Repositories\JobRepository;
use View;

class DashboardController extends Controller
{

    /**
     * The CandidateRepository instance.
     *
     * @var \Modules\Hranalytics\Repositories\CandidateRepository
     */
    protected $jobRepository, $userRepository;

    /**
     *
     * @param \App\Http\Controllers\Front\JobRepository $jobRepository
     * @param \App\Http\Controllers\Front\UserRepository $userRepository
     * @param \App\Http\Controllers\Front\CandidateRepository $candidateRepository
     */
    public function __construct(JobRepository $jobRepository, UserRepository $userRepository, CandidateRepository $candidateRepository)
    {
        $this->jobRepository = $jobRepository;
        $this->userRepository = $userRepository;
        $this->candidateRepository = $candidateRepository;
    }

    /**
     *
     * @param type $sql
     * @param type $title
     * @param type $element_label
     * @return type
     */
    public function prepareChart($sql, $title, $element_label)
    {
        $elements = $labels = [];
        $data = \DB::select(\DB::raw($sql));
        foreach ($data as $each_data) {
            $elements[] = $each_data->total;
            $labels[] = ucfirst($each_data->label);
        }
        return Charts::create('bar', 'highcharts')
            ->title($title)
            ->dimensions(0, 400) // Width x Height
            ->template("material")
            ->elementLabel($element_label)
            ->values($elements)
            ->labels($labels);
    }

    /**
     *
     * @return type
     */
    public function index()
    {
        /* Tab #1 -Start */
        /* Job Requisitions */
        $sql = "select j.status as label,count(*) as total
                    from jobs j
                    WHERE j.deleted_at IS NULL
                    group by j.status";
        $charts['job'][] = $this->prepareChart($sql, "Job Requisitions", "Job Status");

        /* Position By Region */
        $sql = "select rl.region_name as label,count(*) as total
                    from jobs j
                    join customers c
                    join region_lookups rl
                    on j.customer_id=c.id
                    AND c.region_lookup_id = rl.id
                    AND j.deleted_at IS NULL
                    AND j.status IN ('Approved','Completed')
                    group by rl.region_name";
        $charts['job'][] = $this->prepareChart($sql, "Position by Region", "Region");

        /* Highest Turnover */
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
        $charts['job'][] = $this->prepareChart($sql, "Highest Turnover", "Turnover");

        /* Position By Reasons */
        $sql = "select id,reason from job_requisition_reason_lookups where deleted_at IS NULL order by reason asc";
        $reasons = \DB::select(\DB::raw($sql));
        /* foreach ($reasons as $each_reason) {
        $sql = "select jrrl.reason as label,count(*) as total
        from jobs j
        join job_requisition_reason_lookups jrrl
        on j.reason_id=jrrl.id AND jrrl.id='" . $each_reason->id . "'
        AND j.deleted_at IS NULL
        AND j.status IN ('Approved','Completed')
        group by jrrl.reason";
        $data = \DB::select(\DB::raw($sql));
        foreach ($data as $each_data) {
        $elements[$each_data->label] = $each_data->total;
        }
        } */
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
        $charts['job'][] = Charts::create('bar', 'highcharts')
            ->title('Position by Reasons')
            ->dimensions(0, 400) // Width x Height
            ->template("material")
            ->elementLabel('Total')
            ->values(array_values($elements))
            ->labels(array_keys($elements));

        /* Wage by Region */
        $sql = "select rl.region_name as label,ROUND(AVG(j.wage_low),2) as wage_low,ROUND(AVG(j.wage_high),2) as wage_high
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
        $dataset1 = $dataset2 = $labels = [];
        foreach ($data as $each_data) {
            $dataset1[] = $each_data->wage_low;
            $dataset2[] = $each_data->wage_high;
            $labels[] = ucfirst($each_data->label);
        }
        $charts['job'][] = Charts::multi('bar', 'highcharts')
            ->title('Wage by Region')
            ->dimensions(0, 400) // Width x Height
            ->template("material")
            ->elementLabel('Average Wage')
            ->dataset('High Wage', $dataset2)
            ->dataset('Low Wage', $dataset1)
            ->labels($labels);

        /* Wage by Industry Sector */
        $sql = "select isl.industry_sector_name as label,ROUND(AVG(j.wage_low),2) as wage_low,ROUND(AVG(j.wage_high),2) as wage_high
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
        $dataset1 = $dataset2 = $labels = [];
        foreach ($data as $each_data) {
            $dataset1[] = $each_data->wage_low;
            $dataset2[] = $each_data->wage_high;
            $labels[] = ucfirst($each_data->label);
        }
        $charts['job'][] = Charts::multi('bar', 'highcharts')
            ->title('Wage by Industry Sector')
            ->dimensions(0, 400) // Width x Height
            ->template("material")
            ->elementLabel('Average Wage')
            ->dataset('High Wage', $dataset2)
            ->dataset('Low Wage', $dataset1)
            ->labels($labels);

        /* Planned OJT */
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
        $charts['job'][] = $this->prepareChart($sql, "Planned OJT", "OJT");
        /* Tab#1-End */

        /* Tab#2-Start */
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
        $charts['candidate'][] = $this->prepareChart($sql, "Candidates", "Customer");

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
        $charts['candidate'][] = $this->prepareChart($sql, "Candidates Regions", "Regions");

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
        $charts['candidate'][] = $this->prepareChart($sql, "Candidates Certificates", "Guard License");

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
        if (count($final) > 0) {
            $charts['candidate'][] = Charts::create('bar', 'highcharts')
                ->title("Candidates Experiences (Categories)")
                ->dimensions(0, 400) // Width x Height
                ->template("material")
                ->elementLabel('Average Experience')
                ->values(array_values($final))
                ->labels(array_keys($final));
        }
        /* Candidate Experinces(Region) */
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
        $charts['candidate'][] = $this->prepareChart($sql, "Candidates Experiences (Regions)", "Average Experience");

        /* Wage by Region */
        $sql = "select r.region_name as label,ROUND(AVG(cwe.wage_expectations_from),2) as wage_low ,ROUND(AVG(cwe.wage_expectations_to),2) as wage_high
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
        $dataset1 = $dataset2 = $labels = [];
        foreach ($data as $each_data) {
            $dataset1[] = $each_data->wage_low;
            $dataset2[] = $each_data->wage_high;
            $labels[] = ucfirst($each_data->label);
        }
        $charts['candidate'][] = Charts::multi('bar', 'highcharts')
            ->title('Wage Expectation by Region')
            ->dimensions(0, 400) // Width x Height
            ->template("material")
            ->elementLabel('Average Wage')
            ->dataset('High Wage', $dataset2)
            ->dataset('Low Wage', $dataset1)
            ->labels($labels);

        /* Wage by Role */
        $sql = "select p.position as label,ROUND(AVG(cwe.wage_expectations_from),2) as wage_low ,ROUND(AVG(cwe.wage_expectations_to),2) as wage_high
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
        $dataset1 = $dataset2 = $labels = [];
        foreach ($data as $each_data) {
            $dataset1[] = $each_data->wage_low;
            $dataset2[] = $each_data->wage_high;
            $labels[] = ucfirst($each_data->label);
        }
        $charts['candidate'][] = Charts::multi('bar', 'highcharts')
            ->title('Wage Expectation by Position')
            ->dimensions(0, 400) // Width x Height
            ->template("material")
            ->elementLabel('Average Wage')
            ->dataset('High Wage', $dataset2)
            ->dataset('Low Wage', $dataset1)
            ->labels($labels);

        /* Wage by Competitor */
        $sql = "select sp.security_provider as label,ROUND(AVG(cwe.wage_last_hourly),2) as total
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
        $charts['candidate'][] = $this->prepareChart($sql, "Wage by Competitor", "Average Wage");
        /* Tab#2-End */

        /* Tab#3-Start */
        /* Candidate resident status */
        $sql = "select csc.work_status_in_canada as label,count(*) as total
                    from candidate_jobs cj
                    join candidate_security_clearances csc
                    on cj.candidate_id=csc.candidate_id
                    where cj.status='Applied'
                    AND cj.deleted_at IS NULL
                    group by csc.work_status_in_canada";
        $charts['candidate_details'][] = $this->prepareChart($sql, "Candidate Resident Status", "Resident Status");

        /* Guards Drivers license */
        $sql = "select csp.driver_license as label,count(*) as total
                    from candidate_jobs cj
                    join candidate_security_proximities csp
                    on cj.candidate_id=csp.candidate_id
                    where cj.status='Applied'
                    AND cj.deleted_at IS NULL
                    group by csp.driver_license";
        $charts['candidate_details'][] = $this->prepareChart($sql, "Guards Drivers License", "Drivers License");

        /* Access to public transit */
        $sql = "select csp.access_public_transport as label,count(*) as total
                    from candidate_jobs cj
                    join candidate_security_proximities csp
                    on cj.candidate_id=csp.candidate_id
                    where cj.status='Applied'
                    AND cj.deleted_at IS NULL
                    group by csp.access_public_transport";
        $charts['candidate_details'][] = $this->prepareChart($sql, "Access to Public Transit", "Public Transit");

        /* Limited Transportation */
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
        $charts['candidate_details'][] = $this->prepareChart($sql, "Limited Transportation", "Limited Transportation");

        /* Level of language fluency - English */
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
        if (count($language) > 0) {
            $charts['candidate_details'][] = Charts::multi('bar', 'highcharts')
                ->title("Candidates by Level of Language Fluency (English)")
                ->dimensions(0, 400) // Width x Height
                ->template("material")
                ->elementLabel('Proficiency')
                ->dataset('Fluent', [
                    $language['English']['Reading']['C - Fluent - this is my native language.'],
                    $language['English']['Writing']['C - Fluent - this is my native language.'],
                    $language['English']['Speaking']['C - Fluent - this is my native language.']])
                ->dataset('Functional', [
                    $language['English']['Reading']['B - Functional - this is my second language but I can get by.'],
                    $language['English']['Writing']['B - Functional - this is my second language but I can get by.'],
                    $language['English']['Speaking']['B - Functional - this is my second language but I can get by.']])
                ->dataset('Limited', [
                    $language['English']['Reading']['A - Limited - I am just learning the language.'],
                    $language['English']['Writing']['A - Limited - I am just learning the language.'],
                    $language['English']['Speaking']['A - Limited - I am just learning the language.'],
                ])
                ->labels(['Reading', 'Writing', 'Speaking']);
        }

        /* Level of language fluency - French */
        $results = \DB::select(\DB::raw("select ll.language,cl.speaking,cl.reading,cl.writing
                    from candidate_jobs cj
                    join candidate_languages cl
                    join language_lookups ll
                    on cj.candidate_id=cl.candidate_id
                    AND ll.id = cl.language_id
                    where cj.status='Applied'
                    AND cj.deleted_at IS NULL"));

        $language['French']['Reading']['C - Fluent - this is my native language.'] = $language['French']['Writing']['C - Fluent - this is my native language.'] = $language['French']['Speaking']['C - Fluent - this is my native language.'] = $language['French']['Reading']['B - Functional - this is my second language but I can get by.'] = $language['French']['Writing']['B - Functional - this is my second language but I can get by.'] = $language['French']['Speaking']['B - Functional - this is my second language but I can get by.'] = $language['French']['Reading']['A - Limited - I am just learning the language.'] = $language['French']['Writing']['A - Limited - I am just learning the language.'] = $language['French']['Speaking']['A - Limited - I am just learning the language.'] = 0;

        foreach ($results as $result) {
            $language[$result->language]['Reading'][$result->reading] = isset($language[$result->language]['Reading'][$result->reading]) ? $language[$result->language]['Reading'][$result->reading] + 1 : 1;
            $language[$result->language]['Speaking'][$result->speaking] = isset($language[$result->language]['Speaking'][$result->speaking]) ? $language[$result->language]['Speaking'][$result->speaking] + 1 : 1;
            $language[$result->language]['Writing'][$result->writing] = isset($language[$result->language]['Writing'][$result->writing]) ? $language[$result->language]['Writing'][$result->writing] + 1 : 1;
        }
        if (count($language) > 0) {
            $charts['candidate_details'][] = Charts::multi('bar', 'highcharts')
                ->title("Candidates by Level of Language Fluency (French)")
                ->dimensions(0, 400) // Width x Height
                ->template("material")
                ->elementLabel('Proficiency')
                ->dataset('Fluent', [
                    $language['French']['Reading']['C - Fluent - this is my native language.'],
                    $language['French']['Writing']['C - Fluent - this is my native language.'],
                    $language['French']['Speaking']['C - Fluent - this is my native language.']])
                ->dataset('Functional', [
                    $language['French']['Reading']['B - Functional - this is my second language but I can get by.'],
                    $language['French']['Writing']['B - Functional - this is my second language but I can get by.'],
                    $language['French']['Speaking']['B - Functional - this is my second language but I can get by.']])
                ->dataset('Limited', [
                    $language['French']['Reading']['A - Limited - I am just learning the language.'],
                    $language['French']['Writing']['A - Limited - I am just learning the language.'],
                    $language['French']['Speaking']['A - Limited - I am just learning the language.'],
                ])
                ->labels(['Reading', 'Writing', 'Speaking']);
        }

        /* Special Skills */
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
//dd($results, $special_skills);
        $charts['candidate_details'][] = Charts::multi('bar', 'highcharts')
            ->title("Candidates Skills (Computer)")
            ->dimensions(0, 400) // Width x Height
            ->template("material")
            ->elementLabel('Proficiency')
            ->dataset('No Knowledge', $special_skills['No Knowledge'])
            ->dataset('Basic Knowledge', $special_skills['Basic Knowledge'])
            ->dataset('Good Knowledge', $special_skills['Good Knowledge'])
            ->dataset('Advanced Knowledge', $special_skills['Advanced Knowledge'])
            ->dataset('Expert Knowledge', $special_skills['Expert Knowledge'])
            ->labels(array_keys($special_skills['No Knowledge']));

        /* Soft Skills */
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
        $charts['candidate_details'][] = Charts::multi('bar', 'highcharts')
            ->title("Candidates Skills (Soft Skills)")
            ->dimensions(0, 400) // Width x Height
            ->template("material")
            ->elementLabel('Proficiency')
            ->dataset('No Knowledge', $soft_skills['No Knowledge'])
            ->dataset('Basic Knowledge', $soft_skills['Basic Knowledge'])
            ->dataset('Good Knowledge', $soft_skills['Good Knowledge'])
            ->dataset('Advanced Knowledge', $soft_skills['Advanced Knowledge'])
            ->dataset('Expert Knowledge', $soft_skills['Expert Knowledge'])
            ->labels(array_keys($soft_skills['No Knowledge']));

        /* Employement Entities */
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
        $charts['candidate_details'][] = Charts::create('bar', 'highcharts')
            ->title('Employment Entities')
            ->dimensions(0, 400) // Width x Height
            ->template("material")
            ->elementLabel('Total')
            ->values(array_values($elements))
            ->labels(array_keys($elements));

        /* Tab#3-End */

        /* Tab#4-Start */
        /* Candidates By Military Experience */
        $sql = "select cm.veteran_of_armedforce as label,count(*) as total
                    from candidate_jobs cj
                    join candidate_miscellaneouses cm
                    on cj.candidate_id=cm.candidate_id
                    where cj.status='Applied'
                    AND cj.deleted_at IS NULL
                    group by cm.veteran_of_armedforce";
        $charts['candidate_screen'][] = $this->prepareChart($sql, "Candidates by Military Experience", "Military Experience");

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
        $charts['candidate_screen'][] = Charts::create('bar', 'highcharts')
            ->title('Fired/Convicted Candidates')
            ->dimensions(0, 400) // Width x Height
            ->template("material")
            ->elementLabel('Total')
            ->values(array_values($elements))
            ->labels(array_keys($elements));

        /* Candidates By Career Interset In CGL */
        $sql = "select cm.career_interest as label,count(*) as total
                    from candidate_jobs cj
                    join candidate_miscellaneouses cm
                    on cj.candidate_id=cm.candidate_id
                    where cj.status='Applied'
                    AND cj.deleted_at IS NULL
                    group by cm.career_interest";
        $charts['candidate_screen'][] = $this->prepareChart($sql, "Candidates by Career Interset in CGL", "Career Interset in CGL");

        /* Candidates By Average Score */
        $sql = "select csql.category as label,ROUND(AVG(csq.score),2) as total
                    from candidate_screening_question_lookups csql
                    join candidate_screening_questions csq
                    on csq.question_id=csql.id
                    group by csql.category";
        $charts['candidate_screen'][] = $this->prepareChart($sql, "Candidates by Average Score", "Average Score");

        /* Candidates By Loading Documents */
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
        $charts['candidate_screen'][] = Charts::create('bar', 'highcharts')
            ->title('Loading Documents')
            ->dimensions(0, 400) // Width x Height
            ->template("material")
            ->elementLabel('Loading Documents')
            ->values(array_values($elements))
            ->labels(array_keys($elements));

        /* Candidates By Average Cycle Time */
        $sql = "select tpl.process_steps as label,count(*) as total
                    from candidate_trackings ct
                    join tracking_process_lookups tpl
                    on ct.lookup_id=tpl.id
                    group by tpl.process_steps";
        $charts['candidate_screen'][] = $this->prepareChart($sql, "Average Cycle Time", "Average Cycle Time");

        /* Tab#4-End */
        return view('hranalytics::dashboard.index', ['charts' => $charts]);
    }

    /**
     * Undocumented function
     *
     * @param [type] $type
     * @param Request $request
     * @return void
     */
    public function drillDown($type = null, Request $request)
    {
        /* Job Requisitions */
        switch ($type) {
            case 'job-requisitions':
                //$area_managers = $this->userRepository->getUsers('area_manager')->toArray();
                $area_managers = $this->userRepository->getUserLookup(['area_manager']);
                $area_managers = array_combine($area_managers, $area_managers);
                $viewer = Job::select(\DB::raw("status as status,count(*) as count"))
                    ->when($request != null, function ($query) use ($request) {
                        if (!empty($request->get('area_manager'))) {
                            $query->where('area_manager', '=', $request->get('area_manager'));
                        }
                    })
                    ->when($request != null, function ($query) use ($request) {
                        if (!empty($request->get('type'))) {
                            $query->where('reason_id', '=', $request->get('type'));
                        }
                    })
                    ->when($request != null, function ($query) use ($request) {
                        if (!empty($request->get('start_date'))) {
                            $query->whereDate('created_at', '>=', $request->get('start_date'));
                        }
                    })
                    ->when($request != null, function ($query) use ($request) {
                        if (!empty($request->get('end_date'))) {
                            $query->whereDate('created_at', '<=', $request->get('end_date'));
                        }
                    })
                    ->when($request != null, function ($query) use ($request) {
                        if (!empty($request->get('job_status'))) {
                            $query->where('status', '=', $request->get('job_status'));
                        }
                    })
                //->orderBy("created_at")
                    ->groupBy(\DB::raw("status"))
                    ->get()->toArray();
                $status = $viewer;
                $viewer = array_column($viewer, 'count');
                $status = array_column($status, 'status');
                if ($request->get('flag') === null) {

                    return view('hranalytics::dashboard.charts.job-drilldown', ['title' => 'Job Requisitions', 'area_managers' => $area_managers])
                        ->with('viewer', json_encode($viewer, JSON_NUMERIC_CHECK))
                        ->with('status', json_encode($status));
                } else {

                    $htmlview = View::make('hranalytics::dashboard.charts.job-drilldown', ['title' => 'Job Requisitions', 'area_managers' => $area_managers])->with('viewer', json_encode($viewer, JSON_NUMERIC_CHECK))
                        ->with('status', json_encode($status))->render();

                    return json_encode(compact('htmlview', 'viewer', 'status'));

                }

                break;
            case 'candidate-certificates':
                //$area_managers = $this->userRepository->getUsers('area_manager')->toArray();
                $area_managers = $this->userRepository->getUserLookup(['area_manager']);
                $query = CandidateSecurityGuardingExperince::select(\DB::raw("guard_licence as label,count(*) as total"))
                    ->join('candidate_jobs', 'candidate_security_guarding_experinces.candidate_id', '=', 'candidate_jobs.candidate_id')
                    ->join('jobs', 'jobs.id', '=', 'candidate_jobs.job_id')
                    ->when($request != null, function ($query) use ($request) {
                        if (!empty($request->get('license_expiry_from'))) {
                            $query->where(function ($query) use ($request) {
                                return $query->whereBetween('expiry_guard_license', [$request->get('license_expiry_from'), $request->get('license_expiry_to')])
                                    ->orWhereBetween('expiry_first_aid', [$request->get('license_expiry_from'), $request->get('license_expiry_to')])
                                    ->orWhereBetween('expiry_cpr', [$request->get('license_expiry_from'), $request->get('license_expiry_to')]);
                            });

                        }
                    })
                    ->where('candidate_jobs.status', '=', 'Applied')
                    ->where('candidate_jobs.candidate_status', '=', 'Proceed')
                    ->whereNull('candidate_jobs.deleted_at')
                    ->whereNull('jobs.deleted_at')
                    ->groupBy(\DB::raw("guard_licence"))
                    ->get()->toArray();
                $query1 = $query;
                // $query = \DB::select(\DB::raw($sql));
                $total = array_column($query, 'total');
                $label = array_column($query1, 'label');
                if ($request->get('flag1') === null) {
                    return view('hranalytics::dashboard.charts.candidate-certificate-drilldown', ['title' => 'Candidate Certificates', 'area_managers' => $area_managers])
                        ->with('total', json_encode($total, JSON_NUMERIC_CHECK))
                        ->with('label', json_encode($label));
                } else {
                    $htmlview = View::make('hranalytics::dashboard.charts.job-drilldown', ['title' => 'Candidate Certificates', 'area_managers' => $area_managers])->with('total', json_encode($total, JSON_NUMERIC_CHECK))
                        ->with('label', json_encode($label))->render();
                    return json_encode(compact('htmlview', 'total', 'label'));
                }
                break;
        }
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return void
     */
    public function getJobList(Request $request)
    {
        return datatables()->of($this->jobRepository->getJobs($job_status = null, $filter = $request))->toJson();
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return void
     */
    public function getCandidateCertificateList(Request $request)
    {
        return datatables()->of($this->candidateRepository->getCandidates(null, $request))->toJson();
    }

}
