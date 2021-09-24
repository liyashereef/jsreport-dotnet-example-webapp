<!-- Demo Widget -->
<script>
    //Use function name as camalCase version of WidgetTag
    //Always use let instead of var
    widgets.define('widgetCandidateMilitaryExperience',function(payload) {
        let wc; //widget container

        function generateContent() {
            let content = `<div style="padding: 20px;">
                <canvas id="candidateMilitaryExperience" height="125"></canvas>
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
            let ctx = wc.find('#candidateMilitaryExperience');
            let trendAnalysis = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.data.chartDetails.labels,
                    datasets: [{
                        label: 'Military Experience',
                        data: chartData.data.chartDetails.elements,
                        backgroundColor: color,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 3,
                    legend: {
                        display: false
                    },
                    title: {
                        display: false,
                        text: 'Candidate by Military Experience'
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
                                labelString: 'Military Experience'
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

        // function afterBind() {
        //     //After content render (eg:register envent listeners | init eg: select2)
        //     wc.on('click', function() {
        //         alert('Widget body click');
        //     });
        // }

        //Bind contents
        bindContent(generateContent());
        generateGraph(payload);
        //Execute after content is added to dom
        // afterBind();
    });
</script>
