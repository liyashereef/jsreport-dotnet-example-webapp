<script type="text/javascript">
    $(function () {
        var {{ $model->id }} = new Highcharts.Chart({
            colors: [
                @foreach($model->colors as $c)
                    "{{ $c }}",
                @endforeach
            ],
            chart: {
                
            events: {
                click: function(event) {
                    chartClickHandle('{!! $model->title !!}');                    
                }
            },
                renderTo:  "{{ $model->id }}",
                @include('charts::_partials.dimension.js2')
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'column'
            },
            @if($model->title)
                title: {
                    text:  "{!! $model->title !!}"
                },
            @endif
            @if(!$model->credits)
                credits: {
                    enabled: false
                },
            @endif
            plotOptions: {
                series: {
                    colorByPoint: true,
                },
            },
            xAxis: {
                title: {
                    text: "{!! $model->x_axis_title !!}"
                },
                categories: [
                    @foreach($model->labels as $label)
                         "{!! $label !!}",
                    @endforeach
                ],
            },
            yAxis: {
                title: {
                    text: "{!! $model->y_axis_title === null ? $model->element_label : $model->y_axis_title !!}"
                },
            },
            legend: {
                @if(!$model->legend)
                    enabled: false,
                @endif
            },
            series: [{
                point: {
                    events: {
                        click: function() {
                            chartClickHandle('{!! $model->title !!}'); 
                            //console.log ('Category: '+ this.category +', value: '+ this.y);
                        }
                    }
                },
                name: "{!! $model->element_label !!}",
                data: [
                    @foreach($model->values as $dta)
                        {{ $dta }},
                    @endforeach
                ]
            }]
        })
    });
</script>

@if(!$model->customId)
    @include('charts::_partials.container.div')
@endif
