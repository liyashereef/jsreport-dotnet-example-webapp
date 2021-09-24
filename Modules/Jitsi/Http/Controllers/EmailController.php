<?php

namespace Modules\Jitsi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Http\Requests\RolesPermissionRequest;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\EmailGroupAllocation;
use Modules\Admin\Models\EmailGroup;
use Modules\Admin\Models\EmailAccountsMaster;
use Modules\Admin\Models\User;
use Modules\Jitsi\Models\EmailBlastLog;
use Modules\Jitsi\Jobs\SendBlastEmailJob;
use Modules\Jitsi\Jobs\SendQueuedBlastMail;
use Spatie\Permission\Models\Role;
use Modules\Employeescheduling\Repositories\SchedulingRepository;
use Modules\Jitsi\Repositories\BlastEmailRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;

class EmailController extends Controller
{
    protected $schedulingRepository;
    protected $blastEmailRepository;
    protected $customerEmployeeAllocationRepository;

    public function __construct(
        SchedulingRepository $schedulingRepository,
        BlastEmailRepository $blastEmailRepository,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository
    ) {
        $this->schedulingRepository = $schedulingRepository;
        $this->blastEmailRepository = $blastEmailRepository;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('jitsi::index');
    }

    public function viewMailBlastReports(Request $request)
    {
        //
        $allocatedCustomers = $this->blastEmailRepository->getAllocatedCustomers(\Auth::user());

        $customers = $this->blastEmailRepository->getCustomerList();
        $customers = collect($customers)->sortBy('project_number')->toArray();
        $blastMailRepo = $this->blastEmailRepository;
        $customerArray = [];
        foreach ($customers as $key => $value) {
            $customerArray[$value["id"]] = $value["client_name"];
        }

        $userRoles = Role::all();
        $uRoles = [];
        foreach ($userRoles as $userRole) {
            $uRoles[$userRole->id] = $this->blastEmailRepository->changeFormat($userRole->name);
        }
        $emailGroups = EmailGroup::get();
        $emailMasters = EmailAccountsMaster::get();
        // dd($emailMasters);
        $allRoles = Role::whereNotIn("name", ["super_admin", "admin"])
            ->orderBy("name")->get();
        $allRolesExceptionAdmin = Role::all();
        $allURoles = [];
        foreach ($allRoles as $allRole) {
            $allURoles[$allRole->id] = $this->blastEmailRepository->changeFormat($allRole->name);
        }


        return view("jitsi::mailblastreports", compact(
            "emailGroups",
            "allRoles",
            "customers",
            "emailMasters",
            "uRoles",
            "customerArray",
            "allURoles"
        ));
    }


    public function viewMailBlastReportslist(Request $request)
    {
        $allocatedCustomers = $this->blastEmailRepository->getAllocatedCustomers(\Auth::user());
        $emailGroups = EmailGroupAllocation::whereIn("customer_id", $allocatedCustomers)
            ->pluck("id")->toArray();
        $customers = $this->blastEmailRepository->getCustomerList();
        $customers = collect($customers)->sortBy('project_number')->toArray();

        $customerArray = [];
        foreach ($customers as $key => $value) {
            $customerArray[$value["id"]] = $value["client_name"];
        }
        $userRoles = Role::all();
        $uRoles = [];
        foreach ($userRoles as $userRole) {
            $uRoles[$userRole->id] = $this->blastEmailRepository->changeFormat($userRole->name);
        }
        //
        $start_date = $request->start_date;
        $end_date = date('Y-m-d', strtotime('+1 day', strtotime($request->end_date)));
        $userRoles = null;
        if (isset($request->groups)) {
            $userRoles = $request->groups;
        }

        $assignedClients = null;
        if (isset($request->clients)) {
            foreach ($request->clients as $key => $value) {
                $assignedClients[] = intval($value);
            }
        } else if (count($allocatedCustomers) > 0) {
            // $assignedClients = $allocatedCustomers;
        }
        $filterData = EmailBlastLog::when($start_date != "", function ($q) use ($start_date) {
            return $q->where("created_at", '>=', $start_date);
        })
            ->when($end_date != "", function ($q) use ($end_date) {
                return $q->where("created_at", '<=', $end_date);
            })
            ->when($userRoles != null, function ($qry) use ($userRoles) {
                return $qry->whereIn("email_roles_associated", $userRoles);
            })
            ->when($assignedClients != null, function ($qry) use ($assignedClients) {
                return $qry->whereIn("email_clients", $assignedClients);
            })
            ->when(!isset($request->clients), function ($q) use ($allocatedCustomers, $emailGroups) {
                if (!\Auth::user()->hasAnyPermission(["super_admin", "create_blastcom_all_customers"])) {
                    $q->whereIn("email_clients", $allocatedCustomers);
                }
                //$q->orWhereIn("email_clientgroups", $emailGroups);
            })
            ->orderBy("created_at", "desc")->get();
        $allClientGroupAdmin = EmailGroup::get();
        $allClientGroups = [];
        foreach ($allClientGroupAdmin as $allGroups) {
            $allClientGroups[$allGroups->id] = $allGroups->group_name;
        }

        $tableContent = $filterData;
        return view("jitsi::reportView", compact(
            "tableContent",
            "uRoles",
            "customerArray",
            "allClientGroups"
        ));
    }

    public function viewMailDetailedView(Request $request)
    {
        $data = EmailBlastLog::find($request->id);
        $mailSubject = $data->subject;
        $mailMessage = $data->message;
        $rolesAssociated = $data->email_roles_associated;
        $mailClients = $data->email_clients;
        $emailRecipients = $data->email_recipient;
        $emailIndividualEmail = $data->email_individualrecipients;
        $userContent = "";
        $recipData = [];
        $emRecipUser = 0;
        if (count($emailRecipients) > 0) {
            foreach ($emailRecipients as $key => $moreData) {
                $recipData[$key] = [
                    "eMail" => $moreData["email"],
                    "failed" => $moreData["failed"]
                ];
            }
            $emRecipients = (array_keys($emailRecipients));
            $emUsers = User::whereIn("id", $emRecipients)->get();
            $emRecipUser = $emUsers->count();
            foreach ($emUsers as $emUser) {
                $userContent .= $emUser->getFullNameAttribute() .
                    " (" . $recipData[$emUser->id]["eMail"] . ")"
                    . ($recipData[$emUser->id]["failed"] == true ? " <span class='failed'>Failed</span>" : "") .
                    "<br/>";
            }
        }

        if (count($emailIndividualEmail) > 0) {
            foreach ($emailIndividualEmail as $indUser) {
                $userContent .= $indUser  . "<br/>";
            }
        }
        $mailClientGroups = $data->email_clientgroups;
        $mailIndividualGroups = $data->email_individualrecipients;
        $rolesContent = "";
        if ($rolesAssociated != null) {
            $rolesAssoc = Role::whereIn("id", $rolesAssociated)->get();
            foreach ($rolesAssoc as $role) {
                $rolesContent .= $this->blastEmailRepository->changeFormat($role->name)  . "<br/>";
            }
        }
        $clientsContent = "";
        if ($mailClients != null) {
            $clientsAssoc = Customer::whereIn("id", $mailClients)->get();
            foreach ($clientsAssoc as $client) {
                $clientsContent .= $client->project_number . "-" . $client->client_name . "<br/>";
            }
        }

        $clientsGroupContent = "";
        if ($mailClientGroups != null) {
            $clientsGroupAssoc = EmailGroup::whereIn("id", $mailClientGroups)->get();
            foreach ($clientsGroupAssoc as $client) {
                $clientsGroupContent .= $client->group_name . "<br/>";
            }
        }

        if ($emRecipUser > 4) {
            $userContent = '<div id="readMore" class="readMore">' . $userContent . '</div><span id="showall" class="showallbutton">Show All</span><span id="hidediv" class="hidediv">Show Less</span>';
        } else {
            $userContent = "<div>" . $userContent . "</div>";
        }
        $returnArray = [
            "subject" => $mailSubject,
            "message" => $mailMessage,
            "rolesText" => $rolesContent,
            "clientsText" => $clientsContent,
            "clientGroups" => $clientsGroupContent,
            "userContent" => $userContent
        ];
        return json_encode($returnArray, true);
    }

    public function viewMailDesigner(Request $request)
    {
        //
        $allocatedCustomers = $this->blastEmailRepository->getAllocatedCustomers(\Auth::user());
        $customers = Customer::whereIn("id", $allocatedCustomers)->get();
        $customers = collect($customers)->sortBy('project_number')->toArray();
        if (\Auth::user()->hasAnyPermission(["super_admin", "create_blastcom_all_customers"])) {
            $emailGroups = EmailGroup::get();
        } else {
            $emailGroups = EmailGroup::whereHas("allocation", function ($q) use ($allocatedCustomers) {
                return $q->whereIn("customer_id", $allocatedCustomers);
            })->get();
        }
        $emailMasters = EmailAccountsMaster::get();
        // dd($emailMasters);
        $allRoles = Role::whereNotIn("name", ["super_admin", "admin"])
            ->orderBy("name")->get();
        $clientRole = Role::where("name", "client")->first();
        $allRolesExceptionAdmin = Role::all();
        $allURoles = [];
        foreach ($allRoles as $allRole) {
            $allURoles[$allRole->id] = $this->blastEmailRepository->changeFormat($allRole->name);
        }

        return view("jitsi::maildesigner", compact(
            "emailGroups",
            "allRoles",
            "customers",
            "emailMasters",
            "allURoles",
            "clientRole"
        ));
    }


    public function saveMailDesigner(Request $request)
    {



        //
        $emailBlastId = 0;
        $clientCollection = [];
        $expectedUsers = [];
        $userGroups = [];
        if (isset($request->groups)) {
            $userGroups = $request->groups;
        }
        $customerClients = true;
        $clientRole = $request->clientRole;
        if (isset($request->groups)) {
            if (in_array("-1", $request->groups)) {
                $userGroups =  Role::whereNotIn("name", ["super_admin", "admin"])
                    ->orderBy("name")->pluck("id")->toArray();
            }
        }

        $clientGroups = $request->client_groups;
        $clients = $request->clients;
        $mailSubject = $request->mail_subject;
        $mailFrom = $request->mail_from;
        $mailMessage = $request->message;
        $generalClients = 0;
        $generalClientgroups = 0;
        $userLists = collect([]);
        $generalRoles = 0;
        if ($mailFrom > 0) {
            $emailDetails = EmailAccountsMaster::find($request->mail_from);
            $mailHost = $emailDetails->smtp_server;
            $mailUsername = $emailDetails->user_name;
            $mailPassword = $emailDetails->password;
            $mailEncryption = $emailDetails->encryption;
            $mailFromname = $emailDetails->display_name;
            $mailFromaddress = $emailDetails->email_address;
            $mailPort = $emailDetails->port;
        } else {

            $mailHost = config("mail.host");
            $mailUsername = config("mail.username");
            $mailPassword = config("mail.password");
            $mailEncryption = config("mail.encryption");
            $mailFromname = config("mail.from.name");
            $mailFromaddress = config("mail.from.address");
            $mailPort = config("mail.port");
        }
        $details = [
            "name" => \Auth::user()->getFullNameAttribute(),
            "mail_host" => $mailHost,
            "mail_username" => $mailUsername,
            "mail_password" => $mailPassword,
            "mail_encryption" => $mailEncryption,
            "mail_from_name" => $mailFromname,
            "mail_port" => $mailPort,
            "mail_from_address" => $mailFromaddress
        ];
        $allocatedCustomers = $this->blastEmailRepository->getAllocatedCustomers(\Auth::user());
        if ($clientGroups) {
            if (\Auth::user()->hasAnyPermission(["super_admin", "create_blastcom_all_customers"])) {
                $expectedClients =
                    EmailGroupAllocation::whereIn("group_id", $clientGroups)
                    ->get()->pluck("customer_id")->toArray();
            } else {
                $expectedClients =
                    EmailGroupAllocation::whereIn("group_id", $clientGroups)->whereIn("customer_id", $allocatedCustomers)
                    ->get()->pluck("customer_id")->toArray();
            }
            if (count($expectedClients) > 0) {
                $clientCollection = array_merge($clientCollection, $expectedClients);
            }
        } else {
            $generalClientgroups = 1;
        }
        if ($clients) {
            $clientCollection = array_merge($clientCollection, $clients);
        } else {
            $generalClients = 1;
        }
        if ($userGroups) {
        } else {
            $generalRoles = 1;
        }
        if ($request->clientgroup_mail == "false") {
            if (\Auth::user()->hasAnyPermission(["super_admin", "admin"])) {
                $clientCollection = Customer::where("active", 1)->get()->pluck("id")->toArray();
            } else {
                $allocatedCustomers = $this->blastEmailRepository->getAllocatedCustomers(\Auth::user());
                $clientCollection = $allocatedCustomers;
            }
        }
        $customerContracts = collect([]);
        $contractMail = 0;
        if (isset($request->client_groups) ||  isset($request->clients)) {
            if (!isset($request->groups)) {
                $contractMail = 1;
            }
        }

        if (count($clientCollection) > 0 && $contractMail == 1) {

            $customerContracts = Customer::with("latestContract")
                ->where("active", 1)
                ->when(count($clientCollection) > 0, function ($q) use ($clientCollection) {
                    return $q->whereIn("id", $clientCollection);
                })
                ->whereHas("latestContract")->get();
        }
        if ($request->employeegroup_mail == "true" || $request->clientgroup_mail == "true") {
            if (count($clientCollection) > 0) {
                $userLists = User::when(
                    count($clientCollection) > 0 && $request->clientgroup_mail == "true",
                    function ($q) use ($clientCollection) {
                        return $q->whereHas("allocation", function ($qry) use ($clientCollection) {
                            return $qry->whereIn("customer_id", $clientCollection);
                        });
                    }
                )->when($userGroups != null, function ($roleq) use ($userGroups) {
                    return $roleq->whereHas('roles', function ($rq) use ($userGroups) {
                        return $rq->whereIn("id", $userGroups);
                    });
                })->get();
            }
        }

        $emailBlastLog = [
            "subject" => $request->mail_subject,
            "message" => $request->message,
            "mail_from" => $request->mail_from,
            "general_clients" => $generalClients,
            "general_clientgroups" => $generalClientgroups,
            "general_roles" => $generalRoles,
            "created_by" => \Auth::user()->id,
            "email_roles_associated" => null,
            "email_recipient" => null,
            "email_clients" => null,
            "email_clientgroups" => null,
            "email_individualrecipients" => null,
            "created_at" => date("Y-m-d H:i:s"),
            "dispatched" => false
        ];

        $emailBlastRecip = [];
        $emailBlastIndRecip = [];
        // $html = view('sendBlastMail', ['mailMessage' => $mailMessage])->render();
        $html = \View::make('emails.sendBlastMail', [
            'mailMessage' => $mailMessage
        ]);
        // $html = $mailMessage;

        $html = $html->render();
        if ($request->employeegroup_mail == "true" || $request->clientgroup_mail == "true") {
            foreach ($userLists as $userList) {

                $name = ucfirst($userList->first_name) . ' ' .
                    ucfirst($userList->last_name);
                $emailBlastRecip[$userList->id] =  [
                    "user_id" => $userList->id,
                    "email" => $userList->email,
                    "failed" => false
                ];

                $expectedUsers[] = [
                    "id" => $userList->id,
                    "email" => $userList->email,
                    "name" => $name
                ];
            }
        }
        if (isset($request->email_address)) {
            foreach ($request->email_address as $emailAddress) {
                $emailBlastIndRecip[] =
                    $emailAddress;
                $details["identifier"] = 0;
                dispatch(new SendBlastEmailJob($details, $emailAddress, $mailSubject, $html));
            }
        }
        $emailBlastLog["email_individualrecipients"] = $emailBlastIndRecip;

        if ($customerContracts->count() > 0) {
            foreach ($customerContracts as $customerContract) {
                $clientContactInformation = ($customerContract->latestContract->client_contact_information);
                foreach ($clientContactInformation as $clientContact) {
                    $name = ucfirst($clientContact->users->first_name) . ' ' .
                        ucfirst($clientContact->users->last_name);
                    if (isset($clientContact->users)) {
                        $expectedUsers[] = [
                            "id" => isset($clientContact->users->id) ? $clientContact->users->id : "",
                            "email" => isset($clientContact->users->id) ? $clientContact->users->email : "",
                            "name" => $name
                        ];
                        $emailBlastRecip[$clientContact->users->id] =  [
                            "user_id" => $clientContact->users->id,
                            "email" => $clientContact->users->email,
                            "failed" => false
                        ];
                    }
                }
            }
        }

        $emailBlastLog["email_recipient"] = $emailBlastRecip;
        $emailRoles = [];
        if (isset($request->groups)) {

            foreach ($userGroups as $key => $group) {
                $emailRoles[] = $group;
            }
        }
        if (count($emailRoles) > 0) {
            $emailBlastLog["email_roles_associated"] = $emailRoles;
        }

        $emailIndUsers = [];
        if (isset($request->email_address)) {

            foreach ($request->email_address as $key => $email) {
                $emailIndUsers[] = $email;
            }
        }
        if (count($emailIndUsers) > 0) {
        }
        $emailClients = [];
        $emailClientGroups = [];
        $clientGroupCustomers = [];
        if (isset($request->client_groups)) {
            foreach ($request->client_groups as $key => $clientgroup) {
                $emailClientGroups[] =  intval($clientgroup);
            }
        }
        if (count($emailClientGroups) > 0) {
            $emailBlastLog["email_clientgroups"] = $emailClientGroups;
            $clientGroupCustomers = EmailGroupAllocation::whereIn("group_id", $emailClientGroups)->pluck("customer_id")->toArray();
            $emailClients = $clientGroupCustomers;
        }


        if (isset($request->clients)) {

            foreach ($request->clients as $key => $client) {
                $emailClients[] =  intval($client);
            }
        }
        if (count($emailClients) > 0) {
            $emailBlastLog["email_clients"] = $emailClients;
        }





        $insertData = EmailBlastLog::insert($emailBlastLog);
        if ($insertData) {
            dispatch(new SendQueuedBlastMail());

            $content["code"] = 200;
            $content["message"] = "Queued Successfully";
            $content["success"] = "success";
        } else {
            $content["code"] = 401;
            $content["message"] = "Data error";
            $content["success"] = "warning";
        }
        return response()->json($content);
    }



    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('jitsi::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('jitsi::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('jitsi::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
