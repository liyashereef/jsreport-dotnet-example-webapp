{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Rating Tolerance')

@section('content_header')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<h1>Rating Tolerance</h1>
@stop

@section('content')
    {{ Form::open(array('url'=>'#','id'=>'rating-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
        {{csrf_field()}}
       {{--  {{ Form::hidden('id', $existing_template['id'] or '') }} --}}

        <div class="row form-align">
            <div class="col-md-5 col-sm-6 col-xs-12"> </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-styled" >
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Rating</th>
                        <th>Min Value</th>
                        <th>Max Value</th>
                    </tr>
                </thead>
                <tbody class="template-setting-tbody" id="template-setting-tbody">
                    @foreach($template_setting_rules as $key=>$each_rule)
           <tr>
    <td class="sl-no">{{isset($key)?($key+1):"1"}}<input type="hidden" class="row-no" name="position[]" value="{{isset($key)?($key):"0"}}"/></td>
   
    <td>
        <div class="form-group color" id="rule_color_{{isset($key)?($key):"0"}}">
        <input type="hidden" class="min-item form-control" name="rating_id[]" readonly="true"  value="{{$each_rule['id']}}"/>
         <input type="text" class="min-item form-control" name="rating[]" readonly="true"  value="{{$each_rule['rating']}}"/>
        <span class="help-block"></span>
        </div>
    </td>
    <td><div class="form-group min" id="min_value_{{isset($key)?($key):"0"}}">
        @if($key==0)
        <label>Greater than or equal to</label>
         <input type="text" class="min-item form-control  table-option-adjust" name="min_value[]" readonly="true"  value="1"/>
        <span class="help-block"></span>
        @else
        <label>Greater than</label> <span style='padding-left : 72px'></span>
         <input type="text" class="min-item form-control  table-option-adjust" name="min_value[]" id="min_{{isset($key)?($key):'0'}}" readonly="true" value="{{!empty($ratings)?$ratings[$key]:''}}"/>
        <span class="help-block"></span>
        @endif
       
        </div>
        </div>
    </td>
    <td><div class="form-group max" id="max_value_{{isset($key)?($key):"0"}}">
         <label>Less than or equal to</label>
        @if($key==4)   
        <input type="text" class="max-item form-control  table-option-adjust" name="max_value[]" readonly="true"  value="5"/>
        @else
         <input type="text" class="max-item form-control  table-option-adjust maxvalue" name="max_value[]"   id="max_{{isset($key)?($key):"0"}}"  value="{{$ratings[$key+1] or ''}}"/>
         @endif
        <span class="help-block"></span>
        </div>
        </div>
    </td>
</tr>
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
$(document).ready(
    function() {
        $('.maxvalue').keyup(
            function() {
               var entered_val= $(this).val();
               var textbox_id=$(this).attr('id');
               var key_id=textbox_id.match(/\d+/)
               key_id++;
               var intRegex = /^\d+$/;
               var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
              if(intRegex.test(entered_val) || floatRegex.test(entered_val)) {
               $('#min_'+key_id).val(entered_val);
           }
            });
    })
    $(function () {
        rows = $('#template-setting-tbody tr').length;
        /* Posting data to TemplateSettingController - Start*/
        $('#rating-form').submit(function (e) {
                e.preventDefault();
                var $form = $(this);
                url = "{{ route('rating-tolerance.store') }}";
                var formData = new FormData($('#rating-form')[0]);
                $.ajax({
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function (data) {
                        if (data.success) {
                            swal("Saved", "Rating Tolerance been updated successfully", "success");
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
