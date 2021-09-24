@extends('layouts.app')

@section('content')
<div class="table_title">
    <h4> Sent Statements </h4>
</div>
<div class="row">
    <nav class="col-lg-9 col-md-9 col-sm-8">
        <div class="nav nav-tabs expense" id="nav-tab" role="tablist">
            <a class="nav-item nav-link expense" href="{{route('expense-statements.create')}}">Expense Statements</a>
            <a class="nav-item nav-link expense active" href="#">Sent Statements Log</a>
        </div>
    </nav>
</div>
<table class="table table-bordered" id="expense-send-log">
    <thead>
        <tr>
            <th class="sorting">Date</th>
            <th class="sorting">Sent To </th>
            <th class="sorting">Attachment</th>
        </tr>
    </thead>
</table>
@stop
@section('scripts')

<script>
    $(function(){

        $.fn.dataTable.ext.errMode = 'throw';
        try{
            table = $('#expense-send-log').DataTable({
            processing: false,
            fixedHeader: false,
            serverSide: true,
            responsive: true,
       
            ajax: "{{ route('expense-statetement-log.list') }}",
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
                }],
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [
                [0, "desc"]
            ],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            
            columns: [
                { 
                    data: 'created_at',
                    render: function(data, type, row){
                        if(type === "sort" || type === "type"){
                            return data;
                        }
                        return moment(data).format("MMMM DD, Y");
                    }
                },
                { 
                    data: "user.first_name", 
                    render: function ( data, type, row ) {
                        //console.log(row.user.employee.employee_no)
                        if ( type === 'display' || type === 'filter' ) {
                            return row.user.first_name+' '+((row.user.last_name)?(row.user.last_name):' ')+' '+((row.user.employee.employee_no)? '('+(row.user.employee.employee_no)+')':'');
                            } else {
                            return "--";
                            }
                    } 
                },
                
                {
                        data: null,
                        name: 'attachment',
                        defaultContent: "--",
                        sortable: false,
                        render: function (o) {
                    
                         if(o.attachment_id !== null){ 
                         var link ='';
                         var view_url = '{{ route("filedownload", [":id",":module"]) }}';
                         view_url = view_url.replace(':id', o.attachment.id);
                         view_url = view_url.replace(':module', 'expense-send-statements');
                         link = '<a title="Download" target="_blank" href="' + view_url + '">'+o.attachment.original_name+'</a><br>';

                         return link;

                         } else{
                            return '';
                         }
                        },
                }           
                              
            ]
        });
          
         } catch(e){
            console.log(e.stack);
        }
 

}); 


</script>

@stop