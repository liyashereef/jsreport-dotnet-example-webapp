@extends('layouts.app') @section('content')
<div class="table_title">
    <h4>Contracts Management Upload Form</h4>
</div>
<div class="form-group">

@include('contracts::partials.form')

</div>
@endsection @section('scripts') @include('contracts::partials.script') @endsection

