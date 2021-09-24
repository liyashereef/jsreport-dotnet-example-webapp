<table class="table table-bordered" id="allocationtable" style="width: 100% !important;padding:5px">
    <thead>
        <th>#</th>
        <th>Facility</th>
        <th>Service</th>
        <th>Action</th>
    </thead>
    <tbody>
        @foreach ($facilities as $item)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td>{{$item->facility}}</td>
                <td></td>
                <td>
                    
                    @if (count($item->facilityservices)<1)
                        Add Service against facility
                        @if ($item->facilityserviceuserallocation->count()<1)
                            {{-- <button attr-add="addservice" attr-id="{{$item->id}}" id="add-{{$item->id}}" class="btn btn-primary adduseralloc">Add</button>
                            <button attr-add="removeservice" attr-id="{{$item->id}}" id="rem-{{$item->id}}" class="btn btn-danger adduseralloc" style="display: none">Remove</button>  --}}
                        @else
                            {{-- <button attr-add="addservice" attr-id="{{$item->id}}" id="add-{{$item->id}}" class="btn btn-primary adduseralloc" style="display: none">Add</button>
                            <button attr-add="removeservice" attr-id="{{$item->id}}" id="rem-{{$item->id}}" class="btn btn-danger adduseralloc" >Remove</button>  --}}
                            
                        @endif
                    @endif
                    
                </td>
            </tr>
            @if (isset($item->facilityservices))
                @foreach ($item->facilityservices as $service)
                    <tr style="background: #F17437">
                        <td style="color: #F17437"></td>
                        <td style="color: #fff">{{$service->getFacility->facility}}</td>
                        <td style="color: #fff">{{$service->service}}</td>
                        <td style="color: #fff">
                            
                            @if ($service->facilityserviceuserallocation->count()<1)
                                <button attr-add="addservice" attr-id="{{$service->id}}" id="add-{{$service->id}}" class="btn btn-primary adduseralloc">Add</button> 
                                <button attr-add="removeservice" attr-id="{{$service->id}}" id="rem-{{$service->id}}" class="btn btn-danger adduseralloc" style="display: none">Remove</button>
                            @else
                                <button attr-add="addservice" attr-id="{{$service->id}}" id="add-{{$service->id}}" class="btn btn-primary adduseralloc" style="display: none">Add</button>
                                <button attr-add="removeservice" attr-id="{{$service->id}}" id="rem-{{$service->id}}" class="btn btn-danger adduseralloc">Remove</button>
                                
                            @endif
                            
                            
                        </td>
                    </tr>
                @endforeach
                
            @endif
        @endforeach
        
    </tbody>
</table>
<p style="text-align: right;margin-top:20px">
    <button  class="btn btn-primary donealloc">Done</button>
</p>