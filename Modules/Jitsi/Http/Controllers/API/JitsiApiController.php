<?php

namespace Modules\Jitsi\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Jitsi\Models\ConferenceParticipant;
use Modules\Jitsi\Models\ConferenceRecording;
use Modules\Jitsi\Models\ConferenceRoom;
use Modules\Jitsi\Models\ConferenceSession;
use Modules\Jitsi\Models\ConferenceStatus;
use Modules\Jitsi\Models\ConferenceRecordingServer;


class JitsiApiController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('timetracker::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('timetracker::create');
    }


    public function saveRecording(Request $request)
    {
        $roomname = $request->get("room_name");
        $recording = $request->get("recording");
        $explodedarray = explode("/", $recording);
        $lastkey = array_key_last(explode("/", $recording));
        $filename = ($explodedarray[$lastkey]);
        if (count($explodedarray) > 1) {
            $filename = $explodedarray[$lastkey - 1] . "/" . $filename;
        }
        $conference = ConferenceRoom::where("room_name", $roomname)->first();
        $roomid = $conference->id;
        $conferencesession = ConferenceSession::where("roomid", $roomid)->latest()->first();
        $sessionid = $conferencesession->id;
        ConferenceRecording::create([
            "roomid" => $conference->id,
            "sessionid" => $sessionid, "recording" => $filename
        ]);
    }
    public function getJitsiOwner(Request $request)
    {
        $roomName = $request->roomName;
        $roomDetails = ConferenceRoom::where("room_name", $roomName)->first();
        $roomId = $roomDetails->id;
        $conferenceParticipant = ConferenceParticipant::where("sessionid", $roomId)->first();
        if ($conferenceParticipant->jitsiuserid != "") {
            return $conferenceParticipant->jitsiuserid;
        } else {
            return null;
        }
    }
    public function getJibriConferenceStatus(Request $request)
    {
        $roomName = $request->roomName;
        $roomDetails = ConferenceRoom::where("room_name", $roomName)->first();
        if (isset($roomDetails)) {
            $roomid = $roomDetails->id;
            $sessiondetails = ConferenceSession::where("roomid", $roomid)->first();
            $status = $sessiondetails->status;
            $content["code"] = 200;
            $content["message"] = "Retrieved successfully";
            $content["success"] = "success";
            $content["status"] = $status;
        } else {
            $content["code"] = 406;
            $content["message"] = "DB Error";
            $content["success"] = "warning";
            $content["status"] = 0;
        }
        return json_encode($content, true);
    }
    public function getIdlerecordingserver(Request $request)
    {
        $recordings = ConferenceRecordingServer::where("permanentonserver", 0)->get();
        $offserver = "";
        $idleserver = "";
        foreach ($recordings as $record) {
            $ip = $record->ip;
            $instanceid = $record->instanceid;
            $link = "http://" . $ip . ":2222/jibri/api/v1.0/health";
            //$curl = \HTTP::get($link);
            $client = new \GuzzleHttp\Client();
            try {
                $res = (new \GuzzleHttp\Client())->get($link, [
                    'timeout' => 15
                ]);
                $parsedvalue = json_decode($res->getBody(), true);
                if ($parsedvalue["status"]["busyStatus"] == "IDLE") {
                    $idleserver = $instanceid;
                }
            } catch (\Throwable $th) {
                $offserver = $instanceid;
            }
        }
        $serverstatus = ["idleserver" => $idleserver, "offserver" => $offserver];
        return json_encode($serverstatus, true);
        //dd($offserver);
    }


    public function rebootServer(Request $request)
    {
        $conferencestatus = ConferenceStatus::find(1);
        $conferencestatus->conferencecount = 0;
        $conferencestatus->save();
    }

    public function updateConferencecount(Request $request)
    {
        $bodyContent = json_decode($request->getContent(), true);
        $confcount = count($bodyContent);

        $conferencestatus = ConferenceStatus::find(1);
        $conferencestatus->conferencecount = $confcount;
        $conferencestatus->save();
    }



    public function getShutdownprocedure(Request $request)
    {
        $permanentrecordingservercount = ConferenceRecordingServer::where("permanentonserver", 1)
            ->count();
        $recordings = ConferenceRecordingServer::where("permanentonserver", 0);
        $recordingservercount = $recordings->count();
        $status = ConferenceStatus::find(1);
        $totalmeetings = $status->conferencecount;
        if ($totalmeetings < $permanentrecordingservercount) {
            $totalmeetings = 0;
        }
        $offserver = "";
        $idleserver = "";
        $idleserverarray = [];
        foreach ($recordings->get() as $record) {
            $ip = $record->ip;
            $instanceid = $record->instanceid;
            $link = "http://" . $ip . ":2222/jibri/api/v1.0/health";
            //$curl = \HTTP::get($link);
            $client = new \GuzzleHttp\Client();
            try {
                $res = (new \GuzzleHttp\Client())->get($link, [
                    'timeout' => 15
                ]);
                $parsedvalue = json_decode($res->getBody(), true);
                if ($parsedvalue["status"]["busyStatus"] == "IDLE") {
                    $idleserver = $instanceid;
                    $idleserverarray[] = $instanceid;
                }
            } catch (\Throwable $th) {
                $offserver = $instanceid;
            }
        }
        $expectedshutdownarray = [];
        if (count($idleserverarray) > 0) {
            for ($i = $totalmeetings; $i < $recordingservercount; $i++) {
                try {
                    $expectedshutdownarray[] = ["name" => $idleserverarray[$i]];
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }
        }

        return json_encode(["data" => $expectedshutdownarray], true);
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
        return view('timetracker::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('timetracker::edit');
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
