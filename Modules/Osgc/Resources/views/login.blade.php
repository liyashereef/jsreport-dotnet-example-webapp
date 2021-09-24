@extends('layouts.cgl360_osgc_scheduling_layout')
@section('content')
<div id="outer-wrapper" class="row min-height-adjust" style="padding:50px 10px 50px 10px;margin:0px">
    
    <div id="img-div" class="col-xs-12 col-sm-12 col-md-7 col-lg-6 col-xl-6" style="margin-top:12%;">
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
        <div id="block2" style="margin-top:25%;">
            {{ Form::open(array('url'=>'#','class'=>'log-page','id'=>'form-normal', 'method'=> 'POST')) }}
            {{csrf_field()}}
            {{ Form::hidden('type','normal') }}
            <div class="form-group" id='email'>
                {{Form::label('email', 'Email Address', array())}}
                {{ Form::text('email',null,array('class'=>'form-control col-md-10','required'=>TRUE,'id'=>'c_email')) }}
                <small class="help-block"></small>
            </div>
            <div class="form-group" id="g_password">
                {{Form::label('password', 'Password', array())}}
                {{ Form::password('password',array('class'=>'form-control col-md-10','required'=>TRUE,'id'=>'c_password')) }}
                <small class="help-block"></small>
                <a href="{{url('osgc/forgot-password')}}" class="forgotpassword hyperlink"  >Forgot Password ?</a>
            </div>
            
            <div class="text-left">
                {{ Form::submit('Sign In', array('class'=>'landing-page-button landing-page-button-yes'))}}
                &nbsp <span style="font-size: 15px;font-weight: 100;color:black">or</span> &nbsp
                <a href="{{url('osgc/registration')}}" class="landing-page-button btn register">Register</a>
            </div>
            {{ Form::close() }}
        </div>
        
        
        
        
    </div>
    
</div>
<style>
body{
    background: white;
    font-family: 'Montserrat' !important;
}

.navbar{
    display: none;
}
.landing-page-button {
    margin: 25px 0px !important;
    font-family: 'Montserrat' !important;
}
ul li label{
    cursor: pointer;
}
.logo-name { 
    position: absolute;
    top: 142px;
    color: #003A63;
    font-size: 24px;
    font-weight: bold;
    left: 196px;
   
}
.forgotpassword{
    font-size: 14px;
    color: black;
    font-weight: 100;
}
a.forgotpassword:hover
{
    color: #54697a;
}
.content-landing-img {
    /* padding: 1rem; */
}

.register
{
    padding: 4px;
    font-size: 17px;
    font-weight: bold;
}
.log-page {
    font-size: 17px !important;
    margin-top: 0%;
    font-weight: bold;
    color: #54697a;
    
    

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
   
</style>
<script>
  

$(function () {

        
        

    $('input[type="submit"]').click(function (e) {
        var $form = $(this).parents('form');
        e.preventDefault();
        //var $form = $(this);
        var formData = new FormData($form[0]);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ url('osgc/check-login-user') }}",
            dataType: "json",
            type: 'POST',
            data: formData,
            success: function (data) {console.log(data)
                if (data.success) {
                    
                        window.location="{{ url('osgc/guard-training') }}";
                        
                    } else {
                        if(data.message=='Account is Not Activated')
                        {
                            swal("Failed", "This account is not active", "info");
                        }else{
                            swal("Wrong inputs", "Please check credentials and try again", "info");
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
@endsection
