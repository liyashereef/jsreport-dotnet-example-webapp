<?php

namespace Modules\ProjectManagement\Repositories;

use Modules\ProjectManagement\Models\PmTask;
use Modules\Admin\Models\EmailNotificationType;
use Modules\Admin\Models\CustomerTemplateEmail;
use Modules\Admin\Repositories\CustomerRepository;
use App\Repositories\MailQueueRepository;
use Modules\ProjectManagement\Models\PmRatingTolerance;
use Modules\ProjectManagement\Repositories\TaskOwnerRepository;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\EmployeeRatingLookupRepository;
use \Carbon\Carbon;

class TaskRepository
{

    /**
     * The Model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model,$taskOwnerRepository;


    public function __construct(PmTask $task, CustomerRepository $customerRepository, MailQueueRepository $mailQueueRepository, PmRatingTolerance $pmRatingTolerance, TaskOwnerRepository $taskOwnerRepository, EmployeeRatingLookupRepository $employeeRatingLookupRepository)
    {
        $this->model = $task;
        $this->customerRepository = $customerRepository;
        $this->MailQueueRepository = $mailQueueRepository;
        $this->pmRatingTolerance = $pmRatingTolerance;
        $this->taskOwnerRepository = $taskOwnerRepository;
        $this->employeeRatingLookupRepository = $employeeRatingLookupRepository;
    }

    /**
     * Get all Task
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Get all Tasks of a group
     */
    public function getByGroup($groupId)
    {
        return $this->model
            ->with(['status', 'taskOwners.userWithOutTrashed', 'followers.userWithOutTrashed', 'rating'])
            ->where('group_id', '=', $groupId)
            ->get();
    }

    /**
     * Get all Tasks of a project
     */
    public function getByProject($groupId)
    {
        return $this->model
            ->with(['status', 'taskOwners'])
            ->where('project_id', '=', $groupId)
            ->get();
    }

    /**
     * Get all Tasks of a site
     */
    public function getBySite($groupId)
    {
        return $this->model
            ->with(['status', 'taskOwners'])
            ->where('site_id', '=', $groupId)
            ->get();
    }

    /**
     * Get all Tasks based on Unique ID
     */
    public function getByTaskUniqueId($uniqueId)
    {
        return $this->model
            ->where('unique_key', '=', $uniqueId)
            ->first();
    }

    /**
     * Get single Task details
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->with('taskOwners.userWithOutTrashed', 'followers.userWithOutTrashed', 'createdBy', 'projectsWithTrashed', 'groupDetailsWithTrashed')->find($id);
    }

    /**
     * Get single Task Assigned User list details
     *
     * @param $id
     * @return object
     */
    public function getAllAssignedUsersList()
    {
        return $this->model->with('taskOwners')->get()->pluck('taskOwners.userWithOutTrashed.full_name', 'taskOwners.userWithOutTrashed.id')->toArray();
    }

    /**
     * Store a newly created task in storage.
     *
     * @param  $request
     * @return object
     */
    public function save($data)
    {
        if (isset($data['name'])) {
            $data['name'] = strip_tags($data['name']);
        }
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Remove the specified task from storage.
     *
     * @param  $id
     * @return object
     */
    public function deleteTask($id)
    {
        try {
            if (is_array($id)) {
                \DB::beginTransaction();
                foreach ($id as $key => $taskid) {
                    $task_details = $this->get($taskid);
                    $this->sendTaskMail($task_details, 'project_management_task_deleted');
                }
                \DB::commit();
                return $this->model->whereIn('id', $id)->delete();
            } else {
                \DB::beginTransaction();
                $task_details = $this->get($id);
                $this->sendTaskMail($task_details, 'project_management_task_deleted');
                \DB::commit();
                return $this->model->destroy($id);
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * update the specified task  customer from storage.
     *
     * @param  $id
     * @return object
     */
    public function updateCustomer($data)
    {
        return $this->model->where('project_id', $data['id'])->update(['site_id' => $data['customer_id']]);
    }

    /**
     * send the specified task  as mail
     *
     * @param  $data,$type
     * @return object
     */
    public function sendTaskMail($data, $type)
    {
        $task_started_type = EmailNotificationType::where('type', $type)->first();
        $customer_list = [];
        if ($task_started_type != null) {
            $customer_list = CustomerTemplateEmail::where('template_id', $task_started_type->id)->pluck('customer_id')->toArray();
        }
        $current = date_create(Carbon::now()->toDateString());
        $due_date = date_create($data->due_date);
        $diff = date_diff($due_date, $current);
        $customerObject = $this->customerRepository->getSingleCustomer($data['site_id']);
        $taskOwners = $data->taskOwners;
        $followers = $data->followers;
        if (!empty($customerObject)) {
            //send mail to task owners
            if (!empty($taskOwners)) {
                foreach ($taskOwners as $taskOwner) {
                    if ($taskOwner->userWithOutTrashed) {
                        $helper_variable = array(
                            '{receiverFullName}' => $taskOwner->userWithOutTrashed->full_name,
                            '{receiverEmployeeNumber}' => $taskOwner->userWithOutTrashed->employee->employee_no,
                            '{loggedInUser}' => $data->createdBy->full_name, //Author Name
                            '{loggedInUserEmployeeNumber}' => $data->createdBy->employee->employee_no, //Author EMployee Number
                            '{client}' => $customerObject->client_name,
                            '{projectNumber}' => $customerObject->project_number,
                            '{projectName}' => isset($data->projectsWithTrashed) ? $data->projectsWithTrashed->name : '',
                            '{groupName}' => isset($data->groupDetailsWithTrashed) ? $data->groupDetailsWithTrashed->name : '',
                            '{taskName}' => $data->name,
                            '{dueInDays}' => $diff->days,
                            '{dueDate}' => date_format(date_create($data->due_date), "j F, Y")
                        );

                        if (in_array($data->site_id, $customer_list)) {
                            $this->MailQueueRepository->prepareMailTemplate($type, $data->site_id, $helper_variable, $type, 0, $taskOwner->user_id);
                        }
                    }
                }
            }

            //send mail to followers
            if (!empty($followers)) {
                foreach ($followers as $follower) {
                    if ($follower->userWithOutTrashed) {
                        $helper_variable = array(
                            '{receiverFullName}' => $follower->userWithOutTrashed->full_name,
                            '{receiverEmployeeNumber}' => $follower->userWithOutTrashed->employee->employee_no,
                            '{loggedInUser}' => $data->createdBy->full_name, //Author Name
                            '{loggedInUserEmployeeNumber}' => $data->createdBy->employee->employee_no, //Author EMployee Number
                            '{client}' => $customerObject->client_name,
                            '{projectNumber}' => $customerObject->project_number,
                            '{projectName}' => isset($data->projectsWithTrashed) ? $data->projectsWithTrashed->name : '',
                            '{groupName}' => isset($data->groupDetailsWithTrashed) ? $data->groupDetailsWithTrashed->name : '',
                            '{taskName}' => $data->name,
                            '{dueInDays}' => $diff->days,
                            '{dueDate}' => date_format(date_create($data->due_date), "j F, Y")
                        );

                        if (in_array($data->site_id, $customer_list)) {
                            $this->MailQueueRepository->prepareMailTemplate($type, $data->site_id, $helper_variable, $type, 0, $follower->user_id);
                        }
                    }
                }
            }
        }
        return true;
    }

    /**
     * get rating of  the specified task
     *
     * @param  $data,$type
     * @return object
     */
    public function getRatings($startdate = false, $enddate = false, $project_id, $group_id, $emp_id)
    {
        $logged_in_user_id = \Auth::id();
        $logged_in_user = User::find($logged_in_user_id);
        $list = $this->model->select(
            [
                'pm_tasks.id',
                'deadline_rating_id',
                'value_add_rating_id',
                'initiative_rating_id',
                'commitment_rating_id',
                'complexity_rating_id',
                'efficiency_rating_id',
                'pm_tasks.name',
                'due_date',
                'pm_tasks.project_id',
                'group_id',
                'rating_notes',
                'rated_at',
                'rated_by',
                'average_rating',
                'pm_tasks.created_at'
            ]
        )->selectRaw(
            '(deadline_rating_id + value_add_rating_id + initiative_rating_id+commitment_rating_id+complexity_rating_id+efficiency_rating_id)/6 as average'
        );
        if ($logged_in_user->hasPermissionTo('view_all_performance_reports')) {
            $list = $list->with([
                'projects',
                'groupDetails',
                'taskOwners',
                'deadlineRating',
                'valueAddRating',
                'initiativeRating',
                'commitmentRating',
                'complexityRating',
                'efficiencyRating'
            ]);
        } elseif ($logged_in_user->hasPermissionTo('view_allocated_performance_reports')) {
            $arr_user = [\Auth::User()->id];
            $allocatedcustomers =  $this->customerRepository->getAllAllocatedCustomerId($arr_user);
            $list = $list->whereHas('projects', function ($query) use ($allocatedcustomers) {
                $query->whereIn('customer_id', $allocatedcustomers);
            })
                ->with([
                    'projects',
                    'groupDetails',
                    'taskOwners',
                    'deadlineRating',
                    'valueAddRating',
                    'initiativeRating',
                    'commitmentRating',
                    'complexityRating',
                    'efficiencyRating'
                ]);
        }

        $list->when($startdate, function ($q) use ($startdate) {
            return $q->where('pm_tasks.due_date', '>=', $startdate);
        });
        $list->when($enddate, function ($q) use ($enddate) {
            return $q->whereRaw("CAST(pm_tasks.due_date as DATE) <=?", [$enddate]);
        });
        $list->when($project_id, function ($q) use ($project_id) {
            return $q->whereHas('projects', function ($pq) use ($project_id) {
                return $pq->where('id', $project_id);
            });
        });
        $list->when($group_id, function ($q) use ($group_id) {
            return $q->where('pm_tasks.group_id', $group_id);
        });

        if (!empty($emp_id)) {
            $allocatedTasks = $this->taskOwnerRepository->getAllocatedTaskIds([$emp_id]);
            $list->when($emp_id, function ($q) use ($allocatedTasks) {
                return $q->whereIn('pm_tasks.id', $allocatedTasks);
            });
        }



        return $list;
    }

    /**
     * get pmRatingTolerance
     *
     * @param  null
     * @return object
     */
    public function pmRatingTolerance()
    {
        return $this->pmRatingTolerance->orderBy('rating_id')->pluck('max_value')->toArray();
    }

    /**
     * get rating
     *
     * @param  $avg_score
     * @return object
     */
    public function getScoreRating($avg_score)
    {
        $max_value = $this->pmRatingTolerance();
        $ratingShortNameArr = ['DNME', 'MME', 'ME', 'EE', 'FEE'];
        if (sizeof($ratingShortNameArr) == sizeof($max_value)) {
            for ($i = 0; $i < sizeof($max_value); $i++) {
                if ($avg_score <= $max_value[$i]) {
                    return $ratingShortNameArr[$i];
                }
            }
        }
    }

    /**
     * get rating
     *
     * @param  $avg_score
     * @return object
     */
    public function calculateAverage($data)
    {
        $score=$this->employeeRatingLookupRepository->getScore();
        $result = (($data['deadline_weightage'] / 100) * $score[$data['deadline_rating_id']])
            + (($data['value_add_weightage'] / 100) *  $score[$data['value_add_rating_id']])
            + (($data['initiative_weightage'] / 100) * $score[$data['initiative_rating_id']])
            + (($data['commitment_weightage'] / 100) * $score[$data['commitment_rating_id']])
            + (($data['complexity_weightage'] / 100) * $score[$data['complexity_rating_id']])
            + (($data['efficiency_weightage'] / 100) * $score[$data['efficiency_rating_id']]);
        return $result;
    }
}
