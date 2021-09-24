<!-- Site Metric -->
<style>
    td.site-metric-green{
        background: #00b050;
        color: white;
    }

    td.site-metric-red{
        background: #ff0000;
        color: white;
    }

    td.site-metric-yellow{
        background: #ffff00;
        color: black;
    }

    td.site-metric-black{
        background: black;
        color: white;
    }

    td.site-metric-orange {
        background: #f36905;
        color: white;
    }

    td.site-metric-blue, th.site-metric-blue {
        background: #393f4f;
        color: white;
    }

    .site-metric-tbl td {
        border: 1px solid white;
    }

    .site-metric-tbl th {
        width: 25% ;
        border: 1px solid white !important;
    }

    .download-btn {
        cursor: pointer;
    }
    </style>
<script>
    //Use function name as camalCase version of WidgetTag
    //Always use let instead of var
    widgets.define('widgetSiteMetric',function(payload) {
        let wc; //widget container

        function generateContent() {
            let output = (payload.data && payload.data.site_meric_details)? payload.data.site_meric_details: null;

            let tableBody = `<tr><td class="text-center" colspan="4">No data</td></tr>`;
            if((output != null) && (output != undefined)) {
                let i = 0;
                totalRow = '';
                let total = 0, trendScore = 0, trendCss = '', currentScore = 0, averageScore = 0, scoreDifference = 0;
                $.each(output, function(key,item){
                    if(i == 0) {
                        tableBody = '';
                    }
                    if(key == "total") {
                        total = isNaN(item.current_score)? 0.00: item.current_score;
                        total = parseFloat(total).toFixed(2);
                        totalRow = `<tr><td class="site-metric-blue"  style="padding-left: 10px !important;">Current Site Metric</td><td class="site-metric-${item.current_css} text-center">${total}</td><td class="site-metric-blue text-center">Post Orders</td><td class="text-center download-btn">Download</td></tr>`;
                    }else {
                        currentScore = isNaN(item.current_score)? 0.00: item.current_score;
                        averageScore = isNaN(item.average_score)? 0.00: item.average_score;
                        averageScore = parseFloat(averageScore).toFixed(2);
                        currentScore = parseFloat(currentScore).toFixed(2);

                        if(item.current_score == "not_submitted_score") {
                            scoreDifference = averageScore;
                        }else {
                            scoreDifference = (currentScore - averageScore);
                        }

                        if(scoreDifference > 0) {
                            trendCss = "green";
                        }else if(scoreDifference == 0) {
                            trendCss = "yellow";
                        }else {
                            trendCss = "red";
                        }

                        trendScore = parseFloat(scoreDifference).toFixed(2);
                        tableBody += `<tr><td  class="site-metric-orange"  style="padding-left: 10px !important;">${key}</td><td class="site-metric-${item.current_css} text-center">${currentScore}</td><td class="site-metric-${item.average_css} text-center">${averageScore}</td><td class="site-metric-${trendCss} text-center">${trendScore}</td></tr>`;
                    }
                    i++;
                });

                if(tableBody !== "") {
                    tableBody += totalRow;
                }
            }

            //..process
            return `<table class="table site-metric-tbl table-bordered tbl-line-height-1"><thead>
                <tr>
                    <th class="site-metric-blue"  style="padding-left: 10px !important;">Average Trend</th>
                    <th class="site-metric-blue text-center">Current</th>
                    <th class="site-metric-blue text-center">Average</th>
                    <th class="site-metric-blue text-center">Trend</th>
                </tr>
            </thead><tbody>${tableBody}</tbody></table>`;
        }

        function bindContent(el) {
            wc = $('body').find(`.${payload.widgetInfo.dataTargetId}`);
            wc.find('.dasboard-card-body').html(el);
        }

        function afterBind() {
            //After content render (eg:register envent listeners | init eg: select2)
            wc.find('.inner-page-nav').on('click', function() {
                window.open(payload.data.inner_page_url);
            });
        }

        //Bind contents
        bindContent(generateContent());

        //Execute after content is added to dom
        afterBind();
    });
</script>
