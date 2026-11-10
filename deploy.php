<?php

namespace Deployer;

// require 'recipe/yii2.php';
require 'recipe/yii.php';

// Override the migration task from recipe/yii.php
// task('deploy:migrate', function () {
//     writeln('<comment>Database migration skipped.</comment>');
// });

// Project name
set('application', 'upos');

// Git repository
set('repository', 'git@github.com:mwidady/property.git');

// Remote server
host('production')
// mvjozi0s8ntm@198.12.238.178
    ->setHostname('198.12.238.178')
    ->setRemoteUser('mvjozi0s8ntm')
    ->setPort(22)
    ->setDeployPath('/home/mvjozi0s8ntm/public_html/upos');

set('writable_mode', 'chmod');

// Shared files
add('shared_files', [
    'config/db-local.php',
]);

// Shared directories
add('shared_dirs', [
    'runtime',
    'web/assets',
]);

// Writable directories
add('writable_dirs', [
    'runtime',
    'web/assets',
]);

// Composer
set('bin/composer', '/usr/bin/composer');

// PHP
set('bin/php', '/usr/bin/php');

// Keep releases
set('keep_releases', 5);

// Install vendors
task('deploy:vendors', function () {
    run('cd {{release_path}} && composer install --no-dev --prefer-dist --optimize-autoloader');
});

// Yii commands
task('deploy:yii', function () {
    run('cd {{release_path}} && php yii cache/flush-all');
});

// After deployment
after('deploy:vendors', 'deploy:yii');
after('deploy:failed', 'deploy:unlock');