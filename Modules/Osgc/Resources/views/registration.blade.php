@extends('layouts.cgl360_osgc_scheduling_layout')
@section('css')
    <style>
        .navbar{
            display: none;
        }

        html, body {
            font-family: 'Montserrat' !important;
            background: white;
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
        .landing-page-button {
            font-family: 'Montserrat' !important;
        }
        .err-validation
        {
            color: #c00;
            
    
        }
        .err {
            border: 1px solid #c00;
        }
        

        
        
        .input-group .form-control {
                width: 100%;
               
            }
        .radio-group {
            float: left;
            clear: none;
            width: 100%;
            padding: 3px;
        }
    
        label {
        float: left;
        clear: none;
        display: block;
        padding: 4px 1em 0px 8px;
        }
        
        input[type=radio],
        input.radio {
        float: left;
        clear: none;
        margin: 10px 0px 0px 2px;
        }
        .logo-name { 
            position: absolute;
            top: 116px;
            color: #003A63;
            font-size: 24px;
            font-weight: bold;
            left: 270px;
        
        }
        .candidate-landing-img
        {
            width: 58% !important;
        }
        @media (max-width: 500px) {
          
            .input-group .form-control {
                width: 100%;
            }
        }
    </style>
@endsection
@section('content')

<div id="outer-wrapper" class="row min-height-adjust" style="margin:0;background: white;">
    
    <div id="img-div" class="col-xs-12 col-sm-12 col-md-5 col-lg-6 col-xl-6" style="margin-top:13%;">
        <div class="container_fluid">
            <div class="row">
                <div class="col-md-12" style="height: 140px">
                    <img class="content-landing-img"   src="{{asset('images/CGL-LOGO-600px-152px.png')}}" />
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 table_title" style="text-align: center"><h4>                    OSGC Registration
                </h4></div>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
        <div id="block2" style="margin-top:17%;">
            {{ Form::open(array('url'=>'#','class'=>'applyjob-page1','id'=>'registration-form', 'method'=> 'POST')) }}
            {{csrf_field()}}
            <div class="col-md-12">
                <div class="alert alert-success d-none col-md-11" id="msg_div">
                    <span id="res_message"></span>
                </div>
            </div>
            <div class="col-md-12">
            <div class="form-group">
                <div class="row">
                    {{Form::label('first_name', 'First Name', array('class'=>'text-md-left col-md-4'))}}
                    <div class="col-md-8">
                    {{ Form::text('first_name',null,array('class'=>'form-control col-md-10','id'=>'first_name')) }}
                    <span id="first_name_error" class="err-validation"></span>
                    </div>
                </div>
            </div>
            </div>
            <div class="col-md-12">
            <div class="form-group">
                <div class="row">
                    {{Form::label('last_name', 'Last Name', array('class'=>'text-md-left col-md-4'))}}
                    <div class="col-md-8">
                    {{ Form::text('last_name',null,array('class'=>'form-control col-md-10','id'=>'last_name')) }}
                    <span id="last_name_error" class="err-validation"></span>
                    </div>
                </div>
            </div>
            </div>
            <div class="col-md-12">
            <div class="form-group">
                <div class="row">
                    {{Form::label('email', 'Email Address', array('class'=>'text-md-left col-md-4'))}}
                    <div class="col-md-8">
                    {{ Form::text('email',null,array('class'=>'form-control col-md-10','id'=>'email')) }}
                    <span id="email_error" class="err-validation"></span>
                    </div>
                </div>
            </div>
            </div>
            <div class="col-md-12">
            <div class="form-group">
                <div class="row">
                    {{Form::label('password', 'Password', array('class'=>'text-md-left col-md-4'))}}
                    <div class="col-md-8">
                    {{ Form::password('password',array('class'=>'form-control col-md-10','id'=>'password')) }}
                    <span id="password_error" class="err-validation"></span>
                    </div>
                </div>
            </div>
            </div>
            <div class="col-md-12">
            <div class="form-group">
                <div class="row">
                    {{Form::label('password_confirmation', 'Confirm Password', array('class'=>'text-md-left col-md-4'))}}
                    <div class="col-md-8">
                    {{ Form::password('password_confirmation',array('class'=>'form-control col-md-10','id'=>'password_confirmation')) }}
                    <span id="password_confirmation_error" class="err-validation"></span>
                    </div>
                </div>
            </div>
            </div>
            <div class="col-md-12">
            <div class="form-group">
                <div class="row">
                    {{Form::label('is_veteran', 'Are you veteran?', array('class'=>'text-md-left col-md-4'))}}
                    <div class="col-md-8">
                    <div class="radio-group">
                        <input type="radio" class="radio" name="is_veteran" value="1" id="y" />
                        <label for="y">Yes</label>
                        <input type="radio" class="radio" name="is_veteran" value="0" id="z" />
                        <label for="z">No</label>
                    </div>
                    <span id="is_veteran_error" class="err-validation"></span>
                    </div>
                </div>
            </div>
            </div>
            <div class="col-md-12">
            <div class="form-group">
                <div class="row">
                    {{Form::label('indian_status', 'Are you of aboriginal descent?', array('class'=>'text-md-left col-md-4'))}}
                    <div class="col-md-8">
                    <select  name="indian_status"   class="form-control col-md-10"  id="indian_status">
                            <option value="">Select</option>
                            <option value="1">Yes</option>
                            <option value="2">No</option>
                            
                    </select>
                    <span id="indian_status_error" class="err-validation"></span>
                    </div>
                </div>
            </div>
            </div>
            <div class="col-md-12">
            <div class="form-group">
                <div class="row">
                    {{Form::label('referral', 'How did you hear about us?', array('class'=>'text-md-left col-md-4'))}}
                    <div class="col-md-8">
                    <select  name="referral"   class="form-control col-md-10"  id="referral">
                            <option value="">Select</option>
                            @foreach($referralArr as $key=>$value)
                                <option value="{{$key}}">{{$value}}</option>
                            @endforeach
                            
                    </select>
                    <span id="referral_error" class="err-validation"></span>
                    </div>
                </div>
            </div>
            </div>
            <div class="text-left">
                {{ Form::submit('Submit', array('class'=>'landing-page-button landing-page-button-yes'))}}
            </div>
            {{ Form::close() }}
        </div>
        
        
        
        
    </div>
    
</div>

@endsection
@section('scripts')

    <script>

        /* Login form submission - Start */

        $(function() {
            $('#registration-form').submit(function (e) {  //alert();
                e.preventDefault();
                var $form = $(this);
                url = "{{ url('osgc/add-user') }}";
                var formData = new FormData($('#registration-form')[0]);
                // console.log(formData);
                
                $(".err-validation").text('');  
                $(".form-control").removeClass('err');  
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function (data) {
                        $('#res_message').show();
                        //$('#res_message').html('User added successfully');
                        $('#res_message').html('User added successfully and check your email to activate your account');
                        $('#msg_div').removeClass('d-none');
                        document.getElementById("registration-form").reset(); 
                        // setTimeout(function(){
                        // $('#res_message').hide();
                        // $('#msg_div').hide();
                        // },1000);
                    },
                    fail: function (response) {
                        console.log('fail');
                        console.log(data);
                    },
                    error: function (xhr, textStatus, thrownError) {
                        // console.log('error');
                        $.each(xhr.responseJSON.errors, function (key, value) {
                            $("#" + key + "_error").text(value[0]);  
                            $("#" + key).addClass('err');   
                        })
                    },
                    contentType: false,
                    processData: false,
                });
            });
        });

        /* Login form submission - End */

    </script>
@endsection
