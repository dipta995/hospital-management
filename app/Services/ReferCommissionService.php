<?php

namespace App\Services;

use App\Models\CustomPercent;
use App\Models\Product;
use App\Models\Reefer;

class ReferCommissionService
{
    public function referPayload(int $referId): ?array
    {
        $refer = Reefer::with('customParcent')->find($referId);
        if (!$refer) {
            return null;
        }

        return [
            'referID' => $refer->id,
            'name' => $refer->name,
            'percent' => (float) $refer->percent,
            'has_custom_percent' => $refer->customParcent->isNotEmpty(),
            'custom_percents' => $refer->customParcent
                ->pluck('percentage', 'category_id')
                ->map(fn ($value) => (float) $value)
                ->all(),
        ];
    }

    public function percentageForProduct(int $referId, int $productId): float
    {
        $refer = Reefer::find($referId);
        if (!$refer) {
            return 0.0;
        }

        $product = Product::find($productId);
        if (!$product) {
            return (float) $refer->percent;
        }

        $custom = CustomPercent::where('refer_id', $referId)
            ->where('category_id', $product->category_id)
            ->first();

        return $custom ? (float) $custom->percentage : (float) $refer->percent;
    }

    public function calculate(int $referId, array $products, float $discountAmount, ?float $grossTotal = null): float
    {
        if (!$referId) {
            return 0.0;
        }

        $refer = Reefer::with('customParcent')->find($referId);
        if (!$refer) {
            return 0.0;
        }

        if ($refer->customParcent->isNotEmpty()) {
            $amount = 0.0;
            foreach ($products as $product) {
                $productId = (int) ($product['product_id'] ?? 0);
                $price = (float) ($product['price'] ?? 0);
                if (!$productId || $price <= 0) {
                    continue;
                }
                $percentage = $this->percentageForProduct($referId, $productId);
                $amount += ($percentage * $price) / 100;
            }

            return max(0.0, round($amount - $discountAmount, 2));
        }

        $total = $grossTotal ?? array_sum(array_map(
            fn ($product) => (float) ($product['price'] ?? 0),
            $products
        ));

        return max(0.0, round((($refer->percent * $total) / 100) - $discountAmount, 2));
    }
}
