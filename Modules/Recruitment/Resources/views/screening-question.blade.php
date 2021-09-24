
{{ Form::open(array('id'=>'screening-questions-scoring-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
{{Form::hidden('candidate_id',$candidate->id)}}
<?php $already_shown = null;?>

@foreach($candidate->screening_questions as $screening_question)
@if($screening_question->question->category==null || $screening_question->question->category!=$already_shown)
<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-4">{{ $screening_question->question->category }}</label>
<?php $already_shown = $screening_question->question->category;?>
@endif
<div class="form-group row">
    <span class="col-sm-4 col-form-label">{{$screening_question->question->screening_question}}</span>
    <div class="col-sm-4">
        <label class="form-control">{{$screening_question->answer}}</label>
        <div class="form-control-feedback"></div>
    </div>
    <div class="col-sm-4">
        {{ Form::select('score[' .$screening_question->question_id. ']',[0=>'Select',"Poor"=>"Poor","Fair"=>"Fair","Average"=>"Average","Good"=>"Good","Excellent"=>"Excellent"],old('score'.$screening_question->question_id,isset($screening_question->score) ? $screening_question->score :""),array('class' => 'form-control score','onchange'=>"findAverageScore()")) }}
    </div>
</div>
@endforeach
<div class="text-center">
    <div class="form-group row">
        <label class="col-sm-4 text-right">Total:</label>
        <div class="col-sm-4"> {{Form::text('total',old('total',$candidate->awareness->average_score),array('id'=>"total",'class' => 'form-control',"readonly"))}}</div>
    </div>
</div>
<div class="text-center margin-bottom-5">
    <div class="form-group row">
        <label class="col-sm-4 text-right">Please rate candidate's proficiency in English language:</label>
        <div class="col-sm-4">
                {{ Form::select('english_rating_id',[null=>'Please Select']+$lookups['english_ratings'],old('english_rating_id',isset($candidate->awareness->english_rating_id) ? $candidate->awareness->english_rating_id :""),array('class' => 'form-control','id' => 'engilshrating')) }}
        </div>
    </div>
</div>
@if(!$candidate->tracking->isEmpty())
<div class="text-center">
    <div class="form-group row">
        <label class="col-sm-4 text-right">Enter candidate interview score:</label>
        <div class="col-sm-4">
                {{ Form::select('interview_score',[null=>'Please Select']+$lookups['employee_ratings'],old('interview_score',isset($candidate->awareness->interview_score) ? $candidate->awareness->interview_score :""),array('class' => 'form-control','id' => 'interviewscore')) }}
        </div>

            <label class="col-sm-1">Interview Date:</label>
        <div class="col-sm-2">
            {{ Form::text('interview_date',old('interview_date',isset($candidate->awareness->interview_date) ? $candidate->awareness->interview_date :""),array('class' => 'form-control datepicker','id' => 'interview_date', 'placeholder' => 'Interview Date')) }}
    </div>
    </div>
</div>
<div class="text-center margin-bottom-5">
    <div class="form-group row">
        <label class="col-sm-4 text-right">Enter candidate interview notes:</label>
        <div class="col-sm-4">
        {{Form::textarea('interview_notes',old('interview_notes',isset($candidate->awareness->interview_notes) ? $candidate->awareness->interview_notes :""),array('class'=>'form-control','id'=>'interview_notes','placeholder'=>"Please elaborate in detail ", 'maxlength'=>"500",'rows'=>6))}}
        </div>
    </div>
</div>
<div class="text-center">
    <div class="form-group row">
        <label class="col-sm-4 text-right">Enter candidate reference score:</label>
        <div class="col-sm-4">
                {{ Form::select('reference_score',[null=>'Please Select']+$lookups['employee_ratings'],old('reference_score',isset($candidate->awareness->reference_score) ? $candidate->awareness->reference_score :""),array('class' => 'form-control','id' => 'referencescore')) }}
        </div>
        <label class="col-sm-1 nowrap">Reference Date:</label>
        <div class="col-sm-2">
            {{ Form::text('reference_date',old('reference_date',isset($candidate->awareness->reference_date) ? $candidate->awareness->reference_date :""),array('class' => 'form-control datepicker','id' => 'reference_date', 'placeholder' => 'Reference Date')) }}
    </div>
    </div>
</div>
<div class="text-center margin-bottom-5">
    <div class="form-group row">
        <label class="col-sm-4 text-right">Enter candidate reference notes:</label>
        <div class="col-sm-4">
        {{Form::textarea('reference_notes',old('reference_notes',isset($candidate->awareness) ? $candidate->awareness->reference_notes :""),array('class'=>'form-control','placeholder'=>"Please elaborate in detail ",'id'=>'reference_notes', 'maxlength'=>"500",'required'=>FALSE,'rows'=>6))}}
        </div>
    </div>
</div>
@endif
@can('candidate-rate-screening-question-answers')
{{-- @if($candidateJob->candidate_status == 'Proceed') --}}
<div class="text-center margin-bottom-5">
    <button type="submit" class="yes-button" disabled="true"><div class="yes"><span class="yestext">Save</span></div></button>
</div>
{{-- @endif --}}
@endcan
{{ Form::close() }}
@section('scripts')
<script>
    /**
     * To find out average score
     *
     **/
    function findAverageScore(score) {
        var total_score = 0;
        var average_score = 0;
        var value = 0;
        $('.score').each(function (index) {
            switch ($(this).val())
            {
                case "Poor":
                    value = 1;
                    break;
                case "Fair":
                    value = 2;
                    break;
                case "Average":
                    value = 3;
                    break;
                case "Good":
                    value = 4;
                    break;
                case "Excellent":
                    value = 5;
                    break;
            }
            total_score += value;
        });
        average_score = total_score / parseInt($('.score').length);
        $('#total').val(average_score.toFixed(2));
    }
    $(document).ready(function () {
        @can('candidate-rate-screening-question-answers')

        $('#screening-questions-scoring-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            var formData = new FormData($('#screening-questions-scoring-form')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('recruitment.candidate.review.answers') }}",
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                        swal("Saved", "Status has been updated", "success");
                        $('.nav-tabs > .active').removeClass('active').addClass('success').next('li').addClass('active').find('a').removeClass('disabled').trigger('click');
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

        @endcan
    });
</script>

@parent
@stop
