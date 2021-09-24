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

@include('partials.widgets.widget-job-requisitions')
@include('partials.widgets.widget-position-by-region')
@include('partials.widgets.widget-highest-turnover')
@include('partials.widgets.widget-position-by-reasons')
@include('partials.widgets.widget-wage-by-region')
@include('partials.widgets.widget-candidates')
@include('partials.widgets.widget-candidate-resident-status')
@include('partials.widgets.widget-candidate-military-experience')
@include('partials.widgets.widget-guard-drivers-license')
@include('partials.widgets.widget-candidates-regions')
@include('partials.widgets.widget-candidates-certificates')
@include('partials.widgets.widget-access-to-public-transport')
@include('partials.widgets.widget-limited-transportation')
@include('partials.widgets.widget-candidates-experiences')
@include('partials.widgets.widget-level-of-language-fluency-english')
@include('partials.widgets.widget-level-of-language-fluency-french')
@include('partials.widgets.widget-candidates-skills-computer')
@include('partials.widgets.widget-candidates-skills-soft')
@include('partials.widgets.widget-employment-entities')
@include('partials.widgets.widget-fired-vs-convicted-candidates')
@include('partials.widgets.widget-candidates-experiences-regions')
@include('partials.widgets.widget-wage-by-industry-sector')
@include('partials.widgets.widget-planned-ojt')
@include('partials.widgets.widget-wage-expectations-by-region')
@include('partials.widgets.widget-candidates-by-career-interest-in-cgl')
@include('partials.widgets.widget-candidates-by-average-score')
@include('partials.widgets.widget-loading-documents')
@include('partials.widgets.widget-average-cycle-time')
@include('partials.widgets.widget-wage-expectations-by-position')
@include('partials.widgets.widget-wage-by-competitor')
