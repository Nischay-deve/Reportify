<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'ThisDateThatYear'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL', null),

    'upload_image_screenshot' => env('UPLOAD_IMAGE_SCREENSHOT', null),
    'upload_image_featured_image' => env('UPLOAD_IMAGE_FEATURED_IMAGE', null),
    'upload_documents' => env('UPLOAD_DOCUMENTS', null),
    'upload_focus_event_documents' => env('UPLOAD_FOCUS_EVENT_DOCUMENTS', null),

    'color_scheme' => env('COLOR_SCHEME', null),

    'basic_user_role_id' => env('BASIC_USER_ROLE_ID', null),
    'admin_user_role_id' => env('ADMIN_USER_ROLE_ID', null),
    'master_admin_users' => env('MASTER_ADMIN_USERS', null),

    'app_domain' => env('APP_DOMAIN',null),

    'info_db_database' => env('INFO_DB_DATABASE',null),
    'issue_db_database' => env('ISSUE_DB_DATABASE',null),
    'tdty_db_database' => env('TDTY_DB_DATABASE',null),
    'setfacts_db_database' => env('SETFACTS_DB_DATABASE',null),

    'app_domain_info' => env('APP_DOMAIN_INFO',null),
    'app_domain_issue' => env('APP_DOMAIN_ISSUE',null),

    'show_faqs' => env('SHOW_FAQS',null),
    'show_documents' => env('SHOW_DOCUMENTS',null),

    // sendGrid
    'sendgrid_api_key' => env('SENDGRID_API_KEY', false),
    'sendgrid_from_email' => env('SENDGRID_FROM_EMAIL', false),
    'sendgrid_from_name' => env('SENDGRID_FROM_NAME', false),

    'sendgrid_signup_template' => env('SENDGRID_SIGNUP_TEMPLATE', false),
    'sendgrid_password_reset_template' => env('SENDGRID_PASSWORD_RESET_TEMPLATE', false),

    'sendgrid_user_request_template' => env('SENDGRID_USER_REQUEST_TEMPLATE', false),
    'user_request_email_send_to' => env('USER_REQUEST_EMAIL_SEND_TO', false),
    'request_email_subject' => env('REQUEST_EMAIL_SUBJECT', false),
    
    'view_report_url' => env('VIEW_REPORT_URL', false),
    'view_similar_report_url' => env('VIEW_SIMILAR_REPORT_URL', false),
    
    'show_faqs' => env('SHOW_FAQS',null),
    'show_documents' => env('SHOW_DOCUMENTS',null),

    'sendgrid_otp_login_template' => env('SENDGRID_OTP_LOGIN_TEMPLATE',null),

    'share_this_property' => env('SHARE_THIS_PROPERTY',null),
       
    ////////////////
    // s3 images
    ////////////////
    'active_disk_for_images' => env('ACTIVE_DISK_FOR_IMAGES', false),
    'aws_bucket_images_url' => env('AWS_BUCKET_IMAGES_URL', false),
    'aws_access_key_id_images' => env('AWS_ACCESS_KEY_ID_IMAGES', false),
    'aws_secret_access_key_images' => env('AWS_SECRET_ACCESS_KEY_IMAGES', false),    
    'memory_limit_media_manager' => env('MEMORY_LIMIT_MEDIA_MANAGER', 1024),
    'upload_to_public_storage' => env('UPLOAD_TO_PUBLIC_STORAGE', false),
        
    'per_page_reports' => env('PER_PAGE_REPORTS',false),
    
    'allow_mail_sending' => env('ALLOW_MAIL_SENDING',false),
    
    
    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,
         Barryvdh\DomPDF\ServiceProvider::class,
        Tymon\JWTAuth\Providers\LaravelServiceProvider::class,
        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,


        Maatwebsite\Excel\ExcelServiceProvider::class,
        Spatie\DirectoryCleanup\DirectoryCleanupServiceProvider::class,
        Jorenvh\Share\Providers\ShareServiceProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App' => Illuminate\Support\Facades\App::class,
        'Arr' => Illuminate\Support\Arr::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
        'Bus' => Illuminate\Support\Facades\Bus::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'Date' => Illuminate\Support\Facades\Date::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Http' => Illuminate\Support\Facades\Http::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'RateLimiter' => Illuminate\Support\Facades\RateLimiter::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        // 'Redis' => Illuminate\Support\Facades\Redis::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'Str' => Illuminate\Support\Str::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,
        'PDF' => Barryvdh\DomPDF\Facade::class,
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,
        'Helper' => App\Helpers\Helper::class,
        'Share' => Jorenvh\Share\ShareFacade::class,
    ],
];
