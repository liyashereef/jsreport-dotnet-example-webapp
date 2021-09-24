@extends('layouts.app')
<style>
.rating_div i { font-size:30px; }
.sweet-alert h2 {
  font-size: 21px !important;
}
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
                                <h2 class="color-light text-sm-center text-md-left text-center ">
                                  <span style="color: #ffae88;">Duration : </span>
                                  {{$courseDet['course_duration']}} minutes
                                </h2>

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


 <div class="content-component px-3 py-3">
     <div class="row btnrow-link">
         <div class="col-md-6">
          <input type="hidden" name="user" value="{{ Auth::user()->id }}">
             <div class="back-to-course btn-control mx-0" data-title="Back to Dashboard" id="back-to-dashboard">Back to Dashboard</div>
             <div class="back-to-course btn-control mx-0" data-title="Take Test" data-id="{{$courseDet['id']}}"  style="display:none;" id="take-test">Take Test</div>

             @if($has_previous_attempt==1)
              <div class="back-to-course btn-control mx-0" data-title="Attempt" data-id="{{$courseDet['id']}}" id="attempt-history">Attempt History</div>
              @endif
         </div>
     </div>
 </div>

    <div  class="container-fluid inner-body">
        <h1 class="title color-high">{{$courseDet['course_title']}}</h1>

        <div class="List-container">
           @foreach ($courseContents as $courseContent)

            <div class="row listline">
                <div class="col-md-12 list-inner">
                    <div class="listinner-line d-flex align-items-center">
                        @if($courseContent['content_type_id']==3)
                        <a href="{{ route('course-content.video.view','') }}/{{$courseContent['id']}}" class="color-dark">
                                <img src="{{asset('css/training/leaner-dashboard/images/video-icon.png')}}" class="linktype-ico"/> {{$courseContent['content_title']}}
                        </a>
                        @elseif($courseContent['content_type_id']==2)
                        <a href="{{ route('course-content.pdf.view','') }}/{{$courseContent['id']}}" class="color-dark">
                                    <img src="{{asset('css/training/leaner-dashboard/images/pdf-icon.png')}}" class="linktype-ico"/> {{$courseContent['content_title']}}
                        </a>
                        @elseif($courseContent['content_type_id']==1)
                        <a href="{{ route('course-content.image.view','') }}/{{$courseContent['id']}}" class="color-dark">
                                        <img src="{{asset('css/training/leaner-dashboard/images/image-icon.png')}}" class="linktype-ico"/> {{$courseContent['content_title']}}
                         </a>
                         @endif
                    </div>
                </div>
            </div>
            @endforeach

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
          var count_of_active_test = JSON.parse('{!! json_encode($count_of_active_test) !!}');
          var exam_results = JSON.parse('{!! json_encode($exam_results) !!}');
          console.log(exam_results)
          status=(exam_results!=null)?exam_results.status:null;
          is_exam_pass=(exam_results!=null)?exam_results.is_exam_pass:null;
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
if(Math.round({{$circleBar['perc']}})==100 && count_of_active_test==1  && is_exam_pass!=1){$('#take-test').show(); }
            $('#back-to-dashboard').click(function(){
                window.location.href = "{{route("learning.dashboard") }}";
            });

             $('#take-test').click(function(){
                var text='';
                var course_id= $(this).attr('data-id');
                var base_url = "{{ route('test.show-questions',':id') }}";
                var url = base_url.replace(':id', course_id);
                if(status==0){
                  text="Are you sure you want to resume test?"
                }
                else
                {
                  text="Would you like to take the test now?"
                }
                swal({
                title: text,
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            },
            function () {
                window.location.href = url;
            });

            });
      $('#attempt-history').click(function(e){
     var course_id=$(this).attr('data-id');
      var user_id= $("input[name=user]").val();
     var base_url = "{{route('user-test-results.show',[':user_id',':course_id'])}}";
     var url1 = base_url.replace(':user_id', user_id);
    var url = url1.replace(':course_id', course_id);
     $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url:url,
      type: 'GET',
      success: function (data) {
        console.log(data.data.length)
       if(data.success){
        if(data.data.length>0){
         var swal_html = '<div class="panel"> <div class="panel-body"><table align="center" class="table">';
         swal_html+= '<thead><tr><th>Score</th><th>&nbspPercentage</td><th style="text-align: center; vertical-align: middle;">&nbspSubmitted Date and Time</th></tr></thead>';
         $.each(data.data, function( index, value ) {
           swal_html+= '<tr><td style="text-align: center; vertical-align: middle;">'+value.total_exam_score+'</td><td style="text-align: center; vertical-align: middle;">&nbsp'+Math.round(value.score_percentage)+'%</td><td style="text-align: center; vertical-align: middle;">&nbsp'+formatDate(value.submitted_at)+'</td></tr>';
         });
         swal_html+= '</table></div></div>';
       }
       else
       {
        var swal_html='<p>No previous record found</p>';

       }
         swal({title:"Result History", text: swal_html,html: true });
       }

   },
   fail: function (response) {
    swal("Oops", "Something went wrong", "warning");
  },
  error: function (xhr, textStatus, thrownError) {
    associate_errors(xhr.responseJSON.errors, $form, true);
  },

 })
   })

        });
function formatDate(date) {
    var d = new Date(date);
    var options = { year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric' };
    var today  = new Date(date);
    return today.toLocaleDateString("en-US", options);
}
</script>




@stop
