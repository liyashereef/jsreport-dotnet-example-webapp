{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'STC Report Colors')

@section('content_header')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<h1>STC Report Colors</h1>
@stop

@section('content')
    {{ Form::open(array('url'=>'#','id'=>'template-settings-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
        {{csrf_field()}}
       {{--  {{ Form::hidden('id', $existing_template['id'] or '') }} --}}

        <div class="row form-align">
            <div class="col-md-5 col-sm-6 col-xs-12"> </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-styled dataTable" >
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Color Name</th>
                        <th>Min Value</th>
                        <th>Max Value</th>
                    </tr>
                </thead>
                <tbody class="template-setting-tbody" id="template-setting-tbody">
                    @foreach($template_setting_rules as $key=>$each_rule)
                    @include('admin::partials.stctemplaterow')
                    @endforeach
                </tbody>
            </table>
        </div>



        <div class="modal-footer">
            {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
            <button class="btn btn-primary blue" type="reset">Cancel</button>
        </div>
 </div>
    {{ Form::close() }}
    @stop
    @section('js')
    <script>

    $(function () {
        rows = $('#template-setting-tbody tr').length;
        if(rows < 1){
            $('#remove-new-rule-minus').hide();
        }

        /* Posting data to TemplateSettingController - Start*/
        $('#template-settings-form').submit(function (e) {
                e.preventDefault();
                var $form = $(this);
                url = "{{ route('stc-template-rule.store') }}";
                var formData = new FormData($('#template-settings-form')[0]);
                $.ajax({
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function (data) {
                        if (data.success && data.success=="true") {
                            swal("Saved", "STC report colors has been updated successfully", "success");
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                        } else {
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                            console.log(data);
                        }
                    },
                    fail: function (response) {
                        console.log(response);
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
