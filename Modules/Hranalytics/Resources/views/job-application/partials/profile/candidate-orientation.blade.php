
<div class="form-group row {{ $errors->has('orientation') ? 'has-error' : '' }}">
    <label class="col-sm-5 col-form-label">Would you be willing to start as a "floater/spare" until a permanent position comes up, or are you only interested in assignments you've applied to. </label>
    <div class="col-sm-7">
        {{ Form::select('orientation',config('globals.orientation'),old('position_availibility',isset($candidateJob->candidate->referalAvailibility) ? $candidateJob->candidate->referalAvailibility->position_availibility :""),array('class' => 'form-control','id'=>'position_availibility')) }}
        <div class="form-control-feedback">
            {!! $errors->first('position_availibility') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
