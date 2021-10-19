<?php

namespace Modules\Jitsi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Timetracker\Repositories\EmployeeShiftRepository;
use Modules\Admin\Models\Customer;
use Modules\Jitsi\Models\ConferenceRoom;
use Modules\Jitsi\Models\ConferenceSession;
use Modules\Jitsi\Models\ConferenceParticipant;
use Modules\Jitsi\Models\ConferenceStatus;
use Modules\Jitsi\Models\ScheduledMeeting;
use Modules\Jitsi\Models\ScheduledMeetingParticipant;
use Aws\S3\S3Client;
use App\Repositories\PushNotificationRepository;
use Modules\Jitsi\Repositories\CglMeetRepository;
use App\Repositories\MailQueueRepository;

use Modules\Admin\Models\User;
use \Firebase\JWT\JWT;
use Modules\Jitsi\Models\ConferenceRecording;
use Modules\Jitsi\Models\ConferenceRecordingServer;
use Auth;
use \Carbon\Carbon;

class JitsiController extends Controller
{
    protected $employeeShiftRepository;
    private $pushNotificationRepository;
    private $cglMeetRepository;
    private $mailQueueRepository;
    public function __construct(
        EmployeeShiftRepository $employeeShiftRepository,
        PushNotificationRepository $pushNotificationRepository,
        CglMeetRepository $cglMeetRepository,
        MailQueueRepository $mailQueueRepository
    ) {
        $this->employeeShiftRepository = $employeeShiftRepository;
        $this->pushNotificationRepository = $pushNotificationRepository;
        $this->cglMeetRepository = $cglMeetRepository;
        $this->mailQueueRepository = $mailQueueRepository;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $user = \Auth::user();
        $activeguards = $this->employeeShiftRepository->getAllActiveGuards($user);
        $filterstarttime  = Carbon::now()->subMinutes(30)->format('Y-m-d h:i');
        $filterendtime  = Carbon::now()->addMinutes(30)->format('Y-m-d h:i');
        $activeconference = ConferenceStatus::find(1)->conferencecount;
        $recordingservercount = ConferenceRecordingServer::get()->count();
        $restriction = 0;
        if ($activeconference >= $recordingservercount) {
            $restriction = 1;
        }
        $meetings = ScheduledMeeting::whereBetween("startdate", [$filterstarttime, $filterendtime])
            ->where("createdby", \Auth::user()->id)->get();
        //dd(date("Y-m-d"));
        $myarchives = ConferenceRoom::with(["ConferenceSession", "ConferenceRecording"])
            ->where("created_by", \Auth::user()->id)->get();
        $liveemployeearray = [];
        $users = User::whereHas('roles', function ($q) {
            return $q->whereNotIn('name', ['super_admin', 'admin']);
        })->whereActive(true)->orderBy("first_name", "asc")->get();
        //dd($users);
        // $liveusers = User::with(['liveStatus'])
        //     ->whereHas("liveStatus.mostRecentShift", function ($q) {
        //         return $q->live_status_id = 1;
        //     })->get();
        // foreach ($liveusers as $liveuser) {
        //     if ($liveuser->liveStatus->mostRecentShift->live_status_id == 1) {
        //         $livecustomer = ($liveuser->liveStatus->customer);
        //         $customername = $livecustomer->project_number . "-" . $livecustomer->client_name;
        //         $liveemployeearray[] = [
        //             "id" => $liveuser->id,
        //             "name" => $liveuser->getNameWithEmpNoAttribute(),
        //             "project" => $customername,
        //             "image" => $liveuser->employee->image
        //         ];
        //     }
        // }
        // dd($liveemployeearray);
        $customers = Customer::all();

        return view('jitsi::index', compact(
            'activeguards',
            "users",
            "customers",
            "liveemployeearray",
            "myarchives",
            "meetings",
            "restriction"
        ));
    }

    public function joinScheduledmeeting(Request $request)
    {
        //
        $scheduleid = $request->id;
        $flag = 0;
        $roomname = 0;
        $username = 0;
        $sessiondetails = ConferenceSession::with('ConferenceRoom')->where("scheduleid", $scheduleid);
        if ($sessiondetails->count() > 0) {
            $roomdetails = $sessiondetails->first();
            $roomname = $roomdetails->ConferenceRoom->room_name;
            $username = \Auth::user()->name;
            $flag = 1;
            return view("jitsi::scheduledmeeting", compact('roomname', 'username', 'flag'));
        } else {
            return view("jitsi::scheduledmeeting", compact('roomname', 'username', 'flag'));
        }
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */

    public function appJoinMeeting(Request $request)
    {
        $roomname = $request->roomname;
        $username = $request->username;
        $owner = $request->owner;


        /**
         * IMPORTANT:
         * You must specify supported algorithms for your application. See
         * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
         * for a list of spec-compliant algorithms.
         */
        $privateKey = "meetcgl360password";
        //$jwt = JWT::encode($payload, $privateKey, 'HS256');
        return view("jitsi::appmeeting", compact('roomname', 'username', 'owner'));
    }
    public function scheduleMeetingview()
    {
        $user = \Auth::user();
        // $activeguards = $this->employeeShiftRepository->getAllActiveGuards($user);
        $myarchives = ConferenceRoom::with(["ConferenceSession", "ConferenceRecording"])->where("created_by", \Auth::user()->id)->get();
        $liveemployeearray = [];
        $users = User::whereHas('roles', function ($q) {
            return $q->whereNotIn('name', ['super_admin', 'admin']);
        })->whereActive(true)->orderBy("first_name", "asc")->get();
        $ScheduledMeeting = ScheduledMeeting::where([["startdate", ">=", date("Y-m-d")], ["createdby", \Auth::user()->id]])->get();
        //dd($users);
        // $liveusers = User::with(['liveStatus'])
        //     ->whereHas("liveStatus.mostRecentShift", function ($q) {
        //         return $q->live_status_id = 1;
        //     })->get();
        // foreach ($liveusers as $liveuser) {
        //     if ($liveuser->liveStatus->mostRecentShift->live_status_id == 1) {
        //         $livecustomer = ($liveuser->liveStatus->customer);
        //         $customername = $livecustomer->project_number . "-" . $livecustomer->client_name;
        //         $liveemployeearray[] = [
        //             "id" => $liveuser->id,
        //             "name" => $liveuser->getNameWithEmpNoAttribute(),
        //             "project" => $customername
        //         ];
        //     }
        // }


        //dd($liveemployeearray);
        $customers = Customer::all();

        return view('jitsi::scheduleMeetings', compact(
            // 'activeguards',
            "users",
            "customers",
            // "liveemployeearray",
            "myarchives",
            "ScheduledMeeting"
        ));
    }


    public function saveScheduleMeeting(Request $request)
    {
        $startdate = date("Y-m-d H:i", strtotime($request->startdate . " " . $request->starttime));
        $durationinminutes = ($request->duration * 60);
        $massinsert = [];

        $content["code"] = 406;
        $content["message"] = "Data error";
        $content["success"] = "warning";

        $time = new \DateTime($startdate);
        $time->add(new \DateInterval('PT' . $durationinminutes . 'M'));
        $userdetails = User::whereIn("id", $request->activeusers)->get();
        //$enddate = $time->format('Y-m-d H:i');
        $enddate = new \DateTime(date('Y-m-d H:i:s', strtotime('+' . $durationinminutes . ' minutes', strtotime($startdate))));

        $meetingscheduled = ScheduledMeeting::create([
            "title" => $request->meettitle,
            "startdate" => new \DateTime($startdate),
            "enddate" => $enddate,
            "meetinghours" => $request->duration,
            "status" => 0,
            "createdby" => \Auth::user()->id
        ]);
        $maillink = $this->cglMeetRepository->mailInvite($meetingscheduled->id, $request->meettitle, $startdate, $enddate);
        $helper_variables = array(
            '{receiverFullName}' => 'Sir/Mam',
            '{loggedInUserEmployeeNumber}' => Auth::user()->employee->employee_no,
            '{loggedInUser}' => Auth::user()->getFullNameAttribute(),
            '{maillink}' => $maillink,

        );



        if ($meetingscheduled) {
            $massinsert = [];
            foreach ($request->activeusers as $user) {
                $massinsert[] = ["meetingid" => $meetingscheduled->id, "userid" => $user];
                $this->mailQueueRepository
                    ->prepareMailTemplate("cglmeet_scheduling_request_notification", 0, $helper_variables, "Modules\Jitsi\Models\ConferenceRoom", 0, $user);
            }
            ScheduledMeetingParticipant::insert($massinsert);
            $content["code"] = 200;
            $content["message"] = "Saved successfully";
            $content["success"] = "success";
        }

        return json_encode($content, true);
    }

    /**
     * Initiate Meeting
     * @param Request $request
     * @return Response
     */
    public function initiateMeeting(Request $request)
    {
        $flag = true;
        $user = \Auth::user()->id;
        // $roomname = $request->chooseroom;
        // $roompassword = $request->roompassword;
        $hashedroomname = bin2hex(random_bytes(11));
        $conferencestatus = ConferenceStatus::find(1);
        $availability = $conferencestatus->conferencecount;
        if ($availability >= 4) {
            $flag = false;
        }
        $roomname = $hashedroomname;
        $scheduledmeeting = $request->scheduledmeeting;
        $roompassword = rand(1, 10000);
        $roomexist = ConferenceRoom::where("room_name", $roomname)->count();
        $roomowner = ConferenceRoom::where([
            "room_name" => $roomname,
            "created_by" => $user
        ])->count();
        $payload = array(
            "context" => [
                "user" => [
                    "avatar" => "",
                    "name" => \Auth::user()->getFullNameAttribute(),
                    "email" => ""
                ]
            ],
            "aud" => "jitsi",
            "iss" => "meetcgl360user",
            "sub" => "meetcgl360.secture.co.in",
            "room" => $roomname
        );

        /**
         * IMPORTANT:
         * You must specify supported algorithms for your application. See
         * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
         * for a list of spec-compliant algorithms.
         */
        $privateKey = "meetcgl360password";
        //$jwt = JWT::encode($payload, $privateKey, 'HS256');
        $jwt = JWT::encode($payload, $privateKey, 'HS256');

        if (($roomexist > 0 && $roomowner < 1) || $flag == false) {
            //
            return json_encode(["flag" => false], true);
        } else {
            if ($scheduledmeeting > 0) {
                $created = ConferenceRoom::updateorCreate(
                    ["scheduleroomid" => $scheduledmeeting],
                    [
                        "room_name" => $roomname,
                        "room_password" => $roompassword,
                        "scheduleroomid" => $scheduledmeeting,
                        "created_by" => $user
                    ]
                );
            } else {
                $created = ConferenceRoom::updateorCreate(
                    ["room_name" => $roomname],
                    [
                        "room_name" => $roomname,
                        "room_password" => $roompassword,
                        "scheduleroomid" => $scheduledmeeting,
                        "created_by" => $user
                    ]
                );
            }

            $roomid = $created->id;


            if ($scheduledmeeting > 0) {
                $session = ConferenceSession::updateorCreate(
                    ["scheduleid" => $scheduledmeeting],
                    ["roomid" => $roomid, "scheduleid" => $scheduledmeeting]
                );
            } else {
                $session = ConferenceSession::create(["roomid" => $roomid]);
            }

            $sessionid = $session->id;
            //dd($sessionid);
            if ($scheduledmeeting > 0) {

                $participants = ScheduledMeetingParticipant::where("meetingid", $scheduledmeeting)
                    ->get();

                foreach ($participants as $participant) {
                    $scheduleparticipant[] = [
                        "sessionid" => $sessionid,
                        "userid" => $participant->userid
                    ];
                    try {
                        $this->pushNotificationRepository->sendPushNotification(
                            [$participant->userid],
                            $roomname,
                            PUSH_CGL_MEET,
                            "JOIN TO CGL MEET REQUESTED",
                            "CGL Meeting requested "
                        );
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }
                if (count($scheduleparticipant) > 0) {
                    ConferenceParticipant::insert($scheduleparticipant);
                }
            }
            $roomdetail = ["room" => $roomid, "session" => $session->id];
            $link = config("app.url") . "/meet/joinmeeting/" . $session->id;
            return json_encode([
                "flag" => $flag, "detail" => $roomdetail,
                "link" => $link,
                "roomname" => $roomname,
                "availability" => $availability,
                "jwt" => $jwt
            ], true);
        }
    }

    public function finishMeeting(Request $request)
    {
        try {
            $roomid = $request->roomid;
            $sessionid = $request->jitsisession;
            $conferencesession = ConferenceSession::find($sessionid);
            $conferencesession->status = true;
            $conferencesession->save();
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function getSchedules(Request $request)
    {
        $returnarray = [];

        return json_encode($returnarray, true);
    }
    /**
     * Show the form for get Employee details.
     * @return Response
     */
    public function getEmployees(Request $request)
    {
        $searchey = $request->searchkey;
        $searchresult = User::with("employee")
            ->where("first_name", "like", "%" . $searchey . "%")
            ->whereHas('roles', function ($q) {
                return $q->whereNotIn('roles.name', ['super_admin', 'admin']);
            })

            ->get();
        return view("jitsi::employeelist", compact("searchresult"));
    }

    /**
     * Store an Employee to meeting.
     * @param  Request $request
     * @return Response
     */
    public function setEmployeetomeeting(Request $request)
    {
        $roomid = $request->roomid;
        $session = $request->sessionid;
        $userid = $request->userid;
        $roomname = ConferenceRoom::find($roomid);
        try {
            $this->pushNotificationRepository->sendPushNotification(
                [$userid],
                $roomname->room_name,
                PUSH_CGL_MEET,
                "JOIN TO CGL MEET REQUESTED",
                "Meet room: " . $roomname->room_name
            );
        } catch (\Throwable $th) {
            //throw $th;
        }

        return (ConferenceParticipant::updateorCreate(
            ["sessionid" => $session, "userid" => $userid],
            ["sessionid" => $session, "userid" => $userid]
        ))->id;
    }


    public function setJibriconferencestatus(Request $request)
    {
        $process = $request->process;
        if ($process == "on") {
            $exec = ConferenceStatus::find(1)
                ->update([
                    'conferencecount' => \DB::raw('conferencecount+1')
                ]);
            if ($exec) {
                $return = "true";
            } else {
                $return = "false";
            }
        } else {
            try {
                $exec = ConferenceStatus::find(1)
                    ->update([
                        'conferencecount' => \DB::raw('conferencecount-1')
                    ]);
                if ($exec) {
                    $return = "true";
                } else {
                    $return = "false";
                }
            } catch (\Throwable $th) {
                //throw $th;
                $return = "true";
            }
        }
        return $return;
    }

    public function joinMeeting(Request $request)
    {

        $sessid = $request->sessid;
        $userid = \Auth::user()->id;
        $homeurl = config("app.url");
        $auth = ConferenceParticipant::where(
            ["sessionid" => $sessid, "userid" => $userid]
        )->count();
        $owner = ConferenceParticipant::where(
            ["sessionid" => $sessid]
        )->first();
        if ($auth > 0) {
            $room = ConferenceSession::with('ConferenceRoom')->find($sessid);
            $roomname = $room->ConferenceRoom->room_name;
            $roomid = $room->id;
            $membername = \Auth::user()->first_name . " " . \Auth::user()->last_name;


            /**
             * IMPORTANT:
             * You must specify supported algorithms for your application. See
             * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
             * for a list of spec-compliant algorithms.
             */
            return view("jitsi::meeting", compact(
                "roomname",
                "sessid",
                "roomid",
                "membername",
                "homeurl",
                "owner"
            ));
        } else {
            return "<p>You are not authorized in meeting</p>";
        }
    }
    /**
     * Kick out user from chat.
     * @return Response
     */
    public function unsetUsers(Request $request)
    {
        $roomid = $request->roomid;
        $session = $request->sessionid;
        $userid = $request->userid;
        $collection =
            ConferenceParticipant::where([
                "sessionid" => $session,
                "userid" => $userid
            ]);
        $retrieve = $collection->first();

        if ($collection->count() > 0) {
            $jitsiuser = $retrieve->jitsiuserid;
            if ($collection->delete()) {
                $return = json_encode(["success" => true, "jitsiuser" => $jitsiuser]);
            } else {
                $return = json_encode(["success" => false, "jitsiuser" => ""]);
            }
        } else {
            $return = json_encode(["success" => false, "jitsiuser" => ""]);
        }


        return $return;
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('jitsi::show');
    }

    public function setJitsioperations(Request $request)
    {
        $content = [];
        $status = 0;
        if ($request->operation == "associateuser") {
            $user = \Auth::user()->id;
            $jitsiuser = $request->userdata;
            $sessid = $request->sessid;
            $status = ConferenceSession::find($sessid)->status;
            if ($jitsiuser != "") {
                ConferenceParticipant::updateorCreate([
                    "sessionid" => $sessid,
                    "userid" => $user
                ], [
                    "sessionid" => $sessid,
                    "userid" => $user,
                    "jitsiuserid" => $jitsiuser
                ]);
            }
        } else if ($request->operation == "getSessionStatus") {

            $roomname = $request->userdata;
            $roomDetails = ConferenceRoom::where("room_name", $roomname)->first();
            $roomid = $roomDetails->id;
            $sessiondetails = ConferenceSession::where("roomid", $roomid)->first();
            $status = $sessiondetails->status;
            $userexist = ConferenceParticipant::where(["sessionid" => $sessiondetails->id, "userid" => \Auth::user()->id])
                ->count();
            // if ($status < 1 && $userexist > 0) {
            //     return 0;
            // } else {
            //     return $status;
            // }
            return $status;
        } else if ($request->operation == "removejitsiuser") {
            $user = \Auth::user()->id;
            $jitsiuser = $request->userdata;
            $sessid = $request->sessid;
            $status = ConferenceSession::find($sessid)->status;
            ConferenceParticipant::where([
                "sessionid" => $sessid,
                "jitsiuserid" => $jitsiuser
            ])->delete();
        } else if ($request->operation == "getParticipants") {
            $sessid = $request->sessid;
            $participants = ConferenceParticipant::where([
                "sessionid" => $sessid
            ])->get();
            $status = ConferenceSession::find($sessid)->status;
            return view("jitsi::partials.participants", compact("participants"));
        } else if ($request->operation == "savemeetingtextmetadata") {
            $sessid = $request->sessid;
            $userdata = ($request->userdata);
            $meettitle = $userdata["meettitle"];
            $description = $userdata["description"];
            $status = ConferenceSession::find($sessid)->status;
            ConferenceSession::updateorCreate(
                ["id" => $sessid],
                [
                    "meetingtitle" => $meettitle,
                    "description" => $description
                ]
            );
        } else if ($request->operation == "savemeetingcustomermetadata") {
            $sessid = $request->sessid;
            $userdata = ($request->userdata);
            $customer_id = $userdata["customer_id"];
            ConferenceSession::updateorCreate(
                ["id" => $sessid],
                [
                    "customer_id" => $customer_id
                ]
            );
        } else if ($request->operation == "savemeetingemployeemetadata") {
            $sessid = $request->sessid;
            $userdata = ($request->userdata);
            $employee_id = $userdata["employee_id"];
            ConferenceSession::updateorCreate(
                ["id" => $sessid],
                [
                    "employee_id" => $employee_id
                ]
            );
            $status = ConferenceSession::find($sessid)->status;
        } else if ($request->operation == "searchemp") {
            $userdata = $request->get("userdata");
            $name = $userdata["search_key"];
            $search_key = '%' . $userdata["search_key"] . '%';
            $project = $userdata["project"];
            $customers = Customer::select("id", "client_name", "project_number")->get();
            $customerarray = [];
            foreach ($customers as $customer) {
                $customerarray[$customer->id] = $customer->project_number . "-" . $customer->client_name;
            }
            //dd($customerarray);
            $liveusers = User::with("liveStatus")
                ->whereHas("liveStatus", function ($q) use ($project, $name) {
                    if ($project > 0) {
                        return $q
                            // ->addSelect(\DB::raw("select client_name from customers where id=employee_shift_payperiods.customer_id"))
                            ->when($project > 0, function ($qry) use ($project) {
                                return $qry->where("customer_id", $project);
                            });
                    }
                    return $q->when($name != "", function ($qry) use ($name) {
                        return $qry->where("first_name", 'like', '%' . $name . '%');
                    });
                })->whereHas("liveStatus.mostRecentShift", function ($qry) {
                    return $qry->where("live_status_id", 1);
                })->orderBy("first_name", "asc")->get();
            foreach ($liveusers as $liveuserdb) {
                //dd($liveuserdb);
            }
            // dd($liveuserdbclass);
            // if ($project > 0) {

            //     $liveusers = \DB::select(
            //         "select *,emp.employee_no,emp.Image,concat_ws(' ',first_name,last_name) usname
            //         ,(select customer_id from employee_shift_payperiods where employee_id=us.id
            //         order by created_at desc limit 0,1) customer_id,
            //         (select concat_ws(' ',project_number,client_name) from customers where id=customer_id) as project
            //         from users us join employees emp where us.id=emp.user_id and (select live_status_id from employee_shifts
            //         where employee_shift_payperiod_id=(select id from employee_shift_payperiods where employee_id=us.id
            //         order by created_at desc limit 0,1)
            //         order by created_at desc limit 0,1)=1 and
            //          concat_ws(' ',first_name,last_name) like :searchkey and
            //          (select customer_id from employee_shift_payperiods where employee_id=us.id
            //         order by created_at desc limit 0,1)=:project order by first_name asc",
            //         ['searchkey' => $search_key, "project" => $project]
            //     );
            // } else {
            //     $liveusers = \DB::select(
            //         "select *,emp.employee_no,emp.Image,concat_ws(' ',first_name,last_name) usname
            //         ,(select customer_id from employee_shift_payperiods where employee_id=us.id
            //         order by created_at desc limit 0,1) customer_id,
            //         (select concat_ws(' ',project_number,client_name) from customers where id=customer_id) as project
            //         from users us join employees emp where us.id=emp.user_id and (select live_status_id from employee_shifts
            //         where employee_shift_payperiod_id=(select id from employee_shift_payperiods where employee_id=us.id
            //         order by created_at desc limit 0,1)
            //         order by created_at desc limit 0,1)=1 and concat_ws(' ',first_name,last_name) like :searchkey order by first_name asc",
            //         ['searchkey' => $search_key]
            //     );
            // }




            return view("jitsi::partials.searchemployeeresults", compact("liveusers"));
        } else if ($request->operation == "removeSchedule") {
            //
            $meetid = ($request->userdata)["meetid"];
            $meetdetail = ScheduledMeeting::find($meetid);
            $meetparticipants = ScheduledMeetingParticipant::where("meetingid", $meetid)->get();
            // $maillink = $this->cglMeetRepository->mailInvite(
            //     $meetid,
            //     "Cancelled".$meetdetail->title,
            //     new \Datetime($meetdetail->startdate),
            //     new \Datetime($meetdetail->enddate)
            // );

            $helper_variables = array(
                '{receiverFullName}' => 'Sir/Mam',
                '{loggedInUserEmployeeNumber}' => Auth::user()->employee->employee_no,
                '{loggedInUser}' => Auth::user()->getFullNameAttribute(),
                '{maillink}' => "",

            );




            $massinsert = [];
            foreach ($meetparticipants as $meeting) {
                $massinsert[] = ["meetingid" => $meetid, "userid" => $meeting->userid];
                $this->mailQueueRepository
                    ->prepareMailTemplate(
                        "cglmeet_scheduling_cancelled_notification",
                        0,
                        $helper_variables,
                        "Modules\Jitsi\Models\ConferenceRoom",
                        0,
                        $meeting->userid
                    );
            }



            try {
                if (ScheduledMeeting::find($meetid)->delete()) {
                    //ScheduledMeetingParticipant::where("meetingid", $meetid)->delete();
                    $content["code"] = 200;
                    $content["message"] = "Removed successfully";
                    $content["success"] = "success";
                } else {
                    $content["code"] = 406;
                    $content["message"] = "Not able to remove";
                    $content["success"] = "warning";
                    $content["meetingstatus"] = 0;
                }
            } catch (\Throwable $th) {
                $content["code"] = 406;
                $content["message"] = "System error";
                $content["success"] = "warning";
                $content["meetingstatus"] = 0;
            }


            return json_encode($content, true);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function getS3file(Request $request)
    {
        $roomid = $request->id;
        $archive = ConferenceRoom::with(["ConferenceSession", "ConferenceRecording"])->find($roomid);
        $filearray = [];
        $disk = \Storage::disk('s3meet');


        foreach ($archive->ConferenceRecording as $rec) {
            $foldername = (explode("_", explode("/", $rec->recording)[1]))[0];
            $filename = explode("/", $rec->recording)[1];

            $s3Client = new S3Client([
                'version' => 'latest',
                'region' => config('filesystems.disks.s3meet.region'),
                'credentials' => [
                    'key' => config('filesystems.disks.s3meet.key'),
                    'secret' => config('filesystems.disks.s3meet.secret'),
                ]
            ]);
            //$tempCredentials = $stsClient->createCredentials($stsClient->getSessionToken());

            // Create an S3 client using the temporary credentials
            //$s3Client = \Aws\S3\S3Client::factory()->setCredentials($tempCredentials);

            // Get a presigned URL for an Amazon S3 object
            // $signedUrl = $s3Client->getObjectUrl("cgl-meet-dev", $foldername . '/' . $filename, '+10 minutes');
            // dd($signedUrl);

            //Creating a presigned URL
            $cmd = $s3Client->getCommand('GetObject', [
                'Bucket' => config('filesystems.disks.s3meet.bucket'),
                'Key' => $foldername . '/' . $filename
            ]);

            $request = $s3Client->createPresignedRequest($cmd, '+75 minutes');

            // Get the actual presigned-url
            $presignedUrl = (string)$request->getUri();

            $filearray[] =  $presignedUrl;



            // header("Cache-Control: public");
            // header("Content-Description: File Transfer");
            // header("Content-Disposition: attachment; filename=" . basename($assetPath));
            // header("Content-Type: video/mp4");

            // return readfile($assetPath);
        }

        //dd($filearray);
        return view("jitsi::streams3", compact("filearray"));
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
