@extends('layouts.app')
@section('content')
<div class="table_title">
    <h4>Uniform Shipment</h4>
</div>
<table class="table table-bordered" id="uniform-shippment-table">
    <thead>
        <tr>
            <th>Candidate Name</th>
             <th>Customer</th>
            <th>Shipping Address</th>
            <th>Kit</th>
            <th>Status</th>
            <th>Status Date</th>
            <th>Status Time</th>
             <th> Updated By</th>
            <th>Action</th>
        </tr>
    </thead>
</table>

<div class="modal fade" id="changeStatus" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        {{ Form::open(array('url'=>'#','id'=>'candidate-uniform-status-form','class'=>'form-horizontal', 'method'=> 'POST')) }} {{csrf_field()}}
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Update Shipping Status</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="statusId">
            <div class="form-group row" id="uniformstatus">
                <label for="status" class="col-sm-4 col-form-label">Status</label>
                <div class="col-sm-8" id="selectStatus">

                </div>
                 <small class="help-block"></small>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" onclick="saveStatus()">Save</button>
        </div>
         {{ Form::close() }}
      </div>
    </div>
  </div>


  <div class="modal fade" id="uniformUpdate" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      {{ Form::open(array('url'=>'#','id'=>'candidate-uniform-form','class'=>'form-horizontal', 'method'=> 'POST')) }} {{csrf_field()}}
            {{ Form::hidden('candidate_id', null) }}
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Update Uniform Measurement</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="form-group row">
                <label for="status" class="col-sm-4 col-form-label">Gender</label>
                <div class="col-sm-8" id="gender">
               <select type="select" name="gender" class="form-control" readonly style="pointer-events: none">
                <option value="1">Male</option>
                <option value="2">Female</option>
                  <option value="3">Other</option>
               </select>
                </div>
            </div>
            @foreach($measuringPoints as $each_measuringPoint)
             <div class="form-group row" id="{{  $each_measuringPoint['name']}}">
                <label for="status" class="col-sm-4 col-form-label">{{  $each_measuringPoint['name']}}</label>
                <div class="col-sm-4" >
               <select type="select" name="uniformcontrol-{{ $each_measuringPoint['id'] }}" class="form-control">
                 <?php for ($i = 5; $i <= 70; $i++) : ?>
                  <option value="{{ $i }}">{{ $i }}</option>
                <?php endfor; ?>
               </select>
                </div>
                <div class="col-sm-4" id="neck">
               <select type="select" name="point_decimal_value_{{$each_measuringPoint['id'] }}" class="form-control">
                  @foreach(config('globals.uniform_measurement_decimal_points') as $key=>$decimal_point)
                   <option value="{{ $key }}">{{ $decimal_point }}</option>
                  @endforeach
               </select>
                </div>
            </div>
            @endforeach
            <div class="form-group row">
                <label for="status" class="col-sm-4 col-form-label"></label>
                <div class="col-sm-8" id="gender">
               <label for="status" class="col-sm-4 col-form-label">Same as Address</label>
                {{ Form::checkbox('same_address_check',null,null, array('id'=>'check_same_address')) }}<br>
                </div>
            </div>

             <div class="form-group row">
                <label for="status" class="col-sm-4 col-form-label"> Shipping Address</label>
                <div class="col-sm-8" id="gender">
                <textarea class="form-control" name="shipping_address"  rows="3" maxlength="1000"></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
         {{ Form::submit('Save', array('class'=>'button btn submit','id'=>'mdl_save_change'))}}
         {{ Form::button('Cancel', array('class'=>'btn cancel','data-dismiss'=>"modal", 'aria-hidden'=>true))}}
        </div>
         {{ Form::close() }}
      </div>
    </div>
  </div>

@stop
@section('scripts')
<script>
    let shippingStatus = <?php echo json_encode(config('globals.shipping_status')); ?>;
    let candidate = <?php echo json_encode($candidate); ?>;
    $(function() {
        var table = $('#uniform-shippment-table').DataTable({
            processing: false,
            fixedHeader: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('recruitment.candidate-uniform-shippment-detail.list') }}",
            dom: 'Blfrtip',
            buttons: [{
                        extend: 'pdfHtml5',
                        //text: ' ',
                        pageSize: 'A2',
                        //className: 'btn btn-primary fa fa-file-pdf-o',
                        exportOptions: {
                            // columns: [ ':visible'],
                            columns: 'th:not(:last-child)'
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        //text: ' ',
                        //className: 'btn btn-primary fa fa-file-excel-o',
                        exportOptions: {
                            // columns: [ ':visible'],
                            columns: 'th:not(:last-child)'
                        }
                    },
                    {
                        extend: 'print',
                        //text: ' ',
                        pageSize: 'A2',
                        //className: 'btn btn-primary fa fa-print',
                        exportOptions: {
                            // columns: [ ':visible'],
                            columns: 'th:not(:last-child)'
                        }
                    }
                ],
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [
                {
                    data: 'candidate_name',
                    name: 'candidate_name'
                },
                  {
                    data: 'customer_name',
                    name: 'customer_name'
                },
                {
                    data: 'shippment_address',
                    name: 'shippment_address'
                },
                {
                    data:null,
                    name:'kit_name',
                    render: function (row) {
                        return '<a href="#" class="popoverButton" data-placement="right" data-id=' + row.kit_id + ' data-candidate-id=' + row.candidate_id + '>'+row.kit_name+'</a>';

                    }
                },
              {data: 'status.[ <br><br>].status', name: 'shippment_status'},
            {data: 'status.[ <br><br>].date', name: 'status.0.date'},
            {data: 'status.[ <br><br>].time', name: 'status.0.time'},
           {data: 'status.[ <br><br>].user_name', name: 'status.0.user_name'},
                // {
                //     data: null,
                //     name: 'shippment_status',
                //     render: function(data) {
                //         return data.shippment_status!=0?shippingStatus[data.shippment_status]:'--';
                //     }
                // },
                // {
                //     data:null,
                //     name:'status_date_time',
                //     render: function(data) {
                //         return moment(data.status_date_time).format("DD-MMM-YYYY")
                //     }
                // },
                // {
                //     data: null,
                //     name:'status_date_time',
                //     render: function(data) {
                //         return moment(data.status_date_time).format('h:m A');
                //     }
                // },
                {
                    data: null,
                    orderable:false,
                    render: function(data) {
                        actions = '';
                        actions += '<a title="Change Status" class="fa fa-podcast fa-lg link-ico" onclick="changeStatus('+data.id+','+ data.shippment_status+')"></a>'
                          actions += '<a title="Edit" class="fa fa-edit fa-lg link-ico" onclick="updateUniform('+data.candidate_id+')"></a>'
                        return actions;
                    }
                }
            ]
        });
       $('#uniform-shippment-table').on('click', '.popoverButton', function(e){
       e.preventDefault();
       var id = $(this).data('id');
       var candidate_id = $(this).data('candidate-id');
       var base_url = "{{route('recruitment.uniform.getKitDetails',[':id',':candidate_id'])}}";
       var url = base_url.replace(':id', id);
        var url = url.replace(':candidate_id', candidate_id);
        console.log(url)
       $.ajax({
           headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           },
           url:url,
           type: 'GET',
           success: function (data) {
              if(data.success){

               $('#uniform-shippment-table a[data-id="'+data.result.id+'"][data-candidate-id="'+data.result.candidate_id+'"]').popover({
                      "html": true,
                       trigger: 'focus',
                       placement: 'bottom',
                      "content": function () {
                        var result='';
                         if(data.result.result.length>0) {
                       result="<div class='table-wrapper-scroll-y my-custom-scrollbar'><table class='table'><tr><th>Item</th><th>Size</th><th>Quantity</th></tr>";
                      let quantity=0;
                      jQuery.each(data.result.result , function(index, value){
                         jQuery.each(value.kit.customer_uniform_kit_mappings , function(subindex, subvalue){
                             if(value.item_id==subvalue.item_id && value.kit_id==subvalue.kit_id)
                             {
                                quantity=subvalue.quantity;
                             }
                        });
                      result+= "<tr><td>" +value.item.item_name+"</td><td>" +value.size.size_name+"</td><td>" +quantity+"</td></tr>";
                       });
                       result+='</table></div>';
                       }
                        else{
                        result+='No Matching Kit for this measurement';
                       }
                      return result;
                    }
                    });
               $('#uniform-shippment-table  a[data-id="'+data.result.id+'"][data-candidate-id="'+data.result.candidate_id+'"]').popover('toggle');
              }
           },
           fail: function (response) {
               swal("Oops", "Something went wrong", "warning");
           },
           contentType: false,
           processData: false,
       });
   });

            $('#candidate-uniform-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            var formData = new FormData($('#candidate-uniform-form')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('recruitment.applyjob.storeUniform') }}",
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                       swal({
                                title: 'Success',
                                text: "Uniform measurement has been updated",
                                type: "success",
                                icon: "success",
                                button: "Ok",

                            }, function () {
                              $('#uniformUpdate').modal('hide');
                                table.ajax.reload();
                            });

                    } else {
                        alert(data.success);
                    }
                },
                fail: function (response) {
                    alert('here');
                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form);
                },
                contentType: false,
                processData: false,
            });
        });

             $('input[name="same_address_check"]').on("click",function(e){
               let candidateId=   $('input[name="candidate_id"]').val();
               let address =  candidate[candidateId];
            if($(this).is(":checked")){
                $('textarea[name="shipping_address"]').val(address)
                $('textarea[name="shipping_address"]').prop("readonly",true)
            }else{

                $('textarea[name="shipping_address"]').prop("readonly",false)
            }
        })
    });

    function changeStatus(id, status) {
       $('#candidate-uniform-status-form').find('#uniformstatus').removeClass('has-error').find('.help-block').text('');
        $('#changeStatus').modal();
        $('#selectStatus').empty();
        $('#statusId').attr('value', id);
        var select = '';
        select += '<select id="status" class="form-control">';
        shippingStatus.forEach((element,index) => {
          if(index==0)
          {
             select += '<option disabled="disabled" value="'+ index +'">'+ element +'</option>';
          }
          else{
              select += '<option value="'+ index +'">'+ element +'</option>';
        }    
        });
        select += '</select>';
        $('#selectStatus').append(select);
        $("#changeStatus #selectStatus option:eq("+status+")").attr('selected', 'selected');
    }


    function updateUniform(candidate_id)
    {

       var base_url = "{{route('recruitment.candidate-uniform-details',[':candidate_id'])}}";
       var url = base_url.replace(':candidate_id', candidate_id);
        console.log(url)
       $.ajax({
           headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           },
           url:url,
           type: 'GET',
           success: function (data) {
              if(data.success){
                console.log(data)
               $('#uniformUpdate select[name="gender"]').val(data.result.candidateDetails.gender);
               if(data.result.candidateDetails.gender==1 ||data.result.candidateDetails.gender==3 )
               {
               $('#uniformUpdate #Hip').hide();
               }
               else{
                 $('#uniformUpdate #Hip').show();
               }
               $('#uniformUpdate input[name="candidate_id"]').val(data.result.candidateDetails.id);
               $.each( data.result.uniformdetails, function( i, l ){

   $('#uniformUpdate select[name="uniformcontrol-'+i+'"]').val(data.result.uniformdetails[i].split('.')[0]);
   if(data.result.uniformdetails[i].split('.')[1]=='000')
   {
    decimal='00';
   }
   else
   {
      decimal=data.result.uniformdetails[i].split('.')[1];
   }
   $('#uniformUpdate select[name="point_decimal_value_'+i+'"]').val('0.'+decimal);
});
                $('#uniformUpdate textarea[name="shipping_address"]').val(data.result.address.shippment_address);
                 $('#uniformUpdate').modal();
              }
           },
           fail: function (response) {
               swal("Oops", "Something went wrong", "warning");
           },
           contentType: false,
           processData: false,
       });


    }


    function saveStatus() {
            $('#candidate-uniform-status-form').find('#uniformstatus').removeClass('has-error').find('.help-block').text('');
            var id = $('#changeStatus #statusId').val();
            var statusId = $('#selectStatus').find(":selected").val();
            if(statusId==0){
            $('#candidate-uniform-status-form').find('#uniformstatus').addClass('has-error').find('.help-block').text('Please choose any status');
            return false;
            }
            url = "{{ route('recruitment.candidate-uniform-shippment-detail.saveStatus') }}";
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    "id": id,
                    "shippment_status": statusId
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    $('#changeStatus').hide();
                    if (data.success) {
                        swal({
                                title: 'Success',
                                text: "Shipping status has been updated",
                                type: "success",
                                icon: "success",
                                button: "Ok",

                            }, function () {
                                window.location.reload();
                            });
                    } else {
                        swal("Warning", "Something went wrong", "warning");
                    }
                },
                fail: function (response) {
                    alert('here');
                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form);
                }
            });
    }
</script>
<style type="text/css">
.my-custom-scrollbar {
position: relative;
max-height: 200px;
overflow: auto;
}
.table-wrapper-scroll-y {
display: block;
}
</style>
@stop
