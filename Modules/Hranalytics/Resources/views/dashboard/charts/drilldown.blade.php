{{-- @extends('front.layout') --}}
@extends('layouts.app')
@section('content')
<div class="table_title">
    <h4>{{ $title }} </h4>
</div>


<section class="content-header section-box">
    <div class="row">
        <div class="col-xs-12 col-md-5 col-sm-12">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group row styled-form" id="status">
                        <label class="col-md-3 label-adjust col-form-label control-label col-xs-12 padding-left-15">Area Manager</label>
                        <div class="col-md-9 col-xs-12">
                            {{ Form::select('area_manager', ['Administrator'=>'Administrator','Super Admin'=>'Super Admin']+$area_managers,null,array('class'=>'form-control','onchange'=>'refershDataTable();')) }}
                            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group row styled-form" id="start_date">
                        <label class="col-md-3 label-adjust col-form-label control-label col-xs-12 padding-left-15">From</label>
                        <div class="col-md-9 col-xs-12">
                            <input class="form-control datepicker" name="from" id='from' onchange='refershDataTable();'/>
                            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group row styled-form" id="end_date">
                        <label class="col-md-3 label-adjust col-form-label control-label col-xs-12 padding-left-15">To</label>
                        <div class="col-md-9 col-xs-12">
                            <input class="form-control datepicker" name="to" id='to'  onchange='refershDataTable();'/>
                            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row section-box-button">
                <div class="col-md-12 text-right padding-left-15">
                    <button type="button" onclick="refershDataTable()" class="btn submit">YTD</button>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-7 col-sm-12">@if(isset($chart))

                {!! $chart->html() !!}

            @endif</div>
    </div>


</section>


<section class="content-header margin-spacing">
    <div class="table-responsive">
        <table class="table table-bordered" id="jobs-table">
            <thead>
                <tr>
                    <th class="sorting">Job Id</th>
                    <th class="sorting">Area Manager</th>
                    <th class="sorting">Requestor</th>
                    <th class="sorting">Requestor's Phone Number</th>
                    <th class="sorting">Email Address</th>
                    <th class="sorting">Project Number</th>
                    <th class="sorting">Client</th>
                    <th class="sorting">Position Requested</th>
                    <th class="sorting">Posts</th>
                    <th class="sorting">Rationale</th>
                    <th class="sorting">Type</th>
                    <th class="sorting">Date Required</th>
                    <th class="sorting">Wage(Low)</th>
                    <th class="sorting">Wage(High)</th>
                    <th class="sorting">Status</th>
                </tr>
            </thead>
        </table>
    </div>
</section>
@endsection
@section('scripts')
{{-- {!! Charts::scripts() !!} --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/6.0.6/highcharts.js" charset="utf-8"></script>
@if(isset($chart))
{!! $chart->script() !!}
@endif
@include('hranalytics::dashboard.charts.partials.privilage-mapping')
<script>
    var table;
    $(function () {
    table = $('#jobs-table').DataTable({
    fixedHeader: true,
            processing: false,
            serverSide: true,
            responsive: true,
            ajax:{
            url: '{{ route('dashboard.drilldown.list') }}', // Change this URL to where your json data comes from
                    type: "GET", // This is the default value, could also be POST, or anything you want.
                    data: function(d){
                    d.area_manager = $('select[name="area_manager"] option:selected').text();
                    d.from = $('#from').val();
                    d.to = $('#to').val();
                    }
            ,
                    "error": function (xhr, textStatus, thrownError) {
                    if (xhr.status === 401){
                    window.location = "{{ route('login') }}";
                    }
                    }
            },
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [[0, "desc"]],
            lengthMenu: [[10, 25, 50, 100, 500, - 1], [10, 25, 50, 100, 500, "All"]],
            columns: [
            {data: null,
                    name: 'unique_key',
                    render: function (o) {
                    actions = '';
                    var view_url = '{{ route("job.view", ":id") }}';
                    view_url = view_url.replace(':id', o.id);
                    actions += '<a title="View" href="' + view_url + '">' + o.unique_key + '</a>';
                    if (o.status !== 'pending' && o.status !== 'rejected' && o.status !== 'suspended' && (coo || hr || am))
                    {
                    var hr_tracking_url = '{{ route("job.summary",":id") }}'
                            hr_tracking_url = hr_tracking_url.replace(':id', o.id);
                    actions += '<br/><a title="HR TRACKING" href="' + hr_tracking_url + '">HR TRACKING</a>';
                    }
                    return actions;
                    }
            },
            {data: 'area_manager', name: 'area_manager', defaultContent: "--", },
            {data: 'requester', name: 'requester', defaultContent: "--", },
            {data: 'phone', name: 'phone', defaultContent: "--", },
            {data: 'email', name: 'email', defaultContent: "--", },
            {data: 'customer.project_number', name: 'customer.project_number', defaultContent: "--", },
            {data: 'client_name', name: 'client_name'},
            {data: 'position_beeing_hired.position', name: 'position_beeing_hired.position'},
            {data: 'no_of_vaccancies', name: 'no_of_vaccancies'},
            {data: 'reason.reason', name: 'reason.reason'},
            {data: 'assignment_type.type', name: 'assignment_type.type'},
            {data: 'requisition_date', name: 'requisition_date'},
            {data: 'wage_low', name: 'wage_low', render: function (wage_low) {return '$' + parseFloat(wage_low).toFixed(2)}},
            {data: 'wage_high', name: 'wage_high', render: function (wage_high) { return '$' + parseFloat(wage_high).toFixed(2) }},
            {data: null, name: 'status', render: function(row){ return (row.active != 0)? '<span style="text-transform:capitalize;">' + row.status + '</span>':'Archived'; }},
            ]
    });


    });
    function refershDataTable(){
        table.ajax.reload();
    }

</script>
@endsection
