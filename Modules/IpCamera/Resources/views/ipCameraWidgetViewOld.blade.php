@extends('layouts.cgl360_layout')
@section('content')
    <div class="content ip-cam-content" style="height: 100%">
        @for($i = 1, $c = 0; $i <= $camFormat['row']; $i++)
            <div class="row ip-cam-row" style="height: {{100/$camFormat['row']}}%">
                @for($j = 1; $j <= 12/$camFormat['col'.$i] && $c < count($camArr); $j++, $c++)
                <div class="col-md-{{$camFormat['col'.$i]}}">
                    <div class="row">
                        {{$camArr[$c]['name']}}
                    </div>
                    <div class="row" style="height: 100%">
                        <iframe src="{{$camArr[$c]['url']}}" style="height: 100%;width: 100%"></iframe>
                    </div>
                </div>
                @endfor
            </div>
            <br/>
        @endfor
    </div>
@stop
