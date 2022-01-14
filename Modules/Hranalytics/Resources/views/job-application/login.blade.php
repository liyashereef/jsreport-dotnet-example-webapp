@extends('layouts.candidate-layout')
@section('content')
<div id="outer-wrapper" class="row min-height-adjust" style="padding:50px 10px 50px 10px;">
    <div id="img-div" class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-6" style="margin-top:17%;">
        <img class="candidate-landing-img"   src="{{asset('images/CGL-LOGO-1700px-435px.png')}}">
    </div>
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
        <div id="block1" style="margin-top:37%;">
            <p class="applyjob-page">Have you ever applied to a job with Commissionaires in the past and already completed the candidate application screen?</p>
            <div class="clearfix"></div>
            <button class="landing-page-button" onclick="showBlock('#block4,#block5,#block8')">No</button>
            <button class="landing-page-button landing-page-button-yes" onclick="showBlock('#block3')">Yes</button>
        </div>
        <div id="block2" style="margin-top:35%;">
            {{ Form::open(array('url'=>route('applyjob.login'),'class'=>'applyjob-page','id'=>'form-normal', 'method'=> 'POST')) }}
            {{csrf_field()}}
            {{ Form::hidden('type','normal') }}
            <div class="form-group" id='job_id'>
                {{Form::label('job_id', 'Job Id', array())}}
                {{ Form::text('job_id',null,array('class'=>'form-control','required'=>TRUE,'id'=>'c_job_id')) }}
            </div>
            <div class="form-group" id="g_password">
                {{Form::label('g_password', 'Password', array())}}
                {{ Form::password('g_password',array('class'=>'form-control','required'=>TRUE,'id'=>'c_g_password')) }}
            </div>
            <div class="text-center">
                {{ Form::button('Cancel', array('class'=>'landing-page-button cancel_btn'))}}
                {{ Form::submit('Login', array('class'=>'landing-page-button landing-page-button-yes'))}}
            </div>
            {{ Form::close() }}
        </div>
        <div id="block3" style="margin-top:37%;">
            <p class="applyjob-page">Thank you for your interest in Commissionaires.  Because you've already applied to a previous position, we have your application on file and will actively match you to appropriate jobs in our database.  There is no need to reapply.</p>
            <div class="clearfix"></div>
            <!--{{ Form::open(array('url'=>route('applyjob.login'),'class'=>'applyjob-page','id'=>'form-recurring', 'method'=> 'POST')) }}
            {{csrf_field()}}
            {{ Form::hidden('type','recurring') }}
            <div class="form-group" id='name'>
                {{Form::label('name', 'Full Legal Name', array())}}
                {{ Form::text('name',null,array('class'=>'form-control','required'=>TRUE)) }}
            </div>
            <div class="form-group" id="email">
                {{Form::label('email', 'Direct Email', array())}}
                {{ Form::text('email',null,array('class'=>'form-control','required'=>TRUE)) }}
            </div>
            <div class="text-center">
                {{ Form::button('Cancel', array('class'=>'landing-page-button cancel_btn'))}}
                {{ Form::submit('Login', array('class'=>'landing-page-button landing-page-button-yes'))}}
            </div>-->
            {{ Form::close() }}
        </div>
        <div id="block4" style="margin-top: 0%;">
            <p class="applyjob-page">Please be advised you are entering a secured site. To complete the application process, you will be asked to upload scanned copies of the following documents.</p>
            <div class="clearfix"></div>
            <ul>
                <li class="prerequest-messages">A selfie with your face clearly visible (above the neck)</li>
                <li class="prerequest-messages">Copy of Security Guard License (Front and Back)</li>
                <li class="prerequest-messages">Copy of First Aid Certificate</li>
                <li class="prerequest-messages">Void Check (or Bank Printout Showing Account Information)</li>
                <li class="prerequest-messages">Copy of Your Driver's License (Front and Back)</li>
                <li class="prerequest-messages">Copy of Your Birth Certificate Or Passport (Front and Back)</li>
                <li class="prerequest-messages">Copy of Your Social Insurance Number (Front and Back)</li>
                <li class="prerequest-messages">Copy of Your Wallet Card (Record of Military Service - If Applicable)</li>
                <li class="prerequest-messages">Copy of Your Current Resume</li>
            </ul>
           {{--  <p class="applyjob-page h-gutter-20"><span style="font-size:13px;">To get any help, please contact Sam Philip; Cell: 416 807 4906,Email: sphilip@secture360.ca.ca</span></p>           --}}
        </div>
        <div id="block5" style="margin-top: 0%;">
            <p class="applyjob-page">Please listen to a brief message from our SVP/COO before you begin the application process</p>
            <audio id="audio" controls onplay="audioMessage()">
                <source src="{{ asset('audios/welcome.m4a') }}" type="audio/ogg">
                <source src="{{ asset('audios/welcome.m4a') }}" type="audio/mpeg">
              Your browser does not support the audio element.
              </audio>
            <div class="clearfix"></div>
        </div>
        <div  id="block8"><span style="font-size:17px;color:#ea650f">To initiate application process, you must fully listen to above audio instructions.</span></div>
        <div id="block6">
                <p class="applyjob-page" style="
                top: 0;
                margin-top: 0;
            ">Please note the following:</p>
            <div class="clearfix"></div>
            <ul style="list-style:none;padding-left:0;">
                <li class="prerequest-messages">
                    <label for="checkbox1">
                        1) The application process is assessing my fit for the role. As a result, I understand that all questions must be answered thoroughly,in the format specified,
                    to be considered for a role with our organization.&nbsp;
                        <input id="checkbox1" type="checkbox" class="consent" value="1">
                    </label>
                </li>
                <li class="prerequest-messages">
                    <label for="checkbox2">
                        2) All scanned documents to be uploaded must be less than 3MB in size.&nbsp;
                        <input id="checkbox2" type="checkbox" class="consent" value="1">
                    </label>
                </li>
                <li class="prerequest-messages">
                    <label for="checkbox3">
                        3) I am providing my consent to capture and store my personal information in the secured environment for the purpose of streamlining and processing my employment application.&nbsp;
                        <input id="checkbox3" type="checkbox" class="consent" value="1">
                    </label>
                </li>
                 <li class="prerequest-messages">
                    <label for="checkbox4">
                        4) The application process requires a reliable, high speed internet connection to succesfully complete the candidate screen. I confirm I have a reliable, high speed internet connection.&nbsp;
                        <input id="checkbox4" type="checkbox" class="consent" value="1">
                    </label>
                </li>
                <li class="prerequest-messages">
                    <label for="checkbox5">
                        5) I am providing consent to share my personal information and full candidate screen including case studies and personality test with clients who may be interested in hiring me for a specific post.&nbsp;
                        <input id="checkbox5" type="checkbox" class="consent" value="1">
                    </label>
                </li>
                <li class="prerequest-messages">
                    <label for="checkbox6">
                        6) If you have less than 2 years security experience, you will need to have a copy of your Ontario Security Guard test scores ready for upload before you begin the application process.&nbsp;
                         <input id="checkbox6" type="checkbox" class="consent" value="1">
                     </label>
                 </li>
                 <li class="prerequest-messages">
                    <label for="checkbox7">
                        7) I consent to uploading an image to the CGL 360(TM) database for client review along with my candidate application.
                        <input id="checkbox7" type="checkbox" class="consent" value="1">
                    </label>
                </li>
                 <li class="prerequest-messages">
                    <label for="checkbox8">
                        8) Application process requires uniform measurement. You will need to have Neck, Chest, Arm length, Waist, Hip (for female candidates), and Inside Leg measurements ready.
                        <input id="checkbox8" type="checkbox" class="consent" value="1">
                    </label>
                </li>
                <li class="prerequest-messages">"Check" before you can proceed</li>
            </ul>

            <p class="applyjob-page h-gutter-20">Please click "yes" if you are ready to proceed.</p>

            <button style="text-align: center;" class="landing-page-button" onclick="showDocAlert();">No</button>
            <button id="login-trigger-btn" disabled class="landing-page-button landing-page-button-yes" onclick="showBlock('#block2')">Yes</button>

        </div>
    </div>
    <!--<div class="apply-job">COMMISSIONAIRES GREAT LAKES<br><span class="apply-span"> Candidate Tracking System (CTS)</span></div>-->
</div>
<style>
    body {
    background: #ffffff !important;
    }
    #block2,#block3,#block4,#block5,#block6,#block8{
        display:none;
    }
    #login-trigger-btn:disabled {
    background-color: gray;
}
ul li label{
    cursor: pointer;
}
.navbar{
    display: none;
}
audio:focus {
    outline: none;
}

input[type=checkbox]{
    width:16px;
    height: 16px;
    vertical-align: text-top;
}
</style>
<script>
    function audioMessage(){
       // setTimeout(function() {  $('#block6').show(); }, 5000);
    }
    function showBlock(div_id) {
        $('#block1,#block2,#block3,#block4,#block5,#block6,#block8').hide();
        $(div_id).show();
    }

    $(function () {

        var video = document.getElementById('audio');
        var supposedCurrentTime = 0;
        video.addEventListener('timeupdate', function() {
          if (!video.seeking) {
                supposedCurrentTime = video.currentTime;
          }
        });
        // prevent user from seeking
        video.addEventListener('seeking', function() {
          // guard agains infinite recursion:
          // user seeks, seeking is fired, currentTime is modified, seeking is fired, current time is modified, ....
          var delta = video.currentTime - supposedCurrentTime;
          if (Math.abs(delta) > 0.01) {
            console.log("Seeking is disabled");
            video.currentTime = supposedCurrentTime;
          }
        });
        // delete the following event handler if rewind is not required
        video.addEventListener('ended', function() {
          // reset state in order to allow for rewind
            supposedCurrentTime = 0;
            $('#block6').show(); 
            $('#block8').hide(); 

        });

        $('.cancel_btn').on('click', function () {
            $('#block1').show();
            $('#block2,#block3,#block4,#block5,#block6,#block8').hide();
        })
        $('.consent').on('change',function(){
            if($('.consent:checkbox:checked').length == $('.consent:checkbox').length)
            {
                $('#login-trigger-btn').prop('disabled',false);
            }else{
                $('#login-trigger-btn').prop('disabled',true);
            }
        });
    });

    $('input[type="submit"]').click(function (e) {
        var $form = $(this).parents('form');
        e.preventDefault();
        //var $form = $(this);
        var formData = new FormData($form[0]);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('applyjob.login') }}",
            type: 'POST',
            data: formData,
            success: function (data) {
                if (data.success) {
                    window.location = data.url
                } else {
                    swal("Wrong inputs", "Please check credentials and try again", "info");
                }
            },
            fail: function (response) {
                alert('here');
            },
            error: function (xhr, textStatus, thrownError) {
                associate_errors(xhr.responseJSON.errors, $form);
            },
            contentType: false,
            processData: false,
        });
    });

    function showDocAlert() {
        swal({
            title: 'Prepare documents',
            text: "Please begin the application process when you\'ve scanned these documents to your PC",
            type: "info",
            showCancelButton: false,
            confirmButtonText: "OK",
            showLoaderOnConfirm: true,
            closeOnConfirm: false
        });
    }
</script>
@endsection
