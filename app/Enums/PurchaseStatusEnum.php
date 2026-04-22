<?php

namespace App\Enums;

enum PurchaseStatusEnum: string
{
    case REGISTERED = 'registered';
    case CANCELLED = 'cancelled';
    case RECEIVED = 'received';
}
