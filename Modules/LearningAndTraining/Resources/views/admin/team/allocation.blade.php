@extends('layouts.app')

{{--@section('title', 'Allocation')--}}



{{--@stop--}}

    <style>
        .admin-btn {
            background-color: #f26222 !important;
            color: #ffffff !important;
            margin-top: 27px;
        }
    </style>


@section('content')
    <div class="table_title">
        <h4>Employee Allocation</h4>
    </div>

    <div id="message"></div>
<div class="row">
    <div class="col-md-12" id="allocation-container">
        <div class="form-group row">
            <div class="col-md-6">
            </div>
{{--            <div class="col-md-3">--}}
{{--                Role:--}}
{{--            </div>--}}
{{--            <div class="col-md-3">--}}
{{--                {{Form::select('role',$role_list,null,['class' => 'form-control','placeholder' => 'All','id'=>'role'])}}--}}
{{--            </div>--}}
{{--            <div class="col-md-3">--}}
{{--                {{Form::select('supervisor_id',$supervisor_list,null,['id'=>'supervisor_id', 'class' => 'form-control', 'placeholder' => 'Select'])}}--}}
{{--            </div>--}}
            <div class="col-md-3">
                <input type="text" id="justAnInputBox" placeholder="Select Team & Sub Team"/>
            </div>
{{--            <div class="col-md-3">--}}
{{--                <input type="checkbox" name="filter" id="user-filter" value="1">     Allocated--}}
{{--            </div>--}}
        </div>
    </div>
</div>
<table class="table table-bordered" id="allocation-table">
    <thead>
        <tr>
            <th class="dt-body-center text-center"><input name="select_all" value="1" id="example-select-all" type="checkbox"/></th>
            <th>Employee Id</th>
            <th>Employee Name</th>
            <th>Employee Email</th>
{{--            <th>Role</th>--}}
            <th>Team & Sub Team</th>
            <th>Unallocate</th>
        </tr>
    </thead>
</table>
<div class="col-md-6 allocation-controls top-25">
    <button class="btn blue allocate-submit-btn admin-btn" style='margin-right:5px'>Allocate</button>
    <button class="btn blue allocate-cancel-btn admin-btn">Cancel</button>
</div>
@stop
@section('scripts')
    <link href="{{ asset('js/multi-select-dropdown/style.css') }}" rel="stylesheet">
    <script src="{{ asset('js/multi-select-dropdown/icontains.js') }}"  type="text/javascript"></script>
    <script src="{{ asset('js/multi-select-dropdown/comboTreePlugin.js') }}"  type="text/javascript"></script>
<script>
    $(function () {
        $("#supervisor_id").select2();
        $("#role").select2();
        $("#justAnInputBox").val('');
    $.ajaxSetup({
    headers: {
    'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
    });
     $.fn.dataTable.ext.errMode = 'throw';
        try{
            var table = $('#allocation-table').DataTable({
            bProcessing: false,
            dom: 'lfrtBip',
            buttons: [
            {
            extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5],
                        stripNewlines: false
                    }
            },
            {
            extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                    }
            },
            {
            extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                    }
            },
            {
            text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o',
                    action: function (e, dt, node, conf) {
                    emailContent(table, 'Employee Allocation');
                    }
            }
            ],
            processing: true,
            serverSide: true,
            fixedHeader: true,
            ajax:{
{{--                url: '{{route("allocation.list")}}', // Change this URL to where your json data comes from--}}
                url: '{{route("learningandtraining.team.employee-allocation.list")}}', // Change this URL to where your json data comes from
                type: "GET", // This is the default value, could also be POST, or anything you want.
                data: function(d) {
                    d.supervisor_id = $('#supervisor_id').val();
                    d.type = $("#allocation-container input[name='allocation_type']:checked").val(); /* All, Allocated, Unallocated */
                    d.filter = $('#user-filter').val();
                },
                "error": function (xhr, textStatus, thrownError) {
                    if(xhr.status === 401){
                        window.location = "{{ route('login') }}";
                    }
                }
            },
            'columnDefs': [{
            'targets': 0,
                    'searchable':false,
                    'orderable':false,
                    'className': 'dt-body-center',
                    'render': function (data, type, full, meta){
                    return '<input type="checkbox" id="emp_id" name="employee_id" value="' + $('<div/>').text(data).html() + '">';
                    }
            },],
            // select: {
            // style:    'os',
            //         selector: 'td:first-child'
            // },
            lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
            columns: [
            { data: 'id', name: 'id' },
            { data: 'emp_no', name: 'emp_no' },
            { data: 'emp_name', name:'emp_name'},
            { data: 'emp_email', name: 'emp_email' },
            // { data: 'team', name: 'team' },
            { data: 'team', name: 'team','orderable':false },
            { data: null,
                    render: function (o){
                        return (o.team !== '') ? '<a class="unallocate btn fa fa-minus-square fa-lg" data-id=' + o.id + '>' + '</a>':'';
                    }
            }
            ]
    });
    } catch(e){
        console.log(e.stack);
    }

    $("#allocation-table_wrapper").addClass("datatoolbar");


            // Handle click on "Select all" control
            $('#allocation-table').on('click', '#example-select-all', function(){
                var rows = table.rows({ 'search': 'applied' }).nodes();
                $('input[type="checkbox"]', rows).prop('checked', this.checked);
            });





            //Allocate the guard to the supervisor
            $('.allocate-submit-btn').on('click', function (e) {

                team_id = $(":input[name=team_id]:checked").length;
                employee_id = $("#allocation-table input[name=employee_id]:checked").length;

                if (Number(team_id) > 0 && Number(employee_id) > 0) {

                    employee_ids = [];
                    $("#allocation-table input[name=employee_id]:checked").each(function () {
                        employee_ids.push($(this).val());
                    });
                    employee_ids = (JSON.stringify(employee_ids));

                    team_ids = [];
                    $(':input[name=team_id]:checked').each(function (i) {
                        team_ids.push($(this).val());
                    });
                    team_ids = (JSON.stringify(team_ids));

                    $.ajax({
                        url: "{{route('learningandtraining.team.employee-allocation.store')}}",
                        method: 'POST',
                        data: {'team_ids': team_ids, 'employee_ids': employee_ids},
                        success: function (data) {
                            if (data.success) {
                                swal("Allocated", "Employee has been allocated", "success");
                                $("#justAnInputBox").val('');
                                table.ajax.reload();
                            } else {
                                swal("Alert", "Employee not get allocated. Try again.", "warning");
                            }
                        },
                        error: function (xhr, textStatus, thrownError) {
                            //alert(xhr.status);
                            //alert(thrownError);
                            console.log(xhr.status);
                            console.log(thrownError);
                            swal("Oops", "Something went wrong", "warning");
                        },
                    });
                } else {
                    var msg_str = "Please select ";
                    var concat_str = "and ";
                    if (Number(team_id) <= 0 && Number(employee_id) <= 0) {
                        msg_str = msg_str + "team " + concat_str + "employee";
                    } else if (Number(team_id) <= 0) {
                        msg_str = msg_str + "team ";
                    } else {
                        msg_str = msg_str + "employee"
                    }
                    swal("Alert", msg_str, "warning");
                }
            });

    //Unallocate the guard allocated to the supervisor
    $("#allocation-table").on("click", ".unallocate", function (e) {
    id = $(this).data('id');
    // supervisor_id = $("select#supervisor_id").val();

        employee_ids = [];
        $("#allocation-table input[name=employee_id]:checked").each(function () {
            employee_ids.push($(this).val());
        });
        employee_ids = (JSON.stringify(employee_ids));

        team_ids = [];
        $(':input[name=team_id]:checked').each(function (i) {
            team_ids.push($(this).val());
        });
        team_ids = (JSON.stringify(team_ids));

    swal({
        title: "Are you sure?",
        text: "You won't be able to undo this action",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Unallocate",
        showLoaderOnConfirm: true,
        closeOnConfirm: false
    },function(unalloc){
        if (unalloc){
                $.ajax({
{{--                    url: "{{route('allocation.unallocate')}}",--}}
                    url: "{{route('learningandtraining.team.employee-allocation.remove')}}",
                    type: 'POST',
                    data:  {'team_ids': team_ids, 'employee_id':id},
                    success: function (data) {
                        if (data.success) {
                            swal("Unallocated", "Employee has been unallocated", "success");
                            table.ajax.reload();
                        } else {
                            //alert(data);
                            swal("Alert", "Employee not allocated to this team", "warning");
                        }
                    },
                    error: function (xhr, textStatus, thrownError) {
                       //alert(xhr.status);
                       //alert(thrownError);
                       console.log(xhr.status);
                       console.log(thrownError);
                       swal("Oops", "Something went wrong", "warning");
                    },
                });
            };
        });
    });


    //Uncheck all checkbox on clicking cancel button
    //     $(".allocate-cancel-btn").on('click', function(){
    //         $("#example-select-all, #group1 input[name='allocation_type'], input:checkbox").prop('checked', false);
    //     });

    });</script>




<script type="text/javascript">

    // var SampleJSONData = [
    //     {
    //         id: 0,
    //         title: 'choice 1  '
    //     }, {
    //         id: 1,
    //         title: 'choice 2',
    //         subs: [
    //             {
    //                 id: 10,
    //                 title: 'choice 2 1'
    //             }, {
    //                 id: 11,
    //                 title: 'choice 2 2'
    //             }, {
    //                 id: 12,
    //                 title: 'choice 2 3'
    //             }
    //         ]
    //     }, {
    //         id: 2,
    //         title: 'choice 3'
    //     }, {
    //         id: 3,
    //         title: 'choice 4'
    //     }, {
    //         id: 4,
    //         title: 'choice 5'
    //     }, {
    //         id: 5,
    //         title: 'choice 6',
    //         subs: [
    //             {
    //                 id: 50,
    //                 title: 'choice 6 1'
    //             }, {
    //                 id: 51,
    //                 title: 'choice 6 2',
    //             },
    //             {
    //                 id: 510,
    //                 title: 'choice 6 2 1'
    //             },
    //             {
    //                 id: 511,
    //                 title: 'choice 6 2 2'
    //             },
    //             {
    //                 id: 512,
    //                 title: 'choice 6 2 3'
    //             }
    //
    //
    //         ]
    //     }, {
    //         id: 6,
    //         title: 'choice 7'
    //     }
    // ];

    var SampleJSONData = {!! $team_list !!};
    var comboTree1, comboTree2;

    jQuery(document).ready(function($) {

        comboTree1 = $('#justAnInputBox').comboTree({
            source : SampleJSONData,
            isMultiple: true
        });

        // comboTree2 = $('#justAnotherInputBox').comboTree({
        //     source : SampleJSONData,
        //     isMultiple: false
        // });
    });


</script>
@stop
