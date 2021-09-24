
<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 orange table_title">
        <h4>Exit Interview </h4>
    </label>
    <div class="form-group row  {{ $errors->has('date') ? 'has-error' : '' }}" id="date">
        <label for="date" class="col-sm-5 col-form-label">Date</label>
        <div class="col-sm-6">
        {{ Form::text('date',old('date', (isset($current_date)?$current_date:date('Y-m-d'))),array('class'=>'form-control','required'=>true,'readonly'=>true)) }}
        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('date', ':message') !!}</div>
        </div>
        </div>

        <div class="form-group row  {{ $errors->has('time') ? 'has-error' : '' }}" id="time">
        <label for="time" class="col-sm-5 col-form-label">Time</label>
        <div class="col-sm-6">

        {{ Form::text('time',old('time', (isset($current_time)?$current_time:($current_time))),array('class'=>'form-control','required'=>true,'readonly'=>true)) }}
        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('time', ':message') !!}</div>
        </div>
        </div>

        <div class="form-group row  {{ $errors->has('rm') ? 'has-error' : '' }}" id="rm">
        <label for="rm" class="col-sm-5 col-form-label">RM</label>
        <div class="col-sm-6">
            {{ Form::text('regional_manager',old('regional_manager', isset($job)?$job->regional_manager:auth()->user()->full_name),array('placeholder'=>'Regional Manager','class'=>'form-control','required'=>true,'readonly'=>true,'onchange'=>"$('input[name=am_email]').val('')")) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('area_manager', ':message') !!}</div>
        </div>
        </div>

        <div class="form-group row  {{ $errors->has('project_name_id') ? 'has-error' : '' }}" id="project_name_id">
        <label for="project" class="col-sm-5 col-form-label">Project</label>
        <div class="col-md-6">
            <select class="form-control select2 option-adjust" id="project_list" name="project_name_id" required>
                <option value="">Please Select</option>
                @foreach($project_list as $id => $each_project)
                <option value="{{$id}}">{{$each_project}}</option>
                @endforeach
            </select>
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('project_name_id', ':message') !!}</div>

        </div>
        </div>

        <div class="form-group row  {{ $errors->has('employee_name_id') ? 'has-error' : '' }}" id="employee_name_id" required>
        <label for="empname" class="col-sm-5 col-form-label">Employee Name</label>
        <div class="col-md-6">

            <select class="form-control select2 option-adjust" id="employee_list" name="employee_name_id" required>
                <option value="">Please Select</option>
                @foreach($emp_list as $id => $each_emp)
                   {{--  <option value="{{$id}}">{{$each_emp->user->first_name.$each_emp->user->last_name."(".$each_emp->employee_no.")"}}</option> --}}
                    <option value="{{$id}}">{{$each_emp}}</option>
                @endforeach

            </select>
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('employee_name_id', ':message') !!}</div>
        </div>
        </div>

        <div id="reason_id" class="form-group row  {{ $errors->has('reason_id') ? 'has-error' : '' }}" id="reason_id">
            <label for="reason_id" class="col-sm-5 col-form-label">Reason</label>
            <div class="col-sm-6">
                {{ Form::select('reason_id',[null=>'Please Select','1'=>'Resigned','2'=>'Terminated or Removed from Site'], old('reason_id',@$job->permanent_id),array('class' => 'form-control reason_dropdown','required'=>true)) }}
                <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('reason_id', ':message') !!}</div>
            </div>
        </div>

        {{-- <div id="reason_dropdown_2" style="" class="form-group row  {{ $errors->has('permanent_id') ? 'has-error' : '' }}">
            <label for="reason_dropdown_2" class="col-sm-5 col-form-label">Why has the permanent position opened up?</label>
            <div class="col-sm-6">
                {{ Form::select('reason_id',[null=>'Please Select','1'=>'Resignation','2'=>'Termination'], old('reason_id',@$job->permanent_id),array('class' => 'form-control reason_dropdown')) }}
                <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('permanent_id', ':message') !!}</div>
            </div>
        </div> --}}

        <div id="resignation_reason_id" style="{{ empty(old('resignation_reason_id')) ? 'display:none' : '' }}" class="form-group row  {{ $errors->has('resignation_reason_id') ? 'has-error' : '' }}">
            <label for="resignation_reason_id" class="col-sm-5 col-form-label">Resignation Details<span class="mandatory">*</span></label>
            <div class="col-sm-6">
                {{ Form::select('resignation_reason_id',@$resignation_details, old('resignation_reason_id'),array('class' => 'form-control select2')) }}
                <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('resignation_reason_id', ':message') !!}</div>
            </div>
        </div>


        <div id="termination_reason_id" style="{{ empty(old('termination_reason_id')) ? 'display:none' : '' }}" class="form-group row  {{ $errors->has('termination_reason_id') ? 'has-error' : '' }}">
            <label for="termination_reason_id" class="col-sm-5 col-form-label">Termination Details<span class="mandatory">*</span></label>
            <div class="col-sm-6">
                {{ Form::select('termination_reason_id',@$termination_details, old('termination_reason_id'),array('class' => 'form-control')) }}
                <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('termination_reason_id', ':message') !!}</div>
            </div>
        </div>


        <div id="exit_interview_explantion" class="form-group row  {{ $errors->has('exit_interview_explantion') ? 'has-error' : '' }}">
        <label for="exit_interview_explantion" class="col-sm-5 col-form-label">Explanation
        </label>
        <div class="col-sm-6">
            {{Form::textarea('exit_interview_explantion',old('exit_interview_explantion',@$job->exit_interview_explantion),array('class'=>'form-control','placeholder'=>"Explanation",'maxlength'=>2000,'required'=>true))}}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('exit_interview_explantion', ':message') !!}</div>
        </div>
    </div>
