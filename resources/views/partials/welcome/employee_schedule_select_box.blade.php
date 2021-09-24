<div style="float: right;">
<select id="payperiod_element" class="form-control-sm payperiod_element">
            @if(isset($payperiods))
            @foreach($payperiods as $ky => $payperiod)
            <option value='{{$ky}}'
                    @if($currentPayperiodId == $ky)
                    selected
                    @endif
                    >{{$payperiod}}</option>
            @endforeach
            @endif
        </select>
</div>

<style type="text/css">    
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

    .payperiod_select .selection {
        display: initial;
    }

    #table-site-schedule {
        display: block;
        width: 100%;
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
</style>