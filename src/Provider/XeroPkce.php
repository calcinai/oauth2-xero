<?php

namespace Calcinai\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;

/**
 * @package Calcinai\OAuth2\Client\Provider
 */
class XeroPkce extends Xero
{
    /**
     * Enable the PKCE flow
     *
     * @return string PKCE Method S256
     */
    protected function getPkceMethod()
    {
        return AbstractProvider::PKCE_METHOD_S256;
    }
}
