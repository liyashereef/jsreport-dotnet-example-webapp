<?php

namespace Modules\Uniform\Repositories;

use Modules\Uniform\Models\UniformOrderItem;

class UniformOrderItemRepository
{
    protected $model;
    public function __construct(
        UniformOrderItem $uniformOrderItem
    ) {
        $this->model = $uniformOrderItem;
    }

    public function getByID($id)
    {
        return $this->model->find($id);
    }


    public function getByOrderId($orderId)
    {
       $variantOrderLog =  $this->model->with([
            'product',
            'productVariant'
        ])->where('uniform_order_id', $orderId)->get();
        return $this->prepareDataForVariableList($variantOrderLog);
    }

    public function prepareDataForVariableList($variantOrderLog)
    {
        $datatable_rows = array();
        foreach ($variantOrderLog as $key => $each_list) {
            $each_row["id"] = isset($each_list->id) ? $each_list->id : "--";
            $each_row["product_name"] = isset($each_list->product->name) ? $each_list->product->name  : "--";
            $each_row["variant_name"] = isset($each_list->productVariant->variant_name) ? $each_list->productVariant->variant_name  : "--";
            $each_row["product_quantity"] = isset($each_list->quantity) ? $each_list->quantity  : "--";
            $each_row["product_selling_price"] = isset($each_list->product->selling_price) ? $each_list->product->selling_price  : "--";
            $each_row["tax_amount"] = $each_list->tax_amount;
            $each_row["tax_rate"] = $each_list->tax_rate;
            $each_row["tax_type"] = $each_list->taxMasterLog->taxMaster->name;;
            $each_row["net_amount"] = $each_list->item_price;
            $each_row["product_total_cost"] = $each_list->total_price_with_tax;
            
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }
}
