@extends('layouts.candidate-layout')
@section('content')
<div class="table_title">
    <h4>Available Job Opportunities </h4>
</div>
<div class="table-responsive">
    <table class="table table-bordered" id="jobs-table" style="font-weight: normal;">
        <thead>
            <tr>
                <th class="sorting">Click Job for Description</th>
                <th class="sorting">Position Requested</th>
                <th class="sorting">City</th>
                <th class="sorting">Posts</th>
                <th class="sorting">Type</th>
                <th class="sorting">Start Date</th>
                <th class="sorting">Wage</th>
                <th class="sorting">Status</th>
                <th class="sorting">Actions</th>
            </tr>
        </thead>
    </table>
</div>
@stop
@section('scripts')
<script>
    $(function () {
        var table = $('#jobs-table').DataTable({
            fixedHeader: true,
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: '{{ route('applyjob.jobList') }}',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [[0, "desc"]],
            columns: [
                {
                    data: null,
                    name: 'unique_key',
                    render: function (row) {
                        var job_url = '{{ route("job.details", ":id") }}';
                        job_url = job_url.replace(':id', row.id);
                        return '<a target="_blank" href="'+job_url+'">' + row.unique_key + '</a>';
                    }
                },
                {data: 'position_beeing_hired.position', name: 'position_beeing_hired.position'},
                {data: 'city', name: 'city'},
                {data: 'no_of_vaccancies', name: 'no_of_vaccancies'},
                {data: 'assignment_type.type', name: 'assignment_type.type'},
                {data: 'requisition_date', name: 'requisition_date'},
                {data: 'wage_low', name: 'wage_low', render: function (wage_low) {
                        return '$' + parseFloat(wage_low).toFixed(2)
                    }
                },
                {
                    data: 'candidate_jobs.status',
                    name: 'candidate_jobs.status',
                    sortable: false,
                    render: function (status) {
                        return (status == null || status != 'Applied') ? 'Not Applied' : status;
                    }
                },
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var job_url = '{{ route("applyjob", "job_id=:id") }}';
                        job_url = job_url.replace(':id', o.unique_key);
                        var prev_url = '{{ route("applyjob.previous","job_id=:id") }}';
                        prev_url = prev_url.replace(':id', o.unique_key);
                        var application_view_url = '{{ route("applyjob.view", ":id") }}';
                        application_view_url = application_view_url.replace(':id', o.id);
                        actions = '';
                        if (o.candidate_jobs == null || o.candidate_jobs.status != 'Applied')
                        {
                            actions += '<a class="btn submit" title="Use Previous" href="' + prev_url + '">Use Prev</a>&nbsp;';
                            actions += '<a class="btn submit" title="Fill New" href="' + job_url + '">Fill New</a>&nbsp;';
                        } else {
                            actions += '<a class="btn submit" title="View Application" href="' + application_view_url + '">View Application</a>&nbsp;';
                        }
                        return actions;
                    },
                }
            ]
        });
    });
</script>
@stop
