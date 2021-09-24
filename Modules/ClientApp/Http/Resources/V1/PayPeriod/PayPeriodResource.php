<?php

namespace Modules\ClientApp\Http\Resources\V1\PayPeriod;

use Illuminate\Http\Resources\Json\Resource;
use Carbon\Carbon;

class PayPeriodResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     *
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->pay_period_name,
            'shortName' => $this->short_name,
            'start' => $this->start_date,
            'weekOneEnd' => $this->week_one_end_date,
            'weekTwoStart' => $this->week_two_start_date,
            'end' => $this->end_date,
            'current' => $this->isCurrentPayperiod($this->start_date,$this->end_date),
            'active' => ($this->active == 1) ? true : false,
        ];
    }

    private function isCurrentPayperiod($startDate, $endDate) {
        $current = false;
        $startDate = Carbon::createFromFormat('Y-m-d',$startDate);
        $endDate = Carbon::createFromFormat('Y-m-d',$endDate);

        $current = Carbon::now()->between($startDate,$endDate);

        return $current;
    }
}
