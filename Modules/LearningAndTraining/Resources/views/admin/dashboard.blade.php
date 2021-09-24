@extends('layouts.app')
<style>

</style>
<link href="{{ asset('css/training/leaner-dashboard/css/dashboard-styles.css') }}" rel="stylesheet">

@section('content')
    <div class="table_title mt-1">

        <h4 style="color: rgb(19,72,107) !important;">Training - Admin</h4>

    </div>
    <div id="dashboard_div">
         <div class="row mainlink-component toprow">
                <div class="col-md-3 mt-1">
                    <div class="bshadow card-box-lg">
                        <div class="row flex-nowrap listline-link">
                            <a href="{{ route('learningandtraining.team.list.page') }}" class="d-flex mcard-grid justify-content-center align-items-center pr-0">
                                <div class="round-icohold d-flex justify-content-center align-items-center todo-ico"></div>
                                <div class="training-title d-flex flex-column justify-content-center">
                                    <h2 class="font-bold mb-1 color-dark">Team &amp; Sub Team</h2>
                                    <h3 class="mb-0 color-dark">{{$team_count}}@if($team_count>1) Teams @else Team  @endif </h3>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mt-1">
                    <div class="bshadow card-box-lg">
                        <div class="row flex-nowrap listline-link">

                            <a href="{{ route('course-admin') }}" class="d-flex mcard-grid justify-content-center align-items-center pr-0">
                                <div class="round-icohold d-flex justify-content-center align-items-center completed-ico">

                                </div>
                                <div class="training-title d-flex flex-column justify-content-center">
                                    <h2 class="font-bold mb-1 color-dark">Courses</h2>
                                    <h3 class="mb-0 color-dark">{{$course_count}}@if($course_count>1) Courses @else Course  @endif</h3>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <!-- <div class="row">

            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 visit-log-padding">
                <a  href="{{ route('learningandtraining.team.list.page') }}" style="cursor: pointer">
                    <div class="visit-log-card">
                        <i class="fa fa-graduation-cap icon-style"> </i>
                        Team & Sub Team
                        <div class="visit-log-count-text"> {{$team_count}}</div>
                    </div>
                </a>
            </div>

                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 visit-log-padding">
                    <a  href="{{ route('course-admin') }}" style="cursor: pointer">
                        <div class="visit-log-card">
                            <i class="fa fa-users icon-style"></i>
                                Cources
                            <div class="visit-log-count-text">{{$course_count}}</div>
                        </div>
                    </a>
                </div>

        </div> -->
    </div>

    <div class="table_title">
        <h4>Course Lists </h4>
    </div>
    <div class="row">

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 card-padding">

            <table class="table table-bordered" id="team-table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Course</th>
{{--                    <th>Link</th>--}}
                    <th>Assigned People</th>
                    <th>Completed People</th>
                    <th>Due Date</th>
                </tr>
                </thead>
            </table>

        </div>
    </div>

@stop
@section('scripts')

    <script>

        $(document).ready(function() {

            /***** Course  Listing - Start */

            $.fn.dataTable.ext.errMode = 'throw';
            try {
                table = $('#team-table').DataTable({
                    bProcessing: false,
                    responsive: true,
                    dom: 'lfrtip',
                    processing: true,
                    serverSide: true,
                    fixedHeader: true,
                    ajax: {
                        "url": '{{ route('learningandtraining.dashboard.courses') }}',
                        "data": function (d) {
                            d.payperiod = $("#payperiod-filter").val();
                        },
                        "error": function (xhr, textStatus, thrownError) {
                            if (xhr.status === 401) {
                                window.location = "{{ route('login') }}";
                            }
                        }
                    },
                    order: [[0, 'desc']],
                    lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                    columns: [
                        {
                            data: 'id',
                            render: function (data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            },
                            orderable: false
                        },
                        // {data: 'course_title', name: 'course_title'},
                        // {

                            {{--data: 'unique_key',--}}
                            {{--render: function (o) {--}}
                            {{--    actions = '';--}}
                            {{--    var view_url = '{{ route("job.view", ":id") }}';--}}
                            {{--    view_url = view_url.replace(':id', o.id);--}}
                            {{--    actions += '<a title="View" href="' + view_url + '">' + o.unique_key +--}}
                            {{--        '</a>';--}}
                            {{--    return actions;--}}
                            {{--},--}}
                        {{--    data:null,render:function(o){--}}
                        {{--        actions = ''; alert();--}}
                        {{--        if(o.course_title)--}}
                        {{--        {--}}
                        {{--            var view_url = '{{ route("job.view", ":id") }}';--}}
                        {{--            view_url = view_url.replace(':id', o.id);--}}
                        {{--            actions += '<a title="View" href="' + view_url + '">' + o.course_title + '</a>';--}}
                        {{--        }--}}
                        {{--        // orderable: false--}}
                        {{--    },--}}
                        {{--    name:'course_allocation_count'--}}
                        {{--},--}}
                        {
                            //data: 'employee.user.first_name',
                            data:null,render:function(o){
                                name_link = '';
                                var view_url = '{{ route("learningandtraining.dashboard.course-details",":id") }}';
                                    view_url = view_url.replace(':id', o.id);
                                name_link = '<a title="View" href="' + view_url + '">' + o.course_title + '</a>';
                                return name_link;

                            },
                            sortable:true,
                            name:'course_title'
                        },  {
                            //data: 'employee.user.first_name',
                            data:null,render:function(o){
                                if(o.course_allocation_count)
                                {
                                    return o.course_allocation_count.data_count
                                }else{
                                    return ''
                                }
                                orderable: false
                            },
                            sortable:false,
                            name:'course_allocation_count'
                        },{
                            data:null,render:function(o){
                                if(o.course_allocation_completed_count) {
                                    return o.course_allocation_completed_count.data_count
                                }else{
                                    return ''
                                }
                            },
                            sortable:false,
                            name:'course_allocation_completed_count'
                        },
                        {data: 'course_due_date', name: 'course_due_date',sortable:true},

                    ]
                });
            } catch (e) {
                console.log(e.stack);
            }

            /***** Course  Listing - End */
        });
            </script>
@stop
