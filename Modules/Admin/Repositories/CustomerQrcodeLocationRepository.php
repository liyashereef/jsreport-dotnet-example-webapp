<?php

namespace Modules\Admin\Repositories;

use Auth;
use Modules\Admin\Models\CustomerQrcodeLocation;

class CustomerQrcodeLocationRepository
{
    protected $customerQrcodeLocation;

    public function __construct(CustomerQrcodeLocation $customerQrcodeLocation)
    {
        $this->customerQrcodeLocation = $customerQrcodeLocation;
    }

    /**
     * Get employee rating lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll($id)
    {
        return $this->customerQrcodeLocation->select(['id', 'qrcode', 'location', 'no_of_attempts', 'no_of_attempts_week_ends', 'tot_no_of_attempts_week_day', 'tot_no_of_attempts_week_ends', 'created_at', 'updated_at'])->where('customer_id', $id)->get();
    }

    public function save($data)
    {
        $qrCode_data = [
            'qrcode' => $data['qrcode'],
            'location' => $data['location'],
            'no_of_attempts' => $data['no_of_attempts'],
            'no_of_attempts_week_ends' => $data['no_of_attempts_week_ends'],
            'tot_no_of_attempts_week_day' => $data['tot_no_of_attempts_week_day'],
            'tot_no_of_attempts_week_ends' => $data['tot_no_of_attempts_week_ends'],
            'picture_enable_disable' => $data['picture_enable_disable'],
            'picture_mandatory' => $data['picture_enable_disable'] == 1 ? $data['picture_mandatory'] == null ? 0 : $data['picture_mandatory'] : 0,
            'location_enable_disable' => $data['location_enable_disable'],
            'qrcode_active' => $data['qrcode_active'],
        ];
        $qrCode_data['customer_id'] = $data['customerid'];
        $qrCode_data['created_by'] = Auth::user()->id;
        if (empty($data['qrcodeid'])) {
            $qrCode_data['qrcode_active'] = true;
        }
        CustomerQrcodeLocation::updateOrCreate(array('id' => $data['qrcodeid']), $qrCode_data);
        // CustomerQrcodeLocation::updateOrCreate([
        //     'customer_id' => $customerid,
        //     'qrcode' => $qrcode,
        //     'location' => $location,
        //     'no_of_attempts' => $no_of_attempts,
        //     'picture_enable_disable' => $picture_enable_disable,
        //     'picture_mandatory' => $picture_mandatory,
        //     'location_enable_disable' => $location_enable_disable,
        //     'qrcode_active' => $qrcode_active,

        // ], $qrCode_data);
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->customerQrcodeLocation->find($id);
    }
    /**
     * Remove the specified Security Clearance from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->customerQrcodeLocation->destroy($id);
    }

}
