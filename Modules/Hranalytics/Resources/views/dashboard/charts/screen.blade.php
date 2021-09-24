@if(isset($charts['candidate_screen']))
<div class="row">
    @foreach($charts['candidate_screen'] as $chart)
    <div class="col-md-12 col-xs-12 col-sm-12 col-lg-6">
        {!! $chart->html() !!}
    </div>
    @endforeach    
</div>
@endif