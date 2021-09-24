@php
$i=0;
$id= app('request')->input('id');
if ($id>=1)
$i=$id;
@endphp
<div class="reference-container">
    <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12"><div class="pos">Reference {{isset($key) ? $key+1 :"1"}}</div></label>
    <div class="form-group row {{ $errors->has('reference_name.'.$i) ? 'has-error' : '' }}" id="reference_name.{{$i}}">
        <label class="col-sm-5 col-form-label">Name</label>
        <div class="col-sm-7">
            {{Form::text('reference_name[]',old('reference_name.'.$i,isset($references->reference_name) ? $references->reference_name :""),array('class'=>'form-control','placeholder'=>"Name",'required'=>TRUE))}}
            <div class="form-control-feedback">
                {!! $errors->first('reference_name.'.$i, '<small class="help-block">:message</small>') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('reference_employer.'.$i) ? 'has-error' : '' }}" id="reference_employer.{{$i}}">
        <label class="col-sm-5 col-form-label">Employer</label>
        <div class="col-sm-7">
            {{Form::text('reference_employer[]',old('reference_employer.0',isset($references->reference_employer) ? $references->reference_employer :""),array('class'=>'form-control','placeholder'=>"Employer",'required'=>TRUE))}}
            <div class="form-control-feedback">
                {!! $errors->first('reference_employer.'.$i, '<small class="help-block">:message</small>') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('reference_position.'.$i) ? 'has-error' : '' }}" id="reference_position.{{$i}}">
        <label class="col-sm-5 col-form-label">Position</label>
        <div class="col-sm-7">
            {{Form::text('reference_position[]',old('reference_position.'.$i,isset($references->reference_position) ? $references->reference_position :""),array('class'=>'form-control','placeholder'=>"Position",'required'=>TRUE))}}
            <div class="form-control-feedback">
                {!! $errors->first('reference_position.'.$i, '<small class="help-block">:message</small>') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('contact_phone.'.$i) ? 'has-error' : '' }}" id="contact_phone.{{$i}}">
        <label class="col-sm-5 col-form-label">Contact Phone</label>
        <div class="col-sm-7">
            {{Form::text('contact_phone[]',old('contact_phone.'.$i,isset($references->contact_phone) ? $references->contact_phone :""),array('class'=>'phone form-control','placeholder'=>"Please enter in the format (XXX)XXX-XXXX",'required'=>TRUE))}}
            <div class="form-control-feedback">
                {!! $errors->first('contact_phone.'.$i, '<small class="help-block">:message</small>') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('contact_email.'.$i) ? 'has-error' : '' }}" id="contact_email.{{$i}}">
        <label class="col-sm-5 col-form-label">Contact Email</label>
        <div class="col-sm-7">
            {{Form::email('contact_email[]',old('contact_email.'.$i,isset($references->contact_email) ? $references->contact_email :""),array('class'=>'form-control','placeholder'=>"Contact Email",'required'=>TRUE))}}
            <div class="form-control-feedback">
                {!! $errors->first('contact_email.'.$i, '<small class="help-block">:message</small>') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
         @if(isset($key) && $key==$count-1 &&  $key!=0)
        <div class="col-sm-12">
         <a href="javascript:void(0);" class="remove-reference  pull-right"><i class="fa fa-minus" aria-hidden="true"></i> Remove</a>
        </div>
        @else
          <div class="col-sm-12">
         <a href="javascript:void(0);" class="remove-reference hide-this-block pull-right"><i class="fa fa-minus" aria-hidden="true"></i> Remove</a>
        </div>
          @endif
    </div>
</div>
