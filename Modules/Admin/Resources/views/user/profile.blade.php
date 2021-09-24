@extends('layouts.app')
@section('content_header')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@stop
@section('content')
<style>
    .croppie-container{
        width: 10% !important;
        margin-top: 5px !important;
    }

    .profile-image-div img {
        transition: transform .5s, filter 1.5s ease-in-out;
    }

    /* [3] Finally, transforming the image when container gets hovered */
    .profile-image-div:hover img {
        z-index: 9999999;
        transform:scale(1.4);
        -ms-transform:scale(1.4); /* IE 9 */
        -moz-transform:scale(1.4); /* Firefox */
        -webkit-transform:scale(1.4); /* Safari and Chrome */
        -o-transform:scale(1.4); /* Opera */
        position: relative;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.2/croppie.min.css">
<div class="table_title">
    <h4>Edit Profile</h4>
</div>
{!! session('profile-updated') !!}
<div class="box box-primary padding-15">
    <form role="form" id="profile-form" method="POST" action="{{ route('profile.updateProfile',$user->id) }}" enctype="multipart/form-data">
        {{ csrf_field() }}

        <div class="box-body">
            <div class="box-body form-group profile-image-div">
                <img style="border-radius: 50%;" src="{{asset('images/uploads/') }}/{{ $user->employee_profile->image ?? config('globals.noAvatarImg') }}" height="100px" width="100px" name="image" id="profile-img"/>
                <div id="upload-demo" style="display: none;"></div>
            </div>

            <div class="form-group">
                <input type="file" name="file" id="file" style="display: none;">
                <input type="hidden" name="id" value="0">
            </div>

                <div class="form-group {{ $errors->has('first_name') ? 'has-error' : '' }}">
                <label for="name">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $user->first_name }}" readonly >
                <small class="help-block">
                    {{ ucfirst(str_replace('The name', 'First Name', $errors->first('first_name'))) }}
                </small>
                </div>

                <div class="form-group {{ $errors->has('last_name') ? 'has-error' : '' }}">
                <label for="name">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $user->last_name }}" readonly >
                <small class="help-block">
                    {{ ucfirst(str_replace('The name', 'Last Name', $errors->first('last_name'))) }}
                </small>
                </div>

                <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                <label for="email">Email</label>
                <input type="text" class="form-control" id="email" name="email" value="{{ $user->email }}" readonly>
                <small class="help-block">
                    {{ ucfirst(str_replace('The email', 'Email', $errors->first('email'))) }}
                </small>
                </div>

                <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                <label for="phone">Phone</label>
                <input type="text" class="form-control phone" id="phone" name="phone" placeholder="Phone [ format (XXX)XXX-XXXX ]" pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" value="{{$user->employee_profile->phone}}" readonly>
                <small class="help-block">
                    {{ ucfirst(str_replace('The phone', 'Phone', $errors->first('phone'))) }}
                </small>
                </div>

                <div class="form-group {{ $errors->has('username') ? 'has-error' : '' }}">
                <label for="name">User Name</label>
                <input type="text" class="form-control" id="username" name="username" value="{{ $user->username }}" readonly>
                <small class="help-block">
                    {{ ucfirst(str_replace('The username', 'Username', $errors->first('username'))) }}
                </small>
                </div>

                <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                <label for="client_name">New Password</label>
                <input type="password" class="form-control" id="password" name="password" value="{{ old('password') }}">
                <small class="help-block">
                {{ ucfirst(str_replace('The password', 'Password', $errors->first('password'))) }}
                </small>
                </div>

                <div class="form-group {{ $errors->has('password_confirmation') ? 'has-error' : '' }}">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" value="{{ old('password_confirmation') }}">
                <small class="help-block">
                {{ ucfirst(str_replace('The password', 'Password', $errors->first('password_confirmation'))) }}
                </small>
                </div>
        </div>
        <div class="box-footer">
            <button type="button" class="btn btn-primary blue" id="save-profile">Save</button>
            <button type="button" class="btn btn-primary blue" id="profile-cancel">Cancel</button>
        </div>

    </form>
</div>
@stop
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.2/croppie.js"></script>
<script>
var resize = $('#upload-demo').croppie({
        enableExif: true,
        enableOrientation: true,
        viewport: { // Default { width: 100, height: 100, type: 'square' }
            width: 100,
            height: 100,
            type: 'circle' //square
        },
        boundary: {
            width: 130,
            height: 130
        }
    });

    $('#file').on('change', function () {
        $('#upload-demo').hide();
        $('#profile-img').show();

        var reader = new FileReader();
        reader.onload = function (e) {
            result = e.target.result;
            arrTarget = result.split(';');
            imageType = arrTarget[0];
            if (imageType == 'data:image/jpg' || imageType == 'data:image/jpeg' || imageType == 'data:image/png') {
                resize.croppie('bind',{
                    url: e.target.result
                }).then(function(){
                    console.log('jQuery bind complete');
                });
                $('#upload-demo').show();
                $('#profile-img').hide();
            } else {
                $('#upload-demo').hide();
                $('#profile-img').show();
                $('#file').val('');
                swal("Error", "Accept only jpg or png images", "error");
            }
        }

        if(this.files[0]) {
                reader.readAsDataURL(this.files[0]);
        }else{
            $('#file').val('');
        }
    });

    $("#profile-cancel").click(function(){
        $('#profile-img').attr('src', '{{asset('images/uploads/') }}/{{ $user->employee_profile->image ?? config('globals.noAvatarImg') }}');
    });

    /*To accept phone number in a specific format*/
    $(".phone").mask("(999)999-9999");

    $('#save-profile').on('click', function (ev) {
        let userId = $('#id').val();
        let image = $('#file').val();
        resize.croppie('result', {
            type: 'canvas',
            size: {width:512, height:512},
            quality: 1,
            circle: false
        }).then(function (img) {
            console.log(image);
            if(image == "" || image == null || image == 'null') {
                img = null;
            }
            $.ajax({
            url: "{{ route('profile.updateProfile',$user->id) }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                "image":img,
                "id":userId,
                "first_name": $('#first_name').val(),
                "last_name": $('#last_name').val(),
                "email": $('#email').val(),
                "phone": $('#phone').val(),
                "username": $('#username').val(),
                "password": $('#password').val(),
                "password_confirmation": $('#password_confirmation').val()
                },
            success: function (data) {
                if(data.status) {
                    if(image != "" && image != null && image != 'null') {
                        $('#upload-demo').hide();
                        $('#profile-img').show();
                        $("#profile-img").attr('src', img);
                    }
                    swal("Success", "Profile updated successfully", "success");
                }else{
                    swal("Error", "Failed to update profile", "error");
                }
            },
            error: function (xhr) {
                let err = '';
                let i = 0;
                $.each(xhr.responseJSON.errors, function(key,value) {
                    err +=(i > 0)? ", ":"" + value;
                    i++;
                });
                swal("Error", err, "error");
            }
            });
        });
    });


    $('#upload-demo').on('dblclick', function() {
        $('#file').trigger('click');
    });

    $('#profile-img').on('click', function() {
        $('#file').trigger('click');
    });
</script>
@stop
