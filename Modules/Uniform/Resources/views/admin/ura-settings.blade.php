@extends('adminlte::page')
@section('title', 'URA Settings')
@section('content_header')
<h1>URA Settings</h1>
@stop @section('content')
<div class="row">
    <div class="col-md-12">
        <form method="POST" id="ura-rates-form" url="#">
            {{ csrf_field() }}
            <div class="form-group" id="uniform-purchase-threshold">
                <div class="row">
                    <div class="col-md-4"> <label for="uniform-purchase-threshold">Uniform Purchase Threshold <span class="mandatory">*</span></label></div>
                    <div class="col-md-4">
                        {{ Form::number('uniform-purchase-threshold',old('uniform-purchase-threshold',$uniformPurchseThreshold),array('class'=>'form-control','required'=>true,'placeholder'=>''))}}
                        <small class="help-block"></small>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class='button btn btn-primary blue' id="save-btn">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
@section('js')
<script>
    const ura = {
        table: null,
        init() {
            let root = this;
            $('#save-btn').click(function(e) {
                e.preventDefault();
                root.onSave();
            });
        },
        onSave() {
            let root = this;
            let $form = $('#ura-rates-form');
            let formData = new FormData($('#ura-rates-form')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('ura.settings.store')}}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    if (data.success) {
                        swal("Success", "URA Settings has been successfully updated", "success");
                        $('.form-group').removeClass('has-error').find('.help-block').text('');
                    } else {
                        //alert(data);
                        swal("Alert", "Something went wrong", "warning");
                    }
                },
                error: function(xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form);
                },
            });
        },
    };


    $(function() {
        ura.init();
    });
</script>
@endsection