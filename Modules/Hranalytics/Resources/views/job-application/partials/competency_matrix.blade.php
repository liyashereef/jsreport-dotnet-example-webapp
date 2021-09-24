@foreach($lookups['competency_matrix'] as $competency_matrix_category)
<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">{{$competency_matrix_category['competency_matrix_category']}}</label>
<div class="form-group row competency-title">
    <div class="col-sm-2 col-form-label competency-heading">Competency</div>
    <div class="col-sm-4 col-form-label competency-heading">Definition</div>
    <div class="col-sm-4 col-form-label competency-heading">Behaviors</div>
    <div class="col-sm-2 col-form-label competency-heading">Rating</div>
</div>
@foreach($competency_matrix_category['competency'] as $competency)
<div class="form-group row" >
    <div class="col-sm-2 col-form-label">{{$competency['competency']}}</div>
    <div class="col-sm-4 col-form-label">{!! nl2br(e($competency['definition'])) !!}</div>
    <div class="col-sm-4 col-form-label">{!! nl2br(e($competency['behavior'])) !!}</div>
    <div class="col-sm-2">
        {{-- Form::select('industry_sector_lookup_id',[null=>'Select']+$lookups['industrySectorLookup'], old('industry_sector_lookup_id'),array('class' => 'form-control')) --}}

        {{ Form::select('competency_matrix_rating',[null=>'Please select']+$lookups['competency_rating'],
            old('competency_matrix_rating'),
            array('class' => 'form-control competency-rating','required' => 'required', 'data-competency' => $competency['id'])) }}
    </div>
</div>
@endforeach
@endforeach

<script>
    $(".competency-rating").change(saveRating)
    var competencyRatingArr = [];
    function saveRating(){
        let competencyRatingObj = {};

        competencyRatingObj.competency = $(this).data('competency');
        competencyRatingObj.rating = $(this).val();

        var found = competencyRatingArr.some(function (el) {
            if(el.competency === competencyRatingObj.competency){
                el.rating = competencyRatingObj.rating;
            }
            return el.competency === competencyRatingObj.competency;
        });
        if (!found) {
            competencyRatingArr.push(competencyRatingObj);
        }
    }
</script>
