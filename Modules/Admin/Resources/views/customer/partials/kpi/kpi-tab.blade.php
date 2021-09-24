<div role="tabpanel" class="tab-pane" id="kpi-page">
    <ul class="nav nav-tabs">
        <li class="active"><a class="active" href="#kpi-allocation-tab">KPI Allocation</a></li>
        <li><a href="#kpi-tab-define-columns">Group Allocation</a></li>
    </ul>
    <div id="kpi-tab-content" class="tab-content">

        <div class="tab-pane fade active in" id="kpi-allocation-tab">
            @include('admin::customer.partials.kpi.allocation-tab')
        </div>

        <div class="tab-pane fade in" id="kpi-tab-define-columns">
            @include('admin::customer.partials.kpi.define-columns')
        </div>

    </div>
</div>
