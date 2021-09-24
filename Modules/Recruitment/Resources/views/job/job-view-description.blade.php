@extends('layouts.app')
@section('content')
<div class="table_title">
    <h4>Job Description </h4>
</div>
{!! $job->job_description !!}
@endsection