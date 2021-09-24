
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <form id="saveupdateservice" action='{{route("cbs.updatefacilityservice")}}' method="post">
                @csrf
                <input type="hidden" name="facilityid"  id="facilityid" value="{{$facilityid}}" />

                <div class="row" style="margin-top: 20px;margin-bottom: 20px">
                    <div class="col-md-11 " style="font-size: 13px;padding-left:12px"><h5>Edit Facility Service </h5></div>
                    <div class="col-md-1 closebutton" style="text-align: right;cursor:pointer">X</div>

                </div>
                <div class="row" style="margin-top: 20px;margin-bottom: 20px">
                    <div class="col-md-4">
                        Facility
                    </div>
                    <div class="col-md-8" style="text-align: left" id="facility">
                    <input type="text" name="facility" class="form-control" value="{{$data->service}}" />
                        <label for="facility"></label>
                    </div>


                </div>

                <div class="row" style="margin-top: 20px;margin-bottom: 20px">
                    <div class="col-md-4">
                            Description
                    </div>
                    <div class="col-md-8" style="text-align: left" id="description">
                        <textarea name="description" class="form-control" rows="5"  >{{$data->description}}</textarea><label for="description"></label>
                    </div>

                </div>
                <div class="row" style="margin-top: 20px;margin-bottom: 20px;display:none">
                    <div class="col-md-4">
                        Maximum Booking Per Day (Hours)
                    </div>
                    <div class="col-md-8" style="text-align: left" id="maxbooking_perday">
                        <input type="text" class="form-control" name="maxbooking_perday" value="{{$facilitydata->maxbooking_perday}}" /><label for="maxbooking_perday"></label>
                    </div>

                </div>
                <div class="row" style="margin-top: 20px;margin-bottom: 20px">

                    <div class="col-md-4">
                        Booking Interval&nbsp;&nbsp;<i data-container="body" class="fa fa-question-circle" data-content="Interval between slot in hours" style="cursor:pointer" aria-hidden="true"></i>
                    </div>
                    <div class="col-md-8" style="text-align: left" id="booking_interval">
                        <input type="text" maxlength="5"  class="form-control number" name="booking_interval" value="{{$slot_interval}}" /><label for="booking_interval"></label>
                    </div>
                </div>

                <div class="row" style="margin-top: 20px;margin-bottom: 20px">

                    <div class="col-md-4">
                        Maximum Occupancy Per Slot (Count)&nbsp;&nbsp;<i data-container="body" class="fa fa-question-circle" data-content="How many people allowed during an interval" style="cursor:pointer" aria-hidden="true"></i>
                    </div>
                    <div class="col-md-8" style="text-align: left">
                        <input type="text" maxlength="3" class="form-control number notdecimal" value="{{$facilitydata->tolerance_perslot}}" name="tolerance_perslot" />    <label for="tolerance_perslot"></label>
                    </div>

                </div>
                <div class="row" style="margin-top: 20px;margin-bottom: 20px;display:none">


                    <div class="col-md-4">
                        Weekend Booking
                    </div>
                    <div class="col-md-8" style="text-align: left" id="weekend_booking">
                        <select class="form-control " id="weekendbooking"  name="weekend_booking" >
                            <option value="">Select Any</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>

                        </select>

                        <label for="weekend_booking"></label>
                    </div>
                </div>
                <div class="row" style="margin-top: 20px;margin-bottom: 20px;display:none">


                    <div class="col-md-4">
                        Restrict Booking
                    </div>
                    <div class="col-md-8" style="text-align: left" id="restrict_booking" required>
                        <select class="form-control " id="restrict_booking"  name="restrict_booking" >
                            <option value="">Select Any</option>
                            <option value="1" @if($data->restrict_booking==1) selected @endif>Yes</option>
                            <option value="0"  @if($data->restrict_booking==0) selected @endif>No</option>

                        </select>

                        <label for="restrict_booking"></label>
                    </div>
                </div>
                <div class="row" style="margin-top: 20px;margin-bottom: 20px">


                    <div class="col-md-4">
                        Active
                    </div>
                    <div class="col-md-8" style="text-align: left" id="active" required>
                        <select class="form-control " id="active"  name="active" >
                            <option value="">Select Any</option>
                            <option value="1" @if($data->active==1) selected @endif>Yes</option>
                            <option value="0" @if($data->active==0) selected @endif>No</option>

                        </select>

                        <label for="active"></label>
                    </div>
                </div>

                <div class="row" style="margin-top: 30px;margin-bottom: 20px">
                    <div class="col-md-5">

                    </div>

                    <div class="col-md-4" style="">
                                 <button class="btn btn-primary saveupdate" attr-mode="save">Save</button>

                                 <button type="button" class="btn btn-primary cancel closebutton">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


