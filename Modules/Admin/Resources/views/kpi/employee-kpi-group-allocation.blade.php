@extends('adminlte::page')

@section('title', 'Employee Kpi Group Allocation')

@section('content_header')
<h1>Employee KPI Group Allocation</h1>

@stop

@section('content')
<div id="message"></div>
<div class="row">
    <div class="col-md-12" id="allocation-container">
        <div class="form-group row">
            <div class="col-md-3">
                KPI Group:
            </div>
            <div class="col-md-3">
                {{Form::select('kpi_group',$kpi_group_list,null,['class' => 'form-control','placeholder' => 'All','id'=>'kpi_group'])}}
            </div>
            <div class="col-md-3">
                <input type="checkbox" name="filter" id="user-filter" value="1"> Allocated
            </div>
        </div>
    </div>
</div>
<table class="table table-bordered" id="allocation-table">
    <thead>
        <tr>
            <th class="dt-body-center text-center"><input name="select_all" value="1" id="example-select-all" type="checkbox" /></th>
            <th>Employee Id</th>
            <th>Employee Name</th>
            <th>Employee Email</th>
            <th>Role</th>
            <th>Allocated Groups</th>
            <th>Unallocate</th>
        </tr>
    </thead>
</table>
<div class="col-md-6 allocation-controls top-25">
    <button class="btn blue allocate-submit-btn admin-btn" style='margin-right:5px'>Allocate</button>
    <button class="btn blue allocate-cancel-btn admin-btn">Cancel</button>
</div>
@stop
@section('js')
<script>
    $(function() {
        $("#kpi_group").select2();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        $.fn.dataTable.ext.errMode = 'throw';
        try {
            var table = $('#allocation-table').DataTable({
                bProcessing: false,
                dom: 'lfrtBip',
                buttons: [{
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
                        action: function(e, dt, node, conf) {
                            emailContent(table, 'Employee Allocation');
                        }
                    }
                ],
                processing: true,
                serverSide: true,
                fixedHeader: true,
                ajax: {
                    url: '{{route("admin.kpi.groups-allocation.list")}}', // Change this URL to where your json data comes from
                    type: "GET", // This is the default value, could also be POST, or anything you want.
                    data: function(d) {
                        d.type = $("#allocation-container input[name='allocation_type']:checked").val(); /* All, Allocated, Unallocated */
                        d.filter = $('#user-filter').val();
                    },
                    "error": function(xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                'columnDefs': [{
                        'targets': 0,
                        'searchable': false,
                        'orderable': false,
                        'className': 'dt-body-center',
                        'render': function(data, type, full, meta) {
                            return '<input type="checkbox" id="emp_id" name="employee_id" value="' + $('<div/>').text(data).html() + '">';
                        }
                    },
                    // {
                    // 'targets': 6,
                    //         'searchable':false,
                    //         'orderable':false,
                    //         'visible':true
                    // }
                ],
                select: {
                    style: 'os',
                    selector: 'td:first-child'
                },
                lengthMenu: [
                    [10, 25, 50, 100, 500, -1],
                    [10, 25, 50, 100, 500, "All"]
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: '',
                        sortable: false
                    },
                    {
                        data: 'emp_no',
                        name: 'emp_no'
                    },
                    {
                        data: 'emp_name',
                        name: 'emp_name'
                    },
                    {
                        data: 'emp_email',
                        name: 'emp_email'
                    },
                    {
                        data: 'emp_role',
                        name: 'emp_role'
                    },
                    {
                        data: 'emp_groups',
                        name: 'emp_groups',
                        'orderable': false
                    },
                    {
                        data: null,
                        render: function(o) {
                            return (o.emp_groups !== '') ? '<a class="unallocate btn fa fa-minus-square fa-lg" data-id=' + o.id + '>' + '</a>' : '';
                        }
                    }
                ]
            });
        } catch (e) {
            console.log(e.stack);
        }

        $("#allocation-table_wrapper").addClass("datatoolbar");

        // Handle click on "Select all" control
        $('#allocation-table').on('click', '#example-select-all', function() {
            var rows = table.rows({
                'search': 'applied'
            }).nodes();
            $('input[type="checkbox"]', rows).prop('checked', this.checked);
        });


        //Allocate the guard to the supervisor
        $('.allocate-submit-btn').on('click', function(e) {
            groupId = $("select#kpi_group").val();
            employee_id = $("#allocation-table input[name=employee_id]:checked").length;

            if (Number(groupId) > 0 && Number(employee_id) > 0) {
                employee_ids = [];
                $("#allocation-table input[name=employee_id]:checked").each(function() {
                    employee_ids.push($(this).val());
                });
                employee_ids = (JSON.stringify(employee_ids));
                $.ajax({
                    url: "{{route('admin.kpi.groups-allocation.store')}}",
                    method: 'POST',
                    data: {
                        'kpi_group_id': groupId,
                        'user_ids': employee_ids
                    },
                    success: function(data) {
                        if (data.success) {
                            swal("Allocated", "Employee has been allocated", "success");
                            table.ajax.reload();
                        } else {
                            swal("Alert", "Employee(s) already allocated to this group", "warning");
                        }
                    },
                    error: function(xhr, textStatus, thrownError) {
                        console.log(xhr.status);
                        console.log(thrownError);
                        swal("Oops", "Something went wrong", "warning");
                    },
                });
            } else {
                var msg_str = "Please select ";
                var concat_str = "and ";
                if (Number(employee_id) <= 0) {
                    msg_str = msg_str + "Group " + concat_str + "employee";
                } else {
                    msg_str = msg_str + "employee"
                }
                swal("Alert", msg_str, "warning");
            }
        });

        //Unallocate the guard allocated to the supervisor
        $("#allocation-table").on("click", ".unallocate", function(e) {
            id = $(this).data('id');
            group_id = $("select#kpi_group").val();

            swal({
                title: "Are you sure?",
                text: "You won't be able to undo this action",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Unallocate",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            }, function(unalloc) {
                if (unalloc) {
                    $.ajax({
                        url: "{{route('admin.kpi.groups-allocation.unallocate')}}",
                        type: 'POST',
                        data: {
                            'kpi_group_id': group_id,
                            'employee_id': id
                        },
                        success: function(data) {
                            if (data.success) {
                                swal("Unallocated", "Employee has been unallocated", "success");
                                table.ajax.reload();
                            } else {
                                swal("Alert", "Employee not allocated to this group", "warning");
                            }
                        },
                        error: function(xhr, textStatus, thrownError) {
                            console.log(xhr.status);
                            console.log(thrownError);
                            swal("Oops", "Something went wrong", "warning");
                        },
                    });
                };
            });
        });

        /* Clear Search - Start */
        $("#clear-search").click(function() {
            $("#allocation-table_filter input[type='search']").val('');
            table.search('').draw();
        });
        /* Clear Search - End */

        /* Filters for user - Start */
        $('#user-filter').on('change', function() {
            $kpi_group = '';
            //TODO:update the logic
            if (($('#user-filter').prop('checked') == true) && ($('#kpi_group').val() != 0)) {
                $kpi_group = $('#kpi_group').val();
                var table_url = '{{ route("admin.kpi.groups-allocation.list") }}';
                table_url = table_url += '?kpi_group=' + $kpi_group;
            } else {
                var table_url = '{{ route("admin.kpi.groups-allocation.list") }}';
            }
            table.ajax.url(table_url).load();
        });

        //Uncheck all checkbox on clicking cancel button
        $(".allocate-cancel-btn").on('click', function() {
            $("#example-select-all, #group1 input[name='allocation_type'], input:checkbox").prop('checked', false);
        });

    });
</script>
@stop
