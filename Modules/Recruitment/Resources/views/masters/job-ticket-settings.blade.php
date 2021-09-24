@extends('adminlte::page')
@section('title', 'Training Settings')
@section('content_header')
<h1>Job Ticket Settings</h1>
@stop
@section('content')
<div class="row">
    <div class="col-md-8">
        {{ Form::open(array('url'=>'#','id'=>'job-ticket-settings','class'=>'form-horizontal', 'method'=> 'POST')) }}
        {{ csrf_field() }}
        <table class="table" id="priority-table">
            <tbody>
                <tr>
                    <td>
                    <strong>Color Code</strong>
                    </td>
                    <td colspan="2">
                        <strong>Notice Period (in days)</strong>
                    </td>
                </tr>
                <tr class="tr-priority">
                    <td>Red</td>
                    <td>Less than or equal to</td>
                    <td>
                        <input
                        type="number"
                        class="form-control"
                        style="display: inline; width:12%;"
                        id="min"
                        onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13)
                        ? null
                        : event.charCode >= 48 && event.charCode <= 57"
                        onchange="updateResponseTime(this)"
                        min="1"
                        value="{{ $min }}">&nbsp;Days
                    </td>
                </tr>
                <tr class="tr-priority">
                    <td>Yellow</td>
                    <td>Greater than </td>
                    <td>
                        <input
                        type="number"
                        class="form-control"
                        style="display: inline;width:12%;"
                        id="medium_range"
                        value="{{ $min }}"
                        disabled="">&nbsp;Less than or equal to &nbsp;&nbsp;&nbsp;&nbsp;
                        <input
                        type="number"
                        class="form-control"
                        style="display: inline;width:12%;"
                        id="medium"
                        onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13)
                        ? null
                        : event.charCode >= 48 && event.charCode <= 57"
                        onchange="updateResponseTime(this)"
                        min="1"
                        value="{{ $max }}">
                    </td>
                </tr>
                <tr class="tr-priority">
                    <td>Green</td>
                    <td>Greater than</td>
                    <td>
                        <input
                        type="number"
                        class="form-control"
                        style="display: inline;width:12%;"
                        id="max"
                        readonly=""
                        min="1"
                        value="{{ $max}}">&nbsp;Days
                    </td>
                </tr>
            </tbody>
        </table>
        {{ Form::button('Save', array('class'=>'button btn btn-primary blue','id'=>'noticePeriodUpdate'))}}
    </div>
</div>
@endsection
@section('js')
    <script>
         $('#noticePeriodUpdate').on('click', function(e) {
            e.preventDefault();
            console.log('min', $('#min').val());
            if ($('#min').val() == '' || $('#max').val() == '') {
                swal("Alert", "Please fill the days", "warning");
            } else if ( parseInt($('#min').val()) >= parseInt($('#max').val()) ) {
                swal("Alert", "Maximum days should be greater than minimum days", "warning");
            }
            else {
                $.ajax({
                        headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                        url: "{{route('recruitment.job-ticket-settings.store')}}",
                        type: 'POST',
                        data: {
                            "min": $('#min').val(),
                            "max": $('#max').val()
                        },
                        success: function (data) {
                            if (data.success) {
                                swal("Success", "Job ticket settings has been successfully updated", "success");
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
            }

         });

        function updateResponseTime(result){
            if(result.id == 'medium'){
                $('#max').val(result.value);
            }else if(result.id == 'min'){
                $('#medium_range').val(result.value);
            }

        }
    </script>
@endsection

