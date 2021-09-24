{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Template Settings')

@section('content_header')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        templateSettingsRowHtml =  '{!!preg_replace( "/\r|\n/", "",(View::make('admin::partials.templatesettingrow')->with('arr_color', $arr_color)->render()))!!}';
    </script>
</head>
<h1>Template Settings</h1>
@stop

@section('content')
    {{ Form::open(array('url'=>'#','id'=>'template-settings-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
        {{csrf_field()}}
        {{ Form::hidden('id', $existing_template['id'] or '') }}

        <div class="row form-align">
            <div class="col-md-5 col-sm-6 col-xs-12">1.Question Color Rule</div>
            <div class="form-group col-md-3 col-sm-12 col-xs-12 text-align-right" id="template_min_value">
                <label class="padding-right-5">Min Value <span class="mandatory">*</span></label>
                <input type="text" name="template_min_value" pattern="^\d{1,4}(\.\d{4})?$" placeholder="00.0000" class="form-control option-adjust template-min-value" id="template-min-value" value="{{$existing_template['min_value'] or ''}}"/>
                <span class="help-block"></span>
            </div>
            <div class="form-group col-md-3 col-sm-12 col-xs-12 text-align-right" id="template_max_value">
                <label class="padding-right-5">Max Value <span class="mandatory">*</span></label>
                <input type="text" name="template_max_value" pattern="^\d{1,4}(\.\d{4})?$" placeholder="00.0000" class="form-control option-adjust template-max-value" id="template-max-value" value="{{$existing_template['max_value'] or ''}}"/>
                <span class="help-block"></span>
            </div>
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
                        @include('admin::partials.templatesettingrow')
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="col-md-12 col-sm-12 col-xs-12 text-align-right add-more-rule">
                <a title="Add new rule" href="javascript:;" class="add_button margin-left-table-btn" id="add-new-rule-plus" onclick="rulesObj.addRule(this)">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                </a>
                <a title="Remove rule" href="javascript:;" class="remove_button margin-left-table-btn" id="remove-new-rule-minus">
                    <i class="fa fa-minus" aria-hidden="true"></i>
                </a>
        </div>


        <div class="row padding-top-20">
            <div class="col-md-2 col-sm-6 col-xs-12">2.Update Color: </div>
            <div class="form-group col-md-5 col-sm-12 col-xs-12 text-align-right front" id="template_limit">
                <label class="padding-right-5">If last update ></label>
                <select class="form-control option-adjust" name="template_limit">
                    <option value=""  class="clr-item">Choose one</option>
                    <option value="1" @if(isset($existing_template['last_update_limit']) && $existing_template['last_update_limit'] == 1) selected @endif>1 Payperiod</option>
                    <option value="2" @if(isset($existing_template['last_update_limit']) && $existing_template['last_update_limit'] == 2) selected @endif>2 Payperiods</option>
                    <option value="3" @if(isset($existing_template['last_update_limit']) && $existing_template['last_update_limit'] == 3) selected @endif>3 Payperiods</option>
                    <option value="4" @if(isset($existing_template['last_update_limit']) && $existing_template['last_update_limit'] == 4) selected @endif>4 Payperiods</option>
                </select>
                <span class="help-block"></span>
            </div>
            <div class="form-group col-md-5 col-sm-12 col-xs-12 text-align-right" id="template_color">
                <label class="padding-right-5">Client dot color will</label>
                <select class="form-control option-adjust" name="template_color">
                    <option value=""  class="clr-item">Choose one</option>
                    @foreach($arr_color as $color)
                        <option value="{{$color['id']}}"  class="limit-clr-item" @if(isset($existing_template['color_id']) && $existing_template['color_id'] == $color['id']) selected @endif>{{$color['color_name']}}</option>
                    @endforeach
                </select>
                <span class="help-block"></span>
            </div>
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

        rulesObj = {

            htmlRow: templateSettingsRowHtml,
            initRow: function(){
                $("#template-setting-tbody").append(rulesObj.htmlRow);
                $("#template-setting-tbody tr").find(".min-item:last, .max-item:last").attr('pattern','^\\d{1,4}(\\.\\d{4})?$');
            },
            getSettingsRow:function(){
                return htmlRow;
            },
            addRule: function(currObj){
                ruleElementCount =  this.getLastRowNo();
                ruleElementCount++;
                var htmlStr = rulesObj.htmlRow;
                $('#template-setting-tbody').append(htmlStr);
                var newHtmlObj = $('#template-setting-tbody tr:last');
                this.prepareNextRow(newHtmlObj);
            },
            getLastRowNo:function(){
                rule_element_count_value = $("#template-setting-tbody tr:last").find(".row-no").val();
                return rule_element_count_value;
            },
            prepareNextRow: function(htmlObj){
                var positionHtml = (ruleElementCount*1+1)+'<input type="hidden" class="row-no" name="position[]" value="'+ruleElementCount+'"/>';
                $(htmlObj).find("[class='form-group color']").attr('id','rule_color_'+(ruleElementCount));
                $(htmlObj).find("[class='form-group min']").attr('id','min_value_'+(ruleElementCount));
                $(htmlObj).find("[class='form-group min']").find('input').attr('pattern','^\\d{1,4}(\\.\\d{4})?$');
                $(htmlObj).find("[class='form-group max']").attr('id','max_value_'+(ruleElementCount));
                $(htmlObj).find("[class='form-group max']").find('input').attr('pattern','^\\d{1,4}(\\.\\d{4})?$');
                $(htmlObj).find("[class='sl-no']").html(positionHtml);
            }
        }
        {!! (count($template_setting_rules)>0) ? 'rulesObj.addRule($("#add-new-rule-plus"))' : 'rulesObj.initRow()' !!}

        /* Posting data to TemplateSettingController - Start*/
        $('#template-settings-form').submit(function (e) {
                e.preventDefault();
                var $form = $(this);
                url = "{{ route('templatesettings') }}";
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
                            swal("Saved", "Template settings has been updated successfully", "success");
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


        /* Removing Question Color Rule - Start*/
        $('#remove-new-rule-minus').on('click',function(){
            first_id = $('#template-setting-tbody tr:first').find('td div').attr('id');
            last_id = $('#template-setting-tbody tr:last').find('td div').attr('id');
            rows = $('#template-setting-tbody tr').length;
            if(last_id != first_id){
                $('#template-setting-tbody tr:last').remove();
            }
            if(rows == 2){
                $('#remove-new-rule-minus').hide();
            }
        });

        /* Show Remove Icon on Adding new rules- Start*/
        $('#add-new-rule-plus').on('click',function(){
            $('#remove-new-rule-minus').show();
        });
    });
    </script>
    @stop
