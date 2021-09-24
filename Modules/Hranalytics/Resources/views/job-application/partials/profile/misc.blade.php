<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Dismissals</label>
<div class="form-group row {{ $errors->has('dismissed') ? 'has-error' : '' }}" id="dismissed">
    <label class="col-sm-5 col-form-label">Have you ever been dismissed or asked to resign from employment?</label>
    <div class="col-sm-7">
        {{ Form::select('dismissed',[null=>'If the answer is "Yes", please type in the answers below.',"Yes"=>"Yes","No"=>"No"],old('dismissed',isset($candidateJob->candidate->miscellaneous->dismissed) ? $candidateJob->candidate->miscellaneous->dismissed :""),array('class' => 'form-control','required'=>TRUE,'id'=>'asked_resign')) }}
        <div class="form-control-feedback">
            {!! $errors->first('dismissed') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>

<div  class="form-group row {{ @$candidateJob->candidate->miscellaneous->dismissed!='Yes'? 'hide-this-block':'' }} {{ $errors->has('explanation_dismissed') ? 'has-error' : '' }}" id="explanation_dismissed">
    <span class="col-sm-5 col-form-label">If you answered "yes", please explain</span>
    <div class="col-sm-7">
        {{Form::textarea('explanation_dismissed',old('explanation_dismissed',isset($candidateJob->candidate->miscellaneous->explanation_dismissed) ? $candidateJob->candidate->miscellaneous->explanation_dismissed:""),array('class'=>'form-control','placeholder'=>"Accepted only 500 charcters", 'maxlength'=>"500",'rows'=>6))}}
        <div class="form-control-feedback">
            {!! $errors->first('explanation_dismissed', '<span class="text-danger align-middle font-12"><i class="fa fa-close"></i>:message</span>') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>

<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Other Requirements</label>
<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">The majority of our positions require good mobility, and good sensory perception such as hearing, sight, and smell. Applicants must be psychologically healthy and should be capable of working alone for 24 hour rotating shifts.</label>

<div class="form-group row {{ $errors->has('limitations') ? 'has-error' : '' }}" id="limitations">
    <label class="col-sm-5 col-form-label">Do you have any limitations in these areas?</label>
    <div class="col-sm-7">
        {{ Form::select('limitations',[null=>'If the answer is "Yes", Please type in the answers below.',"Yes"=>"Yes","No"=>"No"],old('limitations',isset($candidateJob->candidate->miscellaneous->limitations) ? $candidateJob->candidate->miscellaneous->limitations:""),array('class' => 'form-control','required'=>TRUE,'id'=>'limitation')) }}
        <div class="form-control-feedback">
            {!! $errors->first('limitations') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>

<div class="form-group row {{ @$candidateJob->candidate->miscellaneous->limitations!='Yes'?'hide-this-block':'' }} {{ $errors->has('limitation_explain') ? 'has-error' : '' }}"   id="limitation_explain">
    <span class="col-sm-5 col-form-label">If you answered "yes", please advise of any disposition we could take in order to assist you in performing security guard duties:</span>
    <div class="col-sm-7">
        {{Form::textarea('limitation_explain',old('limitation_explain',isset($candidateJob->candidate->miscellaneous->limitation_explain) ? $candidateJob->candidate->miscellaneous->limitation_explain:""),array('class'=>'form-control','placeholder'=>"Explain Limitations",'maxlength'=>"500",'rows'=>6))}}
        <div class="form-control-feedback">
            {!! $errors->first('limitation_explain') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Criminal Convictions</label>
<div class="form-group row {{ $errors->has('criminal_convicted') ? 'has-error' : '' }}"  id="criminal_convicted">
    <label class="col-sm-5 col-form-label">Have you ever been convicted of a criminal offence for which you've not received a pardon?</label>
    <div class="col-sm-7">
        {{ Form::select('criminal_convicted',[null=>'If the answer is "Yes", please type in the answers below.',"Yes"=>"Yes","No"=>"No"],old('criminal_convicted',isset($candidateJob->candidate->miscellaneous->criminal_convicted) ? $candidateJob->candidate->miscellaneous->criminal_convicted:""),array('class' => 'form-control','required'=>TRUE,'id'=>'criminal_td')) }}
        <div class="form-control-feedback">
            {!! $errors->first('criminal_convicted') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div id="crime" class="{{ @$candidateJob->candidate->miscellaneous->criminal_convicted!='Yes'?'hide-this-block':'' }}">
    <div class="form-group row" {{ $errors->has('offence') ? 'has-error' : '' }}" id="offence" >
        <span class="col-sm-5 col-form-label">What was your offence?</span>
        <div class="col-sm-7">
            {{Form::text('offence',old('offence',isset($candidateJob->candidate->miscellaneous->offence) ? $candidateJob->candidate->miscellaneous->offence:""),array('class'=>'form-control','placeholder'=>"Offence"))}}
            <div class="form-control-feedback">
                {!! $errors->first('offence') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('offence_date') ? 'has-error' : '' }}" id="offence_date">
        <span class="col-sm-5 col-form-label">When did the offence occur?</span>
        <div class="col-sm-7">
            {{Form::text('offence_date',old('offence_date',isset($candidateJob->candidate->miscellaneous->offence_date) ? $candidateJob->candidate->miscellaneous->offence_date:""),array('class'=>'form-control datepicker','placeholder'=>"Offence Date",'max'=>"2900-12-31"))}}
            <div class="form-control-feedback">
                {!! $errors->first('offence_date') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('offence_location') ? 'has-error' : '' }}" id="offence_location">
        <span class="col-sm-5 col-form-label">What was the location of your offence?</span>
        <div class="col-sm-7">
            {{Form::text('offence_location',old('offence_location',isset($candidateJob->candidate->miscellaneous->offence_location) ? $candidateJob->candidate->miscellaneous->offence_location:""),array('class'=>'form-control','placeholder'=>"Offence Location"))}}
            <div class="form-control-feedback">
                {!! $errors->first('offence_location ') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('disposition_granted') ? 'has-error' : '' }}" id="disposition_granted">
        <span class="col-sm-5 col-form-label">What disposition have you been granted?</span>
        <div class="col-sm-7">
            {{Form::text('disposition_granted',old('disposition_granted',isset($candidateJob->candidate->miscellaneous->disposition_granted) ? $candidateJob->candidate->miscellaneous->disposition_granted:""),array('class'=>'form-control','placeholder'=>"Disposition Granted"))}}
            <div class="form-control-feedback">
                {!! $errors->first('disposition_granted') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
</div>

<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Career Interests</label>
<div class="form-group row {{ $errors->has('career_interest') ? 'has-error' : '' }}" id="career_interest">
    <label class="col-sm-5 col-form-label">How would you describe your longer term career interests in security?</label>
    <div class="col-sm-7">
        {{ Form::select('career_interest',[null=>'Please select the appropriate answer from the dropdown list',"1 - Commissionaires is a temporary stop in my career. I have no long term plans."=>"1 - Commissionaires is a temporary stop in my career. I have no long term plans.","2 - I would be interested in exploring a longer term career at Commissionaires."=>"2 - I would be interested in exploring a longer term career at Commissionaires.","3 - I am interested in a long term career with Commissionaires."=>"3 - I am interested in a long term career with Commissionaires.","4 - Commissionaires is strategic to my long term career in security."=>"4 - Commissionaires is strategic to my long term career in security."],old('career_interest',isset($candidateJob->candidate->miscellaneous->career_interest) ? $candidateJob->candidate->miscellaneous->career_interest:""),array('class' => 'form-control','required'=>TRUE)) }}
        <div class="form-control-feedback">
            {!! $errors->first('career_interest') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div class="form-group row {{ $errors->has('other_roles') ? 'has-error' : '' }}" id="other_roles">
    <label class="col-sm-5 col-form-label">Would you consider other role with Commissionaires beyond what you've applied for?</label>
    <div class="col-sm-7">
        {{ Form::select('other_roles',[null=>'Please select the appropriate answer from the dropdown list',"Yes"=>"Yes","No"=>"No"],old('other_roles',isset($candidateJob->candidate->miscellaneous->other_roles) ? $candidateJob->candidate->miscellaneous->other_roles:""),array('class' => 'form-control','required'=>TRUE)) }}
        <div class="form-control-feedback">
            {!! $errors->first('other_roles') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
