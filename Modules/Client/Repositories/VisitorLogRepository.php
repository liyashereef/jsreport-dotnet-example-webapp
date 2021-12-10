<?php

namespace Modules\Client\Repositories;

use App\Services\HelperService;
use Auth;
use Carbon\Carbon;
use Modules\Admin\Models\User;
use Modules\Client\Models\VisitorLogDetails;

//use Modules\Admin\Models\ClientRepository;

class VisitorLogRepository
{

    protected $visitorLogDetails;

    public function __construct(
        VisitorLogDetails $visitorLogDetails
    ) {
        $this->visitorLogDetails = $visitorLogDetails;
        $this->helper_service = new HelperService();
    }

    public function store($request)
    {

        $visitorLog = VisitorLogDetails::create(
            [
                'customer_id' => $request->get('customer_id'),
                'template_id' => $request->get('template_id'),
                'first_name' => $request->get('first_name'),
                'last_name' => $request->get('last_name'),
                'phone' => $request->get('phone'),
                'email' => $request->get('email'),
                'checkin' => $request->get('checkin'),
                'visitor_type_id' => $request->get('visitor_type_id'),
                'name_of_company' => $request->get('name_of_company'),
                'whom_to_visit' => $request->get('whom_to_visit'),
                'license_number' => $request->get('license_number'),
                'created_by' => \Auth::user()->id,
            ]
        );
        return $visitorLog->id;
    }

    public function storeFromApp($inputs)
    {
        return VisitorLogDetails::create($inputs);
    }

    public function getByuuid($uuid)
    {
        return VisitorLogDetails::where('uuid', $uuid)->first();
    }

    public function update($id, $inputs)
    {
        return VisitorLogDetails::where('id', $id)->update($inputs);
    }

    public function procesPayload($payload)
    {
        $pl = $payload;
        $pl['image'] = null;
        $pl['signature'] = null;
        return json_encode($pl, JSON_NUMERIC_CHECK);
    }

    /**
     * function to prepare and return data for table
     *
     */
    function list($visitor_type_id, $customer_id, $from = false, $to = false, $default = true, $widgetRequest = false)
    {
        $customer_id_array = [];
        if ((!is_array($customer_id)) && ($customer_id != 0)) {
            $customer_id_array[] = $customer_id;
        } else {
            $customer_id_array = $customer_id;
        }

        $admins_arr = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['admin', 'super_admin']);
        })->pluck('id')->toArray();
        $user_arr[] = \Auth::user()->id;

        $query = $this->visitorLogDetails;
        $created_by = null;
        if (!(\Auth::user()->hasAnyPermission(['super_admin']))) {

            if (\Auth::user()->can('create_visitorlog')) {
                $created_by = $user_arr;
            }
            if (\Auth::user()->can('view_allocated_visitorlog')) {
                // $created_by = array_merge($user_arr, $admins_arr);
                $created_by = null;
            }

            if (\Auth::user()->can('view_all_visitorlog')) {
                $created_by = null;
            }
        }

        if (!empty($created_by)) {
            $query = $query->whereIn('created_by', $created_by);
        }

        $query = $query->when(($visitor_type_id != 0), function ($query) use ($visitor_type_id) {
            $query->where('visitor_type_id', $visitor_type_id);
        });

        if ($widgetRequest) {
            if (!empty($customer_id_array)) {
                $query->whereIn('customer_id', $customer_id_array);
            } else {
                if (Auth::user()->can('view_allocated_visitorlog') && (!Auth::user()->can('view_all_visitorlog'))) {
                    $userArr = [Auth::User()->id];
                    $customer_id_array = $this->customerEmployeeAllocationRepository->getAllAllocatedCustomerId($userArr);
                    $query->whereIn('customer_id', $customer_id_array);
                } elseif ((!Auth::user()->can('view_allocated_visitorlog')) && (!Auth::user()->can('view_all_visitorlog'))) {
                    $query->whereIn('customer_id', $customer_id_array);
                }
            }
        } else {
            $query = $query->when((!empty($customer_id_array)), function ($query) use ($customer_id_array) {
                $query->whereIn('customer_id', $customer_id_array);
            });
        }

        if ($from != '' && $to != '') {
            $query = $query->whereDate('checkin', '>=', $from)->whereDate('checkin', '<=', $to);
        } elseif ($default) {
            $query = $query->whereDate('checkin', date("Y/m/d"));
        }

        if ($default) {
            $query = $query->orderBy('checkin', 'desc');
        }
        if ($widgetRequest) {
            $count = config('dashboard.visitor_log_row_limit');
            $query = $query->orderBy('id', 'desc');
            $query->limit($count);
        }
        $query = $query->get();


        return $this->prepareTableList($query);
    }

    /**
     * Function to prepare rows for table
     * @param visitor_log_list VisitorLogDetails
     */
    public function prepareTableList($visitor_log_list)
    {
        $datatable_rows = array();
        foreach ($visitor_log_list as $key => $eachlog) {
            $each_row['id'] = data_get($eachlog, "id");
            $each_row['customer_id'] = data_get($eachlog, "customer_id");
            $each_row['template_id'] = data_get($eachlog, "template_id");
            $each_row['full_name'] = data_get($eachlog, "first_name") . " " . data_get($eachlog, "last_name");
            //  $each_row['phone'] = data_get($eachlog, "phone");
            // $each_row['email'] = data_get($eachlog, "email");

            $each_row['date'] = date('Y-m-d', strtotime(data_get($eachlog, "checkin")));
            $each_row['checkin'] = data_get($eachlog, "checkin") ? date('h:i A', strtotime(data_get($eachlog, "checkin"))) : '--';
            $each_row['checkout'] = data_get($eachlog, "checkout") ? date('h:i A', strtotime(data_get($eachlog, "checkout"))) : '--';
            $each_row['visitor_type'] = data_get($eachlog, "type.type") ?? '--';
            $each_row['name_of_company'] = data_get($eachlog, "name_of_company");
            $each_row['whom_to_visit'] = data_get($eachlog, "whom_to_visit");
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }

    public function CurrentLog($customer_id)
    {
        $user_arr = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['admin', 'super_admin']);
        })->pluck('id')->toArray();
        $query = VisitorLogDetails::where('checkout', null)->where('customer_id', $customer_id)->with('type')->whereDate('checkin', date("Y/m/d"))->orderBy('created_at', 'desc')
            ->when(\Auth::user()->can('create_visitorlog') && \Auth::user()->roles[0]->name != 'super_admin', function ($query) {
                $query->where('created_by', \Auth::user()->id);
            })
            ->when(\Auth::user()->can('view_all_visitorlog'), function ($query) use ($customer_id) {
                $query->orWhereDate('checkin', date("Y/m/d"))->where('checkout', null)->where('customer_id', $customer_id);
            })
            ->when(\Auth::user()->roles[0]->name != 'admin' && \Auth::user()->roles[0]->name != 'super_admin', function ($query) use ($customer_id, $user_arr) {
                $query->orWhereIn('created_by', $user_arr)->where('checkout', null)->whereDate('checkin', date("Y/m/d"))->where('customer_id', $customer_id);
            });

        if (!\Auth::user()->can('view_all_visitorlog') && !\Auth::user()->can('view_allocated_visitorlog') && \Auth::user()->can('create_visitorlog')) {
            $query = VisitorLogDetails::where('checkout', null)->where('customer_id', $customer_id)->with('type')->whereDate('checkin', date("Y/m/d"))->orderBy('created_at', 'desc')
                ->when(\Auth::user()->can('create_visitorlog') && \Auth::user()->roles[0]->name != 'super_admin', function ($query) {
                    $query->where('created_by', \Auth::user()->id);
                });
        }

        return $query;
    }

    public function CheckoutLog($customer_id)
    {

        $user_arr = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['admin', 'super_admin']);
        })->pluck('id')->toArray();
        $query = VisitorLogDetails::whereNotNull('checkout')->where('customer_id', $customer_id)->with('type')->whereDate('checkin', date("Y/m/d"))->orderBy('updated_at', 'desc')
            ->when(\Auth::user()->can('create_visitorlog') && \Auth::user()->roles[0]->name != 'super_admin', function ($query) {
                $query->where('created_by', \Auth::user()->id);
            })
            ->when(\Auth::user()->can('view_all_visitorlog'), function ($query) use ($customer_id) {
                $query->orWhereDate('checkin', date("Y/m/d"))->whereNotNull('checkout')->where('customer_id', $customer_id);
            })
            ->when(\Auth::user()->roles[0]->name != 'admin' && \Auth::user()->roles[0]->name != 'super_admin', function ($query) use ($customer_id, $user_arr) {
                $query->orWhereIn('created_by', $user_arr)->whereNotNull('checkout')->whereDate('checkin', date("Y/m/d"))->where('customer_id', $customer_id);
            });

        if (!\Auth::user()->can('view_all_visitorlog') && !\Auth::user()->can('view_allocated_visitorlog') && \Auth::user()->can('create_visitorlog')) {
            $query = VisitorLogDetails::whereNotNull('checkout')->where('customer_id', $customer_id)->with('type')->whereDate('checkin', date("Y/m/d"))->orderBy('updated_at', 'desc')
                ->when(\Auth::user()->can('create_visitorlog') && \Auth::user()->roles[0]->name != 'super_admin', function ($query) {
                    $query->where('created_by', \Auth::user()->id);
                });
        }

        return $query;
    }

    public function Overstay($customer_id, $overstay, $curr_time)
    {

        $user_arr = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['admin', 'super_admin']);
        })->pluck('id')->toArray();
        $query = VisitorLogDetails::when($overstay !== null, function ($query) use ($overstay) {
            $query->whereTime('checkout', '>', $overstay);
        })->where('customer_id', $customer_id)->with('type')->whereDate('checkin', date("Y/m/d"))
            ->when(\Auth::user()->can('create_visitorlog') && \Auth::user()->roles[0]->name != 'super_admin', function ($query) {
                $query->where('created_by', \Auth::user()->id);
            })
            ->when(\Auth::user()->can('view_all_visitorlog') && $overstay !== null, function ($query) use ($customer_id, $overstay) {
                $query->orwhereTime('checkout', '>', $overstay)->whereDate('checkin', date("Y/m/d"))->where('customer_id', $customer_id);
            })
            ->when(\Auth::user()->roles[0]->name != 'admin' && \Auth::user()->roles[0]->name != 'super_admin' && $overstay !== null, function ($query) use ($customer_id, $user_arr, $overstay) {
                $query->orWhereIn('created_by', $user_arr)->whereTime('checkout', '>', $overstay)->whereDate('checkin', date("Y/m/d"))->where('customer_id', $customer_id);
            });

        if (!\Auth::user()->can('view_all_visitorlog') && !\Auth::user()->can('view_allocated_visitorlog') && \Auth::user()->can('create_visitorlog' && $overstay !== null)) {
            $query = VisitorLogDetails::whereTime('checkout', '>', $overstay)->where('customer_id', $customer_id)->with('type')->whereDate('checkin', date("Y/m/d"))->orderBy('created_at', 'desc')
                ->when(\Auth::user()->can('create_visitorlog') && \Auth::user()->roles[0]->name != 'super_admin', function ($query) {
                    $query->where('created_by', \Auth::user()->id);
                });
        }

        return $query;
    }

    public function getAllVisitorslog($customer_id)
    {
        $user_arr = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['admin', 'super_admin']);
        })->pluck('id')->toArray();
        $query = VisitorLogDetails::with('type')->where('customer_id', $customer_id)->whereDate('checkin', date("Y/m/d"))
            ->when(\Auth::user()->can('create_visitorlog') && \Auth::user()->roles[0]->name != 'super_admin', function ($query) use ($customer_id) {
                $query->where('created_by', \Auth::user()->id)->where('customer_id', $customer_id)->whereDate('checkin', date("Y/m/d"));
            })
            ->when(\Auth::user()->can('view_all_visitorlog'), function ($query) use ($customer_id) {
                $query->orWhere('customer_id', $customer_id)->whereDate('checkin', date("Y/m/d"));
            })
            ->when(\Auth::user()->roles[0]->name != 'admin' && \Auth::user()->roles[0]->name != 'super_admin', function ($query) use ($customer_id, $user_arr) {
                $query->orWhereIn('created_by', $user_arr)->where('customer_id', $customer_id)->whereDate('checkin', date("Y/m/d"));
            });

        if (!\Auth::user()->can('view_all_visitorlog') && !\Auth::user()->can('view_allocated_visitorlog') && \Auth::user()->can('create_visitorlog')) {
            $query = VisitorLogDetails::where('customer_id', $customer_id)->with('type')->whereDate('checkin', date("Y/m/d"))
                ->when(\Auth::user()->can('create_visitorlog') && \Auth::user()->roles[0]->name != 'super_admin', function ($query) {
                    $query->where('created_by', \Auth::user()->id);
                });
        }

        return $query;
    }

    public function getVisitorCountWithFilters()
    {
        $inputs = $this->helper_service->getFMDashboardFilters();
        $visitor = VisitorLogDetails::where(function ($query) use ($inputs) {
            if (!empty($inputs)) {

                //For From date
                if (!empty($inputs['from_date'])) {
                    $query->where('checkin', '>=', $inputs['from_date']);
                }
                //For to date
                if (!empty($inputs['to_date'])) {
                    $query->where('checkin', '<=', $inputs['to_date']);
                }

                //For customer_ids
                $query->whereIn('customer_id', $inputs['customer_ids']);
            }
        })->count();

        if (empty($visitor)) {
            $visitor = 0;
        }
        return $visitor;
    }

    public function getScreenedEntries($inputs)
    { //dd($inputs);

        return VisitorLogDetails::when(isset($inputs['start_date']) && $inputs['start_date'] !== 'null' && !empty($inputs['start_date']), function ($query) use ($inputs) {
            return $query->whereDate('checkin', '>=', $inputs['start_date']);
        })
            ->when(isset($inputs['end_date']) && $inputs['end_date'] !== 'null' && !empty($inputs['end_date']), function ($query) use ($inputs) {
                return $query->whereDate('checkin', '<=', $inputs['end_date']);
            })
            ->when(isset($inputs['customer_id']) && $inputs['customer_id'] !== 'null' && !empty($inputs['customer_id']), function ($query) use ($inputs) {
                return $query->whereIn('customer_id', $inputs['customer_id']);
            })
            ->whereNotNull('visitor_log_screening_submission_uid')
            ->get();
    }

    public function getByFilters($inputs)
    {
        $query = $this->visitorLogDetails;

        //Filter by customer Id
        $query = $query->when(isset($inputs['x-ci']), function ($query) use ($inputs) {
            $query->where('customer_id', $inputs['x-ci']);
        });

        //Filter by updated timestamp
        $query = $query->when(isset($inputs['ts']) && $inputs['ts'] !== null && !empty($inputs['ts']), function ($query) use ($inputs) {
            $query->where('updated_at', '>=', $inputs['ts']);
        });

        //Only get results of past 24 hrs
        $query->where('created_at', '>=', Carbon::now()->subDay());

        //Fetch results contains valid payload
        $query = $query->whereNotNull('payload');

        return $query->get();
    }

    public function updateCount($inputs)
    {
        return $this->visitorLogDetails->where('customer_id', $inputs['x-ci'])
        ->when(isset($inputs) && !empty($inputs['visitorLog']), function ($q) use ($inputs) {
            return $q->where('updated_at','>=', $inputs['visitorLog']);
        })->count();
    }
}
