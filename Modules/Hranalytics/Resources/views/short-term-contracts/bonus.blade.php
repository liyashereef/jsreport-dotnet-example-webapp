@extends('layouts.app')
@section('css')
<style>
    th{
        color: #fff !important;
    }
    .strong{
        font-weight: bold
    }
</style>
    
@endsection
@section('content')
<div class="table_title">
    <h4> Spares Pool - Bonus Model </h4>
</div>
<div class="row">
    <div class="col-md-2 mt-1 mb-1 strong">Bonus Pool Start date</div>
    <div class="col-md-1 mt-1 mb-1">
        {{\Carbon::parse($bonusSettings->start_date)->format("d M Y")}}
    </div>
    <div class="col-md-2 mt-1 mb-1 strong">Bonus Pool End date</div>
    <div class="col-md-1 mt-1 mb-1">
        {{\Carbon::parse($bonusSettings->end_date)->format("d M Y")}}
    </div>
    <div class="col-md-2 mt-1 mb-1 strong">Bonus Pool Amount</div>
    <div class="col-md-1 mt-1 mb-1">
        ${{$bonusSettings->bonus_amount}}
    </div>
    <div class="col-md-2 mt-1 mb-1 strong">Bonus Shift Cap</div>
    <div class="col-md-1 mt-1 mb-1">
        {{round($bonusSettings->shiftcap_percentage)}}%
    </div>
    <div class="col-md-2 mt-1 mb-1 strong">Bonus Wage Cap</div>
    <div class="col-md-1 mt-1 mb-1">
        {{round($bonusSettings->wagecap_percentage)}}%

    </div>
    <div class="col-md-2 mt-1 mb-1 strong">Bonus Notice Cap</div>
    <div class="col-md-1 mt-1 mb-1">
        {{round($bonusSettings->noticecap_percentage)}}%

    </div>
    <div class="col-md-2 mt-1 mb-1 strong">Average Rate</div>
    <div class="col-md-1 mt-1 mb-1">
        @if ($bonusLogs!==null)
                    ${{round($bonusLogs->average_site_rate,2)}}

        @endif

    </div>
    <div class="col-md-2 mt-1 mb-1 strong">Per Shift Rate</div>
    <div class="col-md-1 mt-1 mb-1">
        @if ($bonusLogs!==null)

        ${{round($bonusLogs["per_shift_amount"],2)}}
@endif
    </div>
    <div class="col-md-2 mt-1 mb-1 strong">Report Generated On</div>
    <div class="col-md-1 mt-1 mb-1">
        @if ($bonusLogs!==null)

        {{$createdAt}}
@endif
    </div>
</div>
<div id="message"></div>
<table class="table table-bordered" id="stc-table">
    <thead>
        <tr>
            <th>Rank</th>
            <th>Employee</th>
            <th>Role</th>
            <th>Number Of Shifts Taken</th>
            <th>No Of Shifts Applied / Calls Made</th>
            <th>Reliability Score</th>
            <th>Average Wage</th>
            <th>Gross Up</th>
           
            <th>Average Notice</th>
            <th>Gross Up</th>
            <th>Total Adjustment</th>
            <th>Un-Adjusted Bonus</th>
            <th>Adjusted Bonus</th>
        </tr>
    </thead>
    <tbody>
        @if ($rankData!=null && $bonusLogs!=null && $finalizedData==null)
              @foreach ($rankData as $userData)
        <tr>
            <td>{{$userData["rank"]}}</td>
            <td>
                {{-- {{$userData["user_id"]}} --}}
                {{$userArray[$userData["user_id"]]["name"]}}
            </td>
            <td>
                {{$userArray[$userData["user_id"]]["roles"]!=""?$userArray[$userData["user_id"]]["rolename"]:""}}
            </td>
            
            <td>
                    {{$userData["no_of_shifts_taken"]}}
            </td>
            <td>
                    {{$userData["no_of_calls_made"]}}

            </td>
            <td>
                    {{$userData["reliability_score"]}}

            </td>
            <td>
                    ${{round($userData["average_wage"],2)}}

            </td>
            <td>
                    {{round($userData["average_wage_gross_up"],2)}}%
            </td>
            <td>
                    {{round($userData["average_notice"],2)}}
            </td>
            <td>
                    {{round($userData["average_notice_gross_up"],2)}}%
            </td>
            <td>
                    {{round($userData["total_adjustment"],2)}}%
            </td>
            <td>
                ${{round($userData["unadjusted_bonus"],2)}}
            </td>
            <td>
                ${{round($userData["adjusted_bonus"],2)}}
            </td>
        </tr>
        @endforeach
        @elseif($finalizedData!=null)
        @foreach ($finalizedData as $userData)
        <tr>
            <td>
                {{$userData->rank}}
            </td>
            <td>
                {{$userData->user->getFullNameAttribute()}}
            </td>
            <td>
                {{isset($userData->user->roles)?$userData->user->roles->first()->name:""}}
            </td>
            
            <td>
                    {{$userData->no_of_shifts_taken}}
            </td>
            <td>
                    {{$userData->no_of_calls_made}}

            </td>
            <td>
                {{$userData->reliability_score}}

            </td>
            <td>
                ${{round($userData->average_wage,2)}}

            </td>
            <td>
                {{round($userData->average_wage_gross_up,2)}}%

            </td>
            <td>
                {{round($userData->average_notice,2)}}
            </td>
            <td>
                {{round($userData->average_notice_gross_up,2)}}%
            </td>
            <td>
                {{round($userData->total_adjustment,2)}}%
            </td>
            <td>
                ${{round($userData->unadjusted_bonus,2)}}
            </td>
            <td>
                ${{round($userData->adjusted_bonus,2)}}
            </td>
        </tr>
        @endforeach
        @endif
      

    </tbody>
</table>
@endsection

@section('scripts')
 @include('hranalytics::short-term-contracts.partials.bonus-script')
@endsection
