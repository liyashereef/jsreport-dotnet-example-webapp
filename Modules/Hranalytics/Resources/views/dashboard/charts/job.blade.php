@if(isset($charts['job']))
<div class="row">        
    @foreach($charts['job'] as $chart)
    <div class="col-md-12 col-xs-12 col-sm-12 col-lg-6">
        {!! $chart->html() !!}
    </div>
    @endforeach
</div>
@endif