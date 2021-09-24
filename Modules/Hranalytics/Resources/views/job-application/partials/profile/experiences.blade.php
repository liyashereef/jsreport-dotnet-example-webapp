<div class="form-group row {{ $errors->has('current_employee_commissionaries') ? 'has-error' : '' }}" id="current_employee_commissionaries">
    <label class="col-sm-5 col-form-label">Are you a current employee of Commissionaires Great Lakes?</label>
    <div class="col-sm-7">
        {{ Form::select('current_employee_commissionaries',[null=>'If the answer is "Yes", please type in the answers below.',"Yes"=>"Yes","No"=>"No"],old('current_employee_commissionaries',isset($candidateJob->candidate->experience->current_employee_commissionaries) ? $candidateJob->candidate->experience->current_employee_commissionaries :""),array('class' => 'form-control','required'=>TRUE,'id'=>'current_employee')) }}
        <div class="form-control-feedback">
            {!! $errors->first('current_employee_commissionaries') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>

<div id="current_employee_qstn" class="{{ @$candidateJob->candidate->experience->current_employee_commissionaries!='Yes'?'hide-this-block':'' }}">
    <div class="form-group row  {{ $errors->has('employee_number') ? 'has-error' : '' }}"  id="employee_number">
        <label class="col-sm-5 col-form-label">What is your employee number?</label>
        <div class="col-sm-7">
            {{Form::number('employee_number',old('employee_number',isset($candidateJob->candidate->experience->employee_number) ? $candidateJob->candidate->experience->employee_number :""),array('class'=>'form-control','placeholder'=>"Employee Number",'maxlength'=>15,'min'=>0))}}
            <div class="form-control-feedback">
                {!! $errors->first('employee_number') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('currently_posted_site') ? 'has-error' : '' }}" id="currently_posted_site">
        <label class="col-sm-5 col-form-label">Which site are you currently posted?</label>
        <div class="col-sm-7">
            {{Form::text('currently_posted_site',old('currently_posted_site',isset($candidateJob->candidate->experience->currently_posted_site) ? $candidateJob->candidate->experience->currently_posted_site :""),array('class'=>'form-control','placeholder'=>"Currently Posted Site"))}}
            <div class="form-control-feedback">
                {!! $errors->first('currently_posted_site') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('position') ? 'has-error' : '' }}" id="position">
        <label class="col-sm-5 col-form-label">What is your position?</label>
        <div class="col-sm-7">
            {{ Form::select('position',[null=>'Please Select',"Guard"=>"Guard","Supervisor"=>"Supervisor"],old('position',isset($candidateJob->candidate->experience->position) ? $candidateJob->candidate->experience->position :""),array('class' => 'form-control')) }}
            <div class="form-control-feedback">
                {!! $errors->first('position', '<span class="text-danger align-middle font-12"><i class="fa fa-close"></i>:message</span>') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('hours_per_week') ? 'has-error' : '' }}" id="hours_per_week">
        <label class="col-sm-5 col-form-label">How many hours per week?</label>
        <div class="col-sm-7">
            {{Form::text('hours_per_week',old('hours_per_week',isset($candidateJob->candidate->experience->hours_per_week) ? $candidateJob->candidate->experience->hours_per_week :""),array('class'=>'form-control','placeholder'=>"Hours per Week"))}}
            <div class="form-control-feedback">
                {!! $errors->first('hours_per_week') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
</div>
<div class="form-group row {{ $errors->has('applied_employment') ? 'has-error' : '' }}" id="applied_employment">
    <label class="col-sm-5 col-form-label">Have you ever been hired by Commissionaires Great Lakes in the past?</label>
    <div class="col-sm-7">
        {{ Form::select('applied_employment',[null=>'If the answer is "Yes", please type in the answers below.',"Yes"=>"Yes","No"=>"No"],old('applied_employment',isset($candidateJob->candidate->experience->applied_employment) ? $candidateJob->candidate->experience->applied_employment :""),array('class' => 'form-control','required'=>TRUE,'id'=>'applied_job')) }}
        <div class="form-control-feedback">
            {!! $errors->first('applied_employment') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div id="applied_job_qstn" class="{{ @$candidateJob->candidate->experience->applied_employment!='Yes'?'hide-this-block':'' }}">
    <div class="form-group row"  {{ $errors->has('position_applied') ? 'has-error' : '' }}" id="position_applied">
        <label class="col-sm-5 col-form-label">What was your position?</label>
        <div class="col-sm-7">
            {{Form::text('position_applied',old('position_applied',isset($candidateJob->candidate->experience->position_applied) ? $candidateJob->candidate->experience->position_applied :""),array('class'=>'form-control','placeholder'=>"Position",'maxlength'=>100))}}
            <div class="form-control-feedback">
                {!! $errors->first('position_applied ') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('start_date_position_applied') ? 'has-error' : '' }}" id="start_date_position_applied">
        <label class="col-sm-5 col-form-label">When was your start date?</label>
        <div class="col-sm-7">
            {{Form::text('start_date_position_applied',old('start_date_position_applied',isset($candidateJob->candidate->experience->start_date_position_applied) ? $candidateJob->candidate->experience->start_date_position_applied :""),array('class'=>'form-control datepicker','placeholder'=>"Start Date",'max'=>"2900-12-31"))}}
            <div class="form-control-feedback">
                {!! $errors->first('start_date_position_applied', '<span class="text-danger align-middle font-12"><i class="fa fa-close"></i>:message</span>') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('end_date_position_applied') ? 'has-error' : '' }}" id="end_date_position_applied">
        <label class="col-sm-5 col-form-label">When was your end date?</label>
        <div class="col-sm-7">
            {{Form::text('end_date_position_applied',old('end_date_position_applied',isset($candidateJob->candidate->experience->end_date_position_applied) ? $candidateJob->candidate->experience->end_date_position_applied :""),array('class'=>'form-control datepicker','placeholder'=>"End Date",'max'=>"2900-12-31"))}}
            <div class="form-control-feedback">
                {!! $errors->first('end_date_position_applied') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
</div>


<div class="form-group row {{ $errors->has('employed_by_corps') ? 'has-error' : '' }}" id="employed_by_corps">
    <label class="col-sm-5 col-form-label">Have you ever been employed by the Corps of Commissionaires nationally (other division)?</label>
    <div class="col-sm-7">
        {{ Form::select('employed_by_corps',[null=>'If the answer is "Yes", please type in the answers below.',"Yes"=>"Yes","No"=>"No"],old('employed_by_corps',isset($candidateJob->candidate->experience->employed_by_corps) ? $candidateJob->candidate->experience->employed_by_corps :""),array('class' => 'form-control','required'=>TRUE,'id'=>'employed_job')) }}
        <div class="form-control-feedback">
            {!! $errors->first('employed_by_corps') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div id="employed_job_qstn" class="{{ @$candidateJob->candidate->experience->employed_by_corps!='Yes'?'hide-this-block':'' }}">
    <div class="form-group row  {{ $errors->has('position_employed') ? 'has-error' : '' }}" id="position_employed">
        <label class="col-sm-5 col-form-label">What was your position?</label>
        <div class="col-sm-7">
            {{Form::text('position_employed',old('position_employed',isset($candidateJob->candidate->experience->position_employed) ? $candidateJob->candidate->experience->position_employed :""),array('class'=>'form-control','placeholder'=>"Position",'maxlength'=>100))}}
            <div class="form-control-feedback">
                {!! $errors->first('position_employed') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('start_date_employed') ? 'has-error' : '' }}" id="start_date_employed">
        <label class="col-sm-5 col-form-label">When was your start date?</label>
        <div class="col-sm-7">
            {{Form::text('start_date_employed',old('start_date_employed',isset($candidateJob->candidate->experience->start_date_employed) ? $candidateJob->candidate->experience->start_date_employed :""),array('class'=>'form-control datepicker','placeholder'=>"Start Date",'max'=>"2900-12-31"))}}
            <div class="form-control-feedback">
                {!! $errors->first('start_date_employed') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('end_date_employed') ? 'has-error' : '' }}" id="end_date_employed">
        <label class="col-sm-5 col-form-label">When was your end date?</label>
        <div class="col-sm-7">
             {{Form::text('end_date_employed',old('end_date_employed',isset($candidateJob->candidate->experience->end_date_employed) ? $candidateJob->candidate->experience->end_date_employed :""),array('class'=>'form-control datepicker','placeholder'=>"End Date",'max'=>"2900-12-31"))}}
            <div class="form-control-feedback">
                {!! $errors->first('end_date_employed') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('location_employed') ? 'has-error' : '' }}" id="location_employed">
        <label class="col-sm-5 col-form-label">Which division did you work at?</label>
        <div class="col-sm-7">
            {{Form::select('location_employed',[null=>'Please select the appropriate division from the dropdown list']+$lookups['division'],old('location_employed',isset($candidateJob->candidate->experience->location_employed) ? $candidateJob->candidate->experience->location_employed :""),array('class'=>'form-control select2'))}}
            {{-- {{Form::text('location_employed',old('location_employed',isset($candidateJob->candidate->experience->location_employed) ? $candidateJob->candidate->experience->location_employed :""),array('class'=>'form-control','placeholder'=>"Location",'maxlength'=>255))}} --}}
            <div class="form-control-feedback">
                {!! $errors->first('location_employed') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('employee_num') ? 'has-error' : '' }}" id="employee_num">
        <label class="col-sm-5 col-form-label">What was your employee number?</label>
        <div class="col-sm-7">
            {{Form::text('employee_num',old('employee_num',isset($candidateJob->candidate->experience->employee_num) ? $candidateJob->candidate->experience->employee_num :""),array('class'=>'form-control','placeholder'=>"Employee Number"))}}
            <div class="form-control-feedback">
                {!! $errors->first('employee_num') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
</div>

<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Military Experience</label>
<div class="form-group row {{ $errors->has('veteran_of_armedforce') ? 'has-error' : '' }}" id="veteran_of_armedforce">
    <label class="col-sm-5 col-form-label">Are you a reservist/veteran of the Canadian Armed Forces, our allied forces, or RCMP?</label>
    <div class="col-sm-7">
        {{ Form::select('veteran_of_armedforce',[null=>'If the answer is "Yes", please type in the answers below.',"Yes"=>"Yes","No"=>"No"],old('veteran_of_armedforce',isset($candidateJob->candidate->miscellaneous->veteran_of_armedforce) ? $candidateJob->candidate->miscellaneous->veteran_of_armedforce :""),array('class' => 'form-control','required'=>TRUE,'id'=>'canadian_army')) }}
        <div class="form-control-feedback">
            {!! $errors->first('veteran_of_armedforce ') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div id="canadian_army_qstn"  class="{{ @$candidateJob->candidate->miscellaneous->veteran_of_armedforce!='Yes'?'hide-this-block':'' }}">
    <div class="form-group row {{ $errors->has('service_number') ? 'has-error' : '' }}" id="service_number">
        <label class="col-sm-5 col-form-label">What was your Service number?</label>
        <div class="col-sm-7">
            {{Form::text('service_number',old('service_number',isset($candidateJob->candidate->miscellaneous->service_number) ? $candidateJob->candidate->miscellaneous->service_number :""),array('class'=>'form-control','placeholder'=>"Service Number"))}}
            <div class="form-control-feedback">
                {!! $errors->first('service_number') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('canadian_force') ? 'has-error' : '' }}" id="canadian_force">
        <label class="col-sm-5 col-form-label">What was your Canadian Forces Branch or RCMP?</label>
        <div class="col-sm-7">
            {{Form::text('canadian_force',old('canadian_force',isset($candidateJob->candidate->miscellaneous->canadian_force) ? $candidateJob->candidate->miscellaneous->canadian_force :""),array('class'=>'form-control','placeholder'=>"Canadian Forces Branch or RCMP",'maxlength'=>255))}}
            <div class="form-control-feedback">
                {!! $errors->first('canadian_force') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('enrollment_date') ? 'has-error' : '' }}" id="enrollment_date">
        <label class="col-sm-5 col-form-label">When was your Enrollment date?</label>
        <div class="col-sm-7">
            {{Form::text('enrollment_date',old('enrollment_date',isset($candidateJob->candidate->miscellaneous->enrollment_date) ? $candidateJob->candidate->miscellaneous->enrollment_date :""),array('class'=>'form-control datepicker','placeholder'=>"Enrollment Date",'max'=>"2900-12-31"))}}
            <div class="form-control-feedback">
                {!! $errors->first('enrollment_date') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('release_date') ? 'has-error' : '' }}" id="release_date">
        <label class="col-sm-5 col-form-label">When was your Release date?</label>
        <div class="col-sm-7">
             {{Form::text('release_date',old('release_date',isset($candidateJob->candidate->miscellaneous->release_date) ? $candidateJob->candidate->miscellaneous->release_date :""),array('class'=>'form-control datepicker','placeholder'=>"Release Date",'max'=>"2900-12-31"))}}
            <div class="form-control-feedback">
                {!! $errors->first('release_date') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('item_release_number') ? 'has-error' : '' }}"  id="item_release_number">
        <label class="col-sm-5 col-form-label">What is your item release number?</label>
        <div class="col-sm-7">
            {{Form::text('item_release_number',old('item_release_number',isset($candidateJob->candidate->miscellaneous->item_release_number) ? $candidateJob->candidate->miscellaneous->item_release_number :""),array('class'=>'form-control','placeholder'=>"Item Release Number",'maxlength'=>255))}}
            <div class="form-control-feedback">
                {!! $errors->first('item_release_number') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('rank_on_release') ? 'has-error' : '' }}" id="rank_on_release">
        <label class="col-sm-5 col-form-label">What was your rank on release?</label>
        <div class="col-sm-7">
            {{Form::text('rank_on_release',old('rank_on_release',isset($candidateJob->candidate->miscellaneous->rank_on_release) ? $candidateJob->candidate->miscellaneous->rank_on_release :""),array('class'=>'form-control','placeholder'=>"Rank on Release",'maxlength'=>255))}}
            <div class="form-control-feedback">
                {!! $errors->first('rank_on_release') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('military_occupation') ? 'has-error' : '' }}" id="military_occupation">
        <label class="col-sm-5 col-form-label">What was your military occupation?</label>
        <div class="col-sm-7">
            {{Form::text('military_occupation',old('military_occupation',isset($candidateJob->candidate->miscellaneous->military_occupation) ? $candidateJob->candidate->miscellaneous->military_occupation :""),array('class'=>'form-control','placeholder'=>"Military Occupation",'maxlength'=>255))}}
            <div class="form-control-feedback">
                {!! $errors->first('military_occupation') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('reason_for_release') ? 'has-error' : '' }}" id="reason_for_release">
        <label class="col-sm-5 col-form-label">What was your reason for release?</label>
        <div class="col-sm-7">
            {{Form::text('reason_for_release',old('reason_for_release',isset($candidateJob->candidate->miscellaneous->reason_for_release) ? $candidateJob->candidate->miscellaneous->reason_for_release :""),array('class'=>'form-control','placeholder'=>"Release Reason",'maxlength'=>255))}}
            <div class="form-control-feedback">
                {!! $errors->first('reason_for_release') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
</div>
    <div class="form-group row {{ $errors->has('spouse_of_armedforce') ? 'has-error' : '' }}" id="spouse_of_armedforce">
    <label class="col-sm-5 col-form-label">Are you the spouse of someone who served in the Canadian Armed Forces?</label>
    <div class="col-sm-7">
        {{ Form::select('spouse_of_armedforce',[null=>'This is a mandatory field, please enter the required information',"Yes"=>"Yes","No"=>"No"],old('spouse_of_armedforce',isset($candidateJob->candidate->miscellaneous->spouse_of_armedforce) ? $candidateJob->candidate->miscellaneous->spouse_of_armedforce :""),array('class' => 'form-control','required'=>TRUE)) }}
        <div class="form-control-feedback">
            {!! $errors->first('spouse_of_armedforce ') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>

