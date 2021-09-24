@foreach ($candidateJob->candidate->other_languages as $otherlanguage)
        <label class="col-sm-11 orange float-left">{{ $otherlanguage->language_lookup->language }}</label>
        <div class="clearfix"></div>

        <div class="form-group row flex-nowarp">
            <div class="col-sm-2 float-left">
                <label class="pdf-label-1" style="font-size: 12px;">Speaking/ Oral comprehension</label>
            </div>
            <div class="form-check form-check-inline col-sm-2 float-left checkbox-alignnormal text-center">
                <label class="form-check-label padding-left-clear">
                    <img src="{{ ($otherlanguage->speaking=='A - Limited - I am just learning the language.')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                    A
                </label>
            </div>
            <div class="form-check form-check-inline col-sm-2 float-left checkbox-alignnormal text-center">
                <label class="form-check-label padding-left-clear">
                    <img src="{{ ($otherlanguage->speaking=='B - Functional - this is my second language but I can get by.')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                    B
                </label>
            </div>
            <div class="form-check form-check-inline col-sm-2 float-left checkbox-alignnormal text-center">
                <label class="form-check-label padding-left-clear">
                    <img src="{{ ($otherlanguage->speaking=='C - Fluent - this is my native language.')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                    C
                </label>
            </div>
            <div class="form-check form-check-inline col-sm-2 float-left checkbox-alignnormal text-center">
                <label class="form-check-label padding-left-clear">
                    <img src="{{ ($otherlanguage->speaking=='D - No Knowledge.')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                    D
                </label>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="form-group row flex-nowarp">
            <div class="col-sm-2 float-left">
                <label class="pdf-label-1">Reading</label>
            </div>
            <div class="form-check form-check-inline col-sm-2 float-left text-center checkbox-alignnormal">
                <label class="form-check-label padding-left-clear">
                    <img src="{{ ($otherlanguage->reading=='A - Limited - I am just learning the language.')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                    A
                </label>
            </div>
            <div class="form-check form-check-inline col-sm-2 float-left text-center checkbox-alignnormal">
                <label class="form-check-label padding-left-clear">
                    <img src="{{ ($otherlanguage->reading=='B - Functional - this is my second language but I can get by.')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                    B
                </label>
            </div>
            <div class="form-check form-check-inline col-sm-2 float-left text-center checkbox-alignnormal">
                <label class="form-check-label padding-left-clear">
                    <img src="{{ ($otherlanguage->reading=='C - Fluent - this is my native language.')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                    C
                </label>
            </div>
            <div class="form-check form-check-inline col-sm-2 float-left text-center checkbox-alignnormal">
                <label class="form-check-label padding-left-clear">
                    <img src="{{ ($otherlanguage->reading=='D - No Knowledge.')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                    D
                </label>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="form-group row flex-nowarp">
            <div class="col-sm-2 float-left">
                <label class="pdf-label-1">Writing</label>
            </div>
            <div class="form-check form-check-inline col-sm-2 float-left text-center checkbox-alignnormal">
                <label class="form-check-label padding-left-clear">
                    <img src="{{ ($otherlanguage->writing=='A - Limited - I am just learning the language.')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                    A
                </label>
            </div>
            <div class="form-check form-check-inline col-sm-2 float-left text-center checkbox-alignnormal">
                <label class="form-check-label padding-left-clear">
                    <img src="{{ ($otherlanguage->writing=='B - Functional - this is my second language but I can get by.')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                    B
                </label>
            </div>
            <div class="form-check form-check-inline col-sm-2 float-left text-center checkbox-alignnormal">
                <label class="form-check-label padding-left-clear">
                    <img src="{{ ($otherlanguage->writing=='C - Fluent - this is my native language.')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                    C
                </label>
            </div>
            <div class="form-check form-check-inline col-sm-2 float-left text-center checkbox-alignnormal">
                <label class="form-check-label padding-left-clear">
                    <img src="{{ ($otherlanguage->writing=='D - No Knowledge.')?asset('images/checked_checkbox.png'):asset('images/checkbox_unchecked.png') }}">
                    D
                </label>
            </div>
        </div>
        <div class="clearfix"></div>
@endforeach
