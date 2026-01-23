<?php

use Poweroffice\Plugins\UserAgentPlugin;
use Poweroffice\PowerofficeSDK;

require '00-setup.php';

$sdk = new PowerofficeSDK(
    baseUrl: URL,
    applicationKey: APPLICATION_KEY,
    subscriptionKey: SUBSCRIPTION_KEY,
    clientKey: CLIENT_KEY,
    plugins: [new UserAgentPlugin('jbevanac/poweroffice '.VERSION)],
);

$request = $sdk->onboarding()->initiate(ORG_NR, ONBOARDING_REDIRECT_URL);

dd($request);
