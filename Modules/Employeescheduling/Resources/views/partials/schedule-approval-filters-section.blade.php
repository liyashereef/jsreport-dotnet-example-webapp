<style>

    #payperiod_select,#customer_select .select2-selection__rendered{
        color: white !important;
    }

</style>




@if(isset($scheduleId))
<div class="col-md-1 text-right">
    <label class="label">Pay Period</label>
</div>

<div class="col-md-3" id="payperiod_select">
    <select id="payperiod_element" class="form-control payperiod_element" multiple="multiple">
        @else
        <div class="col-md-2" id="payperiod_select">
            <select id="payperiod_element" class="form-control-sm payperiod_element">
                @endif
                @if(isset($payperiods))
                @foreach($payperiods as $ky => $payperiod)
                <option value='{{$ky}}'
                        @if((isset($enableApproveReject) && $enableApproveReject) || (isset($currentPayperiodId) && ($currentPayperiodId == $ky)))
                        selected
                        @endif
                        >{{$payperiod}}</option>
                @endforeach
                @endif
            </select>
        </div>

<!--        <div class="col-md-1 text-right customer_select_box_div hidden">
            <label class="label">Site</label>
        </div>
        <div class="col-md-5 customer_select_box_div hidden" id="customer_select">
            <select id="customer_element" class="form-control form-control-sm largerCheckbox schedule_filter_select2" multiple="multiple">
                @if(isset($customers))
                @foreach ($customers as $key=>$value)
                <option value="{{$key}}"
                        @if(isset($enableApproveReject) && $enableApproveReject)
                        selected
                        @endif
                        >{{$value}}</option>
                @endforeach
                @endif
            </select>
        </div>-->
