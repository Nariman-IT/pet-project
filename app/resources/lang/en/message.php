<?php

return [
    'report' => [
        'not_found' => 'Report with ID :id not found',
        'generation_failed' => 'Report generation failed: :reason',
        'not_ready' => 'Report is not ready for download yet',
        'access_denied' => 'You do not have access to this report',
    ],
    'user' => [
        'not_found' => 'User not found',
        'already_registered' => 'User with email :email is already registered',
        'invalid_credentials' => 'Invalid email or password',
    ],
    'validation' => [
        'invalid_date_range' => 'Invalid date range. Start date must be before end date',
        'invalid_period' => 'Period must not exceed :days days',
    ],
    'system' => [
        'maintenance' => 'System is under maintenance',
        'too_many_requests' => 'Too many requests. Please try again later',
    ],
];