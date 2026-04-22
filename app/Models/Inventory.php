<?php

namespace App\Models;

use App\Models\InventoryMovement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_type',
        'product_id',
        'raw_material_id',
        'current_stock',
        'unit_measure',
        'minimum_stock',
        'last_movement_at',
    ];

    protected function casts(): array
    {
        return [
            'current_stock' => 'decimal:2',
            'minimum_stock' => 'decimal:2',
            'last_movement_at' => 'datetime',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function movements()
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
