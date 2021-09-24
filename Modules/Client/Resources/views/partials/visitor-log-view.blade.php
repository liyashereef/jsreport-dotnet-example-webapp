    <div class="modal fade" id="viewModal" data-backdrop="static" tabindex="-1" role="dialog" style="overflow-y:auto;" aria-labelledby="myModalLabel" aria-hidden="true" data-focus-on="input:first">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">View Visitor Log</h4>
                         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                       {{--  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button> --}}
                    </div>
                    <div class="modal-body">
                    <h5 style="margin-bottom: 10px; font-family: 'Montserrat', sans-serif !important;">Visitor Profile</h5>

                   <div class="form-group row">
                   <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
                    @foreach($template_fields as $key => $eachfield)
                     @if($eachfield->is_visible == 1)
                      <div class="row">
                       <label  class="col-md-6 control-label">
                        {{$eachfield->field_displayname}}
                       </label>
                      <div class="col-md-6 email-break">
                          <span>
                                  @if($eachfield->fieldname=="visitor_type_id")
                                  {{$template_details['type']['type']}}
                                  @else
                                  {{$template_details[$eachfield->fieldname]}}
                                  @endif
                          </span>
                      </div>
                     </div>
                    @endif
                   @endforeach
                   <!-- Meta info -->
                   @foreach($visitorLogMetas as $key => $vm)
                      <div class="row">
                        <label  class="col-md-6 control-label">
                          {{$vm->formattedKey}}
                        </label>
                        <div class="col-md-6 email-break">
                            <span>{{$vm->value}}</span>
                        </div>
                     </div>
                   @endforeach
                   
                 </div>
                 <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                       @if($template_details['picture_file_name']!=null)
                       <img height="170px" width="200px" src="{{asset("visitor_log").'/'. $template_details['id'].'/'.$template_details['picture_file_name']}}" >
                      @else
                       <img height="180px" width="200px" src='{{asset("images/no_avatar.jpg") }}' >
                      @endif
                 </div>
                 </div>
                    @foreach($template_features as $key => $eachfield)
                    @if($eachfield->is_visible == 1)
                      <div class="form-group row" id="{{$eachfield->feature_name}}">
                      <label for="{{$eachfield->feature_name}}" class="col-sm-4 control-label">
                        @if($eachfield->feature_name!="picture")
                        {{$eachfield->feature_displayname}}</label>
                            <div class="col-sm-8">
                                <label for="{{$eachfield->feature_name}}">

                                     @if($template_details['signature_file_name']!=null)
                                     <img height="120px" width="250px" src="{{asset("visitor_log").'/'. $template_details['id'].'/'.$template_details['signature_file_name']}}"
                                      @else
                                    --
                                      @endif


                                 </label>
                            </div>
                                       @endif
                        </div>
                      @endif
                      @endforeach
                      @if($template_details['checkout_file_name']!=null)
                      <div class="form-group row">
                      <label class="col-sm-4 control-label">
                        Visitor Checkout Signature</label>
                            <div class="col-sm-8">
                                <label>
                                     @if($template_details['checkout_file_name']!=null)
                                     <img height="120px" width="250px" src="{{asset("visitor_log").'/'. $template_details['id'].'/'.$template_details['checkout_file_name']}}"
                                      @else
                                    --
                                      @endif


                                 </label>
                            </div>
                        </div>
                         @endif
                    </div>

               </div>
            </div>
        </div>
