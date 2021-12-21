@extends('layouts.appnew')
@section('content')
<div id='app'>
    <div class="table_title">
        <h4>Chat</h4>
    </div>


    <div class="col-md-12 filter-wrapper">
        <div class="row">
            <div class="col-lg-1 col-md-2"><label class="label-name">Customer</label></div>
            <div class="col-md-2" >
                {{ Form::select('clientname-filter',[''=>'Select customer']+$customer_details_arr,null,array('class'=>'form-control select2 option-adjust client-filter', 'id'=>'clientname-filter', 'style'=>"width: 100%;")) }}
            </div>
            <div class="col-lg-1 col-md-2 " style="text-align: right;"><label class="label-name">Employee </label></div>
            <div class="col-md-2">
                <select class="form-control option-adjust employee-filter select2" name="employee-filter" id="employee-name-filter">
                    <option value="0">Select Employee</option>
                    @foreach($user_list as $each_userlist)
                    <option value="{{$each_userlist->id}}">{{ $each_userlist->first_name.' '.$each_userlist->last_name.' ('.$each_userlist->employee->employee_no.')'}}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input class="button btn btn-primary blue" type="button" id="chatbtn" value="Chat">
                <input class="button btn btn-primary blue" type="button" id="textbtn" value="Text">

            </div>
            @can('view_chat_history')
            <div class="col-lg-4 col-md-2" style="text-align: right;">
                <a href="{{url('chat/view-history')}}" class="button btn btn-primary blue">View History</a>
            </div>
            @endcan
        </div>
    </div>
    <br>
    <div class="container">
        <chat-app :user="{{ auth()->user() }}"></chat-app>
    </div>
</div>



<div class="modal fade" id="textModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Send Text Message</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'text-chat-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('candidate-id', null) }}
            {{ Form::hidden('type', 1) }}
            
            <div class="modal-body">
                <div class="form-group row" id="password_reset_mail">
                    <label for="emailScript" class="col-sm-2 col-form-label">To</label>
                    <div class="col-sm-10">
                        <label id="to" for="emailScript" class="col-sm-10 col-form-label"></label>
                        {{Form::hidden('contact_id',null, ['id' => 'contact_id',])}}
                    </div>
                </div>
                <div class="form-group row" id="text">
                    <label for="emailScript" class="col-sm-2 col-form-label">Message</label>
                    <div class="col-sm-10">
                        {{Form::textarea('text',old('text',@$mail_content['body']),array('class'=>'form-control','id'=>'text'))}}
                        <small class="help-block"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {{ Form::submit('Send', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::submit('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

<style>
    
    div.filter-wrapper {
        padding: .5rem !important;
        border: 1px solid #e9ecef !important;
    }

    .container {
        max-width: 100% !important;
        padding: 0px 25% 0px 0px;
    }

    .col-form-label {
        padding-left: 0% !important;
    }

    .modal-footer {
        justify-content: center !important;
    }
    label{
        margin-bottom: 0px !important;
        vertical-align: middle;
    }
</style>
@endsection
@section('scripts')
<script>
    $(function() {
       // $('.select2').select2();
        $('#clientname-filter').on('change', function(e) {
            type = $('input[name=customer-contract-type]:checked').val();
            var client_id = $('#clientname-filter').val() ? $('#clientname-filter').val() : 0;
            var base_url = "{{route('chat.allocated-employee',[':client_id'])}}";
            var url = base_url.replace(':client_id', client_id);
            $.ajax({
                url: url,
                type: 'GET',
                data: client_id,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    if (data.success) {
                        $('#employee-name-filter').find('option:not(:first)').remove();
                        $.each(data.data, function(key, value) {
                        $('#employee-name-filter').append($("<option></option>")
                               .attr("value",value.id)
                             .text(value.name));
                    });
                    } else {
                        console.log(data);
                        swal("Oops", "Something went wrong", "warning");
                    }
                },
                error: function(xhr, textStatus, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                    swal("Oops", "Something went wrong", "warning");
                    if (xhr.status === 401) {
                        window.location = "{{ route('login') }}";
                    }
                },
                contentType: false,
                processData: false,
            });

        });

        $("#chatbtn").on("click", function(e) {
            e.preventDefault();
            var contact_id = $("#employee-name-filter option:selected").val();
            var url = '{{ route("chat.contact.store") }}';
            let form_data = new FormData();
            form_data.append('contact_id', contact_id);
            $.ajax({
                url: url,
                type: 'POST',
                data: form_data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    if (data.success) {
                        console.log(data);
                    } else {
                        console.log(data);
                        swal("Oops", "Employee is already in your contact", "warning");
                    }
                },
                error: function(xhr, textStatus, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                    swal("Oops", "Something went wrong", "warning");
                    if (xhr.status === 401) {
                        window.location = "{{ route('login') }}";
                    }
                },
                contentType: false,
                processData: false,
            });
         });






        $("#textbtn").on("click", function(e) {
            if ($("#employee-name-filter option:selected").text() != 'Select Employee') {
                $('#textModal #to').text($("#employee-name-filter option:selected").text());
                $('#textModal #contact_id').val($("#employee-name-filter option:selected").val());

                $("#textModal").modal();
            } else {
                swal("Warning", "Please select an employee", "warning");
            }

        });



         /* Posting data to AssignmentTypeLookup Controller - Start*/
      /*   $('#text-chat-form').submit(function (e) {
            e.preventDefault();

            formSubmit($('#text-chat-form'), "{{ route('chat.conversation-send') }}");
        });*/


        $('#text-chat-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            var url = "{{ route('chat.conversation-send') }}";
            var formData = new FormData($('#text-chat-form')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.id !=null) {

                        swal({
                                title: "Success",
                                text: "Message has been sent",
                                type: "info",
                                showCancelButton: false,
                                showLoaderOnConfirm: true,
                                closeOnConfirm: true
                            },
                            function () {
                                $("#textModal").modal('hide');
                            });



                    } else {
                        alert("Not able to send message");
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




    });
</script>
@stop