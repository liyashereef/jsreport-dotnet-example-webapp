@extends('adminlte::page')

@section('title', 'Uniform Items')

@section('content_header')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<h3>Uniform Items</h3>
@stop

@section('content')


<div class="container-fluid container-wrap">
    {{ Form::open(array('url'=>'#','id'=>'uniform-products-add-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
    <input type="hidden" name="id" value="{{@$productName->id  }}"/>
    <input type="hidden" name="blockId" id="blockId" value="1">
    <section>
            <div class="form-group row" id="measuring_points">
                <label for="measuring_points" class="col-form-label col-md-2"></label>
                <div class="col-md-4">
                    <input type="hidden" class="form-control"  name="measuringPointId" value="" id="measuringPointId" disabled>
                    <span class="help-block"></span>
                </div>
            </div>
            <div class="form-group row" id="product_name">
                <label class="col-form-label col-md-2" for="product_name">Name <span class="mandatory">*</span></label>
                <div class=" col-md-4">
                    <input type="text" class="form-control" placeholder="Product Name" name="name" value="{{@$productName->name}}" >
                    <span class="help-block"></span>
                </div>
            </div>
            <div class="form-group row" id="selling_price">
                <label class="col-form-label col-md-2" for="selling_price">Selling Price <span class="mandatory">*</span></label>
                <div class=" col-md-4">
                    <input type="number" class="form-control" min="0.01" step="any" name="selling_price" placeholder="Selling Price" value="{{@$productName->selling_price}}">
                    <span class="help-block"></span>
                </div>
            </div>
            <div class="form-group row" id="vendor_price">
                <label class="col-form-label col-md-2" for="vendor_price">Vendor Price</label>
                <div class=" col-md-4">
                    <input type="number" class="form-control" min="0.01" step="any" name="vendor_price" placeholder="Vendor Price"  value="{{@$productName->vendor_price }}">
                    <span class="help-block"></span>
                </div>
            </div>

            <div class="form-group row" id="tax_id">
                <label class="col-form-label col-md-2">Tax <span class="mandatory">*</span></label>
                <div class=" col-md-4">
                    <select name="tax_id" class="form-control">
                        <option value="">Please select</option>
                        @foreach ($taxes as $tax)
                        <?php $selected = ($edit == 1 && $productName->tax_id == $tax->id) ? 'selected' : '' ?>
                            <option value="{{$tax->id}}" {{$selected}}>{{$tax->taxMaster->name}}</option>
                        @endforeach
                    </select>
                    <span class="help-block"></span>
                </div>
            </div>


            {{-- <div class="form-group row blockrow1" id="image_path" >
                <label class="col-form-label col-md-2" for="image_path">Image </label>
                <div class=" col-md-4">
                    <input type="file" class="form-control filecontrol" name="image_file" id="image_file" accept="image/*">
                    <small class="help-block"></small>
                    <label id="uploaded_file_name" style="font-weight: 100;">{{$productName->image_path or ''}}</label>

                    <div class="col-sm-12"   style="background-color: aliceblue;" id="image-results"><!-- server response here --></div>
                </div>
                <div id="success" class="col-sm-1">
                    <input class="btn btn-primary blue image-button"
                    type="button" attr-doctype="image" onclick="fileUpload(this);"
                    value="Upload" />
                </div>
                <div class="col-sm-2 test">
                    <a title="Add" href="javascript:;" attr-id="1" class="add_attachment">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </a>
                </div>

                <input type="hidden" name="uploadedS3ImageFileName" id="uploadedS3ImageFileName" value="{{$productName->image_path or ''}}" />
            </div> --}}
            <div id="upload-attachment" class="form-group" id="pan_mac">
                <label class="col-form-label col-md-2" for="image_path">Image</label>
                <div class="row blockrow1" >
                </div>
                <div class="row blockrow1">
                    <label for="pan_mac" class="col-sm-2 control-label"></label>
                    <div class="col-md-4">
                        <input type="file" class="form-control filecontrol" id="time_off_attachment1" name="time_off_attachment1">
                        <small class="help-block"></small>
                        <input type="hidden" class="uploadedS3AttachedFileName"
                        name="uploadedS3AttachedFileName[]"
                        id="uploadedS3AttachedFileName1" value="" />

                    </div>
                    <div class="col-sm-1" id="upload-block1">
                        <input  attr-doctype="attachment" attr-block="1"  attr-doctype="attachment" onclick="fileUpload(this);" class="btn btn-primary blue video-button" type="button" value="Upload" />
                    </div>
                    <div class="col-sm-1 test">
                        <a title="Add" href="javascript:;" attr-id="1" class="add_attachment">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
                <div class="row blockrow1">
                    <div class="col-md-3"></div>
                    <div class="col-md-5" id="attachment-progressbar1">

                    </div>
                </div>
            </div>

            <h4 class="color-template-title">Add Variant</h4>
            <div class="table-responsive">
                <table id="myTable" style="text-align: center;" class="table table-bordered" role="grid" aria-describedby="position-table_info" >
                    <thead>
                        <tr>
                            <th class="sorting_disabled">Variant Name</th>
                            <th class="sorting_disabled">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($edit)
                        @php ($sizecount=0)
                        @foreach($productVariantMapping as $key=>$each_item)
                        <tr role="row" class="row-1">
                            <td class="size_type">
                                <div class="form-group" id="size_{{ $sizecount}}">
                                    <input class="form-control size_row" type="text" name="variant_name[]" value="{{ $each_item->variant_name}}">
                                  <span class="help-block"></span>
                               </div>
                              </td>
                            <td class="addOrRemove">
                                @if($sizecount!=0)
                                 <a title="Remove size" href="javascript:;" class="remove_button" onclick="addSizeObject.removeSizeRow(this)"> <i class="fa fa-minus" aria-hidden="true"></i>
                                @endif
                                <a title="Add another size" href="javascript:;" class="add_button margin-left-table-btn" onclick="addSizeObject.addNewSizeRow(this)">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                            </td>
                        </tr>
                        @php ($sizecount++)
                        @endforeach
                        @else
                            <tr role="row" class="row-1">
                                <td class="min">
                                    <div class="form-group" id="min_0">
                                        <input class="form-control" type="text" class="min" name="variant_name[]">
                                        <span class="help-block"></span>
                                    </div>
                                </td>
                                <td class="addOrRemove">
                                    <a title="Add another size" href="javascript:;" class="add_button margin-left-table-btn" onclick="addSizeObject.addNewSizeRow(this)">
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="modal-footer">
                <input class="button btn btn-primary blue" id="mdl_save_change" type="submit" value="Save">
                <a href="{{ route('uniform-products') }}" class="btn btn-primary blue">Cancel</a>
            </div>
        </section>
    {!! Form::close() !!}
</div>
@stop

@section('js')
    <script>
        $('.select2').select2();

        const addSizeObject = {
            addSizeRowHtml: "{!! json_encode($measuringPoints) !!}",
            rowCount: 1,
            measuringPointCount: $('#measuringPointId').val().length,
            mesuringPointArray: [],
            measuringPoints: "",
            startLoading() {
                $('body').loading({
                    stoppable: false,
                    message: 'Please wait...'
                });
            },
            getAddSizeRow() {
                var htmlText = '<tr role="row">'+$('#myTable tbody tr:first').html()+'</tr>';
                return htmlText;
            },
            endLoading() {
                $('body').loading('stop');
            },
            init() {
            //Event listeners
            this.registerEventListeners();
            },
            registerEventListeners() {
                let root = this;


                   // $('#measuringPointId').change(function(e) {
                 $('#measuringPointId').on('select2:unselect select2:select', function (e) {
                    if(root.measuringPointCount < $('#measuringPointId').val().length) {
                         let measuringPointTobeAdded = e.params.data.id;

                        if(root.measuringPointCount == 0) {
                        $('.measure_type').empty();
                        $('.measure_type').append(`<div class="form-group measure_type_${root.measuringPoints[$('#measuringPointId').val()]}"> <input type="text" class="form-control" name="measure[]" value="${root.measuringPoints[$('#measuringPointId').val()]}" disabled><span class="help-block"></span></div>`);
                        $('.min div').addClass(`min_type_${root.measuringPoints[$('#measuringPointId').val()]}`);
                        $('.max div').addClass(`max_type_${root.measuringPoints[$('#measuringPointId').val()]}`);
                       } else {

                        $('.measure_type').append(`<div class="form-group measure_type_${root.measuringPoints[measuringPointTobeAdded]}"> <input type="text" class="form-control" name="measure[]" value="${root.measuringPoints[measuringPointTobeAdded]}" disabled>   <span class="help-block"></span></div>`);
                        $('.min').append(` <div class="form-group min_type_${root.measuringPoints[measuringPointTobeAdded]}"> <input class="form-control" type="number" class="min" name="min[]"><span class="help-block"></span> </div>`);
                        $('.max').append(`<div class="form-group max_type_${root.measuringPoints[measuringPointTobeAdded]}"> <input class="form-control" type="number" class="max" name="max[]"><span class="help-block"></span></div>`);

                       }
                    } else {
                         let measuringPointTobeRemoved = e.params.data.id;

                        if(root.measuringPointCount == 1) {
                            $('#myTable').find('tbody').empty();
                            $('#myTable tbody').append(`<tr> <td class="size_type"> <div class="form-group size_row" id="size_0"> <select class="form-control "  name=size[]> <option value="0">Please Select</option> @foreach ($sizes as $size=>$sizeName) <option value="{{$sizeName->id}}">{{ $sizeName->size_name}}</option> @endforeach </select> <span class="help-block"></span> </div></td> <td class="measure_type"> <div class="form-group"> <input type="text" class="form-control measure_type" name="measure[]" disabled>  <span class="help-block"></span> </div> </td> <td class="min" id="min_0"> <div class="form-group"> <input class="form-control" type="number" class="min" name="min[]">   <span class="help-block"></span></div> </td> <td class="max"> <div class="form-group" id="max_0"> <input class="form-control" type="number" class="max" name="max[]">  <span class="help-block"></span> </div> </td> <td> <a title="Add another size" href="javascript:;" class="add_button margin-left-table-btn" onclick="addSizeObject.addNewSizeRow(this)"> <i class="fa fa-plus" aria-hidden="true"></i> </td> </tr>`);
                        } else {
                            $('.measure_type_' + root.measuringPoints[measuringPointTobeRemoved]).remove();
                            $('.min_type_'+ root.measuringPoints[measuringPointTobeRemoved]).remove();
                            $('.max_type_'+ root.measuringPoints[measuringPointTobeRemoved]).remove();
                        }
                    }
                    positionReIndexing();
                    root.mesuringPointArray = $('#measuringPointId').val();
                    root.measuringPointCount = $('#measuringPointId').val().length;
                    root.addSizeRowHtml = addSizeObject.getAddSizeRow();
                });
            },
            addNewSizeRow() {
                let root = this;
                root.rowCount++;
                position=$('.size_type').length;
                $('#myTable tbody').append(root.addSizeRowHtml);
                $('#myTable tbody tr:last').attr('class', 'row-'+ root.rowCount);
                $('#myTable tbody tr:last .addOrRemove').empty().append(` <a title="Remove size" href="javascript:;" class="remove_button" onclick="addSizeObject.removeSizeRow(this)"> <i class="fa fa-minus" aria-hidden="true"></i><a title="Add another size" href="javascript:;" class="add_button margin-left-table-btn" onclick="addSizeObject.addNewSizeRow(this)"> <i class="fa fa-plus" aria-hidden="true"></i>`);
                $('#myTable tr.row-'+root.rowCount).find('input[type="number"]').val('');
                $('#myTable tr.row-'+root.rowCount).find('select').val(0);
                $('#myTable tr.row-'+root.rowCount).find('input[type="text"]').val('');
                $('#myTable tbody tr:last td.size_type').find('div').attr("id", "size_"+position);
                positionReIndexing();

            },

            removeSizeRow(currObj) {
               prev_tr_count=$(currObj).closest('tr').prevAll().length;
               minsize=$(currObj).closest('tr').prevAll().find('.min div').length;
               console.log(minsize)
               maxsize=$(currObj).closest('tr').prevAll().find('.max div').length;
                $(currObj).closest('tr').nextAll().each(function( index,value ) {
                 $(value).attr("class", 'row-'+prev_tr_count);
                 $(value).attr("data-row", prev_tr_count);
                 $(value).find('td.size_type div').attr("id", 'size_'+prev_tr_count);
                 prev_tr_count++;
              });
                $(currObj).closest('tr').remove();
                positionReIndexing();
            }
        }

        addSizeObject.startLoading();
        addSizeObject.addSizeRowHtml = addSizeObject.getAddSizeRow();
        addSizeObject.endLoading();

        $(function() {
            addSizeObject.init();
        })


        $(function() {
            //validate image

            validateAttachment=function(){
            let valid=0;
            let nodata=0;


            if($("#image-file").val()!="" && $("#uploadedS3ImageFileName").val()==""){
                valid++;
            }
            if($("#uploadedS3ImageFileName").val()!="" && $("input[name=name]").val()==""){
                valid++;
            }
            if($("#uploadedS3ImageFileName").val()!="" && $("input[name=id]").val()!=""){
                nodata++;
            }
            if($("#image-file").val()!=""){
                nodata++;
            }
            $('.uploadedS3AttachedFileName').each(function(i, obj) {
                let controlName=(obj.id).replace("uploadedS3AttachedFileName","time_off_attachment");
                let textControlName=(obj.id).replace("uploadedS3AttachedFileName","title_off_attachment");
                let attachmentControl=$("#"+controlName).val()
                let textControl=$("input[name="+textControlName+"]").val()
                if(attachmentControl!="" && obj.value==""){
                    valid++;
                }
                if(textControl=="" && obj.value!=""){
                    valid++;
                }
                if(attachmentControl!=""){
                    nodata++;
                }

            });
            if(parseInt(valid)>0){
                return false;
            }else{
                if(nodata>0){
                    return true
                }else{
                    return false;
                }

            }

        }

        //submit form
        $('#uniform-products-add-form').submit(function(e) {
            e.preventDefault();
            $('body').loading({
                    stoppable: false,
                    message: 'Please wait...'
                });
            if ($('#uniform-products-add-form input[name="id"]').val()) {
                var message = 'Uniform product updated successfully';
            } else {
                var message = 'Uniform product has been created successfully';
            }
            let editId=$("#uniform-products-add-form input[name=id]").val();
            let valid = validateAttachment();
            if(valid==true && editId==""){
                $('.filecontrol').each(function(i, obj) {
                    $("#"+obj.id).val("")
                });
                productFormSubmit($('#uniform-products-add-form'), "{{ route('uniform-products.store') }}", null, e, message);
                // $('body').loading('stop');
            }else if(editId>0 && valid==true){
                productFormSubmit($('#uniform-products-add-form'), "{{ route('uniform-products.store') }}", null, e, message);
                // $('body').loading('stop');
            }else{
                swal("Warning","Please validate inputs/Click upload","warning")
                $('body').loading('stop');
            }
        });

        $(document).on("click",".remove_attachment",function(e){
        e.preventDefault();
        let blockId=$(this).attr("attr-blockid");
        $(".blockrow"+blockId).remove()
        })

        })

        $(document).on("click",".add_attachment",function(e){

        //  $('#upload-attachment').append('<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12"><td class="data-list-disc attachment"><input type="file" class="form-control filecontrol" name="time_off_attachment[]" required></td><td class="data-list-disc attachment-button"><a title="Remove" href="javascript:;" class="remove_attachment"><i class="fa fa-minus size-adjust-icon" aria-hidden="true"></i> Remove Attachment</a></td></div>');
        let blockId=parseInt($("#blockId").val())+1;
        if(blockId <=3){
            $('#upload-attachment').append(` <div class="row blockrow${blockId}">
                </div><div class="row blockrow${blockId}"><label for="pan_mac" class="col-sm-2 control-label"></label>
                <div class="col-md-4"><input type="file" class="form-control filecontrol"  id="time_off_attachment${blockId}" name="time_off_attachment${blockId}">
                    <input type="hidden" name="uploadedS3AttachedFileName[]"
                        id="uploadedS3AttachedFileName${blockId}" value="" /><small class="help-block"></small></div>
                        <div class="col-sm-1"  id="upload-block${blockId}">
                <input class="btn btn-primary blue video-button"  attr-doctype="attachment" attr-block="${blockId}"
                type="button" value="Upload"   onclick="fileUpload(this);" />
                </div><div class="col-sm-1 test"> <a title="Remove" href="javascript:;" attr-blockid="${blockId}" class="remove_attachment">
                <i class="fa fa-minus size-adjust-icon" aria-hidden="true"></i> </a> </div></div>
                <div class="row blockrow${blockId}">
                    <div class="col-md-3"></div>
                    <div class="col-md-5" id="attachment-progressbar${blockId}">

                    </div>
                </div>`);
                $("#blockId").val(blockId);
        }
        })
    function positionReIndexing()
    {
        let minsize=0;
        let maxsize=0;
       $('.min div').each(function( index,minvalue ) {
            $(minvalue).attr("id", 'min_'+minsize);
             minsize++;
       });
       $('.max div').each(function( index,maxvalue ) {
            $(maxvalue).attr("id", 'max_'+maxsize);
             maxsize++;
       });
    }

    function fileUpload(val) {
        let docType=($(val).attr("attr-doctype"))
        $(val).prop("disabled",true)
        var url = {!!json_encode($uploadDet['url']) !!};
        let formData = new FormData();
        var signature = {!!json_encode($uploadDet['signature']) !!};
        var policy = {!!json_encode($uploadDet['policybase64']) !!};
        var iso_date = {!!json_encode($uploadDet['iso_date']) !!};
        var today = {!!json_encode($today) !!};
        console.log({!!json_encode($uploadDet['key']) !!});
        var presigned_url_expiry = {!!json_encode($uploadDet['presigned_url_expiry']) !!};
        var credential = {!!json_encode($uploadDet['amz_credentials']) !!};
        // var blockCount = $("#blockId").val();
        let filename=$("#image_file").val();
        let fileExtension="";
        let allowedExtension=["jpg","png","jpeg"]
        let valid=0;
        if(docType=="attachment"){

            filename=$("#time_off_attachment"+$(val).attr("attr-block")).val();
            filename= filename.split('\\').pop().split('/').pop();
            valid=1;
        }else{
            filename=$("#image_file").val();
            filename= filename.split('\\').pop().split('/').pop();
            fileExtension=filename.split('.').pop();
            if(allowedExtension.includes(fileExtension)){
                valid=1;
            }

        }

        if(valid==1){
            formData.append('X-amz-signature', signature);
        formData.append('acl', "private");
        formData.append('success_action_status', "201");
        formData.append('Content-Type', "");
        formData.append('policy', policy);
        formData.append('X-amz-algorithm', "AWS4-HMAC-SHA256");
        formData.append('X-amz-date', iso_date);
        formData.append('X-amz-expires', presigned_url_expiry);
        formData.append('X-amz-credential', credential);
        formData.append('key', `temp/uniform/${today+"/"+Date.now()+"_"+filename}`);
        if(docType=="attachment"){
            var inputName="time_off_attachment"+$(val).attr("attr-block");
            formData.append('file', $("#"+inputName)[0].files[0]);
        }else{
            var inputName="image-file";
            formData.append('file', $('input[type=file]')[0].files[0]);
        }
        if(filename!=""){
            $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            enctype: 'multipart/form-data',
            contentType: false,
            processData: false,
            xhr: function() {
                var xhr = $.ajaxSettings.xhr();
                if (xhr.upload) {
                    docType=$(val).attr("attr-doctype");
                    if(docType=="image"){
                        var progressbar = $("<div>", {style: "background:#607D8B;height:10px;margin:10px 0;"}).appendTo("#image-results"); //create progressbar
                    }else{
                        let attr_id=$(val).attr("attr-block");
                        var progressbar = $("<div>", {style: "background:#607D8B;height:10px;margin:10px 0;"}).appendTo("#attachment-progressbar"+attr_id); //create progressbar
                    }
                    xhr.upload.addEventListener('progress', function(event) {
                        var percent = 0;
                        var position = event.loaded || event.position;
                        var total = event.total;
                        if (event.lengthComputable) {
                            percent = Math.ceil(position / total * 100);
                            progressbar.css("width", +percent + "%");
                        }
                    }, true);
                }
                return xhr;
            },
            success: function (data) {
                if (data) {
                    var url = ($(data).find("Key").text()); //get file locatio
                    var the_file_name = $(data).find("Key").text(); //get uploaded file name
                    if(the_file_name != ''){
                        let docType=$(val).attr("attr-doctype");
                        if(docType=="image"){
                            // $('#myAwsModal #' + success).show();
                            $('#uniform-products-add-form #image-results').hide();
                            $('#uniform-products-add-form #success').html('');
                            $('#uniform-products-add-form #success').append('<p style="color:green;">File Uploaded</p>')
                            $("#uploadedS3ImageFileName").val(url)
                        }else{
                            //alert("attachment Uploaded")
                            let attr_id=$(val).attr("attr-block")
                            $('#upload-block'+attr_id).html("")
                            $("#uploadedS3AttachedFileName"+attr_id).val(url)
                            $('#attachment-progressbar'+attr_id).html('<p style="color:green;">File Uploaded</p>');
                        }

                    }
            //  $("#results").html("<span>File has been uploaded, Here's your file <a href=" + url + ">" + the_file_name + "</a></span>"); //response
         //   $('#myAwsModal input[name="' + fileid + '"]').val(the_file_name);

                }
            },
            error: function (xhr, textStatus, thrownError) {
                console.log(xhr.status);
                console.log(thrownError);
                $(val).prop("disabled",false)

                swal("Oops", "Something went wrong", "error");
            },

        });

        }else{
            $(val).prop("disabled",false)
            swal("Warning","File Name cannot be empty","warning")
        }
        }else{
            swal("Warning","Please upload a valid File Type","warning")
            $(val).prop("disabled",false)

        }

    }

    /* Form submit - Start */
function productFormSubmit($form, url, table, e, message) {
    var $form = $form;
    var url = url;
    var e = e;
    var table = table;
    var formData = new FormData($form[0]);
    return new Promise(function (resolve, reject) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: 'POST',
            data: formData,
            success: function (data) {
                if (data.success) {
                    swal({
                              title: "Saved",
                              text: message,
                              type: "success",
                              confirmButtonText: "OK",
                          },function(){
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                            window.location.href = "{{ route('uniform-products') }}";
                        });
                } else if (data.success == false) {
                    if (Object.prototype.hasOwnProperty.call(data, 'message') && data.message) {
                        swal("Warning", data.message, "warning");
                    } else {
                        console.log(data);
                    }
                } else {
                    console.log(data);
                }
                resolve(data);
            },
            fail: function (response) {
                resolve();
            },
            error: function (xhr, textStatus, thrownError) {
                associate_errors(xhr.responseJSON.errors, $form);
                resolve();
            }, always: function () {
                resolve();
            },
            contentType: false,
            processData: false,
        });
    });
}
/* Form submit - End */
    </script>
@stop
