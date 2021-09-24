@extends('layouts.cgl360_facility_scheduling_layout')
@section('css')
    <style>
        .navbar{
            display: none;
        }

        .facility-login-page .input-field {
            width: 90%;
            color: rgb(38, 50, 56);
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 1px;
            background: rgba(136, 126, 126, 0.04);
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            outline: none;
            box-sizing: border-box;
            border: 2px solid rgba(0, 0, 0, 0.02);
            text-align: center;
            margin: 0px 25px 10px;
        }

        .facility-login-page .error-field {
            width: 93%;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 1px;
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            outline: none;
            box-sizing: border-box;
            margin: 0px 25px;
            text-align: center;
            /* margin-bottom: 27px; */
        }

        .facility-login-page .header-container {
            padding-top: 20px;
            font-weight: bold;
            font-size: 23px;
            text-align: center;
        }

        .login-container {
            background-color: #FFFFFF;
            width: auto;
            max-width: 500px;
            min-width: 320px;
            height: 480px;
            margin: 6em auto;
            border-radius: 1.5em;
            box-shadow: 0px 11px 35px 2px rgba(0, 0, 0, 0.14);
        }

        .facility-login-page #login-form {
            padding-top: 30px;
        }

        .facility-login-page .submit {
            cursor: pointer;
            border-radius: 5em;
            color: #fff;
            background: linear-gradient(to right, #003A63, #003A63);
            border: 0;
            padding: 10px 40px;
            font-size: 16px;
            box-shadow: 0 0 20px 1px rgba(0, 0, 0, 0.04);
        }

        .cgl-logo {
            width: 25%;
            text-align: center;
            margin-top: -55px;
            margin-left: 39%;
            margin-bottom: -35px;
        }

        .facility-login-page .login-btn {
            text-align: center;
        }

        @media (max-width: 500px) {
            .facility-login-page .input-field {
                width: 100%;
                margin: 0px 0px 10px;
            }
        }
    </style>
@endsection
@section('content')
    <section class="container login-container">
        <img class="cgl-logo" src="{{asset('images/cgl-512-circle.png')}}" alt="cgl-360-logo"/>
        <div class="facility-login-page">
            <div class="row">
                <div class="col-md-12 header-container">
                    <h2 class="orange login-header">Facility Login</h2>
                    <hr>
                </div>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'login-form','class'=>'form-horizontal', 'method'=> 'POST')) }}

            {{ csrf_field() }}

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="input-group input-field">
                            <div class="input-group-addon" style="width: 2.6rem"><i class="fa fa-user"></i></div>
                            <input id="log" type="text" placeholder="Username" class="form-control" name="username" value="{{ old('username') }}" required autofocus>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="input-group input-field">
                            <div class="input-group-addon" style="width: 2.6rem"><i class="fa fa-key"></i></div>
                            <input id="password" type="password" placeholder="Password" class="form-control" name="password" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" id="errorblock" style="display: none">
                <div class="col-md-12">
                    <div class="form-group input-group error-field">
                        <span class="has-error" id="errorMsg"> </span>
                    </div>
                </div>
            </div>

            <div class="row login-btn" style="padding-bottom: 1rem">
                <div class="col-md-12">
                    <button type="submit" class="btn submit" value="@lang('Login')"><i class="fa fa-sign-in"></i> Login</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </section>
@endsection
@section('scripts')

    <script>

        /* Login form submission - Start */

        $(function() {
            $('#login-form').submit(function (e) {  //alert();
                e.preventDefault();
                var $form = $(this);
                url = "{{ route('facility.login') }}";
                var formData = new FormData($('#login-form')[0]);
                // console.log(formData);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function (data) {
                        $('#errorMsg').html('');
                        if (data.success) {
                            window.location="{{ route('facility.booking-page') }}";
                        }else{
                            $("#errorblock").css("display","block")
                            $('#errorMsg').html('<i class="fa fa-close"></i> '+ data.message);
                        }
                    },
                    fail: function (response) {
                        console.log('fail');
                        console.log(data);
                    },
                    error: function (xhr, textStatus, thrownError) {
                        console.log('error');
                        $.each(xhr.responseJSON.errors, function (key, value) {
                            $('#errorMsg').html('<i class="fa fa-close"></i> '+ value[0] +'<br>');
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
