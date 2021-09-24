
@if($sectionDet)

<div class="media-contain media-height video-container">
                                    
                                        @if($content !='')       
                                        <video id='my-video{{$sectionId}}' class="video-js   vjs-default-skin vjs-big-play-centered  vjs-fluid"  controls preload='auto'    style="border: none;" 
                                            data-setup=''>
                                            
                                            <source src="{{$url}}" type='video/mp4'>
                                            <p class='vjs-no-js'>
                                                To view this video please enable JavaScript, and consider upgrading to a web browser thats
                                                <a href='https://videojs.com/html5-video-support/' target='_blank'>supports HTML5 video</a>
                                            </p>
                                        </video>
                                        <script src='https://vjs.zencdn.net/7.5.4/video.js'></script>
                                        <link href="https://vjs.zencdn.net/7.5.4/video-js.css" rel="stylesheet">
                                        <script>





var index = $('#tabs').tabs('option', 'active');
var activechild=$('#tab'+index+' .part1 .vertical-menu .active').attr("href");//console.log(activechild+'ss'+index);
var activechildId=$('#tab'+index+' .part1 .vertical-menu .active').attr("id");
if(activechild !==undefined)
{
var sectionId = activechild.replace('#','');
var vdioId="#my-video"+sectionId; 
var vdioId1="my-video"+sectionId; 
$( function() {
    var videoplay = document.getElementById('video');
  videojs(vdioId1).ready(function() {
    
    var myPlayer = this;
    myPlayer.fluid(true);
    myPlayer.responsive(true);
//Set initial time to 0
var currentTime = 0;

//This example allows users to seek backwards but not forwards.
//To disable all seeking replace the if statements from the next
//two functions with myPlayer.currentTime(currentTime);

if({{$sectionDet->courseContent->fast_forward}}) {
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







myPlayer.on("ended",function(event) {
                                            var lastchild=$('#tab'+index+' .part1 .vertical-menu  a').last().attr("href");//console.log(lastchild)
                                            var courseId=$('#course_id').val();
                                            $.ajax({
                                                headers: {
                                                'X-CSRF-TOKEN':'{{ csrf_token() }}'
                                                },
                                                url:  "{{ route('osgc.checkCourseHavingTest') }}",
                                                type: 'POST',
                                                data:  {
                                                'section_id':sectionId,
                                                'course_id':courseId,
                                                
                                                },
                                                success: function (data) {
                                                if (data.success) {
                                                    
                                                    if(data.flag == 1)
                                                    {
                                                        var message ='You have completed the course successfully';
                                                        var title='Success';

                                                    }else if(data.last_child ==1)
                                                    {
                                                        var message ='Click OK to get your certificate';
                                                        var title='Success';
                                                    }else{
                                                        var message ='You have completed the course successfully';
                                                        var title='Success';
                                                    }
                                                        swal({
                                                            title: title,
                                                            text: message, 
                                                            icon: "success",
                                                            button:true
                                                        })
                                                        .then((willok) => {
                                                        if (willok) {
                                                            
                                                                if(data.flag == 1)
                                                                {
                                                                    $('#exam').removeClass("isDisabled");
                                                                    $('#exam').removeClass("disable-exam-color");
                                                                    $('#exam').addClass("exam-color");
                                                                    $("#tab"+index+" .part1 .vertical-menu #"+activechildId).addClass("watched");
                                                                    if(data.header_complete ==true)
                                                                    {
                                                                        var active = $("#tabs").tabs( "option", "active" );
                                                                        $("#tabs #tab-button .ui-state-active").addClass("header-watched");
                                                                    
                                                                        
                                                                    }else{
                                                                        var newindex = $('#tabs').tabs('option', 'active');
                                                                        var nextid=$('#tab'+newindex+' .part1 .vertical-menu .active').nextAll('a:first').attr("id");
                                                                        $("#tab"+newindex+" .part1 .vertical-menu #"+nextid).removeClass("isDisabled");
                                                                    }
                                                                    //console.log(newindex+'/'+index+'/'+activechildId)
                                                                    
                                                                   
                                                                    showTest(0);
                                                                }else if(data.last_child ==1)
                                                                {
                                                                    $("#tab"+index+" .part1 .vertical-menu #"+activechildId).addClass("watched");
                                                                    if(data.header_complete ==true)
                                                                    {
                                                                        $("#tabs #tab-button .ui-state-active").addClass("header-watched");
                                                                    }
                                                                    generateCertificate()
                                                                }else{
                                                                    if(activechild == lastchild)
                                                                    {console.log('innerdeiv')
                                                                        var active = $("#tabs").tabs( "option", "active" );
                                                                        if(data.header_complete ==true){
                                                                            $("#tabs #tab-button .ui-state-active").addClass("header-watched");
                                                                         }
                                                                        $("#tabs").tabs("option", "active", active+1); 
                                                                        var newindex = $('#tabs').tabs('option', 'active');
                                                                        var nextId=$('#tab'+newindex+' .part1 .vertical-menu a:first-child').attr("id");
                                                                        var nextchild=$('#tab'+newindex+' .part1 .vertical-menu a:first-child').attr("href");
                                                                        var nextSectionId = nextchild.replace('#','');
                                                                    }else{
                                                                        if(data.header_complete ==true){
                                                                            $("#tabs #tab-button .ui-state-active").addClass("header-watched");
                                                                         }
                                                                        var newindex = $('#tabs').tabs('option', 'active');
                                                                        var nextchild=$('#tab'+newindex+' .part1 .vertical-menu  .active').nextAll('a:first').attr("href");
                                                                        var nextid=$('#tab'+newindex+' .part1 .vertical-menu .active').nextAll('a:first').attr("id");
                                                                        var nextSectionId = nextchild.replace('#','');//console.log(nextSectionId)
                                                                    }
                                                                    console.log(newindex+'/'+index+'/'+activechildId)
                                                                    $("#tab"+index+" .part1 .vertical-menu #"+activechildId).addClass("watched");
                                                                    $("#tab"+index+" .part1 .vertical-menu #"+activechildId).removeClass("active");
                                                                    
                                                                    $("#tab"+newindex+" .part1 .vertical-menu #"+nextid).addClass("active");
                                                                    $("#tab"+newindex+" .part1 .vertical-menu #"+nextid).removeClass("isDisabled");
                                                                    
                                                                    showContent(nextSectionId,0)
                                                                    
                                                                }
                                                                    
                                                                
                                                            
                                                        } 
                                                        });
                                                

                                                } else {
                                                    console.log(data);
                                                    swal("Oops", "Something went wrong", "warning");
                                                }
                                                },
                                                fail: function (response) {
                                                console.log(response);
                                                swal("Oops", "Something went wrong", "warning");
                                                },
                                            });
                                                

                                        });
                                    });
    });
}else{
        var firstChild=$('#tab'+index+' .part1 .vertical-menu a:first-child').attr("id");
        $("#tab"+index+" .part1 .vertical-menu #"+firstChild).addClass("active");
        var sectionId=$('#tab'+index+' .part1 .vertical-menu a:first-child').attr("href");
        var sectionId = sectionId.replace('#','');
        showContent(sectionId,0);
        
}
                                           

                                        </script>
                                        @endif
                                   
                            </div>
@endif                        

<style>

.video-container {
        position: absolute;
        top: 0;
        bottom: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        /* object-fit: fill; */
        left: 0;
    right: 0;
    /* display: flex; 
    flex-direction: column; */
    justify-content: center;
    align-items: center;
    }

 
</style>

