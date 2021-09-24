<style>
    .client_show_up_section .form-group{
        margin: 1% !important;
    }
    .total-fee-paid{
        background: rgb(36 169 66 / 51%) !important;
        color: #000000c7 !important;
    }
    .balance-fee{
        background: #ff000073 !important;
        color: #000000c7 !important;
    }
    .deferred-entry-note{
        color: red;
    }
    /* .mask-section .col-sm-6 {
        margin-left: 8px !important;
    } */
</style>
    <!--Start-- IDS  rescheduling request Form -->
        <div class="modal fade" id="toBeRescheduleModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    {{ Form::open(array('url'=>'#','id'=>'ids-to-be-rescheduling-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                    <br>
                    {{ Form::hidden('office_id', null) }}
                    <div class="modal-body">

                        <div class="form-group row" id="schedule_date">
                            <label for="schedule_date" class="col-sm-3 control-label"> Date</label>
                            <div class="col-sm-6">
                                {{ Form::text('schedule_date',null,array('placeholder'=>'Reschedule  Date','class'=>'form-control datepicker', 'id'=>'tobe_reschedule_date')) }}
                                <small class="help-block"></small>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        {{ Form::submit('Submit', array('class'=>'button btn btn-primary blue','id'=>'to_be_rescheduling_mdl_save'))}}
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    <!--End-- IDS  rescheduling request Form -->


    <!-- Slot Rescheduling Form - Start -->
        <div class="modal fade" id="rescheduleModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
            <div class="modal-dialog" >
                <div class="modal-content" style="width: 60rem !important;right: 40px!important;">
                    <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    {{ Form::open(array('url'=>'#','id'=>'ids-slot-rescheduling-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                    {{ Form::hidden('id', null) }}
                    {{ Form::hidden('refundStatuVal', null) }}

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
                                {{ Form::text('first_name',null,array('placeholder'=>'First Name','class'=>'form-control', 'id'=>'ids_first_name')) }}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group row" id="last_name">
                            <label for="last_name" class="col-sm-3 control-label">Last Name</label>
                            <div class="col-sm-9">
                                {{ Form::text('last_name',null,array('placeholder'=>'Last Name','class'=>'form-control', 'id'=>'ids_last_name')) }}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group row" id="email">
                            <label for="email" class="col-sm-3 control-label">Email</label>
                            <div class="col-sm-9">
                                {{ Form::text('email',null,array('placeholder'=>'Email','class'=>'form-control', 'id'=>'ids_email')) }}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group row" id="phone_number">
                            <label for="phone_number" class="col-sm-3 control-label">Phone</label>
                            <div class="col-sm-9">
                                {{ Form::text('phone_number',null,array('placeholder'=>'Phone [ format (XXX)XXX-XXXX ]','class'=>'form-control phone','id'=>'ids_phone_number')) }}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group row" id="ids_service_id">
                            <label for="ids_service_id" class="col-sm-3 control-label">Service</label>
                            <div class="col-sm-9">
                                {{ Form::select('ids_service_id',[''=>'Please Select'], old('ids_service_id'),array('class'=> 'form-control', 'id'=>'idsServiceId')) }}
                                <small class="help-block"></small>
                            </div>
                        </div>

                        <div class="form-group row" id="passport_photo_service_id">
                            <label for="passport_photo_service_id" class="col-sm-3 control-label"> Passport Photo Set <span class="mandatory">*</span></label>
                            <div class="col-sm-9">
                                {{ Form::select('passport_photo_service_id',[''=>'Select or remove photo service']+$photoServices, old('passport_photo_service_id'),array('class'=> 'form-control', 'id'=>'passportPhotoServiceId')) }}
                                <small class="help-block"></small>
                                <small id="passportPhotoServiceMessage" class="help-block"></small>
                            </div>
                        </div>

                        <div class="form-group row" id="no-show-penalty">
                            <label for="no-show-penalty" class="col-sm-3 control-label">No Show Penalty</label>
                            <div class="col-sm-9">
                                <span class='view-form-element' id="noShowPenalty" style="color: #f90000;"></span>
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

                        <div class="online-payment-section form-group row">
                            <label class="col-sm-3">Balance Fee <span id="balanceFeeSpan">>  </span></label>
                            <div class="col-sm-9">
                                {{ Form::hidden('balance_fee',null,array('placeholder'=>'Balance Fee','class'=>'form-control view-form-element', 'id'=>'balanceFeeVal')) }}
                                <span class='view-form-element balance-fee' id="balanceFee"></span>
                            </div>
                        </div>

                        <div class="form-group row" id="office_change">
                            <label for="office_change" class="col-sm-3 control-label">Reschedule to another office?</label>
                            <div class="col-sm-9">
                                <label> <input type="radio" class="is_office_change" name="is_office_change" id="office_change_yes"  value="1" >&nbsp;Yes&nbsp;&nbsp;</label>
                                <label> <input type="radio" class="is_office_change" name="is_office_change" id="office_change_no"  value="0" >&nbsp;No&nbsp;&nbsp;</label>
                            <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group row" id="ids_office_id">
                            <label for="ids_office_id" class="col-sm-3">Office</label>
                            <div class="col-sm-9">
                            <select id="idsOfficeId" name="ids_office_id" required="TRUE" class="form-control" disabled="true">
                                <option value="">Please Select</option>
                                @foreach($officeList as $office)
                                    <option value="{{$office->id}}" data-isPhotoService="{{$office->is_photo_service}}">
                                        {{$office->name}} - {{$office->adress}}
                                    </option>
                                @endforeach
                            </select>
                            <small class="help-block"></small>
                            </div>
                        </div>

                        <div class="form-group row" id="slot_booked_date">
                            <label for="slot_booked_date" class="col-sm-3 control-label">Reschedule Date</label>
                            <div class="col-sm-9">
                                {{ Form::text('slot_booked_date',null,array('placeholder'=>'Reschedule Date','class'=>'form-control datepicker', 'id'=>'ids_reschedule_date')) }}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group row" id="ids_office_slot_id">
                            <label for="ids_office_slot_id" class="col-sm-3 control-label">Time Slot</label>
                            <div class="col-sm-9">
                                {{ Form::select('ids_office_slot_id',[''=>'Please Select'], old('time_slot'),array('class'=> 'form-control', 'id'=>'time_slot')) }}
                                <small class="help-block"></small>
                            </div>
                        </div>

                        <div class="client_show_up_section"  style="border: 0.5px solid;background: #f3642438;">

                            <div class="form-group row" id="is_client_show_up">
                                <label for="is_client_show_up" class="col-sm-3 control-label" style="padding-right: 0px;"> Did the client show up? </label>
                                <div class="col-sm-9">
                                    <label> <input type="radio" class="is_client_show_up" name="is_client_show_up" id="client_show_up_yes"  value="1" >&nbsp;Yes&nbsp;&nbsp;</label>
                                    <label> <input type="radio" class="is_client_show_up" name="is_client_show_up" id="client_show_up_no"  value="0" >&nbsp;No&nbsp;&nbsp;</label>
                                <small class="help-block"></small>
                                </div>
                            </div>

                            <div class="client_show_up_yes_section" >
                                <div class="balance_fee_section">
                                    <div class="form-group row" id="refund_status">
                                        <label for="refund_status" class="col-sm-3 control-label">Request for refund?</label>
                                        <div class="col-sm-9">
                                            <label> <input type="radio" class="refund_status" name="refund_status" id="refund_status_yes"  value="1" >&nbsp;Yes&nbsp;&nbsp;</label>
                                            <label> <input type="radio" class="refund_status" name="refund_status" id="refund_status_no"  value="0" >&nbsp;No&nbsp;&nbsp;</label>
                                            <span class="text-danger align-middle font-12" id="ifRefundStatusYes">
                                                An email will be sent to the finance department
                                                automatically requesting the refund.
                                            </span>
                                            <br/>
                                            <small class="help-block"></small>
                                        </div>
                                    </div>

                                    <div class="form-group row" id="ids_refund_note">
                                        <label for="ids_refund_note" class="col-sm-3 control-label"> Refund Note</label>
                                        <div class="col-sm-9">
                                            {{ Form::textArea('ids_refund_note', null, array('class'=>'form-control', 'placeholder'=>'Refund Notes','rows'=>5,'id'=>'idsRefundNote')) }}
                                            <small class="help-block"></small>
                                        </div>
                                    </div>

                                </div>

                                <div class="payment_received_section">
                                    <div class="form-group row" id="is_payment_received">
                                        <label for="is_payment_received" class="col-sm-3 control-label"> Payment Received </label>
                                        <div class="col-sm-9">
                                            <input type="radio" class="is_payment_received" name="is_payment_received" id="payment_received_yes"  value="1" >&nbsp;Yes&nbsp;&nbsp;
                                            <input type="radio" class="is_payment_received" name="is_payment_received" id="payment_received_no"  value="0" >&nbsp;No&nbsp;&nbsp;
                                            <small class="help-block"></small>
                                        </div>
                                    </div>

                                    <div class="form-group row" id="ids_payment_method_id" >
                                        <label for="ids_payment_method_id" class="col-sm-3 control-label">Payment Type</label>
                                        <div class="col-sm-9">
                                            {{ Form::select('ids_payment_method_id',[''=>'Please Select'] + $paymentMethods, old('ids_payment_method_id'),array('class'=> 'form-control', 'id'=>'idsPaymentMethodId')) }}
                                            <small class="help-block"></small>
                                        </div>
                                    </div>
                                </div>

                                <div class="payment_not_received_section">
                                    <div class="form-group row " id="ids_payment_reason_id" >
                                        <label for="ids_payment_reason_id" class="col-sm-3 control-label"> Reason</label>
                                        <div class="col-sm-9">
                                            {{ Form::select('ids_payment_reason_id',[''=>'Please Select'] + $paymentReasons, old('ids_payment_reason_id'),array('class'=> 'form-control', 'id'=>'idsPaymentReasonId')) }}
                                            <small class="help-block"></small>
                                        </div>
                                    </div>

                                    <div class="form-group row" id="payment_reason" >
                                        <label for="payment_reason" class="col-sm-3 control-label">Other Reason</label>
                                        <div class="col-sm-9">
                                            {{ Form::textArea('payment_reason', null, array('class'=>'form-control', 'placeholder'=>'Other Payment Note','rows'=>3,'id'=>'paymentReason')) }}
                                            <small class="help-block"></small>
                                        </div>
                                    </div>
                                </div>

                                {{-- <div class="col-sm-12 row" style="margin-left: -6px !important">
                                    <div class="form-group row col-sm-7 mask-section" id="is_mask_given">
                                        <label for="is_mask_given" class="col-sm-6 control-label">Mask Given</label>
                                        <div class="col-sm-6" style="margin-left: -5px;">
                                            <label> <input type="radio" class="is_mask_given" name="is_mask_given" id="is_mask_given_yes"  value="1" >&nbsp;Yes&nbsp;&nbsp;</label>
                                            <label> <input type="radio" class="is_mask_given" name="is_mask_given" id="is_mask_given_no"  value="0" >&nbsp;No&nbsp;&nbsp;</label>
                                            <br>
                                            <small class="help-block"></small>
                                        </div>
                                    </div>
                                    <div class="form-group row col-sm-5 mask-section mask-number-section " id="no_masks_given">
                                        <div class="row">
                                            <label for="no_masks_given" class="col-sm-6 control-label">Number of mask(s)</label>
                                            <div class="col-sm-6">
                                                {{ Form::number('no_masks_given',null,array('placeholder'=>'No of mask(s)','class'=>'form-control', 'id'=>'noMasksGiven')) }}
                                            </div>
                                        </div>
                                        <small class="help-block"></small>
                                    </div>
                                </div> --}}

                                <div class="col-sm-12 row">

                                    <div class="form-group row col-sm-6 mask-section" id="is_mask_given">
                                        <label for="is_mask_given" class="col-sm-6 control-label">Mask Given</label>
                                        <div class="col-sm-5" style="margin-left: 11px;">
                                            <label> <input type="radio" class="is_mask_given" name="is_mask_given" id="is_mask_given_yes"  value="1" >&nbsp;Yes&nbsp;&nbsp;</label>
                                            <label> <input type="radio" class="is_mask_given" name="is_mask_given" id="is_mask_given_no"  value="0" >&nbsp;No&nbsp;&nbsp;</label>
                                            <br>
                                            <small class="help-block"></small>
                                        </div>
                                    </div>

                                    <div class="form-group row col-sm-5 mask-section mask-number-section " id="no_masks_given">
                                        <div class="row">
                                            <label for="no_masks_given" class="col-sm-7 control-label">Number of mask(s)</label>
                                            <div class="col-sm-5">
                                                {{ Form::number('no_masks_given',null,array('placeholder'=>'No of mask(s)','class'=>'form-control', 'id'=>'noMasksGiven')) }}
                                            </div>
                                        </div>
                                        <small class="help-block"></small>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="custom-questions" style="border: 0.5px solid;background: #f3642438; margin-top: 1%;">

                            <div class="form-group row" id="is_candidate" style="width: 98% !important; margin: 1%;">
                                <label for="is_candidate" class="col-sm-12 control-label">
                                    Are you currently a full-time employee at Commissionaires Great Lakes ?
                                </label>
                                <div class="col-sm-12">
                                    <select id="isCandidate" name="is_candidate" required="TRUE" class="form-control" >
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                    <input placeholder="Please Eenter Requisition Number (to be provided by recruiter)" class="form-control" id="candidate_requisition_no" name="candidate_requisition_no" type="text" >
                                <small class="help-block"></small>
                                </div>
                            </div>

                            <div class="form-group row" id="is_federal_billing" style="width: 98% !important; margin: 1%;">
                                <label for="is_federal_billing" class="col-sm-12 control-label">
                                    Do you have a letter from your employer for deferred billing?
                                </label>
                                <p class="col-sm-12 text-danger align-middle font-12" id="">(If you selected Yes, the letter must be presented at time of service)</p>
                                <div class="col-sm-12">
                                    <select id="isFederalBilling" name="is_federal_billing" required="TRUE" class="form-control" >
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                    <input placeholder="Please enter your employee number" class="form-control" id="federal_billing_employer" name="federal_billing_employer" type="text" >
                                <small class="help-block"></small>
                                </div>
                            </div>
                        </div>

                        <div id="questionAnswers"> </div>

                        <div class="form-group row" id="notes">
                            <label for="notes" class="col-sm-12 control-label">Note</label>
                            <div class="col-sm-12">
                            {{ Form::textArea('notes', null, array('class'=>'form-control', 'placeholder'=>'Notes','rows'=>5,'id'=>'ids_notes')) }}
                            <small class="help-block"></small>
                            </div>
                        </div>

                        <div id="refundDetails" style="margin-left: -24px;height: 100px; overflow-y: scroll;"> </div>
                        <div id="deferredEntryNote" class="deferred-entry-note"></div>
                    </div>
                    <div class="modal-footer " id="rescheduleModalFooter">
                        <div id="delete-cancel-section">
                            @can('ids_booking_delete')
                                {{ Form::button('Delete',array('class'=>'btn btn-primary orange', 'id'=>'delete_slot'))}}
                            @endcan

                            @can('ids_booking_cancel')
                                {{ Form::button('Cancel Appointment', array('class'=>'button btn btn-primary orange','id'=>'cancel_booking'))}}
                            @endcan
                        </div>
                        @can('ids_reschedule_appointment')
                            {{ Form::submit('Update', array('class'=>'button btn btn-primary blue','id'=>'rescheduling_mdl_save'))}}
                        @endcan
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    <!-- Slot Rescheduling Form - End -->

    <!--Start-- IDS rescheduling cancel request confirm form -->
        <div class="modal fade" id="cancelOrDeleteConfirmModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="myModalLabel">Cancel/Delete Confirm</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    {{ Form::open(array('url'=>'#','id'=>'cancel-delete-confirm-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                        <br>
                        <div class="modal-body">

                            <div class="form-group row">
                                <label  class="col-sm-3">Total Fee</label>
                                <div class="col-sm-9">
                                    <span class='view-form-element' id="total-fee"></span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label  class="col-sm-3">Total Fee Paid</label>
                                <div class="col-sm-9">
                                    <span class='view-form-element' id="total-fee-paid"></span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label  class="col-sm-3">Refund Fee</label>
                                <div class="col-sm-9">
                                    <span class='view-form-element' id="refund-fee"></span>
                                </div>
                            </div>

                            <div class="form-group row" id="refund_status">
                                <label for="refund_status" class="col-sm-3 control-label">Request for refund? <span class="mandatory">*</span></label>
                                <div class="col-sm-9">
                                    <label> <input type="radio" class="refund_status" name="refund_status" id="refund_status_yes"  value="1" >&nbsp;Yes&nbsp;&nbsp;</label>
                                    <label> <input type="radio" class="refund_status" name="refund_status" id="refund_status_no"  value="0" >&nbsp;No&nbsp;&nbsp;</label>
                                    <span class="text-danger align-middle font-12" id="ifRefundStatusYes">
                                        <br/> An email will be sent to the finance department automatically requesting the refund.
                                    </span>
                                    <small class="help-block"></small>
                                </div>
                            </div>

                            <div class="form-group row" id="refund_note">
                                <label for="refund_note" class="col-sm-3 control-label"> Refund Note</label>
                                <div class="col-sm-9">
                                    {{ Form::textArea('refund_note', null, array('class'=>'form-control', 'placeholder'=>'Refund Notes','rows'=>5,'id'=>'refundNote')) }}
                                    <small class="help-block"></small>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            {{ Form::hidden('cancel_delete',null, array('class'=>'','id'=>'cancelOrDelete'))}}
                            {{ Form::submit('Submit', array('class'=>'button btn btn-primary blue','id'=>''))}}
                            {{ Form::button('Cancel', array('class'=>'button btn btn-primary orange','data-dismiss'=>'modal','aria-label'=>'Close'))}}
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    <!--End-- IDS rescheduling cancel request confirm form -->
