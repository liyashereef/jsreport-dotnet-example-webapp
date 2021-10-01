	<!-- Row - Start -->
    <div class="row">
            <!-- Profile - Start -->
         <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 form-panel">
         <div class="row">
             <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                 <div class="margin-bottom-20">
                     <div id="profile" class="data-list-body">
                            <div class="data-list-line row">
                                <div class="data-list-label document-list-label margin-top-1 margin-bottom-10" style="margin-top: -8px;  width: 100vw;">Add Document</div>
                            </div>
                        <div class="data-list-line row">
                                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 col-xl-1 form-control-feedback"> Name : </div>
                                <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11 col-xl-11 data-list-disc" id="last_name" style="padding-left: 0px;margin-top: .25rem;">
                                     {{ Form::hidden('other_category_lookup_id', isset($other_list) ? old('other_category_lookup_id',$other_list['otherlist']['other_category_lookup_id']) : null,array('id'=>'other_category_id')) }}
                                     {{ Form::hidden('other_category_name_id', isset($other_list['otherlist']) ? old('other_category_name_id',$other_list['otherlist']->id) : null,array('id'=>'other_category_name_id')) }}
                                     {{isset($other_list) ? ($other_list['otherlist']['name']) : null}}
                                </div>
                        </div>

                         <div class="data-list-line row">
                             <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 col-xl-1 form-control-feedback">Upload Date : </div>
                             <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11 col-xl-11 data-list-disc" id="last_name" style="padding-left: 0px;margin-top: .25rem;">
                                  {{isset($other_list) ? ($other_list['uploaded_date']) : null}}
                             </div>
                         </div>
                         <div class="data-list-line row">
                             <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 col-xl-1 form-control-feedback">Upload Time : </div>
                             <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11 col-xl-11 data-list-disc" id="last_name" style="padding-left: 0px;margin-top: .25rem;">
                                  {{isset($other_list) ? ($other_list['uploaded_time']) : null}}
                             </div>
                         </div>
                         
                     </div>
                 </div>
             </div>
         </div>
         </div>
         </div>
        
