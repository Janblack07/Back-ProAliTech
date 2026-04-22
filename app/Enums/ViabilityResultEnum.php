<?php

namespace App\Enums;

enum ViabilityResultEnum: string
{
    case PROFITABLE = 'profitable';
    case ACCEPTABLE = 'acceptable';
    case RISK = 'risk';
    case NOT_PROFITABLE = 'not_profitable';
}
