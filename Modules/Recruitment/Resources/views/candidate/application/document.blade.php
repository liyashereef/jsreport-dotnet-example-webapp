  @foreach($document as $i=>$each_document)

                  @if($each_document['submitted_file']!="")
                  <span class="col-sm-12 name-label">{{$each_document['document_name']}}
                     
                   @if($each_document['is_mandatory']==1)
                  <span class="mandatory">*</span>
                  @endif
                  </span>
                  <div class="form-group row attachment_div success" id="attachment_file_name.{{$each_document['id']}}" >
                     <div class="form-group row col-sm-12">
                        <div id="attachment_name_div_{{$each_document['id'] }}" class="col-sm-4">
                           <a class="nav-link" target="_blank" href="{{Storage::disk('s3-recruitment')->temporaryUrl($each_document['submitted_file'],Carbon::now()->addMinutes(60)) }}" />Click here to download the file
                           </a>
                           <input type="hidden" id="uploaded_files_count" value="{{count($attachement_ids)}}">
                        </div>
                      {{--   <div style="display: none;" id="upload_file_div_{{$each_document['id'] }}" class="col-sm-4">
                           {{Form::file('attachment_file_name[' .$each_document['id']. ']',array('class'=>'form-control file_attachment scroll-clear','id'=>'attach_id_'.$each_document['id'],'onchange'=>'validateFileSize(this);'))}}
                           <small class="help-block"  id="attachment-validation"></small>
                           <div class="status_upload{{$each_document['id'] }}" style="padding-bottom:40px;">
                           </div>
                        </div> --}}
                        <div style="display: none;"  id="upload_btn_div_{{$each_document['id'] }}" id="attachment_upload" class="col-sm-7" >
                         <form action="https://{{$my_bucket}}.s3-{{$region}}.amazonaws.com" method="post" class="document_upload_form"  enctype="multipart/form-data">
                              <input type="hidden" name="acl" value="private">
                              <input type="hidden" name="success_action_status" value="201">
                              <input type="hidden" name="policy" value="{{$policybase64}}">
                              <input type="hidden" name="X-amz-credential" value="{{$access_key}}/{{$short_date}}/{{$region}}/s3/aws4_request">
                              <input type="hidden" name="X-amz-algorithm" value="AWS4-HMAC-SHA256">
                              <input type="hidden" name="X-amz-date" value="{{$iso_date}}">
                              <input type="hidden" name="X-amz-expires" value="{{$presigned_url_expiry}}">
                              <input type="hidden" name="X-amz-signature" value="{{$signature}}">
                              <input type="hidden" name="key" id="document-key" value="">
                              <input type="hidden" name="Content-Type" id="document-type" value="">

                        <div class="col-sm-12" >
                            <div id="upload-document-file" class="row form-group">
                                    <div class="col-sm-7">    
                                       <input class="form-control" type="file" id="document-file" data-aid="{{$each_document['id'] }}" name="file" />
                                       <span class="help-block"></span>
                                    </div>
                                    <div class="col-sm-2">    
                                       <input class="button btn btn-edit file_document_upload_btn" type="submit" value="Upload" />
                                    </div>
                                    <div class="col-sm-2" id="document-success" style="display:none;">    
                                       <span  style="color: #35af3f;">Uploaded Successfully</span>
                                    </div>
                                 </div>
                          {{--  <input  id="file_attachment_upload_btn"  data-aid="{{$each_document['id'] }}" class="button btn btn-edit file_attachment_upload_btn" type="button" value="Upload" data-id="{{$attachment->id }}"> --}}
                        </div>
                        <div class="col-sm-7" id="document-results">
                                 <!-- server response here -->
                              </div>
                           </form>
                        </div>
                        <div class="col-sm-4" id="attachment_remove_div_{{$each_document['id'] }}">
                           <input id="file_attachment_remove_btn" class="button btn btn-edit file_attachment_remove_btn" onclick="removeDocument('{{$candidateJob->candidate_id}}','{{$each_document['id'] }}')" type="button" value="Remove" data-id="{{$attachment->id }}">
                        </div>
                     </div>
                  </div>
                  @else
                  <span class="col-sm-12 name-label">{{$each_document['document_name']}}
                   @if($each_document['is_mandatory']==1)
                  <span class="mandatory">*</span>
                   @endif
                  </span>
                  <div class="form-group row attachment_div {{ $errors->has('attachment_file_name') ? 'has-error' : '' }}" id="attachment_file_name.{{$each_document['id'] }}" >
                     <div class="form-group row col-sm-12">
                        <div class="col-sm-12">
                           <form action="https://{{$my_bucket}}.s3-{{$region}}.amazonaws.com" method="post" class="document_upload_form"  enctype="multipart/form-data">
                              <input type="hidden" name="acl" value="private">
                              <input type="hidden" name="success_action_status" value="201">
                              <input type="hidden" name="policy" value="{{$policybase64}}">
                              <input type="hidden" name="X-amz-credential" value="{{$access_key}}/{{$short_date}}/{{$region}}/s3/aws4_request">
                              <input type="hidden" name="X-amz-algorithm" value="AWS4-HMAC-SHA256">
                              <input type="hidden" name="X-amz-date" value="{{$iso_date}}">
                              <input type="hidden" name="X-amz-expires" value="{{$presigned_url_expiry}}">
                              <input type="hidden" name="X-amz-signature" value="{{$signature}}">
                              <input type="hidden" name="key" id="document-key" value="">
                              <input type="hidden" name="Content-Type" id="document-type" value="">
                              <div class="col-sm-7">
                                 <div id="upload-document-file" class="row form-group">
                                    <div class="col-sm-7">    
                                       <input class="form-control" type="file" id="document-file" data-aid="{{$each_document['id'] }}" name="file" />
                                       <span class="help-block"></span>
                                    </div>
                                    <div class="col-sm-2">    
                                       <input class="button btn btn-edit file_document_upload_btn" type="submit" value="Upload" />
                                    </div>
                                    <div class="col-sm-2" id="document-success" style="display:none;">    
                                       <span  style="color: #35af3f;">Uploaded Successfully</span>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-sm-4">
                                 <div id="uploaded-document-file" style="display: none;" class="row">
                                    <div class="col-sm-5">    
                                       <span id="video-file-name"></span>
                                    </div>
                                    <div class="col-sm-2" id="document-success">    
                                       <a href="" target="_blank">Download <i class="fa fa-download fa-lg" aria-hidden="true" style="margin: 7px;"></i></a>
                                    </div>
                                    <div class="col-sm-2">    
                                       <input class="button btn btn-primary blue form-control" id="video-remove" type="button" value="Remove" />
                                    </div>
                                 </div>
                              </div>
                              <div class="col-sm-7" id="document-results">
                                 <!-- server response here -->
                              </div>
                           </form>
                           <span class="help-block"></span>
                        </div>
                        
                     </div>
                  </div>
                  @endif
                  @endforeach
