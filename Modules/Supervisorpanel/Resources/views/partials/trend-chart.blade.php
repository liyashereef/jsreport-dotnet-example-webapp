@if(isset($chart))
<div class="row">        
 {!! $chart->html() !!}
</div>
@endif

	
@if(isset($chart))

{!!$chart->script() !!}

@endif

