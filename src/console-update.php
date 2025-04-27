<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\MainController as Main;
/**
 *  add to cron
 *  example:
 *  0 0 * * * /usr/bin/php /var/www/html/console-update.php
 * 
 */
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$main=new Main($_ENV);
// check is env variables are set
$dotenv->required($main->envVariablesList);

if($main->checkEnvSetup()){
    $main->reloadData();
}

?>