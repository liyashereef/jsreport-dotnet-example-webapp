@extends('layouts.app')
@section('content')

<div class="table_title">
    <h4>Candidates Onboarding Status - {{$candidateName}}</h4>
</div>
{{ Form::open(array('url'=>'','id'=>'hr-tracking-processes','method'=> 'POST')) }}
<input type="hidden" name="candidate_id" value="{{ $id }}">
{{csrf_field()}}
{{-- <div class="table-responsive" id="tracking-form">
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
</div> --}}
<div id="form-container">

    <div class="table-responsive">
        <table class="table table-bordered"  id="tracking-table">
            <thead>
                <th>No</th>
                <th>Process Step</th>
                <th>Completion Date</th>
                <th>Completion Time</th>
                <th width="40%" align="justify">Notes</th>
                <th>Entered By</th>
            </thead>
            <tbody>
                @foreach($lookups as $index=>$lookup)

                <tr>
                    <td>
                           {{ Form::hidden('tracking_lookup['.$lookup->id.']',$lookup->id) }}
                        {{$index+1}}
                    </td>
                    <td>
                        {{$lookup->display_name}}
                    </td>
                    @if(in_array($lookup->id,array_keys($already_processed_track_ids)))
                        <td>
                            {{ Form::label('completion_date['.$lookup->id.']',
                            isset($already_processed_track_ids[$lookup->id]->completed_date)
                            ?date('Y-m-d', strtotime($already_processed_track_ids[$lookup->id]->completed_date))
                            :'--'
                            ) }}
                        </td>
                        <td>
                            {{ Form::label('completion_time['.$lookup->id.']', isset($already_processed_track_ids[$lookup->id]->completed_date)
                            ?\Carbon\Carbon::parse($already_processed_track_ids[$lookup->id]->completed_date)->format('h:i A')
                            :'--'
                            )}}
                        </td>
                        <td>
                            @if($lookup->type==1)
                            {!! Form::label('notes['.$lookup->id.']',(@$already_processed_track_ids[$lookup->id]->notes)) !!}
                            @else
                            {!! Form::label('notes['.$lookup->id.']',(@$already_processed_track_ids[$lookup->id]->notes))!!}
                            @endif
                        </td>

                        <td>

                            @if($lookup->type==0)
                                {!!
                            ($already_processed_track_ids[$lookup->id]->entered_by!=null)?$already_processed_track_ids[$lookup->id]->enteredBy->full_name:'<i>User
                                Removed</i>' !!}
                                @can('delete-hr-tracking') &nbsp;
                                {{-- @if(!isset($candidateJob->candidate->termination)) --}}
                                <a title="Remove this entry" href="javascript:;" class="delete fa fa-trash" data-id="{{ $lookup->id }}"
                                data-candidate-id="{{ $already_processed_track_ids[$lookup->id]->candidate_id}}">
                                </a>
                            {{--  @endif --}}
                                @endcan
                            @else
                                System
                            @endif

                        </td>
                    @else
                        <td id="{{ 'completion_date.'.$lookup->id }}">
                            @if($lookup->type==1)
                            @else
                            {{
                                Form::text('completion_date['.$lookup->id.']',old('completion_date['.$lookup->id.']'),array('class'
                                => 'datepicker form-control')) }}
                                <span class="help-block text-danger align-middle font-12"></span>
                            @endif
                        </td>
                        <td>
                            @if($lookup->type==0)
                            {{-- {{
                                Form::text('completion_time['.$lookup->id.']', old(\Carbon\Carbon::now()->format('h:i A')),
                                array('placeholder'=>'Time (HH:MM AM/PM)','class'=>'form-control time','required'=>true, 'id' => 'completion_time['.$lookup->id.']')
                                )
                            }} --}}
                            <input type="text" name={{'completion_time['.$lookup->id.']'}} class="form-control timepicker" placeholder="Choose Time">
                            <span class="help-block text-danger align-middle font-12"></span>
                            @endif
                        </td>

                            @if($lookup->type==1)
                            <td id="{{ 'notes.'.$lookup->id }}">
                            {{-- {{
                            Form::textArea('notes['.$lookup->id.']',($lookup->notes),array('placeholder'=>"Notes",'cols'=>'30','rows'=>1,'class'
                            => 'form-control','readonly'=>true)) }} --}}
                            <span class="help-block text-danger align-middle font-12"></span>
                        </td>

                            <td >
                            {{-- {{ Form::select('entered_by_id['.$lookup->id.']', [null=>'--']+$users,
                            null,array('class' => 'form-control','readonly'=>true,'style'=>'pointer-events:none;')) }} --}}
                            <span class="help-block text-danger align-middle font-12"></span>
                        </td>
                    @else
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

                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
{{-- @can('hr-tracking') --}}
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 text-xs-center text-sm-center text-md-center text-lg-center text-xl-center margin-top-1">
   {{--  @if(!isset($candidateJob->candidate->termination)) --}}
   @if($showSave)
    {{ Form::submit('Save', array('class' => 'btn submit')) }}
   {{--  @endif --}}
   @endif
    {{ Form::button('Cancel', array('class' => 'btn cancel','onclick'=>'window.history.back();')) }}
</div>
{{-- @endcan --}}
{{ Form::close() }}
@endsection
@section('scripts')
<script>



    $('.timepicker').timepicki();
    $(function () {

        $('.select2').select2();
          var table = $('#tracking-table').DataTable( {
    paging: false
  });
        /**
         * To submit the tracking steps done
         *
         **/
        {{-- @can('hr-tracking') --}}
        $('#hr-tracking-processes').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            $form.find('td').removeClass('has-error').find('.help-block').text('');
            var formData = new FormData($('#hr-tracking-processes')[0]);
            var url ="{{ route('recruitment.candidate-track.store') }}";
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
                           location.reload();
                        });
                    } else {
                     //   alert(data.message);
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
        {{-- @endcan --}}


         /**
         * Remove an hr tracking step
         *
         * */
        $('#hr-tracking-processes').on('click', '.delete', function (e) {
            step_id = $(this).data('id');
            candidate_id = $(this).data('candidate-id');
            var url =
                '{{route("recruitment.candidate-track-delete",[":step_id",":candidate_id"])}}',
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
                              //  alert(data.message);
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


    })


</script>

<style>
    #tracking-table td, #tracking-table th {
        border: 1px solid #ffffff !important;
    }
#tracking-table td{
   position:relative;
}

.empidblock{
    display: none;
}
</style>
@endsection
