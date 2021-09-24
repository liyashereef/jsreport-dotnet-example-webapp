 @extends('adminlte::page') 
 @section('title', 'Candidate Generic Password for Job Application') 
 @section('content_header')
<h1>Generic Password for Candidates</h1>
@stop @section('content')
<div class="row">
    {!! session('password-updated') !!}
    <!-- left column -->
    <div class="col-md-12">

        <!-- general form elements -->
        <div class="box box-primary">
            <!-- form start -->
            <form role="form" method="POST" action="{{ route('settings.genericpwdstore') }}">
                {{ csrf_field() }} {{ method_field('POST') }}
                <div class="box-body">
                    <div class="form-group {{ $errors->has('password') ? 'has-error' : ''}}">
                        <label for="password">@lang('Set or Update generic password for candidates')</label>
                        {{ Form::text('password',old('password',$cs->generic_password),array('class'=>'form-control','maxlength'=>'50','required'=>true))}} 
                        {!! $errors->first('password', '<small class="help-block">:message</small>') !!}
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