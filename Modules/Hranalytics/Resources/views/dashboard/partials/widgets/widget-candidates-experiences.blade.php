<!-- Candidate Resident Status -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" charset="utf-8"></script>
<script>
    //Use function name as camalCase version of WidgetTag
    //Always use let instead of var
    widgets.define('widgetCandidatesExperiences',function(payload) {
        let wc; //widget container

        function generateContent() {
            let content =
            `<div style="padding: 20px;">
                <canvas id="candidatesExperiences" height="125"></canvas>
            </div>`;

            //..process
            return content;
        }

        function generateGraph(chartData) {
            console.log(chartData);
            let color = [];

            for (let index = 0; index < chartData.data.chartDetails.labels.length; index++) {
                color.push('rgb(243, 105, 5)');
                color.push('rgb(0, 58, 99)');
            }
            let ctx = wc.find('#candidatesExperiences');
            let trendAnalysis = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.data.chartDetails.labels,
                    datasets: [{
                        label: 'Average Experience',
                        data: chartData.data.chartDetails.elements,
                        backgroundColor: color,
                        borderWidth: 1
                    }]
                },
                options: {
                    maintainAspectRatio: true,
                    aspectRatio: 3,
                    spanGaps: false,
                    legend: {
                        display: false
                    },
                    title: {
                        display: false,
                        text: 'Candidates Experiences (Categories)'
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
                                labelString: 'Average Experience'
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
            // //After content render (eg:register envent listeners | init eg: select2)
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