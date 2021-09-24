<?php

namespace Modules\IdsScheduling\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IdsEntries extends Model
{
    use SoftDeletes;

    protected $fillable = ['first_name', 'last_name', 'email', 'phone_number', 'ids_office_id', 'ids_recommend_office_id',
        'ids_service_id','passport_photo_service_id', 'ids_office_slot_id', 'slot_booked_date', 'postal_code',
         'given_interval', 'given_rate','balance_fee','online_processing_fee','is_client_show_up', 'ids_payment_reason_id',
        'ids_payment_method_id', 'is_payment_received','payment_reason', 'is_mask_given', 'no_masks_given',
        'to_be_rescheduled','is_rescheduled', 'rescheduled_at',
        'rescheduled_id', 'rescheduled_by', 'updated_by', 'notes', 'is_canceled', 'deleted_by', 'deleted_at',
        'latitude', 'longitude','cancelled_booking_id','is_candidate','is_federal_billing',
        'candidate_requisition_no','federal_billing_employer','is_online_payment_received',
        'refund_initiated_by','refund_initiated_date','refund_completed_by','refund_completed_date','refund_status'];

    public function IdsServices()
    {
        return $this->belongsTo('Modules\Admin\Models\IdsServices', 'ids_service_id')->withTrashed();
    }
    public function IdsServicesWithTrashed()
    {
        return $this->belongsTo('Modules\Admin\Models\IdsServices', 'ids_service_id')->withTrashed();
    }
    public function IdsPaymentReasons()
    {
        return $this->belongsTo('Modules\Admin\Models\IdsPaymentReasons', 'ids_payment_reason_id');
    }
    public function IdsPaymentReasonsWithTrashed()
    {
        return $this->belongsTo('Modules\Admin\Models\IdsPaymentReasons', 'ids_payment_reason_id')->withTrashed();
    }
    public function IdsPaymentMethods()
    {
        return $this->belongsTo('Modules\IdsScheduling\Models\IdsPaymentMethods', 'ids_payment_method_id');
    }
    public function IdsPaymentMethodsWithTrashed()
    {
        return $this->belongsTo('Modules\IdsScheduling\Models\IdsPaymentMethods', 'ids_payment_method_id')->withTrashed();
    }
    public function IdsCustomQuestionAnswers()
    {
        return $this->hasMany(
            'Modules\Admin\Models\IdsCustomQuestionAnswer',
            'ids_entry_id',
            'id'
        );
    }
    public function IdsOffice()
    {
        return $this->belongsTo('Modules\Admin\Models\IdsOffice', 'ids_office_id')->withTrashed();
    }
    public function IdsOfficeSlots()
    {
        return $this->belongsTo('Modules\Admin\Models\IdsOfficeSlots', 'ids_office_slot_id')->withTrashed();
    }
    public function UpdatedBy()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'updated_by')->withTrashed();
    }
    public function DeletedBy()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'deleted_by')->withTrashed();
    }
    public function cancelledBooking()
    {
        return $this->belongsTo('Modules\IdsScheduling\Models\IdsEntries', 'cancelled_booking_id')->withTrashed();
    }
    public function isCancelledBooking()
    {
        return $this->hasOne('Modules\IdsScheduling\Models\IdsEntries', 'cancelled_booking_id')->withTrashed();
    }
    public function rescheduledBooking()
    {
        return $this->belongsTo('Modules\IdsScheduling\Models\IdsEntries', 'rescheduled_id')->withTrashed();
    }
    public function idsEntryAmountSplitUp()
    {
        return $this->hasMany('Modules\IdsScheduling\Models\IdsEntryAmountSplitUp', 'entry_id');
    }
    public function idsPassportPhotoService(){
        return $this->belongsTo('Modules\Admin\Models\IdsPassportPhotoService', 'passport_photo_service_id')->withTrashed();
    }
    public function idsPassportPhotoServiceWithTrashed(){
        return $this->belongsTo('Modules\Admin\Models\IdsPassportPhotoService', 'passport_photo_service_id')->withTrashed();
    }
    public function idsOnlinePayments()
    {
        return $this->hasMany('Modules\IdsScheduling\Models\IdsOnlinePayment', 'entry_id');
    }
    public function idsOnlinePayment()
    {
        return $this->hasOne('Modules\IdsScheduling\Models\IdsOnlinePayment', 'entry_id')->where('status',1);
    }
    public function idsTransactionHistory()
    {
        return $this->hasMany('Modules\IdsScheduling\Models\IdsTransactionHistory', 'entry_id');
    }
    public function refundInitiatedBy()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'refund_initiated_by')->withTrashed();
    }
    public function refundCompletedBy()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'refund_completed_by')->withTrashed();
    }
}
