<?php
// required headers
header("Content-Type: application/json; charset=UTF-8");

// get DB connection
include_once '../config/database.php';

// instantiate queue class
include_once '../classes/queue.php';

$database = new Database();
$db = $database->getConnection();

$queue = new Queue($db);

/* =====================
        POST
Check if request is post
=========================*/
if ( $_SERVER['REQUEST_METHOD'] == 'POST' ){
    // get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    /*
        Check mandatory nodes
    */
    // ensure type and service is sent
    if(!$data->type || !$data->service){

        $response = array(
            'status' => false,
            'error' => 'provide all mandatory fields'
        );

        http_response_code(400);
        die(json_encode($response));
    }else{
        // ensure correct values are sent for type
        if($data->type != 'Citizen' && $data->type != 'Anonymous'){
            $response = array(
                'status' => false,
                'error' => 'incorrect value for type'
            );

            http_response_code(400);
            die(json_encode($response));
        }

        // ensure mandatory fields for Citizen
        if($data->type == 'Citizen'){
            if(!$data->firstName || !$data->lastName){
                $response = array(
                    'status' => false,
                    'error' => 'ensure firstName and lastName are provided'
                );

                http_response_code(400);
                die(json_encode($response));

            }
        }

    }

    // set queue property values
    $queue->type = $data->type;
    $queue->firstName = $data->firstName;
    $queue->lastName = $data->lastName;
    $queue->organization = $data->organization;
    $queue->service = $data->service;
    $queue->created = date('Y-m-d H:i:s');

    $response = array();

    // create the queue
    if($queue->create('queue') === true){
        $response = array(
            'status' => true,
            'data' => 'successfully added to the que'
        );
        http_response_code(200);
        echo json_encode($response);
    }

    // if unable to create the queue, return an error
    else{
        $response = array(
            'status' => false,
            'error' => $queue->create('queue')
        );
        http_response_code(400);
        echo json_encode($response);
    }
}

/* =====================
        GET
Check if request is GET
=========================*/
if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {

    $type = null;
    if(isset($_GET['type'])){
        $type = $_GET['type'];
    }

    // query the queue
    $stmt = $queue->getQue('queue',$type);

    // fetch all results in que
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // create response
    $response = array(
        'status' => true,
        'data' => $result
    );

    http_response_code(200);
    echo json_encode($response);
}


?>