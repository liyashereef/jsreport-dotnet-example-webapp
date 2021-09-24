@extends('layouts.app')
@section('content')
<div class="table_title">
    <h4>Candidates Tracking</h4>
</div>
<div class="">
    <table class="table table-bordered" id="summary-table">
        <thead>
            <tr>
               <th>First Name</th>
                 <th>Last Name</th>
                <th>City</th>
                <th>Postal Code</th>
                <th>Email Address</th>
                <th>Phone</th>
                <th>Last Step Completed</th>
                <th>First Login</th>
                <th>Last Login</th>
                 <th>Completed Date</th>

               {{--  @canany(['hr-tracking','hr-tracking-detailed-view','track_all_candidates']) --}}
                    <th>Track</th>
               {{--  @endcan --}}
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
                ajax: "{{ route('recruitment.candidate-tracking.list') }}",
                dom: 'Blfrtip',
                buttons: [{
                        extend: 'pdfHtml5',
                        //text: ' ',
                        pageSize: 'A2',
                        //className: 'btn btn-primary fa fa-file-pdf-o',
                        exportOptions: {
                            //columns: [ ':visible'],
                            columns: 'th:not(:last-child)',
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        //text: ' ',
                        //className: 'btn btn-primary fa fa-file-excel-o',
                        exportOptions: {
                            //columns: [ ':visible'],
                            columns: 'th:not(:last-child)',
                        }
                    },
                    {
                        extend: 'print',
                        //text: ' ',
                        pageSize: 'A2',
                        //className: 'btn btn-primary fa fa-print',
                        exportOptions: {
                            //columns: [ ':visible'],
                            columns: 'th:not(:last-child)',
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
                    // {
                    //      data: 'step_number',
                    //     name: 'step_number',
                    //     visible: false
                    //  },
                    {
                        data: 'first_name',
                        name: 'first_name',
                        defaultContent: '--'
                    },
                     {
                        data: 'last_name',
                        name: 'last_name',
                        defaultContent: '--'
                    },


                    {
                        data: 'city',
                        name: 'city',
                        defaultContent: '--'
                    },
                    {
                        data: 'postal_code',
                        name: 'postal_code',
                        defaultContent: '--'
                    },
                    {
                        data: 'email',
                        name: 'email',
                        defaultContent: "--",

                    },
                    {
                        data: 'phone',
                        name: 'phone',
                        defaultContent: "--",

                    },

                     {
                        data: 'last_track',
                        name: 'last_track',
                        defaultContent: "--",

                    },
                    {
                        data: 'user_access_tracking_completed_date',
                        name: 'user_access_tracking_completed_date',
                        defaultContent: "--",
                    },

                    {
                        data: 'last_login',
                        name: 'last_login',
                         defaultContent: "--",
        
                    },

                    {
                        data: 'completed_date',
                        name: 'completed_date',
                        defaultContent: "--",

                    },

                    {{-- @canany(['hr-tracking','hr-tracking-detailed-view','track_all_candidates']) --}}
                    {
                        data: null,
                        sortable: false,
                        render: function (row) {
                            actions = '';
                            var tracking_process_url ='{{ route("recruitment.candidate.track",":candidate_id") }}';
                            tracking_process_url = tracking_process_url.replace(':candidate_id',row.id);
                            actions += '<a title="Track Candidate" href="' + tracking_process_url + '" class="fa fa-compress fa-lg"></a>';
                            return actions;
                        }
                    }
                   {{--  @endcan--}}
                ]
            });

        });
    </script>
    @stop
