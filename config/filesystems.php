<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

        // NativePHP User Directory Disks
        'user_home' => [
            'driver' => 'local',
            'root' => function_exists('app') && app()->bound('native') ?
                \Native\Laravel\Facades\System::userHomeDirectory() :
                (PHP_OS_FAMILY === 'Windows' ? 'C:\\Users\\' . get_current_user() : '/home/' . get_current_user()),
            'throw' => false,
            'report' => false,
        ],

        'desktop' => [
            'driver' => 'local',
            'root' => function_exists('app') && app()->bound('native') ?
                \Native\Laravel\Facades\System::userDesktopDirectory() :
                (PHP_OS_FAMILY === 'Windows' ? 'C:\\Users\\' . get_current_user() . '\\Desktop' : '/home/' . get_current_user() . '/Desktop'),
            'throw' => false,
            'report' => false,
        ],

        'documents' => [
            'driver' => 'local',
            'root' => function_exists('app') && app()->bound('native') ?
                \Native\Laravel\Facades\System::userDocumentsDirectory() :
                (PHP_OS_FAMILY === 'Windows' ? 'C:\\Users\\' . get_current_user() . '\\Documents' : '/home/' . get_current_user() . '/Documents'),
            'throw' => false,
            'report' => false,
        ],

        'downloads' => [
            'driver' => 'local',
            'root' => function_exists('app') && app()->bound('native') ?
                \Native\Laravel\Facades\System::userDownloadsDirectory() :
                (PHP_OS_FAMILY === 'Windows' ? 'C:\\Users\\' . get_current_user() . '\\Downloads' : '/home/' . get_current_user() . '/Downloads'),
            'throw' => false,
            'report' => false,
        ],

        'music' => [
            'driver' => 'local',
            'root' => function_exists('app') && app()->bound('native') ?
                \Native\Laravel\Facades\System::userMusicDirectory() :
                (PHP_OS_FAMILY === 'Windows' ? 'C:\\Users\\' . get_current_user() . '\\Music' : '/home/' . get_current_user() . '/Music'),
            'throw' => false,
            'report' => false,
        ],

        'pictures' => [
            'driver' => 'local',
            'root' => function_exists('app') && app()->bound('native') ?
                \Native\Laravel\Facades\System::userPicturesDirectory() :
                (PHP_OS_FAMILY === 'Windows' ? 'C:\\Users\\' . get_current_user() . '\\Pictures' : '/home/' . get_current_user() . '/Pictures'),
            'throw' => false,
            'report' => false,
        ],

        'videos' => [
            'driver' => 'local',
            'root' => function_exists('app') && app()->bound('native') ?
                \Native\Laravel\Facades\System::userVideosDirectory() :
                (PHP_OS_FAMILY === 'Windows' ? 'C:\\Users\\' . get_current_user() . '\\Videos' : '/home/' . get_current_user() . '/Videos'),
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
