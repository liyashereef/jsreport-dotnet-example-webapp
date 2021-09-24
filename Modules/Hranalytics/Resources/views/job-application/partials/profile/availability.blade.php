<div class="form-group row {{ $errors->has('availability_start') ? 'has-error' : '' }}" id="availability_start">
    <label for="availability_start" class="col-sm-5 col-form-label">When you are available to start?</label>
    <div class="col-sm-7">
        {{Form::text('availability_start',old('availability_start',isset($candidateJob->candidate->availability->availability_start) ? $candidateJob->candidate->availability->availability_start :""),array('class'=>'form-control datepicker','required'=>TRUE))}}
        <div class="form-control-feedback">
            {!! $errors->first('availability_start') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div class="form-group row {{ $errors->has('current_availability') ? 'has-error' : '' }}" id="current_availability" >
    <label for="current_availability" class="col-sm-5 col-form-label" >What is your current availability?</label>
    <div class="col-sm-7">
        {{ Form::select('current_availability',[null=>'Please select from the appropriate dropdown box',"Full-Time (Around 40 hours per week)"=>"Full-Time (Around 40 hours per week)","Part-Time (Less than 40 hours per week)"=>"Part-Time (Less than 40 hours per week)"],old('current_availability',isset($candidateJob->candidate->availability->current_availability) ? $candidateJob->candidate->availability->current_availability :""),array('class' => 'form-control','required'=>TRUE,'id'=>'current_available','required'=>TRUE)) }}
        <div class="form-control-feedback">{!! $errors->first('current_availability') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div id="availability_explanation" class="{{ @$candidateJob->candidate->availability->current_availability!='Part-Time (Less than 40 hours per week)'?'hide-this-block':'' }}">
<div class="form-group row {{ $errors->has('availability_explanation') ? 'has-error' : '' }}" id="availability_explanation" style="display: {{(isset($candidateJob->candidate->availability->current_availability) ? $candidateJob->candidate->availability->current_availability :"" )=="Part-Time (Less than 40 hours per week)" }} ? "block;" : "none;" ">
    <label for="availability_explanation" class="col-sm-5 col-form-label">If only part time - please briefly explain your limitation</label>
    <div class="col-sm-7">
        {{Form::textarea('availability_explanation',old('availability_explanation',isset($candidateJob->candidate->availability->availability_explanation) ? $candidateJob->candidate->availability->availability_explanation :""),array('class'=>'form-control','placeholder'=>"Accepted only 500 characters", 'maxlength'=>"500",'rows'=>6))}}
        <div class="form-control-feedback">{!! $errors->first('availability_explanation') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
</div>

<div class="form-group row">
    <label for="days_available" class="col-sm-5 col-form-label">Which days are you available to work?<br><i>(Select those days you are available to work. If you are joining our spares pool, we will expect you to work on the days and shifts you select)</i></label>
    <div class="col-sm-7">
        <ul class="nav flex-column">
            <li class="nav-item"><input type="checkbox" name="" id="ckbsCheckAll" value="all" onchange="$(this).parents('ul').find('input:checkbox').not(this).prop('checked', this.checked);"> All
           <li class="nav-item checkBoxClass_days"><input type="checkbox" name="days_required[]" value="Monday" @if (isset($days_required)) {{ in_array("Monday", $days_required)  ? 'checked="checked"' : '' }} @endif> Monday</li>
           <li class="nav-item checkBoxClass_days"> <input type="checkbox" name="days_required[]" value="Tuesday" @if (isset($days_required)) {{ in_array("Tuesday", $days_required)  ? 'checked="checked"' : '' }} @endif> Tuesday</li>
           <li class="nav-item checkBoxClass_days"> <input type="checkbox" name="days_required[]" value="Wednesday" @if (isset($days_required)) {{ in_array("Wednesday", $days_required)  ? 'checked="checked"' : '' }} @endif > Wednesday </li>
           <li class="nav-item checkBoxClass_days"> <input type="checkbox" name="days_required[]" value="Thursday" @if (isset($days_required)) {{ in_array("Thursday", $days_required)  ? 'checked="checked"' : '' }} @endif > Thursday </li>
           <li class="nav-item checkBoxClass_days"> <input type="checkbox" name="days_required[]" value="Friday" @if (isset($days_required)) {{ in_array("Friday", $days_required)  ? 'checked="checked"' : '' }} @endif> Friday </li>
           <li class="nav-item checkBoxClass_days"><input type="checkbox" name="days_required[]" value="Saturday" @if (isset($days_required)) {{ in_array("Saturday", $days_required)  ? 'checked="checked"' : '' }} @endif> Saturday </li>
           <li class="nav-item checkBoxClass_days"><input type="checkbox" name="days_required[]" value="Sunday" @if (isset($days_required)) {{ in_array("Sunday", $days_required)  ? 'checked="checked"' : '' }} @endif> Sunday </li>
         </ul>
        <div class="form-control-feedback">
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>

<div class="form-group row">
    <label for="shifts_available" class="col-sm-5 col-form-label">Which shifts are you willing to work?</label>
    <div class="col-sm-7">
        <ul class="nav flex-column">
            <li class="nav-item"><input type="checkbox" name="" id="ckbCheckAll" value="all" onchange="$(this).parents('ul').find('input:checkbox').not(this).prop('checked', this.checked);"> All</li>
            <li class="nav-item checkBoxClass_shift"><input type="checkbox" name="shifts[]" value="Days" @if (isset($shifts)) {{ in_array("Days", $shifts)  ? 'checked="checked"' : '' }} @endif> Days  </li>
           <li class="nav-item checkBoxClass_shift"> <input type="checkbox" name="shifts[]" value="Afternoons"  @if (isset($shifts)) {{ in_array("Afternoons", $shifts)  ? 'checked="checked"' : '' }} @endif> Afternoons</li>
            <li class="nav-item checkBoxClass_shift"><input type="checkbox" name="shifts[]" value="Evenings"  @if (isset($shifts)) {{ in_array("Evenings", $shifts)  ? 'checked="checked"' : '' }} @endif> Evenings</li>
            <li class="nav-item checkBoxClass_shift"><input type="checkbox" name="shifts[]" value="Overnight"  @if (isset($shifts)) {{ in_array("Overnight", $shifts)  ? 'checked="checked"' : '' }} @endif> Overnight</li>
           <li class="nav-item checkBoxClass_shift"> <input type="checkbox" name="shifts[]" value="Statutory holidays"  @if (isset($shifts)) {{ in_array("Statutory holidays", $shifts)  ? 'checked="checked"' : '' }} @endif> Statutory Holidays</li>
            <li class="nav-item checkBoxClass_shift"><input type="checkbox" name="shifts[]" value="Continental (12 Hours Shift)"  @if (isset($shifts)) {{ in_array("Continental (12 Hours Shift)", $shifts)  ? 'checked="checked"' : '' }} @endif> Continental (12 Hours Shift)</li>
        </ul>
        <div class="form-control-feedback">

            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>

<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Most of our assignments require employees to work 8-12 hours shifts on a rotating basis. As a result you are likely to be required to be available to work at any time day or night, 7 days per week. Please note that Monday to Friday or day shifts are rarely available.</label>
<div class="form-group row {{ $errors->has('understand_shift_availability') ? 'has-error' : '' }}" id="understand_shift_availability">
    <label for="understand_shift_availability" class="col-sm-5 col-form-label" >Do you understand the shift availability as noted above?</label>
    <div class="col-sm-7">
        {{ Form::select('understand_shift_availability',[null=>'You must select "yes" or "no" from the dropdown box',"Yes"=>"Yes","No"=>"No"],old('understand_shift_availability',isset($candidateJob->candidate->availability->understand_shift_availability) ? $candidateJob->candidate->availability->understand_shift_availability :""),array('class' => 'form-control','required'=>TRUE)) }}
        <div class="form-control-feedback">
            {!! $errors->first('understand_shift_availability') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div class="form-group row {{ $errors->has('available_shift_work') ? 'has-error' : '' }}" id="available_shift_work">
    <label for="available_shift_work" class="col-sm-5 col-form-label" >Are you available for shift work including evenings and nights?</label>
    <div class="col-sm-7">
        {{ Form::select('available_shift_work',[null=>'This is a mandatory field, please enter the required information',"Yes"=>"Yes","No"=>"No"],old('available_shift_work',isset($candidateJob->candidate->availability->available_shift_work) ? $candidateJob->candidate->availability->available_shift_work :""),array('class' => 'form-control','required'=>TRUE,'id'=>'shift_work')) }}
        <div class="form-control-feedback">
            {!! $errors->first('available_shift_work') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div class="form-group row {{ @$candidateJob->candidate->availability->available_shift_work!='No'?'hide-this-block':'' }}"  id="explanation_restrictions">
    <label for="explanation_restrictions" class="col-sm-5 col-form-label">If you answered "no", please explain your restrictions:</label>
    <div class="col-sm-7">
        {{Form::textarea('explanation_restrictions',old('explanation_restrictions',isset($candidateJob->candidate->availability->explanation_restrictions) ? $candidateJob->candidate->availability->explanation_restrictions :""),array('class'=>' form-control','placeholder'=>"Accepted only 500 characters",'maxlength'=>"500",'rows'=>6))}}
        <div class="form-control-feedback">
            {!! $errors->first('explanation_restrictions') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
