@extends('adminlte::page')
@section('title', 'QR Patrol Widget Settings')
@section('content_header')
<h1>QR Patrol Widget Settings</h1>
@stop @section('content')
<div class="row">
   {{-- {!! session('settings-updated') !!} --}}
   <!-- left column -->
   <div class="col-md-12">

       <!-- general form elements -->

           <!-- form start -->
            {{ Form::open(array('url'=>'#','id'=>'qr_patrol_settings','class'=>'form-horizontal', 'method'=> 'POST')) }}
                {{ csrf_field() }}
               <div class="box-body">
                   <div class="form-group" id="days_prior">
                       <label for="days_prior">Tolerence days</label>
                       {{ Form::number('days_prior',old('days_prior',$qrPatrolSettings->days_prior),array('class'=>'form-control','maxlength'=>'14','required'=>true))}}
                      <small class="help-block"></small>
                   </div>

                   <div class="form-group" id="critical_level_percentage">
                       <label for="critical_level_percentage">Critical Level (Percentage which below to this value will be showing in red color)</label>
                       {{ Form::number('critical_level_percentage',old('critical_level_percentage',$qrPatrolSettings->critical_level_percentage),array('class'=>'form-control','maxlength'=>'100','required'=>true))}}
                      <small class="help-block"></small>
                   </div>

                   <div class="form-group" id="acceptable_level_percentage">
                       <label for="acceptable_level_percentage">Acceptable Level (Percentage which above or equal to this value will be showing in green color)</label>
                       {{ Form::number('acceptable_level_percentage',old('acceptable_level_percentage',$qrPatrolSettings->acceptable_level_percentage),array('class'=>'form-control','maxlength'=>'100','required'=>true))}}
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
           $('#qr_patrol_settings').submit(function (e) {
            e.preventDefault();
            var acceptable_level_percentage = ($('input[name=acceptable_level_percentage]').val())? parseInt($('input[name=acceptable_level_percentage]').val()): 0;
            var critical_level_val = ($('input[name=critical_level_percentage]').val())? parseInt($('input[name=critical_level_percentage]').val()): 0;
            if(acceptable_level_percentage == 0) {
                swal("Oops", "Acceptable level value should grater than zero", "error");
                return false;
            }else if(acceptable_level_percentage < critical_level_val) {
                swal("Oops", "Acceptable level should be greater than or equal to critical level value", "error");
                return false;
            }
            var $form = $(this);
            var formData = new FormData($('#qr_patrol_settings')[0]);
            $.ajax({
                    headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                    url: "{{route('qr-patrol-settings.store')}}",
                    type: 'POST',
                    data:  formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        if (data.success) {
                            swal("Success", "QR patrol settings has been successfully updated", "success");
                                $('.form-group').removeClass('has-error').find('.help-block').text('');
                        } else {
                            swal("Alert", "Something went wrong", "warning");
                        }
                    },
                    error: function (xhr, textStatus, thrownError) {
                        associate_errors(xhr.responseJSON.errors, $form, true);
                        swal("Oops", "Something went wrong", "warning");
                    },
                });
            });

        </script>
   @endsection
