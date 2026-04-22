<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_idea_id',
        'user_id',
        'estimated_total_cost',
        'estimated_unit_cost',
        'proposed_sale_price',
        'estimated_profit',
        'estimated_margin_percent',
        'break_even_quantity',
        'viability_result',
        'recommendation',
        'evaluation_date',
    ];

    protected function casts(): array
    {
        return [
            'estimated_total_cost' => 'decimal:2',
            'estimated_unit_cost' => 'decimal:2',
            'proposed_sale_price' => 'decimal:2',
            'estimated_profit' => 'decimal:2',
            'estimated_margin_percent' => 'decimal:2',
            'break_even_quantity' => 'decimal:2',
            'evaluation_date' => 'date',
        ];
    }

    public function productIdea()
    {
        return $this->belongsTo(ProductIdea::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
