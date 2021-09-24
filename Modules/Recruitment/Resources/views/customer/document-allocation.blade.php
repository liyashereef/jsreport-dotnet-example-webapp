{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')
@section('title', 'Document Allocation')
@section('content_header')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<h3>Document Allocation</h3>
@stop
@section('content')
<div class="container-fluid container-wrap">
    {{ Form::open(array('route'=> 'recruitment.customer-uniform-kits.add','id'=>'document-allocation-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
    <!-- Main content -->
    <section class="content">
        <div class="form-group row" id="customer_id">
          <label class="col-form-label col-md-2">  Choose Customer <span class="mandatory">*</span></label>
            <div class="col-md-4">
                <select   name="customer_id" class="form-control select2">
                    <option value="Please Select" selected>Please Select</option>
                    @foreach($customer_list as $projectid => $eachProject)
                   <option value="{{$projectid}}" @if(isset($customer_id) && $projectid == $customer_id) selected @endif >{{$eachProject}}</option>
                   @endforeach
                </select>
                 <span class="help-block"></span>
            </div>
        </div>
        <div class="form-group row" id="category_id">
          <label class="col-form-label col-md-2">  Choose Category <span class="mandatory">*</span></label>
            <div class="col-md-4">
                <select  name="category_id" id="category_id" class="form-control">
                    <option value="Please Select" selected>Please Select</option>
                    @foreach($process_tabs as $each_tab)
                   {{-- <option value="{{$each_tab->id}}" @if(isset($postorder_data) && $projectid == $postorder_data->customer_id) selected @endif >{{$each_tab->display_name}}</option> --}}
                   @if($each_tab->id == 6 || $each_tab->id == 7 || $each_tab->id == 8)
                   <option value="{{$each_tab->id}}" @if(isset($category_id) &&  $category_id == $each_tab->id) selected @endif >{{$each_tab->display_name}}</option>
                   @endif
                   @endforeach
                </select>
                 <span class="help-block"></span>
            </div>
        </div>

       {{-- <div class="row">
        <ul class="breadcrumb breadcrumb-arrow nav nav-tabs width-100" role="tablist">
            <li class="nav-item complete">
                <a class="nav-link active" data-toggle="tab" href="#profile">
                    <span> Enrolment Forms
                    </span>
                </a>
            </li>
            <li class="nav-item complete">
                <a class="nav-link" data-toggle="tab" href="#questions">
                    <span>   Security Clearence
                    </span>
                </a>
            </li>
            <li class="nav-item complete">
                <a class="nav-link" data-toggle="tab" href="#questions">
                    <span>   Tax Forms
                    </span>
                </a>
            </li>
        </ul>
       </div> --}}

        <br>
        <div class="table-responsive">
            <table class="table table-bordered dataTable " role="grid" aria-describedby="position-table_info"  id="add-table">
                <thead>
                    <tr>
                        <th class="sorting_disabled" style="white-space: nowrap">Document</th>
                        <th class="sorting_disabled" style="white-space: nowrap">Document Name</th>
                        <th class="sorting_disabled" style="white-space: nowrap">Order</th>
                        <th class="sorting_disabled">Action</th>
                    </tr>
                </thead>
                <tbody id="option-values">

                @if(isset($customer_id) && !$document_list->isEmpty())
                        @foreach($document_list as $key=>$doc_list)
                        <tr role="row" class="option-row">
                    <td>
                    <input   type="hidden" class="id" name="id[]" value="{{$doc_list->id}}"/>
                        <div class="form-group document_id" id="document_id_{{isset($key)?($key):"0"}}">
                        <select style="margin-top:5px;" class="form-control"  name="document_id[]" onchange="documentName(this)">
                            <option value="0"  >Please Select</option>
                                @if(!empty($onboarding_documents))
                                @foreach ($onboarding_documents as $eachItem)
                                        <option value="{{ $eachItem->id }}" @if($doc_list->document_id == $eachItem->id) selected @endif >{{$eachItem->document_name}} </option>
                               @endforeach
                               @endif
                        </select>
                         <span class="help-block"></span>
                        </div>
                    </td>
                             <td>

                                <div class="form-group document_name" id="document_name_{{isset($key)?($key):"0"}}">
                                    <input  class="form-control cls-option-name"   type="text" name="document_name[]" value="{{$doc_list->document_name}}"/>
                                         <span class="help-block"></span>
                                </div>
                             </td>

                                <td  align="center" >
                                   <div class="form-group order" id="order_{{isset($key)?($key):"0"}}">

                                    <input  class="form-control cls-option-order" style="width: 68px;" type="text" name="order[]" value="{{$doc_list->order}}"/>        <span class="help-block"></span>
                                </div>
                               </td>
                                <td  align="center" class="sorting_disabled">
                                 <div class="input-group">
                                     <span>

                                              <a title="Remove Option" href="javascript:;"  class="remove_button"  >
                                           <i class="fa fa-minus" aria-hidden="true" onclick="removeDocument(this)"
                                            {{-- @if(!isset($key) || (isset($key) && $key == 0) || ($key <= count($doc_list_list)))
                                            style="display: none;"
                                            @endif --}}
                                            ></i>
                                     </a>
                                 </span>

                                 <span>

                                    <a title="Add another option" href="javascript:;" class="add_button margin-left-table-btn">
                                        <i class="fa fa-plus" aria-hidden="true" onclick="questionsObj.addQuestion(this)"></i>

                                    </a>
                                </span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @else

               <tr role="row" class="option-row">

                    <td>
                        <input type="hidden" class="id" name="id[]" value=""/>
                        <div class="form-group document_id" id="document_id_0">
                        <select style="margin-top:5px;" class="form-control document"  name="document_id[]" onchange="documentName(this)">
                            <option value="0"  >Please Select</option>
                                @if(!empty($onboarding_documents))
                                @foreach ($onboarding_documents as $eachItem)
                                        <option value="{{ $eachItem->id }}">{{$eachItem->document_name}} </option>
                               @endforeach
                               @endif
                        </select>
                         <span class="help-block"></span>
                        </div>
                    </td>
                    <td>
                        <div class="form-group document_name" id="document_name_0">
                        <input  class="form-control"   type="text" name="document_name[]" value=""/>
                           <span class="help-block"></span>
                        </div>
                     </td>

                     <td align="center">
                        <div class="form-group order" id="order_0">
                         <input  class="form-control cls-option-order" style="width: 68px;"  type="text" name="order[]" value=""/>
                        <span class="help-block"></span>
                     </div>
                    </td>
                    <td  align="center" class="sorting_disabled">
                     <div class="input-group">
                         <span
                            <a title="Remove Option" href="javascript:;"   class="remove_button" disabled="true" onclick="questionsObj.removeQuestion(this)" >
                             <i class="fa fa-minus"  disabled="true" aria-hidden="true" @if(!isset($key) || (isset($key) && $key == 0)) style="display: none;" @endif></i>
                         </a>
                     </span
                     <span>
                        <a title="Add another option" href="javascript:;" class="add_button margin-left-table-btn" onclick="questionsObj.addQuestion(this)">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                        </a>
                     </span>
                        </div>
                    </td>
                </tr>
    @endif
        </tbody>
    </table>
</div>

<div class="modal-footer">
    <input class="button btn btn-primary blue" id="mdl_save_change" type="submit" value="Save">
    <a href="" class="btn btn-primary blue">Cancel</a>
</div>

</section>
{{ Form::close() }}
</div>
@stop


@section('js')
<script>
$(function () {

    $('.select2').select2();

    questionsObj = {
        childArray: [],
        questionElementCount: 1,
        questionRowHtml: "",
        startLoading: function(){
            $('body').loading({
                stoppable: false,
                message: 'Please wait...'
            });
        },
        endLoading: function(){
            $('body').loading('stop');
        },
        addQuestion: function(currObj){
            questionsObj.startLoading();
            this.questionElementCount++;
            var htmlStr = questionsObj.questionRowHtml.replace(/readonly="true"|fa-disabled/g,"");
            $('#option-values').append(htmlStr);
            var newHtmlObj = $('#option-values tr:last');
            this.prepareNextRow(newHtmlObj);
            questionsObj.endLoading();
        },
        removeQuestion: function(currObj){
                var removedPos = $(currObj).closest('tr').find('input[name="position[]"]').val();
                var removedPosition = $(currObj).closest('tr').prevAll().length+1;
                position_num=removedPosition-1;
                $(currObj).closest('tr').nextAll().each(function( index,value ) {
                    $(value).find('.document_name').attr("id", 'document_name_'+position_num);
                       $(value).find('.document_id').attr("id", 'document_id_'+position_num);
                       $(value).find('.order').attr("id", 'order_'+position_num);
                   position_num++;
               });
                $(currObj).closest('tr').remove();

            },
            getQuestionRow:function(){
                var htmlText = '<tr role="row" class="option-row">'+$('#option-values tr:first').html()+'</tr>';
                return htmlText.replace(/checked="checked"/g, "");
            },
            prepareNextRow: function(htmlObj){
                    //reset values
                    console.log(htmlObj)
                    $(htmlObj).find(".document_id select").val('0');
                    $(htmlObj).find(".cls-option-name").val('');
                    $(htmlObj).find(".cls-option-info").val('');
                    if($("#infomn_id").val()==1){
                    $(htmlObj).find(".info_value_default").attr('style','');
                    }
                    $(htmlObj).find(".cls-option-order").val('');
                    var positionHtml = this.questionElementCount+'<input type="hidden" name="position[]" value="'+this.questionElementCount+'"/>';
                    $(htmlObj).find(".cls-slno").html(positionHtml);

                    $(htmlObj).find(".fa-minus").show();
                    $(htmlObj).find(".document_name").attr('id','document_name_'+(this.questionElementCount*1-1));
                    $(htmlObj).find(".document_id").attr('id','document_id_'+(this.questionElementCount*1-1));
                    $(htmlObj).find(".order").attr('id','order_'+(this.questionElementCount*1-1));
                     //Add id value for the newly added textbox to show validation messages
                     position_num=$('.option-row').length-1;
                      $(".id:last").val('');
                     $(".document_name:last").attr("id", 'document_name_'+position_num);
                     $(".document_id:last").attr("id", 'document_id_'+position_num);
                     $(".order:last").attr("id", 'order_'+position_num);


                     var total_count = $('.cls-slno' ).length-1;

                 }
         }
         questionsObj.startLoading();
         questionsObj.questionRowHtml = questionsObj.getQuestionRow();
         questionsObj.endLoading();

         $('#add-table').dataTable({
                "bPaginate": false
          });



         $('#document-allocation-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            // $('select[name="info_id"]').prop('disabled',false);
           // for (instance in CKEDITOR.instances)
          //  CKEDITOR.instances[instance].updateElement();
            url = "{{ route('recruitment.document-allocation.store') }}";
            var formData = new FormData($('#document-allocation-form')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                        if(data.result == false){
                            result = "Document Allocation has been updated successfully";
                        }else{
                            result = "Document Allocation has been created successfully";
                        }
                        swal({
                          title: "Saved",
                          text: result,
                          type: "success",
                          confirmButtonText: "OK",
                      },function(){
                        // $('.form-group').removeClass('has-error').find('.help-block').text('');
                        window.location.href = "{{ route('recruitment.document-allocation') }}";
                    });
                    } else {
                        $('.form-group').removeClass('has-error').find('.help-block').text('');
                        console.log(data);
                    }
                },
                fail: function (response) {
                    console.log(data);
                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form, true);
                },
                contentType: false,
                processData: false,
            });
        });

    });

//     $('#customer_id').on('change', function () {
//             if($('#customer_id').val() != 0){
//                 customer_id = $('#customer_id').val();
//                 var base_url = "{{ route('recruitment.document-allocation.get',':cid') }}";
//                 var url = base_url.replace(':cid', customer_id);
//                 window.location.href = url;
//             }else{
//               //  window.location.href = "{{ route('recruitment.document-allocation') }}";
//             }
//    });

    $('select[name="customer_id"]').on('change', function() {
       if ($('select[name="customer_id"]').val() != 'Please Select' && $('select[name="category_id"]').val() != 'Please Select') {
            customer_id = $('select[name="customer_id"]').val();
            category_id = $('select[name="category_id"]').val();
            getCustomerCategoryDocument(customer_id, category_id);
       }
    });

   $('#category_id').on('change', function () {
       if($('select[name="customer_id"]').val() != 'Please Select' && $('select[name="category_id"]').val() != 'Please Select') {
            customer_id = $('select[name="customer_id"]').val();
            category_id = $('select[name="category_id"]').val();
            getCustomerCategoryDocument(customer_id, category_id);
       }
   });

   function getCustomerCategoryDocument(custId, catId) {
        var base_url = "{{ route('recruitment.document-allocation.getCategoryDocument',[':custid', ':catid']) }}";
        var url1 = base_url.replace(':custid', custId);
        url = url1.replace(':catid', catId);
        window.location.href = url;
   }

   function documentName(el) {
        if ($("option:selected", el).val() == 0) {
            $(el).closest("tr").find("td:eq(1) input[type='text']").val('');
        } else {
            var docName = $("option:selected", el).text();
            $(el).closest("tr").find("td:eq(1) input[type='text']").val(docName);
        }
   }

   function removeDocument(currObj) {
       var id = $(currObj).closest('tr').find('input[name="id[]"]').val();
        var base_url = "{{ route('recruitment.document-allocation.destroy',':id') }}";
        var url = base_url.replace(':id', id);
        swal({
            title: "Are you sure?",
            text: "You will not be able to undo this action. Proceed?",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes, remove",
            showLoaderOnConfirm: true,
            closeOnConfirm: false
        },
        function () {
            $.ajax({
                url: url,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    if (data.success) {
                        swal("Deleted", "Document has been deleted successfully", "success");
                        questionsObj.removeQuestion(currObj);
                        if ($('#option-values tr').length === 0) {
                            questionsObj.addQuestion();
                            custId = $('select[name="customer_id"]').val();
                            catId = $('select[name="category_id"]').val();
                            var base_url = "{{ route('recruitment.document-allocation.getCategoryDocument',[':custid', ':catid']) }}";
                            var url1 = base_url.replace(':custid', custId);
                            url = url1.replace(':catid', catId);
                            window.location.href = url;
                        }

                    }else{
                        swal("Warning",data.message, "warning");
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                },
                contentType: false,
                processData: false,
            });
        });
   }

</script>
<style type="text/css">
    .fa-disabled {
opacity: 0.6;
cursor: not-allowed;
}
.nav-tabs {
    border-bottom: 0px solid #ddd;
}

.breadcrumb-arrow {
    min-height: 36px;
    padding: 0;
    line-height: 36px;
    list-style: none;
    margin-bottom: 0;
    margin-top: 1rem;
    /*overflow: auto;*/
    background: none;
    /*background: linear-gradient(to right, #eaeaea 0%,#ffffff 100%);*/
}

.breadcrumb-arrow li:first-child a {
    border-radius: 4px 0 0 4px;
    -webkit-border-radius: 4px 0 0 4px;
    -moz-border-radius: 4px 0 0 4px;
}

.breadcrumb-arrow li {
    width: 20%;
}

.breadcrumb-arrow li a,
.breadcrumb-arrow li span {
    display: list-item;
    width: auto;
    /*vertical-align: top;*/
}

.breadcrumb-arrow li:not(:first-child) {
    margin-left: 0px;
}

.breadcrumb-arrow li+li:before {
    padding: 0;
    content: "";
}

.breadcrumb-arrow li span {
    padding: 0 10px;
}

.breadcrumb-arrow li a,
.breadcrumb-arrow li:not(:first-child) span {
    height: 36px;
    padding: 0 10px;
    line-height: 36px;
}

.breadcrumb-arrow li:first-child a {
    padding: 0 10px;
}

.breadcrumb-arrow li a {
    position: relative;
    color: #ffffff;
    text-decoration: none;
    background-color: #dddddd !important;
    border: 1px solid #dddddd !important;
    width: 100%;
    font-size: 15px;
    line-height: 34px;
}

.breadcrumb-arrow li:first-child a {
    padding-left: 10px;
}

.breadcrumb-arrow li a:after,
.breadcrumb-arrow li a:before {
    position: absolute;
    top: -1px;
    width: 0;
    height: 0;
    content: '';
    border-top: 18px solid transparent;
    border-bottom: 18px solid transparent;
}

.breadcrumb-arrow li a:before {
    right: -10px;
    z-index: 3;
    border-left-color: #dddddd;
    border-left-style: solid;
    border-left-width: 10px;
}

.breadcrumb-arrow li a:after {
    right: -11px;
    z-index: 2;
    border-left: 11px solid #fff;
}

.breadcrumb-arrow li.active span {
    width: auto;
    text-decoration: none;
    color: #ffffff;
    background-color: #003A63 !important;
    border: 1px solid #003A63 !important;
    margin-left: -11px;
    height: 36px;
    margin-top: -1px;
    padding-left: 20px;
}

.breadcrumb-arrow li.active:first-child span {
    padding-left: 10px;
}

.breadcrumb-arrow li.active span:after,
.breadcrumb-arrow li.active span:before {
    position: absolute;
    top: -1px;
    width: 0;
    height: 0;
    content: '';
    border-top: 18px solid transparent;
    border-bottom: 18px solid transparent;
}

.breadcrumb-arrow li.active span:before {
    right: -10px;
    z-index: 3;
    border-left-color: #003A63;
    border-left-style: solid;
    border-left-width: 11px;
}

.breadcrumb-arrow li.active span:after {
    right: 0px;
    z-index: 2;
    border-left: 10px solid #003A63;
    background: #003A63;
}

.breadcrumb-arrow li.complete span {
    background-color: #003A63 !important;
    border: 1px solid #003A63 !important;
    width: auto;
    text-decoration: none;
    text-align: center;
    color: #d4edda;
    margin-left: -11px;
    height: 36px;
    margin-top: -1px;
    padding-left: 20px;
}

.breadcrumb-arrow li.complete span:before {
    right: -10px;
    z-index: 3;
    border-left-color: #003A63 !important;
    border-left-style: solid;
    border-left-width: 11px;
}

.breadcrumb-arrow li.complete span:after {
    right: 0px;
    z-index: 2;
    border-left: 10px solid #003A63 !important;
    background: #003A63 !important;
}

.breadcrumb-arrow li.success span {
    width: auto;
    text-decoration: none;
    color: #d4edda;
    background-color: #155724 !important;
    border: 1px solid #155724 !important;
    margin-left: -11px;
    height: 36px;
    margin-top: -1px;
    padding-left: 20px;
}

.breadcrumb-arrow li.success:first-child span {
    padding-left: 10px;
}

.breadcrumb-arrow li.success span:after,
.breadcrumb-arrow li.success span:before,
.breadcrumb-arrow li.complete span:after,
.breadcrumb-arrow li.complete span:before {
    position: absolute;
    top: -1px;
    width: 0;
    height: 0;
    content: '';
    border-top: 18px solid transparent;
    border-bottom: 18px solid transparent;
}

.breadcrumb-arrow li.success span:before {
    right: -10px;
    z-index: 3;
    border-left-color: #155724;
    border-left-style: solid;
    border-left-width: 11px;
}

.breadcrumb-arrow li.success span:after {
    right: 0px;
    z-index: 2;
    border-left: 10px solid #155724;
    background: #155724;
}

.nav-tabs .nav-link.disabled {
    color: rgb(0, 58, 99);
    cursor: not-allowed;
}

.nav-tabs .nav-link.disabled:hover {
    color: rgb(0, 58, 99);
    background: #f48452;
    display: block;
}

.nav-tabs .nav-item.show .nav-link,
.nav-tabs .nav-link.active {
    color: #f48452 !important;
}

.nav-tabs .nav-item.show .nav-link,
.nav-tabs .nav-link.active {
    color: #ffffff;
}

.tabbed-content .nav-tabs .nav-link.active {
    color: #073e61;
    font-weight: bold;
}
</style>
@stop
