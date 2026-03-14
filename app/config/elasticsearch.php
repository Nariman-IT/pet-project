<?php

return [
    'host' => env('ELASTICSEARCH_HOST', 'elasticsearch'),
    'port' => env('ELASTICSEARCH_PORT', '9200'),
    'scheme' => env('ELASTICSEARCH_SCHEME', 'http'),
    
    'indexes' => [
        'products' => [
            'name' => 'products',
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
                'analysis' => [
                    'analyzer' => [
                        'ru_en_analyzer' => [
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => [
                                'lowercase',
                                'russian_stop',
                                'english_stop',
                                'russian_stemmer',
                                'english_stemmer',
                                'russian_keywords',
                                'english_keywords'
                            ]
                        ]
                    ],
                    'filter' => [
                        'russian_stop' => [
                            'type' => 'stop',
                            'stopwords' => '_russian_'
                        ],
                        'english_stop' => [
                            'type' => 'stop',
                            'stopwords' => '_english_'
                        ],
                        'russian_stemmer' => [
                            'type' => 'stemmer',
                            'language' => 'russian'
                        ],
                        'english_stemmer' => [
                            'type' => 'stemmer',
                            'language' => 'english'
                        ],
                        'russian_keywords' => [
                            'type' => 'keyword_marker',
                            'keywords' => ['пицца', 'кола', 'вода'] // пример ключевых слов
                        ],
                        'english_keywords' => [
                            'type' => 'keyword_marker',
                            'keywords' => ['pizza', 'cola', 'drink']
                        ]
                    ]
                ]
            ],
            'mappings' => [
                'properties' => [
                    'id' => ['type' => 'integer'],
                    'name' => [
                        'type' => 'text',
                        'analyzer' => 'ru_en_analyzer',
                        'fields' => [
                            'keyword' => ['type' => 'keyword'],
                            'raw' => ['type' => 'text', 'analyzer' => 'standard']
                        ]
                    ],
                    'description' => [
                        'type' => 'text',
                        'analyzer' => 'ru_en_analyzer'
                    ],
                    'price' => ['type' => 'float'],
                    'weight' => ['type' => 'float'],
                    'category' => ['type' => 'keyword'],
                    'created_at' => ['type' => 'date'],
                    'updated_at' => ['type' => 'date']
                ]
            ]
        ]
    ]
];