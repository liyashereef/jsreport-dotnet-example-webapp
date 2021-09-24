@extends('adminlte::page')
@section('title', 'Payroll Settings')
@section('content_header')
<h1>Payroll Settings</h1>
@stop
@section('content')
<div class="row">
    <div class="col-md-4">
        {{ Form::open(array('url'=>'#','id'=>'payroll-settings','class'=>'form-horizontal', 'method'=> 'POST')) }}
        {{ csrf_field() }}
        <table class="table" id="priority-table">
            <tbody>
                <tr>
                    <td>
                    <strong>Setting</strong>
                    </td>
                    <td>
                        <strong>Threshold (in Hours)</strong>
                    </td>
                </tr>
                <tr class="tr-priority">
                    <td>Manual Timesheet Entry</td>
                    <td id="hours" class="form-group">
                        <input name="hours" class="form-control hoursmask" type="text" value={{$hours}} placeholder="Hours">
                        <small class="help-block"></small>
                    </td>
                </tr>
            </tbody>
        </table>
        {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>''))}}
    </div>
</div>
@endsection
@section('js')
    <script>
        $(function() {
            $('.hoursmask').mask("99:99");
        });

        $('#payroll-settings').submit(function(e) {
            e.preventDefault();
            var $form = $(this);
            var formData = new FormData($('#payroll-settings')[0]);
            $.ajax({
                    url: "{{route('payroll-settings.store')}}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        if (data.success) {
                            swal("Success", "Payroll settings has been successfully updated", "success");
                             $('.form-group').removeClass('has-error').find('.help-block').text('');
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
@endsection

