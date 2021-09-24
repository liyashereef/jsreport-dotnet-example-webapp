<div class="col-md-6 customer_filter_main">
    <div class="row">
        <div class="col-md-3"><label class="filter-text customer-filter-text">Customer </label></div>
        <div class="col-md-6 filter customer-filter">
            <select class="form-control option-adjust client-filter select2" name="clientname-filter" id="clientname-filter">
                <option value="">Select customer</option>
                @foreach($customerList as $eachCustomername)
                <option value="{{ $eachCustomername->id}}">{{ $eachCustomername->client_name .' ('.$eachCustomername->project_number.')' }}
                </option>
                @endforeach
            </select>
            <span class="help-block"></span>
        </div>
    </div>
</div>
<br>
