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
    }
</style>


<input id="schedule_element" type="hidden" value="{{isset($scheduleId)? $scheduleId: ''}}" />
<div id="table_content" class="table_content" style="{{isset($scheduleId)? '': 'overflow-x: hidden;max-width: 100%;'}}">
    <div>&nbsp;</div>
    <table>
        <tr id="emp-schedule-no-data" class="emp-schedule-no-data"><td colspan="2" class="text-center" style="vertical-align:middle;width: 5% !important;">No data found</td></tr>
        <tr id="emp-schedule-with-data" class="emp-schedule-with-data">
            <td valign="top" id="employeeblock" class="employeeblock" style="width:300px">
            </td>
            <td id="scrollarea" style="{{isset($scheduleId)? '': 'overflow-y: scroll !important;'}}">
                <table class="table_1 employee_schedule_tbl" id="employee_schedule_tbl" style="display: none;margin: 0% 2% 1% 0%;">
                    <tbody id="schedule_tbl_header" class="schedule_tbl_header" style="overflow-x:scroll;position: sticky;top: 0;z-index: 1;background: rgba(255, 255, 255, 0.6);"></tbody>
                    <tbody id="schedule_tbl_body" class="schedule_tbl_body" style="overflow-x:hidden;position: sticky;"></tbody>
                </table>
            </td>
        </tr>
    </table>
</div>
