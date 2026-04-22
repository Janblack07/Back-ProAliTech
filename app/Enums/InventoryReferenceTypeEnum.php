<?php

namespace App\Enums;

enum InventoryReferenceTypeEnum: string
{
    case PURCHASE = 'purchase';
    case SALE = 'sale';
    case PRODUCTION = 'production';
    case ADJUSTMENT = 'adjustment';
    case MANUAL = 'manual';
}
