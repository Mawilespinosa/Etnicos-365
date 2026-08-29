<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['product_id', 'location', 'stock_qty', 'min_stock'])]
class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected function casts(): array
    {
        return [
            'stock_qty' => 'decimal:2',
            'min_stock' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}