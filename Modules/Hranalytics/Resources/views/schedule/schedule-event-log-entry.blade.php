@extends('layouts.app')
@section('content')
<div class="table_title">
    <h4>Event Log Entry</h4>
</div>
{{ Form::open(array('url'=>'#','id'=>'event-log-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
<section class="row content-block">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 title-header-align form-panel">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head">Customer Details </div>
        <div class="row" id="customer-details">
            <div class="col-md-12 col-sm-12 xs-12 col-lg-12">
                <div class="row">
                    <div class="col-xs-12 col-md-12 col-lg-4 col-sm-12">
                        <div class="form-group row styled-form">
                            <label  class="col-md-4 label-adjust col-form-label control-label col-xs-3">Project Number</label>
                            <div class="col-md-8 col-xs-9">
                                {{ Form::hidden('shift_id', $shift_id) }}
                                {{ Form::hidden('assignment_type_id',  $scheduleRequirement->trashed_assignment_type->id,array('id'=>'assignment')) }}
                                  {{ Form::hidden('openshift_requirement', session('openshift_requirement'),array('id'=>'openshift_requirement')) }}
                                   {{ Form::hidden('openshift_multifill', session('openshift_multifill'),array('id'=>'openshift_multifill')) }}
                                    {{ Form::hidden('openshift_user', session('openshift_user'),array('id'=>'openshift_user')) }}
                     {{ Form::hidden('customer_id',$scheduleRequirement->customer->id) }}
                                {{ Form::text('project_number', $scheduleRequirement->customer->project_number, array('class'=>'form-control', 'placeholder'=>'Project Number','readonly'=>true)) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-12 col-lg-4 col-sm-12">
                        <div class="form-group row styled-form">
                            <label  class="col-md-4 label-adjust col-form-label control-label col-xs-3">Site Address</label>
                            <div class="col-md-8 col-xs-9">
                                {{ Form::text('adress', $scheduleRequirement->customer->address, array('class'=>'form-control','readonly'=>true)) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-12 col-lg-4 col-sm-12">
                        <div class="form-group row styled-form">
                            <label  class="col-md-4 label-adjust col-form-label control-label col-xs-3">Site Postal Code</label>
                            <div class="col-md-8 col-xs-9">
                                {{ Form::text('postal_code', $scheduleRequirement->customer->postal_code, array('class'=>'form-control','readonly'=>true)) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-12 col-lg-4 col-sm-12">
                        <div class="form-group row styled-form">
                            <label  class="col-md-4 label-adjust col-form-label control-label col-xs-3">Client Name</label>
                            <div class="col-md-8 col-xs-9">
                                {{ Form::text('client_name', $scheduleRequirement->customer->client_name, array('class'=>'form-control','readonly'=>true)) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-12 col-lg-4 col-sm-12">
                        <div class="form-group row styled-form">
                            <label  class="col-md-4 label-adjust col-form-label control-label col-xs-3">Inquiry Date</label>
                            <div class="col-md-8 col-xs-9">
                                {{ Form::text('inquiry_date', $scheduleRequirement->inquiry_date, array('class'=>'form-control','readonly'=>true)) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-12 col-lg-4 col-sm-12">
                        <div class="form-group row styled-form">
                            <label class="col-md-4 label-adjust col-form-label control-label col-xs-3">Time Stamp</label>
                            <div class="col-md-8 col-xs-9">
                                {{ Form::text('inquiry_time', $scheduleRequirement->inquiry_time, array('class'=>'form-control','readonly'=>true)) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-12 col-lg-4 col-sm-12">
                        <div class="form-group row styled-form" >
                            <label class="col-md-4 label-adjust col-form-label control-label col-xs-3">Site Description</label>
                            <div class="col-md-8 col-xs-9">
                                {{ Form::textarea('site_description', $scheduleRequirement->customer->description, array('class'=>'form-control','readonly'=>true,'rows'=>5)) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-12 col-lg-4 col-sm-12">
                        <div class="form-group row styled-form">
                            <label class="col-md-4 label-adjust col-form-label control-label col-xs-3">Duty Officer</label>
                            <div class="col-md-8 col-xs-9">
                                {{ Form::text('duty_officer', $scheduleRequirement->user->full_name, array('class'=>'form-control','readonly'=>true)) }}
                            </div>
                        </div>
                    </div>

                    @if($scheduleRequirement->customer->stcDetails != null)
                    <div class="col-xs-12 col-md-12 col-lg-4 col-sm-12">
                        <div class="form-group row styled-form">
                            <label class="col-md-4 label-adjust col-form-label control-label col-xs-3">Is this a NMSO Account</label>
                            <div class="col-md-8 col-xs-9">
                                {{ Form::text('nmso_account', ucfirst($scheduleRequirement->customer->stcDetails->nmso_account), array('class'=>'form-control','readonly'=>true)) }}
                            </div>
                        </div>
                    </div>
                </div>

                @if($scheduleRequirement->customer->stcDetails->nmso_account == 'yes')
                    <div class="row">
                        <div class="col-xs-12 col-md-12 col-lg-4 col-sm-12">
                            <div class="form-group row styled-form">
                                <label class="col-md-4 label-adjust col-form-label control-label col-xs-3">Employee Security Level</label>
                                <div class="col-md-8 col-xs-9">
                                    {{ Form::text('security_clearance_lookup_id', $scheduleRequirement->customer->stcDetails->trashed_security_clearance->security_clearance, array('class'=>'form-control','readonly'=>true)) }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @endif
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 title-header-align form-panel">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head">Requirements </div>
        <div class="row" id="requirement-details">
            {{ Form::hidden('requirement_id',$scheduleRequirement->id) }}
            <div class="col-md-12 col-sm-12 xs-12 col-lg-12">
                <div class="row">
                    <div class="col-xs-12 col-md-12 col-lg-4 col-sm-12 ">
                        <div class="form-group row styled-form" id="type">
                            <label  class="col-md-4 label-adjust col-form-label control-label col-xs-3">Assignment Type</label>
                            <div class="col-md-8 col-xs-9">
                                {{ Form::text('assignment_type', $scheduleRequirement->trashed_assignment_type->type, array('class'=>'form-control','readonly'=>true)) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-12 col-lg-4 col-sm-12 ">
                        <div class="form-group row styled-form" id="start_date">
                            <label  class="col-md-4 label-adjust col-form-label control-label col-xs-3">Start Date</label>
                            <div class="col-md-8 col-xs-9">
                                {{ Form::text('start_date', $scheduleRequirement->start_date, array('class'=>'form-control','readonly'=>true)) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-12 col-lg-4 col-sm-12 ">
                        <div class="form-group row styled-form" id="end_date">
                            <label  class="col-md-4 label-adjust col-form-label control-label col-xs-3">End Date</label>
                            <div class="col-md-8 col-xs-9">
                                {{ Form::text('end_date', $scheduleRequirement->end_date, array('class'=>'form-control','readonly'=>true)) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                   <div class="col-xs-12 col-md-12 col-lg-4 col-sm-12 ">
                    <div class="form-group row styled-form"  id="site_rate">
                        <label  class="col-md-3 label-adjust col-form-label control-label col-xs-3">Site Rate</label>
                        <label class="col-sm-1 col-form-label">$</label>
                        <div class="col-md-8 col-xs-9">
                            {{ Form::text('site_rate', $scheduleRequirement->site_rate, array('class'=>'form-control','readonly'=>true)) }}
                        </div>
                    </div>
                </div>
                @if($scheduleRequirement->trashed_assignment_type->type != "Multiple Fill")
                <div class="col-xs-12 col-md-12 col-lg-4 col-sm-12 " id="time_scheduled">
                    <div class="form-group row styled-form">
                        <label  class="col-md-4 label-adjust col-form-label control-label col-xs-3">Time Scheduled</label>
                        <div class="col-md-8 col-xs-9">
                            {{ Form::text('time_scheduled', $scheduleRequirement->time_scheduled, array('class'=>'form-control','readonly'=>true)) }}
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-12 col-lg-4 col-sm-12 accepted_shift" id="length_of_shift">
                    <div class="form-group row styled-form">
                        <label class="col-md-4 label-adjust col-form-label control-label col-xs-3">Length of Shift (Hrs)</label>
                        <div class="col-md-8 col-xs-9">
                            {{ Form::text('length_of_shift', $scheduleRequirement->length_of_shift, array('class'=>'form-control','readonly'=>true)) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                @endif
                @if($scheduleRequirement->customer->stcDetails != null)
                <div class="col-xs-12 col-md-12 col-lg-4 col-sm-12 ">
                    <div class="form-group row styled-form">
                        <label  class="col-md-4 label-adjust col-form-label control-label col-xs-3">Does this post require security clearance</label>
                        <div class="col-md-8 col-xs-9">
                            {{ Form::text('require_security_clearance', ucfirst($scheduleRequirement->require_security_clearance), array('class'=>'form-control','readonly'=>true)) }}
                        </div>
                    </div>
                </div>
                @if($scheduleRequirement->require_security_clearance == 'yes')
                <div class="col-xs-12 col-md-12 col-lg-4 col-sm-12 accepted_shift">
                    <div class="form-group row styled-form">
                        <label class="col-md-4 label-adjust col-form-label control-label col-xs-3">Security Level</label>
                        <div class="col-md-8 col-xs-9">
                            {{ Form::text('security_clearance_level', $scheduleRequirement->trashed_security_clearance->security_clearance, array('class'=>'form-control','readonly'=>true)) }}
                        </div>
                    </div>
                </div>
                @endif
                @endif
                <div class="col-xs-12 col-md-12 col-lg-4 col-sm-12 ">
                    <div class="form-group row styled-form">
                        <label class="col-md-4 label-adjust col-form-label control-label col-xs-3">Notes</label>
                        <div class="col-md-8 col-xs-9">
                            {{ Form::textarea('notes', $scheduleRequirement->notes, array('class'=>'form-control','readonly'=>true,'rows'=>5)) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 form-panel" >
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head">Contact Log </div>
    <div class="row">
        <div class="col-md-12 col-sm-12 xs-12 col-lg-12">
            <div class="row">
                <div class="col-xs-12 col-md-12 col-lg-4 col-sm-12">
                    <div class="form-group row styled-form-readonly">
                        <label  class="col-md-4 label-adjust col-form-label control-label col-xs-3">Name</label>
                        <div class="col-md-8 col-xs-9">
                            {{ Form::hidden('user_id', $user->id) }}
                            {{ Form::text('user_name', $user->getFullNameAttribute(), array('class'=>'form-control','readonly'=>true)) }}
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-md-12 col-lg-4 col-sm-12">
                    <div class="form-group row styled-form-readonly">
                        <label class="col-md-4 label-adjust col-form-label control-label col-xs-3">Address</label>
                        <div class="col-md-8 col-xs-9">
                            {{ Form::text('user_address', $user->employee->employee_address, array('class'=>'form-control','readonly'=>true)) }}
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-md-12 col-lg-4 col-sm-12">
                    <div class="form-group row styled-form-readonly">
                        <label class="col-md-4 label-adjust col-form-label control-label col-xs-3">Postal Code</label>
                        <div class="col-md-8 col-xs-9">
                            {{ Form::text('user_postal_code', $user->employee->employee_postal_code, array('class'=>'form-control','readonly'=>true)) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-12 col-lg-4 col-sm-12">
                    <div class="form-group row styled-form-readonly">
                        <label class="col-md-4 label-adjust col-form-label control-label col-xs-3">Cell Phone</label>
                        <div class="col-md-8 col-xs-9">
                            {{ Form::text('user_phone', $user->employee->phone, array('class'=>'form-control','readonly'=>true)) }}
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-12 col-lg-4 col-sm-12">
                    <div class="form-group row styled-form-readonly">
                        <label  class="col-md-4 label-adjust col-form-label control-label col-xs-3">Email Address</label>
                        <div class="col-md-8 col-xs-9">
                            {{ Form::text('user_email', $user->email, array('class'=>'form-control','readonly'=>true)) }}
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-12 col-lg-4 col-sm-12">
                    <div class="form-group row styled-form-readonly">
                        <label  class="col-md-4 label-adjust col-form-label control-label col-xs-3">Inquiry Date</label>
                        <div class="col-md-8 col-xs-9">
                            {{ Form::text('user_inquiry_date', date('Y-m-d'), array('class'=>'form-control','readonly'=>true)) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-12 col-lg-4 col-sm-12">
                    <div class="form-group row styled-form-readonly">
                        <label  class="col-md-4 label-adjust col-form-label control-label col-xs-3">Time Stamp</label>
                        <div class="col-md-8 col-xs-9">
                            {{ Form::text('user_inquiry_time', date('h:i A'), array('class'=>'form-control','readonly'=>true)) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 title-header-align form-panel">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head">Status Log </div>
    <div class="row">
        <div class="col-md-12 col-sm-12 xs-12 col-lg-12">
            <div class="row">
                <div class="col-xs-12 col-md-12 col-lg-4 col-sm-12">
                    <div class="form-group row styled-form" id="status">
                        <label  class="col-md-4 label-adjust col-form-label control-label col-xs-3">Status</label>
                        <div class="col-md-8 col-xs-9">
                            <select class="form-control"  id="shift_acceptance" name="status" required>
                                <option value="" selected disabled>Select Status</option>
                                @foreach($callLogLookup as $id=>$each_status)
                                <option value="{{$id}}" {{ (isset($logDetails) && ($id== ($logDetails->status))? 'selected' : '') }}>{{$each_status}}</option>
                                @endforeach
                            </select>
                            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('status', ':message') !!}</div>
                        </div>
                    </div>
                </div>


                <div class="col-xs-12 col-md-12 col-lg-4 col-sm-12 accepted_shift" id="accepted_rate" style={{(isset($logDetails) && ($logDetails->status==1)) ? "display:block;" : "display:none;"}}>
                    <div class="form-group row styled-form">
                        <label  class="col-md-3 label-adjust col-form-label control-label col-xs-3">Accepted Rate</label>
                        <label class="col-sm-1 col-form-label">$</label>
                        <div class="col-md-8 col-xs-9">
                            {{ Form::text('accepted_rate', (isset($logDetails)?$logDetails->accepted_rate:''), array('class'=>'form-control', 'placeholder'=>"Accepted Rate",'readonly'=>false,'id'=>"accepted_rates")) }}
                            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('accepted_rate', ':message') !!}</div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-md-12 col-lg-4 col-sm-12"  id="status_notes">
                    <div class="form-group row styled-form" >
                        <label class="col-md-4 label-adjust col-form-label control-label col-xs-3">Notes</label>
                        <div class="col-md-8 col-xs-9">
                            {{ Form::textarea('status_notes', (isset($logDetails)?$logDetails->status_notes:''), array('class'=>'form-control', 'placeholder'=>"Notes",'readonly'=>false,'required'=>true,'rows'=>5,'id'=>"notes")) }}
                            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('status_notes', ':message') !!}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-row text-center">
        <div class="col-12">
            <input title="Cancel" class="btn submit" type="button" value="Cancel" id="reset_button" style="margin:5px;" onclick="window.location='{{ route('candidate.schedule') }}'">
            <input class="btn submit" type="submit" value="Save" style="margin:5px;">
        </div>
    </div>
</div>
{{ Form::close() }}
</section>

<script type="text/javascript">
    $(document).ready(function () {

        $("#shift_acceptance").change(function () {
            $("#accepted_rates").val('');
            $("#notes").val('');
            if ($(this).val() == 1)
            {

                $(".accepted_shift").show();
                $('#exampleSelect1').prop('required', true);
            } else
            {
                $(".accepted_shift").hide();
                $('#exampleSelect1').prop('required', false);
            }

        });

        /*Event log form submit - Start*/
    $('#event-log-form').submit(function (e) {
        e.preventDefault();
        var $form = $(this);
        var formData = new FormData($('#event-log-form')[0]);
        console.log(formData);
        $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
        var message_text = "Successfully updated the call log.";
        var redirect_url = "{{ route('candidate.schedule') }}";
        if($("#openshift_requirement").val())
        {
            var openshift_requirement=$("#openshift_requirement").val();
            var openshift_multifill=$("#openshift_multifill").val();
            var openshift_user=$("#openshift_user").val();
            var openshift_status=$('#shift_acceptance').val();
            var base_url = "{{ route('openshift.mail',[':requirement_id',':shift_id',':user_id',':status']) }}";
         base_url = base_url.replace(':requirement_id', openshift_requirement);
         base_url = base_url.replace(':shift_id', openshift_multifill);
         base_url = base_url.replace(':user_id', openshift_user);
          url = base_url.replace(':status', openshift_status);
          redirect_url=url;
        }
        else if(($('#shift_acceptance').val()!=1)||($('#assignment').val()==4))
        {
            redirect_url = "{{ route('candidate.schedule',[$scheduleRequirement->customer->id,$scheduleRequirement->id,$scheduleRequirement->customer->stc]) }}"
            message_text += "\nYou will be redirected to candidate schedule page to call another candidate ";
        }
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('candidate.event_log_save') }}",
            type: 'POST',
            data: formData,
            success: function (data) {
                if (data.success) {
                    swal({
                        title: "Success",
                        text: message_text,
                        type: "info",
                        confirmButtonClass: "btn-success",
                        confirmButtonText: "OK",
                        showLoaderOnConfirm: true,
                        closeOnConfirm: true
                    },
                    function () {
                        window.location = redirect_url;
                    });
                } else if(data.msg && data.msg != ""){
                    swal("Error", data.msg,"error");
                }
            },
            fail: function (response) {

            },
            error: function (xhr, textStatus, thrownError) {
                associate_errors(xhr.responseJSON.errors, $form);
            },
            contentType: false,
            processData: false,
        });
    });
    /*Event log form submit - End*/


    });



</script>
@stop
