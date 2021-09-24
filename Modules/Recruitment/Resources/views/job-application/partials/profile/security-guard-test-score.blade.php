<div class="form-group row {{ $errors->has('test_score_percentage') ? 'has-error' : '' }}"  id="test_score_percentage">
    <label for="test_score_percentage" class="col-sm-5 col-form-label">What was your test score on the Ontario Security Guard exam? (Percent)<span class="mandatory">*</span></label>
    <div class="col-sm-7">
        {{ Form::number('test_score_percentage',old('test_score_percentage',isset($candidate->guardingexperience->test_score_percentage) ? $candidate->guardingexperience->test_score_percentage :""),array('class' => 'form-control','id'=>'test_scorepercentage','placeholder'=>'Percentage','step'=>'0.01','max'=>'100')) }}
        <div class="form-control-feedback">{!! $errors->first('test_score_percentage') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
@if (isset($policybase64))
    <div class="form-group row {{ $errors->has('force_file') ? 'has-error' : '' }}"
         id="ots_file"
         @if (isset($candidate->guardingExperience->test_score_path))
         style="display: none"
            @endif
    >
        <label for="test_score_path" class="col-sm-5 col-form-label">
            Please upload a copy of your test score (this will be verified as part of our background check)<span class="mandatory">*</span>
        </label>
        <div class="aws_upload_form">
            <input type="hidden" name="acl" value="private">
            <input type="hidden" name="success_action_status" value="201">
            <input type="hidden" name="policy" value="{{$policybase64}}">
            <input type="hidden" name="X-amz-credential" value="{{$access_key}}/{{$short_date}}/{{$region}}/s3/aws4_request">
            <input type="hidden" name="X-amz-algorithm" value="AWS4-HMAC-SHA256">
            <input type="hidden" name="X-amz-date" value="{{$iso_date}}">
            <input type="hidden" name="X-amz-expires" value="{{$presigned_url_expiry}}">
            <input type="hidden" name="X-amz-signature" value="{{$signature}}">
            <input type="hidden" name="key" id="attachment-ots-key" value="">
            <input type="hidden" name="Content-Type" id="attachment-ots-type" value="">
            <div class="col-sm-12" >
                <div id="upload-ots-attachment-file" class="row form-group">
                    <div class="col-sm-7">
                        <input
                                class="form-control"
                                type="hidden" id="attachment-file-ots-aws" data-aid=""
                                name="test_score_path"
                                @if (isset($candidate->guardingExperience->test_score_path))
                                disabled
                                @endif
                        />
                        <input
                                class="form-control" type="file"
                                id="ots-attachment-file" data-aid=""
                                name="test_score_file"
                                @if (isset($candidate->guardingExperience->test_score_path))
                                disabled
                                style="display: none"
                                @endif
                        />
                        <span class="help-block"></span>
                    </div>
                    <div class="col-sm-2">
                        <input class="button btn btn-edit file_attachment_upload_btn" id="ots_upload_button" type="button" value="Upload" />
                    </div>
                    <div class="col-sm-2" id="attachement-success" style="display:none;">
                        <span  style="color: #35af3f;">Uploaded Successfully</span>
                    </div>
                </div>
                {{--  <input  id="file_attachment_upload_btn"  data-aid="{{$each_document['id'] }}" class="button btn btn-edit file_attachment_upload_btn" type="button" value="Upload" data-id="{{$attachment->id }}"> --}}
            </div>
            <div class="col-sm-7" id="attachment-results">
                <!-- server response here -->
            </div>
        </div>
    </div>
@endif
@if (isset($candidate->guardingExperience->test_score_path))
    <div class="form-group row {{ $errors->has('test_score_path') ? 'has-error' : '' }}" id="ots_file_set">
        <label for="ots_file" class="col-sm-5 col-form-label">
            Please upload a copy of your test score (this will be verified as part of our background check)<span class="mandatory">*</span>
        </label>
        <div class="col-sm-4">
            <div class="row">
                <div class="col-sm-6">
                    <div id="ots_file_div">
                        <input type="hidden" name="test_score_path" value="{{ $candidate->guardingexperience->test_score_path}}">
                        <a class="nav-link score-document" target="_blank" href="{{Storage::disk('s3-recruitment')->temporaryUrl($candidate->guardingexperience->test_score_path,Carbon::now()->addMinutes(30)) }}">
                            Click here to download the file
                        </a>
                    </div>
                </div>
                <div class="col-sm-4">
                    <input class="button btn btn-edit score_attachment_remove_btn" onclick="removeTestScoreDocument(this)" type="button" value="Remove" style="margin: 7px;">
                </div>
            </div>
        </div>
    </div>
@endif

@if (isset($policybase64))
    <script>
        $("#ots_upload_button").click(function(e) {
            e.preventDefault();
            let formDiv = $(this).closest('.aws_upload_form')[0];
            console.log(formDiv);
            // debugger;
            // $('#aws_upload_attachment_form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            the_file = $("#ots-attachment-file")[0].files[0];
            candidate_id = {{$candidate->id}};
            var filename = 'temp/'+candidate_id+'/ots/'+Date.now() + '.' + the_file.name.split('.').pop();
            $("#attachment-ots-key").val(filename);
            $("#attachment-ots-type").val(the_file.type);
            var results = $(this).find("#video-results");
            var success = 'attachement-success';
            $(this).find("input[name=key]").val(filename);
            $(this).find("input[name=Content-Type]").val(the_file.type);
            var file_attach_id=$(this).find('input[name=file]').data('aid')
            var post_url = "http://{{$my_bucket}}.s3-{{$region}}.amazonaws.com" //get form action url

            var formData = new FormData(); //Creates new FormData object
            formData.append('acl',"private");
            formData.append('success_action_status',201);
            formData.append('policy', $(formDiv).find('[name="policy"]').val());
            formData.append('X-amz-credential', $(formDiv).find('[name="X-amz-credential"]').val());
            formData.append('X-amz-algorithm', $(formDiv).find('[name="X-amz-algorithm"]').val());
            formData.append('X-amz-date', $(formDiv).find('[name="X-amz-date"]').val());
            formData.append('X-amz-expires', $(formDiv).find('[name="X-amz-expires"]').val());
            formData.append('X-amz-signature', $(formDiv).find('[name="X-amz-signature"]').val());
            formData.append('key', $(formDiv).find('[name="key"]').val());
            formData.append('file', the_file);
            formData.append('Content-Type', "");
            $(this).find($(results)).show();
            var linkurl=formDiv;
            $.ajax({
                url : post_url,
                type: 'post',
                datatype: 'json',
                data: formData,
                contentType: false,
                processData:false,
                xhr: function(){
                    var xhr = $.ajaxSettings.xhr();
                    if (xhr.upload){
                        var progressbar = $("<div>", { style: "background:#607D8B;height:10px;margin:10px 0;" }).appendTo(results); //create progressbar
                        xhr.upload.addEventListener('progress', function(event){
                            var percent = 0;
                            var position = event.loaded || event.position;
                            var total = event.total;
                            if (event.lengthComputable) {
                                percent = Math.ceil(position / total * 100);
                                progressbar.css("width", + percent +"%");
                            }
                        }, true);
                    }
                    return xhr;
                }
            }).done(function(response){

                var url = $(response).find("Location").text(); //get file location
                console.log(url);
                var the_file_name = $(response).find("Key").text(); //get uploaded file name
                //  $("#results").html("<span>File has been uploaded, Here's your file <a href=" + url + ">" + the_file_name + "</a></span>"); //response

                var added=false;
                $.map(items, function(elementOfArray, indexInArray) {
                    if (elementOfArray.id == file_attach_id) {
                        elementOfArray.filename = the_file_name;
                        added = true;
                    }
                });
                if (!added) {
                    items.push({id: file_attach_id, filename: the_file_name})
                }


                $(linkurl).find('#attachment-file-ots-aws').val(the_file_name);
                $(linkurl).find('#'+success).show();
                $(linkurl).find('#video-results').hide();

            });
        });
    </script>
@endif
