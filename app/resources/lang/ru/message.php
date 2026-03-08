<?php

return [
    'report' => [
        'not_found' => 'Отчет с ID :id не найден',
        'generation_failed' => 'Ошибка генерации отчета: :reason',
        'not_ready' => 'Отчет еще не готов к скачиванию',
        'access_denied' => 'У вас нет доступа к этому отчету',
    ],
    'user' => [
        'not_found' => 'Пользователь не найден',
        'already_registered' => 'Пользователь с email :email уже зарегистрирован',
        'invalid_credentials' => 'Неверный email или пароль',
    ],
    'validation' => [
        'invalid_date_range' => 'Неверный диапазон дат. Дата начала должна быть раньше даты окончания',
        'invalid_period' => 'Период не должен превышать :days дней',
    ],
    'system' => [
        'maintenance' => 'Система находится на техническом обслуживании',
        'too_many_requests' => 'Слишком много запросов. Пожалуйста, попробуйте позже',
    ],
];