{{-- <div>
    <p style="color: white; background-color: rgb(242, 99, 33, 1);padding-left:10px; margin-bottom:30px;">Site Metric Trends</p>
    <canvas id="myChart"></canvas>
</div>

<script>
    var dates = {!! json_encode($dates) !!};
    var score = {!! json_encode($score) !!};
    var color = [];

    for (let index = 0; index < dates.length; index++) {
        color.push('rgb(242, 99, 33, 1)');
    }




    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Average Site Trend',
                data: score,
                backgroundColor: color,
                borderColor: [
                    'rgb(242, 99, 33, 1)'
                ],
                fill: false,
                pointRadius: 5,
                pointStyle: 'circle'
            }]
        },
        options: {
            maintainAspectRatio: true,
            aspectRatio: 3,
            spanGaps: false,
            elements: {
                line: {
                    tension: 0.000001
                }
            },
            legend: {
                position: 'bottom'
            },
            scales: {
                xAxes: [{
                    gridLines: {
                        display: false
                    }
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Average Site Trend'
                    },
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
    </script> --}}


    @if(isset($chart))
    <p style="color: white; background-color: rgb(242, 99, 33, 1);padding-left:10px;">Site Metric Trends</p>
    <div class="row" style="padding: 10px;">
     {!! $chart->html() !!}
    </div>
    @endif


    @if(isset($chart))

    {!!$chart->script() !!}

    @endif
