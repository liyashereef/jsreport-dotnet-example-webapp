<style>
    .master-slot-container{
        /* margin: 1%; */
        width: auto;
    }
    .day-slot-container{
        background-color: #d5d1d15e;
        border: 1px solid;
    }
    .container-fluid {
        margin-bottom: 5px;
    }
    .day-slot-container .all-container{

    }
    .slot-day-name{
        background-color: #f36905;
        padding: 10px 10px;
        color: #ffffff;
    }
    .slot-day-name span{
        font-weight: bold;
    }
    .slot-day-name p{
        margin-top: 0;
        margin-bottom: 0px;
    }
    .slot-container{
        background-color: #ffffff;
    }
    .slot-container button{
        margin: 12px 5px;
        border: 1px solid;
        border-radius: 10px;
        /* width: 95px; */
        padding: 12px;
        background-color: #003A63 !important;
        color: #fff !important;
        font-weight: 700;
        cursor: pointer;
    }
    .slot-container button:hover{
        border: 1px solid;
        border-radius: 10px;

        background-color: #13486be0 !important;
        color: #fff !important;
    }
    .btn-light.disabled, .btn-light:disabled {
        border: 1px solid;
        background-color: #e0e0e0  !important;
        color: #04040461 !important;
    }
    .btn-light:disabled:hover{
        background-color: #e0e0e0  !important;
        color: #04040461 !important;
    }
    .slot-details{
        border: 1px solid;
        border-radius: 10px;
        margin: 12px;
        padding: 12px;
        text-align:center;
        width: 95px;
        background-color: #ffffff;
        cursor: pointer;
    }
    .today {
        background: #E6E6FA !important;
    }
    .facility-policy-section{
        border: 1px solid #15151561;
        width: 99%;
        margin-left: -2%;
    }
    .policy-section-title{
        color: #f36905;
        font-weight: bold;
        margin-bottom: 1%;
        margin-top: 1%;
    }
    .modal-footer {
        text-align: center;
        display: block !important;
    }
    .slot-not-available{
        height: 50px;
        padding: 13px 1% 1% 1%;
        background-color: #ffffff5e;
        padding-top: 13px;
        padding-left: 25px;
    }

</style>

<div id="scheduling-table-container" class=" master-slot-container" style="">
    @if(sizeof($result['daySlotDetails']))
        @foreach ($result['daySlotDetails'] as $item)
            <div class="day-slot-container container-fluid ">
                <div class="all-container">
                    <div class="row slot-day-name">
                        <span class="col-sm-12"> {{$item['format_date']}} </span>
                        <p class="col-sm-12">{{$item['intervel_text']}}</p>
                    </div>
                    <div class="row slot-container">
                        @foreach ($item['slots'] as $key=>$slots)


                            @if($slots['status'] == 1)
                                <button type="button" title="Open Slot"  class="btn btn-light slot-card open-slot"
                                        data-bookingDate="{{$item['date']}}"  data-officeSlotId="{{$slots['office_slot_id']}}">
                                    {{$slots['title']}}
                                </button>
                            @else
                                <button type="button"
                                        @if($slots['status'] == 0)
                                        title="Temporarily Closed"
                                        @elseif($slots['status'] == 2)
                                        title="Scheduled"
                                        @else
                                        title="Unavailable"
                                        @endif
                                        class="btn btn-light slot-card unavailable-slots" disabled >{{$slots['title']}}
                                </button>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="day-slot-container container-fluid slot-not-available">
            <div class="all-container">
                <div class="row slot-container">
                    Slot not available.
                </div>
            </div>
        </div>
    @endif
</div>
