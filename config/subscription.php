<?php

return [

    'reminder' => [
        'days_before' => (int) env('SUBSCRIPTION_REMINDER_DAYS', 4),
        'interval_hours' => (float) env('SUBSCRIPTION_REMINDER_INTERVAL_HOURS', 12),
        'last_day_interval_hours' => (float) env('SUBSCRIPTION_REMINDER_LAST_DAY_INTERVAL_HOURS', 1),
        'close_delay_seconds' => (int) env('SUBSCRIPTION_REMINDER_CLOSE_DELAY', 5),
    ],

];
