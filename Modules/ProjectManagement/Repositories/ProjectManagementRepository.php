<?php

namespace Modules\ProjectManagement\Repositories;

use App\Repositories\MailQueueRepository;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\CustomerTemplateEmail;
use Modules\Admin\Models\EmailNotificationType;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\ProjectManagement\Models\PmTask;
use Modules\ProjectManagement\Models\PmTaskStatus;
use Modules\ProjectManagement\Models\PmTaskUpdateInterval;
use Modules\ProjectManagement\Repositories\TaskOwnerRepository;

class ProjectManagementRepository
{

    /**
     * The Model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $pmTask, $customerRepository, $pmTaskUpdateInterval, $mailQueueRepository, $employeeAllocationrepository, $taskOwnerRepository;

    /**
     * Create a new Model instance.
     *
     * @param  \App\Models\WorkType $workTypeModel
     */
    public function __construct(CustomerRepository $customerRepository, PmTask $pmTask, PmTaskUpdateInterval $pmTaskUpdateInterval, MailQueueRepository $mailQueueRepository, PmTaskStatus $pmTaskStatus, CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository, EmployeeAllocationRepository $employeeAllocationrepository, User $userModel, UserRepository $userRepository, TaskOwnerRepository $taskOwnerRepository)
    {
        $this->customerRepository = $customerRepository;
        $this->pmTask = $pmTask;
        $this->pmTaskUpdateInterval = $pmTaskUpdateInterval;
        $this->MailQueueRepository = $mailQueueRepository;
        $this->pmTaskStatus = $pmTaskStatus;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->employeeAllocationRepository = $employeeAllocationrepository;
        $this->usermodel = $userModel;
        $this->userRepository = $userRepository;
        $this->taskOwnerRepository = $taskOwnerRepository;
    }

    /**
     * Send Notifictaion  mail in interval all Groups
     *
     * @param empty
     * @return array
     */
    public function sendNotification()
    {
        $pending_task = $this
            ->pmTask
            ->where('is_completed', 0)
            ->whereDate('due_date', '>=', date("Y-m-d"))
            ->whereHas('customer')
            ->whereHas('taskOwners')
            ->with('projects', 'taskOwners', 'followers', 'createdBy')
            ->get();
        $task_due_type = EmailNotificationType::where('type', 'project_management_task_due_reminder')->first();
        $status_due_type = EmailNotificationType::where('type', 'project_management_status_due_reminder')->first();
        $task_due_customers = [];
        $status_due_customers = [];
        if ($task_due_type != null) {
            $task_due_customers = CustomerTemplateEmail::where('template_id', $task_due_type->id)
                ->pluck('customer_id')
                ->toArray();
        }
        if ($status_due_type != null) {
            $status_due_customers = CustomerTemplateEmail::where('template_id', $status_due_type->id)
                ->pluck('customer_id')
                ->toArray();
        }
        $interval = $this
            ->pmTaskUpdateInterval
            ->pluck('interval', 'id')
            ->toArray();
        foreach ($pending_task as $each_task) {
            $current = date_create(\Carbon::now()->toDateString());
            $due_date = date_create($each_task->due_date);
            $diff = date_diff($due_date, $current);
            if (in_array($diff->days, $interval) || $current == $due_date) {
                $customerObject = $this
                    ->customerRepository
                    ->getSingleCustomer($each_task->site_id);

                $taskOwners = $each_task->taskOwners;
                $followers = $each_task->followers;

                //send mail to task owners
                if (!empty($taskOwners)) {
                    foreach ($taskOwners as $taskOwner) {
                        if ($taskOwner->userWithOutTrashed) {
                            if (!empty($customerObject)) {
                                $helper_variable = array(
                                    '{receiverFullName}' => $taskOwner->userWithOutTrashed->full_name,
                                    '{receiverEmployeeNumber}' => $taskOwner->userWithOutTrashed->employee->employee_no,
                                    '{loggedInUser}' => $each_task->createdBy->full_name, //Author Name
                                    '{loggedInUserEmployeeNumber}' => $each_task->createdBy->employee->employee_no, //Author EMployee Number
                                    '{reportUrl}' => route('pm.report', ['taskId' => $each_task->unique_key]),
                                    '{taskUrl}' => route('pm.report', ['taskId' => $each_task->unique_key]),
                                    '{client}' => $customerObject->client_name,
                                    '{projectNumber}' => $customerObject->project_number,
                                    '{projectName}' => isset($each_task->projects) ? $each_task->projects->name : '',
                                    '{groupName}' => isset($each_task->groupDetails) ? $each_task->groupDetails->name : '',
                                    '{taskName}' => $each_task->name,
                                    '{dueInDays}' => $diff->days,
                                    '{dueDate}' => date_format(date_create($each_task->due_date), "j F, Y"),
                                );
                            }
                            if (in_array($each_task->site_id, $task_due_customers)) {
                                $this->MailQueueRepository->prepareMailTemplate("project_management_task_due_reminder", $each_task->site_id, $helper_variable, "project_management_task_due_reminder", 0, $taskOwner->user_id);
                            }
                        }
                    }
                }

                //send mail to followers
                if (!empty($followers)) {
                    foreach ($followers as $follower) {
                        if ($follower->userWithOutTrashed) {
                            if (!empty($customerObject)) {
                                $helper_variable = array(
                                    '{receiverFullName}' => $follower->userWithOutTrashed->full_name,
                                    '{receiverEmployeeNumber}' => $follower->userWithOutTrashed->employee->employee_no,
                                    '{loggedInUser}' => $each_task->createdBy->full_name, //Author Name
                                    '{loggedInUserEmployeeNumber}' => $each_task->createdBy->employee->employee_no, //Author EMployee Number
                                    '{reportUrl}' => route('pm.report', ['taskId' => $each_task->unique_key]),
                                    '{taskUrl}' => route('pm.report', ['taskId' => $each_task->unique_key]),
                                    '{client}' => $customerObject->client_name,
                                    '{projectNumber}' => $customerObject->project_number,
                                    '{projectName}' => isset($each_task->projects) ? $each_task->projects->name : '',
                                    '{groupName}' => isset($each_task->groupDetails) ? $each_task->groupDetails->name : '',
                                    '{taskName}' => $each_task->name,
                                    '{dueInDays}' => $diff->days,
                                    '{dueDate}' => date_format(date_create($each_task->due_date), "j F, Y"),
                                );
                            }
                            if (in_array($each_task->site_id, $task_due_customers)) {
                                $this->MailQueueRepository->prepareMailTemplate("project_management_task_due_reminder", $each_task->site_id, $helper_variable, "project_management_task_due_reminder", 0, $follower->user_id);
                            }
                        }
                    }
                }

                if (in_array($each_task->site_id, $status_due_customers)) {
                    if ($this
                        ->pmTaskStatus
                        ->where('task_id', $each_task->id)
                        ->where('percentage', '>', 0)
                        ->count() == 0) {
                        $this
                            ->MailQueueRepository
                            ->prepareMailTemplate("project_management_status_due_reminder", $each_task->site_id, $helper_variable, "project_management_status_due_reminder");
                    }
                }
            }
        }
        return true;
    }

    public function getDataBasedOnPermissions($request)
    {
        $project_id = $request['project_id'];
        $customer_id = $request['client_id'];
        $user_id = $request['user_id'];
        $startdate = $request['startdate'];
        $enddate = $request['enddate'];
        $status_from = $request['status_from'];
        $status_to = $request['status_to'];
        $q = Customer::whereHas('projects')->with(['projects' => function ($query) {
            $query->withCount(['taskList as total_tasks']);
        }
            , 'projects.groups' => function ($query) {
                $query->select('id', 'name', 'project_id');
            }
            , 'projects.tasks' => function ($query) {
                $query->select('id', 'name', 'followers_can_update', 'project_id', 'site_id', 'is_completed', \DB::raw('DATE(`due_date`) as due_date'), \DB::raw('DATE_FORMAT(due_date,"%W %M %e, %Y") as due_date_formatted'), \DB::raw('DATE_FORMAT(updated_at, "%M %d, %Y") as updated_at_formatted'), \DB::raw('DATE_FORMAT(completed_date,"%M %d, %Y") as completed_date_formatted'), \DB::raw('DATE_FORMAT(due_date,"%W") as due_date_day'), \DB::raw('DATE_FORMAT(due_date,"%M %e, %Y") as due_date_std'));
            }
            , 'projects.groups.tasks.taskOwners.user' => function ($query) {
                $query->select('*');
            }
            , 'projects.groups.tasks.followers.user' => function ($query) {
                $query->select('*');
            }
            , 'projects.groups.tasks.createdBy' => function ($query) {
                $query->select('*');
            }
            , 'projects.groups.tasks.status' => function ($query) {
                $query->select('id', \DB::raw('TIME_FORMAT(TIME(`status_date`),"%h:%i:%s %p") as time'), \DB::raw('DATE_FORMAT(status_date, "%M %d, %Y") as formatted'), \DB::raw('DATEDIFF(NOW(),status_date) as days'), 'task_id', 'percentage', 'notes', \DB::raw('DATE_FORMAT(status_date,"%W %M %e, %Y") as status_date_formatted'), 'updated_by')->with(['updatedBy']);
            }
            , 'projects.groups.tasks' => function ($query) {
                $query->select('name', 'id', 'followers_can_update', 'project_id', 'group_id', 'is_completed', 'created_at', 'updated_at', 'created_by', \DB::raw('DATE_FORMAT(updated_at, "%M %d, %Y") as updated_at_formatted'), \DB::raw('DATE(`due_date`) as due_date'), \DB::raw('DATE_FORMAT(due_date,"%W %M %e, %Y") as due_date_formatted'), \DB::raw('DATE_FORMAT(completed_date,"%M %d, %Y") as completed_date_formatted'), \DB::raw('DATE_FORMAT(due_date,"%W") as due_date_day'), \DB::raw('DATE_FORMAT(due_date,"%M %e, %Y") as due_date_std'));
            }
            ,
        ]);
        if (\Auth::user()
            ->can('view_all_reports')) {
            $q = $q;
        } elseif (\Auth::user()->can('view_allocated_customer_reports')) {
            $allocated_customers = $this
                ->customerEmployeeAllocationRepository
                ->getDirectAllocatedCustomers(\Auth::user());
            $q = $q->whereIn('id', $allocated_customers);
        } elseif (\Auth::user()->can('view_assigned_reports')) {
            $allocatedTasks = $this
                ->taskOwnerRepository
                ->getAllocatedTaskIds([\Auth::user()->id]);

            $user_id = [\Auth::user()->id];

            $q->withAndWhereHas('projects', function ($q) use ($allocatedTasks) {
                $q->withAndWhereHas('groups', function ($q) use ($allocatedTasks) {
                    $q->withAndWhereHas('tasks', function ($q) use ($allocatedTasks) {
                        $q->select('name', 'id', 'project_id', 'group_id', 'is_completed', 'created_by', 'created_at', 'updated_at', \DB::raw('DATE(`due_date`) as due_date'), \DB::raw('DATE_FORMAT(updated_at, "%M %d, %Y") as updated_at_formatted'), \DB::raw('DATE_FORMAT(due_date,"%W %M %e, %Y") as due_date_formatted'), \DB::raw('DATE_FORMAT(completed_date,"%M %d, %Y") as completed_date_formatted'), \DB::raw('DATE_FORMAT(due_date,"%W") as due_date_day'), \DB::raw('DATE_FORMAT(due_date,"%M %e, %Y") as due_date_std'))
                            ->whereIn('id', $allocatedTasks);
                    });
                });
            });
        }
        return $this->getAllfilteredData($project_id, $customer_id, $user_id, $startdate, $enddate, $status_from, $status_to, $q);
    }

    /**
     * Filter
     *
     * @param $project_id
     * @param $customer_id
     * @param $user_id
     * @param $startdate
     * @param $enddate
     * @param $status
     * @return array
     */
    public function getAllfilteredData($project_id = null, $customer_id = null, $user_id = null, $startdate = null, $enddate = null, $status_from = null, $status_to = null, $q)
    {

        $statusFilteredTaskArr = array();
        if ($status_from !== null || $status_to !== null) {
            $statusFilteredTaskArrQuery = PmTaskStatus::select('task_id')->groupBy('task_id');
            if ($status_from == null) {
                $statusFilteredTaskArrQuery->havingRaw("max(percentage) <= ?", [$status_to]);
            } elseif ($status_to == null) {
                $statusFilteredTaskArrQuery->havingRaw("max(percentage) >= ?", [$status_from]);
            } else {
                $statusFilteredTaskArrQuery->havingRaw("max(percentage) >= ? AND max(percentage) <= ?", [$status_from, $status_to]);
            }
            $statusFilteredTaskArr = $statusFilteredTaskArrQuery->pluck('task_id')
                ->toArray();
        }

        $allocatedTasks = [];
        if (!empty($user_id)) {
            $allocatedTasks = $this->taskOwnerRepository->getAllocatedTaskIds([$user_id]);
        }

        $query = $q;
        $q->when(($customer_id !== null), function ($query) use ($customer_id) {
            $query->withAndWhereHas('projects', function ($query) use ($customer_id) {
                $query->where('customer_id', $customer_id)->withCount(['taskList as total_tasks']);
            });
        });

        $q->when(($startdate !== null || $enddate !== null || $user_id !== null || $status_from !== null || $status_to !== null || $project_id !== null), function ($q) use ($startdate, $enddate, $user_id, $status_from, $status_to, $project_id, $statusFilteredTaskArr, $allocatedTasks) {
            $q->with(['projects.groups' => function ($q) use ($startdate, $enddate, $user_id, $status_from, $status_to, $project_id, $statusFilteredTaskArr, $allocatedTasks) {
                $q->when(($project_id !== null), function ($query) use ($project_id) {
                    $query->where('project_id', $project_id);
                });

                $q->withAndWhereHas('tasks', function ($q) use ($startdate, $enddate, $user_id, $status_from, $status_to, $statusFilteredTaskArr, $allocatedTasks) {
                    $q->select('name', 'id', 'project_id', 'group_id', 'is_completed', 'created_at', 'updated_at', 'created_by', \DB::raw('DATE(`due_date`) as due_date'), \DB::raw('DATE_FORMAT(updated_at, "%M %d, %Y") as updated_at_formatted'), \DB::raw('DATE_FORMAT(due_date,"%W %M %e, %Y") as due_date_formatted'), \DB::raw('DATE_FORMAT(completed_date,"%M %d, %Y") as completed_date_formatted'), \DB::raw('DATE_FORMAT(due_date,"%W") as due_date_day'), \DB::raw('DATE_FORMAT(due_date,"%M %e, %Y") as due_date_std'));
                    $q->when($startdate !== null || $enddate !== null, function ($query) use ($startdate, $enddate, $user_id) {
                        $query->select('name', 'id', 'project_id', 'group_id', 'is_completed', 'created_by', 'created_at', 'updated_at', \DB::raw('DATE(`due_date`) as due_date'), \DB::raw('DATE_FORMAT(due_date,"%W %M %e, %Y") as due_date_formatted'), \DB::raw('DATE_FORMAT(due_date,"%W") as due_date_day'), \DB::raw('DATE_FORMAT(updated_at, "%M %d, %Y") as updated_at_formatted'), \DB::raw('DATE_FORMAT(completed_date,"%M %d, %Y") as completed_date_formatted'), \DB::raw('DATE_FORMAT(due_date,"%M %e, %Y") as due_date_std'));
                        if ($startdate == null) {
                            $query->whereBetween(\DB::raw('DATE(due_date)'), array(
                                \Carbon::today(),
                                $enddate,
                            ));
                        } elseif ($enddate == null) {
                            $query->where(\DB::raw('DATE(due_date)'), '>=', $startdate);
                        } else {
                            $query->whereBetween(\DB::raw('DATE(due_date)'), array(
                                $startdate,
                                $enddate,
                            ));
                        }
                    });
                    // }]);

                    $q->when(($user_id !== null), function ($query) use ($user_id, $allocatedTasks) {
                        //$query->with(['projects.groups'=>function ($query) use ($user_id){
                        // $query->withAndWhereHas('tasks', function ($query) use ($user_id) {
                        $query->select('name', 'id', 'followers_can_update', 'project_id', 'group_id', 'is_completed', 'created_by', 'created_at', 'updated_at', \DB::raw('DATE(`due_date`) as due_date'), \DB::raw('DATE_FORMAT(due_date,"%W %M %e, %Y") as due_date_formatted'), \DB::raw('DATE_FORMAT(updated_at, "%M %d, %Y") as updated_at_formatted'), \DB::raw('DATE_FORMAT(completed_date,"%M %d, %Y") as completed_date_formatted'), \DB::raw('DATE_FORMAT(due_date,"%W") as due_date_day'), \DB::raw('DATE_FORMAT(due_date,"%M %e, %Y") as due_date_std'))
                            ->whereIn('id', $allocatedTasks);
                        // });
                    });
                    $q->when(($status_from !== null || $status_to !== null), function ($q) use ($status_from, $status_to, $statusFilteredTaskArr) {
                        $q->withAndWhereHas('status', function ($query) use ($status_from, $status_to, $statusFilteredTaskArr) {
                            $query->select('id', \DB::raw('TIME_FORMAT(TIME(`status_date`),"%h:%i:%s %p") as time'), \DB::raw('DATE_FORMAT(status_date, "%M %d, %Y") as formatted'), \DB::raw('DATEDIFF(NOW(),status_date) as days'), \DB::raw('DATE_FORMAT(updated_at, "%M %d, %Y") as updated_at_formatted'), 'task_id', 'percentage', 'notes', \DB::raw('DATE_FORMAT(status_date,"%W %M %e, %Y") as status_date_formatted'), 'updated_by')->with(['updatedBy'])
                                ->whereIn('task_id', $statusFilteredTaskArr);
                        });
                    });
                });
            },
            ]);
        });

        //Total task count in customer relation
        $query->when(($startdate !== null || $enddate !== null || $user_id !== null || $status_from !== null || $status_to !== null || $project_id !== null || $customer_id !== null), function ($query) use ($startdate, $enddate, $user_id, $status_from, $status_to, $project_id, $customer_id, $statusFilteredTaskArr, $allocatedTasks) {
            $query->withCount(['task as total_tasks' => function ($q) use ($startdate, $enddate, $user_id, $status_from, $status_to, $project_id, $customer_id, $statusFilteredTaskArr, $allocatedTasks) {

                $q->when(($customer_id !== null), function ($query) use ($customer_id) {
                    $query->whereHas('projects', function ($query) use ($customer_id) {
                        $query->where('customer_id', $customer_id);
                    });
                });

                $q->when(($project_id !== null), function ($query) use ($project_id) {
                    $query->where('project_id', $project_id);
                });
                $q->when(($startdate !== null || $enddate !== null), function ($query) use ($startdate, $enddate) {
                    if ($startdate == null) {
                        $query->whereBetween(\DB::raw('DATE(due_date)'), array(
                            \Carbon::today(),
                            $enddate,
                        ));
                    } elseif ($enddate == null) {
                        $query->where(\DB::raw('DATE(due_date)'), '>=', $startdate);
                    } else {
                        $query->whereBetween(\DB::raw('DATE(due_date)'), array(
                            $startdate,
                            $enddate,
                        ));
                    }
                    //}]);
                });
                $q->when(($user_id !== null), function ($query) use ($user_id, $allocatedTasks) {
                    $query->whereIn('id', $allocatedTasks);
                });
                $q->when(($status_from !== null || $status_to !== null), function ($query) use ($status_from, $status_to, $statusFilteredTaskArr) {
                    $query->whereHas('status', function ($query) use ($status_from, $status_to, $statusFilteredTaskArr) {
                        // $query->with(['status' => function ($query) use ($status_from, $status_to) {
                        $query->whereIn('task_id', $statusFilteredTaskArr);

                        //}]);
                    });
                });
            },
            ]);
        });

        //Total task count in project relation
        $query->when(($startdate !== null || $enddate !== null || $user_id !== null || $status_from !== null || $status_to !== null || $project_id !== null || $customer_id !== null), function ($query) use ($startdate, $enddate, $user_id, $status_from, $status_to, $project_id, $customer_id, $statusFilteredTaskArr, $allocatedTasks) {
            $query->with(['projects' => function ($query) use ($startdate, $enddate, $user_id, $status_from, $status_to, $project_id, $customer_id, $statusFilteredTaskArr, $allocatedTasks) {
                $query->withCount(['taskList as total_tasks' => function ($q) use ($startdate, $enddate, $user_id, $status_from, $status_to, $project_id, $customer_id, $statusFilteredTaskArr, $allocatedTasks) {
                    $q->when(($customer_id !== null), function ($query) use ($customer_id) {
                        $query->where('customer_id', $customer_id);
                    });
                    $q->when(($project_id !== null), function ($query) use ($project_id) {
                        $query->where('project_id', $project_id);
                    });
                    $q->when(($startdate !== null || $enddate !== null), function ($query) use ($startdate, $enddate) {
                        if ($startdate == null) {
                            $query->whereBetween(\DB::raw('DATE(due_date)'), array(
                                \Carbon::today(),
                                $enddate,
                            ));
                        } elseif ($enddate == null) {
                            $query->where(\DB::raw('DATE(due_date)'), '>=', $startdate);
                        } else {
                            $query->whereBetween(\DB::raw('DATE(due_date)'), array(
                                $startdate,
                                $enddate,
                            ));
                        }
                    });
                    $q->when(($user_id !== null), function ($query) use ($user_id, $allocatedTasks) {
                        $query->whereIn('id', $allocatedTasks);
                    });
                    $q->when(($status_from !== null || $status_to !== null), function ($query) use ($status_from, $status_to, $statusFilteredTaskArr) {
                        $query->whereHas('status', function ($query) use ($status_from, $status_to, $statusFilteredTaskArr) {

                            $query->whereIn('task_id', $statusFilteredTaskArr);
                        });
                    });
                },
                ]);
            },
            ]);
        });
        //  });

        if ($startdate == null && $enddate == null && $status_from == null && $status_to == null && $user_id == null && $customer_id == null) {
            $q = $q->select('id', 'client_name', 'project_number')
                ->withCount('task as total_tasks')->with(['projects' => function ($query) {
                    $query->withCount('taskList as total_tasks');
                },
                ]);
        }

        return $q->orderBy('project_number')
            ->get();
    }

    public function employeeLookUps()
    {
        $user_list = array();
        if ((\Auth::user()->can('super_admin')) || (\Auth::user()
            ->can('super_admin'))) {
            $user_list = $this
                ->userRepository
                ->getUserLookup(null, ['admin', 'super_admin'], null, true, null, true)
                ->orderBy('first_name', 'asc')
                ->get();
        } else {
            $employees = $this
                ->employeeAllocationRepository
                ->getEmployeeIdAssigned(\Auth::user()
                        ->id);
                $user_list = $this
                    ->usermodel
                    ->orderBy('first_name')->whereIn('id', $employees)->get();
        }
        return $user_list;
    }
}
