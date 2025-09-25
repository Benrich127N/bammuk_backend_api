<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
require_once "../controller/Testimonials.controller.php";

function testimonialsRoutes($endpoint, $data, $path)
{
    switch ($endpoint) {
        case 'add':
            $response = getTestimonialsInstance('add', 'TestimonialsController', $data);
            break;
        case 'view':
            $response = getTestimonialsInstance('view', 'TestimonialsController', $data);
            break;
        default:
            $response = "404 Not Found";
        break;
    }
    return $response;
}

function getTestimonialsInstance($endpoint, $className, $data)
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


