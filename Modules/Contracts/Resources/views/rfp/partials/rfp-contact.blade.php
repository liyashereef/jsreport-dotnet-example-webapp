<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 orange">
    <h5>RFP Contact</h5>
</label>
<div class="form-group row  {{ $errors->has('rfp_contact_name') ? 'has-error' : '' }}" id="rfp_site_name">
    <label for="rfp_contact_name" class="col-sm-5 col-form-label">Name</label>
    <div class="col-sm-6">
        {{ Form::text('rfp_contact_name',
                old('rfp_contact_name',isset($rfpDetails->rfp_contact_name) ? $rfpDetails->rfp_contact_name :""),
                array('placeholder'=>'Name','class'=>'form-control', 'required' => true)) }}
        <div class="form-control-feedback">
            <span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('rfp_contact_name', ':message') !!}
        </div>
    </div>
</div>
<div class="form-group row  {{ $errors->has('rfp_contact_title_available') ? 'has-error' : '' }}"
     id="rfp_contact_title_available">
    <label for="rfp_contact_title_available" class="col-sm-5 col-form-label">Has the person's contact title been listed in the RFP?</label>
    <div class="col-sm-6">
        {{ Form::select(
            'rfp_contact_title_available',
            [null=>'Please Select','1'=>'Yes','0'=>'No'],
            old('rfp_contact_title_available',isset($rfpDetails->rfp_contact_title_available) ? $rfpDetails->rfp_contact_title_available :""),
            array(
                'id'=> 'rfp_contact_title_available_control',
                'class'=> 'form-control',
                'required'=>true
            )
        ) }}
        {{ Form::Hidden('rfp_contact_title_hidden',old('rfp_contact_title',isset($rfpDetails->rfp_contact_title) ? $rfpDetails->rfp_contact_title :""),
        array('placeholder'=>'Title')) }}
        <div class="form-control-feedback">
            <span class="help-block text-danger align-middle font-12"></span>
            {!! $errors->first('rfp_contact_title_available', ':message') !!}
        </div>
    </div>
</div>
<div class="form-group row  {{ $errors->has('rfp_contact_title') ? 'has-error' : '' }}" id="rfp_contact_title">
    <label for="rfp_contact_title" class="col-sm-5 col-form-label">Title<span class="mandatory">*</span></label>
    <div class="col-sm-6">
        {{ Form::text('rfp_contact_title',old('rfp_contact_title',isset($rfpDetails->rfp_contact_title) ? $rfpDetails->rfp_contact_title :""),
                array('placeholder'=>'Title','class'=> 'form-control','id'=>'rfp_contact_title_id')) }}
        <div class="form-control-feedback">
            <span class="help-block text-danger align-middle font-12"></span>
            {!! $errors->first('rfp_contact_title', ':message') !!}
        </div>
    </div>
</div>
<div class="form-group row  {{ $errors->has('rfp_contact_address_available') ? 'has-error' : '' }}"
     id="rfp_contact_address_available">
    <label for="rfp_contact_address_available" class="col-sm-5 col-form-label">Has the address been provided?</label>
    <div class="col-sm-6">
        {{ Form::select(
            'rfp_contact_address_available',
            [null=>'Please Select','1'=>'Yes','0'=>'No'],
            old('rfp_contact_address_available',isset($rfpDetails->rfp_contact_address_available) ? $rfpDetails->rfp_contact_address_available :""),
            array(
                'id'=> 'rfp_contact_address_available_control',
                'class'=> 'form-control',
                'required'=>true
            )
        ) }}
        {{ Form::Hidden('rfp_contact_address_hidden',old('rfp_contact_address',isset($rfpDetails->rfp_contact_address) ? $rfpDetails->rfp_contact_address :""),
        array()) }}
        <div class="form-control-feedback">
            <span class="help-block text-danger align-middle font-12"></span>
            {!! $errors->first('rfp_contact_address_available', ':message') !!}
        </div>
    </div>
</div>
<div class="form-group row  {{ $errors->has('rfp_contact_address') ? 'has-error' : '' }}" id="rfp_contact_address">
    <label for="rfp_contact_address" class="col-sm-5 col-form-label">Address<span class="mandatory">*</span></label>
    <div class="col-sm-6">
        {{ Form::text('rfp_contact_address',old('rfp_contact_address',isset($rfpDetails->rfp_contact_address) ? $rfpDetails->rfp_contact_address :""),
                array('placeholder'=>'Address','class'=> 'form-control','id'=>'rfp_contact_address_id')) }}
        <div class="form-control-feedback">
            <span class="help-block text-danger align-middle font-12"></span>
            {!! $errors->first('rfp_contact_address', ':message') !!}
        </div>
    </div>
</div>
<div class="form-group row  {{ $errors->has('rfp_phone_number_available') ? 'has-error' : '' }}"
     id="rfp_phone_number_available">
    <label for="rfp_phone_number_available" class="col-sm-5 col-form-label">Has the phone number been provided?</label>
    <div class="col-sm-6">
        {{ Form::select(
            'rfp_phone_number_available',
            [null=>'Please Select','1'=>'Yes','0'=>'No'],
            old('rfp_phone_number_available',isset($rfpDetails->rfp_phone_number_available) ? $rfpDetails->rfp_phone_number_available :""),
            array(
                'id'=> 'rfp_phone_number_available_control',
                'class'=> 'form-control',
                'required'=>true
            )
        ) }}
        {{ Form::Hidden('rfp_phone_number_hidden',old('rfp_phone_number',isset($rfpDetails->rfp_phone_number) ? $rfpDetails->rfp_phone_number :""),
        array()) }}
        <div class="form-control-feedback">
            <span class="help-block text-danger align-middle font-12"></span>
            {!! $errors->first('rfp_phone_number_available', ':message') !!}
        </div>
    </div>
</div>
<div class="form-group row  {{ $errors->has('rfp_phone_number') ? 'has-error' : '' }}" id="rfp_phone_number">
    <label for="rfp_phone_number" class="col-sm-5 col-form-label">Phone Number<span class="mandatory">*</span></label>
    <div class="col-sm-6">
        {{ Form::text('rfp_phone_number',old('rfp_phone_number',isset($rfpDetails->rfp_phone_number) ? $rfpDetails->rfp_phone_number :""),
                array('placeholder'=>'Phone Number','class'=>'form-control phone-w-s','id'=>'rfp_phone_number_id')) }}
        <div class="form-control-feedback">
            <span class="help-block text-danger align-middle font-12"></span>
            {!! $errors->first('rfp_phone_number', ':message') !!}
        </div>
    </div>
</div>
<div class="form-group row  {{ $errors->has('rfp_email_available') ? 'has-error' : '' }}"
     id="rfp_email_available">
    <label for="rfp_email_available" class="col-sm-5 col-form-label">Has the email been provided?</label>
    <div class="col-sm-6">
        {{ Form::select(
            'rfp_email_available',
            [null=>'Please Select','1'=>'Yes','0'=>'No'],
            old('rfp_email_available',isset($rfpDetails->rfp_email_available) ? $rfpDetails->rfp_email_available :""),
            array(
                'id'=> 'rfp_email_available_control',
                'class'=> 'form-control',
                'required'=>true
            )
        ) }}

        {{ Form::Hidden('rfp_email_hidden',old('rfp_email',isset($rfpDetails->rfp_email) ? $rfpDetails->rfp_email :""),
        array('placeholder'=>'Email','class'=>'form-control')) }}
        <div class="form-control-feedback">
            <span class="help-block text-danger align-middle font-12"></span>
            {!! $errors->first('rfp_email_available', ':message') !!}
        </div>
    </div>
</div>
<div class="form-group row  {{ $errors->has('rfp_email') ? 'has-error' : '' }}" id="rfp_email">
    <label for="rfp_email" class="col-sm-5 col-form-label">Email<span class="mandatory">*</span></label>
    <div class="col-sm-6">
        {{ Form::text('rfp_email',old('rfp_email',isset($rfpDetails->rfp_email) ? $rfpDetails->rfp_email :""),
                array('placeholder'=>'Email','class'=>'form-control','id'=>'rfp_email_id')) }}
        <div class="form-control-feedback">
            <span class="help-block text-danger align-middle font-12"></span>
            {!! $errors->first('rfp_email', ':message') !!}
        </div>
    </div>
</div>
