     <div id="visitor-log-form-content">  
     <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 orange">
        <h5 style="margin-left:-15px; font-family: 'Montserrat', sans-serif !important;">Visitor Profile </h5>
    </label>
      {{ Form::open(array('id'=>'visitor-log-add-form','class'=>'form-horizontal','method'=> 'POST')) }}
      {{csrf_field()}}
       <input type='hidden' class='form-control' name="template_id"  value="{{$template_id}}" >
      @foreach($template_fields as $key => $eachfield)
       @if($eachfield->is_visible == 1)
    <div id="{{$eachfield->fieldname}}" class="form-group row {{ $errors->has($eachfield->fieldname) ? 'has-error' : '' }}"">
        <label class="col-sm-4 col-form-label">  {{$eachfield->field_displayname}} 
        {{--  @if($eachfield->is_required == 1) <span class="mandatory">*</span> @endif --}}
        </label> 

        <div class="col-sm-6">
           @if($eachfield->field_type == 1) 
              <input type="text" @if($eachfield->is_required == 1) required @endif class="form-control @if($eachfield->fieldname == 'phone') phone @endif" name="{{$eachfield->fieldname}}" value=""/>
               @if($eachfield->fieldname == 'license_number')
               <div class="row" style="margin-top: 5px;">
                <input type="checkbox" style="width:25px;height: 20px;margin-left: 14px; " class="form-control"  name="license_number_validation" value="1"> <label style="float:left;" > Check if not applicable </label>
                </div>
                 @endif
           @elseif($eachfield->field_type == 5)
               <input type='text' class='form-control col-sm-3' readonly name='checkintime' id='checkintime' value="{{ \Carbon\Carbon::now()->format('H : i A')}}" >
               <input type='hidden' class='checkin' id='checkin' name='{{$eachfield->fieldname}}' value="{{ \Carbon\Carbon::now()->toDateTimeString()}}" >
           @elseif($eachfield->field_type == 2 && $eachfield->fieldname == 'visitor_type_id')  
             @foreach($visitor_type as $eachtype)

<label class="radio">
  <input type="radio" name="{{$eachfield->fieldname}}"  @if($eachfield->is_required == 1) required @endif value="{{$eachtype->id}}">
  <span> {{$eachtype->type}}</span>
</label>
             @endforeach  
           @endif
             <small class="help-block"></small>
        </div>
    </div>
      @else
       <input type='hidden' class='form-control' name="{{$eachfield->fieldname}}"  value="" >
      @endif 
     @endforeach  
    
      <input type='hidden' class='form-control' name="customer_id"  value=" {{ Session::get('default_customer') }}" >

       @foreach($template_features as $key => $eachfeature)
        @if($eachfeature->is_visible)
        <div id="am_email" class="form-group row ">
        <label for="am_email" class="col-sm-4 col-form-label"> {{$eachfeature->feature_displayname}} 
          @if($eachfeature->is_required == 1) <span class="mandatory">*</span> @endif
        </label>


     @if($eachfeature->feature_name == 'picture')

        <div class="col-sm-6">
         <small id="image-message" style="color: red;"></small>
         <div id="my_camera"> </div>
         <br>
	     <input id="take-snap" type=button value="Take Snapshot" onClick="take_snapshot()">
	       <input type='hidden' class='form-control' id="picture_feature"  value="1" >
	       <input type='hidden' class='form-control' id="picture_feature_required"  value="{{$eachfeature->is_required ? 1 : 0 }}" >
         <input type='hidden' class='form-control' id="picture_feature_visible"  value="{{$eachfeature->is_visible ? 1 : 0 }}" >
	     <input id="retake" type=button style="display: none;" value="Retake" onclick="retake_snapshot()" >

        </div>
       @elseif($eachfeature->feature_name == 'signature')
       	   <input type='hidden' class='form-control' id="sign_feature"  value="1" >
	       <input type='hidden' class='form-control' id="sign_feature_required"  value="{{$eachfeature->is_required ? 1 : 0 }}" >
         <input type='hidden' class='form-control' id="sign_feature_visible"  value="{{$eachfeature->is_visible ? 1 : 0 }}" >
        <div class="col-sm-6">
          <small id="sign-message" style="color: red;"></small>
           <div id="signature-pad" class="signature-pad">
            <div class="signature-pad--body">
             <canvas id="signature-content"></canvas>
            </div>
          <div class="signature-pad--footer">
           <div class="signature-pad--actions">
           <div>
        	<br>
            <button type="button" class="button clear" data-action="clear">Clear</button>
           </div>
          </div>
         </div>
         </div>
        </div>
       @endif
     </div>
     @endif 
    @endforeach  

     <div class="form-group row">
        <label class="col-sm-4 col-form-label">  
        </label> 

        <div class="col-sm-6">
  <input id="cancel" class="button btn btn-primary blue submit" type="button" value="Cancel">
              <input class="button btn btn-primary blue submit" onclick="saveVisitorLog(true);" type="button" value="Save">
              <input class="button btn btn-primary blue submit"  onclick="saveVisitorLog(false);" type="button" value="Save & Add Another">
        </div>
    </div>
    {{ Form::close() }}
</div>


<script src="{{ asset('js/signature_pad.umd.js') }}"></script>
<script src="{{ asset('js/signature_app.js') }}"></script>
    <link href="{{ asset('css/ie9.css') }}">
    <link href="{{ asset('css/signature-pad.css') }}">
    <script src="{{ asset('js/common.js') }}"></script>

<script>

    function saveVisitorLog(redirect){

       if(($('#sign_feature_required').val() == 1) && signaturePad.isEmpty()){
          $('#sign-message').text('Visitor signature is required');
          return false;        
       }else{
          $('#sign-message').text('');
       }

       if (($('#picture_feature_required').val() == 1) && ($("#imageprev").length == 0)){
          $('#image-message').text('Visitor image is required');
          return false;
        }else{
          $('#image-message').text('');
        }

      url = "{{ route('visitor-log.add') }}";
      var formData = new FormData($('#visitor-log-add-form')[0]);
      var form = $('#visitor-log-add-form');
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: url,
        type: 'POST',
        data: formData,
        success: function (data) {
        if (data.success) { 
     
                       if ($('#sign_feature_required').val() || $('#sign_feature_visible').val() ){
                         saveSnap(data.success.result,'signature');  
                        }

                       if ($('#picture_feature_required').val() || $('#picture_feature_visible').val()){
                         saveSnap(data.success.result,'picture');   
                        }
   
      
          if (redirect){
            swal({
                title: "Success",
                text: "Visitor log details added successfully.",
                type: "success",
                confirmButtonText: "OK",
                showLoaderOnConfirm: true,
                closeOnConfirm: true
              },
                    function () {
                        window.location = "{{ route('visitor-log.dashboard') }}"
                    }); 
           }else{
           	  swal({
                title: "Success",
                text: "Visitor log details added successfully.",
                type: "success",
                confirmButtonText: "OK",
                showLoaderOnConfirm: true,
                closeOnConfirm: true
                });

            	$('#visitor-log-add-form').find("input[type=text]").not(".checkin, #checkintime").val("");
              $('.help-block').text('');
              $('#visitor-log-add-form').find("input[type=radio]").prop('checked', false);
               signaturePad.clear();
              if($('#picture_feature').val()){
            	retake_snapshot();
              }

           }
          } else {

            console.log(data);
            swal("Oops", "The record has not been saved", "warning");
          }
        },
        fail: function (response) {
          console.log(response);
          swal("Oops", "Something went wrong", "warning");
        },
        error: function (xhr, textStatus, thrownError) {
          associate_errors(xhr.responseJSON.errors,form);
        },
        contentType: false,
        processData: false,
      });

    }


   $('#cancel').on('click', function (){
 
    window.location = "{{ route('visitor-log.dashboard') }}";

  });


 $(function () {

  var interval_msec = 30000;
 setInterval("getCurrentTime()", interval_msec);
 	if($('#picture_feature').val()){
   Webcam.on( 'error', function(err) {
      console.log('cam not connected');
   });

     Webcam.set({
      width: 320,
      height: 240,
      image_format: 'jpeg',
      jpeg_quality: 90
     });
    Webcam.attach( '#my_camera' );
   }
 });   
    
   function retake_snapshot(){
      $('#my_camera').empty();
          Webcam.set({
      width: 320,
      height: 240,
      image_format: 'jpeg',
      jpeg_quality: 90
      });
    Webcam.attach( '#my_camera' );
      $('#retake').hide();
      $('#take-snap').show();
   }


    function take_snapshot() {
        Webcam.snap( function(data_uri) {

            document.getElementById('my_camera').innerHTML = 
            '<img id="imageprev" src="'+data_uri+'"/>';
           
             $('#retake').show();
             $('#take-snap').hide();
        } );
    }


    function saveSnap(userid,type){
    if(type =='signature'){
       var canvas = document.getElementById("signature-content");
       var image = canvas.toDataURL(); // data:image/png....
    }else if($("#imageprev").length){
       var image = document.getElementById('imageprev').src;
    }else{
      return false;
    }
         url = "{{ route('visitor-log.uploadimage') }}";
     $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: url,
        type: 'POST',
        data:{
           imageBase64: image ,visitorid: userid, imagetype: type
          },
        success: function (data) {
          if (data.success) {
             return true;
          } else {
            console.log(data);
            swal("Oops", "The record has not been saved", "warning");
          }
        },
        fail: function (response) {
          console.log(response);
          swal("Oops", "Something went wrong", "warning");
        }  ,
        error: function (xhr, textStatus, thrownError) {
          associate_errors(xhr.responseJSON.errors);
        },

      });

	 }


  function getCurrentTime(){

            var url = "{{ route('visitor-log.checkintime') }}";
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data.success) {
                       $('#checkintime').val(data.checkintime);  
                       $('.checkin').val(data.checkin);  
                    } else {
                        console.log(data);
                    }
                },
                fail: function (response) {
                    console.log(response);
                },
                error: function (xhr, textStatus, thrownError) {
                    //associate_errors(xhr.responseJSON.errors, $form);
                },
                contentType: false,
                processData: false,
            });
  }


</script>
               

