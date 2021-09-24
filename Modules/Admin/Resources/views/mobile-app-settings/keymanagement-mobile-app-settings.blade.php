@extends('adminlte::page')
@section('title', ' Key Management Mobile App Settings')
@section('content_header')
<h1>Key Management Mobile App Settings</h1>
@stop @section('content')
<div class="row">
   {{-- {!! session('settings-updated') !!} --}}
   <!-- left column -->
   <div class="col-md-12">

       <!-- general form elements -->

           <!-- form start -->
            {{ Form::open(array('url'=>'#','id'=>'keymanagement_app_settings','class'=>'form-horizontal', 'method'=> 'POST')) }}
                {{ csrf_field() }}
               <div class="box-body">
                     <div class="form-group" id="keymanagement_module_image_limit">
                        <label for="">Image Limit in Key Management Module</label>
                        {{ Form::text('keymanagement_module_image_limit',old('keymanagement_module_image_limit',$mobile_app_settings->keymanagement_module_image_limit),array('class'=>'form-control','maxlength'=>'50'))}}
                        <small class="help-block"></small>
                    </div>
               </div>
               <!-- /.box-body -->
               <div class="box-footer">
                   {{-- <button type="submit" class="btn btn-primary">@lang('Submit')</button> --}}
                   {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>''))}}
                </div>
           </form>

       <!-- /.box -->
   </div>
   <!--/.col (right) -->
   <!-- /.row -->
   @endsection
   @section('js')
       <script>
           $('#keymanagement_app_settings').submit(function (e) {
               e.preventDefault();
                 var $form = $(this);
                var formData = new FormData($('#keymanagement_app_settings')[0]);
                $.ajax({
                        headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                        url: "{{route('keymanagement.mobilesettings.store')}}",
                        type: 'POST',
                        data:  formData,
                        processData: false,
                        contentType: false,
                        success: function (data) {
                            if (data.success) {
                                swal("Success", "Mobile app settings has been successfully updated", "success");
                                 $('.form-group').removeClass('has-error').find('.help-block').text('');
                                //table.ajax.reload();
                            } else {
                                //alert(data);
                                swal("Alert", "Something went wrong", "warning");
                            }
                        },
                        error: function (xhr, textStatus, thrownError) {
                            console.log(xhr.status);
                            console.log(thrownError);
                            associate_errors(xhr.responseJSON.errors, $form);
                            swal("Oops", "Something went wrong", "warning");
                        },
                    });
            });

        </script>
   @endsection
