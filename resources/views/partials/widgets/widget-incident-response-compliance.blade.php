<!-- Demo Widget -->
<style>
    .filter-content {
        width: 60% !important;
    }
    .inres-greenbg{
        background:#047b07;
        color: #fff;
    }
    .inres-yellowbg{
        background:yellow;
        color: #000;
    }
    .inres-redbg{
        background:red;
        color: #fff;
    }
    .inres-orangebg{
        background: #f23c22;
        color:#fff;
    }
    .inres-bluebg{
        background: #343F4E;
        color: #fff;
    }
    .inres-whitebg{
        color: #fff;
        background: #fff;
    }
</style>
<script>
    //Use function name as camalCase version of WidgetTag
    //Always use let instead of var

    widgets.define('widgetIncidentResponseCompliance',function(payload) {
        let wc; //widget container
        let filters = payload.filters;
        const priorityFilterKey = 'selected-priority';

        function applyHeaderFilter() {
            let data = payload.data;
            //select box
            let options = `<option>--No site found--</option>`;
            if ((data.priorities != null) && (data.priorities != undefined)) {
                let priorities = data.priorities;
                //Generate options
                options = ``;
                for(let priority of priorities) {
                    let selected = isFilterSelected(priorityFilterKey, priority.id) ? 'selected' : '';
                    options += `<option ${selected} value="${priority.id}">${priority.value}</option>`;
                }
            }
            let output = `<select class="w-irc-priorities form-control">${options}</select>`;

            //Replace filter content with gen html
            $('body').find(`.${payload.widgetInfo.dataTargetId} .filter-content`).html(output);
        }

        function isFilterSelected(key, value) {
            let fv = filters[priorityFilterKey];
            return fv == value;
        }

        function generateContent() {
            let content = '';
            let data = payload.data;

            if ((data["compliance"] != null) && (data["compliance"] != undefined) && (data["compliance"]["headers"] != null) && (data["compliance"]["headers"] != undefined)) {
                let datatable = '<table class="table table-bordered irc-tbl tbl-line-height-1">';
                let header =  Object.values(data["compliance"]["headers"]);
                let rowdata =  Object.values(data["compliance"])
                let count = 0;
                items =  data["compliance"];
                let selectedPriorityText = $('.w-irc-priorities option:selected').text() + ' ';
                for(let index in items) {
                    if(index == 'headers'){
                    datatable+='<tr><td style="vertical-align: middle;width:42% !important;padding:17px !important;" class="inres-orangebg" rowspan="2">'+selectedPriorityText+'Priority</td><td colspan="3" class="inres-bluebg" align="center">Incident Response Time</td></tr><tr><td class="inres-orangebg"  style="text-align:center">'+ items[index][1]+' </td><td class="inres-orangebg" style="text-align:center">'+  items[index][2]+' </td><td class="inres-orangebg" style="text-align:center">'+  items[index][3] +' </td></tr>';
                    }else{
                        count++;
                    datatable+='<tr><td style="text-align:left;padding:17px !important;">'+ index+' </td><td '+((items[index][1] > 0 )? 'class="inres-greenbg"' : 'class="inres-whitebg"' )+' style="text-align:center">'+ items[index][1]+' </td><td  '+((items[index][2] > 0 )? 'class="inres-yellowbg"' : 'class="inres-whitebg"' )+' style="text-align:center">'+  items[index][2]+' </td><td '+((items[index][3] > 0 )? 'class="inres-redbg"' : 'class="inres-whitebg"' )+' style="text-align:center">'+  items[index][3] +' </td></tr>'
                  //  datatable+='<tr><td style="text-align:left;padding:17px !important;">'+ index+' </td><td class="'+(items[index][1] > 0)? 'inres-greenbg': 'inres-whitebg' +'" style="text-align:center">'+ items[index][1]+' </td><td class="inres-yellowbg" style="text-align:center">'+  items[index][2]+' </td><td class="inres-redbg" style="text-align:center">'+  items[index][3] +' </td></tr>'

                    }
                }

                if(count == 0){
                    datatable+=`<tr><td colspan="4" style="text-align:center">No record </td></tr>`
                }
                datatable+= `</table>`

                content = datatable;
            }
            //..process
            return content;
        }

        function bindContent(el) {
            wc = $('body').find(`.${payload.widgetInfo.dataTargetId}`);
            wc.find('.dasboard-card-body').html(el);
        }

        function refreshWithFilter() {
            widgets.refresh(payload.widgetTag, filters);
        }

        function afterBind() {
            wc.find(`.w-irc-priorities`).on('change', function() {
                filters[priorityFilterKey] = $(this).val();
                refreshWithFilter();
            });
        }

        applyHeaderFilter();
        //Bind contents
        bindContent(generateContent());

        //Execute after content is added to dom
        afterBind();
    });
</script>
