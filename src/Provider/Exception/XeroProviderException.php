<?php

namespace Calcinai\OAuth2\Client\Provider\Exception;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Psr\Http\Message\ResponseInterface;

class XeroProviderException extends IdentityProviderException
{
    /**
     * @param  ResponseInterface $response
     * @param  string|null $message
     *
     * @throws XeroProviderException
     */
    public static function fromResponse(ResponseInterface $response, $message = null)
    {
        throw new static($message, $response->getStatusCode(), (string)$response->getBody());
    }
}
