<div class="col-lg-12 dropdown-adjust">
    <div class="dropdown-alignment">
        <div class="row">
        <label class="col-md-1">Pay Period</label>
        <div class="col-md-3">
            <select class="form-control option-adjust select2" id="payperiod-filter">
                <option value="">All</option>
                @foreach($payperiod_list as $each_payperiod)
                <option value="{{$each_payperiod->id}}" @if($each_payperiod->id == $current_payperiod->id) selected
                    @endif>{{$each_payperiod->pay_period_name}}</option>
                @endforeach
            </select>
            <span class="help-block"></span>
        </div>
       <div class="col-md-1"></div>

        <div class="col-md-7 pull-right" >

            <div class="col-md-11 row border" style="padding: 10px;margin-top: -10px;">
        <div class="col-md-4">
            <input type="text" name="from_date" id="from_date" placeholder="From Date" value="" class="form-control datepicker" style="font-size:12px" />
        </div>
        <div class="col-md-4">
            <input type="text" name="to_date" id="to_date" placeholder="To Date" value="" class="form-control datepicker" style="font-size:12px" />
        </div>
        <div class="col-md-2">
            <button id="filterbutton" type="button" class="button btn submit" style="width:100%">
                Search
            </button>
        </div>
        <div class="col-md-2">
            <button id="resetbutton" type="button" class="button btn submit" style="width:100%">
                Reset
            </button>
        </div>
            </div>
    </div>
        </div>
    </div>
</div>
