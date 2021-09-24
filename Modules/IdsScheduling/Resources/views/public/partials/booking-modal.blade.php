    <!--Start- Slot Scheduling Form --->
    <div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                {{ Form::open(array('url'=>'#','id'=>'ids-slot-scheduling-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                {{ Form::hidden('ids_office_id', null) }}
                {{ Form::hidden('ids_recommend_office_id', null) }}
                {{ Form::hidden('ids_service_id', null) }}
                {{ Form::hidden('ids_office_slot_id', null) }}
                {{ Form::hidden('slot_booked_date', null) }}
                {{ Form::hidden('postal_code', null) }}
                {{-- {{ Form::hidden('cancellation_penalty', 0) }} --}}
                {{ Form::hidden('cancelled_booking_id', null) }}
                {{-- {{ Form::hidden('is_photo_service', null) }} --}}
                {{ Form::hidden('passport_photo_service_id', null) }}

                <br>
                <div class="modal-body">
                    <div class="form-group row" id="first_name">
                        <label for="first_name" class="col-sm-3">Slot Details</label>
                        <div class="col-sm-9">
                            <label id="slotDetails" class="control-label view-form-element"></label>
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group row" id="first_name">
                        <label for="first_name" class="col-sm-3">First Name</label>
                        <div class="col-sm-9">
                            {{ Form::text('first_name',null,array('placeholder'=>'First Name','class'=>'form-control', 'id'=>'ids_first_name', 'required'=>'true')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group row" id="last_name">
                        <label for="last_name" class="col-sm-3 control-label">Last Name</label>
                        <div class="col-sm-9">
                            {{ Form::text('last_name',null,array('placeholder'=>'Last Name','class'=>'form-control', 'id'=>'ids_last_name', 'required'=>'true')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group row" id="email">
                        <label for="email" class="col-sm-3 control-label">Email</label>
                        <div class="col-sm-9">
                            {{ Form::text('email',null,array('placeholder'=>'Email','class'=>'form-control', 'id'=>'ids_email', 'required'=>'true')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group row" id="phone_number">
                        <label for="phone_number" class="col-sm-3 control-label">Phone</label>
                        <div class="col-sm-9">
                            {{ Form::text('phone_number',null,array('placeholder'=>'Phone [ format (XXX)XXX-XXXX ]','class'=>'form-control phone-mask','maxlength'=>6,'id'=>'ids_phone_number', 'required'=>'true')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <!--Start-- Custom Question  Section -->
                    <div class="custom-questions-container">
                    @foreach($questions as $q)
                        <div id="{{$q['id']}}" class="form-group row">
                        {{ Form::hidden('question_ids[]', $q['id'] ) }}
                            <label for="{{$q['id']}}" class="col-sm-12">{{$q['question']}}</label>
                            <div class="col-sm-12">
                                <select id="question_id_{{$q['id']}}" name="selected_option_id_{{$q['id']}}" @if((int)$q['is_required'] == 1) required="TRUE" @endif
                                  class="form-control questionSelect" onChange="getOtherOptionText(this,{{$q['id']}})" >
                                    <option value="">Please Select</option>
                                    @foreach($q['options'] as $option)
                                    <option value="{{$option['id']}}">{{$option['custom_question_option']}}</option>
                                    @endforeach
                                </select>
                                <input placeholder="Please Specify" class="form-control other-option-text" id="option-text-{{$q['id']}}" name="other_option_vale_{{$q['id']}}" type="text" >
                                <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                            </div>
                        </div>
                    @endforeach
                    </div>

                    <div class="form-group row" id="is_candidate" >
                        <label for="is_candidate" class="col-sm-12">
                            Are you currently a full-time employee at Commissionaires Great Lakes ?
                        </label>
                        <div class="col-sm-12">
                            <select id="isCandidate" name="is_candidate" required="TRUE" class="form-control" >
                                <option value="">Please Select</option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                            <div id="requisitionNumber" class="candidate-requisition-no"> </div>
                            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                        </div>
                    </div>

                    <div class="form-group row" id="is_federal_billing" >
                        <label for="is_federal_billing" class="col-sm-12">
                            Do you have a letter from your employer for deferred billing?
                        </label>
                        <p class="col-sm-12 text-danger align-middle font-12">
                            (If you selected Yes, the letter must be presented at time of service)
                        </p>
                        <div class="col-sm-12">
                            <select id="isFederalBilling" name="is_federal_billing" required="TRUE" class="form-control" >
                                <option value="">Please Select</option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                            <div id="federalBillingEmployer" class="federal_billing_section"> </div>
                            <div class="form-control-feedback">
                                <span class="help-block text-danger align-middle font-12"></span>
                            </div>
                        </div>
                    </div>


                     <!--End-- Custom Question  Section -->
                     <div class="form-group row" id="recaptcha-section">
                        <div class="col-sm-12">
                            <div class="g-recaptcha" data-sitekey="{{config('globals.google_recaptcha_key')}}"></div>
                            <small class="help-block">g-recaptcha-response-div</small>
                        </div>
                     </div>
                     <div class="form-group row" id="payment-confirmation-message">
                        <p class="col-sm-12 text-danger align-middle" style="font-size: 15.5px;">
                            You will be redirected to a secure payment gateway. Please complete the payment within 10 minutes.
                        </p>
                     </div>
                     <div class="form-group row col-sm-12 text-danger align-middle" style="font-size: 15.5px;" id="offline-payment-message">
                        <!-- <p class="" >

                        </p> -->
                     </div>
                </div>

                <div class="modal-footer" style="text-align: right;">
                    {{ Form::submit('Cancel',array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
                    {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                    {{ Form::submit('Pay', array('class'=>'button btn btn-primary blue','id'=>'checkout-button','style'=>'font-weight: bold;'))}}
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!--End- Slot Scheduling Form -->

    <!-- Service Description Modal -->
    <div class="modal fade" id="serviceDescriptioModal" role="dialog" style="margin-top: 9%;">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
