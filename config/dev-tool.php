<?php

return [
    // Enable dev tool in production
    'enable' => (bool) env('JW_DEV_TOOL_ENABLE', false),

    /**
     * Default plugin for DEV
     */
    'dev_plugin_default' => env('JW_DEV_PLUGIN_DEFAULT'),


    'release' => [
        'github_token' => env('JW_RELEASE_GITHUB_TOKEN'),
    ],

    'themes' => [
        'options' => [
            'post-types' => [
                'label' => 'Custom Post Types',
            ],
            'taxonomies/create' => [
                'label' => 'Make Custom Taxonomy',
            ],
            'settings' => [
                'label' => 'Settings',
            ],
            'templates' => [
                'label' => 'Page Templates',
            ],
        ]
    ],

    'plugins' => [
        'options' => [
            'post-types' => [
                'label' => 'Custom Post Types',
            ],
            'taxonomies' => [
                'label' => 'Custom Taxonomies',
            ],
            'crud' => [
                'label' => 'Make CRUD',
            ],
            // 'make-migration' => [
            //     'label' => 'Make Migration',
            // ],
        ],
    ]
];
