@extends('layouts.app')
@section('content')
<div class="table_title">
    <h4> Short Term Contracts </h4>
</div>
<div class="col-md-6 customer_filter_main">
    <div class="row">
        <div class="col-md-3"><label class="filter-text customer-filter-text">Customer </label></div>
        <div class="col-md-6 filter customer-filter">
            <select class="form-control option-adjust client-filter select2" name="clientname-filter" id="clientname-filter">
                <option value="">Select customer</option>
                @foreach($customer_details_arr as $eachCustomername)
                <option value="{{ $eachCustomername['id']}}">{{ $eachCustomername['client_name'] .' ('.$eachCustomername['project_number'].')' }}
                </option>
                @endforeach
            </select>
            <span class="help-block"></span>
        </div>
    </div>
</div>
<br>

<div id="message"></div>
<table class="table table-bordered" id="stc-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Project No</th>
            <th>Client Name</th>
            <th>Requestor Name</th>
            <th>Client Contact Name</th>
            <th>Client Contact Email</th>
            <th>Client Contact Phone Number</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>
@endsection

@section('scripts')
 @include('hranalytics::short-term-contracts.partials.script')
@endsection
