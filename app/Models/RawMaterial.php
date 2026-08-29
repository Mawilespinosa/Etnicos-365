<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'code',
    'name',
    'category',
    'unit',
    'stock_qty',
    'min_stock',
    'cost',
    'is_active',
])]
class RawMaterial extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'stock_qty' => 'decimal:2',
            'min_stock' => 'decimal:2',
            'cost' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'bill_of_materials')
            ->withPivot('quantity', 'unit', 'notes')
            ->withTimestamps();
    }
}