@extends('layouts.app') @section('content')
<div class="table_title">
    <h4>Capacity Tool</h4>
</div>
{{ Form::open(array('id'=>'capacity-tool-form','class'=>'form-horizontal', 'method'=> 'POST' )) }}
@include('capacitytool::partials.form')
{{ Form::close() }}
@endsection @section('scripts') @include('capacitytool::partials.script') @endsection
