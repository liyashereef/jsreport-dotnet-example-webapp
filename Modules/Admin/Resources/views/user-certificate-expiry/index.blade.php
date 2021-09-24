@extends('adminlte::page')
@section('title', 'Task Update Interval')
@section('content_header')
<h1>Document Expiry Notification Settings</h1>
@stop
@section('css')
    <style>
        .mb2{
            padding-bottom: 8px !important;
        }
    </style>
@endsection
@section('content')

{{ Form::open(array('url'=>'#','id'=>'onboarding-interval-form','class'=>'form-horizontal', 'method'=> 'POST')) }}

<div id="dynamic-section-notification" class="el_fields row" style="padding-top: 40px;">
    <label for="interval" class="col-md-5 interval_label" id="label_--position_num--">
        <span>
            Number of days prior
            to
            start mobile app notifications
        </span>
        <span class="mandatory">*</span>
    </label>
    <div class="col-md-4">
        {{ Form::number('notification_reminder[]', null,
                    array(
                    'class'=>'form-control notification-reminder-days',
                    'min'=>'1',
                    'id'=>"intervals--position_num--",
                    'placeholder'=>'Number of Days','required'=>true))
        }}
    </div>
</div>

<br />
<br />
<br />
<div id="dynamic-section-email">
</div>

<div class="modal-footer row col-sm-12">
    {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
    <a href="" class="btn btn-primary blue">Cancel</a>
    {{ Form::close() }}
</div>

<template id="more-content">
    <div class="el_fields row" id="--name--_row_--position_num--" data-elid="--position_num--">
        <label for="interval" class="col-md-5 interval_label" id="label_--position_num--">
            <span>
                Number of days prior
                to
                start email remainder
            </span>
            <span class="mandatory">*</span>
        </label>
        <div class="col-md-4">
            {{ Form::number('email_reminder[]', null,
                    array(
                    'class'=>'form-control email-reminder-days',
                    'min'=>'1',
                    'id'=>"intervals--position_num--",
                    'placeholder'=>'Number of Days','required'=>true)) }}
            <small class="help-block"></small>
        </div>

        <div class="col-sm-3">
            <a title="Add More" href="javascript:;" class="add_button_email" data-elid="--position_num--">
                <i class="fa fa-plus" aria-hidden="true"></i>
            </a>
            <a href="javascript:void(0);" class="remove_button_email" title="Remove" data-elid="--position_num--">
                <i class="fa fa-minus" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</template>



@stop
@section('js')


<script src="{{ asset('js/moreel.js') }}"></script>
<script>
$(function () {
    let emailDivParam = {
        containerDiv: '#dynamic-section-email',
        addButton: '.add_button_email',
        addMaxCount: 3,
        removeButton: '.remove_button_email',
        removeOne: true,
        form: '#onboarding-interval-form',
        afterAdd: function (el) {
            let totalLength = $('#dynamic-section-email>div').length;
            let label = $('#dynamic-section-email>div:last label>span:first').text();
            let newLabel = label + ' ' + totalLength;
            $('#dynamic-section-email>div:last label>span:first').text(newLabel);
        },
    };
    let emailRemainder = new MoreEl('email', emailDivParam);
    emailRemainder.initElDiv(true);
    var emailDataVar = JSON.parse('{!! $emailSettings  !!}');
    if (emailDataVar.length == 0) {
        emailRemainder.initElDiv();
    } else {
        emailRemainder.initElDiv(true);
    }

    if (emailDataVar.length > 0) {
        for (let i = 0; i < emailDataVar.length; i++) {
            let rowEl = emailRemainder.addRow();
            $(rowEl).find(".email-reminder-days").val(emailDataVar[i].value);
        }
    }else {
        $(".email-reminder-days").val('');
    }

    $('.notification-reminder-days').val('{{$notificationValue}}');
});
</script>

<script>
    $(function () {

        $('#onboarding-interval-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            $('.form-group').removeClass('has-error').find('.help-block').text('');
            url = "{{ route('userCertificateExpirySettings.store') }}";
            var formData = new FormData($('#onboarding-interval-form')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'accept': 'application/json',
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                        swal({title: "Saved", text: "Certificate expiry notification settings saved successfully", type: "success"},
                                function () {
                                    location.reload();
                                }
                        );
                    } else {
                        alert(data);
                    }
                },
                fail: function (response) {
                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form, true);
                },
                contentType: false,
                processData: false,
            });
        });
    });


</script>
@stop
