<?php
/**
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 * @license LGPL-2.1+
 */
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);
ini_set('error_log', __DIR__.'/../logs/error.log');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/checkSetup.php';
require __DIR__ . '/../src/tools.php';


use Posibrain\RestApi\RestApiController;
$app = new RestApiController();
$app->actionGetPositrons();
echo "\n";
echo "\n";
$app->actionGetBots();
echo "\n";
echo "\n";
$app->actionSubmit('Fylhan', 'Hello !', time());