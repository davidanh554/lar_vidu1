<?php

namespace App\Services;

use App\Models\Order;

class GHNOrderService
{
    public function __construct(private GHNService $ghn)
    {
    }

    public function create(Order $order, bool $isPaid = false): array
    {
        $items = [];
        $weight = 0;

        foreach ($order->items as $item) {
            $itemWeight = (int) ($item->product->weight ?? 200);
            $weight += $itemWeight * (int) $item->quantity;
            $items[] = [
                'name' => $item->product->name ?? 'Sản phẩm',
                'quantity' => (int) $item->quantity,
                'price' => (int) $item->price,
                'weight' => $itemWeight,
            ];
        }

        return $this->ghn->createOrder([
            'payment_type_id' => 2,
            'note' => 'Đơn hàng #' . $order->id,
            'required_note' => 'KHONGCHOXEMHANG',
            'from_name' => 'VUA TABLET',
            'from_phone' => '0776472883',
            'from_address' => 'Số 1 Xuân Phương',
            'from_district_id' => (int) config('services.ghn.from_district_id', 3440),
            'from_ward_code' => (string) config('services.ghn.from_ward_code', '13010'),
            'to_name' => $order->name,
            'to_phone' => $order->phone,
            'to_address' => $order->address,
            'to_ward_code' => (string) $order->to_ward_code,
            'to_district_id' => (int) $order->to_district_id,
            'cod_amount' => $isPaid ? 0 : min((int) $order->total_price, (int) env('GHN_MAX_COD', 300000)),
            'weight' => $weight > 0 ? $weight : 300,
            'length' => 15,
            'width' => 15,
            'height' => 10,
            'service_type_id' => 2,
            'items' => $items,
        ]);
    }
}
