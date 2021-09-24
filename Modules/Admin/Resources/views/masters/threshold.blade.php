@extends('adminlte::page')

@section('title', config('app.name', 'Laravel').'-Regions')

@section('content_header')

<h1>Client Schedule Settings (Threshold Hours)</h1>

@stop @section('content')

    <div class="form-group row">
        <div class="col-sm-3">
            <label style="font-weight: 400; font-size: 16px;">Weekly Threshold Hours</label>
        </div>
        <div class="col-sm-5 text-sm-left">
            <input type="number" id="schedule_time_period_id" class="form-control" placeholder="Weekly Threshold" value={{ $scheduleSettingsData? $scheduleSettingsData->weekly_threshold: ''}}>
            <small class="help-block"></small>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-3">
            <label style="font-weight: 400; font-size: 16px;">Biweekly Threshold Hours</label>
        </div>
        <div class="col-sm-5 text-sm-left">
            <input type="number" id="schedule_threshold" class="form-control" placeholder="Bi Weekly Threshold" value={{ $scheduleSettingsData? $scheduleSettingsData->bi_weekly_threshold: ''}}>
            <small class="help-block"></small>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-3 pl-4"></div>
        <div class="col-sm-6">
            <input id="submit" class="btn btn-primary" type="button" value="Submit">
        </div>
    </div>

@stop

@section('js')
<script>
    $(document).ready(function(){
        $('#submit').on('click',function() {
                var customer = $('#schedule_time_period_id').val();
                var threshold = $('#schedule_threshold').val();
                if(customer === ""){
                    swal("Warning", "Please fill weekly threshold hours", "warning");
                    return false;
                }else if(threshold === ""){
                    swal("Warning", "Please fill biweekly threshold hours", "warning");
                    return false;
                }
                $.ajax({
                url: "{{route('threshold.store')}}",
                type: 'POST',
                data: {
                'customer': customer,
                'threshold': threshold,

                },
                success: function (data)  {
                    if (data.success) {
                        if(data.result == false){
                            result = "Threshold hours has been updated successfully";
                        }else{
                            result = "Threshold hours has been created successfully";
                        }
                        swal({
                          title: "Saved",
                          text: result,
                          type: "success",
                          confirmButtonText: "OK",
                        },function(){
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                           
                        });
                    }    
                },
                headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },

            });
        });
    });
    
    </script>
@stop
