@extends('layouts.candidate-layout')
@section('content')
<div class="container">
    <div class="row">
        <div class="tab-content pdf-content">
            @include('hranalytics::job-application.pdf')
        </div>
    </div>
</div>
@stop
