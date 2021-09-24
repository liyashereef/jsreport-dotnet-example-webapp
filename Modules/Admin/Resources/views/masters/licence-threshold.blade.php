@extends('adminlte::page')
@section('title', 'Licence Threshold')
@section('content_header')
<h1>Licence Threshold</h1>
@stop

@section('content')

<div class="row">

   <div class="col-md-12">

       <div class="box box-primary">

                {{ Form::open(array('url'=>'#','id'=>'threshold-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
               <div class="box-body">
                   <div class="form-group" id="threshold">
                    <div class="col-md-2">
                            <label for="threshold">Licence Threshold (in months)</label>
                            {{ Form::number('threshold',old('threshold',$thresholdData->threshold),array('class'=>'form-control','required'=>true))}}
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
 </div>

   @stop
   @section('js')

   <script>

 /* Posting data to LicenceThresholdController - Start*/
        $('#threshold-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            url = "{{ route('licence-threshold.store') }}";
            var formData = new FormData($('#threshold-form')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                      swal({title: "Saved", text: "Licence threshold has been saved", type: "success"},

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
        /* Posting data to LicenceThresholdController - End*/



   </script>
   @stop
