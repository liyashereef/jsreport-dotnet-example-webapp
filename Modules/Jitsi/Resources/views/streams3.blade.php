@extends('layouts.app')
@section('content')
<div class="container_fluid">
    <div class="row">
        <div class="col-md-10 table_title">
            <h4>Recordings</h4>
     </div>
    </div>
    @foreach ($filearray as $key=>$value)
        
    
    <div class="row">
        <div class="col-md-12">
            <video controls src="{{$value}}" width="100%"></video>
        </div>
    </div>
    @endforeach
</div>
   
 
@endsection
