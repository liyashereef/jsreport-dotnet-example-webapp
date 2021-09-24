
    <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 orange">
        <h5>Job Posting Rationale </h5>
    </label>
    <div class="form-group row  {{ $errors->has('reason_id') ? 'has-error' : '' }}" id="reason_id">
        <label for="reason_dropdown_1" class="col-sm-5 col-form-label">What is the reason for the open position?</label>
        <div class="col-sm-6">
            {{ Form::select('reason_id',  @$lookups['job_requisition_reason_lookups'][0], old('reason_id', @$job->reason_id),array('class'=> 'form-control reason_dropdown','required'=>TRUE)) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('reason_id', ':message') !!}</div>
        </div>
    </div>

    <div id="reason_dropdown_2" style="{{ empty(old('permanent_id', @$job->permanent_id)) ? 'display:none' : '' }}" class="form-group row  {{ $errors->has('permanent_id') ? 'has-error' : '' }}">
        <label for="reason_dropdown_2" class="col-sm-5 col-form-label">Why has the permanent position opened up?</label>
        <div class="col-sm-6">
            {{ Form::select('permanent_id',  @$lookups['job_requisition_reason_lookups'][1], old('permanent_id',@$job->permanent_id),array('class' => 'form-control reason_dropdown')) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('permanent_id', ':message') !!}</div>
        </div>
    </div>

    <div id="reason_dropdown_3" style="{{ empty(old('temp_code_id', @$job->temp_code_id)) ? 'display:none' : '' }}" class="form-group row  {{ $errors->has('temp_code_id') ? 'has-error' : '' }}">
        <label for="temp_code_id" class="col-sm-5 col-form-label">Why has the temporary position opened up?</label>
        <div class="col-sm-6">
            {{ Form::select('temp_code_id',  @$lookups['job_requisition_reason_lookups'][2], old('temp_code_id',@$job->temp_code_id),array('class' => 'form-control reason_dropdown')) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('temp_code_id', ':message') !!}</div>
        </div>
    </div>

    <div id="resign_id" style="{{ empty(old('resign_id', @$job->resign_id)) ? 'display:none' : '' }}" class="form-group row  {{ $errors->has('resign_id') ? 'has-error' : '' }}">
        <label for="resign_id" class="col-sm-5 col-form-label">If the candidate was resigned, why?<span class="mandatory">*</span></label>
        <div class="col-sm-6">
            {{ Form::select('resign_id',  @$lookups['resignation_list'], old('resign_id', @$job->resign_id),array('class'=> 'form-control select2')) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('resign_id', ':message') !!}</div>
        </div>
    </div>

    <div id="terminate_id" style="{{ empty(old('terminate_id', @$job->terminate_id)) ? 'display:none' : '' }}" class="form-group row  {{ $errors->has('terminate_id') ? 'has-error' : '' }}">
        <label for="terminate_id" class="col-sm-5 col-form-label">If the candidate was terminated, why?<span class="mandatory">*</span></label>
        <div class="col-sm-6">
            {{ Form::select('terminate_id',  @$lookups['termination_list'], old('terminate_id',@$job->terminate_id),array('class' => 'form-control select2')) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('terminate_id', ':message') !!}</div>
        </div>
    </div>

    <div id="job_description" class="form-group row  {{ $errors->has('job_description') ? 'has-error' : '' }}">
        <label for="job_description" class="col-sm-5 col-form-label">Job Summary
            <br>
            <small>
                <b>(Enter a detailed job description for your site. The details will appear on the job posting site)</b>
            </small>
            <span class="mandatory">*</span>
        </label>
        <div class="col-sm-6">
            {{Form::textarea('job_description',old('job_description',@$job->job_description),array('class'=>'form-control editor ckeditor','placeholder'=>"Job Description",'maxlength'=>10000,'required'=>TRUE))}}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('job_description', ':message') !!}</div>
        </div>
    </div>

    <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 orange">
        <h5>General Information</h5>
    </label>
    <div class="form-group row  {{ $errors->has('area_manager') ? 'has-error' : '' }}">
        <label for="area_manager" class="col-sm-5 col-form-label">Who is the area manager assigned to the account?</label>
        <div class="col-sm-6">
            {{ Form::text('area_manager',old('area_manager', isset($job)?$job->area_manager:auth()->user()->full_name),array('placeholder'=>'Area Manager','class'=>'form-control','required'=>true,'onchange'=>"$('input[name=am_email]').val('')")) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('area_manager', ':message') !!}</div>
        </div>
    </div>

    <div id="am_email" class="form-group row  {{ $errors->has('am_email') ? 'has-error' : '' }}">
        <label for="am_email" class="col-sm-5 col-form-label">What is the area manager's email address?</label>
        <div class="col-sm-6">
            {{ Form::text('am_email',old('am_email', isset($job)?$job->am_email:auth()->user()->email),array('placeholder'=>'Area Manager\'s Email','class'=>'form-control','required'=>true)) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('am_email', ':message') !!}</div>
        </div>
    </div>

    <div id="requisition_date" class="form-group row ">
        <label for="requisition_date" class="col-sm-5 col-form-label">When was the requisition created? (Date)</label>
        <div class="col-sm-6">
            {{ Form::text('requisition_date',old('requisition_date', (isset($job)?$job->requisition_date:date('Y-m-d'))),array('class'=>'form-control','required'=>true,'readonly'=>true)) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('requisition_date', ':message') !!}</div>
        </div>
    </div>

    <div id="customer_id" class="form-group row   {{ $errors->has('customer_id') ? 'has-error' : '' }}">
        <label for="customer_id" class="col-sm-5 col-form-label">Please enter the post/project number</label>
        <div class="col-sm-6">
            @if(isset($job->customer_id))
            {{ Form::select('customer_id',  @$lookups['customers'], old('customer_id', @$job->customer_id),array('class'=> 'form-control','required'=>TRUE,'style'=>'pointer-events:none;background-color:#e9ecef;')) }}
            @else
            {{ Form::select('customer_id',  @$lookups['customers'], old('customer_id', @$job->customer_id),array('class'=> 'form-control select2','required'=>TRUE)) }}
            @endif
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('customer_id', ':message') !!}</div>
        </div>
    </div>

    <div id="client_name" class="form-group row ">
        <label for="client_name" class="col-sm-5 col-form-label">Who is the client?</label>
        <div class="col-sm-6">
            {{ Form::text('client_name',old('client_name', @$job->customer->client_name),array('placeholder'=>'Client Name','class'=>'form-control','readonly'=>true)) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('client_name', ':message') !!}</div>
        </div>
    </div>

    <div id="address" class="form-group row  {{ $errors->has('address') ? 'has-error' : '' }}">
        <label for="address" class="col-sm-5 col-form-label">Post Address</label>
        <div class="col-sm-6">
            {{ Form::text('address',old('address', @$job->customer->address),array('placeholder'=>'Post Address','class'=>'form-control','readonly'=>true)) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('address', ':message') !!}</div>
        </div>
    </div>

    <div id="city" class="form-group row  {{ $errors->has('city') ? 'has-error' : '' }}">
        <label for="city" class="col-sm-5 col-form-label">Post City</label>
        <div class="col-sm-6">
            {{ Form::text('city',old('city', @$job->customer->city),array('placeholder'=>'City','class'=>'form-control','readonly'=>true)) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('city', ':message') !!}</div>
        </div>
    </div>

    <div id="postal_code" class="form-group row  {{ $errors->has('postal_code') ? 'has-error' : '' }}">
        <label for="postal_code" class="col-sm-5 col-form-label">Postal Code</label>
        <div class="col-sm-6">
            {{ Form::text('postal_code',old('postal_code', @$job->customer->postal_code),array('placeholder'=>'Postal Code','class'=>'form-control','readonly'=>true)) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('postal_code', ':message') !!}</div>
        </div>
    </div>

    <div id="requester" class="form-group row ">
        <label for="requester" class="col-sm-5 col-form-label">Who is requesting the job posting?</label>
        <div class="col-sm-6">
            {{ Form::text('requester',(isset($job)?$job->requester:auth()->user()->full_name),array('placeholder'=>'Requestor','class'=>'form-control','required'=>false)) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('requester', ':message') !!}</div>
        </div>
    </div>

    <div id="email" class="form-group row">
        <label for="email" class="col-sm-5 col-form-label">What is the requestor's email address? (if applicable)</label>
        <div class="col-sm-6">
            {{ Form::text('email',(isset($job)?$job->email:auth()->user()->email),array('placeholder'=>'Requestor Email','class'=>'form-control','required'=>false)) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('email', ':message') !!}</div>
        </div>
    </div>

    <div id="phone" class="form-group row  {{ $errors->has('phone') ? 'has-error' : '' }}">
        <label for="phone" class="col-sm-5 col-form-label">What is the requestor's phone number? (if applicable)</label>
        <div class="col-sm-6">
            {{ Form::text('phone',(isset($job)?$job->phone:auth()->user()->employee->phone),array('placeholder'=>'Requestor Phone No','class'=>'form-control phone','required'=>false)) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('phone', ':message') !!}</div>
        </div>
    </div>

    <div id="position" class="form-group row  {{ $errors->has('position') ? 'has-error' : '' }}">
        <label for="position" class="col-sm-5 col-form-label">What is the requestor's position?</label>
        <div class="col-sm-6">
            {{ Form::select('position',  @$lookups['positions_lookups']+[0=>'Other'], (isset($job)?$job->position:auth()->user()->employee->position_id),array('class'=> 'form-control select2','required'=>TRUE)) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('position', ':message') !!}</div>
        </div>
    </div>

    <div id="employee_num" class="form-group row  {{ $errors->has('employee_num') ? 'has-error' : '' }}">
        <label for="employee_num" class="col-sm-5 col-form-label">Requestor's Employee Number</label>
        <div class="col-sm-6">
            {{ Form::text('employee_num',(isset($job)?$job->employee_num:auth()->user()->employee->employee_no),array('placeholder'=>'Requestor\'s Employee Number','class'=>'form-control','required'=>true)) }}
            <div class="form-control-feedback">
                <span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('employee_num', ':message') !!}</div>
        </div>
    </div>

    <div id="assignment_type_id" class="form-group row  {{ $errors->has('assignment_type_id') ? 'has-error' : '' }}">
        <label for="assignment_type_id" class="col-sm-5 col-form-label">Type of Assignment</label>
        <div class="col-sm-6">
            {{ Form::select('assignment_type_id',  @$lookups['assignment_type_lookups'], old('assignment_type_id',@$job->assignment_type_id),array('class' => 'form-control select2','required'=>TRUE)) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('assignment_type_id', ':message') !!}</div>
        </div>
    </div>

    <div id="required_job_start_date" class="form-group row  {{ $errors->has('required_job_start_date') ? 'has-error' : '' }}">
        <label for="required_job_start_date" class="col-sm-5 col-form-label">When do we need the candidate to start at the new client?</label>
        <div class="col-sm-6">
            {{ Form::text('required_job_start_date',old('required_job_start_date', @$job->required_job_start_date),array('placeholder'=>'New client start date','class'=>'form-control datepicker','required'=>true)) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('required_job_start_date', ':message') !!}</div>
        </div>
    </div>
    <div id="time" class="form-group row  {{ $errors->has('time') ? 'has-error' : '' }}">
        <label for="time" class="col-sm-5 col-form-label">At what time?</label>
        <div class="col-sm-6">
            {{ Form::text('time',old('time', isset($job->time)?\Carbon\Carbon::parse($job->time)->format('h:i a'):''),array('placeholder'=>'Time (HH:MM AM/PM)','class'=>'form-control','required'=>true,'id'=>'timepicker')) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('time', ':message') !!}</div>
        </div>
    </div>

    <div id="ongoing" class="form-group row  {{ $errors->has('ongoing') ? 'has-error' : '' }}">
        <label for="ongoing" class="col-sm-5 col-form-label">Is the position an ongoing permanent position?</label>
        <div class="col-sm-6">
            {{ Form::select('ongoing', [null=>'Please Select',"Yes"=>"Yes","No"=>"No"], old('ongoing', @$job->ongoing),array('class' =>
            'form-control ','onchange'=>"$('#end').toggle(($(this).val() != 'Yes'));",'required'=>TRUE)) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('ongoing', ':message') !!}</div>
        </div>
    </div>

    <div id="end" style="{{ old('ongoing', @$job->ongoing)==" Yes " ? 'display:none' : '' }};" class="form-group row  {{ $errors->has('end') ? 'has-error' : '' }}">
        <label for="end" class="col-sm-5 col-form-label">If the position is not ongoing, when is the end date?</label>
        <div class="col-sm-6">
            {{ Form::text('end',old('end', @$job->end),array('placeholder'=>'End Date','class'=>'form-control datepicker','required'=>false)) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('end', ':message') !!}</div>
        </div>
    </div>

    <div id="training_id" class="form-group row  {{ $errors->has('training_id') ? 'has-error' : '' }}">
        <label for="training_id" class="col-sm-5 col-form-label">Does the position require training?</label>
        <div class="col-sm-6">
            {{ Form::select('training_id',  @$lookups['training_lookups'], old('training_id', @$job->training_id),array('class'=> 'form-control ','required'=>TRUE)) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('training_id', ':message') !!}</div>
        </div>
    </div>

    <div id="training_time" class="form-group row  {{ $errors->has('training_time') ? 'has-error' : '' }}">
        <label for="training_time" class="col-sm-5 col-form-label">How many hours of training will be required?</label>
        <div class="col-sm-6">
            {{ Form::text('training_time',old('training_time', @$job->training_time),array('placeholder'=>'Training Hours','class'=>'form-control','required'=>true)) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('training_time', ':message') !!}</div>
        </div>
    </div>

    <div id="training_timing_id" class="form-group row  {{ $errors->has('training_timing_id') ? 'has-error' : '' }}">
        <label for="training_timing_id" class="col-sm-5 col-form-label">Is the training required as a condition of client onboarding?</label>
        <div class="col-sm-6">
            {{ Form::select('training_timing_id',  @$lookups['training_timing_lookups'], old('training_timing_id',@$job->training_timing_id),array('class' => 'form-control ','required'=>TRUE)) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('training_timing_id', ':message') !!}</div>
        </div>
    </div>

    <div id="course" class="form-group row  {{ $errors->has('course') ? 'has-error' : '' }}">
        <label for="course" class="col-sm-5 col-form-label">Is there a specific course required for this role?</label>
        <div class="col-sm-6">
            {{ Form::text('course',old('course', @$job->course),array('placeholder'=>'Specific Course','class'=>'form-control','required'=>false)) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('course', ':message') !!}</div>
        </div>
    </div>

    <div id="open_position_id" class="form-group row  {{ $errors->has('open_position_id') ? 'has-error' : '' }}">
        <label for="open_position_id" class="position col-sm-5 col-form-label">What is the position being hired?</label>
        <div class="col-sm-6">
            {{ Form::select('open_position_id',  @$lookups['positions_lookups'], old('open_position_id', @$job->open_position_id),array('class'=> 'form-control select2','onfocus'=>"this.setAttribute('PrvSelectedValue', this.value);",'onchange'=>"confirmJob($(this))",'required'=>TRUE))}}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('open_position_id', ':message') !!}</div>
        </div>
    </div>

    <div id="no_of_vaccancies" class="form-group row   {{ $errors->has('no_of_vaccancies') ? 'has-error' : '' }}">
        <label for="no_of_vaccancies" class="col-sm-5 col-form-label">How many posts need to be filled?</label>
        <div class="col-sm-6">
            {{ Form::text('no_of_vaccancies',old('no_of_vaccancies', @$job->no_of_vaccancies),array('placeholder'=>'Number of Vaccancies','class'=>'form-control','required'=>true)) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('no_of_vaccancies', ':message') !!}</div>
        </div>
    </div>

    <div id="notes" class="form-group row  {{ $errors->has('notes') ? 'has-error' : '' }}">
        <label for="notes" class="col-sm-5 col-form-label">Are there any special requirements beyond the standard job description?</label>
        <div class="col-sm-6">
            {{ Form::textarea('notes',old('notes', @$job->notes),array('placeholder'=>'Are there any special requirements beyond the standard job description ?','class'=>'form-control','required'=>false,'max'=>500,'rows'=>6)) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('notes', ':message') !!}</div>
        </div>
    </div>

    <div id="shifts" class="form-group row  {{ $errors->has('shifts') ? 'has-error' : '' }}">
        <label for="shifts" class="col-sm-5 col-form-label">What are the shift requirements? (select all that apply)
            <span class="mandatory">*</span>
        </label>
        <div class="col-sm-6">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <input type="checkbox" id="ckbCheckAll" name="" value="all" onchange="$(this).parents('ul').find('input:checkbox').not(this).prop('checked', this.checked);"> All</li>
                <li class="nav-item checkBoxClass_shift">
                    <input type="checkbox" name="shifts[]" value="Days" @if (isset($shifts)) {{ in_array( "Days", $shifts) ?
                        'checked="checked"' : '' }} @endif> Days</li>
                <li class="nav-item checkBoxClass_shift">
                    <input type="checkbox" name="shifts[]" value="Afternoons" @if (isset($shifts)) {{ in_array( "Afternoons",
                        $shifts) ? 'checked="checked"' : '' }} @endif> Afternoons</li>
                <li class="nav-item checkBoxClass_shift">
                    <input type="checkbox" name="shifts[]" value="Evenings" @if (isset($shifts)) {{ in_array( "Evenings", $shifts)
                        ? 'checked="checked"' : '' }} @endif> Evenings</li>
                <li class="nav-item checkBoxClass_shift">
                    <input type="checkbox" name="shifts[]" value="Overnight" @if (isset($shifts)) {{ in_array( "Overnight", $shifts)
                        ? 'checked="checked"' : '' }} @endif> Overnight</li>
                <li class="nav-item checkBoxClass_shift">
                    <input type="checkbox" name="shifts[]" value="Statutory holidays" @if (isset($shifts)) {{ in_array( "Statutory holidays", $shifts)
                        ? 'checked="checked"' : '' }} @endif> Statutory holidays</li>
                <li class="nav-item checkBoxClass_shift">
                    <input type="checkbox" name="shifts[]" value="Continental (12 Hours Shift)" @if (isset($shifts)) {{ in_array(
                        "Continental (12 Hours Shift)", $shifts) ? 'checked="checked"' : '' }} @endif> Continental (12 Hours Shift)</li>
            </ul>
            <div class="form-control-feedback">
                <span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('shifts', ':message') !!}</div>
        </div>
    </div>

    <div id="days_required" class="form-group row  {{ $errors->has('days_required') ? 'has-error' : '' }}">
        <label for="days_required" class="col-sm-5 col-form-label">Days Required
            <span class="mandatory">*</span>
        </label>
        <div class="col-sm-6">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <input type="checkbox" id="ckbsCheckAll" name="" value="all" onchange="$(this).parents('ul').find('input:checkbox').not(this).prop('checked', this.checked);"> All
                    <li class="nav-item checkBoxClass_days">
                        <input type="checkbox" name="days_required[]" value="Monday" @if (isset($days_required)) {{ in_array(
                            "Monday", $days_required) ? 'checked="checked"' : '' }} @endif> Monday</li>
                    <li class="nav-item checkBoxClass_days">
                        <input type="checkbox" name="days_required[]" value="Tuesday" @if (isset($days_required)) {{ in_array(
                            "Tuesday", $days_required) ? 'checked="checked"' : '' }} @endif> Tuesday</li>
                    <li class="nav-item checkBoxClass_days">
                        <input type="checkbox" name="days_required[]" value="Wednesday" @if (isset($days_required)) {{ in_array(
                            "Wednesday", $days_required) ? 'checked="checked"' : '' }} @endif> Wednesday</li>
                    <li class="nav-item checkBoxClass_days">
                        <input type="checkbox" name="days_required[]" value="Thursday" @if (isset($days_required)) {{ in_array(
                            "Thursday", $days_required) ? 'checked="checked"' : '' }} @endif> Thursday</li>
                    <li class="nav-item checkBoxClass_days">
                        <input type="checkbox" name="days_required[]" value="Friday" @if (isset($days_required)) {{ in_array(
                            "Friday", $days_required) ? 'checked="checked"' : '' }} @endif> Friday</li>
                    <li class="nav-item checkBoxClass_days">
                        <input type="checkbox" name="days_required[]" value="Saturday" @if (isset($days_required)) {{ in_array(
                            "Saturday", $days_required) ? 'checked="checked"' : '' }} @endif> Saturday</li>
                    <li class="nav-item checkBoxClass_days">
                        <input type="checkbox" name="days_required[]" value="Sunday" @if (isset($days_required)) {{ in_array(
                            "Sunday", $days_required) ? 'checked="checked"' : '' }} @endif> Sunday </li>
            </ul>
            <div class="form-control-feedback">
                <span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('days_required', ':message') !!}</div>
        </div>
    </div>

    <div id="criterias" class="form-group row  {{ $errors->has('criterias') ? 'has-error' : '' }}">
        <label for="criterias" class="col-sm-5 col-form-label">What are the position requirements? (select all that apply)
            <small>Hold down CTRL to make multiple selections</small>
        </label>
        <div class="col-sm-6">
            {{ Form::select('criterias[]', @$lookups['criteria_lookups'], $criterias,array('class' => 'form-control ','multiple'=>TRUE,'required'=>TRUE,'style'=>'height:130px;')) }}
            <div class="form-control-feedback">
                <span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('criterias', ':message') !!}</div>

        </div>
    </div>
   <div id="total_experience" class="form-group row   {{ $errors->has('total_experience') ? 'has-error' : '' }}">
        <label for="total_experience" class="col-sm-5 col-form-label">How many years of experience is required for this role?</label>
        <div class="col-sm-6">
            {{ Form::text('total_experience',old('total_experience', @$job->total_experience),array('placeholder'=>'Total experiences','class'=>'form-control','required'=>true)) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('total_experience', ':message') !!}</div>
        </div>
    </div>

    @for($i=0;$i<10;$i++)
        <div style="{{ $i>0 && !isset($job->experiences[$i])?'display:none;':'' }}" class="form-group row" id="experiences.{{$i}}.year">
        <label for="experiences" class="col-sm-5 col-form-label">@if($i==0)What are the additional requirements for the role? (years)@endif</label>
        <div class="col-sm-3">
            {{ Form::select('experiences['.$i.'][experience_id]',  @$lookups['experience_lookups'], @old('experiences['.$i.'][experience_id]',@$job->experiences[$i]->experience_id),array('class'=> 'form-control form-control-danger select2')) }}
            <div class="form-control-feedback"></div>
        </div>
        <div class="col-sm-3">
            {{ Form::text('experiences['.$i.'][year]',@old('experiences['.$i.'][year]',@$job->experiences[$i]->year),array('class'=>"form-control form-control-danger")) }}
            <div class="form-control-feedback">
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
        <div class="col-sm-1">
            @if($i!=9)
            <a title="Add another experience" href="javascript:;" class="add_button" onclick="$(this).parents('.form-group').next('.form-group').show();refreshSideMenu();">
                <i class="fa fa-plus" aria-hidden="true"></i>
            </a>
            @endif
            @if($i!=0)
            <a href="javascript:void(0);" class="remove_button" title="Remove field" onclick="$(this).parents('.form-group').hide().find('select,input').val('');">
                <i class="fa fa-minus" aria-hidden="true"></i>
            </a>
            @endif
        </div>
        <div class="form-control-feedback"></div>
        </div>
    @endfor

        <div id="vehicle" class="form-group row {{ $errors->has('vehicle') ? 'has-error' : '' }}">
            <label for="vehicle" class="col-sm-5 col-form-label">Is a vehicle required for the position?</label>
            <div class="col-sm-6">
                {{ Form::select('vehicle', [null=>'Please Select','No'=>'No','Yes'=>'Yes'], old('vehicle', @$job->vehicle),array('class' =>'form-control ','required'=>TRUE)) }}
                <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('vehicle', ':message') !!}</div>
            </div>
        </div>

        <div class="form-group row ">
            <label class="col-sm-5 col-form-label">What is the hourly wage ($)?</label>
            <label class="col-sm-1 col-form-label">$</label>
            <div id="wage" class="col-sm-5{{ $errors->has('wage') ? 'has-error' : '' }}">
                {{ Form::text('wage',old('wage', @$job->wage),array('placeholder'=>'Wage ($)','class'=>'form-control','required'=>true)) }}
                <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('wage', ':message') !!}</div>
            </div>

        </div>


         <div class="form-group row {{ $errors->has('hours_per_week') ? 'has-error' : '' }}" id="hours_per_week">
            <label class="col-sm-5 col-form-label">Hours per week</label>
            <div class="col-sm-6">
            {{ Form::number('hours_per_week',old('hours_per_week', @$job->hours_per_week),array('placeholder'=>'Hours per week','class'=>'form-control','required'=>true, 'min'=>1)) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('hours_per_week', ':message') !!}</div>
        </div>

        </div>

        <div id="remarks" class="form-group row  {{ $errors->has('remarks') ? 'has-error' : '' }}">
            <label for="remarks" class="col-sm-5 col-form-label">Are there any final remarks?</label>
            <div class="col-sm-6">
                {{ Form::textarea('remarks',old('remarks', @$job->remarks),array('placeholder'=>'Remarks if any','class'=>'form-control','maxlength'=>500,'required'=>false)) }}
                <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('remarks', ':message') !!}</div>
            </div>
        </div>
         

        <div class="form-group row" id="onboardingDocument">
            @if (isset($jobDocumentAllocation) && count($jobDocumentAllocation) != 0)
            <label for="onboardingDocument" class="col-sm-5 col-form-label">On Boarding Documents</label>
            <div class="col-sm-6">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Document Name</th>
                            <th>Display</th>
                            <th>Mandatory</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $processTab = '';
                            $docCount = 0;
                        @endphp
                        @foreach ($jobDocumentAllocation as $key => $value)
                            @if ($key != $processTab)
                            @php $processTab = $key; @endphp
                                <tr>
                                    <th>{{$key}}</th>
                                    <td></td>
                                    <td></td>
                                </tr>
                            @endif
                                @foreach ($value as $k => $v)
                                    <tr>
                                        <td>
                                            <input type="hidden" name="document_allocation_with_trashed[{{$docCount}}][id]" value="{{$v['id']}}">
                                            <input type="hidden" name="document_allocation_with_trashed[{{$docCount}}][process_tab_id]" value="{{$v['process_tab_id']}}">
                                            {{$v['document_allocation_with_trashed']['document_name']}}
                                        </td>
                                        <td>
                                            <input
                                            type="hidden"
                                            name="document_allocation_with_trashed[{{$docCount}}][display][{{$v['document_allocation_with_trashed']['id']}}]"
                                            value="0">
                                            <input
                                            type="checkbox"
                                            name="document_allocation_with_trashed[{{$docCount}}][display][{{$v['document_allocation_with_trashed']['id']}}]"
                                            value="1"
                                            id="display_{{$docCount}}"
                                            onchange="display(this)"
                                            style="width: 20px; height: 20px;"
                                            @if (isset($v['display'])) {{ $v['display'] == 1? 'checked="checked"' : '' }} @endif>
                                        </td>
                                        <td id="document_{{$docCount}}">
                                            <input
                                            type="hidden"
                                            name="document_allocation_with_trashed[{{$docCount}}][mandatory][{{$v['document_allocation_with_trashed']['id']}}]"
                                            value="0">
                                            <input
                                            type="checkbox"
                                            name="document_allocation_with_trashed[{{$docCount}}][mandatory][{{$v['document_allocation_with_trashed']['id']}}]"
                                            value="1"
                                            id="mandatory_{{$docCount}}"
                                            onchange="mandatory(this)"
                                            style="width: 20px; height: 20px;"
                                            @if (isset($v['is_mandatory'])) {{ $v['is_mandatory'] == 1? 'checked="checked"' : '' }} @endif>
                                        </td>
                                    </tr>
                                    @php
                                        $docCount++;
                                    @endphp
                                @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        </div>
<script>
    $('input[name="hours_per_week"]').on('input propertychange paste', function (e) {
        var reg = /^0+/gi;
        if (this.value.match(reg)) {
            this.value = this.value.replace(reg, '');
        }
    });
</script>
