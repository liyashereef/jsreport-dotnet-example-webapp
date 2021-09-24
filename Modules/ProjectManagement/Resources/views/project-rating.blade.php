@extends('layouts.app')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@section('css')
<style>
    .assignee {
        font-size: 10px;
    }
</style>
@endsection

@section('content')
<div class="table_title">
    <h4>Performance Report</h4>
</div>
<!-- top card area -->
<div class="row mainlink-component card-view-section mb-4  position-relative">
    <div class="position-absolute icons-top-left d-flex flex-column">
    </div>

    <div class="col-sm-12" style="padding-left: 20px;" >
        <div class="row">
            <div class="col-md-3">
                <div class="row">
                    <div class="col-md-6">
                        <label>Start Date</label>

                        <input id="startdate"  width="100%" value="{{$startdate}}" class="form-control custom-datepicker" />
                    </div>
                    <div class="col-md-6">
                        <label>End Date</label>
                        <input type="text" id="enddate" width="100%"  value="{{$enddate}}" class="form-control custom-datepicker" />
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <label>Project</label>
                <select class="form-control option-adjust project-filter select2" name="project-filter" id="project-name-filter">
                    <option value="">Select Project</option>
                    @foreach($project_list as $id=>$project_name)
                    <option value="{{$id}}">{{ $project_name}}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label>Group</label>
                <select class="form-control option-adjust group-filter select2" name="employee-filter" id="group-name-filter">
                    <option value="">Select Group</option>
                    @foreach($group_list as $id=>$group_name)
                    <option value="{{$id}}">{{ $group_name}}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label>Employee</label>
                <select class="form-control option-adjust employee-filter select2" name="employee-filter" id="employee-name-filter">
                    <option value="">Select Employee</option>
                    @foreach($user_list as $id=>$each_user)
                    <option value="{{$id}}">{{ $each_user}}
                    </option>
                    @endforeach
                </select>
            </div>


        </div>
    </div>
</div>
<div>
    <table class="table table-bordered auto-refresh" id="rating-table">
        <thead>
            <tr>
                <th class="sorting">Project</th>
                <th class="sorting">Group</th>
                <th class="sorting">Task</th>
                <th >Assignee</th>
                <th class="sorting">Assign Date</th>
                <th class="sorting">Due Date</th>
                <th class="sorting">Rating Date</th>
                <th class="sorting">DL</th>
                <th class="sorting">VA</th>
                <th class="sorting">IN</th>
                <th class="sorting">CM</th>
                <th class="sorting">CX</th>
                <th class="sorting">EF</th>
                <th class="sorting">Notes</th>
                <th class="sorting">Score</th>
                <th >Rating</th>
            </tr>
        </thead>
    </table>
</div>
@stop
@section('scripts')
<script>


    $(function () {
        $('.select2').select2();
        sessionStorage.setItem("project_start_date", $('#startdate').val());
        sessionStorage.setItem("project_end_date", $('#enddate').val());
        count = 0;
        $('#startdate').datepicker({
            format: 'yyyy-mm-dd',
            showRightIcon: false,
            change: function (e) {
            if(sessionStorage.getItem("project_start_date") != $('#startdate').val()){
                changeDateFilter();
            }
            },
        });
        $('#enddate').datepicker({
            format: 'yyyy-mm-dd',
            showRightIcon: false,
            change: function (e) {
            if(sessionStorage.getItem("project_end_date") != $('#enddate').val()){
                changeDateFilter();
            }
            },
        });

        var url = "{{ route('project-report.rating.list',[':startdate',':enddate']) }}";
        var startdate = $('#startdate').val();
        var enddate = $('#enddate').val();
        url = url.replace(':startdate', startdate);
        url = url.replace(':enddate', enddate);

        $.fn.dataTable.ext.errMode = 'throw';
        try {
            table = $('#rating-table').DataTable({
                processing: false,
                fixedHeader: false,
                serverSide: true,
                responsive: true,

                ajax: url,
                dom: 'Blfrtip',
                buttons: [{
                        extend: 'pdfHtml5',
                        pageSize: 'A2',
                        exportOptions: {
                            columns: 'th',
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        exportOptions: {
                            columns: 'th',
                        }
                    },
                    {
                        extend: 'print',
                        pageSize: 'A2',
                        exportOptions: {
                            columns: 'th',
                            stripHtml: false,
                        }
                    }
                ],
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                // order: [
                //     [1, "desc"]
                // ],
                lengthMenu: [
                    [10, 25, 50, 100, 500, -1],
                    [10, 25, 50, 100, 500, "All"]
                ],

                columnDefs: [
                    {width: '10%', targets: 0},
                    {width: '10%', targets: 1},
                    {width: '10%', targets: 2},
                    {width: '16%', targets: 3},
                    {width: '7%', targets: 4},
                    {width: '7%', targets: 5},
                    {width: '7%', targets: 6},
                    {width: '1%', targets: 7},
                    {width: '1%', targets: 8},
                    {width: '1%', targets: 9},
                    {width: '1%', targets: 10},
                    {width: '1%', targets: 11},
                    {width: '1%', targets: 12},
                    {width: '20%', targets: 13},
                    {width: '5%', targets: 14},
                    {width: '2%', targets: 15},
                ],
                columns: [
                    {data: 'project', name: 'projects.name'},
                    {data: 'group', name: 'groupDetails.name'},
                    {data: 'task_name', name: 'name'},
                    {data: 'assignee', name: 'assignee',
                    sortable:false,searchable:false},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'due_date', name: 'due_date'},
                    {data: 'rating_date', name: 'rated_at'},
                    {data: 'deadline_rating_id', name: 'deadline_rating_id'},
                    {data: 'value_add_rating_id', name: 'value_add_rating_id'},
                    {data: 'initiative_rating_id', name: 'initiative_rating_id'},
                    {data: 'commitment_rating_id', name: 'commitment_rating_id'},
                    {data: 'complexity_rating_id', name: 'complexity_rating_id'},
                    {data: 'efficiency_rating_id', name: 'efficiency_rating_id'},
                    {data: 'rating_notes', name: 'rating_notes'},
                    {data: 'average', name: 'average_rating'},
                    {data: 'rating', name: 'average_rating', sortable: false},
                ]
            });

        } catch (e) {
            console.log(e.stack);
        }

    });

    function changeDateFilter() {
        sessionStorage.setItem("project_start_date", $('#startdate').val());
        sessionStorage.setItem("project_end_date", $('#enddate').val());
        var sdate = $('#startdate').val();
        var edate = $('#enddate').val();
        var emp_id = !($('#employee-name-filter').val()) ? 0 : $('#employee-name-filter').val();
        var group_id = !($("#group-name-filter").val()) ? 0 : $("#group-name-filter").val();
        var project_id = !($("#project-name-filter").val()) ? 0 : $("#project-name-filter").val();
        var url = "{{ route('project-report.rating.list',[':startdate',':enddate',':project_id',':group_id',':emp_id']) }}";
        url = url.replace(':startdate', sdate);
        url = url.replace(':enddate', edate);
        url = url.replace(':project_id', project_id);
        url = url.replace(':group_id', group_id);
        url = url.replace(':emp_id', emp_id);
        table.ajax.url(url).load();
    }

    $('#project-name-filter').on('change', function (e)
    {
        $("#group-name-filter").prop('selectedIndex', 0);
    });

    $('#employee-name-filter, #project-name-filter, #group-name-filter').on('change', function (e) {
        var sdate = $('#startdate').val();
        var edate = $('#enddate').val();
        var emp_id = !($('#employee-name-filter').val()) ? 0 : $('#employee-name-filter').val();
        var group_id = !($("#group-name-filter").val()) ? 0 : $("#group-name-filter").val();
        var project_id = !($("#project-name-filter").val()) ? 0 : $("#project-name-filter").val();
        var url = "{{ route('project-report.rating.list',[':startdate',':enddate',':project_id',':group_id',':emp_id']) }}";
        url = url.replace(':startdate', sdate);
        url = url.replace(':enddate', edate);
        url = url.replace(':project_id', project_id);
        url = url.replace(':group_id', group_id);
        url = url.replace(':emp_id', emp_id);
        table.ajax.url(url).load();

    });


    $(".employee-filter").change(function () {
        table.ajax.reload();
    });

    $(".project-filter").change(function () {
        var all_group_list = JSON.parse('{!! json_encode($group_list) !!}');
        var project_id = $("#project-name-filter").val();
        if (!project_id)
        {
            var options = '<option selected="selected" value="">Select Group</option>';
            $.each(all_group_list, function (key, value) {
                options += '<option value="' + key + '">' + value + '</option>'
            });
            $("#group-name-filter").html('');
            $("#group-name-filter").html(options);
        } else {
            let url = '{{ route("project.groupList",":id") }}';
            url = url.replace(':id', project_id);
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        var options = '<option selected="selected" value="">Select Group</option>';
                        $.each(data, function (key, value) {
                            options += '<option value="' + value.id + '">' + value.name + '</option>'
                        });
                        $("#group-name-filter").html('');
                        $("#group-name-filter").html(options);
                    } else {
                        swal("Oops", "Could not retrive data.", "warning");
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    swal("Oops", "Something went wrong", "warning");
                },
                contentType: false
            });
        }
        table.ajax.reload();
    });

    $(".group-filter").change(function () {
        table.ajax.reload();
    });


</script>
<script src="{{asset('js/auto-refresh.js')}}"></script>
<style>
body
{
    overflow-x: hidden;
}
</style>   
@stop
