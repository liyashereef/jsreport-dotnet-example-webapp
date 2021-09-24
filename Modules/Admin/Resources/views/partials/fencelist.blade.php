<table class="table table-bordered" id="customerfence-table" style="width:100%">
<thead>
                <tr>
                    <th>#</th>
                    <th>Location Name</th>
                    <th>Address</th>
                    <th>Visit per Shift/Person</th>
                    <th>Radius</th>
                    <th>Contractual Visit</th>
                    <th>Active</th>
                    <th></th>
                    
                </tr>
</thead>
<tbody>
        @php
         $i=0;   
        @endphp
@foreach ($fencelist as $fence)
@php
         $i++;   
        @endphp
 <tr attr-id="{{$fence->id}}" id="fencerow-{{$fence->id}}" style="height:20px;" attr-fencename="{{$fence->title}}" attr-fencedesc="{{$fence->address}}" attr-latitiude="{{$fence->geo_lat}}" attr-longitude="{{$fence->geo_lon}}" attr-visitcount="{{$fence->visit_count}}"  attr-contractractual="{{$fence->contractual_visit}}"  attr-radiusfence="{{$fence->geo_rad}}">
 <td>{{$i}}</td>
                    <td>{{$fence->title}}</td>
                    <td>{{$fence->address}}</td>
                    <td>{{$fence->visit_count}}</td>
                    <td>{{$fence->geo_rad}}</td>
                    <td>{{$fence->contractual_visit ?? ''}}</td>
                    <td>@if($fence->active==1)
            <a class="disablefences fa fa-check"  attr-fencerowid="{{$fence->id}}" attr-process="false" id="disablefence-{{ $loop->iteration }}">               
            </a>
            @elseif($fence->active==0)
            <a class="disablefences fa fa-ban"  attr-fencerowid="{{$fence->id}}" attr-process="true" id="disablefence-{{ $loop->iteration }}">               
            </a>
            @endif</td>
            <td>
                    <a class="editfences edit fa fa-pencil" attr-fencerowid="{{$fence->id}}" id="editfence-{{ $loop->iteration }}">
            
        </a>
        <a class="savedfences delete fa fa-trash-o" attr-fencerowid="{{$fence->id}}" id="removefence-{{ $loop->iteration }}"  >
            
        </a>
            </td>
                    
                </tr>

@endforeach
</tbody>
</table>