<?php

namespace Modules\Jitsi\Repositories;

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

use Modules\Admin\Models\User;
use \Firebase\JWT\JWT;
use Modules\Jitsi\Models\ConferenceRecording;
use Modules\Jitsi\Models\ConferenceRecordingServer;
use Spatie\CalendarLinks\Link;

class CglMeetRepository extends Controller
{
    public function __construct()
    {
    }

    public function mailInvite($meetid, $meettitle, $startdate, $enddate)
    {


        $from =  $startdate;
        $to =  $enddate;
        $link = Link::create(str_replace(" ", "&#32;", $meettitle), new \DateTime($from), $to)
            ->description(config("app.url") . '/jitsi/joinscheduledmeeting/' . $meetid)
            ->address(config("app.url") . '/jitsi/joinscheduledmeeting/' . $meetid);

        // Generate a link to create an event on Google calendar
        //echo $link->google();

        // Generate a link to create an event on Yahoo calendar
        //echo $link->yahoo();

        // Generate a link to create an event on outlook.com calendar
        $genlink = $link->webOutlook();


        $expectedlink = str_replace("live", "office365", $genlink);

        //$expectedlink = str_replace("+", "%20", $expectedlink);
        //dd($expectedlink);
        return $expectedlink;

        // Generate a data uri for an ics file (for iCal & Outlook)
        //echo $link->ics();

    }
}
