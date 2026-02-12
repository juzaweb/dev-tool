<?php

return [
    'release' => [
        'github_token' => env('JW_RELEASE_GITHUB_TOKEN'),
    ],

    'themes' => [
        'stubs' => [
            'path' => base_path('vendor/juzaweb/dev-tool/stubs/themes/'),

            'files' => [
                'index' => 'src/resources/views/index.blade.php',
                'layout' => 'src/resources/views/layouts/main.blade.php',
                'search' => 'src/resources/views/search.blade.php',
                'mix' => 'assets/webpack.mix.js',
                // 'resources/views/profile/index' => 'src/resources/views/profile/index.blade.php',
                // 'resources/views/profile/notification' => 'src/resources/views/profile/notification.blade.php',
                // 'resources/views/profile/components/sidebar' => 'src/resources/views/profile/components/sidebar.blade.php',
                'ThemeServiceProvider' => 'src/Providers/ThemeServiceProvider.php',
                'RouteServiceProvider' => 'src/Providers/RouteServiceProvider.php',
                'controllers/HomeController' => 'src/Http/Controllers/HomeController.php',
                'controllers/ProfileController' => 'src/Http/Controllers/ProfileController.php',
                'lang' => 'src/resources/lang/en/translation.php',
                'routes/admin' => 'src/routes/admin.php',
                'routes/web' => 'src/routes/web.php',
                'gitignore' => '.gitignore',
                'composer' => 'composer.json',
                'phpunit' => 'phpunit.xml',
                'config' => 'config/[NAME].php',
                'test-case' => 'tests/TestCase.php',
                'changelog' => 'changelog.md',
                'package' => 'package.json',
                'webpack' => 'webpack.mix.js',
                'readme' => 'README.md',
            ],
            'folders' => [
                'assets/js' => 'assets/js',
                'assets/css' => 'assets/css',
                'lang' => 'src/resources/lang/en',
                'views' => 'src/resources/views/layouts',
                'Http/Controllers' => 'src/Http/Controllers',
                // 'Http/Middleware' => 'src/Http/Middleware',
                // 'Http/Requests' => 'src/Http/Requests',
                'Providers' => 'src/Providers',
                'database/seeders' => 'database/seeders',
                'tests/Feature' => 'tests/Feature',
                'tests/Unit' => 'tests/Unit',
            ],
        ],
    ],

    'modules' => [
        /*
        |--------------------------------------------------------------------------
        | Module Stubs
        |--------------------------------------------------------------------------
        |
        | Default module stubs.
        |
        */
        'stubs' => [
            'enabled' => true,
            'path' => base_path('vendor/juzaweb/dev-tool/stubs/modules/'),
            'files' => [
                'routes/admin' => 'routes/admin.php',
                'routes/web' => 'routes/web.php',
                'routes/api' => 'routes/api.php',
                // 'views/index' => 'resources/views/index.blade.php',
                // 'views/master' => 'resources/views/layouts/master.blade.php',
                'scaffold/config' => 'config/config.php',
                'composer' => 'composer.json',
                'webpack' => 'assets/webpack.mix.js',
                'package' => 'package.json',
                'gitignore' => '.gitignore',
                'readme' => 'README.md',
                'test-case' => 'tests/TestCase.php',
                'test-feature-example' => 'tests/Feature/ExampleTest.php',
                'test-unit-example' => 'tests/Unit/ExampleTest.php',
            ],
            'replacements' => [
                'routes/web' => ['LOWER_NAME', 'STUDLY_NAME'],
                'routes/api' => ['LOWER_NAME'],
                'vite' => ['LOWER_NAME'],
                'json' => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE', 'PROVIDER_NAMESPACE', 'KEBAB_SINGULAR_TITLE'],
                'views/index' => ['LOWER_NAME'],
                'views/master' => ['LOWER_NAME', 'STUDLY_NAME'],
                'scaffold/config' => ['STUDLY_NAME'],
                'composer' => [
                    'LOWER_NAME',
                    'STUDLY_NAME',
                    'VENDOR',
                    'AUTHOR_NAME',
                    'AUTHOR_EMAIL',
                    'MODULE_NAMESPACE',
                    'PROVIDER_NAMESPACE',
                ],
                'webpack' => ['LOWER_NAME'],
                'package' => ['LOWER_NAME'],
                'readme' => ['STUDLY_NAME'],
                'test-case' => ['LOWER_NAME', 'STUDLY_NAME', 'PHP_MODULE_NAMESPACE'],
                'test-feature-example' => ['LOWER_NAME', 'STUDLY_NAME', 'PHP_MODULE_NAMESPACE'],
                'test-unit-example' => ['LOWER_NAME', 'STUDLY_NAME', 'PHP_MODULE_NAMESPACE'],
            ],
            'gitkeep' => true,
        ],

        'paths' => [
            /*
            |--------------------------------------------------------------------------
            | Modules path
            |--------------------------------------------------------------------------
            |
            | This path used for save the generated module. This path also will be added
            | automatically to list of scanned folders.
            |
            */

            'modules' => base_path('modules'),
            /*
            |--------------------------------------------------------------------------
            | Modules assets path
            |--------------------------------------------------------------------------
            |
            | Here you may update the modules assets path.
            |
            */

            'assets' => public_path('modules'),
            /*
            |--------------------------------------------------------------------------
            | The migrations path
            |--------------------------------------------------------------------------
            |
            | Where you run 'module:publish-migration' command, where do you publish the
            | the migration files?
            |
            */

            'migration' => base_path('database/migrations'),

            /*
            |--------------------------------------------------------------------------
            | Generator path
            |--------------------------------------------------------------------------
            | Customise the paths where the folders will be generated.
            | Set the generate key too false to not generate that folder
            */
            'generator' => [
                'config' => ['path' => 'config', 'generate' => false],
                'command' => ['path' => 'src/Commands', 'generate' => true, 'namespace' => 'Commands'],
                'migration' => ['path' => 'database/migrations', 'generate' => true],
                'seeder' => ['path' => 'database/seeders', 'generate' => false, 'namespace' => 'Database\\Seeders'],
                'factory' => ['path' => 'database/factories', 'generate' => true, 'namespace' => 'Database\\Factories'],
                'model' => ['path' => 'src/Models', 'generate' => true, 'namespace' => 'Models'],
                'routes' => ['path' => 'src/routes', 'generate' => true],
                'controller' => ['path' => 'src/Http/Controllers', 'generate' => true, 'namespace' => 'Http\\Controllers'],
                'filter' => ['path' => 'src/Http/Middleware', 'generate' => true, 'namespace' => 'Http\\Middleware'],
                'request' => ['path' => 'src/Http/Requests', 'generate' => true, 'namespace' => 'Http\\Requests'],
                'provider' => ['path' => 'src/Providers', 'generate' => true, 'namespace' => 'Providers'],
                'assets' => ['path' => 'assets/public', 'generate' => false],
                'assets-js' => ['path' => 'assets/js', 'generate' => true],
                'assets-css' => ['path' => 'assets/css', 'generate' => true],
                'lang' => ['path' => 'src/resources/lang', 'generate' => true],
                'views' => ['path' => 'src/resources/views', 'generate' => true],
                'test' => ['path' => 'tests/Unit', 'generate' => true, 'namespace' => 'Tests\\Unit'],
                'test-feature' => ['path' => 'tests/Feature', 'generate' => true, 'namespace' => 'Tests\\Feature'],
                'repository' => ['path' => 'src/Repositories', 'generate' => false, 'namespace' => 'Repositories'],
                'event' => ['path' => 'src/Events', 'generate' => false, 'namespace' => 'Events'],
                'listener' => ['path' => 'src/Listeners', 'generate' => false, 'namespace' => 'Listeners'],
                'policies' => ['path' => 'src/Policies', 'generate' => false, 'namespace' => 'Policies'],
                'rules' => ['path' => 'src/Rules', 'generate' => false, 'namespace' => 'Rules'],
                'jobs' => ['path' => 'src/Jobs', 'generate' => false, 'namespace' => 'Jobs'],
                'emails' => ['path' => 'src/Emails', 'generate' => false, 'namespace' => 'Emails'],
                'notifications' => ['path' => 'src/Notifications', 'generate' => false, 'namespace' => 'Notifications'],
                'resource' => ['path' => 'src/Http/Resources', 'generate' => false, 'namespace' => 'Http\\Resources'],
                'component-view' => ['path' => 'src/resources/views/components', 'generate' => false],
                'component-class' => ['path' => 'src/View/Components', 'generate' => false, 'namespace' => 'View\\Components'],
                'facades' => ['path' => 'src/Facades', 'generate' => true, 'namespace' => 'Facades'],
                'datatable' => [
                    'path' => 'src/Http/DataTables',
                    'generate' => false,
                    'namespace' => 'Http/DataTables',
                    // Column has link to edit
                    'titleColumns' => [
                        'name',
                        'title',
                    ],
                    // Exclude columns of Datatable header
                    'excludeColumns' => [
                        'updated_at',
                        'deleted_at',
                        'content',
                        'description',
                        'locale',
                        'type',
                    ],

                    // Exclude actions of Datatable
                    'excludeActions' => [
                        'restore',
                        'forceDelete',
                    ],
                ],
            ],
        ],
    ],
];
