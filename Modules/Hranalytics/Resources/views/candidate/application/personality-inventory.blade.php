<div class="container subTabs">
    <div class="row">
        <ul class="breadcrumb breadcrumb-arrow nav nav-tabs width-100 subTabMarginBottom" role="tablist">
            <li class="nav-item complete">
                <a class="nav-link active" data-toggle="tab" href="#personality-inventory-questions">
                    <span> Questions
                    </span>
                </a>
            </li>
            <li class="nav-item complete">
                <a class="nav-link" data-toggle="tab" href="#scoring">
                    <span> Scoring
                    </span>
                </a>
            </li>
            <li class="nav-item complete">
                <a class="nav-link" data-toggle="tab" href="#personal_analysis">
                    <span> Personal Analysis
                    </span>
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div id="personality-inventory-questions" class="container-fluid tab-pane active">
                <br>
                <div class="row">
	                <section class="candidate full-width">
	                    @include('hranalytics::candidate.application.personality-inventory-partials.questions')
	                </section>
            	</div>
            </div>
            <div id="scoring" class="container-fluid tab-pane fade">
                <br>
                <div class="row">
                    <section class="candidate full-width">
                        @include('hranalytics::candidate.application.personality-inventory-partials.scoring')
                    </section>
                </div>
            </div>
            <div id="personal_analysis" class="container-fluid tab-pane fade">
                <br>
                <div class="row">
                    <section class="candidate full-width">
                        @include('hranalytics::candidate.application.personality-inventory-partials.personal-analysis')
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>
