<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

require_once "../models/model.php";
require_once "../helpers/Utility.helper.php";

class CarsController {
    private static $data;
    private $model;
    private $utility;

    public function __construct($data = []) {
        self::$data = $data;
        $this->model = new Models();
        $this->utility = new UtilityHelper();
    }

    public function add(): string|array
    {
        $required = ["cars_id", "cars_name", "price"];
        $validate = $this->utility->validateFields(self::$data, $required);
        if ($validate["error"]) {
            return ['statuscode' => 401, 'status' => $validate['error_msg'], 'data' => []];
        }
        $data = $validate["data"];
        $created = $this->model->createCar($data);
        if ($created) {
            return $this->utility->jsonResponse(200, "Car added successfully.", $data);
        }
        return $this->utility->jsonResponse(500, "Failed to add car.", []);
    }

    public function view(): string|array
    {
        $fields = ['id','cars_id','cars_name','price','created_at'];
        $cars = $this->model->listCars($fields);
        if ($cars !== false) {
            return $this->utility->jsonResponse(200, "Cars fetched successfully.", $cars);
        }
        return $this->utility->jsonResponse(404, "No cars found.", []);
    }
}


