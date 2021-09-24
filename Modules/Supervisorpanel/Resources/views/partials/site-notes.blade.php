<div>
    <!-- <a href='javascript: history.go(0)'><img src="{{ asset('images/left_arrow.png') }}" style='width: 40px;height:30px'></a> -->
    <!-- <a href='javascript: history.go(0)'><i class="fa fa-arrow-left fa-1x" aria-hidden="true" style="font-size: 23px;padding-left: 14px;padding-bottom: 15px;"></i></a> -->
    <a href='javascript: history.go(0)' style="font-size: 23px;padding-left: 14px;padding-bottom: 15px; color: #f26538;font-weight: bold;font-size: 16px;display: inline-block;" id="back" class="hidden">Back</a>
    <div class="form-group row" id="site-notes-container">
        <div class="col-xs-12 col-md-5 site-note-right">
            <div class="form-group row">
                <label for="date" class="col-sm-4 col-form-label">Date<span class="mandatory">*</span></label>
                <div class="col-sm-8">
                <input id="site-note-date" class="form-control" required="" readonly="" name="date" type="text"
                        value="@if(isset($note_data)) {{\Carbon\Carbon::parse($note_data->created_at)->format($site_note_dateformat) }}  @else {{\Carbon\Carbon::now()->format($site_note_dateformat) }} @endif">
                    <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                </div>
            </div>
            <div class="form-group row">
                <label for="time" class="col-sm-4 col-form-label">Time<span class="mandatory">*</span></label>
                <div class="col-sm-8">
                    <input id="site-note-time" class="form-control" required="" readonly="" name="time" type="text"
                        value="@if(isset($note_data)) {{\Carbon\Carbon::parse($note_data->created_at)->format('H:i') }}  @else {{\Carbon\Carbon::now()->format('H:i') }}  @endif">
                    <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                </div>
            </div>
            <div class="form-group row">
                <label for="subject" class="col-sm-4 col-form-label">Subject<span class="mandatory">*</span></label>
                <div class="col-sm-8">
                <input id="site-note-subject" class="form-control" required="" name="subject" type="text" value="{{$note_data->subject or ''}}" maxlength="50">
                    <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12" id="note-data-subject"></span></div>
                </div>
            </div>
            <div class="form-group row">
                <label for="attendees" class="col-sm-4 col-form-label">Attendees<span class="mandatory">*</span></label>
                <div class="col-sm-8">
                    <input id="site-note-attendees" class="form-control" required="" name="attendees" type="text" value="{{$note_data->attendees or ''}}" maxlength="100">
                    <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12" id="note-data-attendees"></span></div>
                </div>
            </div>
            <div class="form-group row">
                <label for="location" class="col-sm-4 col-form-label">Location<span class="mandatory">*</span></label>
                <div class="col-sm-8">
                    <input id="site-note-location" class="form-control" required="" name="location" type="text" value="{{$note_data->location or ''}}" maxlength="100">
                    <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12" id="note-data-location"></span></div>
                </div>
            </div>
            <div class="form-group row team-roster-group" >
                <div class="col-sm-4"></div>
                <div class="col-sm-8">
                <button id="team_roster" class="btn add-new float-left" >View Team Roster</button>
                </div>
                <div class="form-group row team-roster-table-div">
                    <table id="team-roster-table" style="display: none;">
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Phone</th>
                            <th>Email</th>
                        </tr>
                        @foreach($allocated_users as $user)
                        <tr>
                            <td>{{$user->full_name}}</td>
                            <td>{{App\Services\HelperService::snakeToTitleCase($user->roles[0]->name)}}</td>
                            <td>{{$user->employee_profile->phone}}</td>
                            <td>{{$user->email}}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-7 site-note-left">
            <div class="form-group row">
                <label class="col-sm-12 col-form-label">Notes<span class="mandatory">*</span></label>
                <div class="col-sm-12">
                <textarea id="site-note-notes" class="form-control" name="notes" cols="100" maxlength="10000">{{$note_data->notes or ''}}</textarea>
                    <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"  id="note-data-notes"></span></div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-8 col-form-label">Next Steps</label>
            </div>
            <div class="form-group row">
                <table id="site-note-task-tbl"  >
                    <tr>
                        <th>Task</th>
                        <th>Assigned To</th>
                        <th>Due Date</th>
                        <th>Status</th>
                    </tr>
                    @if(isset($note_data->siteNoteTask) && count($note_data->siteNoteTask) >= 1)
                    @foreach($note_data->siteNoteTask as $index => $each_note_task)
                        @include('supervisorpanel::partials.site-notes-tasks')
                    @endforeach
                    @else
                        @include('supervisorpanel::partials.site-notes-tasks')
                    @endif
                </table>
            </div>
            <div class="form-group row">
                <div class="col-sm-12 task-controls">
                    <label id="site-note-add-tasks" class="col-form-label pull-right" onclick="addTask()">+ Add Tasks</label>
                    <label id="site-note-remove-tasks" class="col-form-label pull-right" onclick="removeTask()">- Remove Tasks</label>
                </div>
            </div>
            <div class="add-new" data-title="Generate Report" onclick="saveSiteNotes()"><span class="add-new-label">Save</span></div>
        </div>
    </div>
</div>
<script>

$(document).ready(function(){
  $('.site-note-user').select2();
});
    function saveSiteNotes(){
        $(".team-roster-table").text('');
        var task = prepareSiteNoteData();
        fetchData(task);
        $("#site-note-remove-tasks").hide();
    }



    function prepareSiteNoteData(){
        var date = $("#site-note-date").val();
        var time = $("#site-note-time").val();
        var subject = $("#site-note-subject").val();
        var attendees = $("#site-note-attendees").val();
        var location = $("#site-note-location").val();
        var notes = $("#site-note-notes").val();
        var tasks = $("#site-note-task-tbl")[0];
        var task_rows = $("#site-note-task-tbl")[0].rows;
        var task_count = task_rows.length;
        var task_list_arr = [];
        for(i = 1; i < task_count; i++){
            var task_obj = {};
            var each_task_row = task_rows[i];
            task_obj['task_id'] = $(each_task_row).data('task-id');
            task_obj['task_subject'] = $(each_task_row).find('.site-note-task-subject').val()
            task_obj['assignee'] = $(each_task_row).find('.site-note-user').val();
            task_obj['due_date'] = $(each_task_row).find('.site-note-due-date').val();
            task_obj['task_status'] = $(each_task_row).find('.site-note-task-status').val();
            task_list_arr.push(task_obj);
        }
        var task = {};
        task["date"] = date;
        task["time"] = time;
        task["subject"] = subject;
        task["attendees"] = attendees;
        task["location"] = location;
        task["notes"] = notes;
        task["task_list"] = task_list_arr;
        return task;
    }

    function addTask(){
        getTaskHTML();
        refreshSideMenu();
        if($("#site-note-task-tbl tr").length <= 2){
            $("#site-note-remove-tasks").hide();
        } else {
            $("#site-note-remove-tasks").show();
        }
    }
    function removeTask(){
        //clearTaskValues();
        $("#site-note-task-tbl tr:last").remove();
        if($("#site-note-task-tbl tr").length <= 2 || $("#site-note-task-tbl tr:last span:first").is(":visible")){
            $("#site-note-remove-tasks").hide();
        }
    }

    function fetchData(task){
        var base_url = "{{ route('customer.sitenotes.save',[':customer_id',':note_id']) }}";
        var url = base_url.replace(':customer_id', {{$customer_id}});
        url = url.replace(':note_id', {{$note_data->id or 0}});
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Content-Type': 'application/json'
            },
            url: url,
            type: 'POST',
            data: JSON.stringify(task),
            success: function (data) {
                if (data.success) {
                    $(".site-note-tab ul li a.active").attr('onclick','siteNoteClick(this,'+data.id+')')
                    $(".site-note-tab ul li a.active").click();
                    refreshSideMenu();
                    swal("Saved", data.message, "success");
                } else {
                    console.log(data);
                }
            },
            fail: function (response) {
                console.log('Unknown error');
            },
            error: function (xhr, textStatus, thrownError) {
                for(var key in xhr.responseJSON.errors){
                    var selector = key.replace(/\./g,'-');
                    $('#note-data-'+selector).text(xhr.responseJSON.errors[key][0]);
                    $('#note-data-'+selector).closest('tr').addClass('error-present');
                }
            },
            processData: false,
        });
    }

    function getTaskHTML(){
        var rowHtml = $("#site-note-task-tbl tr:last").html();
        let idIndex = 2;
        $("#site-note-task-tbl tbody").append('<tr class="site-note-tasks" data-task-id="0">'+rowHtml+'</tr>');
        $("#site-note-task-tbl tr:last span.help-block").each(function( index ) {
            let splitId = $(this).attr('id').split('-');
            let splitIndex = splitId.length - idIndex;
            let newId = "";
            splitId[splitIndex] = parseInt(splitId[splitIndex])+1;
            newId = splitId.join('-');
            $(this).attr('id',newId);
            });
        $("#site-note-task-tbl tr:last .date-input").addClass('datepicker');
        clearTaskValues();
    }

    function clearTaskValues(){
        $("#site-note-task-tbl tr:last").find('.help-block').text('');
        $("#site-note-task-tbl tr:last").find('input').removeAttr('value');
        $("#site-note-task-tbl tr:last").find('input').val('');
        $("#site-note-task-tbl tr:last").find('select').val('');
        $("#site-note-task-tbl tr:last").find('select option').removeAttr('selected');
        $("#site-note-task-tbl tr:last").find('input, select').removeAttr('disabled');
        $("#site-note-task-tbl tr:last").find('.site-note-task-subject-span').css('display','none');
        $("#site-note-task-tbl tr:last").find('input').css('display','block');
        //date picker
        $("#site-note-task-tbl tr:last").find('input.datepicker').removeAttr('data-datepicker');
        $("#site-note-task-tbl tr:last").find('input.datepicker').removeAttr('value');
        var addDatePicker = $("#site-note-task-tbl tr:last").find('input.datepicker');
        addDatePicker.datepicker({
            format: "yyyy-mm-dd",
            showOtherMonths: true
        });
        //Datepicker date format
        $(".datepicker").mask("9999-99-99");
        $("#site-note-task-tbl tr:last .datepicker").val('');
    }


    $("#team_roster").click(function(){
        $("#team-roster-table").toggle();

        $(this).text(function(i, text){
        return text === "View Team Roster" ? "Hide Team Roster" : "View Team Roster";
    })


});




</script>
