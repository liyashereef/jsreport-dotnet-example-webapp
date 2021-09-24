@extends('layouts.content-manager-layout')
@section('content')
<div id="outer-wrapper" class="row min-height-adjust" style="padding:50px 10px 50px 10px;">
    <div id="img-div" class="col-xs-12 col-sm-12 col-md-7 col-lg-7 col-xl-7" style="margin-top:13%;">
        <div class="container_fluid">
            <div class="row">
                <div class="col-md-12" style="height: 160px">
                    <img class="content-landing-img"   src="{{asset('images/CGL-LOGO-600px-152px.png')}}" />
                    
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 table_title" style="text-align: center"><h4>                    Content Manager
                </h4></div>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5 col-xl-5">
        <div id="block2" style="margin-top:35%;">
            {{ Form::open(array('class'=>'videos-page','id'=>'form-normal', 'method'=> 'POST')) }}
            {{csrf_field()}}
            {{ Form::hidden('type','normal') }}
            <div class="form-group" id='content_id'>
                {{Form::label('content_id', 'Enter Content ID', array())}}
                
                {{ Form::text('content_id',null,array('class'=>'form-control col-md-8','required'=>TRUE,'id'=>'c_content_id','style'=>'margin-top:18px;')) }}
            </div>
          {{-- <div class="form-group" id="g_password">
                {{Form::label('g_password', 'Password', array())}}
                {{ Form::password('g_password',array('class'=>'form-control','required'=>TRUE,'id'=>'c_g_password')) }}
            </div> --}} 
            <div class="form-group">   
                
                <input type="submit" id="content-submit-btn" class="content-submit-btn" value="Submit" />
            </div>
            {{ Form::close() }}
        </div>

    </div>
    <!--<div class="apply-job">COMMISSIONAIRES GREAT LAKES<br><span class="apply-span"> Candidate Tracking System (CTS)</span></div>-->
</div>
<style>
    body {
    background: #ffffff !important;
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
.content-submit-btn {
    width: 30%;
    border-radius: 5px;
    box-shadow: none;
    /*background: #E4E432;*/
    background: #ea660f;
    font-weight: bold;
    margin-top: 18px;
    cursor: pointer;
    color: #ffffff;
    border: 0;
    padding: 5px 0px;
    font-family: 'Montserrat', sans-serif !important;
}
.table_title h4{
    font-size: 18pt !important
}
</style>
<script>


    $(function () {



    });

    $('#content-submit-btn').click(function (e) {
        e.preventDefault();
        var $form = $(this).parents('form');
        //var $form = $(this);
        var formData = new FormData($form[0]);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{route("content-manager.validatelogin")}}",
            type: 'POST',
            data: formData,
            success: function (data) {
                if (data.success) {
                    let vKey=$("#c_content_id").val()
                    var sites = {!! json_encode(route("content-manager.listcontentvideos",["key"])) !!};
                    sites =sites.replace("key", vKey);  
                    location.href = sites


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
