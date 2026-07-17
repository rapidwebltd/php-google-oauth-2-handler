# PHP Google OAuth 2 Handler

This package provides a handler to ease authentication with Google's OAuth 2 APIs.

## Installation

PHP Google OAuth 2 Handler can be easily installed using Composer. Just run the following command from the root of your project.

```
composer require rapidwebltd/php-google-oauth-2-handler
```

If you have never used the Composer dependency manager before, head to the [Composer website](https://getcomposer.org/) for more information on how to get started.

## Dependents

This handler is designed to be used by other packages to implement API clients that interact with Google's OAuth 2 APIs. Visit the following URL to see all
the packages that make use of this handler.

[https://packagist.org/packages/rapidwebltd/php-google-oauth-2-handler/dependents](https://packagist.org/packages/rapidwebltd/php-google-oauth-2-handler/dependents)

## Setup

Create OAuth client credentials in Google Cloud and register a redirect URI. Then run:

```bash
php vendor/rapidwebltd/php-google-oauth-2-handler/src/setup.php
```

The retired out-of-band OAuth flow is not used. After authorizing, copy the
`code` query parameter from the registered redirect URL back into the setup
command.

## Usage

```php
use RapidWeb\GoogleOAuth2Handler\GoogleOAuth2Handler;

$handler = new GoogleOAuth2Handler(
    $clientId,
    $clientSecret,
    $scopes,
    $refreshToken,
    $redirectUri
);

$response = $handler->performRequest('GET', 'https://people.googleapis.com/v1/people/me');
```
