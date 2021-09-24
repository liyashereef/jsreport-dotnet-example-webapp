<!-- Trend Analysis -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" charset="utf-8"></script>
<script>
    //Use function name as camalCase version of WidgetTag
    //Always use let instead of var
    widgets.define('widgetTrendAnalysis',function(payload) {
        let wc; //widget container

        function generateContent() {
            let content =
            `<div style="padding: 20px;">
                <canvas id="trendAnalysis" height="125"></canvas>
            </div>`;

            return content;
        }

        function generateGraph(chartData) {
            console.log(chartData.data.chartDetails.dates);
            let color = [];

            for (let index = 0; index < chartData.data.chartDetails.dates.length; index++) {
                color.push('rgb(243, 105, 5)');
            }
            let ctx = wc.find('#trendAnalysis');
            let trendAnalysis = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.data.chartDetails.dates,
                    datasets: [{
                        label: 'Average Site Trend',
                        data: chartData.data.chartDetails.score,
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
                    scales: {
                        xAxes: [{
                            gridLines: {
                                display: false
                            }
                        }],
                        yAxes: [{
                            display: true,
                            gridLines: {
                                display: false
                            },
                            scaleLabel: {
                                display: true,
                            },
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        }

        function bindContent(el) {
            wc = $('body').find(`.${payload.widgetInfo.dataTargetId}`);
            wc.find('.dasboard-card-body').html(el);
        }

        function afterBind() {
        }

        //Bind contents
        bindContent(generateContent());
        generateGraph(payload);
        //Execute after content is added to dom
        afterBind();
    });
</script>
