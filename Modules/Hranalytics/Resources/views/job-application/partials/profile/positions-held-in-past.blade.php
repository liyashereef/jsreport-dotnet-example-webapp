@foreach($lookups['positions_lookups']+array(0=>'Other') as $each_position)
@php
$control_name =  str_replace(' ', '_', strtolower($each_position));
@endphp
<div class="form-group row"  id="{{$control_name}}">
    <label for="{{$control_name}}" class="col-sm-5 col-form-label">{{$each_position}}</label>
    <div class="col-sm-7">
        {{Form::number($control_name,old($control_name,@$position_experience[$control_name]),array('class'=>' form-control','placeholder'=>"This is a mandatory field. Please enter 0 (the number 0) if this does not apply to you.",'min'=>"0",'step'=>"0.1",'max'=>"99",'required'=>TRUE))}}
        <div class="form-control-feedback">{!! $errors->first('site_supervisor') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
@endforeach