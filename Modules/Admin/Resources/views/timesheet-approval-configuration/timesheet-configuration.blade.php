@extends('adminlte::page')
@section('title', 'Timesheet Approval Configuration')
 @section('content_header')
<h1>Timesheet Approval Configuration</h1>
@stop @section('content')
<div class="row">

   <!-- left column -->
   <div class="col-md-12">

       <!-- general form elements -->

           <!-- form start -->
            {{ Form::open(array('url'=>'#','id'=>'timesheetConfiguration-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                {{ csrf_field() }}
             <div class="box-body">
             <div class="deadline_row">
                 <div class="deadline_dayTime">
              <table>
                 <tr>
                 <th><label for="day" class="day">Deadline is on &nbsp;</label></th>
                <td width="40%">
                <div class="form-group" id="day">
                <select class='form-control' name='day'>
                    <option value='' selected>Please select</option><option value='0'>Every Sunday</option>
                    <option value='1'>Every Monday</option><option value='2'>Every Tuseday</option>
                    <option value='3'>Every Wednesday</option><option value='4'>Every Thursday</option>
                    <option value='5'>Every Friday</option><option value='6'>Every Saturday</option>
                </select>
                <small class="help-block"></small>
                </div>
                 </td>

                     <th><label for="time" class="timing">&nbsp;&nbsp;At&nbsp;&nbsp;</label></th>
                     <td width="40%">
                     <div class="form-group" id="time">
                     {{ Form::text('time',null,array('class'=>'form-control timepicker','placeholder' => 'Time')) }}
                     <small class="help-block"></small>
                     </div>
                     </td>
                </tr>

              </table></div>
              <br>

        <div class="form-group email_alerts" id="email_1_time" style="padding-bottom: 5px;">
        <label for="email_1_time col-md-6" class=" col-md-6" style="padding-left: 0px;">Rate Timesheet From Previous Week</label>
        <label class="col-md-6" style="padding-left: 0px;">{{ Form::checkbox('is_previous_week_enabled',null,null, array('id'=>'is_previous_week_enabled')) }}</label>
        <small class="help-block"></small>
        </div>

      <div class="form-group email_alerts" id="email_1_time">
      <label for="email_1_time">Email 1 alert (Hours)</label>
      {{ Form::number('email_1_time',null,array('class'=>'form-control email_1_time','placeholder' => 'Email 1 time (hours)', 'min'=>'0.10', 'max'=>'12','step'=>'0.01')) }}
      <small class="help-block"></small>
      </div>

      <div class="form-group email_alerts" id="email_2_time">
      <label for="email_2_time">Email 2 alert (Hours)</label>
      {{ Form::number('email_2_time',null,array('class'=>'form-control email_2_time','placeholder' => 'Email 2 time (hours)', 'min'=>'0.10', 'max'=>'12','step'=>'0.01')) }}
      <small class="help-block"></small>
      </div>

      <div class="form-group email_alerts" id="email_3_time">
      <label for="email_3_time">Email 3 alert (Hours)</label>
      {{ Form::number('email_3_time',null,array('class'=>'form-control email_3_time','placeholder' => 'Email 3 time (hours)', 'min'=>'0.10', 'max'=>'12','step'=>'0.01')) }}
      <small class="help-block"></small>
      </div>


      <br>

      <h3 class="rating_specification">Rating Specification</h3>
      <br>
      <div class="newRow">
      <div class="col-sm-10 table-responsive pop-in-table" id="user-security-clearance">
      <table  class="table">
      <tr class='inputclass5'>
      <th class="align-left">Approved</th>
      <td><div class='form-group' id='from_0'><input type='hidden' name='row-no[]' class='row-no'><input type='hidden' class='form-control' name='from_0' type='number' value='0'></input><small class='help-block'></small></div></td>
      <td></td>
      <td><div class='form-group' id='early_0'><input type='hidden' name='row-no[]' class='row-no'><input class='form-control' name='early_0' type='number' min='0' max='12' step='0.01'></input><small class='help-block'></small></div></td>
      <th>hours before deadline</th>
      <td><div class='form-group' id='rating_0'><input type='hidden' name='row-no[]' class='row-no'><select class='form-control' name='rating_0'><option value='' selected>Please select</option><option value='1'>1 - Does not meet expectations</option><option value='2'>2 - Marginally meets expectations</option><option value='3'>3 - Meets Expectations</option><option value='4'>4 - Exceeds Expecatations</option><option value='5'>5 - Far Exceeds Expectations</option></select><small class='help-block'></small></div></td>
      </tr>

      <tr class='inputclass5'>
      <th>Approved on/after</th>
      <td><div class='form-group' id='from_1'><input type='hidden' name='row-no[]' class='row-no'><input class='form-control' name='from_1' type='number' value=" " min='0' max='12' step='0.01' readonly></input><small class='help-block'></small></div></td>
      <th>hours and</th>
      <td><div class='form-group' id='early_1'><input type='hidden' name='row-no[]' class='row-no'><input class='form-control' name='early_1' type='number' min='0' max='12' step='0.01'></input><small class='help-block'></small></div></td>
      <th>hours before deadline</th>
      <td><div class='form-group' id='rating_1'><input type='hidden' name='row-no[]' class='row-no'><select class='form-control' name='rating_1'><option value='' selected>Please select</option><option value='1'>1 - Does not meet expectations</option><option value='2'>2 - Marginally meets expectations</option><option value='3'>3 - Meets Expectations</option><option value='4'>4 - Exceeds Expecatations</option><option value='5'>5 - Far Exceeds Expectations</option></select><small class='help-block'></small></div></td>
      </tr>
      <tr class='inputclass5'>
      <th>Approved on/after</th>
      <td><div class='form-group' id='from_2'><input type='hidden' name='row-no[]' class='row-no'><input class='form-control' name='from_2' type='number' value=" " min='0' max='12' step='0.01' readonly></input><small class='help-block'></small></div></td>
      <th>hours and</th>
      <td><div class='form-group' id='early_2'><input type='hidden' name='row-no[]' class='row-no'><input class='form-control' name='early_2' type='number' min='0' max='12' step='0.01'></input><small class='help-block'></small></div></td>
      <th>hours before deadline</th>
      <td><div class='form-group' id='rating_2'><input type='hidden' name='row-no[]' class='row-no'><select class='form-control' name='rating_2'><option value='' selected>Please select</option><option value='1'>1 - Does not meet expectations</option><option value='2'>2 - Marginally meets expectations</option><option value='3'>3 - Meets Expectations</option><option value='4'>4 - Exceeds Expecatations</option><option value='5'>5 - Far Exceeds Expectations</option></select><small class='help-block'></small></div></td>
      </tr>
      <tr class='inputclass5'>
      <th>Approved on/after</th>
      <td><div class='form-group' id='from_3'><input type='hidden' name='row-no[]' class='row-no'><input class='form-control' name='from_3' type='number' value=" " min='0' max='12' step='0.01' readonly></input><small class='help-block'></small></div></td>
      <th>hours and Upto</th>
      <td><div class='form-group' id='early_3'><input type='hidden' name='row-no[]' class='row-no'><input class='form-control' name='early_3' type='number' min='0' max='12' step='0.01'></input><small class='help-block'></small></div></td>
      <th>hours after deadline</th>
      <td><div class='form-group' id='rating_3'><input type='hidden' name='row-no[]' class='row-no'><select class='form-control' name='rating_3'><option value='' selected>Please select</option><option value='1'>1 - Does not meet expectations</option><option value='2'>2 - Marginally meets expectations</option><option value='3'>3 - Meets Expectations</option><option value='4'>4 - Exceeds Expecatations</option><option value='5'>5 - Far Exceeds Expectations</option></select><small class='help-block'></small></div></td>
      </tr>
      <tr class='inputclass5'>
      <th>Approved on/after</th>
      <td><div class='form-group' id='from_4'><input type='hidden' name='row-no[]' class='row-no'><input class='form-control' name='from_4' type='number' value=" " min='0' max='12' step='0.01' readonly></input><small class='help-block'></small></div></td>
      <th></th>
      <td><div class='form-group' id='early_4'><input type='hidden' name='row-no[]' class='row-no'><input class='form-control' name='early_4' type='hidden'></input><small class='help-block'></small></div></td>
      <th>hours after deadline</th>
      <td><div class='form-group' id='rating_4'><input type='hidden' name='row-no[]' class='row-no'><select class='form-control' name='rating_4'><option value='' selected>Please select</option><option value='1'>1 - Does not meet expectations</option><option value='2'>2 - Marginally meets expectations</option><option value='3'>3 - Meets Expectations</option><option value='4'>4 - Exceeds Expecatations</option><option value='5'>5 - Far Exceeds Expectations</option></select><small class='help-block'></small></div></td>
      </tr>

      </table>
      </div>
      </div>


               </div>
             </div>
               <!-- /.box-body -->
               <div class="box-footer">

                   {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>''))}}
                </div>
           </form>

       <!-- /.box -->
   </div>
   <style>

       .box-body .inc_button{
        margin-left: 87%;
       }
       .newRow{
        margin-left: -3em;
       }
       table th  {
        text-align: left;
        }
       .align-left{
        border-spacing: 100px
       }
       .deadline_row{
        margin-left: 1em;
        padding-top: 25px;
       }
       .table>tbody>tr>th{
        padding-left: -10px;
       }
       .deadline_dayTime{
        margin-left: -1em;
       }
       .rating_specification{
        margin-left: -1em;
       }
       .day{
        margin-bottom: 22px;
       }
       .timing{
        margin-bottom: 22px;
        }
       .box-footer{
        margin-left: -0.5%;
        margin-top: -1%;
       }
       .box-body #day{
        margin-left:-17em;

       }
       .box-body #time{
            margin-left:-6em;
        }
        .timepicker_wrap {
            width: 308px !important;
            margin-left: 32em;
        }

        .email_alerts{
            width: 670px !important;
        }


   </style>
   @endsection
   @section('js')
       <script>
            $(function () {
                  $('.timepicker').timepicki();
                  $('.timepicker_hours').timepicki({show_meridian:false});
                  var data={!!json_encode($data)!!};
                  console.log(data);
                  if(data != null){
                  $('select[name="day"]').val(data.day);
                  $('input:text[name="time"]').val(data.time);
                     if(data.is_previous_week_enabled == 1){
                    $('#is_previous_week_enabled').prop('checked', true);
                  }else{
                    $('#is_previous_week_enabled').prop('checked', false);
                  }
                  $('.email_1_time').val(data.email_1_time);

                    $('.email_2_time').val(data.email_2_time);


                  $('.email_3_time').val(data.email_3_time);

                  }

            });

            $("#early_0").on("input", function (e) {
                $('input[name="from_1"]').val($('input[name="early_0"]').val());
            });
            $("#early_1").on("input", function (e) {
                $('input[name="from_2"]').val($('input[name="early_1"]').val());
            });
            $("#early_2").on("input", function (e) {
                $('input[name="from_3"]').val($('input[name="early_2"]').val());
            });
            $("#early_3").on("input", function (e) {
                $('input[name="from_4"]').val($('input[name="early_3"]').val());
                $from4=$('input[name="from_4"]').val();
                $FloatData=parseFloat($from4);
                $data= $FloatData + 1;
                $('input[name="early_4"]').val($data);

            });




            var rating={!!json_encode($ratingData)!!};
            $(".hours-rating-table tbody").empty();
            $.each(rating, function(key, value) {
            var select_box_values = '';
            var key_value=key-1;
            var $early=$('input[name="early_' + key_value + '"]').val();

             $('input[name="early_' + key + '"]').val(value.early);
             $('input[name="from_' + key + '"]').val(value.untill);
             $('select[name="after_' + key + '"] option[value="' + value.after + '"]').prop('selected', true);

             $('select[name="rating_' + key + '"] option[value="' + value.rating + '"]').prop('selected', true);
             $(".hours-rating-table").find('tr:last').find('.row-no').val(key);
             });


           $('#timesheetConfiguration-form').submit(function (e) {
               e.preventDefault();
                 var $form = $(this);
                var formData = new FormData($('#timesheetConfiguration-form')[0]);
                console.log(formData);
                $.ajax({

                        url: "{{route('timesheet-configuration.store')}}",
                        type: 'POST',
                        data:  formData,
                        processData: false,
                        contentType: false,
                        success: function (data) {
                            if (data.success) {
                                swal("Success", "Timesheet approval configuration has been successfully updated", "success");
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
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('globals.google_api_key') }}"></script>
        <script src="{{ asset('js/timepicki.js') }}"></script>
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
        <link rel='stylesheet' type='text/css' href='{{ asset('css/timepicki.css') }}' />
   @endsection
