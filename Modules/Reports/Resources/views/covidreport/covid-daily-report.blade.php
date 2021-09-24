@extends('layouts.app')
@section('content')
    <div class="table_title">

        <h4>Daily Transaction Report</h4>

    </div>
    <div class="row">
        <div class="col-md-1">
            Customers
        </div>
        <div class="col-md-3">
            <select id="project" name="project[]" multiple placeholder="Select a Project">

                <option value=""></option>

                @foreach ($Customers as $value)
                    <option value="{{$value["id"]}}">{{$value["project_number"]}}-{{$value["client_name"]}}</option>
                @endforeach

            </select>
        </div>
        <div class="col-md-1">
            Area Manager
        </div>
        <div class="col-md-3">
            <select id="areamanagerselect" multiple="multiple">
                @foreach ($areaManager as $key=>$value)
                    <option value="{{$value[0]}}">{{$value[1]}}</option>
                @endforeach


            </select>
        </div>
        <div class="col-md-1">
            Employees
        </div>
        <div class="col-md-3">
            <select id="employeesselect" multiple="multiple">

                @foreach ($Employees as $Employee)
                    <option value="{{$Employee->id}}">{{$Employee->getFullNameAttribute()}}</option>
                @endforeach

            </select>
        </div>


    </div>
    <div class="row mt-2 pb-3">


        <div class="col-md-1">
            <label>Start Date</label>
        </div>
        <div class="col-sm-3">
            <input id="start_date" class="form-control datepicker" placeholder="Start Date" type="text" max="2900-12-31"
                   value="{{date('Y-m-d', strtotime("-1 days"))}}">
        </div>
        <div class="col-md-1">
            <label>End Date</label>
        </div>
        <div class="col-sm-3">
            <input id="end_date" class="form-control datepicker" placeholder="End Date" type="text" max="2900-12-31"
                   value="{{date('Y-m-d')}}">
        </div>
        <div class="col-md-1">
            <input id="filterbutton" class="btn btn-primary form-control" type="button" value="Search">
        </div>
    </div>

    <div class="row" id="reportdiv" >
        <div class="col-md-12">
            <table class="table table-bordered" id="report-table">
                <thead>
                <tr>
                    <th>Project Number</th>
                    <th>Project Name</th>
                    <th>Area Manager</th>
                    <th>Employee Id</th>
                    <th>Employee Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Date</th>
                    <th>Sign In Time</th>
                    <th>Screening Time</th>
                    <th>Screening Completed</th>
                    <th>Screening Passed</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">

        function collectFilterData() {
            return {
                "customer_id": $("#project").val(),
                "startDate": $("#start_date").val(),
                "endDate": $("#end_date").val(),
                "employees": $("#employeesselect").val(),
                "area_manager": $("#areamanagerselect").val()
            }
        }

        $(document).ready(function () {
            $("#project").select2()
            $("#areamanagerselect").select2();
            $("#employeesselect").select2();
            // setTimeout(() => {
            //     $("#filterbutton").trigger("click")
            // }, 500);

            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();

            today = yyyy + '-' + mm + '-' + dd;

            var table = $("#report-table").dataTable({
                bProcessing: false,
                dom: 'Blfrtip',
                buttons: [{
                        extend: 'excelHtml5',
                        text: 'Excel',
                        title: 'Daily Transaction Report ' + today,
                        // className: 'btn btn-primary fa fa-file-excel-o',
                        // exportOptions: {
                        //     columns: [0, 1, 2, 3, 4, 5, 6]
                        // }
                    },
                ],
                processing: false,
                serverSide: true,
                ajax: {
                    "url": "{{ route('reports.getCovidReport') }}",
                    "data": function ( d ) {
                        return $.extend({}, d, collectFilterData());
                    },
                    "error": function(xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    },
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                order: [
                    [7, "asc"]
                ],
                lengthMenu: [
                    [10, 25, 50, 100, 500, -1],
                    [10, 25, 50, 100, 500, "All"]
                ],
                columns: [
                    {
                        data: 'project_number',
                        name: 'project_number',
                        width:"5%",
                    },
                    {
                        data: 'project_name',
                        name: 'project_name'
                    },
                    {
                        data: 'area_manager',
                        name: 'area_manager'
                    },
                    {
                        data: 'employee_number',
                        name: 'employee_number'
                    },
                    {
                        data: 'employee_name',
                        name: 'employee_name'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'date',
                        name: 'date',
                        width:"7%",
                        render: function (data, type) {
                        if ( type === 'display' || type === 'filter' ) {
                            return moment(data).format('DD-MMM-YY');
                        }
                        return data;
                    }
                    },
                    {
                        data: 'sign_in',
                        name: 'sign_in',
                        width:"7%",
                    },
                    {
                        data: 'covid_screen_submit',
                        name: 'covid_screen_submit',
                        width:"7%",
                    },
                    {
                        data: 'screening_completed',
                        name: 'screening_completed'
                    },
                    {
                        data: 'screening_passed',
                        name: 'screening_passed'
                    }
                ]
            });
        });



        $(document).on("click", "#filterbutton", function (e) {
            e.preventDefault();
            var table = $('#report-table').DataTable();
            table.ajax.reload();
        })
    </script>
    <script src="sweetalert2/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/promise-polyfill@8/dist/polyfill.js"></script>
@endsection
@section('css')
    <style>
        /* .fixed {
            position: fixed;
            top: 8rem;
        } */
        .dataTables_wrapper {
            width: 100%;
        }

        /* .dt-button.buttons-excel.buttons-html5 {
            position: fixed;
        }
        #resulttable_length.dataTables_length {
            position: fixed;
            margin-left: 4rem;
        }
        #resulttable_paginate{
            float:right;
            margin-right: 116rem;
        }
        #resulttable_info{
            float: left;
        }
        #resulttable_filter{
            right: 1rem;
            position: fixed;
        }
        #resulttable {
            margin-top: 40px;
            position: relative;
            z-index: 5;
        } */
        footer {
            position: fixed;
        }

        .colorClass1 {
            background-color: #F3F3F3;
        }

        .colorClass2 {
            background-color: #d9d9d9;
        }

        .swal2-styled.swal2-confirm {
            background-color: #003A63 !important;
        }

        .swal2-icon.swal2-warning {
            border-color: #F8BB86 !important;
            color: #F8BB86 !important;
        }

        .labelstyle {
            float: right;
            margin-right: -15px;
            margin-top: 6px;
        }

        body {
            overflow-x: hidden; /* Hide horizontal scrollbar */
        }
    </style>
@endsection
