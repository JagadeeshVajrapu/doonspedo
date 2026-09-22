<?php

return [
    'subscriptions_enabled' => env('TAXI_SUBSCRIPTIONS_ENABLED', true),
    'default_commission_rate' => env('TAXI_DEFAULT_COMMISSION', 15),
    'min_withdrawal_amount' => env('TAXI_MIN_WITHDRAWAL', 100),
];
