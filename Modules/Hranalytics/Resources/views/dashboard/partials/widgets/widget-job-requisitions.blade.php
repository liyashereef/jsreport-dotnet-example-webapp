<!-- Demo Widget -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" charset="utf-8"></script>
<script>
    //Use function name as camalCase version of WidgetTag
    //Always use let instead of var
    widgets.define('widgetJobRequisitions',function(payload) {
        let wc; //widget container

        function generateContent() {
            let content =
            `<div style="padding: 20px;">
                <canvas id="JobStatus" height="125"></canvas>
            </div>`;
            //..process
            return content;
        }

        function generateGraph(chartData) {
            console.log(chartData.data.chartDetails);
            let color = [];

            for (let index = 0; index < chartData.data.chartDetails.labels.length; index++) {
                color.push('rgb(243, 105, 5)');
                color.push('rgb(0, 58, 99)');
            }
            let ctx = wc.find('#JobStatus');
            let trendAnalysis = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.data.chartDetails.labels,
                    datasets: [{
                        label: 'job Status',
                        data: chartData.data.chartDetails.elements,
                        backgroundColor: color,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    legend: {
                        display: false
                    },
                    title: {
                        display: false,
                        text: 'Job Requisition'
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
                                labelString: 'Job Status'
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
