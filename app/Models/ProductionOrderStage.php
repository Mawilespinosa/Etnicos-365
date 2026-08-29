<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'production_order_id',
    'stage_number',
    'name',
    'status',
    'notes',
    'completed_by',
    'completed_at',
])]
class ProductionOrderStage extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'stage_number' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    public function productionOrder(): BelongsTo
    {
        return $this->belongsTo(ProductionOrder::class);
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}