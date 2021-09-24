@extends('adminlte::page')
@section('title', 'Site Settings')
@section('content_header')
<h1>Site Settings</h1>
@stop @section('content')
<div class="row">
   {{-- {!! session('settings-updated') !!} --}}
   <!-- left column -->
   <div class="col-md-12">

       <!-- general form elements -->

           <!-- form start -->
            {{ Form::open(array('url'=>'#','id'=>'site_settings','class'=>'form-horizontal', 'method'=> 'POST')) }}
                {{ csrf_field() }}
               <div style="margin-left:15px;" class="box-body">
                   <div class="form-group" id="shift_duration_limit">
                       <label for="shift_duration_limit">Shift Duration (Hours)</label>
                       {{ Form::text('shift_duration_limit',old('shift_duration_limit',$site_settings->shift_duration_limit),array('class'=>'form-control','required'=>true))}}
                      <small class="help-block"></small>
                   </div>
                   <div class="form-group" id="shift_start_time_tolerance">
                       <label for="shift_start_time_tolerance">Shift Start Time Tolerance (Minutes)</label>
                       {{ Form::text('shift_start_time_tolerance',old('shift_start_time_tolerance',$site_settings->shift_start_time_tolerance),array('class'=>'form-control'))}}
                       <small class="help-block"></small>
                   </div>
                   <div class="form-group" id="shift_start_time_tolerance">
                       <label for="shift_start_time_tolerance">Shift End Time Tolerance (Minutes)</label>
                       {{ Form::text('shift_end_time_tolerance',old('shift_end_time_tolerance',$site_settings->shift_end_time_tolerance),array('class'=>'form-control'))}}
                       <small class="help-block"></small>
                   </div>
                   <div class="form-group" id="shift_start_time_tolerance">
                        <label for="daily_heathscreen_to">Daily health Screen Reports to</label>
                        <select name="daily_heathscreen_to[]" id="daily_heathscreen_to" class="select2 form-control" multiple>
                            @foreach ($users as $user)
                                <option
                                @if(in_array($user->id,$reportUsers))
                                    selected
                                @endif
                                value="{{$user->id}}">{{$user->getFullNameAttribute()}}</option>
                            @endforeach
                        </select>
                        <small class="help-block"></small>
                    </div>

                    <div class="form-group" id="shift_duration_limit">
                   {{-- <button type="submit" class="btn btn-primary">@lang('Submit')</button> --}}
                   {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>''))}}
                   </div>
                </div>
               </div>
               <!-- /.box-body -->
           </form>

       <!-- /.box -->
   </div>
   <!--/.col (right) -->
   <!-- /.row -->
   @endsection
   @section('js')
       <script>
           $('#site_settings').submit(function (e) {
               e.preventDefault();
                 var $form = $(this);
                var formData = new FormData($('#site_settings')[0]);
                $.ajax({
                        headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                        url: "{{route('sitesettings.store')}}",
                        type: 'POST',
                        data:  formData,
                        processData: false,
                        contentType: false,
                        success: function (data) {
                            if (data.success) {
                                swal("Success", "Site settings has been successfully updated", "success");
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

            $(document).ready(function () {
                $("#daily_heathscreen_to").select2()
            });

        </script>
   @endsection
