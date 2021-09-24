@if((isset($checkExist) && $checkExist->content_completed ==1))
@if($examSetting)

<div class="row">
  <div class="col-md-12">
      <div  class="container-fluid inner-body">
      <?php $formId='test-form'.$examSetting->osgc_course_section_id;?>
      {{ Form::open(array('url'=>'#','id'=>$formId,'class'=>'form-horizontal', 'method'=> 'POST')) }} {{csrf_field()}}
      <input type="hidden" name="course_id" value="{{$examSetting->course_id}}">
      <input type="hidden" name="sections_id" value="{{$examSetting->osgc_course_section_id}}">
      <input type="hidden" name="test_setting_id" value="{{$examSetting->id}}">
      <input type="hidden" name="test_user_result_id" value="{{$examInputs['test_user_result_id']}}">
      <br>
      <h5 class="title color-high">{{$examSetting['exam_name']}}</h5><br>

          <div class="container">

          <!--START-- Question Section  -->
          @foreach ($examInputs['questions'] as $key=>$each_question)
          <div class="question">
          <div class="form-group row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12" style=background-color:dde9ed> 
             <p class="questionCls"> {{$key+1}}.&nbsp;{!!nl2br($each_question['test_question'])!!}  </p>
            </div>   
          </div>

           <!--START-- Option Looping  -->
          <div class="form-group  col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
          @foreach ($each_question['test_question_options'] as $key=>$each_question_option)
            <div>
            <label class='radio-butto-label' for="{{$each_question['id']}}_options">
            <!--START-- Option radio button  -->
            <input class='doc-title-top' type='radio' name="{{$each_question['id']}}_options" value="{{$each_question_option['id']}}"
              data-id="{{$each_question['id']}}"
              @if($flag ==1) @if((in_array($each_question_option['id'], $examInputs['attemptedOptionIds']))) checked="checked" @endif @endif />
              {{$each_question_option['answer_option']}} 
            </label>
            </div>
            <!--END-- Option radio button  -->        
          
          @endforeach
          </div>  
        <span class="help-block"></span> 
      
      </div> 

      <!--END-- Option Looping  -->
      @endforeach 
  
      <!--END-- Question Section  -->
        @if($flag ==0)
      <div class='text-center margin-bottom-5'> 
        {{ Form::submit('Submit', array('class'=>'button btn submit','id'=>'mdl_save_change'))}}
        
      </div>  
      @endif
</div>
{{ Form::close() }}  

</div>
</div>
</div>
@else 
<div class="col-md-12" align="center"><br>
<h5>No Questions Found</h5>
</div>
@endif                       
@else 
<div class="col-md-12"  align="center"><br>
<h5>Please complete the course to access the quiz</h5>
</div>
@endif
<style>
  .questionCls
  {
    font-size: 1.1rem;
  }
  </style>
<script>
$( function() {
  
  test_result_array = [];
  var index = $('#tabs').tabs('option', 'active');
  var activechild=$('#tab'+index+' .part1 .vertical-menu .active').attr("href");//console.log(activechild);
  var activechildId=$('#tab'+index+' .part1 .vertical-menu .active').attr("id");//console.log(activechild);
  var activeSectionId = activechild.replace('#','');
  
 $('#test-form'+activeSectionId).submit(function (e) {
    e.preventDefault();
    
    if($('div.question:not(:has(:radio:checked))').length>0){
    $('div.question:not(:has(:radio:checked))').find('.help-block').text('Choose any option')
    $("html, body").animate({ scrollTop: 0 },2000);
    return false;
     }
    
     
    var lastchild=$('#tab'+index+' .part1 .vertical-menu  a').last().attr("href");
    var courseId=$('#course_id').val();
    
    var section_id=$('#tab'+index+' input[name="sections_id"]').val();
    var test_setting_id=$('#tab'+index+' input[name="test_setting_id"]').val();
    var test_user_result_id=$('#tab'+index+' input[name="test_user_result_id"]').val();
    var choosen_option=  $('#tab'+index+' #test-form'+activeSectionId).find("input[type=radio]:checked");
    $.each(choosen_option, function (index, element) {
     var selected_option=$(element).val();
     var question_id=$(element).attr('data-id');
     test_result_array=prepareTestResultArray(test_result_array,selected_option,question_id); 
   });
    $.ajax({
      headers: {
       'X-CSRF-TOKEN':'{{ csrf_token() }}'
     },
     url:  "{{ route('osgc.storeOsgcTest') }}",
     type: 'POST',
     data:  {
      'test_result_array':test_result_array,
      'test_setting_id':test_setting_id,
      'test_user_result_id':test_user_result_id,
      'section_id':section_id,
      'course_id':courseId,
      'final_submit':1,
    },
    success: function (data) {
      if (data.success) {
        
        if(data.result)
          {
            showTestResult(section_id);
            return false;
          }else{
          // if(data.last_child ==1)
          // {
          //   var message ='Click OK to get your certificate';
          //   var title='Success';
          //       swal({
          //       title: title,
          //       text: message, 
          //       icon: "success",
          //       buttons: true,
          //       dangerMode: true,
          //   })
          //   .then((willok) => {
          //     if (willok) {
          //       if(data.last_child ==1)
          //       {
          //         $("#tab"+index+" .part1 .vertical-menu #"+activechildId).addClass("watched");
          //         $("#tabs #tab-button .ui-state-active").addClass("header-watched");
          //         generateCertificate()
                  
          //       }
                
          //     } 
          //   }); 
          // }
          
          

        }
        
      
        
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
</script>
