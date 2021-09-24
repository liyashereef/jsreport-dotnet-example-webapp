@if(!$candidateJob->candidate->personality_sums->isEmpty())
	<div class="form-group" id='personality_inventory_score'>
		<table class='table center' id='scoring-table'>
			<thead>
				<tr>
					<th class='no-border'></th>
		        	@foreach($candidateJob->candidate->personality_sums as $id=>$each_column)
			        	@if($id % 2 == 0)
				            <th class="center gray head-border"></th>
				            <th colspan="2" class="center blue gray head-border">Column {{$each_column->column}}</th>
			            @endif
		            @endforeach
		            <th class="center gray head-border"></th>
		        </tr>
		        <tr>
		        	<th class='no-border'></th>
		        	<th class='gray head-border'></th>
		        	@foreach($candidateJob->candidate->personality_sums as $id=>$each_column)
		            	@if($each_column->option == 'a')
				        	<th class="center blue gray head-border">{{ucfirst($each_column->option)}}</th>
						@else
							<th class="center blue gray head-border">{{ucfirst($each_column->option)}}</th>
							<th class='gray head-border'></th>
						@endif
					@endforeach
			    </tr>
	    	</thead>
	    	<tbody>
				@foreach($candidateJob->candidate->personality_inventories->chunk(count($candidateJob->candidate->personality_sums)/2) as $chunked_row)
					<tr>
						<th class='no-border'></th>
						@foreach($chunked_row as $each_row)
							<th class="blue gray">{{$each_row->question_id}}</th>
							@if($each_row->answer->option == 'a')
								<td>1</td>
								<td>0</td>
							@else
								<td>0</td>
								<td>1</td>
							@endif
						@endforeach
						<td class="center gray"></td>
					</tr>
				@endforeach
	    		<tr>
	    		<td class='blue no-border'>Totals</td>
	    		<td class='gray no-border'></td>
				@foreach($candidateJob->candidate->personality_sums as $id=>$each_column)
					@if($each_column->option == 'a')
						<td class='gray'>{{$each_column->sum}}</td>
					@else
						<td class='gray'>{{$each_column->sum}}</td>
						<td class='gray'></td>
					@endif
				@endforeach
				</tr>
				<tr>
				<td class="blue no-border"></td>
				<td class="no-border"></td>
				@php
					$column_sum = []
				@endphp
				@foreach($candidateJob->candidate->personality_sums as $id=>$each_column)
					@if($each_column->column == 1)
						@if($each_column->option == 'a')
							<td class='total no-border'>{{$each_column->sum}}</td>
						@else
							<td class='total no-border'>{{$each_column->sum}}</td>
							<td class="no-border"></td>
						@endif
					@elseif($each_column->column % 2 == 0)
						@if($each_column->option == 'a')
							@php
								$column_sum['a'] = $each_column->sum
							@endphp
							<td colspan='2' class='total no-border'>Total</td>
							<td class="no-border"></td>
						@else
							@php
								$column_sum['b'] = $each_column->sum
							@endphp
						@endif
					@else
						@if($each_column->option == 'a')
							<td class='total no-border'>{{$column_sum['a']+$each_column->sum}}</td>
						@else
							<td class='total no-border'>{{$column_sum['b']+$each_column->sum}}</td>
							<td class="no-border"></td>
						@endif
					@endif
				@endforeach
				</tr>
				<tr>
	    		<td class="center blue">Primary</td>
	    		<td></td>
				<td>E</td><td>I</td><td colspan='4'></td><td>S</td><td>N</td><td colspan='4'><td>T</td><td>F</td><td colspan='4'><td>J</td><td>P</td><td colspan='4'>
				</tr>
				@foreach($candidateJob->candidate->personality_scores as $each_scores)
				<tr>
					<td>{{$each_scores->order == 1 ? 'Primary Score' : 'Secondary Score'}}</td>
					<td></td>
					<td colspan='2'>{{$each_scores->EI}}</td>
					<td colspan='4'>
					<td colspan='2'>{{$each_scores->SN}}</td>
					<td colspan='4'>
					<td colspan='2'>{{$each_scores->TF}}</td>
					<td colspan='4'>
					<td colspan='2'>{{$each_scores->JP}}</td>
				</tr>
				@endforeach
				@foreach($candidateJob->candidate->personality_scores as $each_scores)
				<tr class='rating-color'>
					@if($each_scores->order == 1)
						<td>Primary Rating</td><td class="center" colspan='21'>{{$each_scores->score}}</td>
					@else
						<td>Alternative Rating</td><td class="center" colspan='21'>{{$each_scores->score}}</td>
					@endif
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
@else
	No Scoring Found
@endif
<style>
	#scoring-table td{
		border-bottom: none;
	}
	.no-border{
		border-top: none !important;
		border-bottom: none !important;
	}
	.head-border
	{
		border-bottom: 1px solid #cdcdcd !important;
	}
</style>
