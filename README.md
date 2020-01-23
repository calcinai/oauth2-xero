# Xero Provider for OAuth 2.0 Client
[![Latest Version](https://img.shields.io/github/release/calcinai/oauth2-xero.svg)](https://github.com/calcinai/oauth2-xero/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/calcinai/oauth2-xero/master.svg)](https://travis-ci.org/calcinai/oauth2-xero)
[![Total Downloads](https://img.shields.io/packagist/dt/calcinai/oauth2-xero.svg)](https://packagist.org/packages/calcinai/oauth2-xero)

This package provides Xero OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

To install, use composer:

```bash
$ composer require calcinai/oauth2-xero
```
## Usage

Usage is the same as The League's OAuth client, using `\Calcinai\OAuth2\Client\Provider\Xero` as the provider.

### Authorization Code Flow
```php
session_start();
 
$provider = new \Calcinai\OAuth2\Client\Provider\Xero([
    'clientId'          => '{xero-client-id}',
    'clientSecret'      => '{xero-client-secret}',
    'redirectUri'       => 'https://example.com/callback-url',
]);
 
if (!isset($_GET['code'])) {

    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl([
        'scope' => 'openid email profile accounting.transactions'
    ]);

    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: ' . $authUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);


    //If you added the openid/profile scopes you can access the authorizing user's identity.
    $identity = $provider->getResourceOwner($token);
    print_r($identity);

    //Get the tenants that this user is authorized to access
    $tenants = $provider->getTenants($token);
    print_r($tenants);
}
```

You can then store the token and use it to make requests against the api to the desired tenants


## Scopes
 OAuth scopes, indicating which parts of the Xero organisation you'd like your app to be able to access. The complete list of scopes can be found [here](https://developer.xero.com/documentation/oauth2/scopes).
 
 ```php
$provider = new \Calcinai\OAuth2\Client\Provider\Xero([
    'clientId'          => '{xero-client-id}',
    'clientSecret'      => '{xero-client-secret}',
    'redirectUri'       => 'https://example.com/callback-url',
]);
    
 $authUrl = $provider->$provider->getAuthorizationUrl([
    'scope' => 'bankfeeds accounting.transactions'
 ]);
 ```
 
## Refreshing a token

```php
$newAccessToken = $provider->getAccessToken('refresh_token', [
    'refresh_token' => $existingAccessToken->getRefreshToken()
]);
```


## Testing

``` bash
$ ./vendor/bin/phpunit
```


## Credits

- [All Contributors](https://github.com/calcinai/oauth2-xero/contributors)


## License

The MIT License (MIT). Please see [License File](https://github.com/calcinai/oauth2-xero/blob/master/LICENSE) for more information.
