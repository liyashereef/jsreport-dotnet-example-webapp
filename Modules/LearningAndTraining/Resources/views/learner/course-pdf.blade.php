@extends('layouts.app')
<style>
.rating_div i { font-size:40px; }
body .back-to-course, body .module-next{
        width: 100% !important;

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
          <div class="row">
             <div class="col-md-12" style="padding-left: 0px;padding-right: 0px">
                <iframe src="https://s3.{{config('filesystems.disks.s3.region')}}.amazonaws.com/{{config('filesystems.disks.s3.bucket')}}/pdf/{{$courseContentsDet->value}}"
                    style="border: solid 1px #000000" frameborder="0" width="100%" height="842"></iframe>
             </div>
          </div>

          @if($userContents->completed == 0)
            <div class="row">
              <div class="col-md-12" style="padding-right: 0px !important;padding-left: 0px !important;padding-top: 5px !important">
                <input class="button pdf-read-btn btn col-md-12" id="mdl_save_change" type="button" value="I have read and understood the document">
              </div>
            </div>
          @endif
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
 <script>
 	$('#mdl_save_change').on('click', function(e) {
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
                            swal({ html:true, title:'<font size="3" class="title-lin">Congratulations! You have successfully completed this course.</font>', text:'<h2 class="rating-title">Please rate the course</h2><div class="rating_div"></div>'});
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
                                              window.location.reload();
                                            }
                                        });
                                  }
                                });
                        } else if (data.success) {
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

 	</script>



@stop
