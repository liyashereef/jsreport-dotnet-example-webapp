<style>
    .widget-shift-journal-summary span.selection {
        width: 100% !important;
    }
   .summary-header {
        background: #343F4E;
        color: #fff;
        text-align:center;
        line-height:2;
        width:17% !important;
        padding:7px !important;
    }

    .post-order-header {
        background: #343F4E;
        text-align:left;
        line-height:2;
        width:7% !important;
        padding:10px !important;
        color:#fff;
    }

    .widget-shift-journal-summary span.filter-content {
        width: 25% !important;
        float: right;
    }

    .widget-shift-journal-summary span.pl-2 {
        width: 75% !important;
    }

</style>
<!-- Demo Widget -->
<script>
    //Use function name as camalCase version of WidgetTag
    //Always use let instead of var
    widgets.define('widgetShiftJournalSummary',function(payload) {
        let wc; //widget container
        let filters = payload.filters;
        const employeeFilterKey = 'employee-id';

        function applyHeaderFilter() {
            //select box
            let options = `<option>No employee found</option>`;
            if ((payload.data.users != null) && (payload.data.users != undefined)) {
                let employees = payload.data.users;
                //Generate options
                options = `<option value="">All Employees</option>`;
                $.each(employees, function(index, employeeObject) {
                    let employeeId = employeeObject.id;
                    let employeeName = employeeObject.name_with_emp_no;
                    let selected = isFilterSelected(employeeFilterKey, employeeId) ? 'selected' : '';
                    options += `<option ${selected} value="${employeeId}">${employeeName}</option>`;
                });
            }
            let output = `<select class="w-journal-employee-id form-control">${options}</select>`;

            //Replace filter content with gen html
            $('body').find(`.${payload.widgetInfo.dataTargetId} .filter-content`).html(output);
            $('.w-journal-employee-id').select2();
        }
        function isFilterSelected(key, value) {
            let fv = filters[employeeFilterKey];
            return fv == value;
        }

        function generateContent() {
            let content = '';
            let header = '';
            let count ='';
            let data = payload.data;
                let datatable = '<table class="table table-bordered irc-tbl tbl-line-height-1">';
                let rowdata =  data["post_orders"];
                 let days = data["post_orders"]["days"];
                 let title = data["post_orders"]["title"];
                 let orders = data["post_orders"]["orders"];

                 header += '<tr><td style="background: #f23c22;text-align:left;line-height:2;width:100% !important;padding:10px !important;color:#fff;" ><strong>Post Orders</strong></td>';
                  for (let nkey in days) {
                      header += '<td class="summary-header"><strong>' + days[nkey] + '</strong></td>';
                  }
                  header += '</tr>';
                  datatable += header;

                if (orders != null){
                  datatable += `<tr>`
                   for (let key in title) {
                       datatable += '<td class="post-order-header"><strong>' + title[key] + '</strong></td>'
                       for (let nkey in days) {
                           if ((orders[nkey] != undefined) && (orders[nkey][key] != undefined)) {
                               count = orders[nkey][key];
                               datatable += '<td class="" style="text-align:center;background:#EBE5A2;color:black;"><strong>'+count+'%</strong></td>'
                           } else {
                               count = '';
                               datatable += '<td style="text-align:center;"><strong>'+count+'</strong></td>'
                           }
                       }
                       datatable += `</tr><tr>`
                   }

                }else{
                    datatable += '<tr><td colspan="16" style="text-align:center;"><strong>No records found</strong></td>'
                }

            datatable += `</tr>`
            datatable += `</table>`
            content = datatable;
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
            wc.find(`.w-journal-employee-id`).on('change', function() {
                filters[employeeFilterKey] = $(this).val();
                refreshWithFilter();
            });
        }

        //Bind contents
        applyHeaderFilter();
        bindContent(generateContent());

        //Execute after content is added to dom
        afterBind();
    });
</script>
