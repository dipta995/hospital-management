<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class PharmacyProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'brand_id',
        'type_id',
        'quantity_type_id',
        'name',
        'generic_name',
        'strength',
        'barcode',
        'purchase_price',
        'sell_price',
        'alert_qty',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(PharmacyCategory::class, 'category_id', 'id');
    }

    public function brand()
    {
        return $this->belongsTo(PharmacyBrand::class, 'brand_id', 'id');
    }

    public function type()
    {
        return $this->belongsTo(PharmacyType::class, 'type_id', 'id');
    }

    public function quantityType()
    {
        return $this->belongsTo(PharmacyUnit::class, 'quantity_type_id', 'id');
    }

    public function purchaseItems()
    {
        return $this->hasMany(PharmacyPurchaseItem::class, 'pharmacy_product_id');
    }

    public function saleItems()
    {
        return $this->hasMany(PharmacySaleItem::class, 'pharmacy_product_id');
    }

    public static function purchasedQuantitiesByBranch(int $branchId): Collection
    {
        return PharmacyPurchaseItem::where('branch_id', $branchId)
            ->selectRaw('pharmacy_product_id, SUM(quantity) as total')
            ->groupBy('pharmacy_product_id')
            ->pluck('total', 'pharmacy_product_id');
    }

    public static function soldQuantitiesByBranch(int $branchId): Collection
    {
        return PharmacySaleItem::where('branch_id', $branchId)
            ->selectRaw('pharmacy_product_id, SUM(quantity) as total')
            ->groupBy('pharmacy_product_id')
            ->pluck('total', 'pharmacy_product_id');
    }

    public static function stockMapForBranch(int $branchId): array
    {
        $purchased = self::purchasedQuantitiesByBranch($branchId);
        $sold = self::soldQuantitiesByBranch($branchId);
        $productIds = $purchased->keys()->merge($sold->keys())->unique();

        $map = [];
        foreach ($productIds as $productId) {
            $map[$productId] = max(
                (float) ($purchased[$productId] ?? 0) - (float) ($sold[$productId] ?? 0),
                0
            );
        }

        return $map;
    }

    public function purchasedQty(?int $branchId = null): float
    {
        $branchId = $branchId ?? (auth()->user()->branch_id ?? 0);

        return (float) PharmacyPurchaseItem::where('branch_id', $branchId)
            ->where('pharmacy_product_id', $this->id)
            ->sum('quantity');
    }

    public function soldQty(?int $branchId = null): float
    {
        $branchId = $branchId ?? (auth()->user()->branch_id ?? 0);

        return (float) PharmacySaleItem::where('branch_id', $branchId)
            ->where('pharmacy_product_id', $this->id)
            ->sum('quantity');
    }

    public function currentStock(?int $branchId = null): float
    {
        return max($this->purchasedQty($branchId) - $this->soldQty($branchId), 0);
    }

    public function isLowStock(?int $branchId = null): bool
    {
        $stock = $this->currentStock($branchId);

        return $stock > 0 && $stock <= (float) $this->alert_qty;
    }

    public function isOutOfStock(?int $branchId = null): bool
    {
        return $this->currentStock($branchId) <= 0;
    }

    public static function assertPurchaseChangeSafe(int $branchId, int $purchaseId, array $newQtyByProduct): void
    {
        $oldQtyByProduct = PharmacyPurchaseItem::where('pharmacy_purchase_id', $purchaseId)
            ->selectRaw('pharmacy_product_id, SUM(quantity) as total')
            ->groupBy('pharmacy_product_id')
            ->pluck('total', 'pharmacy_product_id');

        $productIds = collect($newQtyByProduct)->keys()->merge($oldQtyByProduct->keys())->unique();

        foreach ($productIds as $productId) {
            $newQty = (float) ($newQtyByProduct[$productId] ?? 0);
            $oldQty = (float) ($oldQtyByProduct[$productId] ?? 0);

            $totalPurchased = (float) PharmacyPurchaseItem::where('branch_id', $branchId)
                ->where('pharmacy_product_id', $productId)
                ->sum('quantity');

            $adjustedPurchased = $totalPurchased - $oldQty + $newQty;
            $sold = (float) PharmacySaleItem::where('branch_id', $branchId)
                ->where('pharmacy_product_id', $productId)
                ->sum('quantity');

            if ($adjustedPurchased < $sold) {
                $name = self::find($productId)?->name ?? "Product #{$productId}";
                throw new InvalidArgumentException(
                    "Cannot update purchase: \"{$name}\" has {$sold} units sold but only {$adjustedPurchased} would remain in purchase records."
                );
            }
        }
    }

    public static function assertPurchaseDeleteSafe(int $branchId, int $purchaseId): void
    {
        $items = PharmacyPurchaseItem::where('pharmacy_purchase_id', $purchaseId)->get();

        foreach ($items as $item) {
            $remainingPurchased = (float) PharmacyPurchaseItem::where('branch_id', $branchId)
                ->where('pharmacy_product_id', $item->pharmacy_product_id)
                ->where('pharmacy_purchase_id', '!=', $purchaseId)
                ->sum('quantity');

            $sold = (float) PharmacySaleItem::where('branch_id', $branchId)
                ->where('pharmacy_product_id', $item->pharmacy_product_id)
                ->sum('quantity');

            if ($remainingPurchased < $sold) {
                $name = self::find($item->pharmacy_product_id)?->name ?? "Product #{$item->pharmacy_product_id}";
                throw new InvalidArgumentException(
                    "Cannot delete purchase: \"{$name}\" has {$sold} units sold but only {$remainingPurchased} would remain in purchase records."
                );
            }
        }
    }

    public function scopeActive($query)
    {
        if (\Illuminate\Support\Facades\Schema::hasColumn('pharmacy_products', 'status')) {
            return $query->where('status', 1);
        }

        return $query;
    }

    public function isActive(): bool
    {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('pharmacy_products', 'status')) {
            return true;
        }

        return (int) $this->status === 1;
    }
}
