<?php

/**
 * Configuration overrides for WP_ENV === 'staging'
 */

@ini_set('upload_max_size', '64M');
@ini_set('post_max_size', '64M');
@ini_set('max_execution_time', '300');

use Roots\WPConfig\Config;
use function Env\env;

/**
 * You should try to keep staging as close to production as possible. However,
 * should you need to, you can always override production configuration values
 * with `Config::define`.
 *
 * Example: `Config::define('WP_DEBUG', true);`
 * Example: `Config::define('DISALLOW_FILE_MODS', false);`
 */

Config::define('WP_DEBUG', true);
Config::define('WP_DEBUG_DISPLAY', false);
Config::define('WP_DEBUG_LOG', true);


Config::define('DISALLOW_INDEXING', true);
Config::define('JETPACK_DEV_DEBUG', false);

Config::define('WP_REDIS_HOST', env('REDIS_HOST'));
Config::define('WP_REDIS_PORT', env('REDIS_PORT'));

Config::define('WP_SITEURL', env('WP_STAGING_URL'));
Config::define('WP_HOME', env('WP_STAGING_URL'));

Config::define('DB_NAME', env('MYSQL_STAGING_DB_NAME'));
Config::define('DB_USER', env('MYSQL_STAGING_DB_USER'));
Config::define('DB_PASSWORD', env('MYSQL_STAGING_DB_PASSWORD'));

if (null !== env('MYSQL_STAGING_DB_HOST') && null !== env('MYSQL_STAGING_DB_PORT'))
{
    Config::define('DB_HOST', env('MYSQL_STAGING_DB_HOST') . ':' . env('MYSQL_STAGING_DB_PORT'));
}
else
{
    Config::define('DB_HOST', env('MYSQL_STAGING_DB_HOST') ?: 'localhost');
}

$table_prefix = env('MYSQL_STAGING_DB_TABLE_PREFIX') ?: 'wp_';

if ($_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
{
    $_SERVER['HTTPS'] = 'on';
}

if (isset($_SERVER['HTTP_X_FORWARDED_HOST']))
{
    $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_X_FORWARDED_HOST'];
}
