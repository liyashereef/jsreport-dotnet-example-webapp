<script src="https://cdn.jsdelivr.net/npm/gijgo@1.9.9/js/gijgo.min.js" type="text/javascript"></script>
<link href="https://cdn.jsdelivr.net/npm/gijgo@1.9.9/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<style>
    .gj-datepicker-md {
        color: #e74b20;
    }
    .popover-body {
        max-height: 250px;
        overflow-y: auto;
    }
    .sensor-room-header {
        background: #e74b1c;
        text-align:center;
        line-height:1;
        position: sticky;
        top: 0;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        color:#fff;
    }
    .sensor-time-header{
        background: #343F4E;
        color: #fff;
        text-align:left;
        line-height:1;
        width:7% !important;
    }
    .sensor-room-severity-header {
        background: #e74b1c;
        text-align:center;
        line-height:1;
        position: sticky;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        color:#fff;
        outline: solid 1px #dce1e6;
        outline-offset: -2px;
        top:31px;
    }

    .irc-tbl td {
        padding: 9px;
    }
</style>
<!-- Demo Widget -->
<script>
    //Use function name as camalCase version of WidgetTag
    //Always use let instead of var
    widgets.define('widgetMotionSensor', function(payload) {
        let wc; //widget container
        let filters = payload.data["motion_sensor"]["filters"];
        let start_date = filters['start_date'];
        let end_date = filters['end_date'];
        color_code = ['#ffffff','#f44336','#ffff00','#047B06'];
        function applyHeaderFilter() {
            let data = payload.data;
            start_date_pre_val = start_date;
            end_date_pre_val = end_date;
            let output = '<div class="d-flex" style="display: inline-block;float:right;"><span style="margin:10px 10px 0px 10px;">From: </span> <input width="200" style="line-height:1 !important;" type="text" class="datepicker" value="' + start_date + '" id="start_date" /><span style="margin:10px 10px 0px 10px;">To:</span> <input width="200" style="line-height:1 !important;" type="text" class="datepicker" value="' + end_date + '" id="end_date" /></div>';

            //Replace filter content with gen html
            $('body').find(`.${payload.widgetInfo.dataTargetId} .filter-content`).html(output);
            $('#start_date').datepicker({
                "format": "yyyy-mm-dd",
                "setDate": start_date,
            });
            $('#end_date').datepicker({
                "format": "yyyy-mm-dd",
                "setDate": end_date,
            });

        }

        function generateContent() {
            let content = '';
            let data = payload.data;
            let firstTable = '<div class="motion-sensor-time" style="width:20%;float:left;height:500px;overflow:auto;"><table class="table table-bordered irc-tbl" style="height:100%;"><tr><td style="background: #343F4E;color: #fff;text-align:left;line-height:1; position: sticky; top: 0;box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);border-bottom:1px solid;" ><strong>Time</strong></td></tr><tr><td style="background: #343F4E;color: #fff;text-align:left;line-height:1; position: sticky; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);border-bottom:1px solid;top:31px;" ><strong>Severity</strong></td></tr>';
            let datatable = '<div class="motion-sensor-header" style="width:80%;float:right;overflow:auto;"><table class="table table-bordered irc-tbl">';
            let time = data["motion_sensor"]["time"];
            let room = data["motion_sensor"]["rooms"];
            let severity = data["motion_sensor"]["severity"];
            let more_details = data["motion_sensor"]["more_details"];
            let sensor_details = data["motion_sensor"]["sensor_details"];
            let count = '';
            let header = '';
            let severity_header = '';

            for (let key in time) {
                firstTable += '<tr><td class="sensor-time-header" style="border-bottom:1px solid;"><strong>' + time[key] + '</strong></td></tr><tr>';
            }
            firstTable += '</table></div>';
            datatable = firstTable+datatable;

            for (let nkey in room) {
                let dataStr = room[nkey];
                header += '<td class="sensor-room-header" title="'+dataStr+'"><strong>' + (((dataStr.length) >= 20) ? dataStr.substr(0,15) + '...' : dataStr) + '</strong></td>';
            }
            header += '</tr>';
            datatable += header;

            for (let severity_key in severity) {
                severity_header += '<td class="sensor-room-severity-header" style="padding:6px !important;background:'+color_code[severity[severity_key]]+'">&nbsp;</td>';
            }
            severity_header += '</tr></table></div>';
            datatable += severity_header;

            datatable += `<div class="motion-sensor-content" style="width:80%;float:right;height:438px;overflow:hidden;"><table class="table table-bordered irc-tbl" style="height:100%;"><tr>`
            for (let key in time) {
                for (let nkey in room) {
                    if(sensor_details){
                      if ((sensor_details[nkey] != undefined) && (sensor_details[nkey][key] != undefined)) {
                          count = sensor_details[nkey][key];
                          datatable += '<td class="inres-bluebg items" data-toggle="popover" title="More Details" data-content="' + more_details[nkey][key] + '" style="text-align:left;line-height:1;"><strong></strong>&nbsp;</td>'
                      } else {
                          count = '';
                          datatable += '<td class="items" style="text-align:left;line-height:1;">&nbsp;</td>'
                      }
                    }else{
                        count = '';
                        datatable += '<td class="items" style="text-align:left;line-height:1;">&nbsp;</td>'
                    }
                }
                datatable += `</tr><tr>`
            }
            datatable += `</tr>`
            datatable += `</table></table>`
            content = datatable;
            //..process
            return content;
        }

        function bindContent(el) {
            wc = $('body').find(`.${payload.widgetInfo.dataTargetId}`);
            wc.find('.dasboard-card-body').html(el);

            applyHeaderScroll();
        }

        function applyHeaderScroll() {
            //scroll elements
            let desTbl = $('body').find(`.motion-sensor-content:visible`);
            let srcTbl = $('body').find(`.motion-sensor-header:visible`);
            let employeeListDiv = $('body').find(`.motion-sensor-time:visible`);

            employeeListDiv.scroll(function() {
                desTbl.prop("scrollTop", this.scrollTop);
            });

            //scroll events
            srcTbl.scroll(function() {
                desTbl.prop("scrollLeft", this.scrollLeft);
            });
        }

        function refreshWithFilter() {
            widgets.refresh(payload.widgetTag, filters);
        }

        function afterBind() {
            wc.find('#start_date').on('change', function() {
                start_date = $(this).val();
                if (start_date !== start_date_pre_val) {
                    filters['start_date'] = $(this).val();
                    refreshWithFilter();
                }
            });
            wc.find('#end_date').on('change', function() {
                end_date = $(this).val();
                if (end_date !== end_date_pre_val) {
                    filters['end_date'] = $(this).val();
                    refreshWithFilter();
                }
            });
        }


        applyHeaderFilter();

        //Bind contents
        bindContent(generateContent());
        makeCommonWidthForHeaders();

        $('.inres-bluebg').popover({
            trigger: "click",
            html: true
        });
        $('.inres-bluebg').on('click', function(e) {
            $('.inres-bluebg').not(this).popover('hide');
        });
        $('body').on('click', function(e) {
            //did not click a popover toggle or popover
            if ($(e.target).data('toggle') !== 'popover' &&
                $(e.target).parents('.popover.in').length === 0) {
                $('[data-toggle="popover"]').popover('hide');
            }
        });

        function makeCommonWidthForHeaders() {
            let tdWidth = 0;
            $('.sensor-room-header').each(function(index, element){
                if(tdWidth <  $(element).width()) {
                    tdWidth = $(element).width() + 15;
                }
            });

            $('.sensor-room-header').css('min-width', tdWidth);
            $('.sensor-room-header').css('max-width', tdWidth);
            $('.items').css('min-width', tdWidth);
            $('.items').css('max-width', tdWidth);
        }

        //Execute after content is added to dom
        afterBind();
    });
</script>
