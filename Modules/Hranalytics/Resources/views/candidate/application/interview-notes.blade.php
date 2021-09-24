<div id="interview" class="container-fluid tab-pane fade">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Interview notes
    </div>
    {{ Form::open(array('id'=>'interview-notes-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
    {{Form::hidden('candidate_id',$candidateJob->candidate->id)}}
    {{Form::hidden('job_id',$candidateJob->job_id)}}
    <div class="form-group row">
        <label class="col-sm-5 col-form-label">Interviewer</label>
        <div class="col-sm-7 {{ $errors->has('interviewer_id') ? 'has-error' : '' }}" id="interviewer_id">
            {{ Form::select('interviewer_id',[null=>'Please Select']+$interviewres,old('interviewer_id',@$interview_notes->interviewer_id),array('class'=> 'form-control interviewer','required'=>TRUE)) }}
            <div class="form-control-feedback">
                <small class="help-block"></small>
            </div>
        </div>
    </div>
    <div class="form-group row">
        <label for="interview_date" class="col-sm-5 col-form-label">Interview Date</label>
        <div class="col-sm-7 {{ $errors->has('interview_date') ? 'has-error' : '' }}" id="interview_date">
            {{ Form::text('interview_date',old('interview_date',@$interview_notes->interview_date),array('class' => 'form-control datepicker','required'=>TRUE,'readonly'=>"readonly"))}}
            <div class="form-control-feedback">
                <small class="help-block"></small>
            </div>
        </div>
    </div>
    <div class="form-group row">
        <label for="interview_notes" class="col-sm-5 col-form-label">Interview Notes</label>
        <div class="col-sm-7 {{ $errors->has('interview_notes') ? 'has-error' : '' }}" id="interview_notes">
            {{ Form::textarea('interview_notes',old('interview_notes',@$interview_notes->interview_notes),array('class' => 'form-control','required'=>TRUE))}}
            <div class="form-control-feedback">
                <small class="help-block"></small>
            </div>
        </div>
    </div>
    @can('candidate-add-interview-notes')

        <div id="download-application" class="row { (null==$interview_notes)?'hide-this-block':'' }}">
            <div class="col-sm-5 col-form-label"></div>
            <div class=" margin-bottom-5  col-md-7 col-sm-7 ">
                <button type="submit" class="yes-button padding-clear">
                    <div class="yes">
                        <span class="yestext">Submit</span>
                    </div>
                </button>

            </div>
        </div>
    @endcan
    {{ Form::close() }}
</div>
@section('scripts')
<script>
    $(function () {
        $('.interviewer').select2();
        $('#interview-notes-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            var formData = new FormData($('#interview-notes-form')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('candidate.interview') }}",
                type: 'POST',
                data: formData,
                success: function (data) {
                    $('#download-application').removeClass('hide-this-block');
                    swal({
                        title: 'Success',
                        text: 'Interview notes has been successfully saved',
                        icon: "success",
                        button: "OK",
                    });
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
</script>
@parent
@stop
