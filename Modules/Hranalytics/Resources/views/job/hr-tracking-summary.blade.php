@extends('layouts.app')
@section('content')
<div class="table_title">
    <h4>Job Ticket Status
    <?php
     $selected_customer_ids = (new \App\Services\HelperService())->getCustomerIds();
     if(!empty($selected_customer_ids)){
         echo '<button type="button" class="dashboard-filter-customer-reset btn btn-primary float-right"> Reset Filter</button>';
     }
    ?>
    </h4>

</div>
<div class="">
    <table class="table table-bordered" id="jobs-table">
        <thead>
            <tr>
                <th class="sorting" width="5%">Job Id</th>
                <th class="sorting" width="10%">Requestor</th>
                <th class="sorting" width="10%">HR Assigned</th>
                <th class="sorting" width="2%">Project Number</th>
                <th class="sorting" width="15%">Client</th>
                <th class="sorting" width="15%">Position Requested</th>
                <th class="sorting" width="1%">Posts</th>
                <th class="sorting" width="5%">Type</th>
                <th class="sorting" width="10%">Date Entered</th>
                <th class="sorting" width="10%">Date Required</th>
                <th class="sorting" width="5%">Notice</th>
                <th class="sorting" width="10%">Last Updated</th>
                <th class="sorting" width="2%">Wage (Low)</th>
                <th class="sorting" width="2%">Wage (High)</th>
                <th class="sorting" width="10%">Wage</th>
                <th class="sorting" width="10%">Status</th>
                <th title="HR Tracking" width="1%"></th>
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
            responsive: false,
            ajax: "{{ route('job.hr-tracking-summary.list') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [
                [11, "desc"]
            ],
            dom: 'Blfrtip',
            buttons: [{
                    extend: 'pdfHtml5',
                    //text: ' ',
                    pageSize: 'A2',
                    //className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        @canany(['hr-tracking','hr-tracking-detailed-view'])
                        columns: 'th:not(:last-child)',
                        @endcan
                    }
                },
                {
                    extend: 'excelHtml5',
                    //text: ' ',
                    //className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        @canany(['hr-tracking','hr-tracking-detailed-view'])
                        columns: 'th:not(:last-child)',
                        @endcan
                    }
                },
                {
                    extend: 'print',
                    //text: ' ',
                    pageSize: 'A2',
                    //className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        @canany(['hr-tracking','hr-tracking-detailed-view'])
                        columns: 'th:not(:last-child)',
                        @endcan
                        stripHtml: false,
                    }
                }
            ],
            "rowCallback": function (row, data, index) {
                var bg_color = 'red';
                    var color = 'black';
                    if (data.process_id >= 0 && data.process_id <= 5) {
                        bg_color = 'red';
                        color = 'white';
                    } else if (data.process_id > 5 && data.process_id <= 10) {
                        bg_color = 'yellow';
                    } else if (data.process_id > 10 && data.process_id <= 14) {
                        bg_color = 'green';
                        color = 'white';
                    }
                    $(row).find('td:eq(13)').css('background-color', bg_color).css('color',color);

                    // notice period color
                    if (data.colorCode == 'red' || data.colorCode == 'green') {
                        fontColor = 'white';
                    } else if (data.colorCode == 'yellow') {
                        fontColor = 'black';
                    }

                    $(row).find('td:eq(10)').css('background-color', data.colorCode).css('color', fontColor);
            },
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [{
                    data: null,
                    name: 'unique_key',
                    render: function (o) {
                        actions = '';
                        var view_url = '{{ route("job.view", ":id") }}';
                        view_url = view_url.replace(':id', o.id);
                        actions += '<a title="View" href="' + view_url + '">' + o.unique_key +'</a>';
                        return actions;
                    }
                },
                {
                    data: 'requester',
                    name: 'requester',
                    defaultContent: "--",
                },
                {
                    data: 'assignee',
                    name: 'assignee',
                    defaultContent: "--",
                },
                {
                    data: 'customer',
                    name: 'customer',
                    defaultContent: "--",
                },
                {
                    data: 'client_name',
                    name: 'client_name'
                },
                {
                    data: 'position',
                    name: 'position'
                },
                {
                    data: 'no_of_vaccancies',
                    name: 'no_of_vaccancies'
                },
                {
                    data: 'assignment_type',
                    name: 'assignment_type'
                },
                {
                    data: 'date_entered',
                    name: 'date_entered'
                },
                {
                    data: 'requisition_date',
                    name: 'requisition_date'
                },
                {
                    data: 'notice',
                    name: 'notice'
                },
                {
                    data: 'updated_at',
                    name: 'updated_at',
                    defaultContent: "--"
                },
                {
                    data: 'wage_low',
                    name: 'wage_low',
                    visible:false,
                    render: function (wage_low) {
                        return '$' + parseFloat(wage_low).toFixed(2)
                    }
                }, {
                    data: 'wage_high',
                    name: 'wage_high',
                    visible:false,
                    render: function (wage_high) {
                        return '$' + parseFloat(wage_high).toFixed(2)
                    }
                },
                {
                    data:null,
                    name: 'wage_high',
                    render: function (row) {
                        wage = 'High: $' + Number(row.wage_high).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                        wage += '\r\n<br/>Low: $' + Number(row.wage_low).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                        return wage;
                    }
                },
                {
                    data: null,
                    name: 'process_id',
                    defaultContent: "--",
                    render: function (row) {
                        return row.process_id + ' - ' + row.process_name;
                    }
                },
                @canany(['hr-tracking','hr-tracking-detailed-view'])
                {
                    data: null,
                    sortable: false,
                    render: function (row) {
                        actions = '';
                        if (row.active && ["approved", "completed"].includes(row.status)) {
                            var hr_tracking_url = '{{ route("job.hr-tracking",":id") }}'
                            hr_tracking_url = hr_tracking_url.replace(':id', row.id);
                            actions += '<a class="fa fa-compress fa-lg link-ico" title="HR Tracking" href="' + hr_tracking_url +'"> </a>';
                        }
                        return actions;
                    }
                }
                @endcan

            ]
        });

    });
</script>
<script src="{{asset('js/auto-refresh.js')}}"></script>
@stop
