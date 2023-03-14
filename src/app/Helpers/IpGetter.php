<?php

namespace App\Helpers;

use Psr\Http\Message\ServerRequestInterface as Request;

class IpGetter
{
    public static function getIpAddress(Request $request): string
    {
        return $request->getServerParams()['REMOTE_ADDR'];
    }
}