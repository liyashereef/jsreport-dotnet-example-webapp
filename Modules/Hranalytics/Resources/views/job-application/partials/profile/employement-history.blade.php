@php
$i=0;
$id= app('request')->input('id');
if ($id>=1)
$i=$id;
@endphp
<div class="position-container">
    <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12"><div class="pos">Position {{isset($key) ? $key+1 :"1"}}</div>  </label>
    <div class="form-group row  {{ $errors->has('employement_start_date.'.$i) ? 'has-error' : '' }}" id="employement_start_date.{{$i}}">
        <label class="col-sm-5 col-form-label">Approximate Start Date</label>
        <div class="col-sm-7">
            {{Form::text('employement_start_date[]',old('employement_start_date.'.$i,isset($history->start_date) ? $history->start_date :""),array('class'=>' form-control datepicker','placeholder'=>"Start Date",'required'=>TRUE,'max'=>"2900-12-31",'readonly'=>"readonly"))}}
            <div class="form-control-feedback">
                {!! $errors->first('employement_start_date.'.$i) !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('employement_end_date.'.$i) ? 'has-error' : '' }}" id="employement_end_date.{{$i}}">
        <label class="col-sm-5 col-form-label">Approximate End Date</label>
        <div class="col-sm-7">
            {{Form::text('employement_end_date[]',old('employement_end_date.'.$i,isset($history->end_date) ? $history->end_date :""),array('class'=>' form-control datepicker','placeholder'=>"End Date",'required'=>TRUE,'max'=>"2900-12-31",'readonly'=>"readonly"))}}
            <div class="form-control-feedback">
                {!! $errors->first('employement_end_date.'.$i) !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('employer.'.$i) ? 'has-error' : '' }}" id="employer.{{$i}}">
        <label class="col-sm-5 col-form-label">Employer</label>
        <div class="col-sm-7">
            {{Form::text('employer[]',old('employer.'.$i,isset($history->employer) ? $history->employer :""),array('class'=>' form-control','placeholder'=>"Employer",'required'=>TRUE,'maxlength'=>255))}}
            <div class="form-control-feedback">
                {!! $errors->first('employer.'.$i) !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('employement_role.'.$i) ? 'has-error' : '' }}"  id="employement_role.{{$i}}">
        <label class="col-sm-5 col-form-label">Role</label>
        <div class="col-sm-7">
            {{Form::text('employement_role[]',old('employement_role.'.$i,isset($history->role) ? $history->role :""),array('class'=>' form-control','placeholder'=>"Role",'required'=>TRUE,'maxlength'=>255))}}
            <div class="form-control-feedback">
                {!! $errors->first('employement_role.'.$i) !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('employement_duties.'.$i) ? 'has-error' : '' }}"  id="employement_duties.{{$i}}">
        <label class="col-sm-5 col-form-label">Duties</label>
        <div class="col-sm-7">
            {{Form::text('employement_duties[]',old('employement_duties.'.$i,isset($history->duties) ? $history->duties :""),array('class'=>' form-control','placeholder'=>"Duties",'required'=>TRUE,'maxlength'=>255))}}
            <div class="form-control-feedback">
                {!! $errors->first('employement_duties.'.$i) !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('employement_reason.'.$i) ? 'has-error' : '' }}" id="employement_reason.{{$i}}">
        <label class="col-sm-5 col-form-label">Reason for Leaving</label>
        <div class="col-sm-7">
            {{Form::text('employement_reason[]',old('employement_reason.0',isset($history->reason) ? $history->reason :""),array('class'=>' form-control','placeholder'=>"Reason for Leaving",'required'=>TRUE,'maxlength'=>255))}}
            <div class="form-control-feedback">{!! $errors->first('employement_reason.'.$i) !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>

        </div>
        @if(isset($key) && $key==$count-1 &&  $key!=0 )
       <div class="col-sm-12">
        <a href="javascript:void(0);" class="remove-position  pull-right"><i class="fa fa-minus" aria-hidden="true"></i> Remove</a>
      </div>
      @else
      <div class="col-sm-12">
        <a href="javascript:void(0);" class="remove-position hide-this-block pull-right"><i class="fa fa-minus" aria-hidden="true"></i> Remove</a>
      </div>
       @endif
    </div>
</div>
