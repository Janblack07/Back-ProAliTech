<?php

namespace App\Enums;

enum ProductIdeaStatusEnum: string
{
    case DRAFT = 'draft';
    case EVALUATED = 'evaluated';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CONVERTED_TO_PRODUCT = 'converted_to_product';
}
