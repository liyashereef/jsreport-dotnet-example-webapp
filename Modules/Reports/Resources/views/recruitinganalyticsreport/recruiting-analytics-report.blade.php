@extends('layouts.app')
@section('content')
<style>
    #table-id .fa {
        margin-left: 11px;
    }
    table.dataTable tbody th, table.dataTable tbody td {
         padding: 8px 18px;
    }
    .add-new {
        margin-top: 0px;
        margin-bottom: 10px;
    }
    html {
        overflow-x: hidden;
    }

    #content-div{
        padding-right: 53px;
    }

</style>
<div class="table_title">
    <h4>Recruiting Analytics Report</h4>
</div>
<div class="add-new">Export Report Via <span class="add-new-label"> Email</span></div>
<div class="table-responsive">
    <table class="table table-bordered" id="jobs-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Candidate Name</th>
                <th>Date Applied</th>
                <th>Initial Rating</th>
                <th>City</th>
                <th>Years Of Security Experience</th>
                <th>Last Wage in Industry</th>
                <th>Last Provider</th>
                <th>Last Provider - Other</th>
                <th>Working Status</th>
                <th>Years Lived In Canada</th>
                <th>Do You Have Drivers Licence</th>
                <th>English Speaking</th>
                <th>English Reading</th>
                <th>English Writing</th>
                <th>Career Interest With Commissionaires</th>
                <th>Case Study Score</th>
                <th>English Proficiency</th>
                <th>Personality Score</th>
                <th>Candidate Email</th>
                <th>Candidate Phone</th>
                <th>Job Initially Applied To</th>
                <th>Client</th>
                <th>Date Required</th>
                <th>Number Of Positions Open</th>
                <th>Role</th>
                <th>Job Code Reassignment</th>
                <th>Client Reassignment</th>
                <th>Current Wage</th>
                <th>Reassigned Wage</th>
                <th>Process Number</th>
                <th>Process Step</th>
                <th>Completion Date</th>
                <th>Notes</th>
                <th>Entered By</th>
            </tr>
        </thead>
    </table>
</div>
@stop @section('scripts')
<script>
    $(function () {
        var table = $('#jobs-table').DataTable({
            processing: false,
            serverSide: true,
            responsive: false,
            scrollX:true,
            ajax: {
                "url":'{{ route('reports.recruitinganalyticsreport.list') }}',
                 "type": "POST",
                 headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                "error": function (xhr, textStatus, thrownError) {
                    if(xhr.status === 401){
                        window.location = "{{ route('login') }}";
                    }
                },
            },

            order: [
                [0, "asc"]
            ],
            lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
            columns: [
                {data: 'DT_RowIndex', name: '',sortable:false},
                {
                    data: 'candiate_name',
                    name: 'candiate_name',
                    defaultContent: '--'
                },
                {
                    data: 'date_applied',
                    name: 'date_applied',
                    defaultContent: '--'
                },
                {
                    data: 'inital-rating',
                    name: 'inital-rating',
                    defaultContent: '--'
                },
                {
                    data: 'candiate_city',
                    name: 'candiate_city',
                    defaultContent: '--'
                },
                {
                    data: 'years_of_security_experiance',
                    name: 'years_of_security_experiance',
                    defaultContent: '--'
                },
                {
                    data: 'last_wage',
                    name: 'last_wage',
                    defaultContent: '--'
                },
                {
                    data: 'last_provider',
                    name: 'last_provider',
                    defaultContent: '--'
                },
                {
                    data: 'last_provider_other',
                    name: 'last_provider_other',
                    defaultContent: '--'
                },
                {
                    data: 'working_status',
                    name: 'working_status',
                    defaultContent: '--'
                },
                {
                    data: 'years_lived_in_canada',
                    name: 'years_lived_in_canada',
                    defaultContent: '--'
                },
                {
                    data: 'drivers_licenece',
                    name: 'drivers_licenece',
                    defaultContent: '--'
                },
                {
                    data: 'english_speaking',
                    name: 'english_speaking',
                    defaultContent: '--'
                },
                {
                    data: 'english_reading',
                    name: 'english_reading',
                    defaultContent: '--'
                },
                {
                    data: 'english_writing',
                    name: 'english_writing',
                    defaultContent: '--'
                },
                {
                    data: 'career_interest',
                    name: 'career_interest',
                    defaultContent: '--'
                },
                {
                    data: 'case_study_score',
                    name: 'case_study_score',
                    defaultContent: '--'
                },
                {
                    data: 'english_proficiency',
                    name: 'english_proficiency',
                    defaultContent: '--'
                },
                {
                    data: 'personality_score',
                    name: 'personality_score',
                    defaultContent: '--'
                },
                {
                    data: 'candiate_email',
                    name: 'candiate_email',
                    defaultContent: '--'
                },
                {
                    data: 'candiate_phone',
                    name: 'candiate_phone',
                    defaultContent: '--'
                },
                {
                    data: 'job_intially_applied_to',
                    name: 'job_intially_applied_to',
                    defaultContent: '--'
                },
                {
                    data: 'client_name',
                    name: 'client_name',
                    defaultContent: '--'
                },
                {
                    data: 'date_required',
                    name: 'date_required',
                    defaultContent: '--'
                },
                {
                    data: 'position_open',
                    name: 'position_open',
                    defaultContent: '--'
                },
                {
                    data: 'position_role',
                    name: 'position_role',
                    defaultContent: '--'
                },
                {
                    data: 'job_code_reassignment',
                    name: 'job_code_reassignment',
                    defaultContent: '--'
                },
                {
                    data: 'client_reassignment',
                    name: 'client_reassignment',
                    defaultContent: '--'
                },
                {
                    data: 'current_wage',
                    name: 'current_wage',
                    defaultContent: '--'
                },
                {
                    data: 'reassigned_wage',
                    name: 'reassigned_wage',
                    defaultContent: '--'
                },
                {
                    data: 'process_number',
                    name: 'process_number',
                    defaultContent: '--'
                },
                {
                    data: 'process_step',
                    name: 'process_step',
                    defaultContent: '--'
                },
                {
                    data: 'completion_date',
                    name: 'completion_date',
                    defaultContent: '--'
                },
                {
                    data: 'notes',
                    name: 'notes',
                    defaultContent: '--'
                },
                {
                    data: 'entered_by',
                    name: 'entered_by',
                    defaultContent: '--'
                }
            ]
        });

        $('.add-new').on('click',function (e) {
             var url = "{{ route('reports.recruitinganalyticsreport.excel') }}";
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        console.log(data);
                        if (data.success) {
                            swal("Success","Request processing. You will receive the report in your email in few minutes", "success");
                            table.ajax.reload();
                        }
                     else {
                              swal("Warning", "Email Sending Failed. Try again", "warning");
                        }
                    },
                    error: function (xhr, textStatus, thrownError) {
                        alert(xhr.status);
                        alert(thrownError);
                    },
                    contentType: false,
                    processData: false,
                });
        });
    });
</script>
@stop
