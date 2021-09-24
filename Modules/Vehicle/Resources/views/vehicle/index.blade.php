@extends('layouts.app')
@section('content')
<div class="table_title">
    <h4>Initiate Vehicle </h4>
</div>
<div  class="form-group row   {{ $errors->has('is_initiated') ? 'has-error' : '' }}">
    <label for="is_initiated" class="col-sm-5 col-form-label">Select </label>
    <div class="col-sm-6">
        {{ Form::select('is_initiated',$is_initiated, old('is_initiated'),array('class'=> 'form-control select2','id'=>'is_initiated')) }}
        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('vehicle_id', ':message') !!}</div>
    </div>
</div>
{{ Form::open(array('id'=>'vehicle-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
{{ Form::hidden('id',null) }}
<div id="vehicle_id" class="form-group row   {{ $errors->has('vehicle_id') ? 'has-error' : '' }}">
    <label for="vehicle_id" class="col-sm-5 col-form-label">Vehicle</label>
    <div class="col-sm-6">
        {{ Form::select('vehicle_id',[0=>'Please Select']+$vehicles, old('vehicle_id'),array('class'=> 'form-control select2','required'=>TRUE,'id'=>'vehicle')) }}
        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('vehicle_id', ':message') !!}</div>
    </div>
</div>

<div id="odometer_reading" class="form-group row  {{ $errors->has('odometer_reading') ? 'has-error' : '' }}">
    <label for="odometer_reading" class="col-sm-5 col-form-label">Current Odometer Reading</label>
    <div class="col-sm-6">
        {{ Form::text('odometer_reading',null,array('placeholder'=>'Odometer Reading','class'=>'form-control','required'=>TRUE,'maxlength'=>6,'id'=>'odometer_reading')) }}
        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('odometer_reading', ':message') !!}</div>
    </div>
</div>
<div  class="form-group row add-remove" id="add-remove_0">
    <input type="hidden" name="position[]" class="pos"  value="0">
    <input type="hidden" name="data_type[]" class="data_type" id="data_type_0" value="km">
    <!-- <label for="experiences" class="col-sm-5 col-form-label">What are the minimum requirements for the role? (years)</label> -->
    <div class="col-sm-5 form-group type" id="type_0" style="padding-left: 4%">
        {{ Form::select('type[]', [0=>'Please Select Type']+$type, old('type'),array('class'=> 'form-control form-control-danger','id'=>'types_0','data-id'=>'0')) }}
        <small class="help-block"></small>
    </div>
    <div class="col-sm-3  form-group service" id="service_0">
        {{ Form::number('service[]', null,array('class'=> 'form-control form-control-danger','min'=>"0",'max'=>"999999",'placeholder'=>'Last Service (date/km)','id'=>'services_0')) }}
        <small class="help-block"></small>
    </div>
    <div class="col-sm-3  form-group interval" id="interval_0">
        {{ Form::text('interval[]', null,array('class'=>"form-control form-control-danger",'placeholder'=>'Service Interval','id'=>'intervals_0')) }}
        <small class="help-block"></small>
    </div>
    <div class="col-sm-1">

        <a title="Add another experience" href="javascript:;" class="add_button">
            <i class="fa fa-plus" aria-hidden="true"></i>
        </a>

           <!--  <a href="javascript:void(0);" class="remove_button" title="Remove field">
                <i class="fa fa-minus" aria-hidden="true"></i>
            </a> -->

        </div>
        <div class="form-control-feedback"></div>
    </div>


    <div id="notes" class="form-group row">
        <label for="remarks" class="col-sm-5 col-form-label">Notes</label>
        <div class="col-sm-6">
            {{ Form::textarea('notes',old('notes'),array('placeholder'=>'Notes','class'=>'form-control','maxlength'=>500)) }}
            <small class="help-block"></small>
        </div>
    </div>


    <div class="form-group row">
        <div class="col-sm-5"></div>
        <div class="col-sm-6">
          <input type="reset" name="CANCEL" value="Cancel" class="btn cancel" onclick="window.location='{{ route("home") }}'">
            {{ Form::submit('Save',array('class'=>'btn submit'))}}
        </div>
    </div>
    {{ Form::close() }}

    @stop
    @section('scripts')
    <script type="text/javascript">

       $(document).ready(function() {
           $('#vehicle').select2();
           $('#types_0').select2();
           $('body').on('change','select[name="type[]"]', function() {
            var id = $(this).val();
            var position=($(this).data('id'));
            var base_url = "{{route('vehicle.getTypeDetails',':id')}}";
            var url = base_url.replace(':id', id);
            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                   $('#intervals_'+position).val('');
                   $('#services_'+position).val('');
                   //type ==1 indicating its a kilometer and type==2 denoting date
                   if(data.type==1)
                   {
                       $('#services_'+position).prop("type", "number");
                       $('#services_'+position).datepicker("destroy");
                       $('#data_type_'+position).val("km");
                       $('#services_'+position).addClass('form-control form-control-danger');
                       $('#services_'+position).attr("placeholder", "Last Service Kilometer");

                   }
                   else
                   {
                     $('#services_'+position).prop("type", "text");
                     $('#services_'+position).addClass('datepicker');
                     $('#services_'+position).attr("placeholder", "Last Service Date");
                     $('#data_type_'+position).val("date");

                 }

             }
         });
        });

           $("body").on("click",".add_button",function(){
               addRow();

           });


           $("body").on("click",".remove_button",function(){
            var removedPos = $(this).parents(".form-group").find('input[name="position[]"]').val();
            position_num=removedPos;
            $(this).parents('.form-group').nextAll('.add-remove').each(function( index,value ) {
               $(value).attr('id', 'add-remove_'+position_num);
               $(value).find('.pos').val(position_num);
               $(value).find('.type').attr('id', 'type_'+position_num);
               $(value).find('.types').attr('id', 'types_'+position_num);
               $(value).find('.types').attr('data-id',position_num);
               $(value).find('.service').attr('id', 'service_'+position_num);
               $(value).find('.interval').attr('id', 'interval_'+position_num);
               $(value).find('.data_type').attr('id', 'data_type_'+position_num);
               $(value).find("input[name='interval[]']").attr('id', 'intervals_'+position_num);
               position_num++;
           });
            $(this).parents(".form-group").remove();
            $('.add-remove').find('.add_button').hide();
            $('.add-remove:last').find('.add_button').show();

        });

       });

       $('#vehicle-form').submit(function (e) {
        e.preventDefault();
        var $form = $(this);
        url = "{{ route('vehicle.initiate-store') }}";
        var formData = new FormData($('#vehicle-form')[0]);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: 'POST',
            data: formData,
            success: function (data) {
                if (data.success) {
                   swal({
                      title: "Saved",
                      text: "Vehicle initiated successfully",
                      type: "success",
                      confirmButtonText: "OK",
                  },function(){
                    $('.form-group').removeClass('has-error').find('.help-block').text('');
                    window.location.href = "{{ route('vehicle.initiate') }}";
                });
               } else {
                $('.form-group').removeClass('has-error').find('.help-block').text('');
                console.log(data);
            }
        },
        fail: function (response) {
            console.log(data);
        },
        error: function (xhr, textStatus, thrownError) {
            associate_errors(xhr.responseJSON.errors, $form, true);
        },
        contentType: false,
        processData: false,
    });
    });

       $('#vehicle').on('change', function() {
        $('#vehicle-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
        $('.newly-added-block').remove();
        var id = $(this).val();
        var base_url = "{{route('vehicle.editVehicleInitiate',':id')}}";
        var url = base_url.replace(':id', id);
        $.ajax({
            url: url,
            type: 'GET',
            success: function(data) {
                 $('.add-remove:last').find('.add_button').show();
                $.each( data.vehicle.vehicles, function( key, value ) {
                    var index=key;
                    if(index!=0)
                    {
                        addRow();
                    }
                    $.each( value, function( key, value ) {

                        if(key=='service_type_id')
                            $('#types_'+index).val(value).select2();
                        if((key=='service_date') &&(value!=null)){
                            $('#services_'+index).prop("type", "text");
                            $('#services_'+index).datepicker({format: 'yyyy-mm-dd'});
                            $('#services_'+index).val(value);
                            $('#data_type_'+index).val("date");
                        }
                        if((key=='service_km') &&(value!=null))
                        {
                            $('#services_'+index).prop("type", "number");
                           // $('#services_'+index).removeClass('datepicker')
                            $('#services_'+index).datepicker("destroy");
                             $('#services_'+index).addClass('form-control form-control-danger');
                            $('#services_'+index).val(value);
                            $('#data_type_'+index).val("km");

                        }
                        if((key=='interval_km') &&(value!=null))
                           $('#intervals_'+index).val(value);
                       if((key=='interval_day') &&(value!=null))
                           $('#intervals_'+index).val(value);
                   });
                });
                $("input[name='odometer_reading']" ).val( parseInt(data.vehicle.odometer_reading) );
                $("textarea[name='notes']" ).val( data.vehicle.notes );



            }
        });
    });
       $('#is_initiated').on('change', function() {
           console.log($('#is_initiated').val());
          $("#vehicle-form").trigger('reset');
          $('.newly-added-block').remove();
          $('.add-remove:last').find('.add_button').show();
          var id = $(this).val();
          if($('#is_initiated').val() == 1){
            $('input:text[name="odometer_reading"]').prop('readonly', true);
          }else{
            $('input:text[name="odometer_reading"]').prop('readonly', false);
          }
          var base_url = "{{route('vehicle.getVehicleName',':id')}}";
          var url = base_url.replace(':id', id);
          $.ajax({
            url: url,
            type: 'GET',
            success: function(data) {
              var temp=[];
             $.each(data.vehicles, function(index, name) {
              temp.push({ index,name});
              });
             temp.sort(function(a,b){
             if(a.name.toLowerCase() > b.name.toLowerCase()){ return 1}
             if(a.name.toLowerCase() < b.name.toLowerCase()){ return -1}
             return 0;
             });
                $('#vehicle').empty().append($("<option></option>")
                    .attr("value",0)
                    .text('Please Select'));;
                $.each(temp, function(index, vehicle) {
                   $('#vehicle')
                   .append($("<option></option>")
                    .attr("value",vehicle['index'])
                    .text(vehicle['name']));
               });

            }
        });
      })
       function addRow()
       {
          position_num= $('.pos:last').val()
          new_position_num=parseInt(position_num)+1;
          var html = '<div  class="form-group row add-remove newly-added-block" id="add-remove_'+new_position_num+'"><input type="hidden" name="position[]" class="pos" value='+new_position_num+'><input type="hidden" name="data_type[]" id="data_type_'+new_position_num+'" class="data_type"><div class="col-sm-5  form-group type" id="type_'+new_position_num+'" style="padding-left: 4%"><select name="type[]" data-id='+new_position_num+' class="form-control types"  id="types_'+new_position_num+'"><option value="0">Please select Type</option> @foreach($type as $id=>$data) <option value={{$id}}>{{$data}}</option> @endforeach</select><small class="help-block"></small><div class="form-control-feedback"></div></div><div class="col-sm-3 form-group service" id="service_'+new_position_num+'"><input type="number"  min="0" max="99999" name="service[]" id="services_'+new_position_num+'" class="form-control form-control-danger" placeholder="Last Service (date/km)"><small class="help-block"></small></div><div class="col-sm-3 form-group interval" id="interval_'+new_position_num+'"><input type="text" name="interval[]" class="form-control form-control-danger" placeholder="Service Interval" id="intervals_'+new_position_num+'"><small class="help-block"></small></div><div class="col-sm-1"><a title="Add another experience" href="javascript:;" class="add_button"><i class="fa fa-plus" aria-hidden="true"></i></a><a href="javascript:void(0);" class="remove_button" title="Remove field"><i class="fa fa-minus" aria-hidden="true"></i></a></div><div class="form-control-feedback"></div></div></div>';
          $("#add-remove_"+position_num).after(html);
          $('#types_'+new_position_num).select2();
          $('.add-remove').find('.add_button').hide();
          $('.add-remove:last').find('.add_button').show();
      }
  </script>
  @stop
