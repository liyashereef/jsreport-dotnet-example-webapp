@extends('adminlte::page')
@section('title', 'Email Template Allocation')
@section('content_header')
<h1>Email Template Allocation</h1>
@stop
@section('content')

<div class="row">
    <div class="col-md-12" id="allocation-container">
        <div class="form-group row">
            <div class="col-md-2">
                Choose Type:
            </div>
            <div class="col-md-4">
                <select name="type"  id="filter_id" class="form-control">
                    <option value="" selected>Select All</option>
                    @foreach($type as $key=>$data)
                    <option value={{$key}}>{{$data}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <div class="add-new" onclick="window.location='{{ route("email-template-allocation") }}'"  data-title="Add New Customer">Add <span class="add-new-label">New</span></div>
            </div>
        </div>
    </div>
</div>
<div class="row block-for-customers">
    <div class="col-md-12" id="allocation-container">
        <div class="form-group row">
            <div class="col-md-2">
                Choose Customer 
            </div>
            <div class="col-md-4">
                <select name="customer_id" id="customer_id" class="form-control">
                    <option value=0 selected>Select All</option>
                    @foreach($customer_list as $id=>$data)
                    <option value={{$data->id}}>{{$data->client_name}}</option>
                    @endforeach
                </select>
            </div>
           
            
        </div> 
    </div>
</div>
<table class="table table-bordered" id="allocation-table">
    <thead>
        <tr>

            <th>Client</th>
            <th>Type</th>
            <th>Users</th>
            <th>Edit</th> 
        </tr>
    </thead>
</table>

@stop
@section('js')
<script>
    //SAVE PREVIOUS STATE ON PRESSING BACK BUTTON
   window.addEventListener( "pageshow", function ( event ) {
  var historyTraversal = event.persisted || 
                         ( typeof window.performance != "undefined" && 
                              window.performance.navigation.type === 2 );
  if ( historyTraversal ) {
    // Handle page restore.
    //window.location.reload();
    $(document).ready(function () {
     var typeid=$("select[name='type']").find(":selected").val();
      $("select[name='type']").val(typeid).trigger('change');
});
      
  }
});
    //SAVE PREVIOUS STATE ON PRESSING BACK BUTTON


    $(function () {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        $('#customer_id').select2();//Added Select2 to project listing
        $.fn.dataTable.ext.errMode = 'throw';
        try{
            var base_url = "{{ route('email-template-allocation.list') }}";
            var table = $('#allocation-table').DataTable({
                bProcessing: false,
                processing: true,
                serverSide: true,
                fixedHeader: true,
                bFilter: false,
                dom: 'lfrtBip',
                buttons: [
                {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5],
                        stripNewlines: false
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    }
                },
                {
                    text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o',
                    action: function (e, dt, node, conf) {
                        emailContent(table, 'Customer Allocation');
                    }
                }
                ],

                ajax:{
                    url: base_url, // Change this URL to where your json data comes from
                    type: "GET", // This is the default value, could also be POST, or anything you want.

                    "error": function (xhr, textStatus, thrownError) {
                        if(xhr.status === 401){
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                select: {
                    style:    'os',
                    selector: 'td:first-child'
                },
                lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                 columns: [
                    {data: 'client_name', name: 'customer.client_name',defaultContent: 'No Customer'},
                    {data: 'display_name', name: 'type.display_name'},
                    {data: 'user_name.[, ]', name: 'user_name.[0]',   defaultContent:'--',
                    orderable:false},
                    {
                    data: null,
                    render: function (o){
                     if( o.client_id!=null)
                       var url = "{{route('email-template-allocation',[':type_id',':customer_id'])}}";
                   else
                       var url = "{{route('email-template-allocation',[':type_id'])}}";
                   url = url.replace(':type_id', o.type_id);
                   url = url.replace(':customer_id', o.client_id);
                   return  '<a href="'+ url +'" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>';
               },
           }
                   
                ]
                
       });
        } catch(e){
            console.log(e.stack);
        }







        $('#customer_id').on('change', function () 
        {
            $type=($('select[name=type]').val()!=="")?$('select[name=type]').val():null;   
            $customer_id = $('#customer_id').val();
            if( $('#customer_id').val() != 0 && $('select[name=type]').val()!==""){
                $customer_id = $('#customer_id').val();
                table.ajax.url('datalist/'+$type+'/'+$customer_id).load();
            }
            else if( $('#customer_id').val() == 0){
             table.ajax.url('datalist/'+$type).load();
         }
         else{
             table.ajax.url('datalist/'+$type+'/'+$customer_id).load();
         }
     });

        $('select[name=type]').on('change', function () {
          showHideCustomerBlock($(this).val());
          $type = ($(this).val()!=="")?$(this).val():null;
          $customer_id = $('#customer_id').val();
          if($customer_id == 0){
            table.ajax.url('datalist/'+$type).load();
        }else{
                //table.ajax.url('datalist').load();
                table.ajax.url('datalist/'+$type+'/'+$customer_id).load();
            }


            
        });
        /*Filters for customer - End*/


    });

function showHideCustomerBlock(type_id){
  var customer_based = JSON.parse('{!! json_encode($customer_based) !!}');
  if(customer_based[(type_id)]==1 || customer_based[(type_id)]==null)
  {
    $('.block-for-customers').show();
}
else{
   $('.block-for-customers').hide();
   $("select[name='customer_id']").val(0).trigger('change');

}
}

</script>
@stop
