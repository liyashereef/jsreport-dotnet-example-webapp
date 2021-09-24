<div class="form-group row {{ $errors->has('driver_license') ? 'has-error' : '' }}" id="driver_license">
    <span class="col-sm-5 col-form-label">Do you have a valid drivers licence?<br/></span>
    <div class="col-sm-7">
        {{ Form::select('driver_license',[null=>'Leave blank if you do not have a drivers licence, otherwise select answer from the dropdown list',"I have a valid G1 license"=>"I have a valid G1 license","I have a valid G2 license"=>"I have a valid G2 license","I have a full class G license"=>"I have a full class G license"],old('no_clearance',isset($candidateJob->candidate->securityproximity->driver_license) ? $candidateJob->candidate->securityproximity->driver_license :""),array('class' => 'form-control')) }}
        <div class="form-control-feedback">
            {!! $errors->first('driver_license') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>

<div class="form-group row {{ $errors->has('access_vehicle') ? 'has-error' : '' }}" id="access_vehicle">
    <label class="col-sm-5 col-form-label">Do you have access to a vehicle?</label>
    <div class="col-sm-7">
        {{ Form::select('access_vehicle',[null=>'Select from the dropdown the most appropriate answer',"I do not have access to a vehicle"=>"I do not have access to a vehicle","I have access to a vehicle that is not my own"=>"I have access to a vehicle that is not my own","I have my own vehicle"=>"I have my own vehicle"],old('access_vehicle',isset($candidateJob->candidate->securityproximity->access_vehicle) ? $candidateJob->candidate->securityproximity->access_vehicle :""),array('class' => 'form-control','required'=>TRUE)) }}
        <div class="form-control-feedback">
            {!! $errors->first('access_vehicle') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div class="form-group row {{ $errors->has('access_public_transport') ? 'has-error' : '' }}" id="access_public_transport">
    <label class="col-sm-5 col-form-label">If you do not have a licence or access to a vehicle, do you have access to public transit?</label>
    <div class="col-sm-7">
        {{ Form::select('access_public_transport',[null=>'Select from the dropdown the most appropriate answer',"I have little access to the client site via public transit"=>"I have little access to the client site via public transit","I have some access to the client site via public transit"=>"I have some access to the client site via public transit","I have ready access to the client site via public transit"=>"I have ready access to the client site via public transit"],old('access_public_transport',isset($candidateJob->candidate->securityproximity->access_public_transport) ? $candidateJob->candidate->securityproximity->access_public_transport :""),array('class' => 'form-control','required'=>TRUE)) }}
        <div class="form-control-feedback">
            {!! $errors->first('access_public_transport') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>

<div class="form-group row {{ $errors->has('transportation_limitted') ? 'has-error' : '' }}" id="transportation_limitted">
    <label class="col-sm-5 col-form-label">Does your method of transportation limit your availability?</label>
    <div class="col-sm-7">
        {{ Form::select('transportation_limitted',[null=>'This is a mandatory field, please enter the required information.',"Yes"=>"Yes","No"=>"No"],old('transportation_limitted',isset($candidateJob->candidate->securityproximity->transportation_limitted) ? $candidateJob->candidate->securityproximity->transportation_limitted :""),array('class' => 'form-control','required'=>TRUE,'id'=>'transport_limit'))}}
        <div class="form-control-feedback">  <span class="help-block text-danger align-middle font-12"></span> </div>
    </div>

</div>

<div class="form-group row  {{ $errors->has('explanation_transport_limit') ? 'has-error' : '' }} {{ @$candidateJob->candidate->securityproximity->transportation_limitted!='Yes'?'hide-this-block':'' }}" id="explanation_transport_limit">
    <span class="col-sm-5 col-form-label">If you answered "Yes", please explain:</span>
    <div class="col-sm-7">
        {{Form::textarea('explanation_transport_limit',old('explanation_transport_limit',isset($candidateJob->candidate->securityproximity->explanation_transport_limit) ? $candidateJob->candidate->securityproximity->explanation_transport_limit :""),array('class'=>'form-control','placeholder'=>"Accepted only 500 characters", 'maxlength'=>"500",'rows'=>6))}}
        <div class="form-control-feedback">
            {!! $errors->first('explanation_transport_limit') !!}
            <span class="help-block text-danger align-middle font-12"></span>

    </div>
</div>
</div>
