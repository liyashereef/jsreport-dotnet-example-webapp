<style>
.filter-content {
    width: 60% !important;
}
</style>

<script>
    //Use function name as camalCase version of WidgetTag
    //Always use let instead of var
    widgets.define('widgetIncidentAnalytics',function(payload) {
        let wc; //widget container

        function generateContent() {
            let data = payload.data;
            let objectdata = Object.values(data);
            let kpidata = objectdata["kpi"]
            let selection = "";
            let dataelementhiddenplot = ``;
            let xmonths = ``;
            for (let index = 0; index < objectdata.length; index++) {
                let dataelement = objectdata[index];
                xmonths = (Object.keys(dataelement["headermonths"]));
                (Object.keys(dataelement).reverse()).forEach(function(key) {
                    if(dataelement[key]["name"]){
                        dataelementhiddenplot+=`<input type="hidden" attr-dataname="${dataelement[key]["name"]}" class="dataset" name="${key}"
                        id="${key}"
                        value="${Object.values(dataelement[key]["months"])}" />  `
                        selection+=`<option value="${key}">${dataelement[key]["name"]}</option>`
                    }


                });

                // Object.values(dataelement).forEach(function(elm) {
                //     console.log("elm",elm)
                //     debugger
                // });
                //selection+=`<option>${element["name"]}</option>`
            }
            if(objectdata.length>1){
                //selection=`<select>${selection}</select>`
            }
            let datatable = `<div class="container_fluid">`
                datatable+=`<div class=row>`
                datatable+=`<div class="col-md-12">`
                datatable+=`<input type="hidden" name="xmonths" id="xmonths" value="${xmonths}" />`
                datatable+=`<input type="hidden" name="selectval" id="selectval" value='${selection}' />`
                datatable+=dataelementhiddenplot;
                // datatable+=`<select class="form-control reskpiselection">${selection}</select>`
                datatable+=`</div>`
                datatable+=`</div>`
                datatable+=`<div class=row>`
                datatable+=`<div class="col-md-12 chartcanvas_incanalytics">
                <canvas id="IncanalyticsChart"></canvas>`
                datatable+=`</div>`
                datatable+=`</div>`
                datatable+=`</div>`
            let content = `
            ${datatable}
            `;
            //..process
            return content;
        }

        function bindContent(el) {
            wc = $('body').find(`.${payload.widgetInfo.dataTargetId}`);
            wc.find('.dasboard-card-body').html(el);

            changeHeader(el);
            //$(".widget-elevator-entrapment-response .card-header").css("display","none")
            reskpiselection();
        }

        function changeHeader(el){
            let selectval = $("#selectval").val();
            $(".widget-incident-analytics .card-header .filter-content").html(`<div style="padding:5px !important;width:100%">
                    <select class="form-control reskpiselection"><option value="all">All</option>${selectval}
                    </select></div>`);
        }
        function afterBind() {
            //After content render (eg:register envent listeners | init eg: select2)
            wc.on('click', function() {
                //alert('Widget body click');
            });
            try {

                let chartwcselec = $('body').find(`.${payload.widgetInfo.dataTargetId}`);

                $(document).on("change","."+payload.widgetInfo.dataTargetId+" .reskpiselection",function(e){
                    e.preventDefault();
                    reskpiselection();
                })

            } catch (error) {
                alert("Error")
            }



        }

        //Bind contents
        bindContent(generateContent());

        //Execute after content is added to dom
        afterBind();

        // $(document).on("change",".reskpiselection",function(e){
        //     e.preventDefault();
        //     // reskpiselection();
        // })

        function reskpiselection(){
            let colorarray =["#be1256","#0847fe","#a21be5","#b8ccd3","#7a82a0","#a15157","#eb71c1","#7db5b9","#d8b72f","#081ff5"
            ,"#6991c3","#4f9dd3","#447dbf","#bce3c5","#f8ee65","#4b4a4e","#995b41","#69b222","#91478d","#7782dc",
            "#be1256","#0847fe","#a21be5","#b8ccd3","#7a82a0","#a15157","#eb71c1","#7db5b9","#d8b72f","#081ff5"
            ,"#6991c3","#4f9dd3","#447dbf","#bce3c5","#f8ee65","#4b4a4e","#995b41","#69b222","#91478d","#7782dc",
            "#be1256","#0847fe","#a21be5","#b8ccd3","#7a82a0","#a15157","#eb71c1","#7db5b9","#d8b72f","#081ff5"
            ,"#6991c3","#4f9dd3","#447dbf","#bce3c5","#f8ee65","#4b4a4e","#995b41","#69b222","#91478d","#7782dc",
            "#be1256","#0847fe","#a21be5","#b8ccd3","#7a82a0","#a15157","#eb71c1","#7db5b9","#d8b72f","#081ff5"
            ,"#6991c3","#4f9dd3","#447dbf","#bce3c5","#f8ee65","#4b4a4e","#995b41","#69b222","#91478d","#7782dc"
];

            let chartwc = $('body').find(`.${payload.widgetInfo.dataTargetId}`);

            let incidentcolorarray = [];
            incidentcolorarray["Suspicious Activity / Intruder"] = "#348AC7"
            incidentcolorarray["Hazardous Chemicals"] = "#F8AF29"
            incidentcolorarray["Theft/Break-Ins/Vandalism"] = "#F55A35"
            incidentcolorarray["Plumbing, Electrical, maintenance conditions"] = "#348AC7"
            incidentcolorarray["Slips, Trips & Falls"] = "#1D617A"
            incidentcolorarray["Fire, Life Safety Equipment deficiencies"] = "#288386"
            incidentcolorarray["Medical Emergency"] = "#6CAF7F"
            incidentcolorarray["Damaged caused to buildings"] = "#185071"
            incidentcolorarray["Hazardous conditions"] = "#267C8A"
            incidentcolorarray["Fire/Police site visits"] = "#B5D568"
            incidentcolorarray["Windows, outside door left open after-hours"] = "#EEE9BB"
            incidentcolorarray["Media ativity on site"] = "#0E0E0E"
            incidentcolorarray["Protest activities"] = "#5C89AE"
            incidentcolorarray["Lost and found articles"] = "#3DBAC5"

            incidentcolorarray["Parking incidents"] = "#B5D568"
            incidentcolorarray["Elevator entrapment"] = "#45D2D1"
            incidentcolorarray["Access card lost/found/damaged"] = "#E0C769"
            incidentcolorarray["Other major security deficiencies"] = "#E16A68"

            incidentcolorarray["Active Alarm"] = "#CDCDCD"
            incidentcolorarray["Safety Hazards"] = "yellow"


            let responsekpi = $("."+payload.widgetInfo.dataTargetId+" .reskpiselection").val();
            chartwc.find('.dasboard-card-body .chartcanvas_incanalytics').html('');
            chartwc.find('.dasboard-card-body .chartcanvas_incanalytics').html(`<canvas class="incanalytics"  id="IncanalyticsChart"></canvas>`);
            if(responsekpi!="all"){
            let responsevalues = $("#"+responsekpi).val();
            responsevalues = responsevalues.split(",")
            let xmonths = ($("#xmonths").val()).split(",");

            let ctx = chartwc.find('.dasboard-card-body .incanalytics');
            try {
                let indlinecolor = "f23c22";
                if(incidentcolorarray[$("#"+responsekpi).attr("attr-dataname")]){
                    indlinecolor = incidentcolorarray[$("#"+responsekpi).attr("attr-dataname")];
                }
                let chart = new Chart(ctx, {
                // The type of chart we want to create
                type: 'line',

                // The data for our dataset
                data: {
                    labels: xmonths,
                    datasets: [{
                        label: $("#"+responsekpi).attr("attr-dataname"),
                        backgroundColor: 'transparent',
                        borderColor: indlinecolor,
                        data: responsevalues
                    }]
                },

                // Configuration options go here
                options: {
                    legend:{
                         display:false
                    },
                    layout: {
                        padding: {
                            left:25,
                            right: 0,
                            top: 30,
                            bottom: 50
                        }
                    }
                }
            });
            } catch (error) {
               console.log("Error")
            }
            }else{
                try {
                    let dataarray = [];
                    let ctx = chartwc.find('.dasboard-card-body .incanalytics');
                    let xmonths = ($("#xmonths").val()).split(",");


                    let i =0;

                    $('.dataset').each(function() {
                            let currentElement = $(this);
                            let linecolor = colorarray[i];
                            if(incidentcolorarray[currentElement.attr("attr-dataname")]){
                                linecolor = incidentcolorarray[currentElement.attr("attr-dataname")];
                            }
                            let responsevalues = currentElement.val();
                            responsevalues = responsevalues.split(",");
                            let dobject = {
                                label: currentElement.attr("attr-dataname"),
                                backgroundColor: 'transparent',
                                borderColor: linecolor,
                                data: (currentElement.val()).split(",")
                            }

                            dataarray.push(dobject)
                            i++;
                        });


                    let chart = new Chart(ctx, {
                    // The type of chart we want to create
                    type: 'line',

                    // The data for our dataset
                    data: {
                        labels: xmonths,
                        datasets: dataarray
                    },

                    // Configuration options go here
                    options: {
                        legend:{
                             display:false
                        },
                        scales:{

                            yAxes: [ {
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Time (Hours)'
                                }
                                } ]

                        },
                        layout: {
                            padding: {
                                left:25,
                                right: 0,
                                top: 30,
                                bottom: 50
                            }
                        }
                    }
                });
                } catch (error) {
                    console.log(error)
                    $(".chartcanvas").html(`<div style="text-align:center;padding-top:10px">No records found</div>`);
                }


            }

        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
