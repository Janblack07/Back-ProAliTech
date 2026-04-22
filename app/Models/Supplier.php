<?php

namespace App\Models;

use App\Models\RawMaterial;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_name',
        'ruc',
        'contact_name',
        'phone',
        'email',
        'address',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function rawMaterials()
    {
        return $this->hasMany(RawMaterial::class);
    }
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
