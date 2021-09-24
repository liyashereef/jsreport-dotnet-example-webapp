<div class="form-group row {{ $errors->has('guard_licence') ? 'has-error' : '' }}"  id="guard_licence">
    <label for="guard_licence" class="col-sm-5 col-form-label">Do you have a valid security guarding licence in Ontario with First Aid and CPR?</label>
    <div class="col-sm-7">
        {{ Form::select('guard_licence',[null=>'Some positions DO NOT require a security licence.  Select "No" if this does not apply to you.',"Yes"=>"Yes","No"=>"No"],old('guard_licence',isset($candidateJob->candidate->guardingexperience->guard_licence) ? $candidateJob->candidate->guardingexperience->guard_licence :""),array('class' => 'form-control','id'=>'guard_licences','required'=>TRUE,'max'=>"2900-12-31")) }}
        <div class="form-control-feedback">{!! $errors->first('guard_licence') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div id="guard_licence_start_qstn" class="{{ @$candidateJob->candidate->guardingexperience->guard_licence!='Yes'?'hide-this-block':'' }}">
    <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Licence Start Dates</label>
    <div class="form-group row {{ $errors->has('start_date_guard_license') ? 'has-error' : '' }}"  id="start_date_guard_license">
        <label for="start_date_guard_license" class="col-sm-5 col-form-label">Please note the start date when you first acquired your guarding licence in Ontario?</label>
        <div class="col-sm-7">
            {{Form::text('start_date_guard_license',old('start_date_guard_license',isset($candidateJob->candidate->guardingexperience->start_date_guard_license) ? $candidateJob->candidate->guardingexperience->start_date_guard_license :""),array('class'=>' form-control datepicker','id'=>'guardlicence','placeholder'=>"If you have a guarding licence, when did you acquire it? ",'max'=>"2900-12-31",'readonly'=>true))}}
            <div class="form-control-feedback">{!! $errors->first('start_date_guard_license') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('start_date_first_aid') ? 'has-error' : '' }}"  id="start_date_first_aid">
        <label for="start_date_guard_license" class="col-sm-5 col-form-label">Please note the start date when you first acquired your First Aid Certificate?</label>
        <div class="col-sm-7">
            {{Form::text('start_date_first_aid',old('start_date_first_aid',isset($candidateJob->candidate->guardingexperience->start_date_first_aid) ? $candidateJob->candidate->guardingexperience->start_date_first_aid :""),array('class'=>' form-control datepicker','placeholder'=>"If you have First Aid when did you acquire it? Mandatory field if you have First Aid certificate.",'max'=>"2900-12-31"))}}
            <div class="form-control-feedback">{!! $errors->first('start_date_first_aid') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('start_date_cpr') ? 'has-error' : '' }}"  id="start_date_cpr">
        <label for="start_date_guard_license" class="col-sm-5 col-form-label">Please note the start date when you first acquired your CPR Certificate?</label>
        <div class="col-sm-7">
            {{Form::text('start_date_cpr',old('start_date_cpr',isset($candidateJob->candidate->guardingexperience->start_date_cpr) ? $candidateJob->candidate->guardingexperience->start_date_cpr :""),array('class'=>' form-control datepicker','placeholder'=>"If you have CPR when did you acquire it? Mandatory field if you have your CPR certificate",'max'=>"2900-12-31"))}}
            <div class="form-control-feedback">{!! $errors->first('start_date_cpr') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
</div>
<div id="guard_licence_expiry_qstn" class="{{ @$candidateJob->candidate->guardingexperience->guard_licence!='Yes'?'hide-this-block':'' }}">
    <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Licence Expiry Dates</label>
    <div class="form-group row {{ $errors->has('expiry_guard_license') ? 'has-error' : '' }}"  id="expiry_guard_license">
        <label for="expiry_guard_license" class="col-sm-5 col-form-label">Please enter the expiry date of your security guard licence</label>
        <div class="col-sm-7">
            {{Form::text('expiry_guard_license',old('expiry_guard_license',isset($candidateJob->candidate->guardingexperience->expiry_guard_license) ? $candidateJob->candidate->guardingexperience->expiry_guard_license :""),array('class'=>' form-control datepicker','placeholder'=>"If you have a guarding licence, when does it expire.  Mandatory field if you have a licence.",'max'=>"2900-12-31"))}}
            <div class="form-control-feedback">{!! $errors->first('expiry_guard_license') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('expiry_first_aid') ? 'has-error' : '' }}"  id="expiry_first_aid">
        <label for="expiry_first_aid" class="col-sm-5 col-form-label">Please enter the expiry date of your First Aid certificate</label>
        <div class="col-sm-7">
            {{Form::text('expiry_first_aid',old('expiry_first_aid',isset($candidateJob->candidate->guardingexperience->expiry_first_aid) ? $candidateJob->candidate->guardingexperience->expiry_first_aid :""),array('class'=>' form-control datepicker','placeholder'=>"If you have First Aid, when does it expire?  Mandatory field if you have a licence.",'max'=>"2900-12-31"))}}
            <div class="form-control-feedback">{!! $errors->first('expiry_first_aid') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('expiry_cpr') ? 'has-error' : '' }}"  id="expiry_cpr">
        <label for="expiry_cpr" class="col-sm-5 col-form-label">Please enter the expiry date of your CPR certificate</label>
        <div class="col-sm-7">
            {{Form::text('expiry_cpr',old('expiry_cpr',isset($candidateJob->candidate->guardingexperience->expiry_cpr) ? $candidateJob->candidate->guardingexperience->expiry_cpr :""),array('class'=>' form-control datepicker','placeholder'=>"If you have CPR, when does it expire?  Mandatory field if you have a licence.",'max'=>"2900-12-31"))}}
            <div class="form-control-feedback">{!! $errors->first('expiry_cpr') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
</div>
<div class="{{ @$candidateJob->candidate->guardingexperience->guard_licence!='No' && (@$candidateJob->candidate->guardingexperience->test_score_percentage) ?'':'hide-this-block' }}" id="test_score_block">
<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Upload Ontario Security Guard Test Scores</label>
@include('hranalytics::job-application.partials.profile.security-guard-test-score')
</div>

<div id="security_clearness_expiry_qstn" class="{{ @$candidateJob->candidate->guardingexperience->guard_licence!='Yes'?'hide-this-block':'' }}">
    <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Security Clearance Information</label>
    <div class="form-group row {{ $errors->has('security_clearance') ? 'has-error' : '' }}"  id="security_clearance">
        <label for="security_clearance" class="col-sm-5 col-form-label">Do you have a valid security clearance ? </label>
        <div class="col-sm-7">
            {{ Form::select('security_clearance',[null=>'If the answer is "Yes", please type in the answers below.',"Yes"=>"Yes","No"=>"No"],old('security_clearance',isset($candidateJob->candidate->guardingexperience->guard_licence) ? $candidateJob->candidate->guardingexperience->security_clearance :""),array('class' => 'form-control','id'=>'security_clearance')) }}
            <div class="form-control-feedback">{!! $errors->first('security_clearance') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div id="security_clearance_type_div" class="{{ @$candidateJob->candidate->guardingexperience->security_clearance!='Yes'?'hide-this-block':'' }}">
    <div class="form-group row {{ $errors->has('security_clearance_type') ? 'has-error' : '' }}"  id="security_clearance_type">
        <label for="security_clearance_type" class="col-sm-5 col-form-label">What type of security clearance ? </label>
        <div class="col-sm-7">
            {{Form::text('security_clearance_type',old('security_clearance_type',isset($candidateJob->candidate->guardingexperience->security_clearance_type) ? $candidateJob->candidate->guardingexperience->security_clearance_type :""),array('class'=>' form-control','placeholder'=>"Mandatory field if you have a security clearance."))}}
            <div class="form-control-feedback">{!! $errors->first('security_clearance_type') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    </div>
    <div id="security_clearance_expiry_date_div" class="{{ @$candidateJob->candidate->guardingexperience->security_clearance!='Yes'?'hide-this-block':'' }}">
    <div class="form-group row {{ $errors->has('security_clearance_expiry_date') ? 'has-error' : '' }}"  id="security_clearance_expiry_date">
        <label for="security_clearance_expiry_date" class="col-sm-5 col-form-label">Enter the expiry date</label>
        <div class="col-sm-7">
            {{Form::text('security_clearance_expiry_date',old('security_clearance_expiry_date',isset($candidateJob->candidate->guardingexperience->security_clearance_expiry_date) ? $candidateJob->candidate->guardingexperience->security_clearance_expiry_date :""),array('class'=>' form-control datepicker','placeholder'=>"If you have Security clearance, when does it expire?  Mandatory field if you have a security clearance.",'max'=>"2900-12-31"))}}
            <div class="form-control-feedback">{!! $errors->first('security_clearance_expiry_date') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
</div>
</div>
<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Use of Force</label>
<div class="form-group row {{ $errors->has('force') ? 'has-error' : '' }}"  id="use_of_force">
    <label for="use_of_force" class="col-sm-5 col-form-label">Are you use of force certified?</label>
    <div class="col-sm-7">
        {{ Form::select('use_of_force',[null=>'Please Select',"Yes"=>"Yes","No"=>"No"],old('use_of_force',isset($candidateJob->candidate->force->force) ? $candidateJob->candidate->force->force :""),array('class' => 'form-control','id'=>'use_of_forces','required'=>TRUE)) }}
        <div class="form-control-feedback">{!! $errors->first('use_of_force') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div id="use_of_force_question" class="{{ @$candidateJob->candidate->force->force!='Yes'?'hide-this-block':'' }}">
    <div class="form-group row {{ $errors->has('force_certification') ? 'has-error' : '' }}"  id="force_certification">
        <label for="force_certification" class="col-sm-5 col-form-label">If yes, please provide your certification</label>
        <div class="col-sm-7">
            {{ Form::select('force_certification',[null=>'Please Select']+$lookups['force'],old('force_certification',isset($candidateJob->candidate->force->use_of_force_lookups_id) ? $candidateJob->candidate->force->use_of_force_lookups_id :""),array('class' => 'form-control','id'=>'force_certifications')) }}
            <div class="form-control-feedback">{!! $errors->first('force_certification') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('expiry') ? 'has-error' : '' }}"  id="force_expiry">
        <label for="force_expiry" class="col-sm-5 col-form-label">When does your certification expire?</label>
        <div class="col-sm-7">
            {{Form::text('force_expiry',old('force_expiry',isset($candidateJob->candidate->force->expiry) ? $candidateJob->candidate->force->expiry :""),array('class'=>' form-control datepicker','placeholder'=>"If you have use of force certification, when does it expire?  Mandatory field if you have a certification.",'max'=>"2900-12-31"))}}
            <div class="form-control-feedback">{!! $errors->first('force_expiry') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>

    @if (!isset($candidateJob->candidate->force->attachment_id))
        <div class="form-group row {{ $errors->has('force_file') ? 'has-error' : '' }}"  id="force_file">
            <label for="force_file" class="col-sm-5 col-form-label">Please upload your UOF certificate<span class="mandatory">*</span></label>
            <div class="col-sm-7">
                {{Form::file('force_file',array('class'=>'form-control file_attachment scroll-clear','id'=>'force_file','onchange'=>'validateFileSize(this);'))}}
                <div class="form-control-feedback">{!! $errors->first('force_file') !!}
                    <span class="help-block text-danger align-middle font-12"></span>
                </div>
            </div>
        </div>
    @else
        <div class="form-group row {{ $errors->has('force_file') ? 'has-error' : '' }}" id="force_file">
            <label for="force_file" class="col-sm-5 col-form-label">Please upload your UOF certificate<span class="mandatory">*</span></label>
            <div class="col-sm-4">
            <div class="row">
                <div class="col-sm-6">
                    <div id="force_div">
                    <input type="hidden" name="force_file" value="{{ $candidateJob->candidate->force->attachment_id}}" id="force_file">
                    <a class="nav-link score-document" target="_blank" href="{{ route('force-document.download', ['file_name'=>$candidateJob->candidate->force->attachment_id])}}" />Click here to download the file
                    </a>
                    </div>
                </div>
            <div class="col-sm-4">
                <input class="button btn btn-edit score_attachment_remove_btn" onclick="removeForceDocument(this)" type="button" value="Remove" style="margin: 7px;">
            </div>
            </div>
        </div>
        </div>
    @endif
</div>

<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Security Guarding Experience</label>

<div class="form-group row {{ $errors->has('social_insurance_number') ? 'has-error' : '' }}" id="social_insurance_number">
        <label for="social_insurance_number" class="col-sm-5 col-form-label" >Do you have a valid Social Insurance Number in Canada?</label>
        <div class="col-sm-7 form-group row">
                <div class="radio-inline col-sm-2"><input type="radio" @if(isset($candidateJob->candidate->guardingexperience->social_insurance_number)&& (@$candidateJob->candidate->guardingexperience->social_insurance_number)==1) checked @endif  name="social_insurance_number" value=1><label class="padding-5" ><b>Yes</b></label></div>
                <div class="radio-inline col-sm-2"><input type="radio" @if(isset($candidateJob->candidate->guardingexperience->social_insurance_number)&& (@$candidateJob->candidate->guardingexperience->social_insurance_number)==0) checked @endif name="social_insurance_number" value=0 ><label class="padding-5" ><b>No</b></label></div>
                <div class="form-control-feedback">
                {!! $errors->first('social_insurance_number') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
</div>

<div id="sin_expiry_date_status_div" class="{{ @$candidateJob->candidate->guardingexperience->social_insurance_number==0 ?'hide-this-block':'' }}">

            <div class="form-group row {{ $errors->has('sin_expiry_date_status') ? 'has-error' : '' }}" id="sin_expiry_date_status">
                    <label for="sin_expiry_date_status" class="col-sm-5 col-form-label" >Do you have an expiry date on your SIN ?</label>
                    <div class="col-sm-7 form-group row">
                            <div class="radio-inline col-sm-2"><input  type="radio"
                            @if(isset($candidateJob->candidate->guardingexperience->sin_expiry_date_status)&& (@$candidateJob->candidate->guardingexperience->sin_expiry_date_status)==1) checked @endif name="sin_expiry_date_status" value=1><label class="padding-5" ><b>Yes</b></label></div>
                            <div class="radio-inline col-sm-2"><input  type="radio"  @if(isset($candidateJob->candidate->guardingexperience->sin_expiry_date_status)&& (@$candidateJob->candidate->guardingexperience->sin_expiry_date_status)==0) checked @endif name="sin_expiry_date_status" value=0><label class="padding-5" ><b>No</b></label></div>
                            <div class="form-control-feedback">
                            {!! $errors->first('sin_expiry_date_status') !!}
                            <span class="help-block text-danger align-middle font-12"></span>
                        </div>
                    </div>
            </div>
</div>
<?php //echo '<pre>'; print_r($candidateJob->candidate->guardingexperience);exit; ?>
<div id="sin_expiry_date_div" class="{{ @$candidateJob->candidate->guardingexperience->sin_expiry_date_status== 0 ?'hide-this-block':'' }}">
        <div class="form-group row {{ $errors->has('sin_expiry_date') ? 'has-error' : '' }}" id="sin_expiry_date">
                <label for="sin_expiry_date" class="col-sm-5 col-form-label" >Please enter the expiry date of your SIN</label>
                <div class="col-sm-7">
                    {{ Form::text('sin_expiry_date',old('sin_expiry_date',isset($candidateJob->candidate->guardingexperience->sin_expiry_date) ? $candidateJob->candidate->guardingexperience->sin_expiry_date :""),array('id' => 'sin_expiry_date','class' => 'form-control datepicker','readonly'=>"readonly")) }}
                    <div class="form-control-feedback">
                        {!! $errors->first('sin_expiry_date') !!}
                        <span class="help-block text-danger align-middle font-12"></span>
                    </div>
                </div>
            </div>
</div>

<div class="form-group row {{ $errors->has('years_security_experience') ? 'has-error' : '' }}"  id="years_security_experience">
    <label for="years_security_experience" class="col-sm-5 col-form-label">How many total years of security industry experience do you have?</label>
    <div class="col-sm-7">
        {{Form::number('years_security_experience',old('years_security_experience',isset($candidateJob->candidate->guardingexperience->years_security_experience) ? $candidateJob->candidate->guardingexperience->years_security_experience :""),array('class'=>' form-control','placeholder'=>"Leave blank if not applicable. If you are applying for a guard position, you must enter this field",'min'=>"0",'step'=>"0.1"))}}
        <div class="form-control-feedback">{!! $errors->first('years_security_experience') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>

<div class="form-group row {{ $errors->has('most_senior_position_held') ? 'has-error' : '' }}"  id="most_senior_position_held">
    <label for="most_senior_position_held" class="col-sm-5 col-form-label">What is the most senior position you've held in security?  </label>
    <div class="col-sm-7">
         {{ Form::select('most_senior_position_held',[null=>'Leave blank if not applicable. If you are applying for a guard position, you must enter this field']+$lookups['positions_lookups']+[0=>'Other'],old('last_role_held',isset($candidateJob->candidate->guardingexperience->most_senior_position_held) ? $candidateJob->candidate->guardingexperience->most_senior_position_held :""),array('class' => 'form-control select2')) }}
        <div class="form-control-feedback">{!! $errors->first('most_senior_position_held') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
