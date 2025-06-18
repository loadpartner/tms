<?php

declare(strict_types=1);

namespace App\Enums\Documents;

enum DocumentTemplateType: string
{
    case CARRIER_RATE_CONFIRMATION = 'carrier_rate_confirmation';
    case CUSTOMER_INVOICE = 'customer_invoice';
    case BILL_OF_LADING = 'bill_of_lading';
} 