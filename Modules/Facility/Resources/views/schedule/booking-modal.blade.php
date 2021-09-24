 <!--Start-- Facility booking form  -->
 <div class="modal fade" id="bookingConfirmModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <!-- {{ Form::open(array('url'=>'#','id'=>'booking-form','class'=>'form-horizontal', 'method'=> 'POST')) }} -->
                    <br>
                    <!-- {{ Form::hidden('office_id', null) }} -->
                    <div class="modal-body">  
                        <div class="clicked-slot-details">
                            <div class="form-group row" id="facility">
                                <label id="facility_label" class="col-sm-4 control-label">Facility </label>
                                <label id="facility_name" class="col-sm-6 control-label view-form-element"> </label>
                            </div>

                            <div class="form-group row" id="service">
                                <label id="facility_service_label" class="col-sm-4 control-label"> Service</label>
                                <label id="facility_service" class="col-sm-6 control-label view-form-element">  </label>
                            </div>

                            <div class="form-group row" id="date">
                                <label id="date_label" class="col-sm-4 control-label"> Booked Date and Time  </label>
                                <label id="facility_date" class="col-sm-6 control-label view-form-element"> </label>
                            </div>
                            
                        </div> 
                       
                        <div class="clicked-slot-booking-details" id="clicked-slot-booking-details">
                            
                        </div>                 
                       
                    
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">Close </button>
                    </div>
                    <!-- {{ Form::close() }} -->
                </div>
            </div>
        </div>
    <!--End-- Facility booking form -->