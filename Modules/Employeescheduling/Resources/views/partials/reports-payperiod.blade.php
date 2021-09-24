<div class="col-md-12" id="contenttable" style="max-width:100% !important;overflow-y:hidden;display: none">
    <table id="genreport"  class="table table-bordered " style="">
    <thead >
        <th>Project Number</th>
        <th>Customer Name</th>
        <th>Area Manager</th>
        <th>Supervisor</th>
        @foreach ($payperiods as $payperiod)
        <th class="theadclass" style="text-align: center;border:solid 1px #000 !important">
            <span class="rotatespan">{{$payperiod["short_name"]}}</span>
        </th>   
        @endforeach
    </thead>

<tbody>
@foreach ($customers as $customer)

            <td class="customerclass" >{{$customer["project_number"]}}</td>
            <td class="customerclass" >{{substr($customer["client_name"],0,40)}}</td>
            <td class="customerclass" id="{{$customer["id"]}}-areamanager">
                <p class="namespan" title="areamanager">{{$customer["areamanager"]}}</p>
            </td>
            <td class="customerclass" id="{{$customer["id"]}}-supervisor">
                <p class="namespan"  title="supervisor">{{$customer["supervisor"]}}</p>
            </td>
         
    @foreach ($payperiods as $payperiod)
    <td class="tbodyclass {{$customer["id"]}}-{{$payperiod["id"]}}" id="{{$customer["id"]}}-{{$payperiod["id"]}}" style="border:solid 1px #000 !important">
        <span class="rotatespan"></span>
    </td>   
    @endforeach  
    </tr>
@endforeach
</tbody>
</table>
</div>