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
        // Optional details
        $data["brand"] = self::$data["brand"] ?? null;
        $data["model"] = self::$data["model"] ?? null;
        $data["body_style"] = self::$data["body_style"] ?? null;
        $data["car_condition"] = self::$data["car_condition"] ?? null;
        $data["fuel_type"] = self::$data["fuel_type"] ?? null;
        $data["year"] = self::$data["year"] ?? null;
        $created = $this->model->createCar($data);
        if ($created) {
            return $this->utility->jsonResponse(200, "Car added successfully.", $data);
        }
        return $this->utility->jsonResponse(500, "Failed to add car.", []);
    }

    public function view(): string|array
    {
        $fields = ['id','cars_id','cars_name','price','brand','model','body_style','car_condition','fuel_type','year','created_at'];
        $cars = $this->model->listCars($fields);
        if ($cars !== false) {
            return $this->utility->jsonResponse(200, "Cars fetched successfully.", $cars);
        }
        return $this->utility->jsonResponse(404, "No cars found.", []);
    }

    public function buy(): string|array
    {
        $required = ["user_id", "cars_id", "price"];
        $validate = $this->utility->validateFields(self::$data, $required);
        if ($validate["error"]) {
            return ['statuscode' => 401, 'status' => $validate['error_msg'], 'data' => []];
        }
        $data = $validate["data"];
        $created = $this->model->createPurchase($data);
        if ($created) {
            return $this->utility->jsonResponse(200, "Purchase recorded.", $data);
        }
        return $this->utility->jsonResponse(500, "Failed to record purchase.", []);
    }

    public function rent(): string|array
    {
        $required = ["user_id", "car_id", "start_date", "end_date"];
        $validate = $this->utility->validateFields(self::$data, $required);
        if ($validate["error"]) {
            return ['statuscode' => 401, 'status' => $validate['error_msg'], 'data' => []];
        }
        $data = $validate["data"];
        $data["daily_rate"] = self::$data["daily_rate"] ?? null;
        $created = $this->model->createRental($data);
        if ($created) {
            return $this->utility->jsonResponse(200, "Rental recorded.", $data);
        }
        return $this->utility->jsonResponse(500, "Failed to record rental.", []);
    }

    public function options(): string|array
    {
        $options = [
            'brand' => [],
            'model' => [],
            'body_style' => ['Sedan','Hatchback','SUV','Truck','Coupe','Convertible','Wagon','Van'],
            'car_condition' => ['New','Used','Certified'],
            'fuel_type' => ['Petrol','Diesel','Hybrid','Electric','CNG'],
            'year' => range((int)date('Y'), (int)date('Y') - 30)
        ];
        return $this->utility->jsonResponse(200, "Car options.", $options);
    }
}


