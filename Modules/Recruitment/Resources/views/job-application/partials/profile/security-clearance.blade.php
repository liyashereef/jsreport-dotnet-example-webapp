<div class="form-group row {{ $errors->has('optradio') ? 'has-error' : '' }}" id="optradio">
    <label class="col-sm-5 col-form-label">Were you born outside of Canada?<span class="mandatory">*</span></label>
    <div class="col-sm-7 form-group row">
        <div class="radio-inline col-sm-2"><input type="radio" @if(@$candidate->securityclearance->born_outside_of_canada=="Yes") checked @endif name="optradio" value="Yes"><label class="padding-5" ><b>Yes</b></label></div>
        <div class="radio-inline col-sm-2"><input type="radio" @if(@$candidate->securityclearance->born_outside_of_canada=="No") checked @endif name="optradio" value="No" ><label class="padding-5" ><b>No</b></label></div>
        <div class="form-control-feedback">
            {!! $errors->first('optradio') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div class="form-group row {{ $errors->has('work_status_in_canada') ? 'has-error' : '' }}" id="work_status_in_canada">
    <label for="work_status_in_canada" class="col-sm-5 col-form-label" >Please indicate your working status in Canada?</label>
    <div class="col-sm-7">
        {{ Form::select('work_status_in_canada',[null=>'Please select the appropriate response from the dropdown list',"Canadian Citizen"=>"Canadian Citizen","Landed Immigrant"=>"Landed Immigrant","Permanent Resident"=>"Permanent Resident"],old('work_status_in_canada',isset($candidate->securityclearance->work_status_in_canada) ? $candidate->securityclearance->work_status_in_canada :""),array('id' => 'work_status_in_canada','class' => 'form-control','required'=>TRUE)) }}
        <div class="form-control-feedback">
            {!! $errors->first('work_status_in_canada') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div id="landed_immigrant" class="{{ @$candidate->securityclearance->work_status_in_canada!='Landed Immigrant'?'hide-this-block':'' }}">
    <div class="form-group row {{ $errors->has('status_expiry_date') ? 'has-error' : '' }}" id="status_expiry_date">
            <label for="work_status_in_canada" class="col-sm-5 col-form-label" >When does your status expire?</label>
            <div class="col-sm-7">
                {{ Form::text('status_expiry_date',old('status_expiry_date',isset($candidate->securityclearance->status_expiry_date) ? $candidate->securityclearance->status_expiry_date :""),array('id' => 'status_expiry_date','class' => 'form-control datepicker')) }}
                <div class="form-control-feedback">
                    {!! $errors->first('status_expiry_date') !!}
                    <span class="help-block text-danger align-middle font-12"></span>
                </div>
            </div>
        </div>
        <div class="form-group row {{ $errors->has('renew_status') ? 'has-error' : '' }}" id="renew_status">
                <label for="renew_status" class="col-sm-5 col-form-label" >Do you anticipate renewing your landed immigrant status or applying for permanent residency after your status expire?</label>
                <div class="col-sm-7 form-group row">
                        <div class="radio-inline col-sm-2"><input type="radio" @if(isset($candidate->securityclearance->renew_status)&& (@$candidate->securityclearance->renew_status)==1) checked @endif name="renew_status" value="1"><label class="padding-5" ><b>Yes</b></label></div>
                        <div class="radio-inline col-sm-2"><input type="radio" @if(isset($candidate->securityclearance->renew_status)&& (@$candidate->securityclearance->renew_status)==0) checked @endif name="renew_status" value="0"><label class="padding-5" ><b>No</b></label></div>
                        <div class="form-control-feedback">
                        {!! $errors->first('renew_status') !!}
                        <span class="help-block text-danger align-middle font-12"></span>
                    </div>
                </div>
        </div>
</div>


<div class="form-group row {{ $errors->has('years_lived_in_canada') ? 'has-error' : '' }}" id="years_lived_in_canada">
    <label class="col-sm-5 col-form-label">How many years have you lived in Canada (approximately)?</label>
    <div class="col-sm-7">
        {{Form::number('years_lived_in_canada',old('years_lived_in_canada',isset($candidate->securityclearance->years_lived_in_canada) ? $candidate->securityclearance->years_lived_in_canada :""),array('class'=>'form-control','placeholder'=>"Please Enter Approximate Value",'required'=>TRUE,'max'=>100,'min'=>0))}}
        <div class="form-control-feedback">
            {!! $errors->first('years_lived_in_canada') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div class="form-group row {{ $errors->has('prepared_for_security_screening') ? 'has-error' : '' }}" id="prepared_for_security_screening">
    <label class="col-sm-5 col-form-label">Are you prepared to submit to a security screening?</label>
    <div class="col-sm-7">
        {{ Form::select('prepared_for_security_screening',[null=>'Please select the appropriate response from the dropdown list',"Yes"=>"Yes","No"=>"No"],old('prepared_for_security_screening',isset($candidate->securityclearance->prepared_for_security_screening) ? $candidate->securityclearance->prepared_for_security_screening :""),array('class' => 'form-control','required'=>TRUE)) }}
        <div class="form-control-feedback">
            {!! $errors->first('prepared_for_security_screening') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>

<div class="form-group row {{ $errors->has('no_clearance') ? 'has-error' : '' }}" id="no_clearance">
    <label for="no_clearance" class="col-sm-5 col-form-label" >Do you have reason to believe you may <b><u>NOT</u></b> be granted a clearance?</label>
    <div class="col-sm-7">
        {{ Form::select('no_clearance',[null=>'This is a mandatory field, please enter the required information from the dropdown list',"Yes"=>"Yes","No"=>"No"],old('no_clearance',isset($candidate->securityclearance->no_clearance) ? $candidate->securityclearance->no_clearance :""),array('class' => 'form-control','required'=>TRUE,'id'=>'no_clearances')) }}
        <div class="form-control-feedback">
            {!! $errors->first('no_clearance') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div id="no_clearance_explanation" class="{{ @$candidate->securityclearance->no_clearance!='Yes'?'hide-this-block':'' }}">
<div class="form-group row" id="no_clearance_explanation"  style="display: {{(isset($candidate->securityclearance->no_clearance) ?
        $candidate->securityclearance->no_clearance :"" )=='Yes' }} ? "block;" : "none;" {{ $errors->has('no_clearance_explanation') ? 'has-error' : '' }}">
    <label class="col-sm-5 col-form-label">If you answered "Yes", please explain:</label>
    <div class="col-sm-7">
        {{Form::textarea('no_clearance_explanation',old('no_clearance_explanation',isset($candidate->securityclearance->no_clearance_explanation) ? $candidate->securityclearance->no_clearance_explanation :""),array('class'=>'form-control','placeholder'=>"Accepted Only 500 Characters", 'maxlength'=>"500",'rows'=>6))}}
        <div class="form-control-feedback">
            {!! $errors->first('no_clearance_explanation') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
    </div>
</div>
