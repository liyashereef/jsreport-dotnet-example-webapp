<?php

namespace Modules\KPI\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Modules\KPI\Repositories\KpiAnalyticsRepository;
use Modules\KPI\Repositories\KpiCacheRepository;

use Modules\KPI\Repositories\KpiBulkJobRepository;

class KpiWidgetController extends Controller
{
  protected $helperService;
  protected $kpiCacheRepository;
  protected $kpiAnalyticsRepository;
  protected $kpiBulkJobRepository;

  public function __construct(
    HelperService $helperService,
    KpiCacheRepository $kpiCacheRepository,
    KpiAnalyticsRepository $kpiAnalyticsRepository
  ) {
    $this->helperService = $helperService;
    $this->kpiCacheRepository = $kpiCacheRepository;
    $this->kpiAnalyticsRepository = $kpiAnalyticsRepository;

    $this->yesterday = Carbon::now()->subDays(1)->format('Y-m-d');
  }

  public function index(Request $request)
  {
    //Get queries
    $queries = [
      'activeGroup' => $request->input('active-group'),
      'from' => $request->input('from'),
      'to' => $request->input('to'),
      'customerIds' => $request->input('customer-search'),
      'userId' => auth()->user()->id
    ];

    //Calculate query hash key
    $keyHash = $this->kpiCacheRepository->keyFromQueries($queries);

    //Check for cache
    $c = $this->kpiCacheRepository->get($keyHash);
    // if ($c != null) {
    //   $data =  $c->decodedValue;
    // } else {
    //Compute data from repo fn
    $data = $this->kpiAnalyticsRepository->processRequest($queries);

    //   //Store data in cache
    //   $this->kpiCacheRepository->set($keyHash, $data, $queries);
    // }

    return response()->json([
      "data" => $data,
      "type" => "json",
      "widgetTag" => 'widget-key-performance-indicators',
    ], 200);
  }

  //Execute kpi processing job
  public function executeJob()
  {
    $options = [];
    $this->kpiAnalyticsRepository->executeJob($options, false);
  }
  public function executeBulkJob()
  {
    $options = [];
    $this->kpiAnalyticsRepository->executeJob($options, true);
  }
}
