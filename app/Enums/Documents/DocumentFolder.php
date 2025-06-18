<?php
declare(strict_types=1);
namespace App\Enums\Documents;


enum DocumentFolder: string
{
    case RATECONS = 'ratecons';
    case BOLS = 'bols';
    case CUSTOMER_INVOICE = 'customer invoices';
    case CARRIER_BILLS = 'carrier bills';
    case CYA = 'cya';
}