@extends('layouts.app')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
       .filter{
        margin-left: -120px;

       } 
    </style>
</head>

@section('content')
<div class="table_title">
    <h4>Client Documents</h4>
</div>

<div class="timesheet-filters mb-2 filter-div">
    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-4"><label class="filter-text">Project Name</label></div>
                <div class="col-md-8 filter">
                    <select class="form-control option-adjust client-filter select2" name="clientname-filter" id="clientname-filter">
                        <option value="">Select Project</option>
                        @foreach($customer_list as $each_customername)
                        <option value="{{ $each_customername->id}}">{{ $each_customername->client_name .' ('.$each_customername->project_number.')' }}
                        </option>
                        @endforeach
                    </select>
                    <span class="help-block"></span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="pull-right" id="">
                <div class="check-controlline checkboxes" style="margin-top:70px;margin-right: 33px;">
                    <input type="hidden" name="requirements" value="" id="requirement_id_hidden">
                        <div class="checkboxs">
                             <input type="checkbox" id="activeCheck" class="chk" name="status_check" value="1"  checked="checked">
                             <label for="activeCheck">
                             Active
                            </label>
                        </div>
                        <div class="checkboxs">
                            <input type="checkbox" id="inactiveCheck" class="chk" name="status_check" value="0"  checked="checked">
                            <label for="inactiveCheck">
                            Inactive
                            </label>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>

<table class="table table-bordered" id="table-id">
     <thead>
         <tr>
             <th class="sorting" width="10%">Project Number</th>
             <th class="sorting" width="15%">Project Name</th>
             <th class="sorting" width="15%">Client Contact Person</th>
             <th class="sorting" width="15%">Client Contact Person Email</th>
             <th class="sorting" width="15%">Client Contact Phone Number</th>
             <th class="sorting" width="10%">Actions</th>
         </tr>
     </thead>
 </table> 
 
 @stop
 
 @section('scripts')
   <script>
    $(function () {
        function collectFilterData() {
            return {
                projectname: $("#clientname-filter").val(),
                projectno: $('#clientno-filter').val(),
            }
        }

        $('#activeCheck').prop('checked', true);
        $('#inactiveCheck').prop('checked', true);
        $.fn.dataTable.ext.errMode = 'throw';
        try{
            table = $('#table-id').DataTable({
            processing: false,
            fixedHeader: false,
            serverSide: true,
            responsive: true,
            ajax: {
                "url":'{{ route('client-document.list',[":checked"]) }}',
                "data": function ( d ) {
                    return $.extend({}, d, collectFilterData());
                            
                        },
                    "error": function (xhr, textStatus, thrownError) {
                    if(xhr.status === 401){
                        window.location = "{{ route('login') }}";
                    }
                },
            },
            dom: 'Blfrtip',
               
              buttons: [{
                    extend: 'pdfHtml5',
                    pageSize: 'A2',
                    exportOptions: {
                        columns: 'th:not(:last-child)', 
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: 'th:not(:last-child)',
                    }
                },
                {
                    extend: 'print',
                    pageSize: 'A2',
                    exportOptions: {
                        columns: 'th:not(:last-child)',
                        stripHtml: false,
                    }
                }
            ],
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [[ 0, "asc" ]],
            lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
           
            columns: [
                
                {data: 'project_number', name: 'project_number'},
                {data: 'client_name', name: 'client_name'},
                {data: 'contact_person_name', name: 'contact_person_name'},
                {data: 'contact_person_email_id', name: 'contact_person_email_id'},
                {data: 'contact_person_phone', name: 'contact_person_phone'},
                
                
                {
                    data: null,
                    sortable: false,
                    render: function (row) {
                        actions = '';
                        var user = row.allocated_flag;
                        @if(auth::user()->can('add_client_document'))
                        actions = '<a title="Add" href="{{route("add-client.document", ["typeid" => "" , "id" => ""])}}/'+ row.type_id +'/'+ row.id  +'"  class="fa fa-plus size-adjust-icon" id="add_document"></a>';
                        @elseif(auth()->user()->can('add_allocated_client_document'))
                        if(user==1)
                        {
                            actions = '<a title="Add" href="{{route("add-client.document", ["typeid" => "" , "id" => ""])}}/'+ row.type_id +'/'+ row.id  +'"  class="fa fa-plus size-adjust-icon" id="add_document"></a>';
                        }
                        @endif
                        actions +='<a title="View"  href="{{ route("documents.view-document",["typeid" => "", "id" => ""])}}/'+ row.type_id +'/'+ row.id +'" class="view btn fa fa-eye id="view_document"></a>';

                      return actions;
                       
                           }      
                           }
                ]
        });
        } catch(e){
            console.log(e.stack);
        }
        });

        $(".client-filter").change(function(){
            table.ajax.reload();
        });
        $(".select2").select2()
        
        $("input[name='status_check']").change(function(){
        var length=  $('.chk:checked').length;
        if(length == 2) {
            var checked = ":checked";
        } else {
            //if active is checked
            if($('#activeCheck').is(':checked')){
                $('#activeCheck').prop('checked', true);
                var checked = $('#activeCheck').val();
            } else {
                $('#inactiveCheck').prop('checked', true);
                var checked = $('#inactiveCheck').val();
            }
              //if inactive is checked
            if($('#inactiveCheck').is(":checked")){
                $('#inactiveCheck').prop('checked', true);
                var checked = $('#inactiveCheck').val();
            }else{
                $('#activeCheck').prop('checked', true);
                var checked = $('#activeCheck').val();
            }
        }
        var table = $('#table-id').DataTable();
        var url = '{{ route('client-document.list',[":checked"]) }}';
            url = url.replace(':checked', checked);
            table.ajax.url( url ).load();
        

    });


    </script>
    <style>
        
    .client-filter {
        padding-right: 0px;
        padding-bottom: 5px;
        z-index: 6000;
    }
    #clientname-filter {
        width: 365px;
    }
    .select2-dropdown {
        z-index: 0 !important;
    }
    .filter-div{
     padding-bottom: 15px;
    }
    .checkboxs {
            padding-left: 20px;
        }
        .check-controlline .checkboxs {
            float: left;
            line-height: 16px;
        }
        .pull-right {
       
    margin-top: -40px;
            
        }
    </style>
                @stop
