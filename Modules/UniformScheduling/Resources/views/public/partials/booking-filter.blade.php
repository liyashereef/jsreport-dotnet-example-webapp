<!--Start- Filter section --->
        <div class="row">
            <div class="col-sm-12 col-md-6">
                {{ Form::open(array('id'=>'uniformscheduling-form', 'class'=>'form-horizontal', 'method'=> 'POST')) }}

                        <div id="booked_date" class="form-group row col-sm-12" >
                            <label for="booked_date" class="col-sm-5">Schedule Date <span class="mandatory"> *</span></label>
                            <div class="col-sm-7">
                                {{ Form::text('booked_date', null, array('class'=>'form-control datepicker filterInputs', 'id'=>'uniform_booked_date')) }}
                                <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                            </div>
                        </div>

                        <div class="form-group row col-sm-12" style="text-align:center">
                            <div class="col-sm-12">
                                {{ Form::submit('Search', array('class'=>'button btn btn-primary blue pull-right'))}}
                            </div>
                        </div>

                {{ Form::close() }}

                 <!--Start- legend - -->
                <div class="row legend-main-box" id="legend-main-box" >
                    <div class="col-sm-12 col-md-2">
                        <strong> Legend </strong>
                    </div>
                    <div class="col-sm-4">
                        <span class="row">
                            <div class="legend-box closed-slot" style="background-color: #e0e0e0 !important;"></div>
                            <span class="legend-text col-sm-7 col-md-9 pull-right"> Closed & Booked Slots </span>
                        </span>
                    </div>
                    <div class="col-sm-5">
                        <span class="row">
                            <div class="legend-box open-slot" style="background-color:#003A63 !important "></div>
                            <span class="legend-text col-sm-7 col-md-10 pull-right"> Available Appointment Slots</span>
                        </span>
                    </div>
                </div>
                <!--End- legend - -->
            </div>


            <div class="col-sm-12 col-md-6 ids-office-details-container ids-office-details-box" style="">
                <div class="ids-office-details-container" id="office-detais" style="">
                    <div class="row col-sm-12">
                            <div id="office-name" class="col-sm-12 title">{{$office->name}}</div>
                            <div class="col-sm-8">
                                <div id="office-address" class="office-address">{{$office->adress}}</div>
                            </div>
                            <div class="col-sm-4">
                                <input class="button btn add-new pull-right" id="mapView" type="button" onclick="getOfficeMap()" value="Click Here To View Map" style="">
                                <input type="hidden" id="lat" value="{{$office->latitude}}">
                                <input type="hidden" id="lng" value="{{$office->longitude}}">
                            </div>
                    </div>
                        <div class="row col-sm-12">
                            <div class="col-sm-4">
                                <div id="office-opening-hours" class="title"> Office Opening Hours </div>
                                        <p style="margin-bottom: 2px;font-size: 15px;">Monday
                                            <span class="start-end">
                                            ({{Carbon::parse($office->office_start_time)->format("h:i A")}}
                                            to
                                            {{Carbon::parse($office->office_end_time)->format("h:i A")}})
                                           </span>
                                       </p>
                                        <p style="margin-bottom: 2px;font-size: 15px;">Tuesday
                                            <span class="start-end">
                                                ({{Carbon::parse($office->office_start_time)->format("h:i A")}}
                                                to
                                                {{Carbon::parse($office->office_end_time)->format("h:i A")}})
                                               </span>
                                        </p>
                                        <p style="margin-bottom: 2px;font-size: 15px;">Wednesday
                                            <span class="start-end">
                                                ({{Carbon::parse($office->office_start_time)->format("h:i A")}}
                                                to
                                                {{Carbon::parse($office->office_end_time)->format("h:i A")}})
                                               </span>
                                        </p>
                                        <p style="margin-bottom: 2px;font-size: 15px;">Thursday
                                            <span class="start-end">
                                                ({{Carbon::parse($office->office_start_time)->format("h:i A")}}
                                                to
                                                {{Carbon::parse($office->office_end_time)->format("h:i A")}})
                                               </span>
                                        </p>
                                        <p style="margin-bottom: 2px;font-size: 15px;">Friday
                                            <span class="start-end">
                                                ({{Carbon::parse($office->office_start_time)->format("h:i A")}}
                                                to
                                                {{Carbon::parse($office->office_end_time)->format("h:i A")}})
                                               </span>
                                        </p>
                                </div>
                            <div class="col-sm-8">
                                <div id="office-special-instruction" class="title"> Office Instructions</div>
                                <div id="special-instruction">{{$office->special_instructions}}</div>
                            </div>
                            <p class="col-sm-12 common-note"> ** Please note - we are not taking walk-in clients at this time to mitigate COVID 19 exposure ** </p>
                        </div>
                    </div>
                </div>

        </div>
<!--End- Filter section --->

 <!-- Map Modal Start-->
 <div id="mapModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mapModalTitle">Office Location Location</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <div class="modal-body" style="padding: 10px 10px;">
                    <div id="MapContainer" style="height: 500px; "></div>

            </div>

        </div>
    </div>
</div>
<!-- Map Modal End-->


