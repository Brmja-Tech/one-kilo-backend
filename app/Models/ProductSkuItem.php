<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSkuItem extends Model
{
    protected $fillable = [
        'product_sku_id',
        'variant_id',
        'variant_item_id',
    ];

    public function sku(): BelongsTo
    {
        return $this->belongsTo(ProductSku::class, 'product_sku_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(Variant::class, 'variant_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(VariantItem::class, 'variant_item_id');
    }
}
