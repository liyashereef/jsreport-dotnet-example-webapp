@extends('layouts.app')
@section('content')
<!-- Style for status -->
<style>
    .not-approved {
        color: #808080 !important;
    }

    .approved {
        color: #008000 !important;
    }

    .status {
        cursor: default !important;
    }

    .copy {
        margin-top: 20% !important;
    }

    #timesheet-tabs {
        margin: 0px 0px 3px 1px;
    }

    #timesheet-tabs .nav-tabs .nav-item.show .nav-link,
    .nav-tabs .nav-link.active {
        color: #f48452;
    }
    .timesheet-filters{
        background: #f9f1ec;
        padding: 11px 5px;
    }
    .timesheet-filters .filter-text{
        position: absolute;
        top: 1;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .export_btn_ta{
        min-width: 130px;
        text-align: center;
    }
</style>

<div class="table_title">
    <h4>Timesheet Approval</h4>
</div>
<div class="timesheet-filters mb-2">
    <div class="row">
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-3"><label class="filter-text">Pay Period</label></div>
                <div class="col-md-8">
                    <select class="form-control option-adjust timesheet-filter" name="pay-period" id="payperiod-filter">
                        <option value="">All</option>
                        @foreach($payperiod_list as $each_payperiod)
                        <option value="{{$each_payperiod->id}}" @if($each_payperiod->id == $current_payperiod->id) selected
                            @endif>{{$each_payperiod->pay_period_name}}
                            {{!empty($each_payperiod->short_name)?('('.$each_payperiod->short_name.')'):''}}
                        </option>
                        @endforeach
                    </select>
                    <span class="help-block"></span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-3">
                    <label class="filter-text">Week</label>
                </div>
                <div class="col-md-8">
                    <select class="form-control option-adjust timesheet-filter" id="e-week-filter">
                        <option selected value="">Select Week</option>
                        <option value="1">Week 1</option>
                        <option value="2">Week 2</option>
                    </select>
                    <span class="help-block"></span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-3">
                    <label class="filter-text">Customer</label>
                </div>
                <div class="col-md-8">
                    <select class="form-control option-adjust timesheet-filter" id="e-customer-filter">
                        <option selected value="">Select Customer</option>
                        @foreach($allocated_customers as $allocated_customer)
                        <option value="{{$allocated_customer->id}}">
                            {{$allocated_customer->project_number}} - {{$allocated_customer->client_name}}</option>
                        @endforeach
                    </select>
                    <span class="help-block"></span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-3">
                    <label class="filter-text">Employee</label>
                </div>
                <div class="col-md-8">
                    <select class="form-control option-adjust timesheet-filter" id="e-employee-filter">
                        <option selected value="">Select Employee</option>
                        @foreach($employeeLookupList as $key=>$employees)
                        <option value="{{$key}}">{{$employees}}</option>
                        @endforeach
                    </select>
                    <span class="help-block"></span>
                </div>
            </div>
        </div>
    </div>
</div>
<a  href="javascript:void(0)"
            style="display:none;"
            id="timesheet-export"
            class="add-new buttons-excel buttons-html5 export_btn_ta float-right m-1">Export</a>
<a  href="javascript:void(0)"
            style="display:none;"
            id="timesheet-vision-export"
            class="add-new buttons-excel buttons-html5 export_btn_ta float-right m-1">Vision Export</a>


<ul class="nav nav-tabs" id="timesheet-tabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#approval-tab" role="tab" data-approved-status="0" aria-selected="false">Pending</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#approval-tab" role="tab" data-approved-status="1" aria-selected="true">Approved</a>
    </li>
</ul>

<div class="tab-content" id="approval-tabs">
    <div class="table-responsive tab-pane fade show active" id="approval-tab" role="tabpanel" aria-labelledby="home-tab">
        <table class="table table-bordered" id="timesheet-table">
            <thead>
                <tr>
                    <th></th>
                    <th>Employee Id</th>
                    <th>Employee Name</th>
                    <th>Role</th>
                    <th>Project Number</th>
                    <th>Client</th>
                    <th>Week</th>
                    <th>Pay Period</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Total Regular Hours</th>
                    <th>Total Overtime Hours</th>
                    <th>Total Stat Hours</th>
                    @can('show_total_earnings')
                    <th>Total Earnings</th>
                    @endcan
                    <th>Status</th>
                    <th>View</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@stop


@section('scripts')
<script>
    $(function() {
        //convert time to seconge eg: 00:20 -> 1200
        function parsetTimeStringToSeconds(time) {
            var seconds = 0;
            var timeArray = time.split(':');
            if(timeArray.length >=2){
                seconds += Number(timeArray[1] * 60); //convert minuts
                seconds += Number(timeArray[0] * 60 * 60); //convert hours
            }
            return seconds;
        }

        //convert seconds to time string eg: 1200 -> 00:20
        function secondsToTimeString(seconds) {
            var seconds = parseInt(seconds, 10); // don't forget the second param
            var hours = Math.floor(seconds / 3600);
            var minutes = Math.floor((seconds - (hours * 3600)) / 60);
            if (hours < 10) {
                hours = "0" + hours;
            }
            if (minutes < 10) {
                minutes = "0" + minutes;
            }
            return hours + ':' + minutes;
        }

        function formatCpidTime(time){
            if(!time){
                return '-';
            }
            return secondsToTimeString(time);
        }

        var tabStatus = 0; //is approved for first time
        function collectFilterData() {
            return {
                payperiod: $("#payperiod-filter").val(),
                customer: $('#e-customer-filter').val(),
                employee: $('#e-employee-filter').val(),
                week: $('#e-week-filter').val(),
                is_manual:0,
                status: tabStatus
            }
        }
        ['#payperiod-filter','#e-week-filter','#e-customer-filter','#e-employee-filter'].forEach(function(item){
            $(item).select2();
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        $.fn.dataTable.ext.errMode = 'throw';
        try {
            var table = $('#timesheet-table').DataTable({
                processing: true,
                serverSide: true,
                fixedHeader: true,
                responsive: false,
                bProcessing: false,
                //dom: 'Blfrtip',
                // buttons: [{
                //         extend: 'pdfHtml5',
                //         // text: ' ',
                //         // className: 'btn btn-primary fa fa-file-pdf-o',
                //         //orientation: 'landscape', //landscape give you more space
                //         pageSize: 'A3', //A0 is the largest A5 smallest(A0,A1,A2,A3,legal,A4,A5,letter))
                //         exportOptions: {
                //             columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 11, 12, 13],
                //             stripNewlines: false,
                //             format: {
                //                 body: function(data, rowIdx, columnIdx) {
                //                     if (columnIdx == 0) {
                //                         return '';
                //                     }
                //                     if (columnIdx == 12) {
                //                         if (data.search("data-status=1") >= 0) {
                //                             return "Approved";
                //                         } else {
                //                             return "Not Approved";
                //                         }
                //                     }
                //                     return data;
                //                 }
                //             }

                //         }
                //     },
                //     {
                //         extend: 'excelHtml5',
                //         // text: ' ',
                //         // className: 'btn btn-primary fa fa-file-excel-o',
                //         exportOptions: {
                //             columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 11, 12, 13],
                //             stripNewlines: false,
                //             format: {
                //                 body: function(data, rowIdx, columnIdx) {
                //                     if (columnIdx == 0) {
                //                         return '';
                //                     }
                //                     if (columnIdx == 12) {
                //                         if (data.search("data-status=1") >= 0) {
                //                             return "Approved";
                //                         } else {
                //                             return "Not Approved";
                //                         }
                //                     }
                //                     return data;
                //                 }
                //             }
                //         }
                //     },
                //     {
                //         extend: 'print',
                //         // text: ' ',
                //         // className: 'btn btn-primary fa fa-print',
                //         exportOptions: {
                //             columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 11, 12, 13],
                //             stripNewlines: false,
                //             format: {
                //                 body: function(data, rowIdx, columnIdx) {
                //                     if (columnIdx == 0) {
                //                         return '';
                //                     }
                //                     if (columnIdx == 12) {
                //                         if (data.search("data-status=1") >= 0) {
                //                             return "Approved";
                //                         } else {
                //                             return "Not Approved";
                //                         }
                //                     }
                //                     return data;
                //                 }
                //             }
                //         }
                //     }
                // ],
                ajax: {
                    "url": "{{ route('approval.getTimesheetReport') }}",
                    "data": function(d) {
                        return $.extend({}, d, collectFilterData());
                    },
                    "error": function(xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    },
                    'dataFilter': function(response) {
                        return response;
                    },
                },
                'columnDefs': [{
                        'targets': 0,
                        'searchable': false,
                        'orderable': false,
                        'className': 'dt-body-center',
                        'render': function(data, type, full, meta) {
                            // return '<input type="checkbox" class="list_check" id="employee-shift-payperiod-id" name="employee_shift_payperiod_ids" value="' +
                            //     $('<div/>').text(data).html() + '">';
                            return '';
                        }
                    },
                    {
                        className: "nowrap",
                        "targets": [12]
                    }
                ],
                select: {
                    style: 'os',
                    selector: 'td:first-child'
                },
                lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                columns: [{
                        data: 'id',
                        name: 'id',
                        // "visible": false,
                        "searchable": false,
                        render: function(data, type, full, meta) {
                            //if no cpids hide the button.
                            if (full.cpids.length <= 0) {
                                return '';
                            }
                            return '<button attr-id="'+data+'"  class="btn fa fa-plus-square "></button>';
                        },
                        className: 'details-control',
                    },
                    {
                        data: 'employee_no',
                        name: 'employee_no'
                    },
                    {
                        data: 'full_name',

                        name: 'full_name'
                    },
                    {
                        data: null,
                        name: 'role',
                        render: function(data, type, row, meta) {
                            return uppercase(data.role.replace('_', ' '));
                        },
                    },
                    {
                        data: 'project_number',
                        name: 'project_number'
                    },
                    {
                        data: 'client_name',
                        name: 'client_name'
                    },
                    {
                        data: 'payperiod_week',
                        name: 'payperiod_week'
                    },
                    {
                        data: 'pay_period_name',
                        name: 'pay_period_name'
                    },
                    {
                        data: 'start_date',
                        name: 'start_date'
                    },
                    {
                        data: 'end_date',
                        name: 'end_date'
                    },
                    {
                        data: 'total_regular_hours',
                        name: 'total_regular_hours'
                    },
                    {
                        data: 'total_overtime_hours',
                        name: 'total_overtime_hours'
                    },
                    {
                        data: 'total_statutory_hours',
                        name: 'total_statutory_hours'
                    },
                    @can('show_total_earnings')
                    {
                        data: 'total_earnings',
                        name: 'total_earnings',
                        defaultContent:'--'
                    },
                    @endcan
                    {
                        data: null,
                        render: function(o) {
                            if (o.approved == 0 || o.approved == null)
                                btnclass = 'not-approved';
                            else
                                btnclass = 'approved';
                            return '<a href="#" class="status btn ' + btnclass +
                                ' fa fa-check-circle fa-lg" data-id=' + o.id + ' data-status=' + o.approved +
                                '>' + '' + '</a>';
                        },
                        name: 'approved'
                    },
                    {
                        data: null,
                        render: function(o) {
                            return '<a target="_blank" href="timesheet/view/' + o.id +
                                '" class="view btn fa fa-eye" data-id=' + o.id +
                                ' data-status=' + o.notes + '>' + '' + '</a>';
                        },
                        orderable: false,
                    },
                ]

            });

        } catch (e) {
            console.log(e.stack);
        }

        // Add event listener for opening and closing details
        $('#timesheet-table tbody').on('click', 'td.details-control', function() {
            //console.log($(this+" .fa-plus-square").attr("attr-id"))
            //debugger

            var tr = $(this).closest('tr');
            var row = table.row(tr);

            let shiftRepoId=row.data().id;


            if (row.data()) {

                if (row.data().cpids.length <= 0) {
                    // return;
                }
            }
            if (row.child.isShown()) {
                // This row is already open - close it
                tr.find('td.details-control').html('<button  class="btn fa fa-plus-square "></button>');
                row.child.hide();
                tr.removeClass('shown');
                refreshSideMenu();
            } else {
                // Open this parentNode
                tr.find('td.details-control').html('<button  class="btn fa fa-minus-square "></button>');
                $.ajax({
                    type: "post",
                    url: "{{route("approval.getTimesheetCpidData")}}",
                    data: {"shiftId":shiftRepoId},
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        var responseData=jQuery.parseJSON(response);
                        row.child(format(responseData)).show();
                        tr.addClass('shown');
                        refreshSideMenu();
                    }
                });

            }
        });

        /* Formatting function for row details - modify as you need */
        function format(d) {
            if (d.length <= 0) {
                return '';
            }
            var tbody = '';


            for (const [key, value] of Object.entries(d)) {
                    c_row = '';
                    c_row += '<td>' +value.cpids+ '</td>';
                    c_row += '<td>' +  value.function+ '</td>';
                    c_row += '<td>' + value.position + '</td>';
                    c_row += '<td>' + value.type + '</td>';
                    c_row += '<td>' + value.code + '</td>';
                    c_row += '<td>' + (value.hours) + '</td>';
                    tbody += '<tr>' + c_row + '</tr>';
            }


            return '<table class="DataTable subtable dataTable">' +
                    '<tr>' +
                    '<th>CPID</th> <th>CPID Function</th> <th>Position</th> <th>Type</th> <th>Code</th> <th>Hours</th>' +
                    '</tr>' +
                    '<tbody>' + tbody + '</tbody>' +
                    '</table>'
        }

        //on filter change refresh data
        $(".timesheet-filter").change(function() {
            table.ajax.reload();
        });
        //on tab switch refresh data
        $('#timesheet-tabs .nav-link').on('click', function() {
            tabStatus = $(this).data('approved-status');
            let exportButton =  $('#timesheet-export');
            let exportVisionButton =  $('#timesheet-vision-export');
            if(tabStatus){
                exportButton.show();
                exportVisionButton.show();
            }else{
                exportButton.hide();
                exportVisionButton.hide();

            }
            table.ajax.reload();
        });

        $("#timesheet-table_wrapper").addClass("no-datatoolbar datatoolbar");
        $('#approve').on('click', function(e) {
            employee_shift_payperiod_ids = [];
            $("#timesheet-table input[name=employee_shift_payperiod_ids]:checked").each(function() {
                employee_shift_payperiod_ids.push($(this).val());
            });
            employee_shift_payperiod_ids = (employee_shift_payperiod_ids);
            swal({
                title: "Are you sure?",
                text: "You want to approve the timesheet",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-success",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                closeOnConfirm: false,
                closeOnClickOutside: false,
            }, function(TimesheetApprove) {
                $.ajax({
                    url: "{{route('approval.store')}}",
                    method: 'POST',
                    data: {
                        'employee_shift_payperiod_ids': employee_shift_payperiod_ids
                    },
                    success: function(data) {
                        if (data.success) {
                            if (data.not_approved.length > 0) {
                                var datasetContents = [];
                                for (ele in data.not_approved) {
                                    var rowContents = [];
                                    var addHourStr = "";
                                    rowContents.push(data.not_approved[ele][
                                        'employee_name'
                                    ]);
                                    if (data.not_approved[ele]['overtime_hours'] !=
                                        "00:00") {
                                        addHourStr += " Overtime (" + data.not_approved[
                                            ele]['overtime_hours'] + ") ";
                                    }
                                    if (data.not_approved[ele]['statutory_hours'] !=
                                        "00:00") {
                                        if (addHourStr != "") {
                                            addHourStr += " / ";
                                        }
                                        addHourStr += " Statutory (" + data.not_approved[
                                            ele]['statutory_hours'] + ") ";
                                    }
                                    rowContents.push(addHourStr);
                                    datasetContents.push(rowContents);
                                }
                                var alertTable = document.createElement("table");
                                alertTable.setAttribute("id", "noApprove");
                                document.body.appendChild(alertTable);
                                var dataSet = datasetContents;
                                $('#noApprove').DataTable({
                                    data: dataSet,
                                    columns: [{
                                            title: "Name"
                                        },
                                        {
                                            title: "Additional Hours"
                                        }
                                    ]
                                });
                                $('#noApprove').parents('div.dataTables_wrapper').first()
                                    .remove();
                                swal({
                                    title: "Alert",
                                    text: 'Employee(s) observed with additional hours (overtime/statutory hours) will need to be approved individually from their respective Timesheet Details screen.<br><br>Additional hours observed for the following employee(s):<br><br>' +
                                        '<table id="noApprove"  class="no-footer" role="grid" aria-describedby="noApprove_info">' +
                                        alertTable.innerHTML + '</table>',
                                    html: true,
                                    className: "no-approve"
                                });
                                //console.log(data.not_approved);
                            } else {
                                swal("Approved", "Timesheet has been approved",
                                    "success");
                            }
                            table.ajax.reload();
                        } else {
                            //alert(data);
                            console.log(data);
                            swal("Oops", "Timesheet approval was unsuccessful",
                                "warning");
                        }
                    },
                    error: function(xhr, textStatus, thrownError) {
                        //alert(xhr.status);
                        //alert(thrownError);
                        console.log(xhr.status);
                        console.log(thrownError);
                        swal("Oops", "Something went wrong", "warning");
                    },
                });
            });
        });
        $('#timesheet-table').on('click', '.list_check', function() {
            if ($('.list_check').is(":checked"))
                $("#right-button").show();
            else
                $("#right-button").hide();
        });
        $('#timesheet-export').click(function(e){
            e.preventDefault();
            let queryString = $.param(collectFilterData());
            let url = "{{route('timesheet.export-approved')}}";
            window.open(url+'?'+queryString,'_self');
        });

        $('#timesheet-vision-export').click(function(e){
            e.preventDefault();
            let queryString = $.param(collectFilterData());
            let url = "{{route('timesheet.export-approved-vision')}}";
            window.open(url+'?'+queryString,'_blank');
        });
    });
</script>


@stop
