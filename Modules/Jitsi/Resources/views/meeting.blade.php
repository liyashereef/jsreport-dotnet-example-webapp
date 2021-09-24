@extends('layouts.app')
<style>
    body{
        overflow-x: hidden;
    }
    .meet {
        width: "100%";
        height:  80%;
    }
    @media screen
    and (device-width: 320px)
    and (device-height: 640px)
    and (-webkit-device-pixel-ratio: 3) {
        .videoview {
            display: none;
        }
    }
    @media only screen and (max-width: 780px) {
        .videoview {
            display: none;
        }
    }


</style>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@section('content')
        <div class="row">
            <div class="col-md-12 table_title">
                <h4>Conference Room</h4>
            </div>
        </div>
        <div class="row">
        <div class="col-md-12" style="min-height:800px;bottom:0 !important">
            <input type="hidden" id="jitsisession" name="jitsisession" value="{{$sessid}}" />
            <input type="hidden" id="roomid" name="roomid" value="{{$roomid}}" />
            <input type="hidden" id="roomname" name="roomname" value="{{$roomname}}" />
            <input type="hidden" id="myjitsiid" name="myjitsiid" value="" />
            <input type="hidden" id="owner" name="owner" value="{{$owner->jitsiuserid}}" />

            <div id="meet" class="meet"></div>
        </div>
    </div>
@endsection
<script src="https://meet.cgl360.ca/external_api.js"></script>

@section('scripts')
<script>
    var joineduserid=0;
    var startmeeting = function(roomName){
    var domain = "meet.cgl360.ca";
    var api=null;
    let name = {!! json_encode($membername) !!};
            var options = {
                roomName: roomName,
                width: "100%",
                height: "100%",
                parentNode: document.querySelector('#meet'),
                configOverwrite: { // filmStripOnly: true
                    startWithVideoMuted: true,
                    disableDeepLinking: true
                     },
                interfaceConfigOverwrite: {
                    // filmStripOnly: true
                },
                userInfo: {
                    id:"jitsi"+name,
                    email: name+'@cgl360.ca',
                    displayName: name
                }
            }
            api = new JitsiMeetExternalAPI(domain, options);
          
            api.addEventListeners({
                'participantKickedOut': onParticipantKickedOut,
                'participantJoined':participantJoined,
                'participantLeft':participantLeft,
                'videoConferenceJoined':videoConferenceJoined,
                'videoConferenceLeft':videoConferenceLeft
            })

            api.on('readyToClose', () => {
                //jitsioperations("removejitsiuser",joineduserid);
                api.executeCommand('hangup');
                swal("success","Ready to close","success");
                $("#meet").html("")
                setTimeout(() => {
                    location.href="/"
                }, 1000);
            });
            api.addEventListener('participantRoleChanged', function (event) {
                
            });
    }

function videoConferenceLeft(object){
    // console.log(object);
    // debugger
}
function videoConferenceJoined(object){
    $("#myjitsiid").val(object.id);
}
function onParticipantKickedOut(object) {
     let kickedoutuserid = object.kicked.id;
     if(kickedoutuserid==$("#myjitsiid").val()){
         swal("Warning","Meeting terminated by admin","warning")
         setTimeout(() => {
             location.href={!! json_encode($homeurl) !!};
         }, 500);
     }
    // let roomName = $("#roomname").val()
    // setTimeout(() => {
    //    jitsioperations("getSessionStatus",roomName);
    // }, 500);

}
function participantLeft(object) {
    
     let kickedoutuserid = object.id;
     if(kickedoutuserid==$("#myjitsiid").val() || kickedoutuserid==$("#owner").val()){
         swal("Warning","Thanks for attending the meeting","warning")
         setTimeout(() => {
             location.href={!! json_encode($homeurl) !!};
         }, 500);
     }
     

     
    
}

function getParcipantinfo(){
    
}
function participantJoined(object) {
     joineduserid=object.id
     if($("#myjitsiid").val()==""){
        $("#myjitsiid").val(joineduserid)
     }
     jitsioperations("associateuser",joineduserid);
}

var jitsioperations = function(operation,data){
    $.ajax({
        type: "post",
        url: '{{route("jitsi.operations")}}',
        global:false,
        data: {"operation":operation,userdata:data,sessid:$("#jitsisession").val()},
        headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            if(operation=="getSessionStatus"){
                if(response==1){
                    swal("success","Meeting terminated by host","success");
                    $("#meet").html("")
                    setTimeout(() => {
                        location.href="/"
                    }, 1000);
                }
            }
        }
    });
}

$(document).ready(function () {
    let roomName = $("#roomname").val()
    startmeeting(roomName);
});
    </script>

@endsection
