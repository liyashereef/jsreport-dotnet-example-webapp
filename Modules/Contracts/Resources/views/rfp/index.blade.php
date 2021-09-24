@extends('layouts.app')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@section('content')
<div class="table_title">
    <h4>RFP Stages</h4>
</div>

{{ Form::open(array('url'=>'#','id'=>'rfp-status-form','class'=>'form-horizontal', 'method'=> 'POST')) }} {{csrf_field()}}
 {{ Form::hidden('id', null) }}
 <div id="form-container">

<div class="table-responsive" id="tracking-table">
    <table class="table table-bordered dataTable">
        <thead>
            <th>No</th>
            <th>Process Step</th>
            <th>Completion Date</th>
            <th>Notes</th>
            <th>Entered By</th>
        </thead>
        <tbody>
                @foreach($lookups as $index=>$lookup)
                <tr>
                    <td>
                    {{$lookup->step_number}}
                    </td>
                    <td>
                    {{$lookup->process_steps}}
                    </td>
                    @if(in_array($lookup->id,array_keys($already_processed_track_ids)))
                    <td>
                            {{ Form::label('completion_date['.$lookup->id.']',
                            $already_processed_track_ids[$lookup->id]->completion_date) }}
                        </td>
                        <td>
                            {{ Form::label('notes['.$lookup->id.']',$already_processed_track_ids[$lookup->id]->notes) }}
                        </td>
                        <td>

                            {!!
                            ($already_processed_track_ids[$lookup->id]->entered_by!=null)?$already_processed_track_ids[$lookup->id]->entered_by->full_name:'<i>User
                                name</i>' !!}
                         @can('delete_rfp') &nbsp;

                            <a title="Remove this entry" href="javascript:;" class="delete fa fa-trash" data-id="{{ $lookup->id }}"  data-rfp-id="{{$rfpDetails->id}}">
                            </a>
                            @endcan
                        </td>

                    @else

                    <td>

                          {{
                        Form::text('completion_date['.$lookup->id.']',old('completion_date['.$lookup->id.']'),array('class'
                        => 'datepicker form-control','id'=> 'completion_date')) }}
                        <span class="help-block text-danger align-middle font-12"></span>
                    </td>
                    <td>
                    {{
                        Form::textArea('notes['.$lookup->id.']',old('notes['.$lookup->id.']'),array('placeholder'=>"Notes",'cols'=>'30','rows'=>1,'class'
                        => 'form-control')) }}
                        <span class="help-block text-danger align-middle font-12"></span>
                    </td>
                     <td>

                       {{ Form::select('entered_by_id['.$lookup->id.']', [null=>'Please Select'] + $users,
                        null,array('class' => 'form-control','id'=>'test['.$lookup->id.']','data-enteredby'=> '['.$lookup->id.']')) }}
                        <span class="help-block text-danger align-middle font-12"></span>
                    </td>

                    </tr>
                    @endif
                @endforeach
            </tbody>

    </table>
</div>

</div>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 text-xs-center text-sm-center text-md-center text-lg-center text-xl-center margin-top-1">

    {{ Form::submit('Save', array('class' => 'btn submit')) }}

    {{ Form::button('Cancel', array('class' => 'btn cancel','onclick'=>'window.history.back();')) }}
</div>
{{ Form::close() }}


@stop
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script>
    $(document).on('click', "#completion_date", function(){
            var time = moment().format('YYYY-MM-DD');
             $(this).val(time);
    });

    $(document).on('mousedown','[data-enteredby]', function(e){
            var userId = "{{ Auth::user()->id }}";
            $(e.target).find("option").each(function(){
                    if($(this).val() == userId){
                        $(this).attr("selected","selected");
                    }
                });
    });

</script>
<script>

$(function () {
$('select').select2();
$('#rfp-status-form').submit(function (e) {
    e.preventDefault();

    try {
        var $form = $(this);
    $form.find('td').removeClass('has-error').find('.help-block').text('');
    var formData = new FormData($('#rfp-status-form')[0]);
    //console.log('yes');
    var url =
        "{{ route('rfp.track-store',[$rfpDetails->id]) }}";
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: url,
        type: 'POST',
        data: formData,
        success: function (data) {
            console.log(data);
            if (data.success) {
                swal({
                    title: 'Success',
                    text: 'Tracking step has been successfully updated',
                    icon: "success",
                    button: "OK",

                }, function () {
                    window.location = "{{ route('rfp.summary') }}";
                });
            }else if(data.status === 422){
                alert("here");
            } else {
                alert(data.message);
            }
        },
        fail: function (response) {
            alert('here');
        },
        error: function (xhr, textStatus, thrownError) {
            if( xhr.status === 422 ) {
                swal({
                    title: 'Warning',
                    text: 'Completion Date and Entered By are mandatory fields',
                    icon: "warning",
                    button: "Ok",

                }, function () {

                });
            }
            associate_errors(xhr.responseJSON.errors, $form);
        },
        contentType: false,
        processData: false,
    });
    } catch (error) {
        alert("Here");
    }
});


   $('#rfp-status-form').on('click', '.delete', function (e) {
            lookup_id = $(this).data('id');
            rfp_id = $(this).data('rfp-id');
            var url = '{{route("remove-rfp-tracking-step",[":lookup_id",":rfp_id"])}}',
            url = url.replace(':lookup_id', lookup_id);
            url = url.replace(':rfp_id', rfp_id);
            swal({
                    title: "Are you sure?",
                    text: "You will not be able undo this action",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, remove",
                    showLoaderOnConfirm: true,
                    closeOnConfirm: false
                },
                function () {
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function (data) {
                            if (data.success) {
                                swal({
                                    title: 'Success',
                                    text: 'The RFP Tracking step has been successfully removed',
                                    icon: "success",
                                    button: "OK",
                                }, function () {
                                    location.reload();
                                });
                            } else {
                                alert(data.message);
                            }
                        },
                        error: function (xhr, textStatus, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                        },
                        contentType: false,
                        processData: false,
                    });
                });
        });
});
</script>
@endsection
