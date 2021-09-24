@extends('layouts.candidate-layout')
@section('content')
<div class="container">
    <div class="row">
        <div class="tab-content pdf-content">
            @include('recruitment::job-application.pdf')
        </div>
    </div>
</div>
@stop
