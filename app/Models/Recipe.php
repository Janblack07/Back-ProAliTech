<?php

namespace App\Models;

use App\Models\RecipeDetail;
use App\Models\Production;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'recipe_name',
        'expected_yield',
        'unit_measure',
        'estimated_labor_cost',
        'estimated_energy_cost',
        'estimated_indirect_cost',
        'estimated_waste_percent',
        'instructions',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'expected_yield' => 'decimal:2',
            'estimated_labor_cost' => 'decimal:2',
            'estimated_energy_cost' => 'decimal:2',
            'estimated_indirect_cost' => 'decimal:2',
            'estimated_waste_percent' => 'decimal:2',
            'status' => 'boolean',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function details()
    {
        return $this->hasMany(RecipeDetail::class);
    }

    public function productions()
    {
        return $this->hasMany(Production::class);
    }
}
