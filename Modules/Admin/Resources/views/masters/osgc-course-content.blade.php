@extends('adminlte::page')
@section('title', 'OSGC Course Contents')
@section('content_header')
<h1>{{ $result->title ?? ''}} </h1>
@stop
@section('content')
<div class="add-new" data-title="Add New Course">Add
    <span class="add-new-label">New</span>
</div>

<input type="hidden" name="title" id="title" value="{{ $result->title ?? ''}}">
<table class="table table-bordered" id="course-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Headings</th>
            <th>Section Name</th>
            <th>Price</th>
            <th>Content Type</th>
            <th>Sort Order</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>
<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">OSGC Course Contents</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'course-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}

            <div class="modal-body">
                </ul>
                <input type="hidden" name="course_id" id="course_id" value="{{$courseId}}">
                <div class="form-group" id="header_id">
                    <label for="header_id" class="col-sm-3 control-label">Course Heading<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                    {{Form::select('header_id',[null=>'Please Select']+$headings,null, ['class' => 'form-control select2','id' => 'header_id', 'style'=>"width: 100%;",'required'=>TRUE])}}
                   
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="name">
                    <label for="name" class="col-sm-3 control-label">Course Section Name<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::text('name',null,array('class'=>'form-control', 'Placeholder'=>'Course Section Name','required', "maxlength"=>"27")) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="price">
                    <label for="price" class="col-sm-3 control-label">Course Price<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::text('price',null,array('class'=>'form-control', 'Placeholder'=>'Course Price','required', "maxlength"=>"255")) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="sort_order">
                    <label for="sort_order" class="col-sm-3 control-label">Sort Order<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::text('sort_order',null,array('class'=>'form-control', 'Placeholder'=>'Sort Order','required', "maxlength"=>"255")) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="content_type">
                    <label for="content_type" class="col-sm-3 control-label">Content Type<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                    <select  name="content_type_id"  id="content_type_id" class="form-control" required>
                        <option value="">Select</option>
                        @foreach($contentTypes as $row)
                        <option value="{{$row->id}}">{{$row->type}}</option>
                        @endforeach
                    </select>
                    
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="course_content">
                    <label for="course_content" class="col-sm-3 control-label">course Content<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                    {{ Form::file('course_content',null,array('required')) }}
                    <label id="course_file_name"></label>
                         <style>
                            .progress {
                                position: relative;
                                width: 100%;
                                padding: 1px;
                                border-radius: 3px;
                                margin-top: 1%
                            }

                            .bar {
                                background-color: #F48452;
                                width: 0%;
                                height: 25px;
                                border-radius: 3px;
                            }

                            .percent {
                                position: absolute;
                                display: inline-block;
                                top: 1%;
                                left: 48%;
                                color: #003A63;
                            }
                        </style>
                        <div class="progress">
                            <div class="bar"></div>
                            <div class="percent">0%</div>
                        </div>
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="completion_mandatory">
                    <label for="completion_mandatory" class="col-sm-3 control-label">Is Course Completion mandatory?</label>
                    <div class="col-sm-9">
                    
                    {{ Form::checkbox('completion_mandatory', 1) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                
               
                
            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::submit('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
<div class="modal fade" id="delete_Modal" tabindex="-1" role="dialog" aria-labelledby="delete_ModalLabel" aria-hidden="true">
<div class="modal-dialog modal-confirm">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Warning</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to delete your account? This action cannot be undone and you will be unable to recover any data.</p>
			</div>
			<div class="modal-footer">
                <a href="#" class="btn btn-info" data-dismiss="modal">Cancel</a>

			</div>
		</div>
	</div>
</div>
@stop @section('js')
<style>
.heading-div   {
            display: block;
            margin-left:0px;
            padding-left:0px;

        }
        .heading-div label {
            width: 60px;
            margin-left: 0px;
            margin-right: 1%;

        }
        #course-heading{
            margin-left:0px;
            padding-left:0px;
        }
        #course-heading select {
            width: 35%;

        }
        .dataTable a.view,.dataTable a.add, .dataTable .edit-disable {
    padding-right: 8%;
}
</style>
<script>
    $(function () {

            $.fn.dataTable.ext.errMode = 'throw';
        try{
            
        var table = $('#course-table').DataTable({
            dom: 'lfrtBip',
                bprocessing: false,
                buttons: [
                {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [ 0,1, 2, 3,4,5,6,7,8,9]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0,1, 2, 3,4,5,6,7,8,9]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [ 0,1, 2, 3,4,5,6,7,8,9]
                    }
                },
                {
                    text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o',
                    action: function (e, dt, node, conf) {
                        emailContent(table, 'Cpid');
                    }
                }
                ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: {
                    "url":'{{ route("osgc-course-contents.list") }}',
                    "data": function ( d ) {
                        d.course_id = $("#course_id").val();
                        
                    },
                    "error": function (xhr, textStatus, thrownError) {
                        if(xhr.status === 401){
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
           
            // order: [
            //     [10, "desc"]
            // ],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [{
                    data: 'DT_RowIndex',
                    name: '',
                    sortable:false,
                },
                {
                    data: 'course_heading.name',
                    name: 'course_heading.name'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data:null,render:function(o){
                        var name='';
                        if(o.course_content)
                        {   
                            if(o.course_content.course_price)
                            {   
                                name= o.course_content.course_price.price
                            }
                        }
                        return name;
                    },
                    name:'name'
                },
                {
                    data:null,render:function(o){
                        var name='';
                        if(o.course_content)
                        {   
                            if(o.course_content.course_content_type)
                            {   
                                name= o.course_content.course_content_type.type
                            }
                        }
                        return name;
                    },
                    name:'name'
                },
                // {
                //     data:null,render:function(o){
                //         var name='dd';
                //         if(o.course_headers.length !=0)
                //         {   
                //             if(o.course_headers.name !=undefined)
                //             {   console.log(o.course_headers.name)
                //                 name= o.course_headers.name
                //             }
                //         }
                //         return name;
                //     },
                //     name:'name'
                // },
                {
                    data: 'sort_order',
                    name: 'sort_order'
                },

                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        @can('edit_masters')
                        actions += '<a href="#" class="edit fa fa-pencil" data-id=' + o.id + '></a>'
                        @endcan
                       
                        return actions;
                    },
                }
            ]
        });
         } catch(e){
            console.log(e.stack);
        }
        var bar = $('.bar');
        var percent = $('.percent');
        /* Posting data to PositionLookupController - Start*/
        $('#course-form').submit(function (e) {
            e.preventDefault();
            var $form = $('#course-form');
            var formData = new FormData($form[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                xhr: function () {
                    var xhr = new window.XMLHttpRequest();
                    var percentVal = '0%';
                    xhr.upload.addEventListener("progress", function (evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            percentComplete = parseInt(percentComplete * 100);
                            bar.width(percentComplete + '%')
                            percent.html(percentComplete + '%');
                            if (percentComplete === 100) {
                                console.log('completed');
                            }
                        }
                    }, false);

                    return xhr;
                },
                url: '{{ route("osgc-course-contents.store") }}',
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                        // console.log(data)
                        if($('#course-form input[name="id"]').val()){
                            swal("Saved", "Course content has been updated successfully", "success");
                        }else{
                            swal("Saved", "Course content has been created successfully", "success");
                        }
                        $("#course-form")[0].reset();
                        bar.width('0%')
                        percent.html('0%');
                        $("#myModal").modal('hide');
                        if (table != null) {
                            table.ajax.reload();
                        }
                    } else {
                        console.log('else',data);
                    }
                },
                fail: function (response) {
                    console.log(response);
                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form);
                    bar.width('0%')
                    percent.html('0%');
                },
                contentType: false,
                processData: false,
            });
        });


            

        /* Clear Uploaded File label - Start */
        $('.add-new').click(function(){
           
           $("#myModal").modal();
           
        $('#myModal form').trigger('reset');
           $('#myModal input[name="course_id"]').val({{$courseId}});
            

        });
        /* Clear Uploaded File label - End */
        $("#course-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("osgc-course-contents.single",":id") }}';
            var url = url.replace(':id', id);
            $('#course-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    console.log(data)
                    $("#course-form").trigger('reset');
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="name"]').val(data.name)
                        $('#myModal input[name="sort_order"]').val(data.sort_order)
                        if(data.course_content)
                        {
                            $('#myModal input[name="price"]').val(data.course_content.course_price.price)
                        }
                        $('#myModal select[name="header_id"] option[value="'+data.header_id+'"]').prop('selected',true);
                        if(data.course_content)
                        {
                            $('#myModal select[name="content_type_id"] option[value="'+data.course_content.content_type_id+'"]').prop('selected',true);
                        
                        }
                        if(data.course_content){
                            $('#myModal #course_file_name').text(data.course_content.content)
                            $('#myModal #course_file_name').css('font-weight',500)
                        }
                        if(data.completion_mandatory ==1)
                        {
                            $('#myModal input[name="completion_mandatory"]').prop('checked',true);
                        }
                        //.prop('checked', true);
                       // $('#myModal input[name="sort_order"]').val(data.sort_order)
                       
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit OSGC Course: "+ data.name)
                    } else {
                        alert(data);
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                },
                contentType: false,
                processData: false,
            });
        });

        $('#course-table').on('click', '.delete', function (e) {

            var id = $(this).data('id');
            var base_url = "{{ route('osgc-course.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Course has been deleted successfully';
            deleteRecord(url, table, message);
           
        });

        $('.remove-heading').hide();
        $("#add-heading").on("click", function(e) {
            $last_row_no = $(".course-heading-table").find('tr:last .row-no').val();
            if ($last_row_no != undefined) {
                $next_row_no = ($last_row_no * 1) + 1;
            } else {
                $next_row_no = 0;
            }

            var customer_cpid_allocation_new_row =
                "<tr><td><div class='form-group' id='content_heading_" + $next_row_no + "'><input type='text' name='row-no[]' class='row-no'><div class='col-sm-5'><input type='text' name='heading_" + $next_row_no + "' class='form-control'><small class='help-block'></small></div><div class='col-sm-5'><input type='text' name='sort_order_" + $next_row_no + "' class='form-control'><small class='help-block'></small></div><div class='col-sm-2'><label for='remove-heading'  class='btn btn-primary remove-heading'>-</label></div></div></td></tr>";
            $(".course-heading-table tbody").append(customer_cpid_allocation_new_row);
            $(".course-heading-table").find('tr:last').find('.row-no').val($next_row_no);

            if ($last_row_no > 0 || $last_row_no == undefined) {
                $('.remove-heading').show();
            }
        });
        //*
        $(".course-heading-table").on('click', '.remove-heading', function () {
            var t=$(this).closest('tr');
            var id = t.find(".heading_id").val();
            var url = '{{ route("osgc-course.deleteCourseHeader",":id") }}';
            var url1 = url.replace(':id', id);
            
            
            $.ajax({
                url: url1,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    console.log(data)
                    if (data.success == true) {console.log('dd')
                        t.remove();
                        
                    } else {
                        var swalHtml = `You cannot delete this Header Beacause It is Used in Course Content.`;
                                swal({
                                    title: "Failed",
                                    text : swalHtml,
                                    html: true,
                                    icon: "warning",
                                    confirmButtonText: "OK",
                                });
                               
                    }
                }
            });
           
          

        });
        
        $('.remove-heading').show();
            $('#course-heading tbody').find('tr').remove();

        $customer_cpid_first_row = "<tr><td><div class='form-group' id='content_heading_0'><input type='text' name='row-no[]' class='row-no' value='0'><div class='col-sm-5'><input type='text' name='heading_0' class='form-control'><small class='help-block'></small></div><div class='col-sm-5'><input type='text' name='sort_order_0' class='form-control'><small class='help-block'></small></div><div class='col-sm-2'><label for='remove-heading'  class='btn btn-primary remove-heading'>-</label></div></div></td></tr>";
            $('#course-heading tbody').append($customer_cpid_first_row);
       
    });
</script>
@stop
