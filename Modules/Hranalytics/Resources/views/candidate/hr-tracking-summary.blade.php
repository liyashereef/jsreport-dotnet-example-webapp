@extends('layouts.app')
@section('content')
<div class="table_title">
    <h4>Candidates Onboarding Status</h4>
</div>
<div class="">
    <table class="table table-bordered" id="summary-table">
        <thead>
            <tr>
                <th class="sorting"></th>
                <th class="sorting">Candidate</th>
                <th class="sorting">Original Client Name</th>
                <th class="sorting">Original Project Number</th>
                <th >Reassigned Client Name</th>
                <th >Reassigned Project Number</th>
                <th class="sorting">Proposed Wage</th>
                <!--<th class="sorting">Step Number</th>-->
                <th class="sorting">Last Step Completed</th>
                <th class="sorting">Date</th>
                <th class="sorting">Notes</th>
                <th></th>
                <th></th>
                <th class="sorting">Entered By</th>
                <th class="sorting">Status</th>
                @canany(['hr-tracking','hr-tracking-detailed-view','track_all_candidates'])
                    <th>Track</th>
                @endcan
            </tr>
        </thead>
    </table>
    @stop @section('scripts')
    <script>
        $(function () {
            var table = $('#summary-table').DataTable({
                processing: false,
                fixedHeader: false,
                serverSide: false,
                responsive: true,
                ajax: "{{ route('summary.list') }}",
                dom: 'Blfrtip',
                buttons: [{
                        extend: 'pdfHtml5',
                        //text: ' ',
                        pageSize: 'A2',
                        //className: 'btn btn-primary fa fa-file-pdf-o',
                        exportOptions: {
                            columns: [ ':visible'],
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        //text: ' ',
                        //className: 'btn btn-primary fa fa-file-excel-o',
                        exportOptions: {
                            columns: [ ':visible'],
                        }
                    },
                    {
                        extend: 'print',
                        //text: ' ',
                        pageSize: 'A2',
                        //className: 'btn btn-primary fa fa-print',
                        exportOptions: {
                            columns: [ ':visible'],
                        }
                    }
                ],
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                order: [
                    [0, "asc"]
                ],
                lengthMenu: [
                    [10, 25, 50, 100, 500, -1],
                    [10, 25, 50, 100, 500, "All"]
                ],
                createdRow: function (row, data, dataIndex) {
                    if(data.terminated)
                    {
                        $(row).addClass('archived');
                    }
                },
                columns: [
                    {
                         data: 'step_number',
                        name: 'step_number',
                        visible: false
                     },
                    {
                        data: 'candidate_name',
                        name: 'candidate_name',
                        defaultContent: '--'
                    },


                    {
                        data: 'client_name',
                        name: 'client_name',
                        defaultContent: '--'
                    },
                    {
                        data: 'project_number',
                        name: 'project_number',
                        defaultContent: '--'
                    },
                    {
                        data: 'client_name1',
                        name: 'client_name1',
                        defaultContent: "--",
                        sortable: false,
                    },
                    {
                        data: 'project_number1',
                        name: 'project_number1',
                        defaultContent: "--",
                        sortable: false,
                    },
                    {
                        data: null,
                        name: 'wage_low',
                        render: function (row) {
                            return '$' + parseFloat((row.job_reassigned_id == null || row.job_reassigned_id == 0) ? row.wage_low : row.reassigned_job_wagelow).toFixed(2)
                        }
                    },

                    {
                        data: null,
                        name: 'process_steps',
                        render: function(row){
                            if(!(row.terminated))
                            {
                                return row.step_number+'. '+row.process_steps;
                            }else{
                                return '0. Application Terminated';
                            }
                        },
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
                        data: 'first_name',
                        name: 'first_name',
                        visible:false,
                        defaultContent: '--'
                    },
                    {
                        data: 'last_name',
                        name: 'last_name',
                        visible:false,
                        defaultContent: '--'
                    },
                    {
                        data: 'full_name',
                        name: 'full_name',
                        defaultContent: '--'
                    },
                    {
                        data: null,
                        name: 'termination',
                        render: function(row){
                            return (!(row.terminated)?'Active':'Terminated');
                        },
                        defaultContent: '--'
                    },
                    @canany(['hr-tracking','hr-tracking-detailed-view','track_all_candidates'])
                    {
                        data: null,
                        sortable: false,
                        render: function (row) {
                            actions = '';
                            var tracking_process_url ='{{ route("candidate.track",array(":candidate_id",":job_id")) }}';
                            tracking_process_url = tracking_process_url.replace(':candidate_id',row.candidate_id);
                            tracking_process_url = tracking_process_url.replace(':job_id', row.job_id);
                            actions += '<a title="Track Candidate" href="' + tracking_process_url + '" class="fa fa-compress fa-lg"></a>';
                            return actions;
                        }
                    }
                    @endcan
                ]
            });

        });
    </script>
    @stop
