
<div class="form-group row {{ $errors->has('position_availibility') ? 'has-error' : '' }}">
    <label class="col-sm-5 col-form-label">Would you be willing to start as a "floater/spare" until a permanent position comes up, or are you only interested in assignments you've applied to. </label>
    <div class="col-sm-7">
        {{ Form::select('position_availibility',config('globals.position_availibility'),old('position_availibility',isset($candidateJob->candidate->referalAvailibility) ? $candidateJob->candidate->referalAvailibility->position_availibility :""),array('class' => 'form-control','id'=>'position_availibility')) }}
        <div class="form-control-feedback">
            {!! $errors->first('position_availibility') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div  id="floater_hours" class="form-group row {{ @$candidateJob->candidate->referalAvailibility->position_availibility!='1'?'hide-this-block':'' }}">
    <label for="floater_hours" class="col-sm-5 col-form-label">How many hours a week are you looking for?</label>
    <div class="col-sm-7">
        {{Form::number('floater_hours',isset($candidateJob->candidate->referalAvailibility)?$candidateJob->candidate->referalAvailibility->floater_hours:'',array('class'=>'form-control','min'=>0,'max'=>168))}}
        <div class="form-control-feedback">
            {!! $errors->first('floater_hours') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div class="form-group row {{ $errors->has('starting_time') ? 'has-error' : '' }}" id="starting_time">
    <label class="col-sm-5 col-form-label">How soon could you start? </label>
    <div class="col-sm-7">
        {{ Form::select('starting_time',config('globals.starting_time'),old('position_availability',isset($candidateJob->candidate->referalAvailibility) ? $candidateJob->candidate->referalAvailibility->starting_time :""),array('class' => 'form-control','id'=>'starting_time')) }}
        <div class="form-control-feedback">
            {!! $errors->first('starting_time') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>