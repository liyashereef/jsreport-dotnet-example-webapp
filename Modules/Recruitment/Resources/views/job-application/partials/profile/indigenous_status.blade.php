<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Indigenous Status</label>
<div class="form-group row {{ $errors->has('is_indian_native') ? 'has-error' : '' }}" id="is_indian_native">
    <label class="col-sm-5 col-form-label">Are you a native Indian/Indigenous person in Canada and hold an official Certificate of Indian Status?
</label>
    <div class="col-sm-7">
        {{ Form::select('is_indian_native',[null=>'This is a mandatory field, please enter the required information',"Yes"=>"Yes","No"=>"No"],old('is_indian_native',isset($candidate->miscellaneous->is_indian_native) ? $candidate->miscellaneous->is_indian_native :""),array('class' => 'form-control','required'=>TRUE)) }}
        <div class="form-control-feedback">
            {!! $errors->first('is_indian_native ') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>