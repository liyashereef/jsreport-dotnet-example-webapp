<div class="container">
    <div class="row">
        <div>
            @php
                $category_array = [];
            @endphp

            @foreach($candidateJob->candidate->competency_matrix as $competency_matrix)
                @php $category_name = $competency_matrix->competency_matrix->category->category_name @endphp

                @if(!in_array($category_name,$category_array))
                    @php
                        array_push($category_array, $category_name)
                    @endphp
                    <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">{{$competency_matrix->competency_matrix->category->category_name}}</label>
                    <div class="form-group row" >
                        <div class="col-sm-2 col-form-label competency-heading">Competency</div>
                        <div class="col-sm-4 col-form-label competency-heading">Definition</div>
                        <div class="col-sm-4 col-form-label competency-heading">Behaviors</div>
                        <div class="col-sm-2 col-form-label competency-heading">Rating</div>
                    </div>
                @endif

                <div class="form-group row" >
                    <div class="col-sm-2 col-form-label">{{$competency_matrix->competency_matrix->competency}}</div>
                    <div class="col-sm-4 col-form-label">{!! nl2br(e($competency_matrix->competency_matrix->definition)) !!}</div>
                    <div class="col-sm-4 col-form-label">{!! nl2br(e($competency_matrix->competency_matrix->behavior)) !!}</div>
                    <div class="col-sm-2 col-form-label">{{$competency_matrix->competency_matrix_rating->rating }}</div>
                    <!--<div class="col-sm-2">
                        {{-- Form::select('competency_matrix_rating',[null=>'Please select']+$lookups['competency_rating'],
                            old('competency_matrix_rating'),
                            array('class' => 'form-control competency-rating','required' => 'required', 'data-competency' => $competency['id'])) --}}
                    </div>-->
                </div>
            @endforeach
        </div>
    </div>
</div>
