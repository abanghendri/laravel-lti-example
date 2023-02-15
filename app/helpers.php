<?php

use Psr\Http\Message\ServerRequestInterface;

function getPayload(ServerRequestInterface $request)
{
    if ($request->getMethod() === 'POST') {
        return $request->getParsedBody();
    } elseif ($request->getMethod() === 'GET') {
        return $request->getQueryParams();
    }
}
