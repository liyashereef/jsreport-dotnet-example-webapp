<?php

namespace Modules\Uniform\Repositories;

use App\Repositories\MailQueueRepository;
use Modules\Admin\Models\User;
use Modules\Uniform\Utils\OrderStatusType;

class UniformEmailRepository
{
    protected $mailQueueRepository;

    public function __construct(MailQueueRepository $mailQueueRepository)
    {
        $this->mailQueueRepository = $mailQueueRepository;
    }

    public function sendNotificationEmail($order)
    {
       // $orderStatusMachineCode = $order->currentStatus()->machine_code;
       $orderStatusMachineCode = $order['machine_code'];

        switch ($orderStatusMachineCode) {
            case OrderStatusType::ORDER_RECEIVED:
                $this->sendOrderedEmail($order,'uniform_order_received');
                break;
            case OrderStatusType::SHIPPED:
                $this->sendOrderedEmail($order,'uniform_order_shipped');
                break;
            case OrderStatusType::CANCELLED:
                $this->sendOrderedEmail($order,'uniform_order_cancelled');
                break;
            case OrderStatusType::DELIVERED:
                $this->sendOrderedEmail($order,'uniform_order_delivered');
                break;
            case OrderStatusType::RETURNED:
                $this->sendOrderedEmail($order,'uniform_order_returned');
                break;
        }
    }

    public function sendOrderedEmail($order, $template)
    {
        ///code
        $user = User::where('id', $order['id'])->first();
        $to   = $user->email;
        $helper_variable = [
            '{receiverFullName}' => $user->first_name.' '.$user->last_name,
            '{loggedInUserEmployeeNumber}' => \Auth::user()->employee->employee_no,
            '{loggedInUser}' => \Auth::user()->getFullNameAttribute(),
        ];

        //send email
        $this
        ->mailQueueRepository
        ->prepareMailTemplate(
            $template,
            0,
            $helper_variable,
            UniformOrder::class,
            $requestor = 0,
            $assignee = 0,
            $from = null,
            $cc = null,
            $bcc = null,
            $mail_time = null,
            $created_by = null,
            $attachment_id = null,
            $to
        );
    }
}
