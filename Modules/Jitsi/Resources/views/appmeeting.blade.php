@extends('layouts.cgl360_conference_layout')
<style>
    body{
        overflow-x: hidden;
        overflow-y: hidden
    }
    .meet {
        width: "100%";
        height:  100%;
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
.button-group-center{
    visibility: hidden;
}
    .navbar{
        margin-bottom: 0px !important;
    }
    .content-div{
        height: 100% !important;
    }

    .closebutton {
        padding-right: 30px !important;
        padding-left: 30px !important;
        padding-top: 6px !important;
        padding-bottom: 6px !important;
        font-size: 0.9rem;
        text-transform: uppercase;
        -webkit-box-shadow: 0px 4px 7px -3px rgba(0, 0, 0, 0.64);
        -moz-box-shadow: 0px 4px 7px -3px rgba(0, 0, 0, 0.64);
        box-shadow: 0px 4px 7px -3px rgba(0, 0, 0, 0.64);
    }
.btn-grad {
  --background: none;
  background-image: linear-gradient(to right, #f06c2b, #f1501c, #f13f16, #f12711);
  color: #ffffff;
  border-radius: 50px;
  width: 50%;
}



</style>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@section('content')

        <div class="row" style="height: 100%">
        <div class="col-md-12"  >
            {{-- <input type="hidden" id="jitsisession" name="jitsisession" value="{{$sessid}}" /> --}}
            {{-- <input type="hidden" id="roomid" name="roomid" value="{{$roomid}}" /> --}}
            <input type="hidden" id="roomname" name="roomname" value="{{$roomname}}" />
            <input type="hidden" id="myjitsiid" name="myjitsiid" value="" />
            <input type="hidden" id="owner" name="owner" value="{{$owner}}" />

            <div id="meet" name="meet" class="meet">
            </div>
            {{-- <div id="video" name="video"></div> --}}
            
        </div>
        {{-- <div class="col-md-12" style="display: inline !important">
            <select name="audiodev" id="audiodev" class="form-control">
                
            </select>
        </div>
        <div class="col-md-12"  style="display: inline !important">    
            <button id="refreshaudio" class="form-control btn btn-primary">Refresh Audio output</button>
        </div>
        <div class="col-md-12">
            <button id="toggleaudio" class="form-control btn btn-primary">Set Default Audio Output</button>
        </div> --}}
    </div>
@endsection
<script src="https://meet.cgl360.ca/external_api.js"></script>

@section('scripts')
<script>
    var api=null;
    $(document).on("click",".closebutton",function(e){
        $("#stopbutton").trigger("click");
        try {
            window.close();
        } catch (error) {

        }
    })

    
    var joineduserid=0;
    var startmeeting = function(roomName){
    var domain = "meet.cgl360.ca";
    let name = {!! json_encode($username) !!};
            var options = {
                roomName: roomName,
                width: "100%",
                height: "100%",
                parentNode: document.querySelector('#meet'),
                configOverwrite: { 
                    startWithAudioMuted: false,
                    startWithVideoMuted: true,
                    disableDeepLinking: true,
                    disableAGC: true 
                },
                interfaceConfigOverwrite: {
                    // filmStripOnly: true                    
                    // disableDeepLinking: true
                },
                userInfo: {
                    email: name+'@cgl360.ca',
                    displayName: name
                }

            }
            api = new JitsiMeetExternalAPI(domain, options);
            setTimeout(() => {
                $("#refreshaudio").trigger("click");
            }, 1000);
            api.addEventListeners({
                'participantKickedOut': onParticipantKickedOut,
                'participantJoined':participantJoined,
                'participantLeft':participantLeft,
                'videoConferenceJoined':videoConferenceJoined
            })
            
            api.on('readyToClose', () => {
                $("#toggleaudio").hide()
                //jitsioperations("removejitsiuser",joineduserid);
                $("#meet").html(`<center>Thanks for attending the conference</center>
                </center><center>
                <p style="margin-top: 30px;text-align: center">
                        <a href="javascript:history.go(-3)" class="closebutton btn-grad">Close</a>
                    </p>
                </center>`);
                $("#meet").css("padding-top","80px")
                $("#mainnavbar").show()
                setTimeout(() => {
                    // history.go(-2);
                }, 1000);
                });
    }


function onParticipantKickedOut(object) {
    let kickedoutuserid = object.kicked.id;
     if(kickedoutuserid==$("#myjitsiid").val()){
            $("#meet").html(`<center>Thanks for attending the conference</center>
                </center><center>
                    <p style="margin-top: 30px;text-align: center">
                        <a href="javascript:history.go(-3)" class="closebutton btn-grad">Close</a>
                    </p>
                </center>`);
                    $("#meet").css("padding-top","80px")
                    $("#mainnavbar").show()

        setTimeout(() => {
           // history.go(-1);
        }, 1000);
     }
     

}

function videoConferenceJoined(object){
    $("#myjitsiid").val(object.id);
}



function participantLeft(object) {

        try {
            
        } catch (error) {
            
        }
    // let userstatus=jitsioperations("getSessionStatus",$("#roomname").val());
    if(object.id==$("#owner").val()){
            $("#toggleaudio").hide()
            //jitsioperations("removejitsiuser",joineduserid);
            $("#meet").html(`<center>Thanks for attending the conference</center>
            </center><center>
            <p style="margin-top: 30px;text-align: center">
                    <a href="javascript:history.go(-3)" class="closebutton btn-grad">Close</a>
                </p>
            </center>`);
            $("#meet").css("padding-top","80px")
            $("#mainnavbar").show()
            setTimeout(() => {
                // history.go(-2);
            }, 1000);
    }
    if(joineduserid==$("#myjitsiid").val()){

    //jitsioperations("removejitsiuser",joineduserid);
    //  $("#meet").html(`<center>Thanks for attending the conference</center>
    //         </center><center>
    //             <p style="margin-top: 30px;text-align: center">
    //                 <a href="javascript:history.go(-3)" class="closebutton btn-grad">Close</a>
    //             </p>
    //          </center>`);
    //             $("#meet").css("padding-top","80px")
    //             $("#mainnavbar").show()
    //             setTimeout(() => {
    //                 //history.go(-1);
    //             }, 1000);
     }
}

function participantJoined(object) {
     joineduserid=object.id
     if($("#myjitsiid").val()==""){  
     }
     $("#myjitsiid").val(joineduserid)
     //jitsioperations("associateuser",joineduserid);
}

var jitsioperations = function(operation,data){
    // $.ajax({
    //     type: "post",
    //     url: '{{route("jitsi.operations")}}',
    //     data: {"operation":operation,userdata:data,sessid:$("#jitsisession").val()},
    //     headers: {
    //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //     },
    //     success: function (response) {

    //     }
    // });
}

$(document).ready(function () {
    let roomName = $("#roomname").val()
    startmeeting(roomName);
    
});

$(document).on("click","#refreshaudio",function(e){
    e.preventDefault();
    let defaultoutobject="";
   
    try {
        api.getAvailableDevices().then(devices => {
            
            if(devices["audioOutput"]){
                $.each(devices["audioOutput"], function (indexInArray, valueOfElement) { 
                    defaultoutobject+="<option value="+valueOfElement["deviceId"]+">"+valueOfElement["label"]+"</option>"
                });
                
                // $("#audiodev").html(defaultoutobject);
                
                

            }
    
});
    } catch (error) {
        console.log(error)
    }
})

$(document).on("click","#toggleaudio",function(e){
    e.preventDefault();
    let defaultoutobject=null;
    let deviceoutputiddefault=null;
    let deviceoutputidother=null;
    let outobject=null;
    try {
        // api.getAvailableDevices().then(devices => {
        //     if(devices["audioOutput"]){
        //         let label=$("#audiodev option:selected").text();
        //         let deviceId=$("#audiodev option:selected").val();
        //         api.setAudioOutputDevice(deviceId, label);
        //         swal("Success","Audio output changed","success")
        //     }
    
// });
    } catch (error) {
        console.log(error)
    }
})
    </script>

@endsection
