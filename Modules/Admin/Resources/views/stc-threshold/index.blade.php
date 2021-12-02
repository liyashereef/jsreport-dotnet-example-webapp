@extends('adminlte::page')
@section('title', 'STC Threshold Settings')
@section('content_header')
<h1>STC Threshold Settings</h1>
@stop
@section('content')
<div class="row">
   <!-- left column -->
   <div class="col-md-12">

       <!-- general form elements -->

           <!-- form start -->
            {{ Form::open(array('url'=>'#','id'=>'stc_threshold_settings','class'=>'form-horizontal', 'method'=> 'POST')) }}
                {{ csrf_field() }}
               <div class="box-body" style="margin-left: 0px !important;">
                   <div class="col-md-10" style="padding-left: 0px !important;">
                        <div class="form-group row">
                            <div class="col-md-8"></div>
                            <div class="col-md-2 text-center"><b>Background Color</b></div>
                            <div class="col-md-2 text-center"><b>Font Color</b></div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <label class="form-control-label">Level 1(Number of days below or equal to this value will be indicated by the respective color)</label>
                            </div>
                            <div class="col-md-8" id="no_of_days_critical">
                                    {{ Form::number('no_of_days_critical',($stcThresholdSettings? $stcThresholdSettings->no_of_days_critical : null),array('class'=>'form-control no_of_days_critical','required'=>true, 'min' => 1))}}
                                    <small class="help-block"></small>
                                </div>
                            <div class="col-md-2" id="critical_days_color">
                                    {{ Form::color('critical_days_color',($stcThresholdSettings? $stcThresholdSettings->critical_days_color : null),array('class' => 'form-control form-control-color critical_days_color', 'id'=>'critical_days_color', 'Placeholder'=>'Color', 'required'=>TRUE)) }}
                                    <small class="help-block"></small>
                                </div>
                                <div class="col-md-2" id="critical_days_font_color">
                                {!!Form::select('critical_days_font_color', ['White'=>'White','Black'=>'Black'],($stcThresholdSettings? $stcThresholdSettings->critical_days_font_color : null), ['class' => 'form-control'])!!}
                                    <small class="help-block"></small>
                                </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <label class="form-control-label">Level 2(Number of days below or equal to this value will be indicated by the respective color)</label>
                            </div>
                            <div class="col-md-8" id="no_of_days_major">
                                    {{ Form::number('no_of_days_major',($stcThresholdSettings ? $stcThresholdSettings->no_of_days_major :null),array('class'=>'form-control no_of_days_major','required'=>true, 'min' => 1))}}
                                    <small class="help-block"></small>
                                </div>
                            <div class="col-md-2" id="major_days_color">
                                    {{ Form::color('major_days_color',($stcThresholdSettings ? $stcThresholdSettings->major_days_color: null),array('class' => 'form-control form-control-color major_days_color', 'id'=>'major_days_color', 'Placeholder'=>'Color', 'required'=>TRUE)) }}
                                    <small class="help-block"></small>
                                </div>
                                <div class="col-md-2" id="critical_days_font_color">
                                {!!Form::select('major_days_font_color', ['White'=>'White','Black'=>'Black'],($stcThresholdSettings? $stcThresholdSettings->major_days_font_color : null), ['class' => 'form-control'])!!}
                                    <small class="help-block"></small>
                                </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <label class="form-control-label">Level 3(Number of days above to this value will be indicated by the respective color)</label>
                            </div>
                            <div class="col-md-8" id="no_of_days_minor">
                                    {{ Form::number('no_of_days_minor',($stcThresholdSettings? $stcThresholdSettings->no_of_days_minor: null),array('class'=>'form-control no_of_days_minor','required'=>true, 'readonly' => 'readonly', 'min' => 1))}}
                                    <small class="help-block"></small>
                                </div>
                            <div class="col-md-2" id="minor_days_color">
                                    {{ Form::color('minor_days_color',($stcThresholdSettings? $stcThresholdSettings->minor_days_color: null),array('class' => 'form-control form-control-color minor_days_color', 'id'=>'minor_days_color', 'Placeholder'=>'Color', 'required'=>TRUE)) }}
                                    <small class="help-block"></small>
                                </div>
                                <div class="col-md-2" id="critical_days_font_color">
                                {!!Form::select('minor_days_font_color', ['White'=>'White','Black'=>'Black'],($stcThresholdSettings? $stcThresholdSettings->minor_days_font_color : null), ['class' => 'form-control'])!!}
                                    <small class="help-block"></small>
                                </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-4" id="stc_threshold_hours">
                            <label class="form-control-label">Minimum hours b/w shifts</label>
                                    {{ Form::number('stc_threshold_hours',($stcThresholdSettings? $stcThresholdSettings->stc_threshold_hours: null),array('class'=>'form-control stc_threshold_hours','required'=>true, 'min' => 1))}}
                                    <small class="help-block"></small>
                            </div>
                        </div>
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
            $('.no_of_days_major').on('keyup', function(){
                var no_of_days_major = parseInt($('input[name=no_of_days_major]').val());
                $('input[name=no_of_days_minor]').val(no_of_days_major);
            });

           $('#stc_threshold_settings').submit(function (e) {
            e.preventDefault();
            var no_of_days_critical = parseInt($('input[name=no_of_days_critical]').val());
            var no_of_days_major = parseInt($('input[name=no_of_days_major]').val());
            var no_of_days_minor = parseInt($('input[name=no_of_days_minor]').val());
            if(no_of_days_critical == 0  || no_of_days_critical == "" || no_of_days_major == 0  || no_of_days_major == "" || no_of_days_minor == 0  || no_of_days_minor == "") {
                swal("Oops", "Number of Days should grater than zero", "error");
                return false;
            }else if(no_of_days_major <= no_of_days_critical) {
                swal("Oops", "Major level should be greater than critical level value", "error");
                return false;
            }
            var $form = $(this);
            var formData = new FormData($('#stc_threshold_settings')[0]);
            $.ajax({
                    headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                    url: "{{route('stc_threshold.store')}}",
                    type: 'POST',
                    data:  formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        if (data.success) {
                            swal("Success", "Stc threshold settings has been successfully updated", "success");
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
