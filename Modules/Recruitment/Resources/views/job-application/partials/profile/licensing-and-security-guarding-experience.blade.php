<div class="form-group row {{ $errors->has('guard_licence') ? 'has-error' : '' }}"  id="guard_licence">
    <label for="guard_licence" class="col-sm-5 col-form-label">Do you have a valid security guarding licence in Ontario with First Aid and CPR?</label>
    <div class="col-sm-7">
        {{ Form::select('guard_licence',[null=>'Some positions DO NOT require a security licence.  Select "No" if this does not apply to you.',"Yes"=>"Yes","No"=>"No"],old('guard_licence',isset($candidate->guardingexperience->guard_licence) ? $candidate->guardingexperience->guard_licence :""),array('class' => 'form-control','id'=>'guard_licences','required'=>TRUE,'max'=>"2900-12-31")) }}
        <div class="form-control-feedback">{!! $errors->first('guard_licence') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div id="guard_licence_start_qstn" class="{{ @$candidate->guardingexperience->guard_licence!='Yes'?'hide-this-block':'' }}">
    <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Licence Start Dates</label>
    <div class="form-group row {{ $errors->has('start_date_guard_license') ? 'has-error' : '' }}"  id="start_date_guard_license">
        <label for="start_date_guard_license" class="col-sm-5 col-form-label">Please note the start date when you first acquired your guarding licence in Ontario?</label>
        <div class="col-sm-7">
            {{Form::text('start_date_guard_license',old('start_date_guard_license',isset($candidate->guardingexperience->start_date_guard_license) ? $candidate->guardingexperience->start_date_guard_license :""),array('class'=>' form-control datepicker','id'=>'guardlicence','placeholder'=>"If you have a guarding licence, when did you acquire it? ",'max'=>"2900-12-31",'readonly'=>true))}}
            <div class="form-control-feedback">{!! $errors->first('start_date_guard_license') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('start_date_first_aid') ? 'has-error' : '' }}"  id="start_date_first_aid">
        <label for="start_date_guard_license" class="col-sm-5 col-form-label">Please note the start date when you first acquired your First Aid Certificate?</label>
        <div class="col-sm-7">
            {{Form::text('start_date_first_aid',old('start_date_first_aid',isset($candidate->guardingexperience->start_date_first_aid) ? $candidate->guardingexperience->start_date_first_aid :""),array('class'=>' form-control datepicker','placeholder'=>"If you have First Aid when did you acquire it? Mandatory field if you have First Aid certificate.",'max'=>"2900-12-31"))}}
            <div class="form-control-feedback">{!! $errors->first('start_date_first_aid') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('start_date_cpr') ? 'has-error' : '' }}"  id="start_date_cpr">
        <label for="start_date_guard_license" class="col-sm-5 col-form-label">Please note the start date when you first acquired your CPR Certificate?</label>
        <div class="col-sm-7">
            {{Form::text('start_date_cpr',old('start_date_cpr',isset($candidate->guardingexperience->start_date_cpr) ? $candidate->guardingexperience->start_date_cpr :""),array('class'=>' form-control datepicker','placeholder'=>"If you have CPR when did you acquire it? Mandatory field if you have your CPR certificate",'max'=>"2900-12-31"))}}
            <div class="form-control-feedback">{!! $errors->first('start_date_cpr') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
</div>
<div id="guard_licence_expiry_qstn" class="{{ @$candidate->guardingexperience->guard_licence!='Yes'?'hide-this-block':'' }}">
    <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Licence Expiry Dates</label>
    <div class="form-group row {{ $errors->has('expiry_guard_license') ? 'has-error' : '' }}"  id="expiry_guard_license">
        <label for="expiry_guard_license" class="col-sm-5 col-form-label">Please enter the expiry date of your security guard licence</label>
        <div class="col-sm-7">
            {{Form::text('expiry_guard_license',old('expiry_guard_license',isset($candidate->guardingexperience->expiry_guard_license) ? $candidate->guardingexperience->expiry_guard_license :""),array('class'=>' form-control datepicker','placeholder'=>"If you have a guarding licence, when does it expire.  Mandatory field if you have a licence.",'max'=>"2900-12-31"))}}
            <div class="form-control-feedback">{!! $errors->first('expiry_guard_license') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('expiry_first_aid') ? 'has-error' : '' }}"  id="expiry_first_aid">
        <label for="expiry_first_aid" class="col-sm-5 col-form-label">Please enter the expiry date of your First Aid certificate</label>
        <div class="col-sm-7">
            {{Form::text('expiry_first_aid',old('expiry_first_aid',isset($candidate->guardingexperience->expiry_first_aid) ? $candidate->guardingexperience->expiry_first_aid :""),array('class'=>' form-control datepicker','placeholder'=>"If you have First Aid, when does it expire?  Mandatory field if you have a licence.",'max'=>"2900-12-31"))}}
            <div class="form-control-feedback">{!! $errors->first('expiry_first_aid') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('expiry_cpr') ? 'has-error' : '' }}"  id="expiry_cpr">
        <label for="expiry_cpr" class="col-sm-5 col-form-label">Please enter the expiry date of your CPR certificate</label>
        <div class="col-sm-7">
            {{Form::text('expiry_cpr',old('expiry_cpr',isset($candidate->guardingexperience->expiry_cpr) ? $candidate->guardingexperience->expiry_cpr :""),array('class'=>' form-control datepicker','placeholder'=>"If you have CPR, when does it expire?  Mandatory field if you have a licence.",'max'=>"2900-12-31"))}}
            <div class="form-control-feedback">{!! $errors->first('expiry_cpr') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
</div>
<div class="{{ @$candidate->guardingexperience->guard_licence!='No' && (@$candidate->guardingexperience->test_score_percentage) ?'':'hide-this-block' }}" id="test_score_block">
<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Upload Ontario Security Guard Test Scores</label>
@include('recruitment::job-application.partials.profile.security-guard-test-score')
</div>

<div id="security_clearness_expiry_qstn" class="{{ @$candidate->guardingexperience->guard_licence!='Yes'?'hide-this-block':'' }}">
    <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Security Clearance Information</label>
    <div class="form-group row {{ $errors->has('security_clearance') ? 'has-error' : '' }}"  id="security_clearance">
        <label for="security_clearance" class="col-sm-5 col-form-label">Do you have a valid security clearance ? </label>
        <div class="col-sm-7">
            {{ Form::select('security_clearance',[null=>'If the answer is "Yes", please type in the answers below.',"Yes"=>"Yes","No"=>"No"],old('security_clearance',isset($candidate->guardingexperience->guard_licence) ? $candidate->guardingexperience->security_clearance :""),array('class' => 'form-control','id'=>'security_clearance')) }}
            <div class="form-control-feedback">{!! $errors->first('security_clearance') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div id="security_clearance_type_div" class="{{ @$candidate->guardingexperience->security_clearance!='Yes'?'hide-this-block':'' }}">
    <div class="form-group row {{ $errors->has('security_clearance_type') ? 'has-error' : '' }}"  id="security_clearance_type">
        <label for="security_clearance_type" class="col-sm-5 col-form-label">What type of security clearance ? </label>
        <div class="col-sm-7">
            {{Form::text('security_clearance_type',old('security_clearance_type',isset($candidate->guardingexperience->security_clearance_type) ? $candidate->guardingexperience->security_clearance_type :""),array('class'=>' form-control','placeholder'=>"Mandatory field if you have a security clearance."))}}
            <div class="form-control-feedback">{!! $errors->first('security_clearance_type') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    </div>
    <div id="security_clearance_expiry_date_div" class="{{ @$candidate->guardingexperience->security_clearance!='Yes'?'hide-this-block':'' }}">
    <div class="form-group row {{ $errors->has('security_clearance_expiry_date') ? 'has-error' : '' }}"  id="security_clearance_expiry_date">
        <label for="security_clearance_expiry_date" class="col-sm-5 col-form-label">Enter the expiry date</label>
        <div class="col-sm-7">
            {{Form::text('security_clearance_expiry_date',old('security_clearance_expiry_date',isset($candidate->guardingexperience->security_clearance_expiry_date) ? $candidate->guardingexperience->security_clearance_expiry_date :""),array('class'=>' form-control datepicker','placeholder'=>"If you have Security clearance, when does it expire?  Mandatory field if you have a security clearance.",'max'=>"2900-12-31"))}}
            <div class="form-control-feedback">{!! $errors->first('security_clearance_expiry_date') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
</div>
</div>

<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Use of Force</label>
<div class="form-group row {{ $errors->has('force') ? 'has-error' : '' }}"  id="use_of_force">
    <label for="use_of_force" class="col-sm-5 col-form-label">Are you use of force certified?</label>
    <div class="col-sm-7">
        {{ Form::select('use_of_force',[null=>'Please Select',"Yes"=>"Yes","No"=>"No"],old('use_of_force',isset($candidate->force->force) ? $candidate->force->force :""),array('class' => 'form-control','id'=>'use_of_forces','required'=>TRUE)) }}
        <div class="form-control-feedback">{!! $errors->first('use_of_force') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div id="use_of_force_question" class="{{ @$candidate->force->force!='Yes'?'hide-this-block':'' }}">
    <div class="form-group row {{ $errors->has('use_of_force_lookups_id') ? 'has-error' : '' }}"  id="use_of_force_lookups_id">
        <label for="use_of_force_lookups_id" class="col-sm-5 col-form-label">If yes, please provide your certification</label>
        <div class="col-sm-7">
            {{ Form::select('use_of_force_lookups_id',[null=>'Please Select']+$lookups['force'],old('use_of_force_lookups_id',isset($candidate->force->use_of_force_lookups_id) ? $candidate->force->use_of_force_lookups_id :""),array('class' => 'form-control','id'=>'use_of_force_lookups_id')) }}
            <div class="form-control-feedback">{!! $errors->first('use_of_force_lookups_id') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row {{ $errors->has('expiry') ? 'has-error' : '' }}"  id="force_expiry">
        <label for="force_expiry" class="col-sm-5 col-form-label">When does your certification expire?</label>
        <div class="col-sm-7">
            {{Form::text('force_expiry',old('force_expiry',isset($candidate->force->expiry) ? $candidate->force->expiry :""),array('class'=>' form-control datepicker','placeholder'=>"If you have use of force certification, when does it expire?  Mandatory field if you have a certification.",'max'=>"2900-12-31"))}}
            <div class="form-control-feedback">{!! $errors->first('force_expiry') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    @if (isset($policybase64))
        <div class="form-group row {{ $errors->has('force_file') ? 'has-error' : '' }}"
             id="force_file"
        @if (isset($candidate->force->s3_location_path))
            style="display: none"
        @endif
        >
            <label for="force_file" class="col-sm-5 col-form-label">Please upload your UOF certificate<span class="mandatory">*</span></label>
            <div class="aws_upload_form">
                <input type="hidden" name="acl" value="private">
                <input type="hidden" name="success_action_status" value="201">
                <input type="hidden" name="policy" value="{{$policybase64}}">
                <input type="hidden" name="X-amz-credential" value="{{$access_key}}/{{$short_date}}/{{$region}}/s3/aws4_request">
                <input type="hidden" name="X-amz-algorithm" value="AWS4-HMAC-SHA256">
                <input type="hidden" name="X-amz-date" value="{{$iso_date}}">
                <input type="hidden" name="X-amz-expires" value="{{$presigned_url_expiry}}">
                <input type="hidden" name="X-amz-signature" value="{{$signature}}">
                <input type="hidden" name="key" id="attachment-key" value="">
                <input type="hidden" name="Content-Type" id="attachment-type" value="">

                <div class="col-sm-12" >
                    <div id="upload-attachment-file" class="row form-group">
                        <div class="col-sm-7">
                            <input
                                    class="form-control"
                                    type="hidden" id="attachment-file-aws" data-aid=""
                                    name="uof_path"
                                    @if (isset($candidate->force->s3_location_path))
                                    disabled
                                    style="display: none"
                                    @endif
                            />
                            <input
                                    class="form-control" type="file"
                                    id="attachment-file" data-aid=""
                                    name="file"
                                    @if (isset($candidate->force->s3_location_path))
                                    disabled
                                    style="display: none"
                                    @endif
                            />
                            <span class="help-block"></span>
                        </div>
                        <div class="col-sm-2">
                            <input class="button btn btn-edit file_attachment_upload_btn" id="uof_upload_button" type="button" value="Upload" />
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
    @if (isset($candidate->force->s3_location_path))
        <div class="form-group row {{ $errors->has('force_file') ? 'has-error' : '' }}" id="force_file_set">
            <label for="force_file" class="col-sm-5 col-form-label">Please upload your UOF certificate<span class="mandatory">*</span></label>
            <div class="col-sm-4">
            <div class="row">
                <div class="col-sm-6">
                    <div id="force_div">
                    <input type="hidden" name="uof_path" value="{{ $candidate->force->s3_location_path}}" id="attachment-file-aws">
                    <a class="nav-link score-document" target="_blank" href="{{Storage::disk('s3-recruitment')->temporaryUrl($candidate->force->s3_location_path,\Carbon\Carbon::now()->addMinutes(30)) }}">
                        Click here to download the file
                    </a>
                    </div>
                </div>
            <div class="col-sm-4">
                <input class="button btn btn-edit score_attachment_remove_btn" onclick="removeForceDocument(this)" type="button" value="Remove" style="margin: 7px;">
            </div>
            </div>
        </div>
        </div>
    @endif
</div>
<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Security Guarding Experience</label>

<div class="form-group row {{ $errors->has('social_insurance_number') ? 'has-error' : '' }}" id="social_insurance_number">
        <label for="social_insurance_number" class="col-sm-5 col-form-label" >Do you have a valid Social Insurance Number in Canada?</label>
        <div class="col-sm-7 form-group row">
                <div class="radio-inline col-sm-2"><input type="radio" @if(isset($candidate->guardingexperience->social_insurance_number)&& (@$candidate->guardingexperience->social_insurance_number)==1) checked @endif  name="social_insurance_number" value=1><label class="padding-5" ><b>Yes</b></label></div>
                <div class="radio-inline col-sm-2"><input type="radio" @if(isset($candidate->guardingexperience->social_insurance_number)&& (@$candidate->guardingexperience->social_insurance_number)==0) checked @endif name="social_insurance_number" value=0 ><label class="padding-5" ><b>No</b></label></div>
                <div class="form-control-feedback">
                {!! $errors->first('social_insurance_number') !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
</div>

<div id="sin_expiry_date_status_div" class="{{ @$candidate->guardingexperience->social_insurance_number==0 ?'hide-this-block':'' }}">

            <div class="form-group row {{ $errors->has('sin_expiry_date_status') ? 'has-error' : '' }}" id="sin_expiry_date_status">
                    <label for="sin_expiry_date_status" class="col-sm-5 col-form-label" >Do you have an expiry date on your SIN ?</label>
                    <div class="col-sm-7 form-group row">
                            <div class="radio-inline col-sm-2"><input  type="radio"
                            @if(isset($candidate->guardingexperience->sin_expiry_date_status)&& (@$candidate->guardingexperience->sin_expiry_date_status)==1) checked @endif name="sin_expiry_date_status" value=1><label class="padding-5" ><b>Yes</b></label></div>
                            <div class="radio-inline col-sm-2"><input  type="radio"  @if(isset($candidate->guardingexperience->sin_expiry_date_status)&& (@$candidate->guardingexperience->sin_expiry_date_status)==0) checked @endif name="sin_expiry_date_status" value=0><label class="padding-5" ><b>No</b></label></div>
                            <div class="form-control-feedback">
                            {!! $errors->first('sin_expiry_date_status') !!}
                            <span class="help-block text-danger align-middle font-12"></span>
                        </div>
                    </div>
            </div>
</div>
<?php //echo '<pre>'; print_r($candidateJob->candidate->guardingexperience);exit; ?>
<div id="sin_expiry_date_div" class="{{ @$candidate->guardingexperience->sin_expiry_date_status== 0 ?'hide-this-block':'' }}">
        <div class="form-group row {{ $errors->has('sin_expiry_date') ? 'has-error' : '' }}" id="sin_expiry_date">
                <label for="sin_expiry_date" class="col-sm-5 col-form-label" >Please enter the expiry date of your SIN</label>
                <div class="col-sm-7">
                    {{ Form::text('sin_expiry_date',old('sin_expiry_date',isset($candidate->guardingexperience->sin_expiry_date) ? $candidate->guardingexperience->sin_expiry_date :""),array('id' => 'sin_expiry_date','class' => 'form-control datepicker','readonly'=>"readonly")) }}
                    <div class="form-control-feedback">
                        {!! $errors->first('sin_expiry_date') !!}
                        <span class="help-block text-danger align-middle font-12"></span>
                    </div>
                </div>
            </div>
</div>

<div class="form-group row {{ $errors->has('years_security_experience') ? 'has-error' : '' }}"  id="years_security_experience">
    <label for="years_security_experience" class="col-sm-5 col-form-label">How many total years of security industry experience do you have?</label>
    <div class="col-sm-7">
        {{Form::number('years_security_experience',old('years_security_experience',isset($candidate->guardingexperience->years_security_experience) ? $candidate->guardingexperience->years_security_experience :""),array('class'=>' form-control','placeholder'=>"Leave blank if not applicable. If you are applying for a guard position, you must enter this field",'min'=>"0",'step'=>"0.1"))}}
        <div class="form-control-feedback">{!! $errors->first('years_security_experience') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>

<div class="form-group row {{ $errors->has('most_senior_position_held') ? 'has-error' : '' }}"  id="most_senior_position_held">
    <label for="most_senior_position_held" class="col-sm-5 col-form-label">What is the most senior position you've held in security?  </label>
    <div class="col-sm-7">
         {{ Form::select('most_senior_position_held',[null=>'Leave blank if not applicable. If you are applying for a guard position, you must enter this field']+$lookups['positions_lookups']+[0=>'Other'],old('last_role_held',isset($candidate->guardingexperience->most_senior_position_held) ? $candidate->guardingexperience->most_senior_position_held :""),array('class' => 'form-control select2')) }}
        <div class="form-control-feedback">{!! $errors->first('most_senior_position_held') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
@if (isset($policybase64))
<script>
    $("#uof_upload_button").click(function(e) {
        e.preventDefault();
        let formDiv = $(this).closest('.aws_upload_form')[0];
        console.log(formDiv);
        // debugger;
        // $('#aws_upload_attachment_form').find('.form-group').removeClass('has-error').find('.help-block').text('');
        the_file = $("#attachment-file")[0].files[0];
        candidate_id = {{$candidate->id}};
        var filename = 'temp/'+candidate_id+'/uof/'+Date.now() + '.' + the_file.name.split('.').pop();
        $("#attachment-key").val(filename);
        $("#attachment-type").val(the_file.type);
        var results = $(this).find("#video-results");
        var fileid = 'attachment_file_id';
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


            $(linkurl).find('#attachment-file-aws').val(the_file_name);
            $(linkurl).find('#'+success).show();
            $(linkurl).find('#video-results').hide();

        });
    });
</script>
@endif
