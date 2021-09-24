@extends('layouts.app')
@section('css')
<style>
    th{
        color: #fff !important;
    }

    .activeblock{
        background: wheat !important;
        color: #000 !important;
    }
    .activeblock td{
        color: #000 !important
    }
</style>
    
@endsection
@section('content')
@csrf
@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif

    <div class="row table_title">
        <div class="col-md-11"><h4> Bonus Programs </h4></div>
        <div class="col-md-1"><button type="button"  style="float: right" class="addnew btn btn-primary">+</button></div>

        
    </div>

    <div class="row ">
        <div class="col-md-12">

        
        <table class="table table-bordered programTable">
            <thead>
                <th>
                    #
                </th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Bonus Amount</th>
                <th>Wage Cap</th>
                <th>Shift Cap</th>
                <th>Notice Cap</th>
                <th>Process</th>
                <th>Status</th>
                <th>Actions</th>
            </thead>
            <tbody>
                @foreach ($settingsData as $item)
                    <tr 
                    @if ($item->active===1)
                        class="activeblock"
                    @else
                        
                    @endif
                    >
                    <td>
                        {{$loop->iteration}}
                    </td>
                    <td>
                        {{date("d M Y",strtotime($item->start_date))}}
                    </td>
                    <td>
                        {{date("d M Y",strtotime($item->end_date))}}
                    </td>
                    <td>
                        {{$item->bonus_amount}}
                    </td>                    
                    <td>{{$item->wagecap_percentage>0?$item->wagecap_percentage:0}}%
                    </td>
                    <td>
                        {{$item->shiftcap_percentage>0?$item->shiftcap_percentage:0}}%
                    </td>                                            
                    <td>
                        {{$item->noticecap_percentage>0?$item->noticecap_percentage:0}}%
                    </td>
                    <td>
                        @if ($item->active==0  )
                        @if ($item->start_date<=date("Y-m-d") && $item->end_date>=date("Y-m-d"))
                            <button class="btn btn-primary process" type="button" 
                            attr-process="activate" attr-id="{{$item->id}}">Activate</button>
                        @endif

                        @elseif($item->active==3)
                        @if ($item->start_date<=date("Y-m-d") && $item->end_date>=date("Y-m-d"))
                        <button class="btn btn-primary process" type="button" attr-process="activate" attr-id="{{$item->id}}">Activate</button>

                        @endif

                        @elseif($item->active==1  )
                            <button class="btn btn-danger process" type="button" attr-process="hold" attr-id="{{$item->id}}">Hold</button>

                        @endif
                    </td>
                    <td>
                        @if ($item->active==0)
                            Pending
                            @elseif ($item->active==1)
                            Active
                            @elseif ($item->active==2)
                            Completed
                            @elseif ($item->active==3)
                            On Hold
                        @endif
                    </td>
                    <td>
                       @if ($item->end_date>date("Y-m-d"))
                       <a href="{{route("stc.bonus",["id"=>$item->id])}}"><i class="fa fa-eye"></i></a>

                            <a href="{{route("stc.bonussettings",["id"=>$item->id])}}"><i class="fa fa-edit"></i></a>

                        @else
                            <a href="{{route("stc.bonus",["id"=>$item->id])}}"><i class="fa fa-eye"></i></a>

                       @endif
                       @if ($item->start_date>date("Y-m-d"))
                          <a class="process" href="#"  attr-process="remove" attr-id="{{$item->id}}"><i class="fa fa-trash"></i></a>
                       @endif
                        
                    </td>                </tr>
                @endforeach
                
            </tbody>
        </table></div>
    </div>

@endsection

@section('scripts')
<script>
    $(".programTable").DataTable();
    $(document).on("click",".addnew",function(e){
        e.preventDefault();
        location.href="{{route("stc.bonussettings")}}"
    })

    $(document).on("click",".process",function(e){
        e.preventDefault();
        let id=$(this).attr("attr-id");
        let process=$(this).attr("attr-process");
        var url = "{{route('stc.processbonusprogram')}}";
        swal({
                title: "Are you sure?",
                text: "Proceed?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Ok",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            },
            function () {
                $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: url,
                        type: "post",
                        data:{"id":id,"process":process},
                        success: function (response) {
                            let data=jQuery.parseJSON(response)
                            if (data.success) {
                            swal({
                                title: "Success",
                                text: data.message,
                                type: "success"
                            },
                            function() {
                                // $('#id').val(data.id);
                                location.reload();
                            }
                        );
                            
                            }else{
                                swal({
                                title: "Warning",
                                text: data.message,
                                type: "warning"
                            },
                            function() {
                                // $('#id').val(data.id);
                                location.reload();
                            }
                        );
                            }
                        }
                    });
            });
        
    })
</script>
@endsection
