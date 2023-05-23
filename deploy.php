<?php

namespace Deployer;

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config/application.php';

use Roots\WPConfig\Config;

require 'recipe/wordpress.php';

// Config

set('repository', 'git@github.com:boyer-nicolas/wptest.git');

add('shared_files', []);

add('shared_dirs', [
    'public/wp-content/uploads',
    'sql',
    'vendor'
]);

add('writable_dirs', []);

// Hosts
// Production
host(Config::get('SSH_PRODUCTION_HOST'))
    ->set('alias', 'production')
    ->set('hostname', Config::get('SSH_PRODUCTION_HOST'))
    ->set('remote_user', Config::get('SSH_PRODUCTION_USER'))
    ->set('deploy_path', Config::get('SSH_PRODUCTION_DIR'))
    ->set('branch', 'production')
    ->set('shell', '/bin/zsh')
    ->set('identity_file', Config::get('SSH_PRODUCTION_IDENTITY_FILE'))
    ->set('keep_releases', 5);

// Staging
host(Config::get('SSH_STAGING_HOST'))
    ->set('alias', 'staging')
    ->set('hostname', Config::get('SSH_STAGING_HOST'))
    ->set('remote_user', Config::get('SSH_STAGING_USER'))
    ->set('deploy_path', Config::get('SSH_STAGING_DIR'))
    ->set('branch', 'staging')
    ->set('shell', '/bin/zsh')
    ->set('identity_file', Config::get('SSH_STAGING_IDENTITY_FILE'))
    ->set('keep_releases', 3);

// Tasks
/**
 * Ensure composer is installed on the environment
 */
task('ensure:composer', function ()
{
    run('curl -sS https://getcomposer.org/installer | php');
    run('mv composer.phar /usr/local/bin/composer');
});

/**
 * Ensure wp-cli is installed on the environment
 */
task('ensure:wpcli', function ()
{
    run('curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar');
    run('chmod +x wp-cli.phar');
    run('mv wp-cli.phar /usr/local/bin/wp');
});

/**
 * Install composer dependencies
 */
task('build:composer', function ()
{
    run('cd {{release_path}} && composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader');
});

/**
 * Pull the uploads & sql from the media server
 */
task('build:media', function ()
{
    run('cd {{release_path}} && composer media:pull');
});

/**
 * Import the database
 */
task('build:db', function ()
{
    run('cd {{release_path}} && wp db import {{deploy_path}}/shared/local.sql');
});

/**
 * Switch from local to production urls
 */
task('build:urls', function ()
{
    $TARGET_SITE_URL = Config::get('WP_SITEURL');
    run("cd {{release_path}} && wp search-replace --all-tables $(wp option get siteurl) $TARGET_SITE_URL");
});

/**
 * Clear the cache
 */
task('build:cache', function ()
{
    run('cd {{release_path}} && composer cache:clear');
});

task('upload:secrets', function ()
{
    upload(getenv('DOTENV'), '{{deploy_path}}/shared/.env');
});

task('build:all', [
    'upload:secrets',
    'build:composer',
    'build:media',
    'build:db',
    'build:urls',
    'build:cache',
]);

// Hooks

after('deploy:failed', 'deploy:unlock');
after('deploy:prepare', 'ensure:composer');
after('deploy:prepare', 'ensure:wpcli');
after('deploy:update_code', 'build:all');
