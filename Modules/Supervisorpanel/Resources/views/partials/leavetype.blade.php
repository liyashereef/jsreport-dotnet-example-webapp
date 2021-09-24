@if(auth()->user()->can('edit-survey') || (auth()->user()->can('submit-survey') && (!$completed)))
	<!-- <label class="col-sm-6 col-md-6 col-form-label">If yes, what is their name and employee number?</label> -->
	<div class="col-sm-6 col-md-6 employeeListHtmlBox">
    <select 
    @if(isset($employee_id)) 
        attr-answer="{{$employee_id}}"
        @else
        attr-answer=""
    @endif 
    name="{{$name}}" class="select2-employee-list form-control emplist" tabindex="-1">
        {{-- <option value="" selected="selected">Please Select</option>
        @foreach($employee_list as $each_employee_id => $each_empolyee)
        <option
            value="{{trim($each_employee_id)}}"
            @if(isset($employee_id) && ((trim($each_employee_id) == $employee_id) ||(trim($each_empolyee) == $employee_id))) selected @endif>{{$each_empolyee or ''}}</option>
        @endforeach --}}
    </select>
    </div>
    <label class="col-sm-6 col-md-6 col-form-label child-align margin-top-1">Date of absenteeism</label><div class="col-sm-6 col-md-6 margin-top-1"><input type="text" class="datepicker form-control" name="{{$name}}" value="{{$date or ''}}"></div>
    <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
    <label class="col-sm-6 col-md-6 col-form-label child-align margin-top-1">Hours Booked Off</label><div class="col-sm-6 col-md-6 margin-top-1"><input type="number" class="form-control" name="{{$name}}" max="999.99" step="0.01" value="{{$hours_off or ''}}"></div>
	<div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
    <label class="col-sm-6 col-md-6 col-form-label child-align margin-top-1">Reason</label><div class="col-sm-6 col-md-6 margin-top-1">
        <select name="{{$name}}" class="select2-reason-list form-control" tabindex="-1">
            <option value="" selected="selected">Please Select</option>
            @foreach($leave_reason as $each_reason_id => $each_reason)
            <option
                value="{{trim($each_reason_id)}}"
                @if(isset($reason_id) && ((trim($each_reason_id) == $reason_id) ||(trim($each_reason) == $reason_id))) selected @endif>{{$each_reason or ''}}</option>
            @endforeach
        </select>
	</div>
	<div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
	<label class="col-sm-6 col-md-6 col-form-label child-align margin-top-1">Note</label><div class="col-sm-6 col-md-6 margin-top-1"><textarea class="form-control" name="{{$name}}" maxlength="1000">{{$notes or ''}}</textarea></div><div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
@else
    @foreach($employee_list as $each_employee_id => $each_empolyee)
        @if(isset($employee_id) && ((trim($each_employee_id) == $employee_id) ||(trim($each_empolyee) == $employee_id)))
        <span class="view-form-element col-sm-6 col-md-6">{{$each_empolyee or ''}}</span>
        @endif
    @endforeach
    <label class="col-sm-6 col-md-6 col-form-label child-align margin-top-1">Date of absenteeism</label>
    <span class="view-form-element col-sm-6 col-md-6 margin-top-1">{{$date or ''}}</span>
	<label class="col-sm-6 col-md-6 col-form-label child-align margin-top-1">Hours Booked Off</label>
    <span class="view-form-element col-sm-6 col-md-6 margin-top-1">{{$hours_off or ''}}</span>
    @foreach($leave_reason as $each_reason_id => $each_reason)
        @if(isset($reason_id) && ((trim($each_reason_id) == $reason_id) ||(trim($each_reason_id) == $reason_id)))
        <label class="col-sm-6 col-md-6 col-form-label child-align margin-top-1">Reason</label>
        <span class="view-form-element col-sm-6 col-md-6 margin-top-1">{{$each_reason or ''}}</span>
        @endif
    @endforeach
    <label class="col-sm-6 col-md-6 col-form-label child-align margin-top-1">Note</label>
    <span class="view-form-element col-sm-6 col-md-6 margin-top-1">{{$notes or ''}}</span>
@endif
