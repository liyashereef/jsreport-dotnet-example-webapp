@extends('adminlte::page')
@section('title', 'Mobile App Settings')
@section('css')
    <style>
        .mb2{
            padding-bottom: 8px !important;
        }
    </style>
@endsection
@section('content_header')
<h1>Mobile App Settings</h1>
@stop @section('content')
<div class="row">
   {{-- {!! session('settings-updated') !!} --}}
   <!-- left column -->
   <div class="col-md-12">

       <!-- general form elements -->

           <!-- form start -->
            {{ Form::open(array('url'=>'#','id'=>'mobile_app_settings','class'=>'form-horizontal', 'method'=> 'POST')) }}
                {{ csrf_field() }}
               <div class="box-body">
                   <div class="form-group" id="time_interval">
                       <label for="time_interval">Time Interval (Minutes)</label>
                       {{ Form::text('time_interval',old('time_interval',$mobile_app_settings->time_interval),array('class'=>'form-control','maxlength'=>'50','required'=>true))}}
                      <small class="help-block"></small>
                   </div>
                   <div class="form-group" id="speed_limit">
                        <label for="speed_limit">Speed Limit (km/hr)</label>
                        {{ Form::text('speed_limit',old('speed_limit',$mobile_app_settings->speed_limit),array('class'=>'form-control','maxlength'=>'50','required'=>true))}}
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group" id="trip_show_speed">
                        <label for="">Minimum/Average Speed to List a Trip (km/hr)</label>
                        {{ Form::text('trip_show_speed',old('trip_show_speed',$mobile_app_settings->trip_show_speed),array('class'=>'form-control','maxlength'=>'50'))}}
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group" id="trip_show_distance">
                        <label for="">Minimum/Average Distance to List a Trip (km)</label>
                        {{ Form::text('trip_show_distance',old('trip_show_distance',$mobile_app_settings->trip_show_distance),array('class'=>'form-control','maxlength'=>'50'))}}
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group" id="average_speed_limit">
                        <label for="">Highlight in Red if the Average Speed is Above (km/hr)</label>
                        {{ Form::text('average_speed_limit',old('average_speed_limit',$mobile_app_settings->average_speed_limit),array('class'=>'form-control','maxlength'=>'50'))}}
                        <small class="help-block"></small>
                    </div>
                     <div class="form-group" id="shift_module_image_limit">
                        <label for="">Image Limit in Shift Module</label>
                        {{ Form::text('shift_module_image_limit',old('shift_module_image_limit',$mobile_app_settings->shift_module_image_limit),array('class'=>'form-control','maxlength'=>'50'))}}
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group" id="key_management_module_image_limit">
                        <label for="">Image Limit in Key Management Module</label>
                        {{ Form::text('key_management_module_image_limit',old('key_management_module_image_limit',$mobile_app_settings->key_management_module_image_limit),array('class'=>'form-control','maxlength'=>'50'))}}
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group" >
                    <label for="view_ura_balance">View URA balance</label>
                    <input type="checkbox" name="view_ura_balance" id="view_ura_balance" value="1"  style="margin-left: 2em;">
                    <span class="help-block"></span>
                    </div>
               </div>
               <div class="row mb2">
                <div class="col-md-3">

                </div>
                <div class="col-md-2"><label > No Of Days</label></div>
                <div class="col-md-2"><label >Background Color</label></div>
                <div class="col-md-2"><label >Font Color</label></div>
            </div>
            <div class="row mb2">
                <label for="grace_period" class="col-md-3 safe_period" id="label_--position_num--">

                Document Expiry Grace Period
                </label>
                <div class="col-md-2">
                    {{ Form::number('expiry_grace_in_days', isset($documentColorSettings->grace_period_in_days)?$documentColorSettings->grace_period_in_days:"",
                                array(
                                'class'=>'form-control expiry_grace_in_days',
                                'min'=>'1',
                                'max'=>'2000',
                                'id'=>"expiry_grace_in_days",
                                'placeholder'=>'Number of Days','required'=>true)) }}


                </div>
                <div class="col-md-2">
                    <input type="color" required
                    id="grace_period_color_code" name="grace_period_color_code" class="form-control " style="float: right"
                    onchange="clickColor(0, -1, -1, 5)" value="{{isset($documentColorSettings->grace_period_color_code)?$documentColorSettings->grace_period_color_code:""}}" style="width:85%;">


                </div>
                <div class="col-md-2">
                    <input type="color" required
                    id="grace_period_font_color_code" name="grace_period_font_color_code" class="form-control " style="float: right"
                    onchange="clickColor(0, -1, -1, 5)" value="{{isset($documentColorSettings->grace_period_font_color_code)?$documentColorSettings->grace_period_font_color_code:""}}" style="width:85%;">


                </div>

            </div>
            <div class="row mb2">
                <label for="alert_period" class="col-md-3 safe_period" id="label_--position_num--">

                Document Expiry Alert Period
                </label>
                <div class="col-md-2">
                    {{ Form::number('expiry_alert_in_days',  isset($documentColorSettings->alert_period_in_days)?$documentColorSettings->alert_period_in_days:"",
                                array(
                                'class'=>'form-control expiry_alert_in_days',
                                'min'=>'1',
                                'max'=>'2000',
                                'id'=>"expiry_alert_in_days",
                                'placeholder'=>'Number of Days','required'=>true)) }}


                </div>
                <div class="col-md-2">
                    <input type="color" required
                    id="alert_period_color_code" name="alert_period_color_code"
                    onchange="clickColor(0, -1, -1, 5)"  class="form-control" style="float: right"
                    value="{{isset($documentColorSettings->alert_period_color_code)?$documentColorSettings->alert_period_color_code:""}}" style="width:85%;">




                                 <small class="help-block"></small>

                </div>
                <div class="col-md-2">
                    <input type="color" required
                    id="alert_period_font_color_code" name="alert_period_font_color_code"
                    onchange="clickColor(0, -1, -1, 5)"  class="form-control" style="float: right"
                    value="{{isset($documentColorSettings->alert_period_font_color_code)?$documentColorSettings->alert_period_font_color_code:""}}" style="width:85%;">




                                 <small class="help-block"></small>

                </div>

            </div>
            <div class="row mb2">
                <label for="alert_period" class="col-md-3 safe_period" id="label_--position_num--">

                Document Overdue Alert Period (Less than 0)
                </label>
                <div class="col-md-2">
                    <input type="text" value="0" class="form-control" readonly />
                </div>
                <div class="col-md-2">
                    <input type="color" required
                    id="overdue_period_color_code" name="overdue_period_color_code"
                    onchange="clickColor(0, -1, -1, 5)"  class="form-control" style="float: right"
                    value="{{isset($documentColorSettings->overdue_period_color_code)?$documentColorSettings->overdue_period_color_code:""}}" style="width:85%;">




                                 <small class="help-block"></small>

                </div>
                <div class="col-md-2">
                    <input type="color" required
                    id="overdue_period_font_color_code" name="overdue_period_font_color_code"
                    onchange="clickColor(0, -1, -1, 5)"  class="form-control" style="float: right"
                    value="{{isset($documentColorSettings->overdue_period_font_color_code)?$documentColorSettings->overdue_period_font_color_code:""}}" style="width:85%;">




                                 <small class="help-block"></small>

                </div>

            </div>
            <div class="row mb2">
                <div class="col-md-3">

                </div>
                <div class="col-md-2"><label > % of Schedule Tolerance</label></div>
                <div class="col-md-2"><label >Background Color</label></div>
                <div class="col-md-2"><label >Font Color</label></div>
            </div>
            <div class="row mb2">
                <label for="schedule_grace_period_days" class="col-md-3 safe_period" id="label_--position_num--">

                Schedule Tolerance Grace Period  Configuration (%)
                </label>
                <div class="col-md-2">
                    {{ Form::number('schedule_grace_period_days', isset($documentColorSettings->schedule_grace_period_days)?$documentColorSettings->schedule_grace_period_days:"",
                                array(
                                'class'=>'form-control schedule_grace_period_days',
                                'min'=>'1',
                                'max'=>'99',
                                'id'=>"schedule_grace_period_days",
                                'placeholder'=>'Percentage %','required'=>true)) }}


                </div>
                <div class="col-md-2">
                    <input type="color" required
                    id="schedule_grace_period_color_code" name="schedule_grace_period_color_code" class="form-control " style="float: right"
                    onchange="clickColor(0, -1, -1, 5)" value="{{isset($documentColorSettings->schedule_grace_period_color_code)?$documentColorSettings->schedule_grace_period_color_code:""}}" style="width:85%;">


                </div>
                <div class="col-md-2">
                    <input type="color" required
                    id="schedule_grace_period_font_color_code" name="schedule_grace_period_font_color_code"
                     class="form-control " style="float: right"
                    onchange="clickColor(0, -1, -1, 5)" value="{{isset($documentColorSettings->schedule_grace_period_font_color_code)?$documentColorSettings->schedule_grace_period_font_color_code:""}}" style="width:85%;">


                </div>

            </div>
            <div class="row mb2">
                <label for="alert_period" class="col-md-3 safe_period" id="label_--position_num--">

                Schedule Tolerance Exceeded Configuration
                </label>
                <div class="col-md-2">



                </div>
                <div class="col-md-2">
                    <input type="color" required
                    id="schedule_alert_color_code" name="schedule_alert_color_code"
                    onchange="clickColor(0, -1, -1, 5)"  class="form-control" style="float: right"
                    value="{{isset($documentColorSettings->schedule_alert_color_code)?$documentColorSettings->schedule_alert_color_code:""}}" style="width:85%;">




                                 <small class="help-block"></small>

                </div>
                <div class="col-md-2">
                    <input type="color" required
                    id="schedule_alert_period_font_color_code" name="schedule_alert_period_font_color_code"
                    onchange="clickColor(0, -1, -1, 5)"  class="form-control" style="float: right"
                    value="{{isset($documentColorSettings->schedule_alert_period_font_color_code)?$documentColorSettings->schedule_alert_period_font_color_code:""}}" style="width:85%;">




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
          var data={!!json_encode($mobile_app_settings)!!};
          if(data.view_ura_balance){
                $("#view_ura_balance").prop("checked", true);
           }
           $('#mobile_app_settings').submit(function (e) {
               e.preventDefault();
                 var $form = $(this);
                var formData = new FormData($('#mobile_app_settings')[0]);
                $.ajax({
                        headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                        url: "{{route('mobilesettings.store')}}",
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
