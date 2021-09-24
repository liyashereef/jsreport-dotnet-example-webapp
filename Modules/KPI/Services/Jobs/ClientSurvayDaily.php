<?php

namespace Modules\KPI\Services\Jobs;

use Carbon\Carbon;
use Modules\Client\Repositories\ClientSurveyRepository;
use Modules\KPI\Services\AbstractKpiJob;
use Modules\KPI\Services\KpiJobInterface;
use Modules\KPI\Services\KpiJobOption;

class ClientSurvayDaily extends AbstractKpiJob implements KpiJobInterface
{
    protected $options;
    protected $clientSurveyRepository;

    public function __construct(KpiJobOption $kpiJobOption)
    {
        $this->options = $kpiJobOption;
        $this->clientSurveyRepository = app()->make(ClientSurveyRepository::class);
    }

    public function run()
    {
        $datas = [];
        $request['date'] = $this->options->yesterday;
        $surveyData = $this->clientSurveyRepository->getAllCustomerSurveyData($request);

        foreach ($surveyData as $survey) {
            $datas[] = [
                "kpid" => $this->options->kpi->id,
                "customer_id" => $survey->customer_id,
                "process_date" => Carbon::parse($this->options->yesterday),
                "value" => floatval($survey->rating),
                "value_total" => '',
                "value_output" => ''
            ];
        }
        return $datas;
    }
}
