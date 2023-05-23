<?php

/**
 * Your base production configuration goes in this file. Environment-specific
 * overrides go in their respective config/environments/{{WP_ENV}}.php file.
 *
 * A good default policy is to deviate from the production config as little as
 * possible. Try to define as much of your configuration in this file as you
 * can.
 */

use Roots\WPConfig\Config;
use function Env\env;

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

/**
 * Directory containing all of the site's files
 *
 * @var string
 */
$root_dir = dirname(__DIR__);

/**
 * Document Root
 *
 * @var string
 */
$webroot_dir = $root_dir . '/public';

/**
 * Use Dotenv to set required environment variables and load .env file in root
 * .env.local will override .env if it exists
 */
if (file_exists($root_dir . '/.env'))
{
    $env_files = file_exists($root_dir . '/.env.local')
        ? ['.env', '.env.local']
        : ['.env'];

    $dotenv = Dotenv\Dotenv::createUnsafeImmutable($root_dir, $env_files, false);

    $dotenv->load();

    $dotenv->required(['WP_ENV']);

    define('WP_ENV', env('WP_ENV'));

    /**
     * Set up our global environment constant and load its config first
     * Default: production
     */

    switch (WP_ENV)
    {
        case 'development':
            $dotenv->required([
                'WP_DEV_URL',
                'MYSQL_DEVELOPMENT_DB_NAME',
                'MYSQL_DEVELOPMENT_DB_USER',
                'MYSQL_DEVELOPMENT_DB_PASSWORD',
                'MYSQL_DEVELOPMENT_DB_HOST',
                'WP_ENV'
            ]);
            break;

        case 'staging':
            $dotenv->required([
                'WP_STAGING_URL',
                'MYSQL_STAGING_DB_NAME',
                'MYSQL_STAGING_DB_USER',
                'MYSQL_STAGING_DB_PASSWORD',
                'MYSQL_STAGING_DB_HOST',
                'WP_ENV'
            ]);
            break;

        case 'production':
            $dotenv->required([
                'WP_PRODUCTION_URL',
                'MYSQL_PRODUCTION_DB_NAME',
                'MYSQL_PRODUCTION_DB_USER',
                'MYSQL_PRODUCTION_DB_PASSWORD',
                'MYSQL_PRODUCTION_DB_HOST',
                'WP_ENV'
            ]);
            break;

        default:
            $dotenv->required([
                'WP_PRODUCTION_URL',
                'MYSQL_PRODUCTION_DB_NAME',
                'MYSQL_PRODUCTION_DB_USER',
                'MYSQL_PRODUCTION_DB_PASSWORD',
                'MYSQL_PRODUCTION_DB_HOST',
                'WP_ENV'
            ]);
            break;
    }
}


if (WP_ENV != "development" && WP_ENV != "staging" && WP_ENV != "production")
{
    throw new Exception('WP_ENV must be set to either "development", "staging" or "production". Currently set to "' . WP_ENV . '".');
}

/**
 * Infer WP_ENVIRONMENT_TYPE based on WP_ENV
 */
if (!env('WP_ENVIRONMENT_TYPE') && in_array(WP_ENV, ['production', 'staging', 'development']))
{
    Config::define('WP_ENVIRONMENT_TYPE', WP_ENV);
}

/**
 * URLs
 */
Config::define('WP_HOME', env('WP_HOME'));
Config::define('WP_SITEURL', env('WP_SITEURL'));

/**
 * Custom Content Directory
 */
Config::define('CONTENT_DIR', '/wp-content');
Config::define('WP_CONTENT_DIR', $webroot_dir . Config::get('CONTENT_DIR'));
Config::define('WP_CONTENT_URL', Config::get('WP_HOME') . Config::get('CONTENT_DIR'));

/**
 * DB settings
 */
if (env('DB_SSL'))
{
    Config::define('MYSQL_CLIENT_FLAGS', MYSQLI_CLIENT_SSL);
}

Config::define('DB_NAME', env('MYSQL_DATABASE'));
Config::define('DB_USER', env('MYSQL_USER'));
Config::define('DB_PASSWORD', env('MYSQL_PASSWORD'));

if (null !== env('MYSQL_HOST') && null !== env('MYSQL_PORT'))
{
    Config::define('DB_HOST', env('MYSQL_HOST') . ':' . env('MYSQL_PORT'));
}
else
{
    Config::define('DB_HOST', env('MYSQL_HOST') ?: 'localhost');
}

$table_prefix = env('MYSQL_TABLE_PREFIX') ?: 'wp_';

Config::define('DB_CHARSET', 'utf8mb4');
Config::define('DB_COLLATE', '');

if (env('DATABASE_URL'))
{
    $dsn = (object) parse_url(env('DATABASE_URL'));

    Config::define('DB_NAME', substr($dsn->path, 1));
    Config::define('DB_USER', $dsn->user);
    Config::define('DB_PASSWORD', isset($dsn->pass) ? $dsn->pass : null);
    Config::define('DB_HOST', isset($dsn->port) ? "{$dsn->host}:{$dsn->port}" : $dsn->host);
}

/**
 * Authentication Unique Keys and Salts
 */
Config::define('AUTH_KEY', env('AUTH_KEY'));
Config::define('SECURE_AUTH_KEY', env('SECURE_AUTH_KEY'));
Config::define('LOGGED_IN_KEY', env('LOGGED_IN_KEY'));
Config::define('NONCE_KEY', env('NONCE_KEY'));
Config::define('AUTH_SALT', env('AUTH_SALT'));
Config::define('SECURE_AUTH_SALT', env('SECURE_AUTH_SALT'));
Config::define('LOGGED_IN_SALT', env('LOGGED_IN_SALT'));
Config::define('NONCE_SALT', env('NONCE_SALT'));

/**
 * Custom Settings
 */
Config::define('AUTOMATIC_UPDATER_DISABLED', true);
Config::define('DISABLE_WP_CRON', env('DISABLE_WP_CRON') ?: false);

// Disable the plugin and theme file editor in the admin
Config::define('DISALLOW_FILE_EDIT', true);

// Disable plugin and theme updates and installation from the admin
Config::define('DISALLOW_FILE_MODS', true);

// Limit the number of post revisions
Config::define('WP_POST_REVISIONS', env('WP_POST_REVISIONS') ?? true);

/**
 * Debugging Settings
 */
Config::define('WP_DEBUG_DISPLAY', false);
Config::define('WP_DEBUG_LOG', false);
Config::define('SCRIPT_DEBUG', false);
ini_set('display_errors', '0');

/**
 * Allow WordPress to detect HTTPS when used behind a reverse proxy or a load balancer
 * See https://codex.wordpress.org/Function_Reference/is_ssl#Notes
 */
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
{
    $_SERVER['HTTPS'] = 'on';
}

$env_config = __DIR__ . '/environments/' . WP_ENV . '.php';

if (file_exists($env_config))
{
    require_once $env_config;
}

Config::apply();

/**
 * Bootstrap WordPress
 */
if (!defined('ABSPATH'))
{
    define('ABSPATH', $webroot_dir);
}

$whoops->unregister();
