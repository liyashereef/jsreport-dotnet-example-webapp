<div class="form-group row {{ $errors->has('test_score_percentage') ? 'has-error' : '' }}"  id="test_score_percentage">
    <label for="test_score_percentage" class="col-sm-5 col-form-label">What was your test score on the Ontario Security Guard exam? (Percent)<span class="mandatory">*</span></label>
    <div class="col-sm-7">
        {{ Form::number('test_score_percentage',old('test_score_percentage',isset($candidateJob->candidate->guardingexperience->test_score_percentage) ? $candidateJob->candidate->guardingexperience->test_score_percentage :""),array('class' => 'form-control','id'=>'test_scorepercentage','placeholder'=>'Percentage','step'=>'0.01','max'=>'100')) }}
        <div class="form-control-feedback">{!! $errors->first('test_score_percentage') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
@if(!isset($candidateJob->candidate->guardingexperience->test_score_document_id))
<div class="form-group row {{ $errors->has('test_score_document_id') ? 'has-error' : '' }}"  id="test_score_document_id">
    <label for="test_score_document_id" class="col-sm-5 col-form-label">Please upload a copy of your test score (this will be verified as part of our background check)<span class="mandatory">*</span></label>
    <div class="col-sm-3">

            <input type="file" class="form-control" name="test_score_document_id"
                    >

        <div class="form-control-feedback">{!! $errors->first('test_score_document_id') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>       
</div>
@else

 

<div class="form-group row {{ $errors->has('test_score_document_id') ? 'has-error' : '' }}" id="test_score_document_id">
    <label for="test_score_document_id" class="col-sm-5 col-form-label">Please upload a copy of your test score (this will be verified as part of our background check)<span class="mandatory">*</span></label>
    <div class="col-sm-4">
      <div class="row">
        <div class="col-sm-6">             
            <div id="document_div">
             <input type="hidden" name="test_score_doc_id" value="{{ $candidateJob->candidate->guardingexperience->test_score_document_id}}">
             <a class="nav-link score-document" target="_blank" href="{{ route('test-score-document.download', ['file_name'=>$candidateJob->candidate->guardingexperience->test_score_document_id])}}" />Click here to download the file
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