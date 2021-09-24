@extends('layouts.cgl360_osgc_scheduling_layout')
@section('css')
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU"
        crossorigin="anonymous">
    <style>
         html, body {
        height: 100%;
        margin: 0;
        font-family: 'Montserrat' !important;
        background: white;    
        }
        .navbar{
            display: none;
        }
        .icon-success{
            padding: 2rem !important;
            color:green
        }
        body{
            background: white;
        }
        .log-page {
            font-size: 20px !important;
            margin-top: 0%;
            font-weight: bold;
            color: #54697a;
            
            

        }
   
        .landing-page-button {
            margin: 0px !important;
            font-family: 'Montserrat' !important;
        }
        .content-landing-img {
            display: block;
            margin-left: auto;
            margin-right: auto;
            position: absolute;
            overflow: hidden;
            margin-top: 0px;
            margin-bottom: 50px;
            height: 152px;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-size: cover;
        }
        .table_title h4 {
           
            font-family: Montserrat;
            font-weight: bold;
            font-size: 18pt;
            color: rgb(51,63,80);
        }
        .msg{
            font-size: 17px;
            font-weight: 300;
        }
    </style>
@endsection

@section('content')
<div id="outer-wrapper" class="row min-height-adjust" style="padding:50px 10px 50px 10px;margin:0px">
    
    <div id="img-div" class="col-xs-12 col-sm-12 col-md-7 col-lg-6 col-xl-6" style="margin-top:13%;">
        <div class="container_fluid">
            <div class="row">
                <div class="col-md-12" style="height: 140px">
                    <img class="content-landing-img"   src="{{asset('images/CGL-LOGO-600px-152px.png')}}" />
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 table_title" style="text-align: center"><h4>                    Ontario Security Guard Course
    </h4></div>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-5 col-lg-6 col-xl-6">
        <div id="block2" style="margin-top:28%;">
            {{ Form::open(array('url'=>'#','class'=>'log-page','id'=>'form-normal', 'method'=> 'POST')) }}
            {{csrf_field()}}
            {{ Form::hidden('type','normal') }}
            <p style="font-weight: bold;color:black;font-size:17px">Forgot Password?</p>
            <div class="form-group">
                <div class="alert alert-success d-none" id="msg_div">
                    <span id="res_message" class="msg"></span>
                </div>
            </div>
            <div class="form-group" id='email'>
                <span style="font-weight: 100;color:black;font-size:15px">Enter your registered email address below to reset your password</span>
                {{ Form::text('email',null,array('class'=>'form-control col-md-10','required'=>TRUE,'id'=>'email_id','placeholder'=>'Email Address')) }}
                <small class="help-block"></small>
            </div>
            
            
            <div class="text-left">
                {{ Form::submit('Submit', array('class'=>'landing-page-button landing-page-button-yes'))}}
            </div>
            {{ Form::close() }}
        </div>
        
        
        
        
    </div>
    
</div>
    
  



@stop
@section('scripts')

<script>
function validateEmail(email) {
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    return emailReg.test( email );
}
$(function () {

        
        

$('input[type="submit"]').click(function (e) {
    var $form = $(this).parents('form');
    e.preventDefault();
    $('#res_message').hide();
    $('#res_message').html('');
    var formData = new FormData($form[0]);
    var email=$('#email_id').val();
    if(email =='')
    {
        swal("Wrong inputs", "Email Address is Required", "info");
        return false;
    }else{
        if( !validateEmail(email) ) 
        {
            swal("Wrong inputs", "Enter Valid Email Address", "info");
            return false; 
        }
        
    }
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ url('osgc/reset-password') }}",
        dataType: "json",
        type: 'POST',
        data: formData,
        success: function (data) {console.log(data)
            if(data.success)
            {
                $('#res_message').show();
                $('#res_message').html('Your password has been reset and the same is sent to your registered mail id');
                $('#msg_div').removeClass('d-none');
                document.getElementById("form-normal").reset(); 
            }else{
                $('#msg_div').hide();
                if(data.message)
                {
                    swal("Failed", data.message, "info");
                }else{
                    swal("Failed", "Account does not exist. Please use your registered email address to reset your password", "info");
                }
               
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

});   
</script>
    
@stop



