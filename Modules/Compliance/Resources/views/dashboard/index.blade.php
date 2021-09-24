@extends('layouts.app')
@section('content')

<div class="table_title">
  <h4>Compliance Module</h4>
</div>
<div class="row dashboard-row">
  {{-- <div class="col-sm-3 dashboard-box">
        <div class="progressbar-text">Compliance Module</div>
    </div> --}}
     <div class="col-sm-4 dashboard-box">
 <span class="chart">  {!! $policy_count_chart->render() !!}</span>
 </div>
  <div class="col-sm-4 dashboard-box">
 <span class="chart">  {!! $compliant_count_chart->render() !!}</span>
</div>
  <div class="col-sm-4 dashboard-box">
 <span class="chart">  {!! $average->render() !!}</span>
</div>
</div>
<div class="table-responsive">
  <table class="table table-bordered" id="policy-table">
    <thead>
      <tr>
        <th class="sorting" width="10%">Policy Id</th>
        <th class="sorting" width="10%">Policy Name</th>
        <th class="sorting" width="20%">Policy Description</th>
        <th class="sorting" width="10%">Category</th>
        <th class="sorting" width="10%">Status</th>
        <th class="sorting" width="10%">Date Completed</th>
        <th class="sorting" width="5%">Action</th>
      </tr>
    </thead>
  </table>
</div>
@stop
@section('scripts')
<style>

.chart
{
  display: inline-block;
}
.dashboard-row {
        background-color: #f26222;
        margin-bottom: 1%;
        margin-left: 0px;
        margin-right: 0px;
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19)
    }
     .dashboard-box {
        border-right: 1px solid;
        padding-bottom: 1%;
        text-align: center;
    }

</style>
<script>{!! Charts::assets() !!}</script>
<script>
  $(document).ready(function() {
    setTimeout(() => {
      var textId = [1,6,11];
      for (const i of textId) {
        $($("svg text")[i]).attr('y',130);
      }
    }, 5000);
    
    var table = $('#policy-table').DataTable({
      fixedHeader: true,
      processing: false,
      serverSide: true,
      responsive: true,
      ajax:" {{route('policyTable.list')}}",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      order: [
      [1, "desc"]
      ],
      lengthMenu: [
      [10, 25, 50, 100, 500, -1],
      [10, 25, 50, 100, 500, "All"]
      ],

      columns: [{
        data: 'reference_code',
        name: 'reference_code',

      },
      {
        data: 'policy_name',
        name: 'policy_name',
      },
      {
        data: 'policy_description',
        name: 'policy_description',
      },
      {
        data: 'compliance_policy_category',
        name: 'compliance_policy_category',
      },
      {
        data: 'status',
        name: 'status',
      },
      {
        data: 'updated_at',
        name: 'updated_at',
      },


      {
        data: null,

        sortable: false,
        render: function (o) {
           var actions = '';
          if(o.status=='Pending' && o.is_compliant == 1)
          {
            @can('view_assigned_compliance')
            actions +='<button type="submit" class="btn btn-primary" id="start" name="foo" value='+o.id+' onclick="getPolicyID(this.value,1)">Start</button>&nbsp;&nbsp;'
            @endcan
              chart_url = '{{route("policy.chart",[':id'])}}';chart_url = chart_url.replace(':id', o.id);
              @can('view_analytics')
                  actions += '<a  href="'+chart_url+'" class="edit fa fa-bar-chart fa-lg link-ico" value=' + o.id + '></a>'
              @endcan
          }
          else
          {
            var id=o.id
            var url = '{{route("policy.get",[':boolean',':id'])}}';
              url = url.replace(':boolean', 0);
              url = url.replace(':id', id);
            actions += '<a href="'+url+'" class="edit fa fa-podcast fa-lg link-ico" value=' + o.id + '></a>&nbsp;'
            chart_url = '{{route("policy.chart",[':id'])}}';chart_url = chart_url.replace(':id', id);
            @can('view_analytics')
              actions += '<a  href="'+chart_url+'" class="edit fa fa-bar-chart fa-lg link-ico" value=' + o.id + '></a>'
            @endcan
        }
          return actions;
        },
      }
      ]
    });
  });
  function getPolicyID(id,boolean)
  {
   var url ='{{route("policy.get",[':boolean',':id'])}}';
   url = url.replace(':boolean', boolean);
   url = url.replace(':id', id);

    window.location.href = url;
  }



</script>
@stop
