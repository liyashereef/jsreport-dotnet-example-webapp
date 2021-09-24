<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.2/croppie.min.css">
<style>
    .croppie-container{
        width: 10% !important;
        margin-top: 1px !important;
        height: auto;
    }
    /* [2] Transition property for smooth transformation of images */
    .candidate-image-div img {
        transition: transform .5s, filter 1.5s ease-in-out;
    }

    /* [3] Finally, transforming the image when container gets hovered */
    .candidate-image-div:hover img {
        z-index: 9999999;
        transform:scale(5);
        -ms-transform:scale(5); /* IE 9 */
        -moz-transform:scale(5); /* Firefox */
        -webkit-transform:scale(5); /* Safari and Chrome */
        -o-transform:scale(5); /* Opera */
        position: relative;
    }
</style>

<div class="form-group row {{ $errors->has('first_name') ? 'has-error' : '' }}"  id="first_name">
    <label for="first_name" class="col-sm-5 col-form-label">First Name</label>
    <div class="col-sm-7">
        {{Form::text('first_name',old('first_name',isset($candidate->first_name) ? $candidate->first_name : (isset($candidate->name) ? $candidate->name:'')),array('class'=>'form-control','required'=>TRUE,'pattern'=>"[A-Z a-z\s.\-]+"))}}
        <div class="form-control-feedback">{!! $errors->first('first_name') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div class="form-group row {{ $errors->has('last_name') ? 'has-error' : '' }}"  id="last_name">
    <label for="last_name" class="col-sm-5 col-form-label">Last Name</label>
    <div class="col-sm-7">
        {{Form::text('last_name',old('last_name',isset($candidate->last_name) ? $candidate->last_name :""),array('class'=>'form-control','required'=>TRUE,'pattern'=>"[A-Z a-z\s.\-]+"))}}
        <div class="form-control-feedback">{!! $errors->first('last_name') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div class="form-group row {{ $errors->has('dob') ? 'has-error' : '' }}" id="dob">
    <label for="dob" class="col-sm-5 col-form-label">Date of Birth</label>
    <div class="col-sm-7">
        {{Form::text('dob',old('dob',isset($candidate->dob) ? $candidate->dob :""),array('class'=>'form-control datepicker','required'=>TRUE))}}
        <div class="form-control-feedback">
            {!! $errors->first('dob') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div class="form-group row {{ $errors->has('phone') ? 'has-error' : '' }}" id="phone">
    <label for="phone" class="col-sm-5 col-form-label">Home Phone Number</label>
    <div class="col-sm-7">
        {{Form::text('phone',old('phone',isset($candidate->phone) ? $candidate->phone :""),array('class'=>'phone form-control','placeholder'=>"Please enter in the  format (XXX)XXX-XXXX. If you don't have one - enter your cell number here."))}}
        <div class="form-control-feedback">{!! $errors->first('phone') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div class="form-group row {{ $errors->has('phone_cellular') ? 'has-error' : '' }}" id="phone_cellular">
    <label for="phone_cellular" class="col-sm-5 col-form-label">Cellular Phone Number</label>
    <div class="col-sm-7">
        {{Form::text('phone_cellular',old('phone_cellular',isset($candidate->phone_cellular) ? $candidate->phone_cellular :""),array('class'=>'phone form-control','placeholder'=>"Please enter in the  format (XXX)XXX-XXXX. If you don't have one, please leave blank."))}}
        <div class="form-control-feedback">{!! $errors->first('phone_cellular') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div class="form-group row {{ $errors->has('email') ? 'has-error' : '' }}" id="email">
    <label for="email" class="col-sm-5 col-form-label">Email</label>
    <div class="col-sm-7">
          {{Form::email('email',old('email',isset($candidate->email) ? $candidate->email :""),array('class'=>'form-control mail','required'=>TRUE,'maxlength'=>255))}}
        <div class="form-control-feedback">{!! $errors->first('email') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>

<div class="form-group row" id="candidate-image" style="padding-top:20px;padding-bottom:10px;">
    <label for="image" class="col-sm-5 col-form-label">Upload Profile Picture<span class="mandatory">*</span><br />
        <span style="color: red !important;font-size: 14px;">Please upload a selfie from your smart phone. Your image will be provided electronically along with your candidate profile to our clients for potential opportunities.</span><br />
        <span>&nbsp;</span><br />
        <span style="color: red !important;font-size: 14px;padding-top:2px;">Please make sure that your face is clearly visible. Failure to complete this step as instructed will limit your placement opportunities</span>
    </label>
    <div class="col-sm-4">
    {!! Form::file('profile_image', array('class' => 'candidate-image-element', 'style' => 'padding-top: 30px;')) !!}
    </div>
    <div class="col-sm-3">
        <div id="candidate-image-div" class="candidate-image-div" style="width:10% !important;">
            @if(isset($candidate->profile_image))
                <img style="border-radius: 50%;" src="{{asset('images/uploads/') }}/{{ $candidate->profile_image }}" data-status="1" height="100px" width="100px" name="image" id="image-element"/>
            @else
                <img style="border-radius: 50%;" src="{{asset('images/uploads/') }}/{{ config('globals.noAvatarImg') }}" data-status="0" height="100px" width="100px" name="image" id="image-element"/>
            @endif
        </div>
        <div class="candidate-image-upload" style="display: none;"></div>
    </div>
</div>

<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Current Address Details</label>
<div class="form-group row {{ $errors->has('address') ? 'has-error' : '' }}" id="address">
    <label for="address" class="col-sm-5 col-form-label">Apartment Number/Street Address</label>
    <div class="col-sm-7">
        {{Form::text('address',old('address',isset($candidate->address) ? $candidate->address :""),array('class'=>'form-control','placeholder'=>"If you have an apartment number, please enter the unit number first, then address",'required'=>TRUE,'maxlength'=>255))}}
        <div class="form-control-feedback">{!! $errors->first('address') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div class="form-group row {{ $errors->has('city') ? 'has-error' : '' }}" id="city">
    <label for="city" class="col-sm-5 col-form-label">City</label>
    <div class="col-sm-7">
        {{Form::text('city',old('city',isset($candidate->city) ? $candidate->city :""),array('class'=>'form-control','placeholder'=>"Please enter the city where you currently reside",'required'=>TRUE,'maxlength'=>255))}}
        <div class="form-control-feedback">{!! $errors->first('city') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div class="form-group row {{ $errors->has('postal_code') ? 'has-error' : '' }}" id="postal_code">
    <label for="postal_code" class="col-sm-5 col-form-label">Postal Code</label>
    <div class="col-sm-7">
        {{Form::text('postal_code',old('postal_code',isset($candidate->postal_code) ? $candidate->postal_code :""),array('class'=>'form-control postal-code','placeholder'=>"Please enter the postal code where you currently reside (6 characters)",'pattern'=>"(.){6,6}",'required'=>TRUE,'maxlength'=>6))}}
        <div class="form-control-feedback">{!! $errors->first('postal_code') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div class="form-group row">
    <label class="col-sm-5 col-form-label">Past address over the last five years (Required for RCMP background check)</label>
    <div class="col-sm-7">
        <a href="javascript:void(0);" class="add-previous-adresses"><i class="fa fa-plus" aria-hidden="true"></i>Add Address</a>
    </div>
</div>
@if ( isset($candidate) && Auth::user()!=null)
@foreach($candidate->addresses as $addres)
@include('recruitment::job-application.partials.profile.previous-address', array('addres' => $addres))
@endforeach
@endif

<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.2/croppie.js"></script>
<script>
    var resize = $('.candidate-image-upload').croppie({
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

    $('.candidate-image-element').on('change', function () {
        $('.candidate-image-upload').hide();
        $('.candidate-image-div').show();

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
                $('.candidate-image-upload').show();
                $('.candidate-image-div').hide();
            } else {
                $('.candidate-image-upload').hide();
                $('.candidate-image-div').show();
                $('.candidate-image-element').val('');
                swal("Error", "Accept only jpg or png images", "error");
            }
        }

        if(this.files[0]) {
                reader.readAsDataURL(this.files[0]);
        }else{
            $('.candidate-image-element').val('');
        }
    });

    $('.candidate-image-upload').on('dblclick', function() {
        $('.candidate-image-element').trigger('click');
    });

    $('.candidate-image-div').on('click', function() {
        $('.candidate-image-element').trigger('click');
    });
</script>
