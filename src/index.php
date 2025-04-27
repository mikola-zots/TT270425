<?php
require_once __DIR__ . '/vendor/autoload.php';
use App\MainController;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$main=new MainController($_ENV);

// check is env variables are set
$dotenv->required($main->envVariablesList);

if($main->checkEnvSetup()){
    $main->index();
}
?>
