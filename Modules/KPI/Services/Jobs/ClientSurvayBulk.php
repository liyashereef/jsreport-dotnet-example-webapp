<?php

namespace Modules\KPI\Services\Jobs;

use Carbon\Carbon;
use Doctrine\DBAL\Query\QueryBuilder;
use Modules\Client\Repositories\ClientSurveyRepository;
use Modules\KPI\Services\AbstractKpiJob;
use Modules\KPI\Services\KpiJobInterface;
use Modules\KPI\Services\KpiJobOption;

class ClientSurvayBulk extends AbstractKpiJob implements KpiJobInterface
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
        $inputs = [];
        $datas = [];
        $arguments = $this->options->arguments;
        $surveyData = $this->clientSurveyRepository->getAllCustomerSurveyData($arguments);
        foreach ($surveyData as $survey) {
            if ($survey->rating > 0) {
                $datas[] = [
                    "kpid" => $this->options->kpi->id,
                    "customer_id" => $survey->customer_id,
                    "process_date" => Carbon::parse($survey->createdAt),
                    "value" => floatval($survey->rating),
                    "value_total" => '',
                    "value_output" => ''
                ];
            }
        }
        return $datas;
    }
}
