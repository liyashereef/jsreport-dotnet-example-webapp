
    <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 orange">
        <h5>Information</h5>
    </label>
    <div class="form-group row  {{ $errors->has('rfp_response_type_id') ? 'has-error' : '' }}" id="rfp_response_type_id">
        <label for="rfp_response_type_id" class="col-sm-5 col-form-label">What type of response is this?</label>
        <div class="col-sm-6">
            {{ Form::select('rfp_response_type_id', $rfpResponseTypeLookup,
            old('rfp_response_type_id', (isset($rfpDetails)?$rfpDetails->rfp_response_type_id:null)),
            array('class'=> 'form-control', 'required'=>TRUE, 'placeholder' => 'Please Select')) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('rfp_response_type_id', ':message') !!}</div>
        </div>
    </div>
