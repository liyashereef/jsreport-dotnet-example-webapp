	<!-- Row - Start -->
    <div class="row">
            <!-- Profile - Start -->
         <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 form-panel">
         <div class="row">
             <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                 <div class="margin-bottom-20">
                     <div id="profile" class="data-list-body">
                         <div class="data-list-line row">
                         <div class="data-list-label document-list-label margin-top-1 margin-bottom-10" style="margin-top: -8px; width: 100vw;">Add Document</div>
                         </div>
 
                         <div class="data-list-line row">
                             <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 col-xl-1 form-control-feedback">Employee :</div>
                             <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11 col-xl-11 data-list-disc" id="employee_details" style="padding-left: 0px;margin-top: .25rem;">
                                {{ Form::hidden('user_id', isset($employee_list) ? old('user_id',$employee_list['user_id']) : null,array('id'=>'user_id')) }}
                                {{isset($employee_list) ? ($employee_list['employee_details']) : null}}
                             </div>
                         </div>
                         
                         <div class="data-list-line row">
                             <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 col-xl-1 form-control-feedback">Upload Date : </div>
                             <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11 col-xl-11 data-list-disc" id="upload_date" style="padding-left: 0px;margin-top: .25rem;">
                                  {{isset($employee_list) ? ($employee_list['uploaded_date']) : null}}
                             </div>
                         </div>
                         <div class="data-list-line row">
                             <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 col-xl-1 form-control-feedback">Upload Time : </div>
                             <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11 col-xl-11 data-list-disc" id="upload_time" style="padding-left: 0px;margin-top: .25rem;">
                                  {{isset($employee_list) ? ($employee_list['uploaded_time']) : null}}
                             </div>
                         </div>
                         
                     </div>
                 </div>
             </div>
         </div>
         </div>
         </div>