@extends('adminlte::page')
@section('title', 'Maximum Shift Timings')
@section('content_header')
<h1>Schedule Maximum Shift Timings</h1>
@stop

@section('content')

<div class="row">

   <div class="col-md-12">

       <div class="box box-primary">

                {{ Form::open(array('url'=>'#','id'=>'max-hours-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
               <div class="box-body">
                   <div class="form-group" id="hour">
                    <div class="col-md-6">
                            <label for="hour">Maximum Hours</label>
                            {{ Form::text('hour',old('hour',$cs->hours),array('id' => 'hour','read-only' => 'true','class'=>'form-control','maxlength'=>'50','required'=>true))}}
                             <span class="help-block"></span>

                    </div>
                   </div>
               </div>
               <div class="box-footer">
                   <button type="submit" class="btn btn-primary">@lang('Submit')</button>
               </div>

           {{ Form::close() }}
       </div>

   </div>

   @stop
   @section('js')

   <script>

 /* Posting data to ScheduleMaximumHoursController - Start*/
        $('#max-hours-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            url = "{{ route('schedule-maximum-hours.store') }}";
            var formData = new FormData($('#max-hours-form')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                      swal({title: "Saved", text: "The maximum shift time has been saved", type: "success"},
                     function(){
                         location.reload();
                     }
                  );
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
        /* Posting data to ScheduleMaximumHoursController - End*/



   </script>
   @stop
