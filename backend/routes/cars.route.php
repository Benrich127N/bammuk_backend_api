<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
require_once "../controller/Cars.controller.php";

function carsRoutes($endpoint, $data, $path)
{
    switch ($endpoint) {
        case 'add':
            $response = getCarsInstance('add', 'CarsController', $data);
            break;
        case 'view':
            $response = getCarsInstance('view', 'CarsController', $data);
            break;
        default:
            $response = "404 Not Found";
        break;
    }
    return $response;
}

function getCarsInstance($endpoint, $className, $data)
{
    if (!class_exists($className)) {
        return "Class not found.";
    }
    $classInstance = new $className($data);
    if (!method_exists($classInstance, $endpoint)) {
        return "Method not found.";
    }
    return call_user_func_array([$classInstance, $endpoint], [$data]);
}


