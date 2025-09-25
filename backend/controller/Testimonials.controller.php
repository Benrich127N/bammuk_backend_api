<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

require_once "../models/model.php";
require_once "../helpers/Utility.helper.php";

class TestimonialsController {
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
        $required = ["Testimonials_id", "cars_id", "content"];
        $validate = $this->utility->validateFields(self::$data, $required);
        if ($validate["error"]) {
            return ['statuscode' => 401, 'status' => $validate['error_msg'], 'data' => []];
        }
        $data = $validate["data"];
        $created = $this->model->createTestimonial($data);
        if ($created) {
            return $this->utility->jsonResponse(200, "Testimonial added successfully.", $data);
        }
        return $this->utility->jsonResponse(500, "Failed to add testimonial.", []);
    }

    public function view(): string|array
    {
        $fields = ['id','Testimonials_id','cars_id','content','created_at'];
        $testimonials = $this->model->listTestimonials($fields);
        if ($testimonials !== false) {
            return $this->utility->jsonResponse(200, "Testimonials fetched successfully.", $testimonials);
        }
        return $this->utility->jsonResponse(404, "No testimonials found.", []);
    }
}


