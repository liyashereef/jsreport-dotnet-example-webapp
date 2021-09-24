@extends('layouts.app')
<style>
.rating_div i { font-size:40px; }
body .back-to-course, body .module-next{
        width: 100% !important;

    }
    video {
      object-fit:inherit;
    }

</style>

@section('content')



<div class="container_fluid">
  <div class="row">
    <div class="col-md-4 p-25">
        <div class="container_fluid mt-20" style="padding: 50px">
          <div class="row logorow">
            <div class="col-md-12" style="text-align: center">
              <img class="logo" src="{{asset('images/CGL-LOGO-600px-152px.png') }}">
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 coursedescription" >
              {{$courseContentsDet->training_courses->course_description}}
            </div>
          </div>
        </div>
    </div>
    <div class="col-md-7">
      <div class="container_fluid mt-15">
        <div class="row">
          <div class="col-md-12 table_title">
            <h4>{{$courseContentsDet->training_courses->course_title}}</h4>
          </div>
        </div>
        <div class="row" >
            <div class="col-md-12 " style="
            padding: 0px !important;height: 590px;">

                <video id='my-video' class='video-js ' controls preload='auto'
                      data-setup='{}' style="width:100%;height: 100%;background-color: #fff">

                        <source src="{{$videoLink}}" type='video/mp4'>
                        <p class='vjs-no-js'>
                          To view this video please enable JavaScript, and consider upgrading to a web browser thats
                          <a href='https://videojs.com/html5-video-support/' target='_blank'>supports HTML5 video</a>
                        </p>
                  </video>
            </div>



        </div>
        <div class="row">
          <div class="col-md-6" style="padding-left: 0px !important">
            <div class="back-to-course btn-control form-control  mx-0" data-title="back to course">Back to Course list</div>
          </div>
          <div class="col-md-6"  style="padding-left: 0px !important;padding-right: 0px">
            <div class="module-next btn-control form-control  mx-0">Next Module</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>




@stop
@section('scripts')
 <link href="{{ asset('css/training/course-list.css') }}" rel="stylesheet">
 <script type="text/javascript" src="{{ asset('js/stars.js') }}"></script>
<script src='https://vjs.zencdn.net/7.5.4/video.js'></script>

<script>
 videojs("my-video").ready(function() {

    var myPlayer = this;

    //Set initial time to 0
    var currentTime = 0;

    //This example allows users to seek backwards but not forwards.
    //To disable all seeking replace the if statements from the next
    //two functions with myPlayer.currentTime(currentTime);

    if({{$courseContentsDet->fast_forward}}) {
       myPlayer.on("seeking", function(event) {
          if (currentTime < myPlayer.currentTime()) {
            myPlayer.currentTime(currentTime);
          }
       });
       myPlayer.on("seeked", function(event) {
      if (currentTime < myPlayer.currentTime()) {
        myPlayer.currentTime(currentTime);
      }
    });

    setInterval(function() {
      if (!myPlayer.paused()) {
        currentTime = myPlayer.currentTime();
      }
    }, 1000);
    } else {
      //alert("abcddd")
    }

    myPlayer.on("ended", function(event) {
      var content_id={{$courseContentsDet->id}};
      var url = "{{ route('content-update') }}";
                    $.ajax({
                     headers: {
                        'X-CSRF-TOKEN':'{{ csrf_token() }}'
                    },
                    url: url,
                    type: 'POST',
                    data:  {'content_id': content_id, 'completed':1},
                    success: function (data) {
                        if (data.completed=="true") {
                            swal({ html:true, title:'<font size="3" class="title-lin">Congratulations! Successfully completed this course.</font>', text:'<h2 class="rating-title">Please rate the course</h2><div class="rating_div"></div>'});
                                        $(".rating_div").stars({ text: ["Bad", "Not so bad", "hmmm", "Good", "Perfect"] ,  value:0,
                                 click: function(index) {

                                        var course_id="{{$courseContentsDet->course_id}}";
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
                                        });
                                  }
                                });
                        } else if (data.success == true) {
                            swal("Completed", "Congratulations. You have successfully completed this module.", "success");
                            //table.ajax.reload();
                        } else {
                            //alert(data);
                            //swal("Alert", "Employee not allocated to this team", "warning");
                        }
                    },
                    error: function (xhr, textStatus, thrownError) {
                       //alert(xhr.status);
                       //alert(thrownError);
                       console.log(xhr.status);
                       console.log(thrownError);
                       swal("Oops", "Something went wrong", "warning");
                    },
                });
    });



  });

  $('.back-to-course').click(function(){
      window.location.href = "{{route("course-learner.view",'') }}/"+{{$courseContentsDet->course_id}};
  });
  $('.module-next').click(function(){
      if ({{$nextContentId}} == 0) {
         window.location.href = "{{route("course-learner.view",'') }}/"+{{$courseContentsDet->course_id}};
      } else if ({{$nextContentType}} == 1) {
         window.location.href = "{{route("course-content.image.view",'') }}/"+{{$nextContentId}};
      } else if ({{$nextContentType}} == 2) {
         window.location.href = "{{route("course-content.pdf.view",'') }}/"+{{$nextContentId}};
      } else if ({{$nextContentType}} == 3) {
         window.location.href = "{{route("course-content.video.view",'') }}/"+{{$nextContentId}};
      } else {
         window.location.href = "{{route("course-learner.view",'') }}/"+{{$courseContentsDet->course_id}};
      }
  });

  $(document).ready(function () {
    // $("#sidebar").css("height","600px")
    var videoElement = document.getElementById("my-video");

    videoElement.addEventListener('loadedmetadata', function(e){
        // Print the native height of the video
        console.log(videoElement.videoHeight);
        debugger

        // Print the native width of the video
        console.log(videoElement.videoWidth);
    });

  });



</script>



@stop
