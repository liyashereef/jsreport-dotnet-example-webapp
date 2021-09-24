<?php

namespace Modules\KPI\Repositories;


use DateTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\UTCDateTime;

use Modules\KPI\Models\KpiCache;
use Modules\KPI\Models\KpiData;
use Modules\Admin\Repositories\KpiGroupRepository;
use App\Services\HelperService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\KpiMaster;
use Modules\Admin\Repositories\KpiCustomerHeaderRepository;
use Modules\Admin\Repositories\KpiMasterRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\KPI\Services\KpiBulkJobFactory;
use Modules\KPI\Services\KpiDailyJobFactory;
use Modules\KPI\Services\KpiDataStoreTrait;
use Modules\KPI\Services\KpiFrequencyMap;
use Modules\KPI\Services\KpiJobInterface;
use Modules\KPI\Services\KpiJobOption;

class KpiAnalyticsRepository
{
    use KpiDataStoreTrait;

    /**
     * The Model instance.
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;
    protected $logger;
    protected $kpiGroupRepository;
    protected $kpiMasterRepository;
    protected $helperService;
    protected $kpiCustomerHeaderRepository;
    protected $customerEmployeeAllocationRepository;
    protected $kpiDailyJobRepository;
    protected $kpiBulkJobRepository;
    protected $employeeAllocationRepository;



    public function __construct(
        KpiCache $kpiCache,
        KpiGroupRepository $kpiGroupRepository,
        KpiMasterRepository $kpiMasterRepository,
        KpiCustomerHeaderRepository $kpiCustomerHeaderRepository,
        EmployeeAllocationRepository $employeeAllocationRepository,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository
    ) {
        $this->model = $kpiCache;
        $this->kpiGroupRepository = $kpiGroupRepository;
        $this->kpiMasterRepository = $kpiMasterRepository;
        $this->helperService = new HelperService();
        $this->kpiCustomerHeaderRepository = $kpiCustomerHeaderRepository;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->employeeAllocationRepository = $employeeAllocationRepository;

        $this->logger =  Log::channel('kpiLog');
        $this->yesterday = Carbon::now()->subDays(1)->format('Y-m-d');
    }


    public function getKpiAnalyticStructure($inputs)
    {
        $results['filter'] = [];
        $results['groups'] = [];
        $arrayCustomers = [];
        $customers = [];
        $arrayKpiMasters = [];
        //Fetch all allocated customers.
        $allocatedCustomerIds = $this->customerEmployeeAllocationRepository->getDirectAllocatedCustomers(Auth::user());
        $selectedCustomerIds = $allocatedCustomerIds;
        //If customer filter applied.
        if (isset($inputs['customerIds']) && sizeof($inputs['customerIds']) > 0) {
            $selectedCustomerIds = $inputs['customerIds'];
        }
        //Fetch all group with childrens and allocated customers and kpis.
        $noads =  $this->kpiGroupRepository->getParentAndChildWithCustomers($inputs);

        foreach ($noads as $key => $node) {
            $customerIds = [];
            $kpiMasterIds = [];
            $childCustomerIds = [];
            $kpiMasterId = [];
            $isLeafNode = 0;

            if (!empty($node)) {
                if (sizeof($node->kpiGroupCustomers) == 0 && sizeof($node->family) == 0) {
                    //If group has an allocation with customers.
                    $isLeafNode = 1;
                }
                //Get Direct allocated datas.
                if (sizeof($node->kpiGroupCustomers) > 0) {

                    //Select group allocated customerIds.
                    $customerIds = array_filter(collect($node->kpiGroupCustomers)
                        ->whereIn('customer_id', $selectedCustomerIds)
                        ->pluck('customer_id')
                        ->toArray());

                    //Select customer allocated kpids.
                    $kpiMasterIds = array_filter(collect($node->kpiGroupCustomers)
                        ->whereIn('customer_id', $customerIds)
                        ->pluck('kpiMasterCustomerAllocation.*.kpi_master_id')
                        ->toArray());

                    //If group has an allocation with customers
                    $isLeafNode = 1;
                } elseif (sizeof($node->family) > 0) {

                    foreach ($node->family as $familyKey => $family) {
                        $childCustomerIds = [];
                        $childkpiMasterIds = [];

                        if (sizeof($family->kpiGroupCustomers) > 0) {

                            //Select group allocated customerIds.
                            $childCustomerIds = array_filter(collect($family->kpiGroupCustomers)
                                ->whereIn('customer_id', $selectedCustomerIds)
                                ->pluck('customer_id')
                                ->toArray());

                            //Select customer allocated kpids.
                            $childkpiMasterIds = array_filter(collect($family->kpiGroupCustomers)
                                ->whereIn('customer_id', $childCustomerIds)
                                ->pluck('kpiMasterCustomerAllocation.*.kpi_master_id')
                                ->toArray());
                        } elseif (sizeof($family->family) > 0) {

                            $customers = [
                                'childCustomerIds' => [],
                                'kpiMasterId' => [],
                            ];
                            //Recursive function to fetch nested group's customer and kpi.
                            $this->getCustomersFromChildren($selectedCustomerIds, $family->family, $childCustomerIds, $kpiMasterId, $customers);
                            $childCustomerIds = $customers['childCustomerIds'];
                            $childkpiMasterIds = $customers['kpiMasterId'];

                            // foreach ($family->family as $innerNode) {
                            //     if (sizeof($innerNode['kpiGroupCustomers']) > 0) {

                            //         //Select group allocated customerIds.
                            //         $childCustomerIds = array_filter(collect($innerNode['kpiGroupCustomers'])
                            //         ->whereIn('customer_id',$selectedCustomerIds)
                            //         ->pluck('customer_id')
                            //         ->toArray());

                            //         //Select customer allocated kpids.
                            //         $kpiMasterIds = array_filter(collect($innerNode['kpiGroupCustomers'])
                            //         ->whereIn('customer_id',$childCustomerIds)
                            //         ->pluck('kpiMasterCustomerAllocation.*.kpi_master_id')
                            //         ->toArray());

                            //     } else {

                            //         $customers = [
                            //             'childCustomerIds' => [],
                            //             'kpiMasterId' => [],
                            //         ];
                            //         $this->getCustomersFromChildren($selectedCustomerIds,$innerNode['family'], $childCustomerIds, $kpiMasterId, $customers);
                            //         $childCustomerIds = $customers['childCustomerIds'];
                            //         $kpiMasterId = $customers['kpiMasterId'];
                            //     }
                            //     $customerIds = array_merge($customerIds, $childCustomerIds);
                            //     $kpiMasterIds = array_merge($kpiMasterIds, $kpiMasterId);
                            // }
                        }
                        $customerIds = array_merge($customerIds, $childCustomerIds);
                        $kpiMasterIds = array_merge($kpiMasterIds, $childkpiMasterIds);
                    }
                } else {
                }
                $customerIds = $this->helperService->getNumerics(json_encode($customerIds));
                $kpiMasterIds = $this->helperService->getNumerics(json_encode($kpiMasterIds));

                $arrayCustomers = array_merge($arrayCustomers, $customerIds);
                $arrayKpiMasters = array_merge($arrayKpiMasters, $kpiMasterIds);

                $results['filter'] = [
                    'customerIds' => array_intersect($this->numercArray(array_unique($arrayCustomers)), $selectedCustomerIds),
                    'kpiMasterIds' => $this->numercArray(array_unique($arrayKpiMasters))
                ];

                $groupCustomerIds = array_intersect($this->numercArray(array_unique($customerIds)), $selectedCustomerIds);
                if (sizeof($groupCustomerIds) > 0) {
                    $results['groups'][] = [
                        'id' => $node->id,
                        'name' => $node->name,
                        'isLeafNode' => $isLeafNode,
                        'customerIds' => $groupCustomerIds,
                        'kpiMasterIds' => $this->numercArray(array_unique($kpiMasterIds))
                    ];
                }
            }
        }

        return $results;
    }

    public function numercArray($values)
    {
        return array_map('intval', $values);
    }

    /**
     * Recursive function.
     * Fetching nested group's customerIds.
     */
    public function getCustomersFromChildren($selectedCustomerIds, $children, $childCustomerIds, $kpiMasterId, &$output = [])
    {
        foreach ($children as $child) {
            if (sizeof($child['kpiGroupCustomers']) > 0) {

                //Select group allocated customerIds.
                $childCustomerIds = array_merge(
                    $childCustomerIds,
                    $this->helperService->getNumerics(
                        json_encode(array_filter(
                            collect($child['kpiGroupCustomers'])
                                ->whereIn('customer_id', $selectedCustomerIds)
                                ->pluck('customer_id')
                                ->toArray()
                        ))
                    )
                );

                //Select customer allocated kpids.
                $kpiMasterId = array_merge(
                    $kpiMasterId,
                    $this->helperService->getNumerics(
                        json_encode(array_filter(
                            collect($child['kpiGroupCustomers'])
                                ->whereIn('customer_id', $childCustomerIds)
                                ->pluck('kpiMasterCustomerAllocation.*.kpi_master_id')
                                ->toArray()
                        ))
                    )
                );
            } else {
                $this->getCustomersFromChildren($selectedCustomerIds, $child['family'], $childCustomerIds, $kpiMasterId, $output);
            }
        }
        $output['kpiMasterId'] = array_merge($output['kpiMasterId'], $kpiMasterId);
        $output['childCustomerIds'] = array_merge($output['childCustomerIds'], $childCustomerIds);
    }

    public function getThreholdColorByValue($kpiThresholds, $value)
    {
        $result['color'] = '';
        $result['font_color'] = '';
        if (empty($value) && $value <= 0) {
            return $result;
        }

        $kpit = collect($kpiThresholds)->sortByDesc('max');
        $kts = $kpit->where('min', '<=', $value)->where('max', '>=', $value);
        if ($kts->isNotEmpty()) {
            $result['color'] = $kts->first->kpiThresholdColor->kpiThresholdColor->color_code;
            $result['font_color'] = $kts->first->kpiThresholdColor->kpiThresholdColor->font_color;
        }
        return $result;
    }

    public function colorWithFallback($color)
    {
        $out = [];
        $out['color'] = empty($color['color']) ? 'white' : $color['color'];
        $out['font_color'] =  empty($color['font_color']) ? 'black' : $color['font_color'];
        return $out;
    }

    public function toValueByThresholdType($type, $value)
    {
        $type =  (int) $type;
        //Rating
        if ($type == 1) {
            return $value;
        }
        //Percentage
        if ($type == 2) {
            return $value . '%';
        }
    }

    public function processRequest($filters)
    {

        $fromDate = new DateTime(Carbon::now()->subDays(31)->format('Y-m-d'));
        $toDate = new DateTime($this->yesterday);

        $structure = $this->getKpiAnalyticStructure($filters);

        if (sizeof($structure['filter']) >= 1) {

            $customers = $structure['filter']["customerIds"];
            $kpids = $structure['filter']["kpiMasterIds"];

            //Filter by customers
            $q = KpiData::whereIn('customer_id', $customers);

            //Filter by from date
            if (isset($filters['from']) && !empty($filters['from'])) {
                // $fromDate = new DateTime($filters['from']);
                $fromDate = Carbon::parse($filters['from'])->startOfDay();
            }
            $q->where('process_date', '>=', $fromDate);
            //Filter by to date
            if (isset($filters['to']) && !empty($filters['to'])) {
                // $toDate = new DateTime($filters['to']);
                $toDate = Carbon::parse($filters['to'])->endOfDay();
            }
            $q->where('process_date', '<=', $toDate);

            //Filter by kpids
            $q->whereIn('kpid', $kpids);
            $collections =  $q->get();
            return $this->transformRequest($structure, $collections);
        } else {
            $structure['infos'] = [];
            return $structure;
        }
    }

    public function transformRequest($rawStruct, $collection)
    {
        //Get unique kpids
        // $unKpids  = $collection->unique('kpid')->pluck('kpid')->all();
        $groups = $rawStruct["groups"];
        $structure = [];
        // $kpStructure = $this->kpiCustomerHeaderRepository->getByKpids($unKpids);
        $kpStructure = $this->kpiCustomerHeaderRepository->getByKpids($rawStruct['filter']['kpiMasterIds']);
        //Build Headers
        foreach ($kpStructure as $key => $header) {
            $structure[$key] = [
                "id" => $header->id,
                "name" => $header->name
            ];
            //Build Kpis
            $kpis = [];

            foreach ($header->kpiMasterAllocation as $kpidAlloc) {
                // //Skip inactive KPI Allocation
                if ($kpidAlloc->is_active == 0) {
                    continue;
                }
                $kv = [
                    "id" => $kpidAlloc->kpiMaster->id,
                    "name" => $kpidAlloc->kpiMaster->name,
                ];

                //Get Group Kpids & process
                $values = [];
                $avg = 0;
                $avgColor = '';
                $percentSign = '';
                if ($kpidAlloc->kpiMaster->threshold_type == 2) {
                    $percentSign = '%';
                }

                foreach ($groups as $group) {
                    $_kpid = $kpidAlloc->kpiMaster->id;
                    $color = [];
                    $value = 0;

                    //Calculate values
                    if (in_array($_kpid, $group["kpiMasterIds"])) {
                        $res = $collection->whereIn('customer_id', $group['customerIds'])
                            ->where('kpid', '=', $_kpid)
                            ->average('value');
                        if ($res != null || $res != 'null') {
                            $value = $this->toValueByThresholdType($kpidAlloc->kpiMaster->threshold_type, $res);
                            $avg += $res;
                        }
                        $color = $this->getThreholdColorByValue($kpidAlloc->kpiThresholds, $res);
                    }

                    $color = $this->colorWithFallback($color);

                    //Assign values
                    $values[] = [
                        "color" => $color['color'],
                        "font_color" => $color['font_color'],
                        "value" => round($value, 2) . $percentSign,
                        "group_id" => $group["id"]
                    ];
                }

                //Kpi average calculation
                $avg = $avg / count($groups);
                $avg = round($avg, 2);

                //Average color
                $avgColor = $this->colorWithFallback($this->getThreholdColorByValue($kpidAlloc->kpiThresholds, $avg));

                $avg = $this->toValueByThresholdType($kpidAlloc->kpiMaster->threshold_type, $avg);

                //Assign kpi extra fields
                $kv['average'] = $avg;
                $kv['color'] = $avgColor['color'];
                $kv['font_color'] = $avgColor['font_color'];
                $kv["values"] = $values;

                //Save to kpi array
                array_push($kpis, $kv);
            }
            $structure[$key]["kpis"] = $kpis;
        }
        $rawStruct["infos"] = $structure;
        //Return structure
        return $rawStruct;
    }


    public function executeJob($arguments, $bulkMode = false)
    {

        $jobType = $bulkMode ? 'KPI-BULK: ' : 'KPI-DAILY: ';

        try {
            $this->logger->info('--------------------------------------------------');
            $this->logger->info($jobType . 'Job started');

            KpiCache::truncate();
            $this->logger->info($jobType . 'Cache cleared');

            //Load all kpis
            if (isset($arguments) && isset($arguments['kpid'])) {
                $kpis = KpiMaster::where('id', $arguments['kpid'])->get();
            } else {
                $kpis = KpiMaster::all();
            }

            //Process each kpi
            foreach ($kpis as $index => $kpi) {
                $this->logger->info($jobType . 'Processing [ ' . $kpi->name . ' ] ' . ($index + 1) . '/' . count($kpis));

                $kpiJobOption = new KpiJobOption($kpi, $arguments);
                $job = null;

                if ($bulkMode) {
                    $job = KpiBulkJobFactory::create($kpiJobOption);
                } else {
                    $job = KpiDailyJobFactory::create($kpiJobOption);
                }

                if ($job instanceof KpiJobInterface) {
                    $res = $job->run();
                    $this->storeToDB($res, $bulkMode, $job);

                    $this->logger->info($jobType . '(' . count($res) . ') data uploaded to DB');
                } else {
                    $this->logger->info($jobType . 'Skip [' . $kpi->name . ' ] ');
                }
            }
            $this->logger->info($jobType . 'Job finished');
        } catch (\Exception $e) {
            $this->logger->info($jobType . 'Job failed - ' . $e->getMessage() . ' in ' . $e->getFile() . ' at ' . $e->getLine());
        }
    }

    public function getCustomerRecordsByFreq($frequency, $customerIds, $from = null, $to = null)
    {
        //   //Todo:replace customer id
        //     $customerIds = Customer::all()->pluck('id');

        //     $dates =  KpiFrequencyMap::resolveToFreqDate($frequency);

        //     //Filter by customers
        //     $q = KpiData::whereIn('customer_id', $customerIds);

        //     //Filter by from date
        //     if (isset($dates['from']) && !empty($dates['from'])) {
        //         // $fromDate = new DateTime($filters['from']);
        //         $fromDate = ($dates['from'])->startOfDay();
        //     }

        //     $q->where('process_date', '>=', $fromDate);

        //     //Filter by to date
        //     if (isset($dates['to']) && !empty($dates['to'])) {
        //         // $toDate = new DateTime($filters['to']);
        //         $toDate =  ($dates['to'])->endOfDay();
        //     }
        //     $q->where('process_date', '<=', $toDate);

        //     //Filter by kpids
        //     $q->whereIn('kpid', [6]);
        //     $q->groupBy('process_date');
        //     $q->aggregate('avg', ['value']);

        //     $q->select('value', 'process_date');

        //     $collections =  $q->get();

        //     return $collections;  


        $dates = [];
        if ($frequency == 'custom' && $from && $to) {
            $dates = [
                'from' => Carbon::parse($from),
                'to'  => Carbon::parse($to)
            ];
        } else {
            $dates = KpiFrequencyMap::resolveToFreqDate($frequency);
        }

        $response = KpiData::raw(function ($collection) use ($customerIds, $dates) {

            function toMongoDate($timestamp)
            {
                // $timestamp = strtotime($timestamp) * 1000;
                $timestamp = strtotime($timestamp) * 1000;
                $object = new UTCDateTime($timestamp);
                return $object;
            }

            if (!empty($customerIds)) {
                $customerIds = array_map('intval', $customerIds);
            } else {

                $customers  = $this->customerEmployeeAllocationRepository
                    ->getAllocatedCustomersList(auth()->user());
                $customerIds = array_keys($customers);
            }

            // dd($customerIds);

            // $dates['from']= Carbon::parse('2021-03-01');
            // $dates['to'] =  Carbon::parse('2021-05-01');
            // $customerIds = [];

            return $collection->aggregate([
                [
                    '$match' => [
                        '$and' => [
                            ['kpid' => 6],
                            ['customer_id' => ['$in' => $customerIds]],
                            [
                                'process_date' =>
                                [
                                    '$lte' => toMongoDate(($dates['to'])->format('Y-m-d H:i:s'))
                                ]
                            ],
                            [
                                'process_date' =>
                                [
                                    '$gte' => toMongoDate(($dates['from'])->format('Y-m-d H:i:s'))
                                ]
                            ],

                        ]
                    ]
                ],
                // [
                //     '$group' => [
                //         '_id' => [
                //             'process_date' => '$process_date'
                //         ],
                //         "value" => ['$first' => '$value'],
                //         "process_date" => ['$first' => '$process_date'],
                //         "split_up" => ['$first' => '$split_up']
                //     ]
                // ],
                [
                    '$sort' => [
                        'process_date' => -1
                    ]
                ],
            ]);
        });


        $result = $response->groupBy(['process_date.year', 'process_date.month', 'customer_id']);

        $out = [
            'graph' => [],
            'list' => [],
            'average' => 0
        ];

        $custAvgs = [];

        foreach ($result as $yrKy => $yr) {

            foreach ($yr as $mthKy => $mth) {
                $key = $yrKy . '-' . $mthKy . '-01';

                $avg = 0;
                $dArr = data_get($mth, '*.0.value');

                if (count($dArr) > 0) {
                    $avg = array_sum($dArr) / count($dArr);
                    array_push($custAvgs, $avg);
                }

                $out['graph'][] = [
                    'date' =>  $key,
                    'value' => $avg
                ];

                $mthSplitUp = [];
                foreach ($mth as $custId => $custData) {
                    $day = $custData->first();
                    //User rating of month
                    if (isset($day) && isset($day->split_up)) {
                        //Loop spitup data
                        foreach ($day->split_up as $sp) {
                            $gen = [];
                            $gen['user_id'] = $sp['userId'];
                            $gen['customer_id'] = $custId;
                            $gen['average'] = $sp['average_rating'];
                            $mthSplitUp[] = $gen;
                        }
                    }
                }

                // $processedMs = [];
                // foreach ($mthSplitUp as $k => $ms) {
                //     if (!empty($ms)) {
                //         $processedMs[$k] = array_sum($ms) / sizeof($ms);
                //     } else {
                //         $processedMs[$k] = null;
                //     }
                // }
                $out['list'][$key] = Crypt::encryptString(serialize($mthSplitUp));
                if (!empty($custAvgs)) {
                    $out['average'] = array_sum($custAvgs) / count($custAvgs);
                }
            }
        }
        return $out;
    }
}
