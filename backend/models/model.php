<?php
require_once "../data/Crud.data.php";

class Models
{
    public $table;
    private $conn;
    protected static $crud;

    public function __construct()
    {
        self::$crud = new Crud();
    }
    
    public function createUser($data) {
        $password = password_hash($data["password"], PASSWORD_DEFAULT);
        $result = self::$crud->create("users", [
            "email" => $data["email"],
            "password" => $password
        ]);
        return $result;
    }
    
    public function getUserByEmail($email) {
        $result = self::$crud->findByEmail("users", $email);
        if ($result) {
            return $result;
        } else {
            return null;
        }
    }
    

    /**
     * Log in a user with email and password
     * @param array $data Contains 'email' and 'password'
     * @return array Returns an array with status code and user data if login is successful, or an error message if not.
     * @throws Exception If the user is not found or the password is incorrect.
     */
    public function loginUser($data) {
        $result = self::$crud->findByEmail("users", $data["email"]);
        if (is_array($result) && isset($result['email'])){
            $hashedPassword = $result['password'];
            if (password_verify($data["password"], $hashedPassword)) {
                return ['statuscode' => 200, 'data'=>$result]; // Login successful
            } else {
                return ['statuscode' => 404, 'data' => []]; // Invalid password
            }
        } else {
            return ['statuscode' => 404, 'data' => []]; // User not found
        }
    }

    /**
     * Satrt a session for the user
     * @param array $userData Contains user data to be stored in the session
     * @return string|array Returns a JSON response with the session data or an error message.
     * @throws Exception If the session cannot be started.
     */
    public function startSession($userData) {
       $result = self::$crud->create("sessions", $userData);
       if ($result) {
            return [
                'statuscode' => 200,
                'status' => 'Session started successfully.',
                'data' => $userData
            ];
       } else {
            return [
                'statuscode' => 500,
                'status' => 'Failed to start session.',
                'data' => []
            ];
        }
    }

    /** 
     * Get session data by session ID
     * @param string $sessionId The session ID to search for
     * @return array Returns an array with session data if found, or an error message if not found.
     * @throws Exception If the session ID is not provided or if the session cannot be found.
     */
    public function getSessionById($sessionId)
    {
        $result = self::$crud->findOne("sessions", ['session_id' => $sessionId], ['*']);
        if (is_array($result)) {
            $get_user = self::$crud->findOne("users", ['id' => $result["cars_id"]]);
            $user = (is_array($get_user)) ? $get_user : [];
            return [
                'statuscode' => 200,
                'status' => 'Session found.',
                'data' => $user,
                'session' => $result
            ];
        } else {
            return [
                'statuscode' => 404,
                'status' => 'Session not found.',
                'data' => []
            ];
        }
    }

    // Cars
    public function createCar($data)
    {
        return self::$crud->create("car", [
            "cars_id" => $data["cars_id"],
            "cars_name" => $data["cars_name"],
            "price" => $data["price"],
            "brand" => $data["brand"] ?? null,
            "model" => $data["model"] ?? null,
            "body_style" => $data["body_style"] ?? null,
            "car_condition" => $data["car_condition"] ?? null,
            "fuel_type" => $data["fuel_type"] ?? null,
            "year" => $data["year"] ?? null
        ]);
    }

    public function listCars($fields = ['*'])
    {
        return self::$crud->findAll("car", $fields);
    }

    // Testimonials
    public function createTestimonial($data)
    {
        return self::$crud->create("testimonials", [
            "Testimonials_id" => $data["Testimonials_id"],
            "cars_id" => $data["cars_id"],
            "content" => $data["content"]
        ]);
    }

    public function listTestimonials($fields = ['*'])
    {
        return self::$crud->findAll("testimonials", $fields);
    }

    // Transactions
    public function createPurchase($data)
    {
        // Ensure referenced user and car exist to satisfy FK
        $userExists = self::$crud->findById("users", (int)$data["user_id"], ['id']) !== false;
        $carExists = self::$crud->findById("car", (int)$data["cars_id"], ['id']) !== false;
        if (!$userExists || !$carExists) {
            return false;
        }
        return self::$crud->create("purchases", [
            "user_id" => $data["user_id"],
            "cars_id" => $data["cars_id"],
            "price" => $data["price"]
        ]);
    }

    public function createRental($data)
    {
        // Ensure referenced user and car exist to satisfy FK
        $userExists = self::$crud->findById("users", (int)$data["user_id"], ['id']) !== false;
        $carExists = self::$crud->findById("car", (int)$data["cars_id"], ['id']) !== false;
        if (!$userExists || !$carExists) {
            return false;
        }
        return self::$crud->create("rentals", [
            "user_id" => $data["user_id"],
            "cars_id" => $data["cars_id"],
            "start_date" => $data["start_date"],
            "end_date" => $data["end_date"],
            "daily_rate" => $data["daily_rate"] ?? null
        ]);
    }
}