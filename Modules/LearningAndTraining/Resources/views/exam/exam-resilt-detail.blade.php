@extends('layouts.app')
<style>
.rating_div i { font-size:30px; }
</style>


@section('content')
 <div class="wide-block jumbotron">
               <div class="container-fluid mb-0">
                    <div class="row">
                            <div class="col-md-3 col-lg-3 col-xl-2">
                                @if($courseDet['course_image'] =="")
                                    <img src="{{ asset('images/courses_noimage.png') }}" alt="" class="w-100 banner-intro"/>
                                @else
                                    <img src="{{ asset('LearningAndTraining/course_images') }}/{{ $courseDet['course_image'] }}" alt="" class="w-100 banner-intro"/>
                                @endif
                            </div>
                            <div class="col-md-7 jum-titleblock col-lg-7 col-xl-8">
                                <h1 class="color-high-md mt-4 text-sm-center text-md-left text-center">{{$courseDet['course_title']}}</h1>
                                <h2 class="color-light text-sm-center text-md-left text-center">{{$courseDet['course_description']}}</h2>
                                <!-- <div class="rating_div"></div> -->
                                <div class="star-rating">
                                        @for ($i = 1; $i <= $course_rating; $i++)
                                        <span><img src="{{asset('css/training/leaner-dashboard/images/Rating-star-icon.png')}}" alt=""></span>
                                        @endfor

                                    </div>

                            </div>
                            <div class="second circle progress-circle col-md-2 col-lg-2 col-xl-2 d-flex align-items-center justify-content-center">
                                <div class="progress-component">
                                    <strong></strong>
                                </div>


                            </div>

                        </div>
               </div>

            </div>
            <div class="add-new" data-title="Close Module">
    <span class="add-new-label">Close Module</span>
</div>
<div class="result-message">
    <div  class="inner-body text" style="display: inline-block;">
        <!-- @if($result->is_exam_pass==1)
            <strong>Congratulations! You've passed the course with a final score of {{round($result->score_percentage)}}%. Your training records will be updated accordingly.</strong>
        @else
            <strong><div>Score - {{round($result->score_percentage)}}% </div></br><div>Passing Score - {{round($result->course_pass_percentage)}}% <div></br><div>Unfortunately, You have failed the test. Would you like to re-take the test now?</div></strong>
        <div class='text-center margin-bottom-5' style="padding-top: 10px">
            {{ Form::submit('Yes', array('class'=>'button btn submit yes-option','id'=>''))}}
            {{ Form::button('No', array('class'=>'button btn submit no-option','id'=>'mdl_save_change'))}}
        </div>
        @endif -->
        <div class="card text-center"  style="width: 50rem;">
            <div class="card-body">
                @if($result->is_exam_pass==1)
                    <i class="fas fa-check-circle fa-10x" style="padding: 2rem !important;"></i>
                    <h3 class="card-title">Congratulations, you passed!</h3>
                    <div class="row">
                        <div class="col-1"></div>
                        <div class="col">
                            <h4 style="float: right; margin-right: -20px !important;">Your Score:</h4>
                        </div>
                        <div class="col">
                            <h4 style="float: left; margin-left: -5px !important;">{{round($result->score_percentage)}}%</h4>
                        </div>
                    </div>
                    <div class="row" style="padding-bottom: 2rem !important;">
                    <div class="col-1"></div>
                        <div class="col">
                            <h4 style="float: right; margin-right: -20px !important;">Passing Score:</h4>
                        </div>
                        <div class="col">
                            <h4 style="float: left; margin-left: -5px !important;">{{round($result->course_pass_percentage)}}%</h4>
                        </div>
                    </div>
                @else
                    <i class="far fa-times-circle fa-6x" style="padding: 17px !important;"></i>
                    <h3 class="card-title">Sorry, you didn't make it!</h3>
                    <div class="row" style="padding-top: 5px;">
                        <div class="col-1"></div>
                        <div class="col">
                            <h4 style="float: right; margin-right: -20px !important;">Your Score:</h4>
                        </div>
                        <div class="col">
                            <h4 style="float: left; margin-left: -5px !important;">{{round($result->score_percentage)}}%</h4>
                        </div>
                    </div>
                    <div class="row" style="padding-bottom: 5px;">
                        <div class="col-1"></div>
                        <div class="col">
                            <h4 style="float: right; margin-right: -20px !important;">Passing Score:</h4>
                        </div>
                        <div class="col">
                            <h4 style="float: left; margin-left: -5px !important;">{{round($result->course_pass_percentage)}}%</h4>
                        </div>
                    </div>
                    <h3 class="card-text">Would you like to re-take the test now?</h3>
                    <div class='text-center margin-bottom-5' style="padding-top: 10px">
                        {{ Form::submit('Yes', array('class'=>'button btn submit option yes-option','id'=>''))}}
                        {{ Form::button('No', array('class'=>'button btn submit option no-option','id'=>'mdl_save_change'))}}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@stop
@section('scripts')
<script type="text/javascript" src="{{ asset('js/stars.js') }}"></script>
<link href="{{ asset('css/training/leaner-dashboard/css/dashboard-styles.css') }}" rel="stylesheet">
<link href="{{ asset('css/training/course-list.css') }}" rel="stylesheet">
<script src="https://rawgit.com/kottenator/jquery-circle-progress/1.2.2/dist/circle-progress.js"></script>
 <script>

        $(document).ready(function () {

             $(".rating_div").stars({ text: ["Bad", "Not so bad", "hmmm", "Good", "Perfect"] ,  value:0,
             click: function(index) {
                   var course_id="{{$courseDet['id']}}";
                   var ratingUrl = "{{ route('course-rating') }}";
                   $.ajax({
                         headers: {
                            'X-CSRF-TOKEN':'{{ csrf_token() }}'
                        },
                        url: ratingUrl,
                        type: 'POST',
                        data:  {'course_id':course_id, 'rating':index},
                        success: function (data) {
                        }
                    });;
              }
            });
             $('.progress-component').circleProgress({
                value: {{$circleBar['value']}}
            }).on('circle-animation-progress', function(event, progress) {
            $(this).find('strong').html(Math.round({{$circleBar['perc']}} * progress) + '<i>%</i>');
             });

            $('.add-new,.no-option').click(function ()
            {
                window.location.href = "{{ route('learning.dashboard') }}";
            })

            $('.yes-option').click(function ()
            {
                var course_id="{{$courseDet['id']}}";
                var base_url = "{{ route('course-learner.view',':id') }}";
                var url = base_url.replace(':id', course_id);
                window.location.href = url;

            })

        });


</script>
<style type="text/css">
   .result-message {

        text-align: center;
        padding:100px;
      }

    .fa-times-circle {
        color: red !important;
    }
    .fa-check-circle {
        color: green !important;
    }
    .card {
        filter: drop-shadow(0 0 0.75rem grey);
    }
    .option {
        font-size: 17px;
        font-weight: 500;
        border-radius: 5px;
        padding: 10px 32px;
        cursor: pointer;
    }
</style>



@stop
