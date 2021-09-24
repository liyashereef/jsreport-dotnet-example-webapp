@extends('layouts.app')
<style>


    .card-title{
        font-size: 16px;
        font-weight: 600;
        line-height: 18px;
        margin-bottom: 0;
        color: #4d4d4d;
        margin: 0 0 12.5px;
    }
    .course-card{
        width: 15.5rem; margin: 5px; float: left
    }
    .progress {
        height: 0.3rem !important;
        line-height: 1.4;
    }
    .progress-bar{
        background-color:#0ccf6b !important;
    }
    a{
        cursor: pointer;
    }
    .col-container {
    display: table; /* Make the container element behave like a table */
    width: 100%; /* Set full-width to expand the whole page */
    }

    .col {
        display: table-cell; /* Make elements inside the container behave like table cells */
    }
    .readmore a{
        color: rgb(51,63,80);
    }

    .readmore a:hover{
        color: #F24224;
    }

    .readmore i{
        display: inline-block;
    }
    /*.widget-color-change{*/
    /*    background-color:#333f50 !important;*/
    /*}*/

</style>
<link href="{{ asset('css/training/leaner-dashboard/css/dashboard-styles.css') }}" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Montserrat:100,200,300" rel="stylesheet">

@section('content')


    <!-- Page Content  -->
    @include('learningandtraining::learner.partials.dashboard-content')


    <?php /* ?>
   {{-- <div class="table_title">
        <div class="row">
            <div class="col-xs-6 col-sm-6">
                <h4>Training Module</h4>
            </div>
            <div class="col-xs-6 col-sm-6">
{{--                <span class="row pull-right"> Search Course &nbsp; &nbsp;--}}
                <input type="text" name="course_search" id="course_search" placeholder="Search : Enter course name" class="form-control pull-right" style="width: 40%;">
{{--                </span>--}}
            </div>
        </div>


    </div>


    <div id="dashboard_div">
        <div class="row" id="CourseTypeCount">

            <div class="col-xs-2 col-sm-2">
                <a class="CourseTypeCount_a" data-id="1" href="#" >
                    <div class="visit-log-card widget-color-change">
                        <i class="fa fa-graduation-cap icon-style"> </i>
                        To – Do
                        <div class="visit-log-count-text"> {{$todo_count}}</div>
                    </div>
                </a>
            </div>

            <div class="col-xs-2 col-sm-2 ">
                <a class="CourseTypeCount_a" data-id="2" href="#" >
                    <div class="visit-log-card">
                        <i class="fa fa-graduation-cap icon-style"> </i>
                        Completed
                        <div class="visit-log-count-text">{{$completed_count}}</div>
                    </div>
                </a>
            </div>

            <div class="col-xs-2 col-sm-2">
                <a class="CourseTypeCount_a" data-id="3" href="#" >
                    <div class="visit-log-card">
                        <i class="fa fa-graduation-cap icon-style"> </i>
                        Overdue
                        <div class="visit-log-count-text"> {{$over_due_count}}</div>
                    </div>
                </a>
            </div>

            <div class="col-xs-2 col-sm-2 ">
                <a class="CourseTypeCount_a" data-id="4" href="#" >
                    <div class="visit-log-card">
                        <i class="fa fa-graduation-cap icon-style"> </i>
                        Recommended
                        <div class="visit-log-count-text">{{$recommended_count}}</div>
                    </div>
                </a>
            </div>

            <div class="col-xs-2 col-sm-2 ">
                <a class="CourseTypeCount_a" data-id="5" href="#" >
                    <div class="visit-log-card">
                        <i class="fa fa-graduation-cap icon-style"> </i>
                        Course Library
                        <div class="visit-log-count-text">{{$total_course_count}}</div>
                    </div>
                </a>
            </div>

        </div>
    </div>

    <br>

    <div class="table_title">
{{--        <h4 id="course_list_type"></h4>--}}
    </div>

    <div class="row">

        <div class="col-xs-12 col-sm-12">

             <div class="col-xs-9 col-sm-9" id="course_list" style="float: left">

{{--                 <div class="card" style="width: 15.5rem; float: left;margin: 5px;" >--}}
{{--                     <img class="card-img-top" src="https://d16smq18f8amlc.cloudfront.net/102383/CourseImages/2528498-12256646-a5a9-4525-9308-ecf4aa1ac922.png?Expires=1561184397&Signature=bdktiYRL4z1WG4d5aXqbOQEOorXMfY0359siw1TkE5i~C9QRhx1Hhi-nFeVzkbiR8XvUZQpElkcpOTAL46o8eZrqYXqHGXxicQFz9Utk6ajHnJfuk~jEBI87Ki7mw7~MmasBr9CaUEdPNp3sy6uDMvPYheIKMpd-VtZK9RQaY8Y_&Key-Pair-Id=APKAIQKC33KEMABEYE6A" alt="Card image cap">--}}
{{--                     <div class="card-body">--}}
{{--                         <p class="card-title">Competitive Advantage in Organizational Strategy (US)</p>--}}
{{--                     </div>--}}
{{--                     <div class="card-body">--}}
{{--                         <div class="progress" style="width: 80%; float: left">--}}
{{--                             <div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:70%">--}}

{{--                             </div>--}}
{{--                         </div>--}}
{{--                         <small style=" color: black;" class="">70%</small>--}}
{{--                     </div>--}}
{{--                 </div>--}}

             </div>


            <div class="col-xs-3 col-sm-3" style="max-width: none">

                <div class="card">
                    <h5 class="card-header">Recent Achievements</h5>
                    @if(sizeof($recent_achievements) == 0)
                    <div class="card-body">

                        <div class="row">
                            <div class="col-xs-4 col-sm-4">


                            </div>
                            <div class="col-xs-8 col-sm-8">
                                <h6 class="card-title"></h6>
                                <p class="card-text" style="font-size: 14px;"></p>
                            </div>
                        </div>

                    </div>
                    @endif
                    @foreach($recent_achievements as $recent)

                        <div class="card-body">

                            <div class="row">
                                <div class="col-xs-4 col-sm-4">
                                    @if($recent->course->course_image !='')
                                        <img class="card-img-top" src="{{asset('LearningAndTraining/course_images').'/'.$recent->course->course_image}}" alt="Course Image" >
                                    @else
                                        <img class="card-img-top" src="{{asset('images/courses_noimage.png')}}" alt="Course Image" >
                                    @endif

                                </div>
                                <div class="col-xs-8 col-sm-8">
                                    <h6 class="card-title">{{$recent->course->course_title}}</h6>
                                    <p class="card-text" style="font-size: 14px;">Completed On {{$recent->completed_date}}</p>
                                </div>
                            </div>

                        </div>

                    @endforeach
                </div>
            </div>

        </div>
    </div> <?php */ ?>

@stop
@section('scripts')
@include('learningandtraining::learner.partials.dashboard-scripts')
@stop
