   <!-- Slot Rescheduling Form - Start -->
   <div class="modal fade" id="entryDetailsModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
            <div class="modal-dialog" >
                <div class="modal-content" style="width: 60rem !important;right: 40px!important;">
                    <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    {{ Form::open(array('url'=>'#','id'=>'ids-slot-rescheduling-form','class'=>'form-horizontal', 'method'=> 'POST')) }}

                    <br>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label  class="col-sm-3">Scheduled Date</label>
                            <div class="col-sm-9">
                            <span class='view-form-element' id="pre-scheduled-date"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3">Scheduled Time</label>
                            <div class="col-sm-9">
                                <span class='view-form-element'  id="pre-scheduled-time"></span>
                            </div>
                        </div>
                        <div class="form-group row" id="first_name">
                            <label for="first_name" class="col-sm-3">First Name</label>
                            <div class="col-sm-9">
                                <span class='view-form-element'  id="firstName"></span>
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group row" id="last_name">
                            <label for="last_name" class="col-sm-3 control-label">Last Name</label>
                            <div class="col-sm-9">
                                <span class='view-form-element'  id="lastName"></span>
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group row" id="emailArea">
                            <label for="emailArea" class="col-sm-3 control-label">Email</label>
                            <div class="col-sm-9">
                                <span class='view-form-element'  id="email"></span>
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group row" id="phone_number">
                            <label for="phone_number" class="col-sm-3 control-label">Phone</label>
                            <div class="col-sm-9">
                                <span class='view-form-element'  id="phoneNumber"></span>
                                <small class="help-block"></small>
                            </div>
                        </div>

                        <div class="form-group row" id="ids_office_id">
                            <label for="ids_office_id" class="col-sm-3">Office</label>
                            <div class="col-sm-9">
                                <span class='view-form-element' id="officeName"></span>
                                <small class="help-block"></small>
                            </div>
                        </div>

                        <div class="form-group row" id="ids_service_id">
                            <label for="ids_service_id" class="col-sm-3 control-label">Service</label>
                            <div class="col-sm-9">
                                <span class='view-form-element'  id="serviceName"></span>
                                <small class="help-block"></small>
                            </div>
                        </div>

                        <div class="form-group row" id="passport_photo_service_id">
                            <label for="passport_photo_service_id" class="col-sm-3 control-label"> Passport Photo Set </label>
                            <div class="col-sm-9">
                                <span class='view-form-element' id="photo-service"></span>
                                <small class="help-block"></small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3">Total Fees </label>
                            <div class="col-sm-9">
                                <span class='view-form-element ' id="totalFeePayable"></span>
                            <span class="text-danger align-middle font-12" id="taxIncluded">Tax Included</span>
                            </div>
                        </div>

                        <div class="online-payment-section form-group row">
                            <label class="col-sm-3">Total Fee Paid</label>
                            <div class="col-sm-9">
                                <span class='view-form-element total-fee-paid' id="totalFeePaid"></span>
                            </div>
                        </div>

                        <div class="form-group row" id="refunds">
                            <label for="refunds" class="col-sm-3 control-label">Refund</label>
                            <div class="col-sm-9">
                                <span class='view-form-element' id="refund" ></span>
                                <small class="help-block"></small>
                            </div>
                        </div>

                        <div class="form-group row" id="refunds">
                            <label for="refunds" class="col-sm-3 control-label">Payment Intent </label>
                            <div class="col-sm-9">
                                <span class='view-form-element' id="paymentIntent" ></span>
                                <small class="help-block"></small>
                            </div>
                        </div>

                        <div class="form-group row" id="refunds">
                            <label for="refunds" class="col-sm-3 control-label">Email given to strip</label>
                            <div class="col-sm-9">
                                <span class='view-form-element' id="stripEmail" ></span>
                                <small class="help-block"></small>
                            </div>
                        </div>

                        <div class="form-group row" id="refunds">
                            <label for="refunds" class="col-sm-3 control-label">Payment started</label>
                            <div class="col-sm-9">
                                <span class='view-form-element' id="paymentStarted" ></span>
                                <small class="help-block"></small>
                            </div>
                        </div>

                        <div class="form-group row" id="refunds">
                            <label for="refunds" class="col-sm-3 control-label">Payment ended</label>
                            <div class="col-sm-9">
                                <span class='view-form-element' id="paymentEnded" ></span>
                                <small class="help-block"></small>
                            </div>
                        </div>

                        <div class="form-group row" id="refunds">
                            <label for="refunds" class="col-sm-12 control-label">
                                Are you currently a full-time employee at Commissionaires Great Lakes ?
                            </label>
                            <div class="col-sm-12">
                                <span class='view-form-element' id="isCandidate" ></span>
                                <small class="help-block"></small>
                            </div>
                        </div>

                        <div class="form-group row" id="refunds">
                            <label for="refunds" class="col-sm-12 control-label">
                            Do you have a letter from your employer for deferred billing?
                            </label>
                            <div class="col-sm-12">
                                <span class='view-form-element' id="isFederalBilling" ></span>
                                <small class="help-block"></small>
                            </div>
                        </div>

                        <div id="refundDetails" style="margin-left: -24px;height: 120px; overflow-y: scroll;"> </div>

                    </div>
                    <div class="modal-footer " id="rescheduleModalFooter">

                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    <!-- Slot Rescheduling Form - End -->
