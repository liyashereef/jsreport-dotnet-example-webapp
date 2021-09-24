<style>
    .widget-qr-patrol span.selection {
        width: 100% !important;
    }
    .qr-petrol-widget-icon{
        display: none;
    }
    td.qr-title:hover > .qr-petrol-widget-icon {
        display: inline-block;
    }

    .qr-patrol-blue {
        background: #393f4f;
        color: white;
    }

    .qr-patrol-orange{
        background: #f36424;
        color: white;
    }

    td,th .qr-patrol-tbl {
        white-space: nowrap;
    }

    .qr-patrol-border {
        border: 1px solid white;
    }

    .qr-patrol-tbl tr {
        line-height: 1em;
    }

    .qr-patrol-tbl > tbody > tr {
        border: 1px solid white;
    }

    .qr-patrol-tbl > thead > tr > th {
        border: 1px solid white !important;
    }

    .widget-qr-patrol span.filter-content {
        width: 25% !important;
        float: right;
    }

    .widget-qr-patrol span.pl-2 {
        width: 75% !important;
    }
</style>
<script>
    //Use function name as camalCase version of WidgetTag
    //Always use let instead of var
    widgets.define('widgetQrPatrol',function(payload) {
        let wc; //widget container
        let data = payload.data.qr_details;
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
            let output = `<select class="w-employee-id form-control">${options}</select>`;

            //Replace filter content with gen html
            $('body').find(`.${payload.widgetInfo.dataTargetId} .filter-content`).html(output);
            $('.w-employee-id').select2();
        }

        function isFilterSelected(key, value) {
            let fv = filters[employeeFilterKey];
            return fv == value;
        }

        function generateContent() {
            let dynamicHeaderHtml = '';
            let headerLength = 1;
            let rowDataFound = 0;

            if(data.headerData !== null && data.headerData !== undefined) {
                let formatedDateHeader = data.formatedDateHeader;
                headerLength = (data.headerData.length + 1);
                dynamicHeaderHtml = `<th style="min-width:300px !important;">&nbsp;</th>`;
                let headerObject = data.headerData;
                $.each(headerObject, function(key, column) {
                    dynamicHeaderHtml +=`<th  class="qr-patrol-blue qr-patrol-border">${column + ' ('+formatedDateHeader[column]+')'}</th>`;
                });
            }

            let dynamicBodyHtml = ``;
            if(data.rowData !== null && data.rowData !== undefined) {
                dynamicBodyHtml =`<tr><th class="qr-patrol-orange text-left" colspan="${headerLength}">Checkpoint</th></tr>`;
                $.each(data.rowData, function(ky1, headCol) {
                    rowDataFound++;
                    let checkPointName = '';
                    if(ky1.length > 25) {
                        checkPointName = ky1.substr(0, 22) + `...`;
                    }else {
                        checkPointName = ky1;
                    }
                    dynamicBodyHtml +=`<tr><th class="text-left qr-patrol-border qr-patrol-blue qr-title" style="width:200px;" title="${ky1}">${checkPointName}</th>`;
                    $.each(headCol, function(ky2, col) {
                        dynamicBodyHtml +=`<td class="text-center qr-patrol-border qr-title" style="background:${col.color}" title="Required Scan:&nbsp;${col.required_scan}\nActual Scan:&nbsp;${col.actual_scan}\nCompliance:&nbsp;${col.value}%\nFirst Scan:&nbsp;${col.first_scan_at? col.first_scan_at: ''}\nFirst Scan By:&nbsp;${col.first_scan_by? col.first_scan_by: ''}\nLast Scan:&nbsp;${col.last_scan_at? col.last_scan_at: ''}\nLast Scan By:&nbsp;${col.last_scan_by? col.last_scan_by: ''}"><i class="fa fa-circle qr-petrol-widget-icon" style="color:white;"></i></td>`;
                    });
                    dynamicBodyHtml +=`</tr>`;
                });
            }

            if(rowDataFound == 0) {
                dynamicHeaderHtml = ``;
                dynamicBodyHtml = `<tr><td class="text-center" colspan="${headerLength}">No data found</td></tr>`;
            }

            //..process
            return `<table class="table qr-patrol-tbl"><thead><tr>${dynamicHeaderHtml}</tr></thead><tbody class="qr-patrol-body">${dynamicBodyHtml}</tbody></table>`;
        }

        function bindContent(el) {
            wc = $('body').find(`.${payload.widgetInfo.dataTargetId}`);
            wc.find('.dasboard-card-body').html(el);
        }

        function refreshWithFilter() {
            widgets.refresh(payload.widgetTag, filters);
        }

        function afterBind() {
            wc.find(`.w-employee-id`).on('change', function() {
                filters[employeeFilterKey] = $(this).val();
                refreshWithFilter();
            });
        }

        //Bind contents
        applyHeaderFilter();
        bindContent(generateContent());
        afterBind();
    });
</script>
