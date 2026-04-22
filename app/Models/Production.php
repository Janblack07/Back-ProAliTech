<?php

namespace App\Models;

use App\Models\ProductionDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'recipe_id',
        'user_id',
        'batch_number',
        'production_date',
        'expected_quantity',
        'produced_quantity',
        'unit_measure',
        'labor_cost',
        'energy_cost',
        'indirect_cost',
        'waste_quantity',
        'total_cost',
        'unit_cost',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'production_date' => 'date',
            'expected_quantity' => 'decimal:2',
            'produced_quantity' => 'decimal:2',
            'labor_cost' => 'decimal:2',
            'energy_cost' => 'decimal:2',
            'indirect_cost' => 'decimal:2',
            'waste_quantity' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'unit_cost' => 'decimal:2',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(ProductionDetail::class);
    }
}
