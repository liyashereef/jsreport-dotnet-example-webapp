<!-- candidates computer skill -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" charset="utf-8"></script>
<script>
    //Use function name as camalCase version of WidgetTag
    //Always use let instead of var
    widgets.define('widgetCandidatesSkillsComputer',function(payload) {
        let wc; //widget container

        function generateContent() {
            let content =
            `<div style="padding: 20px;">
                <canvas id="candidateComputerSkill" height="125"></canvas>
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
            let ctx = wc.find('#candidateComputerSkill');
            let trendAnalysis = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.data.chartDetails.labels,
                    datasets: [{
                            label: 'No Knowledge',
                            data: chartData.data.chartDetails.elements.noKnowledge,
                            backgroundColor: color,
                            borderWidth: 1
                        },
                        {
                            label: 'Basic Knowledge',
                            data: chartData.data.chartDetails.elements.basicKnowledge,
                            backgroundColor: color,
                            borderWidth: 1
                        },
                        {
                            label: 'Good Knowledge',
                            data: chartData.data.chartDetails.elements.goodKnowledge,
                            backgroundColor: color,
                            borderWidth: 1
                        },
                        {
                            label: 'Advanced Knowledge',
                            data: chartData.data.chartDetails.elements.advancedKnowledge,
                            backgroundColor: color,
                            borderWidth: 1
                        },
                        {
                            label: 'Expert Knowledge',
                            data: chartData.data.chartDetails.elements.expertKnowledge,
                            backgroundColor: color,
                            borderWidth: 1
                        },
                        ],
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
                        text: 'Candidates Skills - Computer'
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
            wc.find('.widget-candidates-skills-computer-tittle')
            .text('Candidates Skills - Computer');
        }

        //Bind contents
        bindContent(generateContent());
        generateGraph(payload);
        bindCustomWidgetTitle();
        //Execute after content is added to dom
        afterBind();
    });
</script>
