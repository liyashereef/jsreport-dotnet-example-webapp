@php
$i=0;
$id= app('request')->input('id');
if ($id>=1)
$i=$id;
@endphp

<div class="education-container">
    <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12"><div class="pos">Education {{isset($key) ? $key+1 :"1"}}</div></label>
    <div class="form-group row {{ $errors->has('start_date_education.'.$i) ? 'has-error' : '' }}" id="start_date_education.{{$i}}">
        <label class="col-sm-5 col-form-label">Approximate Start Date</label>
        <div class="col-sm-7">
            {{Form::text('start_date_education[]',old('start_date_education.'.$i,isset($educations->start_date_education) ? $educations->start_date_education :""),array('class'=>' form-control datepicker','placeholder'=>"Education Start Date",'required'=>TRUE,'max'=>"2900-12-31",'readonly'=>"readonly"))}}
            <div class="form-control-feedback">
                {!! $errors->first('start_date_education.'.$i) !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('end_date_education.'.$i) ? 'has-error' : '' }}" id="end_date_education.{{$i}}">
        <label class="col-sm-5 col-form-label">Approximate End Date</label>
        <div class="col-sm-7">
            {{Form::text('end_date_education[]',old('end_date_education.'.$i,isset($educations->end_date_education) ? $educations->end_date_education :""),array('class'=>' form-control datepicker','placeholder'=>"Education End Date",'required'=>TRUE,'max'=>"2900-12-31",'readonly'=>"readonly"))}}
            <div class="form-control-feedback">
                {!! $errors->first('end_date_education.'.$i) !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('grade.'.$i) ? 'has-error' : '' }}" id="grade.{{$i}}">
        <label class="col-sm-5 col-form-label">Grade</label>
        <div class="col-sm-7">
            {{Form::text('grade[]',old('grade.'.$i,isset($educations->grade) ? $educations->grade :""),array('class'=>' form-control','placeholder'=>"Grade",'required'=>TRUE))}}
            <div class="form-control-feedback">
                {!! $errors->first('grade.'.$i) !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('program.'.$i) ? 'has-error' : '' }}" id="program.{{$i}}">
        <label class="col-sm-5 col-form-label">Program</label>
        <div class="col-sm-7">
            {{Form::text('program[]',old('program.'.$i,isset($educations->program) ? $educations->program :""),array('class'=>' form-control','placeholder'=>"Program",'required'=>TRUE))}}
            <div class="form-control-feedback">
                {!! $errors->first('program.'.$i) !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('school.'.$i) ? 'has-error' : '' }}" id="school.{{$i}}">
        <label class="col-sm-5 col-form-label">School/Institute</label>
        <div class="col-sm-7">
            {{Form::text('school[]',old('school.'.$i,isset($educations->school) ? $educations->school :""),array('class'=>' form-control','placeholder'=>"School",'required'=>TRUE))}}
            <div class="form-control-feedback">
                {!! $errors->first('school.'.$i) !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
        @if(isset($key) && $key==$count-1 &&  $key!=0 )
        <div class="col-sm-12">
         <a href="javascript:void(0);"  class="remove-education  pull-right"><i class="fa fa-minus" aria-hidden="true"></i> Remove</a>
        </div>
        @else
        <div class="col-sm-12">
         <a href="javascript:void(0);"  class="remove-education hide-this-block pull-right"><i class="fa fa-minus" aria-hidden="true"></i> Remove</a>
        </div>
        @endif
     </div>
</div>
