<?php

/**
 * Configuration overrides for WP_ENV === 'development'
 */

@ini_set('upload_max_size', '64M');
@ini_set('post_max_size', '64M');
@ini_set('max_execution_time', '300');

use Roots\WPConfig\Config;
use function Env\env;

Config::define('SAVEQUERIES', true);
Config::define('WP_DEBUG', true);
Config::define('WP_DEBUG_DISPLAY', true);
Config::define('WP_DEBUG_LOG', true);


// Config::define('WP_DISABLE_FATAL_ERROR_HANDLER', true);
Config::define('SCRIPT_DEBUG', true);
Config::define('DISALLOW_INDEXING', true);
Config::define('FS_METHOD', 'direct');
Config::define('WP_REDIS_HOST', env('REDIS_HOST'));
Config::define('WP_REDIS_PORT', env('REDIS_PORT'));

Config::define('WPMS_MAIL_FROM_FORCE', true); // True turns it on, false turns it off.
Config::define('WPMS_MAIL_FROM_NAME', env('SMTP_NAME'));
Config::define('WPMS_MAIL_FROM_NAME_FORCE', true); // True turns it on, false turns it off.
Config::define('WPMS_MAILER', 'smtp'); // Possible values: 'mail', 'gmail', 'mailgun', 'sendgrid', 'smtp'.
Config::define('WPMS_SET_RETURN_PATH', true); // Sets $phpmailer->Sender if true.
Config::define('WPMS_DO_NOT_SEND', false); // Possible values: true, false.
Config::define('WPMS_SMTP_HOST', env('SMTP_HOST')); // The SMTP mail host.
Config::define('WPMS_SMTP_PORT', env('SMTP_PORT')); // The SMTP server port number.
Config::define('WPMS_SMTP_AUTOTLS', true); // True turns it on, false turns it off.
Config::define('WPMS_MAIL_FROM', env('SMTP_EMAIL')); // The SMTP SMTPFrom email address.
Config::define('WP_SITEURL', env('WP_DEV_URL'));
Config::define('WP_HOME', env('WP_DEV_URL'));

Config::define('DB_NAME', env('MYSQL_DEVELOPMENT_DB_NAME'));
Config::define('DB_USER', env('MYSQL_DEVELOPMENT_DB_USER'));
Config::define('DB_PASSWORD', env('MYSQL_DEVELOPMENT_DB_PASSWORD'));

if (null !== env('MYSQL_DEVELOPMENT_DB_HOST') && null !== env('MYSQL_DEVELOPMENT_DB_PORT'))
{
    Config::define('DB_HOST', env('MYSQL_DEVELOPMENT_DB_HOST') . ':' . env('MYSQL_DEVELOPMENT_DB_PORT'));
}
else
{
    Config::define('DB_HOST', env('MYSQL_DEVELOPMENT_DB_HOST') ?: 'localhost');
}

$table_prefix = env('MYSQL_DEVELOPMENT_DB_TABLE_PREFIX') ?: 'wp_';
