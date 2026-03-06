<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Queue Connection Name
    |--------------------------------------------------------------------------
    |
    | Laravel's queue supports a variety of backends via a single, unified
    | API, giving you convenient access to each backend using identical
    | syntax for each. The default queue connection is defined below.
    |
    */

    'default' => env('QUEUE_CONNECTION', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection options for every queue backend
    | used by your application. An example configuration is provided for
    | each backend supported by Laravel. You're also free to add more.
    |
    | Drivers: "sync", "database", "beanstalkd", "sqs", "redis",
    |          "deferred", "background", "failover", "null"
    |
    */


    // СПРОСИТЬ ПРАВИЛЬНО ЛИ СОЗДАВАТЬ ДВА EXCHANGE ПОД РАЗНЫЕ ОЧЕРЕДИ И СТОИТ ЛИ ЭТО ВООБЩЕ ДЕЛАТЬ?
    'connections' => [
        //Это настройки для RabbitMQ
        'rabbitmq' => [
            // Драйвер очереди
            'driver' => 'rabbitmq',
            // Класс подключения (стандартный для AMQP)
            'connection' => PhpAmqpLib\Connection\AMQPStreamConnection::class,
            
            'host' => env('RABBITMQ_HOST', '127.0.0.1'),
            'port' => env('RABBITMQ_PORT', 5672),
            'vhost' => env('RABBITMQ_VHOST', '/'),
            'login' => env('RABBITMQ_LOGIN', 'guest'),
            'password' => env('RABBITMQ_PASSWORD', 'guest'),
            
            'queue' => env('RABBITMQ_QUEUE', 'default'),
            
            // Тут идет настройка exchange и queues
            'options' => [
                
                // первый exchange reports.generate
                'exchange_generate' => [
                    // Имя exchange - сюда отправляем запросы на генерацию
                    'name' => 'reports.generate',
                    // Тип exchange: direct, fanout, topic, headers
                    // direct - сообщение идет в очередь с точным совпадением routing_key
                    'type' => 'direct',
                    // passive: false - создать exchange, если не существует
                    'passive' => false,
                    // durable: true - exchange сохранится после перезапуска RabbitMQ
                    'durable' => true,
                    // auto_delete: false - не удалять, когда отключатся все потребители
                    'auto_delete' => false,
                ],
                
                // второй exchange reports.completed
                'exchange_completed' => [
                    // Имя второго exchange - сюда публикуем готовые отчеты
                    'name' => 'reports.completed',
                    'type' => 'direct',
                    'passive' => false,
                    'durable' => true,
                    'auto_delete' => false,
                ],
                
                // Очереди
                'queues' => [
                    // Первая очередь - для генерации отчетов
                    'reports_generation_queue' => [
                        // Имя очереди
                        'name' => 'reports_generation_queue',
                        // К какому exchange привязана
                        'exchange' => 'reports.generate',
                        // routing_key - сообщения с этим ключом попадут в очередь
                        // (работает только для direct и topic exchange)
                        'routing_key' => 'reports_generation_queue',
                        // durable: очередь сохраняется после перезапуска
                        'durable' => true,
                        // exclusive: только для одного соединения
                        'exclusive' => false,
                        // auto_delete: удалять при отключении последнего потребителя
                        'auto_delete' => false,
                    ],
                    

                    //Вторая очередь - для уведомлений о завершении
                    'reports_completed_queue' => [
                        'name' => 'reports_completed_queue',
                        // Привязана к reports.completed (fanout exchange)
                        'exchange' => 'reports.completed',
                        'routing_key' => 'reports_completed_queue',
        
                        'durable' => true,
                        'exclusive' => false,
                        'auto_delete' => false,
                    ],
                    
                ],

                // Это настройка QoS (Quality of Service)
                // QoS контролирует, сколько сообщений RabbitMQ отправляет воркеру 
                // одновременно. Это защитный механизм, чтобы не перегрузить воркер.
                'channel' => [
                    'qos' => [
                        // Максимальный размер сообщения в байтах.
                        'prefetch_size' => 0,
                        // Сколько неподтвержденных сообщений может быть у воркера.
                        'prefetch_count' => 1,
                        // К кому применяются ограничения.
                        'global' => false,
                    ],
                ],
            ],
        ],
    ],

        // Таблица для failed jobs
        'failed' => [
            'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
            'database' => env('DB_CONNECTION', 'mysql'),
            'table' => 'failed_jobs',
        ],

        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver' => 'database',
            'connection' => env('DB_QUEUE_CONNECTION'),
            'table' => env('DB_QUEUE_TABLE', 'jobs'),
            'queue' => env('DB_QUEUE', 'default'),
            'retry_after' => (int) env('DB_QUEUE_RETRY_AFTER', 90),
            'after_commit' => false,
        ],

        'beanstalkd' => [
            'driver' => 'beanstalkd',
            'host' => env('BEANSTALKD_QUEUE_HOST', 'localhost'),
            'queue' => env('BEANSTALKD_QUEUE', 'default'),
            'retry_after' => (int) env('BEANSTALKD_QUEUE_RETRY_AFTER', 90),
            'block_for' => 0,
            'after_commit' => false,
        ],

        'sqs' => [
            'driver' => 'sqs',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'prefix' => env('SQS_PREFIX', 'https://sqs.us-east-1.amazonaws.com/your-account-id'),
            'queue' => env('SQS_QUEUE', 'default'),
            'suffix' => env('SQS_SUFFIX'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'after_commit' => false,
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => env('REDIS_QUEUE_CONNECTION', 'default'),
            'queue' => env('REDIS_QUEUE', 'default'),
            'retry_after' => (int) env('REDIS_QUEUE_RETRY_AFTER', 90),
            'block_for' => null,
            'after_commit' => false,
        ],

        'deferred' => [
            'driver' => 'deferred',
        ],

        'background' => [
            'driver' => 'background',
        ],

        'failover' => [
            'driver' => 'failover',
            'connections' => [
                'database',
                'deferred',
            ],
        ],


    /*
    |--------------------------------------------------------------------------
    | Job Batching
    |--------------------------------------------------------------------------
    |
    | The following options configure the database and table that store job
    | batching information. These options can be updated to any database
    | connection and table which has been defined by your application.
    |
    */

    'batching' => [
        'database' => env('DB_CONNECTION', 'sqlite'),
        'table' => 'job_batches',
    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs
    |--------------------------------------------------------------------------
    |
    | These options configure the behavior of failed queue job logging so you
    | can control how and where failed jobs are stored. Laravel ships with
    | support for storing failed jobs in a simple file or in a database.
    |
    | Supported drivers: "database-uuids", "dynamodb", "file", "null"
    |
    */

    'failed' => [
        'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
        'database' => env('DB_CONNECTION', 'sqlite'),
        'table' => 'failed_jobs',
    ],

];
