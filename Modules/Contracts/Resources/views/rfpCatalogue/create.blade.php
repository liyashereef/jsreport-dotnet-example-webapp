@extends('layouts.app')
@section('content')
<div class="table_title">
	@if(isset($rfpCatalogue_data))
    <h4>Edit RFP Catalog</h4>
    @else
    <h4>Add RFP Catalog</h4>
    @endif
</div>
<div id="rfp-catalogue" class="container candidate-screen"><br>
    {{ Form::open(array('url'=>'#','id'=>'rfp-catalogue-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
    {{csrf_field()}}
    {{ Form::hidden('id', null) }}
    {{ Form::hidden('customer_id', $customer_id) }}
     <div class="">
        <div class="form-group row" id="rfpCatalogueTopic">
            <label for="title" class="col-sm-4 control-label">Topic <span class="mandatory">*</span></label>
            <div class="col-sm-8">
                <input type="text"
                       class="form-control"
                       name="rfpCatalogueTopic"
                       value="@if(isset($rfpCatalogue_data)){{$rfpCatalogue_data->topic}}@endif ">
                <small class="help-block"></small>
            </div>
        </div>
        <div class="form-group row" id="rfpCatalogueGroup">
            <label for="group" class="col-sm-4 control-label">Group <span class="mandatory">*</span></label>
            <div class="col-sm-8">
                <select class="form-control select2" name="rfpCatalogueGroup" value="">
                    <option selected value="">Please Select</option>
                    @foreach($rfpCatalogueGroups as $id=>$eachrfpCatalogueGroups)
                        <option value="{{$id}}" @if(isset($rfpCatalogue_data) && $rfpCatalogue_data->group_id == $id ) {{ 'selected' }} @endif>{{$eachrfpCatalogueGroups}}</option>
                    @endforeach
                </select>
                <small class="help-block"></small>
            </div>
        </div>
        <div class="form-group row" id="rfpCatalogueDescription">
            <label for="custom_subject" class="col-sm-4 control-label">Description<span class="mandatory">*</span></label>
            <div class="col-sm-8">
                <textarea class="form-control" rows="6" name="rfpCatalogueDescription" placeholder="Description">@if(isset($rfpCatalogue_data)) {{$rfpCatalogue_data->description}} @endif </textarea>
                <small class="help-block"></small>
            </div>
        </div>
        <div class="form-group row" id="employee_name">
            <label for="employee_name" class="col-sm-4 control-label">Uploaded By</label>
            <div class="col-sm-8">
                <span>@if(isset($rfpCatalogue_data)) {{$rfpCatalogue_data->getCreatedby->full_name}} @else {{$user_name}} @endif</span>
                <small class="help-block"></small>
            </div>
        </div>
        <div class="form-group row" id="all_attachments">
            <label class="col-sm-4" >Document<span class="mandatory">*</span></label>
            <div class="col-sm-8">
                <div class="attachment_div">
                    <div class="attachement-control col-sm-12" id="attachment_div_po">
                        <div class="form-group row col-sm-12">
                            <label for="file_attachment" class="col-sm-4 control-label">Upload File</label>
                            <div class="col-sm-8" id="attachment_div">
                                <input type="file" class="form-control file_attachment scroll-clear"
                                       id="file_attachment"
                                       name="file_attachment"
                                       placeholder="Attachment"
                                       value=""
                                       accept=".docx,.doc,.odt">
                                <small class="help-block" id="attachment-validation"></small>
                            </div>
                        </div>
                        <div class="form-group row  col-sm-12 short_description_file_div" id="short_description">
                            <label for="short_description" class="col-sm-4 control-label">File Name</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="short_description" id="short_descriptions">
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group row col-sm-12 file_info_btn_div">
                            <div class="col-sm-12">
                                <span class="upload-message" style="display: none"> Uploading...</span>
                                <input id="file_attachment_upload_btn"
                                       class="button btn btn-edit file_attachment_upload_btn"
                                       type="button" value="Upload">
                            </div>
                        </div>
                    </div>
                    <div class="attachment-list col-sm-12" id="attachment_div_po">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-8 pull-right">
        {{ Form::submit('Save', array('class'=>'button btn btn-edit','id'=>'mdl_save_change'))}}
        {{ Form::reset('Cancel', array('class'=>'btn btn-edit','onclick'=>'window.history.back();'))}}
    </div>
    {{ Form::close() }}
</div>
@endsection
@section('scripts')

    <script>

        $(function () {
            var rfp_catalogue_data = {!! json_encode($rfpCatalogue_data) !!};
            $('.select2').select2();
            if(rfp_catalogue_data!=null)
            {
                $('input[name="id"]').val(rfp_catalogue_data['id'])
                console.log(rfp_catalogue_data['attachment_details'])
                onFileUploadSuccess(rfp_catalogue_data['attachment_details']);
            }
            $('.select2').select2();
        });
        var filesize = 50;
        window.attachment = {};
        window.attachment.moduleName = 'rfp-catalogue'
        window.attachment.extensionArr = Array('doc', 'docx', 'odt');
        window.attachment.fileCount = 1;



        $("#file_attachment").on('change',function(){$("#file_div .help-block").text("")})

        /*Rfp-Catalogue - Save - Start*/
        $('#rfp-catalogue-form').submit(function (e) {
            e.preventDefault();
            if($('#file_attachment').val() != '' || ($('#file_attachment').val() == '' && $('#short_description').val() != '')){
                $('#attachment-validation').addClass('has-error').text('Please upload the file');
                return false;
            }
            var $form = $(this);
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
            url = '{{ route('rfp-catalogue.create')}}';
            var formData = new FormData($('#rfp-catalogue-form')[0]);
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
                                title: "Success",
                                text:  data.message,
                                type: "success"
                            },
                            function(){
                                window.location.href =  "{{route('rfp-catalogue.view')}}";
                            });

                    } else {
                        swal("Oops", "Could not create", "warning");
                    }
                },
                fail: function (response) {
                    console.log(response);
                    swal("Oops", "Something went wrong", "warning");
                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form);
                },
                contentType: false,
                processData: false,
            });
        });


    </script>
    @include('contracts::partials.fileupload-script')
@endsection
