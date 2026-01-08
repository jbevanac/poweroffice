<?php

use Poweroffice\PowerofficeSDK;

require '00-setup.php';

$sdk = new PowerofficeSDK(
    baseUrl: URL,
    applicationKey: APPLICATION_KEY,
    clientKey: CLIENT_KEY,
    subscriptionKey: SUBSCRIPTION_KEY
);
