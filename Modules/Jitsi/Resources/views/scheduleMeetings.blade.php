@extends('layouts.app')
@section('css')

<link href="{{ asset('plugins/full-calendar/core/main.css') }}" rel="stylesheet">
<link href="{{ asset('plugins/full-calendar/daygrid/main.css') }}" rel="stylesheet">
{{-- <link href="{{ asset('css/ids.css') }}" rel="stylesheet"> --}}

<script src="{{ asset('plugins/full-calendar/core/main.js') }}"></script>
<script src="{{ asset('plugins/full-calendar/interaction/main.js') }}"></script>
<script src="{{ asset('plugins/full-calendar/daygrid/main.js') }}"></script>


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
        padding-bottom: 10px;
    }
    .select2-container {
    /* z-index: 99999; */
}
.select2-close-mask{
    /* z-index: 2099; */
}
.select2-dropdown{
    /* z-index: 3051; */
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

#calendar {
    /* max-width: 100%; */
    margin: 0px;
}
.fc-unthemed td.fc-today {
    background: #f36424;
}
.fc-more-popover {
    width:23%
}
.fc-event, .fc-event-dot {
    background-color: #0e3a63;
}
.fc-content .fc-title{
    color: #fff !important;
}
.fc-event {
    border: 1px solid #0e3a63;
}
.fc-day-grid-event .fc-content {
    white-space: none !important;
    /* overflow: hidden; */
}
.fc-highlight {
    background: #9595f9 !important;
}
.fc-day-header{
    background: #2c3e50;
    padding: 10px !important;
    color: #fafafa;
}
.fc-toolbar h2 {
    float: left;
    /* margin: 0px 10px 0px 10px !important; */
}
.fc-prev-button{
    float: left;
}

.fc h2 {
    font-size: 23px;
    margin-top: 6px;
}
.fc-button-primary {
    color: #000;
    background-color: #ffffff;
    height: 34px;
    margin-top: 4px;
}
.fc-icon{
    margin-top: -1px;
}

/***END* IDS Calendar Section */

</style>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

@section('content')
<div class="container_fluid">
    <div class="row">
        <div class="col-md-10 table_title">
               <h4>Schedule CGL Meeting</h4>
        </div>
        <div class="col-md-2" >
            <button class="btn btn-primary conferencebutton" style="display: none;float: right;">
                Switch to Scheduling
            </button>
            <button class="btn btn-primary archivebutton" style="float: right">
                Switch to List
            </button>
        </div>
    </div>
    <div class="row">
        <!--- Start -- Calendar Section -->
<div class="col-sm-12 section-display" id="calendar-section">
    <div class="row">

        <div class="col-md-12" style="padding: unset;">
            <div id='calendar'></div>
        </div> <!--- col-md-10 -->




    </div> <!---row  -->
</div> <!---col-sm-12 -->

<!--- End -- Calendar Section -->
</div>
</div>
<div class="container_fluid videoview" style="height: 100%">

    <form name="meeting" id="meeting" method="post">
        <div class="row" style="padding-bottom: 50px;">
            <div class="col-md-8">
                <div class="container_fluid">
                    <div class="row">
                        <p class="pagep col-md-2" >
                            Title
                        </p>
                        <p class="pagep col-md-8" >
                            <input class="form-control"   name="meettitle"
                            id="meettitle"  maxlength="220" />
                        </p>

                    </div>
                    <div class="row">
                        <p class="pagep col-md-2" >
                            Date
                        </p>
                        <p class="pagep col-md-8" >
                        <input type="text" readonly='true'  name="startdate" value="{{date("Y-m-d")}}" class="form-control datepick" />
                        </p>
                    </div>
                    <div class="row">
                        <p class="pagep col-md-2" >
                            Time
                        </p>
                        <p class="pagep col-md-8" >
                        <input type="text" name="starttime" id="starttime" value="" class="form-control " />
                        </p>
                    </div>
                    <div class="row">
                        <p class="pagep col-md-2" >
                            Select duration
                        </p>
                        <p class="pagep col-md-8" >
                            <select name="duration" id="duration" class="form-control" >
                                    <option value="0" selected>Select any</option>
                                @for ($x = 0.25; $x <= 10; $x=$x+0.25) {
                                    <option value="{{$x}}">{{$x}} hours</option>

                                @endfor
                            </select>
                        </p>
                    </div>
                    <div class="row">
                        <p class="pagep col-md-2" >
                            Users
                        </p>
                        <p class="pagep col-md-8" >
                            <select name="activeusers[]"  id="activeusers" class="form-control" multiple>

                                @foreach ($users as $user)
                                    <option value="{{$user->id}}">{{$user->getFullNameAttribute()}}</option>
                                @endforeach
                            </select>
                        </p>
                    </div>

                    <div class="row">
                        <p class="pagep col-md-2" ></p>
                        <p class="pagep col-md-4" >
                            <button class="btn btn-primary" id="saveschedulemeeting">Save</button>
                        </p>

                    </div>
                </div>

            </div>
        </div></form>

    <div class="row" style="height: 80%">
        <div class="col-md-9 "  style="height: 100%;padding: 0;padding-left: 15px;">
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
        <div class="col-md-3" id="searchemployee" style="display: none">
            <div class="container_fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div id="tabs" style="height: 300px">
                            <ul>
                              <li><a href="#tabs-1">Live Employees</a></li>
                              <li><a href="#tabs-2">Participants</a></li>

                            </ul>
                            <div id="tabs-1" style="height: 260px;overflow-y:scroll;">
                                

                            </div>

                            <div id="tabs-2"
                            style="padding:0 !important;
                            font-size:12px;padding-right:15px !important;padding-left:5px !important">
                                <div class="container_fluid">
                                    <div class="row">

                                        <div class="col-md-12">
                                            {{-- <input type="text" name="employeename" id="employeename" class="form-control" /> --}}

                                        </div>
                                    </div>
                                    <div class="row" style="height: 300px;overflow-y:scroll;font-size:12px">
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
                <div class="row" style="padding-bottom: 50px;font-size:12px;height:200px;overflow-y:scroll">
                    <div class="col-md-12">
                        <p class="pagep" style="font-weight: bold;padding-top:6px;color:orangered;height:20px">
                            Meeting Title
                        </p>
                        <p class="pagep" style="height: 25px">
                            <textarea class="textcontent"   name="meettitle"
                            id="meettitle" style="width: 100%;resize: none;" maxlength="220" rows="1"></textarea>
                        </p>
                        <p class="pagep" style="font-weight: bold;padding-top:6px;color:orangered;height:25px">
                            Description
                        </p>
                        <p class="pagep">
                            <textarea class="textcontent"  name="description"
                            id="description" style="width: 100%;resize: none;" maxlength="500" rows="2"></textarea>
                        </p>
                        <p class="pagep" style="font-weight: bold;padding-top:6px;color:orangered">
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
                        <p class="pagep" style="font-weight: bold;padding-top:6px;color:orangered">
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
                        <th>Date</th>
                        <th>Duration</th>
                        <th>Created at</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ScheduledMeeting as $meeting)

                            <tr>
                                <td>{{$meeting->title}}</td>
                                <td>{{
                                date("d M Y H:i A",strtotime($meeting->startdate))
                                }}</td>
                                <td>
                                    {{$meeting->meetinghours*60}} Minutes

                                </td>
                                <td>
                                    {{date("d M Y h:i A",strtotime($meeting->created_at))}}
                                </td>
                                <td>
                                    <i style="cursor:pointer" attr-id="{{$meeting->id}}"
                                        class="removeschedule fa fa-remove"></i>
                                </td>
                            </tr>


                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>


@stop
@section('scripts')

<script>
    var api =null;
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


    $('.timepicker').timepicki();
    $("#createroom").select2({
    placeholder: "Select a room",
    allowClear: true
});
var stoprecording = function(roomName,api){
        api.executeCommand('stopRecording','file')
}

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
            var domain = "meetcgl360.secture.co.in";
            var options = {
                roomName: roomName,
                width: "100%",
                height: "100%",
                jwt:jwt,
                noSsl: false,
                parentNode: document.querySelector('#meet'),
                configOverwrite: { startWithVideoMuted: true },

            }
            api = new JitsiMeetExternalAPI(domain, options);
            api.addEventListeners({
                'participantKickedOut': onParticipantKickedOut,
                'participantJoined':participantJoined,
                'participantLeft':participantLeft
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
                            swal("success","Ready to close","success")
                            $('body').loading('stop');
                            setTimeout(() => {
                                $("#meet").html("")
                                location.reload()
                            }, 1000);
                        }
                    });

                });
            $(self).hide()
            // $(".endmeeting").show();
}

function onParticipantKickedOut(object) {
     //console.log("Admin side"+object)
     setTimeout(() => {
        $("#meet").html("")
        location.reload()
    }, 1000);
}

function participantLeft(object) {

//swal("Success","Left the room","success");
setTimeout(() => {
   //location.href="/";
}, 1000);

}

function participantJoined(object) {
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
    $('#starttime').timepicki();
    $("#activeusers").select2({
        placeholder: "Select a user",
        allowClear: true

    });

    $(".select").select2();
    var availableTags = [
      "ActionScript",
      "AppleScript",
      "Asp",
      "BASIC",
      "C",
      "C++",
      "Clojure",
      "COBOL",
      "ColdFusion",
      "Erlang",
      "Fortran",
      "Groovy",
      "Haskell",
      "Java",
      "JavaScript",
      "Lisp",
      "Perl",
      "PHP",
      "Python",
      "Ruby",
      "Scala",
      "Scheme"
    ];
    $( "#chooseroom" ).autocomplete({
      source: availableTags
    });

    $( "#tabs" ).tabs();
    $(".datepick").datepicker({
                dateFormat: "yy-mm-dd",
                maxDate: "+900y",
                minDate:0
            });


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
            }else if(operation=="removeSchedule"){
                let jsondata = jQuery.parseJSON(response);
                if(jsondata.code==200){
                    swal({
                        title: "Success",
                        text: jsondata.message,
                        type: "success"
                    }, function() {
                        location.reload();
                    });
                }else{
                    swal("Warning",jsondata.message,"warning")
                }
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

$(document).on("click","#saveschedulemeeting",function(e){
    e.preventDefault();
    if($("#meettitle").val()==""){
        swal("Warning","Meeting title cannot be empty","warning")
    }else if($("#duration").val()==0){
        swal("Warning","Please select a duration","warning")
    }else if($("#activeusers").val()==0){
        swal("Warning","Participants cannot be empty","warning")
    }else{
    $.ajax({
            type: "post",
            url: '{{route("jitsi.savescheduleMeeting")}}',
            data:$("#meeting").serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                let jsondata = jQuery.parseJSON(response);
                if(jsondata.code==200){
                    $("#meeting")[0].reset()
                    swal({
                        title: "Success",
                        text: "Saved successfully",
                        type: "success"
                    },
                    function(){
                        location.reload()
                    });
                }else{
                    swal("warning","Something went wrong","warning");
                }
                //startmeeting(roomName,self)
            }
        }).done(function(response){

        }).fail(function(response){

        });
}
})


//Calendar

const ids = {
        ref: {
            calerderDate : moment().format('YYYY-MM-DD'),
            calerderDefaultDate : new Date(),
            calendarEl : null,
            calendar : null,
            calendarEvents : [],
            //   bookedServiceId : null,
            //   bookedOfficeId : null,
            calenderHeight : null,
        },
        init() {
                //Initialize calendar managemet view
                // this.fetchCalendarEvent();
                // this.trigerCalendarClick();
                this.initCalendar();
            //Event listeners
            this.registerEventListeners();
            //Calendar and day slot height
            var calenderHeight = $('#content-div').height();
            this.ref.calenderHeight = parseInt(calenderHeight) - 200;
        },
        registerEventListeners(){
                let root = this;

                /**Start** OnChange office
                * Initialize calendar
                * Fetch Office allocated services
                */
                $("#filter-form").on("change", "#office", function(e) {
                    //Load Calendar Event
                    root.ref.idsofficeId = $("#office").val();
                    $('#toBeRescheduleModal input[name="office_id"]').val(root.ref.idsofficeId);
                    if(root.ref.idsofficeId != 0 && root.ref.idsofficeId != null){
                        root.ref.calerderDefaultDate = new Date();
                        root.ref.calerderDate = moment().format('YYYY-MM-DD');
                        root.fetchCalendarEvent();
                        $("#calendar-section").removeClass("section-display");
                        $("#toBeRescheduleModalButton").removeClass("section-display");

                        /** Start- Fetch Office allocated services */
                        root.officeAllocatedServices($("#office").val());
                        /** End- Fetch Office allocated services */

                    }else{
                    $("#calendar-section").addClass("section-display");
                    $("#toBeRescheduleModalButton").addClass("section-display");
                    }
                });
                /**End** OnChange office */


                /**Start** Rescheduling request modal opening -  */
                $('#toBeRescheduleModalButton').on('click', function() {
                    $('#toBeRescheduleModal').modal();
                    $('#toBeRescheduleModal .modal-title').text("Reschedule Request");
                });
                /**End** Rescheduling request modal opening -  */


        },
        fetchCalendarEvent(){

                let root = this;
                let url = '{{ route("jitsi.scheduledbooking") }}';
                $.ajax({
                    url: url,
                    data: {'date':root.ref.calerderDate},
                    type: 'GET',
                    success: function(data) {
                    root.ref.calendarEvents = data;


                    //root.initCalendar();

                    },
                    error: function(xhr, textStatus, thrownError) {
                        if(xhr.status === 401) {
                                window.location = "{{ route('login') }}";
                        }
                    },
                    contentType: false
                });
        },
        initCalendar() {
            let root = this;

            $("#calendar").empty();
            this.ref.calendarEl = document.getElementById('calendar');
            this.ref.calendar = new FullCalendar.Calendar(this.ref.calendarEl, {
                plugins: ['dayGrid','interaction'],
                header: {
                left: '',
                center: 'prev , title , next',
                right: ''
                },
                defaultView: 'dayGridMonth',
                views: {
                    dayGridMonth: {
                        columnFormat: 'dddd',
                    }
                },
                titleFormat: { // will produce something like "Tuesday, September 18, 2018"
                    month: 'long',
                    year: 'numeric',
                },
                columnHeaderFormat : {
                    weekday: 'long'
                },
                customButtons: {
                    prev: {
                        text: 'Prev',
                        click: function(info) {
                            root.ref.calendar.prev();
                            var month = $('.fc-center h2').html();
                            root.trigerMonthChange(month);
                        }
                    },
                    next: {
                        text: 'Next',
                        click: function(info) {
                            root.ref.calendar.next();
                            var month = $('.fc-center h2').html();

                            root.trigerMonthChange(month);
                        }
                    },
                },

                selectable: true,
                defaultDate: root.ref.calerderDefaultDate,
                // navLinks: true, // can click day/week names to navigate views
                editable: true,
                eventLimit: true, // allow "more" link when too many events
                fixedWeekCount: false,
                height: root.ref.calenderHeight,
                events: root.ref.calendarEvents,
                dateClick: function(info) { // Fetch data on calender click.
                root.ref.calerderDate  = info.dateStr;
                root.trigerCalendarClick();

                },
                eventClick: function(info) { //On clicking an event

                },

            });
            this.ref.calendar.render()
            root.trigerCalendarClick();
        },
        trigerCalendarClick(){
            // let root = this;
            //     let url = '{{ route("idsscheduling-calendar.office.slot-details") }}';
            //     $.ajax({
            //         url: url,
            //         data: {"ids_office_id":root.ref.idsofficeId,'calendar_date':root.ref.calerderDate},
            //         type: 'GET',
            //         success: function(data) {
            //             if(data.success == true){
            //                 root.setDaySlotDataSet(data);
            //             }
            //         },
            //         error: function(xhr, textStatus, thrownError) {
            //             if(xhr.status === 401) {
            //                 window.location = "{{ route('login') }}";
            //             }
            //         },
            //         contentType: false
            //     });
                // root.fetchCaladerEvents(root.ref.calerderDate);
        },
        setDaySlotDataSet(data){
            // console.log(data);
            let root = this;
            $('#slot-list').html();
                $('#day-details-title').html(data.slotTitle);

                var slotList = '';
                $.each(data.slots, function(index, value) {
                    console.log('inside');
                    console.log(value);
                    $.each(value.slots, function(index, slot) {
                        console.log('double inside');
                        console.log(slot);
                        let name = '';
                        let serviceName = '';
                        let className = '';
                        // let day_value = value.day[0];
                        // if(value.day[0].slot != ''){
                        //     var slot_details = JSON.parse(atob(value.day[0].slot));
                        //     if(slot_details != ''){
                        //         name = slot_details.first_name+' '+slot_details.last_name;
                        //         if(slot_details.ids_services !=''){
                        //             serviceName = slot_details.ids_services.name;
                        //         }
                        //     }
                        // }

                            if(slot.status == 1){
                                className = 'open-slot';
                                slot_title = 'Open Slot'
                            }else if(slot.status == 2){
                                className = 'booked-slot';
                                slot_title = 'Booked Slot'
                            }else if(slot.status == 3){
                                className = 'rescheduled-request-slot';
                                slot_title = 'To Be Rescheduled'
                            }else{
                                className = 'blocked-slot';
                                slot_title = 'Temporarily Closed'
                            }

                            let start_time = moment(slot.start_time,"HH:mm:ss").format("hh:mm");
                            let start_time_type = moment(slot.start_time,"HH:mm:ss").format("A");

                            slotList += `<div class='card js-card' data-bookingid="${slot.booking_id}"
                            data-slotname="${slot.display_name}" data-bookingdate="${value.format_date}">
                                            <div class='container ${className}' title="${slot_title}">
                                                <div class="row" class="booking-item">
                                                    <div class="office-slot" id="office-slot">
                                                        <div>${start_time} <span style="font-size: 12px;"> ${start_time_type} </span></div>
                                                        <div>${value.intervel} <span style="font-size: 10px;"> Minutes </span> </div>
                                                    </div>
                                                    <div class="booking-details" id="booking-details" >
                                                        <div class="booked-by" id="booked-by">${slot.bookedBy}</div>
                                                        <div class="service-name" id="service-name">${slot.serviceName}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>`;

                    });
                });
                $('#slot-list').html(slotList);
                var slotListHeight = parseInt(root.ref.calenderHeight) - 110;
                $('#slot-list').css('height',slotListHeight);

        },
        officeAllocatedServices(officeId){
            let root = this;
            let selectBookedServiceId = false;
                var base_url = "{{route('ids-office-services', ':id')}}";
                var url = base_url.replace(':id', officeId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        $('#rescheduleModal #idsServiceId').empty().append($("<option></option>").attr("value",'').text('Please Select'));
                        $.each(data, function(index, service) {
                            if(service.id == root.ref.bookedServiceId){
                                selectBookedServiceId = true;
                            }
                            $('#idsServiceId').append($("<option></option>")
                            .attr("value",service.id)
                            .text(service.name));
                        });
                        if(selectBookedServiceId){
                            $('#rescheduleModal #idsServiceId').val(root.ref.bookedServiceId);
                        }else{
                            $('#rescheduleModal #idsServiceId').val();
                        }
                    }
                });
        },
        trigerMonthChange(monthYear){
            let root = this;

            // Current month set as
            let currentMonth = moment().format("MMMM");
            let monthYearArray = monthYear.split(' ');
            root.ref.calerderDate = moment('01-'+monthYear).format('YYYY-MM-DD');
            if(monthYearArray.length >= 1){
                if(currentMonth == monthYearArray[0]){
                    root.ref.calerderDate = moment().format('YYYY-MM-DD');
                }
            }

            let defaultDate = moment('01-'+monthYear).format('MM-DD-YYYY');
            root.ref.calerderDefaultDate =new Date(moment(defaultDate).format('LLL'));
            root.fetchCalendarEvent();

        }
   }

   // Code to run when the document is ready.
   $(function() {
      //ids.init();
    });
    $(document).on("click",".removeschedule",function(e){
        e.preventDefault();
        let meetid=$(this).attr("attr-id");
        jitsioperations("removeSchedule",{"meetid":meetid});
    })
</script>
<script src="{{ asset('js/timepicki.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@endsection
<link rel='stylesheet' type='text/css' href="{{ asset('css/timepicki.css') }}" />
