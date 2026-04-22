<?php

namespace App\Models;

use App\Models\SaleDetail;
use App\Models\Income;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sale_date',
        'invoice_number',
        'customer_name',
        'customer_document',
        'subtotal',
        'tax',
        'discount',
        'total',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'sale_date' => 'date',
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function income()
    {
        return $this->hasOne(Income::class);
    }
}
