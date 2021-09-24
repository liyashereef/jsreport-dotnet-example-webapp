 <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 orange">
        <h5>Respondent</h5>
    </label>
    @php
    if(isset($rfpDetails)){
    $date=date('Y-m-d', strtotime($rfpDetails->created_at));
    }
    @endphp
    <div class="form-group row  {{ $errors->has('date') ? 'has-error' : '' }}" id="date">
        <label for="date" class="col-sm-5 col-form-label">Date</label>
        <div class="col-sm-6">
             {{ Form::text('date',isset($rfpDetails) ?$date :date('Y-m-d'),array('placeholder'=>'Area Manager','class'=>'form-control','readonly'=>true)) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('date', ':message') !!}</div>
        </div>
    </div>
     <div class="form-group row  {{ $errors->has('employee_id') ? 'has-error' : '' }}" id="employee_id">
        <label for="rfp_response_type_id" class="col-sm-5 col-form-label">Employee</label>
        <div class="col-sm-6">
            {{ Form::select('employee_id',  array('' => 'Select Any') + $employeeLookup, isset($rfpDetails->employee_id) ? $rfpDetails->employee_id :"",array('class'=> 'form-control select2','required'=>TRUE)) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('employee_id', ':message') !!}</div>
        </div>
    </div>
