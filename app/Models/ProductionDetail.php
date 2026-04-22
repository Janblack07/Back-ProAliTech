<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_id',
        'raw_material_id',
        'quantity_used',
        'unit_measure',
        'unit_cost',
        'total_cost',
        'batch_number',
        'expiration_date',
    ];

    protected function casts(): array
    {
        return [
            'quantity_used' => 'decimal:2',
            'unit_cost' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'expiration_date' => 'date',
        ];
    }

    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
