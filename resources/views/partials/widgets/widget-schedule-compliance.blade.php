<!-- Schedule Compliance -->
<style>
    .bluebg{
        background: #003b63;
        color: #fff;
    }
    .blackbg{
        background: #000;
        color: #fff;
    }
    .orangebg{
        background: #f23c22;
        color:#fff;
    }
    .redbg{
        background: red;
        color:#fff;
    }
    .greenbg{
        background: green;
        color:#fff;
    }
    .yellowbg{
        background: yellow;
        color:#fff;
    }
    .staticblock{
        /* font-weight:bold; */
    }
    .schedulefilter{
        max-width:40%;
        float:right;
    }
    thead tr{
    }
    #schdtbl th {
    position:-webkit-sticky;
    position: sticky;
    top: 0;
    z-index: 5;

    }
    body .tooltip-inner {
        max-width: 250px;
        padding: 3px 8px;
        color: #fff;
        text-align: center;
        background-color: #000;
        border-radius: .25rem
    }
    body .tooltip .arrow::before {
    border-bottom-color: black;
    }


</style>

<script>
    //Use function name as camalCase version of WidgetTag
    //Always use let instead of var
    widgets.define('widgetScheduleCompliance', function(payload) {
        let wc; //widget container

        function generateContent() {
            let data = payload.data;
            let tabledata = jQuery.parseJSON(data[1]) ;
            return processdata(data,tabledata)
        }


        function processdata(data,tabledata){
            let start_date = new Date(data[0][0]);
            let end_date = new Date(data[0][1]);
            let datearray = data[2];
            let datalength = tabledata.length;

            let leftblock="";
            let datatable = `<table id="schdtbl" class="table tbl-line-height-1 schedule-compliance-tbl">`
            if(datalength<1){
                datatable+=`<tr><td style="text-align:center">No record found </td></tr>`;

            }   else{
                leftblock=`<table id="leftschdtbl" class="table tbl-line-height-1
             left-schedule-compliance-tbl">`;
             leftblock+= `<thead style="background:white !important;position: sticky;">
            <tr style="background:white !important;">
            <th class="staticblock" style="padding-left:18px !important;padding-right:50px !important;background:white">
               &nbsp;
            </th>`;

            datatable+= `<thead style="background:white !important;position: sticky;">
            <tr style="background:white !important;">
            `;
            let dt = new Date(end_date);
            let dtcount = 0;

            for (let index = 0; index < datearray.length; index++) {
                const element = datearray[index];
                let datevar =element["display"]
                datatable+=`<th class="bluebg staticblock"
                style="text-align:center;border-right:solid 1px #fff !important">
                ${datevar}
                </th>`;
                dtcount++;
            }
            datatable+="</tr></thead>";
            colspan = dtcount+1;


            tabledata  = (Object.values(tabledata));
            datatable+="</tbody>";
            tabledata.forEach(function(element) {
                try {

                    Object.keys(element).forEach(key => {
                       try {
                           let name = element[key]["name"];
                           if(element[key]["name"]!=undefined){
                                datatable+=`<tr class="orangebg">`
                                datatable+=`<td  colspan="${colspan}" class="staticblock"
                                style="padding-left:18px !important;padding-right:50px !important;">&nbsp;</td>`
                                datatable+=`</tr>`

                                leftblock+=`<tr class="orangebg">`
                                leftblock+=`<td  class="staticblock"
                                style="padding-left:18px !important;padding-right:50px !important;">${name}</td>`
                                leftblock+=`</tr>`
                                leftblock+=`<tr>`
                                leftblock+=`<td class="bluebg staticblock"
                                style="padding-left:18px !important;padding-right:50px !important;">On Time</td></tr>`
                                leftblock+=`<tr>`
                                leftblock+=`<td class="bluebg staticblock"
                                style="padding-left:18px !important;padding-right:50px !important;">Full Shift</td></tr>`
                                datatable+=`<tr>`
                                let start = new Date(data[0][0]);
                                let end = new Date(data[0][1]);

                                let sloop = new Date(end);

                                for (let index = 0; index < datearray.length; index++) {
                                    let  element2 = datearray[index];
                                    let dyndatevar  = element2["date"];

                                //let tbldata = Object.keys(empdata);
                                let empdata =  element[key];

                                if(empdata[dyndatevar]){
                                    let ontimecolor = empdata[dyndatevar]["ontimecolor"];
                                    var scheduledStartTime = empdata[dyndatevar]["converted_scheduled_starttime"];
                                    var actualSigninTime = empdata[dyndatevar]["converted_actual_starttime"];
                                        let tooltip="";
                                        if(ontimecolor=="black"){
                                            tooltip=`data-toggle="tooltip"
                                            data-placement="top" title="No show"`;
                                        }else if(ontimecolor=="green"){
                                            tooltip=`data-toggle="tooltip"
                                            data-placement="top" title="Scheduled Start Time : `+scheduledStartTime+`&#013; Actual Sign In Time : `+actualSigninTime+` "
                                            `;
                                        }
                                        else if(ontimecolor=="yellow"){
                                            tooltip=`data-toggle="tooltip"
                                            data-placement="top" title="Scheduled Start Time : `+scheduledStartTime+`&#013; Actual Sign In Time : `+actualSigninTime+` "
                                            `;
                                        }else if(ontimecolor=="red"){
                                            tooltip=`data-toggle="tooltip"
                                            data-placement="top" title="Scheduled Start Time : `+scheduledStartTime+`&#013; Actual Sign In Time : `+actualSigninTime+` "
                                            `;
                                        }
                                        datatable+=`<td class="tooltp" ${tooltip}
                                        style="background:${ontimecolor};border-right:solid 1px #fff !important">&nbsp;</td>`

                                }else{
                                    datatable+=`<td data-toggle="tooltip" data-placement="top" title="No schedule" style="
                                    border-right:solid 1px #fff !important">&nbsp;</td>`
                                }
                                let dynnewDate = sloop.setDate(sloop.getDate() - 1);
                                sloop = new Date(dynnewDate);

                                }
                                datatable+=`</tr>`


                                datatable+=`<tr>`

                                let start2 = new Date(data[0][0]);
                                let end2 = new Date(data[0][1]);

                                let sloop2 = new Date(end2);
                                for (let index3 = 0; index3 < datearray.length; index3++) {
                                    let  element3 = datearray[index3];
                                    let dyndatevar2  = element3["date"];

                                //let tbldata = Object.keys(empdata);
                                let empdata2 =  element[key];

                                if(empdata2[dyndatevar2]){
                                    var scheduledEndTime  = empdata2[dyndatevar2]["converted_scheduled_endtime"];
                                    var actualSignoutTime = empdata2[dyndatevar2]["converted_actual_endtime"];
                                    if(empdata2[dyndatevar2]["fullshift"]==1){
                                            datatable+=`<td class="greenbg tooltp"
                                            style="border-right:solid 1px #fff !important"
                                            data-toggle="tooltip"  attr-id="1"
                                            data-placement="top" title="Scheduled End Time : `+scheduledEndTime+`&#013; Actual Signout Time : 
                                            `+actualSignoutTime+` &#013; Actual Work Hours : `+empdata2[dyndatevar2]["actual_work_hours"]+` Hours">&nbsp;</td>`
                                    }else if(empdata2[dyndatevar2]["work_hours"]=="Nil"){
                                            datatable+=`<td class="blackbg tooltp"
                                            data-toggle="tooltip"
                                            data-placement="top" title="No show" style="border-right:solid 1px #fff !important">&nbsp;</td>`
                                    }else if(empdata2[dyndatevar2]["work_hours"]<empdata2[dyndatevar2]["expectedworkhours"]){
                                        let wrkHours=empdata2[dyndatevar2]["actual_work_hours"];
                                        let wrkHourString="";
                                        if(wrkHours!=""){
                                            wrkHourString=wrkHours+" Hours";
                                        }
                                        if(empdata2[dyndatevar2]["work_hours"]>=empdata2[dyndatevar2]["expectedworkhoursundertolerance"]){
                                            datatable+=`<td class="yellowbg tooltp"
                                            data-toggle="tooltip" attr-id="3"
                                            data-placement="top" title="Scheduled End Time : `+scheduledEndTime+`&#013; 
                                            Actual Signout Time : `+actualSignoutTime+`&#013; 
                                            Actual Work Hours : `+wrkHourString+`   
                                            " style="border-right:solid 1px #fff !important"></td>`
                                        }else{
                                            datatable+=`<td class="redbg tooltp"
                                            data-toggle="tooltip" attr-id="3"
                                            data-placement="top" title="Scheduled End Time : `+scheduledEndTime+`&#013; 
                                            Actual Signout Time : `+actualSignoutTime+`&#013; 
                                            Actual Work Hours : `+wrkHourString+`   
                                            " style="border-right:solid 1px #fff !important"></td>`
                                        }
                                           
                                    }
                                }else{
                                    datatable+=`<td class="" style="
                                    border-right:solid 1px #fff !important">&nbsp;</td>`
                                }
                                let dynnewDate2 = sloop2.setDate(sloop2.getDate() - 1);
                                sloop2 = new Date(dynnewDate2);

                                }
                                datatable+=`</tr>`



                           }

                       } catch (error) {

                       }

                    });

                } catch (error) {

                }
            });
            leftblock+=`</table>`
            datatable+=`</tr></tbody></table>`
        }

            let content = `<div
            style="position: absolute;top: 48px;left: 0px;z-index: 1200;width: 300px;background: #FAFAFA;height: 36px;"></div>
            <div class="test" >
                <table >
                <tr>
                <td  valign="top">
                <div style="left:0;position:absolute;width:300px;overflow:hidden;z-index:1005;height:85%" class="dv1" id="dv1">
                ${leftblock}
                </div></td>
                <td valign="top">
                <div style="margin-left:300px;z-index:888" id="dv2">
                ${datatable}
                </div>
                </td>
                </tr></table>

            </div>
            `;
            //..process
            return content;
        }

        function bindContent(el) {
            let stdate = (payload.data)[0][0];
            wc = $('body').find(`.${payload.widgetInfo.dataTargetId}`);
            wc.find('.dasboard-card-body').html(el);
            let filtertext = `<input readonly type="text" id="schedulefilter" class="schedulefilter form-control" value="${stdate}" />`
            wc.find(".filter-content").html(filtertext)
            wc.find(".schedulefilter").datepicker({
                format: 'yyyy-mm-dd',maxDate: new Date()

            })
            wc.find(".schedulefilter").on("change",function(e){
                e.preventDefault();
                $.ajax({
                    type: "post",
                    url: '{{ route("scheduling.compliancewidgetdata") }}',
                    data: {startdate:$(this).val(),
                    "customer-search":$("#dashboard-filter-customer").val(),
                    "customer-id":$("#dashboard-filter-customer").val()    },
                    headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        let data = response;
                        let tabledata = jQuery.parseJSON(data[1]) ;
                        let newdata =  processdata(data,tabledata);
                        wc.find('.dasboard-card-body').html(newdata);
                        let cssheight=wc.find(".dasboard-card-body").height()-10
                        wc.find('.dv1').css("height",cssheight+"px")

                    }
                });
            })
            let dvid =(wc.find(".widget-div").attr("id"))
            setTimeout(() => {
                $("#"+dvid).animate({ scrollTop: 0 }, "fast");
            }, 50);
            $(".tooltp").tooltip("hide")
        }

        function afterBind() {
            //After content render (eg:register envent listeners | init eg: select2)
            let dvid =(wc.find(".widget-div").attr("id"))
            let expheight = ($("#"+dvid).height())-5
            wc.find("#dv1").css("height",expheight+"px")
            wc.on('click', function() {
               // alert('Widget body click');
            });
            wc.find('#leftschdtbl').on("scroll",function(e){
               // alert("here");
            })

            $("div").scroll(function(){
                let sid=(this.id);
                if(sid.search("schedule-compliance")){
                    var $divs = $('#'+sid+' .dv1, #'+sid);
                    // var sync = function(e){
                    //     var $other = $divs.not(this).off('scroll'), other = $other.get(0);
                    //     var percentage = this.scrollTop / (this.scrollHeight - this.offsetHeight);
                    //     other.scrollTop = percentage * (other.scrollHeight - other.offsetHeight);
                    //     // Firefox workaround. Rebinding without delay isn't enough.
                    //     setTimeout( function(){ $other.on('scroll', sync ); },10);
                    // }
                    // $divs.on( 'scroll', sync);
                    $('#'+sid).on('scroll', function () {
                        $('#'+sid+' .dv1').scrollTop($(this).scrollTop());
                    });
                }
            });


        }

        //Bind contents
       // bindContent(widgetheader());
        bindContent(generateContent());

        //Execute after content is added to dom
        afterBind();
        $(document).on("mouseover",".tooltp",function(){
            // if($('.tooltip').is(':ui-tooltip')) {
            //     $('.tooltip').tooltip("hide")
            // }
                if(!$(this).data('tooltip')){
                        $('[data-toggle="tooltip"]').tooltip();
                }else{
                    $('[data-toggle="tooltip"]').tooltip("hide");
                }
                
            
        })


    });
</script>
