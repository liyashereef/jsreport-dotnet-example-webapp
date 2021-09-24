@if(!$candidate->personality_scores->isEmpty())
	<table class='table'>
	@foreach($candidate->personality_scores->chunk(8) as $chunked_scores)
	<tr>
		@foreach($chunked_scores as $each_scores)
			@if($each_scores->order == 1)
				@php
					$primary_score = $each_scores->score;
					$description = $each_scores->score_type->description;
				@endphp
				<td class='gray'>Primary</td><td class='rating-color center'>{{$primary_score}}</td>
			@else
				<td class='gray'>Secondary</td><td class='rating-color center'>{{$each_scores->score}}</td>
			@endif
		@endforeach
	</tr>
	@endforeach
	<tr><td colspan="2"></td></tr>
	<tr>
		<td class='gray'>Secondary (If Applicable)</td><td class='rating-color center'>{{$primary_score}}</td>
	</tr>
	</table>
	{{-- {!!nl2br(e($description))!!} --}}
	{!!nl2br(($description))!!}
	@else
	No Personal Analysis Found
@endif
