@extends('layouts.app')
@section('content')
<div class="table_title">
    <h4>Termination Report</h4>
</div>
<div class="row">
    <div class="col-lg-2"></div>
    <div class="col-lg-1">
        <label for="startDate" class="labelstyle">Start Date</label>
    </div>
    <div class="col-lg-2">
        <input id="startDate" class="form-control datepicker" placeholder="Start Date" type="text" max="2900-12-31" value="{{date('Y-m-d', strtotime("-30 days"))}}">
    </div>
    <div class="col-lg-1"></div>
    <div class="col-lg-1">
        <label for="endDate" class="labelstyle">End Date</label>
    </div>
    <div class="col-lg-2">
        <input id="endDate" class="form-control datepicker" placeholder="End Date" type="text" max="2900-12-31" value="{{date('Y-m-d')}}">
    </div>
    <div class="col-lg-1">
        <input id="filterbutton" class="btn btn-primary" type="button" value="Submit">
    </div>
</div>

<div class="row" id="reportdiv" style="padding: 12px 3px 12px 12px;">
    <table id="resulttable" class="table table-bordered dataTable no-footer dtr-inline">
        <thead>
            <tr>
                <th>Employee No.</th>
                <th>Employee Name</th>
                <th>Unique Id</th>
                <th style="white-space: nowrap;">Exit Interview Date</th>
                <th>Date of Conversion/Hire</th>
                <th>Familiarity</th>
                <th>Understanding of CGL</th>
                <th>Other Companies</th>
                <th>City</th>
                <th>Postal Code</th>
                <th>Years of Experience</th>
                <th>Last Wage</th>
                <th>Last Provider</th>
                <th>Last Provider Other</th>
                <th>Canadian Status</th>
                <th>Age</th>
                <th>License</th>
                <th>Access to Vehicle</th>
                <th>Public Transit</th>
                <th>Education 1</th>
                <th>Education 2</th>
                <th>Education 3</th>
                <th>English Speaking</th>
                <th>French Speaking</th>
                <th>MS Word</th>
                <th>MS Excel</th>
                <th>MS PowerPoint</th>
                <th>Customer Service</th>
                <th>Leadership</th>
                <th>Problem Solving</th>
                <th>Time Management</th>
                <th>Smartphone</th>
                <th>Type of Phone</th>
                <th>Proficiency with Phone</th>
                <th>Military Experience</th>
                <th>Dismissals</th>
                <th>Criminal Convictions</th>
                <th>Career Interest</th>
                <th>Screening Questions (average count)</th>
                <th>Screening Questions (score)</th>
                <th>English Fluency</th>
                <th>Personality</th>
                <th>Employee Rating (cumulative)</th>
                <th>Length of Service</th>
                <th>Type</th>
                <th>Reason Category</th>
                <th>Number of Staff at Site</th>
                <th>Position</th>
                <th>Current Wage 1</th>
                <th>Current Wage 2</th>
                <th>Current Wage 3</th>
                <th>Distance between Work and Home</th>
                <th>Time between Work and Home</th>
            </tr>
        </thead>
    </table>
</div>
@endsection

@section('scripts')
    <script>
        var table;
        $('#startDate').on('change', function (evt) {
            var selectedDate = $('#startDate').val();
            var endDate = $('#endDate').val();

            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();

            today = yyyy + '-' + mm + '-' + dd;

            if (endDate != '' && endDate < selectedDate) {
                $('#start_date').val('');
                swal({
                    icon: 'error',
                    title: 'Oops',
                    text: 'End date is less than start date',
                });
            }
        });

        $('#endDate').on('change', function (evt) {
            var selectedDate = $('#endDate').val();
            var startDate = $('#startDate').val();

            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();

            today = yyyy + '-' + mm + '-' + dd;

            if (startDate > selectedDate) {
                $('#endDate').val('');
                swal({
                    icon: 'error',
                    title: 'Oops',
                    text: 'Start date is greater than end date',
                });
            }
        });

        $(function() {
            $('#startDate').datepicker({
                "format": "yyyy-mm-dd",
                "setDate": new Date() - 30,
            });

            $('#endDate').datepicker({
                "format": "yyyy-mm-dd",
                "setDate": new Date(),
            });

            terminationReport()
        });

        $("#filterbutton").on("click", function (e) {
            let startDate = $("#startDate").val();
            let endDate = $("#endDate").val();
            if (startDate == '') {
                swal({
                icon: 'error',
                title: 'Oops',
                text: 'Please fill start date',
                });
            } else if (endDate == '') {
                swal({
                icon: 'error',
                title: 'Oops',
                text: 'Please fill end date',
                });
            } else {
                table.destroy();
                terminationReport();
            }
        });

        function terminationReport() {
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();

            today = yyyy + '-' + mm + '-' + dd;

            let sDate = $("#startDate").val();
            let eDate = $("#endDate").val();
            table = $('#resulttable').DataTable({
                processing: false,
                serverSide: true,
                responsive: false,
                bInfo: false,
                scrollX: true,
                "scrollY": "450px",
                "scrollCollapse": true,
                dom: 'Blfrtip',
                "pageLength": 10,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    ['10', '25', '50', '100', 'All']
                ],
                buttons: [{
                        extend: 'excelHtml5',
                        title: 'Termination Report - ' + today,
                        exportOptions: {
                            columns: ':visible'
                        },
                    }],
                ajax: {
                    headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
                    "url": "{{route('reports.getTerminationReport')}}",
                    type: "POST",
                    "data": {
                        startDate: sDate,
                        endDate: eDate
                    },
                    'global': true,
                    "error": function (xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    },
                },
                // columnDefs: [
                //     {width: 500, targets: 4},
                //     {"orderable": true, "targets": [2]},
                //     {"orderable": true, "targets": "_all"}
                // ],
                "order": [[3, "desc"]],
                columns: [
                    {data: 'employee_no', name: 'employee_no'},
                    {data: 'employee_name', name: 'employee_name'},
                    {data: 'unique_id', name: 'unique_id'},
                    //{data: 'exit_interview_date', name: 'exit_interview_date'},
                    {data: null, name: 'exit_interview_date',
                    render: function (data, type) {
                        if ( type === 'display' || type === 'filter' ) {
                            return moment(data.exit_interview_date).format('Y-MM-DD');
                        }
                        return data;
                    }
                    },
                    {data: 'date_of_conversion', name: 'date_of_conversion'},
                    {data: 'familiarity', name: 'familiarity'},
                    {data: 'understanding_of_cgl', name: 'understanding_of_cgl'},
                    {data: 'other_companies', name: 'other_companies'},
                    {data: 'city', name: 'city'},
                    {data: 'postal_code', name: 'postal_code'},
                    {data: 'years_of_experience', name: 'years_of_experience'},
                    {data: null, name: 'last_wage', render: function(data) {
                        return data.last_wage != null
                        ? new Intl.NumberFormat('en-US',
                        {style: 'currency',currency: 'USD'}).format(data.last_wage)
                        : null;
                    }},
                    {data: 'last_provider', name: 'last_provider'},
                    {data: 'last_provider_other', name: 'last_provider_other'},
                    {data: 'canadian_status', name: 'canadian_status'},
                    {data: 'age', name: 'age'},
                    {data: 'license', name: 'license'},
                    {data: 'access_to_vehicle', name: 'access_to_vehicle'},
                    {data: 'public_transit', name: 'public_transit'},
                    {data: 'education_1', name: 'education_1'},
                    {data: 'education_2', name: 'education_2'},
                    {data: 'education_3', name: 'education_3'},
                    {data: 'english_speaking', name: 'english_speaking'},
                    {data: 'french_speaking', name: 'french_speaking'},
                    {data: 'msword', name: 'msword'},
                    {data: 'msexcel', name: 'msexcel'},
                    {data: 'mspowerpoint', name: 'mspowerpoint'},
                    {data: 'customerService', name: 'customerService'},
                    {data: 'leadership', name: 'leadership'},
                    {data: 'problemsolving', name: 'problemsolving'},
                    {data: 'timemgmt', name: 'timemgmt'},
                    {data: 'smartphone', name: 'smartphone'},
                    {data: 'type_of_smartphone', name: 'type_of_smartphone'},
                    {data: 'proficiency_with_phone', name: 'proficiency_with_phone'},
                    {data: 'military_experience', name: 'military_experience'},
                    {data: 'dismissals', name: 'dismissals'},
                    {data: 'criminal_convictions', name: 'criminal_convictions'},
                    {data: 'career_interest', name: 'career_interest'},
                    {data: 'screening_questions_avg_count', name: 'screening_questions_avg_count'},
                    {data: 'screening_questions_score', name: 'screening_questions_score'},
                    {data: 'english_fluency', name: 'english_fluency'},
                    {data: 'personality', name: 'personality'},
                    {data: 'employee_rating', name: 'employee_rating'},
                    {data: 'length_of_service', name: 'length_of_service'},
                    {data: 'reason_type', name: 'reason_type'},
                    {data: 'reason_category', name: 'reason_category'},
                    {data: 'no_of_guards', name: 'no_of_guards'},
                    {data: 'position', name: 'position'},
                    {data: null, name: 'current_wage_1', render: function(data) {
                        return data.current_wage_1 != null
                        ? new Intl.NumberFormat('en-US', {style: 'currency', currency: 'USD'})
                        .format(data.current_wage_1)
                        : null;
                    }},
                    {data: null, name: 'current_wage_2', render: function(data) {
                        return data.current_wage_2 != null
                        ? new Intl.NumberFormat('en-US',
                        {style: 'currency',currency: 'USD'}).format(data.current_wage_2)
                        : null;
                    }},
                    {data: null, name: 'current_wage_3', render: function(data) {
                        return data.current_wage_3 != null
                        ? new Intl.NumberFormat('en-US',
                        {style: 'currency', currency: 'USD'}).format(data.current_wage_3)
                        : null;
                    }},
                    {data: 'distance_between_work_and_home', name: 'distance_between_work_and_home'},
                    {data: 'time_between_work_and_home', name: 'time_between_work_and_home'}
                ]
            });
            table.on('page.dt', function() {
                $('html, body').animate({
                scrollTop: $("html").offset().top
                }, 'slow');
                $('#selectAll').prop('checked', false);
            });
        }
    </script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script> --}}
    <script src="sweetalert2/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/promise-polyfill@8/dist/polyfill.js"></script>
@endsection

@section('css')
<style>
    .labelstyle {
        float: right;
        margin-right: -15px;
        margin-top: 6px;
    }
    .dataTables_wrapper{
        width: 97%;
    }
    footer {
        position: fixed;
    }
    body {
        overflow-x: hidden; /* Hide horizontal scrollbar */
        overflow-y: hidden;
    }
</style>
@endsection
