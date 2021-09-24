<?php

namespace Modules\KPI\Services;

use Carbon\Carbon;
use Exception;

class KpiFrequencyMap
{
    const THIS_MONTH = 'TM';
    const LAST_MONTH = 'LM';
    const LAST_THREE_MONTHS = 'L3M';
    const LAST_SIX_MONTHS = 'L6M';
    const LAST_YEAR = 'LY';

    static function resolveToFreqDate($freqency)
    {
        $from = null;
        $label = null;
        $now = Carbon::now()->subDays(1);

        switch ($freqency) {
            case self::THIS_MONTH:
                $from = $now->startOfMonth();
                $label = 'This Month';
                break;
            case self::LAST_MONTH:
                $from = $now->subMonth(1);
                $label = 'Last 1 Month';
                break;
            case self::LAST_THREE_MONTHS:
                $from = $now->subMonth(3);
                $label = 'Last 3 Months';
                break;
            case self::LAST_SIX_MONTHS:
                $from = $now->subMonth(6);
                $label = 'Last 6 Months';
                break;
            case self::LAST_YEAR:
                $from = $now->subYear(1);
                $label = 'Last 12 Months';
                break;
            default:
                throw new Exception('Invalid frequency provided');
                break;
        }

        return [
            'from' => $from->startOfDay(),
            'to' => Carbon::now()->subDays(1)->endOfDay(),
            'label' => $label
        ];
    }

    public static function getFrequencyList($only = [])
    {
        $fs = ['LM', 'L3M', 'L6M', 'LY'];
        $list = [];

        if (!empty($only)) {
            $fs = $only;
        }

        foreach ($fs as $f) {
            $list[$f] = self::resolveToFreqDate($f);
        }
        return $list;
    }
}
