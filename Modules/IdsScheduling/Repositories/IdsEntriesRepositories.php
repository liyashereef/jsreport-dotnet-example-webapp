<?php

namespace Modules\IdsScheduling\Repositories;

use Modules\IdsScheduling\Models\IdsEntries;
use Modules\IdsScheduling\Repositories\IdsEntryAmountSplitUpRepository;

class IdsEntriesRepositories
{

    protected $model;
    protected $dashboardWidgetUserRepository;

    public function __construct(IdsEntries $model, IdsEntryAmountSplitUpRepository $idsEntryAmountSplitUpRepository)
    {
        $this->model = $model;
        $this->idsEntryAmountSplitUpRepository = $idsEntryAmountSplitUpRepository;
    }

    public function store($inputs)
    {
        return $this->model->create($inputs);
    }

    public function getById($id)
    {
        return $this->model
        ->with([
            'IdsOffice' => function ($query) {
                return $query->select('id', 'name', 'adress','phone_number','phone_number_ext');
            },
            'IdsOfficeSlots' => function ($query) {
                return $query->select('id', 'display_name', 'start_time', 'end_time');
            },
            'idsServicesWithTrashed' => function ($query) {
                return $query->select('id', 'name', 'short_name');
            },
            'idsPassportPhotoServiceWithTrashed' => function ($query) {
                return $query->select('id', 'name', 'rate');
            },
            'idsTransactionHistory' => function ($query) {
                return $query->select('id', 'entry_id', 'ids_online_payment_id','ids_payment_method_id',
                'amount','transaction_type','user_id','refund_note','refund_status','created_at');
            }
        ])->find($id);
    }

    public function updateEntry($inputs)
    {
        return $this->model->updateOrCreate(['id' => $inputs['id']], $inputs);
    }

    public function delete($id)
    {
        return $this->model->where('id', $id)->delete();
    }

    public function updateRefundStatus($inputs){
        $id = $inputs['entry_id'];
        unset($inputs['entry_id']);
        return $this->model->withTrashed()->where('id',$id)->update($inputs);
    }

    /**
     * Check slot booked .
     * set to_be_rescheduled field as true.
     * @param ids_office_slot_id, slot_booked_date,
     */
    public function checkSlotAlreadyBooked($inputs)
    {
        return $this->model
            ->where('ids_office_slot_id', $inputs['ids_office_slot_id'])
            ->where('slot_booked_date', $inputs['slot_booked_date'])
            ->count();
    }

    /**
     * Setting a flag for reshedule request.
     * set to_be_rescheduled field as true.
     * @param slot_booked_date, ids_office_id,
     */
    public function setToBeReshedule($inputs)
    {
        return $this->model
            ->where('ids_office_id', $inputs['ids_office_id'])
            ->where('slot_booked_date', $inputs['slot_booked_date'])
            ->update(['to_be_rescheduled' => true]);
    }

    /**
     * Get count of today and future dates booking counts for Delete office or service
     * @param ids_office_id, ids_service_id,
     */
    public function getBookings($inputs)
    {
        return $this->model
            ->when(isset($inputs) && !empty($inputs['ids_office_id']), function ($q) use ($inputs) {
                return $q->where('ids_office_id', $inputs['ids_office_id']);
            })
            ->when(isset($inputs) && !empty($inputs['ids_service_id']), function ($q) use ($inputs) {
                return $q->where('ids_service_id', $inputs['ids_service_id']);
            })
            ->where('slot_booked_date', '>=', date('Y-m-d'))
            ->count();
    }

    /**
     * Get count of today and future dates booking counts for Delete office or service
     * @param ids_office_timing_id,
     */
    public function getTimingBookedCount($inputs)
    {
        $que = $this->model
            ->whereHas('IdsOfficeSlots', function ($query) use ($inputs) {
                return $query->where('ids_office_timing_id', $inputs['ids_office_timing_id']);
            })
            ->when(isset($inputs) && !empty($inputs['slot_booked_date']), function ($q) use ($inputs) {
                return $q->where('slot_booked_date', '>=', $inputs['slot_booked_date']);
            });
        if (isset($inputs['count']) && $inputs['count'] == true) {
            return $que->count();
        } else {
            return $que->orderBy('slot_booked_date', 'DESC')->first();
        }

    }

    /**
     * Fetching forcast report data
     * @param slot_booked_date, ids_office_id,
     */

    public function getServiceReport($inputs)
    {
        return $this->model
            ->whereIn('slot_booked_date', $inputs['slot_booked_date'])
            ->when(isset($inputs) && !empty($inputs['ids_office_id']), function ($q) use ($inputs) {
                return $q->whereIn('ids_office_id', $inputs['ids_office_id']);
            })
            ->whereIn('is_online_payment_received',[1,2])
            ->select('slot_booked_date', 'ids_service_id', \DB::raw("SUM(given_rate) as service_fee_sum"))
            ->groupBy('slot_booked_date', 'ids_service_id')
            ->orderBy('slot_booked_date')
            ->has('IdsServices')
            ->get();
    }

    public function getServiceBalanceFeeReport($inputs)
    {
        return $this->model
            ->whereIn('slot_booked_date', $inputs['slot_booked_date'])
            ->when(isset($inputs) && !empty($inputs['ids_office_id']), function ($q) use ($inputs) {
                return $q->whereIn('ids_office_id', $inputs['ids_office_id']);
            })
            ->whereIn('is_online_payment_received',[1,2])
            ->whereIn('refund_status', [0,3])
            ->where('balance_fee','<', 0)
            ->select('slot_booked_date', 'ids_service_id', \DB::raw("SUM(balance_fee) as service_fee_sum"))
            ->groupBy('slot_booked_date', 'ids_service_id')
            ->orderBy('slot_booked_date')
            ->has('IdsServices')
            ->get();
    }

    /**
     * For Calendar page.
     * Fetching 3 months data(Past,Carrent,Future)
     * @param start_dates and ids_office_id
     */

    public function getCalendarData($inputs)
    {
        return $this->model
            ->when(isset($inputs) && !empty($inputs['ids_office_id']), function ($q) use ($inputs) {
                return $q->where('ids_office_id', $inputs['ids_office_id']);
            })
            ->when(isset($inputs) && !empty($inputs['startDate']), function ($q) use ($inputs) {
                return $q->where('slot_booked_date', '>=', $inputs['startDate']);
            })
            ->when(isset($inputs) && !empty($inputs['endDate']), function ($q) use ($inputs) {
                return $q->where('slot_booked_date', '<=', $inputs['endDate']);
            })
            ->whereIn('is_online_payment_received',[1,2])
            // ->where(function($q) {
            //     $q->where('is_online_payment_received',1)
            //       ->orWhereNull('is_online_payment_received');
            // })
            ->select('slot_booked_date','ids_service_id', \DB::raw('count(*) as total'))
            ->groupBy('slot_booked_date', 'ids_service_id')
            ->with(['IdsServices' => function ($query) {
                return $query->select('id', 'name', 'short_name');
            }])
            ->get();
    }

    /**
     * Forecast Report page, Calculates 12 months sum of fees
     * @param start_dates and ids_office_id
     */

    public function getYearForecast($inputs)
    {
        return $this->model->select(\DB::raw("SUM(given_rate) as service_fee_sum"), \DB::raw("DATE_FORMAT(slot_booked_date, '%Y-%m') month_year"))
            ->groupBy('month_year')
            ->orderBy('month_year')
            ->when(isset($inputs) && !empty($inputs['start_date']) && !empty($inputs['end_date']), function ($q) use ($inputs) {
                return $q->whereBetween('slot_booked_date', [$inputs['start_date'], $inputs['end_date']]);
            })
            ->whereIn('is_online_payment_received',[1,2])
            ->when(isset($inputs) && !empty($inputs['ids_office_id']), function ($q) use ($inputs) {
                return $q->whereIn('ids_office_id', $inputs['ids_office_id']);
            })
            ->get();
    }

    public function getYearBalanceForecast($inputs)
    {
        return $this->model->select(\DB::raw("SUM(balance_fee) as service_fee_sum"), \DB::raw("DATE_FORMAT(slot_booked_date, '%Y-%m') month_year"))
            ->groupBy('month_year')
            ->orderBy('month_year')
            ->whereIn('refund_status', [0,3])
            ->where('balance_fee','<', 0)
            ->where('is_online_payment_received',1)
            ->when(isset($inputs) && !empty($inputs['start_date']) && !empty($inputs['end_date']), function ($q) use ($inputs) {
                return $q->whereBetween('slot_booked_date', [$inputs['start_date'], $inputs['end_date']]);
            })
            ->whereIn('is_online_payment_received',[1,2])
            ->when(isset($inputs) && !empty($inputs['ids_office_id']), function ($q) use ($inputs) {
                return $q->whereIn('ids_office_id', $inputs['ids_office_id']);
            })
            ->get();
    }



    /**
     * Forecast Report page, Calculates 12 months sum of fees
     * @param start_dates and ids_office_id
     */

    public function getAnalytics($inputs)
    {
        return $this->model
            ->when(isset($inputs) && !empty($inputs['start_date']) && !empty($inputs['end_date']), function ($q) use ($inputs) {
                return $q->whereBetween('slot_booked_date', [$inputs['start_date'], $inputs['end_date']]);
            })
            ->when(isset($inputs) && !empty($inputs['ids_office_id']), function ($q) use ($inputs) {
                return $q->whereIn('ids_office_id', $inputs['ids_office_id']);
            })
            ->with([
                'IdsOffice' => function ($query) {
                    return $query->select('id', 'name', 'adress');
                },
                'IdsOfficeSlots' => function ($query) {
                    return $query->select('id', 'display_name', 'start_time', 'end_time');
                },
                'idsServicesWithTrashed' => function ($query) {
                    return $query->select('id', 'name', 'short_name');
                },
                'idsPassportPhotoServiceWithTrashed' => function ($query) {
                    return $query->select('id', 'name', 'rate');
                },
                'IdsCustomQuestionAnswers' => function ($query) {
                    return $query->where('ids_custom_questions_id', 10)->select(
                        'id',
                        'ids_entry_id',
                        'ids_custom_questions_id',
                        'ids_custom_questions_str',
                        'ids_custom_option_id',
                        'ids_custom_option_str',
                        'other_value'
                    );
                },
                'idsEntryAmountSplitUp' => function ($query) {
                    return $query->select(
                        'id',
                        'entry_id',
                        'type',
                        'service_id',
                        'rate'
                    );
                },
                // 'IdsPaymentMethods'=>function ($query){
                //     return $query->select('id','short_name','full_name');
                // }

            ])
            ->get();
    }

    public function getTrends($inputs)
    {
        return $this->model
            ->select(\DB::raw("count(id) as total_count"), 'slot_booked_date', 'ids_office_id')
            ->groupBy('slot_booked_date')
            ->groupBy('ids_office_id')
            ->when(isset($inputs) && !empty($inputs['start_date']) && !empty($inputs['end_date']), function ($q) use ($inputs) {
                return $q->whereBetween('slot_booked_date', [$inputs['start_date'], $inputs['end_date']]);
            })
            ->when(isset($inputs) && !empty($inputs['ids_office_id']), function ($q) use ($inputs) {
                return $q->whereIn('ids_office_id', $inputs['ids_office_id']);
            })
            ->with([
                'IdsOffice' => function ($query) {
                    return $query->select('id', 'name', 'adress');
                },
            ])
            ->get();
    }

    /**
     * Generate Revenue Report,Photorevenue Report and cancellde list page data.
     * @param
     */

    public function getReports($inputs)
    {
        return $this->model
            ->when(isset($inputs) && !empty($inputs['start_date']) && !empty($inputs['end_date']), function ($q) use ($inputs) {
                return $q->whereBetween('slot_booked_date', [$inputs['start_date'], $inputs['end_date']]);
            })
            ->when(isset($inputs) && !empty($inputs['ids_office_id']), function ($q) use ($inputs) {
                return $q->whereIn('ids_office_id', $inputs['ids_office_id']);
            })
            ->when(isset($inputs) && !empty($inputs['ids_service_id']), function ($q) use ($inputs) {
                return $q->whereIn('ids_service_id', $inputs['ids_service_id']);
            })
            ->when(isset($inputs) && !empty($inputs['passport_photo_service_id']), function ($q) use ($inputs) {
                return $q->whereIn('passport_photo_service_id', $inputs['passport_photo_service_id']);
            })
            ->when(isset($inputs) && !empty($inputs['ids_payment_method_id']), function ($q) use ($inputs) {
                if(isset($inputs['is_online_payment_received']) && $inputs['is_online_payment_received'] == 1){
                    return $q->where(function ($query) use($inputs){
                        $query->whereIn('ids_payment_method_id', $inputs['ids_payment_method_id'])
                        ->orWhere('is_online_payment_received',1);
                    });
                }else{
                    return $q->whereIn('ids_payment_method_id', $inputs['ids_payment_method_id']);
                }
            })
            ->when(isset($inputs) && !empty($inputs['ids_payment_reason_id']), function ($q) use ($inputs) {
                return $q->whereIn('ids_payment_reason_id', $inputs['ids_payment_reason_id']);
            })
            ->when(isset($inputs) &&
                isset($inputs['is_payment_received'])&&
                !isset($inputs['is_online_payment_received'])
            ,function ($q) use ($inputs) {
                if($inputs['is_payment_received'] == 1) {
                    return $q->where(function ($query) use($inputs){
                        $query->where('is_payment_received',1)->orWhere('is_online_payment_received',1);
                    });
                }else{
                    return $q->where('is_payment_received',0)->where('is_online_payment_received',2);
                }
            })
            ->when(
                isset($inputs) &&
                !empty($inputs['is_client_show_up']) &&
                !isset($inputs['is_online_payment_received'])
            ,function ($q) use ($inputs) {
                // return $q->where('is_client_show_up', $inputs['is_client_show_up']);
                return $q->where(function ($query) use($inputs){
                    $query->where('is_client_show_up',$inputs['is_client_show_up'])
                    ->orWhere('is_online_payment_received',1);
                 });
            })
            ->when(isset($inputs) && !empty($inputs['is_canceled']), function ($q) use ($inputs) {
                 return $q->where('is_canceled', $inputs['is_canceled'])->onlyTrashed();
            })
            ->when(isset($inputs) && isset($inputs['is_passport_photo_service']), function ($q) use ($inputs) {
                if ($inputs['is_passport_photo_service'] <= 1) {
                    return $q->where('passport_photo_service_id',"!=",null);
                }
            })
            ->with([
                'IdsOffice' => function ($query) {
                    return $query->select('id', 'name', 'adress');
                },
                'IdsOfficeSlots' => function ($query) {
                    return $query->select('id', 'display_name', 'start_time', 'end_time');
                },
                'IdsServices' => function ($query) {
                    return $query->select('id', 'name', 'short_name');
                },
                'IdsPaymentMethodsWithTrashed' => function ($query) {
                    return $query->select('id', 'short_name', 'full_name');
                },
                'IdsPaymentReasonsWithTrashed' => function ($query) {
                    return $query->select('id', 'name');
                },
                'UpdatedBy' => function ($query) {
                    return $query->select('id', 'first_name', 'last_name');
                },
                'DeletedBy' => function ($query) {
                    return $query->select('id', 'first_name', 'last_name');
                },'idsOnlinePayment' => function ($query) {
                    return $query->select('id', 'amount', 'payment_intent','entry_id');
                },
                'idsPassportPhotoServiceWithTrashed' => function ($query) {
                    return $query->select('id', 'name', 'rate');
                },
                'idsEntryAmountSplitUp' => function ($query) {
                    return $query->select('id', 'entry_id', 'service_id','type','rate','tax_percentage');
                },

            ])
            ->get();
    }

    public function getCanceledOrDeletedEntries($inputs)
    {
        return $this->model
            ->when(isset($inputs) && !empty($inputs['start_date']) && !empty($inputs['end_date']), function ($q) use ($inputs) {
                return $q->whereBetween('slot_booked_date', [$inputs['start_date'], $inputs['end_date']]);
            })
            ->when(isset($inputs) && !empty($inputs['ids_office_id']), function ($q) use ($inputs) {
                return $q->whereIn('ids_office_id', $inputs['ids_office_id']);
            })
            ->when(isset($inputs) && !empty($inputs['ids_service_id']), function ($q) use ($inputs) {
                return $q->whereIn('ids_service_id', $inputs['ids_service_id']);
            })
            ->when(isset($inputs) && !empty($inputs['passport_photo_service_id']), function ($q) use ($inputs) {
                return $q->whereIn('passport_photo_service_id', $inputs['passport_photo_service_id']);
            })
            ->when(isset($inputs) && !empty($inputs['ids_payment_method_id']), function ($q) use ($inputs) {
                if(isset($inputs['is_online_payment_received']) && $inputs['is_online_payment_received'] == 1){
                    return $q->where(function ($query) use($inputs){
                        $query->whereIn('ids_payment_method_id', $inputs['ids_payment_method_id'])
                        ->orWhere('is_online_payment_received',1);
                    });
                }else{
                    return $q->whereIn('ids_payment_method_id', $inputs['ids_payment_method_id']);
                }
            })
            ->when(isset($inputs) && !empty($inputs['ids_payment_reason_id']), function ($q) use ($inputs) {
                return $q->whereIn('ids_payment_reason_id', $inputs['ids_payment_reason_id']);
            })
            ->when(isset($inputs) &&
                isset($inputs['is_payment_received'])&&
                !isset($inputs['is_online_payment_received'])
            ,function ($q) use ($inputs) {
                if($inputs['is_payment_received'] == 1) {
                    return $q->where(function ($query) use($inputs){
                            $query->where('is_payment_received',1)->orWhere('is_online_payment_received',1);
                    });
                }else{
                    return $q->where('is_payment_received',0)->where('is_online_payment_received',2);
                }
            })
            ->when(
                isset($inputs) &&
                !empty($inputs['is_client_show_up']) &&
                !isset($inputs['is_online_payment_received'])
            ,function ($q) use ($inputs) {
                // return $q->where('is_client_show_up', $inputs['is_client_show_up']);
                return $q->where(function ($query) use($inputs){
                    $query->where('is_client_show_up',$inputs['is_client_show_up'])
                    ->orWhere('is_online_payment_received',1);
                 });
            })
            ->when(isset($inputs) && !empty($inputs['is_canceled']), function ($q) use ($inputs) {
                 return $q->where('is_canceled', $inputs['is_canceled'])->onlyTrashed();
            })
            ->when(isset($inputs) && isset($inputs['is_passport_photo_service']), function ($q) use ($inputs) {
                if ($inputs['is_passport_photo_service'] <= 1) {
                    return $q->where('passport_photo_service_id',"!=",null);
                }
            })
            ->whereNotNull('deleted_by')
            ->whereIn('refund_status',$inputs['refund_status'])
            ->with([
                'IdsOffice' => function ($query) {
                    return $query->select('id', 'name', 'adress');
                },
                'IdsOfficeSlots' => function ($query) {
                    return $query->select('id', 'display_name', 'start_time', 'end_time');
                },
                'IdsServices' => function ($query) {
                    return $query->select('id', 'name', 'short_name');
                },
                'IdsPaymentMethodsWithTrashed' => function ($query) {
                    return $query->select('id', 'short_name', 'full_name');
                },
                'IdsPaymentReasonsWithTrashed' => function ($query) {
                    return $query->select('id', 'name');
                },
                'UpdatedBy' => function ($query) {
                    return $query->select('id', 'first_name', 'last_name');
                },
                'DeletedBy' => function ($query) {
                    return $query->select('id', 'first_name', 'last_name');
                },'idsOnlinePayment' => function ($query) {
                    return $query->select('id', 'amount', 'payment_intent','entry_id');
                },
                'idsPassportPhotoServiceWithTrashed' => function ($query) {
                    return $query->select('id', 'name', 'rate');
                },
                'idsEntryAmountSplitUp' => function ($query) {
                    return $query->select('id', 'entry_id', 'service_id','type','rate','tax_percentage');
                },
            ])
            ->onlyTrashed()
            ->get();
    }

    /**
     * Revenue Forecast Report page, Calculates 12 months sum of fees
     * @param start_dates and ids_office_id
     */

    public function getYearRevenue($inputs)
    {
        return $this->model->select(\DB::raw("SUM(given_rate) as service_fee_sum"), \DB::raw("DATE_FORMAT(slot_booked_date, '%Y-%m') month_year"))
            ->groupBy('month_year')
            ->orderBy('month_year')
            ->when(isset($inputs) && !empty($inputs['ids_office_id']), function ($q) use ($inputs) {
                return $q->whereIn('ids_office_id', $inputs['ids_office_id']);
            })
            ->when(isset($inputs) && !empty($inputs['ids_service_id']), function ($q) use ($inputs) {
                return $q->whereIn('ids_service_id', $inputs['ids_service_id']);
            })
            ->when(isset($inputs) && !empty($inputs['passport_photo_service_id']), function ($q) use ($inputs) {
                return $q->whereIn('passport_photo_service_id', $inputs['passport_photo_service_id']);
            })
            ->when(isset($inputs) && !empty($inputs['ids_payment_method_id']), function ($q) use ($inputs) {
                if(isset($inputs['is_online_payment_received']) && $inputs['is_online_payment_received'] == 1){
                    return $q->where(function ($query) use($inputs){
                        $query->whereIn('ids_payment_method_id', $inputs['ids_payment_method_id'])
                        ->orWhere('is_online_payment_received',1);
                    });
                }else{
                    return $q->whereIn('ids_payment_method_id', $inputs['ids_payment_method_id']);
                }
            })
            ->when(isset($inputs) && !empty($inputs['ids_payment_reason_id']), function ($q) use ($inputs) {
                return $q->whereIn('ids_payment_reason_id', $inputs['ids_payment_reason_id']);
            })
            ->when(isset($inputs) &&
                isset($inputs['is_payment_received'])&&
                !isset($inputs['is_online_payment_received'])
            ,function ($q) use ($inputs) {
                if($inputs['is_payment_received'] == 1) {
                    return $q->where(function ($query) use($inputs){
                        $query->where('is_payment_received',1)->orWhere('is_online_payment_received',1);
                    });
                }else{
                    return $q->where('is_payment_received',0)->where('is_online_payment_received',2);
                }
            })
            ->when(
                isset($inputs) &&
                !empty($inputs['is_client_show_up']) &&
                !isset($inputs['is_online_payment_received'])
            ,function ($q) use ($inputs) {
                // return $q->where('is_client_show_up', $inputs['is_client_show_up']);
                return $q->where(function ($query) use($inputs){
                    $query->where('is_client_show_up',$inputs['is_client_show_up'])
                    ->orWhere('is_online_payment_received',1);
                 });
            })
            ->when(isset($inputs) && !empty($inputs['is_canceled']), function ($q) use ($inputs) {
                 return $q->where('is_canceled', $inputs['is_canceled'])->onlyTrashed();
            })
            ->when(isset($inputs) && isset($inputs['is_passport_photo_service']), function ($q) use ($inputs) {
                if ($inputs['is_passport_photo_service'] <= 1) {
                    return $q->where('passport_photo_service_id',"!=",null);
                }
            })
            ->get();
    }

    public function getYearBalanceRevenue($inputs)
    {
        return $this->model->select(\DB::raw("SUM(balance_fee) as service_fee_sum"), \DB::raw("DATE_FORMAT(slot_booked_date, '%Y-%m') month_year"))
            ->groupBy('month_year')
            ->orderBy('month_year')
            ->whereIn('refund_status', [0,3])
            ->where('balance_fee','<', 0)
            ->where('is_online_payment_received',1)
            ->when(isset($inputs) && !empty($inputs['ids_office_id']), function ($q) use ($inputs) {
                return $q->whereIn('ids_office_id', $inputs['ids_office_id']);
            })
            ->when(isset($inputs) && !empty($inputs['ids_service_id']), function ($q) use ($inputs) {
                return $q->whereIn('ids_service_id', $inputs['ids_service_id']);
            })
            ->when(isset($inputs) && !empty($inputs['passport_photo_service_id']), function ($q) use ($inputs) {
                return $q->whereIn('passport_photo_service_id', $inputs['passport_photo_service_id']);
            })
            ->when(isset($inputs) && !empty($inputs['ids_payment_method_id']), function ($q) use ($inputs) {
                if(isset($inputs['is_online_payment_received']) && $inputs['is_online_payment_received'] == 1){
                    return $q->where(function ($query) use($inputs){
                        $query->whereIn('ids_payment_method_id', $inputs['ids_payment_method_id'])
                        ->orWhere('is_online_payment_received',1);
                    });
                }else{
                    return $q->whereIn('ids_payment_method_id', $inputs['ids_payment_method_id']);
                }
            })
            ->when(isset($inputs) && !empty($inputs['ids_payment_reason_id']), function ($q) use ($inputs) {
                return $q->whereIn('ids_payment_reason_id', $inputs['ids_payment_reason_id']);
            })
            ->when(isset($inputs) &&
                isset($inputs['is_payment_received'])&&
                !isset($inputs['is_online_payment_received'])
            ,function ($q) use ($inputs) {
                if($inputs['is_payment_received'] == 1) {
                    return $q->where(function ($query) use($inputs){
                        $query->where('is_payment_received',1)->orWhere('is_online_payment_received',1);
                    });
                }else{
                    return $q->where('is_payment_received',0)->where('is_online_payment_received',2);
                }
            })
            // ->when(
            //     isset($inputs) &&
            //     !empty($inputs['is_client_show_up']) &&
            //     !isset($inputs['is_online_payment_received'])
            // ,function ($q) use ($inputs) {
            //     // return $q->where('is_client_show_up', $inputs['is_client_show_up']);
            //     return $q->where(function ($query) use($inputs){
            //         $query->where('is_client_show_up',$inputs['is_client_show_up'])
            //         ->orWhere('is_online_payment_received',1);
            //      });
            // })
            ->when(isset($inputs) && isset($inputs['is_passport_photo_service']), function ($q) use ($inputs) {
                if ($inputs['is_passport_photo_service'] <= 1) {
                    return $q->where('passport_photo_service_id',"!=",null);
                }
            })
            ->withTrashed()
            ->get();
    }

    public function getBookedEntries($inputs)
    {
        return $this->model
            ->whereIn('slot_booked_date', $inputs['date'])
            ->select(
                'id',
                'ids_office_slot_id',
                'slot_booked_date',
                'to_be_rescheduled',
                'ids_service_id',
                'first_name',
                'last_name',
                'is_candidate',
                'passport_photo_service_id',
                'is_online_payment_received'
            )
            ->with(['IdsServices' => function ($query) {
                $query->select('id', 'name');
            }])
            ->get();
    }

    public function getEntryById($id)
    {
        return $this->model
            ->where('id', $id)
            ->select(
                'id',
                'ids_office_slot_id',
                'slot_booked_date',
                'first_name',
                'last_name',
                'email',
                'phone_number',
                'given_rate',
                'given_interval',
                'to_be_rescheduled',
                'ids_office_id',
                'ids_service_id',
                'is_client_show_up',
                'ids_payment_method_id',
                'is_mask_given',
                'no_masks_given',
                'is_payment_received',
                'payment_reason',
                'ids_payment_reason_id',
                'updated_by',
                'notes',
                'cancelled_booking_id',
                'is_candidate',
                'candidate_requisition_no',
                'is_federal_billing',
                'federal_billing_employer',
                'passport_photo_service_id',
                'refund_status',
                'refund_initiated_by',
                'refund_initiated_date',
                'refund_completed_by',
                'refund_completed_date'
            )

            ->with([
                'IdsServices' => function ($query) {
                    return $query->select('id', 'name','rate','tax_master_id',
                    'is_photo_service','is_photo_service_required');
                },
               'IdsServices.taxMaster'=> function($query){
                    return $query->select('id','name','short_name');
                },
                'IdsServices.taxMaster.taxMasterLog'=> function($query){
                    return $query->select('id','tax_master_id','tax_percentage',
                    'effective_from_date','effective_end_date');
                },
                'idsEntryAmountSplitUp'=>function($query){
                    return $query->select(
                        "id",
                        'type',
                        'entry_id',
                        'service_id',
                        'rate',
                        'tax_percentage'
                    );
                },
                'idsOnlinePayment'=>function($query){
                    return $query->select(
                        "id",
                        "entry_id",
                        "amount",
                        "transaction_id",
                        "payment_intent",
                        "email",
                        "status",
                        "started_time",
                        "end_time",
                        "entry_id_updated_at"
                    );
                },
                'idsTransactionHistory' => function ($query) {
                    return $query->select(
                        'id',
                        'entry_id',
                        'ids_online_payment_id',
                        'ids_payment_method_id',
                        'amount',
                        'transaction_type',
                        'user_id',
                        'refund_note',
                        'refund_status',
                        'online_refund_id',
                        'created_at'
                    );
                },
                'idsTransactionHistory.user' => function ($query) {
                    return $query->select('id', 'first_name', 'last_name');
                },
                'idsTransactionHistory.refund' => function ($query) {
                    return $query->select('id', 'ids_online_refund_id', 'refund_end_time','refund_start_time');
                },
                'idsTransactionHistory.idsPaymentMethod'=> function ($query) {
                    return $query->select('id', 'short_name', 'full_name');
                },
                'cancelledBooking'=>function($query){
                    return $query->select(
                        "id",
                        "email",
                        "phone_number",
                        "slot_booked_date",
                        "cancelled_booking_id",
                        'ids_office_slot_id',
                        'slot_booked_date',
                        'ids_office_id'
                    );
                },
                'cancelledBooking.IdsOffice'=>function($query){
                    return $query->select("id","name");
                },
                'IdsCustomQuestionAnswers' => function ($query) {
                    $query->select(
                        'id',
                        'ids_entry_id',
                        'ids_custom_questions_id',
                        'ids_custom_option_id',
                        'ids_custom_questions_str',
                        'ids_custom_option_str',
                        'other_value'
                    );
                },
                'refundInitiatedBy' => function ($query) {
                    return $query->select('id', 'first_name', 'last_name');
                },
                'refundCompletedBy' => function ($query) {
                    return $query->select('id', 'first_name', 'last_name');
                },
            ])
            ->first();
    }

    /**
     * Revenue Forecast Report page, Calculates 12 months sum of fees
     * @param start_dates and ids_office_id
     */

    public function getAppointmentGeoMapData($inputs)
    {
        return $this->model->with(['IdsOffice', 'IdsServices', 'IdsOfficeSlots', 'IdsPaymentMethods', 'IdsPaymentReasons'])
            ->where('postal_code', '!=', null)->where('latitude', '!=', null)->where('longitude', '!=', null)
            ->when(isset($inputs) && !empty($inputs['start_date']) && !empty($inputs['end_date']), function ($q) use ($inputs) {
                return $q->whereBetween('slot_booked_date', [$inputs['start_date'], $inputs['end_date']]);
            })
            ->when(isset($inputs) && !empty($inputs['ids_office_id']), function ($q) use ($inputs) {
                return $q->whereIn('ids_office_id', $inputs['ids_office_id']);
            })
            ->when(isset($inputs) && !empty($inputs['ids_service_id']), function ($q) use ($inputs) {
                return $q->whereIn('ids_service_id', $inputs['ids_service_id']);
            })
            ->when(isset($inputs) && !empty($inputs['ids_payment_method_id']), function ($q) use ($inputs) {
                return $q->whereIn('ids_payment_method_id', $inputs['ids_payment_method_id']);
            })
            ->when(isset($inputs) && !empty($inputs['ids_payment_reason_id']), function ($q) use ($inputs) {
                return $q->whereIn('ids_payment_reason_id', $inputs['ids_payment_reason_id']);
            })
            ->when(isset($inputs) && !empty($inputs['client_show_up']), function ($q) use ($inputs) {
                $flag = ($inputs['client_show_up'] == 2) ? 0 : 1;
                return $q->where('is_client_show_up', $flag);
            })
            ->when(isset($inputs) && isset($inputs['is_payment_received']), function ($q) use ($inputs) {
                if ($inputs['is_payment_received'] <= 1) {
                    return $q->where('is_payment_received', $inputs['is_payment_received']);
                }
            })
            ->get();
    }

    public function lastCancelledEntry($inputs){

        return $this->model
        ->orderBy('id','DESC')
        ->where(function($que) use($inputs){
            return $que->where('is_canceled',1)->where('email', $inputs['email'])
            ->orWhere('is_client_show_up',0)->where('email', $inputs['email']);
        })
        ->orWhere(function($que) use($inputs){
            return $que->where('is_canceled',1)->where('phone_number', $inputs['phone_number'])
            ->orWhere('is_client_show_up',0)->where('phone_number', $inputs['phone_number']);
        })
        ->withTrashed()
        ->select("id","first_name","last_name","email","phone_number","ids_office_id",
        "ids_office_slot_id","slot_booked_date","is_client_show_up","is_canceled","deleted_at")
        ->with(['IdsOfficeSlots'=>function($query){
            return $query->select("id","ids_office_timing_id","display_name","start_time","end_time");
        },
        'isCancelledBooking'=>function($query){
            return $query->select("id","first_name","last_name","email","phone_number",
            "slot_booked_date","cancelled_booking_id");
        },
        ])
        ->first();
    }
    /**
     * Generate refund list
     * @param
     */

    public function getRefundList($inputs)
    {
        return $this->model
            ->where('refund_status','>=',1)
            ->orderBy('refund_initiated_date','DESC')
            ->select(
                'id',
                'ids_office_slot_id',
                'ids_office_id',
                'ids_service_id',
                'passport_photo_service_id',
                'slot_booked_date',
                'first_name',
                'last_name',
                'email',
                'phone_number',
                'postal_code',
                'is_client_show_up',
                'given_rate',
                'balance_fee',
                'is_online_payment_received',
                'refund_status',
                'refund_initiated_by',
                'refund_initiated_date',
                'refund_completed_by',
                'refund_completed_date'
            )
            ->when(isset($inputs) && !empty($inputs['start_date']) && !empty($inputs['end_date']), function ($q) use ($inputs) {
                return $q->whereBetween('slot_booked_date', [$inputs['start_date'], $inputs['end_date']]);
            })
            ->when(isset($inputs) && !empty($inputs['ids_office_id']), function ($q) use ($inputs) {
                return $q->whereIn('ids_office_id', $inputs['ids_office_id']);
            })
            ->when(isset($inputs) && !empty($inputs['ids_service_id']), function ($q) use ($inputs) {
                return $q->whereIn('ids_service_id', $inputs['ids_service_id']);
            })
            ->when(isset($inputs) && !empty($inputs['refund_status']), function ($q) use ($inputs) {
                return $q->whereIn('refund_status', $inputs['refund_status']);
            })
            ->with([
                'IdsOffice' => function ($query) {
                    return $query->select('id', 'name', 'adress');
                },
                'IdsOfficeSlots' => function ($query) {
                    return $query->select('id', 'display_name', 'start_time', 'end_time');
                },
                'IdsServicesWithTrashed' => function ($query) {
                    return $query->select('id', 'name', 'short_name');
                },
                // 'IdsPaymentMethodsWithTrashed' => function ($query) {
                //     return $query->select('id', 'short_name', 'full_name');
                // },
                // 'IdsPaymentReasonsWithTrashed' => function ($query) {
                //     return $query->select('id', 'name');
                // },
                // 'UpdatedBy' => function ($query) {
                //     return $query->select('id', 'first_name', 'last_name');
                // },
                'refundInitiatedBy' => function ($query) {
                    return $query->select('id', 'first_name', 'last_name');
                },
                'refundCompletedBy' => function ($query) {
                    return $query->select('id', 'first_name', 'last_name');
                },
                'idsOnlinePayment' => function ($query) {
                    return $query->select('id','entry_id',
                    'amount','payment_intent','email',
                    'started_time','end_time');
                },
                'idsPassportPhotoServiceWithTrashed' => function ($query) {
                    return $query->select('id', 'name', 'rate');
                },
                'idsTransactionHistory' => function ($query) {
                    return $query->select(
                        'id',
                        'entry_id',
                        'amount',
                        'transaction_type',
                        'user_id',
                        'ids_online_payment_id',
                        'ids_payment_method_id',
                        'refund_note',
                        'refund_status',
                        'created_at'

                    );
                },
                'idsTransactionHistory.user' => function ($query) {
                    return $query->select('id', 'first_name', 'last_name');
                },
                'idsOnlineRefund' => function ($query) {
                    return $query->select('id', 'entry_id', 'ids_online_refund_id','refund_status');
                },
                'idsTransactionHistory.idsPaymentMethod'=> function ($query) {
                    return $query->select('id', 'short_name', 'full_name');
                },
            ])
            ->withTrashed()
            ->get();
    }

    public function getByIdWithTrashed($id)
    {
        return $this->model
        ->with([
            'IdsOffice' => function ($query) {
                return $query->select('id', 'name', 'adress','phone_number','phone_number_ext');
            },
            'IdsOfficeSlots' => function ($query) {
                return $query->select('id', 'display_name', 'start_time', 'end_time');
            },
            'idsServicesWithTrashed' => function ($query) {
                return $query->select('id', 'name', 'short_name');
            },
            'idsPassportPhotoServiceWithTrashed' => function ($query) {
                return $query->select('id', 'name', 'rate');
            },
            'idsTransactionHistory' => function ($query) {
                return $query->select('id', 'entry_id', 'ids_online_payment_id','ids_payment_method_id',
                'amount','transaction_type','user_id','refund_note','refund_status', 'created_at','online_refund_id');
            },
            'idsTransactionHistory.refund' => function ($query) {
                return $query->select('id', 'ids_online_refund_id', 'refund_end_time','refund_start_time');
            },
            'idsTransactionHistory.user' => function ($query) {
                return $query->select('id', 'first_name', 'last_name');
            },
            'idsTransactionHistory.idsPaymentMethod'=> function ($query) {
                return $query->select('id', 'short_name', 'full_name');
            },
            'refundInitiatedBy' => function ($query) {
                return $query->select('id', 'first_name', 'last_name');
            },
            'refundCompletedBy' => function ($query) {
                return $query->select('id', 'first_name', 'last_name');
            },
            'idsOnlinePayment' => function ($query) {
                return $query->select('id', 'entry_id', 'payment_intent','amount','email','started_time','end_time');
            }
        ])
        ->withTrashed()
        ->find($id);
    }

    public function getOfficeRevenue($inputs)
    {
        $result = $this->idsEntryAmountSplitUpRepository->getSplitUps($inputs);

        $result['totalFee'] = $this->model->select(\DB::raw("SUM(given_rate) as totalFee"),\DB::raw("SUM(online_processing_fee) as online_processing_fee"),'ids_office_id')
            ->groupBy('ids_office_id')
            ->when(isset($inputs) && !empty($inputs['start_date']) && !empty($inputs['end_date']), function ($q) use ($inputs) {
                return $q->whereBetween('slot_booked_date', [$inputs['start_date'], $inputs['end_date']]);
            })
            ->when(isset($inputs) && !empty($inputs['ids_office_id']), function ($q) use ($inputs) {
                return $q->whereIn('ids_office_id', $inputs['ids_office_id']);
            })
            ->where(function ($query) use($inputs){
                $query->where('is_client_show_up',1)->orWhere('is_online_payment_received',1);
             })
            ->get();

        $result['refunds'] = $this->model->select(\DB::raw("SUM(balance_fee) as balanceFee"),'ids_office_id','refund_status','ids_payment_reason_id')
            ->groupBy('ids_office_id','refund_status','ids_payment_reason_id')
            ->whereIn('refund_status', [0,1,3,2])
            ->where('balance_fee','<', 0)
            ->where('is_online_payment_received',1)
            // ->whereNull('ids_payment_reason_id')
            ->when(isset($inputs) && !empty($inputs['start_date']) && !empty($inputs['end_date']), function ($q) use ($inputs) {
                return $q->whereBetween('slot_booked_date', [$inputs['start_date'], $inputs['end_date']]);
            })
            ->when(isset($inputs) && !empty($inputs['ids_office_id']), function ($q) use ($inputs) {
                return $q->whereIn('ids_office_id', $inputs['ids_office_id']);
            })
            ->withTrashed()
            ->get();

        //  $result['deferredBilling'] = $this->model->select(\DB::raw("SUM(given_rate) as totalFee"),\DB::raw("SUM(balance_fee) as balanceFee"),'ids_office_id')
        //     ->groupBy('ids_office_id')
        //     ->when(isset($inputs) && !empty($inputs['start_date']) && !empty($inputs['end_date']), function ($q) use ($inputs) {
        //         return $q->whereBetween('slot_booked_date', [$inputs['start_date'], $inputs['end_date']]);
        //     })
        //     ->when(isset($inputs) && !empty($inputs['ids_office_id']), function ($q) use ($inputs) {
        //         return $q->whereIn('ids_office_id', $inputs['ids_office_id']);
        //     })
        //     ->where(function ($query) use($inputs){
        //         $query->where('is_client_show_up',1)->orWhere('is_online_payment_received',1);
        //      })
        //     ->where(function ($query) use($inputs){
        //         $query->where('is_candidate',1)->orWhere('is_federal_billing',1);
        //      })
        //     ->get();
        return $result;
    }



}
