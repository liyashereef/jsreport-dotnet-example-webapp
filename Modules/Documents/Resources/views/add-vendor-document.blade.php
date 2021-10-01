@extends('layouts.app')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@section('content')
<div class="table_title">
    <h4> {{$result[0]['category_name']}}    Details</h4>
</div>

{{ Form::hidden('document_type_id', $result[0]['document_type_id'],array('id'=>'type_id')) }}
{{ Form::hidden('id', $result[0]['id'],array('id'=>'id')) }}
<table class="table table-bordered" id="table-id">
     <thead>
         <tr>
        
             <th class="sorting" width="10%">Names </th>
             <th class="sorting" width="10%"> Number of Documents</th>
             <th class="sorting" width="10%">Last Updated Date</th>
             <th class="sorting" width="10%">Actions</th>
         </tr>
     </thead>
 </table> 
 
 @stop

 @section('scripts')
   <script>
   var typeid =$('#type_id').val(); 
   var id =$('#id').val(); 
   var url = '{{ route("other-vendor.list",[":typeid",":id"]) }}';
   url = url.replace(':typeid', typeid);
   url = url.replace(':id', id);
            
    $(function () {
        $.fn.dataTable.ext.errMode = 'throw';
        try{
            table = $('#table-id').DataTable({
            processing: false,
            fixedHeader: false,
            serverSide: true,
            responsive: true,

            ajax: url,
                      
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
                
                {data: 'name', name: 'name'},
                {data: 'documentCount', name: 'documentCount'},
                {data: 'updated_at', name: 'updated_at'},
                {
                    data: null,
                    sortable: false,
                    render: function (row) {
                       
                    
                        actions = '';
                        @canany(['add_other_document'])    
                             actions = '<a title="Add" href="{{route("add-client.document", ["typeid" => "" , "id" => ""])}}/'+ typeid +'/'+ row.categ_id  +'"  class="fa fa-plus size-adjust-icon" id="add_document"></a>';
                        @endcan
                             actions +='<a title="View"  href="{{ route("documents.view-document",["typeid" => "", "id" => ""])}}/'+ typeid +'/'+ row.categ_id +'" class="view btn fa fa-eye id="view_document"></a>';
                      return actions;
                       
                           }      
                           }
                ]
        });

         /* Page redirection to add document page - Start */
         $('#add-new-button').on('click',  function(e){
            window.location='{{route("add-client.document", ["typeid" => "" , "id" => ""])}}/'+ typeid +'/'+ id  +'';
            });
        /* Page redirection to add document page - End */

    } catch(e){
            console.log(e.stack);
        }
    });
    
    </script>
    <style type="text/css">
        #add-new-button{ margin-top: 10px;margin-right:5px;}
    </style>
    @stop
                