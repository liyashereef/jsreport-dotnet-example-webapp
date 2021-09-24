<div class="col-md-5 employee-filter employee-filter-main">
    <div class="row">
        <div class="col-md-3"><label class="filter-text employee-filter-text">Employee</label></div>
        <div class="col-md-6 filter employee-filter-align">
            <select class="form-control option-adjust employee-filter select2" name="employee-filter" id="employee-name-filter">
                <option value="0">Select employee</option>
                @foreach($userList as $eachUserlist)
                <option value="{{$eachUserlist->id}}">{{ $eachUserlist->first_name.' '.$eachUserlist->last_name.' ('.$eachUserlist->employee['employee_no'].')'}}</option>
                @endforeach
            </select>
            <span class="help-block"></span>
        </div>
    </div>
</div>
