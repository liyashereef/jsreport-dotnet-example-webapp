<!-- Demo Widget -->
<style>
    .card-bg, .Monday, .Tuesday, .Wednesday, .Thursday, .Friday, .Saturday, .Sunday{
        background-color: #f26321;
    }

    .Saturday_unscheduled, .Sunday_unscheduled {
        background-color: lightyellow;
    }

    .card-header-bg {
        background: #13486b;
        color: white;
    }

    .txt_center {
        align:center;
        vertical-align: middle !important;
    }

    .td_1 {
        vertical-align: middle !important;
        /*font-size: 10px;*/
        font-weight: bold;
        width: 0.9em;
    }

    .td_user_name {
        vertical-align: middle !important;
        /*font-size: 10px;*/
        font-weight: bold;
        width: 0.9em;
    }

    .th_1 {
        vertical-align: middle !important;
        /*font-size: 10px;*/
        font-weight: bold;
        width: 0.9em;
    }

    .blockquote_custom {
        font-size: 10px;
        font-weight: bold;
    }

    .blockquote_row {
        /*height: 70px;*/
    }

    .blockquote_header {
        /*        height: 60px;*/
    }


    .bg_black {
        color: black;
    }

    #employee_schedule_tbl td.value_card:hover{
        -webkit-transform: scale(1.1, 1.1);
        -moz-transform: scale(1.1, 1.1);
        -o-transform: scale(1.1, 1.1);
        -ms-transform: scale(1.1, 1.1);
        transform: scale(1.1, 1.1);
    }

    .schedule_tbl_body {
        display:block;
        max-height:40%;
    }
    .employee_schedule_tbl tbody tr {
        display:table;
        width:100%;
        table-layout:fixed;/* even columns width , fix width of table too*/
    }

    .schedule_tbl_header{
        display: block;
    }

    #payperiod_select .selection {
        display: initial;
    }

    .table_1 {
        display: block;
        width: 100%;
    }

    .table_content {
        /*overflow: auto;*/
    }


    .card-custom {
        position: relative;
        display: -ms-flexbox;
        -ms-flex-direction: column;
        flex-direction: column;
        background-clip: border-box;
        border: 1px solid rgba(0, 0, 0, .125);
        border-radius: .25rem;
        word-wrap: normal !important;
        display: block;
        font-size: 14px;
    }

    .table_1 tr:first-child>td{
        position: sticky;
        top: 0;
        z-index: 1;
        background: rgba(255, 255, 255, 0.6);
        max-height: 450px  !important;
    }

    .schedule-tbl {
        margin-top: 5px;
    }

    .schedule-header-1 {
        padding-bottom: 5px !important;
    }

    .schedule-header-2 {
        padding-bottom: 6px !important;
    }


    .image-wrapper {
        position: absolute;
        overflow-y:auto;
        background: #003A63;
        border-radius: 5px;
        min-width: 200px;
        max-width: 900px;
        max-height: 100px;
        max-height: 200px;
        padding: 5px;
        padding-right: 25px;
        color: #fff;
        transform: translate(-50%, -50%);
        opacity: 1;
        font-size: 13px;
        z-index: 2;
        line-height: 16px;
    }
</style>

<script>
    //Use function name as camalCase version of WidgetTag
    //Always use let instead of var
    widgets.define('widgetScheduling',function(payload) {
        let wc; //widget container
        let schedulesObjects = payload.data.schedules;
        var schedules = $.map(schedulesObjects, function(value, index) { return [value]; });
        schedules.sort((a,b) => (a.user_name > b.user_name) ? 1 : ((b.user_name > a.user_name) ? -1 : 0));
        let tableHeaderRow = payload.data.headerData;
        let payPeriods = payload.payPeriods;
        let filters = payload.filters;
        let defaultSelectedOption = payload.defaultSelectedOption;
        const scheduleFilterKey = 'payperiod_id';

        function applyHeaderFilter() {
            //select box
            let options = `<option>--No options found--</option>`;
            if ((payPeriods != null) && (payPeriods != undefined)) {
                options = ``;
                for(let payPeriod of payPeriods) {
                    let selected = isFilterSelected(scheduleFilterKey, payPeriod.id) ? 'selected' : '';
                    options += `<option ${selected} value="${payPeriod.id}">${payPeriod.pay_period_name+` (`+payPeriod.short_name+`)`}</option>`;
                }
            }
            let outupt = `<select class="w-sch-payperiod form-control">${options}</select>`;

            //Replace filter content with gen html
            $('body').find(`.${payload.widgetInfo.dataTargetId} .filter-content`).html(outupt);

            if(defaultSelectedOption != '') {
                $('.w-sch-payperiod:visible').val(defaultSelectedOption);
            }
        }

        function isFilterSelected(key, value) {
            let fv = filters[scheduleFilterKey];
            return fv == value;
        }

        function generateContent() {
            let employeeHtml = ``;
            let scheduleWithDataStyle = `display: none;`;
            let scheduleWithOutDataStyle = ``;
            let scheduleHeader = ``;
            let scheduleBody = ``;

            if((schedules != null) && (schedules != '')) {
                scheduleWithDataStyle = ``;
                scheduleWithOutDataStyle = `display: none;`;
                employeeHtml = `<table class="table_1 employee_list_schedule" id="employee_list_schedule" style="overflow-y: scroll;"><tr><td><div class="schedule_user_header card-custom card-header-bg text-white text-center schedule-header-1"><span>Employee Name</span><br><small>Role</small></div></td></tr>`;

                $.each(schedules, function(indexKey, schedule) {
                    let trainingContent = ``;
                    if(schedule.training_details != '') {
                        trainingContent = `<span style="padding-left:10px;cursor:pointer;" class="trainingdetail-approval text-center" data-expand="false" data-training="`+schedule.training_details+`">
                        <i class="fas fa-book-reader fa-sm" title="click here to view training details"></i>
                    </span>`;
                    }
                    employeeHtml += ` <tr>
                                        <td style="width: 300px;">
                                            <div class="card-custom card-header-bg text-white text-center" style="height: 100%;">
                                                <span class="user-details" title="`+schedule['title']+`">`+schedule['user_name']+`</span><br />
                                                <small class="user-details" title="`+schedule['role']+`">
                                                    `+schedule['role']+`
                                                </small>`+trainingContent+`
                                            </div>
                                        </td>
                                        </tr>`;

                    let scheduleData = schedule.schedule_data;
                    scheduleBody += `<tr>`;
                    $.each(scheduleData, function(peyPeriod, entries) {
                        if(typeof entries === 'object') {
                            $.each(entries, function(week, dataArray) {
                                if(typeof dataArray === 'object') {
                                    $.each(dataArray, function(key, data) {
                                        if(data.is_data == true) {
                                            scheduleBody += `  <td class=" value_card" style="width: 150px;z-index:-1">
                                                <div class="card-custom `+data.day+` text-white text-center" title="`+data.start_datetime+`-`+data.end_datetime+` (`+data.hours+` hrs)`+`"  style="height: 100%;">
                                                    <span>`+data.start_datetime+` - `+data.end_datetime+`</span><br />
                                                    <b>`+data.hours+` hrs</b>
                                                </div>
                                            </td>`;
                                        }else{
                                            scheduleBody += `<td style="width: 150px;z-index:-1">
                                                <div class="card-custom `+data.day+`_unscheduled text-center"  style="height: 100%;">
                                                    <span>&nbsp;</span><br />
                                                    <b>&nbsp;</b>
                                                </div>
                                            </td>`;
                                        }
                                    });

                                    let weekHeader = schedule[`week_`+peyPeriod+`_`+week];
                                    scheduleBody += `<td style="width: 150px;z-index:-1">
                                                        <div class="card-custom text-center"  style="height: 100%;">
                                                            <span>`+weekHeader+` hrs</span><br />
                                                            <b>&nbsp;</b>
                                                        </div>
                                                    </td>`;
                                }
                            });
                            let payPeriodObjectKey = peyPeriod+`_display`;
                            let payPeriodKeyExists = schedule.hasOwnProperty(payPeriodObjectKey);
                            let payPeriodHeader = `00:00`;
                            if(payPeriodKeyExists) {
                                payPeriodHeader = schedule[payPeriodObjectKey];
                            }
                            scheduleBody += `<td style="width: 150px;z-index:-1">
                                                <div class="card-custom text-center"  style="height: 100%;">
                                                    <span>`+payPeriodHeader+` hrs</span><br />
                                                    <b>&nbsp;</b>
                                                </div>
                                            </td>`;
                        }
                    });

                    let scheduleObjectKey = `total_hours_display`;
                    let scheduleKeyExists = schedule.hasOwnProperty(scheduleObjectKey);
                    let scheduleObjectHeader = `00:00`;
                    if(scheduleKeyExists) {
                        scheduleObjectHeader = schedule.total_hours_display;
                    }
                    scheduleBody += `<td style="width: 150px;z-index:-1">
                                        <div class="card-custom text-center"  style="height: 100%;">
                                            <span>`+scheduleObjectHeader+` hrs</span><br />
                                            <b>&nbsp;</b>
                                        </div>
                                    </td></tr>`;
                });
                employeeHtml += `</table>`;


                if(tableHeaderRow != null) {
                    let payPeriodCount = 1;
                    $.each(tableHeaderRow, function(index1, headerRows) {
                        $.each(headerRows, function(index2, headerRow) {
                            $.each(headerRow, function(index3, header) {
                                scheduleHeader +=`<th class="" style="width: 150px;">
                                                    <div class="card-custom card-header-bg text-white text-center" title="`+header.value+ ` (`+header.display+`)` + `" style="height: 100%;">
                                                        <span>`+header.value+`</span><br />
                                                        <b>`+header.display+`</b>
                                                    </div>
                                                </th>`;
                            });

                            scheduleHeader +=`<th style="width: 150px;">
                                                <div class="card-custom card-header-bg text-white text-center" title="Week Total" style="height: 100%;">
                                                    <span>Week `+index2+`</span><br />
                                                    <b>Hours</b>
                                                </div>
                                            </th>`;
                        });

                        scheduleHeader +=`<th class="" style="width: 150px;">
                                            <div class="card-custom card-header-bg text-white text-center" title="Week Total" style="height: 100%;">
                                                <span>Payperiod</span><br />
                                                <b>Hours</b>
                                            </div>
                                        </th>`;
                        payPeriodCount++;
                    });

                    if(payPeriodCount > 1) {
                        scheduleHeader +=`<th class="" style="width: 150px;">
                                            <div class="card-custom card-header-bg text-white text-center" title="Week Total" style="height: 100%;">
                                                <span>Total</span><br />
                                                <b>Hours</b>
                                            </div>
                                        </th>`;
                    }
                }
            }else{
                 scheduleWithDataStyle = `display: none;`;
                 scheduleWithOutDataStyle = ``;
            }

            let content = `<div class="schedule-widget-main-div" style="overflow-x: hidden !important;"><div id="table_content" class="table_content" style="overflow-x: hidden;max-width: 100%;">
                <table class="schedule-tbl">
                    <tr id="emp-schedule-no-data" class="emp-schedule-no-data"  style="`+scheduleWithOutDataStyle+`"><td colspan="2" class="text-center" style="vertical-align:middle;width: 5% !important;">No data found</td></tr>
                    <tr id="emp-schedule-with-data" class="emp-schedule-with-data" style="`+scheduleWithDataStyle+`">
                        <td valign="top" id="employeeblock" class="employeeblock" style="width:300px">`+employeeHtml+`</td>
                        <td id="scrollarea" class="scrollarea-td" style="overflow-y: scroll !important;">
                            <table class="table_1 employee_schedule_tbl" id="employee_schedule_tbl" style="margin: 0% 2% 1% 0%;">
                                <tbody id="schedule_tbl_header" class="schedule_tbl_header" style="overflow-x:scroll;position: sticky;top: 0;z-index: 1;background: rgba(255, 255, 255, 0.6);">`+scheduleHeader+`</tbody>
                                <tbody id="schedule_tbl_body" class="schedule_tbl_body" style="overflow-x:hidden;overflow-y:hidden;position: sticky;">`+scheduleBody+`</tbody>
                            </table>
                        </td>
                    </tr>
                </table>
            </div></div>`;
            //..process
            return content;
        }

        function bindContent(el) {
            wc = $('body').find(`.${payload.widgetInfo.dataTargetId}`);
            wc.find('.dasboard-card-body').html(el);

            bindTrainingToolTip();
            bindUserDetailToolTip();
            applyHeaderScroll();
        }

        function refreshWithFilter() {
            widgets.refresh(payload.widgetTag, filters);
        }

        function afterBind() {
            wc.find(`.w-sch-payperiod`).on('change', function() {
                filters[scheduleFilterKey] = $(this).val();
                refreshWithFilter();
            });
        }

        function bindTrainingToolTip() {
            let iw = $('body').find(`.trainingdetail-approval`);
            iw.on('click', function(e) {
                $(".image-wrapper").remove();
                let content = $(this).attr("data-training");
                let expand = $(this).attr("data-expand");
                if (content !== "" && expand == "false") {
                    $(this).attr('data-expand', true);
                    tooltipcreation(e, content);
                }else{
                    $(this).attr('data-expand', false);
                }
            });
        }

        let tooltipcreation = function (ev, content) {
            let left = Number(ev.pageX) + 200;
            let ulcontentarray = content.split("|");
            let ulcontent = "<ol>";
            ulcontentarray.forEach(element => {
                if (element.trim() != "") {
                    ulcontent += "<li>" + element + "</li>";
                }

            });
            ulcontent += "</ol>";
            let div = $('<div class="image-wrapper">')
                    .addClass('image-wrapper')
                    .css("cssText", "left:" + left + "px !important;" + "top:" + ev.pageY + 'px !important;')
                    .append("<p><b>Completed Training</b><span class='tooltip-schedule' style='float:right;cursor:pointer'>x</span></p><p>" + ulcontent + "</p>")
                    .appendTo(document.body);

            let iw = $('body').find(`.tooltip-schedule`);
            iw.on('click', function() {
                $(".image-wrapper").remove();
                $(this).attr('data-expand', false);
            });
        };

        function bindUserDetailToolTip() {
            let udT = $('body').find(`.user-details`);

            udT.tooltip({
                position: {
                    my: "center bottom-20",
                    at: "center top",
                    using: function (position, feedback) {
                        $(this).css(position)
                        $("<div>")
                                .addClass("arrow")
                                .addClass(feedback.vertical)
                                .addClass(feedback.horizontal)
                                .appendTo(this);
                    }
                }
            });
        }

        function applyHeaderScroll() {
            let employeeTblHeight = $('.schedule-widget-main-div:visible').parent().height();
            let employeeListWidth = $('.employee_list_schedule:visible').height();
            let headerHeight = $('.schedule_tbl_header:visible').height();
            if(employeeListWidth > employeeTblHeight) {
                $('.employee_list_schedule:visible').height(employeeTblHeight);
                $('.schedule_tbl_body:visible').height((employeeTblHeight - headerHeight));
            }

            let scrollAreaWidth = $('.widget-scheduling:visible').width();
            let newScrollAreaWidth = (scrollAreaWidth - 300);
            $(".scrollarea-td:visible" ).css( "maxWidth", newScrollAreaWidth + 'px');

            //scroll elements
            let desTbl = $('body').find(`.schedule_tbl_body:visible`);
            let srcTbl = $('body').find(`.schedule_tbl_header:visible`);
            let employeeListDiv = $('body').find(`.employee_list_schedule:visible`);

            //scroll events
            srcTbl.scroll(function() {
                desTbl.prop("scrollLeft", this.scrollLeft);
            });

            employeeListDiv.scroll(function() {
                desTbl.prop("scrollTop", this.scrollTop);
            });
        }

        //Bind contents
        applyHeaderFilter();
        bindContent(generateContent());

        //Execute after content is added to dom
        afterBind();
    });
</script>
