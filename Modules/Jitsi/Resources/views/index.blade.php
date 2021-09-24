@extends('layouts.app')
<style>
        body{
            overflow-x: hidden;
        }
        .meet {
            /* padding-top: 50px; */
            width: "100%";
            height:  75%;
        }

        .pagep{
            margin-bottom:5px !important;
        }
        .select2-container {
        z-index: 99999;
        }
        .select2-close-mask{
            z-index: 2099;
        }
        .select2-dropdown{
            z-index: 3051;
        }
        .ui-state-active{
            background: orangered !important;
        }

        #tabs ul li{
            /* font-size: 10px; */
        }

        .ui-tabs-panel{
            width:100%;
            padding: 5px !important;
        }
        .ui-tabs-panel button{
            padding-bottom: 3px;
        }
</style>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

@section('content')
<div class="container_fluid">
    <div class="row">
        <div class="col-md-10 table_title">
               <h4>Video Conference</h4>
        </div>
        <div class="col-md-2" >
            <button class="btn btn-primary conferencebutton" style="display: none;float: right;">
                Switch to Conference
            </button>
            <button class="btn btn-primary archivebutton" style="float: right">
                Switch to Archives
            </button>
        </div>
    </div>
</div>
<div class="container_fluid videoview" style="height: 100%">

    <form name="meeting" id="meeting" method="post">
    <div class="initiate row" style="margin-top:20px ">
        {{-- <div class="col-md-5">
            <input name="chooseroom" id="chooseroom" style="display: inline-block"
            class="form-control " autocomplete="off" placeholder="Room Name" />

        </div>
        <div class="col-md-2">
            <input name="roompassword" id="roompassword" type="password"
             class="form-control" placeholder="Password" autocomplete="off" />
        </div> --}}
        @if ($restriction==0)
        <div class="col-md-3">
            <select name="scheduledmeeting" id="scheduledmeeting" class="form-control">
                <option value="0">Select any scheduled meeting</option>
                @foreach ($meetings as $meeting)
                    <option value="{{$meeting->id}}">{{$meeting->title}} ({{date("d M Y H:i A",strtotime($meeting->startdate))}})</option>
                @endforeach
            </select>
        </button>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-primary initiatemeeting form-control" style="">
                    Initiate meeting
            </button>


        <input type="hidden" id="jitsisession" name="jitsisession" value="" />
        <input type="hidden" id="roomid" name="roomid" value="" />
        <input type="hidden" id="myjitsiid" name="myjitsiid" value="" />

        <button class="endmeeting  form-control " style="display: none">
            End meeting
        </button>
        </div>
            @else
            <div class="col-md-12">
                Conference cannot be conducted due to room restrictions.Will notify once we room is free
            </div>
            @endif



    </div></form>

    <div class="row" style="height: 90%">
        <div class="col-md-8 "  style="height: 100%;padding: 0;padding-left: 15px;">
            <div class="meet" id="meet"></div>
            <div class="container_fluid">
                <div class="row shareblock" style="display: none;padding-top: 14px;padding-bottom: 15px;">
                    <div class="col-md-9 " style="
                    padding: 0;
                    padding-left: 15px;">
                        <input type="text"  id="sharelink" name="sharelink" class="form-control" readonly />
                    </div>
                    <div class="col-md-1">
                        <i class="fas fa-copy copysharelink" style="margin-top: 10px"></i>
                    </div>
                    <div class="col-md-2">
                        {{-- <button  type="button" class="btn btn-danger endcall">End call</button> --}}
                    </div>
                    {{-- <div class="col-md-2">
                        <button type="button" id="addparticip" class="endmeeting btn btn-primary  form-control "
                        style="display: none"
                        >
                            Add participant
                        </button>
                    </div> --}}
                </div>
            </div>
        </div>
        <div class="col-md-4" id="searchemployee" style="display: none">
            <div class="container_fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div id="tabs" style="height: 312px">
                            <ul>
                              <li><a href="#tabs-1">Live Employees</a></li>
                              <li><a href="#tabs-2">Participants</a></li>

                            </ul>
                            <div id="tabs-1" style="height: 260px;overflow-y:scroll;">
                                <div style="position: sticky;
                                position: -webkit-sticky;
                                top: 0;">
                                    <input style="display: inline" placeholder="Search by name..."  type="text"
                                        name="searchemp" class="form-control col-md-12" id="searchemp" />
                                    <select style="display:inline" name="projectlist" id="projectlist"
                                    class="form-control  col-md-5">
                                    <option value="0">Select any project</option>
                                        @foreach ($customers as $item)
                                            <option value="{{$item->id}}">{{$item->project_number}}-{{$item->client_name}}</option>
                                        @endforeach

                                    </select>
                                </div>
                                <div id="liveemployeelist">
                                    Live Employee list is loading...
                                </div>
                            </div>

                            <div id="tabs-2"
                            style="padding:0 !important;
                            padding-right:15px !important;padding-left:5px !important">
                                <div class="container_fluid">
                                    <div class="row">

                                        <div class="col-md-12">
                                            {{-- <input type="text" name="employeename" id="employeename" class="form-control" /> --}}
                                            <select name="activeusers"  id="activeusers" class="form-control" >
                                                <option value="0">Select Employee to add to the meeting</option>
                                                @foreach ($users as $user)
                                                    <option value="{{$user->id}}">{{$user->getFullNameAttribute()}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row" style="height: 224px;overflow-y:scroll;">
                                        <div class="col-md-12" style="padding-bottom: 20px">
                                            <div class="container_fluid" id="participants">
                                        </div>
                                    </div>
                                </div>


                            </div>
                            </div>
                          </div>

                    </div>
                </div>
                <div class="row" style="padding-bottom: 50px;height:298px;overflow-y:scroll">
                    <div class="col-md-12">
                        <p class="pagep" style="font-weight: bold;padding-top:6px;padding-bottom: 6px;
                        color:orangered;height:25px">
                            Meeting Title
                        </p>
                        <p class="pagep" style="height: 50px">
                            <textarea class="textcontent"   name="meettitle"
                            id="meettitle" style="width: 100%;resize: none;" maxlength="220" rows="2"></textarea>
                        </p>
                        <p class="pagep" style="font-weight: bold;padding-top:6px;color:orangered;height:25px">
                            Description
                        </p>
                        <p class="pagep">
                            <textarea class="textcontent"  name="description"
                            id="description" style="width: 100%;resize: none;" maxlength="500" rows="2"></textarea>
                        </p>
                        <p class="pagep" style="font-weight: bold;padding-top:6px;color:orangered;height:25px">
                            Client
                        </p>
                        <p class="pagep">
                            <select class="select" name="client" id="client" >
                                <option value="0">Select a customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{$customer->id}}">
                                        {{$customer->project_number."-".$customer->client_name}}
                                    </option>
                                @endforeach
                            </select>
                        </p>
                        <p class="pagep" style="font-weight: bold;padding-top:6px;color:orangered;height:25px">
                            Employee
                        </p>
                        <p class="pagep">
                            <select class="select" name="employee" id="employee" >
                                <option value="0">Select an Employee</option>
                                @foreach ($users as $user)
                                    <option value="{{$customer->id}}">
                                        {{$user->getFullNameAttribute()}}
                                    </option>
                                @endforeach
                            </select>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>

<div class="container_fluid archiveview" style="display: none">
    <div class="row">
        <div class="col-md-12">
            <table id="archivetable" class="table table-bordered">
                <thead >
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Recording</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($myarchives as $archive)
                        @if ($archive->ConferenceRecording->count()>0 || $archive->ConferenceSession->meetingtitle!=""
                        ||  $archive->ConferenceSession->description!="")
                            <tr>
                                <td>{{$archive->ConferenceSession->meetingtitle}}</td>
                                <td>{{$archive->ConferenceSession->description}}</td>
                                <td>
                                    @if ($archive->ConferenceRecording->count()>0)
                                <a target="_blank" href="{{route("jitsi.getVideorecordings",[$archive->id])}}">
                                    Stream
                                </a>
                                    @else
                                        No Recording
                                    @endif

                                </td>
                                <td>
                                    {{date("d M Y h:i A",strtotime($archive->created_at))}}
                                </td>
                            </tr>
                        @endif

                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>


@stop
@section('scripts')

<script src="https://meet.cgl360.ca/external_api.js"></script>
<script>
    var api =null;
    var participinfo=null;
    $(document).on("click",".archivebutton",function(e){
        $(this).hide();
        $(".conferencebutton").show();
        $(".videoview").hide();
        $(".archiveview").show();
    })

    $(document).on("click",".conferencebutton",function(e){
        $(this).hide();
        $(".archivebutton").show();
        $(".archiveview").hide();
        $(".videoview").show();
    })

    $(".initiatemeeting").click(function (e) {
        e.preventDefault();
        let roomName=$("#chooseroom").val();
        let self = this;
        var jwt="";
        var roomnm=""
        $('body').loading({
                    stoppable: false,
                    message: 'Please wait till environment set up completes...'
        });
        $.ajax({
                type: "post",
                url: '{{route("jitsi.initiateMeeting")}}',
                data:$("#meeting").serialize(),
                global: false, //This is the key property.
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    var data = jQuery.parseJSON(response);
                    if(data.flag==true){
                        let roomdetail = data.detail;
                        $("#jitsisession").val(roomdetail["session"])
                        $("#roomid").val(roomdetail["room"])
                        jwt=data.jwt;
                        roomnm=data.roomname;
                        // $("#addparticip").show()
                        $(".initiate").hide()
                        $("#sharelink").val(data.link)
                        $(".shareblock").show()

                        startmeeting(roomnm,self,jwt)

                    }else{
                        swal("warning","Video conference app not available now")
                    }
                    //startmeeting(roomName,self)
                }
            }).done(function(response){
                var data = jQuery.parseJSON(response);
                if(data.flag==true){
                $("#searchemployee").show();
                }
                setTimeout(() => {
                    $('body').loading('stop');
                }, 16000);

            }).fail(function(response){
                $('body').loading('stop');
            });

    });
    $('.timepicker').timepicki();
    $("#createroom").select2({
    placeholder: "Select a room",
    allowClear: true
});
var stoprecording = function(roomName,api){
        api.executeCommand('stopRecording','file')
}


$(document).on("keyup","#searchemp",function(e){
    e.preventDefault();
    let searchres = jitsioperations("searchemp",{"search_key":$("#searchemp").val(),
    "project":$("#projectlist").val()})

})
$(document).on("change","#projectlist",function(e){
    e.preventDefault();
    let searchres = jitsioperations("searchemp",{"search_key":$("#searchemp").val(),
    "project":$("#projectlist").val()})

})

$(".endcall").on("click",function(e){
    e.preventDefault();
    try {
        api.executeCommand('stopRecording', 'file');
    } catch (error) {

    }


    setTimeout(() => {
        api.executeCommand('hangup');
    }, 12000);


})

var startmeeting = function(roomName,self,jwt){
            var domain = "meet.cgl360.ca";
            var options = {
                roomName: roomName,
                width: "100%",
                height: "100%",
                jwt:jwt,
                noSsl: false,
                parentNode: document.querySelector('#meet'),
                configOverwrite: { // filmStripOnly: true
                    startWithVideoMuted: true,
                    disableDeepLinking: true,
                    disableAGC: true },

            }
            api = new JitsiMeetExternalAPI(domain, options);
            document.cookie = "meeting=true";
            api.addEventListeners({
                'participantKickedOut': onParticipantKickedOut,
                'participantJoined':participantJoined,
                'participantLeft':participantLeft,
                'videoConferenceJoined':videoConferenceJoined,

            })
            api.on('readyToClose', () => {
                //stoprecording(roomName,api);
                $('body').loading({
                            stoppable: false,
                            message: 'Please wait...'
                });
                $("#meet").hide();
                $.ajax({
                    type: "post",
                    url: '{{route("jitsi.finishMeeting")}}',
                    data:$("#meeting").serialize(),
                    global: false, //This is the key property.
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        //swal("success","Ready to close","success")
                        $('body').loading('stop');
                        setTimeout(() => {
                            $("#meet").html("");
                            location.reload();
                        }, 1000);
                    }
                });

                });

                api.addEventListener('participantRoleChanged', function (event) {
                    if(event.role === 'moderator') {
                        $.ajax({
                        type: "post",
                        url: '{{route("jitsi.setMeetingstart")}}',
                        data:{"process":"on"},
                        global: false, //This is the key property.
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {

                        }
                    });
                    }
                });
                if($("#scheduledmeeting").val()>0){
                    setTimeout(() => {
                        jitsioperations("getParticipants","");
                    }, 1000);
                }

            $(self).hide()
            // $(".endmeeting").show();
}

function onParticipantKickedOut(object) {
     //console.log("Admin side"+object)
     setTimeout(() => {
        $("#meet").html("")
        location.reload()
    }, 1000);
    getParcipantinfo();
}

function videoConferenceJoined(object){
    //alert(object.id)
    $("#myjitsiid").val(object.id);
    jitsioperations("associateuser",object.id);
    //debugger

}

function participantLeft(object) {
    getParcipantinfo();

//swal("Success","Left the room","success");
setTimeout(() => {
   //location.href="/";
}, 1000);

}
function getParcipantinfo(){
    participinfo=api.getParticipantsInfo();
    console.log(participinfo);
}
function participantJoined(object) {
    getParcipantinfo();

    setTimeout(() => {
        jitsioperations("getParticipants","");
    }, 1000);

}

    $(document).on("keyup",".textcontent",function(e){
        let meettitle=$("#meettitle").val()
        let description=$("#description").val()
        jitsioperations("savemeetingtextmetadata",{"meettitle":meettitle,"description":description})
    })
    $("#client").on("select2:select",function(e){
        let client=$("#client").val()
        jitsioperations("savemeetingcustomermetadata",{"customer_id":client})
    })
    $("#employee").on("select2:select",function(e){
        let employee=$("#employee").val()
        jitsioperations("savemeetingemployeemetadata",{"employee_id":employee})
    })
    $("#activeusers").on("select2:select",function(e){
        if($(this).val()>0)
        {
        $.ajax({
        type: "post",
        url: '{{route("jitsi.employeetomeeting")}}',
        global: false, //This is the key property.
        headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            "roomid":$("#roomid").val(),
            "sessionid":$("#jitsisession").val(),
            "userid":$(this).val(),
        },
        success: function (response) {
            jitsioperations("getParticipants","");
        }
    }).done(function(response){
        $("#activeusers").val("0").select2();
        })
}else{
        swal("Warning","Choose any employee","warning");
    }
})

$(document).on("click",".addliveemp",function(e){
    let self = this;
    $(self).prop('disabled', true);
        if($(this).attr("attr-id")>0)
        {
        $.ajax({
        type: "post",
        url: '{{route("jitsi.employeetomeeting")}}',
        global: false, //This is the key property.
        headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            "roomid":$("#roomid").val(),
            "sessionid":$("#jitsisession").val(),
            "userid":$(this).attr("attr-id"),
        },
        success: function (response) {
            jitsioperations("getParticipants","");
            $(self).prop('disabled', true);
        }
    }).fail(function(e){
        $(self).prop('disabled', true);
    });}else{
        swal("Warning","Choose any employee","warning");
        $(self).prop('disabled', false);
    }
})

$(document).ready(function () {
    $("#archivetable").dataTable()


    $("#projectlist").select2({
        placeholder: "Select a project",
        allowClear: true

    });
    $("#activeusers").select2();
    $(".select").select2();
    // var availableTags = [
    //   "ActionScript",
    //   "AppleScript",
    //   "Asp",
    //   "BASIC",
    //   "C",
    //   "C++",
    //   "Clojure",
    //   "COBOL",
    //   "ColdFusion",
    //   "Erlang",
    //   "Fortran",
    //   "Groovy",
    //   "Haskell",
    //   "Java",
    //   "JavaScript",
    //   "Lisp",
    //   "Perl",
    //   "PHP",
    //   "Python",
    //   "Ruby",
    //   "Scala",
    //   "Scheme"
    // ];
    // $( "#chooseroom" ).autocomplete({
    //   source: availableTags
    // });

    $( "#tabs" ).tabs();
    $("#searchemp").trigger("keyup")

});
var jitsioperations = function(operation,data){
    let responsedata="";
    $.ajax({
        type: "post",
        url: '{{route("jitsi.operations")}}',
        data: {"operation":operation,userdata:data,sessid:$("#jitsisession").val()},
        headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        global: false, //This is the key property.

        success: function (response) {
            responsedata= response
            if(operation=="getParticipants"){
                $("#participants").html(response)
            }else if(operation=="searchemp"){
                $("#liveemployeelist").html(response)
            }
        }
    });
    return responsedata;
}

$(document).on("click",".removeuser",function(e){
    e.preventDefault()
    $.ajax({
        type: "post",
        url: "{{route("jitsi.removeUser")}}",
        headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },data: {
            "roomid":$("#roomid").val(),
            "sessionid":$("#jitsisession").val(),
            "userid":$(this).attr("attr-userid"),
        },
        success: function (response) {
            let jsondata = jQuery.parseJSON(response);
            if(jsondata.success==true){
                jitsioperations("getParticipants","");
            }
        }
    });
})

$(document).on("click","#addparticip",function(e){
    $("#activeusers").val("").select2({
        placeholder: "Select a user",
        allowClear: true

    })
    jitsioperations("getParticipants","");

    $("#myModal").modal();
   // $("#participants").html(usershtml)
})

$(document).on("click",".addemployee",function(e){
    e.preventDefault();
    $.ajax({
        type: "post",
        url: '{{route("jitsi.employeetomeeting")}}',
        global: false, //This is the key property.
        headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            "roomid":$("#roomid").val(),
            "sessionid":$("#jitsisession").val(),
            "userid":$(this).attr("attr-id"),
        },
        success: function (response) {
            jitsioperations("getParticipants","");
        }
    });
})

$(document).on("keyup","#employeename",function(e){
    $.ajax({
        type: "post",
        url: '{{route("jitsi.getEmployees")}}',
        data: {"searchkey":$(this).val()},
        headers:
        {
           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            $("#searchres").html(response)
        }
    });
})

$(".copysharelink").click(function(e){
    e.preventDefault();
    var copyGfGText = document.getElementById("sharelink");

    /* Select the text field */
    copyGfGText.select();

    /* Copy the text inside the text field */
    document.execCommand("copy");


})


</script>
<script src="{{ asset('js/timepicki.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@endsection
<link rel='stylesheet' type='text/css' href="{{ asset('css/timepicki.css') }}" />
