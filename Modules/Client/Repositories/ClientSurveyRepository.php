<?php

namespace Modules\Client\Repositories;

use Auth;
use Carbon\Carbon;
use Modules\Client\Models\ClientSurvey;
use App\Services\HelperService;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\CustomerEmployeeAllocation;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Contracts\Repositories\ContractsRepository;
use Modules\Admin\Repositories\PayPeriodRepository;

class ClientSurveyRepository
{
    protected $customeremployeeallocationrepository;
    protected $contractsrepository;
    protected $payperiodrepository;

    public function __construct(
        CustomerEmployeeAllocationRepository $customeremployeeallocationrepository,
        ContractsRepository $contractsrepository,
        PayPeriodRepository $payperiodrepository

    ) {
        $this->customeremployeeallocationrepository = $customeremployeeallocationrepository;
        $this->contractsrepository = $contractsrepository;
        $this->payperiodrepository = $payperiodrepository;
    }

    public function getClientchartsinglecustomer($request)
    {
        $clientid = $request->client;
        $startdate = $request->startdate;
        $datearray = [];
        $payperiodids = [];
        $payperiods = $this->payperiodrepository->getPayperiodRangeAll($startdate, date("Y-m-d"));
        foreach ($payperiods as $payp) {
            array_push($payperiodids, $payp->id);
            if (!in_array($payp->short_name, $datearray)) {
                array_push($datearray, $payp->short_name);
            }
        }
        $chartdata = ClientSurvey::with("customer")
            ->where("client_id", $clientid)
            // ->whereIn("payperiod", $payperiodids)
            ->whereBetween("created_at", [$startdate, date('Y-m-d', strtotime("+1 day"))])
            ->when($request->userratingcondition == "equal", function ($q) use ($request) {
                return $q->when($request->userratingfilter != 0, function ($qry) use ($request) {
                    return $qry->where("rating", $request->userratingfilter);
                });
            })
            ->when($request->userratingcondition == "greater", function ($q) use ($request) {
                return $q->when($request->userratingfilter != 0, function ($qry) use ($request) {
                    return $qry->where("rating", '>=', $request->userratingfilter);
                });
            })
            ->when($request->userratingcondition == "less", function ($q) use ($request) {
                return $q->when($request->userratingfilter != 0, function ($qry) use ($request) {
                    return $qry->where("rating", '<=', $request->userratingfilter);
                });
            })
            ->orderBy("created_at")->get();
        $date = $startdate;
        // End date
        $end_date = date("Y-m-d");

        // while (strtotime($date) <= strtotime($end_date)) {
        //     array_push($datearray, $date);
        //     $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
        // }
        $returnarray = [];
        $returnarray["data"] = [];
        foreach ($chartdata as $data) {
            $returnarray["customer"] = $data->customer->client_name;
            $returnarray["data"][$data->pay_period->short_name] = $data->rating;
            if (isset($returnarray[$data->customer->id][$data->pay_period->short_name]["data"])) {
                $index = count($returnarray[$data->customer->id][$data->pay_period->short_name]["data"]);

                $returnarray[$data->customer->id][$data->pay_period->short_name]["data"][$index] = ["rating" => $data->rating];
            } else {
                $returnarray[$data->customer->id][$data->pay_period->short_name]["data"][0] = ["rating" => $data->rating];
            }
        }
        // dd($returnarray);

        foreach ($returnarray["data"] as $key => $value) {
            $payp = $key;
            $ratingarray = ($returnarray[$clientid][$key]);
            foreach ($ratingarray as $key => $ratingval) {
                $returnarray["data"][$payp] = collect($ratingval)->avg("rating");
            }
        }
        //dd($returnarray);
        $returnarray["xaxis"] = $datearray;
        return json_encode($returnarray, true);
    }

    public function getClientchartallcustomer($request)
    {
        $clientid = $request->client;
        $startdate = $request->startdate;
        $area_manager = $request->area_manager;
        $countarray = [];
        $datearray = [];
        $payperiodids = [];
        $permission = "";
        $clientallocation = [];
        $payperiodratingarray = [];
        $averagevaluearray = [];
        $clients = [];
        $allocation = [];
        $tilldate = date('Y-m-d', strtotime("+1 day"));
        if ($request->has("client")) {
            $clients = $request->client;
        }
        if (Auth::user()->hasAnyPermission(["super_admin", "view_all_clientsurvey"])) {
            $permission = "all";
            if ($area_manager > 0) {
                $allocation = CustomerEmployeeAllocation::whereIn("user_id", $area_manager)
                    ->get()
                    ->pluck("customer_id")->toArray();
            }
        } else {
            $clientallocation = $this->customeremployeeallocationrepository->getAllocatedCustomerId([\Auth::user()->id]);

            if ($area_manager > 0) {
                $allocation = CustomerEmployeeAllocation::whereIn("user_id", $area_manager)
                    ->get()
                    ->pluck("customer_id")->toArray();
            }

            $allocation = array_intersect($clientallocation, $allocation);
        }


        $payperiods = $this->payperiodrepository->getPayperiodRangeAll($startdate, $tilldate);
        foreach ($payperiods as $payp) {
            array_push($payperiodids, $payp->id);
            if (!in_array($payp->short_name, $datearray)) {
                array_push($datearray, $payp->short_name);
            }
        }

        $chartdata = ClientSurvey::with("customer")
            // ->whereIn("payperiod", $payperiodids)
            ->whereBetween("created_at", [$startdate, date('Y-m-d', strtotime("+1 day"))])
            ->when($permission != "all", function ($q) use ($clientallocation, $request, $allocation) {
                if ($request->has("client")) {
                    if (count($allocation) > 0) {
                        if ($request->has("area_manager")) {
                            $allocation = array_merge($allocation, $request->client);
                            return $q->whereIn("client_id", $allocation);
                        } else if ($request->has("client")) {
                            return $q->whereIn("client_id", $request->client);
                        } else {
                            return $q->whereIn("client_id", $clientallocation);
                        }
                    } else {
                        return $q->whereIn("client_id", $request->client);
                    }
                } else if ($request->has("area_manager")) {
                    $allocation = array_merge($allocation, $request->client);
                    return $q->whereIn("client_id", $allocation);
                } else {
                    return $q->whereIn("client_id", $clientallocation);
                }
            })
            ->when($permission == "all", function ($q) use ($clientallocation, $request, $allocation) {
                if ($request->has("client")) {

                    if ($request->has("area_manager")) {
                        $allocation = array_merge($allocation, $request->client);
                        return $q->when(count($allocation) > 0, function ($q) use ($allocation, $request) {
                            return $q->whereIn("client_id", $allocation);
                        });
                    } else {
                        if ($request->has("client")) {
                            return $q->whereIn("client_id", $request->client);
                        }
                    }
                } else {

                    return $q->when(count($allocation) > 0, function ($q) use ($allocation, $request) {

                        if ($request->has("area_manager")) {
                            return $q->whereIn("client_id", $allocation);
                        }
                    });
                }
            })
            ->when($request->userratingcondition == "equal", function ($q) use ($request) {
                return $q->when($request->userratingfilter != 0, function ($qry) use ($request) {
                    return $qry->where("rating", $request->userratingfilter);
                });
            })
            ->when($request->userratingcondition == "greater", function ($q) use ($request) {
                return $q->when($request->userratingfilter != 0, function ($qry) use ($request) {
                    return $qry->where("rating", '>=', $request->userratingfilter);
                });
            })
            ->when($request->userratingcondition == "less", function ($q) use ($request) {
                return $q->when($request->userratingfilter != 0, function ($qry) use ($request) {
                    return $qry->where("rating", '<=', $request->userratingfilter);
                });
            })
            ->orderBy("created_at")->get();
        $date = $startdate;
        $end_date = $tilldate;




        $colors = [
            "#F2351F", "red ", "#F55A35", "#348AC7", "#1D617A", "#288386", "#6CAF7F",
            "#185071", "#267C8A", "#B5D568", "#EEE9BB", "#0E0E0E", "#5C89AE", "#3DBAC5", "#B5D568", "#45D2D1",
            "#E0C769", "#E16A68", "#CDCDCD", "#00000"
        ];
        $returnarray = [];
        $returnarray["data"] = [];
        $allreturnarray = [];
        $averagereturnarray = [];
        $averagereturnarray["1"]["name"] = "Consolidated Report";
        $averagereturnarray["1"]["color"] = "#F55A35";
        $c = 0;

        foreach ($chartdata as $data) {
            if ($c % 19 == 0) {
                $c = 0;
            }
            try {

                $returnarray["customer"][$data->customer->id] = $data->customer->client_name;
                $returnarray["data"][$data->customer->id][$data->pay_period->short_name] = $data->rating;
                $allreturnarray["customer"][$data->customer->id]["id"] = $data->customer->id;
                $allreturnarray["customer"][$data->customer->id]["name"] = $data->customer->client_name;
                $allreturnarray["customer"][$data->customer->id]["color"] = $colors[$c];
                if (isset($allreturnarray["customer"][$data->customer->id]["count"])) {
                    $allreturnarray["customer"][$data->customer->id]["count"] =
                        $allreturnarray["customer"][$data->customer->id]["count"] + 1;
                } else {
                    $allreturnarray["customer"][$data->customer->id]["count"] = 1;
                }
                if (isset($returnarray[$data->customer->id][$data->pay_period->short_name]["data"])) {
                    $index = count($returnarray[$data->customer->id][$data->pay_period->short_name]["data"]);
                    $returnarray[$data->customer->id][$data->pay_period->short_name]["data"][$index] = ["rating" => $data->rating];
                    $countarray[$data->customer->id][$data->pay_period->short_name]
                        =
                        $countarray[$data->customer->id][$data->pay_period->short_name] + 1;
                } else {
                    $returnarray[$data->customer->id][$data->pay_period->short_name]["data"][0]
                        = ["rating" => $data->rating];
                    $countarray[$data->customer->id][$data->pay_period->short_name] = 1;
                }
                $c++;
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
        $returnarray["xaxis"] = $datearray;
        $allreturnarray["xaxis"] = $datearray;
        $allreturnarray["count"] = $countarray;
        try {
            if ($request->graphtype == "individual") {
                foreach ($returnarray["xaxis"] as $payp) {
                    $allreturnarray["yaxis"][0][$payp] = 0;
                    $allreturnarray["customer"][0]["id"] = "0";
                    $allreturnarray["customer"][0]["name"] = "0";
                    $allreturnarray["customer"][0]["color"] = "transparent";
                }
                foreach ($returnarray["customer"] as $customerid => $name) {
                    foreach ($returnarray["xaxis"] as $payp) {


                        if (isset($returnarray[$customerid][$payp]["data"])) {
                            $rating = 0;

                            foreach ($returnarray[$customerid][$payp]["data"] as $key => $value) {
                                $rating = $rating + $value["rating"];
                            }
                            $allreturnarray["yaxis"][$customerid][$payp] = $rating / count($returnarray[$customerid][$payp]["data"]);
                        } else {
                            $allreturnarray["yaxis"][$customerid][$payp] = null;
                        }
                    }
                }
            } else if ($request->graphtype == "average") {
                $valuesinpayperiod = [];
                foreach ($returnarray["customer"] as $customerid => $name) {
                    foreach ($returnarray["xaxis"] as $payp) {

                        if (isset($returnarray[$customerid][$payp])) {
                            $totrat = 0;
                            foreach ($returnarray[$customerid][$payp]["data"] as $k => $v) {
                                $totrat = $totrat + $v["rating"];
                            }
                            $payperiodratingarray[$customerid][$payp] = $totrat / count($returnarray[$customerid][$payp]["data"]);
                            $valuesinpayperiod[$payp][$customerid]["totrating"] = $totrat;
                            $valuesinpayperiod[$payp][$customerid]["noofrating"] = count($returnarray[$customerid][$payp]["data"]);
                        } else {
                            $payperiodratingarray[$customerid][$payp] = 0;
                        }
                    }
                }
                $collectarray = collect($payperiodratingarray);
                foreach ($returnarray["xaxis"] as $val) {
                    if (isset($valuesinpayperiod[$val])) {

                        $payperiodratingdata = $valuesinpayperiod[$val];
                        $noofcustomerrating = count($valuesinpayperiod[$val]);
                        $averagerating = 0;

                        foreach ($payperiodratingdata as $key => $value) {

                            $averagerating = $averagerating + ($value["totrating"] / $value["noofrating"]);
                        }
                        $averagerating = $averagerating / $noofcustomerrating;
                        $averagevaluearray[1][$val] = $averagerating;
                    } else {
                        $averagevaluearray[1][$val] = null;
                    }
                }




                $allreturnarray = [];
                $averagecountarray = [];
                foreach ($countarray as $key => $value) {
                    foreach ($value as $payp => $count) {
                        if (!isset($averagecountarray[1][$payp])) {
                            $averagecountarray[1][$payp] = $count;
                        } else {
                            $averagecountarray[1][$payp] = $averagecountarray[1][$payp] + $count;
                        }
                    }
                }
                $allreturnarray["customer"][1]["name"] = "Consolidated Customer";
                $allreturnarray["customer"][1]["id"] = 1;
                $allreturnarray["customer"][1]["color"] = "#F55A35";
                $allreturnarray["xaxis"] = $returnarray["xaxis"];
                $allreturnarray["yaxis"] = $averagevaluearray;
                $allreturnarray["count"] = $averagecountarray;
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
        //
        if ($request->graphtype == "individual") {
            return json_encode($allreturnarray, true);
        } else if ($request->graphtype == "average") {
            return json_encode($allreturnarray, true);
        }
    }

    public function getClientchartallcustomeranalytics($request)
    {
        $clientid = $request->get("customer-search");
        $startdate = $request->startdate;
        $area_manager = $request->area_manager;
        $countarray = [];

        if ($request->has("startdate")) {
        } else {
            $startdate =    date('Y-m-d', strtotime(date("Y-m-d") . ' - 365 days'));
        }
        $datearray = [];
        $payperiodids = [];
        $permission = "";
        $clientallocation = [];
        $payperiodratingarray = [];
        $averagevaluearray = [];
        $clients = [];
        $allocation = [];
        if ($request->has("customer-search")) {
            $clients = $request->get("customer-search");
        }

        if (Auth::user()->hasAnyPermission(["super_admin", "view_all_clientsurvey"])) {
            $permission = "all";
            if ($area_manager > 0) {
                $allocation = CustomerEmployeeAllocation::whereIn("user_id", $area_manager)
                    ->get()
                    ->pluck("customer_id")->toArray();
            }
        } else {
            $clientallocation = $this->customeremployeeallocationrepository->getAllocatedCustomerId([\Auth::user()->id]);

            if ($area_manager > 0) {
                $allocation = CustomerEmployeeAllocation::whereIn("user_id", $area_manager)
                    ->get()
                    ->pluck("customer_id")->toArray();
            }

            $allocation = array_intersect($clientallocation, $allocation);
        }


        $payperiods = $this->payperiodrepository->getPayperiodRangeAll($startdate, date('Y-m-d', strtotime("+1 day")));

        foreach ($payperiods as $payp) {
            array_push($payperiodids, $payp->id);
            if (!in_array($payp->short_name, $datearray)) {
                array_push($datearray, $payp->short_name);
            }
        }
        $chartdata = ClientSurvey::with("customer")
            // ->whereIn("payperiod", $payperiodids)
            ->whereBetween("created_at", [$startdate, date('Y-m-d', strtotime("+1 day"))])
            ->when($permission != "all", function ($q) use ($clientallocation, $request, $allocation) {
                if ($request->has("customer-search")) {
                    if (count($allocation) > 0) {
                        if ($request->has("customer-search")) {
                            return $q->whereIn("client_id", $request->get("customer-search"));
                        } else {
                            return $q->whereIn("client_id", $clientallocation);
                        }
                    } else {
                        return $q->whereIn("client_id", $request->get("customer-search"));
                    }
                } else {
                    return $q->whereIn("client_id", $clientallocation);
                }
            })
            ->when($permission == "all", function ($q) use ($clientallocation, $request, $allocation) {
                if ($request->has("customer-search")) {


                    if ($request->has("customer-search")) {
                        return $q->whereIn("client_id", $request->get("customer-search"));
                    }
                } else {
                }
            })

            ->orderBy("created_at")->get();
        $date = $startdate;
        $end_date = date('Y-m-d', strtotime("+1 day"));



        $colors = [
            "#348AC7", "#F8AF29", "#F55A35", "#348AC7", "#1D617A", "#288386", "#6CAF7F",
            "#185071", "#267C8A", "#B5D568", "#EEE9BB", "#0E0E0E", "#5C89AE", "#3DBAC5", "#B5D568", "#45D2D1",
            "#E0C769", "#E16A68", "#CDCDCD", "#00000"
        ];
        $returnarray = [];
        $returnarray["data"] = [];
        $allreturnarray = [];
        $averagereturnarray = [];
        $averagereturnarray["1"]["name"] = "Consolidated Report";
        $averagereturnarray["1"]["color"] = "#F55A35";
        $c = 0;
        $countarray = [];
        foreach ($chartdata as $data) {
            if ($c % 19 == 0) {
                $c = 0;
            }
            try {

                $returnarray["customer"][$data->customer->id] = $data->customer->client_name;
                $returnarray["data"][$data->customer->id][$data->pay_period->short_name] = $data->rating;
                $allreturnarray["customer"][$data->customer->id]["id"] = $data->customer->id;
                $allreturnarray["customer"][$data->customer->id]["name"] = $data->customer->client_name;
                $allreturnarray["customer"][$data->customer->id]["color"] = $colors[$c];
                if (isset($allreturnarray["customer"][$data->customer->id]["count"])) {
                    $allreturnarray["customer"][$data->customer->id]["count"] =
                        $allreturnarray["customer"][$data->customer->id]["count"] + 1;
                } else {
                    $allreturnarray["customer"][$data->customer->id]["count"] = 1;
                }
                if (isset($returnarray[$data->customer->id][$data->pay_period->short_name]["data"])) {
                    $index = count($returnarray[$data->customer->id][$data->pay_period->short_name]["data"]);
                    $returnarray[$data->customer->id][$data->pay_period->short_name]["data"][$index]
                        =
                        ["rating" => $data->rating];
                    $countarray[$data->customer->id][$data->pay_period->short_name]
                        =
                        $countarray[$data->customer->id][$data->pay_period->short_name] + 1;
                } else {
                    $returnarray[$data->customer->id][$data->pay_period->short_name]["data"][0] = ["rating" => $data->rating];
                    $countarray[$data->customer->id][$data->pay_period->short_name] = 1;
                }
                $c++;
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
        $returnarray["xaxis"] = $datearray;
        $allreturnarray["xaxis"] = $datearray;
        $allreturnarray["count"] = $countarray;
        try {
            if ($request->graphtype == "individual" || !$request->has("graphtype")) {
                foreach ($returnarray["customer"] as $customerid => $name) {
                    foreach ($returnarray["xaxis"] as $payp) {


                        if (isset($returnarray[$customerid][$payp]["data"])) {
                            $rating = 0;

                            foreach ($returnarray[$customerid][$payp]["data"] as $key => $value) {
                                $rating = $rating + $value["rating"];
                            }
                            $allreturnarray["yaxis"][$customerid][$payp] = $rating / count($returnarray[$customerid][$payp]["data"]);
                            if (isset($countarray[$customerid][$payp])) {
                                //$countarray[$customerid][$payp] = $countarray[$customerid][$payp]+1;
                            } else {
                                //$countarray[$customerid][$payp]=1;
                            }
                        } else {
                            $allreturnarray["yaxis"][$customerid][$payp] = 0;
                        }
                    }
                }
            } else if ($request->graphtype == "average") {
                $valuesinpayperiod = [];
                foreach ($returnarray["customer"] as $customerid => $name) {
                    foreach ($returnarray["xaxis"] as $payp) {

                        if (isset($returnarray[$customerid][$payp])) {
                            $totrat = 0;
                            foreach ($returnarray[$customerid][$payp]["data"] as $k => $v) {
                                $totrat = $totrat + $v["rating"];
                            }
                            $payperiodratingarray[$customerid][$payp] = $totrat / count($returnarray[$customerid][$payp]["data"]);
                            $valuesinpayperiod[$payp][$customerid]["totrating"] = $totrat;
                            $valuesinpayperiod[$payp][$customerid]["noofrating"] = count($returnarray[$customerid][$payp]["data"]);
                        } else {
                            $payperiodratingarray[$customerid][$payp] = 0;
                        }
                    }
                }
                $collectarray = collect($payperiodratingarray);
                foreach ($returnarray["xaxis"] as $val) {
                    if (isset($valuesinpayperiod[$val])) {

                        $payperiodratingdata = $valuesinpayperiod[$val];
                        $noofcustomerrating = count($valuesinpayperiod[$val]);
                        $averagerating = 0;

                        foreach ($payperiodratingdata as $key => $value) {

                            $averagerating = $averagerating + ($value["totrating"] / $value["noofrating"]);
                        }
                        $averagerating = $averagerating / $noofcustomerrating;
                        $averagevaluearray[1][$val] = $averagerating;
                    } else {
                        $averagevaluearray[1][$val] = 0;
                    }
                }

                $allreturnarray = [];
                $averagecountarray = [];
                foreach ($countarray as $key => $value) {
                    foreach ($value as $payp => $count) {
                        if (!isset($averagecountarray[1][$payp])) {
                            $averagecountarray[1][$payp] = $count;
                        } else {
                            $averagecountarray[1][$payp] = $averagecountarray[1][$payp] + $count;
                        }
                    }
                }
                $allreturnarray["customer"][1]["id"] = 1;
                $allreturnarray["customer"][1]["name"] = "Consolidated Customer";
                $allreturnarray["customer"][1]["id"] = 1;
                $allreturnarray["customer"][1]["color"] = "#F55A35";
                $allreturnarray["xaxis"] = $returnarray["xaxis"];
                $allreturnarray["yaxis"] = $averagevaluearray;
                $allreturnarray["count"] = $averagecountarray;
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
        //

        if ($request->graphtype == "individual"  || !$request->has("graphtype")) {
            return json_encode($allreturnarray, true);
        } else if ($request->graphtype == "average") {
            return json_encode($allreturnarray, true);
        }
    }

    public function getSurveyData($request)
    {
        $permission = "";
        $search = isset(($request->search)["value"]) ? ($request->search)["value"] : "";
        $time = strtotime("-1 year", time());
        $startdate = date("Y-m-d", $time);
        $clientallocation = [];

        if (\Auth::user()->hasAnyPermission(["super_admin", "view_all_clientsurvey"])) {
            $permission = "all";
        } else if (\Auth::user()->hasAnyPermission(["view_allocated_clientsurvey"])) {
            $clientallocation = $this->customeremployeeallocationrepository->getAllocatedCustomerId([\Auth::user()->id]);
        }
        $datearray = [];
        $payperiodids = [];

        $payperiods = $this->payperiodrepository->getPayperiodRangeAll($startdate, date("Y-m-d"));
        foreach ($payperiods as $payp) {
            array_push($payperiodids, $payp->id);
            if (!in_array($payp->short_name, $datearray)) {
                array_push($datearray, $payp->short_name);
            }
        }
        try {
            $data = ClientSurvey::with(["customer", "pay_period", "user", "created_user", "user.employee", "user.employee.employeePosition"])
                ->when($permission != "all", function ($q) use ($clientallocation) {
                    return $q->whereIn("client_id", $clientallocation);
                })
                ->when($request->get("customer-search") != null, function ($q) use ($request) {
                    return $q->whereIn("client_id", $request->get("customer-search"));
                })
                // ->whereIn("payperiod", $payperiodids)
                ->whereBetween("created_at", [$startdate, date('Y-m-d', strtotime("+1 day"))])
                ->get();
            $i = 0;
            $surveydataarray = [];
            foreach ($data as $surveydata) {
                $surveydataarray[$i]["client_name"] = $surveydata->customer->project_number .
                    " - " . $surveydata->customer->client_name;

                $surveydataarray[$i]["client_contact"] = $surveydata->user->first_name . " " . $surveydata->user->last_name;

                if ($surveydata->user->employee->position_id != null) {

                    $surveydataarray[$i]["client_contact"] .= " (" . $surveydata->user->employee->employeePosition->position . ")";
                }

                if (isset($surveydata->user->employee)) {

                    $surveydataarray[$i]["phone"] = $surveydata->customer->contact_person_phone;
                }
                $surveydataarray[$i]["rating"] = $surveydata->rating;
                $surveydataarray[$i]["notes"] = $surveydata->notes;
                $surveydataarray[$i]["created_by"] = $surveydata->created_user->first_name . " " . $surveydata->created_user->last_name;
                $surveydataarray[$i]["created_at"] = date("d M Y H:i A", strtotime($surveydata->created_at));
                $i++;
            }

            return json_encode($surveydataarray, true);
        } catch (\Exception $e) {
            throw $e;
        }
    }
    /**
     * For KPI.
     * Fetching average rating of a customer.
     * @param  date or startDate and startDate.
     */
    public function getAllCustomerSurveyData($request)
    {
        return ClientSurvey::when((isset($request['date'])), function ($query) use ($request) {
            return $query->whereDate('created_at', $request['date']);
        })
        ->when((isset($request['startDate']) && isset($request['endDate'])), function ($query) use ($request) {
            return $query->whereDate('created_at', '>=', $request['startDate'])
                ->whereDate('created_at', '<=', $request['endDate']);
        })
        ->when(isset($request['start_date']),function($query) use($request){
            return $query->whereDate('created_at','>=',$request['start_date']);
        })
        ->when(isset($request['end_date']),function($query) use($request){
            return $query->whereDate('created_at','<=',$request['end_date']);
        })
        ->select(
            'client_id as customer_id',
            \DB::raw('DATE(created_at) as createdAt'),
            \DB::raw('AVG(rating) as rating')
        )
        ->groupBy('customer_id', 'createdAt')
        ->get();
    }
}
