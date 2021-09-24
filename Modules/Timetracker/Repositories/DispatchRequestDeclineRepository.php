<?php


namespace Modules\Timetracker\Repositories;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Timetracker\Models\DispatchRequestDecline;

class DispatchRequestDeclineRepository
{
    protected  $dispatchRequestDecline;

    public function __construct(DispatchRequestDecline $dispatchRequestDecline)
    {
        $this->model = $dispatchRequestDecline;
    }

    /**
     * Store decline request
     * @param $dispatch_request_id, $user_id, $comment
     *
     * @return
     */
    public function store($inputs){

        $inputs['user_id'] = Auth::id();
//        $inputs['user_id'] = 1;

        try {
            DB::beginTransaction();

            $decline = $this->model->create($inputs);

            if ($decline) {
                $content['success'] = true;
                $content['data'] = $decline;
                $content['message'] = 'Decline successfully updated';
                $content['code'] = 200;
            } else {
                $content['success'] = false;
                $content['message'] = 'Decline not updated. Please try again.';
                $content['code'] = 406;
            }

            DB::commit();

        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }

        return response()->json(['content' => $content], $content['code']);
    }

    public function getByRequestId($dispatch_request_id){
        return $this->model->where('dispatch_request_id',$dispatch_request_id)
            ->get()->load('dispatch_request','user');
    }


}
