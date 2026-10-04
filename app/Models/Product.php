<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'image',
        'price',
        'sale_price',
        'stock_quantity',
        'description',
        'screen_size',
        'chip',
        'ram',
        'storage',
        'connectivity',
        'color',
        'colors',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'colors' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function getAverageRatingAttribute(): float
    {
        $avg = $this->reviews()->avg('rating');
        return $avg ? round($avg, 1) : 5.0;
    }

    /**
     * Tự động tìm sản phẩm theo cả ID hoặc SLUG trên thanh URL
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('id', $value)
            ->orWhere('slug', $value)
            ->firstOrFail();
    }

    /**
     * Lấy danh sách các màu sắc kèm số lượng tồn kho tương ứng:
     * Trả về mảng dạng: [ ['name' => 'Hồng', 'quantity' => 2], ['name' => 'Đen', 'quantity' => 5] ]
     */
    public function getColorVariantsAttribute(): array
    {
        // 1. Nếu có dữ liệu trong cột 'colors' dạng mảng JSON
        if (!empty($this->colors) && is_array($this->colors)) {
            $variants = [];
            foreach ($this->colors as $item) {
                if (is_array($item) && !empty($item['name'])) {
                    $variants[] = [
                        'name' => trim($item['name']),
                        'quantity' => max(0, (int)($item['quantity'] ?? 0)),
                    ];
                } elseif (is_string($item) && trim($item) !== '') {
                    $variants[] = [
                        'name' => trim($item),
                        'quantity' => (int)$this->stock_quantity,
                    ];
                }
            }
            if (!empty($variants)) {
                return $variants;
            }
        }

        // 2. Nếu có dữ liệu trong cột 'color' (chuỗi phân cách dấu phẩy)
        if (!empty($this->color)) {
            $colorNames = array_filter(array_map('trim', explode(',', $this->color)));
            if (!empty($colorNames)) {
                $count = count($colorNames);
                $qtyPerColor = $count > 0 ? (int)floor($this->stock_quantity / $count) : (int)$this->stock_quantity;
                $variants = [];
                foreach ($colorNames as $name) {
                    $variants[] = [
                        'name' => $name,
                        'quantity' => max(0, $qtyPerColor),
                    ];
                }
                return $variants;
            }
        }

        // 3. Mặc định nếu không có màu sắc cụ thể
        return [
            [
                'name' => 'Tiêu chuẩn',
                'quantity' => (int)$this->stock_quantity,
            ]
        ];
    }

    /**
     * Lấy số lượng tồn kho của một màu cụ thể
     */
    public function getStockForColor(?string $colorName): int
    {
        $variants = $this->color_variants;
        if (empty($colorName) || strcasecmp($colorName, 'Tiêu chuẩn') === 0) {
            if (!empty($variants) && isset($variants[0]['quantity'])) {
                return max((int)$variants[0]['quantity'], (int)$this->stock_quantity);
            }
            return (int)$this->stock_quantity;
        }

        foreach ($variants as $variant) {
            if (strcasecmp($variant['name'], trim($colorName)) === 0) {
                return (int)$variant['quantity'];
            }
        }

        return (int)$this->stock_quantity;
    }

    /**
     * Trừ số lượng tồn kho của một màu cụ thể khi đặt hàng
     */
    public function reduceColorStock(?string $colorName, int $quantity): bool
    {
        $quantity = max(1, $quantity);
        $variants = $this->color_variants;
        $found = false;
        $newVariants = [];
        $totalStock = 0;

        foreach ($variants as $variant) {
            $name = $variant['name'];
            $qty = (int)$variant['quantity'];

            if (!$found && !empty($colorName) && strcasecmp($name, trim($colorName)) === 0) {
                $qty = max(0, $qty - $quantity);
                $found = true;
            }

            $newVariants[] = [
                'name' => $name,
                'quantity' => $qty,
            ];
            $totalStock += $qty;
        }

        // Nếu không khớp chính xác tên màu sắc, trừ vào biến thể đầu tiên còn hàng
        if (!$found) {
            $rem = $quantity;
            foreach ($newVariants as &$v) {
                if ($v['quantity'] > 0 && $rem > 0) {
                    $deduct = min($v['quantity'], $rem);
                    $v['quantity'] = max(0, $v['quantity'] - $deduct);
                    $rem -= $deduct;
                }
            }
            unset($v);
            $totalStock = array_sum(array_column($newVariants, 'quantity'));
        }

        // Cập nhật mảng colors và tổng stock_quantity
        $this->colors = $newVariants;
        $this->stock_quantity = max(0, min((int)$this->stock_quantity - $quantity, $totalStock));
        
        // Đồng bộ chuỗi color tương thích ngược
        $this->color = implode(', ', array_column($newVariants, 'name'));
        
        return $this->save();
    }

    /**
     * Hoàn lại số lượng tồn kho của một màu cụ thể khi hủy đơn hàng
     */
    public function restoreColorStock(?string $colorName, int $quantity): bool
    {
        $quantity = max(1, $quantity);
        $variants = $this->color_variants;
        $found = false;
        $newVariants = [];
        $totalStock = 0;

        foreach ($variants as $variant) {
            $name = $variant['name'];
            $qty = (int)$variant['quantity'];

            if (!$found && !empty($colorName) && strcasecmp($name, trim($colorName)) === 0) {
                $qty += $quantity;
                $found = true;
            }

            $newVariants[] = [
                'name' => $name,
                'quantity' => $qty,
            ];
            $totalStock += $qty;
        }

        $this->colors = $newVariants;
        $this->stock_quantity = max(0, $found ? $totalStock : ($this->stock_quantity + $quantity));
        $this->color = implode(', ', array_column($newVariants, 'name'));

        return $this->save();
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}