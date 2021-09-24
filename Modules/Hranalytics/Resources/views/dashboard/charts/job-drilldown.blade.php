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
                            {{ Form::select('area_manager', [null=>'All','Administrator'=>'Administrator','Super Admin'=>'Super Admin']+$area_managers,null,array('class'=>'form-control','onchange'=>'refershDataTable();')) }}
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
                            <input class="form-control datepicker" name="from" id='from' onchange='refershDataTable();plot_chart()'/>
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
                            <input  class="form-control datepicker" name="to" id='to'  onchange='refershDataTable();plot_chart()'/>
                            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                        </div>
                    </div>
                </div>
            </div>
             <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group row styled-form" id="jobstatus">
                        <label class="col-md-3 label-adjust col-form-label control-label col-xs-12 padding-left-15">Job Status</label>
                        <div class="col-md-9 col-xs-12">
                            {{ Form::select('job_status', [null=>'All','pending'=>'Pending','approved'=>'Approved','completed'=>'Completed','rejected'=>'Rejected','suspended'=>'Suspended'],null,array('class'=>'form-control','onchange'=>'refershDataTable();plot_chart()')) }}
                            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group row styled-form" id="type">
                        <div class="col-md-1 col-sm-1 col-xs-12 radio-placement col-form-label">
                            {{ Form::radio('type', '', true, ['onchange'=>'refershDataTable();']) }}
                        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                        </div>
                        <label class="col-md-3 col-sm-11 label-adjust col-form-label control-label col-xs-12 padding-left-15">Both</label>

                        <div class="col-md-1 col-sm-1 col-xs-12 radio-placement col-form-label">
                            {{ Form::radio('type', 1, null, ['onchange'=>'refershDataTable();','id'=>'r1']) }}
                        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                        </div>
                        <label class="col-md-3 col-sm-11 label-adjust col-form-label control-label col-xs-12 padding-left-15">Permanent</label>

                        <div class="col-md-1 col-sm-1 col-xs-12 radio-placement col-form-label">
                            {{ Form::radio('type', 2, null, ['onchange'=>'refershDataTable();','id'=>'r1']) }}
                         <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                        </div>
                        <label class="col-md-3 col-sm-11 label-adjust col-form-label control-label col-xs-12 padding-left-15">Temporary</label>




                    </div>
                </div>
            </div>


            <div class="row section-box-button">
                <div class="col-md-12 text-right padding-left-15">
                    <button type="button" onclick="ytd()" class="btn submit">YTD</button>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-7 col-sm-12 chart" id="draw_chart">@if(isset($chart))
            {{--   {!! $chart->html() !!} --}}
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
        var flag = 1;
    table = $('#jobs-table').DataTable({
    fixedHeader: true,
            processing: false,
            serverSide: true,
            responsive: true,
            ajax:{
            url: '{{ route('dashboard.drilldown.job_list') }}', // Change this URL to where your json data comes from
                    type: "GET", // This is the default value, could also be POST, or anything you want.
                    data: function(d){
                    d.area_manager = $('select[name="area_manager"] option:selected').val();
                    d.from = $('#from').val();
                    d.to = $('#to').val();
                    d.type = $('input[name=type]:checked').val();
                    d.job_status = $('select[name="job_status"] option:selected').val();
                    d.flag=flag;

                    },
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
                    {{--var hr_tracking_url = '{{ route("job.summary",":id") }}' --}}
                    var hr_tracking_url = '{{ route("job.hr-tracking",":id") }}'
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
            {data: 'customer.client_name', name: 'customer.client_name', defaultContent: "--",},
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
     plot_chart();

    })
 function ytd()
{
   var date = new Date();
   var day = date.getDate();
    var month = date.getMonth() + 1;
    var year = date.getFullYear();
    if (month < 10) month = "0" + month;
    if (day < 10) day = "0" + day;

    var today = year + "-" + month + "-" + day;
    var start_date = year + "-" + '01' + "-" + '01';
    $('#from').val(start_date);
    $('#to').val(today);
    refershDataTable();
     plot_chart();
}

 $('select,input[type="radio"]').on('change', plot_chart);

 function plot_chart() {
    var flag=0
    area_manager=$('select[name="area_manager"] option:selected').val();
    type=$('input[name=type]:checked').val();
    start_date=$('#from').val();
    end_date=$('#to').val();
    job_status=$('select[name="job_status"] option:selected').val();
    $.ajax({
        url: '{{ route('dashboard.drilldown','job-requisitions') }}',
        type: "GET",
        data: {area_manager:area_manager,flag:flag,type:type,start_date:start_date,end_date:end_date,job_status:job_status  },
        success: function(data){
        var status_data = JSON.parse(data);
        $status = status_data.status
        $viewer = status_data.viewer
        var data_viewer =  $viewer;
        var data_status =  $status;

    $('#draw_chart').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Job Requisitions'
        },
        xAxis: {
            categories: data_status
        },
        yAxis: {
            title: {
                text: 'Job Status'
            }
        },
        credits: {
        enabled: false
         },
         plotOptions: {
                column: {
                    colorByPoint: true
                }
            },
            colors: [
                '#053d5f',
                '#f8553a',
                '#7692a2',
                '#FFC107',
                '#0D47A1'
            ],
        series: [{

            name: 'Job Status',
            data: data_viewer,
        }]
    });
}
 });

}

    function refershDataTable(){
        table.ajax.reload();
    }

</script>
@endsection
