
    <!-- Slot Rescheduling Form - Start -->
        <div class="modal fade" id="scheduleModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    {{ Form::open(array('url'=>'#','id'=>'scheduling-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                    {{ Form::hidden('id', null) }}

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
                                {{ Form::label('first_name',null,array('placeholder'=>'First Name','class'=>'view-form-element', 'id'=>'firstName')) }}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group row" id="last_name">
                            <label for="last_name" class="col-sm-3 control-label">Last Name</label>
                            <div class="col-sm-9">
                                {{ Form::label('last_name',null,array('placeholder'=>'Last Name','class'=>'view-form-element', 'id'=>'lastName')) }}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group row" id="email">
                            <label for="email" class="col-sm-3 control-label">Email</label>
                            <div class="col-sm-9">
                                {{ Form::text('email',null,array('placeholder'=>'Email','class'=>'form-control', 'id'=>'emailId', 'required'=>'true')) }}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group row" id="phone_number">
                            <label for="phone_number" class="col-sm-3 control-label">Phone</label>
                            <div class="col-sm-9">
                                {{ Form::text('phone_number',null,array('placeholder'=>'Phone [ format (XXX)XXX-XXXX ]','class'=>'form-control phone','maxlength'=>6,'id'=>'phoneNumber', 'required'=>'true')) }}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group row" id="gender">
                            <label for="gender" class="col-sm-3 control-label">Gender</label>
                            <div class="col-sm-9">
                                {{ Form::select('gender',[null=>'Please select',1=>'Male',2=>'Female'],null,array('class'=>'form-control','id'=>'genderValue', 'required'=>'true')) }}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group row" >
                            <div class="col-sm-12">
                                <img style="width: 103%;" src="{{asset("images/measurement-chart.jpg")}}" alt="" srcset="">
                            </div>
                        </div>

                        @foreach ($measurementPoints as $measurement)
                            <div class="form-group row" id="point_value_{{$measurement->id}}">
                                <label for="point_value_{{$measurement->id}}" id="label_{{$measurement->id}}" class="col-sm-3 control-label">{{$measurement->name}} </label>
                                <div class="col-sm-9 row" style="margin-left: 0px;">
                                    {{ Form::hidden('point_ids[]', $measurement->id ) }}
                                    {{Form::selectRange('point_value_'.$measurement->id, 5, 70,null,array('placeholder'=>'Please select','class'=>'form-control col-sm-5','id'=>'','required'=>'true'))}}
                                    {{Form::select('point_decimal_value_'.$measurement->id, config('globals.uniform_measurement_decimal_points'),null,array('class'=>'form-control col-sm-4 offset-sm-1','id'=>''))}}
                                    <small class="help-block"></small>
                                </div>
                            </div>
                        @endforeach

                        <div class="form-group row" id="booked_date">
                            <label for="booked_date" class="col-sm-3 control-label">Reschedule Date</label>
                            <div class="col-sm-9">
                                {{ Form::text('booked_date',null,array('placeholder'=>'Reschedule Date','class'=>'form-control datepicker', 'id'=>'bookedDate')) }}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group row" id="slot_timimgs">
                            <label for="slot_timimgs" class="col-sm-3 control-label">Slot Timings</label>
                            <div class="col-sm-9">
                                {{ Form::select('slot_timimgs',[''=>'Please Select'], old('time_slot'),array('class'=> 'form-control', 'id'=>'time_slots')) }}
                                <small class="help-block"></small>
                            </div>
                        </div>

                        <div class="form-group row" id="is_client_show_up">
                            <label for="is_client_show_up" class="col-sm-3 control-label" style="padding-right: 0px;"> Did the candidate show up? </label>
                            <div class="col-sm-9">
                                <label> <input type="radio" class="is_client_show_up" name="is_client_show_up" id="client_show_up_yes"  value="1" >&nbsp;Yes&nbsp;&nbsp;</label>
                                <label> <input type="radio" class="is_client_show_up" name="is_client_show_up" id="client_show_up_no"  value="0" >&nbsp;No&nbsp;&nbsp;</label>
                            <small class="help-block"></small>
                            </div>
                        </div>

                        <div id="questionAnswers"> </div>

                        <div class="form-group row" id="notes">
                            <label for="notes" class="col-sm-12 control-label">Note</label>
                            <div class="col-sm-12">
                            {{ Form::textArea('notes', null, array('class'=>'form-control', 'placeholder'=>'Notes','rows'=>5,'id'=>'reviewNotes')) }}
                            <small class="help-block"></small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                    @can('uniform_booking_delete')
                        {{ Form::button('Delete',array('class'=>'btn btn-primary orange', 'id'=>'delete_slot'))}}
                    @endcan
                    @can('uniform_booking_cancel')
                        {{ Form::button('Cancel Appointment', array('class'=>'button btn btn-primary orange','id'=>'cancel_booking'))}}
                    @endcan
                    @can('uniform_reschedule_appointment')
                        {{ Form::submit('Update', array('class'=>'button btn btn-primary blue','id'=>'rescheduling_mdl_save'))}}
                    @endcan
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    <!-- Slot Rescheduling Form - End -->

