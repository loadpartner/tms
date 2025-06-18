<?php

namespace App\Enums;

enum ContactMethodType: string
{
    case PHONECALL = 'phonecall';
    case EMAIL = 'email';
    case TEXT = 'text';
    case FAX = 'fax';
    case WEB = 'web';
    case OTHER = 'other';
}