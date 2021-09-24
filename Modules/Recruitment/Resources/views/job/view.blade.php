@extends('layouts.app')
@section('content')
<div class="table_title">
    <h4>Open Posting Requisition Form </h4>
</div>
<section>
    <div class="row">
        <div class="col-sm-1"></div>
        <div class="col-sm-9">
            <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 form-heading-small orange">Job Posting Rationale</label>
        </div>
        <div class="col-sm-3"></div>
    </div>
    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-9">
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">What is the reason for the open position?</div>
                <div class="col-sm-6 text-sm-left">
                    {{$job->reason->reason}}
                </div>
            </div>
            @if(!empty($job->permanent_id))
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">Why has the permanent position opened up? </div>
                <div class="col-sm-6 text-sm-left">
                    {{$job->reason_permanent->reason}}
                </div>
            </div>
            @endif @if(!empty($job->temp_code_id))
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">Why has the temporary position opened up?</div>
                <div class="col-sm-6 text-sm-left">
                    {{$job->reason_temp_code->reason}}
                </div>
            </div>
            @endif @if(!empty($job->resign_id))
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">Why did CM resign? </div>
                <div class="col-sm-6 text-sm-left">
                     <a class="btn submit" style="color: white;" target="_blank" href="{{ route('employee.exitterminationsummary') }}">{{$lookups['resignation_list'][$job->resign_id]}}</a>
                </div>
            </div>
            @endif @if(!empty($job->terminate_id))
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">If the candidate was terminated, why? </div>
                <div class="col-sm-6 text-sm-left">
                     <a class="btn submit" style="color: white;" target="_blank" href="{{ route('employee.exitterminationsummary') }}">{{$lookups['termination_list'][$job->terminate_id]}}</a>

                </div>
            </div>
            @endif
        </div>
        <div class="col-sm-1"></div>
    </div>
    <div class="row">
        <div class="col-sm-1"></div>
        <div class="col-sm-9">
            <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 form-heading-small orange">General Information</label>
        </div>
        <div class="col-sm-3"></div>
    </div>
    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-9">
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5 orange bold">What is the position being hired? </div>
                <div class="col-sm-6 text-sm-left orange bold">
                    {{$job->positionBeeingHired->position}}
                </div>
            </div>
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">Job Code </div>
                <div class="col-sm-6 text-sm-left">
                    {{$job->unique_key}}
                </div>
            </div>
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">Job Description</div>
                <div class="col-sm-6 text-sm-left">
                    <a class="btn submit" style="color: white;" target="_blank" href="{{ route('recruitment-job.view-description',$job->id) }}" title="Click here for full job description">Full Job description</a>

                </div>
            </div>
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">How many posts need to be filled? </div>
                <div class="col-sm-6 text-sm-left">
                    {{$job->no_of_vaccancies}}
                </div>
            </div>
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">Who is the area manager assigned to the account?</div>
                <div class="col-sm-6 text-sm-left">
                    {{$job->area_manager}}
                </div>
            </div>
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">What is the area manager's email address? </div>
                <div class="col-sm-6 text-sm-left">
                    {{!empty($job->am_email)?$job->am_email:'--'}}
                </div>
            </div>
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">When was the requisition created? (Date) </div>
                <div class="col-sm-6 text-sm-left">
                    {{$job->requisition_date}}
                </div>
            </div>
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">Please enter the post number. </div>
                <div class="col-sm-6 text-sm-left">
                    {{$job->customer->project_number}}
                </div>
            </div>
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">Who is the client?
                </div>
                <div class="col-sm-6 text-sm-left">
                    {{$job->customer->client_name}}
                </div>
            </div>
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">Who is requesting the job posting? </div>
                <div class="col-sm-6 text-sm-left">
                    {{$job->requester}}
                </div>
            </div>
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">What is the requestor's email address? (if applicable) </div>
                <div class="col-sm-6 text-sm-left">
                    {{$job->email}}
                </div>
            </div>
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">What is the requestor's phone number? (if applicable)</div>
                <div class="col-sm-6 text-sm-left">
                    {{$job->phone}}
                </div>
            </div>
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">What is the requestor's position? </div>
                <div class="col-sm-6 text-sm-left">
                    {{$job->position > 0 ? $job->requestorPosition->position:'Other'}}
                </div>
            </div>
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">Post Address</div>
                <div class="col-sm-6 text-sm-left">
                    {{$job->customer->address}}
                </div>
            </div>
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">Post City</div>
                <div class="col-sm-6 text-sm-left">
                    {{$job->customer->city}}
                </div>
            </div>
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">Postal Code </div>
                <div class="col-sm-6 text-sm-left">
                    {{$job->customer->postal_code}}
                </div>
            </div>
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">Requestor's Employee Number </div>
                <div class="col-sm-6 text-sm-left">
                    {{$job->employee_num}}
                </div>
            </div>
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">Type of Assignment</div>
                <div class="col-sm-6 text-sm-left">
                    {{$job->assignmentType->type}}
                </div>
            </div>
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">When do we need the candidate to start at the new client? </div>
                <div class="col-sm-6 text-sm-left">
                    {{$job->required_job_start_date}}
                </div>
            </div>

            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">At what time?</div>
                <div class="col-sm-6 text-sm-left">
                    {{ \Carbon\Carbon::parse($job->time)->format('h:i a') }}
                </div>
            </div>

            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">Is the position an ongoing permanent position?</div>
                <div class="col-sm-6 text-sm-left">
                    {{$job->ongoing}}
                </div>
            </div>
            @if(!empty($job->end))
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">If the position is not ongoing, when is the end date? </div>
                <div class="col-sm-6 text-sm-left">
                    {{$job->end}}
                </div>
            </div>
            @endif
            @if(!empty($job->training->training))
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">Does the position require training?</div>
                <div class="col-sm-6 text-sm-left">
                    {{$job->training->training}}
                </div>
            </div>
            @endif
            @if(!empty($job->training_time))
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">How many hours of training will be required? </div>
                <div class="col-sm-6 text-sm-left">
                    {{$job->training_time}}
                </div>
            </div>
            @endif @if(!empty($job->training_timing))
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">Is the training required as a condition of client onboarding?</div>
                <div class="col-sm-6 text-sm-left">
                    {{$job->training_timing->timings}}
                </div>
            </div>
            @endif
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">Is there a specific course required for this role?</div>
                <div class="col-sm-6 text-sm-left">
                    {{$job->course}}
                </div>
            </div>
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">Are there any special requirements beyond the standard job description?</div>
                <div class="col-sm-6 text-sm-left">
                    {{$job->notes}}
                </div>
            </div>
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">What are the shift requirements? (select all that apply)</div>
                <div class="col-sm-6 text-sm-left">
                    {!!implode('<br/>',json_decode($job->shifts))!!}
                </div>
            </div>
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">Days Required</div>
                <div class="col-sm-6 text-sm-left">
                    {!!implode('<br/>',json_decode($job->days_required))!!}
                </div>
            </div>
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">What are the position requirements? </div>
                <div class="col-sm-6">
                    @foreach(json_decode($job->criterias) as $each_criteria)
                        {{@Modules\Admin\Models\CriteriaLookup::find($each_criteria)->criteria}}
                    <br/>
                    @endforeach
                </div>
            </div>
              <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">How many years of experience is required for this role?</div>
                <div class="col-sm-6 text-sm-left">
                    {{$job->total_experience}}
                </div>
            </div>
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">What are the additional requirements for the role? (years) </div>
                <div class="col-sm-6">
                    @foreach($job->experiences as $each_experience) {{$each_experience->experienceLookup->experience}}({{$each_experience->year}}
                    Years)
                    <br/> @endforeach
                </div>
            </div>
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">Is a vehicle required for the position? </div>
                <div class="col-sm-6 text-sm-left">
                    {{$job->vehicle}}
                </div>
            </div>
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">What is the hourly wage ($)? </div>
                <div class="col-sm-3 text-sm-left">
        
                    <span class="orange"> :</span> ${{number_format((float)$job->wage, 2, '.', '')}}
                 
                </div>
            </div>
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">Hours per week?

                </div>
                <div class="col-sm-6 text-sm-left">
                    {{$job->hours_per_week}}
                </div>
            </div>
            <div class="form-group row">
                <div for="reason_dropdown_1" class="col-sm-6 pl-5">Are there any final remarks?

                </div>
                <div class="col-sm-6 text-sm-left">
                    {{$job->remarks}}
                </div>
            </div>
        </div>
        <div class="col-sm-1"></div>
    </div>
</section>
@can('job-attachement-settings')
<section>
    {{ Form::open(array('id'=>'attachment-mandatory-form','class'=>'form-horizontal','method'=>'POST','autocomplete'=>'dfgggf')) }}
    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-9">
            <div class="form-group row">
                <div class="col-sm-6 pl-5 orange bold">Select Mandatory Documents </div>
                <div class="col-sm-6 text-sm-left doc-title-top">
                    <b>Default Documents </b>
                    <div class="nav flex-column checkbox-edit">
                        <div class="row">
                            <div class="col-lg-12">
                                @foreach($candidateAttachment_default_Lookups as $key=>$value)
                                <div class="input-group doc-title-top">
                                    <span class="checkbox-margin">
                                        {{ Form::checkbox('mandatory_attachements[]', ($key),(is_array($mandatory_attachment_ids) && in_array($key, $mandatory_attachment_ids)?$key:null),['class'=>'input-checkbox-size'])}}
                                    </span>
                                    {{$value}}
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-9">
            <div class="form-group row">
                <div class="col-sm-6 pl-5 orange bold"> </div>
                <div class="col-sm-6 text-sm-left doc-title-top">
                    <div>
                        <b>User Defined Documents</b>
                        <button onclick="$('.user-defined-attachements').find('.custom-attachment-item:hidden:first').show().find('input').prop('disabled', false);" class="btn checkbox-right-margin append-click" type="button">
                            <a id="add_new" title="Add another" href="javascript:;" class="add_button">
                                <i class="fa fa-plus"></i>
                            </a>
                        </button>
                    </div>
                    <div class="nav flex-column checkbox-edit user-defined-attachements">
                        <div class="row">
                            <div class="col-lg-12">
                                @if($custom_lookup_count > 0)
                                    @foreach ($candidateAttachment_custom_Lookups as $custom_key => $val)
                                        @include('hranalytics::job.partials.attachment-field')
                                    @endforeach
                                    @php
                                        $custom_key = null
                                    @endphp
                                @endif
                                @for($i=($custom_lookup_count+1);$i<=20;$i++)
                                    @include('hranalytics::job.partials.attachment-field')
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-5"></div>
        <div class="col-sm-6">
            <button type="submit" class="btn submit">Update Job</button>
        </div>
    </div>
    {{Form::close()}}
</section>
    @endcan
@endsection
@can('job-attachement-settings')
@section('scripts')
<script>
    $('#attachment-mandatory-form').submit(function (e) {
        e.preventDefault();
        var $form = $(this);
        var formData = new FormData($('#attachment-mandatory-form')[0]);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('recruitment-job.attachment-settings',$job->id) }}",
            type: 'POST',
            data: formData,
            success: function (data) {
                if (data.success) {
                    showMessageIfUpdate($form);
                } else {
                    alert(data.success);
                }
            },
            fail: function (response) {
                alert('here');
            },
            error: function (xhr, textStatus, thrownError) {
                associate_errors(xhr.responseJSON.errors, $form);
            },
            contentType: false,
            processData: false,
        });
    });

    function showMessageIfUpdate($form) {
        swal({
                title: "Success",
                text: "Successfully updated.",
                type: "info",
                confirmButtonClass: "btn-success",
                confirmButtonText: "OK",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            },
            function () {
                window.location = "{{ route('recruitment-job') }}"
            });
    }
</script>
@endsection
@endcan
