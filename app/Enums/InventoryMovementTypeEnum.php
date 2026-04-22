<?php

namespace App\Enums;

enum InventoryMovementTypeEnum: string
{
    case ENTRY = 'entry';
    case EXIT = 'exit';
    case ADJUSTMENT = 'adjustment';
    case WASTE = 'waste';
    case RETURN = 'return';
}
