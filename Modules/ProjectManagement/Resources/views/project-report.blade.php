@extends('layouts.app')

@section('css')
<link href="{{ asset('css/project_management/style.css') }}" rel="stylesheet">

<style>
    /* Global */
    #content-div {
        overflow-x: hidden;
        /* margin: 9px; */
    }

    .pm-calendar-section {
        max-width: 96vw;
    }

    .pmr-table th {
        color: #ffffff;
        background: #003A63;
        box-shadow: 0 0 black;
    }

    .disabled {
        pointer-events: none;
        opacity: 1;
    }

    .pm-container {
        overflow: auto;

    }

    .pg-comp {
        background: green;
    }

    .pg-incomp {
        background: red;
    }

    .pg-current {
        background: #014210;
    }

    .rotate {
        writing-mode: vertical-lr;
        -webkit-transform: rotate(-180deg);
        -moz-transform: rotate(-180deg);
        font-size: 15px;
    }

    .pm-report-filter {
        padding: 20px;
        margin: 10px 5px;
        background: #f3f3f3;
    }

    .pmr-table td {
        border: 1px solid #f3f3f3
    }

    .pmr-table tr td:last-child {
        border: 1px solid black;
    }

    /* .pmr-table th,
    table thead th {
        border-bottom: 1px solid #052742 !important;
    }*/

    .freeze-col {
        position: absolute;
        left: 0;
        top: auto;
        border-top-width: 1px;
        margin-top: -1px;
    }

    .pm-container .nav-pills .nav-link.active {
        background-color: #F17437;
    }

    /* Report page */
    .pmr-table .site-el,
    .pmr-table .project-el {
        background: #F17437;
        color: white;
    }

    .pmr-table .site-el,
    .pmr-table .project-el .pmr-table .group-el {
        vertical-align: middle;
        text-align: center;
    }

    .pmr-table .group-el,
    .pmr-table .status-details-el {
        color: #ffffff;
        background: #365063;
    }

    #pm-report-table .task-done {
        background-color: #4bad4b;
        color: white;
    }


    #pm-report-table td:not(.status-details-el) {

        vertical-align: middle;
    }

    #pm-report-table td {
        white-space: nowrap;
        text-align: left;
    }

    #pm-report-table th,
    #pm-report-table td.aligncenter {
        white-space: nowrap;
        text-align: center;

    }


    /* Calendar page */

    #pm-cal-table td {
        white-space: nowrap;
        text-align: left;
        vertical-align: middle;
        border: 1px solid #f3f3f3;
    }

    #pm-cal-table th {
        white-space: nowrap;
        text-align: center;
    }

    table td {
        white-space: initial;
    }

    .assignee {
        font-size: 10px;
        word-break:  break-word;

    }

    a:hover {
        color: #007bff;
    }

    .sweet-alert h2 {
        margin: 14px 0px;
        font-size: 21px !important;
    }

    .sweet-alert {
        max-height: 60vh;
        overflow-y: auto;
    }

    .panel th {
        font-weight: bold;
        color: rgb(33, 37, 41);
    }

    .panel td {
        color: rgb(33, 37, 41);
    }

    .alert-table {
        table-layout: fixed;
        width: 100%;
    }
    .pendingTask
    {
        text-align: center !important;
    }


</style>
@endsection



@section('content')
<div class="pm-container">
    <div class="table_title">
        <h4>Project Report</h4>
    </div>
    @include('projectmanagement::partials.pm-filter')
    <div class="mt-3">
        <ul class="nav nav-pills mb-3" id="pm-report-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="pm-home-tab" data-toggle="pill" href="#pm-home-content" role="tab" aria-controls="pills-home" aria-selected="true">Report Table</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pm-cal-tab" data-toggle="pill" href="#pm-cal-content" role="tab" aria-controls="pills-profile" aria-selected="false">Calendar</a>
            </li>
        </ul>
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pm-home-content" role="tabpanel" aria-labelledby="pm-home-tab">
                @include('projectmanagement::partials.pm-report-table')
            </div>
            <div class="tab-pane fade" id="pm-cal-content" role="tabpanel" aria-labelledby="pm-cal-tab">
                @include('projectmanagement::partials.pm-report-calendar')
            </div>
        </div>
    </div>
</div>

<!-- Task Status -->
<div class="modal fade" id="task-status-modal" data-backdrop="static" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Task Status</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="task-status-form">
                <div class="modal-body">
                    {{ Form::hidden('id', null) }}
                    {{ Form::hidden('task_id', null) }}
                    <div class="form-group" id="percentage">
                        <label for="concern" class="col-sm-3 control-label">Percentage</label>
                        <div class="col-sm-11">
                            <select class="form-control" name="percentage" id="percentage-field" required>
                                <option value="" selected disabled>Select Percentage</option>
                                @for ($i = 0; $i <= 20 ; $i++) <option value="{{$i*5}}">{{$i*5}}%</option>
                                    @endfor
                            </select>
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group" id="notes">
                        <label for="severity" class="col-sm-3 control-label">Notes</label>
                        <div class="col-sm-11">
                            <textarea name="notes" rows="10" class="form-control" required></textarea>
                            <small class="help-block"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                    {{ Form::submit('Cancel',array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
                </div>
            </form>
        </div>
    </div>
</div>


@include('projectmanagement::partials.pm-rating')

@stop

@section('scripts')
<script>
    const pmr = {
        reports: [],
        init() {
            //Fetch report data
            this.getReportData();
            //Register all event listeners
            this.registerEventListeners();
            //Init select2
            this.initSelect2();
        },
        registerEventListeners() {
            let root = this;
            //On filter search
            $('#pm-filter-search').on('click', function() {
                root.getReportData();
            });
            //On filter reset
            $('#pm-filter-reset').on('click', function() {
                root.resetFilters();
                root.getReportData();
            });
            //On new task status button pressed
            $(".pmr-table").on("click", ".new-task-status-btn", function(e) {
                $('#task-status-form input[type=hidden]').val('');
                $('#percentage-field').prop('selectedIndex', 0)
                $('#task-status-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
                $('#task-status-form')[0].reset();
                $('#task-status-modal input[name="task_id"]').val($(this).data('task-id'));
                $('#project-modal .modal-title').text("Task Status");
                $("#task-status-modal").modal();
            });

            $('#task-rate-form').submit(function(e) {
                e.preventDefault();
                root.onRateSubmit();
            });
            $('#pm-cal-table').on('click', '.rate-task', function(e) {
                e.preventDefault();
                root.onRateTaskShow($(this).data('task-id'));
            });

            // $('#percentage-field').on('change',function()
            // {
            //   $("textarea[name='notes']").val('');
            // });

            //On edit task status button pressed
            $(".pmr-table").on("click", ".edit-task-status-btn", function(e) {
                $('#task-status-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
                $('#task-status-form')[0].reset();
                root.onEditTaskStatus($(this).data('id'));
            });


            $('#pm-cal-table').on('click', '.popupShow', function(e) {
                var task_id = $(this).data('task-id');
                var base_url = "{{route('user-task-status.show',[':task_id'])}}";
                var url = base_url.replace(':task_id', task_id);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        if (data.success) {
                            var swal_html = '<div class="panel"> <div class="panel-body"><table align="center" class="table alert-table">';
                            swal_html += '<thead><tr><th style="font-weight: bold;color:rgb(33, 37, 41);width:40%;">Date</th><th style="width:20%;">&nbspStatus</td><th style="text-align: center; vertical-align: middle;width:40%;">&nbspNotes</th></tr></thead>';
                            $.each(data.result, function(index, value) {
                                let notes_str = value.notes;
                                notes_str_clip = notes_str;
                                if (notes_str.length > 20) {
                                    notes_str = '<span class="show-btn nowrap" ' +
                                        'onclick="$(this).hide();$(this).next().show();">' +
                                        notes_str_clip.substr(0, 20) +
                                        '..<a href="javascript:;" title="Expand" class="fa fa-chevron-circle-down cgl-font-blue"></a>' +
                                        '</span>' +
                                        '<span class = "notes big-notes" style="display:none;word-break: break-word;" onclick="$(this).hide();$(this).prev().show();">' +
                                        notes_str + '&nbsp;&nbsp;' +
                                        '<a href="javascript:;" title="Collapse" class="fa fa-chevron-circle-up cgl-font-blue"></a>' +
                                        '</span><br/>\r\n';
                                }
                                swal_html += '<tr><td style="text-align: center;color:rgb(33, 37, 41); vertical-align: middle; width:40%;">' + formatDate(value.status_date) + '</td><td style="text-align: center; vertical-align: middle;">&nbsp' + Math.round(value.percentage) + '%</td><td style="text-align: left; vertical-align: middle;width:40%;">' + notes_str + '</td></tr>';
                            });
                            swal_html += '</table></div></div>';
                            var can_rate_task = 0;
                            @can('rate_project_management_task')
                            var can_rate_task = 1;
                            @endcan
                            swal({
                                title: "Status History",
                                text: swal_html,
                                html: true,
                                confirmButtonText: "Rate Task",
                                cancelButtonText: "Ok",
                                showConfirmButton: can_rate_task,
                                showCancelButton: true,
                                showLoaderOnConfirm: true,
                                closeOnConfirm: true,
                            }, function(isConfirm) {
                                if (isConfirm) {
                                    root.onRateTaskShow(task_id);
                                }

                            });
                            $(".sweet-alert").animate({
                                scrollTop: 0
                            }, 2000);
                        }
                    },
                    fail: function(response) {
                        swal("Oops", "Something went wrong", "warning");
                    },
                    error: function(xhr, textStatus, thrownError) {
                        associate_errors(xhr.responseJSON.errors, $form, true);
                    },
                })
            })

            //On submit Task status
            $('#task-status-form').submit(function(e) {
                e.preventDefault();
                root.onSubmitTaskStatus();
            });

        },
        collectFilters() { //Collect filter data
            return $('#pm-filter-form').serialize();

        },
        resetFilters() { //Reset filters
            $('#pm-filter-form')[0].reset();
            $(".pm-select2").trigger("change");
        },
        initSelect2() {
            $('.pm-select2').select2();
        },
        isToday(someDate) {
            const today = new Date()
            return someDate.getDate() == today.getDate() &&
                someDate.getMonth() == today.getMonth() &&
                someDate.getFullYear() == today.getFullYear()
        },
        currentMonthDateRange() {
            let date = new Date();
            let firstDay = date;
            let lastDay = new Date();
            lastDay.setDate(lastDay.getDate() + 30);
            // let firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
            // let lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
            return this.getDateRange(firstDay, lastDay);
        },
        getDateRange(startDate, stopDate) {
            startDate.setHours(0, 0, 0, 0);
            stopDate.setHours(0, 0, 0, 0);
            var dateArray = new Array();
            var currentDate = startDate;
            while (currentDate <= stopDate) {
                dateArray.push(new Date(currentDate));
                currentDate = moment(currentDate)
                    .add(1, 'd')
                    .toDate();
            }
            return dateArray;
        },
        getAppliedDateRange(maximumDuedate = null) {
            let start = $('#startdate').val() ? $('#startdate').val() : new Date();
            let end = $('#enddate').val();

            if (start && end) {
                return this.getDateRange(new Date(start), new Date(end));
            }
            if (maximumDuedate) {
                return this.getDateRange(new Date(start), new Date(maximumDuedate));
            }
            return this.currentMonthDateRange();
        },
        getReportData() {
            let root = this;
            let url = '{{ route("pm.report-api") }}';
            //Apply filters
            url += `?${this.collectFilters()}`;

            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    if (response.data) {
                        root.reports = response.data;
                        root.renderReportTable();
                        root.renderCalendarTable();
                    } else {
                        swal("Oops", "Could not retrive data.", "warning");
                    }
                },
                error: function(xhr, textStatus, thrownError) {
                    swal("Oops", "Something went wrong", "warning");
                },
            });
        },
        renderReportTable() {
            let table = $('#pm-report-table');
            let rows = '';

            this.reports.forEach(function(report, rindex) {
                let isShowSite = true;
                report.projects.forEach(function(project, pindex) {
                    let isShowProject = true;
                    if (project.total_tasks == 0) {
                        return;
                    }
                    project.groups.forEach(function(group, gindex) {
                        let isShowGroup = true;
                        group.tasks.forEach(function(task, tindex) {
                            let sdates = '';
                            let stats = '';
                            let notes = '';
                            let task_str = '';
                            let statusCard = '';
                            task_str = task.name;
                            let completed_date=task.completed_date_formatted ?task.completed_date_formatted :'--';
                            let completed_date_class=task.completed_date_formatted ? '' :'pendingTask';
                            let taskCompletedHideClass = task.is_completed ? 'hide-this-block' : '';
                            let task_str_clip = task_str;
                            if (task_str.length > 35) {
                                task_str =
                                    '<span class="show-btn nowrap" ' +
                                    'onclick="$(this).hide();$(this).next().show();">' +
                                    task_str_clip.substr(0, 30) +
                                    '..<a href="javascript:;" title="Expand" class="fa fa-chevron-circle-down cgl-font-blue"></a>' +
                                    '</span>' +
                                    '<span class = "notes big-notes" style="display:none" onclick="$(this).hide();$(this).prev().show();">' +
                                    task_str + '&nbsp;&nbsp;' +
                                    '<a href="javascript:;" title="Collapse" class="fa fa-chevron-circle-up cgl-font-blue"></a>' +
                                    '</span><br/>\r\n';
                            }

                            //Task color
                            let taskDoneClass = task.is_completed ? ' task-done' : '';
                            let taskCompletedStatusClass = task.is_completed ? 'disabled' : '';
                            let selectedUserId = ($('#user_id').val() != "") ? Number($('#user_id').val()) : {{Auth::user()->id}}

                            //task owner ,follower logic integration
                            let task_assignee_ids = [];
                            let task_owners = '';
                            task.task_owners.forEach(function(task_owner, sindex) {
                                if (task_owner.user) {
                                    task_owners += task_owner.user.full_name + "<br />";
                                    task_assignee_ids.push(task_owner.user.id);
                                }
                            });
                            if (task_owners != '') {
                                task_owners = "<span class='assignee'>Task Owner</span><br/>" + task_owners;
                            }

                            let consider_followers = false;
                            @if($followerConfigurationValue == "1")
                            consider_followers = true;
                            @else
                            if (task.followers_can_update) {
                                consider_followers = true;
                            }
                            @endif

                            let task_followers = '';
                            task.followers.forEach(function(follower, sindex) {
                                if (follower.user) {
                                    task_followers += follower.user.full_name + "<br />";

                                    if (consider_followers) {
                                        task_assignee_ids.push(follower.user.id);
                                    }
                                }
                            });
                            if (task_followers != '') {
                                task_followers = "<br /><span class='assignee'>Task Follower</span><br/>" + task_followers;
                            }

                            let enable_edit_task = task_assignee_ids.indexOf(selectedUserId);
                            @if((!Gate::check('admin')) && (!Gate::check('super_admin')))
                            if ((enable_edit_task < 0)) {
                                taskCompletedStatusClass = 'disabled';
                                taskCompletedHideClass = 'hide-this-block';
                            }
                            @endif

                            task.status.forEach(function(status, sindex) {
                                let time = status.time.replace(/^0+/, '')
                                let status_notes_str = '';
                                status_notes_str = status.notes;
                                let status_updated_by = (status.updated_by) ? '(' + status.updated_by.full_name + ')' : '';
                                let status_notes_str_clip = status_notes_str;
                                if (status_notes_str.length > 20) {
                                    status_notes_str =
                                        '<span class="show-btn" ' +
                                        'onclick="$(this).hide();$(this).nextAll().show();$(this).next().hide();">' +
                                        status_notes_str_clip.substr(0, 20) +
                                        '..<a href="javascript:;" title="Expand" class="fa fa-chevron-circle-down"></a>' + '&nbsp;' +
                                        '</span>' +
                                        '<a href="#" >@can('update_report')<i class="fa fa-edit edit-task-status-btn ' + taskCompletedHideClass + '" data-id=' + status.id + '></i>@endcan</a><span class = "notes big-notes" style="display:none" onclick="$(this).hide();$(this).next().show();$(this).prev().prev().show();">' +
                                        status_notes_str + '&nbsp;<sub class="assignee">' + status_updated_by + '</sub>&nbsp;&nbsp;' +
                                        '<a href="javascript:;" title="Collapse" class="fa fa-chevron-circle-up"></a>' + '&nbsp;' +
                                        '</span>' + '<a href="#" style="display:none">@can('update_report')<i class="fa fa-edit edit-task-status-btn ' + taskCompletedHideClass + ' " data-id=' + status.id + '></i>@endcan</a>';
                                    status_notes_str = '<p>' + status_notes_str + '</p><br />';
                                } else {
                                    status_notes_str = '<p>' + status_notes_str + '&nbsp;<sub class="assignee">' + status_updated_by + '</sub>&nbsp;' +
                                        '@can('update_report')<i class="fa fa-edit edit-task-status-btn ' + taskCompletedHideClass + '" data-id=' + status.id + '"></i>@endcan</p><br>';
                                }
                                // sdates += `<p>${status.formatted}<br>${time}</p>`;
                                // stats += `<p>${status.percentage}%</p><br>`
                                // notes += `${status_notes_str}`

                                //old code-start
                                // statusCard += `<tr><td style="width:80px !important; border: 0px; vertical-align: text-top;">${moment(status.formatted).format('MMM DD, YYYY')}<br />${time}</td><td style="white-space: initial; width:6% !important; border-top: 0px;text-align:center; border-bottom: 0px; vertical-align: text-top;">${status.percentage}%</td><td style="white-space: initial; border: 0px; vertical-align: text-top;width:30% !important;word-break:break-word !important;">${status_notes_str}</td></tr>`;
                                //old code-end

                                 statusCard += `<tr><td style="width:30% !important; border: 0px; vertical-align: text-top;">${moment(status.formatted).format('MMM DD, YYYY')}<br />${time}</td><td style="white-space: initial; width:20% !important; border-top: 0px;text-align:center; border-bottom: 0px; vertical-align: text-top;">${status.percentage}%</td><td style="white-space: initial; border: 0px; vertical-align: text-top;width:50% !important;word-break:break-word !important;">${status_notes_str}</td></tr>`;
                            });
                            //Site span logic
                            //  let isShowSite = !(tindex > 0 || gindex > 0 || pindex > 0);
                            //isShowSite=true;
                            let siteEl = `<td  style="white-space: initial;" class="site-el" rowspan="${report.total_tasks}">${report.project_number} - ${ report.client_name}</td>`;

                            //Project span logic
                            // let isShowProject = !(tindex > 0 || gindex > 0);
                            let projectEl = `<td style="white-space: initial;" class="project-el" rowspan="${project.total_tasks}">${ project.name}</td>`;

                            //Group span logic
                            // let isShowGroup = !(tindex > 0);
                            let groupEl = `<td style="white-space: initial;" class="group-el" rowspan="${group.tasks.length}">${ group.name}</td>`;


                            let row = `<tr>
                                ${isShowSite?siteEl:''}
                                ${isShowProject?projectEl:''}
                                ${isShowGroup?groupEl:''}
                                <td  style="white-space: initial;" class="task ${taskDoneClass}">${task_str}</td>
                                <td class="task ${taskDoneClass}" style="white-space: initial;">${task_owners}${task_followers}</td>
                                <td class="task ${taskDoneClass}">${moment(task.updated_at_formatted).format('MMM DD, YYYY')}</td>
                                <td class
                                ="task ${taskDoneClass}" style="white-space: initial;">${task.due_date_formatted}</td>
                                <td class="task ${taskDoneClass} ${completed_date_class}" style="white-space: initial;">${completed_date}</td>
                                <td class="status-details-el" style="padding:0px;height: 150px;"><table style="width:100% !important;height:100% !important;height: 150px;table-layout:fixed;">${statusCard}</table></td>
                                @can('update_report')
                                <td><button data-task-id="${task.id}" type="button" class="btn btn-sm btn-primary new-task-status-btn ${taskCompletedStatusClass}" title="Update"><i class="fa fa-plus p-0" style="color: white !important" aria-hidden="true"></i></button></td>
                                @endcan
                            </tr>`;

                            rows += row;
                            isShowSite = false;
                            isShowProject = false;
                            isShowGroup = false;

                        });
                    });
                });
            });
            table.find('tbody.primary-tbody').html(rows);
        },
        renderCalendarTable() {
            let root = this;
            let table = $('#pm-cal-table');
            let rows = '';
            let ranges = root.getAppliedDateRange();
            if ($('#user_id').val()) {
                let max_due_date = '1970-01-01';
                this.reports.forEach(function(report, rindex) {
                    let isShowSite = true;

                    report.projects.forEach(function(project, pindex) {
                        let isShowProject = true;
                        if (project.total_tasks == 0) {
                            return;
                        }
                        project.groups.forEach(function(group, gindex) {
                            let isShowGroup = true;
                            group.tasks.forEach(function(task, tindex) {

                                if (task.due_date > max_due_date) {
                                    max_due_date = task.due_date;
                                }
                                isShowSite = false;
                                isShowProject = false;
                                isShowGroup = false;
                            });

                        });
                    });

                });
                if (max_due_date <= moment().format("YYYY-MM-DD")) {
                    ranges = root.getAppliedDateRange(null);
                } else {
                    ranges = root.getAppliedDateRange(max_due_date);
                }
            }
            let headEl = '';
            //Remove existing header els
            $('.gen-el-cal-head').remove();
            //Generate table heder rows
            ranges.forEach(function(dt) {
                let date = moment(dt).format('dddd  MMMM DD, YYYY');
                let day = moment(dt).format('dddd')
                headEl += `<th style="border:1px solid white;"  class="gen-el-cal-head ${day}"><span class="rotate">${date}</span></th>`;

            });

            //Genreate table body

            this.reports.forEach(function(report, rindex) {
                let isShowSite = true;
                report.projects.forEach(function(project, pindex) {
                    let isShowProject = true;
                    if (project.total_tasks == 0) {
                        return;
                    }
                    project.groups.forEach(function(group, gindex) {
                        let isShowGroup = true;
                        group.tasks.forEach(function(task, tindex) {
                            //Site span logic
                            let task_str = '';
                            task_str = task.name;
                            let task_str_clip = task_str;
                            let completed_date=task.completed_date_formatted ?task.completed_date_formatted :'--';
                            let completed_date_class=task.completed_date_formatted ? '' :'pendingTask';
                            if (task_str.length > 35) {
                                task_str =
                                    '<span class="show-btn nowrap" ' +
                                    'onclick="$(this).hide();$(this).next().show();">' +
                                    task_str_clip.substr(0, 30) +
                                    '..<a href="javascript:;" title="Expand" class="fa fa-chevron-circle-down cgl-font-blue"></a>' +
                                    '</span>' +
                                    '<span class = "notes big-notes" style="display:none" onclick="$(this).hide();$(this).prev().show();">' +
                                    task_str + '&nbsp;&nbsp;' +
                                    '<a href="javascript:;" title="Collapse" class="fa fa-chevron-circle-up cgl-font-blue"></a>' +
                                    '</span><br/>\r\n';
                            }
                            // let isShowSite = !(tindex > 0 || gindex > 0 || pindex > 0);

                            let siteEl = `<td  style="white-space: initial;" class="site-el"rowspan="${report.total_tasks}">${report.project_number} - ${ report.client_name}</td>`;


                            //Project span logic
                            // let isShowProject = !(tindex > 0 || gindex > 0);
                            let projectEl = `<td style="white-space: initial;" class="project-el" rowspan="${project.total_tasks}">${ project.name}</td>`;

                            //Group span logic
                            // let isShowGroup = !(tindex > 0);
                            let groupEl = `<td style="white-space: initial;" class="group-el" rowspan="${group.tasks.length}">${ group.name}</td>`;

                            //Task color
                            let taskDoneClass = task.is_completed ? ' task-done' : '';

                            //Generate progress bar
                            let pgEls = '';

                            ranges.forEach(function(range) {
                                let pgClass = '';
                                let pgBov = '';
                                if (range <= new Date(task.due_date) && range >= new Date(task.created_at)) {
                                    pgClass = task.is_completed ? 'pg-comp' : 'pg-incomp';
                                } else {
                                    pgClass = 'pg-none';
                                }

                                if (root.isToday(range)) {
                                    pgClass = 'pg-current';
                                    pgBov = 'border-bottom:#014210;border-top:#014210;';

                                }
                                pgEls += `<td class="task pg-el ${pgClass}" style="${pgBov}border-right:none;border-left:none;"></td>`;
                            });

                            let task_owners = '';
                            task.task_owners.forEach(function(task_owner, sindex) {
                                if (task_owner.user) {
                                    task_owners += task_owner.user.full_name + "<br />";
                                }
                            });
                            if (task_owners != '') {
                                task_owners = "<span class='assignee'>Task Owner</span><br/>" + task_owners;
                            }

                            let task_followers = '';
                            task.followers.forEach(function(follower, sindex) {
                                if (follower.user) {
                                    task_followers += follower.user.full_name + "<br />";
                                }
                            });
                            if (task_followers != '') {
                                task_followers = "<br /><span class='assignee'>Task Follower</span><br/>" + task_followers;
                            }
                            var actions = '';
                            actions = '<a href="#" class="popupShow" data-task-id= ' + task.id + ' >' + task.status[0].percentage + '%</a>';
                            @can('rate_project_management_task')
                            actions += '<br><br><button class="btn btn-sm rate-task" data-task-id= ' + task.id + '><i class="fa fa-star"></i>  Rate Task</button>';
                            @endcan
                            let row = `<tr>
                                ${isShowSite?siteEl:''}
                                ${isShowProject?projectEl:''}
                                ${isShowGroup?groupEl:''}
                                <td style="white-space: initial;"  class="task ${taskDoneClass}">${task_str}</td>
                                <td class="task ${taskDoneClass}">${task_owners}${task_followers}</td>
                                <td class="task" >${task.due_date_day}<br>${task.due_date_std}</td>
                                <td class="task ${completed_date_class}" >${completed_date}</td>
                                <td class="task" style="text-align:center;">
                                ${actions}
                                </td>
                                ${pgEls}
                            </tr>`;

                            rows += row;
                            isShowSite = false;
                            isShowProject = false;
                            isShowGroup = false;

                        });
                    });
                });
            });
            table.find("thead>tr").append(headEl);
            $('.Saturday, .Sunday').css('background-color', 'black');

            table.find('tbody').html(rows);
            $('.task').css('border', '1px solid black')
        },
        onSubmitTaskStatus() {
            let root = this;
            let $form = $('#task-status-form');
            url = "{{ route('task-status.store') }}";
            let formData = new FormData($('#task-status-form')[0]);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function(data) {
                    $("#task-status-modal").modal('hide');
                    if (data.success) {
                        swal({
                            title: "Saved",
                            text: "Task status has been saved",
                            type: "success"
                        }, function() {
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                            $('#task-status-form')[0].reset();
                            root.getReportData();
                        });
                    } else {
                        swal("Oops", "The record has not been saved", "warning");
                    }
                },
                fail: function(response) {
                    swal("Oops", "Something went wrong", "warning");
                },
                error: function(xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form, true);
                },
                contentType: false,
                processData: false,
            });
        },
        onRateTaskShow(task_id) {
            var url = '{{ route("task.show",":id") }}';
            url = url.replace(':id', task_id);
            $.ajax({
                url: url,
                type: "GET",
                success: function(data) {
                    if (data) {
                        $('#task-rate-form')[0].reset();
                        $('#task-rate-modal input[name="id"]').val(data.id);
                        $('#task-rate-modal input[name="name"]').val(data.name);
                        $('#task-rate-modal input[name="group_id"]').val(data.group_id);
                        $('#task-rate-modal input[name="project_id"]').val(data.project_id);
                        $('#task-rate-modal select[name="deadline_rating_id"] option[value="' + data.deadline_rating_id + '"]').prop('selected', true);
                        $('#task-rate-modal select[name="value_add_rating_id"] option[value="' + data.value_add_rating_id + '"]').prop('selected', true);
                        $('#task-rate-modal select[name="initiative_rating_id"] option[value="' + data.initiative_rating_id + '"]').prop('selected', true);
                        $('#task-rate-modal select[name="commitment_rating_id"] option[value="' + data.commitment_rating_id + '"]').prop('selected', true);
                        $('#task-rate-modal select[name="complexity_rating_id"] option[value="' + data.complexity_rating_id + '"]').prop('selected', true);
                        $('#task-rate-modal select[name="efficiency_rating_id"] option[value="' + data.efficiency_rating_id + '"]').prop('selected', true);
                        $('#task-rate-modal textarea[name="rating_notes"]').val(data.rating_notes);
                        $('#task-rate-modal select[name="deadline_weightage"] option[value="' + data.deadline_weightage + '"]').prop('selected', true);
                        $('#task-rate-modal select[name="value_add_weightage"] option[value="' + data.value_add_weightage + '"]').prop('selected', true);
                        $('#task-rate-modal select[name="initiative_weightage"] option[value="' + data.initiative_weightage + '"]').prop('selected', true);
                        $('#task-rate-modal select[name="commitment_weightage"] option[value="' + data.commitment_weightage + '"]').prop('selected', true);
                        $('#task-rate-modal select[name="complexity_weightage"] option[value="' + data.complexity_weightage + '"]').prop('selected', true);
                        $('#task-rate-modal select[name="efficiency_weightage"] option[value="' + data.efficiency_weightage + '"]').prop('selected', true);
                        $('#task-modal-title').text("Rate Task: " + data.name);

                        $("#task-rate-modal").modal();
                    } else {
                        swal("Oops", "Could not retrieve data.", "warning");
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    swal("Something went wrong!", "Please try again", "error");
                },
                contentType: false
            });
        },
        onEditTaskStatus(id) {
            let root = this;
            let url = '{{ route("task-status.show",":id") }}';
            url = url.replace(':id', id);
            $('#task-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    if (data) {
                        $('#task-status-modal input[name="id"]').val(data.id);
                        $('#task-status-modal input[name="task_id"]').val(data.task_id);
                        $('#task-status-modal textarea[name="notes"]').val(data.notes);
                        $('#task-status-modal select[name="percentage"] option[value="' + data.percentage + '"]').prop('selected', true);
                        $('#task-status-modal-title').text("Edit Task Status");
                        $("#task-status-modal").modal();
                    } else {
                        swal("Oops", "Could not retrive data.", "warning");
                    }
                },
                error: function(xhr, textStatus, thrownError) {
                    swal("Oops", "Something went wrong", "warning");
                },
                contentType: false
            });
        },
        onRateSubmit() {
            let root = this;
            let $form = $('#task-rate-form');
            url = "{{ route('task-rating.store') }}";
            let formData = new FormData($('#task-rate-form')[0]);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function(data) {
                    $("#task-rate-modal").modal('hide');
                    if (data.success) {
                        swal({
                            title: "Saved",
                            text: "Task rating has been saved",
                            type: "success",
                            closeOnConfirm: true
                        }, function() {
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                            $('#task-rate-form')[0].reset();
                        });
                    } else {
                        swal("Oops", "The record has not been saved", "warning");
                    }
                },
                fail: function(response) {
                    swal("Oops", "Something went wrong", "warning");
                },
                error: function(xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form, true);
                },
                contentType: false,
                processData: false,
            });
        },
    }

    function formatDate(date) {
        var d = new Date(date);
        var options = {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: 'numeric',
            minute: 'numeric',
            second: 'numeric'
        };
        var date_formatted = d.toLocaleDateString("en-US", options);
        return date_formatted.replace(/^(.+?,.+?),\s*/g, '$1<br>');
    }

    /// Code to run when the document is ready.
    $(function() {
        pmr.init();
    });
</script>

@stop
