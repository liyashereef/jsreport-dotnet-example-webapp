<style>
table {
    margin-bottom: 0px !important;
}
</style>
<script>
    const widgets = {
        define(property, value) {
            if (this.hasOwnProperty(property)) {
                console.error(`Widget: duplicate widget assignment [${property}] detected.`);
            }
            this[property] = value;
        },
        refresh(tag, filters = {}) {
            console.log(`Widget: refresh requested [${tag}] with ${Object.keys(filters).length} filters`);
            dashboard.loadSingleWidget(tag, filters);
        },
    }; //Global scope for widgets
</script>

<!-- Landing Page Dashboard Widgets -->
@include('partials.widgets.widget-demo')
@include('partials.widgets.widget-site-details')
@include('partials.widgets.widget-site-summary')
@include('partials.widgets.widget-trend-analysis')
@include('partials.widgets.widget-schedule-compliance')
@include('partials.widgets.widget-training-mandatory-permanent')
@include('partials.widgets.widget-training-mandatory-spares')
@include('partials.widgets.widget-response-kpis')
@include('partials.widgets.widget-site-metric')
@include('partials.widgets.widget-elavator-entrapment-responce')
@include('partials.widgets.widget-incident-response-compliance')
@include('partials.widgets.widget-shift-journal-summary')
@include('partials.widgets.widget-client-survey')
@include('partials.widgets.widget-client-survey-analytics')
@include('partials.widgets.widget-timesheet-reconciliation')
@include('partials.widgets.widget-motion-sensor')
@include('partials.widgets.widget-qr-patrol')
@include('partials.widgets.widget-scheduling')
@include('partials.widgets.widget-kpi-analytics')

<!-- Recruitment Analytics Dashboard Widgets -->
@include('hranalytics::dashboard.partials.widgets.widget-job-requisitions')
@include('hranalytics::dashboard.partials.widgets.widget-position-by-region')
@include('hranalytics::dashboard.partials.widgets.widget-highest-turnover')
@include('hranalytics::dashboard.partials.widgets.widget-position-by-reasons')
@include('hranalytics::dashboard.partials.widgets.widget-wage-by-region')
@include('hranalytics::dashboard.partials.widgets.widget-candidates')
@include('hranalytics::dashboard.partials.widgets.widget-candidate-resident-status')
@include('hranalytics::dashboard.partials.widgets.widget-candidate-military-experience')
@include('hranalytics::dashboard.partials.widgets.widget-guard-drivers-license')
@include('hranalytics::dashboard.partials.widgets.widget-candidates-regions')
@include('hranalytics::dashboard.partials.widgets.widget-candidates-certificates')
@include('hranalytics::dashboard.partials.widgets.widget-access-to-public-transport')
@include('hranalytics::dashboard.partials.widgets.widget-limited-transportation')
@include('hranalytics::dashboard.partials.widgets.widget-candidates-experiences')
@include('hranalytics::dashboard.partials.widgets.widget-level-of-language-fluency-english')
@include('hranalytics::dashboard.partials.widgets.widget-level-of-language-fluency-french')
@include('hranalytics::dashboard.partials.widgets.widget-candidates-skills-computer')
@include('hranalytics::dashboard.partials.widgets.widget-candidates-skills-soft')
@include('hranalytics::dashboard.partials.widgets.widget-employment-entities')
@include('hranalytics::dashboard.partials.widgets.widget-fired-vs-convicted-candidates')
@include('hranalytics::dashboard.partials.widgets.widget-candidates-experiences-regions')
@include('hranalytics::dashboard.partials.widgets.widget-wage-by-industry-sector')
@include('hranalytics::dashboard.partials.widgets.widget-planned-ojt')
@include('hranalytics::dashboard.partials.widgets.widget-wage-expectations-by-region')
@include('hranalytics::dashboard.partials.widgets.widget-candidates-by-career-interest-in-cgl')
@include('hranalytics::dashboard.partials.widgets.widget-candidates-by-average-score')
@include('hranalytics::dashboard.partials.widgets.widget-loading-documents')
@include('hranalytics::dashboard.partials.widgets.widget-average-cycle-time')
@include('hranalytics::dashboard.partials.widgets.widget-wage-expectations-by-position')
@include('hranalytics::dashboard.partials.widgets.widget-wage-by-competitor')
