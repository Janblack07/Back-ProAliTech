<?php

namespace App\Models;

use App\Models\ProductEvaluation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductIdea extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'user_id',
        'idea_name',
        'description',
        'proposed_sale_price',
        'expected_demand',
        'competition_level',
        'estimated_labor_cost',
        'estimated_energy_cost',
        'estimated_indirect_cost',
        'estimated_waste_percent',
        'observations',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'proposed_sale_price' => 'decimal:2',
            'estimated_labor_cost' => 'decimal:2',
            'estimated_energy_cost' => 'decimal:2',
            'estimated_indirect_cost' => 'decimal:2',
            'estimated_waste_percent' => 'decimal:2',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function evaluations()
    {
        return $this->hasMany(ProductEvaluation::class);
    }
}
