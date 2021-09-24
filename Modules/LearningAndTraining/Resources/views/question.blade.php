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



<div  class="container-fluid inner-body">
 {{ Form::open(array('url'=>'#','id'=>'test-form','class'=>'form-horizontal', 'method'=> 'POST')) }} {{csrf_field()}}
 <input type="hidden" name="course_id" value="{{$courseDet['id']}}">
 <input type="hidden" name="test_setting_id" value="{{$examSetting->id}}">
 <input type="hidden" name="test_user_result_id" value="{{$examInputs['test_user_result_id']}}">
 <h1 class="title color-high">{{$examSetting['exam_name']}}</h1>
 <div class="container">

  <!--START-- Question Section  -->
  @foreach ($examInputs['questions'] as $key=>$each_question)
  <div class="question">
  <div class="form-group row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12" style=background-color:dde9ed> 
      {{$key+1}}.&nbsp;{!!nl2br($each_question['test_question'])!!}  
    </div>   
  </div>

  <!--START-- Option Looping  -->
  @foreach ($each_question['test_question_options'] as $key=>$each_question_option)
  <div class="form-group row col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
    <label class='radio-butto-label' for="{{$each_question['id']}}_options">
      <!--START-- Option radio button  -->
      <input class='doc-title-top' type='radio' name="{{$each_question['id']}}_options" value="{{$each_question_option['id']}}"
      data-id="{{$each_question['id']}}"
      @if((in_array($each_question_option['id'], $examInputs['attemptedOptionIds']))) checked="checked" @endif />
      {{$each_question_option['answer_option']}} 
    </label>
 </br>


    <!--END-- Option radio button  -->
 
  </div>  
 
  @endforeach
   <span class="help-block"></span> 
 
   </div> 

  <!--END-- Option Looping  -->
  @endforeach 
  
  <!--END-- Question Section  -->
    @if($examInputs['questions']->count()!=0)
  <div class='text-center margin-bottom-5'> 
    {{ Form::submit('Submit', array('class'=>'button btn submit','id'=>'mdl_save_change'))}}
    
  </div>  
  @endif
</div>
{{ Form::close() }}  

</div>


@stop
@section('scripts')
<script type="text/javascript" src="{{ asset('js/stars.js') }}"></script>
<link href="{{ asset('css/training/leaner-dashboard/css/dashboard-styles.css') }}" rel="stylesheet">
<link href="{{ asset('css/training/course-list.css') }}" rel="stylesheet">
<script src="https://rawgit.com/kottenator/jquery-circle-progress/1.2.2/dist/circle-progress.js"></script>
<script>
window.addEventListener( "pageshow", function ( event ) {
  var historyTraversal = event.persisted || 
                         ( typeof window.performance != "undefined" && 
                              window.performance.navigation.type === 2 );
  if ( historyTraversal ) {
    // Handle page restore.
    window.history.go(-1)
  }
});
  $(document).ready(function () {
   test_result_array = [];
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
      });
     }
   });
   $('.progress-component').circleProgress({
    value: {{$circleBar['value']}}
  }).on('circle-animation-progress', function(event, progress) {
    $(this).find('strong').html(Math.round({{$circleBar['perc']}} * progress) + '<i>%</i>');
  });
  

  $('input[type=radio]').change(function(){
    $('div.question:has(:radio:checked)').find('.help-block').text('')
    var selected_option=$(this).val();
    var question_id=$(this).attr('data-id');
    var course_id=$('input[name="course_id"]').val();
    var test_setting_id=$('input[name="test_setting_id"]').val();
    var test_user_result_id=$('input[name="test_user_result_id"]').val();
    test_result_array=prepareTestResultArray(test_result_array,selected_option,question_id);     
    $.ajax({
     headers: {
      'X-CSRF-TOKEN':'{{ csrf_token() }}'
    },
    url: "{{ route('test-results.store') }}",
    type: 'POST',
    data:  {
      'test_result_array':test_result_array,
      'test_setting_id':test_setting_id,
      'test_user_result_id':test_user_result_id,
      'course_id':course_id,
      'final_submit':0,
    },
     beforeSend: function() {
        // setting a timeout
        $('.loading-overlay-content').hide();
    },
    success: function (data) {
    }
  });
  })

  $('#test-form').submit(function (e) {
    e.preventDefault();
    if($('div.question:not(:has(:radio:checked))').length>0){
    $('div.question:not(:has(:radio:checked))').find('.help-block').text('Choose any option')
    $("html, body").animate({ scrollTop: 0 },2000);
    return false;
     }
    var course_id=$('input[name="course_id"]').val();
    var test_setting_id=$('input[name="test_setting_id"]').val();
    var test_user_result_id=$('input[name="test_user_result_id"]').val();
    var choosen_option=  $('#test-form').find("input[type=radio]:checked");
    $.each(choosen_option, function (index, element) {
     var selected_option=$(element).val();
     var question_id=$(element).attr('data-id');
     test_result_array=prepareTestResultArray(test_result_array,selected_option,question_id); 
   });
    $.ajax({
      headers: {
       'X-CSRF-TOKEN':'{{ csrf_token() }}'
     },
     url:  "{{ route('test-results.store') }}",
     type: 'POST',
     data:  {
      'test_result_array':test_result_array,
      'test_setting_id':test_setting_id,
      'test_user_result_id':test_user_result_id,
      'course_id':course_id,
      'final_submit':1,
    },
    success: function (data) {
      if (data.success) {
        swal({
          title: "Saved",
          text: "The record has been saved",
          type: "success"},
          function(){ 
          var base_url = "{{ route('test-result.show',':id') }}";
           var url = base_url.replace(':id', test_user_result_id);
           window.location.href = url;
          });
      } else {
        console.log(data);
        swal("Oops", "The record has not been saved", "warning");
      }
    },
    fail: function (response) {
      console.log(response);
      swal("Oops", "Something went wrong", "warning");
    },
    error: function (xhr, textStatus, thrownError) {
      associate_errors(xhr.responseJSON.errors, $form);
    },
    
  });
  });


  function prepareTestResultArray(test_result_array,selected_option,question_id)
  {
    test_result={}; 
    let flag = test_result_array.some(function(item) {
      return item.question_id === question_id
    });
    if(flag)
    {
      test_result_array = test_result_array.filter(function(item){  return item.question_id != question_id }) 
    }
          // test_result.course_id=course_id;
          test_result.question_id = question_id;
          test_result.selected_option = selected_option;
          test_result_array.push(test_result);
          return test_result_array;
        }
      });



    </script>




    @stop