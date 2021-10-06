@extends('layouts.app')
@section('content')

<div class="table_title">
    <h4>Candidate Onboarding Status</h4>
</div>
@if(!isset($candidateJob->candidate->termination))
<!-- application termincation-start -->
@can('terminate_candidate_application')
<input onclick="terminateApplication();" class="btn submit" type="submit" value="Terminate Application" style="
    float: right;
    margin-top: -47px;
">
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(array('url'=>'#','id'=>'candidate-application-termincation-form','class'=>'form-horizontal',
            'method'=> 'POST')) }} {{csrf_field()}}
            <div class="modal-body">
                <div class="form-group" id="recruiter">
                    <label for="recruiter" class="col-sm-12 control-label">Recruiter Name:</label>
                    <div class="col-sm-12">
                        {{ Form::text('recruiter',auth()->user()->full_name,array('class'
                        =>'form-control','readonly'=>true))
                        }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="reason_id">
                    <label for="reason_id" class="col-sm-12 control-label">Reason:</label>
                    <div class="col-sm-12">
                        {{ Form::select('reason_id', [null=>'Please Select']+$termination_reasons, null,array('class'
                        =>'form-control','required'=>true)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="reason">
                    <label for="reason" class="col-sm-12 control-label">Notes:</label>
                    <div class="col-sm-12">
                        {{ Form::textarea('reason', null,array('class' =>'form-control','required'=>true,'max'=>1000))
                        }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="date">
                    <label for="date" class="col-sm-12 control-label">Date Stamp:</label>
                    <div class="col-sm-12">
                        {{ Form::text('date',\Carbon\Carbon::now()->format('d-m-Y'),array('class'
                        =>'form-control','readonly'=>true))
                        }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="time">
                    <label for="time" class="col-sm-12 control-label">Time Stamp:</label>
                    <div class="col-sm-12">
                        {{ Form::text('time',\Carbon\Carbon::now()->format('h : i A'),array('class'
                        =>'form-control','readonly'=>true))
                        }}
                        <small class="help-block"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn submit','id'=>'mdl_save_change'))}}
                {{ Form::button('Cancel', array('class'=>'btn cancel','data-dismiss'=>"modal", 'aria-hidden'=>true))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@endcan
@else
<script>
    $(function(){
    swal({
                                title: "Terminated",
                                text: "Application Terminated on: {{ \Carbon\Carbon::parse($candidateJob->candidate->termination->created_at)->format('l F d, Y h:i A') }}\n\n By: {{ $candidateJob->candidate->termination->user->full_name }}\n\n Reason: {{ $candidateJob->candidate->termination->reasonLookup->reason }}\n\nNotes: {{ preg_replace('/[\r\n]*/','',$candidateJob->candidate->termination->reason) }}",
                                type: "info",
                                showCancelButton: false,
                                showLoaderOnConfirm: true,
                                closeOnConfirm: true
                            },
                            function () {
                            });
                        });
</script>

@if(auth()->user()->roles[0]->name=='super_admin')
<input onclick="reactivateApplication();" class="btn submit" type="submit" value="Reactivate Application" style="
    float: right;
    margin-top: -47px;
">
<!-- application termincation-end -->
@endif
<img src="{{ asset('images/terminated.png') }}" style="
        margin-top: -81px;
    float: right;
    margin-right: 17%;
    /* position: fixed; */
">
@endif
{{ Form::open(array('url'=>'#','id'=>'hr-tracking-processes','method'=> 'POST')) }}
<div class="table-responsive" id="tracking-form">
    <table class="table table-bordered dataTable">
        <thead>
            <th width="15%">Candidate Name</th>
            <th width="20%">Address</th>
            <!--<th width="2%">City</th>
            <th width="2%">Postal Code</th>-->
            <th width="15%">Job Initially Applied To</th>
            <th width="15%">Client</th>
            <th width="11%">Job Code Reassignment</th>
            <th width="15%">Client Reassignment</th>
            <th width="10%">Current Wage</th>
            <th width="10%">Reassigned Wage</th>
        </thead>
        <tbody>
            <tr>
                <td class="candidate_id">{{$candidateJob->candidate->name}}</td>
                <td class="address">{{$candidateJob->candidate->address}}, {{$candidateJob->candidate->city}},   {{$candidateJob->candidate->postal_code}}</td>
                <!--<td class="city">{{$candidateJob->candidate->city}}</td>
                <td class="psotal-code">{{$candidateJob->candidate->postal_code}}</td>-->
                <td class="job_id">{{$candidateJob->job->unique_key}}</td>
                <td class="client-name">{{$candidateJob->job->customer->client_name}}</td>
                <td>
                    {{
                    Form::select('job_reassigned_id',[0=>'-None-']+$all_jobs,old('job_reassigned_id',$candidateJob->job_reassigned_id),array('class'=>'form-control
                    job-reassignment-select')) }}
                </td>
                <td class="client-reassigned-name">{{$candidateJob->job_reassigned_id!=0?$candidateJob->jobReassigned->customer->client_name:''}}</td>
                <td class="client-wage">
                    ${{$candidateJob->proposed_wage_low!=null?number_format((float)$candidateJob->proposed_wage_low,2,'.',''):number_format((float)$candidateJob->job->wage_low,2,'.','')}}-${{$candidateJob->proposed_wage_high!=null?number_format((float)$candidateJob->proposed_wage_high,2,'.',''):number_format((float)$candidateJob->job->wage_high,2,'.','')}}
                </td>
                <td class="client-reassigned-wage">
                    @if($candidateJob->job_reassigned_id!=0)
                    ${{number_format((float)$candidateJob->jobReassigned->wage_low,2,'.','')}}-${{number_format((float)$candidateJob->jobReassigned->wage_high,2,'.','')}}
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div id="form-container">

    <div class="table-responsive" id="tracking-table">
        <table class="table table-bordered dataTable">
            <thead>
                <th>No</th>
                <th>Process Step</th>
                <th>Completion Date</th>
                <th width="40%" align="justify">Notes</th>
                <th>Entered By</th>
            </thead>
            <tbody>
                @foreach($lookups as $index=>$lookup)
                <tr>
                    <td>
                        {{$lookup->step_number}}
                    </td>
                    <td>
                        {{$lookup->process_steps}}
                    </td>
                    @if(in_array($lookup->id,array_keys($already_processed_track_ids)))
                    <td>
                        {{ Form::label('completion_date['.$lookup->id.']',
                        $already_processed_track_ids[$lookup->id]->completion_date) }}
                    </td>
                    <td>
                        {{ Form::label('notes['.$lookup->id.']',$already_processed_track_ids[$lookup->id]->notes) }}
                    </td>
                    <td>
                        {!!
                        ($already_processed_track_ids[$lookup->id]->entered_by!=null)?$already_processed_track_ids[$lookup->id]->entered_by->full_name:'<i>User
                            Removed</i>' !!}
                        @can('delete-hr-tracking') &nbsp;
                        @if(!isset($candidateJob->candidate->termination))
                        <a title="Remove this entry" href="javascript:;" class="delete fa fa-trash" data-id="{{ $lookup->id }}"
                            data-job-id="{{ $candidateJob->job_id }}" data-candidate-id="{{ $candidateJob->candidate_id }}"></a>
                        @endif
                        @endcan
                    </td>
                    @else
                    <td id="{{ 'completion_date.'.$lookup->id }}">
                        {{
                        Form::text('completion_date['.$lookup->id.']',old('completion_date['.$lookup->id.']'),array('class'
                        => 'datepicker form-control')) }}
                        <span class="help-block text-danger align-middle font-12"></span>
                    </td>
                    <td id="{{ 'notes.'.$lookup->id }}">
                        {{
                        Form::textArea('notes['.$lookup->id.']',old('notes['.$lookup->id.']'),array('placeholder'=>"Notes",'cols'=>'30','rows'=>1,'class'
                        => 'form-control')) }}
                        <span class="help-block text-danger align-middle font-12"></span>
                    </td>
                    <td id="{{ 'entered_by_id.'.$lookup->id }}">
                        {{ Form::select('entered_by_id['.$lookup->id.']', [null=>'Please Select']+$users,
                        null,array('class' => 'form-control select2')) }}
                        <span class="help-block text-danger align-middle font-12"></span>
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@can('hr-tracking')
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 text-xs-center text-sm-center text-md-center text-lg-center text-xl-center margin-top-1">
    @if(!isset($candidateJob->candidate->termination))
    {{ Form::submit('Save', array('class' => 'btn submit')) }}
    @endif
    {{ Form::button('Cancel', array('class' => 'btn cancel','onclick'=>'window.history.back();')) }}
</div>
@endcan
{{ Form::close() }}
@endsection
@section('scripts')
<script>
    $(function () {

        $('.select2').select2();
        /**
         * To fill the selected job's details
         *
         * */
        $('#hr-tracking-processes select[name="job_reassigned_id"]').select2().on('change', function () {
            id = $(this).val();
            if (Number(id) !== 0) {
                var url = '{{ route("candidate.get-job",":job_id") }}';
                url = url.replace(':job_id', id);
                $.ajax({
                    type: 'GET',
                    url: url,
                    dataType: 'json',
                    success: function (data) {
                        $('#tracking-form .client-reassigned-name').text(data.customer.client_name);
                        $('#tracking-form .client-reassigned-wage').text('$' + parseFloat(
                                data.wage_low).toFixed(2) + '-$' + parseFloat(data.wage_high)
                            .toFixed(2));
                    },
                    error: function () {}
                });
            } else {
                $('#tracking-form .client-reassigned-name,#tracking-form .client-reassigned-wage').text(
                    '');
            }
        });

        /**
         * To submit the tracking steps done
         *
         **/
        @can('hr-tracking')
        $('#hr-tracking-processes').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            $form.find('td').removeClass('has-error').find('.help-block').text('');
            var formData = new FormData($('#hr-tracking-processes')[0]);
            var url =
                "{{ route('candidate.track-store',[$candidateJob->candidate_id,$candidateJob->job_id]) }}";
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    console.log(data);
                    if (data.success) {
                        swal({
                            title: 'Success',
                            text: 'HR tracking step has been successfully updated',
                            icon: "success",
                            button: "OK",
                        }, function () {
                            window.location =
                                "{{ route('candidate.track',[$candidateJob->candidate_id,$candidateJob->job_id]) }}";
                        });
                    } else {
                        alert(data.message);
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
        @endcan


        @can('delete-hr-tracking')
        /**
         * Remove an hr tracking step
         *
         * */
        $('#hr-tracking-processes').on('click', '.delete', function (e) {
            step_id = $(this).data('id');
            job_id = $(this).data('job-id');
            candidate_id = $(this).data('candidate-id');
            var url =
                '{{route("candidate.remove-hr-tracking-step",[":job_id",":candidate_id",":step_id"])}}',
                url = url.replace(':job_id', job_id);
            url = url.replace(':step_id', step_id);
            url = url.replace(':candidate_id', candidate_id);
            swal({
                    title: "Are you sure?",
                    text: "You will not be able undo this action",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes",
                    showLoaderOnConfirm: true,
                    closeOnConfirm: false
                },
                function () {
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function (data) {
                            if (data.success) {
                                swal({
                                    title: 'Success',
                                    text: 'The HR tracking step has been successfully removed',
                                    icon: "success",
                                    button: "OK",
                                }, function () {
                                    location.reload();
                                });
                            } else {
                                alert(data.message);
                            }
                        },
                        error: function (xhr, textStatus, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                        },
                        contentType: false,
                        processData: false,
                    });
                });
        });
        @endcan

        $('#candidate-application-termincation-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            var url = "{{ route('candidate.terminate',$candidateJob->candidate_id) }}";
            var formData = new FormData($('#candidate-application-termincation-form')[0]);
            console.log(formData);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {

                        swal({
                                title: "Saved",
                                text: "Candidate application has been terminated",
                                type: "info",
                                showCancelButton: false,
                                showLoaderOnConfirm: true,
                                closeOnConfirm: true
                            },
                            function () {
                                $("#myModal").modal('hide');
                                location.reload();
                            });



                    } else {
                        alert(data);
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
    })

    function terminateApplication() {
        swal({
                title: "Are you sure?",
                text: "Are you sure to terminate this candidate application",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, terminate",
                showLoaderOnConfirm: true,
                closeOnConfirm: true
            },
            function () {
                openModal();
            });
    }

    function reactivationAjax(url) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: 'POST',
            success: function (data) {
                if (data.success) {

                    swal({
                            title: "Activated",
                            text: "Candidate application has been re-activated",
                            type: "info",
                            showCancelButton: false,
                            showLoaderOnConfirm: true,
                            closeOnConfirm: true
                        },
                        function () {
                            location.reload();
                        });
                } else {
                    alert(data);
                }
            },
            fail: function (response) {
                alert('here');
            },
            error: function (xhr, textStatus, thrownError) {
                //associate_errors(xhr.responseJSON.errors, $form);
            },
            contentType: false,
            processData: false,
        });
    }

    function reactivateApplication() {

        var url;
        swal({
                title: "Are you sure?",
                text: "Do you want to reset all information after activation?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function (isConfirm) {
                if (isConfirm) {
                    url = '{{ route("candidate.reactivate",array($candidateJob->candidate_id,TRUE)) }}';
                    reactivationAjax(url);
                } else {
                    url = "{{ route('candidate.reactivate',$candidateJob->candidate_id) }}";
                    reactivationAjax(url);
                }
            });

    }
</script>
@endsection
