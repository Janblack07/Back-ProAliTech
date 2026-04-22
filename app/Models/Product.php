<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'code',
        'name',
        'description',
        'unit_measure',
        'cost_price',
        'sale_price',
        'minimum_stock',
        'shelf_life_days',
        'image_url',
        'image_public_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'minimum_stock' => 'decimal:2',
            'shelf_life_days' => 'integer',
            'status' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }
    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }
    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    public function productions()
    {
        return $this->hasMany(Production::class);
    }
}
