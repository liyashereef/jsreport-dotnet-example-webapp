@extends('adminlte::page')
@section('content_header')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@section('content')
<h1>Permissions</h1>
<div class="container-fluid container-wrap">
    {{ Form::open(array('url'=>'#','id'=>'permission-form','class'=>'form-horizontal', 'method'=> 'POST')) }}

    <section class="content">
        <div class="form-group"  id="module">
            <label for="module" class="col-form-label col-md-2">Module<span class="mandatory">*</span></label>
            <div class="col-md-7">
              {!!Form::select('module', [1=>'Admin',2=>'Hr Analytics',3=>'Time Tracker',4=>'Supervisor Panel'],null, ['class' => 'form-control col-sm-3'])!!}
                <span class="help-block"></span>
            </div>
        </div>
           <div class="form-group"  id="permission">
            <label for="role" class="col-form-label col-md-2">Permission<span class="mandatory">*</span></label>
            <div class="col-md-7">
              {!!Form::text('permission',null, ['class' => 'form-control col-sm-3'])!!}
                <span class="help-block"></span>
            </div>
        </div>
          <div class="form-group"  id="description">
            <label for="description" class="col-form-label col-md-2">Module Description<span class="mandatory">*</span></label>
            <div class="col-md-7">
             {!! Form::textarea('description',null,['class'=>'form-control col-sm-3', 'rows' => 2, 'cols' => 10]) !!}
                <span class="help-block"></span>
            </div>
        </div>
</section>
</div>
<div class="modal-footer">
    {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
    <button class="btn btn-primary blue" data-dismiss="modal" aria-hidden="true">Cancel</button>
    {{ Form::close() }}
</div>
@stop


@section('js')
<script>
 /* Posting data to AdminController - Start*/
        $('#permission-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            url = "{{ route('permission.store') }}";
            var formData = new FormData($('#permission-form')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                        swal("Saved", "The record has been saved", "success");

                    } else {
                        alert(data);
                    }
                },
                fail: function (response) {
                    alert('here');
                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form);
                },
                contentType: false,
                processData: false,
            });
        });
        /* Posting data to AdminController - End*/


        </script>
@stop
