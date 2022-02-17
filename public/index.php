<?php
require "../bootstrap.php";
use Src\Controller\BranchController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$endPoint = str_replace(getenv('SITE_URL'), '', $_SERVER['REQUEST_URI']);
$endPointArray = explode( '/', $endPoint);

if(($endPointArray[0] != 'branch')||(count($endPointArray) > 2)){
    header("HTTP/1.1 404 Not Found");
    exit();
}

$requestMethod = $_SERVER["REQUEST_METHOD"];

// pass the request method and user ID to the PersonController and process the HTTP request:
$controller = new BranchController($dbConnection, $requestMethod);
$controller->processRequest();