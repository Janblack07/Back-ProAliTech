<?php

namespace App\Enums;

enum SaleStatusEnum: string
{
    case REGISTERED = 'registered';
    case CANCELLED = 'cancelled';
    case PAID = 'paid';
    case PENDING = 'pending';
}
