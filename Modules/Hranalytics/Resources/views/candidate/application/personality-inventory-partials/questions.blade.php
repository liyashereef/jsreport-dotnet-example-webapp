@if(!$candidateJob->candidate->personality_inventories->isEmpty())
	@foreach($candidateJob->candidate->personality_inventories as $each_question_anwer)
		<div class='form-group row'>
		<label class='col-sm-6 col-form-label'>{{$each_question_anwer->question->id}}) {{$each_question_anwer->question->question}}</label>
		<label class='col-sm-6 col-form-label'>{{ucfirst($each_question_anwer->answer->option)}}) {{$each_question_anwer->answer->value}}</label>
		</div>
	@endforeach
@else
	No Personality Inventory Questions Found
@endif
