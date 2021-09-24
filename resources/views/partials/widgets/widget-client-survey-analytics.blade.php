<!-- Demo Widget -->
<script>
    //Use function name as camalCase version of WidgetTag
    //Always use let instead of var
    widgets.define('widgetClientSurveyAnalytics',function(payload) {
        let wc; //widget container

        function generateContent() {
            let content = `
            <div class="test">
                <p>Simple widget: click here</p>
                <p>Time is: ${(new Date().toString())}</p>
            </div>
            `;
            //..process
            return content;
        }

        function bindContent(el) {
            wc = $('body').find(`.${payload.widgetInfo.dataTargetId}`);
            let filterhtml = `<select name="graphtype" id="graphtype" class="form-control"
            style="max-width:40%!important;float:right">
                <option value="individual">Individual</option>
                <option value="average">Average</option>

            </select>`;
            wc.find('.filter-content').html(filterhtml);
            wc.find('.dasboard-card-body').html(`<canvas  id="surveyanalysisChart" ></canvas>`);

            //let ctx = document.getElementById('surveyanalysisChart').getContext('2d');
            let ctx=wc.find('#surveyanalysisChart');

            let data = jQuery.parseJSON(payload.data);
            var alldata = jQuery.parseJSON(payload.data);
            //Multiple Line chart

            let dataarray = [];
            let i =0;
            try {
                Object.keys(data["customer"]).forEach(element => {

                let dobject = {
                label: data["customer"][element]["name"],
                backgroundColor: 'transparent',
                borderColor: data["customer"][element]["color"],
                data: Object.values(data["yaxis"][element])
                }
                dataarray.push(dobject);
                });
            } catch (error) {

            }


            let chart = new Chart(ctx, {
            // The type of chart we want to create
            type: 'line',

            // The data for our dataset
            data: {
                labels: data["xaxis"],
                datasets: dataarray
            },

            // Configuration options go here
            options: {
                maintainAspectRatio: false,
                legend:{
                    display:false
                },
                // events: ['click'],
                layout: {
                    padding: {
                        left:25,
                        right: 0,
                        top: 30,
                        bottom: 40
                    }
                },tooltips:{
                    callbacks: {
                        labelColor: function(tooltipItem, chart) {
                            // console.log(dataarray)
                            // debugger
                        return {
                            borderColor: dataarray[tooltipItem.datasetIndex]["borderColor"],
                            backgroundColor: dataarray[tooltipItem.datasetIndex]["borderColor"]
                        }
                    },
                    label: function(tooltipItem,data) {
                                        var dataarray = alldata["count"];
                                        var customerdataarray = alldata["customer"];
                                        console.log(customerdataarray);
                                        let customervalues = (Object.values(customerdataarray)[tooltipItem.datasetIndex]["id"]);
                                        let noofrating = 0;
                                        if(customervalues){
                                            //console.log(tooltipItem["label"])
                                           // console.log(data["datasets"][tooltipItem.datasetIndex])
                                           noofrating=(dataarray[customervalues][tooltipItem["label"]])
                                        }


                                        //console.log(tooltipItem.datasetIndex);
                                        if(tooltipItem.value>0){
                                            if(noofrating>1){
                                                if((tooltipItem.value).search(".")){
                                                    return data["datasets"][tooltipItem.datasetIndex]["label"]+" Rating - "+parseFloat(tooltipItem.value).toFixed(2)+" (No of Rating : "+noofrating+")";
                                                }else{
                                                    return data["datasets"][tooltipItem.datasetIndex]["label"]+" Rating - "+parseFloat(tooltipItem.value).toFixed(2)+" (No of Rating : "+noofrating+")";
                                                }

                                            }else{
                                                return data["datasets"][tooltipItem.datasetIndex]["label"]+" Rating - "+parseFloat(tooltipItem.value).toFixed(2);
                                            }

                                        }else{
                                            return false;
                                        }

                                                //return clientname[0].text+" : Rating - "+tooltipItem.value;
                                        },
                                        title:function(){
                                            return false
                                        },

                                    },
                }

            }
            });


        }

        function afterBind() {
            //After content render (eg:register envent listeners | init eg: select2)
            wc.on('click', function() {
               // alert('Widget body click');
            });
            wc.find('.filter-content #graphtype').on("change",function(e){
                let self = this;
                let customersearch =$("#dashboard-filter-customer").val();
                console.log(customersearch)
                $.ajax({
                    type: "post",
                    url: '{{route("clientsurvey.plotdataanalytics")}}',
                    data: {graphtype:$(self).val(),"customer-search":$("#dashboard-filter-customer").val()},
                    headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
                    success: function (response) {
                        dataprocessing(jQuery.parseJSON(response));
                    }
                });
            })
        }

        function dataprocessing(data){

            wc.find('.dasboard-card-body').html(`<canvas  id="surveyanalysisChart" ></canvas>`);
            let alldata = data;
            let ctx=wc.find('#surveyanalysisChart');

                let dataarray = [];
                let i =0;
                try {
                    Object.keys(data["customer"]).forEach(element => {

                    let dobject = {
                    label: data["customer"][element]["name"],
                    backgroundColor: 'transparent',
                    borderColor: data["customer"][element]["color"],
                    data: Object.values(data["yaxis"][element])
                    }
                    dataarray.push(dobject);
                    });
                } catch (error) {

                }


                let chart = new Chart(ctx, {
                // The type of chart we want to create
                type: 'line',

                // The data for our dataset
                data: {
                    labels: data["xaxis"],
                    datasets: dataarray
                },

                // Configuration options go here
                options: {
                    maintainAspectRatio: false,
                    legend:{
                        display:false
                    },
                    // events: ['click'],
                    layout: {
                        padding: {
                            left:25,
                            right: 0,
                            top: 30,
                            bottom: 40
                        }
                    },tooltips:{
                        callbacks: {
                            labelColor: function(tooltipItem, chart) {
                                // console.log(dataarray)
                                // debugger
                            return {
                                borderColor: dataarray[tooltipItem.datasetIndex]["borderColor"],
                                backgroundColor: dataarray[tooltipItem.datasetIndex]["borderColor"]
                            }
                        },
                        label: function(tooltipItem,data) {

                                        var dataarray = alldata["count"];
                                        var customerdataarray = alldata["customer"];
                                        let customervalues = (Object.values(customerdataarray)[tooltipItem.datasetIndex]["id"]);
                                        let noofrating = 0;
                                        if(customervalues){
                                            //console.log(tooltipItem["label"])
                                           // console.log(data["datasets"][tooltipItem.datasetIndex])
                                           noofrating=(dataarray[customervalues][tooltipItem["label"]])
                                        }


                                        //console.log(tooltipItem.datasetIndex);
                                        if(tooltipItem.value>0){
                                            if(noofrating>1){
                                                if((tooltipItem.value).search(".")){
                                                    return data["datasets"][tooltipItem.datasetIndex]["label"]+" Rating - "+parseFloat(tooltipItem.value).toFixed(2)+" (No of Rating : "+noofrating+")";
                                                }else{
                                                    return data["datasets"][tooltipItem.datasetIndex]["label"]+" Rating - "+parseFloat(tooltipItem.value).toFixed(2)+" (No of Rating : "+noofrating+")";
                                                }

                                            }else{
                                                return data["datasets"][tooltipItem.datasetIndex]["label"]+" Rating - "+parseFloat(tooltipItem.value).toFixed(2);
                                            }

                                        }else{
                                            return false;
                                        }

                                                //return clientname[0].text+" : Rating - "+tooltipItem.value;
                                        },
                                        title:function(){
                                            return false
                                        },

                                    },
                    }

                }
                });
        }

        //Bind contents
        bindContent(generateContent());

        //Execute after content is added to dom
        afterBind();
    });
</script>
