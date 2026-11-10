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
set('repository', 'git@github.com:mwidady/upos.git');

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

// Use the PATH-resolved CLI binary; the host's /usr/bin/php is not the CLI SAPI.
set('bin/php', 'php');

// Keep releases
set('keep_releases', 5);

// Install vendors
task('deploy:vendors', function () {
    // The release checkout lacks nested Git metadata from tracked vendor submodules.
    run('cd {{release_path}} && rm -rf vendor && composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-progress');
});

// Yii commands
task('deploy:yii', function () {
    run('cd {{release_path}} && {{bin/php}} yii cache/flush-all');
});

// After deployment
after('deploy:vendors', 'deploy:yii');
after('deploy:failed', 'deploy:unlock');
