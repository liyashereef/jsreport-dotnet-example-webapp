<?php

namespace Modules\Uniform\Repositories;

use Modules\Uniform\Models\UniformOrder;
use App\Repositories\MailQueueRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Uniform\Models\UniformOrderItem;
use Modules\Uniform\Models\UniformOrderStatus;
use Modules\Uniform\Models\UniformOrderStatusLog;
use Modules\Uniform\Utils\OrderStatusType;
use Modules\Admin\Models\EmailTemplate;
use Modules\Admin\Models\EmailNotificationType;

class UniformOrderRepository
{
    protected $model;
    protected $mailQueueRepository;
    protected $uniformOrderItemRepository;
    protected $uniformOrderStatusLog;
    protected $uniformOrderStatus;
    protected $uniformProductRepository;
    protected $uraTransactionRepository;

    public function __construct(
        UniformOrder $uniformOrder,
        UniformOrderItem $uniformOrderItem,
        MailQueueRepository $mailQueueRepository,
        UniformOrderItemRepository $uniformOrderItemRepository,
        UniformEmailRepository $uniformEmailRepository,
        UniformOrderStatusLog $uniformOrderStatusLog,
        UniformOrderStatus $uniformOrderStatus,
        UniformProductRepository $uniformProductRepository,
        UraTransactionRepository $uraTransactionRepository,
        EmailTemplate $emailTemplate
    ) {
        $this->model = $uniformOrder;
        $this->uniformProductRepository = $uniformProductRepository;
        $this->uniformOrderItem = $uniformOrderItem;
        $this->mailQueueRepository = $mailQueueRepository;
        $this->uniformOrderItemRepository = $uniformOrderItemRepository;
        $this->uniformEmailRepository = $uniformEmailRepository;
        $this->uniformOrderStatusLog = $uniformOrderStatusLog;
        $this->uniformOrderStatus = $uniformOrderStatus;
        $this->emailTemplate = $emailTemplate;
        $this->uraTransactionRepository = $uraTransactionRepository;
    }


    public function storeUniformOrder($data)
    {
        $userId = $data['user_id'];
        $orderDetails = $data['orderDetails'];
        $addr = $data['billingAddress'];

        $uniformOrders = [];
        $orderUpdateArr = [];
        $orderData = [];
        $totalPrice = 0;

        $uniformOrder = $this->model->create(['user_id' => $userId]);
        foreach ($orderDetails as $k => $order) {
            $itemId = isset($order['itemId']) ? $order['itemId'] : null;
            $uniformDetails = $this->uniformProductRepository->getProduct($itemId);
            if (!empty($uniformDetails)) {
                $productPrice = isset($order['quantity']) ? ($uniformDetails->selling_price) * ($order['quantity']) : '';
                if (!isset($uniformOrders[$k])) {
                    $uniformOrders['site_id'] =  $order['projectId'];
                }

                $product =  $this->uniformProductRepository->getProduct($order['itemId']);
                $tax = $product->taxMasterLog;
                $taxAmount = ($productPrice * $tax->tax_percentage) / 100;

                $orderData = [
                    'item_price' => $productPrice,
                    'quantity' => $order['quantity'],
                    'uniform_product_id' => $order['itemId'],
                    'uniform_product_variant_id' => $order['sizeId'],
                    'uniform_order_id' => $uniformOrder->id,
                    'tax_id' => $tax->id,
                    'tax_rate' => $tax->tax_percentage,
                    'tax_amount' => $taxAmount,
                    'unit_price' => $product->selling_price,
                    'total_price_with_tax' => ($productPrice + $taxAmount)
                ];
                $this->uniformOrderItem->create($orderData);

                //Price
                $totalPrice +=  $taxAmount;
                $totalPrice +=  $productPrice;
            }
        }


        $orderUpdateArr = [
            'site_id' => $uniformOrders['site_id'],
            'shipping_address_1' => $addr['address'],
            'shipping_city' => $addr['city'],
            'shipping_province' => $addr['province'],
            'shipping_postal_code' => $addr['postalCode'],
            'price' => $totalPrice,
        ];

        $store = $this->model->where('id', $uniformOrder->id)->update($orderUpdateArr);

        if (!$this->uraTransactionRepository->canPurchaseUniform($uniformOrder->user_id, $totalPrice)) {
            $uniformOrder->orderItems()->delete();
            $uniformOrder->delete();
            return ["success" => false, "message" => "Sorry, You have exceeded the purchase limit. Please remove some items from the cart to continue with your purchase.", 'code' => 400];
        }

        if ($store) {
            //Ura Purchase decuction
            $order = $this->model->find($uniformOrder->id);
            $amount = $this->uraTransactionRepository->processUniformPurchase($order);
            $order->ura_deducted = $amount;
            $order->save();
            return ["success" => true, "message" => "Order placed successfully", 'code' => 200];
        } else {
            return ["success" => false, "message" => "Failed to place the order. Try again", 'code' => 400];
        }
    }

    public function getList()
    {
        $uniformOrderLog = $this->model->with([
            'site',
            'user',
            'statusLog',
            'statusLog.orderStatus',
            'statusLog.createdBy',
            'updatedBy'
        ])->get();
        return $this->prepareDataForUniformList($uniformOrderLog);
    }

    public function prepareDataForUniformList($uniformOrderLog)
    {
        $datatable_rows = array();
        foreach ($uniformOrderLog as $key => $each_list) {
            $each_row["id"] = isset($each_list->id) ? $each_list->id : "--";
            $each_row["employee_name_no"] = isset($each_list->user_id) ? $each_list->user->name_with_emp_no  : "--";
            $each_row["site_name_no"] = isset($each_list->site_id) ? $each_list->site->client_name_and_number  : "--";
            $each_row["total_cost"] = isset($each_list->price) ? "$" . $each_list->price  : "--";
            $each_row["shipping_address"] = isset($each_list->shipping_address_1) ? $each_list->shipping_address_1 . ", " . $each_list->shipping_city . ", " . $each_list->shipping_province . ", " . $each_list->shipping_postal_code  : "--";
            $each_row["ura_deducted"] = $each_list->ura_deducted;
            $each_row_status_log = array();

            if (!empty($each_list->statusLog)) {
                foreach ($each_list->statusLog as $orderStatusKey => $statusLog) {
                    $each_row_status_log[$orderStatusKey]['taken_by'] = isset($statusLog->createdBy) ? $statusLog->createdBy->name_with_emp_no  : "--";
                    $each_row_status_log[$orderStatusKey]['notes'] =  isset($statusLog->notes) ? $statusLog->notes  : "--";
                    $each_row_status_log[$orderStatusKey]['updated_at'] =  isset($statusLog->updated_at) ? ($statusLog->updated_at)->format('Y-m-d h:i:s')  : "--";
                    $each_row_status_log[$orderStatusKey]['status'] =  isset($statusLog->orderStatus->display_name) ? $statusLog->orderStatus->display_name  : "--";
                }
            }
            if ($each_list->statusLog->isEmpty()) {
                $each_row_status_log[0]['taken_by'] = "--";
                $each_row_status_log[0]['notes'] =  "--";
                $each_row_status_log[0]['updated_at'] =  ($each_list->updated_at)->format('Y-m-d h:i:s');
                $each_row_status_log[0]['status'] =  "--";
            }
            $each_row['statusLogArr'] = $each_row_status_log;
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }



    public function getByID($id)
    {
        return $this->model->with(['statusLog', 'statusLog.orderStatus'])->find($id);
    }

    public function revertUraFromOrder($transaction)
    {
        $order = UniformOrder::find($transaction->uniform_order_id);
        if ($order) {
            $order->ura_deducted = 0.00;
        }
    }

    public function updateStatus(Request $request)
    {
        $email_body = $request->input('email_script');
        $machinecode = $request->input('status');
        $is_email_required = $request->input('is_email_required') ? 1 : 0;
        $order = $this->model->find($request->input('id'));

        //Credit URA balance on cancel
        if ($machinecode == OrderStatusType::CANCELLED) {
            $transaction = $this->uraTransactionRepository->processUniformPurchaseCancel($order);
            if ($transaction) {
                $this->revertUraFromOrder($transaction);
            }
        }

        //Credit URA balance on product return
        if ($machinecode == OrderStatusType::RETURNED) {
            $transaction = $this->uraTransactionRepository->processUniformPurchaseReturn($order);
            if ($transaction) {
                $this->revertUraFromOrder($transaction);
            }
        }

        if ($email_body) {
            $this->saveEmailTemplate($order, $email_body, $machinecode);
        }
        if ($order != null) {
            $this->saveOrderStatusLog([
                'id' => $order->id,
                'notes' => $request->input('notes'),
                'is_email_required' => $is_email_required
            ], $request->input('status'));
        }
    }

    public function saveEmailTemplate($id, $template, $machine_code)
    {
        if ($machine_code) {
            switch ($machine_code) {
                case OrderStatusType::ORDER_RECEIVED:
                    return $this->storeEmailTemplate($template, 'uniform_order_received');

                case OrderStatusType::SHIPPED:
                    return $this->storeEmailTemplate($template, 'uniform_order_shipped');

                case OrderStatusType::CANCELLED:
                    return $this->storeEmailTemplate($template, 'uniform_order_cancelled');

                case OrderStatusType::DELIVERED:
                    return $this->storeEmailTemplate($template, 'uniform_order_delivered');

                case OrderStatusType::RETURNED:
                    return $this->storeEmailTemplate($template, 'uniform_order_returned');
                default:
                    return null;
            }
        }
    }

    public function storeEmailTemplate($template, $emailTemplateId)
    {
        $templateId = EmailNotificationType::where('type', $emailTemplateId)->pluck('id');
        if ($templateId) {
            $data['email_body'] = $template;
            return $this->emailTemplate->updateOrCreate(array('type_id' => $templateId), $data);
        }
    }

    public function getOrderItems($orderId)
    {
        return $this->uniformOrderItemRepository->getByOrderId($orderId);
    }

    public function saveOrderStatusLog($order, $status)
    {
        $orderStatDet = $this->uniformOrderStatus->where('machine_code', $status)->first();
        $uniformOrderStatusLog = $this->uniformOrderStatusLog->create([
            'uniform_order_id' => $order['id'],
            'uniform_order_status_id' => $orderStatDet->id,
            'notes' => isset($order['notes']) ? $order['notes'] : null,
            'is_email_required' => isset($order['is_email_required']) ? $order['is_email_required'] : 0,
            'created_by' => Auth::user()->id
        ]);
        if ($uniformOrderStatusLog != null && $uniformOrderStatusLog['is_email_required'] == true) {
            $uniformOrderDet = $this->model->where('id', $order['id'])->first();
            $this->uniformEmailRepository->sendNotificationEmail(['id' => $uniformOrderDet->user_id, 'machine_code' => $status]);
        }
        return $uniformOrderStatusLog;
    }

    public function getTemplateByOrderId($id)
    {
        $machinecode = null;
        $status = UniformOrderStatusLog::with('orderStatus')->where('uniform_order_id', $id)->select('uniform_order_status_id')->orderBy('updated_at', 'DESC')->first();
        if ($status) {
            $machinecode = $status->orderStatus->machine_code;
            return $this->getTemplate($machinecode);
        } else {
            return $machinecode;
        }
    }

    public function getTemplate($machine_code)
    {
        if ($machine_code) {
            switch ($machine_code) {
                case OrderStatusType::ORDER_RECEIVED:
                    return $this->getEmailTemplate(null, 'uniform_order_received');

                case OrderStatusType::SHIPPED:
                    return $this->getEmailTemplate(null, 'uniform_order_shipped');

                case OrderStatusType::CANCELLED:
                    return $this->getEmailTemplate(null, 'uniform_order_cancelled');

                case OrderStatusType::DELIVERED:
                    return $this->getEmailTemplate(null, 'uniform_order_delivered');

                case OrderStatusType::RETURNED:
                    return $this->getEmailTemplate(null, 'uniform_order_returned');
                default:
                    return null;
            }
        }
    }

    public function getEmailTemplate($order, $template)
    {
        $templates = EmailNotificationType::where('type', $template)->first();
        if (isset($templates)) {
            $tid = $templates->id;
            $template = EmailTemplate::where('type_id', $tid)->first();
            return $template;
        }
        return null;
    }

    public function validateOrderStatus($request)
    {
        $orderStatus = $request['status'];
        $orderId = $request['id'];
        $orderStatDet = $this->uniformOrderStatus->where('machine_code', $orderStatus)->first();
        $orderStatId = ($orderStatDet) ? $orderStatDet->id : null;
        $orderStatusLogDet = $this->uniformOrderStatusLog->where(['uniform_order_id' => $orderId, 'uniform_order_status_id' => $orderStatId])->first();
        if (empty($orderStatusLogDet)) {
            return true;
        } else {
            return false;
        }
    }
}
