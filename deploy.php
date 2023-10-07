<?php
namespace Deployer;

require 'recipe/laravel.php';

// Config
set('application', 'khien');
set('keep_releases', 2);
set('repository', 'git@github.com:mbvb1223/copy-excel.git');


//add('shared_files', []);
//add('shared_dirs', []);
//add('writable_dirs', []);

// Hosts

host('52.221.129.246')
    ->set('remote_user', 'ubuntu')
    ->set('branch', 'master')
    ->set('identity_file', '../khienlien1223.pem')
    ->set('deploy_path', '~/{{application}}');

desc('Deploys your project');
task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'artisan:storage:link',
    'artisan:config:cache',
//    'artisan:route:cache',
    'artisan:view:cache',
    'artisan:event:cache',
    'artisan:migrate',
    'deploy:publish',
]);
