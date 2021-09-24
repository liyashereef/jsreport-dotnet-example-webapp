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
            <img class="col-md-12" style="min-height: 400px;border:solid 1px #00000;padding-left:0px !important;padding-right:0px !important" src="https://s3.{{config('filesystems.disks.s3.region')}}.amazonaws.com/{{config('filesystems.disks.s3.bucket')}}/images/{{$courseContentsDet->value}}" alt=""/>
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
 <script>
 	$(document).ready(function () {
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
                        if (data.success) {
                           // swal("Completed", "Your Content has been completed", "success");
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

