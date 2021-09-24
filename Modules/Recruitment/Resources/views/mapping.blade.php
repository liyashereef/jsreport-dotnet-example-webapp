@extends('layouts.app')
@section('content')
<div class="table_title">
    <h4>Candidate Geomapping </h4>
</div>
<div class="table-responsive">
    <table class="table table-bordered" id="jobs-table">
        <thead>
            <tr>
                <th class="sorting" width="15%">Job Id</th>
                <th class="sorting" width="10%">Area Manager</th>
                <th class="sorting" width="5%">Project Number</th>
                <th class="sorting" width="15%">Client</th>
                <th class="sorting" width="10%">Position Requested</th>
                <th class="sorting" width="1%">Posts</th>
                <th class="sorting" width="10%">Rationale</th>
                <th class="sorting" width="10%">Type</th>
                <th class="sorting" width="5%">Date Required</th>
                {{-- <th class="sorting" width="5%">Wage(Low)</th>
                <th class="sorting" width="5%">Wage(High)</th> --}}
                <th class="sorting" width="10%">Wage</th>
                <th width="1%">Map</th>
            </tr>
        </thead>
    </table>
</div>
@stop @section('scripts')
<script>
    $(function () {
        var table = $('#jobs-table').DataTable({
            fixedHeader: true,
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('recruitment-job.list','approved') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [
                [8, "desc"]
            ],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            createdRow: function (row, data, dataIndex) {
                if (data.active) {
                    $(row).addClass(data.status);
                } else {
                    $(row).addClass('archived');
                }
            },
            columns: [{
                    data: null,
                    name: 'unique_key',
                    render: function (o) {
                        actions = '';
                        var view_url = '{{ route("recruitment-job.view", ":id") }}';
                        view_url = view_url.replace(':id', o.id);
                        actions += '<a title="View" href="' + view_url + '">' + o.unique_key +
                            '</a>';
                        return actions;
                    }
                },
                {
                    data: 'area_manager',
                    name: 'area_manager'
                },
                {
                    data: 'customer.project_number',
                    name: 'customer.project_number'
                },
                {
                    data: 'customer.client_name',
                    name: 'customer.client_name'
                },
                {
                    data: 'position_beeing_hired.position',
                    name: 'position_beeing_hired.position'
                },
                {
                    data: 'no_of_vaccancies',
                    name: 'no_of_vaccancies'
                },
                {
                    data: 'reason.reason',
                    name: 'reason.reason'
                },
                {
                    data: 'assignment_type.type',
                    name: 'assignment_type.type'
                },
                {
                    data: 'requisition_date',
                    name: 'requisition_date'
                },
                // {
                //     data: 'wage_low',
                //     name: 'wage_low',
                //     visible: false,
                //     render: function (wage_low) {
                //         return '$' + parseFloat(wage_low).toFixed(2)
                //     }
                // }, {
                //     data: 'wage_high',
                //     name: 'wage_high',
                //     visible: false,
                //     render: function (wage_high) {
                //         return '$' + parseFloat(wage_high).toFixed(2)
                //     }
                // },
                // {
                //     data:null,
                //     name: 'wage_high',
                //     render: function (row) {
                //         wage = 'High: $' + Number(row.wage_high).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                //         wage += '<br/>Low: $' + Number(row.wage_low).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                //         return wage;
                //     }
                // },
                {
                    data: null,
                    name: 'wage',
                    render: function (row) {
                        wage = '$' + Number(row.wage).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                        return wage;
                    }
                },
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        actions = '';
                        var map_url = '{{ route("recruitment.candidate.plot-in-map-with-customer", ":id") }}';
                        map_url = map_url.replace(':id', o.id);
                        actions += '<a title="Map" href="' + map_url +
                            '" class="fa fa-users"></a>';
                        return actions;
                    },
                }
            ]
        });
    });
</script>
@stop
