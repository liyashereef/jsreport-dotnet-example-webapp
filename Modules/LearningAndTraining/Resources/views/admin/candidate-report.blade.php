@extends('layouts.app')
<style>
.table_title_this_page{
    padding-top: 23px;
    padding-bottom: 24px;
}
</style>

@section('content')

    <div class="table_title">
        <h4>Candidate Training Course Report </h4>
    </div>
    <div class="row">

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 card-padding">

            <table class="table table-bordered" id="team-table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Course</th>
                    <th>Assigned Date</th>
                    <th>Completed</th>
                    <th>Completed Date</th>
                    <th>Score (%) </th>
                    <th>No.of Attempts</th>
                </tr>
                </thead>
            </table>

        </div>
    </div>

@stop
@section('scripts')
    <link href="{{ asset('css/training/leaner-dashboard/css/dashboard-styles.css') }}" rel="stylesheet">
    <link href="{{ asset('css/training/course-list.css') }}" rel="stylesheet">
    <script>

        $(document).ready(function() {

            /***** Course  Listing - Start */

            $.fn.dataTable.ext.errMode = 'throw';

            try {

                var view_url = '{{ route("learningandtraining.dashboard.candidate-reports_api") }}';
                

                table = $('#team-table').DataTable({
                    bProcessing: false,
                    responsive: true,
                    dom: 'lfrtBip',
                    processing: true,
                    serverSide: true,
                    fixedHeader: true,
                    ajax: {
                        "url": view_url,
                        "data": function (d) {
                            d.payperiod = $("#payperiod-filter").val();
                        },
                        "error": function (xhr, textStatus, thrownError) {
                            if (xhr.status === 401) {
                                window.location = "{{ route('login') }}";
                            }
                        }
                    },
                    // order: [[0, 'desc']],
                    lengthMenu: [
                    [10, 25, 50, 100, 500, -1],
                    [10, 25, 50, 100, 500, "All"]
                     ],
                    columns: [
                        {
                            data: 'id',
                            render: function (data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            },
                            orderable: false
                        },
                        { data: 'name', name:'name'},
                        { data: 'course', name: 'course' },
                        { data: 'alloted_date', name: 'alloted_date' },
                        {
                            data:null,render:function(o){
                                if(o.completed == 1)
                                {
                                    return 'Yes'
                                }else{
                                    return ''
                                }
                                orderable: false
                            },
                            name:'completed'
                        },
                        {
                            data:null,render:function(o){
                                if(o.completed_date != null)
                                {
                                    return o.completed_date
                                }else{
                                    return ''
                                }
                                orderable: false
                            },
                            name:'completed_date'
                        },
                        { data: 'score', name: 'score' },
                        { data: 'number_attempts', name: 'number_attempts' },
                        
                    ]
                });
            } catch (e) {
                console.log(e.stack);
            }

            /***** Course  Listing - End */
        });
    </script>
@stop
