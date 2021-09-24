
<div class="col-md-12">

      <br>
      <div class="result-message text-center">
      <div  class="inner-body text" style="display: inline-block;padding-top: 2rem !important;">
          
          <div class="card text-center"  style="width: 40rem;">
              <div class="card-body">
                  @if($examResult->is_exam_pass==1)
                  <input type="hidden" name="section_id" value="{{$examResult->course_section_id}}">
                      <i class="fas fa-check-circle fa-5x success" style="padding: 0rem !important;color:green"></i>
                      <h5 class="card-title">Congratulations, you passed!</h5>
                      <div class="row">
                          <div class="col-1"></div>
                          <div class="col">
                              <p style="float: right; margin-right: -20px !important;">Your Score:</p>
                          </div>
                          <div class="col">
                              <p style="float: left; margin-left: -5px !important;">{{round($examResult->score_percentage)}}%</p>
                          </div>
                      </div>
                      <div class="row" style="padding-bottom: 0rem !important;">
                      <div class="col-1"></div>
                          <div class="col">
                              <p style="float: right; margin-right: -20px !important;">Passing Score:</p>
                          </div>
                          <div class="col">
                              <p style="float: left; margin-left: -5px !important;">{{round($examResult->course_pass_percentage)}}%</p>
                          </div>
                      </div>
                      
                      <div class='text-center margin-bottom-5' style="padding-top: 10px"> 
                          <a class="button btn submit option" onclick="nextCourse()" style="font-size: 15px">OK</a>
                          
                         
                      </div>
                  @else
                      <i class="far fa-times-circle fa-5x" style="padding: 17px !important;"></i>
                      <h5 class="card-title">Sorry, you didn't make it!</h5>
                      <div class="row" style="padding-top: 5px;">
                          <div class="col-1"></div>
                          <div class="col">
                              <p style="float: right; margin-right: -20px !important;">Your Score:</p>
                          </div>
                          <div class="col">
                              <p style="float: left; margin-left: -5px !important;">{{round($examResult->score_percentage)}}%</p>
                          </div>
                      </div>
                      <div class="row" style="padding-bottom: 5px;">
                          <div class="col-1"></div>
                          <div class="col">
                              <p style="float: right; margin-right: -20px !important;">Passing Score:</p>
                          </div>
                          <div class="col">
                              <p style="float: left; margin-left: -5px !important;">{{round($examResult->course_pass_percentage)}}%</p>
                          </div>
                      </div>
                      <p class="card-text">Would you like to re-take the test now?</p>
                      <div class='text-center margin-bottom-5' style="padding-top: 10px"> 
                          <a class="button btn submit option" onclick="showTest(0)" style="font-size: 15px">Yes</a>
                          <a class="button btn submit option" onclick="showContent({{$examResult->course_section_id}},0)" style="font-size: 15px">No</a>
                         
                      </div>
                  @endif
              </div>
          </div>
      </div>
  </div>    

</div>
<style>
    .swal-text {
        color: #868e96!important;
        font-family: 'Open Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
    }
</style>                 
<script>
  function nextCourse()
  {
    var index = $('#tabs').tabs('option', 'active');
    var activechild=$('#tab'+index+' .part1 .vertical-menu .active').attr("href");//console.log(activechild);
    var activechildId=$('#tab'+index+' .part1 .vertical-menu .active').attr("id");//console.log(activechild);
    var activeSectionId = activechild.replace('#','');
    var lastchild=$('#tab'+index+' .part1 .vertical-menu  a').last().attr("href");
    var courseId=$('#course_id').val();
    $.ajax({
          headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
                url: '{{ route("osgc.checkLastCourse") }}',
                type: 'POST',
                data: "section_id=" + sectionId+"&course_id=" + courseId,
                success: function (data) {
                   
                  if(data.success == true)
                  {
                        $("#tab"+index+" .part1 .vertical-menu #"+activechildId).addClass("watched");
                        if(lastchild == activechild)
                        {
                            $("#tabs #tab-button .ui-state-active").addClass("header-watched");
                        }
                        if(data.last_child == 1){
                            var message ='Click OK to get your certificate';
                            var title='Success';
                            swal({
                                title: title,
                                text: message, 
                                icon: "success",
                                
                            });
                            generateCertificate()
                        }
                  }else{
                    if(lastchild == activechild)
                    {
                      var active = $("#tabs").tabs( "option", "active" );
                      $("#tabs #tab-button .ui-state-active").addClass("header-watched");
                      $("#tabs").tabs("option", "active", active+1); 
                      var newindex = $('#tabs').tabs('option', 'active');
                      var nextId=$('#tab'+newindex+' .part1 .vertical-menu a:first-child').attr("id");
                      var nextchild=$('#tab'+newindex+' .part1 .vertical-menu a:first-child').attr("href");
                      var sectionId = nextchild.replace('#','');
                    }else{
                      var newindex = $('#tabs').tabs('option', 'active');
                      var nextchild=$('.vertical-menu .active').nextAll('a:first').attr("href");//console.log(nextchild+'kii');
                      var nextId=$('.vertical-menu .active').nextAll('a:first').attr("id");
                      var sectionId = nextchild.replace('#','');
                    }
                    
                    $("#tab"+index+" .part1 .vertical-menu #"+activechildId).addClass("watched");
                    $("#tab"+index+" .part1 .vertical-menu #"+activechildId).removeClass("active");
                    $("#tab"+newindex+" .part1 .vertical-menu #"+nextId).addClass("active");
                    $("#tab"+newindex+" .part1 .vertical-menu #"+nextId).removeClass("isDisabled");
                    showContent(sectionId,0)
                  }
                    
                }
            });
    
            
  }
  </script>



