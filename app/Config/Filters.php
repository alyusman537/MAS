<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;

use App\Filters\AuthFilter;
use App\Filters\AdminAuth;
use App\Filters\Cors;

class Filters extends BaseConfig
{
    /**
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     *
     * @var array<string, class-string|list<class-string>> [filter_name => classname]
     *                                                     or [filter_name => [classname1, classname2, ...]]
     */
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'auth'          => AuthFilter::class,
        'admin'          => AdminAuth::class,
        'cors'          => Cors::class,
    ];

    /**
     * List of filter aliases that are always
     * applied before and after every request.
     *
     * @var array<string, array<string, array<string, string>>>|array<string, list<string>>
     */
    public array $globals = [
        'before' => [
            // 'honeypot',
            // 'csrf',
            // 'invalidchars',
            'cors',
            'auth' => [
                'except' => [
                    '/api/render/*',
                    '/api/user-login',
                    '/api/admin*',
                    '/login',
                    '/reset-password',
                    '/profile',
                    '/infaq',
                    '/infaq*',
                    '/laporan-kas',
                    '/',
                    ////////// ADMIN /////////////
                    '/administrator/login',
                    '/administrator/dashboard',
                    '/administrator/wilayah',
                    '/administrator/anggota',
                    '/administrator/infaq',
                    '/administrator/penerimaan-infaq',
                    '/administrator/penerimaan-infaq-umum',
                    '/administrator/transaksi-kas',
                    '/administrator/laporan-kas',

                    ////////////
                    '/api/pdf/*',
                    '/api/excel/*',
                    '/api/user/minta-otp',
                    '/api/user/kirim-otp',
                ]
            ],
            'admin' => [
                'except' => [
                    '/api/render/*',
                    '/api/admin-login',
                    '/api/user*',
                    '/login',
                    '/reset-password',
                    '/profile',
                    '/infaq',
                    '/infaq*',
                    '/laporan-kas',
                    '/',
                    ////////// ADMIN /////////////
                    '/administrator/login',
                    '/administrator/dashboard',
                    '/administrator/wilayah',
                    '/administrator/anggota',
                    '/administrator/infaq',
                    '/administrator/penerimaan-infaq',
                    '/administrator/penerimaan-infaq-umum',
                    '/administrator/transaksi-kas',
                    '/administrator/laporan-kas',

                    ///////////
                    '/api/pdf/*',
                    '/api/excel/*',
                    '/api/user/minta-otp',
                    '/api/user/kirim-otp',
                ]
            ],
        ],
        'after' => [
            'toolbar',
            // 'honeypot',
            // 'secureheaders',
        ],
    ];

    /**
     * List of filter aliases that works on a
     * particular HTTP method (GET, POST, etc.).
     *
     * Example:
     * 'post' => ['foo', 'bar']
     *
     * If you use this, you should disable auto-routing because auto-routing
     * permits any HTTP method to access a controller. Accessing the controller
     * with a method you don't expect could bypass the filter.
     *
     * @var array<string, list<string>>
     */
    public array $methods = [];

    /**
     * List of filter aliases that should run on any
     * before or after URI patterns.
     *
     * Example:
     * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
     *
     * @var array<string, array<string, list<string>>>
     */
    public array $filters = [];
}
