<?php

namespace Calcinai\OAuth2\Client\Provider;

use Calcinai\OAuth2\Client\Provider\Exception\XeroProviderException;
use Calcinai\OAuth2\Client\XeroResourceOwner;
use Calcinai\OAuth2\Client\XeroTenant;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @package Calcinai\OAuth2\Client\Provider
 */
class Xero extends AbstractProvider
{
    const METHOD_DELETE = 'DELETE';

    /**
     * Returns the base URL for authorizing a client.
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return 'https://login.xero.com/identity/connect/authorize';
    }

    /**
     * Returns the base URL for requesting an access token.
     *
     * @param array $params
     *
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return 'https://identity.xero.com/connect/token';
    }

    /**
     * @param array $params
     * @return string
     */
    public function getTenantsUrl(array $params = null)
    {
        if ($params) {
            $params = '?' . http_build_query($params);
        }

        return 'https://api.xero.com/connections' . $params;
    }

    /**
     * @return string
     */
    public function getRevokeUrl()
    {
        return 'https://identity.xero.com/connect/revocation';
    }

    /**
     * @param AccessTokenInterface $token
     * @param array $params
     * @return XeroTenant[]
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     * @throws \Exception
     */
    public function getTenants(AccessTokenInterface $token, array $params = null)
    {
        $request = $this->getAuthenticatedRequest(
            self::METHOD_GET,
            $this->getTenantsUrl($params),
            $token
        );

        $response = $this->getParsedResponse($request);
        $tenants = [];

        foreach ($response as $tenantData) {
            $tenants[] = XeroTenant::fromArray($tenantData);
        }

        return $tenants;
    }

    /**
     * @param AccessTokenInterface $token
     * @param $connectionId
     * @return mixed
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function disconnect(AccessTokenInterface $token, $connectionId)
    {
        $url = sprintf('%s/%s', $this->getTenantsUrl(), $connectionId);

        $request = $this->getAuthenticatedRequest(self::METHOD_DELETE, $url, $token);

        $response = $this->getParsedResponse($request);
        return $response;
    }

    /**
     * Revoke authorisation; remove all granted scopes and all tenants for the user
     * holding this refresh token.
     *
     * @param string|AccessTokenInterface $refreshToken
     * @return array|mixed|string
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     * @throws \Calcinai\OAuth2\Client\Provider\Exception\XeroProviderException
     */
    public function revoke($refreshToken)
    {
        if ($refreshToken instanceof AccessTokenInterface) {
            // Support access token for consistency.
            $refreshToken = $refreshToken->getRefreshToken();
        }

        // Enpoint uses Basic auth.
        $headers = $this->getDefaultHeaders();
        $headers['Authorization'] = 'Basic ' . base64_encode($this->clientId . ':' .$this->clientSecret);
        $headers['Content-Type'] = 'application/x-www-form-urlencoded';

        // Xero supports only the refresh token for revoking at this time.
        $body = [
            'token' => $refreshToken,
            // See https://tools.ietf.org/html/rfc7009#section-2.1
            'token_type_hint' => 'refresh_token',
        ];

        // PSR-7 requires a stream for the body; Guzzle is happy accept a string.
        $options = [
            'headers' => $headers,
            'body' => http_build_query($body),
        ];

        $request = $this->getRequest(static::METHOD_POST, $this->getRevokeUrl(), $options);

        // Empty string for the response payload if the revoke does not fail.
        return $this->getParsedResponse($request);
    }

    /**
     * Returns the URL for requesting the resource owner's details.
     *
     * @param AccessToken $token
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        // This does not exist as it comes down in the JWT
        return '';
    }

    /**
     * @param AccessToken $token
     * @return XeroResourceOwner
     */
    public function getResourceOwner(AccessToken $token)
    {
        return XeroResourceOwner::fromJWT($token->getValues()['id_token']);
    }

    /**
     * Checks a provider response for errors.
     *
     * @param ResponseInterface $response
     * @param array|string $data Parsed response data
     *
     * @throws \Calcinai\OAuth2\Client\Provider\Exception\XeroProviderException
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if ($response->getStatusCode() >= 400) {
            throw new XeroProviderException(
                isset($data['error']) ? $data['error'] : $response->getReasonPhrase(),
                $response->getStatusCode(),
                $response
            );
        }
    }

    /**
     * @return array
     */
    protected function getDefaultScopes()
    {
        return ['openid email profile'];
    }

    /**
     * Generates a resource owner object from a successful resource owner
     * details request.
     *
     * @param  array $response
     * @param  AccessToken $token
     * @return void|ResourceOwnerInterface
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        // This does nothing as we get the resource owner from the token itself, don't need to make a request to get it.
    }

    /**
     * @param mixed|null $token
     * @return array
     */
    protected function getAuthorizationHeaders($token = null)
    {
        return [
            'Authorization' => 'Bearer ' . $token->getToken()
        ];
    }
}
