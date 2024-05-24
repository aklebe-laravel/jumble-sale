<?php

use Illuminate\Support\Facades\Facade;

return [


    /*
    |--------------------------------------------------------------------------
    | Deployment settings
    |--------------------------------------------------------------------------
    |
    | Deployment relevant settings used by console commands to deploy data.
    |
    */
    'deployment'            => [
        'data_git_url'      => env('JUMBLE_SALE_DEPLOYMENT_DATA_GIT_URL', ''),
        'data_git_location' => env('JUMBLE_SALE_DEPLOYMENT_DATA_GIT_LOCATION', ''),
        'profiles'          => [
            /**
             * Testing migration only
             */
            'test-migration' => [
                'app_env'                 => 'local',
                'deployment_branch'       => 'master',
                'deployment_prevent_pull' => env('JUMBLE_SALE_DEPLOYMENT_DATA_GIT_PREVENT_PULL', false),
                'artisan_commands'        => [
                    [
                        'cmd'     => 'migrate:refresh',
                        'options' => [
                            '--no-interaction' => true,
                        ],
                    ],
                    [
                        'cmd'     => 'deploy:env-git-data',
                        'options' => [
                            'env'              => 'local',
                            '--no-interaction' => true,
                        ],
                    ],
                    [
                        'cmd'     => 'db:seed',
                        'options' => [
                            '--no-interaction' => true,
                        ],
                    ],
                ],
            ],
            /**
             * test deployment scripts only
             */
            'test-scripts'   => [
                'app_env'                 => 'local',
                'deployment_branch'       => 'master',
                'deployment_scripts'      => [
                    //                    'init-test.json',
                    //                    'system.json',
                    'media-files.json',
                ],
                'deployment_prevent_pull' => env('JUMBLE_SALE_DEPLOYMENT_DATA_GIT_PREVENT_PULL', false),
                'artisan_commands'        => [
                    [
                        'cmd'     => 'deploy:env-git-data',
                        'options' => [
                            'env' => 'local',
                            //                            '--no-interaction' => true,
                        ],
                    ],
                ],
            ],
            /**
             * local
             */
            'local'          => [
                'app_env'                 => 'local', //                'deployment_branch' => 'staging-local',
                'deployment_branch'       => 'master',
                'deployment_prevent_pull' => env('JUMBLE_SALE_DEPLOYMENT_DATA_GIT_PREVENT_PULL', false),
                'artisan_commands'        => [
                    [
                        'cmd'     => 'migrate:refresh',
                        'options' => [
                            '--no-interaction' => true,
                        ],
                    ],
                    [
                        'cmd'     => 'deploy:env-git-data',
                        'options' => [
                            'env'              => 'local',
                            '--no-interaction' => true,
                        ],
                    ],
                    [
                        'cmd'     => 'db:seed',
                        'options' => [
                            '--no-interaction' => true,
                        ],
                    ],
                    [
                        'cmd'     => 'deploy:env-media',
                        'options' => [
                            'env'              => 'local',
                            '--no-interaction' => true,
                        ],
                    ],
                ],
            ],
            /**
             * no media here
             */
            'local-quick'    => [
                'app_env'                 => 'local', //                'deployment_branch' => 'staging-local',
                'deployment_branch'       => 'master',
                'deployment_prevent_pull' => env('JUMBLE_SALE_DEPLOYMENT_DATA_GIT_PREVENT_PULL', false),
                'artisan_commands'        => [
                    [
                        'cmd'     => 'migrate:refresh',
                        'options' => [
                            '--no-interaction' => true,
                        ],
                    ],
                    [
                        'cmd'     => 'deploy:env-git-data',
                        'options' => [
                            'env'              => 'local-quick',
                            '--no-interaction' => true,
                        ],
                    ],
                    [
                        'cmd'     => 'db:seed',
                        'options' => [
                            '--no-interaction' => true,
                        ],
                    ],
                ],
            ],
            /**
             * production
             */
            'prod'           => [
                'app_env'                 => 'prod', //                'deployment_branch' => 'staging-prod',
                'deployment_branch'       => 'master',
                'deployment_prevent_pull' => env('JUMBLE_SALE_DEPLOYMENT_DATA_GIT_PREVENT_PULL', false),
                'artisan_commands'        => [
                    [
                        'cmd'     => 'migrate:refresh',
                        'options' => [
                            '--no-interaction' => true,
                        ],
                    ],
                    [
                        'cmd'     => 'deploy:env-git-data',
                        'options' => [
                            'env'              => 'prod',
                            '--no-interaction' => true,
                        ],
                    ], //                    [
                    //                        'cmd'     => 'db:seed',
                    //                        'options' => [
                    //                            '--no-interaction' => true,
                    //                        ],
                    //                    ],
                    [
                        'cmd'     => 'deploy:env-media',
                        'options' => [
                            'env'              => 'prod',
                            '--no-interaction' => true,
                        ],
                    ],
                ],
            ],
            /**
             * integration
             */
            'int'            => [
                'app_env'                 => 'int', //                'deployment_branch' => 'staging-int',
                'deployment_branch'       => 'master',
                'deployment_prevent_pull' => env('JUMBLE_SALE_DEPLOYMENT_DATA_GIT_PREVENT_PULL', false),
                'artisan_commands'        => [
                    [
                        'cmd'     => 'migrate:refresh',
                        'options' => [
                            '--no-interaction' => true,
                        ],
                    ],
                    [
                        'cmd'     => 'deploy:env-git-data',
                        'options' => [
                            'env'              => 'int',
                            '--no-interaction' => true,
                        ],
                    ],
                    [
                        'cmd'     => 'db:seed',
                        'options' => [
                            '--no-interaction' => true,
                        ],
                    ],
                    [
                        'cmd'     => 'deploy:env-media',
                        'options' => [
                            'env'              => 'int',
                            '--no-interaction' => true,
                        ],
                    ],
                ],
            ],
            /**
             * dev (external)
             */
            'dev'            => [
                'app_env'                 => 'dev', //                'deployment_branch' => 'staging-dev',
                'deployment_branch'       => 'master',
                'deployment_prevent_pull' => env('JUMBLE_SALE_DEPLOYMENT_DATA_GIT_PREVENT_PULL', false),
                'artisan_commands'        => [
                    [
                        'cmd'     => 'migrate:refresh',
                        'options' => [
                            '--no-interaction' => true,
                        ],
                    ],
                    [
                        'cmd'     => 'deploy:env-git-data',
                        'options' => [
                            'env'              => 'dev',
                            '--no-interaction' => true,
                        ],
                    ],
                    [
                        'cmd'     => 'db:seed',
                        'options' => [
                            '--no-interaction' => true,
                        ],
                    ],
                    [
                        'cmd'     => 'deploy:env-media',
                        'options' => [
                            'env'              => 'dev',
                            '--no-interaction' => true,
                        ],
                    ],
                ],
            ],
        ],
    ], // 'store'  => 'redis',

    // run queue by cron?
    'run_queue_in_schedule' => true,

];
