@extends('layouts.app')
@section('content')


@section('content')





    <div style="padding-right: 10px;">

       
        <div class="modal-dialog" style="margin:20px; ">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title table_title" id="myModalLabel">Course Detail Page</h4>
                </div>

                <input name="team_id" type="hidden" value="@if(isset($team_details->id)){{$team_details->id}} @endif">
                <div class="modal-body">
                    <div class="form-group" id="severity">
                        <label for="severity" class="col-sm-3 control-label">Title</label>

                    </div>

                   


                </div>

                <video id='my-video' class='video-js' controls preload='auto' width='640' height='264'
  poster='http://vjs.zencdn.net/v/oceans.png' data-setup='{}'>
    <source src='https://cgl360training.s3.ap-south-1.amazonaws.com/video/course1-1559571810.mp4' type='video/mp4'>
    <p class='vjs-no-js'>
      To view this video please enable JavaScript, and consider upgrading to a web browser that
      <a href='https://videojs.com/html5-video-support/' target='_blank'>supports HTML5 video</a>
    </p>
  </video>


            </div>
        </div>

       
    </div>



@stop
@section('scripts')

   <script src='https://vjs.zencdn.net/7.5.4/video.js'></script>

<script>
 videojs("my-video").ready(function() {
    
    var myPlayer = this;

    //Set initial time to 0
    var currentTime = 0;
    
    //This example allows users to seek backwards but not forwards.
    //To disable all seeking replace the if statements from the next
    //two functions with myPlayer.currentTime(currentTime);

    myPlayer.on("seeking", function(event) {
      if (currentTime < myPlayer.currentTime()) {
        myPlayer.currentTime(currentTime);
      }
    });
    myPlayer.on("ended", function(event) {
      alert("dfgdfgd");
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

  });


</script>



@stop