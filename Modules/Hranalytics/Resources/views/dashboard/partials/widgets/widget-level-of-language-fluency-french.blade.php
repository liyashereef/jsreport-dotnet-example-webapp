<!-- candidates by level of language fluency french -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" charset="utf-8"></script>
<script>
    //Use function name as camalCase version of WidgetTag
    //Always use let instead of var
    widgets.define('widgetCandidatesByLevelOfLanguageFluencyFrench',function(payload) {
        let wc; //widget container

        function generateContent() {
            let content =
            `<div style="padding: 20px;">
                <canvas id="candidatesByLevelOfLanguageFluencyFrench" height="125"></canvas>
            </div>`;

            //..process
            return content;
        }

        function generateGraph(chartData) {
            console.log(chartData.data.chartDetails.elements);
            let color = [];

            for (let index = 0; index < chartData.data.chartDetails.labels.length; index++) {
                color.push('rgb(243, 105, 5)');
                color.push('rgb(0, 58, 99)');
            }
            let ctx = wc.find('#candidatesByLevelOfLanguageFluencyFrench');
            let trendAnalysis = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.data.chartDetails.labels,
                    datasets: [{
                            label: 'Fluent',
                            data: chartData.data.chartDetails.elements.fluent,
                            backgroundColor: color,
                            borderWidth: 1
                        },
                        {
                            label: 'Functional',
                            data: chartData.data.chartDetails.elements.functional,
                            backgroundColor: color,
                            borderWidth: 1
                        },
                        {
                            label: 'Limited',
                            data: chartData.data.chartDetails.elements.limited,
                            backgroundColor: color,
                            borderWidth: 1
                        }],
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
                        text: 'Candidates by Level of Language Fluency - French'
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
                                labelString: 'Proficiency'
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

        function bindCustomWidgetTitle() {
            wc.find('.widget-candidates-by-level-of-language-fluency-french-tittle')
            .text('Candidates by Level of Language Fluency - French');
            wc.find('.filter-content').remove();
        }

        //Bind contents
        bindContent(generateContent());
        generateGraph(payload);
        bindCustomWidgetTitle();
        //Execute after content is added to dom
        afterBind();
    });
</script>
