# Xero Provider for OAuth 2.0 Client
[![Latest Version](https://img.shields.io/github/release/calcinai/oauth2-xero.svg?style=flat-square)](https://github.com/calcinai/oauth2-xero/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/calcinai/oauth2-xero/master.svg?style=flat-square)](https://travis-ci.org/calcinai/oauth2-xero)
[![Total Downloads](https://img.shields.io/packagist/dt/calcinai/oauth2-xero.svg?style=flat-square)](https://packagist.org/packages/calcinai/oauth2-xero)

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

<?php

session_start();
 
$provider = new \Calcinai\OAuth2\Client\Provider\Xero([
    'clientId'          => '{xero-client-id}',
    'clientSecret'      => '{xero-client-secret}',
    'redirectUri'       => 'https://example.com/callback-url',
]);
 
if (!isset($_GET['code'])) {
 
    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    
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
 
    // Optional: Now you have a token you can look up a users profile data
    try {
 
        // We got an access token, let's now get the user's details
        $organization = $provider->getResourceOwner($token);
 
        // Use these details to create a new profile
        printf('Hello %s!', $organization->getName());
 
    } catch (Exception $e) {
 
        // Failed to get user details
        exit('Oh dear...');
    }
 
    // Use this to interact with an API on the users behalf
    echo $token->getToken();
}


```

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
 

## Testing

``` bash
$ ./vendor/bin/phpunit
```


## Credits

- [All Contributors](https://github.com/calcinai/oauth2-xero/contributors)


## License

The MIT License (MIT). Please see [License File](https://github.com/calcinai/oauth2-xero/blob/master/LICENSE) for more information.
