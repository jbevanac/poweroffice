# PowerOffice v2 SDK for PHP
## Features

- Onboarding.
- Offboarding.
- Employees.
- ContactBankAccounts.

## Installation

Install via Composer:

```bash
composer require jbevanac/poweroffice
```

## Usage

Initialize the SDK
```php
use Poweroffice\PowerofficeSDK;
use Poweroffice\Plugins\UserAgentPlugin;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

/* To avoid unnecessary authentication calls and repeated token requests */
$psr6Cache = new FilesystemAdapter('poweroffice', 3600, CACHE_DIR);
$cache = new Psr16Cache($psr6Cache);

/* Recommended by Poweroffice to include */
$userAgent = new UserAgentPlugin(YOUR_APP.' '.YOUR_EMAIL);

$sdk = new PowerofficeSDK(
    baseUrl: 'https://api.poweroffice.com', // /v2 is added by the SDK 
    applicationKey: 'YOUR_APPLICATION_KEY',
    subscriptionKey: 'YOUR_SUBSCRIPTION_KEY',
    clientKey: 'YOUR_CLIENT_KEY',
    plugins: [$userAgent],
    cache: $cache,
    cacheKey: 'poweroffice_access_token', // key under which the access token is stored
);
```

Onboarding
```php
use Poweroffice\PowerofficeSDK;

$sdk = new PowerofficeSDK(
    baseUrl: 'https://api.poweroffice.com', // /v2 is added by the SDK 
    applicationKey: 'YOUR_APPLICATION_KEY',
    subscriptionKey: 'YOUR_SUBSCRIPTION_KEY',
    clientKey: null,
);

// Step 1: Initiate onboarding
$onboardingResponse = $sdk->onboarding()->initiate(YOUR_CLIENT_ORG_NR, YOUR_WHITELISTED_REDIRECT_URL);

// Step 2: Redirect to
$onboardingResponse->temporaryUrl;

// Step 3: User is redirected to your whitelisted URL with a token
$token = $_GET['onboarding_token']; 
$finalizeOnboardingResponse = $sdk->onboarding()->finalize($token);

foreach ($finalizeOnboardingResponse->onboardedClientsInformation as $clientInfo) {
    if (YOUR_CLIENT_ORG_NR === $clientInfo->clientOrganizationNumber) {
        $clientKey = $clientInfo->clientKey;
    }
}
```