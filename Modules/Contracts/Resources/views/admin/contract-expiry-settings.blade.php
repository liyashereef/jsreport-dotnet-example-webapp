@extends('adminlte::page')
@section('title', 'Contract Expiry Settings')
@section('content_header')
<h1>Contract Expiry Settings</h1>
@stop
@section('content')
<style>
.btn{
margin-left: 15px;
}
</style>

<div class="row">

   <div class="col-md-12">

           <!-- form start -->
            {{ Form::open(array('url'=>'#','id'=>'contract_expiry_settings','class'=>'form-horizontal', 'method'=> 'POST')) }}
                {{ csrf_field() }}
               <div style="margin-left:-10px;" class="box-body">

                 <div class="form-group col-sm-12 row">


                    <div  id="alert_period_1">
                    <label for="alert_period_1" class="col-sm-4">Number of days prior to due date for sending reminder email 1 </label>
                    <div class="col-sm-2">
                    {{ Form::text('alert_period_1',old('alert_period_1',$contract_expiry['alert_period_1']),array('class'=>'form-control','required'=>true))}}
                    <small class="help-block"></small>
                    </div>
                    </div>
                    <div id="email_1_time">
                    <label for="email_1_time" class="col-sm-2">Time for sending email reminder 1</label>
                    <div class="col-sm-2">
                    {{ Form::text('email_1_time',$email_time['email_1_time'],array('class'=>'form-control timepicker','placeholder' => 'Email 1 Time')) }}
                    <small class="help-block"></small>
                    </div>
                    </div>
                 </div>

                 <div class="form-group col-sm-12 row">
                    <div id="alert_period_2">
                    <label for="alert_period_2" class="col-sm-4" >Number of days prior to due date for sending reminder email 2</label>
                    <div class="col-sm-2">
                    {{ Form::text('alert_period_2',old('alert_period_2',$contract_expiry['alert_period_2']),array('class'=>'form-control','required'=>true))}}
                    <small class="help-block"></small>
                    </div>
                    </div>
                    <div id="email_2_time">
                    <label for="email_2_time" class="col-sm-2">Time for sending email reminder 2</label>
                    <div class="col-sm-2">
                    {{ Form::text('email_2_time',$email_time['email_2_time'],array('class'=>'form-control timepicker','placeholder' => 'Email 2 Time')) }}
                    <small class="help-block"></small>
                    </div>
                    </div>
                 </div>

                 <div class="form-group col-sm-12 row">
                    <div  id="alert_period_3">
                    <label for="alert_period_3" class="col-sm-4">Number of days prior to due date for sending reminder email 3</label>

                    <div class="col-sm-2">
                    {{ Form::text('alert_period_3',old('alert_period_3',$contract_expiry['alert_period_3']),array('class'=>'form-control','required'=>true))}}
                    <small class="help-block"></small>
                    </div>
                    </div>
                    <div id="email_3_time">
                    <label for="email_3_time" class="col-sm-2">Time for sending email reminder 3</label>
                    <div class="col-sm-2">
                    {{ Form::text('email_3_time',$email_time['email_3_time'],array('class'=>'form-control timepicker','placeholder' => 'Email 3 Time')) }}
                    <small class="help-block"></small>
                    </div>
                    </div>
                 </div>



                   <div class="form-group">
                   {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>''))}}
                   </div>
               </div>
               <!-- /.box-body -->
           </form>
   </div>

@stop
@section('js')
<script>
            $(function () {
                  $('.timepicker').timepicki();
                  $('.timepicker_hours').timepicki({show_meridian:false});
            });
           $('#contract_expiry_settings').submit(function (e) {
               e.preventDefault();
                 var $form = $(this);
                var formData = new FormData($('#contract_expiry_settings')[0]);
                $.ajax({
                        headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                        url: "{{route('contract-expiry-settings.store')}}",
                        type: 'POST',
                        data:  formData,
                        processData: false,
                        contentType: false,
                        success: function (data) {
                            if (data.success) {
                                swal("Success", "Contract Expiry settings has been successfully updated", "success");
                                 $('.form-group').removeClass('has-error').find('.help-block').text('');
                                //table.ajax.reload();
                            } else {
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
        <script src="{{ asset('js/timepicki.js') }}"></script>
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
        <link rel='stylesheet' type='text/css' href='{{ asset('css/timepicki.css') }}' />

@stop
