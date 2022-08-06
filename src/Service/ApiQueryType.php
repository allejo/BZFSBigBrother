<?php declare(strict_types=1);

namespace App\Service;

enum ApiQueryType
{
    case IP;

    case CMD;

    case CALLSIGN;
}
