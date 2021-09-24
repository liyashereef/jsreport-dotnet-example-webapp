@extends('adminlte::page') 
@section('title', 'Year to Date') 
@section('content_header')
<h1>Year To Date</h1>
@stop 
<style>
.ui-datepicker-year{
    display: none;
}

</style>
@section('content')
<div class="row">
   {{-- {!! session('password-updated') !!} --}}
   <!-- left column -->
   <div class="col-md-12">

       <!-- general form elements -->
       <div class="box box-primary">
           <!-- form start -->
           <form role="form" method="POST" action="{{ route('year-to-date.store') }}">
               {{ csrf_field() }} {{ method_field('POST') }}
               <div class="box-body">
                   <div class="form-group {{ $errors->has('year_to_date') ? 'has-error' : ''}}">
                    <div class="col-md-6">
                            <label for="year_to_date">Start Date</label>
                            {{ Form::text('year_to_date',old('year_to_date',$cs->year_to_date),array('id' => 'year_to_date','read-only' => 'true','class'=>'form-control datepicker-startdate','maxlength'=>'50','required'=>true))}} 
                            {!! $errors->first('year_to_date', '<small class="help-block">:message</small>') !!}
     
                    </div>   
                   </div>
               </div>
               <!-- /.box-body -->
               <div class="box-footer">
                   <button type="submit" class="btn btn-primary">@lang('Submit')</button>
               </div>
           </form>
       </div>
       <!-- /.box -->
   </div>
   <!--/.col (right) -->
   <!-- /.row -->
   @endsection
   @section('js')
   <script>
        $(function () {

            $(".datepicker").mask("99-99");
        });
   </script>
   @endsection