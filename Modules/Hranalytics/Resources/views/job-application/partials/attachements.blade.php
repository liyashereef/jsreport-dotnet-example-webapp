<div class="form-group row" id='attachment_file_name'>
    <span class="col-sm-5 col-form-label"><b></b></span>
    <div class="col-sm-7">
        <div class="form-control-feedback">
            <span class="help-block text-danger align-middle font-12"><span class="help-block text-danger align-middle font-12"></span></span>
            <input type="hidden" name="candidate_job_id"  val="{{$session_obj['job']->id}}">
        </div>
    </div>
</div>
@php
$mandatory_items = json_decode($session_obj['job']->required_attachments);
@endphp

@foreach($lookups['attachmentLookups'] as $i=>$attachment)
        <span class="col-sm-12 name-label">{{$attachment->attachment_name}}</span>
        <div class="form-group row attachment_div {{ $errors->has('attachment_file_name') ? 'has-error' : '' }}" id="attachment_file_name.{{$attachment->id }}" >
                <div class="form-group row col-sm-12">
                    <label for="file_attachment" class="col-sm-4 control-label upload-class">Upload File{!! (is_array($mandatory_items)&&in_array($attachment->id,$mandatory_items))?'<span class="mandatory">*</span>':'' !!}</label>
                        <div class="col-sm-4">
                                    {{Form::file('attachment_file_name[' .$attachment->id. ']',array('class'=>'form-control file_attachment scroll-clear','id'=>'attach_id_'.$attachment->id,'onchange'=>'validateFileSize(this);'))}}
                                     <small class="help-block"  id="attachment-validation"></small>
                                    <div class="status_upload{{$attachment->id }}" style="padding-bottom:40px;">

                        </div>
                        </div>

                        <div class="col-sm-4" id="attachment_upload">
                                     <input id="file_attachment_upload_btn" class="button btn btn-edit file_attachment_upload_btn" type="button" value="Upload" data-id="{{$attachment->id }}">
                        </div>
                </div>
        </div>
@endforeach
