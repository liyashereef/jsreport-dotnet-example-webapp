@extends('adminlte::page')
@section('title', 'Training Settings')
@section('content_header')
<h1>Training Settings</h1>
@stop
@section('content')
<div class="row">
    <div class="col-md-12">
             {{ Form::open(array('url'=>'#','id'=>'training_settings','class'=>'form-horizontal', 'method'=> 'POST')) }}
                 {{ csrf_field() }}
                <div class="box-body">
                    <div class="form-group" id="trainingWidgetTolerenceDays">
                        <label for="trainingWidgetTolerenceDays">Training widget tolerence days</label>
                        {{ Form::number('trainingWidgetTolerenceDays',old('trainingWidgetTolerenceDays', $training_settings->value),array('class'=>'form-control', 'min'=>1, 'required'=>true))}}
                       <small class="help-block"></small>
                    </div>
                </div>
                <div class="box-footer">
                    {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>''))}}
                 </div>
            </form>
    </div>
</div>
@endsection
@section('js')
    <script>
        $('#training_settings').submit(function (e) {
               e.preventDefault();
                 var $form = $(this);
                var formData = new FormData($('#training_settings')[0]);
                $.ajax({
                        headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                        url: "{{route('training-settings.store')}}",
                        type: 'POST',
                        data:  formData,
                        processData: false,
                        contentType: false,
                        success: function (data) {
                            if (data.success) {
                                swal("Success", "Training settings has been successfully updated", "success");
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
