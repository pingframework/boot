<?php

use Pingframework\Boot\Application\SlimPingBootApplication;

return [
    'foo' => 'bar',
    SlimPingBootApplication::CONFIG_SLIM_LOG_ERRORS => true,
    SlimPingBootApplication::CONFIG_SLIM_LOG_ERRORS_DETAILS => true,
    SlimPingBootApplication::CONFIG_SLIM_DISPLAY_ERRORS_DETAILS => true,
];