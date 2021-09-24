@extends('layouts.app') @section('content')
<div class="table_title">
    <h4>Short Term Contracts </h4>
</div>
{{ Form::open(array('id'=>'stc-form','class'=>'form-horizontal', 'method'=> 'POST' )) }} 
@include('hranalytics::short-term-contracts.partials.form')
{{ Form::close() }}
@endsection @section('scripts') @include('hranalytics::short-term-contracts.partials.script') @endsection