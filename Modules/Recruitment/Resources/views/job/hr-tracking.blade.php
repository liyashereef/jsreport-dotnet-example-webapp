@extends('layouts.app')
@section('content')
<div class="table_title">
    <h4>Status Summary </h4>
</div>
@if($errors->any())
<h5 class="has-error">{{$errors->first()}}</h5>
@endif
<div class="table-responsive">
    <table class=" table table-bordered dataTable">
        <thead>
            <th>Job Id</th>
            <th>Area Manager</th>
            <th>Post Number</th>
            <th>Client</th>
            <th>Position Requested</th>
            <th>Posts</th>
            <th>Date Entered</th>
            <th>Rationale</th>
            <th>Position Type</th>
            <th>Date Required</th>
            <th>Wage </th>
            <th>HR Assigned</th>
        </thead>
        <tbody>
            <tr>
                <td>{{$job->unique_key}}</td>
                <td>{{$job->area_manager}}</td>
                <td>{{$job->customer->project_number}}</td>
                <td>{{$job->customer->client_name}}</td>
                <td>{{$job->positionBeeingHired->position}}</td>
                <td>{{$job->no_of_vaccancies}}</td>
                <td>{{$job->requisition_date}}</td>
                <td>{{$job->reason->reason}}</td>
                <td>{{$job->assignmentType->type}}</td>
                <td>{{$job->required_job_start_date}}</td>
                <td>${{ number_format($job->wage, 2) }}</td>
                <td>{{ucfirst(@$job->assignee->full_name)}}</td>
            </tr>
        </tbody>
    </table>
</div>
{{ Form::open(array('url'=>'#','id'=>'job-hr-tracking-form','method'=>'POST')) }}
<div class="table-responsive">
    <table class="table table-bordered dataTable" id="hr-tracking-processes">
        <thead>
            <th>Process</th>
            <th>Completion Date</th>
            <th>Notes</th>
            <th>Entered By</th>
        </thead>
        <tbody>
            @foreach($process_lookups as $key=>$each_process)
            <tr>
                <td>
                    {{$each_process->id.'. '.$each_process->process_name}}
                </td>
                @if(in_array($each_process->id,array_keys($already_processed_process_ids)))
                <td>
                    {{ Form::label('completion_date['.$each_process->id.']', $already_processed_process_ids[$each_process->id]->process_date) }}
                </td>
                <td>
                    {{ Form::label('notes['.$each_process->id.']',$already_processed_process_ids[$each_process->id]->process_note) }}
                </td>
                <td>
                    {!! ($already_processed_process_ids[$each_process->id]->enteredBy!=null)?$already_processed_process_ids[$each_process->id]->enteredBy->full_name:'<i>User Removed</i>' !!}
                    @can('rec-delete-hr-tracking') &nbsp;
                    <a title="Remove this entry" href="javascript:;" class="delete fa fa-trash" data-id="{{ $each_process->id }}" data-job-id="{{ $job->id }}"></a>
                    @endcan
                </td>
                @else
                <td id="{{ 'completion_date.'.$each_process->id }}">
                    {{ Form::text('completion_date['.$each_process->id.']',null,array('class' => 'datepicker form-control')) }}
                    <span class="help-block text-danger align-middle font-12"></span>
                </td>
                <td  id="{{ 'notes.'.$each_process->id }}">
                    {{ Form::textArea('notes['.$each_process->id.']',null,array('placeholder'=>"Notes",'cols'=>'30','rows'=>1,'class'=>'form-control')) }}
                    <span class="help-block text-danger align-middle font-12"></span>
                </td>
                <td  id="{{ 'entered_by_id.'.$each_process->id }}">
                    {{ Form::select('entered_by_id['.$each_process->id.']', [null=>'Please Select']+$users, null,array('class' => 'form-control select2'))}}
                    <span class="help-block text-danger align-middle font-12"></span>
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@can('rec-hr-tracking')
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 text-xs-center text-sm-center text-md-center text-lg-center text-xl-center margin-top-1">
    {{ Form::submit('Submit', array('class' => 'btn submit')) }}
    {{ Form::button('Cancel', array('class' => 'btn cancel','onclick'=>'window.history.back();')) }}
</div>
@endcan
{{ Form::close() }}
@endsection
@section('scripts')
<script>
    $(function () {
        @can('rec-hr-tracking')
        $('.select2').select2();
        $('#job-hr-tracking-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            var formData = new FormData($('#job-hr-tracking-form')[0]);
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('recruitment-job.hr-tracking',$job->id) }}",
                type: 'POST',
                data: formData,
                success: function (data) {
                    $('.dataTable').DataTable({
                        "bSort" : false,
                        "bPaginate": false,
                    });
                    if (data.success) {
                        swal({
                            title: 'Success',
                            text: 'HR tracking step has been successfully updated',
                            icon: "success",
                            button: "OK",
                        }, function () {
                            @can('rec-job-tracking-summary')
                                window.location = "{{ route('recruitment-job.hr-tracking-summary') }}";
                            @else
                                location.reload();
                            @endcan
                        });
                    } else {
                        alert(data.message);
                    }
                },
                fail: function (response) {
                    $('.dataTable').DataTable({
                        "bSort" : false,
                        "bPaginate": false,
                    });
                    alert('here');
                },
                error: function (xhr, textStatus, thrownError) {
                    $('.dataTable').DataTable({
                        "bSort" : false,
                        "bPaginate": false,
                    });
                    associate_errors(xhr.responseJSON.errors, $form);
                },
                contentType: false,
                processData: false,
            });
        });
        @endcan

        @can('rec-delete-hr-tracking')
        $('#hr-tracking-processes').on('click', '.delete', function (e) {
            step_id = $(this).data('id');
            job_id = $(this).data('job-id');
            var url = '{{route("recruitment-job.remove-hr-tracking-step",[":job_id",":step_id"])}}',
            url = url.replace(':job_id', job_id);
            url = url.replace(':step_id', step_id);
            swal({
                    title: "Are you sure?",
                    text: "You will not be able undo this action",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, remove",
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
    });
</script>
@stop
