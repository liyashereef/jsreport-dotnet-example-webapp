<!--Start- Filter section --->

        <div class="col-sm-12 col-md-6">
            {{ Form::open(array('id'=>'facility-form', 'class'=>'form-horizontal', 'method'=> 'POST')) }}

                    <div id="facility_id" class="form-group row col-sm-12">
                        <label for="facility_id" class="col-sm-5">Please select facility <span class="mandatory"> *</span></label>
                        <div class="col-sm-7">
                            <select id="facilityId" name="facility_id" class="form-control select2 filterInputs" >
                                <option value="">Please Select</option>
                                @foreach($allocatedFacilities as $facility)
                                <option value="{{$facility->id}}"  data-serviceAvaliable="{{$facility->single_service_facility}}">{{$facility->facility}}</option>
                                @endforeach
                            </select>
                            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                        </div>
                    </div>

                    <div id="facility_service_id" class="form-group row col-sm-12 section-display">
                        <label for="facility_service_id" class="col-sm-5">Please select service <span class="mandatory"> *</span></label>
                        <div class="col-sm-7">
                            <select id="facilityServiceId" name="facility_service_id"  class="form-control select2 filterInputs" >
                                <option value="">Please Select</option>
                            </select>
                            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                        </div>
                    </div>

                    <div id="booking_start_date" class="form-group row col-sm-12" >
                        <label for="booking_start_date" class="col-sm-5">Schedule start date <span class="mandatory"> *</span></label>
                        <div class="col-sm-7">
                            {{ Form::text('booking_start_date', null, array('class'=>'form-control datepicker filterInputs','placeholder'=>'Start Date', 'id'=>'facility_booking_start_date')) }}
                            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                        </div>
                    </div>

                    <div id="booking_end_date" class="form-group row col-sm-12" >
                        <label for="booking_end_date" class="col-sm-5">Schedule end date <span class="mandatory"> *</span></label>
                        <div class="col-sm-7">
                            {{ Form::text('booking_end_date', null, array('class'=>'form-control datepicker filterInputs', 'placeholder'=>'End Date', 'id'=>'facility_booking_end_date')) }}
                            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                        </div>
                    </div>

                    <div class="form-group row col-sm-12" style="text-align:center">
                        <div class="col-sm-12">
                            {{ Form::submit('Search', array('class'=>'button btn btn-primary blue pull-right'))}}
                        </div>
                    </div>

            {{ Form::close() }}
        </div>

<!--End- Filter section --->
