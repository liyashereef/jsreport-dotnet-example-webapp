@extends('layouts.app')
@section('content')
<div class="table_title">
    <h4>Visitor Log Report</h4>
</div>
<div class="row" style="padding-top:30px;">
    <div class="col-lg">
        <label for="startDate">Start Date</label>
    </div>
    <div class="col-lg-2">
        <input id="startDate" class="form-control datepicker" placeholder="Start Date" type="text" max="2900-12-31" value="{{date('Y-m-d', strtotime("-30 days"))}}">
    </div>
    <div class="col-lg">
        <label for="endDate">End Date</label>
    </div>
    <div class="col-lg-2">
        <input id="endDate" class="form-control datepicker" placeholder="End Date" type="text" max="2900-12-31" value="{{date('Y-m-d')}}">
    </div>
    <div class="col-lg">
        <label for="site">Customer</label>
    </div>
    <div class="col-lg-2">
        <select name="site" id="site">
            @foreach($customerName as $key => $value)
                <option value="{{$key}}">{{$value}}</option>
            @endforeach

        </select>
    </div>
    <div class="col-lg-1">
        <button type="submit" class="form-control btn btn-primary" id="filterbutton">Search</button>
    </div>
    <div class="col-lg-3">
    </div>
</div>
<div class="container" style="width: 75%; padding: 40px !important;">
<canvas id="myChart"></canvas>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    var barChartData = [];

    $(document).ready(function(e){
        $("#site").select2({
            placeholder: {
                id: '-1',
                text: 'Select a site'
            }
        });
        $("#site").val($("#site option:first").val());
        $('#site').trigger('change');
        $('#filterbutton').trigger('click');
    });

    window.chartColors = {
        blue: 'rgb(0, 58, 99)',
        orange: 'rgb(248, 98, 34)',
        yellow: 'rgb(236, 148, 0)',
    };

    function drawChart() {
        $.ajax({
            "url": "{{route('reports.getVisitorLogReport')}}",
            "type": "GET",
            "data": {
                startDate: $('#startDate').val(),
                endDate: $('#endDate').val(),
                site: $('#site').val()
            },
            "global": true,
                "error": function (xhr, textStatus, thrownError) {
                    if (xhr.status === 401) {
                        window.location = "{{ route('login') }}";
                    }
                },
            "headers": {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
              createChart(data);
            }
        });
    }

    function createChart(data) {
        if(data.dates.length != 0) {
            barChartData.labels = [];
            barChartData.datasets = [];

            var ctx = document.getElementById('myChart').getContext('2d');
            window.myBar = new Chart(ctx, {
                            type: 'bar',
                            data: barChartData,
                            options: {
                                responsive: true,
                                legend: {
                                    position: 'right',
                                },
                                scales: {
                                    xAxes: [{
                                        gridLines: {
                                            display:false
                                        }
                                    }],
                                    yAxes: [{
                                        gridLines: {
                                            //display:false,
                                            color: [
                                                'rgba(0, 0, 0, 0.05)',
                                                'rgba(0, 0, 0, 0)',
                                                'rgba(0, 0, 0, 0)',
                                                'rgba(0, 0, 0, 0)',
                                                'rgba(0, 0, 0, 0)',
                                                'rgba(0, 0, 0, 0)',
                                                'rgba(0, 0, 0, 0)',
                                                'rgba(0, 0, 0, 0)',
                                                'rgba(0, 0, 0, 0)',
                                                'rgba(0, 0, 0, 0)',
                                                'rgba(0, 0, 0, 0)',
                                            ]
                                        },
                                        ticks: {
                                            beginAtZero: true,
                                        }
                                    }]
                                }
                            }
                        });

                   for(i =0; i < data.dates.length; i++) {
                       barChartData.labels.push(data.dates[i]);
                   }
                   var visitor = {
                        label: 'Visitor',
                        backgroundColor: [],
                        data: []
                    };

                    for (let index = 0; index < data.visitor.length; index++) {
                        visitor.data.push(data.visitor[index]);
                        visitor.backgroundColor.push(window.chartColors.blue);
                    }
                    barChartData.datasets.push(visitor);

                    var employee = {
                        label: 'Employee',
                        backgroundColor: [],
                        data: []
                    };

                    for (let index = 0; index < data.employee.length; index++) {
                        employee.data.push(data.employee[index]);
                        employee.backgroundColor.push(window.chartColors.yellow);
                    }
                    barChartData.datasets.push(employee);

                    var contractor = {
                        label: 'Contractor',
                        backgroundColor: [],
                        data: []
                    };

                    for (let index = 0; index < data.contractor.length; index++) {
                        contractor.data.push(data.contractor[index]);
                        contractor.backgroundColor.push(window.chartColors.orange);
                    }
                    barChartData.datasets.push(contractor);
                    window.myBar.update();
                } else {
                    swal({
                        icon: 'warning',
                        title: 'Oops',
                        text: 'No Visitors',
                    });
                    //to hide report value
                    barChartData.labels = [];
                    barChartData.datasets = [];
                    var ctx = document.getElementById('myChart').getContext('2d');
                        window.myBar = new Chart(ctx, {
                            type: 'bar',
                            data: barChartData,
                            options: {
                                responsive: true,
                                legend: {
                                    position: 'right',
                                },
                                scales: {
                                    xAxes: [{
                                        gridLines: {
                                            display:false
                                            //color: 'rgba(0, 0, 0, 0.1)',
                                        }
                                    }],
                                    yAxes: [{
                                        gridLines: {
                                            display:false,
                                            // color: [
                                            //     'rgba(0, 0, 0, 0.05)',
                                            //     'rgba(0, 0, 0, 0)',
                                            //     'rgba(0, 0, 0, 0)',
                                            //     'rgba(0, 0, 0, 0)',
                                            //     'rgba(0, 0, 0, 0)',
                                            //     'rgba(0, 0, 0, 0)',
                                            //     'rgba(0, 0, 0, 0)',
                                            //     'rgba(0, 0, 0, 0)',
                                            //     'rgba(0, 0, 0, 0)',
                                            //     'rgba(0, 0, 0, 0)',
                                            //     'rgba(0, 0, 0, 0)',
                                            // ]
                                        },
                                        ticks: {
                                            beginAtZero: true,
                                            display: false
                                        }
                                    }]
                                }
                            }
                        });

                }
    }

    function checkdate() {
        if ($('#startDate').val() === '' || $('#endDate').val() === '') {
            swal({
                icon: 'warning',
                title: 'Oops',
                text: "Please fill the date"
            });
        } else {
            var date1 = new Date($('#startDate').val());
            var date2 = new Date($('#endDate').val());
            var differenceInTime = date2.getTime() - date1.getTime();
            var differenceInDays = differenceInTime / (1000 * 3600 * 24);

            if (differenceInDays > 365) {
                swal({
                    icon: 'warning',
                    title: 'Oops',
                    text: "You can't select more than 365 days"
                });
            } else if (differenceInDays < 0 ) {
                swal({
                    icon: 'warning',
                    title: 'Oops',
                    text: "Please select proper dates"
                });
            } else {
                drawChart();
            }
        }

    }

    $('#filterbutton').on('click', function() {
        checkdate();
    });

</script>
{{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script> --}}
<script src="sweetalert2/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/promise-polyfill@8/dist/polyfill.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" charset="utf-8"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.css"></script> -->
@endsection

@section('css')
<style>
    label {
        margin-top: 6px;
        margin-right: -15px;
    }
    .col-lg-2 {
        padding-left: 0px;
    }
    canvas {
        cursor: pointer;
    }
    html, body {
    max-width: 100%;
    overflow-x: hidden;
    }
    footer {
        position: fixed;
    }
    .swal2-styled.swal2-confirm {
        background-color: #003A63 !important;
    }
</style>
@endsection
