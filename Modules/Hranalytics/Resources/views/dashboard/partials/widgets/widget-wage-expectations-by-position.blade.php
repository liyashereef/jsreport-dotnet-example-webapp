<!-- Demo Widget -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" charset="utf-8"></script>
<script>
    //Use function name as camalCase version of WidgetTag
    //Always use let instead of var
    widgets.define('widgetWageExpectationsByPosition',function(payload) {
        let wc; //widget container

        function generateContent() {
            let content =
            `<div style="padding: 20px;">
                <canvas id="WageExpectationsByPosition" height="125"></canvas>
            </div>`;
            //..process
            return content;
        }


        function generateGraph(chartData) {
            let color = [];

            for (let index = 0; index < chartData.data.chartDetails.labels.length; index++) {
                color.push('rgb(243, 105, 5)');
                color.push('rgb(0, 58, 99)');
            }
            let ctx = wc.find('#WageExpectationsByPosition');
            let trendAnalysis = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.data.chartDetails.labels,
                    datasets: [{
                        label: 'High Wage',
                        additionalData:chartData.data.chartDetails.additionalData,
                        data: chartData.data.chartDetails.elements.highWage,
                        backgroundColor: color,
                        borderWidth: 1
                    },
                    {
                        label: 'Low Wage',
                        additionalData:chartData.data.chartDetails.additionalData,
                        data: chartData.data.chartDetails.elements.lowWage,
                        backgroundColor: color,
                        borderWidth: 1
                    }
                    ],
                },
                options: {
                    tooltips: {
                        callbacks: {
                            afterBody: function(t, d) {
                                if(t[0]['datasetIndex'] == '0') {
                                    let formatedLabel = '';
                                    formatedLabel += '\t\t\t Maximum Wage:\t$' + d['datasets'][0]['additionalData'][t[0]['index']]['wage_high_max'];
                                    formatedLabel += '\n\t\t\t Minimum Wage:\t$' + d['datasets'][0]['additionalData'][t[0]['index']]['wage_high_min'];
                                    formatedLabel += '\n\t\t\t Sample:\t' + d['datasets'][0]['additionalData'][t[0]['index']]['sampleSize'];
                                    return formatedLabel;
                                }else {
                                    let formatedLabel = '';
                                    formatedLabel += '\t\t\t Maximum Wage:\t$' + d['datasets'][0]['additionalData'][t[0]['index']]['wage_low_max'];
                                    formatedLabel += '\n\t\t\t Minimum Wage:\t$' + d['datasets'][0]['additionalData'][t[0]['index']]['wage_low_min'];
                                    formatedLabel += '\n\t\t\t Sample:\t' + d['datasets'][0]['additionalData'][t[0]['index']]['sampleSize'];
                                    return formatedLabel;
                                }
                            },
                            label: function(tooltipItems, data) {
                                return 'Average ' + ((tooltipItems.datasetIndex == '0')? 'High': 'Low') + ' Wage:\t$'+tooltipItems.yLabel;
                            }
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 2,
                    legend: {
                        display: false
                    },
                    title: {
                        display: false,
                        text: 'Wage Expectations By Position'
                    },
                    scales: {
                        xAxes: [{
                            gridLines: {
                                display:false
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                callback: function(label, index, labels) {
                                    return label;
                                }
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Average Wage'
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
            //After content render (eg:register envent listeners | init eg: select2)
            // wc.on('click', function() {
            //     alert('Widget body click');
            // });
        }

        //Bind contents
        bindContent(generateContent());
        generateGraph(payload);
        //Execute after content is added to dom
        afterBind();
    });
</script>
