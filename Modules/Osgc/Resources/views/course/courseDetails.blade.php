@extends('layouts.cgl360_osgc_scheduling_layout')

@section('css')

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU"
        crossorigin="anonymous">
<style>
  html, body {
  height: 100%;
  margin: 0;
  font-family: 'Montserrat' !important;
    
}
.main{
  height: 100%;
}
.content-footer
{
  /* position: absolute;
  bottom: 0;
  width: 100%; */
}
.navbar
{
  margin-bottom: 20px;
}
.customtab
{
  width: 100%;
  height: 100%;
}
#tab-button {
  display: table;
  table-layout: fixed;
  width: 100%;
  margin: 0;
  padding: 0;
  list-style: none;
}
#tab-button li {
  display: table-cell;
  width: 20%;
}

#tab-button li a {
  display: block;
  padding: .6em;
  background: #07253a;
  border: 1px solid #ddd;
  text-align: center;
  color: #fff;
  text-decoration: none;
  font-size: 18px;
}
#tab-button li:not(:first-child) a {
  border-left: none;
}
#tab-button li  a:hover,
#tabs .ui-state-active a,
#tabs .ui-tabs-active a{
  background: #f36424 !important;
  
}
.ui-state-focus:focus { outline:1px solid #ddd !important }
.tab-contents {
  /* padding: .5em 2em 1em; */
  border: 1px solid #ddd;
  
}
.first-tab {
  background: #458d3c;
}
.second-tab {
  background: #f36424;
}
.other-tab {
  background: #1a182b;
}

.vertical-menu {
    display: block;
    /* position: relative; */
}
.vertical-menu a {
  
  color: #fff !important;
  display: block;
  padding: 10px;
  background-color: #07253a;
  font-size: 17px; 
  
  text-align: justify;
  padding-left: 15px;
  border :1px solid #07253a;
}
.err-validation
{
  color: #c00;
}
.err {
  border: 1px solid #c00;
}
.vertical-menu a:hover {
  opacity: 0.7;
}
.opacityCls{
  opacity: 0.4;
}
.full-height{
  height: 74vh;
}
.part1 {
  
  background-color:#f36424;
  padding-top: 50px;
  padding-bottom: 45px;
  overflow: auto;
}
.part2{
  
  
  /* padding: 0px; */
 
}
.content-area
{
  /* margin: auto 5%; */
  /* padding: 3%; */
}
.content-area-scroll{
  overflow-y: scroll;
}
.black-background{
  background: black;
}
.video-border{
  background-color:black;
  /* border: 20px solid #07253a; */
}

.content-bottom-div
{
  padding: .4em;
  background: #07253a;
  border-right: 1px solid white;
}
.vertical-menu .active
{
  
  opacity: 0.7;
}
.exam-color{
  color: white;
}
.disable-exam-color{
  color: #796a6a !important;
}
.isDisabled {
  cursor: not-allowed;
  pointer-events: none;
  text-decoration: none;
  
}
#tabs .header-watched a,#tabs #tab-button .ui-tabs-active .header-watched,.header-watched{
  background:#458d3c;
  
}
.swal-text {
    color: #868e96!important;
    font-family: 'Open Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif !important;
}

.watched{
  background:#458d3c !important;
  border :1px solid #2d7224 !important; 
}
.media-height{
  /* position: absolute;
  top: 50%;left: 50%;
  transform: translate(-50%, -50%); */
}
@media screen and (min-width: 768px) {
    .tab-button-outer {
      position: relative;
      z-index: 2;
      display: block;
    }
    .tab-select-outer {
      display: none;
    }
    
    
}
</style>
@stop
@section('content')
@if(count($result->ActiveCourseHeaders) >0)
<section class="container">
<div class="col-md-12 main" >
  <div class="row justify-content-center" >
    <input type="hidden" name="course_id" id="course_id" value="{{$id ?? ''}}">
        
        <!-- start -->
        <div class="tabs customtab" id="tabs" style="display: none;"> 
              <ul id="tab-button" class="tabclass">
                    @foreach($result->ActiveCourseHeaders as $key=> $header)
                    <?php $headId=$header->courseUserCompletion->course_header_id ?? 0;?>
                      <li class="@if($isheaderComplted[$header->id] ==true) {{'header-watched'}}@endif " id="{{$header->id}}"><a href="#tab{{$key}}"   >@if($trimValue !=''){{Illuminate\Support\Str::limit($header->name, $trimValue, '..')}}@else {{$header->name}} @endif</a></li>
                    @endforeach
                </ul>
               @foreach($result->ActiveCourseHeaders as $key1=> $header)
                <div id="tab{{$key1}}" class="tab-contents">
                <div class="input-group full-height">
                        <div class="part1 col-md-3 col-sm-4 ">
                            <div class="vertical-menu row">
                                @foreach($result->ActiveCourseSections as $key2=> $section)
                                    @if($header->id == $section->header_id)
                                    <a href="#{{$section->id}}" id="{{$key2+1}}" class="@if(isset($section->courseUserCompletion->status) && $section->courseUserCompletion->status ==1) {{'watched'}}@endif @if(isset($section->courseUserCompletion->status)) {{''}} @else {{'isDisabled'}} @endif"  onclick="showContent({{$section->id}},0)" data-value="@if(isset($section->courseUserCompletion->status) && $section->courseUserCompletion->status ==1){{'0'}}@else{{$section->completion_mandatory}}@endif">{{$section->name}}</a>
                                    <input type="hidden" name="study_guide_name{{$section->id}}" value="{{$section->studyGuide->file_name ?? ''}}">  
                                    @if(isset($firstSection->id) && $firstSection->id ==$section->id )
                                      <script>
                                      $( document ).ready(function() {
                                        $(".vertical-menu a").removeClass("active");
                                        $(".vertical-menu #{{$key2+1}}").addClass("active");
                                          showContent("{{$section->id}}",1);
                                      });
                                      </script>
                                      @endif
                                    @endif
                                @endforeach
                                </div>
                              
                        </div>
                        <div class="part2 col-md-9 col-sm-8">
                            <div id="course_content_div{{$key1}}" class="content-area result "></div>
                        </div>
              </div>
              </div>
              @endforeach
        </div>
        
        <!-- End -->
            
  </div>
  <!-- footer-->
  <div class="row justify-content-center content-footer ">
      <div class="input-group content-bottom" id="certification-added" align="center" style="display: none;">
          <div class="col-md-4  content-bottom-div"  ><a href="#" style="color: white;" class="studyguide"  id="studyguide" onclick="showStudyGuide(0)">Study Guide </a></div>
          <div class="col-md-4  content-bottom-div" ><a  href="#" id="exam"  class="exam-color" onclick="showTest(0)" >Test Your Knowledge </a></div>
          <div class="col-md-4  content-bottom-div" ><a  href="#" id="getCertificate"  class=" exam-color" onclick="downloadCertificate()">Download Certificate </a></div>
      </div>
      <div class="input-group content-bottom" id="certification" align="center" style="display: none;">
          <div class="col-md-6  content-bottom-div"><a href="#" style="color: white;"  class="studyguide" id="studyguide" onclick="showStudyGuide(0)">Study Guide </a></div>
          <div class="col-md-6  content-bottom-div" ><a  href="#" id="exam" class="exam-color" onclick="showTest(0)" >Test Your Knowledge </a></div>
      </div>      
  </div>
  <!-- footer-->
</div>
</section>
@endif         
  



@stop
@section('scripts')


<script>
  
  function generateCertificate(defaultTab=0)
      { 
        
        var index = $('#tabs').tabs('option', 'active');
        
       $.ajax({
          headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
                url: '{{ route("osgc.getCertificate") }}',
                type: 'POST',
                data: "course_id=" + {{$id}},
                success: function (data) {
                  
                  $('#tab'+index+' .part2').removeClass('content-area-scroll');
                  $('#tab'+index+' .part2').removeClass('video-border');
                  $('#tab'+index+' .result').removeClass('content-area');
                  
                  $('#certification-added').show();
                  $('#certification').hide();
                  $( "#tabs" ).tabs({ selected: index });
                  var newDiv='course_content_div'+index;
                  $('#'+newDiv).html(data);
                    
                }
            });
      }
      function downloadCertificate()
      { 
        
      
       $.ajax({
          headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
                url: '{{ route("osgc.downloadCertificate") }}',
                type: 'POST',
                data: "course_id=" + {{$id}},
                success: function (data) {
                  if(data.url)
                  {
                    window.location.href = data.url;
                  }
                 
                 
                    
                }
            });
      }
      function showStudyGuide(defaultTab=0)
      { 
        
      
       
        var index = $('#tabs').tabs('option', 'active');
        var sectionDet=$("#studyguide").attr("href");
        var sectionId = sectionDet.replace('#','');
       $.ajax({
          headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
                url: '{{ route("osgc.downloadStudyGuide") }}',
                type: 'POST',
                data: "course_section_id=" + sectionId+"&course_id=" + {{$id}},
                success: function (data) {
                  if(data.url)
                  {
                    window.location.href = data.url;
                  }
                 
                 
                    
                }
            });
      }
  function showTest(defaultTab=0)
      { 
        
        if(defaultTab ==1)
       {
         var index=0;
       }else{
        var index = $('#tabs').tabs('option', 'active');
       }
       var newDiv='course_content_div'+index;
       $('#'+newDiv).html('');
      
       $('#tab'+index+' .part2').addClass('content-area-scroll');
       $('#tab'+index+' .part2').removeClass('video-border');
       $('#tab'+index+' .result').removeClass('content-area');
        var sectionDet=$("#exam").attr("href");
        var sectionId = sectionDet.replace('#','');
        $.ajax({
          headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
                url: '{{ route("osgc.showTestContent") }}',
                type: 'POST',
                data: "section_id=" + sectionId+"&course_id=" + {{$id}},
                success: function (data) {
                   
                  $( "#tabs" ).tabs({ selected: index });
                  var newDiv='course_content_div'+index;
                  $('#'+newDiv).html(data);
                    
                }
            });
      }
      function showTestResult(sectionId)
      { 
        
        var index = $('#tabs').tabs('option', 'active');
        $('#tab'+index+' .part2').removeClass('black-background');
        $('#tab'+index+' .part2').removeClass('video-border');
        $('#tab'+index+' .result').removeClass('content-area');
        $.ajax({
          headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
                url: '{{ route("osgc.showTestResult") }}',
                type: 'POST',
                data: "section_id=" + sectionId+"&course_id=" + {{$id}},
                success: function (data) {
                   
                  $( "#tabs" ).tabs({ selected: index });
                  var newDiv='course_content_div'+index;
                  $('#'+newDiv).html(data);
                    
                }
            });
      }
  function showContent(sectionId,defaultTab)
      {
       
       if(defaultTab ==1)
       {
         var index=0;
         var firstChild=$('#tab0 .part1 .vertical-menu a:first-child').attr("id");
         $("#tab0 .part1 .vertical-menu #"+firstChild).removeClass("isDisabled");
         
       }else{
        var index = $('#tabs').tabs('option', 'active');
       }
       var newDiv='course_content_div'+index;
       $('#'+newDiv).html('');

       var prevsectionDet=$("#studyguide").attr("href")
        if(prevsectionDet !==undefined)
        {
          var prevsectionId = prevsectionDet.replace('#','');
          $('#my-video'+prevsectionId+'_html5_api').trigger('pause'); 
        }

       $("#exam").attr("href", "#"+sectionId);
       $("#studyguide").attr("href", "#"+sectionId);
       $('#tab'+index+' .part2').removeClass('content-area-scroll');
      
       var courseId=$('#course_id').val();
        $.ajax({
          headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
                url: '{{ route("osgc.showCourseContent") }}',
                type: 'POST',
                data: "section_id=" + sectionId+"&course_id="+courseId,
                success: function (data) {
                   
                  $( "#tabs" ).tabs({ selected: index });
                  var newDiv='course_content_div'+index;
                 
                  
                  $('#tab'+index+' .part2').addClass('video-border');
                  $('#tab'+index+' .result').addClass('content-area');
                  $('#'+newDiv).html(data);

                  
                 

                  var CurrentValue=$('#tab'+index+' .part1 .vertical-menu .active').data("value");
                  var nextId=$('#tab'+index+' .part1 .vertical-menu .active').nextAll('a:first').attr("id");
                  var curId=$('#tab'+index+' .part1 .vertical-menu .active').attr("id");console.log('11'+curId)
                  if(nextId !==undefined){console.log('222'+$('#tab'+index+' .part1 .vertical-menu #'+curId).hasClass('watched'))
                    if($('#tab'+index+' .part1 .vertical-menu #'+curId).hasClass('watched')==true)
                    {
                      $('#tab'+index+' .part1 .vertical-menu #'+nextId).removeClass("isDisabled");
                      
                    }else{
                      if($('#tab'+index+' .part1 .vertical-menu #'+nextId).hasClass('watched')==false)
                        {
                          if(CurrentValue ==1)
                          {
                            $('#tab'+index+' .part1 .vertical-menu #'+nextId).addClass("isDisabled");
                          }else{
                            $('#tab'+index+' .part1 .vertical-menu #'+nextId).removeClass("isDisabled");
                          }
                        }
                    }
                  }

                  var lastWatched=$('#tab'+index+' .part1 .vertical-menu .watched').last().attr("id");
                  if(lastWatched !==undefined)
                  {
                    var nextUnwatchedId=$("#tab"+index+" .part1 .vertical-menu #"+lastWatched).nextAll('a:first').attr("id");
                    if(nextUnwatchedId !==undefined)
                    {
                    $("#tab"+index+" .part1 .vertical-menu #"+nextUnwatchedId).removeClass("isDisabled");
                    }
                  }
                  //checkStudyGuideExist(sectionId); 
                  var file_name= $('#tab'+index+' .part1 .vertical-menu input[name="study_guide_name'+sectionId+'"]').val();
                  $('.studyguide').removeClass("isDisabled");
                  $('.studyguide').removeClass("opacityCls");
                  if(file_name =='')
                  {
                    $('.studyguide').addClass("isDisabled");
                    $('.studyguide').addClass("opacityCls");
                  }
                }
            });
          
      }
    
function isCourseComplted()
{
  var certification_flag='{{$isExist}}';//console.log('{{$isExist}}')
  if(certification_flag)
  {
    $('#certification-added').show();
    $('#certification').hide();
  }else{
    $('#certification').show();
    $('#certification-added').hide();
  }
}
function checkStudyGuideExist(sectionId)
      { 
        
      
       
        var index = $('#tabs').tabs('option', 'active');
        $.ajax({
          headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
                url: '{{ route("osgc.checkStudyGuideExist") }}',
                type: 'POST',
                data: "course_section_id=" + sectionId+"&course_id=" + {{$id}},
                success: function (data) {
                  $('.studyguide').removeClass("isDisabled");
                  $('.studyguide').removeClass("opacityCls");
                  if(data.file_name ==undefined)
                  {
                    $('.studyguide').addClass("isDisabled");
                    $('.studyguide').addClass("opacityCls");
                  }
                 
                 
                    
                }
            });
      }
$( function() {
  isCourseComplted();
  $(".vertical-menu a").click(function(){
        $(".vertical-menu a").removeClass("active");
        $(this).addClass("active"); 
        
    });
  $("#tabs").removeAttr('style');
  $( "#tabs" ).tabs(
      {
        active: 0,
        activate: function (event, ui) {
        var index=ui.newTab.index();
        var sectionDet=$("#studyguide").attr("href");
        if(sectionDet !==undefined)
        {
          var sectionId = sectionDet.replace('#','');
          $('#my-video'+sectionId+'_html5_api').trigger('pause'); 
        }
        var newDiv='course_content_div'+index;
        $('#'+newDiv).html('');
       
        $('#tab'+index+' .part1 .vertical-menu .watched').removeClass("isDisabled");
        var lastWatched=$('#tab'+index+' .part1 .vertical-menu .watched').last().attr("id");
        if(lastWatched !==undefined)
        {
          var nextUnwatchedId=$("#tab"+index+" .part1 .vertical-menu #"+lastWatched).nextAll('a:first').attr("id");
          if(nextUnwatchedId !==undefined)
          {
          $("#tab"+index+" .part1 .vertical-menu #"+nextUnwatchedId).removeClass("isDisabled");
          }
        }
        
        var sectionId=$('#tab'+index+' .part1 .vertical-menu a:first-child').attr("href");//console.log('dd'+sectionId)
        var firstChild=$('#tab'+index+' .part1 .vertical-menu a:first-child').attr("id");
        if(sectionId !==undefined)
        {
          var sectionId = sectionId.replace('#','');
          $("#tab"+index+" .part1 .vertical-menu .active").removeClass("active");
          $("#tab"+index+" .part1 .vertical-menu #"+firstChild).addClass("active");
          var courseId=$('#course_id').val();
          $.ajax({
          headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
                url: '{{ route("osgc.checkContentActive") }}',
                type: 'POST',
                data: "section_id=" + sectionId+"&course_id="+courseId,
                success: function (data) {
                  if(data.success ==true)
                  {
                    showContent(sectionId,0);
                    $("#tab"+index+" .part1 .vertical-menu #"+firstChild).removeClass("isDisabled");
                  }
                  
                }
            });
          
        }else{
          $("#studyguide").attr("href", "#0");
          $("#exam").attr("href", "#0");
        }
        
        }
      }
    );



    
  } );

</script>
@stop



