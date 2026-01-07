<?php

use Poweroffice\PowerofficeSDK;

require '00-setup.php';

try {
    $sdk = new PowerofficeSDK(
        baseUrl: URL,
        applicationKey: APPLICATION_KEY,
        clientKey: CLIENT_KEY,
        subscriptionKey: SUBSCRIPTION_KEY
    );
    var_dump('Connected successfully');
} catch (Exception $e) {
    var_dump('Failed');
    throw $e;
}
