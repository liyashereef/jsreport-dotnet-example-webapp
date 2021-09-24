@extends('layouts.app')
<style>
    body{
        overflow-x: hidden;
        overflow-y: hidden
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

    .navbar{
        margin-bottom: 0px !important;
    }

</style>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@section('content')
        <div class="row">
            <div class="col-md-12 table_title" >
                <h4>Conference Room</h4>
            </div>
        </div>
        <div class="row">
        <div class="col-md-12" style="height: 600px">
            {{-- <input type="hidden" id="jitsisession" name="jitsisession" value="{{$sessid}}" /> --}}
            {{-- <input type="hidden" id="roomid" name="roomid" value="{{$roomid}}" /> --}}
            <input type="hidden" id="roomname" name="roomname" value="{{$roomname}}" />
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
    let name = {!! json_encode($username) !!};
            var options = {
                roomName: roomName,
                width: "100%",
                height: "100%",
                parentNode: document.querySelector('#meet'),
                configOverwrite: { startWithVideoMuted: true,
                disableDeepLinking: true },
                interfaceConfigOverwrite: {
                    // filmStripOnly: true
                    startWithVideoMuted: true,
                    disableDeepLinking: true
                },
                userInfo: {
                    email: 'email@cgl360.ca',
                    displayName: name
                }
            }
            var api = new JitsiMeetExternalAPI(domain, options);
            api.addEventListeners({
                'participantKickedOut': onParticipantKickedOut,
                'participantJoined':participantJoined,
                'participantLeft':participantLeft
            })

            api.on('readyToClose', () => {
                jitsioperations("removejitsiuser",joineduserid);
                $("#meet").html(`<center>Thanks for attending the conference</center>`)
                setTimeout(() => {
                   // history.go(-1);
                }, 1000);
            });
    }


function onParticipantKickedOut(object) {
     let kickedoutuserid = object.kicked.id;
     jitsioperations("removejitsiuser",joineduserid);
     $("#meet").html(`<center>Thanks for attending the conference</center>`)
    setTimeout(() => {
        //history.go(-1);
    }, 1000);

}
function participantLeft(object) {

     jitsioperations("removejitsiuser",joineduserid);
     $("#meet").html(`<center>Thanks for attending the conference</center>`)
    setTimeout(() => {
        //history.go(-1);
    }, 1000);

}

function participantJoined(object) {
     joineduserid=object.id
     jitsioperations("associateuser",joineduserid);
}

var jitsioperations = function(operation,data){
    $.ajax({
        type: "post",
        url: '{{route("jitsi.operations")}}',
        data: {"operation":operation,userdata:data,sessid:$("#jitsisession").val()},
        headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {

        }
    });
}

$(document).ready(function () {
    let roomName = $("#roomname").val()
    let flag = {!! json_encode($flag) !!};
    if(flag>0){
        startmeeting(roomName);
    }else{
        $("#meet").html("There is no ongoing meeting.Please wait till meeting starts")
    }

});
    </script>

@endsection
