@extends('layouts.app') @section('content')
<div class="table_title">
    <h4>Capacity Tool</h4>
</div>

@include('contracts::partials.form')

@endsection @section('scripts') @include('capacitytool::partials.script') @endsection
