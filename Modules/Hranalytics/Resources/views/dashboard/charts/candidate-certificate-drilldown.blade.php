{{-- @extends('front.layout') --}}
@extends('layouts.app')
@section('content')
<div class="table_title">
    <h4>{{ $title }} </h4>
</div>
<section class="content-header section-box">
    <div class="row">
        <div class="col-xs-12 col-md-5 col-sm-12">
            <div class="row" >
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group row styled-form" id="start_date">
                        <label class="col-md-3 label-adjust col-form-label control-label col-xs-12 padding-left-15">Certificate Expiry From</label>
                        <div class="col-md-9 col-xs-12">
                            <input class="form-control datepicker" value="" name="from" id='from' onchange='refershDataTable();plot_chart()'/>
                            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="row graph-adjust" >
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group row styled-form" id="end_date">
                        <label class="col-md-3 label-adjust col-form-label control-label col-xs-12 padding-left-15">To</label>
                        <div class="col-md-9 col-xs-12">
                            <input class="form-control datepicker" name="to"  value=""  id='to'  onchange='refershDataTable();plot_chart()'/>
                            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-xs-12 col-md-7 col-sm-12" id="draw_charts">@if(isset($chart))
          {{-- {!! $chart->html() !!} --}}
        @endif
        </div>
    </div>


</section>


<section class="content-header margin-spacing">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-3 text-align-right"><span class="expire-within-2-months"></span> Expire within 2 Months</div>
        <div class="col-md-3 text-align-right"><span class="expire-within-3-months"></span> Expire within 3 Months</div>
        <div class="col-md-3 text-align-right"><span class="expire-after-3-months"></span> Expire after 3 Months</div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered align" id="table">
            <thead>
                <tr>
                    <th class="sorting" width="10%">Client Name</th>
                    <th class="sorting" width="10%">Guard Name</th>
                    <th class="sorting" width="10%">City</th>
                    <th class="sorting" width="5%">Postal Code</th>
                    <th class="sorting" width="15%">Guard License Expiry Date</th>
                    <th class="sorting" width="15%">First Aid Expiry Date</th>
                    <th class="sorting" width="15%">CPR Expiry Date</th>
                    <th class="sorting" width="10%">Email Address</th>
                    <th class="sorting" width="10%">Phone</th>
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
    function getExpiryClass(d2) {
        var months,$class;
        if(d2!=null)
        {
            d2 = new Date(d2);
            d1 = new Date('{{ date("Y-m-d") }}');
            months = (d2.getFullYear() - d1.getFullYear()) * 12;
            months -= d1.getMonth() + 1;
            months += d2.getMonth() + 1;
         //   console.log(months);
            if(months<=2){
                $class = 'expire-within-2-months';
            }else if(months<=3){
                $class = 'expire-within-3-months';
            }else if(months>3){
                $class = 'expire-after-3-months';
            }
        }
        return $class;
    }
    $(function () {
        var flag1 = 1;
    table = $('#table').DataTable({
    fixedHeader: true,
            processing: false,
            serverSide: true,
            responsive: true,
            ajax:{
            url: '{{ route('dashboard.drilldown.candidate_certificate_list') }}', // Change this URL to where your json data comes from
                    type: "GET", // This is the default value, could also be POST, or anything you want.
                    data: function(d){
                    d.license_expiry_from = $('#from').val();
                    d.license_expiry_to = $('#to').val();
                    d.flag1=flag1;
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
            {data: 'latest_job_applied.job.customer.client_name', name: 'latest_job_applied.job.customer.client_name', defaultContent: "--", },
            {data: 'name', name: 'name', defaultContent: "--", },
            {data: 'city', name: 'city', defaultContent: "--", },
            {data: 'postal_code', name: 'postal_code', defaultContent: "--", },
            {data: 'guarding_experience.expiry_guard_license', name: 'guarding_experience.expiry_guard_license', defaultContent: "--",
                    render:function(expiry_date){
                        return (expiry_date!=null)?'<span class="'+getExpiryClass(expiry_date)+'">'+expiry_date+'</span>':'--';
                    }
            },
            {data: 'guarding_experience.expiry_first_aid', name: 'guarding_experience.expiry_first_aid', defaultContent: "--",
                    render:function(expiry_date){
                        return (expiry_date!=null)?'<span class="'+getExpiryClass(expiry_date)+'">'+expiry_date+'</span>':'--';
                    }
            },
            {data: 'guarding_experience.expiry_cpr', name: 'guarding_experience.expiry_cpr', defaultContent: "--",
                        render:function(expiry_date){
                        return (expiry_date!=null)?'<span class="'+getExpiryClass(expiry_date)+'">'+expiry_date+'</span>':'--';
                    }
            },
            {data: 'email', name: 'email', defaultContent: "--", },
            {data: 'phone_cellular', name: 'phone_cellular', defaultContent: "--", },

            ]
    });

  plot_chart();
    });


 function plot_chart() {
    var flag1=0
    license_expiry_from = $('#from').val();
    license_expiry_to = $('#to').val();
    $.ajax({
        url: '{{ route('dashboard.drilldown','candidate-certificates') }}',
        type: "GET",
        data: {license_expiry_from:license_expiry_from,flag1:flag1,license_expiry_to:license_expiry_to },
        success: function(data){
        var parsed_data = JSON.parse(data);
        $total=parsed_data.total
        $label=parsed_data.label
        var data_viewers =  $total;
        var data_statuss =  $label;

    $('#draw_charts').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Candidate Certificates'
        },
        xAxis: {
            categories: data_statuss
        },
        yAxis: {
            title: {
                text: 'Guard License'
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

            name: 'Guard License',
            data: data_viewers,

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
