<div class="timesheet-filters mb-2 col-md-12">
    <div class="row">
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-3 div-text"><label class="filter-text">Pay Period</label></div>
                <div class="col-md-8">
                    <select class="form-control option-adjust select2" name="pay-period" id="payperiod-filter">
                        <option value="">All</option>
                        @foreach($payperiod_list as $each_payperiod)
                        <option value="{{$each_payperiod->id}}" @if($each_payperiod->id == $current_payperiod->id) selected
                            @endif>{{$each_payperiod->pay_period_name}}</option>
                        @endforeach
                    </select>
                    <span class="help-block"></span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-3 div-text">
                    <label class="filter-text">Employee</label>
                </div>
                <div class="col-md-8">
                    <select class="form-control option-adjust timesheet-filter select2" id="employee-filter">
                        <option selected value="">Select Employee</option>
                        @foreach($employeeLookupList as $key=>$employees)
                        <option value="{{$key}}">{{$employees}}</option>
                        @endforeach
                    </select>
                    <span class="help-block"></span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-3 div-text">
                    <label class="filter-text">Customer</label>
                </div>
                <div class="col-md-8">
                    <select class="form-control option-adjust timesheet-filter select2" id="customer-filter">
                        <option selected value="">Select Customer</option>
                        @foreach($allocated_customers as $allocated_customer)
                        <option value="{{$allocated_customer->id}}">
                            {{$allocated_customer->project_number}} - {{$allocated_customer->client_name}}</option>
                        @endforeach
                    </select>
                    <span class="help-block"></span>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="row">
                <div class="col-md-6">
                    <input type="text" name="from_date" id="from_date" placeholder="From Date" value="" class="form-control datepicker" style="font-size:12px" />
                    <span class="help-block"></span>
                </div>
                <div class="col-md-6">
                    <input type="text" name="to_date" id="to_date" placeholder="To Date" value="" class="form-control datepicker" style="font-size:12px" />
                    <span class="help-block"></span>
                </div>
            </div>
        </div>
        <div class="col-md-1">
            <div class="row">
                <div class="col-md-6">
                    <button id="filterbutton" type="button" class="button btn submit">Search</button>
                    <span class="help-block"></span>
                </div>
                <div class="col-md-6">
                    <button id="resetbutton" type="button" class="button btn submit">Reset</button>
                    <span class="help-block"></span>
                </div>
            </div>
        </div>
    </div>
</div>
