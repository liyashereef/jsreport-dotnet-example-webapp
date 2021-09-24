@extends('layouts.app')

@section('css')
<link href="{{ asset('css/project_management/style.css') }}" rel="stylesheet">
<style>
    .disable_div {
        pointer-events: none;
        opacity: 0.4;
    }

    #assigned_to_div,
    #followers_div .select2-selection__rendered {
        color: white !important;
    }

    .pmc .pm-sub-table {
        box-shadow: none !important;
    }

    .pm-btn {
        margin: 0px 2px;
    }

    .pm-action-td {
        text-align: center;
    }

    .new-project-btn {
        margin-bottom: 5px;
        clear: both;
    }

    .pmt-lv2 td {
        background-color: #efedec;
    }

    .pmc .pm-sub-table th {
        background-color: #655d59;
    }

    .pmt-lv1 tbody tr {
        background-color: white !important;
    }

    .zero-padding {
        padding: 0px !important;
    }

    .pm-action-td {
        align: left;
    }
</style>
@endsection

@section('content')
<div class="pmc">
    <div class="table_title">
        <h4>Project Management</h4>
    </div>

<div class="col-md-6 customer_filter_main">
    <div class="row">
        <div class="col-md-3"><label class="filter-text customer-filter-text">Customer </label></div>
        <div class="col-md-6 filter customer-filter">
            <select class="form-control option-adjust client-filter select2" name="clientname-filter" id="clientname-filter">
                <option value="">Select customer</option>
                @foreach($customers as $eachCustomername)
                <option value="{{ $eachCustomername->id}}">{{ $eachCustomername->client_name .' ('.$eachCustomername->project_number.')' }}
                </option>
                @endforeach
            </select>
            <span class="help-block"></span>
        </div>
    </div>
</div>
    <div id="message"></div>

    <button class="btn btn-lg new-project-btn add-new" data-title="Add New Project" style="margin-top:0px">New Project</button>

    <!-- Project Table -->
    <table class="pm-table table table-bordered"  id="pm-project-table">
        <thead>
            <tr>
                <th></th>
                <th>Id</th>
                <th>Project</th>
                <th>Created Date</th>
                <th>Customer Name</th>
                <th class="text-center"></th>
            </tr>
        </thead>
    </table>
</div>

<!-- Project Modal -->
<div class="modal fade" id="project-modal" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Project</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'project-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}

            <div class="modal-body">
                <div class="form-group" id="name">
                    <label for="severity" class="col-sm-3 control-label">Project Name</label>
                    <div class="col-sm-11">
                        {{ Form::text('name',null,array('class'=>'form-control','placeholder' => 'Project Name','maxlength'=>100,'required'=>true)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="customer_id">
                    <label for="concern" class="col-sm-3 control-label">Customer</label>
                    <div class="col-sm-11">
                        <select class="form-control select2" name="customer_id" id="customerid" required>
                            <option value="">Select Customer</option>
                            @foreach($customers as $each_customername)
                            <option value="{{ $each_customername->id}}">{{ $each_customername->client_name .' ('.$each_customername->project_number.')' }}
                            </option>
                            @endforeach
                        </select>
                        <small class="help-block"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::submit('Cancel',array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

<!-- Group Modal -->
<div class="modal fade" id="group-modal" data-backdrop="static" role="dialog" aria-labelledby="group-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Project</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>

            </div>
            {{ Form::open(array('url'=>'#','id'=>'group-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            {{ Form::hidden('project_id', null) }}
            <div class="modal-body">
                <div class="form-group" id="name">
                    <label for="name" class="col-sm-3 control-label">Group</label>
                    <div class="col-sm-9">
                        {{ Form::text('name',null,array('class'=>'form-control','placeholder' => 'Group','maxlength'=>100,'required'=>true)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::submit('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

<!-- Task Modal -->
<div class="modal fade" id="task-modal" data-backdrop="static" role="dialog" aria-labelledby="task-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Project</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>

            </div>
            {{ Form::open(array('url'=>'#','id'=>'task-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            {{ Form::hidden('project_id', null) }}
            {{ Form::hidden('group_id', null) }}
            {{ Form::hidden('is_completed', null) }}

            <div class="modal-body">
                <div class="form-group" id="name">
                    <label for="name" class="col-sm-3 control-label">Task Name</label>
                    <div class="col-sm-9">
                        {{ Form::text('name',null,array('class'=>'form-control','placeholder' => 'Task Name','required'=>true)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="assigned_to_div">
                    <label for="assignee" class="col-sm-3 control-label" style="color:black;">Task Owner</label>
                    <div class="col-sm-9">
                        <select class="form-control task_owners" name="assigned_to[]" id="assignee" multiple="multiple" required>
                        </select>
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="followers_div">
                    <label for="followerids" class="col-sm-3 control-label" style="color:black;">Task Follower</label>
                    <div class="col-sm-9">
                        <select class="form-control followers" name="followers[]" id="followerids" multiple="multiple">
                        </select>
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group {{($followerConfigurationValue=='1')? 'disable_div':''}}" id="followers_can_update">
                    <div class="col-sm-12">
                        <input type="checkbox" id="followers_can_update" name="followers_can_update">&nbsp;Enable Update for the Task Follower
                    </div>
                </div>
                <div class="form-group" id="due_date">
                    <label for="due-date" class="col-sm-3 control-label">Due Date</label>
                    <div class="col-sm-9">
                        <input type="text" name="due_date" id="due-date" placeholder="Due Date" value="" autocomplete="off" class="form-control" required />
                        <small class="help-block"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::submit('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>


@include('projectmanagement::partials.pm-rating')

@endsection


@section('scripts')
<script>
    $('.select2').select2();
    $('.task_owners').select2({
        placeholder: "Select Task Owners"
    });
    $('.followers').select2({
        placeholder: "Select Followers"
    });

    function collectFilterData() {
            return {
                client_id:$("#clientname-filter").val(),
            }
    }

    $(".client-filter").change(function(){
        var table = $('#pm-project-table').DataTable();
            table.ajax.reload();
        });

    const pm = {
        ref: {
            projectTable: null,
            groupTable: {},
            expandedPanels: [],
        },
        init() {
            //Initialize project managemet table
            this.initPmTable();
            //Event listeners
            this.registerEventListeners();
        },
        registerEventListeners() {
            //Global scope inside closure.
            let root = this;
            $('#task-modal').on('show.bs.modal', function(e) {
                //init date pickers
                $("#due-date").datepicker({
                    minDate: new Date(),
                    format: "yyyy-mm-dd",
                });
            })
            $("#due-date").mask("9999-99-99");

            //New project modal
            $('.new-project-btn').click(function() {
                $('#project-modal .modal-title').text("Project");
                $('#project-form input[type=hidden]').val('');

                $('.form-group').removeClass('has-error').find('.help-block').text('');
                $('#project-form')[0].reset();
                $("#customerid").trigger("change");
                $("#project-modal").modal();
            });
            //New Group modal
            $(".pm-table").on("click", ".new-group-btn", function(e) {
                $('#group-form input[type=hidden]').val('');
                $('.form-group').removeClass('has-error').find('.help-block').text('');
                $('#group-form')[0].reset();
                $('#group-modal .modal-title').text("Group");
                $('#group-modal input[name="project_id"]').val($(this).data('id'));
                $("#group-modal").modal();
            });

            //New Task modal
            $(".pm-table").on("click", ".new-task-btn", function(e) {
                $('#task-form input[type=hidden]').val('');
                $('.form-group').removeClass('has-error').find('.help-block').text('');
                $('#task-form')[0].reset();
                $('#task-modal .modal-title').text("Task");
                $('#task-modal input[name="group_id"]').val($(this).data('id'));
                $('#task-modal input[name="is_completed"]').prop('checked', false);
                //Load users

                $("#task-modal").modal();
                root.loadUsers($(this).data('pid'));
            });

            // Add event listener for opening and closing details project
            $('#pm-project-table tbody').on('click', '.details-control', function(e, arg) {
                let tr = $(this).closest('tr');
                let row = root.ref.projectTable.row(tr);

                if (row.child.isShown()) {
                    // This row is already open - close it
                    root.toggleDetails(this, false);
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Open this row
                    root.toggleDetails(this, true);
                    row.child(root.renderGroupTable(row.data())).show();
                    root.afterRenderGroupTable(row.data());
                    //init it as data table
                    tr.addClass('shown');
                    $($(tr).next('tr').find('td')[0]).addClass('zero-padding');
                }
                if (arg === undefined) {

                    root.handleExpantionPanels($(this).data("panel-id"));
                }
                // $('.pmt-lv1').find('thead').css('display','none');
            });
            $('.pm-table tbody').on('click', '.gd-control', function(e, arg) {
                let tr = $(this).closest('tr');
                let gid = $(this).data('id');
                let row = root.ref.groupTable[gid].row(tr);

                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                    root.toggleDetails(this, false);
                } else {
                    // Open this row
                    root.toggleDetails(this, true);
                    row.child(root.renderTaskTable(row.data())).show();
                    root.afterRenderTaskTable(row.data());
                    //init it as data table
                    tr.addClass('shown');
                    $($(tr).next('tr').find('td')[0]).addClass('zero-padding')
                }
                if (arg === undefined) {
                    root.handleExpantionPanels($(this).data('panel-id'));
                }
            });


            //On edit project
            $("#pm-project-table").on("click", ".project-edit", function(e) {
                root.onEditProject($(this).data('id'));
            });

            //On delete project
            $('#pm-project-table').on('click', '.project-delete', function(e) {
                let id = $(this).data('id');

                root.actionPrompt(function() {
                    root.onDeleteProject(id);
                });

            });

            //On submit project
            $('#project-form').submit(function(e) {
                e.preventDefault();
                root.onSubmitProject();
            });

            //On edit group
            $(".pm-table").on("click", ".group-edit", function(e) {
                root.onEditGroup($(this).data('id'));
            });

            //On delete group
            $('.pm-table').on('click', '.group-delete', function(e) {
                let id = $(this).data('id');

                root.actionPrompt(function() {
                    root.onDeleteGroup(id);
                });

            });

            //On submit group
            $('#group-form').submit(function(e) {
                e.preventDefault();
                root.onSubmitGroup();
            });

            //On submit task
            $('#task-form').submit(function(e) {
                e.preventDefault();
                root.onSubmitTask();
            });
            $('#task-rate-form').submit(function(e) {
                e.preventDefault();
                root.onRateSubmit();
            });

            //On edit task
            $(".pm-table").on("click", ".task-edit", function(e) {
                root.onEditTask($(this).data('id'));
            });

            //On delete task
            $('.pm-table').on('click', '.task-delete', function(e) {
                let id = $(this).data('id');

                root.actionPrompt(function() {
                    root.onDeleteTask(id);
                });
            });
            //on edit task
            $(".pm-table").on("click", ".rate-task", function(e) {
                root.onRateTask($(this).data('id'));
            });
            $(".pm-table").on("click", ".mark-progress", function(e) {
                root.onMarkProgress($(this).data('id'), $(this).data('is-completed'));
            });

        },
        toggleDetails(el, expand) {
            if (expand) {
                $(el).removeClass('fa-plus-square').addClass('fa-minus-square');
            } else {
                $(el).removeClass('fa-minus-square').addClass('fa-plus-square');
            }
        },
        initPmTable() {
            this.ref.projectTable = $('#pm-project-table').DataTable({
                "drawCallback": this.drawCallback,
                "pageLength": 50,
                "ajax": {
                    "url": '{{ route("project.list") }}',
                    "data": function ( d ) {
                            return $.extend({}, d, collectFilterData());
                        },
                    "error": function(xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                order: [
                    [1, "desc"]
                ],
                lengthMenu: [
                    [10, 25, 50, 100, 500, -1],
                    [10, 25, 50, 100, 500, "All"]
                ],
                "columnDefs": [{
                        "width": "4%",
                        "targets": 0
                    },
                    {
                        "width": "20%",
                        "targets": 1
                    },
                    {
                        "width": "20%",
                        "targets": 2
                    },
                    {
                        "width": "20%",
                        "targets": 3
                    },

                ],
                columns: [{
                        data: null,
                        render: function(o) {

                            return '<button  class="btn details-control fa fa-plus-square pro-' + o.id + '" data-panel-id="pro-' + o.id + '"></button>';
                        },
                        orderable: false,
                    },
                    {
                        data: 'id',
                        name: 'id',
                        visible: false,
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: null,
                        name: 'created_at',
                        render: function(o) {
                            return moment(o.created_at).format("MMMM DD, Y")
                        }
                    },
                    {
                        data: 'customer_details.client_name_and_number',
                        name: 'customer_details.client_name_and_number'
                    },
                    {
                        data: null,
                        orderable: false,
                        className: 'pm-action-td text-right',
                        render: function(o) {
                            var actions = '';
                            @can('edit_masters')
                            actions += '<button class="pm-btn project-edit btn btn-sm" data-id=' + o.id + '><i class="fa fa-pencil"></i> Edit</button>'
                            @endcan
                            @can('lookup-remove-entries')
                            actions += '<button class="pm-btn btn btn-sm project-delete" data-id=' + o.id + '><i class=" fa fa-trash-o"></i> Delete</button>';
                            @endcan
                            actions += '<button class="pm-btn btn btn-sm new-group-btn" data-id=' + o.id + '><i class=" fa fa-plus"></i> Add Group</button>';
                            return actions;
                        },
                    }
                ],
            });
        },
        renderTaskTable(d) {
            return `
                <table class="pm-sub-table pmt-lv2 table table-bordered"  id="pm-task-table-${d.id}">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Task</th>
                            <th>Assignee</th>
                            <th>Due Date</th>
                            <th>Status </th>
                            <th>Status Notes </th>
                            <th>Rating</th>
                            <th class="text-center"></th>
                        </tr>
                    </thead>
                </table>
                `;
        },
        renderGroupTable(d) {
            return `
                <table class="pm-sub-table pmt-lv1 table table-bordered"  id="pm-group-table-${d.id}">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Group</th>
                            <th class="text-center"></th>
                        </tr>
                    </thead>
                </table>
                `;
        },
        afterRenderGroupTable(d) {
            let url = '{{ route("project.groups",":id") }}';
            url = url.replace(':id', d.id);
            this.ref.groupTable[d.id] = $('#pm-group-table-' + d.id).DataTable({
                "drawCallback": this.drawCallback,
                paging: false,
                bFilter: false,
                bInfo: false,
                "ajax": {
                    "url": url,
                    "error": function(xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                "columnDefs": [{
                        "width": "4%",
                        "targets": 0
                    },
                    {
                        "width": "20%",
                        "targets": 1
                    },
                ],
                columns: [{
                        data: null,
                        render: function(o) {
                            return '<button class="gd-control btn fa fa-plus-square gro-' + o.id + '" data-id="' + d.id + '" data-panel-id="gro-' + o.id + '"></button>';
                        },
                        orderable: false,
                    },
                    {
                        data: 'name',
                        name: 'name',
                        orderable: false,
                    },

                    {
                        data: null,
                        orderable: false,
                        className: 'pm-action-td text-right',
                        render: function(o) {
                            var actions = '';
                            @can('edit_masters')
                            actions += '<button class="pm-btn btn btn-sm group-edit" data-pid="' + d.id + '" data-id=' + o.id + '><i class=" fa fa-pencil"></i> Edit &nbsp</button>'
                            @endcan
                            @can('lookup-remove-entries')
                            actions += '<button class="pm-btn btn btn-sm group-delete" data-id=' + o.id + '><i class=" fa fa-trash-o"></i> Delete&nbsp</button>';

                            @endcan
                            actions += '<button class="pm-btn btn btn-sm new-task-btn" data-pid="' + d.id + '" data-id=' + o.id + '><i class=" fa fa-plus"></i> Add Task </button>';
                            return actions;
                        },
                    }
                ],
            });
        },
        afterRenderTaskTable(d) {
            let url = '{{ route("group.tasks",":id") }}';
            url = url.replace(':id', d.id);

            $('#pm-task-table-' + d.id).DataTable({
                paging: false,
                bFilter: false,
                bInfo: false,
                "ajax": {
                    "url": url,
                    "error": function(xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                "columnDefs": [{
                        "width": "4%",
                        "targets": 0
                    },
                    {
                        "width": "15%",
                        "targets": 1
                    },
                    {
                        "width": "10%",
                        "targets": 2
                    },
                    {
                        "width": "7%",
                        "targets": 3
                    },
                    {
                        "width": "8%",
                        "targets": 4
                    },
                    {
                        "width": "15%",
                        "targets": 5
                    },
                    {
                        "width": "14%",
                        "targets": 6
                    },

                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: '',
                        sortable: false
                    },
                    {
                        data: null,
                        name: name,
                        className: "datatable-v-center",
                        render: function(data) {
                            var task_str = '';
                            task_str = data.name;
                            var task_str_clip = task_str;
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
                            return task_str;
                        }

                    },
                    {
                        data: null,
                        render: function(o) {
                            let assignee_task_owner = '',
                                assignee_task_follower = '';
                            if (o.task_owners) {
                                $.each(o.task_owners, function(k2, owner) {
                                    if (owner.user_with_out_trashed) {
                                        assignee_task_owner += owner.user_with_out_trashed.full_name + ",";
                                    }
                                });
                            }

                            if (o.followers) {
                                $.each(o.followers, function(k2, fwr) {
                                    if (fwr.user_with_out_trashed) {
                                        assignee_task_follower += fwr.user_with_out_trashed.full_name + ",";
                                    }
                                });
                            }

                            return (((assignee_task_owner != "") ? 'Task Owner: <br />' + assignee_task_owner.replace(/,\s*$/, "") : '') + ((assignee_task_follower != "") ? '<br /><br /> Task Follower:<br />' + assignee_task_follower.replace(/,\s*$/, "") : ''));
                        },
                    },
                    {
                        data: null,
                        name: 'due_date',
                        render: function(o) {
                            return moment(new Date(o.due_date)).format('Y-MM-DD');
                        }
                    },
                    {
                        data: null,
                        render: function(o) {
                            return o.is_completed == 1 ? 'Completed' : 'Open';
                        }
                    },
                    {
                        data: null,
                        name: null,
                        className: "datatable-v-center",
                        render: function(data) {
                            var status_notes_str = '';
                            if (data.status.length > 0) {
                                status_notes_str = data.status[0].notes;
                                var status_notes_str_clip = status_notes_str;
                                if (status_notes_str.length > 35) {
                                    status_notes_str =
                                        '<span class="show-btn nowrap" ' +
                                        'onclick="$(this).hide();$(this).next().show();">' +
                                        status_notes_str_clip.substr(0, 30) +
                                        '..<a href="javascript:;" title="Expand" class="fa fa-chevron-circle-down cgl-font-blue"></a>' +
                                        '</span>' +
                                        '<span class = "notes big-notes" style="display:none" onclick="$(this).hide();$(this).prev().show();">' +
                                        status_notes_str + '&nbsp;&nbsp;' +
                                        '<a href="javascript:;" title="Collapse" class="fa fa-chevron-circle-up cgl-font-blue"></a>' +
                                        '</span><br/>\r\n';
                                }
                            }
                            return status_notes_str;
                        }

                    },
                    {
                        data: null,
                        render: function(o) {
                            return o.average_rating != null ? o.average_rating : '--';
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        className: 'pm-action-td text-right',
                        render: function(o) {
                            var actions = '';
                            @can('edit_masters')
                            actions += '<button class="pm-btn btn btn-sm task-edit" data-id=' + o.id + '><i class="fa fa-pencil"></i> Edit</button>'
                            @endcan
                            @can('lookup-remove-entries')
                            actions += '<button class="pm-btn btn btn-sm task-delete" data-id=' + o.id + '><i class="fa fa-trash-o"></i> Delete</a>';
                            @endcan
                            @can('rate_project_management_task')
                            actions += '<button class="pm-btn btn btn-sm rate-task" data-id=' + o.id + '><i class="fa fa-star"></i>  Rate Task</a>';
                            @endcan
                            if (o.is_completed == 0)
                                actions += '<button class="pm-btn btn btn-sm mark-progress" data-id=' + o.id + ' data-is-completed=' + o.is_completed + '><i class="fa fa-tasks"></i>  Mark as Completed</a>';
                            else
                                actions += '<button class="pm-btn btn btn-sm mark-progress" data-id=' + o.id + ' data-is-completed=' + o.is_completed + '><i class="fa fa-tasks"></i>  Mark as Incomplete</a>';
                            return actions;
                        },
                    }
                ],
            });
        },
        onDeleteProject(projectId) {
            let root = this;
            let url = "{{ route('project.destroy',':id') }}";
            url = url.replace(':id', projectId);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'DELETE',
                success: function(data) {
                    if (data.success) {
                        root.reloadTable(root.ref.projectTable);
                        swal("Deleted", "Project has been deleted successfully", "success");
                    }
                },
                error: function(xhr, textStatus, thrownError) {},
                contentType: false,
                processData: false,
            });
        },
        onEditProject(projectId) {
            let url = '{{ route("project.show",":id") }}';
            url = url.replace(':id', projectId);
            $('#project-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    if (data) {
                        $('#project-modal input[name="id"]').val(data.id)
                        $('#project-modal input[name="name"]').val(data.name)
                        $("#project-modal select[name='customer_id']").select2().val(data.customer_id).trigger("change");
                        // $('#project-modal select[name="customer_id"] option[value="' + data.customer_id + '"]').prop('selected', true);
                        $('#project-modal .modal-title').text("Edit Project: " + data.name);
                        $("#project-modal").modal();
                    } else {
                        swal("Oops", "Could not retrive data.", "warning");
                    }
                },
                error: function(xhr, textStatus, thrownError) {
                    swal("Oops", "Something went wrong", "warning");
                },
                contentType: false,
                processData: false,
            });
        },
        onSubmitProject() {
            let root = this;
            let $form = $('#project-form');
            url = "{{ route('project.store') }}";
            let formData = new FormData($('#project-form')[0]);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function(data) {
                    $("#project-modal").modal('hide');
                    if (data.success) {
                        swal({
                            title: "Saved",
                            text: "Project has been saved",
                            type: "success"
                        }, function() {
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                            $('#project-form')[0].reset();
                            root.reloadTable(root.ref.projectTable);
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
        onEditGroup(groupId) {
            let url = '{{ route("group.show",":id") }}';
            url = url.replace(':id', groupId);
            $('#group-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {

                    if (data) {
                        $('#group-modal input[name="id"]').val(data.id);
                        $('#group-modal input[name="name"]').val(data.name);
                        $('#group-modal input[name="project_id"]').val(data.project_id);
                        $('#group-modal-title').text("Edit Group: " + data.name);
                        $("#group-modal").modal();
                    } else {
                        swal("Oops", "Could not retrive data.", "warning");
                    }
                },
                error: function(xhr, textStatus, thrownError) {
                    swal("Oops", "Something went wrong", "warning");
                },
                contentType: false,
                processData: false,
            });
        },
        onDeleteGroup(groupId) {
            let root = this;
            let url = "{{ route('group.destroy',':id') }}";
            url = url.replace(':id', groupId);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'DELETE',
                success: function(data) {
                    if (data.success) {
                        root.reloadTable(root.ref.projectTable); //TODO: change sub table to reload
                        swal("Deleted", "Group has been deleted successfully", "success");
                    }
                },
                error: function(xhr, textStatus, thrownError) {},
                contentType: false,
                processData: false,
            });

        },
        onSubmitGroup() {
            let root = this;
            let $form = $('#group-form');
            url = "{{ route('group.store') }}";
            let formData = new FormData($('#group-form')[0]);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function(data) {
                    $("#group-modal").modal('hide');
                    if (data.success) {
                        swal({
                            title: "Saved",
                            text: "Group has been saved",
                            type: "success"
                        }, function() {
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                            $('#group-form')[0].reset();
                            root.reloadTable(root.ref.projectTable); //TODO:change to grup table
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
        onSubmitTask() {
            let root = this;
            let $form = $('#task-form');
            url = "{{ route('task.store') }}";
            let formData = new FormData($('#task-form')[0]);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function(data) {
                    $("#task-modal").modal('hide');
                    if (data.success) {
                        swal({
                            title: "Saved",
                            text: "Task has been saved",
                            type: "success"
                        }, function() {
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                            $('#task-form')[0].reset();
                            root.reloadTable(root.ref.projectTable);
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
        onEditTask(taskId) {
            let root = this;
            let url = '{{ route("task.show",":id") }}';
            url = url.replace(':id', taskId);
            $('#task-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    if (data) {
                        console.log(data)
                        $('#task-modal input[name="id"]').val(data.id);
                        $('#task-modal input[name="name"]').val(data.name);
                        $('#task-modal input[name="group_id"]').val(data.group_id);
                        $('#task-modal input[name="project_id"]').val(data.project_id);
                        $('#task-modal input[name="is_completed"]').val(data.is_completed);
                        $('#task-modal-title').text("Edit Task: " + data.name);

                        if (data.followers_can_update == "1") {
                            $('#task-modal input[name="followers_can_update"]').prop("checked", true);
                        } else {
                            $('#task-modal input[name="followers_can_update"]').prop("checked", false);
                        }
                        //Get ratings of a project


                        $("#task-modal").modal();
                        $('#task-modal input[name="due_date"]').val(moment(new Date(data.due_date)).format('Y-MM-DD'));
                        var task_owner_users = [],
                            follower_users = [];
                        console.log(data.task_owners);
                        if (data.task_owners) {
                            $.each(data.task_owners, function(index, item) {
                                if (item.user_with_out_trashed) {
                                    task_owner_users.push(item.user_with_out_trashed.id);
                                }
                            });
                        }

                        if (data.followers) {
                            $.each(data.followers, function(index, item) {
                                if (item.user_with_out_trashed) {
                                    follower_users.push(item.user_with_out_trashed.id);
                                }
                            });
                        }
                        root.loadUsers(data.project_id, task_owner_users, follower_users);
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
        onDeleteTask(taskId) {
            let root = this;
            let url = "{{ route('task.destroy',':id') }}";
            url = url.replace(':id', taskId);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'DELETE',
                success: function(data) {
                    if (data.success) {
                        swal("Deleted", "Task has been deleted successfully", "success");
                        root.reloadTable(root.ref.projectTable);
                    }
                },
                error: function(xhr, textStatus, thrownError) {},
                contentType: false,
                processData: false,
            });
        },
        onRateTask(taskId) {
            let root = this;
            let url = '{{ route("task.show",":id") }}';
            url = url.replace(':id', taskId);
            $('#task-rate-form')[0].reset();
            $('#task-rate-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    if (data) {
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
                        $('#task-rate-modal select[name="deadline_weightage"] option[value="' + data.deadline_weightage + '"]').prop('selected', true);
                        $('#task-rate-modal select[name="value_add_weightage"] option[value="' + data.value_add_weightage + '"]').prop('selected', true);
                        $('#task-rate-modal select[name="initiative_weightage"] option[value="' + data.initiative_weightage + '"]').prop('selected', true);
                        $('#task-rate-modal select[name="commitment_weightage"] option[value="' + data.commitment_weightage + '"]').prop('selected', true);
                        $('#task-rate-modal select[name="complexity_weightage"] option[value="' + data.complexity_weightage + '"]').prop('selected', true);
                        $('#task-rate-modal select[name="efficiency_weightage"] option[value="' + data.efficiency_weightage + '"]').prop('selected', true);

                        $('#task-rate-modal textarea[name="rating_notes"]').val(data.rating_notes);
                        $('#task-modal-title').text("Rate Task: " + data.name);

                        $("#task-rate-modal").modal();
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
                            type: "success"
                        }, function() {
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                            $('#task-rate-form')[0].reset();
                            root.reloadTable(root.ref.projectTable);
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
        onMarkProgress(taskId, isCompleted) {
            var title = (isCompleted == 1) ? 'Mark as Incomplete' : 'Mark as Completed';
            var message = (isCompleted == 1) ? 'Task has been marked as incomplete' : 'Task has been marked as complete';
            var text = (isCompleted == 1) ? 'Do you want to mark task as Incomplete?' : 'Do you want to mark task as completed?';
            let root = this;
            swal({
                    title: title,
                    text: text,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: 'btn-danger',
                    confirmButtonText: 'Yes',
                    cancelButtonText: "No",
                    closeOnConfirm: true,
                    closeOnCancel: true
                },
                function(isConfirm) {
                    if (isConfirm) {
                        var url = '{{ route("mark.progress",[":id",":is_completed"]) }}';
                        var url = url.replace(':id', taskId);
                        var url = url.replace(':is_completed', isCompleted);
                        $.ajax({
                            url: url,
                            type: 'GET',
                            //  data:{'id':taskId,'is_completed' : isCompleted},
                            success: function(data) {
                                if (data) {
                                    root.reloadTable(root.ref.projectTable);
                                    swal("Success", message, "success");
                                } else {
                                    swal("Oops", "Could not retrive data.", "warning");
                                }
                            },
                            error: function(xhr, textStatus, thrownError) {
                                swal("Oops", "Something went wrong", "warning");
                            },

                        });
                    } else {
                        // swal("Cancelled", "Page safe!", "error");
                    }
                });


        },
        loadUsers(projectId, selected = null, selected_followers = null) {
            let url = '{{ route("project.users",":id") }}';
            url = url.replace(':id', projectId);

            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    if (data) {
                        //fill assignee dropdown
                        let list = $('#task-modal select[name="assigned_to[]"]');
                        list.empty();
                        $.each(data, function(index, item) {
                            list.append(new Option(item.full_name, item.id))
                        });
                        if (selected) {
                            $('#task-modal select[name="assigned_to[]"]').val(selected).trigger("change");
                        }

                        //fill followers dropdown
                        let followers = $('#task-modal select[name="followers[]"]');
                        followers.empty();
                        $.each(data, function(index, item) {
                            followers.append(new Option(item.full_name, item.id));
                        });
                        if (selected_followers) {
                            $('#task-modal select[name="followers[]"]').val(selected_followers).trigger("change");
                        }
                    }
                },
            });
        },
        loadRatings(selected = null) {
            let url = '{{ route("emp.ratings") }}';
            $.ajax({
                url: url,
                async: false,
                type: 'GET',
                success: function(data) {
                    if (data) {
                        //fill the dropdown
                        let list = $('#task-modal select[name="rating_id"]');
                        list.empty();
                        list.append('<option ' + (selected == null ? 'selected' : '') + 'disabled >Select Rating</option>');
                        $.each(data, function(index, item) {
                            list.append(new Option(item.rating, item.id))
                        });
                        if (selected) {
                            $('#task-modal select[name="rating_id"] option[value="' + selected + '"]').prop('selected', true);
                        }
                    }
                },
            });
        },
        handleExpantionPanels(panelId) {
            if (this.ref.expandedPanels.includes(panelId)) {
                this.ref.expandedPanels = this.ref.expandedPanels.filter(f => f !== panelId)
            } else {

                this.ref.expandedPanels.push(panelId);
            }
        },
        actionPrompt(callback) {
            swal({
                title: "Are you sure?",
                text: "You won't be able to undo this action",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            }, function(action) {
                if (action) {
                    callback();
                };
            });
        },
        reloadTable(table) {

            table.ajax.reload(null, false);
        },
        drawCallback(settings) {
            if (settings.sTableId === 'pm-project-table') {
                pm.triggerExpand('pro');
            }
            if (settings.sTableId.startsWith('pm-group-table')) {
                pm.triggerExpand('gro');
            }
        },
        triggerExpand(prefix) {
            this.ref.expandedPanels.forEach(function(el) {
                if (el.startsWith(prefix)) {
                    let node = $('body').find('.' + el);
                    if (node.length > 0) {
                        node.trigger('click', ['re-render']);
                    }
                }
            });
        }
    }


    /// Code to run when the document is ready.
    $(function() {
        pm.init();
    });


    //change event for task owner dropdown
    $(".task_owners").on("change", function() {
        var followers = $('.followers').val();
        var task_owners = $(this).val();
        var followers_length = followers.length;

        if (followers_length > 0) {
            followers = followers.filter((el) => !task_owners.includes(el));
            if (followers_length != followers.length) {
                $('#task-modal select[name="followers[]"]').val(followers).trigger("change");
            }
        }
    });

    //change event for followers dropdown
    $(".followers").on("change", function() {
        var task_owners = $('.task_owners').val();
        var followers = $(this).val();
        var task_owners_length = task_owners.length;

        if (task_owners_length > 0) {
            task_owners = task_owners.filter((el) => !followers.includes(el));
            if (task_owners_length != task_owners.length) {
                $('#task-modal select[name="assigned_to[]"]').val(task_owners).trigger("change");
            }
        }
    });
</script>

@endsection
