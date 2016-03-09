<?php
include 'bootstrap.php';

$messaging = new SoftLayer\Messaging();

if ($messaging->authenticate(QUEUE_ACCOUNT, QUEUE_USERNAME, QUEUE_API_KEY)) {
    echo "Welcome to the SoftLayer Message Queue!" . PHP_EOL;
}
