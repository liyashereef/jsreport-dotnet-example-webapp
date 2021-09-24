@extends('layouts.cgl360_osgc_scheduling_layout')
@section('css')

    <style>
         html, body {
        height: 100%;
        margin: 0;
        font-family: 'Montserrat' !important;
        font-weight: normal;    
        
        }
       
        .icon-success{
            padding: 2rem !important;
            color:green
        }
        
        .help-block{
            font-weight: 100 !important;
            color: #d9534f;
            font-size: 15px;
        }
       
   
        .landing-page-button {
            margin: 0px !important;
            font-family: 'Montserrat' !important;
        }
       
       
    </style>
@endsection

@section('content')
<div class="container" style="width: 45%;">

        
        {{ Form::open(array('url'=>'#','class'=>'password-page','id'=>'form-normal', 'method'=> 'POST')) }}
            {{csrf_field()}}
            <div class="text-center" style="color: #534f4f;font-size: 20px;"><h5>Change Password</h5></div>
            <div class="alert alert-success d-none" id="msg_div">
                    <span id="res_message" style="font-size: 15px;"></span>
                
            </div>
            <div class="form-group" id='old_password'>
                {{Form::label('old_password', 'Old password', array())}}
                {{ Form::password('old_password',array('class'=>'form-control','required'=>TRUE)) }}
                <small class="help-block"></small>
            </div>
            <div class="form-group" id='password'>
                {{Form::label('password', 'New password', array())}}
                {{ Form::password('password',array('class'=>'form-control','required'=>TRUE)) }}
                <small class="help-block"></small>
            </div>
            <div class="form-group" id='password_confirmation'>
            
                {{Form::label('password_confirmation', 'Confirm password', array('class'=>'text-md-left'))}}
                {{ Form::password('password_confirmation',array('class'=>'form-control','required'=>TRUE)) }}
                <small class="help-block"></small>
                
            </div>
            
            
            <div class="text-center">
                {{ Form::submit('Submit', array('class'=>'landing-page-button landing-page-button-yes'))}}
                
            </div>
        {{ Form::close() }}
        
      
    </div>


          
  



@stop
@section('scripts')

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
        url: "{{ url('osgc/update-password') }}",
        dataType: "json",
        type: 'POST',
        data: formData,
        success: function (data) {console.log(data)
            if(data.success)
            {
                $('#res_message').show();
                $('#res_message').html('Your password has been updated successfully');
                $('#msg_div').removeClass('d-none');
                document.getElementById("form-normal").reset(); 
            }else{
                swal("Failed", "Incorrect old password", "info");
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



