<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RawMaterial extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'code',
        'name',
        'description',
        'material_type',
        'unit_measure',
        'cost_per_unit',
        'minimum_stock',
        'expiration_date',
        'image_url',
        'image_public_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'cost_per_unit' => 'decimal:2',
            'minimum_stock' => 'decimal:2',
            'expiration_date' => 'date',
            'status' => 'boolean',
        ];
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }
    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class);
    }
}
